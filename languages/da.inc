<?php

/**
 * Returns an array with all the localizaion mappings.
 * 
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @return String[] an array with all the localizaion mappings.
 */
function getLocals()
{
	//The stat. site.
	$locale['statSiteFor'] = 'Statistik for %1$s'; //%1$s is the title of the site.
	$locale['secondsShort'] = 'sek.';
	$locale['minuteShort'] = 'min.';

	//Low bandwidth mode
	$locale['lowBandwidthStatsite'] = 'Der er begr�nset b�ndbredde til r�dighed, s� for at tingene ikke kommer til at g� alt for langsomt kan der derfor kun ses %1$s statistikker p� �n gang, p� statistiksiden. Brug statistikv�lgeren som er nederst p� statistiksiden til at v�lge de statistikker du vil se (v�lgeren findes kun i html-udgaven af statistiksiden).';
	$locale['statsiteWhyGettingMail'] = 'Framelding.'."\n".'Du har modtaget denne e-mail fordi du eller en anden har tilmeldt dig ZIP Stat og bedt om at f� tilsendt disse mails.'."\n".'Du kan slette den p�g�ldende konto ved at logge ind p� brugeromr�det p� www.zipstat.dk (med brugernavn %1$s og kodeord %2$s) og f�lge instruktionerne nederst p� siden "Oplysninger". P� siden "Mail stats" kan du evt. n�jes med at sl� disse e-mails fra.';
	
	
	//The days of the week
	$locale['sunday'] = 's�ndag';
	$locale['monday'] = 'mandag';
	$locale['tuesday'] = 'tirsdag';
	$locale['wednesday'] = 'onsdag';
	$locale['thursday'] = 'torsdag';
	$locale['friday'] = 'fredag';
	$locale['saturday'] = 'l�rdag';
	
	//The days of the week - short
	$locale['shortSunday'] = 's�n';
	$locale['shortMonday'] = 'man';
	$locale['shortTuesday'] = 'tirs';
	$locale['shortWednesday'] = 'ons';
	$locale['shortThursday'] = 'tors';
	$locale['shortFriday'] = 'fre';
	$locale['shortSaturday'] = 'l�r';
	
	/*Set to 0 if Sunday is the first day og the week, 1 if Monday is first*/
	$locale['weekStarts'] = 1;
	
	$locale['weekDays'][] = $locale['sunday'];
	$locale['weekDays'][] = $locale['monday'];
	$locale['weekDays'][] = $locale['tuesday'];
	$locale['weekDays'][] = $locale['wednesday'];
	$locale['weekDays'][] = $locale['thursday'];
	$locale['weekDays'][] = $locale['friday'];
	$locale['weekDays'][] = $locale['saturday'];

	$locale['shortWeekDays'][] = $locale['shortSunday'];
	$locale['shortWeekDays'][] = $locale['shortMonday'];
	$locale['shortWeekDays'][] = $locale['shortTuesday'];
	$locale['shortWeekDays'][] = $locale['shortWednesday'];
	$locale['shortWeekDays'][] = $locale['shortThursday'];
	$locale['shortWeekDays'][] = $locale['shortFriday'];
	$locale['shortWeekDays'][] = $locale['shortSaturday'];

	//The months
	$locale['January'] = 'januar';
	$locale['February'] = 'februar';
	$locale['March'] = 'marts';
	$locale['April'] = 'april';
	$locale['maj'] = 'maj';
	$locale['June'] = 'juni';
	$locale['July'] = 'juli';
	$locale['August'] = 'august';
	$locale['September'] = 'september';
	$locale['October'] = 'oktober';
	$locale['November'] = 'november';
	$locale['December'] = 'december';

	//The months in another version
	$locale['months'][] = $locale['January'];
	$locale['months'][] = $locale['February']; 
	$locale['months'][] = $locale['March'];
	$locale['months'][] = $locale['April'];
	$locale['months'][] = $locale['maj'];
	$locale['months'][] = $locale['June'];
	$locale['months'][] = $locale['July'];
	$locale['months'][] = $locale['August'];
	$locale['months'][] = $locale['September'];
	$locale['months'][] = $locale['October'];
	$locale['months'][] = $locale['November'];
	$locale['months'][] = $locale['December'];
	
	//The languages
	$locale['languages'][''] = "";

	$locale['languages']['da'] = "dansk";
	$locale['languages']['en'] = "engelsk";
	$locale['languages']['de'] = "tysk";
	$locale['languages']['es'] = "spansk";
	$locale['languages']['no'] = "norsk";
	$locale['languages']['sv'] = "svensk";
	$locale['languages']['us'] = "amerikansk";
	$locale['languages']['aa'] = "Afar";
	$locale['languages']['ab'] = "Abkhazian";
	$locale['languages']['am'] = "Amharic";
	$locale['languages']['ar'] = "arabisk";
	$locale['languages']['as'] = "Assamese";
	$locale['languages']['ay'] = "Aymara";
	$locale['languages']['az'] = "azerbaijansk";
	$locale['languages']['ba'] = "Bashkir";
	$locale['languages']['be'] = "hviderussisk";
	$locale['languages']['bg'] = "bulgarsk";
	$locale['languages']['bh'] = "Bihari";
	$locale['languages']['bi'] = "Bislama";
	$locale['languages']['bn'] = "bengalsk";
	$locale['languages']['bo'] = "tibetansk";
	$locale['languages']['br'] = "bretonsk";
	$locale['languages']['ca'] = "katalansk";
	$locale['languages']['co'] = "korsikansk";
	$locale['languages']['cs'] = "tjekkisk";
	$locale['languages']['cy'] = "walisisk";
	$locale['languages']['dz'] = "Bhutani";
	$locale['languages']['el'] = "gr�sk";
	$locale['languages']['eo'] = "esperanto";
	$locale['languages']['et'] = "estisk";
	$locale['languages']['eu'] = "baskisk";
	$locale['languages']['fa'] = "persisk";
	$locale['languages']['fi'] = "finsk";
	$locale['languages']['fj'] = "fiji";
	$locale['languages']['fo'] = "f�r�sk";
	$locale['languages']['fr'] = "fransk";
	$locale['languages']['fy'] = "frisisk";
	$locale['languages']['ga'] = "irsk";
	$locale['languages']['gd'] = "skotsk";
	$locale['languages']['gl'] = "galicisk";
	$locale['languages']['gn'] = "Guarani";
	$locale['languages']['gu'] = "Gujarati";
	$locale['languages']['ha'] = "Hausa";
	$locale['languages']['hi'] = "hindi";
	$locale['languages']['hr'] = "kroatisk";
	$locale['languages']['hu'] = "ungarsk";
	$locale['languages']['hy'] = "armenisk";
	$locale['languages']['ia'] = "Interlingua";
	$locale['languages']['ie'] = "Interlingue";
	$locale['languages']['ik'] = "Inupiak";
	$locale['languages']['in'] = "indonesisk";
	$locale['languages']['is'] = "islandsk";
	$locale['languages']['it'] = "italiensk";
	$locale['languages']['iw'] = "hebraisk";
	$locale['languages']['ja'] = "japansk";
	$locale['languages']['ji'] = "jiddisch";
	$locale['languages']['jw'] = "javanesisk";
	$locale['languages']['ka'] = "georgisk";
	$locale['languages']['kk'] = "Kazakh";
	$locale['languages']['kl'] = "gr�nlandsk";
	$locale['languages']['km'] = "cambodjansk";
	$locale['languages']['kn'] = "Kannada";
	$locale['languages']['ko'] = "koreansk";
	$locale['languages']['ks'] = "Kashmiri";
	$locale['languages']['ku'] = "kurdisk";
	$locale['languages']['ky'] = "kirgisisk";
	$locale['languages']['la'] = "latin";
	$locale['languages']['ln'] = "Lingala";
	$locale['languages']['lo'] = "Laothian";
	$locale['languages']['lt'] = "litauisk";
	$locale['languages']['lv'] = "lettisk";
	$locale['languages']['mg'] = "madagassisk";
	$locale['languages']['mi'] = "Maori";
	$locale['languages']['mk'] = "makedonisk";
	$locale['languages']['ml'] = "Malayalam";
	$locale['languages']['mn'] = "mongolsk";
	$locale['languages']['mo'] = "moldavisk";
	$locale['languages']['mr'] = "Marathi";
	$locale['languages']['ms'] = "malajisk";
	$locale['languages']['mt'] = "maltesisk";
	$locale['languages']['my'] = "burmesisk";
	$locale['languages']['na'] = "Nauru";
	$locale['languages']['ne'] = "nepalesisk";
	$locale['languages']['nl'] = "hollandsk";
	$locale['languages']['oc'] = "Occitan";
	$locale['languages']['om'] = "Afan";
	$locale['languages']['or'] = "Oriya";
	$locale['languages']['pa'] = "Punjabi";
	$locale['languages']['pl'] = "polsk";
	$locale['languages']['ps'] = "Pashto/Pushto";
	$locale['languages']['pt'] = "portugisisk";
	$locale['languages']['qu'] = "Quechua";
	$locale['languages']['rm'] = "Rhaeto-Romance";
	$locale['languages']['rn'] = "Kirundi";
	$locale['languages']['ro'] = "rum�nsk";
	$locale['languages']['ru'] = "russisk";
	$locale['languages']['rw'] = "Kinyarwanda";
	$locale['languages']['sa'] = "sanskrit";
	$locale['languages']['sd'] = "Sindhi";
	$locale['languages']['sg'] = "Sangro";
	$locale['languages']['sh'] = "serbo-kroatisk";
	$locale['languages']['si'] = "singalesisk";
	$locale['languages']['sk'] = "slovakkisk";
	$locale['languages']['sl'] = "slovensk";
	$locale['languages']['sm'] = "Samoan";
	$locale['languages']['sn'] = "Shona";
	$locale['languages']['so'] = "somali";
	$locale['languages']['sq'] = "albansk";
	$locale['languages']['sr'] = "serbisk";
	$locale['languages']['ss'] = "Siswati";
	$locale['languages']['st'] = "Sesotho";
	$locale['languages']['su'] = "Sundanese";
	$locale['languages']['sw'] = "swahili";
	$locale['languages']['ta'] = "Tamil";
	$locale['languages']['te'] = "Tegulu";
	$locale['languages']['tg'] = "Tajik";
	$locale['languages']['th'] = "thai";
	$locale['languages']['ti'] = "Tigrinya";
	$locale['languages']['tk'] = "Turkmen";
	$locale['languages']['tl'] = "Tagalog";
	$locale['languages']['tn'] = "Setswana";
	$locale['languages']['to'] = "Tonga";
	$locale['languages']['tr'] = "tyrkisk";
	$locale['languages']['ts'] = "Tsonga";
	$locale['languages']['tt'] = "Tatar";
	$locale['languages']['tw'] = "Twi";
	$locale['languages']['uk'] = "ukrainsk";
	$locale['languages']['ur'] = "urdu";
	$locale['languages']['uz'] = "Uzbek";
	$locale['languages']['vi'] = "vietnamesisk";
	$locale['languages']['vo'] = "volapyk";
	$locale['languages']['wo'] = "Wolof";
	$locale['languages']['xh'] = "Xhosa";
	$locale['languages']['yo'] = "Yoruba";
	$locale['languages']['zh'] = "kinesisk";
	$locale['languages']['zu'] = "zulusprog";
	$locale['languages']['gb'] = "britisk engelsk";
	$locale['otherLangs'] = "Andet";

	$locale['yes'] = "Ja";
	$locale['no'] = "Nej";
	$locale['dontKnow'] = "Ved ikke";

	//Date formated, formated acording to the PHP date function.
	$locale['dateLong'] = '\\d. d-m Y \\k\\l. H:i';
	$locale['dateShort'] = 'd-m Y H:i';
	$locale['dateReallyShort'] = 'd-m H:i';
	$locale['dateTime'] = '\\k\\l. H:i';
	$locale['dateDate'] = '\\d. d-m Y';
	
	//Basic stats/Enkeltst�ende statistikker
	$locale['basicStats'] = 'Enkeltst�ende statistikker';
	$locale['basicStatsType'] = 'Type';
	$locale['basicStatsData'] = 'Data';
	$locale['basicStatsDate'] = 'Dato';
	$locale['basicSHitsSStart'] = 'Hits siden start';
	$locale['basicSHitsS'] = 'Hits siden';
	$locale['basicSUniq'] = 'Heraf unikke';
	$locale['basicSHitsPrUser'] = 'Hits pr. bruger';
	$locale['basicSMaxHitsDay'] = 'Max hits pr. dag';
	$locale['basicSMaxHitsMonth'] = 'Max hits p� en m�ned';
	$locale['basicSMaxUniqHDay'] = 'Max unikke hits p� en dag';
	$locale['basicSMaxUniqHMonth'] = 'Max unikke hits p� en m�ned';
	$locale['basicSUniqHToday'] = 'Unikke hits idag';
	$locale['baticSUIniqHThisMonth'] = 'Unikke hits denne m�ned';
	$locale['basicSUniqPerDay'] = 'Unikke hits pr. dag';
	$locale['basicSUniqPerHour'] = 'Unikke hits pr. time';
	$locale['basicSHitsDay'] = 'Hits pr. dag';
	$locale['basicSHitsHour'] = 'Hits pr. time';
	$locale['basicSVisitorsNow'] = 'Personer p� siden nu (beregnet)';
	$locale['basicSeSiteSeen'] = 'Hver side ses';
	
	//Projections
	$locale['proj'] = 'Prognoser';
	$locale['projTheText'] = 'Eftersom %1$s af sidens hits plejer at komme f�r %2$s, vil der komme ca. %3$s hits mere, hvilket bringer siden op p� %4$s hits i dag. Yderligere, vil der komme ca. %5$s hits mere i denne m�ned, hvilket bringer siden op p� i alt %6$s i denne m�ned.';
	
	//Stats with graphs
	$locale['hits'] = 'hits';
	$locale['percent'] = 'procent';
	$locale['graph'] = 'graf';
	
	$locale['sgMonthStat'] = 'Pr. m�ned i et �r';
	$locale['sgMonthDesc'] = 'Denne m�ned er markeret';
	$locale['month'] = 'm�ned';
	
	$locale['sgDayStat'] = 'Pr. dag i en m�ned';
	$locale['sgDayDesc'] = 'Dagen i dag er markeret';
	$locale['day'] = 'dag';
	
	$locale['sgHourStat'] = 'Pr. time';
	$locale['sgHourDesc'] = 'Denne time er markeret';
	$locale['hour'] = 'time';

	$locale['sgWeekStat'] = 'Pr. ugedag';
	$locale['sgWeekDesc'] = 'Denne uge er markeret';
	$locale['week'] = 'uge';
	
	$locale['sgTopdomainStat'] = "Topdom�ner";
	$locale['sgTopdomainDesc'] = "";
	$locale['topdomain'] = "topdom.";

	$locale['sgDomainStat'] = "Dom�ner";
	$locale['sgDomainDesc'] = "";
	$locale['domain'] = "dom�ne";

	$locale['sgBrowserStat'] = "Browsere";
	$locale['sgBrowserDesc'] = "";
	$locale['browser'] = "browser";
	
	$locale['sgBrowserMakerStat'] = "Browsere summeret";
	$locale['sgBrowserMakerDesc'] = "Hits for hver browser, uanset version.";
	
	$locale['sgOsStat'] = "Styresystemer";
	$locale['sgOsDesc'] = "";
	$locale['os'] = "styresystem";
	
	$locale['sgOsMakerStat'] = "Styresystemer summeret";
	$locale['sgOsMakerDesc'] = "Hits for hvert styresystem, uanset version.";
	
	$locale['sgLangStat'] = "Sprog";
	$locale['sgLangDesc'] = "";
	$locale['lang'] = "sprog";
	
	$locale['sgResStat'] = "Opl�sninger";
	$locale['sgResDesc'] = "";
	$locale['resolution'] = "opl�sning";

	$locale['sgColoursStat'] = "Antal farver (i bit)";
	$locale['sgColoursDesc'] = "Tip: 4 bit = 16 farver - 8 bit = 256 farver - 16 bit = 65.536 farver - 24 bit = 16.777.216 farver - 32 bit = 4.294.967.296 farver";
	$locale['colours'] = "farver";

	$locale['sgJavaStat'] = "JAVA-support";
	$locale['sgJavaDesc'] = "Ja betyder at den bes�gende har sl�et JAVA (ikke JAVA-script) til. Nej betyder at browseren ikke underst�tter det, eller det er sl�et fra. Ved ikke, betyder at browseren ikke ville opgive det, og s� underst�tter den det nok ikke.";
	$locale['enabledJava'] = "Sl�et til";

	$locale['sgCountersStat'] = "T�llere";
	$locale['sgCountersDesc'] = "T�lleren %1\$s t�lles op n�r der ikke er nogle af de �vrige t�llere der kan t�lles op, fordi der ikke er flere ledige t�llere.";
	$locale['counters'] = "T�ller";
	$locale['sgStdCounter'] = "Div.";

	$locale['sgJavaScriptStat'] = "JAVA-script support";
	$locale['sgJavaScriptDesc'] = "Sorteret.\nJa betyder at den bes�gende har sl�et JAVA-script til. Nej betyder at browseren ikke underst�tter det, eller det er sl�et fra. Ved ikke, betyder at browseren ikke ville opgive det, og s� underst�tter den det nok ikke.";
	$locale['enabledJavaScript'] = "Sl�et til";

	$locale['sgRefererStat'] = "Seneste referencesider";
	$locale['sgRefererDesc'] = "Der registreres hvor mange bes�gende du har f�et fra de enkelte refererencesider. Der registreres dog kun et begr�nset antal forskellige referencesider, s� n�r der ikke er mere plads til flere referencesider, bliver den side, der ikke har v�ret referenceside i l�ngst tid, slettet fra listen. Links �bner i et nyt vindue.";

	$locale['sgEntryStat'] = "Indgangssider";
	$locale['sgEntryDesc'] = "De sider folk f�rst kommer ind p�.";
	$locale['sgEntryUrl'] = "Side";
	
	$locale['sgExitStat'] = "Udgangssider";
	$locale['sgExitDesc'] = "De sider folk forlader siden fra.";
	$locale['sgExitUrl'] = "Side";

	$locale['sgMoveStat'] = "Bev�gelser";
	$locale['sgMoveDesc'] = "Der vises hvilke sider folk bev�ger sig imellem. Siden til venstre for -> er den folk kom fra, og den til h�jre er den de kom til. St�r der ikke noget til venstre, er personen lige kommet ind p� siden.";
	$locale['sgMovePages'] = "Sider";

	$locale['sgClickCountStat'] = "Klikt�llere";
	$locale['sgClickCountDesc'] = "";
	$locale['sgClicks'] = "Nr/navn";
	$locale['sgNoName'] = "Navnel�s";

	$locale['sgSearchWordStat'] = "S�geord";
	$locale['sgSearchWordDesc'] = "Disse ord er ord folk har s�gt p� n�r de er kommet ind p� din hjemmeside via en af de s�gemaskiner ZIP Stat underst�tter.";
	$locale['sgSearchWord'] = "S�geord";

	$locale['sgSearchEngineStat'] = "S�gemaskiner";
	$locale['sgSearchEngineDesc'] = "Der vises hvilke s�gemaskiner (der er underst�ttet af ZIP stat) folk, har fundet siden med.";
	$locale['sgSearchEngine'] = "S�gemaskine";

	$locale['sgVoteStat'] = "Afstemninger";
	$locale['sgQuestion'] = "Sp�rgsm�l";
	$locale['sgNoVotes'] = "Der er ikke oprettet nogle afstemninger.";

	$locale['sgLatestStat'] = "Seneste bes�gende";
	$locale['sgLatestDesc'] = "Links �bner i et nyt vindue.";
	$locale['sgLatestTime'] = "Tid";
	$locale['sgLatestBrowser'] = "Brow.";
	$locale['sgLatestOs'] = "OS";
	$locale['sgLatestIp'] = "IP-adr";
	$locale['sgLatestDomain'] = "Dom�ne";
	$locale['sgLatestRes'] = "Opl.";
	$locale['sgLatestColours'] = "Frv.";
	$locale['sgLatestLang'] = "Spr.";
	$locale['sgLatestPage'] = "Side";
	$locale['sgLatestRefpage'] = "Referenceside";
	$locale['sgLatestNA'] = "Ej givet";
	$locale['sgLatestBit'] = "bit";
	$locale['sgLatestOthers'] = "Andre";
	
	$locale['sgSeriesBrowser'] = "Browsere";

	//The stat selector box on the stat page
	$locale['sgNext'] = "N�ste";
	$locale['sgPrevious'] = "Forrige";
	$locale['sgAll'] = "Alle";
	$locale['sgRest'] = "Resten";
	$locale['sgShowSel'] = "Vis valgte";
	$locale['sgResetSel'] = "Nulstil valg";
	$locale['sgType_text'] = "Ren tekst";
	$locale['sgType_csv'] = "Regneark (semikolon-separeret)";
	$locale['sgType_html'] = "HTML med grafer";
	$locale['sgTypeText'] = "Vis siden som";

	//The HtmlSite class
	$locale['htmlsHelpFor'] = "Hj�lp til";
	
	
	//The registration page
	$locale['regHeadline'] = "Tilmeld";
	$locale['regXOfY'] = "Side %1\$s af %2\$s...";
	$locale['regWelcomePart1'] = "Velkommen til ZIP stat. Udfyld nedenst�ende skema, tryk p� &quot;Tilmeld&quot;, og s�t den kode du f�r ind p� din hjemmeside. S� er du oppe at k�re.<br>Tryk p� ";
	$locale['regWelcomePart2'] = " for ekstra hj�lp.";
	$locale['regHelpHelp'] = "hj�lp";
	$locale['regHelpHText'] = "N�r du trykker p� de bl� sp�rgsm�lstegn,\nf�r du lidt ekstra hj�lp.";
	$locale['regWithLegal'] = "Det med sm�t";
	$locale['regWithLegalTxt'] = "ZIP Stat er en gratis service, og kan derfor ikke p� nogen m�de g�res ansvarlig for tab eller skade der m�ttet v�re opst�et i forbindelse med brug eller afbenyttelse. Jeg g�r mit bedste for at lave en god stabil statistik, men skulle der glippe noget, vil jeg ikke resikere at skulle ud og t�mme min uddannelsesopsparing for at udbetale erstatinger.</P><p>Hvis der er noget der driller, kan du pr�ve at kigge p� <a href=\"hjaelp.shtml\">hj�lpesiden</a> og i de <a href=\"oss.shtml\">Ofte Stillede Sp�rgsm�l</a>, men hj�lper dette ikke, m� du gerne <a href=\"http://www.zip.dk/zipstat/kontakt.shtml\">skrive</a>, hvilket du ogs� er velkommen til, hvis du har ris, ros eller kommentarer.";
	$locale['regName'] = "Navn";
	$locale['regNameHelp'] = "Skriv dit navn i boksen.";
	$locale['regNameEx'] = "f.eks. Hans Jensen";
	$locale['regEmail'] = "E-mail";
	$locale['regEmailHelp'] = "Skriv din e-mail adresse i boksen.\nHvis den er lidt speciel kan det v�re der bliver meldt at den er forkert.\nDet er lavet af sikkerhedsgrunde, og der er ikke andet at g�re end enten\nat skrive en falsk adresse, eller f� en anden (evt. et alias).";
	$locale['regEmailEx'] = "f.eks. hans_jensen@worldonline.dk";
	$locale['regUrl'] = "Sidens adresse";
	$locale['regUrlHelp'] = "Skriv titlen p� din hjemmeside i boksen.";
	$locale['regUrlEx'] = "f.eks. http://www.worldonline.dk/~hans_jensen/";
	$locale['regTitle'] = "Sidens titel";
	$locale['regTitleHelp'] = "Skriv titlen p� din hjemmeside i boksen.";
	$locale['regTitleEx'] = "f.eks. Hans Jensens hjemmeside";
	$locale['regUsername'] = "�nsket brugernavn";
	$locale['regUsernameHelp'] = "Skriv det brugernavn du gerne vil have, i boksen til h�jre.\nDet kan kun indeholde bogstaverne a-z, tallene 0-9 samt - og _.\n\nVIGTIGT: Modsat alt andet p� ZIP Stat, kan du ikke �ndre\ndit brugernavn uden at tilmelde dig igen (og dermed slette alle\ndine statistikker), s� v�lg det med omtanke.";
	$locale['regUsernameEx'] = "f.eks. hans_jensen";
	$locale['regPassword'] = "�nsket kodeord";
	$locale['regPasswordHelp'] = "Du skal skrive det kodeord du �nsker i boksen til h�jre.\nDet skal du bruge n�r du f.eks. vil �ndredine oplysninger - s�\nandre ikke kan g�re det. Det m� kun indeholde tegnene a-z samt\ntallene 0-9, og husk at der er forskel p� store og sm� bogstaver!\n\nN�r du skriver det, vil du kun kunne se nogle stjerner - det er for\nandre ikke kan aflure det, men det du taster ind bliver tilmeldt.";
	$locale['regPasswordEx'] = "f.eks. uD67G6yuftf";
	$locale['regPassword2'] = "Skriv det igen";
	$locale['regPassword2Help'] = "Her skal du skrive pr�cis det samme kodeord som du skrev\ni boksen for oven. Da du ikke kan se, hvad du skrev, er dette\nm�den du er sikker p� ikke at have lavet en sl�fejl.";
	$locale['regPassword2Ex'] = "som ovenst�ende, dvs. f.eks. uD67G6yuftf";
	$locale['regUserAreaHelp'] = "Den side hvorfra du administrere din statistik indeholder mange funktioner,\ns� derfor er der lavet en simpel udgave, som kun har de vigtigste, s� det\nhele bliver lettere at finde rundt i.\nDu kan altid skifte brugerflade n�r du vil, uden at det p� nogen m�de\np�virker din statistik.";
	$locale['regUserArea'] = "brugerflade";
	$locale['regUserAreaWich'] = "Hvilken brugerflade vil du gerne bruge?";
	$locale['regUserAreaSimple'] = "Den simple";
	$locale['regUserAreaAdvanced'] = "Den avancerede";
	$locale['regForKidsHelp'] = "Hvis din side indeholder materiale, der normalt ikke b�r ses af b�rn og\nunge under 18 �r, skal du svare \'Ja\'. Dette bevirker bl.a. at folk f�r en\nadvarsel, f�r de g�r ind p� din side fra toplisten.\n\nEnvidre f�r dit link p� toplisten ogs� en speciel farve, s� folk ikke\nkommer til at g� ind p� siden ved et uheld. Dette medf�rer samtidig, at\nfolk der f.eks. er ude efter erotiske sider, lettere kan finde dem.";
	$locale['regForKidsHelpFor'] = "valg af indholdstype";
	$locale['regForKids'] = "Indeholder din side noget der ikke b�r ses af b�rn og unge under 18 �r?";
	$locale['regForKidsYes'] = "Ja";
	$locale['regForKidsNo'] = "Nej";
	$locale['regNext'] = "Registrer...";
	
	$locale['regUASimple'] = "Du valgte under registreringen at benytte det simple brugeromr�de. Dette\nhar f�rre funktioner end det avancerede, men er enklere og mere\noverskueligt at benytte. Du kan skifte mellem det simple og avancerede\nbrugeromr�det s� ofte du vil, i menuen \"Indstillinger\" p� brugeromr�det.";
	$locale['regUAAdvanced'] = "Du valgte under registreringen at benytte det avancerede brugeromr�de. Dette\nhar flere funktioner end det simple, men er sv�rer og mere\nuoverskueligt at benytte. Du kan skifte mellem det simple og avancerede\nbrugeromr�det s� ofte du vil, i menuen \"Indstillinger\" p� brugeromr�det.";
	//The registration confirmation e-mail
			$email = "";
		$email .= "Du er nu tilmeldt hos %1\$s\n";
		$email .= "Dit brugernavn er: %2\$s\n";
		$email .= "Dit kodeord    er: %3\$s\n";
		$email .= "\n";
		$email .= "Gem disse oplysninger et sted hvor du altid kan finde dem.\n";
		$email .= "\n";
		$email .= "Hvis du vil �ndre dine oplysninger, s� g� til %4\$s ,\n";
		$email .= "log ind p� brugeromr�det og v�lge punktet \"Oplysninger\" i menuen til venstre.\n";
		$email .= "Fra brugeromr�det vil du fx. ogs� kunne oprette sp�rgsm�l, t�llere mm., men\n";
		$email .= "vigtigst af alt, kan du f� vist den kode du skal s�tte p� din hjemmeside,\n";
		$email .= "for at f� ZIP Stat til at virke. Denne finder du under \"Obligatorisk kode\".\n";
		$email .= "\n";
		$email .= "P� brugeromr�det (log ind via ZIP Stats forside), kan du envidere s�tte\n";
		$email .= "mere avancerede funktioner igang, f.eks. en funktion s� du ikke selv\n";
		$email .= "bliver registreret af statistikken, men du kan ogs� p� selvvalgte tidspunkter\n";
		$email .= "f� tilsendt dine statistikker med e-mail, eller hente dem i en udgave s� de\n";
		$email .= "let kan importeres i ethvert regneark.\n";
		$email .= "\n";
		$email .= "P� siden \"Indstillinger\", p� brugeromr�det, kan du selv v�lge, hvilke\n";
		$email .= "e-mails du �nsker at modtage. Du kan f.eks. v�lge aldrig at f� e-mails\n";
		$email .= "fra ZIP Stat, men du kan ogs� f� e-mails, lige s� snart der sker den\n";
		$email .= "mindste �ndring.\n";
		$email .= "\n";
		$email .= "%7\$s";
		$email .= "\n";
		$email .= "God forn�jelse!\n";
		$email .= "-- \n";
		$email .= "Mvh. %5\$s, %6\$s\n";
		$email .= "\n";
		$email .= "Husk ogs� www.ZIP.dk - alt godt til hjemmesiden...\n";
		$email .= "  Og hvis du vil have en g�stebog, er det www.ZIP.dk/gaeste/\n";
		$email .= "    Det hele er p� dansk og gratis.\n";
	$locale['regConfirmationEmail'] = $email;
	$locale['regConfirmationEmailSubj'] = "Du er nu tilmeldt...";
	$locale['regYouAreRegistered'] = "Du er nu registreret";
	$locale['regCongRegistered'] = "Tillykke! Du er nu ZIP Stat bruger.";
	$locale['regNowOnlyMissing'] = "Du mangler nu kun...";
	$locale['regGenCode1'] = "Du skal nu s�tte noget <em>HTML-kode</em> ind p� din hjemmeside. Denne HTML-kode kaldes &quot;Obligatorisk kode&quot;, og du kan";
	$locale['regGenCode2'] = "generere den lige her";
	$locale['regGenCode3'] = "Du skal bare <em>kopier</em> koden der bliver genereret ind p� din hjemmeside, og s� virker det! <br>Hvis du bruger <em>FrontPage</em>, eller et lignende program, skal du huske at inds�tte koden som HTML, da den ellers bare vil blive vist p� siden.";
	$locale['regGenCodeAgain'] = "Hvis du f�r brug for at <em>generere</em> denne kode <em>igen</em>, skal du g� ind p� &quot;Brugeromr�det&quot;, via &quot;Log ind&quot; formularen p� <em>forsiden</em> af %1\$s. P� &quot;Brugeromr�det&quot; skal du v�lge linket &quot;Obligatorisk kode&quot;, som findes i menuen til venstre, og her kan du f� genereret den n�dvendige <em>kode</em>.</p> <i>Held og lykke!</i>";
	$locale['regAboutHelp'] = "Hj�lp";
	$locale['regAboutHelpText'] = "Under menupunktet &quot;%1\$s&quot; finder du	en <em>manual</em> der grundigt beskriver alle ZIP Stats funktioner. P� &quot;Brugeromr�det&quot; finder du endvidre nogle bl� <em>sp�rgsm�lstegn</em>. N�r du klikker p� dem, kommer der en <em>hj�lpetekst</em>	frem, til det punkt, sp�rgsm�lstegnet er ved siden af.";
	$locale['regErrorNoName'] = "Du har ikke skrevet dit navn.";
	$locale['regErrorBadEmail'] = "Du har ikke skrevet din e-mailadresse, eller ogs� mangler den et @ og/eller et punktum. Begge dele skal v�re tilstede i en korrekt e-mailadresse.";
	$locale['regErrorBadUrl'] = "Du har ikke skrevet sidens adresse, eller ogs� starter den ikke med <tt>http://</tt> eller ogs� indeholder den ikke et punktum. Begge dele er n�dvendig for en korrekt internetadresse.";
	$locale['regErrorNoTitle'] = "Du har ikke skrevet sidens titel.";
	$locale['regBadUsername'] = "Du har ikke skrevet et �nsket brugernavn, eller ogs� har du brugt andre tegn end bogstaverne a-z (store og sm�), tallene 0-9, en bindestreg ( - ) og en understregning ( _ ).";
	$locale['regTakenUsername'] = "Det brugernavn du har valgt er desv�rre optaget. V�lg et nyt.";
	$locale['regBadPassword'] = "Du har ikke skrevet dit kodeord to gange, eller ogs� har du skrevet det forkert den ene gang.";

	//$locale[''] = "";
	//$locale[''] = "";

	//Stat site passwords
	$locale['siteEnterPwdHead'] = "Indtast kodeord";
	$locale['siteEnterPwd'] = "Det kr�ver et kodeord for at se denne statistikside. Hvis du ikke har et, skal du kontakte sidens ejer, for at f� et tildelt.";
	$locale['siteEnterUsername'] = "Indtast brugernavnet til den statistik du �nsker at se. Kodeordet skal kun indtastes hvis der er kodeord p� statistiksiden.";
	$locale['username'] = "Brugernavn";
	$locale['password'] = "Kodeord";
	$locale['loginNotText'] = "Login er desv�rre ikke til stede i ren tekst. Log i stedet ind via en anden brugergr�nseflade (den almindelige i HTML).";
	$locale['login'] = "Log ind";
	$locale['forgottenPwd'] = "Glemt brugernavn eller kodeord...";

	//Users area
	$locale['uaEnterPwd'] = "Indtast dit brugernavn og din adgangskode.";
	$locale['uaEnterPwdWrong'] = "Brugernavnet og adgangskoden passer desv�rre ikke sammen. Det er nok bare en sl�fejl, s� pr�v igen. Hvis det ikke hj�lper, s� tjek om CAPS LOCK er sl�et til.";
	
	//General error messages
	$locale['errAnErrorOccured'] = 'Der opstod en fejl';
	$locale['errDamagedDatasource'] = 'Din datafil er desv�rre blevet beskadiet, og der kan derfor ikke registreres statistikker. Kontakt %1$s\'s administrator via e-mail-adressen nederst p� siden.';
	$locale['errDatasourceInaccessible'] = 'Datafilen kunne ikke hentes. Enten er det et problem p� %1$s eller ogs� har du skrevet det forkerte brugernavn - det kan indeholder tegn der ikke er tilladt - pr�v at generere den obligatoriske kode igen.';
	$locale['errDatasourceNotSaved'] = 'Der opstod en fejl der betyder at dine data <b>ikke er gemt</b>. Pr�v evt. igen senere, men hvis fejlen ikke forsvinder kan du kontakte administratoren via e-mail-adressen nederst p� siden.'; 

	//Collective stats
	$locale['collHeadline'] = 'Samlet statistik';
	$locale['collTimespan'] = "Statistik fra %1\$s til %2\$s.";
	$locale['collDay'] = "Statistik for %s.";
	$locale['collGeneratingHeadline'] = "Siden genereres...";
	$locale['collGeneratingText'] = "Sidens indhold genereres. Hvis siden ikke opdateres automatisk efter nogle sekunder, s� tryk venligst p� din browsers Opdater-knap.";
	
	$locale['collIndexHeadline'] = "Samlede statistikker";
	$locale['collIndexDesc'] = "Tryk p� en af de viste datoer for at se samlede statistikker for denne dato. De samlede statistikker er anonymiseret, s� ingen data kan f�res tilbage til en enkelt bruger eller en gruppe af brugere.";
	$locale['collIndexLinkDateFormat'] = 'd-m-Y, \u\g\e W';
	$locale['colCalWeek'] = "uge";
	$locale['collIndex'] = "";
	

	return $locale;
}


?>