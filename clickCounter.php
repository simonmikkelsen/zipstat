<?php

require "Html.php";
require "Stier.php";
require "lib/SiteContext.php";
require "lib/Localizer.php";

$options = new Stier();

//Fetches parameters for the script.
$in = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

//Validates the username.
$datafile = DataSource::createInstance($in['brugernavn'],&$options);

//Creates the standard library.
$lib = new Html(&$in,&$datafile);

//Instantiates the SiteContext-objecet.
$siteContext = new SiteContext(&$lib, &$options, &$in, 'da');

//Fetches the users data.
$res = $datafile->hentFil();

$lib->setSiteContext(&$siteContext);

$lib->setStier(&$options);

//Sets the code lib in the site context.
$siteContext->setCodeLib(&$lib);

if ($res === -2)
	$errMsg .= "Din datafil er desvrre blevet beskadiet, og der kan derfor ikke registreres statistikker. Kontakt ".$options->getOption('name_of_service')."'s administrator via e-mail-adressen nederst p siden.";
elseif (! $res)
	$errMsg .= "Datafilen kunne hentes. Enten er det et problem p ".$options->getOption('name_of_service')." eller ogs har du skrevet det forkerte brugernavn - det kan indeholder tegn der ikke er tilladt - prv at generere den obligatoriske kode igen.";

if (strlen($errMsg) !== 0)
{
	$errMsg = $lib->problemer($errMsg);
	include "view.php";
	$site = new HtmlSite($siteContext, "Fejl");
	$site->addHtml($errMsg);
	echo $site->getSite();
	exit;
}

//Click counter names/numbers 70; clicks 69; url 71.
$cclicksArr = explode("::",$datafile->getLine(69));
$cnameArr = explode("::",$datafile->getLine(70));
$curlArr = explode("::",$datafile->getLine(71));

$pro_max_clickcounters = $lib->pro(10);

if ($in['urlnavn'] and in_array($in['urlnavn'],$cnameArr))
{
	$i = 0;
	$found = 0;
	while (($found === 0) and ($i <= $pro_max_clickcounters))
	{
		if ($cnameArr[$i] === $in['urlnavn'])
		{
			$cclicksArr[$i]++;
			$found = 1;
			$fun = $i;
		}
		else
		{
			$i++;
		}
	} //End of while.
} //End of if $in['urlnavn'].
elseif (($in['urlnr'] <= sizeof($cnameArr)) and ($in['urlnr'] >= 0) and ($in['urlnr'] <= $pro_max_clickcounters))
{
	$cclicksArr[$in['urlnr']]++;
	$fun = $in['urlnr'];
}
else //If not a counter name nor number is found.
{
	$thits[0]++;
}

if ($lib->okurl($curlArr[$fun]))
{
	header("Location: ".$curlArr[$fun]);
}
elseif ($lib->okurl($datafile->getLine(3)))
{
	header("Location: ".$datafile->getLine(3));
}
else
{
	require "view.php";
	require "lib/SiteGenerator/SiteGenerator.php";
	$side = new HtmlSite($siteContext, "Fejl");
	$side->addHtml("<div class=forside><h1>Ingen adresse angivet</h1>Der var desvrre ikke givet en adresse til det angivne adressenummer eller -navn.</p><h2>For besgende</h2>Hvis du er en besgende p denne side, beklager jeg at der ikke var angivetgivet en korrekt adresse. Der er ikke andet at gre, end at trykke p din browsers &quot;tilbage&quot; knap, og trykke p et andet link, da dette desvrre ikke virker.<h2>For sidens ejer</h2>Ejer du siden hvorp linket befandt sig, skal du g ind p brugeromrdet, og vlge funktionen &quot;Adresser&quot;. Der skal du anive en korrekt adresse til det link du har benyttet her.</div>");
	echo $side->getSite();
	exit;
}

$datafile->setLine(69, implode("::",$cclicksArr));
$datafile->setLine(70, implode("::",$cnameArr));

//Skriver datafil, men kun hvis man skal tlles med

if (! $lib->countVisit(getenv('HTTP_REFERER'),$datafile->getLine(111)))
{
	$in['taelop'] = "nej";
}

if ((getenv('REMOTE_ADDR') === $datafile->getLine(52)) and ($datafile->getLine(52) !== ""))
{
	$in['taelop'] = "nej";
}

if (strpos($HTTP_COOKIE_VARS[$in['brugernavn']], "ikkeop") !== false)
{
	$in['taelop'] = "nej";
}

if ($in['taelop'] !== "nej")
	{
		$datafile->gemFil();
	}

?>