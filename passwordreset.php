<?php

usleep(rand(80, 150) * 1000); // Make automation more painfull.

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
require "lib/authorize.php";
require "Stier.php";
require "Html.php";
require "view.php";

//Program
$stier = new Stier();

//$ind = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);
$ind = $_REQUEST;

if (isset($ind['username'])) {
  $lib = new Html($ind, $datafil);
} else {
  $nop = '';
  $lib = new Html($ind, $nop);
}
$siteContext = new SiteContext($lib, $stier, $ind, 'da');
if (isset($ind['token'])) {
  $token = $ind['token'];
  $authFactory = new AuthorizeFactory($stier);
  $auth = $authFactory->create();
  $username = $auth->isPasswordResetTokenValid($token);
  if ($username !== null and $username !== "") {
     if (isset($ind['pwd1'])) {
       if (! isset($ind['pwd2'])) {
         $message = "Du skal indtaste kodeordet i begge felter. Tryk p&aring; browserens tilbageknap og pr&oslash; igen.";
       } else if ($ind['pwd1'] !== $ind['pwd2']) {
         $message = "Du skal indtaste det samme kodeord i begge felter. Tryk p&aring; browserens tilbageknap og pr&oslash; igen.";
       } else if ($ind['pwd1'] == "") {
         $message = "Kodeordet m&aring; ikke v&aelig;re tomt. Tryk p&aring; browserens tilbageknap og pr&oslash; igen.";
       } else {
         $auth->updatePasswordHash($username, $ind['pwd1']);
         $auth->invalidateToken($token);
         $datafil = DataSource::createInstance($username,$stier);
         $res = $datafil->hentFil();
         $datafil->dataArray[6] = ""; // Remove old cleartext password
         $datafil->gemFil();
       }
       $side = new HtmlSite($siteContext, "Opdater kodeord");
       $message = "Dit kodeord er opdateret.";
       $side->addHtml($message);
       echo $side->getSite();
       exit;
     } else {
       showResetForm($siteContext, $username, $token);
     }
     exit;
 } else {
    $message = "Linket til at nulstille dit kodeord er ikke gyldigt mere. Det fungere i en time fra det tidspunkt det bliver oprettet. Du er n&oslash;d til at bede om at nulstille dit kodeord igen.";
    $side = new HtmlSite($siteContext, "Link ikke gyldigt mere");
    $side->addHtml($message);
    echo $side->getSite();
    exit;
  }
} else if (! isset($ind['type']) or ! isset($ind['username'])) {
	if (isset($ind['username'])) {
		$username = $ind['username'];
	} else {
		$username = "";
	}
	$side = new HtmlSite($siteContext, "Glemt kodeord");
	$html = "<div class=forside>\n";
	$html = "<p>Hvis du har glemt dit kodeord kan du f&aring; det tilsendt en e-mail med instruktioner i at nulstille det. Fordi kodeordet opbevares sikkert, er det ikke muligt at sende det.</p>";
	$html .= "	<form action='".htmlentities(getenv("SCRIPT_NAME"))."' method='POST'>\n";
	$html .= "		<p><label>Brugernavn <input type='text' name='username' value='".htmlentities($username)."'/></label></p>\n";
	$html .= "		<p><input type='submit' value='Nulstil kodeord' /></p>\n";
	$html .= "		<input type='hidden' name='type' value='mailpwd' />\n";
	$html .= "	</form>\n";
	$html .= "	<h2>Glemt brugernavn</h2>\n";
	$html .= "	<p>Du kan let finde dit brugernavn:</p>\n";
	$html .= "		<ol>\n";
	$html .= "			<li>&Aring;ben en side p&aring; din webside, som har ZIP Stats kode.</li>\n";
	$html .= "			<li>H&oslash;jreklik og v&aelig;lg &quot;Vis kilde&quot;.</li>\n";
	$html .= "			<li>Find ZIP Stats kode: Dit brugernavn s&aring;r der flere steder lige efter teksten <code>brugernavn=</code></li>\n";
	$html .= "		</ol>\n";
	$html .= "</div>\n";
	$side->addHtml($html);
	echo $side->getSite();
	exit;
}

$username = $ind['username'];

if ($res === -2 or ! $res) {
	$message = "Det opstod en intern fejl. Kontakt ".$stier->getOption('name_of_service')."'s administrator via e-mail-adressen nederst p&aring; siden.";
	$side = new HtmlSite($siteContext, "Der opstod en fejl");
	$side->addHtml($message);
	echo $side->getSite();
	exit;
}

$b = openssl_random_pseudo_bytes(128);
$token = base64_encode($b);

$authFactory = new AuthorizeFactory($stier);
$auth = $authFactory->create();
$auth->createPwResetRequest($username, $token);

$email = $datafil->getLine(2);
mailReset($email, $stier, $token, $siteContext);

/*
	$side = new HtmlSite($siteContext, "Dit kodeord er sendt");
	$side->addHtml("<div class=forside>Dit kodeord er nu afsendt. Gem mailen med det, eller skriv det ned. Hvis du glemmer dit kodeord igen, skal du dog være velkommen til at sende bud efter det igen. Nå, men mailen skulle være der nu!</div>");
	echo $side->getSite();
	exit;
*/

function showResetForm($siteContext, $username, $token) {
  $message = '
  <p>V&aelig;lg et nyt kodeord til kontoen med brugernavn '.htmlentities($username).'. Et godt tip: Brug kodeord p&aring; mindst 8 tegn, men gerne 10. Brug b&aring;de store og sm&aring; bogstaver samt tal og specialtegn. &AElig;, &oslash; og &aring; kan give problemer hvis du skifter computer eller browser.</p>
<p>Indtast det samme kodeord i begge felter:</p>
<form action="passwordreset.php" method="POST">
<input type="hidden" name="token" value="'.htmlentities($token).'">
<table>
  <tr><td>F&oslash;rste gang:</td><td><input type="password" name="pwd1"></td></tr>
  <tr><td>Anden gang</td><td><input type="password" name="pwd2"></td></tr>
  <tr><td></td><td><input type="submit" value="Gem nyt kodeord"></td></tr>
</table>
</form>';
  $side = new HtmlSite($siteContext, "V&aelig;lg nyt kodeord");
  $side->addHtml($message);
  echo $side->getSite();
  exit;
}

function mailReset($email, $settings, $resetToken, $siteContext)
{
	if (Html::okmail($email)) {
                $resetUrl = $settings->getOption('urlMain')."/passwordreset.php?token=".urlencode($resetToken);
$mail='Hej
Du, eller muligvis en anden, har bedt om at få kodeordet nulstillet til en ZIP Stat konto.

For at nulstille kodeordet skal du benytte dette link:
'.$resetUrl.'
Linket vil virke 1 time efter denne mail blev sendt, hvorefter det er n�dvendigt at bede
om en ny mail.

Hvis du ikke har bedt om at f� nulstillet dit kodeord, såse venligst bort fra denne mail.
Det er kun dig som har mulighed for at nulstille kodeordet.

-- 
Mvh. '.$settings->getOption('adminName').', '.$settings->getOption('name_of_service').'
';

	mail($email, "Nulstil kodeord til ".$settings->getOption('name_of_service'), $mail,
		"From: ".$settings->getOption('adminEMail')."\nReply-To: ".$settings->getOption('adminEMail')."\n");
	} else {
	   usleep(rand(80, 300) * 1000); // Make timing attacks harder.
	}
	$message = "Der er sendt en e-mail til indehaveren af brugernavnet, med instruktioner i at nulstille kodeordet. Hvis du ikke modtager en e-mail kan det v&aelig;re, at brugernavnet ikke findes eller at det er registreret med en anden e-mail-adresse. Indtil nulstilningen er gennemf&oslash;rt vil det gamle kodeord fortsat virke.";
	$side = new HtmlSite($siteContext, "Der er sendt en mail");
	$side->addHtml($message);
	echo $side->getSite();
	exit;

}

?>
