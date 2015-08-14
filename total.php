<?php

/*----Import libs----*/
	require_once dirname(__FILE__)."/Html.php";
	require_once dirname(__FILE__)."/Stier.php";
	require_once dirname(__FILE__)."/lib/SiteContext.php";
	require_once dirname(__FILE__)."/lib/StatSite.php";
	require_once dirname(__FILE__)."/lib/Localizer.php";

/*----Init vars----*/
	//Paths and options.
	$settings = new Stier();

	//Get site parameters.
	$http_vars = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

	//Instantierer klassen med standardkode
	$datasource = DataSource::createCollectiveReader($settings);
	$lib = new Html($http_vars, $datasource);

	//Make a SiteContext (in da=danish)
	$siteContext = new SiteContext($lib, $settings, $http_vars, 'da');

	$lib->setSiteContext($siteContext);
	$lib->setStier($settings);

	//Sets the code lib in the site context
	$siteContext->setCodeLib($lib);
	
//Show index or stat site?
$dateInfo = Html::getDateFromPathinfo();
if (count($dateInfo) === 0) {
	//instantiate the stat site with the default type.
	$indexSite = new CollectiveIndex($siteContext, '');

	//Generate HTML
	$pageHtml = $indexSite->generateSiteCached();

	$indexSite->outputHeaders();
	//Tell the browser much there is.
	header("Content-Length: ".strlen($pageHtml));

	//And send it.
	echo $pageHtml;
	exit(0);
} else {
	//Make a general useable stat request.
	$unique = 0; //Show non unique visits.
	
	$startTime = $dateInfo['time'];
	$endTime = $dateInfo['end'];
	
	$statReq = new CollectiveStatRequest('', $unique, $startTime, $endTime);
	//Currently this class only supports data from total
	$statReq->setGroupby('total');
	
	//instantiate the stat site with the default type.
	$statSite = new CollectiveStatSite($siteContext, '');

	//Generate HTML
	$pageHtml = $statSite->generateSiteCached($statReq);

	$statSite->outputHeaders();
	//Tell the browser much there is.
	header("Content-Length: ".strlen($pageHtml));

	//And send it.
	echo $pageHtml;
	exit(0);
}
?>