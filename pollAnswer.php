<?php

//Data file format
//41-Questions
//42-Anwsers n::n::n::n::n,,n::n::n::n::n (...)
//43-Number of anwsers n::n::n::n::n,,n::n::n::n::n (...)

#QUERY_STRING
#spNsvN


require "Html.php";
require "Stier.php";
require "lib/SiteContext.php";
require "lib/UsersArea/Utils.php";

//Stier og options
$stier = new Stier();

//Henter variable udefra
$ind = Html::setPostOrGetVars($_POST, $_GET);

if (isset($ind['brugernavn']))
	$username = $ind['brugernavn'];
else
	$username = '';

//Load the data source
$datafil = DataSource::createInstance($username, $stier);
$datafil->hentFil();

//Instantierer klassen med standardkode
$lib = new Html($ind,$datafil);

$siteContext = new SiteContext($lib, $stier, $ind, 'da');
$lib->setSiteContext($siteContext);

$lib->setStier($stier);

//spIsvN
$svar = explode(",,",$datafil->getLine(43));
$spoer = explode("::",$datafil->getLine(41));

$svaret = $ind['svaret'];

//Must we receive anwsers from this site?
$problemer = '';
if (!$lib->countVisit(getenv('HTTP_REFERER'),$datafil->getLine(111))) {
	$problemer .= "Sidens ejer har angivet, at der ikke m registreres statistikker eller svar fra andre sider end en rkke angivne sider, og denne side er ikke en af dem. Derfor er dette svar <em>ikke</em> registreret.<br>Sidens ejer kan inkludere denne side ($ENV{'HTTP_REFERER'}) i de tilladte sider, ved at logge ind p brugeromrdet p <a href=\"$options{'ZSHomePage'}\">$options{'ZSHomePage'}c</a> og g til siden &quot;Indstillinger&quot;, hvorp der kan opgives en rkke sider hvorfra der m registreres statistikker og svar.";
}

$pro_max_sp = $lib->pro(3);
$pro_max_sv = $lib->pro(4);
for ($i = 0; $i < $pro_max_sp; $i++) {
	$j = $i+1; //Question number
	$sv = explode("::", $svar[$i]); //The anwsers in the questions
	for ($n = 0; $n < $pro_max_sv; $n++) {
		$m = $n+1; //Anwser number
		//Fill indexes with no value present
		if (! isset($sv[$n]) or strlen($sv[$n]) === "") {
			$sv[$n] = 0;
		}
		if ($ind["sp$j"] == "$m") {
			if (($_COOKIE[$ind['brugernavn']."sp".$i] == strlen($spoer[$i])) and strlen($spoer[$i]) > 0) {
				$fsvaret .= "sprgsml nr. $j, ";
			} else {
				//Count the anwser up
				$sv[$n]++;
			}
			if (strpos($svaret, "sp".$i) === FALSE) {
				$svaret .= "sp$i";
			}
			setcookie($ind['brugernavn']."sp".$i, strlen($spoer[$i]), time()+30*24*3600, "/", $stier->getOption('domain'));
			}
		}
	$svar[$i] = implode("::", $sv);
	}
$datafil->setLine(43, implode(",,", $svar));
$datafil->gemFil();

//Gives the user the option to anwser more questions.

$sp = explode("::", $datafil->getLine(41));
$svar = explode(",,", $datafil->getLine(42));

for ($i = 0; $i < $pro_max_sp; $i++) {
	if ((strpos($svaret, "sp".$i) === FALSE) and isset($sp[$i]) and (strlen($sp[$i]) > 0)) {
		$sv = explode("::", $svar[$i]);
		$svarmug .= "<h3>$sp[$i]</h3>\n";
		for ($n = 0; $n < $pro_max_sv; $n++) {
			if (isset($sv[$n]) and strlen($sv[$n]) > 0) {
				$j = $i+1;
				$k = $n+1;
				$svarmug .= "<input type=radio name=\"sp$j\" value=\"$k\">$sv[$n]<BR>\n";
				}
			}
		}
	}

if (isset($svarmug) and strlen($svarmug) > 0) {
	$svarmug = "<div class=forside><h2>Hvis du vil svare p flere sprgsml</h2>\n" . $svarmug . "<input type=submit value=\"Svar\"> <input type=reset value=\"Nulstil formular\"></div>\n";
} else {
	$svarmug = "<div class=forside><h2>Tak!</h2>\nNu er der ikke flere sprgsml at svare p!</div>\n";
}

$uaUtils = new UsersAreaUtils($siteContext);

//Did something go wrong?
if (strlen($problemer) > 0) {
	$errors = new Errors();
	$errors->addError(new ZsError(2, "<div class=problemer><h1>Der opstod desvrre problemer...</h1>$problemer</div>"));
	$uaUtils->showErrors($errors);
	exit;
} else {
	if (isset($fsvaret) and strlen($fsvaret) > 0) {
		$fxsvaret = "<p>Da du fr har svaret p $fsvaret er dette/disse svar ikke talt med.</p>";
	}

	$uaUtils->echoSiteHead("Tak fordi du har svaret!", 1);

	echo "<div class=forside>$fxsvaret <form action=\"".$stier->getOption('cgiURL')
	    ."/pollAnswer.php\" method=POST><input type=hidden name=brugernavn value=\""
	    .htmlentities($ind['brugernavn'])."\"><input type=hidden name=svaret value=\"$svaret\">\n"
	    .$svarmug."</form><BR><a href=\""
			.$stier->getOption('urlStatsite')."?brugernavn="
			.htmlentities($ind['brugernavn'])."&amp;show[]=HitsVotes\">Se hvad folk svarede...</A></div>";
	$uaUtils->echoSiteEnd();
}

?>
