<?php
//52 - IP adr to ignore
//53 - redirect adress

require "Html.php";
require "Stier.php";
require "lib/SiteContext.php";
require "lib/UsersArea/Utils.php";

$stier = new Stier();

$datafil = DataSource::createInstance($_SERVER['QUERY_STRING'], $stier);

$res = $datafil->hentFil();

$problemer = ''; //No problems so far
if ($res === -2)
	$problemer .= "Din datafil er desvrre blevet beskadiet, og der kan derfor ikke registreres statistikker. Kontakt ".$stier->getOption('name_of_service')."'s administrator via e-mail-adressen nederst p siden.";
elseif (! $res)
	$problemer .= "Datafilen kunne hentes. Enten er det et problem p ".$stier->getOption('name_of_service')." eller ogs har du skrevet det forkerte brugernavn - det kan indeholder tegn der ikke er tilladt - prv at generere den obligatoriske kode igen.";

$lib = new Html($ind,$datafil);
$lib->setStier($stier);

Html::outputNoCacheHeaders();

if (strlen($problemer) === 0) {
	$datafil->setLine(52, getenv('REMOTE_ADDR'));
	$datafil->gemFil();

	$url = trim($datafil->getLine(53));
	
	if (strlen($url) > 0) {
		if (strpos(strtolower($url), 'http://') !== 0 and strpos(strtolower($url), 'https://') !== 0)
			$url = "https://" . $url;
		header('Location: '.$url);
	} else {
		$ind = Html::setPostOrGetVars($_POST, $_GET);
		$siteContext = new SiteContext($lib, $stier, $ind, 'da');
		$utils = new UsersAreaUtils($siteContext);
		$utils->echoSiteHead("Ingen adresse angivet", 1);
		echo "Der var ikke angivet nogen adresse. Dette skal gres under &quot;Rediger Indstillinger&quot; (i kassen &quot;Send-vidre adresse&quot;) p brugeromrdet.";
		$utils->echoSiteEnd();
	}
}

?>
