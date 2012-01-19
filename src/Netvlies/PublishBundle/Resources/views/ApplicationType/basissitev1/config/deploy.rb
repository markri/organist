# Setup for multi stage environments in OTAP
# see deploy folder where T.rb, P.rb are
set :stage_dir,   "config/deploy"
set :stages, %w(T A P)
set :default_stage, "T"
require 'capistrano/ext/multistage'





set :stages, %w(testing acceptance production)
set :default_stage, "testing"
require 'capistrano/ext/multistage'

set :application, "#{project}"
set :repository, "#{gitrepo}"
set :scm, :git
set :deploy_via, :remote_cache
set :revision, "#{revision}"

set :ssh_options, {:forward_agent => true}
set :use_sudo, false
set :keep_releases, 3

set :shared_files, "#{userfiles}".split(/,/)
set :shared_children, "#{userdirs}".split(/,/)

set :copy_exclude, [".git", "Capfile", "build.xml", "config", ".gitignore"]


namespace :deploy do

  #@todo setup??
  after "deploy:update_code", "deploy:imagefix"
  after "deploy:update_code", "deploy:sharedsymlinks"
  after "deploy:symlink", "deploy:cleanup"

	desc "This will get the img folder from repository and copy its content into the shared folder"
	task :imagefix do
		run "mkdir -p #{shared_path}/img; cp -R  #{release_path}/img/* #{shared_path}/img"
	end
  
    desc "Link shared files and dirs"
    task :sharedsymlinks do
	
		shared_files.each { |x| 
			run "rm -f #{release_path}/#{x}; ln -fs #{shared_path}/#{x} #{release_path}/#{x}"
		}
		
		shared_children.each { |x| 
			run "rm -rf #{release_path}/#{x}; ln -fs #{shared_path}/#{x} #{release_path}/#{x}"
		}		
    end

end