<?php	Domeinen::add(		Config::LIVE_ENVIRONMENT, // environment		'#primarydomain#', // hostname/domeinnaam		1, // domein_index		1, // binnenkomst pagina		true // default domein aangeven van deze omgeving	);	Config::$environment = Config::LIVE_ENVIRONMENT;		Config::$mysql[Config::LIVE_ENVIRONMENT]['user'] = '#mysqluser#';	Config::$mysql[Config::LIVE_ENVIRONMENT]['password'] = '#mysqlpw#';	Config::$mysql[Config::LIVE_ENVIRONMENT]['database'] = '#mysqldb#';	Config::$mysql[Config::LIVE_ENVIRONMENT]['host'] = 'localhost';	?>