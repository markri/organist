set :deploy_to, "#{caproot}"
set :user, "#{username}"
server "#{hostname}", :app, :web, :primary => true