set :stages, %w(T A P)
set :default_stage, "T"
require 'capistrano/ext/multistage'

set :application, "#{project}"
set :repository, "#{gitrepo}"
set :revision, "#{revision}"
set :scm, :git
set :deploy_via, :rsync_with_remote_cache
set :local_cache, ''
set :rsync_options, '-az --delete --exclude=Capfile --exclude=build.xml --exclude=config/ --exclude=.gitignore --delete-excluded'
set :shared_children, "#{userdirs}".split(/,/)
set :shared_files, "#{userfiles}".split(/,/)
set :port, #{sshport}

set :use_sudo, false
default_run_options[:pty] = true


namespace :deploy do

	# Make sure that all shared_childs are 777 mode writable (should be suitable for OMS and Symfony2)
	after "deploy:finalize_update", "deploy:shared_childs_writable"
    after "deploy:update_code", "deploy:dbconfig", "deploy:updatevhost"


    desc "Setup database"
    task :setupdb do
		set :user, "#{sudouser}"
		sessions.values.each { |session| session.close }
		sessions.clear

        run "sudo #{bridgebin} mysql -u #{project} -p #{mysqlpw} -d #{project}"

		set :user, "#{username}"
		sessions.values.each { |session| session.close }
		sessions.clear
    end


	desc 'Link parameters.ini.$env to db_config_local.inc.php DB params enclosed with # will be replaced'
	task :dbconfig do
        run "sed -i -e 's/#primarydomain#/#{primarydomain}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"
        run "sed -i -e 's/#mysqldb#/#{mysqldb}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"
        run "sed -i -e 's/#mysqluser#/#{mysqluser}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"
        run "sed -i -e 's/#mysqlpw#/#{mysqlpw}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"

		run "ln -fs #{release_path}/cms/db_config_local.inc.#{otap}.php #{release_path}/cms/db_config_local.inc.php"
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


	desc 'Make sure that shared dirs and files are fully writable with chmod 777'
	task :shared_childs_writable do
		if shared_children
			shared_children.each do |link|
				run "rm -rf #{release_path}/#{link}"
				run "mkdir -p #{shared_path}/#{link}"
				run "ln -nfs #{shared_path}/#{link} #{release_path}/#{link}"
				run "chmod 777 #{shared_path}/#{link}"
			end
		end
		if shared_files
			# Make links and make sure they are 777
			shared_files.each do |link|
				link_dir = File.dirname("#{shared_path}/#{link}")
				run "rm #{release_path}/#{link}"
				run "mkdir -p #{link_dir}"
				run "touch #{shared_path}/#{link}"
				run "ln -nfs #{shared_path}/#{link} #{release_path}/#{link}"
				run "chmod 777 #{shared_path}/#{link}"
			end
		end
	end
end
