<?php
	require "Html.php";
	require "Stier.php";
	require "lib/Localizer.php";
	require "lib/SiteContext.php";
	require "lib/UsersArea/Utils.php";
	
	$stier = new Stier();
	$in = Html::setPostOrGetVars($_POST, $_GET);

	$dummy_for_lib = NULL;
	                               //$lib
	$siteContext = new SiteContext($dummy_for_lib, $stier, $in, 'da');
	$utils = new UsersAreaUtils($siteContext);

	$utils->echoSiteHead('ZIP Stat brugerområde', 1);
	$utils->echoSiteEnd(0);
?>
