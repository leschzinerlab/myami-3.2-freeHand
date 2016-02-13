<?php

/**
 *	The Leginon software is Copyright 2010 
 *	The Scripps Research Institute, La Jolla, CA
 *	For terms of the license agreement
 *	see  http://ami.scripps.edu/software/leginon-license
 *
 */
 
 /**
  *  Please visit http://yourhost/myamiwebfolder/setup
  *  for automatically setup this config file for the
  *  first time.
  */
 

require_once 'inc/config.inc';
define('WEB_ROOT',dirname(__FILE__));

// --- define myamiweb tools base --- //
define('PROJECT_NAME', "myamiweb");
define('PROJECT_TITLE', "Appion and Leginon Tools");

// --- define site base path -- //
// --- This should be changed if the myamiweb directory is located -- //
// --- in a sub-directory of the Apache web directory. -- //
// --- ex. myamiweb is in /var/www/html/applications/myamiweb/ then -- //
// --- change "myamiweb to "applications/myamiweb" -- //
define('BASE_PATH',"");

//define('BASE_URL', "/".BASE_PATH."/");
define('BASE_URL', "/");
define('PROJECT_URL', BASE_URL."project/");

// --- myamiweb login --- //
// Browse to the administration tools in myamiweb prior to 
// changing this to true to populate DB tables correctly.
define('ENABLE_LOGIN', false);
define('ENABLE_ANONYMOUS', false);
define('COOKIE_PASSPHRASE', 'fatw4909umt4f09pmutsr9mushv9uvi08gn4swn4sc');

// --- Administrator email title and email address -- //
define('EMAIL_TITLE', 'The name of your institute');
define('ADMIN_EMAIL', 'helpdes@physics.ucsd.edu');

// --- When 'ENABLE_SMTP set to true, email will send out -- //
// --- via ADMIN_EMIL's SMTP server. --// 
define('ENABLE_SMTP', false);
define('SMTP_HOST', 'mail.institute.edu');	//your smtp host

// --- Check this with your email administrator -- //
// --- Set it to true if your SMTP server requires authentication -- //
define('SMTP_AUTH', false);

// --- If SMTP_AUTH is not required(SMTP_AUTH set to false, -- //
// --- no need to fill in 'SMTP_USERNAME' & SMTP_PASSWORD -- //
define('SMTP_USERNAME', "");
define('SMTP_PASSWORD', "");

// --- Set your MySQL database server parameters -- //
define('DB_HOST', 'handel.al.ucsd.edu');
define('DB_USER', 'production_user');
define('DB_PASS', 'gHyrEx9VquqQgYF865HNFLzs0zCkuXtZ');
define('DB_LEGINON', 'production_leginondb');
define('DB_PROJECT', 'production_projectdb');

// --- default URL for project section --- //
define('VIEWER_URL', BASE_URL."3wviewer.php?expId=");
define('SUMMARY_URL', BASE_URL."summary.php?expId=");
define('UPLOAD_URL', BASE_URL."processing/uploadimage.php");

// --- Set cookie session time -- //
define('COOKIE_TIME', 0);		//0 is never expire. 

// --- defaut user group -- //
define('GP_USER', 'users');

// --- XML test dataset -- //
$XML_DATA = "test/viewerdata.xml";

// --- Set Default table definition -- //
define('DEF_PROCESSING_TABLES_FILE', "defaultprocessingtables.xml");
define('DEF_PROCESSING_PREFIX', "production_ap");		//update mysql privileges

// --- Set External SQL server here (use for import/export application) -- //
// --- You can add as many as you want, just copy and paste the block -- //
// --- to a new one and update the connection parameters -- //
// --- $SQL_HOSTS['example_host_name']['db_host'] = 'example_host_name'; -- //
// --- $SQL_HOSTS['example_host_name']['db_user'] = 'usr_object'; -- //
// --- $SQL_HOSTS['example_host_name']['db_pass'] = ''; -- //
// --- $SQL_HOSTS['example_host_name']['db'] = 'legniondb'; -- //

$SQL_HOSTS[DB_HOST]['db_host'] = DB_HOST;
$SQL_HOSTS[DB_HOST]['db_user'] = DB_USER;
$SQL_HOSTS[DB_HOST]['db_pass'] = DB_PASS;
$SQL_HOSTS[DB_HOST]['db'] = DB_LEGINON;

// --- path to main --- //
set_include_path(dirname(__FILE__).PATH_SEPARATOR
				.dirname(__FILE__)."/project".PATH_SEPARATOR
				.dirname(__FILE__)."/lib".PATH_SEPARATOR
				.dirname(__FILE__)."/lib/PEAR");

// --- add plugins --- //
// --- uncomment to enable processing web pages -- //
addplugin("processing");


// --- Add as many processing hosts as you like -- //
// --- Please enter your processing host information as in the following example. -- //
// --- if the login and passphrase are false/null	-- //
// --- the username and password that the user   	-- //
// --- logs into appion with will be used.			-- //
$PROCESSING_HOSTS[] = array(
'host' => 'handel.al.ucsd.edu', // for a single computer installation, this can be 'localhost'    
'nproc' => 12,  // number of processors available on the host, not used
'nodesdef' => '2', // default number of nodes used by a refinement job
'nodesmax' => '280', // maximum number of nodes a user may request for a refinement job
'ppndef' => '8', // default number of processors per node used for a refinement job
'ppnmax' => '8', // maximum number of processors per node a user may request for a refinement job
'reconpn' => '16', // recons per node, not used 
'walltimedef' => '48', // default wall time in hours that a job is allowed to run
'walltimemax' => '240', // maximum hours in wall time a user may request for a job
'cputimedef' => '1536', // default cpu time in hours a job is allowed to run (wall time x number of cpu's) 
'cputimemax' => '10000', // maximum cpu time in hours a user may request for a job
'memorymax' => '', // the maximum memory a job may use
'appionbin' => 'bin/', // the path to the myami/appion/bin directory on this host
'appionlibdir' => 'appion/', // the path to the myami/appion/appionlib directory on this host
'baseoutdir' => 'appion', // the directory that processing output should be stored in
'localhelperhost' => '', // a machine that has access to both the web server and the processing host file systems to copy data between the systems
'dirsep' => '/', // the directory separator used by this host
'wrapperpath' => '', // advanced option that enables more than one Appion installation on a single machine, contact us for info 
'loginmethod' => 'SHAREDKEY', // Appion currently supports 'USERPASSWORD'.  'SHAREKEY' is possible, but not preferable. 
'loginusername' => 'appionuser', // if this is not set, Appion uses the username provided by the user in the Appion Processing GUI
'passphrase' => 'dddTs1Tfm2pX4lL2EWubecFebDSG2cGP', // if this is not set, Appion uses the password provided by the user in the Appion Processing GUI
'publickey' => '/etc/myami/appionuser.pub', // set this if using 'SHAREDKEY' (not supported 
'privatekey' => '/etc/myami/appionuser'      // set this if using 'SHAREDKEY'
);

// --- Restrict file server if you want --- //
// --- Add your allowed processing directory as string in the array
$DATA_DIRS = array();

// --- Enable Image Cache --- //
define('ENABLE_CACHE', false);
// --- caching location --- //
// --- please make sure the apache user has read access to this folder --- //
// --- define('CACHE_PATH', "/srv/www/cache/"); --- //
define('CACHE_PATH', "");

// --- define Flash player base url --- //
define('FLASHPLAYER_URL', "/flashplayer/");

// --- define python commands - path --- //

// to download images as TIFF or JPEG
// $pythonpath="/your/site-packages";
// putenv("PYTHONPATH=$pythonpath");

// To use mrc2any, you need to install the pyami package which is part
// of myami.  See installation documentation for help.
// --- define('MRC2ANY', "/usr/bin/mrc2any" --- //
define('MRC2ANY', "");

// --- Check if IMAGIC is installed and running, otherwise hide all functions --- //
define('HIDE_IMAGIC', false);

// --- Check if MATLAB is installed and running, otherwise hide all functions --- //
define('HIDE_MATLAB', false);

// --- hide processing tools still under development. --- //
define('HIDE_FEATURE', true);

// --- temporary images upload directory --- //
define('TEMP_IMAGES_DIR', "/data/microscopes/appion/uploads");

// --- use appion wrapper (prepends APPION_WRAPPER_PATH to all Appion commands) --- //
define('USE_APPION_WRAPPER', false);
// --- define('APPION_WRAPPER_PATH', ""); --- //

// --- default appion base directory to store processing results (parallel to leginon image directory defined in leginon/leginon.cfg) --- //
define('DEFAULT_APPION_PATH',"/data/microscopes/appion");

// --- number of parent directories before the sessionname to append after DEFAULT_APPION_PATH --- // 
// The result is a subdivision of data by user, for example, as configured in each user's leginon/leginon.cfg or upon creation of the Leginon session--- //
define('APPION_PARENT_ADDITION',0); 

// --- sample tracking ---//
define('SAMPLE_TRACK', false);

// --- exclude projects in statistics. give a string with numbers separated by ',' ---//
// --- for example, "1,2" ---//
define('EXCLUDED_PROJECTS',"");

// --- hide testing tools related to specific data sets. --- //
define('HIDE_TEST_TOOLS', true);

// --- A startup message may be shown to the user --- //
define('STARTUP_MESSAGE', "");

// --- Redux server Host and Port information. --- //
// --- define('SERVER_HOST', "redux.schools.edu"); --- //
// --- define('SERVER_PORT', "55123"); --- //
define('SERVER_HOST', 'localhost');
define('SERVER_PORT', "55123");

// --- list specifc datasets that have test scripts associated with them --- //
// --- example:
// --- $TEST_SESSIONS = array(
// ---		'zz07jul25b'
// ---		,'zz06apr27c'
// ---		,'zz09feb12b'
// ---		,'zz09apr14b'
// ---		,'zz09feb18c'
// ---	);

$TEST_SESSIONS = array();

?>
