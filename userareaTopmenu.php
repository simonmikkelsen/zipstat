<?php

	require "Html.php";
	require "Stier.php";
	require "lib/Localizer.php";
	require "lib/SiteContext.php";
	require "lib/UsersArea/Utils.php";
	
	$stier = new Stier();
	$in = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

	$dummy_for_lib = NULL;
	                               //$lib
	$siteContext = new SiteContext($dummy_for_lib, $stier, $in, 'da');
	$utils = new UsersAreaUtils($siteContext);

	if (!isset($in['username']))
		$username = '';
	else
		$username = $in['username'];

	$utils->echoSiteHead('ZIP Stat brugeromrde ['.$username.']', 1);
	$utils->echoSiteEnd(0);
	
/*		
		$filename = $stier->getPath("templates")."/HtmlDefault.txt";
		$fd = fopen ($filename, "r");
		$template = fread ($fd, filesize ($filename));
		fclose ($fd);
		$template = substr($template, 0, strpos($template, '%start_footer%')).substr($template, strpos($template, '%end_footer%')+strlen('%end_footer%'));
		$keys = array(
			'%title%',
			'%css_url%',
			'%start_menu%',
			'%end_menu%',
			'%mainSite%',
			'%start_footer%',
			'%end_footer%',
			'%frontpage_url%',
			'%signup_url%',
			'%userarea_url%',
			'%contact_url%',
			'%help_url%');
		$vals = array(
			'ZIP Stat brugeromrde ['.$username.']',	//title
			$stier->getOption('urlCss'),							//css_url
			'',										//start_menu
			'',										//end_menu
			'',										//mainSite
			'',										//start_footer
			'',										//end_footer
			$stier->getOption('ZSHomePage'),					//frontpage_url
			$stier->getOption('urlSignup'),						//signup_url
			$stier->getOption('urlUserArea'),					//userarea_url
			$stier->getOption('urlContact'),					//contact_url
			$stier->getOption('urlHelp'),							//help_url
		);
		echo str_replace($keys,$vals,$template);
*/

?>