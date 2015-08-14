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

	$errors = new Errors();
	if (isset($ind) and isset($username)) {
		//Tjekker brugernavnet
		$datafil = DataSource::createInstance($username, $stier);

		//Henter datafilen
		$res = $datafil->hentFil();

		//Temporarely instance for error handling
		$siteContext = new ShortSiteContext($stier, $ind, 'da');
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

	//Set the cookie for users area type
	$uaUtils = new UsersAreaUtils($siteContext);
	$uaUtils->setUAType();

	if ((! isset($ind)) or (! isset($password)) or (! isset($username)))
	{
		$uaUtils->doLoginForm($lib, $stier, $ind, $siteContext, 1);
		exit;
	} else if (!$datafil->authenticate($username, $password)) {
		$uaUtils->doLoginForm($lib, $stier, $ind, $siteContext, 2);
		exit;
	} else if ($errors->isOccured()) {
		//$uaUtils = new UsersAreaUtils($siteContext);
		$uaUtils->showErrors($errors);
		exit;
	}
	
	//If the stat site is password protected
	if (strlen($datafil->getLine(57)) > 0) {
		$brugerkodeord = "&amp;brugerkodeord=".$ind['password']."&amp;menu=hide&amp;tableWidth=100%25";
		$statside_offentlig = "";
		$targetTop = " target=\"main\"";
	} else {
		$brugerkodeord = '';
		$statside_offentlig = "Statistiksiden er offentlig <a href=\"javascript:alert('Din statistikside er lige nu offentlig for alle.\\nDu kan dog stte kodeord p den,\\ns kun du kan se den.\\nDette gres p siden Indstillinger\\nher p brugeromrdet.');\">Ls mere</a>";
		$targetTop = " target=\"_top\"";
	}


$uaUtils = new UsersAreaUtils($siteContext);
//Get the users area type
if ($uaUtils->getUAType() === $uaUtils->UA_TYPE_SIMPLE)
	$simpel = 1;
else
	$simpel = 0;

//Set a new type?
if (isset($ind['skift'])) {
	if ($ind['skift'] === 'avanceret') {
		$uaUtils->setUAType($uaUtils->UA_TYPE_ADVANCED);
		$simpel = 0;
	} else {
		$uaUtils->setUAType($uaUtils->UA_TYPE_SIMPLE);
		$simpel = 1;
	}
}
###########################

if ($simpel)
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang=dk>
<head>
	<title>Administrationsmenu</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta http-equiv="Title" CONTENT="Administrationsmenu">
	<meta name="robots" content="none,noindex,follow">
	<meta http-equiv="expires" content="sat, 01 jan 1980 12:35:00 gmt">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-store">
	<meta name="MSSmartTagsPreventParsing" content="TRUE">
	<LINK REL=STYLESHEET TYPE="text/css" HREF="<?php echo $stier->getOption('urlCss'); ?>" TITLE="ZIP">
</head>
<body>

<div class=forside><h3>Rediger</h3>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=roplysninger"; ?>" target="main">Oplysninger</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rindstillinger"; ?>" target="main">Indstillinger</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=remailstats"; ?>" target="main">Mail stats</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rtaellere"; ?>" target="main">Tllere</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rnulstil"; ?>" target="main">Nulstil</A><br>
</div>
<p>
<div class=forside><h3>Lav kode</h3>
	<a href="<?php echo $stier->getOption('urlUserAreaCodegen')."?username=$username&amp;password=$password&amp;type=Obligatorisk+kode"; ?>" target="main">Obligatorisk</A><br>
</div>
<p>
<div class=forside><h3>Vis</h3>
	<a href="<?php echo $stier->getOption('urlStatsite')."?username=$username&amp;show[]=all$brugerkodeord"; ?>"$targetTop>Statistik</A><br>
	<?php if (isset($statside_offentlig)) { echo $statside_offentlig; } ?>
</div>
<p>
<div class=underh>[Simpel visning]<br>
<a href="<?php echo $stier->getOption('urlUserAreaLeftmenu')."?username=$username&amp;password=$password&amp;skift=avanceret"; ?>" class=underh>Skift til avanceret visning</a>
</div>

</body>
</html>
<?php

	}
else //If advanced
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang=dk>
<head>
	<title>Administrationsmenu</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta http-equiv="Title" CONTENT="Administrationsmenu">
	<meta name="robots" content="none,noindex,follow">
	<meta http-equiv="expires" content="sat, 01 jan 1980 12:35:00 gmt">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-store">
	<LINK REL=STYLESHEET TYPE="text/css" HREF="<?php echo $stier->getOption('urlCss'); ?>" TITLE="ZIP">
</head>
<body>

<div class=forside><h3>Rediger</h3>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=roplysninger"; ?>" target="main">Oplysninger</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rindstillinger"; ?>" target="main">Indstillinger</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=remailstats"; ?>" target="main">Mail stats</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rzipklik"; ?>" target="main">Kliktllere</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rtaellere"; ?>" target="main">Tllere</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rspoergsmaal"; ?>" target="main">Sprgsml</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rnulstil"; ?>" target="main">Nulstil</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=rkodeord"; ?>" target="main">Kodeord</A><br />
</div>
<p>
<div class=forside><h3>Lav kode</h3>
	<a href="<?php echo $stier->getOption('urlUserAreaCodegen')."?username=$username&amp;password=$password&amp;type=Obligatorisk+kode"; ?>" target="main">Obligatorisk</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaCodegen')."?username=$username&amp;password=$password&amp;type=Sprgsml+kode"; ?>" target="main">Sprgsml</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaCodegen')."?username=$username&amp;password=$password&amp;type=zipklik_vis"; ?>" target="main">Kliktller</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaCodegen')."?username=$username&amp;password=$password&amp;type=vis_js_kode"; ?>" target="main">JavaScript-stats</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaCodegen')."?username=$username&amp;password=$password&amp;type=Statistik-panel+kode"; ?>" target="main">Ministatistik</A><br>
</div>
<p>
<div class=forside><h3>Vis</h3>
	<a href="<?php echo $stier->getOption('urlStatsite')."?brugernavn=$username&amp;show[]=all$brugerkodeord\"$targetTop"; ?>>Statistik</A><br>
	<a href="<?php echo $stier->getOption('urlStatsite')."?brugernavn=$username&amp;show[]=all$brugerkodeord&amp;type=text\"$targetTop"; ?>>Statistik (ren tekst)</A><br>
	<a href="<?php echo $stier->getOption('urlStatsite')."?brugernavn=$username&amp;show[]=all$brugerkodeord&amp;type=csv\"$targetTop"; ?>>Statistik (regneark)</A><br>
	<a href="<?php echo $stier->getOption('urlUserAreaMain')."?username=$username&amp;password=$password&amp;type=backup"; ?>" target="main">Backup</A><br />
	<?php if (isset($statside_offentlig)) { echo $statside_offentlig; } ?>
</div>
<p>
<div class=underh>[Avanceret visning]<br>
<a href="<?php echo $stier->getOption('urlUserAreaLeftmenu')."?username=$username&amp;password=$password&amp;skift=simpel"; ?>" class=underh>Skift til simpelt visning</a>
</div>

</body>
</html>

<?php
} //End if simple else

?>