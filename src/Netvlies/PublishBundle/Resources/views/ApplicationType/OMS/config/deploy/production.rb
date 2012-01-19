set :deploy_to, "/home/allaboutlease-ftp/www/allaboutlease"
set :user, "allaboutlease-ftp"
role :web, "allaboutlease.nl"                          # Your HTTP server, Apache/etc
role :app, "allaboutlease.nl"                          # This may be the same as your `Web` server
role :db,  "allaboutlease.nl", :primary => true 	    # This is where Rails migrations will run
