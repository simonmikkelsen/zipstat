<?php

#Datafile:
#0-Tom
#1-Navn
#2-E-mail
#&3-URL
#&4-Tittel
#&5-Startdato
#6-Password

#Variable
require "lib/SiteGenerator/SiteGenerator.php";
require "lib/SiteContext.php";
require "Stier.php";
require "Html.php";
require "view.php";

//Program
$stier = new Stier();

$ind = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

//Tjekker brugernavnet
$datafil = DataSource::createInstance($ind['username'],$stier);

$res = $datafil->hentFil();

$lib = new Html($ind, $res);
$siteContext = new SiteContext($lib, $stier, $ind, 'da');

if (! isset($ind['type'])) {
	if (isset($ind['username'])) {
		$username = $ind['username'];
	} else {
		$username = "";
	}
	$side = new HtmlSite($siteContext, "Glemt kodeord");
	$html = "<div class=forside>\n";
	$html .= "	<form action='".htmlentities(getenv("SCRIPT_NAME"))."' method='POST'>\n";
	$html .= "		<p><label>Brugernavn <input type='text' name='username' value='".htmlentities($username)."'/></label></p>\n";
	$html .= "		<p><input type='submit' value='Send kodeord' /></p>\n";
	$html .= "		<input type='hidden' name='type' value='mailpwd' />\n";
	$html .= "	</form>\n";
	$html .= "	<h2>Glemt brugernavn</h2>\n";
	$html .= "	<p>Du kan let finde dit brugernavn:</p>\n";
	$html .= "		<ol>\n";
	$html .= "			<li>�ben en side p� din webside, som har ZIP Stats kode.</li>\n";
	$html .= "			<li>H�jreklik og v�lg &quot;Vis kilde&quot;.</li>\n";
	$html .= "			<li>Find ZIP Stats kode: Dit brugernavn st�r der flere steder lige efter teksten <code>brugernavn=</code></li>\n";
	$html .= "		</ol>\n";
	$html .= "</div>\n";
	$side->addHtml($html);
	echo $side->getSite();
	exit;
}

$problemer = "";
//Can we send mail?
if ($siteContext->getOption('send_mail') === 0) {
	$problemer .= "<li>".$stier->getOption('name_of_service')." kan desv�rre ikke sende e-mails p� nuv�rende tidspunkt. Kontakt venligst administratoren (se kontaktsiden), s� vedkommende kan sende dig dit kodeord. Husk at sende dit brugernavn med!";
}

if ($res === -2)
	$problemer .= "Din datafil er desv�rre blevet beskadiet, og der kan derfor ikke registreres statistikker. Kontakt ".$stier->getOption('name_of_service')."'s administrator via e-mail-adressen nederst p� siden.";
elseif (! $res)
	$problemer .= "Datafilen kunne hentes. Enten er det et problem p� ".$stier->getOption('name_of_service')." eller ogs� har du skrevet det forkerte brugernavn - det kan indeholder tegn der ikke er tilladt - pr�v at generere den obligatoriske kode igen.";

if (strlen($problemer) === 0) {
	if (Html::okmail($datafil->getLine(2)))
	{
		mailpwd($datafil, $stier);
	}
	else
	{
		$problemer .= "<LI>Den e-mail adresse der er opgivet ved din registrering er ikke en gyldig e-mail adresse, og kodeordret kan derfor af sikkerhedsgrunde ikke sendes.";
	}
}

if (strlen($problemer) > 0)
{
	$side = new HtmlSite($siteContext, "Fejl");
	$side->addHtml("<div class=problemer>$problemer</div>");
	echo $side->getSite();
	exit;
}
else
{
	$side = new HtmlSite($siteContext, "Dit kodeord er sendt");
	$side->addHtml("<div class=forside>Dit kodeord er nu afsendt. Gem mailen med det, eller skriv det ned. Hvis du glemmer dit kodeord igen, skal du dog v�re velkommen til at sende bud efter det igen. N�, men mailen skulle v�re der nu!</div>");
	echo $side->getSite();
	exit;
}

/**
 * Sends the pwd.
 * 
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @param $datafile an instance of {@link Datafil}.
 * @param $settings an instance of {@link Stier}.
 * @returns void
 */
function mailpwd($datafile, $settings)
{
	
$mail='Hej
Du har bedt om at f� tilsendt dit kodeord til '.$settings->getOption('name_of_service').'.
Dit brugernvn er: '.$datafile->getUsername().'
Dit kodeord   er: '.$datafile->getLine(6).'

Husk det nu ;-)

-- 
Mvh. '.$settings->getOption('adminName').', '.$settings->getOption('name_of_service').'

Husk ogs� www.ZIP.dk - alt godt til hjemmesiden...
';

mail($datafile->getLine(2), "Dit kodeord til ".$settings->getOption('name_of_service'), $mail,
	"From: ".$settings->getOption('adminEMail')."\nReply-To: ".$settings->getOption('adminEMail')."\n");
}

?>
