<?php

	require "Html.php";
	require "Stier.php";
	require "lib/Localizer.php";
	require "lib/SiteContext.php";
	require "lib/UsersArea/Utils.php";

	//Stier og options
	$stier = new Stier();

	//Henter variable udefra
	$ind = Html::setPostOrGetVars($_POST, $_GET);
	
	if (isset($ind['username'])) {
		$username = $ind['username'];
  } else {
    $username = '';
  }

  if (isset($ind['password'])) {
		$password = $ind['password'];
  } else {
    $password = '';
  }

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
			$errors->addError(new ZsError(2, sprintf($siteContext->getLocale('errDamagedDatasource'), $stier->getOption('name_of_service'))));
		} elseif (! $res or $res === 0) {
			$errors->addError(new ZsError(2, sprintf($siteContext->getLocale('errDatasourceInaccessible'), $stier->getOption('name_of_service'))));
		}
	}

	//Instantierer klassen med standardkode
	$lib = new Html($ind,$datafil);

	$siteContext = new SiteContext($lib, $stier, $ind, 'da');
	$lib->setSiteContext($siteContext);

	$lib->setStier($stier);
	
	$utils = new UsersAreaUtils($siteContext);

	if ((! isset($ind)) or (! isset($ind)) or (! isset($username)))
	{
		$utils->doLoginForm(1, $siteContext->getOption('urlUserAreaMain'));
		exit;
	} else if (!$datafil->authenticate($username, $password, 'admin', array('admin', 'statsite'))) {
		$utils->doLoginForm(2, $siteContext->getOption('urlUserAreaMain'));
		exit;
	} else if ($errors->isOccured()) {
		$uaUtils = new UsersAreaUtils($siteContext);
		$uaUtils->showErrors($errors);
		exit;
	}

//Set the latest use with username and password
$datafil->setLine(110, time());

if (isset($ind['type']))
	$gotoSite = $ind['type'];
else
	$gotoSite = '';

if ($gotoSite === 'rtaellere') {
	r_taellere($utils, $siteContext); //ok
} else if ($gotoSite === 'rspoergsmaal') {
	r_spoer($utils, $siteContext); //ok
} else if ($gotoSite === 'rindstillinger') {
	r_indstillinger($utils, $siteContext); //ok
} else if ($gotoSite === 'roplysninger') {
	r_oplysninger($utils, $siteContext); //ok
} else if ($gotoSite === 'rkodeord') {
	r_kodeord($utils, $siteContext); //ok
} else if ($gotoSite === 'rnulstil') {
	r_nulstil($utils, $siteContext); //ok
} else if (($gotoSite === 'remailstats') and ($utils->getUAType() === $utils->UA_TYPE_SIMPLE)) {
	r_emailstats_simpel($utils, $siteContext); //ok
} else if ($gotoSite === 'remailstats') {
	r_emailstats($utils, $siteContext); //ok
} else if ($gotoSite === 'rzipklik') {
	r_zipklik($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_indstillinger') {
	gem_indstillinger($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_oplysninger') {
	gem_oplysninger($utils, $siteContext);  //ok
} else if ($gotoSite === 'slet_konto') {
	slet_konto($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_nulstil') {
	gem_nulstil($utils, $siteContext); //ok
} else if ($gotoSite === 'gemkodeord') {
	gem_kodeord($utils, $siteContext); //ok
} else if ($gotoSite === 'gemkodeord_ok') {
	gemkodeord_ok($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_spoers') {
	gem_spoergs($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_taellere') {
	gem_taellere($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_mailstats') {
	gem_mailstats($utils, $siteContext); //ok
} else if ($gotoSite === 'gem_zipklik') {
	gem_zipklik($utils, $siteContext); //ok
} else if ($gotoSite === 'backup') {
	show_backup($utils, $siteContext); //ok
} else if ($gotoSite === 'dlbackup') {
	download_backup($utils, $siteContext); //ok
} else {
	$utils->showMessage("V&aelig;lg funktion","Vælg funktion i menuen til venstre. Husk at benytte Gem-knapperne i bunden af siderne for at gemme de ændringer. De bliver ikke gemt hvis du forladerne siderne via menuerne eller hvis du skifter mellem simpelt og avanceret brug.");
}
exit(0);

#######################################

/**
 * Sends the user the backup. This page is not ment for viewing but
 * for backup.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function download_backup(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();
	header("Content-type: text/sql");
	echo $datafile->getBackup();
}

/**
 * Displays download of a backup.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function show_backup(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$utils->echoSiteHead("Backup", 0);
?>
<div class=forside>
<p><cite>Rigtige m&aelig;nd tager ikke backup...</cite></p>
<h2>Intro</h2>
<p>ZIP Stat tager ikke dine data som gidsel: Du kan når som helst <a target="_blank" href="https://zipstat.org/">downloade ZIP Stat</a> og k&oslash;re det hos dig selv. Du kan endda starte en konkurerende service!</p>
<p>ZIP Stat benytter databasen MySQL (eller de frie alternativer s&aring;som MariaDB) og de data du kan downloade er formateret til at blive brugt af den. Benyt kun disse data til nyeste version af ZIP Stat. ZIP Stat kan nogle gange automatisk opgradere et ældre dataformat, men en gammel version af ZIP Stat kan ikke bruge nyere data.</p>
<h2>Download dine data</h2>
<p><a href="<?php
	echo htmlentities($_SERVER["SCRIPT_NAME"])
	      ."/zipstat_backup_".date($siteContext->getOption('dateformat_backup'))
	      ."_".htmlentities($ind['username'])
	      .".sql?type=dlbackup&amp;username=".htmlentities($ind['username']);
?>">download dine data</a>.</p>

</div>

<?php

	$utils->echoSiteEnd();
}

/**
 * Displays editing of the counters.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_taellere(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();
	if ($lib->pro()) {

		if (isset($ind['navneloese'])) {
			$_COOKIE['navneloese'] = $ind['navneloese'];
			setcookie('navneloese', $ind['navneloese'], time()+3600*24*365, '/', $siteContext->getOption('domain'));
		}	else if (isset($_COOKIE['navneloese'])) {
			$ind['navneloese'] = $_COOKIE['navneloese'];
		}
	}

	$thits  = explode('::', $datafile->getLine(37));
	$tnavne = explode('::', $datafile->getLine(38));

	$utils->echoSiteHead("Rediger tællere", 0);

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_taellere\">";

	if ($lib->pro()) {
		#$qs = $ENV{'QUERY_STRING'};
		#$qs =~ s/\&navneloese\=skjul//ig;
		#$qs =~ s/\&navneloese\=vis//ig;
		$qs = "username=".$ind['username']."&amp;type=rtaellere";

		#$pro_tekst = "<p>Du kan forge eller formindse antallet af tællere på siden &quot;Indstillinger&quot; (brug linket i menuen til hjre)</P>.\n";
		if ($ind['navneloese'] === "skjul")
			$pro_tekst = "<a href=\"".$siteContext->getOption('urlUserAreaMain')."?$qs&amp;navneloese=vis\">Vis navnelse tællere...</A><br>\n";
		else
			$pro_tekst = "<a href=\"".$siteContext->getOption('urlUserAreaMain')."?$qs&amp;navneloese=skjul\">Skjul navnelse tællere...</A><br>\n";

		$pro_tekst .= "Antal tællere <input type=text name=\"pro_taellere\" value=\"".$lib->pro(5)."\" size=3>\n";
		$pro_tekst .= "<a href=\"JAVAscript: alert('Som standard har du 50 tællere, men hvis du har brug for flere ellere ?rre, kan du indtaste antallet her. Du bør dog ikke sætte tallet til mere end nogle hundrede, da mange ?llere dels vil gøre din statistik mere uoverskuelig, men selve statistikken bliver o? langsommere, når der skal holder styr ? store mængder data.');\"><img src=\"".$siteContext->getPath('zipstat_icons')."/stegn2.gif\" width=9 height=14 border=0 alt=\"?lp til antal tællere...\"></a><br>\n";
	} else {
		$pro_tekst = '';
	}

?>
<div class=forside>
<p>Hvis du vil ændre eller slette dens navn, så gør det i kassen ud for tælleren. Hvis du vil nulstille en tæller, så sæt kryds i kassen ud for den espektive tæller.</p>
<?php echo $pro_tekst; ?>
<form action="<?php echo $siteContext->getOption('urlUserAreaMain'); ?>" method=POST>
<table border=1>
<tr><td>Når.</td><td>Navn</td><td>Nulstil</td><td>Hits</td></tr>
<?php

	$pro_max_taellere = $lib->pro(5);
	//Find the width of the largest counter.
	$nameFieldWidth = 30;
	for ($i = 0; $i <= $pro_max_taellere; $i++) {
		if (strlen($tnavne[$i]) > $nameFieldWidth) {
			$nameFieldWidth = strlen($tnavne[$i]);
		}
	}
	
	//Add a bit more and round up.
	$nameFieldWidth = ceil(1.1 * $nameFieldWidth);
	
	//Enforce max width.
	if ($nameFieldWidth > 100) {
		$nameFieldWidth = 100;
	}

	$hasPro = $lib->pro();
	for ($i = 0; $i <= $pro_max_taellere; $i++) {
		if ($hasPro) {
			if ( (($ind['navneloese'] === "skjul") and ($tnavne[$i] !== "")) or ($ind['navneloese'] === "vis") or (! isset($ind['navneloese'])) or ($ind['navneloese'] === "") ) {
				echo "<tr>\n<td>$i</td>\n<td><input type=textbox size=\"$nameFieldWidth\" name=\"navntaeller$i\" value=\"".htmlentities($tnavne[$i])."\"></td>\n<td><input type=checkbox name=\"nultael$i\"></td>\n<td>".htmlentities($thits[$i])."</td>\n</tr>\n";
			}
		} else {
			echo "<tr>\n<td>$i</td>\n<td><input type=textbox size=\"$nameFieldWidth\" name=\"navntaeller$i\" value=\"".htmlentities($tnavne[$i])."\"></td>\n<td><input type=checkbox name=\"nultael$i\"></td>\n<td>".htmlentities($thits[$i])."</td>\n</tr>\n";
		}
	}

?>
</table>

</div>

<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">
</form>
<?php

	$utils->echoSiteEnd();


} //End function edit counters

/**
 * Calculates how many (typically lines) to show.
 *
 * @param $lib         an instance of the code lib.
 * @param $antalPoster the current amount of items.
 * @param $proNr       the index for the Html::pro function.
 */
function visAntal(&$lib, $antalPoster, $proNr) {
	if ($proNr == 3) {
		$antalPoster += 3;
		$lib->setPro($proNr, $antalPoster);
	}	else if ($proNr == 4)	{
		$antalPoster += 3;
		$lib->setPro($proNr, $antalPoster);
	} else {
		$antalPoster += 5;
	}

	//If the number is too large
	if ($lib->pro($proNr) < $antalPoster) {
		$antalPoster = $lib->pro($proNr);
	}

	return $antalPoster;
}

/**
 * Displays editing of the questions.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_spoer(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Sp?gsmål", 0);

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post>";

	$sp = explode('::', $datafile->getLine(41));
	$sv = explode(',,', $datafile->getLine(42));
	$hi = explode(',,', $datafile->getLine(43));

	$noQuestions = $lib->pro(3);
	$noAnswers = $lib->pro(4);

	//Hvor mange der skal vises
	$visAntalSp = $noQuestions;

	$maxNrSv = 0;
	for ($i = 0; $i < count($sv); $i++) {
		$dSv = explode('::', $sv[$i]);
		for ($n = 0; $n < count($dSv); $n++) {
			if (($dSv[$n] != '') and ($n > $maxNrSv)) {
				$maxNrSv = $n;
			} //End if
		} //End inner for
	} //End outer for
	$visAntalSv = $maxNrSv+1;

	$visAntalSv = visAntal($lib, $visAntalSv, 4);
	$visAntalSp = visAntal($lib, $visAntalSp, 3);

	for ($i = 0;$i < $visAntalSp; $i++) {
		$dy = $i + 1;
		?>
		<a href="JAVAscript: alert('Hvis du vil ændre et spørgsmål, skal du skrive ændringen i den tilsvarende boks.\nHvis du vil ændre svarene, skal du skrive ændringerne i de\nmindre bokse.\nDu kan nulstille antal svar (hits) hvert svar har fået, ved at\nsætte kryds i den lille kasse.\nHvis du ikke skriver noget i et svarfelt, vil svaret ikke blive givet som svarmulighed.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til rediger spørgsmål..."></a>
		<p align=center><table border=1 class=forside><caption>Hvis du ikke skriver noget i et svarfelt, vil svaret ikke blive vist.</caption>
		<tr><td colspan=3>
		<big align=center>Sprgsm&aring;l <?php echo $dy; ?></big>
		</td></tr>
		<?php
		echo "<tr><td colspan=2><input type=text size=50 name=\"spoergs$i\" value=\"";
		if (isset($sp[$i])) echo htmlentities($sp[$i]);
		echo "\"></td>\n";
		echo "<td>Nulstil svar <input type=checkbox name=\"nulstilsp$i\"></td></tr>\n";
		echo "<tr><td>Svar nr.</td><td>Tekst</td><td>Svar</td></tr>\n";

		if (isset($sv[$i]))
			$sva = explode('::', $sv[$i]);
		else
			$sva = array();
		if (isset($hi[$i]))
			$hit = explode('::', $hi[$i]);
		else
			$hit = array();

		for ($n = 0;$n < $visAntalSv; $n++) {
			$k = $n + 1;
			$tgk = "sp$i" . "sv$n";
			echo "<tr><td>$k:</td><td><input size=43 type=text name=\"$tgk\" value=\"";
			if (isset($sva[$n])) echo htmlentities($sva[$n]);
			echo "\"></td><td>";
			if (isset($hit[$n])) echo htmlentities($hit[$n]);
			echo "</td></tr>";
		}
		echo "</table></p>";
	}

	if ($lib->pro()) {
		echo "<div class=forside>N&aring;r du gemmer dine sp&oslash;rgsm&aring;l og svar, og g&aring;r ind p&aring; denne side igen, vil der altid v&aelig;re plads til endnu et nyt sp&oslash;rgsm&aring;l samt 3 ydligere svar til hvert sp&oslash;rgsm&aring;l.</div>";
	} else {
		echo "<div class=forside>N&aring;r du gemmer dine sp&oslash;rgsm&aring;l og svar, og g&aring;r ind p&aring; denne side igen, vil der, s&aring; l&aelig;nge du har nogle ledige, v&aelig;re plads til endnu et nyt sp&oslash;rgsm&aring;l samt 3 ydligere svar til hvert sp&oslash;rgsm&aring;l.</div>";
	}

	echo "<input type=hidden value=\"$visAntalSv\" name=antalVistSv>\n";
	echo "<input type=hidden value=\"$visAntalSp\" name=antalVistSp>\n";
	echo "<input type=hidden value=\"".$ind['username']."\" name=username>\n";
	echo "<input type=hidden value=\"gem_spoers\" name=\"type\">\n";
	echo "<input type=submit value=\"   Gem   \"> <input type=reset value=\"Nulstil formular\">\n";
	echo "</form>\n";

	$utils->echoSiteEnd();

} //End function edit questions


/**
 * Displays editing of the settings.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_indstillinger(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Indstillinger", 0);

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_indstillinger\">";

	if ($lib->pro()) {
		$faa_pro = '';
		$pro_kode = '';

		$pro_inst = explode('::', $datafile->getLine(58));

		if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
			$pro_kode .= "<div class=forside><h2>Pro indstillinger</h2>\n<p>Når du har valgt den simple udgave af ZIP Stat, har du ikke mulighed for at ndre på dine pro-indstillinger. For at gøre dette skal du skifte til avanceret, hvilket du kan gøre via linket &quot;Skift til avanceret brug&quot; i menuen til venstre.</div>";
		} else {
			$pro_kode .= "<div class=forside><h3>Pro indstillinger</h3>\n<p>Hvis der ikke str noget i en boks benyttes standardværdien. ";
			if ($siteContext->getOption('always_pro') !== 1)
				$pro_kode .= "Du har ZIP Stat Pro.</p>";
			else
				$pro_kode .= "Du har ZIP Stat Pro på ubestemt tid.</p>";

			$pro_kode .= "<table border=0>\n";
			$pro_kode .= "<tr><td>Overskrift til statistiksiden</td><td><input type=text name=\"pro_overskrift\" value=\"".htmlentities($datafile->getLine(59))."\">";
					$pro_kode .= "<a href=\"JAVAscript: alert('Hvis du vil have en anden overskrift på statistiksiden, end den der er der i forvejen, skal du skrive den her. Hvis du vil have den overskrift der benyttes på den normale ZIP Stat, skal du ikke skrive noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til overskrift på statistiksiden...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Indhold af body-tag</td><td><tt>&lt;BODY&nbsp;</tt><input type=text name=\"pro_body\" value=\"".htmlentities($datafile->getLine(56))."\"><tt>&gt;</tt>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Hvis du vil have andre farver på din statistikside, end dem der er nu, skal du skrive attributterne der skal være i sidens BODY tag. Hvis du vil have farverne på den normale ZIP Stat, skal du ikke skrive noget.\\nVær opmrksom på, at visse farver på siden er sat via CSS (StyleSheets). Disse farver mm. skal derfor ændres via CSS (se hjlpen til det nste punkt).');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til BODY tagget...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Link til CSS-fil</td><td><input type=text name=\"pro_css\" value=\"".htmlentities($datafile->getLine(60))."\">\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Hvis du vil benytte et CSS (StyleSheet) til at ndre farver ol. på statistiksiden, så skal du angive adressen til CSS filen her. Husk http:// foran!\\nDer er benyttet class tags til at specificere udseendet af forskellige tags - kig i HTML\\'en eller se oversigten på hjlpesiden.\\nHvis du vil bruge det CSS der er på den normale ZIP Stat, så lad feltet være tomt.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til CSS...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Max antal referencesider</td><td><input type=text name=\"pro_maxref\" value=\"".(isset($pro_inst[0]) ? $pro_inst[0] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('På den normale ZIP Stat bliver der højest registreret de seneste 50 referencesider. Hvis du vil have registreret flere, så skriv antallet her. Det anbefales dog at antallet holder under 100, da man normalt ikke kan bruge resten til noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til max antal referencesider...\"></a></td></tr>\n";

		$pro_kode .= "<tr><td>Max antal indgangssider</td><td><input type=text name=\"pro_maxindgang\" value=\"".(isset($pro_inst[16]) ? $pro_inst[16] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('På den normale ZIP Stat bliver der højest registreret de seneste 50 indgangssider. Hvis du vil have registreret flere, så skriv antallet her. Det anbefales dog at antallet holder under 100, da man normalt ikke kan bruge resten til noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til max antal indgangssider...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Max antal udgangssider</td><td><input type=text name=\"pro_maxudgang\" value=\"".(isset($pro_inst[17]) ? $pro_inst[17] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('På den normale ZIP Stat bliver der højest registreret de seneste 50 udgangssider. Hvis du vil have registreret flere, så skriv antallet her. Det anbefales dog at antallet holder under 100, da man normalt ikke kan bruge resten til noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til max antal udgangssider...\"></a></td></tr>\n";

		$pro_kode .= "<tr><td>Max antal IP-adresser (til unikke hits)</td><td><input type=text name=\"pro_maxipadr\" value=\"".(isset($pro_inst[1]) ? $pro_inst[1] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Når der registreres unikke besøgende foregår det ved at gemme en unik adresse hver besøgende har (IP-adressen), og kun tælle op hvis denne ikke er registreret. Normalt bliver de seneste 50 IP-adresser registreret, hvilket er rigeligt er mere end rigeligt for de fleste sider. Men hvis man har mere end ca. 500 hits pr. dag er 50 IP-adresser ikke altid nok, og man bør derfor sætte tallet op.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til antal IP-adresser...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Oplysninger om max</td><td><input type=text name=\"pro_maxbrugere\" value=\"".(isset($pro_inst[2]) ? $pro_inst[2] : '')."\" size=3> antal brugere\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard gemmes der detaljerede oplysninger om de seneste 20 besøgende - dette kan du sætte op (eller ned) her. Du bør dog ikke sætte tallet til mere end ca. 100, da dette dels vil betyde din statistikside er lnge om at blive indlst, men registreringen af din statistik vil ogs blive langsommere, hvis der er for mange data at holde styr på. Endelig kan man slet ikke overskue ret mange af disse, og pga. de store mngder data er denne statistik noget der fylder meget i datafilen - og jeg har ikke ret meget plads til disse!');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til oplysninger om X antal besøgende...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Antal forskellige domæner</td><td><input type=text name=\"pro_maxdom\" value=\"".(isset($pro_inst[7]) ? $pro_inst[7] : '')."\" size=3>";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard registreres der 100 forskellige domæner. Hvis du ønsker der registreres flere (hvis de nederste på listen har fået mindre end 10 hits på en måned, anbefales det ikke at få registreret), kan du sætte dette tal op. Du kan ogs sætte det ned, hvis det kun er fx. de 20 verste på statistiksiden der reelt bliver talt op i løbet af en uges tid.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til max antal forskellige domæner...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Antal forskellige søgeord</td><td><input type=text name=\"pro_maxsoegeord\" value=\"".(isset($pro_inst[8]) ? $pro_inst[8] : '')."\" size=3>";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard registreres der 100 forskellige søgeord. Hvis du ønsker der registreres flere (hvis de nederste på listen har fået mindre end 10 hits på en måned, anbefales det ikke at få registreret), kan du sætte dette tal op. Du kan ogs sætte det ned, hvis det kun er fx. de 20 verste på statistiksiden der reelt bliver talt op i løbet af en uges tid.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til max antal forskellige søgeord...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Hits pr. besøgende beregnes over </td><td><input type=text name=\"pro_hpbover\" value=\"".(isset($pro_inst[6]) ? $pro_inst[6] : '')."\" size=3> uger\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard beregnes hits pr. besøgende over 3 uger, men sider med meget få hits vil med fordel kunne sætte dette tal i vejret. Får ens side mange hits kan godt sætte tallet ned, hvis man ønsker det mest mulige aktuelle resultat.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til antal svar pr. spørgsmål...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Antal forskellige bevgelser </td><td><input type=text name=\"pro_bevaeg\" value=\"".(isset($pro_inst[11]) ? $pro_inst[11] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard har du mulighed for 50 forskellige bevgelser. Dem kan du bruge til at se hvilke sider folk bevger sig imellem. ');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjælp til antal bevgelser...\"></a></td></tr>\n";
			$pro_kode .= "</table>\nAntal tællere, kliktællere samt spørgsmål og svar kan ændres på deres respektive redigeringssider.</div>\n";
		}#Slut på if simpel else
	}
else #Hvis man ikke har pro
	{
	$pro_kode = '';
	$faa_pro = "<hr>\n<div class=forside><h2>Få ZIP Stat Pro</h2>";
	//$faa_pro .= "<p>Du kan få ZIP Stat Pro gratis i ca. 1 r. Den njagtige dato vil fremg verst på denne side, når du har ZIP Stat Pro. Det eneste du skal gøre er at skrive et pro-kodeord i kassen forneden, og trykke på &quot;Gem&quot; knapper lidt hjere oppe. Lige nu fungere følgende pro-kodeord:<br><code>intpro</code></p>";
	$faa_pro .= "<p>Du kan gratis få ZIP Stat Pro uden nogen hager. Engang var tanken at tage penge for det, men så ændrede verden sig</p>";
	$faa_pro .= "<p>Indtast pro-kodeord for at få gratis ZIP Stat Pro: <input type=text name=prokodeord size=8></p></p>";
	}

	//Ignore the owner of the website?
	
	if (isset($ind['saved']) and $ind['saved'] === 'true') {
		//The value has just been saved - follow what the user just selected.
		if (isset($ind['ikkeop'])) {
			$ikopchek = ' CHECKED';
		} else {
			$ikopchek = '';
		}
	} else {
		//The page was only loaded: Get it from the cookie.
		if (isset($ind['username']) and isset($_COOKIE[$ind['username']]) and $_COOKIE[$ind['username']] === "ikkeop") {
			$ikopchek = ' CHECKED';
		} else {
			$ikopchek = '';
		}
	}

	if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
	?>

	<div class=forside>
	<h3>Bliv ikke selv talt med</h3>
	<p><input type=checkbox name="ikkeop"<?php echo $ikopchek; ?>> Tæl aldrig mig med i min statistik</P>
	<p>OBS. Hvis du skifter til en anden computer eller en anden internet-browser, skal du gå inp denne side og krydse af igen.</p>
	</div>

	<br>

	<input type="hidden" value="<?php echo $ind['username']; ?>" name="username">
	<input type="hidden" value="ja" name="simpelgem">
	<input type="hidden" value="true" name="saved">
	<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">
	<?php
	if (isset($faa_pro))
		echo $faa_pro;
	} #Slut på if simpel
else
	{
	if (isset($pro_kode))
		echo $pro_kode;

	?>
	<div class=forside>
	<h2>Sæt tællerens startværdi</h2>
	<p>Du har her mulighed for at forge den tæller der tæller antal hits på hele siden seneste nulstilning. Hvis du gør det, vil det st på statistiksiden, og det vil ikke påvirke tælletallet på toplisten. Du kan skrive et negativt tal, ved at sætte et &quot;-&quot; (minus) foran tallet.</p>
	<p>Tælop med <input type=text name=standardop value="<?php echo htmlentities($datafile->getLine(82)); ?>"> hits.</p>
	</div>
	<?php

	#Kodeord til statistiksiden
	echo "<div class=forside>\n";
        echo "<h2>Offentlig statistikside</h2>";

        $statsitePublic = $datafile->getField('statsitePublic');
        echo "Statistiksiden er: <select name=\"statsitePublic\">\n";
        echo "  <option value=\"true\"".($statsitePublic ? ' SELECTED' : '').">offentlig</option>\n";
        echo "  <option value=\"false\"".((! $statsitePublic) ? ' SELECTED' : '').">lukket for offentligheden</option>\n";
        echo "</select>";

        echo "<p>Ønsker du at udvalgte personer skal kunne se statistiksiden uden at &aelig;ndre i resten af indstillingerne, kan du angive en r&aelig;kke kodeord herunder. Angiv 1 kodeord pr. linie.</p>\n";
        echo "<p><b>OBS</b>: Disse kodeord opbevares i klar tekst - skulle nogen f&aring; adgang til ZIP Stats database vil angriberen f&aring; adgang til disse kodeord. Derfor b&oslash;r disse kodeord under ingen omst&aelig;ndigheder genbruges p&aring; andre sider! (dette g&aelig;lder i &oslash;vrigt for alle kodeord)</p>";
	print "<TEXTAREA NAME=\"brugerkodeord\" ROWS=\"5\" COLS=\"10\">";
	echo htmlentities(str_replace("::", "\n", $datafile->getLine(57)));
	print "</TEXTAREA>Kun 1 kodeord pr. linie";
        echo "<p><b>OBS</b>: Selvom der st&aring;r kodeord herover, er statistiksiden altid offentlig hvis dette er valgt - der skal altså&aring; st&aring: &quot;Statistiksiden er: lukket for offentligheden&quot; for at den IKKE er offentlig.</p>";
	print "</div><br>\n";

	#Spærringer
	$tillad = explode('::', $datafile->getLine(106));
	for ($i = 0;$i <= 2;$i++) {
		if ($tillad[$i])
			$tillad[$i] = ' CHECKED';
		else
			$tillad[$i] = '';
	}

	?>
	<div class=forside>
	<h2>Spærringer</h2>
	<p>Du kan her spærre eller tillade visning af statistikker på forskellige måder.</p>
	<input type=checkbox name="tillad0"<?php echo $tillad[0]; ?>> Vis siden på toplisten<br>
	<input type=checkbox name="tillad1"<?php echo $tillad[1]; ?>> Tillad visning af statistikker via javascriptstats og ministatistik<br>
	<input type=checkbox name="tillad2"<?php echo $tillad[2]; ?>> Tillad visning af tælletal med tællerbilleder<br>
	</div>
	<?php

	#Nyhedsbreve	
	$nyhedsbrev = explode('::', $datafile->getLine(107));
	for ($i = 0;$i <= 6;$i++) {
		if ($nyhedsbrev[$i])
			$nyhedsbrev[$i] = ' CHECKED';
		else
			$nyhedsbrev[$i] = '';
	}
	?>
	<div class=forside>
	<h2>Nyhedsbreve</h2>
	<p>Du kan her bestemme hvilke typer e-mail du ønsker at modtage fra ZIP Stat. De kommer ikke så tit. Har ikke sendt nogen ud de seneste 15 år...<br>
	Send mig e-mails med besked om følgende:</p>
	<input type=checkbox name="nyhedsbrev0"<?php echo $nyhedsbrev[0]; ?>> Større opdateringer af ZIP Stat (anbefales)<br>
	<input type=checkbox name="nyhedsbrev1"<?php echo $nyhedsbrev[1]; ?>> Mindre opdateringer af ZIP Stat<br>
	<input type=checkbox name="nyhedsbrev2"<?php echo $nyhedsbrev[2]; ?>> Når en fejl er konstateret, inkl. et gæt på hvornår den er rettet<br>
	<input type=checkbox name="nyhedsbrev3"<?php echo $nyhedsbrev[3]; ?>> Når en større fejl er rettet (anbefales)<br>
	<input type=checkbox name="nyhedsbrev4"<?php echo $nyhedsbrev[4]; ?>> Når en mindre fejl er rettet<br>
	<input type=checkbox name="nyhedsbrev5"<?php echo $nyhedsbrev[5]; ?>> Andre nyhedsbreve om ZIP Stat<br>
	<input type=checkbox name="nyhedsbrev6"<?php echo $nyhedsbrev[6]; ?>> Andre nyhedsbreve om andre services fra <?php echo htmlentities($siteContext->getOption('adminName').' og '.$siteContext->getOption('domain')); ?><br>
	</div>
	
	<?php
	
		//Ignore query string on counters.
		$counterIgnoreQuery = ($datafile->getUserSetting('ignoreQuery') !== "false");
		$ignoreQueryChecked = ($counterIgnoreQuery ? "checked=\"checked\"" : "");
		
		?>
	<div class=forside>
	<h2>Tællere</h2>
		<label>
			<input type="checkbox" name="ignoreQuery"<?php echo $ignoreQueryChecked;?> />
			Fjern en eventuel &quot;query string&quot; adresserne i tællere.
		</label>
		<p>En <b>query string</b> er den del af en web-adresse der kommer efter et spørgsmålstegn, fx i <code>http://zipstat.dk/userarea.php?username=zip</code> er det &quot;<code>?username=zip</code>&quot; som er query string. Hvis du er i tvivl, så st kryds i denne boks.</p>
	</div>
		<?php

		#Tæl kun hits på disse sider
		$okSider = explode('::', $datafile->getLine(111));
		$okSideHtml = '';

	if ($lib->pro()) {
		for ($i = 0; $i < count($okSider) +2; $i++) {
			$okSideHtml .= "	<input type=text name=\"okSider$i\" value=\"";
			if (isset($okSider[$i]))
				$okSideHtml .= $okSider[$i];
			$okSideHtml .= "\" size=45><br>\n";
		}
		$okSideHtml .= "Når du trykker på &quot;Gem&quot;-knappen og går ind på siden, kommer der 2 nye, tomme bokse.<br>\n";
		$okSideHtml .= "<input type=hidden name=okSiderAntal value=".((count($okSider))+2).">\n";
	} else {
		for ($i = 0; $i < $lib->pro(15) +2; $i++) {
			$okSideHtml .= "	<input type=text name=\"okSider$i\" value=\"";
			if (isset($okSider[$i]))
				$okSideHtml .= $okSider[$i];
			$okSideHtml .= "\" size=45><br>\n";
			$okSideHtml .= "<input type=hidden name=okSiderAntal value=".($lib->pro(15)+2).">\n";
		}
	}

	?>
	<p>
	<div class=forside>
	<h2>Registrer kun hits på disse sider</h2>
	<p>Hvis en anden person benytter din obligatoriske kode, spørgsmål/svar-kode eller dine kliktællere, kan du her vælge, at der kun må registreres hits på de sider, du angiver her. Skriver du ikke noget, vil der blive registreret hits på alle sider.</p>
	<p>Starter du en adresse med <code>http://</code> eller <code>https://</code>, skal adressen, til den side der må registreres hits på, <em>starte</em> med denne adresse. Skriver du ikke <code>http://</code> eller <code>https://</code>, vil der blive registreret hits fra alle sider, hvis adresse blot <em>indeholder</em> det du har skrevet.</p>
	<?php echo $okSideHtml; ?>
	</div>
	<?php

	#Ikke selv talt med: cookie
	?>
	<p>
	<div class=forside>
	<h2>Bliv ikke selv talt med</h2>
	<h3>Via cookies</h3>
	<p>Vil du hellere benytte en lsning hvor der benyttes cookies, så st kryds i nste kasse og tryk på &quot;Gem&quot;. Du fjerner cookien ved at fjerne krydset og trykke på &quot;Gem&quot;.</p>

	<p><input type=checkbox name="ikkeop"<?php echo $ikopchek; ?>> Tæl aldrig mig (denne browser på denne computer) med i statistikken
	</P>

	<h3>Via IP-adresse</h3>
	Hvis du henter siden <a href="<?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?>" target="_top"><?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?></A>
	i din browser, vil du ikke selv blive registreret af ZIP Stat når du besøger dine egne sider. Dette krver dog at du henter 
	adressen <b>hver</b> gang du går på Internettet (dvs. hver gang du ringer op med dit modem - hvis du har fast IP-adresse <small>[hvis du har det, ved du det helt sikkert]</small>).
	Den letteste måde at gøre dette på er, at du sætter adressen som din startside (se her under for en instruktion). Den side din browser normalt starter med, skal du så skrive i kassen herunder. S vil du nsten ikke opdage denne funktion.<BR>

	Send-videre adresse: <br><input type=text name="taelopredirect" value="<?php echo htmlentities($datafile->getLine(53)); ?>" size="35"><br>
	Slet IP-adresse: <input type=checkbox name=sletipadr> Har du en fast IP-adresse, og ikke ønsker at benytte denne funktion lngere, kan du slette den sidst registrerede IP-adresse, så du igen bliver talt med i din statistik.
	</p>

	</div>

	<input type=hidden value="<?php echo $ind['username']; ?>" name=username>
	<input type="hidden" value="true" name="saved">
	<input type=submit value="   Gem   "> <input type=reset value="Nulstil formular">

	<hr>
	<div class=forside>
	<h3>Sdan ndrer du din startside</h3>
	<h4>I Netscape</h4>
	<P>I selve browseren vælger du menuen Rediger/Edit. Her vælge du punktet Prferencer/Preferences. S vælger du kategorien Navigator. Hvis der str noget i feltet Adresse/Adress, skal du skrive det i kassen mrket &quot;Send-videre&quot; adresse (her på siden). Nu skriver du <tt><?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?></tt> i kassen Adresse/Adress (i Netscape), og trykker OK. S trykker på Gem-knappen her på siden, og så virker det!</P>

	<h4>I Internet Eksplorer</h4>
	<P>I selve browseren vælger du menuen Vis/View. Her vælger du punktet Internet-indstillinger/Intenret-options. Derefter vælger du fanen Generet/General. Hvis der str noget i feltet Adresse/Adress, skal du skrive det i kassen mrket Send-videre adresse (her på siden). Nu skriver du <tt><?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?></tt> i kassen Adresse/Adress (i IE), og trykker OK. S trykker på Gem-knappen her på siden, og så virker det!</P>
	</div>

	<?php echo $faa_pro; ?>

	<?php
		} #Slut på if simpel else
	echo  "</form>";

	if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
		?>
	<h2>I avanceret visning</h2>
	<p>Hvis du skifter til avanceret visning (benyt linket &quot;Skift til avanceret visning&quot;i menuen til venstre), kan du ogs gøre følgende på denne side:
	<ul>
		<li>Sætter den overordnede tællers startværdi. Dette er nyttigt hvis du ønsker at &quot;tage gamle hits med&quot; fra en anden tæller eller statistik.
		<li>Angive selvvalgte kodeord til statistiksiden. Disse kodeord kan f.eks. gives til andre, da de kun giver adgang til statistiksiden.
		<li>Foretage sprringer, så du selv kan vælge om
		<ul>
			<li>din side skal med på toplisten.
			<li>nogle af dine statistikker skal være tilgængelige gennem javascript-stats og ministatistikken.
			<li>antal hits for hele siden, samt for de enkelte tællere, skal kunne vises via en grafisk tæller.
		</ul>
		<li>Selv vælge hvilke typer nyhedsbreve du ønsker at modtage fra ZIP Stat - du kan fx. vælge alle fra.
		<li>Vælge at en bestemt IP-adresse ikke skal tælles med i statistikken. Dette er praktisk hvis du har en fast internetforbindelse med fast IP-adresse.
		<li>Vælge at du kun vil have registreret statistikker fra nogle bestemte sider.
	</ul>

	<?php
	echo $faa_pro;

	}

	$utils->echoSiteEnd();
}

/**
 * Displays editing of the user info.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_oplysninger(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Redigere oplysninger", 0);
	
	$siteType = $datafile->getLine(83);
	$sel['erotik'] = $sel['okunder18'] = ''; //reset them
	if (strpos(strtolower($siteType), 'erotik') !== FALSE) {
		$sel['erotik'] = ' SELECTED';
		$kun_over_18_aar = 1;
	}	else if (strpos(strtolower($siteType), 'okunder18') !== FALSE) {
		$sel['okunder18'] = ' SELECTED';
		$kun_over_18_aar = 0;
	}
	/*
	if ($siteContext->getOption['use_index'] == 1)
	{
		$pro_max_kategorier = $lib->pro(14); #<11-Antal mulige kategorier i indekset>
		$pro_max_tegn_sord = $lib->pro(12); #<12-Max antal tegn til søgeord>
		$pro_max_tegn_besk = $lib->pro(13); #<13-Max antal tegn i beskrivelse>

		$kategorier = '';
		require $stier{'index_rediger'}; #Lib. med subrutiner til at redigere i indexfilerne.

		#Udskriver selectbox'e med kategorier
		$inddata[86] =~ s/\n//g;
		@kate_stier = explode(/::/,$inddata[86]);
		for ($i = 1;$i <= $pro_max_kategorier;$i++)
			{ $kategorier .= "Kategori nr. $i ".&getMuligeKategorier("kategorier$i",$kate_stier[$i-1],$kun_over_18_aar)."<br>\n"; }

		$kategorier .= "<p>Hvis ZIP Stats administrator vurderer at en side passer bedre i en anden kategori, kan han flytte siden. Dette sker dog typisk kun i forbindelse med oprettelse af nye underkategorier.</p>\n<p>Sider med indhold der ikke bør ses af brn og unge under 18 r, må kun placeres i kategorien &quot;Kun over 18 r&quot; samt dennes underkategorier.</p>\n<p>Sider med indhold der, efter den daønske lovligning, kan karakteriseres som ulovligt, må ikke tilfjes til indekset!</p>";
	} #End of if $options{'use_index'} - use index or not

	#84-Beskrivelse
	#85-Ngleord
	*/

	?>
	<div class=forside>
	<form action="<?php echo $siteContext->getOption('urlUserAreaMain'); ?>" method=POST>
	<input type=hidden name=type value="gem_oplysninger">
	<table>
	<tr><td>Navn</td><td><a href="JAVAscript: alert('Hvis du vil ndre dit navn, så skriv ændringen i boksen.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til navn..."></a>
		<input type=text name="navn" value="<?php echo htmlentities($datafile->getLine(1)); ?>"></td></tr>
	<tr><td>E-mail</td><td><a href="JAVAscript: alert('Hvis du vil ndre din e-mail adresse, så skriv ændringen i boksen.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til e-mail..."></a>
		<input type=text name="e-mail" value="<?php echo htmlentities($datafile->getLine(2)); ?>"></td></tr>
	<tr><td>Siden adresse</td><td><a href="JAVAscript: alert('Hvis du vil ndre din hjemmesides adresse, skal du skrive ændringen her.\nDet er vigtigt den er korrekt, fordi den bruges til at sortere dine egne sider\nfra, på listen over referencesider.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til sidens adresse..."></a>
		<input type=text name="url" value="<?php echo htmlentities($datafile->getLine(3)); ?>"></td></tr>
	<tr><td>Sidens titel</td><td><a href="JAVAscript: alert('Hvis du vil ndre sidens titel, så skriv ændringen i boksen.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til sidens titel..."></a>
		<input type=text name="titel" value="<?php echo htmlentities($datafile->getLine(4)); ?>"></td></tr>
	</table>
	</div>
	<?php
	/*
	<div class=forside>
		<p>
		Søgeord <input type=text name=sord value="<?php echo htmlentities($datafile->getLine(85)); ?>" maxlength=$pro_max_tegn_sord> max. $pro_max_tegn_sord tegn, adskildt af komma (, ).<br>
		Beskrivelse <input type=text name=beskrivelse value="<?php echo htmlentities($datafile->getLine(84)); ?>" maxlength=$pro_max_tegn_besk> max $pro_max_tegn_besk tegn, skal beskrive sidens <em>indhold</em>.<br>
		Overdrevent brug af udrbstegn, store bogstaver ol. vil automatisk blive rettet, samt trkke ned i rangeringen ved sgninger.<br>
		</p>
		$kategorier
	*/
	?>
	<p>Indeholder siden erotisk, pornografisk materiale eller andet der ikke bør ses af børn under og unge 18 år?<br>
	<select size=1 name=under18ok>
	<option value="">-vlg-
	<option value="Ja"<?php echo $sel['erotik']; ?>>Ja
	<option value="Nej"<?php echo $sel['okunder18']; ?>>Nej
	</select>
	</p>
	<?php
	/*
	<p>Sider med erotisk indhold ol. vil på toplisten blive nedtonet, og man fr en advarsel fr man går ind på statistiksiden.</p>
	*/
	?>
	</div>

	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type=submit value="   Gem   "> <input type=reset value="Nulstil formular">
	</form>

	<hr>
	<div class=forside>
	<h1>Slette konto</h1>
        <p>Hvis du ønsker at slette denne ZIP Stat konto, skal du udfylde nedenstående skema og trykke på den meget lange knap.</p>
	<p>Når kontoen er slettet kan du <em>ikke</em> fortryde!</p>
	<h2>Sletning af konto</h2>

	<form action="<?php echo $siteContext->getOption('urlUserAreaMain'); ?>" method=POST target="_top">
	Brugernavn: <input type=text name="brugernavn_slet"><br>
	Kodeord: <input type=password name="kodeord_slet"><br>
	<input type=checkbox name=sletvirkelig> Jeg ønsker at slette min ZIP Stat konto, og ved at når jeg har trykket på knappen &quot;Slet denne ZIP Stat konto - alle mine statistikker bliver slettet!&quot; er mine statistikker slettet for altid.<br>

	<input type="hidden" name="type" value="slet_konto">
	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type="submit" value="Slet denne ZIP Stat konto - alle mine statistikker bliver slettet!"> <input type=reset value="Nulstil formular">

	</form>
	</div>
	<?php

	$utils->echoSiteEnd();
} //End edit user info


/**
 * Displays editing of the user info.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_kodeord(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
//	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Rediger kodeord", 0);

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=get target=\"_top\"><input type=hidden name=type value=\"gemkodeord\">";
	?>
	<h3>&AElig;ndre kodeord</h3>
	<p>For at ændre dit kodeord skal du skrive det nye kodeord <em>to</em> gange, for at sikre mod sålfejl. Det vil kun blive opdateret hvis du skriver det samme kodeord i begge bokse. Det vil <em>ikke</em> blive opdateret hvis du ike skriver noget.<BR>
	Nyt kodeord 1. gang <a href="JAVAscript: alert('Hvis du vil ændre dit kodeord, skal du skrive det nye i boksen.\nDerefter skal du skrive det igen i ænste boks');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til ny tkodeord (1)..."></a>
		<input type=password name=pwd1><BR>
	Nyt kodeord 2. gang <a href="JAVAscript: alert('Hvis du har valgt at ndre dit kodeord ved at skrive det nye\nkodeord i ovenstende boks, skal du skrive det igen i denne\nfør det bliveræ ndret');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til nyt kodeord (2)..."></a>
		<input type=password name=pwd2>
	</P>

	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">
	</form>
	</div>

	<?php

	$utils->echoSiteEnd();
}

/**
 * Displays the form for resetting stats.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_nulstil(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Nulstil", 0);

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_nulstil\">";

	if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
		?>
		<div class=forside>
		<h3>Nulstil</h3>

		<p><input type=checkbox name="nulalt">Nulstil alt.</p>

		<p>Sæt kryds i kassen &quot;Nulstil alt&quot; oven for og tryk ? &quot;Gem&quot;, for at nulstille alle dine
			statistikker. Ved tællerene er det kun hitsne der bliver nulstillet - de enkelte tællere og kliktællere kan
			nulles seperat på tællersiden (brug linket i menuen venstre).</p>
		<p>I den avancerede ZIP Stat kan du nulstille de enkelte statistikker seperat.</p>

		<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
		<input type="submit" value="   Nulstil valgte   "> <input type="reset" value="Nulstil formular">

		</div>
		<?php
	} else {
		?>
		<div class=forside>
		<h3>Nulstil</h3>

		<input type=checkbox name="nulalt"> <a href="JAVAscript: alert('Hvis du krydser af her, vil alt på din statistikside blive nulstillet.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til nulstil alt..."></a>
		Nulstil alt (svarer til at afkrydse alle bokse).<p>
		</div>

		<table class=forside border=1>
		<caption>* Hvis du nulstiller én statistik der er markeret med *, bør du nulstille dem alle tre.<br>Disse benyttes nemlig i prognosen, som forudstter at de dækker samme tidsrum.</caption>
		<tr>
		<td>
		 <input type=checkbox name="nul7"> Samlet antal hits.<br>
		<input type=checkbox name="nul44"> Antal unikke besøgende.<br>
		<input type=checkbox name="nul64"> Hits pr. besøgende.<br>
		<input type=checkbox name="nul16"> Max besøgende på en dag.<br>
		<input type=checkbox name="nul18"> Max besøgende på en måned.<br>
		<input type=checkbox name="nul77"> Max unikke hits på en dag<br>
		<input type=checkbox name="nul80"> Max unikke hits på en måned<br>
		<input type=checkbox name="nul76"> Antal unikke hits i dag<br>
		<input type=checkbox name="nul79"> Antal unikke hits denne måned<br>
		<input type=checkbox name="nul14"> Hits pr. time.<br>
		<input type=checkbox name="nul73"> Tid på siden<br>
		 <input type=checkbox name="nul9"> *Hits pr. måned.<br>
		<input type=checkbox name="nul11"> *Hits 31 dage tilbage.<br>
		<input type=checkbox name="nul15"> *Hits pr. ugedag.<br>
		<input type=checkbox name="nul37"> Alle tællere.<br>
		<td>
		<input type=checkbox name="nul22"> Topdomæner.<br>
		<input type=checkbox name="nul20"> Domner.<br>
		<input type=checkbox name="nul24"> Browsere.<br>
		<input type=checkbox name="nul31"> Oplsning.<br>
		<input type=checkbox name="nul33"> Antal farver.<br>
		<input type=checkbox name="nul35"> JAVA support.<br>
		<input type=checkbox name="nul39"> JAVA-script support.<br>
		<input type=checkbox name="nul46"> Referencesider.<br>
		<input type=checkbox name="nul112"> Indgangssider.<br>
		<input type=checkbox name="nul114"> Udgangssider.<br>
		<input type=checkbox name="nul74"> Bevægelser<br>
		<input type=checkbox name="nul69"> Alle kliktællere.<br>
		<input type=checkbox name="nul47"> Søgeord.<br>
		<input type=checkbox name="nul49"> Søgemaskiner.<br>
		<input type=checkbox name="nul43"> Alle spørgsmål.<br>
		<input type=checkbox name="nul28"> Info om de seneste 20 besøgende.
		</table>

		Hos tællere, spørgsmål og kliktællere nulstilles kun hitsne. Disse kan envidere nulstilles fra de sider hvor de redigeres.
		</P>
		<p>
		<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
		<input type="submit" value="   Nulstil valgte   "> <input type="reset" value="Nulstil formular">

		<?php
	} #Slut på if simpel else
	echo "</form>";
	$utils->echoSiteEnd();
}


/**
 * Displays editing of the simple e-mail stats settings.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_emailstats_simpel(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Mail stats", 0);
	
	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_mailstats\">";

	$viser = explode('::', $datafile->getLine(67));

	$pro_mx_tidspunkter = $lib->pro(9);

	$visAntal = visAntal($lib, count($viser), 9);
	$pro_mx_tidspunkter = $visAntal;

	#Tæller antal dage angivet, og antal datoer angivet
	$ant_dage = 0;
	$ant_datoer = 0;
	$sidste_dag = 0;
	$sidste_dato = 0;
	for ($i = 1; $i <= $pro_mx_tidspunkter; $i++) {
		if (
			(strpos($viser[$i], 'man;;') === 0) or	
			(strpos($viser[$i], 'tir;;') === 0) or 
			(strpos($viser[$i], 'ons;;') === 0) or
			(strpos($viser[$i], 'tor;;') === 0) or
			(strpos($viser[$i], 'fre;;') === 0) or
			(strpos($viser[$i], 'lor;;') === 0) or
			(strpos($viser[$i], 'son;;') === 0)
			) {
			$ant_dage++;
			$sidste_dag = $i;
		}

		if (preg_match("/\A[1-9];;/", $viser[$i]) or preg_match("/\A[1-2][0-9];;/", $viser[$i]) or preg_match("/\A3[0-1];;/", $viser[$i])) {
			$ant_datoer++;
			$sidste_dato = $i;
		}
	}

	#Beslutter hvad de fundne tal betyder
	$mail_om_ugen = '';
	$mail_om_dagen = '';
	$mail_maaned = '';
	if ( ($ant_dage > 0) and ($ant_dage <= 2)) {#n mail om ugen
		$mail_om_ugen = ' SELECTED';
	} else if (($ant_dage > 0) or ($ant_datoer >= 15)) {#n mail om dagen
		$mail_om_dagen = ' SELECTED';
	} else if (($ant_datoer > 0) and ($ant_datoer <= 2)) {#n mail om måneden
		$mail_maaned = ' SELECTED';
	}

	?>
	<div class=forside>
	<h3>Mail stat</h3>
	<p>Send mig 
	<select name=simpel_mailstat size=1>
		<option>aldrig
		<option value=hver_dag<?php echo $mail_om_dagen; ?>>hver dag
		<option value=hver_uge<?php echo $mail_om_ugen; ?>>hver uge
		<option value=hver_maaned<?php echo $mail_maaned; ?>>hver måned
	</select> en e-mail med mine statistikker.</p>
	<p>Vælger du at få mailen hver dag, vil den kommer umiddelbart efter kl. 20. Vælger du at få en mail
	om ugen, vil den komme søndag umiddelbart efter kl. 20. Vælger du at få en mail om måneden, vil den komme
	d. 1. umiddelbart efter kl. 20.</p>
	</div>

	<input type="hidden" name="simpel" value="ja">
	<input type="hidden" value="<?php echo $visAntal; ?>" name="antalVist">
	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">

	<?php

	echo "</form>";

	$utils->echoSiteEnd();
}

/**
 * Displays editing of the advanced e-mail stats settings.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_emailstats(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Mail stats", 0);

	$statKeys = array('enkeltstat', 'prognoser', 'maaned_i_aar', 'sidste_31_dage', 'timer_hits', 'ugedag_hits', 'top_domain', 'domaene_hits', 'info20', 'hits_browser', 'hits_os', 'hits_sprog', 'hits_opl', 'hits_farver', 'java_support', 'js', 'spoergs', 'ref', 'sord', 'smask', 'zipklik', 'bev', 'taellere', 'zipklik');
		if (strpos($datafile->getLine(68), 'alle=vis') !== FALSE) {
			$check['alle'] = ' CHECKED';
			//Fill the ones we'r not using
			for ($i = 0; $i < count($statKeys); $i++)
				$check[$statKeys[$i]] = '';
		} else {
			$check['alle'] = ''; //Fill with nothing
			
			$selectedData = $datafile->getLine(68);
			for ($i = 0; $i < count($statKeys); $i++) {
				if (strpos($selectedData, $statKeys[$i].'=vis') !== FALSE)
					$check[$statKeys[$i]] = ' CHECKED';
				else
					$check[$statKeys[$i]] = '';
			}
	} #Slut på inddata[68] =~/alle=vis/...

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_mailstats\">";
	?>
	<div class=forside>
	<h3>Mail stat</h3>

	<h4>Send følgende statistikker</h4>
	<a href="JAVAscript: alert('Hvis du krydser af her, vil alt på din statistikside nulstillet.\nHvis du vil nulstille noget, atbefaler jeg kraftigt, at du kun\nbenytter denne mulighed for at nulstille, da\nprognoserne på statistiksiden ellers ikke vil være korrekte.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til nulstil alt..."></a>

	<label><input type=checkbox name="alle"<?php echo $check['alle']; ?>> Alle statistikker (anbefales!)</label><BR>
	<a href="JAVAscript: alert('Hvis du har valgt ikke at sætte kryds i ovenstende boks,\\nskal du sætte kryds i en eller flere af de nedenstende.\\nDu vil så på de valgte tidspunkter, få sendt de\\nvalgte statistikker til din e-mail adresse.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjælp til resten..."></a>
	Hvis du ikke har valgt at sætte kryds i ovenstende boks, skal du sætte kryds i en eller flere af nedenstende.

	<table border="1">
	<tr><td><label><input type=checkbox name="enkeltstat"<?php echo $check['enkeltstat']; ?>>Enkeltstende stastikker.</label>
		<td><label><input type=checkbox name="prognoser"<?php echo $check['prognoser']; ?>>Prognoser.</label>
	<tr><td><label><input type=checkbox name="maaned_i_aar"<?php echo $check['maaned_i_aar']; ?>>Hits for de seneste 12 måneder.</label>
		<td><label><input type=checkbox name="sidste_31_dage"<?php echo $check['sidste_31_dage']; ?>>Hits for de seneste 31 dage.</label>
	<tr><td><label><input type=checkbox name="timer_hits"<?php echo $check['timer_hits']; ?>>Hits pr. time.</label>
		<td><label><input type=checkbox name="ugedag_hits"<?php echo $check['ugedag_hits']; ?>>Hits pr. ugedag.</label>
	<tr><td><label><input type=checkbox name="top_domain"<?php echo $check['top_domain']; ?>>Hits pr. topdomæne (.dk, .com osv.)</label>
		<td><label><input type=checkbox name="domaene_hits"<?php echo $check['domaene_hits']; ?>>Hits pr. domæne.</label>
	<tr><td><label><input type=checkbox name="info20"<?php echo $check['info20']; ?>>Info om seneste besøgende.</label>
		<td><label><input type=checkbox name="hits_browser"<?php echo $check['hits_browser']; ?>>Hits pr. browser.</label>
	<tr><td><label><input type=checkbox name="hits_os"<?php echo $check['hits_os']; ?>>Hits pr. styresystem.</label>
		<td><label><input type=checkbox name="hits_sprog"<?php echo $check['hits_sprog']; ?>>Hits pr. sprog.</label>
	<tr><td><label><input type=checkbox name="hits_opl"<?php echo $check['hits_opl']; ?>>Hits pr. skærmopløsning.</label>
		<td><label><input type=checkbox name="hits_farver"<?php echo $check['hits_farver']; ?>>Hits pr. antal understøttede farver (i bits).</label>
	<tr><td><label><input type=checkbox name="java_support"<?php echo $check['java_support']; ?>>JAVA support.</label>
		<td><label><input type=checkbox name="js"<?php echo $check['js']; ?>>JAVA-script support.</label>
	<tr><td><label><input type=checkbox name="taellere"<?php echo $check['taellere']; ?>>Tællere.</label>
		<td><label><input type=checkbox name="spoergs"<?php echo $check['spoergs']; ?>>Spørgsmål og svar.</label>
	<tr><td><label><input type=checkbox name="ref"<?php echo $check['ref']; ?>>Referencesider.</label>
		<td><label><input type=checkbox name="sord"<?php echo $check['sord']; ?>>Søgeord.</label>
	<tr><td><label><input type=checkbox name="smask"<?php echo $check['smask']; ?>>Søgemaskiner.</label>
		<td><label><input type=checkbox name="zipklik"<?php echo $check['zipklik']; ?>>Klikt&aelig;llere</label>
	<tr><td><label><input type=checkbox name="bev"<?php echo $check['bev']; ?>>Bevægelser.</label>
		<td>
	</table>

	<h4>Send statistik med e-mail på følgende tidspunkter</h4>
	<?php

	//Format of this line:
	//time of last mail in unix time::send in hour;;send in day::send in hour;;send in day etc.
	$viser = explode('::', $datafile->getLine(67));

	//$pro_mx_tidspunkter = $lib->pro(9);

	//$visAntal = visAntal($lib, count($viser), 9);
	//$pro_mx_tidspunkter = $visAntal;
	
	//The number of entries to show: The existing + some - (minus) the first entry of $viser
	$showCount = count($viser) + 3 -1;

	$selected = '';
	//for ($i = 1; $i <= $pro_mx_tidspunkter; $i++) {
	//Count from 1 because we use the 1st entry of $viser to something else.
	for ($i = 1; $i <= $showCount; $i++) {
		echo "<label>Tidspunkt $i: <select size=1 name=\"tidspunkt$i\">\n";
		if (! isset($viser[$i]) or strlen($viser[$i]) === 0)	{ $selected = ' SELECTED'; } print "<option value=\"intet\"$selected>-Intet-\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'hda;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"hda\"$selected>Hver dag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'man;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"man\"$selected>Hver mandag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'tir;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"tir\"$selected>Hver tirsdag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'ons;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"ons\"$selected>Hver onsdag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'tor;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"tor\"$selected>Hver torsdag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'fre;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"fre\"$selected>Hver fredag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'lor;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"lor\"$selected>Hver lrdag\n"; $selected = '';
		if (isset($viser[$i]) and strpos($viser[$i], 'son;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"son\"$selected>Hver søndag\n"; $selected = '';
		for ($n = 1; $n <= 31; $n++) {
			if (isset($viser[$i]) and strpos($viser[$i], "$n;;") === 0) { $selected = ' SELECTED'; }
			echo "<option value=\"$n\"$selected>D. $n. i hver måned\n";
			$selected = '';
		}
		echo "</SELECT></label>\n";

		echo " <label>kl. ";
		echo "<SELECT SIZE=1 NAME=\"klokken$i\">\n";
		echo "<option value=\"intet\">-Intet-\n";
		for ($n = 0; $n <= 23; $n++) {
			if (isset($viser[$i]) and strpos($viser[$i], ";;$n") !== FALSE)
				$selected = ' SELECTED';
			echo "<option value=\"$n\"$selected>".($n >= 10 ? $n : '0'.$n).":00\n";
			$selected = '';
		} //End for
		echo "</SELECT></label><br>\n";

	} //End for
	?>
	<p>Løbet ?r for tidspunkter? Når du trykker ? gem kommer der flere.</p>
	</div>
	<input type="hidden" name="showCount" value="<?php echo $showCount; ?>" />
	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">
	<?php

	echo "</form>";

	$utils->echoSiteEnd();
}

/**
 * Displays editing of the click counters.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function r_zipklik(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$utils->echoSiteHead("Rediger kliktællere", 0);
	
	$pro_max_adresser = $lib->pro(10);

	$hits  = explode('::', $datafile->getLine(69));
	$navne = explode('::', $datafile->getLine(70));
	$urler = explode('::', $datafile->getLine(71));

	$okNames = array();
	$okUrls = array();
	$okVisits = array();
	//Remove empty pairs.
	for ($i = 0; $i < count($navne) or $i < count($urler)
	  or $i < count($hits); $i++) {
		if (strlen($navne[$i]) > 0 or strlen($urler[$i]) > 0) {
			$okNames[] = $navne[$i];
			$okUrls[] = $urler[$i];
			$okVisits[] = $hits[$i];
		}
	}

	$navne = $okNames;
	$urler = $okUrls;
	$hits = $okVisits;

	//How many are there?
	if (count($navne) > count($urler))
		$visAntal = count($navne);
	else
		$visAntal = count($urler);
	$visAntal = visAntal($lib, $visAntal, 10);


	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=POST>\n<table border=1 class=forside>";
	echo "<tr><td>Når.</td><td>Navn</td><td>Hits</td><td><small>Nulstil</small></td><td>Link</td></tr>";

	for ($i = 0; $i < $visAntal; $i++) {
		if (! isset($navne[$i]))
			$navne[$i] = '';
		if (! isset($hits[$i]))
			$hits[$i] = '';
		if (! isset($urler[$i]))
			$urler[$i] = '';
		print "<tr><td>$i</td><td><input type=text name=navne$i value=\"".htmlentities($navne[$i])."\" size=6></td><td>".$hits[$i]."</td><td><input type=checkbox name=\"nulstil$i\"></td><td><input type=text name=url$i value=\"".htmlentities($urler[$i])."\" size=45></td></tr>\n";
	}

	echo "</table>\n";

	if ($lib->pro())
		print "<div class=forside>N&aring;r du gemmer dine klikt&aelig;llere, og g&aring;r ind p&aring; denne side igen, vil der v&aelig;re plads til 5 klikt&aelig;llere mere.</div>";
	else
		print "<div class=forside>N&aring;r du gemmer dine klikt&aelig;llere, og g&aring;r ind p&aring; denne side igen, vil der, s&aring; l&aelig;nge du har ledige klikt&aelig;llere, v&aelig;re plads til 5 klikt&aelig;llere mere.</forside>";

	?>
	<input type="hidden" value="<?php echo $visAntal; ?>" name="antalVist">
	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type="hidden" value="gem_zipklik" name="type">
	<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">
	</form>
	<?php

	$utils->echoSiteEnd();
}

/**
 * Saves the user settings.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_indstillinger(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	#ZIP Stat Pro settings

	if ($lib->pro() and ((isset($ind['simpelgem']) and $ind['simpelgem'] !== 'ja') or !isset($ind['simpelgem']))) {

	if (isset($ind['pro_overskrift']) and ($utils->validateString($ind['pro_overskrift']) or strlen($ind['pro_overskrift']) === 0))
		$datafile->setLine(59, $ind['pro_overskrift']);
	
	if (isset($ind['pro_body'])) {
		$datafile->setLine(56, $ind['pro_body']);
	}
	
	if (isset($ind['pro_css']) and ($utils->validateUrl($ind['pro_css']) or strlen($ind['pro_css']) === 0))
		$datafile->setLine(60, $ind['pro_css']);

	$pro_inst = explode('::',$datafile->getLine(58));
	$pro_inst = $lib->addZeros(17, $pro_inst);
	if ($utils->validateInteger($ind['pro_maxref']))
		 $pro_inst[0] = (isset($ind['pro_maxref']) ? $ind['pro_maxref'] : ''); 

	if ($utils->validateInteger($ind['pro_maxipadr']))
		 $pro_inst[1] = (isset($ind['pro_maxipadr']) ? $ind['pro_maxipadr'] : ''); 

	if ($utils->validateInteger($ind['pro_maxbrugere']))
		 $pro_inst[2] = (isset($ind['pro_maxbrugere']) ? $ind['pro_maxbrugere'] : ''); 

	if ($utils->validateInteger($ind['pro_hpbover']))
		 $pro_inst[6] = (isset($ind['pro_hpbover']) ? $ind['pro_hpbover'] : ''); 

	if ($utils->validateInteger($ind['pro_maxdom']))
		 $pro_inst[7] = (isset($ind['pro_maxdom']) ? $ind['pro_maxdom'] : ''); 

	if ($utils->validateInteger($ind['pro_maxsoegeord']))
		 $pro_inst[8] = (isset($ind['pro_maxsoegeord']) ? $ind['pro_maxsoegeord'] : ''); 

	if ($utils->validateInteger($ind['pro_bevaeg']))
		 $pro_inst[11] = (isset($ind['pro_bevaeg']) ? $ind['pro_bevaeg'] : ''); 

	if ($utils->validateInteger($ind['pro_maxindgang']))
		 $pro_inst[16] = (isset($ind['pro_maxindgang']) ? $ind['pro_maxindgang'] : ''); 

	if ($utils->validateInteger($ind['pro_maxudgang']))
		 $pro_inst[17] = (isset($ind['pro_maxudgang']) ? $ind['pro_maxudgang'] : ''); 

	$datafile->setLine(58, implode('::', $pro_inst));

	}#Slut på if pro

	$tillad = array();
	for ($i = 0; $i <= 2; $i++) {
		if (isset($ind['tillad'.$i]))
			$tillad[$i] = 1;
		else
			$tillad[$i] = 0;
	}
	$datafile->setLine(106, implode('::', $tillad));

	$nyhedsbrev = array();
	for ($i = 0; $i <= 6; $i++) {
		if (isset($ind['nyhedsbrev'.$i]))
			$nyhedsbrev[$i] = 1;
		else
			$nyhedsbrev[$i] = 0;
	}
	$datafile->setLine(107, implode('::', $nyhedsbrev));
	
	//Ignore query string on counters.
	if (isset($ind['ignoreQuery']) and strlen($ind['ignoreQuery']) > 0) {
		$datafile->setUserSetting('ignoreQuery', 'true');
	} else {
		$datafile->setUserSetting('ignoreQuery', 'false');
	}

        // Sets the stat site to public only if the field is set and is set to true in lower case: Default to private.
        $datafile->setField('statsitePublic', isset($ind['statsitePublic']) and $ind['statsitePublic'] === 'true');

	if (isset($ind['simpelgem'])) {
            // This functionality is disabled due to the new password management.
	} else {
		#Gemmer brugerkodeord
		$tmp = isset($ind['brugerkodeord']) ? $ind['brugerkodeord'] : '';
		$tmp = str_replace(array("\n"), array("::"), $tmp);
		while (strpos($tmp, '::::') !== FALSE)
			$tmp = str_replace(array('::::'), array('::'), $tmp);
		while (strpos($tmp, '::') === 0)
			$tmp = substr($tmp, 2);
		while (strpos($tmp, '::') === strlen($tmp)-3)
			$tmp = substr($tmp, 0, -2);

		$datafile->setLine(57, $tmp);
	}

	if ($utils->getUAType() !== $utils->UA_TYPE_SIMPLE) {
		$fej = '';
		#Forger standardtælleren med denne værdi.
		$datafile->setLine(82, (isset($ind['standardop']) ? $ind['standardop'] : ''));

		#Sletter evt. den registrerede ip-adresse
		if (isset($ind['sletipadr']))
			$datafile->setLine(52, '');

		#Sætter send-videre adressen
		if (!isset($ind['taelopredirect']) or strlen($ind['taelopredirect']) === 0 or $utils->validateUrl($ind['taelopredirect'])) {
			$datafile->setLine(53, isset($ind['taelopredirect']) ? $ind['taelopredirect'] : '');
		} else if ($utils->validateUrl('https://'.$ind['taelopredirect'])) {
			$datafile->setLine(53, 'https://'.$ind['taelopredirect']);
			$fej .= "Der blev automatisk indsat <code>https://</code> i starten af din adresse, fordi en korrekt internetadresse starter med dette. Starter din med http? Se at få TLS på.";
		}	else {
			$fej .= "Din send-videreadresse blev ikke opdateret, fordi den ikke har den korrekte syntax for en internetadresse.";
		}

		#Gemmer "registrer ikke hits her"-adresser
		if ($lib->pro())
			$okSiderAntal = isset($ind['okSiderAntal']) ? $ind['okSiderAntal'] : '';
		else
			$okSiderAntal = $lib->pro(15);

		$okSider = array();
		for ($i = 0; $i < $okSiderAntal; $i++)
			$okSider[$i] = isset($ind['okSider'.$i]) ? $ind['okSider'.$i] : '';
		$datafile->setLine(111, implode('::', $okSider));
	} #Slut på if not simpel

	if (isset($ind['ikkeop'])) {
		if (isset($ind['username']))
			setcookie($ind['username'], 'ikkeop', time()+60*60*24*365*3, '/', '.'.$siteContext->getOption('domain'));
	} else {
		if (isset($ind['username']))
			setcookie($ind['username'], 'op', time()+60*60*24*365*3, '/', '.'.$siteContext->getOption('domain'));
	}

	//Handle the pro password
	if (! $lib->pro() and isset($ind['prokodeord']) and strlen($ind['prokodeord']) > 0) {
		$prokodeord = array(
		"zippro", "nettips","computerworld","fyens","stiften","tv2fyn","comon","intpro"
		);

		for ($i = 0; $i < count($prokodeord); $i++) {
			if ($ind['prokodeord'] == $prokodeord[$i]) {
				$datafile->setLine(61, 1034310769);
				$add = "<li>Du har nu ZIP Stat Pro, gratis indtil ".localtime(1034310769).". God fornøjelse!<br>Vælg menupunktet &quot;Indstillinger&quot; i menuen til venstre, for at foretage de nye indstillinger du har mulighed for med ZIP Stat Pro. <b>OBS</b> Dette fremgår som en fejl, men det er det <em>ikke</em>.";
			}
		}

		if (! isset($add)) {
			$add = "<li>Det pro-kodeord du angav er forkert. Tryk på din browsers &quot;Tilbage&quot; knap, og se efter om du har tastet <em>helt</em> rigtigt. Husk at der er forskel på store og små bogstaver! Virker kodeordret stadig ikke efter det, så skriv til <a href=\"mailto:".$siteContext->getOption('errorEMail')."\">".$siteContext->getOption('errorEMail')."</a>. Skriv pro-kodeordret, samt hvor du har fået det fra. Hvis et magarsin har lavet en trykfejl opretter jeg et pro-kodeord der svarer til trykfejlen.";
		}
	}

	$problemer = (isset($fej) ? $fej : '').(isset($add) ? $add : '');
	$utils->saveData($datafile, "<p>Dine indstillinger er gemt</p>\n", $problemer, 'kunHvisProblemer');
	r_indstillinger($utils, $siteContext);
}

/**
 * Saves the user information.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_oplysninger(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	#Gemmer navn, email, url og sidetitel
	$datafile->setLine(1, (isset($ind['navn']) ? $ind['navn'] : ''));
	$datafile->setLine(2, (isset($ind['e-mail']) ? $ind['e-mail'] : ''));
	$datafile->setLine(3, (isset($ind['url']) ? $ind['url'] : ''));
	$datafile->setLine(4, (isset($ind['titel']) ? $ind['titel'] : ''));
	
	#Gemmer om siden er ok for folk under 18
	if (isset($ind['under18ok']) and $ind['under18ok'] === 'Ja') {
		$datafile->setLine(83, 'erotik');
		$kun_over_18_aar = 1;
	} else if(isset($ind['under18ok']) and $ind['under18ok'] === 'Nej') {
		$datafile->setLine(83, 'okunder18');
		$kun_over_18_aar = 0;
	} else {
		$datafile->setLine(83, '');
	}

	#Gemmer oplysninger til indekset
	$pro_max_kategorier = $lib->pro(14); #<11-Antal mulige kategorier i indekset>
	$pro_max_tegn_sord =  $lib->pro(12); #<12-Max antal tegn til søgeord>
	$pro_max_tegn_besk =  $lib->pro(13); #<13-Max antal tegn i beskrivelse>

#-------------
/*
if ($options{'use_index'} == 1) {
	require $stier{'index_rediger'}; #Lib. med subrutiner til at redigere i indexfilerne.
	#Indsamler oplysninger om selectbox'ene
	$inddata[86] =~ s/\n//g;
	$inddata[87] =~ s/\n//g;
	@kategorier = explode(/::/,$inddata[86]);
	@placeringer = explode(/::/,$inddata[87]);

	#Tager hjde for at samme kategori kan være valgt i flere felter.
	#Tjekker ogs om man har valgt at siden ikke er ok for folk under 18,
	#men at man har valgt en kategori hvor dette ikke er tilladt.
	my @opdater = ();
	for ($i = 0;$i < $pro_max_kategorier;$i++)
		{
		for ($n = 0;$n < $pro_max_kategorier;$n++)
			{
			if ( #Er samme kategori valgt to gange?
				($ind{'kategorier'.($i+1)} eq $ind{'kategorier'.($n+1)}) and ($i != $n)
				or #Har en ikke ok under 18 r-side valgt en anden kategori end Kun_over_18_aar?
				(($kun_over_18_aar) and (! $ind{'kategorier'.($i+1)} =~ /Kun_over_18_aar/i))
				)
				{
				$ind{'kategorier'.($n+1)} = "";
				}
			}
		}

	for ($i = 0;$i < $pro_max_kategorier;$i++)
		{
		if ( ($ind{'kategorier'.($i+1)} ne "") and ($kategorier[$i] ne $ind{'kategorier'.($i+1)}) and (&findesDenneKategori($ind{'kategorier'.($i+1)})) )
			{ #Ny kategori
			if ($kategorier[$i])
				{
				&removeBruger($kategorier[$i],$ind{'brugernavn'});
				$opdater[@opdater] = $kategorier[$i];
				}
			&addNyBruger($ind{'kategorier'.($i+1)},$ind{'brugernavn'});
			$kategorier[$i] = $ind{'kategorier'.($i+1)};
			$placeringer[$i] = "/";
			$opdater[@opdater] = $ind{'kategorier'.($i+1)};
			}
		elsif ($ind{'kategorier'.($i+1)} eq "")
			{ #Fjern fra denne
			&removeBruger($kategorier[$i],$ind{'brugernavn'});
			$opdater[@opdater] = $kategorier[$i];
			$kategorier[$i] = "";
			$placeringer[$i] = "";
			}
		}
	$inddata[86] = join("::",@kategorier)."\n";
	$inddata[87] = join("::",@placeringer)."\n";
	
	#Tjekker om man har skrevet for mange søgeord.
		#Den brokker sig dog kun hvis der er mere end 5 tegn for meget
	if (length($ind{'sord'}) > $pro_max_tegn_sord + 5)
		{ $opdateret .= "	<p>Dine søgeord fylder ".length($ind{'sord'})."tegn, men de må kun fylde $pro_max_tegn_sord tegn, så derfor er de ikke blevet opdateret.</p>\n"; }
	else
		{
		$ind{'sord'} =~ s/\n\r//g;
		$inddata[85] = $ind{'sord'}."\n";
		}

	#Tjekker om beskrivelsen fylder for mange tegn.
		#Den brokker sig dog kun hvis der er mere end 5 tegn for meget
	if (length($ind{'beskrivelse'}) > $pro_max_tegn_besk + 5)
		{
		$opdateret .= "	<p>Din beskrivelse fylder ".length($ind{'beskrivelse'})." tegn, men den må kun fylde $pro_max_tegn_besk tegn, så derfor er den ikke blevet opdateret.</p>\n";
		}
		else
		{
		$ind{'beskrivelse'} =~ s/\n//g;
		$inddata[84] = $ind{'beskrivelse'}."\n";
		}

} #End of if ($options{'use_index'} == 1) - use index or not.
#--------------
*/

/*
if ($options{'use_index'} == 1)
{
	#Opdaterer ndrede kategorier
	if (@opdater)
		{ 	require $stier{'index_opdater'}; }

	#open (HH,">hh.txt");
	foreach $opdaterKat (@opdater)
		{
		@inddata = ();
		$opdaterKat =~ s/\A\///;
		$opdaterKat =~ s/\/\Z//;
	#	&opdater($stier{'index_base'}."/".$opdaterKat."/index.txt");
	##print HH $stier{'index_base'}."/".$opdaterKat."/index.txt\n";
	#print HH $i.": ".$opdaterKat."\n";	
	#print HH "\nMMMMMMMM\n".$stier{'index_base'}."/".$opdaterKat."/index.txt";
	#open (FM,$stier{'index_base'}."/".$opdaterKat."/index.txt");
	#@fm = <FM>;
	#print HH @fm;

		}
} #End of if ($options{'use_index'} == 1) - use index or not.
*/

	$utils->saveData($datafile, "<p>Dine oplysninger er gemt</p>\n",(isset($problemer) ? $problemer : ''), 'kunHvisProblemer');
	r_oplysninger($utils, $siteContext);
}

/**
 * Saves the user information.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function slet_konto(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();
	
	if (!isset($ind['kodeord_slet']) or strlen($ind['kodeord_slet']) === 0) {
		$utils->echoSiteHead("Intet kodeord");
		echo "<div class=forside>\n<h1>Intet kodeord</h1>\n<p>Du indtastede <em>ikke</em> dit kodeord. Det skal du gøre for at du ikke kommer til at slette din konto ved et uheld. Tryk på din browsers &quot;tilbage&quot;-knap og indtast det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if (!isset($ind['brugernavn_slet']) or strlen($ind['brugernavn_slet']) === 0) {
		$utils->echoSiteHead("Intet brugernavn");
		echo "<div class=forside>\n<h1>Intet brugernavn</h1>\n<p>Du indtastede <em>ikke</em> dit brugernavn. Det skal du gøre for at du ikke kommer til at slette din konto ved et uheld. Tryk på din browsers &quot;tilbage&quot;-knap og indtast det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

        $authFactory = new AuthenticationFactory($siteContext->getOptions());
        $auth = $authFactory->create();
        if (! $auth->doAuthenticate($ind['username'], $ind['kodeord_slet'])) {
		$utils->echoSiteHead("Forkert kodeord");
		echo "<div class=forside>\n<h1>Forkert kodeord</h1>\n<p>Det kodeord du indtastede er forkert. Tryk på din browsers &quot;tilbage&quot;-knap og ret det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if ($ind['username'] !== $ind['brugernavn_slet']) {
		$utils->echoSiteHead("Forkert brugernavn");
		echo "<div class=forside>\n<h1>Forkert brugernavn</h1>\n<p>Det brugernavn du indtastede er forkert. Tryk på din browsers &quot;tilbage&quot;-knap og ret det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if (!isset($ind['sletvirkelig']) or strlen($ind['sletvirkelig']) === 0) {
		$utils->echoSiteHead("Du satte ikke hak");
		echo "<div class=forside>\n<h1>Du satte ikke hak</h1>\n<p>Du skal s&aelig;tte hak i kassen <i>Jeg ønsker at slette min ZIP Stat konto, og ved at n&aring;r jeg har trykket p&aring; knappen &quot;Slet denne ZIP Stat konto - alle mine statistikker bliver slettet!&quot; er mine statistikker slettet for altid.</i>. Dette er for at sikre, at du ikke kommer til at slette din ZIP Stat konto ved et uheld. Tryk på din browsers &quot;tilbage&quot;-knap og st hak.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	$datafile->deleteUser();
	$utils->echoSiteHead("Din konto er slettet");
	echo "<div class=forside>\n<p>Din ZIP Stat konto er nu slettet. Kontroller venligst at du ikke l&aelig;ngere kan logge ind. Tak fordi du valgte at bruge ZIP Stat indtil nu.</p></div>";
	$utils->echoSiteEnd();
	exit;
}

/**
 * Resets the requested stats.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_nulstil(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$nul = explode('::', $datafile->getLine(51));
	$nul = $lib->addZeros(114, $nul);

	if (isset($ind['nul7']) or strlen($ind['nul7']) > 0 or isset($ind['nulalt']) or strlen($ind['nulalt']) > 0) {
		$datafile->setLine(7, '0');
		$datafile->setLine(8, $utils->getShortDate());
	}

	if ((isset($ind['nul9']) and strlen($ind['nul9']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(9, '');
		$datafile->setLine(10, '0');
		$nul[9] = $utils->getShortDate();
	}

	if ((isset($ind['nul11']) and strlen($ind['nul11']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(11, '');
		$datafile->setLine(12, '');
		$nul[11] = $utils->getShortDate();
	}

	if ((isset($ind['nul14']) and strlen($ind['nul14']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(14, '');
		$nul[14] = $utils->getShortDate();
	}

	if ((isset($ind['nul15']) and strlen($ind['nul15']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(15, '');
		$nul[15] = $utils->getShortDate();
	}

	if ((isset($ind['nul16']) and strlen($ind['nul16']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(16, '0');
		$datafile->setLine(17, '');
		$nul[16] = $utils->getShortDate();
	}

	if ((isset($ind['nul18']) and strlen($ind['nul18']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(18, '0');
		$datafile->setLine(19, '');
		$nul[18] = $utils->getShortDate();
	}

	if ((isset($ind['nul20']) and strlen($ind['nul20']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(20, '');
		$datafile->setLine(21, '');
		$nul[20] = $utils->getShortDate();
	}

	if ((isset($ind['nul22']) and strlen($ind['nul22']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(22, '');
		$datafile->setLine(23, '');
		$nul[22] = $utils->getShortDate();
	}

	if ((isset($ind['nul24']) and strlen($ind['nul24']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(24, '');
		$datafile->setLine(25, '');
		$nul[24] = $utils->getShortDate();
	}

	if ((isset($ind['nul26']) and strlen($ind['nul26']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(26, '');
		$datafile->setLine(27, '');
		$nul[26] = $utils->getShortDate();
	}

	if ((isset($ind['nul28']) and strlen($ind['nul28']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(28, '');
		$nul[28] = $utils->getShortDate();
	}

	if ((isset($ind['nul29']) and strlen($ind['nul29']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(29, '');
		$datafile->setLine(30, '');
		$nul[29] = $utils->getShortDate();
	}

	if ((isset($ind['nul31']) and strlen($ind['nul31']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(31, '');
		$datafile->setLine(32, '');
		$nul[31] = $utils->getShortDate();
	}

	if ((isset($ind['nul33']) and strlen($ind['nul33']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(33, '');
		$datafile->setLine(34, '');
		$nul[33] = $utils->getShortDate();
	}

	if ((isset($ind['nul35']) and strlen($ind['nul35']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(35, '');
		$datafile->setLine(36, '');
		$nul[35] = $utils->getShortDate();
	}

	if ((isset($ind['nul37']) and strlen($ind['nul37']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(37, '');
		$nul[37] = $utils->getShortDate();
	}

	if ((isset($ind['nul39']) and strlen($ind['nul39']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(39, '');
		$datafile->setLine(40, '');
		$nul[39] = $utils->getShortDate();
	}

	if ((isset($ind['nul43']) and strlen($ind['nul43']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(43, '');
		$nul[43] = $utils->getShortDate();
	}

	if ((isset($ind['nul44']) and strlen($ind['nul44']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(44, '');
		$datafile->setLine(45, '');
		$nul[44] = $utils->getShortDate();
	}

	if ((isset($ind['nul46']) and strlen($ind['nul46']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(46, '');
		$nul[46] = $utils->getShortDate();
	}

	if ((isset($ind['nul47']) and strlen($ind['nul47']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(47, '');
		$datafile->setLine(48, '');
		$nul[47] = $utils->getShortDate();
	}

	if ((isset($ind['nul49']) and strlen($ind['nul49']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(49, '');
		$datafile->setLine(50, '');
		$nul[49] = $utils->getShortDate();
	}

	if ((isset($ind['nul54']) and strlen($ind['nul54']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(54, '');
		$nul[54] = $utils->getShortDate();
	}

#if ($ind{'nul64'} or $ind['nulalt'])
#	{
#	$inddata[64] = "0::0::0\n";
#	$nul[64] = &kortdato;
#	}

	if ((isset($ind['nul64']) and strlen($ind['nul64']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(64, '');
		$datafile->setLine(65, "0::0::0");
		$nul[64] = $utils->getShortDate();
	}

	if ((isset($ind['nul69']) and strlen($ind['nul69']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(69, '');
		$datafile->setLine(69, '');
		$nul[69] = $utils->getShortDate();
	}

	if ((isset($ind['nul73']) and strlen($ind['nul73']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(73, "0:0");
		$nul[73] = $utils->getShortDate();
	}

	if ((isset($ind['nul74']) and strlen($ind['nul74']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(74, '');
		$datafile->setLine(75, '');
		$nul[74] = $utils->getShortDate();
	}

	if ((isset($ind['nul76']) and strlen($ind['nul76']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(76, '0');
		$nul[76] = $utils->getShortDate();
	}

	if ((isset($ind['nul77']) and strlen($ind['nul77']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(77, '');  #Mske med 0
		$datafile->setLine(78, '');
		$nul[77] = $utils->getShortDate();
	}

	if ((isset($ind['nul79']) and strlen($ind['nul79']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(79, '0');
		$nul[79] = $utils->getShortDate();
	}

	if ((isset($ind['nul80']) and strlen($ind['nul80']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(80, ''); #Mske med 0
		$datafile->setLine(81, '');
		$nul[80] = $utils->getShortDate();
	}

	if ((isset($ind['nul112']) and strlen($ind['nul112']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(112, '');
		$datafile->setLine(113, '');
		$nul[112] = $utils->getShortDate();
	}

	if ((isset($ind['nul114']) and strlen($ind['nul114']) > 0) or (isset($ind['nulalt']) and strlen($ind['nulalt']) > 0)) {
		$datafile->setLine(114, '');
		$datafile->setLine(115, '');
		$nul[114] = $utils->getShortDate();
	}

	$datafile->setLine(51, implode('::',$nul));

	$utils->saveData($datafile, "<p>De ønskede statistikker er nu nulstillet.</p>\n", '', 'kunHvisProblemer');
	r_nulstil($utils, $siteContext);
	exit;
}

/**
 * Saves the new password.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_kodeord(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

         if (isset($ind['pwd1']) and isset($ind['pwd2']) and $ind['pwd1'] === $ind['pwd2'] and trim($ind['pwd1']) !== '') {
            $authFactory = new AuthenticationFactory($siteContext->getOptions());
            $auth = $authFactory->create();
            $auth->updatePasswordHash($ind['username'], $ind['pwd1'], '');
            header('Location: '.$siteContext->getOption('urlUserArea').'?username='.urlencode($ind['username']).'&start_type=adminmain&start=gemkodeord_ok');
	    exit;
	} else if (isset($ind['pwd1']) or isset($ind['pwd2'])) {
		$utils->echoSiteHead("Fejl");
		echo "Dit <em>kodeord</em> blev <em>ikke</em> opdateret, da du ikke havde skrevet det samme i de to bokse. Du skal derfor fortsat bruge dit <em>gamle kodeord</em>!<BR>Tryk p&aring; din browsers &quot;Tilbage&quot;-knap og skrive dit nye kodeord to gange.";
		$utils->echoSiteEnd();
		exit;
	} else {
		$utils->echoSiteHead("Intet kodeord");
		echo "Du skrev intet kodeord i nogle af boksene - du er <em>n&oslash;d</em> til at have et kodeord. Du skal altså fortsat bruge dit gamle kodeord! Tryk p&aring; din browsers &quot;Tilbage&quot;-knap, for at &aelig;ndre dit kodeord, eller for at benytte brugerområdet.";
		$utils->echoSiteEnd();
		exit;
	}

}

/**
 * Tells the user that the password is saved.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gemkodeord_ok(&$utils, &$siteContext) {
	$utils->echoSiteHead("Kodeord &aelig;ndret", 0);
	echo "Dit kodeord er nu &aelig;ndret.";
	$utils->echoSiteEnd();
	exit;
}

/**
 * Saves the users polls.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_spoergs(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$sp = explode('::', $datafile->getLine(41));
	$sv = explode(',,', $datafile->getLine(42));
	$hi = explode(',,', $datafile->getLine(43));

	$maxNrSp = -1;
	$maxNrSv = -1;
	$antalVistSp = (isset($ind['antalVistSp']) ? $ind['antalVistSp'] : 0);
	$antalVistSv = (isset($ind['antalVistSv']) ? $ind['antalVistSv'] : 0);

	#Udregner hvor mange svar og spørgsmål der er brugt
	#Hvis der er et svar i et spørgsmål, men ikke noget spørgsmål, skal det alligevel medtages.
	for ($i = 0; $i < $antalVistSp; $i++) {
		$udfyldteSv = 0;
		for ($n = 0; $n < $antalVistSv; $n++) {
			if (isset($ind["sp$i"."sv$n"]) and strlen($ind["sp$i"."sv$n"]) > 0 and ($n > $maxNrSv))
				$maxNrSv = $n;
			if (isset($ind["sp$i"."sv$n"]) and strlen($ind["sp$i"."sv$n"]) > 0)
				$udfyldteSv = 1;
		} //End inner for

		if ((isset($ind["spoergs$i"]) and strlen($ind["spoergs$i"]) > 0) or ($udfyldteSv !== 0))
			$maxNrSp = $i;
	} //End outer for

	$noQuestions = $maxNrSp+1;
	$noAnswers = $maxNrSv+1;

	if ($lib->pro()) { #Sætter antallet, hvis man har pro
		$lib->setPro(3, $noQuestions);
		$lib->setPro(4, $noAnswers);
	} else {
		if ($noQuestions > $lib->pro(3)) #Srger for at der ikke gemmes flere end man m, hvis ikke pro
			$noQuestions = $lib->pro(3);
		if ($noAnswers > $lib->pro(4)) #Srger for at der ikke gemmes flere end man m, hvis ikke pro
			$noAnswers = $lib->pro(4);
	}

	$sp = array();
	for ($i = 0;$i < $antalVistSp; $i++) {
		$sp[$i] = isset($ind["spoergs$i"]) ? $ind["spoergs$i"] : '';
	}

	for ($i = 0; $i < $antalVistSp; $i++) {
		if (isset($sv[$i]))
			$svar = explode('::', $sv[$i]);
		else
			$svar = array();
		for ($n = 0; $n < $antalVistSv; $n++) {
			$tgk = "sp$i" . "sv$n";
			$svar[$n] = isset($ind[$tgk]) ? $ind[$tgk] : '';
		}
		if (count($svar) > $noAnswers)
			$svar = array_slice($svar, 0, $noAnswers);
		$sv[$i] = implode('::', $svar);
	}

	for ($i = 0; $i < $antalVistSp; $i++) {
		if (isset($ind["nulstilsp$i"])) {
			$doto = $i + 1;
			$hi[$i] = "0::0::0::0::0";
			$nulst .= "nr. " . $doto . ", ";
		}
	}

	if (count($sp) > $noQuestions)
		$sp = array_slice($sp, 0, $noQuestions);
	if (count($sv) > $noQuestions)
		$sv = array_slice($sv, 0, $noQuestions);
	if (count($hi) > $noQuestions)
		$hi = array_slice($hi, 0, $noQuestions);
	$datafile->setLine(41, implode('::', $sp));
	$datafile->setLine(42, implode(',,', $sv));
	$datafile->setLine(43, implode(',,', $hi));

	if (isset($nulst) and strlen($nulst) > 0) {
		$nulst = substr($nulst, 0, -2);
		$nulst = "<p>Spørgsmålene $nulst blev nulstillet.</p>\n";
	} else {
		$nulst = '';
	}
	if (!isset($problemer))
		$problemer = '';

	$utils->saveData($datafile, "<p>Dine spørgsmål er gemt</p>$nulst\n", $problemer, 'kunHvisProblemer');
	r_spoer($utils, $siteContext);
}

/**
 * Saves the counters.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_taellere(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$hits = explode('::', $datafile->getLine(37));
	$navne = explode('::', $datafile->getLine(38));

	if ($lib->pro()) {
			# nr,antal
		$lib->setPro(5, isset($ind['pro_taellere']) ? $ind['pro_taellere'] : 0);
	}

	$pro_max_taellere = $lib->pro(5);

	$nulst = '';
	for ($i = 0; $i <= $pro_max_taellere; $i++) {
		$navne[$i] = (isset($ind["navntaeller$i"]) ? $ind["navntaeller$i"] : '');
		if (isset($ind["nultael$i"])) {
			$hits[$i] = 0;
			$nulst .= "nr. $i, ";
		}
	}

	if (isset($nulst)) {
		$nulst = substr($nulst, 0, -2);
		$nulst = "<p>Tællerene $nulst blev nulstillet.</p>\n";
	}

	$datafile->setLine(37, implode('::',$hits));
	$datafile->setLine(38, implode('::',$navne));

	if (!isset($problemer))
		$problemer = '';
	$utils->saveData($datafile, "<p>Dine tællere er gemt</p>$nulst\n", $problemer, 'kunHvisProblemer');
	r_taellere($utils, $siteContext);
}

/**
 * Saves the mail stat settings.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_mailstats(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	list($sms) = explode('::', $datafile->getLine(67));

	#<select name=simpel_mailstat size=1>
	#	<option value=hver_dag>en gang om dagen
	#	<option value=hver_uge>en gang om ugen
	#	<option value=hver_maaned>en gang om måneden

	if (isset($ind['simpel']) and $ind['simpel'] === 'ja') {
		if (isset($ind['simpel_mailstat'] ) and $ind['simpel_mailstat'] === 'hver_dag') {
			$ind["tidspunkt1"] = "man";
			$ind["tidspunkt2"] = "tir";
			$ind["tidspunkt3"] = "ons";
			$ind["tidspunkt4"] = "tor";
			$ind["tidspunkt5"] = "fre";
			$ind["tidspunkt6"] = "lor";
			$ind["tidspunkt7"] = "son";
			for ($i = 1; $i <= 7; $i++)
				$ind["klokken$i"] = 20;
		} else if (isset($ind['simpel_mailstat']) and $ind['simpel_mailstat'] === "hver_uge") {
			$ind["tidspunkt1"] = "son";
			$ind["klokken1"] = 20;
		} else if (isset($ind['simpel_mailstat']) and $ind['simpel_mailstat'] === "hver_maaned") {
			$ind["tidspunkt1"] = 1;
			$ind["klokken1"] = 20;
		}
	} #Slut på if simpel

	if (isset($ind['alle']) or isset($ind['simpel']) and $ind['simpel'] === "ja")
		$vis = "alle=vis";
	else {
		$vis = '';
		if (isset($ind['enkeltstat'])) $vis .= "enkeltstat=vis&";
		if (isset($ind['prognoser'])) {$vis .= "prognoser=vis&";}
		if (isset($ind['maaned_i_aar'])) $vis .= "maaned_i_aar=vis&";
		if (isset($ind['sidste_31_dage'])) $vis .= "sidste_31_dage=vis&";
		if (isset($ind['timer_hits'])) $vis .= "timer_hits=vis&";
		if (isset($ind['ugedag_hits'])) $vis .= "ugedag_hits=vis&";
		if (isset($ind['top_domain'])) $vis .= "top_domain=vis&";
		if (isset($ind['domaene_hits'])) $vis .= "domaene_hits=vis&";
		if (isset($ind['info20'])) $vis .= "info20=vis&";
		if (isset($ind['hits_browser'])) $vis .= "hits_browser=vis&";
		if (isset($ind['hits_os'])) $vis .= "hits_os=vis&";
		if (isset($ind['hits_sprog'])) $vis .= "hits_sprog=vis&";
		if (isset($ind['hits_opl'])) $vis .= "hits_opl=vis&";
		if (isset($ind['hits_farver'])) $vis .= "hits_farver=vis&";
		if (isset($ind['java_support'])) $vis .= "java_support=vis&";
		if (isset($ind['js'])) $vis .= "js=vis&";
		if (isset($ind['spoergs'])) $vis .= "spoergs=vis&";
		if (isset($ind['ref'])) $vis .= "ref=vis&";
		if (isset($ind['sord'])) $vis .= "sord=vis&";
		if (isset($ind['smask'])) $vis .= "smask=vis&";
		if (isset($ind['zipklik'])) $vis .= "zipklik=vis&";
		if (isset($ind['bev'])) $vis .= "bev=vis&";
		if (isset($ind['taellere'])) $vis .= "taellere=vis&";
		if (isset($ind['zipklik'])) $vis .= "zipklik=vis&";

		$vis = substr($vis, 0, -1);
	}
	$datafile->setLine(68, $vis);

	//$pro_mx_tidspunkter = $lib->pro(9);

//	$antalVist = isset($ind['antalVist']) ? $ind['antalVist'] : '';
	$showCount = isset($ind['showCount']) ? $ind['showCount'] : 0;
/*	$maxNr = 0;
	for ($i = 0; $i < $antalVist; $i++) {
		if (
				(
					(isset($ind["tidspunkt$i"]) and strlen($ind["tidspunkt$i"]) > 0 and $ind["tidspunkt$i"] !== "intet")
					and
					(isset($ind["klokken$i"]) and strlen($ind["klokken$i"]) > 0 and $ind["klokken$i"]*1 >= 0 and $ind["klokken$i"]*1 <= 23)
				)
			)
			{
			$maxNr = $i;
			}
		}

	if ($lib->pro()) #Sætter antallet, hvis man har pro
		$lib->setPro(9, $maxNr+1);
	else if ($maxNr > $lib->pro(9)) #Srger for at der ikke gemmes flere end man m, hvis ikke pro
		$maxNr = &pro(9);

	$pro_mx_tidspunkter = $maxNr;
*/
	$sendp = "";
	for ($i = 1; $i <= $showCount; $i++) {
		if (isset($ind["tidspunkt$i"]) and $ind["tidspunkt$i"] !== "intet") {
			if (
					($ind["tidspunkt$i"] === "man") or
					($ind["tidspunkt$i"] === "tir") or
					($ind["tidspunkt$i"] === "ons") or
					($ind["tidspunkt$i"] === "tor") or
					($ind["tidspunkt$i"] === "fre") or
					($ind["tidspunkt$i"] === "lor") or
					($ind["tidspunkt$i"] === "son") or
					($ind["tidspunkt$i"] === "hda") #Hver dag
				)
				{
				$mdato = $ind["tidspunkt$i"];
			} else if (($ind["tidspunkt$i"] > 0) and ($ind["tidspunkt$i"] <= 31)) {
				$mdato = $ind["tidspunkt$i"]*1;
			}	#Slut på if $ind["klokken$i"] ne intet elsif $ind{"klokken$i"}> 0 and $ind{"klokken$i"}<= 23
		}	else {	#Slut på if ind klokken$i
			$mdato = "";
		}

		if (isset($ind["klokken$i"]) and ($ind["klokken$i"]*1 >= 0) and ($ind["klokken$i"]*1 <= 23)) {
			$mtid = $ind["klokken$i"];
		} else {
			$mtid = "";
		}

		if (isset($mdato) and strlen($mdato) > 0 and $mdato !== 0 and isset($mtid) and strlen($mtid) > 0) {
			$sendp .= $mdato . ";;" . $mtid . "::";
		}
	} //End for

	if (isset($sendp) and strlen($sendp) >= 2)
		$sendp = substr($sendp, 0, -2);

	$datafile->setLine(67, $sms . "::" . $sendp);

	if (!isset($problemer))
		$problemer = '';

	$utils->saveData($datafile, "<p>Dine mail stats oplysninger er gemt</p>\n", $problemer, 'kunHvisProblemer');

	if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE)
		r_emailstats_simpel($utils, $siteContext);
	else
		r_emailstats($utils, $siteContext);

}

/**
 * Saves the click counters.
 * 
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gem_zipklik(&$utils, &$siteContext) {
	$lib = &$siteContext->getCodeLib();
	$ind = $lib->getHTTPVars();
	$datafile = &$lib->getDatafil();

	$maxNr = 0;
	$antalVist = isset($ind['antalVist']) ? $ind['antalVist'] : 0;

	#Udregner hvor mange kliktællere der er brugt
	for ($i = 0; $i < $antalVist; $i++) {
	if (
			(
				(isset($ind["navne$i"]) and $utils->validateString($ind["navne$i"]))
				or
				(isset($ind["url$i"]) and $utils->validateUrl($ind["url$i"]))
			)
		)
		{
		$maxNr = $i;
		}
	}

	if ($lib->pro()) #Sætter antallet, hvis man har pro
		$lib->setPro(10, $maxNr+1);
	else if ($maxNr > $lib->pro(10)) #Srger for at der ikke gemmes flere end man m, hvis ikke pro
		$maxNr = $lib->pro(10);

	$hits =  explode('::', $datafile->getLine(69));
	$navne = explode('::', $datafile->getLine(70));
	$urler = explode('::', $datafile->getLine(71));

	$problemer = '';
	for ($i = 0; $i <= $maxNr; $i++) {
		if (isset($ind["navne$i"]) and $utils->validateString($ind["navne$i"])) {
			$navne[$i] = $ind["navne$i"];
		} else {
			$problemer .= "<li>Det angivne navn for nr. $i ($str) indeholder tegn det ikke m&aring; indeholde. Det m&aring; kun indeholde tegnene a-z (store og sm&aring; bogstaver), tallene 0-9 samt en - og en _. Navnet blev ikke gemt.";
		}

		if (isset($ind["url$i"]) and
		 ($utils->validateUrl($ind["url$i"]) or strlen($ind["url$i"]) === 0)) {
			$urler[$i] = isset($ind["url$i"]) ? $ind["url$i"] : '';
		} else {
			if (isset($ind["navne$i"])) {
				$adr = $ind["url$i"];
				$problemer .= "<li>Klikt&aelig;ller nr. $i ($adr) er ikke en gyldig internetadresse, og blev derfor ikke gemt.\n";
			}
		} #Slut på if okurl or not ind url else
		if (isset($ind["nulstil$i"])) {
			$hits[$i] = 0;
		}

	} //End for

	$datafile->setLine(69, implode('::', $hits));
	$datafile->setLine(70, implode('::', $navne));
	$datafile->setLine(71, implode('::', $urler));

	if (!isset($problemer))
		$problemer = '';

	$utils->saveData($datafile, "<p>Dine klikt&aelig;llere er gemt</p>\n", $problemer, 'kunHvisProblemer');
	r_zipklik($utils, $siteContext);

}

?>
