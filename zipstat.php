<?php
//The following uncommented code can be used for profiling.
#class TimeLogger
#{
#	var $lastTime;
#
#	var $usr;
#
#	var $start;
#
#	function TimeLogger($usr)
#	{
#		list($usec, $sec) = explode(" ",microtime());
#		$this->usr = $usr."[".($sec + $usec)."]";
#		$this->lastTime = $sec + $usec;
#		$this->start = $sec + $usec;
#	}
#
#	function timeLog($msg)
#	{
#		list($usec, $sec) = explode(" ",microtime());
#		$thisTime = $sec + $usec;
#		$out = $this->usr." ".($thisTime - $this->lastTime)." ".$msg."\n";
#		$this->lastTime = $thisTime;
#		$fp = fopen("timelog2.txt","a");
#		fwrite($fp, $out);
#		fclose($fp);
#	}
#
#	function theend()
#	{
#		list($usec, $sec) = explode(" ",microtime());
#		$thisTime = $sec + $usec;
#		$out = $this->usr." ".($thisTime - $this->start)." ----- End. ------\n";
#		$fp = fopen("timelog3.txt","a");
#		fwrite($fp, $out);
#		fclose($fp);
#	}
#}

//$tl = new TimeLogger("x");
//$tl->timeLog("1");
#list($usec, $sec) = explode(" ",microtime());
#$startTime = $sec + $usec;

//Now fire up ZIP Stat!
require_once(dirname(__FILE__))."/Html.php";
require_once(dirname(__FILE__))."/Stier.php";
require_once(dirname(__FILE__))."/lib/StatMail.php";

//Program
$stier = new Stier();

$ind = Html::setPostOrGetVars(array(), array());

Html::outputNoCacheHeaders();

//Shall this user be ignored?
if (isset($ind['brugernavn']) and isset($HTTP_COOKIE_VARS[$ind['brugernavn']])
	and $HTTP_COOKIE_VARS[$ind['brugernavn']] === 'ikkeop')
{
	$ind['taelop'] = "nej";
	$timeAdjusted = Html::getTimeAdjusted(NULL, $stier);
	setcookie ($ind['brugernavn'], 'ikkeop',$timeAdjusted+28*24*3600, "/", ".".$stier->getOption('domain'));
}

if ($stier->getOption('logMode') !== 0 and (!isset($ind['taelop']) or $ind['taelop'] !== "nej")) {
	require "lib/Logger.php";
	
	//Find the username to log
	if (isset($ind['brugernavn']) and strlen($ind['brugernavn']) > 0)
		$username = $ind['brugernavn'];
	else if (isset($ind['username']) and strlen($ind['username']) > 0)
		$username = $ind['username'];
	else
		$username = '';

	//Log the visit
	$timeAdjusted = Html::getTimeAdjusted(NULL, $stier);
	$logger = new Logger($stier);
	$logger->logVisit($timeAdjusted,
		(isset($ind['ssto']) ? $ind['ssto'] : ''),
		(isset($ind['referer']) ? $ind['referer'] : ''),
		(isset($ind['colors']) ? $ind['colors'] : ''),
		(isset($ind['java']) ? $ind['java'] : ''),
		(isset($ind['taelnr']) ? $ind['taelnr'] : ''),
		(isset($ind['taelnavn']) ? $ind['taelnavn'] : ''),
		(isset($ind['js']) ? $ind['js'] : ''),
		getenv('HTTP_USER_AGENT'),
		getenv('REMOTE_ADDR'),
		getenv('HTTP_ACCEPT_LANGUAGE'),
		getenv('HTTP_REFERER'),
		$username,
		'' //Not used yet
	);
	                 
} //End if is log mode enabled

//Set the engine to NULL, so we can see if it has not been created.
$engine = NULL;

if ($stier->getOption('processMode') !== 0) {
require_once "Mstat.php";
require_once "lib/ZipStatEngine.php";
require_once "lib/SiteContext.php";
require_once "lib/Localizer.php";

//Loads the data file

$datafil = DataSource::createInstance($ind['brugernavn'],$stier);

$res = $datafil->hentFil();

//Handle errors:
	$errors = new Errors();
	//Was the datafile fetched successfully
	if ($res === -2) {
		//Temporarely instance for error handling
		//'da' is danish, currently the only user interface language.
		$siteContext = new ShortSiteContext($stier, $ind, 'da');
		$errors->addError(new Error(2, sprintf($siteContext->getLocale('errDamagedDatasource'), $stier->getOption('name_of_service'))));
	} elseif (! $res or $res === 0) {
		//Temporarely instance for error handling
		$siteContext = new ShortSiteContext($stier, $ind, 'da');
		$errors->addError(new Error(2, sprintf($siteContext->getLocale('errDatasourceInaccessible'), $stier->getOption('name_of_service'))));
	}

$lib = new Html($ind,$datafil);
$lib->setStier($stier);

//Write the stat image
if (!$errors->isOccured()) {
	//'billed' is 'image' in danish: The image to display.
	if (! array_key_exists('billed', $ind)) {
		writeImage("stats1.gif",$stier);
	} else {
		//1-8: Normal images. trans: transparent/invisible.
		//sh: Blach/white, hs: White/blach.
		if ($ind['billed'] == "2") writeImage("stats2.gif",$stier);
		elseif ($ind['billed'] == "3") writeImage("stats3.gif",$stier);
		elseif ($ind['billed'] == "4") writeImage("stats4.gif",$stier);
		elseif ($ind['billed'] == "5") writeImage("stats5.gif",$stier);
		elseif ($ind['billed'] == "6") writeImage("stats6.gif",$stier);
		elseif ($ind['billed'] == "7") writeImage("stats7.gif",$stier);
		elseif ($ind['billed'] == "8") writeImage("stats8.gif",$stier);
		elseif ($ind['billed'] == "trans") writeImage("stats_trans.gif",$stier);
		elseif ($ind['billed'] == "taelsh") zipcount(0,$stier,$ind,$datafil);
		elseif ($ind['billed'] == "taelhs") zipcount(1,$stier,$ind,$datafil);
		else writeImage("stats1.gif",$stier);
	}

} else {
	require_once "lib/UsersArea/Utils.php";
	$uaUtils = new UsersAreaUtils($siteContext);
	$uaUtils->showErrors($errors);
	exit;
}

//Ignore this visit on this page?
//'taelop' is danish for count up. nej is danish for no.
if (!$lib->countVisit(getenv("HTTP_REFERER"),$datafil->getLine(111))) {
	$ind['taelop'] = "nej";
}
//Ignore visits from this IP-address?
if ((getenv("REMOTE_ADDR") === $datafil->getLine(52)) and ($datafil->getLine(52) != "")) {
	$ind['taelop'] = "nej";
}

//Update the http vars.
$lib->setHTTPVars($ind);

//Only register visit if the taelop parameter does not forbid it.
if (isset($ind['taelop']) and $ind['taelop'] !== "nej" or !isset($ind['taelop']))
{
	//Do the actual registering of the visit.
	$engine = new ZipStatEngine($lib);

	//Set user settings.
	$engine->setCounterIgnoreQuery($datafil->getUserSetting('ignoreQuery') !== 'false');

	//Process the visit.
	$engine->process($lib->getTimeAdjusted(),
		(isset($ind['ssto']) ? $ind['ssto'] : ''),
		(isset($ind['referer']) ? $ind['referer'] : ''),
		(isset($ind['colors']) ? $ind['colors'] : ''),
		(isset($ind['java']) ? $ind['java'] : ''),
		(isset($ind['taelnr']) ? $ind['taelnr'] : ''),
		(isset($ind['taelnavn']) ? $ind['taelnavn'] : ''),
		(isset($ind['js']) ? $ind['js'] : ''),
		getenv('HTTP_USER_AGENT'),
		getenv('REMOTE_ADDR'),
		getenv('HTTP_ACCEPT_LANGUAGE'),
		getenv('HTTP_REFERER')
	);

}

//Is it time to send the user an e-mail with stats - if the user wants it.
$send = explode("::",$datafil->getLine(67));
$lastMailSend = array_shift($send);

$event = new EventCalculator($lib->getTimeAdjusted());
$found = $event->repeatNow($lastMailSend, $send);
if ($stier->getOption('send_stat_mails') === 0)
	$found = false;

//Yes: Send an e-mail
if ($found === true)
{
	//Decode settings for the requested stat site.
	$parts = explode("&",$datafil->getLine(68));
	$varsForStatSite = array();
	for ($i = 0; $i < sizeof($parts); $i++)
	{
		$keyVal = explode("=",$parts[$i]);
		if (count($keyVal) >= 2)
			$varsForStatSite[$keyVal[0]] = $keyVal[1];
	}

	//Store that the mail has been send before it is send:
	//Worst case: An e-mail is lorst, but we don't risk sending an e-mail
	//that and not storing that it has been send, resulting in spamming the user.
	array_unshift($send, $lib->getTimeAdjusted());
	$datafil->setLine(67,implode("::",$send));
	$datafil->gemFil();

//Create the statsite
	require_once "lib/StatSite.php";
	require_once "lib/LegacyMapper.php";
	require_once "lib/SiteContext.php";
	require_once "lib/Localizer.php";
	
	//Maps old to new parameters
	$statSiteMapper = new StatSiteLegacyMapper();
	$mappedInd = $statSiteMapper->applyMapping(array_merge($varsForStatSite, $ind));

	//Creates a new site context for the stat site.
	$siteContext = new SiteContext($lib, $stier, $mappedInd, 'da');
	
	//Creates the statsite
	$statSite = new StatSite($siteContext, 'text');
	$statSite->setShowStatselector(0);
	$statSite->setSendByMail(true);
	$statSite->setMaxStatsToShow(-1); //Disable bandwidth limitation

	//Generates the html for the statsite.
	$side = $statSite->generateSite();

	@mail($datafil->getLine(2),"Statistikker fra ZIP Stat", $side, "From: zipstat_mailstats@zip.dk\nReply-to: zipstat_mailstats@zip.dk\nX-abuse: postmaster@zip.dk\nX-Mailer: ZIP Stat mailstats\nContent-Type: text/plain");
	//Stat site end

	//Log the sending of the mail
	$fp = fopen("mailsSend.txt","a");
	fwrite($fp, "Mail send ".date('r')." ".$ind['brugernavn'].", adress: ".$datafil->getLine(2)."\n");
	fclose($fp);

}
else
{
	$datafil->gemFil();
}

} //End if is processing mode not enabled
else
{
	//Processing mode: Save the visit in a log file so it can be processed later.
	if (! array_key_exists('billed', $ind)) {
		writeImage("stats1.gif",$stier);
	} else {
		if ($ind['billed'] == "taelsh" and $ind['billed'] == "taelhs")
			$ind['billed'] = "trans";

		if ($ind['billed'] == "2") writeImage("stats2.gif",$stier);
		elseif ($ind['billed'] == "3") writeImage("stats3.gif",$stier);
		elseif ($ind['billed'] == "4") writeImage("stats4.gif",$stier);
		elseif ($ind['billed'] == "5") writeImage("stats5.gif",$stier);
		elseif ($ind['billed'] == "6") writeImage("stats6.gif",$stier);
		elseif ($ind['billed'] == "7") writeImage("stats7.gif",$stier);
		elseif ($ind['billed'] == "8") writeImage("stats8.gif",$stier);
		elseif ($ind['billed'] == "trans") writeImage("stats_trans.gif",$stier);
		elseif ($ind['billed'] == "taelsh") zipcount(0,$stier,$ind,$datafil);
 		elseif ($ind['billed'] == "taelhs") zipcount(1,$stier,$ind,$datafil);
		else writeImage("stats1.gif",$stier);
	}
}

//Register visits collectively?
if ($stier->getOption('collective') === 1) {
	require_once "lib/ZipStatEngine.php";

	$visit = new Visit();
	$visit->setUnique($lib->isVisitUnique(getenv('REMOTE_ADDR')) ? 1 : 0);
	$visit->setTime($lib->getTimeAdjusted());
	$visit->setBrowser(ZipStatEngine::short_browser(getenv('HTTP_USER_AGENT')));
	$visit->setOs(ZipStatEngine::platform(getenv('HTTP_USER_AGENT')));
	$visit->setResolution((isset($ind['ssto']) ? $ind['ssto'] : '')); //Todo: Test for correct syntax
	$visit->setColorDepth((isset($ind['colors']) ? $ind['colors'] : ''));
	
	if (isset($ind['java']) and strlen($ind['java']) > 0) {
		$visit->setJavaEnabled($ind['java'] === "true" ? 1 : 0);
	} else {
		$visit->setJavaEnabled(2);
	}
	
	if (isset($ind['js']) and strlen($ind['js']) > 0) {
		$visit->setJavaScriptEnabled($ind['js'] === "true" ? 1 : 0);
	} else {
		$visit->setJavaScriptEnabled(2);
	}

	//Only set if the engine exists	
	if ($engine !== NULL) {
		$visit->setSearchEngine($engine->getLatestSearchEngine());
		$visit->setSearchWords($engine->getLatestSearchWords());
		$visit->setLanguage($engine->getLatestPrefLanguage());
		$visit->setTopdom($engine->getLatestTopdom());
	}

	$writer = $datafil->getWriter();
	$writer->logVisitCollectively($visit);
}


///////////////////


/**
 * Generates and displays a counter image.
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @param $coltype 0 is b/w and 1 is w/b.
 * @param $stier   an instance of Stier.
 * @param $ind     the http input parameters the script was given.
 * @param $datafil the current datasource with the users data.
 * @return void
 */
function zipcount($coltype,$stier,$ind,$datafil) {
	if (isset($ind['taelnr']) and  $ind['taelnr'] > 0 and (! isset($ind['etael']) or $ind['etael'] === "") and (! isset($ind['ntael']) or $ind['ntael'] === "")) {
		$tmp = explode("::",$datafil->getLine(37));
		$ind['taelnr'] = round($ind['taelnr']);
		$counter = $tmp[$ind['taelnr']] + 1;
	}
	elseif (isset($ind['etael']))
		$counter = $datafil->getLine(13) + 1 + $datafil->getLine(82);
	elseif (isset($ind['ntael']))
		$counter = $datafil->getLine(7) + 1 + $datafil->getLine(82);
	elseif (isset($ind['taelnavn']))
	{
		$tmp = explode("::",$datafil->getLine(38));
		$i = 0;

		while (($i < sizeof($tmp)) and ($tmp[$i] != $ind['taelnavn']))
			$i++;
		$tmp = explode("::",$datafil->getLine(37));
		$counter = $tmp[$i] +1;
	}
	else
	{	//If nothing is given and the file name of the page does not exist in
		//any of the counters
		$counter = $datafil->getLine(7) + 1 + $datafil->getLine(82);
	}

	$tillad = explode("::",$datafil->getLine(106));
	//If the counter image may not be shown:
	if (! $tillad[2]) {
		$counter = "Skjult";
	}

	//$counter = 1234567890;
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Content-type: image/png");
	
	$brede = strlen($counter)*8 +3;

	$im = imagecreate($brede,16);

	if ($coltype === 1)
	{
		$white = ImageColorAllocate($im, 255,255,255);
		$black = ImageColorAllocate($im, 0,0,0);
		$txtCol = $black;
	}
	else
	{
		$black = ImageColorAllocate($im, 0,0,0);
		$white = ImageColorAllocate($im, 255,255,255);
		$txtCol = $white;
	}

	imagestring($im, 4, 0, 0, $counter, $txtCol);
	ImagePng($im);
	ImageDestroy($im);

}

/**
 * Writes an image to the browser.
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @param $imageFile The file name of the image to write.
 * @param $stier instance of the settings class Stier.
 * @return void
 */
function writeImage($imageFile,&$stier)
{
	//return 1; //Uncomment for debugging purposes - then text can be output instead of an image
	header("Content-type: image/gif\n\n");
	$filename = $stier->getSti('zipstat_icons')."/".$imageFile;

	$fd = fopen ($filename, "r");
	$contents = fread ($fd, filesize ($filename));
	fclose ($fd);

	print $contents;
	flush();
}


//$tl->theend();
?>
