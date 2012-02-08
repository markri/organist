set :deploy_to, "#{homedirsBase}/#{username}/vhosts/#{project}"
set :user, "#{username}"
server "#{hostname}", :app, :web, :primary => true