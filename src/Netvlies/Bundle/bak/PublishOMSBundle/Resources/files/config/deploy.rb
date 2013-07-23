set :application, "#{project}"
set :repository, "#{gitrepo}"
set :revision, "#{revision}"
set :scm, :git
set :deploy_via, :rsync_with_remote_cache
set :local_cache, ''
set :rsync_options, '-az --delete --exclude=/Capfile --exclude=/config --exclude=/.gitignore --delete-excluded'
set :shared_children, "#{userdirs}".split(/,/)
set :shared_files, "#{userfiles}".split(/,/)
set :use_sudo, false
set :keep_releases, 3

default_run_options[:pty] = true

namespace :deploy do

    # Make sure that all shared_childs are 777 mode writable
    after "deploy:finalize_update", "deploy:shared_childs_writable"

    # Cleanup old releases
    after 'deploy:symlink', 'deploy:cleanup'

    desc 'Link OTAP specific params to right file, and replace variables'
    task :dbconfig do
        run "sed -i -e 's/#primarydomain#/#{primarydomain}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"
        run "sed -i -e 's/#mysqldb#/#{mysqldb}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"
        run "sed -i -e 's/#mysqluser#/#{mysqluser}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"
        run "sed -i -e 's/#mysqlpw#/#{mysqlpw}/' #{release_path}/cms/db_config_local.inc.#{otap}.php"

        run "ln -fs #{release_path}/cms/db_config_local.inc.#{otap}.php #{release_path}/cms/db_config_local.inc.php"
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
