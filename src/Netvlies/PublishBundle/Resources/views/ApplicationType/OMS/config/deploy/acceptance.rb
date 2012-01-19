set :deploy_to, "/home/allaboutlease-dev/www/allaboutlease"
set :user, "allaboutlease-dev"
role :web, "allaboutlease.netvlies-demo.nl"                          # Your HTTP server, Apache/etc
role :app, "allaboutlease.netvlies-demo.nl"                          # This may be the same as your `Web` server
role :db,  "allaboutlease.netvlies-demo.nl", :primary => true 	    # This is where Rails migrations will run