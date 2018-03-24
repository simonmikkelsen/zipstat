<?php

//Version: 2.0
//Author:  Simon Mikkelsen, http://mikkelsen.tv/

class stier
{
function Stier()
{
	////////////////////////////////////
	 //|Basic settings|//
	  ////////////////////////////////////

	// INTRODUCTION
	// ==============
	// In order to get ZIP Stat to work, you must change all of the
	// basic settings. They are related to your database, the web address
	// you will run ZIP Stat on and the path to ZIP Stat on your server.
	//

	/* The name of the database to use.
	*/
	$this->options['DB_database'] = "zipstat";
	
	/* The username to access the database with
	*/
	$this->options['DB_username'] = "zipstat";
	
	/* The hostname or IP-address to the database host.
	*/
	$this->options['DB_hostname'] = "localhost";
	
	/* The password for the database.
	*/
	$this->options['DB_password'] = "Gyb.98_Yprg%qq!jv5O";

        /** The path part of the main url, e.g. used for cookies. */
	$this->options['urlPath'] = '/test';

	/** The web address to the folder where files such as zipstat.php
	 *  and stats.php can be found. There must be no / in the end.
	 */
	$this->options['urlMain'] = 'https://zipstat.dk' . $this->options['urlPath'];

	/** The web address all links to the front page must point to.
	 */
	$this->options['ZSHomePage'] = "https://zipstat.dk/";
	
	/** The web address all links to the contact page must point to.
	 */
	$this->options['urlContact'] = 'https://www.zip.dk/zipstat/kontakt.shtml';

	/** The e-mail address of the administrator - probably yours.
	 */
	$this->options['adminEMail'] = "zipstat@zipstat.dk";

	/** The name of the administrator - probably yours.
	 */
	$this->options['adminName'] = "Simon Mikkelsen";

	/** The e-mail addres to which users can send errorrs to.
	 */
	$this->options['errorEMail'] = "zipstat@zipstat.dk";

	/** The domain you are hosting ZIP Stat on. IMPORTANT: Only put your
	 *  domain, and skip any www etc. E.g. zipstat.dk
	 */
	$this->options['domain'] = "zipstat.dk";
	
	/** The full (absolute) path to the web root of your domain. Note
	 *  that this  path is on your domain. If you don't knows the path you
	 *  can ask the support of your web hotel.
	 */
	//This does not seem to be used anywhere. Remove when ZIP Stat is tested without it.
	//$this->stier['base'] = "/home/zipstat/public_html";

	////////////////////////////////////////////
	 //|Advanced settings.|//
	  ////////////////////////////////////////////

	/*The name of the service.
	*/
	$this->options['name_of_service'] = "ZIP Stat";

	/* Can ZIP Stat send e-mails using PHPs mail() function?
	 * Yes 1, 0 no.
	 */
	$this->options['send_mail'] = 1; //Values: 0 or 1.

	/*Shall IP adresses be translated into domain names, using reverse DNS?
	  1 yes, 0 no. This may require a lot of CPU resources, so if they are a
	  problem this value may be set to 0.
	  This feature is required for the hits for domains and top domains.
	  */
	$this->options['look_up_domains'] = 1; //Values: 0 or 1 or 2.

	/*Send an e-mail with a users stats, if the user requests it.
	  The primary reason to disable this feature is, if the mail function
	  is not present at the system.
	  1 enabled, 0 disabled.
	*/
	$this->options['send_stat_mails'] = 1; //Values: 0 or 1.

	/*Shall all users always have a pro account. If you are running ZIP Stat
	  only for yourself, and may be some close friends, you will usually
	  enable this. 1 enabled, 0 disabled.
	*/
	$this->options['always_pro'] = 0; //Values: 0 or 1.

	/*States if users with a pro account also shall have limitations. If ýou
	  do not have problems with users who e.g. uses the stats for the latest
	  visits as a log file, you should disable this. The limits are defined
	  in the file Html.php in the function pro in the array $maxvalue.
	  1 enabled, 0 disabled.
	*/
	$this->options['use_pro_limits'] = 1; //Values: 0 or 1.
	
	/*Always adjust the time with this amount of seconds.
	  Other time adjustments may be performed later, but this adjustment will
	  always be applied.
	*/
	$this->options['timeAdjustSec'] = 0*2*3600; //Add 0 hours.
	
	//Date formats - may be put in the localization file in the future.
	$this->options['dateformat_long_no_sec'] = "\d. d. m. Y \k\l. H:i";
	$this->options['dateformat_shortdate'] = "\d. d/m-Y \k\l. H:i";
	$this->options['dateformat_backup'] = "d-m-Y_H-i";


	////////////////////////////
	 //|Optimizing|//
	  ////////////////////////////

	/*When saving data in a text file, enabling this will cause the file to
	  be checked after it has been written. If you have problems with
	  distroyed data files you should enable this.
	  1 enabled, 0 disabled.
	 */
	$this->options['safe_save'] = 1; //Values: 0 or 1.

	/*Register stats for a monthly summary. Currently this is not fully
	  implemented and cannot be used.
	  1 enabled, 0 disabled.
	*/
	$this->options['reg_mstats'] = 0; //Values: 0 or 1.

	/*The largest number of non unique hits to register using a normal
	  account. If more hits occure they are just ignored. If you do not
	  have any problems, or just running ZIP Stat for yourself, set this
	  very heigh.
	*/
	$this->options['max_hits_day'] = 10000;

	/*The largest number of non unique hits to register using a pro
	  account. If more hits occure they are just ignored. If you do not
	  have any problems, or just running ZIP Stat for yourself, set this
	  very heigh.
	*/
	$this->options['max_hits_day_pro'] = 10000;
	
	/* Is low bandwidth mode enabled (true or false)?
	   In low bandwidth mode ZIP Stat will try to save bandwidth, typically
		 by disabling non essential bandwidth consuming functions, or by not
		 allowing a user to get as much information at one time as normal.
	 */
	$this->options['low_bandwidth'] = false;

	/* The maximum number of stats a user can see on the statsite at one time
	   in low bandwidth mode.
	*/
	$this->options['low_bandwidth_max_stats'] = 2;
	
	/* Adjust the time, on which the cache of the index of collective stats,
	   will expire (in seconds relative to midnight).
	 */
	$this->options['cache_expire_adjust_collindex'] = 5*3600;

///////////////////////////
	//This is beyond advanced - expect things to break if you play with this.
/////////////////////////////////////////

	////////////////////////////////////
	 //|Persistence|//
	  ////////////////////////////////////
	/* Sets how to read data. Must be an array of strings. Valid strings are:
	 *  "mysql.20": Use a MySQL database in ZIP Stat 2.0 format
	 *  "textfile": Use a textfile
	 * 
	 * First the user will be attempted located using the first method given.
	 * If the user is not found, an attempt will be made using the second method
	 * and so on. If the user is not found in any of the methods the user will be
	 * declared non existent.
	 *
	 * This mechanisem is made to make it easy to move from one storage format to
	 * another. One way would be to convert all file at once, but that is pretty hard
	 * to do in practice using PHP. In this way the conversion will be almost
	 * transparent and anybody will be able to do it. When all users are converted
	 * it provides no overhead, because all users are found in the first attempt.
	 */
	/*The following line will combined with a mysql.20 writer move the text files
	  to the ZIP Stat 2.0 MySQL format. It actually says: If the user exists in
	  MySQL, use MySQL. If the user does not exists in MySQL but exists as a textfile
	  read from that text file. If the writer is then set to mysql.20 the user will be
	  written to MySQL and the text time the user is loaded it will be from MySQL.
	*/
	$this->options['persistenceRead'] = array("mysql.20");
	//Read from a text file
	//$this->options['persistenceRead'] = array("textfile");
	
	/* How to write data (supports the same modes as for reading). 
	 * Only one method can be given, in the plain old one-text-string way.
	*/
	$this->options['persistenceWrite'] = "mysql.20";
	

	////////////////////////////////////
	 //|Database|//
	  ////////////////////////////////////
	
	/* The name of the main table.
	*/
	$this->options['DB_tablename_main'] = "zs20_main";

	/* The name of the table storing todays collective visits.
	*/
	$this->options['DB_tablename_visits'] = "zs20_visits";
	
	/* The name of the table storing the archive of collective visits.
	*/
	$this->options['DB_tablename_visits_archive'] = "zs20_visits_storage";
	
	/* The name of the table storing cached content.
	*/
	$this->options['DB_tablename_cache'] = "zs20_cache";

	$this->options['decimal_comma'] = ",";

	////////////////////
	 //|Sites|//
	  ////////////////////
	$this->options['cgiURL'] = $this->options['urlMain'];
	$this->options['urlCss'] = $this->options['urlMain'].'/css.php';
	$this->options['urlUserArea'] = $this->options['urlMain'].'/userarea.php';
	$this->options['urlUserAreaMain'] = $this->options['urlMain'].'/userareaMain.php';
	$this->options['urlUserAreaCodegen'] = $this->options['urlMain'].'/codegen.php';
	$this->options['urlUserAreaLeftmenu'] = $this->options['urlMain'].'/userareaLeftmenu.php';
	$this->options['urlUserAreaTopmenu'] = $this->options['urlMain'].'/userareaTopmenu.php';
	$this->options['urlStatsite'] = $this->options['urlMain'].'/stats.php';
	$this->options['urlIgnore'] = $this->options['urlMain'].'/ignore.php';
	$this->options['urlSignup'] = $this->options['urlMain'].'/register.php';
	$this->options['urlTotal'] = $this->options['urlMain'].'/total.php'; //Without any trailing /
	$this->options['urlHelp'] = $this->options['ZSHomePage'].'hjaelp.shtml';
	$this->options['urlLogout'] = $this->options['urlMain'].'/logout.php';
	$this->options['imageURL'] = $this->options['urlMain']."/images";


	//////////////////////
	 //|Processing modes|//
	  //////////////////////
	
	//Update the users data file for every hit (1=enable, 0=disable).
	$this->options['processMode'] = 1;
	//Log hits in one file for later processing (1=enable, 0=disable).
	$this->options['logMode'] = 0;
	//The name of the log file, as a pattern for the PHP date() function.
	$this->options['logModeFileName'] = '\z\i\p\s\t\a\t\_Y\_n\_d\.\t\x\t';
	//The extention of a non processed log file. A dot will automatically be 
	//added before the extention.
	$this->options['logModeFileExt'] = 'txt';

	//Register visits collective. (1=enable, 0=disable).
	$this->options['collective'] = 1;

	////////////////
	 //|Processing|//
	  ////////////////
	//Shall the processed hits be processed into one overall
	//user (1=enable, 0=disable), as given in the option processModeAllInOneUsername
	$this->options['processAllInOne'] = 1;
	//The username to process all stats to
	$this->options['processAllInOneUsername'] = 'all';
	//Process the data into each users data file.
	//Note: All processed users data may be stored in memory, so this may
	//require a lot of memory. Run the processing often to minimize this.
	$this->options['processIntoEach'] = 1;
	//States what to do with processed logs:
	//0: Do nothing (note: the same logs will be processed on next run)
	//1: Rename extention so the files will be processed later
	//2: Delete logs
	$this->options['processedLogs'] = 1;
	//If above has option 1, rename the extention to this. A dot will
	//automatically be added before the extention.
	$this->options['processedLogsExp'] = 'processed';

	////////////////////////
	 //|Internals|//
	  ////////////////////////

	//You wil only have to change the following variables if you do not want to
	//use the folder names ZIP Stat uses by default.
	//Changing these should work, but is largely untested. Please report if any
	//of them won't work.
	$this->stier['zipstat_base'] 			= dirname(__FILE__);

  //The zipstat_datafiler option set the folder in which ZIP Stats user
	//data is kept, if ZIP Stat uses plain text files instead of a database.
	//This old way is not tested anymore, but should work.
	//Remember to give the full / absolute path from the root if your
	//webserver. If you don't have this, you can ask the support of the
	//web hotel.
	//You can name the directory one of the following ways:
	//1. Put the folder outside the "web root", that is even if somebody knows
	//the name of the folder, they cannot download any files from it.
	//This is by far the safest method, but not always possible.
	//2. Give the folder a name nobody can guess, like aomi54370h5ouafjkl
	//Also make sure to put a index.html file in the directory you put the
	//folder in, so people cannot see the folder name in any way.
	$this->stier['zipstat_datafiler'] 	= "";
	
	$this->stier['zipstat_logs'] 	= $this->stier['zipstat_base']."/logs";
	$this->stier['zipstat_lib'] 			= $this->stier['zipstat_base']."/html.php";
	$this->stier['zipstat_icons'] 		= $this->stier['zipstat_base']."/images";
	$this->stier['panelHtmlFolder'] = $this->stier['zipstat_base']."/styles/panel_html";
	$this->stier['panelCssFolder'] = $this->stier['zipstat_base']."/styles/panel_css";
	$this->stier['templates'] = $this->stier['zipstat_base']."/templates";
	$this->stier['languages'] = $this->stier['zipstat_base']."/languages";

	$this->stier['zipstatCgi'] = "zipstat.php";
	$this->stier['cssCgi'] = "css.php";
	$this->stier['jsvarsCgi'] = "jsvars.php";
} /*End function stier() */

//For compatibillity only. May be deleted if ZIP Stat is proporly testet.
/*function hentStier()
{
	return $this->stier;
}*/

/**
 * Returns the full set of path related options.
 */
function getStier()
{
	return $this->stier;
}

/**
 * Returns the full set of non path related options.
 */
function getOptions()
{
	return $this->options;
}

/**
 * Returns the requested non path related option.
 */
function getOption($option)
{
	if (! isset($this->options[$option]))
		die("The requested option ($option) was not found.");
	return $this->options[$option];
}

/**
 * Returns the requested path.
 *
 * @deprecated please use {@link #getPath}.
 */
function getSti($sti)
{
	return $this->getPath($sti);
}

/**
 * Returns the corresponding path.
 * 
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @param $path the key for the path.
 * @returns String
 * @return the corresponding path.
 */
function getPath($path)
{
	if (!isset($this->stier[$path]))
		die("The requested path ($path) was not found.");
	return $this->stier[$path];
}

} /* End class stier */

/**
 * Makes it possible to use the danish named class Stier using
 * an english name.
 */
class Options extends Stier {
}

?>
