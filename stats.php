<?php

/*----Import libs----*/
	require "Html.php";
	require "Stier.php";
	require "lib/SiteContext.php";
	require "lib/StatSite.php";
	require "lib/Localizer.php";
	include "lib/LegacyMapper.php";
	
/*----Init vars----*/
	//Stier og options
	$stier = new Stier();

	//Henter variable udefra
	$ind = Html::setPostOrGetVars($_POST, $_GET);

	//Maps old to new parameters
	$statSiteMapper = new StatSiteLegacyMapper(); // todo: This seems to be not used anymore.
	//$ind = $statSiteMapper->applyMapping(array_merge($varsForStatSite, $ind));

	if (!isset($ind['brugernavn'])) {
		require_once "lib/SiteGenerator/SiteGenerator.php";
		require_once "lib/StatGenerator.php";
		
		//The 2nd parameter is only needed to trick PHP into thinking it gets
		//a 2nd parameter that can be passed as reference.
		$lib = new Html($ind, $ind);
		$siteContext = new SiteContext($lib, $stier, $ind, 'da');
		$lib->setSiteContext($siteContext);
		$lib->setStier($stier);
		//Give the site context to the legasy mapper (which will give itself to
		//the site context.
		$statSiteMapper->setSiteContext($siteContext);

		$sg = SiteGenerator::getGenerator($ind['type'],$siteContext);
		$headline = $sg->newElement("headline");
		$headline->setHeadline($siteContext->getLocale("siteEnterPwdHead"));
		$headline->setSize(1);
		$sg->addElement($headline);
		$text = $sg->newElement("text");
		$text->setText($siteContext->getLocale("siteEnterUsername"));
		$sg->addElement($text);

		$login = $sg->newElement("loginForm");
		$login->setUrl($stier->getOption('urlStatsite'));
		$login->setKeyUsername("brugernavn");
		$login->setKeyPassword("brugerkodeord");
		$login->setSubmitMethod("POST");
		$login->setUsername($ind["brugernavn"]);
		$sg->addElement($login);

		echo $sg->getSite();
		exit;
	}

	//Tjekker brugernavnet
	$datafil = DataSource::createInstance($ind['brugernavn'],$stier);

	//Henter datafilen
	$res = $datafil->hentFil();

	//Instantierer klassen med standardkode
	$lib = new Html($ind,$datafil);

	//Instantierer <code>SiteContext</code>-objektet.
	$siteContext = new SiteContext($lib, $stier, $ind, 'da');

	$lib->setSiteContext($siteContext);
	$lib->setStier($stier);
	
	//Give the site context to the legasy mapper (which will give itself to
	//the site context.
	$statSiteMapper->setSiteContext($siteContext);

	//Sets the code lib in the site context
	$siteContext->setCodeLib($lib);

	//Was the datafile fetched successfully
	$errors = new Errors();
	if ($res === -2) {
		$errors->addError(new ZsError(2, sprintf($siteContext->getLocale('errDamagedDatasource'), $stier->getOption('name_of_service'))));
	} elseif (! $res or $res === 0) {
		$errors->addError(new ZsError(2, sprintf($siteContext->getLocale('errDatasourceInaccessible'), $stier->getOption('name_of_service'))));
	}

	if ($errors->isOccured()) {
		require_once "lib/SiteGenerator/SiteGenerator.php";
		require_once "lib/StatGenerator.php";

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
	
	//Do a password check
	if (! $datafil->getField('statsitePublic'))
	{
		$pwds = explode("::", $datafil->getLine(57)."::pqi.iodngvu4-39w902jf0");
		//Is a valid password given?
                $mainPwOK = $datafil->authenticate($ind['brugernavn'], $ind['brugerkodeord'], 'statsite', 'statsite');

		if ((strlen($ind['brugerkodeord']) === 0 or ! in_array($ind['brugerkodeord'], $pwds)) and ! $mainPwOK)
		{
			require_once "lib/SiteGenerator/SiteGenerator.php";
			require_once "lib/StatGenerator.php";

			$sg = SiteGenerator::getGenerator($ind['type'],$siteContext);
			$headline = $sg->newElement("headline");
			$headline->setHeadline($siteContext->getLocale("siteEnterPwdHead"));
			$headline->setSize(1);
			$sg->addElement($headline);
			$text = $sg->newElement("text");
			$text->setText($siteContext->getLocale("siteEnterPwd"));
			$sg->addElement($text);
			
			$login = $sg->newElement("loginForm");
			$login->setUrl($stier->getOption('urlStatsite'));
			$login->setKeyUsername("brugernavn");
			$login->setKeyPassword("brugerkodeord");
			$login->setSubmitMethod("POST");
			$login->setUsername($ind["brugernavn"]);
			$sg->addElement($login);

                        echo $sg->getSite();
			exit;
		}
	}

	//Instantierer statistiksiden og angiver typen p statistiksiden.
	if (array_key_exists('type' , $ind))
		$statSite = new StatSite($siteContext, $ind['type']);
	else
		$statSite = new StatSite($siteContext, '');

/*----Selve programmet----*/

//Sender headers til browseren om at siden ikke m caches.
Html::outputNoCacheHeaders();

//Genererer HTML'en
$side = $statSite->generateSite();

$statSite->outputHeaders();
//Fortller browseren hvor meget HTML der er.
header("Content-Length: ".strlen($side));

//Sender HTML'en.
echo $side;

?>
