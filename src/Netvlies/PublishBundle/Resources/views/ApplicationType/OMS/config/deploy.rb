set :stages, %w(testing acceptance production)
set :default_stage, "testing"
require 'capistrano/ext/multistage'

set :application, 'All About Lease'
set :repository,  'git@bitbucket.org:netvlies/allaboutlease.git'
set :scm, :git
set :deploy_via, :rsync_with_remote_cache
set :local_cache, ''
set :rsync_options, '-az --delete --exclude=Capfile --exclude=build.xml --exclude=config/ --exclude=.gitignore --delete-excluded'

set :use_sudo, false
default_run_options[:pty] = true


namespace :deploy do

  after "deploy:setup", "deploy:media:setup"
  after "deploy:symlink", "deploy:media:symlink"
  
  after "deploy:setup", "deploy:cache:setup"
  after "deploy:symlink", "deploy:cache:symlink"  

  after "deploy:symlink", "deploy:htacl:symlink"

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

  namespace :htacl do
    desc "Link cache from shared to common."
    task :symlink do
      run "cd #{current_path}; rm cms/.htpasswd; ln -s #{shared_path}/.htpasswd cms/"
      run "cd #{current_path}; rm cms/.htaccess; ln -s #{shared_path}/.htaccess cms/"
    end

  end  
  
end
