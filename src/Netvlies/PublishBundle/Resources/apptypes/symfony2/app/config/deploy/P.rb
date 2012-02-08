set :deploy_to, "#{approot}"
set :user, "#{username}"
server "#{hostname}", :app, :web, :primary => true