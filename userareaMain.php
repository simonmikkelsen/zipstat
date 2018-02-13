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
	gem_zipklik($utils, $siteContext);
} else if ($gotoSite === 'backup') {
	show_backup($utils, $siteContext);
} else if ($gotoSite === 'dlbackup') {
	download_backup($utils, $siteContext);
} else {
	$utils->showMessage("V√lg funktion","V√¶lg funktion i menuen til venstre. Husk at benytte Gem-knapperne i bunden af siderne for at gemme de ndringer. De bliver ikke gemt hvis du forladerne siderne via menuerne eller hvis du skifter mellem simpelt og avanceret brug.");
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
<p><cite>Rigtige m√¶nd tager ikke backup...</cite></p>
<h2>Intro</h2>
<p>ZIP Stat tager ikke dine data som gidsel: Du kan n√•r som helst <a target="_blank" href="http://zipstat.org/">downloade ZIP Stat</a> og √re det hos dig selv. Du kan endda starte en konkurerende service!</p>
<p>ZIP Stat benytter databasen MySQL (eller de frie alternativer s√som MariaDB) og de data du kan downloade er formateret til at blive brugt af den. Benyt kun disse data til nyeste version af ZIP Stat. ZIP Stat kan nogle gange automatisk opgradere et √¶ldre dataformat, men en gammel version af ZIP Satat kan ikke bruge nyere data.</p>
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

	$utils->echoSiteHead("Rediger tllere", 0);

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_taellere\">";

	if ($lib->pro()) {
		#$qs = $ENV{'QUERY_STRING'};
		#$qs =~ s/\&navneloese\=skjul//ig;
		#$qs =~ s/\&navneloese\=vis//ig;
		$qs = "username=".$ind['username']."&amp;type=rtaellere";

		#$pro_tekst = "<p>Du kan forge eller formindse antallet af tllere p siden &quot;Indstillinger&quot; (brug linket i menuen til hjre)</P>.\n";
		if ($ind['navneloese'] === "skjul")
			$pro_tekst = "<a href=\"".$siteContext->getOption('urlUserAreaMain')."?$qs&amp;navneloese=vis\">Vis navnelse tllere...</A><br>\n";
		else
			$pro_tekst = "<a href=\"".$siteContext->getOption('urlUserAreaMain')."?$qs&amp;navneloese=skjul\">Skjul navnelse tllere...</A><br>\n";

		$pro_tekst .= "Antal t√¶llere <input type=text name=\"pro_taellere\" value=\"".$lib->pro(5)."\" size=3>\n";
		$pro_tekst .= "<a href=\"JAVAscript: alert('Som standard har du 50 t√¶llere, men hvis du har brug for flere ellere √rre, kan du indtaste antallet her. Du b√∏r dog ikke stte tallet til mere end nogle hundrede, da mange √llere dels vil g√∏re din statistik mere uoverskuelig, men selve statistikken bliver o√ langsommere, n√•r der skal holder styr √ store m√¶ngder data.');\"><img src=\"".$siteContext->getPath('zipstat_icons')."/stegn2.gif\" width=9 height=14 border=0 alt=\"√lp til antal t√¶llere...\"></a><br>\n";
	} else {
		$pro_tekst = '';
	}

?>
<div class=forside>
<p>Hvis du vil √¶ndre eller slette dens navn, √ g√∏r det i kassen ud for √lleren. Hvis du vil nulstille en t√¶ller, √ s√¶t kryds i kassen ud for den espektive t√¶ller.</p>
<?php echo $pro_tekst; ?>
<form action="<?php echo $siteContext->getOption('urlUserAreaMain'); ?>" method=POST>
<table border=1>
<tr><td>Nr.</td><td>Navn</td><td>Nulstil</td><td>Hits</td></tr>
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

	$utils->echoSiteHead("Sp√gsm√•l", 0);

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
		<a href="JAVAscript: alert('Hvis du vil √¶ndre √ et sp√∏rgs√l, skal du skrive √¶ndringen i den vnrsre boks.\nHvis du vil √¶ndre svarene, skal du skriv√ndringerne i de\nmindre bokse.\nDu kan nulstille antal svar (hits) hvert svar har fet, ved at\ns√¶tte kryds i den lille kasse.\nHvis du ikke skriver noget i et svarfelt, vil svaret ikke blive givet som svarmulighed.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="√lp til rediger sp√rgsm√•l..."></a>
		<p align=center><table border=1 class=forside><caption>Hvis du ikke skriver noget i et svarfelt, vil svaret ikke blive vist.</caption>
		<tr><td colspan=3>
		<big align=center>Sprgsml <?php echo $dy; ?></big>
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
		echo "<div class=forside>Nr du gemmer dine sprgsml og svar, og gr ind p denne side igen, vil der altid vre plads til endnu et nyt sprgsml samt 3 ydligere svar til hvert sprgsml.</div>";
	} else {
		echo "<div class=forside>Nr du gemmer dine sprgsml og svar, og gr ind p denne side igen, vil der, s lnge du har nogle ledige, vre plads til endnu et nyt sprgsml samt 3 ydligere svar til hvert sprgsml.</div>";
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
			$pro_kode .= "<div class=forside><h2>Pro indstillinger</h2>\n<p>Nr du har valgt den simple udgave af ZIP Stat, har du ikke mulighed for at ndre p dine pro-indstillinger. For at gre dette skal du skifte til avanceret, hvilket du kan gre via linket &quot;Skift til avanceret brug&quot; i menuen til venstre.</div>";
		} else {
			$pro_kode .= "<div class=forside><h3>Pro indstillinger</h3>\n<p>Hvis der ikke str noget i en boks benyttes standardvrdien. ";
			if ($siteContext->getOption('always_pro') !== 1)
				$pro_kode .= "Du har ZIP Stat Pro.</p>";
			else
				$pro_kode .= "Du har ZIP Stat Pro p ubestemt tid.</p>";

			$pro_kode .= "<table border=0>\n";
			$pro_kode .= "<tr><td>Overskrift til statistiksiden</td><td><input type=text name=\"pro_overskrift\" value=\"".htmlentities($datafile->getLine(59))."\">";
					$pro_kode .= "<a href=\"JAVAscript: alert('Hvis du vil have en anden overskrift p statistiksiden, end den der er der i forvejen, skal du skrive den her. Hvis du vil have den overskrift der benyttes p den normale ZIP Stat, skal du ikke skrive noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til overskrift p statistiksiden...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Indhold af body-tag</td><td><tt>&lt;BODY&nbsp;</tt><input type=text name=\"pro_body\" value=\"".htmlentities($datafile->getLine(56))."\"><tt>&gt;</tt>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Hvis du vil have andre farver p din statistikside, end dem der er nu, skal du skrive attributterne der skal vre i sidens BODY tag. Hvis du vil have farverne p den normale ZIP Stat, skal du ikke skrive noget.\\nVrd opmrksom p, at visse farver p siden er sat via CSS (StyleSheets). Disse farver mm. skal derfor ndres via CSS (se hjlpen til det nste punkt).');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til BODY tagget...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Link til CSS-fil</td><td><input type=text name=\"pro_css\" value=\"".htmlentities($datafile->getLine(60))."\">\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Hvis du vil benytte et CSS (StyleSheet) til at ndre farver ol. p statistiksiden, s skal du angive adressen til CSS filen her. Husk http:// foran!\\nDer er benyttet class tags til at specificere udseendet af forskellige tags - kig i HTML\\'en eller se oversigten p hjlpesiden.\\nHvis du vil bruge det CSS der er p den normale ZIP Stat, s lad feltet vre tomt.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til CSS...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Max antal referencesider</td><td><input type=text name=\"pro_maxref\" value=\"".(isset($pro_inst[0]) ? $pro_inst[0] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('P den normale ZIP Stat bliver der hjest registreret de seneste 50 referencesider. Hvis du vil have registreret flere, s skriv antallet her. Det anbefales dog at antallet holder under 100, da man normalt ikke kan bruge resten til noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til max antal referencesider...\"></a></td></tr>\n";

		$pro_kode .= "<tr><td>Max antal indgangssider</td><td><input type=text name=\"pro_maxindgang\" value=\"".(isset($pro_inst[16]) ? $pro_inst[16] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('P den normale ZIP Stat bliver der hjest registreret de seneste 50 indgangssider. Hvis du vil have registreret flere, s skriv antallet her. Det anbefales dog at antallet holder under 100, da man normalt ikke kan bruge resten til noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til max antal indgangssider...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Max antal udgangssider</td><td><input type=text name=\"pro_maxudgang\" value=\"".(isset($pro_inst[17]) ? $pro_inst[17] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('P den normale ZIP Stat bliver der hjest registreret de seneste 50 udgangssider. Hvis du vil have registreret flere, s skriv antallet her. Det anbefales dog at antallet holder under 100, da man normalt ikke kan bruge resten til noget.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til max antal udgangssider...\"></a></td></tr>\n";

		$pro_kode .= "<tr><td>Max antal IP-adresser (til unikke hits)</td><td><input type=text name=\"pro_maxipadr\" value=\"".(isset($pro_inst[1]) ? $pro_inst[1] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Nr der registreres unikke besgende foregr det ved at gemme en unik adresse hver besgende har (IP-adressen), og kun tlle op hvis denne ikke er registreret. Normalt bliver de seneste 50 IP-adresser registreret, hvilket er rigeligt er mere end rigeligt for de fleste sider. Men hvis man har mere end ca. 500 hits pr. dag er 50 IP-adresser ikke altid nok, og man br derfor stte tallet op.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til antal IP-adresser...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Oplysninger om max</td><td><input type=text name=\"pro_maxbrugere\" value=\"".(isset($pro_inst[2]) ? $pro_inst[2] : '')."\" size=3> antal brugere\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard gemmes der detaljerede oplysninger om de seneste 20 besgende - dette kan du stte op (eller ned) her. Du br dog ikke stte tallet til mere end ca. 100, da dette dels vil betyde din statistikside er lnge om at blive indlst, men registreringen af din statistik vil ogs blive langsommere, hvis der er for mange data at holde styr p. Endelig kan man slet ikke overskue ret mange af disse, og pga. de store mngder data er denne statistik noget der fylder meget i datafilen - og jeg har ikke ret meget plads til disse!');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til oplysninger om X antal besgende...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Antal forskellige domner</td><td><input type=text name=\"pro_maxdom\" value=\"".(isset($pro_inst[7]) ? $pro_inst[7] : '')."\" size=3>";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard registreres der 100 forskellige domner. Hvis du nsker der registreres flere (hvis de nederste p listen har fet mindre end 10 hits p en mned, anbefales det ikke at f registreret), kan du stte dette tal op. Du kan ogs stte det ned, hvis det kun er fx. de 20 verste p statistiksiden der reelt bliver talt op i lbet af en uges tid.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til max antal forskellige domner...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Antal forskellige sgeord</td><td><input type=text name=\"pro_maxsoegeord\" value=\"".(isset($pro_inst[8]) ? $pro_inst[8] : '')."\" size=3>";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard registreres der 100 forskellige sgeord. Hvis du nsker der registreres flere (hvis de nederste p listen har fet mindre end 10 hits p en mned, anbefales det ikke at f registreret), kan du stte dette tal op. Du kan ogs stte det ned, hvis det kun er fx. de 20 verste p statistiksiden der reelt bliver talt op i lbet af en uges tid.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til max antal forskellige sgeord...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Hits pr. besgende beregnes over </td><td><input type=text name=\"pro_hpbover\" value=\"".(isset($pro_inst[6]) ? $pro_inst[6] : '')."\" size=3> uger\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard beregnes hits pr. besgende over 3 uger, men sider med meget f hits vil med fordel kunne stte dette tal i vejret. Fr ens side mange hits kan godt stte tallet ned, hvis man nsker det mest mulige aktuelle resultat.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til antal svar pr. sprgsml...\"></a></td></tr>\n";
		$pro_kode .= "<tr><td>Antal forskellige bevgelser </td><td><input type=text name=\"pro_bevaeg\" value=\"".(isset($pro_inst[11]) ? $pro_inst[11] : '')."\" size=3>\n";
					$pro_kode .= "<a href=\"JAVAscript: alert('Som standard har du mulighed for 50 forskellige bevgelser. Dem kan du bruge til at se hvilke sider folk bevger sig imellem. ');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til antal bevgelser...\"></a></td></tr>\n";
			$pro_kode .= "</table>\nAntal tllere, kliktllere samt sprgsml og svar kan ndres p deres respektive redigeringssider.</div>\n";
		}#Slut p if simpel else
	}
else #Hvis man ikke har pro
	{
	$pro_kode = '';
	$faa_pro = "<hr>\n<div class=forside><h2>F ZIP Stat Pro</h2>";
	$faa_pro .= "<p>Du kan f ZIP Stat Pro gratis i ca. 1 r. Den njagtige dato vil fremg verst p denne side, nr du har ZIP Stat Pro. Det eneste du skal gre er at skrive et pro-kodeord i kassen forneden, og trykke p &quot;Gem&quot; knapper lidt hjere oppe. Lige nu fungere flgende pro-kodeord:<br><code>intpro</code></p>";
	$faa_pro .= "<p>Indtast pro-kodeord for at f gratis ZIP Stat Pro: <input type=text name=prokodeord size=8></p>\n<p>Nr din gratis pro-periode er ved at udlbe, vil du f nogle f e-mails med besked om dette. Du vil s mod et mindre belb (nok ca. 200 kr.) f mulighed for at forlnge din pro-periode med 2 r. nsker du ikke at forlnge pro-perioden til den tid, skal du bare ignorere e-mailsne, og nr perioden er endeligt udlbet, vil du helt automatisk skifte til den gratis udgave af ZIP Stat.</div>";
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
		#Kodeord p statistiksiden
		if (strlen($datafile->getLine(57)) > 0)
			$chk = ' CHECKED';
		else
			$chk = '';

	if (isset($pro_kode)) 
		echo $pro_kode;	
	echo "<div class=forside><h3>Kodeord p√• statistiksiden</h3>\n<pKodeord p√ statistiksiden kan kun sl√s til via det avancerede brugeromr√de. Du kan skifte til det nederst i menuen til venstre.</p>\n";

	?>

	<div class=forside>
	<h3>Bliv ikke selv talt med</h3>
	<p><input type=checkbox name="ikkeop"<?php echo $ikopchek; ?>> T√¶l aldrig mig med i min statistik</P>
	<p>OBS. Hvis du skifter til en anden computer eller en anden internet-browser, skal du g√• ind √•p denne side og krydse af igen.</p>
	</div>

	<br>

	<input type="hidden" value="<?php echo $ind['username']; ?>" name="username">
	<input type="hidden" value="ja" name="simpelgem">
	<input type="hidden" value="true" name="saved">
	<input type="submit" value="   Gem   "> <input type="reset" value="Nulstil formular">
	<?php
	if (isset($faa_pro))
		echo $faa_pro;
	} #Slut p if simpel
else
	{
	if (isset($pro_kode))
		echo $pro_kode;

	?>
	<div class=forside>
	<h2>St tllerens startvrdi</h2>
	<p>Du har her mulighed for at forge den tller der tller antal hits p hele siden seneste nulstilning. Hvis du gr det, vil det st p statistiksiden, og det vil ikke pvirke tlletallet p toplisten. Du kan skrive et negativt tal, ved at stte et &quot;-&quot; (minus) foran tallet.</p>
	<p>Tl op med <input type=text name=standardop value="<?php echo htmlentities($datafile->getLine(82)); ?>"> hits.</p>
	</div>
	<?php

	#Kodeord til statistiksiden
	echo "<div class=forside>\n";
	echo "<h2>Kodeord til statistiksiden</h2><p>nsker du ikke at andre skal kunne se dine statistikker, kan du stte kodeord p statistiksiden.</p>";

	print "<TEXTAREA NAME=\"brugerkodeord\" ROWS=\"5\" COLS=\"10\">";
	echo htmlentities(str_replace("::", "\n", $datafile->getLine(57)));
	print "</TEXTAREA>Kun t kodeord pr. linie";

	print "<a href=\"JAVAscript: alert('Hvis du vil have man skal skrive et kodeord for at kunne se din statistikside, skal du skrive dem her. t kodeord pr. linie. Hvis du ikke skriver nogle kodeord, skal man ikke bruge kodeord for at se statistiksiden. Disse kodeord giver kan kun benyttes til adgangskontrol til statistiksiden - de kan ikke bruges andre steder.');\"><img src=\"".htmlentities($siteContext->getPath('zipstat_icons'))."/stegn2.gif\" width=9 height=14 border=0 alt=\"Hjlp til kodeord p statistiksiden...\"></a>\n";
	print "</div><br>\n";

	#Sprringer
	$tillad = explode('::', $datafile->getLine(106));
	for ($i = 0;$i <= 2;$i++) {
		if ($tillad[$i])
			$tillad[$i] = ' CHECKED';
		else
			$tillad[$i] = '';
	}

	?>
	<div class=forside>
	<h2>Sprringer</h2>
	<p>Du kan her sprre eller tillade visning af statistikker p forskellige mder.</p>
	<input type=checkbox name="tillad0"<?php echo $tillad[0]; ?>> Vis siden p toplisten<br>
	<input type=checkbox name="tillad1"<?php echo $tillad[1]; ?>> Tillad visning af statistikker via javascriptstats og ministatistik<br>
	<input type=checkbox name="tillad2"<?php echo $tillad[2]; ?>> Tillad visning af tlletal med tllerbilleder<br>
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
	<p>Du kan her bestemme hvilke typer e-mail du nsker at modtage fra ZIP Stat.<br>
	Send mig e-mails med besked om flgende:</p>
	<input type=checkbox name="nyhedsbrev0"<?php echo $nyhedsbrev[0]; ?>> Strre opdateringer af ZIP Stat (anbefales)<br>
	<input type=checkbox name="nyhedsbrev1"<?php echo $nyhedsbrev[1]; ?>> Mindre opdateringer af ZIP Stat<br>
	<input type=checkbox name="nyhedsbrev2"<?php echo $nyhedsbrev[2]; ?>> Nr en fejl er konstateret, inkl. et gt p hvornr den er rettet<br>
	<input type=checkbox name="nyhedsbrev3"<?php echo $nyhedsbrev[3]; ?>> Nr en strre fejl er rettet (anbefales)<br>
	<input type=checkbox name="nyhedsbrev4"<?php echo $nyhedsbrev[4]; ?>> Nr en mindre fejl er rettet<br>
	<input type=checkbox name="nyhedsbrev5"<?php echo $nyhedsbrev[5]; ?>> Andre nyhedsbreve om ZIP Stat<br>
	<input type=checkbox name="nyhedsbrev6"<?php echo $nyhedsbrev[6]; ?>> Andre nyhedsbreve om andre services fra <?php echo htmlentities($siteContext->getOption('adminName').' og '.$siteContext->getOption('domain')); ?><br>
	</div>
	
	<?php
	
		//Ignore query string on counters.
		$counterIgnoreQuery = ($datafile->getUserSetting('ignoreQuery') !== "false");
		$ignoreQueryChecked = ($counterIgnoreQuery ? "checked=\"checked\"" : "");
		
		?>
	<div class=forside>
	<h2>Tllere</h2>
		<label>
			<input type="checkbox" name="ignoreQuery"<?php echo $ignoreQueryChecked;?> />
			Fjern en eventuel &quot;query string&quot; adresserne i tllere.
		</label>
		<p>En <b>query string</b> er den del af en web-adresse der kommer efter et sprgsmlstegn, fx i <code>http://zipstat.dk/userarea.php?username=zip</code> er det &quot;<code>?username=zip</code>&quot; som er query string. Hvis du er i tvivl, s st kryds i denne boks.</p>
	</div>
		<?php

		#Tl kun hits p disse sider
		$okSider = explode('::', $datafile->getLine(111));
		$okSideHtml = '';

	if ($lib->pro()) {
		for ($i = 0; $i < count($okSider) +2; $i++) {
			$okSideHtml .= "	<input type=text name=\"okSider$i\" value=\"";
			if (isset($okSider[$i]))
				$okSideHtml .= $okSider[$i];
			$okSideHtml .= "\" size=45><br>\n";
		}
		$okSideHtml .= "Nr du trykker p &quot;Gem&quot;-knappen og gr ind p siden, kommer der 2 nye, tomme bokse.<br>\n";
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
	<h2>Registrer kun hits p disse sider</h2>
	<p>Hvis en anden person benytter din obligatoriske kode, sprgsml/svar-kode eller dine kliktllere, kan du her vlge, at der kun m registreres hits p de sider, du angiver her. Skriver du ikke noget, vil der blive registreret hits p alle sider.</p>
	<p>Starter du en adresse med <code>http://</code> eller <code>https://</code>, skal adressen, til den side der m registreres hits p, <em>starte</em> med denne adresse. Skriver du ikke <code>http://</code> eller <code>https://</code>, vil der blive registreret hits fra alle sider, hvis adresse blot <em>indeholder</em> det du har skrevet.</p>
	<?php echo $okSideHtml; ?>
	</div>
	<?php

	#Ikke selv talt med: cookie
	?>
	<p>
	<div class=forside>
	<h2>Bliv ikke selv talt med</h2>
	<h3>Via cookies</h3>
	<p>Vil du hellere benytte en lsning hvor der benyttes cookies, s st kryds i nste kasse og tryk p &quot;Gem&quot;. Du fjerner cookien ved at fjerne krydset og trykke p &quot;Gem&quot;.</p>

	<p><input type=checkbox name="ikkeop"<?php echo $ikopchek; ?>> Tl aldrig mig (denne browser p denne computer) med i statistikken
	</P>

	<h3>Via IP-adresse</h3>
	Hvis du henter siden <a href="<?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?>" target="_top"><?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?></A>
	i din browser, vil du ikke selv blive registreret af ZIP Stat nr du besger dine egne sider. Dette krver dog at du henter 
	adressen <b>hver</b> gang du gr p Internettet (dvs. hver gang du ringer op med dit modem - hvis du har fast IP-adresse <small>[hvis du har det, ved du det helt sikkert]</small>).
	Den letteste mde at gre dette p er, at du stter adressen som din startside (se her under for en instruktion). Den side din browser normalt starter med, skal du s skrive i kassen herunder. S vil du nsten ikke opdage denne funktion.<BR>

	Send-videre adresse: <br><input type=text name="taelopredirect" value="<?php echo htmlentities($datafile->getLine(53)); ?>" size="35"><br>
	Slet IP-adresse: <input type=checkbox name=sletipadr> Har du en fast IP-adresse, og ikke nsker at benytte denne funktion lngere, kan du slette den sidst registrerede IP-adresse, s du igen bliver talt med i din statistik.
	</p>

	</div>

	<input type=hidden value="<?php echo $ind['username']; ?>" name=username>
	<input type="hidden" value="true" name="saved">
	<input type=submit value="   Gem   "> <input type=reset value="Nulstil formular">

	<hr>
	<div class=forside>
	<h3>Sdan ndrer du din startside</h3>
	<h4>I Netscape</h4>
	<P>I selve browseren vlger du menuen Rediger/Edit. Her vlge du punktet Prferencer/Preferences. S vlger du kategorien Navigator. Hvis der str noget i feltet Adresse/Adress, skal du skrive det i kassen mrket &quot;Send-videre&quot; adresse (her p siden). Nu skriver du <tt><?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?></tt> i kassen Adresse/Adress (i Netscape), og trykker OK. S trykker p Gem-knappen her p siden, og s virker det!</P>

	<h4>I Internet Eksplorer</h4>
	<P>I selve browseren vlger du menuen Vis/View. Her vlger du punktet Internet-indstillinger/Intenret-options. Derefter vlger du fanen Generet/General. Hvis der str noget i feltet Adresse/Adress, skal du skrive det i kassen mrket Send-videre adresse (her p siden). Nu skriver du <tt><?php echo $siteContext->getOption('urlIgnore').'?'.$ind['username']; ?></tt> i kassen Adresse/Adress (i IE), og trykker OK. S trykker p Gem-knappen her p siden, og s virker det!</P>
	</div>

	<?php echo $faa_pro; ?>

	<?php
		} #Slut p if simpel else
	echo  "</form>";

	if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
		?>
	<h2>I avanceret visning</h2>
	<p>Hvis du skifter til avanceret visning (benyt linket &quot;Skift til avanceret visning&quot;i menuen til venstre), kan du ogs gre flgende p denne side:
	<ul>
		<li>Stter den overordnede tllers startvrdi. Dette er nyttigt hvis du nsker at &quot;tage gamle hits med&quot; fra en anden tller eller statistik.
		<li>Angive selvvalgte kodeord til statistiksiden. Disse kodeord kan f.eks. gives til andre, da de kun giver adgang til statistiksiden.
		<li>Foretage sprringer, s du selv kan vlge om
		<ul>
			<li>din side skal med p toplisten.
			<li>nogle af dine statistikker skal vre tilgngelige gennem javascript-stats og ministatistikken.
			<li>antal hits for hele siden, samt for de enkelte tllere, skal kunne vises via en grafisk tller.
		</ul>
		<li>Selv vlge hvilke typer nyhedsbreve du nsker at modtage fra ZIP Stat - du kan fx. vlge alle fra.
		<li>Vlge at en bestemt IP-adresse ikke skal tlles med i statistikken. Dette er praktisk hvis du har en fast internetforbindelse med fast IP-adresse.
		<li>Vlge at du kun vil have registreret statistikker fra nogle bestemte sider.
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
		$pro_max_tegn_sord = $lib->pro(12); #<12-Max antal tegn til sgeord>
		$pro_max_tegn_besk = $lib->pro(13); #<13-Max antal tegn i beskrivelse>

		$kategorier = '';
		require $stier{'index_rediger'}; #Lib. med subrutiner til at redigere i indexfilerne.

		#Udskriver selectbox'e med kategorier
		$inddata[86] =~ s/\n//g;
		@kate_stier = split(/::/,$inddata[86]);
		for ($i = 1;$i <= $pro_max_kategorier;$i++)
			{ $kategorier .= "Kategori nr. $i ".&getMuligeKategorier("kategorier$i",$kate_stier[$i-1],$kun_over_18_aar)."<br>\n"; }

		$kategorier .= "<p>Hvis ZIP Stats administrator vurderer at en side passer bedre i en anden kategori, kan han flytte siden. Dette sker dog typisk kun i forbindelse med oprettelse af nye underkategorier.</p>\n<p>Sider med indhold der ikke br ses af brn og unge under 18 r, m kun placeres i kategorien &quot;Kun over 18 r&quot; samt dennes underkategorier.</p>\n<p>Sider med indhold der, efter den danske lovligning, kan karakteriseres som ulovligt, m ikke tilfjes til indekset!</p>";
	} #End of if $options{'use_index'} - use index or not

	#84-Beskrivelse
	#85-Ngleord
	*/

	?>
	<div class=forside>
	<form action="<?php echo $siteContext->getOption('urlUserAreaMain'); ?>" method=POST>
	<input type=hidden name=type value="gem_oplysninger">
	<table>
	<tr><td>Navn</td><td><a href="JAVAscript: alert('Hvis du vil ndre dit navn, s skriv ndringen i boksen.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til navn..."></a>
		<input type=text name="navn" value="<?php echo htmlentities($datafile->getLine(1)); ?>"></td></tr>
	<tr><td>E-mail</td><td><a href="JAVAscript: alert('Hvis du vil ndre din e-mail adresse, s skriv ndringen i boksen.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til e-mail..."></a>
		<input type=text name="e-mail" value="<?php echo htmlentities($datafile->getLine(2)); ?>"></td></tr>
	<tr><td>Siden adresse</td><td><a href="JAVAscript: alert('Hvis du vil ndre din hjemmesides adresse, skal du skrive ndringen her.\nDet er vigtigt den er korrekt, fordi den bruges til at sortere dine egne sider\nfra, p listen over referencesider.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til sidens adresse..."></a>
		<input type=text name="url" value="<?php echo htmlentities($datafile->getLine(3)); ?>"></td></tr>
	<tr><td>Sidens titel</td><td><a href="JAVAscript: alert('Hvis du vil ndre sidens titel, s skriv ndringen i boksen.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til sidens titel..."></a>
		<input type=text name="titel" value="<?php echo htmlentities($datafile->getLine(4)); ?>"></td></tr>
	</table>
	</div>
	<?php
	/*
	<div class=forside>
		<p>
		Sgeord <input type=text name=sord value="<?php echo htmlentities($datafile->getLine(85)); ?>" maxlength=$pro_max_tegn_sord> max. $pro_max_tegn_sord tegn, adskildt af komma (, ).<br>
		Beskrivelse <input type=text name=beskrivelse value="<?php echo htmlentities($datafile->getLine(84)); ?>" maxlength=$pro_max_tegn_besk> max $pro_max_tegn_besk tegn, skal beskrive sidens <em>indhold</em>.<br>
		Overdrevent brug af udrbstegn, store bogstaver ol. vil automatisk blive rettet, samt trkke ned i rangeringen ved sgninger.<br>
		</p>
		$kategorier
	*/
	?>
	<p>Indeholder siden erotisk, pornografisk materiale eller andet der ikke br ses af brn under og unge 18 r?<br>
	<select size=1 name=under18ok>
	<option value="">-vlg-
	<option value="Ja"<?php echo $sel['erotik']; ?>>Ja
	<option value="Nej"<?php echo $sel['okunder18']; ?>>Nej
	</select>
	</p>
	<?php
	/*
	<p>Sider med erotisk indhold ol. vil p toplisten blive nedtonet, og man fr en advarsel fr man gr ind p statistiksiden.</p>
	*/
	?>
	</div>

	<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
	<input type=submit value="   Gem   "> <input type=reset value="Nulstil formular">
	</form>

	<hr>
	<div class=forside>
	<h1>Slette konto</h1>
        <p>Hvis du &oslash;nsker at slette denne ZIP Stat konto, skal du udfylde nedenst√•ende skema og trykke p√• den meget lange knap.</p>
	<p>N√•r kontoen er slettet kan du <em>ikke</em> fortryd!</p>
	<h2>Sletning af konto</h2>

	<form action="<?php echo $siteContext->getOption('urlUserAreaMain'); ?>" method=POST target="_top">
	Brugernavn: <input type=text name="brugernavn_slet"><br>
	Kodeord: <input type=password name="kodeord_slet"><br>
	<input type=checkbox name=sletvirkelig> Jeg √∏nsker at slette min ZIP Stat konto, og ved atn√•r jeg har trykket p√• knappen &quot;Slet denne ZIP Stat konto - alle mine statistikker bliver slettet!&quot; er mine statistikker slettet for altid.<br>

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
	<p>For at √¶ndre dit kodeord skal du skrive det nye kodeord <em>to</em> gange, for at sikre mod s√•lfejl. Det vil kun blive opdateret hvis du skriver det samme kodeord i begge bokse. Det vil <em>ikke</em> blive opdateret hvis du ike skriver noget.<BR>
	Nyt kodeord 1. gang <a href="JAVAscript: alert('Hvis du vil √¶ndre dit kodeord, skal du skrive det nye i boksen.\nDerefter skal du skrive det igen i √¶nste boks');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hj√¶lp til ny tkodeord (1)..."></a>
		<input type=password name=pwd1><BR>
	Nyt kodeord 2. gang <a href="JAVAscript: alert('Hvis du har valgt at ndre dit kodeord ved at skrive det nye\nkodeord i ovenstende boks, skal du skrive det igen i denne\nf√∏r det bliver√¶ ndret');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hj√¶lp til nyt kodeord (2)..."></a>
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

		<p>S√¶t kryds i kassen &quot;Nulstil alt&quot; oven for og tryk √ &quot;Gem&quot;, for at nulstille alle dine
			statistikker. Ved t√¶llerene er det kun hitsne der bliver nulstillet - de enkelte √¶tllere og klik√¶tllere kan
			nulles seperat p√• √llersiden (brug linket i menuen venstre).</p>
		<p>I den avancerede ZIP Stat kan du nulstille de enkelte statistikker seperat.</p>

		<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
		<input type="submit" value="   Nulstil valgte   "> <input type="reset" value="Nulstil formular">

		</div>
		<?php
	} else {
		?>
		<div class=forside>
		<h3>Nulstil</h3>

		<input type=checkbox name="nulalt"> <a href="JAVAscript: alert('Hvis du krydser af her, vil alt p din statistikside blive nulstillet.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til nulstil alt..."></a>
		Nulstil alt (svarer til at afkrydse alle bokse).<p>
		</div>

		<table class=forside border=1>
		<caption>* Hvis du nulstiller n statistik der er markeret med *, br du nulstille dem alle tre.<br>Disse benyttes nemlig i prognosen, som forudstter at de dkker samme tidsrum.</caption>
		<tr>
		<td>
		 <input type=checkbox name="nul7"> Samlet antal hits.<br>
		<input type=checkbox name="nul44"> Antal unikke besgende.<br>
		<input type=checkbox name="nul64"> Hits pr. besgende.<br>
		<input type=checkbox name="nul16"> Max besgende p en dag.<br>
		<input type=checkbox name="nul18"> Max besgende p en mned.<br>
		<input type=checkbox name="nul77"> Max unikke hits p en dag<br>
		<input type=checkbox name="nul80"> Max unikke hits p en mned<br>
		<input type=checkbox name="nul76"> Antal unikke hits i dag<br>
		<input type=checkbox name="nul79"> Antal unikke hits denne mned<br>
		<input type=checkbox name="nul14"> Hits pr. time.<br>
		<input type=checkbox name="nul73"> Tid p siden<br>
		 <input type=checkbox name="nul9"> *Hits pr. mned.<br>
		<input type=checkbox name="nul11"> *Hits 31 dage tilbage.<br>
		<input type=checkbox name="nul15"> *Hits pr. ugedag.<br>
		<input type=checkbox name="nul37"> Alle tllere.<br>
		<td>
		<input type=checkbox name="nul22"> Topdomner.<br>
		<input type=checkbox name="nul20"> Domner.<br>
		<input type=checkbox name="nul24"> Browsere.<br>
		<input type=checkbox name="nul31"> Oplsning.<br>
		<input type=checkbox name="nul33"> Antal farver.<br>
		<input type=checkbox name="nul35"> JAVA support.<br>
		<input type=checkbox name="nul39"> JAVA-script support.<br>
		<input type=checkbox name="nul46"> Referencesider.<br>
		<input type=checkbox name="nul112"> Indgangssider.<br>
		<input type=checkbox name="nul114"> Udgangssider.<br>
		<input type=checkbox name="nul74"> Bevgelser<br>
		<input type=checkbox name="nul69"> Alle kliktllere.<br>
		<input type=checkbox name="nul47"> Sgeord.<br>
		<input type=checkbox name="nul49"> Sgemaskiner.<br>
		<input type=checkbox name="nul43"> Alle sprgsml.<br>
		<input type=checkbox name="nul28"> Info om de seneste 20 besgende.
		</table>

		Hos tllere, sprgsml og kliktllere nulstilles kun hitsne. Disse kan envidere nulstilles fra de sider hvor de redigeres.
		</P>
		<p>
		<input type="hidden" value="<?php echo htmlentities($ind['username']); ?>" name="username">
		<input type="submit" value="   Nulstil valgte   "> <input type="reset" value="Nulstil formular">

		<?php
	} #Slut p if simpel else
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

	#Tller antal dage angivet, og antal datoer angivet
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
	} else if (($ant_datoer > 0) and ($ant_datoer <= 2)) {#n mail om mneden
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
		<option value=hver_maaned<?php echo $mail_maaned; ?>>hver mned
	</select> en e-mail med mine statistikker.</p>
	<p>Vlger du at f mailen hver dag, vil den kommer umiddelbart efter kl. 20. Vlger du at f en mail
	om ugen, vil den komme sndag umiddelbart efter kl. 20. Vlger du at f en mail om mneden, vil den komme
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
	} #Slut p inddata[68] =~/alle=vis/...

	echo "<form action=\"".$siteContext->getOption('urlUserAreaMain')."\" method=post><input type=hidden name=type value=\"gem_mailstats\">";
	?>
	<div class=forside>
	<h3>Mail stat</h3>

	<h4>Send flgende statistikker</h4>
	<a href="JAVAscript: alert('Hvis du krydser af her, vil alt p din statistikside nulstillet.\nHvis du vil nulstille noget, atbefaler jeg kraftigt, at du kun\nbenytter denne mulighed for at nulstille, da\nprognoserne p statistiksiden ellers ikke vil vre korrekte.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til nulstil alt..."></a>

	<label><input type=checkbox name="alle"<?php echo $check['alle']; ?>> Alle statistikker (anbefales!)</label><BR>
	<a href="JAVAscript: alert('Hvis du har valgt ikke at stte kryds i ovenstende boks,\\nskal du stte kryds i en eller flere af de nedenstende.\\nDu vil s p de valgte tidspunkter, f sendt de\\nvalgte statistikker til din e-mail adresse.');"><img src="<?php echo $siteContext->getPath('zipstat_icons'); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til resten..."></a>
	Hvis du ikke har valgt at stte kryds i ovenstende boks, skal du stte kryds i en eller flere af nedenstende.

	<table border="1">
	<tr><td><label><input type=checkbox name="enkeltstat"<?php echo $check['enkeltstat']; ?>>Enkeltstende stastikker.</label>
		<td><label><input type=checkbox name="prognoser"<?php echo $check['prognoser']; ?>>Prognoser.</label>
	<tr><td><label><input type=checkbox name="maaned_i_aar"<?php echo $check['maaned_i_aar']; ?>>Hits for de seneste 12 mneder.</label>
		<td><label><input type=checkbox name="sidste_31_dage"<?php echo $check['sidste_31_dage']; ?>>Hits for de seneste 31 dage.</label>
	<tr><td><label><input type=checkbox name="timer_hits"<?php echo $check['timer_hits']; ?>>Hits pr. time.</label>
		<td><label><input type=checkbox name="ugedag_hits"<?php echo $check['ugedag_hits']; ?>>Hits pr. ugedag.</label>
	<tr><td><label><input type=checkbox name="top_domain"<?php echo $check['top_domain']; ?>>Hits pr. topdomne (.dk, .com osv.)</label>
		<td><label><input type=checkbox name="domaene_hits"<?php echo $check['domaene_hits']; ?>>Hits pr. domne.</label>
	<tr><td><label><input type=checkbox name="info20"<?php echo $check['info20']; ?>>Info om seneste besgende.</label>
		<td><label><input type=checkbox name="hits_browser"<?php echo $check['hits_browser']; ?>>Hits pr. browser.</label>
	<tr><td><label><input type=checkbox name="hits_os"<?php echo $check['hits_os']; ?>>Hits pr. styresystem.</label>
		<td><label><input type=checkbox name="hits_sprog"<?php echo $check['hits_sprog']; ?>>Hits pr. sprog.</label>
	<tr><td><label><input type=checkbox name="hits_opl"<?php echo $check['hits_opl']; ?>>Hits pr. skrmoplsning.</label>
		<td><label><input type=checkbox name="hits_farver"<?php echo $check['hits_farver']; ?>>Hits pr. antal understttede farver (i bits).</label>
	<tr><td><label><input type=checkbox name="java_support"<?php echo $check['java_support']; ?>>JAVA support.</label>
		<td><label><input type=checkbox name="js"<?php echo $check['js']; ?>>JAVA-script support.</label>
	<tr><td><label><input type=checkbox name="taellere"<?php echo $check['taellere']; ?>>Tllere.</label>
		<td><label><input type=checkbox name="spoergs"<?php echo $check['spoergs']; ?>>Sprgsml og svar.</label>
	<tr><td><label><input type=checkbox name="ref"<?php echo $check['ref']; ?>>Referencesider.</label>
		<td><label><input type=checkbox name="sord"<?php echo $check['sord']; ?>>Sgeord.</label>
	<tr><td><label><input type=checkbox name="smask"<?php echo $check['smask']; ?>>Sgemaskiner.</label>
		<td><label><input type=checkbox name="zipklik"<?php echo $check['zipklik']; ?>>Kliktllere</label>
	<tr><td><label><input type=checkbox name="bev"<?php echo $check['bev']; ?>>Bevgelser.</label>
		<td>
	</table>

	<h4>Send statistik med e-mail p flgende tidspunkter</h4>
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
		if (isset($viser[$i]) and strpos($viser[$i], 'son;;') === 0)	{ $selected = ' SELECTED'; } echo "<option value=\"son\"$selected>Hver sndag\n"; $selected = '';
		for ($n = 1; $n <= 31; $n++) {
			if (isset($viser[$i]) and strpos($viser[$i], "$n;;") === 0) { $selected = ' SELECTED'; }
			echo "<option value=\"$n\"$selected>D. $n. i hver mned\n";
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
	<p>L√∏bet √r for tidspunkter? N√•r du trykker √ gem kommer der flere.</p>
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

	$utils->echoSiteHead("Rediger kliktllere", 0);
	
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
	echo "<tr><td>Nr.</td><td>Navn</td><td>Hits</td><td><small>Nulstil</small></td><td>Link</td></tr>";

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
		print "<div class=forside>Nr du gemmer dine kliktllere, og gr ind p denne side igen, vil der vre plads til 5 kliktllere mere.</div>";
	else
		print "<div class=forside>Nr du gemmer dine kliktllere, og gr ind p denne side igen, vil der, s lnge du har ledige kliktllere, vre plads til 5 kliktllere mere.</forside>";

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
		if (get_magic_quotes_gpc())
			$pro_body_val = stripslashes($ind['pro_body']);
		else
			$pro_body_val = $ind['pro_body'];
		$datafile->setLine(56, $pro_body_val);
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

	}#Slut p if pro

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
		#Forger standardtlleren med denne vrdi.
		$datafile->setLine(82, (isset($ind['standardop']) ? $ind['standardop'] : ''));

		#Sletter evt. den registrerede ip-adresse
		if (isset($ind['sletipadr']))
			$datafile->setLine(52, '');

		#Stter send-videre adressen
		if (!isset($ind['taelopredirect']) or strlen($ind['taelopredirect']) === 0 or $utils->validateUrl($ind['taelopredirect'])) {
			$datafile->setLine(53, isset($ind['taelopredirect']) ? $ind['taelopredirect'] : '');
		} else if ($utils->validateUrl('http://'.$ind['taelopredirect'])) {
			$datafile->setLine(53, 'http://'.$ind['taelopredirect']);
			$fej .= "Der blev automatisk indsat <code>http://</code> i starten af din adresse, fordi en korrekt internetadresse starter med dette.";
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
	} #Slut p if not simpel

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
				$add = "<li>Du har nu ZIP Stat Pro, gratis indtil ".localtime(1034310769).". God fornjelse!<br>Vlg menupunktet &quot;Indstillinger&quot; i menuen til venstre, for at foretage de nye indstillinger du har mulighed for med ZIP Stat Pro. <b>OBS</b> Dette fremgr som en fejl, men det er det <em>ikke</em>.";
			}
		}

		if (! isset($add)) {
			$add = "<li>Det pro-kodeord du angav er forkert. Tryk p din browsers &quot;Tilbage&quot; knap, og se efter om du har tastet <em>helt</em> rigtigt. Husk at der er forskel p store og sm bogstaver! Virker kodeordret stadig ikke efter det, s skriv til <a href=\"mailto:".$siteContext->getOption('errorEMail')."\">".$siteContext->getOption('errorEMail')."</a>. Skriv pro-kodeordret, samt hvor du har fet det fra. Hvis et magarsin har lavet en trykfejl opretter jeg et pro-kodeord der svarer til trykfejlen.";
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
	$pro_max_tegn_sord =  $lib->pro(12); #<12-Max antal tegn til sgeord>
	$pro_max_tegn_besk =  $lib->pro(13); #<13-Max antal tegn i beskrivelse>

#-------------
/*
if ($options{'use_index'} == 1) {
	require $stier{'index_rediger'}; #Lib. med subrutiner til at redigere i indexfilerne.
	#Indsamler oplysninger om selectbox'ene
	$inddata[86] =~ s/\n//g;
	$inddata[87] =~ s/\n//g;
	@kategorier = split(/::/,$inddata[86]);
	@placeringer = split(/::/,$inddata[87]);

	#Tager hjde for at samme kategori kan vre valgt i flere felter.
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
	
	#Tjekker om man har skrevet for mange sgeord.
		#Den brokker sig dog kun hvis der er mere end 5 tegn for meget
	if (length($ind{'sord'}) > $pro_max_tegn_sord + 5)
		{ $opdateret .= "	<p>Dine sgeord fylder ".length($ind{'sord'})."tegn, men de m kun fylde $pro_max_tegn_sord tegn, s derfor er de ikke blevet opdateret.</p>\n"; }
	else
		{
		$ind{'sord'} =~ s/\n\r//g;
		$inddata[85] = $ind{'sord'}."\n";
		}

	#Tjekker om beskrivelsen fylder for mange tegn.
		#Den brokker sig dog kun hvis der er mere end 5 tegn for meget
	if (length($ind{'beskrivelse'}) > $pro_max_tegn_besk + 5)
		{
		$opdateret .= "	<p>Din beskrivelse fylder ".length($ind{'beskrivelse'})." tegn, men den m kun fylde $pro_max_tegn_besk tegn, s derfor er den ikke blevet opdateret.</p>\n";
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
		echo "<div class=forside>\n<h1>Intet kodeord</h1>\n<p>Du indtastede <em>ikke</em> dit kodeord. Det skal du gre for at du ikke kommer til at slette din konto ved et uheld. Tryk p din browsers &quot;tilbage&quot;-knap og indtast det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if (!isset($ind['brugernavn_slet']) or strlen($ind['brugernavn_slet']) === 0) {
		$utils->echoSiteHead("Intet brugernavn");
		echo "<div class=forside>\n<h1>Intet brugernavn</h1>\n<p>Du indtastede <em>ikke</em> dit brugernavn. Det skal du gre for at du ikke kommer til at slette din konto ved et uheld. Tryk p din browsers &quot;tilbage&quot;-knap og indtast det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

        $authFactory = new AuthenticationFactory($siteContext->getOptions());
        $auth = $authFactory->create();
        if (! $auth->doAuthenticate($username, $ind['kodeord_slet'])) {
		$utils->echoSiteHead("Forkert kodeord");
		echo "<div class=forside>\n<h1>Forkert kodeord</h1>\n<p>Det kodeord du indtastede er forkert. Tryk p din browsers &quot;tilbage&quot;-knap og ret det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if ($ind['username'] !== $ind['brugernavn_slet']) {
		$utils->echoSiteHead("Forkert brugernavn");
		echo "<div class=forside>\n<h1>Forkert brugernavn</h1>\n<p>Det brugernavn du indtastede er forkert. Tryk p din browsers &quot;tilbage&quot;-knap og ret det.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if (!isset($ind['sletvirkelig']) or strlen($ind['sletvirkelig']) === 0) {
		$utils->echoSiteHead("Du satte ikke hak");
		echo "<div class=forside>\n<h1>Du satte ikke hak</h1>\n<p>Du skal stte hak i kassen <i>Jeg nsker at slette min ZIP Stat konto, og ved at nr jeg har trykket p knappen &quot;Slet denne ZIP Stat konto - alle mine statistikker bliver slettet!&quot; er mine statistikker slettet for altid.</i>. Dette er for at sikre, at du ikke kommer til at slette din ZIP Stat konto ved et uheld. Tryk p din browsers &quot;tilbage&quot;-knap og st hak.</p></div>";
		$utils->echoSiteEnd();
		exit;
	}

	if ($datafile->deleteUser()) {
		$utils->echoSiteHead("Din konto er slettet");
		echo "<div class=forside>\n<p>Din ZIP Stat konto er nu slettet. Tak fordi du valgte at bruge ZIP Stat indtil nu. Jeg h√•ber du √lger at komme tilbage.<br>Mvh. ".$siteContext->getOption('adminName').", ".$siteContext->getOption('name_of_service')."</p></div>";
		$utils->echoSiteEnd();
	}
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

	$utils->saveData($datafile, "<p>De nskede statistikker er nu nulstillet.</p>\n", '', 'kunHvisProblemer');
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
		echo "Du skrev intet kodeord i nogle af boksene - du er <em>n&oslash;d</em> til at have et kodeord. Du skal alts fortsat bruge dit gamle kodeord! Tryk p&aring; din browsers &quot;Tilbage&quot;-knap, for at &aelig;ndre dit kodeord, eller for at benytte brugeromrdet.";
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

	#Udregner hvor mange svar og sprgsml der er brugt
	#Hvis der er et svar i et sprgsml, men ikke noget sprgsml, skal det alligevel medtages.
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

	if ($lib->pro()) { #Stter antallet, hvis man har pro
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
		$nulst = "<p>Sprgsmlene $nulst blev nulstillet.</p>\n";
	} else {
		$nulst = '';
	}
	if (!isset($problemer))
		$problemer = '';

	$utils->saveData($datafile, "<p>Dine sprgsml er gemt</p>$nulst\n", $problemer, 'kunHvisProblemer');
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
		$nulst = "<p>Tllerene $nulst blev nulstillet.</p>\n";
	}

	$datafile->setLine(37, implode('::',$hits));
	$datafile->setLine(38, implode('::',$navne));

	if (!isset($problemer))
		$problemer = '';
	$utils->saveData($datafile, "<p>Dine tllere er gemt</p>$nulst\n", $problemer, 'kunHvisProblemer');
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
	#	<option value=hver_maaned>en gang om mneden

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
	} #Slut p if simpel

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

	if ($lib->pro()) #Stter antallet, hvis man har pro
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
			}	#Slut p if $ind["klokken$i"] ne intet elsif $ind{"klokken$i"}> 0 and $ind{"klokken$i"}<= 23
		}	else {	#Slut p if ind klokken$i
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

	#Udregner hvor mange kliktllere der er brugt
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

	if ($lib->pro()) #Stter antallet, hvis man har pro
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
			$problemer .= "<li>Det angivne navn for nr. $i ($str) indeholder tegn det ikke m indeholde. Det m kun indeholde tegnene a-z (store og sm bogstaver), tallene 0-9 samt en - og en _. Navnet blev ikke gemt.";
		}

		if (isset($ind["url$i"]) and
		 ($utils->validateUrl($ind["url$i"]) or strlen($ind["url$i"]) === 0)) {
			$urler[$i] = isset($ind["url$i"]) ? $ind["url$i"] : '';
		} else {
			if (isset($ind["navne$i"])) {
				$adr = $ind["url$i"];
				$problemer .= "<li>Kliktller nr. $i ($adr) er ikke en gyldig internetadresse, og blev derfor ikke gemt.\n";
			}
		} #Slut p if okurl or not ind url else
		if (isset($ind["nulstil$i"])) {
			$hits[$i] = 0;
		}

	} //End for

	$datafile->setLine(69, implode('::', $hits));
	$datafile->setLine(70, implode('::', $navne));
	$datafile->setLine(71, implode('::', $urler));

	if (!isset($problemer))
		$problemer = '';

	$utils->saveData($datafile, "<p>Dine kliktllere er gemt</p>\n", $problemer, 'kunHvisProblemer');
	r_zipklik($utils, $siteContext);

}

?>
