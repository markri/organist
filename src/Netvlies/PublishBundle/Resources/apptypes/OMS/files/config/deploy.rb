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

set :use_sudo, false
default_run_options[:pty] = true


namespace :deploy do

    after "deploy:setup", "deploy:media:setup"
    after "deploy:setup", "deploy:cache:setup"
    after "deploy:setup", "deploy:setupdb"

    after "deploy:symlink", "deploy:cache:symlink"
    after "deploy:symlink", "deploy:media:symlink"
    after "deploy:symlink", "deploy:htacl:symlink"

    after "deploy:update_code", "deploy:dbconfig", "deploy:updatevhost"

    namespace :media do
        desc "Create the media dir in shared path."

        task :setup do
          run "cd #{shared_path}; mkdir -p media; chmod 777 media"
        end

        desc "Link media from shared to common."
        task :symlink do
          # in case media folder is in repository remove it, it;'s already there
          run "cd #{current_path}; rm -rf media; ln -s #{shared_path}/media ."
        end
    end
  
    namespace :cache do
    desc "Create the cache dir in shared path."
        task :setup do
          run "cd #{shared_path}; mkdir -p cache; chmod 777 cache"
        end

        desc "Link cache from shared to common."
        task :symlink do
          # in case cache folder is in repository remove it, it;'s already there
          run "cd #{current_path}; rm -rf cache; ln -s #{shared_path}/cache ."
        end
    end


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


	desc 'Link parameters.ini.$env to parameters.ini DB params enclosed with # will be replaced'
	task :dbconfig do
        run "sed -i -e 's/#primarydomain#/#{primarydomain}/' #{release_path}/cms/db_config_local.inc.php"
        run "sed -i -e 's/#mysqldb#/#{mysqldb}/' #{release_path}/cms/db_config_local.inc.php"
        run "sed -i -e 's/#mysqluser#/#{mysqluser}/' #{release_path}/cms/db_config_local.inc.php"
        run "sed -i -e 's/#mysqlpw#/#{mysqlpw}/' #{release_path}/cms/db_config_local.inc.php"
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


    namespace :htacl do
    desc "Link cache from shared to common."
        task :symlink do
            run "cd #{current_path}; rm cms/.htpasswd; ln -s #{shared_path}/.htpasswd cms/"
            run "cd #{current_path}; rm cms/.htaccess; ln -s #{shared_path}/.htaccess cms/"
        end
    end
end
