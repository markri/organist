# Setup for multi stage environments in OTAP
# see deploy folder where T.rb, A.rb, P.rb are located
set :stage_dir,   "app/config/deploy"
set :stages, %w(T A P)
set :default_stage, "T"
require 'capistrano/ext/multistage'

# What to deploy...
set :application, "#{project}"
set :repository, "#{gitrepo}"
set :scm, :git
set :revision, "#{revision}"

# Connection and deployment type settings
set :deploy_via, :remote_cache
set :keep_releases, 5
set :ssh_options, {:forward_agent => true}
set :default_run_options, {:pty => true}
default_run_options[:pty] = true
set :use_sudo, false

# Additional directory and file settings for deployment
set :shared_files, "#{userfiles}".split(/,/)
set :shared_children, "#{userdirs}".split(/,/)
set :copy_exclude, [".git", "Capfile", "build.xml", ".gitignore", "web/app_dev.php", "app/config/deploy.rb", "app/config/deploy"]

# Symfony related settings
set :app_path,    "app"
#set :asset_children, [] somehow enabling this will even generate more errors!
set :update_vendors, true
set :vendors_mode, "install"

namespace :deploy do

    #after "deploy:setup", "deploy:media:setup"
    # create db and user
    # create vhost



	# Share application user folders. e.g. "web/uploads". Given in userfiles and dirs in database
	after "deploy:update_code", "deploy:sharedsymlinks", "deploy:update_acl"

	# Symfony2 related targets in Capifony
	after "deploy:share_childs", "deploy:parameteters_symlink"

	# Cleanup to max 5 releases
	after "deploy:symlink", "deploy:cleanup"


	# Custom targets / helpers to deploy application
    desc "Link shared files and dirs"
    task :sharedsymlinks do

		shared_files.each { |x|
			run "rm -f #{release_path}/#{x}; ln -fs #{shared_path}/#{x} #{release_path}/#{x}"
		}

		shared_children.each { |x|
			run "rm -rf #{release_path}/#{x}; ln -fs #{shared_path}/#{x} #{release_path}/#{x}"
		}

    end


	desc "Link parameters.ini.$env to parameters.ini DB params enclosed with # will be replaced"
	task :parameteters_symlink do

		# Create parameters.ini for env
		run "sed -i -e 's/#mysqldb#/#{mysqldb}/' #{release_path}/app/config/parameters.ini.#{otap}"
		run "sed -i -e 's/#mysqluser#/#{mysqluser}/' #{release_path}/app/config/parameters.ini.#{otap}"
		run "sed -i -e 's/#mysqlpw#/#{mysqlpw}/' #{release_path}/app/config/parameters.ini.#{otap}"

		run "ln -fs #{release_path}/app/config/parameters.ini.#{otap} #{release_path}/app/config/parameters.ini"
	end


	desc "set the ACL on the app/logs and app/cache directories"
	task :update_acl do
		# This will run the setfacl command on the cache and logs directory of the symfony2 app
		set :user, "#{sudouser}"
		sessions.values.each { |session| session.close }
		sessions.clear
		run "sudo #{bridgebin} symfony2acl -b #{release_path} -u #{username}"
		set :user, "#{username}"
		sessions.values.each { |session| session.close }
		sessions.clear
	end

end
