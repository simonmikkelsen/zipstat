<?php

/*----Import libs----*/
	require "Html.php";
	require "Stier.php";
	require "lib/SiteContext.php";
	require "lib/StatSite.php";
	require "lib/Localizer.php";
	
/*----Init vars----*/
	//Stier og options
	$stier = new Stier();

	//Henter variable udefra
	$ind = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

	//Tjekker brugernavnet
	$collReader = DataSource::createCollectiveReader($stier);

	//Instantierer klassen med standardkode
	$datasource = DataSource::createCollectiveReader($stier);
	$lib = new Html($ind, $datasource);

	//Instantierer <code>SiteContext</code>-objektet.
	$siteContext = new SiteContext($lib, $stier, $ind, 'da');

	$lib->setSiteContext($siteContext);
	$lib->setStier($stier);

	//Sets the code lib in the site context
	$siteContext->setCodeLib($lib);

	//Was the datafile fetched successfully
/*	$errors = new Errors();
	if ($res === -2) {
		$errors->addError(new ZsError(2, sprintf($siteContext->getLocale('errDamagedDatasource'), $stier->getOption('name_of_service'))));
	} elseif (! $res or $res === 0) {
		$errors->addError(new ZsError(2, sprintf($siteContext->getLocale('errDatasourceInaccessible'), $stier->getOption('name_of_service'))));
	}
	if ($errors->isOccured()) {
		require_once dirname(__FILE__)."/lib/SiteGenerator/SiteGenerator.php";
		require_once dirname(__FILE__)."/lib/StatGenerator.php";

		$sg = SiteGenerator::getGenerator($ind['type'], $siteContext);
		$headline = $sg->newElement("headline");
		$headline->setHeadline($siteContext->getLocale('errAnErrorOccured'));
		$headline->setSize(1);
		$sg->addElement($headline);

		$errorList = $errors->getErrors();
		foreach ($errorList as $error) {
			$text = $sg->newElement("text");
			$text->setText($error->getMessage());
			$sg->addElement($text);
		}

		echo $sg->getSite();
		exit;
	}
*/
	
//Make a general useable stat request
$unique = 0; //For testing only
$startTime = 0; //For testing only
$endTime = time(); //For testing only
$statReq = new CollectiveStatRequest('', $unique, $startTime, $endTime);
//Currently this class only supports data from total
$statReq->setGroupby('total');
//Instantierer statistiksiden og angiver typen p� statistiksiden.
if (array_key_exists('type' , $ind))
	$statSite = new CollectiveStatSite($siteContext, $ind['type']);
else
	$statSite = new CollectiveStatSite($siteContext, '');

/*----Selve programmet----*/

//Sender headers til browseren om at siden ikke m� caches.
$lib->outputNoCacheHeaders();

//Genererer HTML'en
$side = $statSite->generateSite($statReq);

$statSite->outputHeaders();
//Fort�ller browseren hvor meget HTML der er.
header("Content-Length: ".strlen($side));

//Sender HTML'en.
echo $side;

?>
