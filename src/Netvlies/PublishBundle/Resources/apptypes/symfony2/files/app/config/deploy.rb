# Setup for multi stage environments in OTAP
# see deploy folder where T.rb, A.rb, P.rb are located
set :stage_dir,   'app/config/deploy'
set :stages, %w(T A P)
set :default_stage, 'T'
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
set :use_sudo, false

# Additional directory and file settings for deployment
set :shared_files, "#{userfiles}".split(/,/)
set :shared_children, "#{userdirs}".split(/,/)
set :copy_exclude, ['.git', 'Capfile', 'build.xml', '.gitignore', 'web/app_dev.php', 'app/config/deploy.rb', 'app/config/deploy']

# Symfony related settings
set :app_path,    'app'
set :update_vendors, true
set :vendors_mode, 'install'

namespace :deploy do

    # create db and user
    after 'deploy:setup', 'deploy:setupdb'

	# Share application user folders. e.g. "web/uploads". Given in userfiles and dirs in database
	after 'deploy:update_code', 'deploy:update_acl'

	# Symfony2 related targets in Capifony
	before 'deploy:finalize_update', 'deploy:parameters_symlink', 'deploy:vendorcheck', 'deploy:shared_childs_writable'

	# Cleanup to max 5 releases
	after 'deploy:symlink', 'deploy:cleanup', 'deploy:updatevhost'


    desc 'Initial vendors install if needed'
    task :vendorcheck do
		exists = capture("if [ -d \"#{shared_path}/vendor/symfony\" ]; then echo \"true\"; fi")
		
		if "#{exists}".strip != "true"
			set :vendors_mode, "reinstall"
			# also create db schema on initial deployment
			# run "#{release_path}/app/console doctrine:schema:create"
		end
    end	
	

    desc 'Setup database'
    task :setupdb do
		set :user, "#{sudouser}"
		sessions.values.each { |session| session.close }
		sessions.clear

        run "sudo #{bridgebin} mysql -u #{project} -p #{mysqlpw} -d #{project}"

		set :user, "#{username}"
		sessions.values.each { |session| session.close }
		sessions.clear
    end

	desc 'Make sure that shared dirs are fully writable with chmod 777'
	task :shared_childs_writable do
		if shared_children
			shared_children.each do |link|
				run "chmod 777 #{shared_path}/#{link}"
			end
		end
		if shared_files
			shared_files.each do |link|
				run "chmod 777 #{shared_path}/#{link}"
			end
		end		
	end		
	

	desc 'Link parameters.ini.$env to parameters.ini DB params enclosed with # will be replaced'
	task :parameters_symlink do

		# Create parameters.ini for env
		run "sed -i -e 's/#mysqldb#/#{mysqldb}/' #{release_path}/app/config/parameters.ini.#{otap}"
		run "sed -i -e 's/#mysqluser#/#{mysqluser}/' #{release_path}/app/config/parameters.ini.#{otap}"
		run "sed -i -e 's/#mysqlpw#/#{mysqlpw}/' #{release_path}/app/config/parameters.ini.#{otap}"

		run "ln -fs #{release_path}/app/config/parameters.ini.#{otap} #{release_path}/app/config/parameters.ini"
	end

    desc 'update vhost if otap=T'
    task :updatevhost do
        if "#{otap}" == 'T'
            set :user, "#{sudouser}"
            sessions.values.each { |session| session.close }
            sessions.clear

            # serverroot (-s) is the path were the logs directory needs to be
            run "sudo #{bridgebin} apache -dn #{primarydomain} -s #{homedirsBase}/#{username} -d #{webroot}"

            set :user, "#{username}"
            sessions.values.each { |session| session.close }
            sessions.clear
        end
    end


	desc 'set the ACL on the app/logs and app/cache directories.'
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
