<?php
	require "Html.php";
	require "Stier.php";
	require "lib/Localizer.php";
	require "lib/SiteContext.php";
	require "lib/UsersArea/Utils.php";

	//Stier og options
	$stier = new Stier();

	//Henter variable udefra
	$ind = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);
	
	if (isset($ind['username']))
		$username = $ind['username'];
	if (isset($ind['password']))
		$password = $ind['password'];

	//Temporarely instance for error handling
	$siteContext = new ShortSiteContext($stier, $ind, 'da');

	$errors = new Errors();
	if (isset($ind) and isset($username)) {
		//Tjekker brugernavnet
		$datafil = DataSource::createInstance($username, $stier);

		//Henter datafilen
		$res = $datafil->hentFil();

		//Was the datafile fetched successfully
		if ($res === -2) {
			$errors->addError(new Error(2, sprintf($siteContext->getLocale('errDamagedDatasource'), $stier->getOption('name_of_service'))));
		} elseif (! $res or $res === 0) {
			$errors->addError(new Error(2, sprintf($siteContext->getLocale('errDatasourceInaccessible'), $stier->getOption('name_of_service'))));
		}
	}
	
	//Instantierer klassen med standardkode
	$lib = new Html($ind,$datafil);

	$siteContext = new SiteContext($lib, $stier, $ind, 'da');
	$lib->setSiteContext($siteContext);

	$lib->setStier($stier);

	if ((! isset($ind)) or (! isset($password)) or (! isset($username))) {
		$uaUtils = new UsersAreaUtils($siteContext);
		$uaUtils->doLoginForm(1, $stier->getOption('urlUserArea'));
		exit;
	} else if (!$datafil->authenticate($username, $password)) {
		$uaUtils = new UsersAreaUtils($siteContext);
		$uaUtils->doLoginForm(2, $stier->getOption('urlUserArea'));
		exit;
	} else if ($errors->isOccured()) {
		$uaUtils = new UsersAreaUtils($siteContext);
		$uaUtils->showErrors($errors);
		exit;
	}
	
	//Find out what page to start with
	if (isset($ind['start']) and isset($ind['start_type'])) {
		if ($ind['start_type'] === "adminmain")
			$main = $stier->getOption('urlUserAreaMain')."?username=".$username."&amp;password=".$password."&amp;type=".$ind['start'];
		elseif ($ind['start_type'] === "kodegen")
			$main = $stier->getOption('urlUserAreaCodegen')."?username=".$username."&amp;password=".$password."&amp;type=".$ind['start'];
		else
			$main = $stier->getOption('urlUserAreaMain')."?username=".$username."&password=".$password;
	}	else {
		$main = $stier->getOption('urlStatsite')."?brugernavn=".$username."&amp;brugerkodeord=".$password."&amp;enkeltstat=vis&amp;prognoser=vis&amp;menu=hide&amp;tabelWidth=100%25&amp;show%5B0%5D=BasicStats&amp;show%5B0%5D=Projection";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
        "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
	<title>[<?php echo $username;?>] - Administration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta name="robots" content="none,noindex,follow">
	<meta http-equiv="expires" content="sat, 01 jan 1980 12:35:00 gmt">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-store">

<FRAMESET FRAMEBORDER="0" FRAMESPACING="0" BORDER="0" ROWS="62,*" COLS="100%">
	<FRAME SRC="<?php echo htmlentities($stier->getOption('urlUserAreaTopmenu'))."?username=".htmlentities(urlencode($username))."&amp;password=".htmlentities(urlencode($password)); ?>" NAME="top" SCROLLING="NO">
	<FRAMESET COLS="20%,*">
		<FRAME SRC="<?php echo htmlentities($stier->getOption('urlUserAreaLeftmenu'))."?username=".htmlentities(urlencode($username))."&amp;password=".htmlentities(urlencode($password)); ?>" NAME="menu" SCROLLING="AUTO">
		<FRAME SRC="<?php echo $main; ?>" NAME="main" SCROLLING="AUTO">
	</FRAMESET>
</FRAMESET>

</head>
<body>
	<noframes>
		<h3>Menufaciliteten er kun tilgngelig i browsere der understtter frames.<h3>
		<p>Men bare rolig. Du kan flge nedenstende link til menuen.</p>
		<a href="<?php echo htmlentities($stier->getOption('urlUserAreaLeftmenu'))."?username=".htmlentities(urlencode($username))."&amp;password=".htmlentities(urlencode($password)); ?>">G til menuen...</a>
	</noframes>
</body>
</html>
<?php
exit;


?>
