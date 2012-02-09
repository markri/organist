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