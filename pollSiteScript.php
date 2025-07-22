<?php

//Data file format
//41-Questions
//42-Anwsers n::n::n::n::n,,n::n::n::n::n (...)
//43-Number of anwsers n::n::n::n::n,,n::n::n::n::n (...)

//QUERY_STRING
		//spN: Question N
		//oskst: The HTML tag before the headline
		//osksl: The HTML tag after the headline
		//stype: radio/checkbox
		//fs: The HTML tag before the anwser
		//es: The HTML tag after the anwser

//$ENV{QUERY_STRING} = "sp1=vis&sp3=vis&sp4=vis&brugernavn=zip";
//$ENV{REQUEST_METHOD} = "GET";

require "Html.php";
require "Stier.php";
require "lib/SiteContext.php";

//Stier og options
$stier = new Stier();

//Henter variable udefra
$ind = Html::setPostOrGetVars($_POST, $_GET);

if (isset($ind['brugernavn'])) {
	$username = $ind['brugernavn'];
} else {
	$username = '';
}

//Load the data source
$datafil = DataSource::createInstance($username, $stier);
$datafil->hentFil();

//Instantierer klassen med standardkode
$lib = new Html($ind,$datafil);

$siteContext = new SiteContext($lib, $stier, $ind, 'da');
$lib->setSiteContext($siteContext);

$lib->setStier($stier);

//May anwsers be registered from this url?
if (! $lib->countVisit(getenv("HTTP_REFERER"), $datafil->getLine(111))) {
	//No: Write it on the website.
	echo "document.write(\"";
	echo addslashes("Sidens ejer har angivet, at der ikke m registreres statistikker eller svar fra andre sider end en rkke angivne sider, og denne side er ikke en af dem. Derfor gives der ikke mulighed for at svare p sprgsml fra denne side.<br>Sidens ejer kan inkludere denne side ($ENV{'HTTP_REFERER'}) i de tilladte sider, ved at logge ind p brugeromrdet p <a href=http://www.zipstat.dk>www.zipstat.dk</a> og g til siden &quot;Indstillinger&quot;, hvorp der kan opgives en rkke sider hvorfra der m registreres statistikker og svar.");
	echo "\");\n";
	exit;
}


$sp = explode("::",$datafil->getLine(41));
$svar = explode(",,",$datafil->getLine(42));

$pro_max_sp = $lib->pro(3);
$pro_max_sv = $lib->pro(4);

echo "document.write(\"<form action='".addslashes($stier->getOption('cgiURL'))
     ."/pollAnswer.php' method=POST><input type=hidden name=brugernavn value=\\\""
     .addslashes($username)."\\\">\");\n";
for ($i = 1; $i <= $pro_max_sp; $i++) {
	if (($ind["sp$i"]) and ($sp[$i-1])) {
	
		echo "document.writeln(\"<span class=spover$i>".$sp[$i-1]."</span>\");\n";
		echo "document.writeln(\"<SELECT size=1 name=sp$i class=spsel0><OPTION value=ingen class=spseltxt0> - Vlg svar - \");\n";

		$sv = explode("::",$svar[$i-1]);
		for ($n = 0; $n < $pro_max_sv; $n++) {
			$j = $n+1;
			$name = "sp$i";
			if ($sv[$n])
				print "document.writeln(\"<OPTION name=\\\"$name\\\" value=\\\"$j\\\" class=spseltxt$n>".addslashes($sv[$n])."\");\n";
		}
		print "document.writeln(\"</SELECT><BR>\");\n";
	}
} //End for

print "document.write(\"<input type=submit value=\\\"    Svar    \\\" class=spsubmit></form>\");\n";
?>