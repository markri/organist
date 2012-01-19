set :deploy_to, "/home/tester/vhosts/allaboutlease"
set :user, "tester"
role :web, "dev1.netvlies.net"                          # Your HTTP server, Apache/etc
role :app, "dev1.netvlies.net"                          # This may be the same as your `Web` server
role :db,  "dev1.netvlies.net", :primary => true 	    # This is where Rails migrations will run
