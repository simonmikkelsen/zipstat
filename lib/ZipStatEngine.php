<?php
class ZipStatEngine
{
	/**
	 * The data source, as an instance of Datasource.
	 */
	var $datafil;
	
	/**
	 * The settings object.
	 */
	var $stier;
	
	/**
	 * The code lib.
	 */
	var $lib;
	
	var $time;
	var $screen_res;
	var $referer;
	var $colors;
	var $counterNo;
	var $counterName;
	var $jsSupport;
	var $useragent;
	var $ipAddr;
	var $lang;
	
	/**
	 * States if the search part of the url (from ? and the rest) should be
	 * cut off when dealing with the counters. @c true for yes, c false for
	 * no.
	 * @private
	 */
	var $counterIgnoreQuery;
	
	/**
	 * The name of the search engine registered for the latest visit.
	 * @private
	 */
	var $latestSearchEngine;
	
	/**
	 * Array of the search words registered for the latest visit.
	 * @private
	 */
	var $latestSearchWords;
	
	/**
	 * The prefered language registered for the latest visit.
	 * @private
	 */
	var $latestPrefLanguage;
	
	/**
	 * The top domain registered for the latest visit.
	 * @private
	 */
	var $latestTopdom;

	/**
	 * Crates a new instance.
	 * 
	 * $param $lib the instance of the code lib. Must contain the instance
	 *             of the data source and settings objects. Can be set to @c NULL
	 *             for test purposes - on your responserbillity!
	 */
	function ZipStatEngine(&$lib) {
		if ($lib !== NULL) {
			$this->datafil = &$lib->getDataSource();
			$this->lib = &$lib;
			$this->stier = &$lib->getStier();
		}
		
		$this->counterIgnoreQuery = true;
		
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->mstat = new Mstat($this->lib);
	}

	/**
	 *
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $time the time in unix format.
	 * @param $screen_res the screen resolution i &quot;XxY&quot; format.
	 * @param $referer the referer url.
	 * @param $colors the users screen resolution in &quot;XxY&quot; format.
	 * @param $javasupport is java enabled: <code>true</code> | <code>false</code> (as text).
	 * @param $counterNo the number of the counter.
	 * @param $counterName the name of the counter
	 * @param $jsSupport is javascript enabled: <code>true</code> | <code>false</code> (as text).
	 * @param $useragent the user agent identifyer.
	 * @param $ipAddr the users IP-adr.
	 * @param $lang the users language, as iso code.
	 * @param $url the url the hit was on.
	 * @return void
	 */
	function process($time, $screen_res, $referer,
	                 $colors, $javasupport, $counterNo, $counterName, $jsSupport,
	                 $useragent, $ipAddr, $lang, $url)
	{
		$this->latestSearchWords = array();
		$this->latestSearchEngine = "";
		
		$this->time = &$time;
		$this->screen_res = &$screen_res;
		$this->referer = &$referer;
		$this->colors = &$colors;
		$this->javasupport = &$javasupport;
		$this->counterNo = &$counterNo;
		$this->counterName = &$counterName;
		$this->jsSupport = &$jsSupport;
		$this->useragent = &$useragent;
		$this->ipAddr = &$ipAddr;
		$this->lang = &$lang;
		$this->url = &$url;
		$this->processStat();
	}

	function processStat()
	{
		$sec =  date('s', $this->time); //Second
		$min =  date('i', $this->time); //Minute
		$hour = date('G', $this->time); //Hour
		$mday = date('j', $this->time); //Day in month
		$mon =  date('n', $this->time); //Month
		$year = date('Y', $this->time); //Year
		$wday = date('w', $this->time); //Day of week
		$yday = date('z', $this->time); //Day of year

/*
		//The old system for monthly/collective stats are not used anymore
		//It is OK to delete the code
		
		//Initialisere datoer til mnedsstatistik
		$mstat_time = $this->datafil->getLine(89);
		$mstat_sec =  date('s',$mstat_time); //Sekund
		$mstat_min =  date('i',$mstat_time); //Minut
		$mstat_hour = date('G',$mstat_time); //time uden 0 foran timer < 10
		$mstat_mday = date('j',$mstat_time); //dag i mned
		$mstat_mon =  date('n',$mstat_time); //mned
		$mstat_year = date('Y',$mstat_time); //r
		$mstat_wday = date('w',$mstat_time); //ugedage
		$mstat_yday = date('z',$mstat_time); //dag i r

		if ( ($mstat_mon != $mon) or ($mstat_year != $year))
		{ //Det er tid til at rykke mnedsstatistikkerne en tak hen.
			for ($i = 90;$i <= 102;$i++)
			{
				$mstat_tmp = array();

				$mstat_tmp = explode(";;",$this->datafil->getLine($i));
				$mstat_tmp[1] = $mstat_tmp[0];
				$mstat_tmp[0] = "";
				$this->datafil->setLine($i, implode(";;",$mstat_tmp));
			}
			$this->datafil->setLine(89,$this->time);
		}
*/
		//Count up the eternal counter
		$tmp = $this->datafil->getLine(7);
		$tmp++;
		$this->datafil->setLine(7,$tmp);


		//Are we in a new month?
		$tmp2 = explode("::",$this->datafil->getLine(9));
		$tmp2 = $this->lib->addZeros(12-1,$tmp2);
		if ($this->datafil->getLine(10) != $mon)
		{ //New month
			$this->datafil->setLine(10,$mon);
			$tmp2[$mon-1] = 1;
			$this->datafil->setLine(79,0); //Reset number of unique visits this month
		}
		else
		{
			$tmp2[$mon-1]++;
		}
		$this->datafil->setLine(9,implode("::",$tmp2));

	//Tller for en mned 11, dato 12
	//    Hent datoen for dags dato
	//    Hvis mned ogs er lig, tl op,
	//    ellers st til en.

	//For den normale tller

	$tmp = explode("::",$this->datafil->getLine(12));
	$tmp2 = explode("::",$this->datafil->getLine(11));

	$tmp  = $this->lib->addZeros(12-1,$tmp);
	$tmp2 = $this->lib->addZeros(31-1,$tmp2);

	if (isset($tmp[$mday-1]) and $tmp[$mday-1] == $mon)
	{
		$tmp2[$mday-1]++;

		if (($this->lib->pro()) and ($tmp2[$mday-1] > $this->stier->getOption('max_hits_day_pro')))
			exit;
		elseif ((! $this->lib->pro()) and ($tmp2[$mday-1] > $this->stier->getOption('max_hits_day')))
			exit;

		//Til mnedsstatistik
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->datafil->setLine(101,Mstat::mstat_denneop($this->datafil->getLine(101),$mday-1));
		//$this->mstat->mstat_denneop(101, $mday-1);
	}
	else
	{
		$tmp2[$mday-1] = 1;
		$tmp[$mday-1] = $mon;
		$this->datafil->setLine(76,"0"); /*Nulstiller unikke hits i dag*/
	}

	//Den normale tller
	$this->datafil->setLine(11,implode("::",$tmp2));
	$this->datafil->setLine(12,implode("::",$tmp));


	//Evig tller der ikke kan nulstilles 13
	$tmp = $this->datafil->getLine(13);
	$tmp++;
	$this->datafil->setLine(13,$tmp);

	//Evig tller pr. time 14
	$tmp = explode("::",$this->datafil->getLine(14));
	$tmp = $this->lib->addZeros(24-1,$tmp);
	$tmp[$hour]++;
	$this->datafil->setLine(14,join("::",$tmp));

	//Til mnedsstatistik
	//$this->datafil->setLine(100,Mstat::mstat_denneop($this->datafil->getLine(100),$hour));
	//Old monthly/collective stats not used anymore - safe to delete
	//$this->mstat->mstat_denneop(100, $hour);


	//Tller ugedagstller op
	$tmp = explode("::",$this->datafil->getLine(15));

		for ($i = 0;$i < 7;$i++)
			if (!isset($tmp[$i]) or $tmp[$i] == 0 or $tmp[$i] == "")
				$tmp[$i] = 0;

	$tmp[$wday]++;
	$this->datafil->setLine(15,implode("::",$tmp));

	//$this->datafil->setLine(102,Mstat::mstat_denneop($this->datafil->getLine(102),$wday));
	//Old monthly/collective stats not used anymore - safe to delete
	//$this->mstat->mstat_denneop(102, $wday);

	//Max antal besgende p en dag max 16 ndret 17 bruger dage i 11
	$tmp = explode("::",$this->datafil->getLine(11));
	if (isset($tmp[$mday-1]) and $tmp[$mday-1] > $this->datafil->getLine(16))
	{
		$this->datafil->setLine(16,$tmp[$mday-1]);
		$this->datafil->setLine(17,$this->lib->kortdato());
	}

	//Max besgende p en mned 18, ndret 19, bruger 9 (mned - 1)
	$tmp = explode("::",$this->datafil->getLine(9));
	if ($tmp[$mon-1] > $this->datafil->getLine(18))
	{
		$tmp[$mon-1] = str_replace("\n", "", $tmp[$mon-1]);

		$this->datafil->setLine(18,$tmp[$mon-1]);
		$this->datafil->setLine(19,$this->lib->kortdato());
	}

	//Splitter til domner/topdomner
    $name = "";
    $latestRows = explode("::", $this->datafil->getLine(28));
    foreach ($latestRows as $row) {
            $cols = explode(";;", $row);
            if (count($cols) > 3 and $this->ipAddr === $cols[2]) {
                    $name = $cols[3];
                    break;
            }
    }

    if (strlen($name) == 0) {
            if ($this->stier->getOption('look_up_domains') == 1) {
                    $name = gethostbyaddr($this->ipAddr);
                    if ($name === $this->ipAddr)
                            $name = "";
            } elseif ($this->stier->getOption('look_up_domains') == 2) {
                    $ip = long2ip(ip2long($this->ipAddr)*1);
                    $ip = escapeshellarg($ip);
                    $out = shell_exec("host --quick --timeout=2 $ip");
                    $out = split("\n", $out);
                    if (count($out) >= 2) {
                            $out = split(" ", $out[0]);
                            if (count($out) >= 2) {
                              $name = $out[1];
                            }
                    }
            }
    }

	//Domner 20, antal 21
	$pro_max_stk = $this->lib->pro(7);

	if (strlen($name) === 0)
		$domaene = "Andre";
	elseif (!$this->stier->getOption('look_up_domains'))
		$domaene = "Ikke registreret";
	else
		$domaene = strtolower($this->lib->getDom($name));

	if ($domaene != "Andre")
	{
		$this->datafil->setLines(
			$this->stringstatmax(		/*Giver en array*/
				$domaene,					/*Ny tekst*/
				$this->datafil->getLine(20),	/*Tekst ind*/
				$this->datafil->getLine(21),	/*Tal ind*/
				$pro_max_stk				/*Max stk.*/
				)
			,20,21							/*Det er linie 20 og 21 der gives*/
			);
	}

	//Topdomner 22, antal 23

	$topdomaene = $this->lib->getTopDom($domaene);

	if (strlen($topdomaene) > 0)
	{
		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				$topdomaene,				/*Ny tekst*/
				$this->datafil->getLine(22),	/*Tekst ind*/
				$this->datafil->getLine(23)	/*Tal ind*/
				)
			,22,23							/*Det er linie 22 og 23 der gives*/
			);
		$this->latestTopdom = $topdomaene;
	}


	//Browser 24, antal 25
	$browser = $this->short_browser($this->useragent);
		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				$browser,					/*Ny tekst*/
				$this->datafil->getLine(24),	/*Tekst ind*/
				$this->datafil->getLine(25)	/*Tal ind*/
				)
			,24,25							/*Det er linie 24 og 25 der gives*/
			);

	//Til mnedsstatistik
		/*$this->datafil->setLines(
			Mstat::stringstat(			//Giver en array
				$browser,					//Ny tekst
				$this->datafil->getLine(90),	//Tekst ind
				$this->datafil->getLine(91)	//Tal ind
				)
			,90,91
			);*/
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->mstat->stringstat($browser, 90, 91);
		
	//Styreystem 26, antal 27
	$platform = $this->platform($this->useragent);
		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				$platform,					/*Ny tekst*/
				$this->datafil->getLine(26),	/*Tekst ind*/
				$this->datafil->getLine(27)	/*Tal ind*/
				)
			,26,27
			);

	/*
	  $REMOTE_ADDR = "201.223.145.185"; #Tmp-var
	  $REMOTE_HOST = "in2.image.dk"; #Tmp-var
	  Browser,,styresystem,,IP-adresse,,Domne,,tidspunkt-dato 28
	  */

	//Info om de seneste besgende, line 28 i datafilen
	$max20info = explode("::",$this->datafil->getLine(28));

	$pro_max20 = $this->lib->pro(2);
	while (sizeof($max20info) >= $pro_max20)
		array_shift($max20info);

	//Browserens streng er tideligere lagt i $browser

	$tid20 = $this->lib->kortdato();
	//Flgende er allerede krt tidligere
	//$platform = platform($HTTP_USER_AGENT);

	if ($this->ipAddr != false)
		$addr = $this->ipAddr;
	else
		$addr = "Ukendt";

	if (strlen($this->screen_res) === 0)
		$this->screen_res = "Andre";

	if (strlen($this->colors) === 0)
		$this->colors = "Andre";

	if ($this->lang != false and strlen($this->lang) >= 2) {
		//We only want the main language, which is the two first letters.
		//Normally we only gets two letters, but e.g. Opera may output
		//something like da;q=1.0,en;q=0.9
		//in this case we'll only get 'da'
		$lang = substr($this->lang, 0, 2);
	} else {
		$lang = "Andre";
	}

	//Set default values. "Ikke opgivet" is danish for "Not given".
	if ($this->url != false)
		$http_ref = $this->url;
	else
		$http_ref = "Ikke opgivet";

	if (strlen($this->referer) === 0)
		$this->referer = "Ikke opgivet";

	//If the referer url is a page on ZIP Stat, then remove the search part.
	//ZIP Stat has since been updated not to expose urls with passwords, but this
	//is just more security in case that failes.
	if (
			strpos(strtolower($this->referer),strtolower($this->stier->getOption('cgiURL'))) > 0
			and
			strpos($this->referer,'?') > 0
		) {
		$this->referer = substr($this->referer,0,strpos($this->referer,'?'));
	}

	if (strlen($name) === 0)
		$name = "Ikke opgivet";

	$max20info[$pro_max20 - 1] = $browser.",,".$platform.",,".$addr.",,".$name.",,"
								.$tid20.",,".$this->screen_res.",,".$this->colors.",,"
								.$lang.",,".$http_ref
								.",,".$this->referer;
	$this->datafil->setLine(28,implode("::",$max20info));
	//echo $this->datafil->getLine(28);

	//Sprog i ISO-kode 29 antal 30
	//We got $lang from above stat.
	$lang = strtolower($lang);

	if (preg_match("/^[a-z][a-z]/",$lang))
	{
		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				substr($lang,0,2),		/*Ny tekst*/
				$this->datafil->getLine(29),	/*Tekst ind*/
				$this->datafil->getLine(30)	/*Tal ind*/
				)
			,29,30
			);
		$this->latestPrefLanguage = substr($lang,0,2);
	}

	//Oplsning 31 antal 32
	if (preg_match("/[0-9]{3,4}x[0-9]{3,4}/",$this->screen_res))
			$opl = $this->screen_res;
		else
			$opl = "Andre";

	$this->datafil->setLines(
		$this->stringstat(			/*Giver en array*/
			$opl,							/*Ny tekst*/
			$this->datafil->getLine(31),	/*Tekst ind*/
			$this->datafil->getLine(32)	/*Tal ind*/
			)
		,31,32
		);

	//Til mnedsstatistik
	/*$this->datafil->setLines(
		MStat::stringstat(			//Giver en array
			$opl,							//Ny tekst
			$this->datafil->getLine(92),	//Tekst ind
			$this->datafil->getLine(93)	//Tal ind
			)
		,92,93
		);*/
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->mstat->stringstat($opl, 92, 93);

	//Farver (bit) 33 antal 34
	$cols = $this->colors;
	if (($cols == round(1*$cols)) and ($cols > 0) or 1)
		{
		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				$cols,						/*Ny tekst*/
				$this->datafil->getLine(33),	/*Tekst ind*/
				$this->datafil->getLine(34)	/*Tal ind*/
				)
			,33,34
			);

		//Til mnedsstatistik
		/*$this->datafil->setLines(
			MStat::stringstat(			//Giver en array
				$cols,						//Ny tekst
				$this->datafil->getLine(94),	//Tekst ind
				$this->datafil->getLine(95)	//Tal ind
				)
			,94,95
			);*/
		}
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->mstat->stringstat($cols, 94, 95);

	//JAVA support 35 antal 36
	if (strlen($this->javasupport) === 0)
		$this->javasupport = "Ved ikke";

	if ($this->javasupport == "1") $this->javasupport = "true";
	if ($this->javasupport == "0") $this->javasupport = "flase";

	if ( ($this->javasupport === "true") or ($this->javasupport === "false") or ($this->javasupport === "Ved ikke") )
	{
		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				$this->javasupport,				/*Ny tekst*/
				$this->datafil->getLine(35),	/*Tekst ind*/
				$this->datafil->getLine(36)	/*Tal ind*/
				)
			,35,36
			);
	}

	//37 visits, 38 names/urls
	$cntVisits = explode("::",$this->datafil->getLine(37));
	$cntUrls = explode("::",$this->datafil->getLine(38));
	$max_counters = $this->lib->pro(5);
	
	//Count for this url.
	list($cntUrls, $cntVisits) = $this->applyPageCounter($cntUrls, $cntVisits, $max_counters);
	
	$this->datafil->setLine(37,implode("::",$cntVisits));
	$this->datafil->setLine(38,implode("::", $cntUrls));

	//JAVA-script slet til 39 antal 40
	if (strlen($this->jsSupport) === 0)
		$this->jsSupport = "Ved ikke";

		$this->datafil->setLines(
			$this->stringstat(			/*Giver en array*/
				$this->jsSupport,					/*Ny tekst*/
				$this->datafil->getLine(39),	/*Tekst ind*/
				$this->datafil->getLine(40)	/*Tal ind*/
				)
			,39,40
			);

		//Til mnedsstatistik
		/*$this->datafil->setLines(
			MStat::stringstat(			//Giver en array
				$this->jsSupport,					//Ny tekst
				$this->datafil->getLine(96),	//Tekst ind
				$this->datafil->getLine(97)	//Tal ind
				)
			,96,97
			);*/
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->mstat->stringstat($this->jsSupport, 96, 97);


		//Antal unikke IP-adresser 44, 50 IP-adresser 45
		/*
			64-Unikke IP-adresser til hits/bruger
			65-Hits til hits/bruger
			66-Ugenumre p ovenstende

			72-Unix-tider til linie 45
			73-antal sek ialt p siden::antal besgende

			112-Indgangssider
			113-Hits til ovenstende
			114-Udgangssider
			115-Hits til ovenstende
			116-Sider der kan blive udgangssider - synkroniseret med 44/45
		*/


		$pro_max_ipadr = $this->lib->pro(1);
		$nutid = time();
		$ugenr = round(($yday/7)+1);
		$genr = $ugenr;
		while ($ugenr > 3)
			$ugenr -= 3;

		$tmp3 = explode("::",$this->datafil->getLine(64));
		$tmp4 = explode("::",$this->datafil->getLine(65));

		if ($this->datafil->getLine(66) != $genr)
		{
			$tmp3 = array(0,$tmp3[0],$tmp3[1]);
			$tmp4 = array(0,$tmp4[0],$tmp4[1]);
			$this->datafil->setLine(66,$genr);
		}
		$tmp4[0]++;

		///////
		$tmp = explode(":",$this->datafil->getLine(45));
		$unixtid = explode(":",$this->datafil->getLine(72));
		$udSider = explode("::",$this->datafil->getLine(116)); /*Kandidater til udgangssider*/

		while (sizeof($unixtid) > sizeof($tmp))
			array_shift($unixtid);
		while (sizeof($tmp) > sizeof($unixtid))
			array_shift($tmp);
		while (sizeof($udSider) > sizeof($unixtid))
			array_shift($udSider);

		//Sider hittet registreres fra
		$paaSide = $this->url;
		$paaSide = str_replace("::", ":", $paaSide);

		//If the IP address exists: Not unique
		if (! $this->lib->isVisitUnique($this->ipAddr))
		{
			for ($i = 0; $i < sizeof($tmp);$i++)
			{
				if ($tmp[$i] == $this->ipAddr)
				{
					array_splice($tmp,$i,1);

					if ($unixtid[$i])
					{
						//Splitter antal sek ialt og antal besgende
						$tider = explode(":",$this->datafil->getLine(73));
						if ($nutid - $unixtid[$i] < 3600*10)
						{ /*Hvis personen ikke har vret der for lnge.*/
							$tider[0] += ($nutid - $unixtid[$i]);
							$tider[1]++;
							$this->datafil->setLine(73,implode(":",$tider));
						}
					} /*Slut p if unixtid[$i]*/
					array_splice($unixtid,$i,1);
					array_splice($udSider,$i,1); /*Fjerner den kandidat der blev registreret sidst.*/
					break;
				}
			}
		}
		else
		{
		//The visit is unique
			$tmp3[0]++;
			$linie44 = $this->datafil->getLine(44);
			$linie44++;
			$this->datafil->setLine(44,$linie44);

		/*
		112-Indgangssider
		113-Hits til ovenstende
		114-Udgangssider
		115-Hits til ovenstende
		116-Sider der kan blive udgangssider - synkroniseret med 44/45
		*/

			//Tilfjer siden som ingangsside
			if ($this->lib->okurl($paaSide))
			{
				$this->datafil->setLines(
					$this->stringstatmax(		/*Giver en array*/
						$paaSide,					/*Ny tekst*/
						$this->datafil->getLine(112),	/*Tekst ind*/
						$this->datafil->getLine(113),	/*Tal ind*/
						$pro_max_ipadr				/*Max stk.*/
						)
					,112,113							/*Det er linie 20 og 21 der gives*/
					);
			}

		/*
		76-Unikke hits i dag
		77-max unikke hits p en dag
		78-Dato for ovenstende
		79-unikke hits i denne mned
		80-max unikke hits i en mned
		81-Dato for ovenstende
		*/
				$tmp76 = $this->datafil->getLine(76);
				$tmp77 = $this->datafil->getLine(77);

				$tmp79 = $this->datafil->getLine(79);
				$tmp80 = $this->datafil->getLine(80);

			$tmp76++;
			$this->datafil->setLine(76,$tmp76);

			$tmp79++;
			$this->datafil->setLine(79,$tmp79);

			if ($tmp76>$tmp77)
			{
				$this->datafil->setLine(77,$tmp76);
				$this->datafil->setLine(78,$this->lib->kortdato());
			}

			if ($tmp79>$tmp80)
			{
				$this->datafil->setLine(80,$tmp79);
				$this->datafil->setLine(81,$this->lib->kortdato());
			}

		} /*Slut p else: unik*/
	//$tl->timeLog("2");

		while ((sizeof($tmp) > $pro_max_ipadr) and ($pro_max_ipadr > 0))
			array_shift($tmp);

		while ((sizeof($unixtid) > $pro_max_ipadr) and ($pro_max_ipadr > 0))
			array_shift($unixtid);

		$this->datafil->setLine(45,implode(":",$tmp). ":" . $this->ipAddr);
		$this->datafil->setLine(72,implode(":",$unixtid). ":" . $nutid);
		array_push($udSider,$paaSide);

		/*
		114-Udgangssider
		115-Hits til ovenstende
		116-Sider der kan blive udgangssider - synkroniseret med 44/45
		*/

		while ((sizeof($udSider) > $pro_max_ipadr) and ($pro_max_ipadr > 0))
		{
			$udSide = array_shift($udSider);

			$this->datafil->setLines(
				$this->stringstatmax(		/*Giver en array*/
					$udSide,					/*Ny tekst*/
					$this->datafil->getLine(114),	/*Tekst ind*/
					$this->datafil->getLine(115),	/*Tal ind*/
					$pro_max_ipadr				/*Max stk.*/
					)
				,114,115							/*Det er linie 20 og 21 der gives*/
				);
		}
		$this->datafil->setLine(116,implode("::",$udSider));

		$this->datafil->setLine(64,implode("::",$tmp3));
		$this->datafil->setLine(65,implode("::",$tmp4));

	//-----------------------------------------------------------
	//Registrer sgeord 47 antal 48 og sgemaskiner 49 antal 50

	//Definere sgemaskiner
	$smask[0] = "altavista"; //.com
	$sords[0] = "q";

	$smask[1] = "excite"; //.com
	$sords[1] = "search";

	$smask[2] = "lycos"; //.com/.dk
	$sords[2] = "query";

	$smask[3] = "webcrawler"; //.com
	$sords[3] = "searchText";

	$smask[4] = "hotbot"; //.com
	$sords[4] = "MT";

	$smask[5] = "yahoo"; //.com
	$sords[5] = "p";

	$smask[6] = "jubii"; //.dk
	$sords[6] = "soegeord";

	$smask[7] = "sol"; //.dk
	$sords[7] = "q";

	$smask[8] = "yahuu"; //.dk
	$sords[8] = "query";

	$smask[9] = "voila"; //.com
	$sords[9] = "kw";

	$smask[10] = "msn"; //.com/.dk
	$sords[10] = "MT";

	$smask[11] = "auu"; //.dk
	$sords[11] = "q";

	$smask[12] = "overture"; //.com
	$sords[12] = "Keywords";

	$smask[13] = "crawler"; //.de
	$sords[13] = "query";

	$smask[14] = "suchen"; //.com
	$sords[14] = "q";

	$smask[15] = "goto"; //.com
	$sords[15] = "Keywords";

	$smask[16] = "directhit"; //.com
	$sords[16] = "qry";

	$smask[17] = "thunderstone"; //.com
	$sords[17] = "q";

	$smask[18] = "northernlight"; //.com
	$sords[18] = "qr";

	$smask[19] = "whatuseek"; //.com
	$sords[19] = "arg";

	$smask[20] = "about"; //.com
	$sords[20] = "terms";

	$smask[21] = "planetsearch"; //.com
	$sords[21] = "text";

	$smask[21] = "google"; //.com
	$sords[21] = "q";

	$smask[22] = "factfinder"; //.dk
	$sords[22] = "scope";

	$smask[23] = "add2me"; //.dk
	$sords[23] = "query";

	$smask[24] = "cybercity"; //.dk
	$sords[24] = "words";

	$smask[25] = "map.net.uni-c"; //.dk
	$sords[25] = "keyword";

	$smask[26] = "ditdanmark"; //.dk
	$sords[26] = "Sogeord";

	$smask[27] = "search.dmoz"; //.org
	$sords[27] = "search";

	$smask[28] = "1klik"; //.dk
	$sords[28] = "query";

	$smask[29] = "123portal"; //.dk
	$sords[29] = "HarIngen"; //Benytter POST, s sgeord kan ikke udtrkkes.

	$smask[30] = "netstjernen"; //.dk
	$sords[30] = "HasNone"; //Is using POST, so we can't get keywords.

	$smask[31] = "alltheweb"; //.com
	$sords[31] = "query";

	$smask[32] = "opasia"; //find.opasia.dk
	$sords[32] = "q";

	$smask[33] = "ofir"; //find.opasia.dk
	$sords[33] = "querytext";

	$smask[34] = "find"; //.dk
	$sords[34] = "string";

	$smask[35] = "krak"; //.dk
	$sords[35] = "sogenavn_vis"; //Uses a redirect that may prevent us from getting it.

	$smask[36] = "eniro"; //.dk
	$sords[36] = "q"; //We may not get this due to a redirect via Google.

	$smask[37] = "degulesider"; //.dk
	$sords[37] = "compTrade";
	
	

		/*
		http://www.factfinder.dk/compass?scope=ord1&ui=sr&view-template=simple1
		http://add2me.dk/add2me/search.cgi?query=ord1
		http://www.cybercity.dk/search/start_seek.phtml?config=&restrict=&exclude=&method=and&format=long&sort=score&words=ord1
		http://www.map.net.uni-c.dk/getwww.cgi?keyword=ord1&submit-b=SEARCH
		http://www.ditdanmark.dk/soegres.asp?admId=1&menu=soeg&Sogeord=ord1&Sogeomraade=1
		http://search.dmoz.org/cgi-bin/search?search=ord1&all=yes&cat=World%2FDansk

		http://www.google.com/search?q=ord1
		http://www.auu.dk:80/cgi-bin/query?mss=da%2Fsimple&pg=q&fmt=.&what=web&user=searchintranet&enc=iso88591&site=main&q=Skriv+s%F8geord+her...&q1=Skriv+soegeord+her...&filter=intranet
		http://infoseek.go.com/Titles?qt=ord1&col=WW&sv=IS&lk=noframes
		http://crawler.de/cgi-bin/suche.C?Maschine=CrawlerNeu&limit=2&Menue=35&query=ord1&anzahl=10
		http://www.hotbot.com/?MT=ord1&SM=MC&DV=0&LG=any&DC=10&DE=2&BT=H&Search.x=35&Search.y=8
		http://suchen.com/?q=ord1&delay=25&hits=30&method=any&com-excite=on&com-northernlight=on&com-goto=on&com-webcrawler=on&com-thunderstone=on&com-magellan=on&com-miningco=on&com-directhit=on&com-whatuseek=on&com-planetsearch=on
		http://www.goto.com/d/search/;$sessionid$VHZ3TIAABGA5LQFIEE3QPUQ?type=home&Keywords=ord1
		http://www.directhit.com/fcgi-bin/TopTenDemo.fcg?cmd=demo_qry&oose=1&qry=ord1
		http://search.thunderstone.com/texis/websearch/?q=ord1&max=20&dbsu=1
		http://www.northernlight.com/nlquery.fcg?cb=0&qr=ord1&orl=
		http://element.whatuseek.com/cgi-bin/texis/texis/meta?shock=0&adspace=SearchResults2&scheme=orange&arg=ord1
		http://search.about.com/scripts/query70.asp?terms=ord1&Site=home&SUName=home&TopNode=%2F&PM=59_100_S
		http://www.planetsearch.com/?a=1&flags=7&count=10&index=1&text=ord1
		*/
	$pro_maxsoegeord = $this->lib->pro(8);

	/*
	$ind{'referer'} = "http://www.google.com/search?q=ord5K";
	$pro_maxsoegeord = 3;
	$inddata[47] = "ord1K::ord2K::ord3K::ord4K::ord5K";
	$inddata[48] = "11::22::33::44::55";
	*/

	$tmp = explode("/",strtolower($this->referer));
	$hitFraSoegemaskine = 0;

	//Afkoder url'en fra sgemaskinen
	$sord = $this->afkod($this->referer);

	if (isset($tmp[2])) {
		for ($i = 0;$i < sizeof($smask);$i++)
		{
			if (strpos(strtolower($tmp[2]),strtolower($smask[$i])) !== false)
			{
			//We recognized the referer url as a search engine
			
			$seName = ucwords(strtolower($smask[$i]));
			$this->datafil->setLines(
				$this->stringstat(			/*Giver en array*/
					$seName,	/*Ny tekst*/
					$this->datafil->getLine(49),	/*Tekst ind*/
					$this->datafil->getLine(50)	/*Tal ind*/
					)
				,49,50
				);
				$this->latestSearchEngine = $seName;
				
			//Til mnedsstatistik
			/*$this->datafil->setLines(
				MStat::stringstat(			//Giver en array
					ucwords(strtolower($smask[$i])),	//Ny tekst
					$this->datafil->getLine(98),	//Tekst ind
					$this->datafil->getLine(99)	//Tal ind
					)
				,98,99
				);*/
		//Old monthly/collective stats not used anymore - safe to delete
		//$this->mstat->stringstat(ucwords(strtolower($smask[$i])), 98, 99);

				//Register the search keywords, if any
				if (isset($sord[$sords[$i]]) and strlen($sord[$sords[$i]]) > 0)
				{
					$sord[$sords[$i]] = str_replace("+", " ", $sord[$sords[$i]]);
					$ord = explode(" ",$sord[$sords[$i]]);
					foreach ($ord as $ordk)
					{
						$ordk = preg_replace("/[\+\?\"]/","",$ordk);
						$this->latestSearchWords[] = $ordk;
						
						$this->datafil->setLines(
							$this->stringstatmax(		/*Giver en array*/
								$ordk,						/*Ny tekst*/
								$this->datafil->getLine(47),	/*Tekst ind*/
								$this->datafil->getLine(48),	/*Tal ind*/
								$pro_maxsoegeord			/*Max stk.*/
								)
							,47,48							/*Det er linie 20 og 21 der gives*/
							);
					}
				}
				$hitFraSoegemaskine = 1; /*Hittet kommer fra en sgemaskine - skal bruges i referencesider*/
				break; /*Bryder ud af for-lkken*/
			} /*Slut p if ($tmp[2] ...*/
		} //End for
	} //End if isset($tmp[2])
	
	//De seneste 50 referencesider 46 (uden sidens URL)

	//Format:
	//side;;hits::

	$ejerSide = $this->datafil->getLine(3);

	//Reference url.
	$ref = $this->referer;

	//The site is:
	$comp = new UrlComparator();
	$refererUrlIsOwner = $comp->isInSite($ejerSide, $ref);
	if (
			($refererUrlIsOwner === false) //not the owners
			and
			(strpos(strtolower($ref),"http://") !== false) //it seems valid
			and
			($hitFraSoegemaskine === 0) //and is not from a search engine.
		)
	{
		if (strpos(strtolower($ref),"http://") === 0)
			$ref = substr($ref,7);
		if (strpos($ref,"?") !== false)
			$ref = substr($ref,0,strpos($ref,"?"));
		if (strrpos($ref,"/") === strlen($ref)-1)
			$ref = substr($ref,0,strlen($ref)-1);
		$refStier = explode("::",$this->datafil->getLine(46));

		//Find out of the urls is already registered.
		$sted = -1;
		for ($i = 0; $i < sizeof($refStier); $i++)
		{
			if (strpos(strtolower($refStier[$i]),strtolower("$ref;;")) === 0)
			{
				$sted = $i;
				break;
			}
		}

		//Yes it is.
		if ($sted !== -1)
		{
			$tmp2 = explode(";;",$refStier[$sted]);
			$tmp2[1]++;
			$refStier[$sted] = implode(";;",$tmp2);
			$tmp3 = $refStier[$sted];
			array_splice($refStier,$sted,1);
			array_unshift($refStier,$tmp3);

			$pro_max_ref = $this->lib->pro(0);
			while (sizeof($refStier) > $pro_max_ref)
				array_pop($refStier);
				
			//Remove old ref urls that are part of the site.
			//      The old detection was that not that good.
			$cleanedSet = array();
			for ($i = 0; $i < sizeof($refStier); $i++)
			{
				list($refererUrl, $hits) = split(';;', $refStier[$i]);
				if (!$comp->isInSite($ejerSide, 'http://'.$refererUrl)) {
					$cleanedSet[] = $refStier[$i];
				}
			}

			$this->datafil->setLine(46,implode("::",$cleanedSet));
		}
		else
		{ //No: Add it.
			$tmp = $this->datafil->getLine(46);
			$this->datafil->setLine(46,$ref . ";;1" . "::" . $tmp);
		}

	}

	//////////////////////
	//74-Bevgelser :: separeret
	//75-Hits til ovenstende

	//Stter url'erne lig intet hvis Ikke opgivet
	$ownerSite = $this->datafil->getLine(3);
	$refside = $this->referer;

	//Is the referer site an internal site?
	if (  $this->referer != "Ikke opgivet" and $refererUrlIsOwner  )
	{
		//if ($this->url !== "Ikke opgivet")
		$paaside = $this->url;

		//Finder filnavn
		$sidenavnArray = explode("/",$paaside);
		$sidenavnArray = array_reverse($sidenavnArray);
		$sidenavn = $sidenavnArray[0];

		//Fjerner tekst efter ?, # og + alle inkl.
		if (strpos($sidenavn,"?") !== false)
			$sidenavn = substr($sidenavn,0,strpos($sidenavn,"?"));
		if (strpos($sidenavn,"#") !== false)
			$sidenavn = substr($sidenavn,0,strpos($sidenavn,"#"));
		if (strpos($sidenavn,"+") !== false)
			$sidenavn = substr($sidenavn,0,strpos($sidenavn,"+"));

		$refnavnArray = explode("/",$refside);
		$refnavnArray = array_reverse($refnavnArray);
		$refnavn = $refnavnArray[0];
		//Fjerner tekst efter ? og # begge inkl.
		if (strpos($refnavn,"?") !== false)
			$refnavn = substr($refnavn,0,strpos($refnavn,"?"));
		if (strpos($refnavn,"#") !== false)
			$refnavn = substr($refnavn,0,strpos($refnavn,"#"));

		if (strlen($refnavn) > 7 and strlen($sidenavn) > 7) /*strlen(http://) === 6, so at least 7 */
		{
			//$hjemmeside = $this->datafil->getLine(3);

			//if (!preg_match("/^$hjemmeside/i",$this->referer))
			//	$refnavn = "";

			$bev = $refnavn ."->". $sidenavn;

			$pro_max_bevaeg = $this->lib->pro(11);

			$this->datafil->setLines(
				$this->stringstatmax(		/*Giver en array*/
					$bev,							/*Ny tekst*/
					$this->datafil->getLine(74),	/*Tekst ind*/
					$this->datafil->getLine(75),	/*Tal ind*/
					$pro_max_bevaeg			/*Max stk.*/
					)
				,74,75							/*Det er linie 20 og 21 der gives*/
				);

		}
	} /*End of if not on an internal site*/

	//$tl->timeLog("3");

} /*End of function processStat*/

/**
 * Tilfjet et element, men srger for at der hjest er et givet antal elementer.
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @return String[]
 */
function stringstatmax($nyTekst,$eTekst,$eTal,$maxstk)
{
//Kald	($tekststreng,$talstreng) = &stringstatmax($nytekst,$eksisterende_tekststreng,$eksisterende_talstreng,$max_stk:integer);

	//Hvis der ikke er nogen ny tekst.
	if (sizeof($nyTekst) === 0)
		return array($eTekst,$eTal);

	$maxstk = ($maxstk >= 0) ? $maxstk : 0;

	$eTekstArray = explode("::",$eTekst);
	$eTalArray = explode("::",$eTal);

	$ufs = $this->stringstat($nyTekst,$eTekst,$eTal,"array");
	$uTekst = $ufs[0];
	$uTal = $ufs[1];

	while (sizeof($uTekst) > $maxstk)
		{
		array_pop($uTekst);
		array_pop($uTal);
		}

	$uTekstStr = implode("::",$uTekst);

		//Er der :: i starten, s slet dem
		if (strpos($uTekstStr,"::") === 0)
			$uTekstStr = substr($uTekstStr,2);

		//Er der :: i slutningen, s slet dem
		if (strpos($uTekstStr,"::",strlen($uTekstStr)-2) === strlen($uTekstStr)-2)
			$uTekstStr = substr($uTekstStr,0,strlen($uTekstStr)-2);

	$uTalStr = implode("::",$uTal);

		//Er der :: i starten, s slet dem
		if (strpos($uTalStr,"::") === 0)
			$uTalStr = substr($uTalStr,2);

		//Er der :: i slutningen, s slet dem
		if (strlen($uTalStr)-2 >= 0 and strpos($uTalStr,"::",strlen($uTalStr)-2) === strlen($uTalStr)-2 and strlen($uTalStr) > 0)
			$uTalStr = substr($uTalStr,0,strlen($uTalStr)-2);

	return array ($uTekstStr,$uTalStr);
} /*Slut p sub stringstatmax*/

/**
 *
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 * @param $nyTekst den tekst der skal tilfjes
 * @param $eTekst den tekst el. array der indeholder den eksisterende tekst.
 * @param $eTal de tal (str el. array) der indeholder tallene.
 * @param $returnType &quot;<code>array</code>&quot; hvis der nskes to
 *                    arrays, &quot;<code>string</code>&quot; hvis der
 *                    nskes to strings.
 * @return String[]
 */
function stringstat($nyTekst,$eTekst,$eTal,$returnType = "string")
{
	//If it's not an array, make it an array
	if (! is_array($eTekst))
		$eTekst = explode("::",$eTekst);
	if (! is_array($eTal))
		$eTal = explode("::",$eTal);

	//If the two array not are the same length.
	if (sizeof($eTekst) > sizeof($eTal))
		$eTekst = array_slice($eTekst,0,sizeof($eTal));
	elseif (sizeof($eTekst) < sizeof($eTal))
		$eTal = array_slice($eTal,0,sizeof($eTekst));

	if ($nyTekst !== "" and strlen($nyTekst) > 0)
	{
		$sted = array_search($nyTekst,$eTekst);

		//Hvis den findes
		if (!($sted === false or $sted === null))
		{
			$indTekst = $eTekst[$sted];
			$indTal = $eTal[$sted];
			$indTal++;

			array_splice($eTekst,$sted,1);
			array_splice($eTal,$sted,1);
		}
		else
		{
			$indTekst = $nyTekst;
			$indTal = 1;
		}
		array_unshift($eTekst,$indTekst);
		array_unshift($eTal,$indTal);
	} /*End of if $nyTekst !== ""*/

	if ($returnType === "array")
		return array($eTekst,$eTal);
	elseif ($returnType === "string")
	{
		return array(
			implode("::",$eTekst),
			implode("::",$eTal)
			);
	}
	else
	{
		echo "<b>Error:</b> Incorrect returntype ($returnType) given as 4th parameter to function <code>stringstat()</code> in zipstat.php line ".__LINE__.".";
		exit;
	}
}

/**
 * Returnere browsernavnet.
 * Modtager den tekststreng browserens identificerer sig med.
 */
function short_browser($agent) {
$agentl = strtolower($agent);
if (strpos($agentl, 'mozilla/') !== FALSE and preg_match("#Mozilla/(\d)#i",$agent,$verNN))
	{
	if (strpos($agentl, 'compatible') !== FALSE)
		{
		if (strpos($agentl, 'webtv') !== FALSE and preg_match("#WebTV#i",$agent,$ver))
			{$longagent = "WebTV";}
		elseif (strpos($agentl, 'opera ') !== FALSE and preg_match("#opera (\d+)#i",$agent,$ver))
			{$longagent = "Opera v$ver[1].X";}
		elseif (strpos($agentl, 'opera') !== FALSE and preg_match("#opera/(\d+)#i",$agent,$ver))
			{$longagent = "Opera v$ver[1].X";}
		elseif (strpos($agentl, 'konqueror/') !== FALSE and preg_match("#konqueror/(\d)#i",$agent,$ver))
			{$longagent = "Konqueror v$ver[1].X";}
		elseif (strpos($agentl, 'konqueror') !== FALSE and preg_match("#konqueror (\d)#i",$agent,$ver))
			{$longagent = "Konqueror v$ver[1].X";}
		elseif (strpos($agentl, 'msie ') !== FALSE and preg_match("#MSIE (\d+)#i",$agent,$ver))
			{$longagent = "MSIE v$ver[1].X";}
		elseif (strpos($agentl, 'internet ninja ') !== FALSE and preg_match("#Internet Ninja (\d)#i",$agent,$ver))
			{$longagent = "Internet Ninja v$ver[1].X";}
		elseif (strpos($agentl, 'netbox/') !== FALSE and preg_match("#Netbox/(\d)#i",$agent,$ver))
			{$longagent = "Netbox v$ver[1].X";}
		elseif (strpos($agentl, 'lotus-notes/') !== FALSE and preg_match("#Lotus-Notes/(\d)#i",$agent,$ver))
			{$longagent = "Lotus-Notes v$ver[1].X";}
		elseif (strpos($agentl, 'webwasher ') !== FALSE and preg_match("#WebWasher (\d)#i",$agent,$ver))
			{$longagent = "WebWasher v$ver[1].X";}
		elseif (strpos($agentl, 'php/') !== FALSE and preg_match("#PHP/(\d)#i",$agent,$ver))
			{$longagent = "PHP v$ver[1].X";}
		elseif (strpos($agentl, 'realdownload/') !== FALSE and preg_match("#RealDownload/(\d)#i",$agent,$ver))
			{$longagent = "RealDownload v$ver[1].X";}
		elseif (strpos($agentl, 'wget/') !== FALSE and preg_match("#Wget/(\d)#i",$agent,$ver))
			{$longagent = "Wget v$ver[1].X";}
		elseif (strpos($agentl, 'ms frontpage ') !== FALSE and preg_match("#MS FrontPage (\d)#i",$agent,$ver))
			{$longagent = "MS FrontPage v$ver[1].X";}
		elseif (strpos($agentl, 'aol ') !== FALSE and preg_match("#AOL (\d)#i",$agent,$ver))
			{$longagent = "AOL's Browser v$ver[1].X";}
		elseif (strpos($agentl, 'aol-iweng ') !== FALSE and preg_match("#AOL-IWENG (\d)#i",$agent,$ver))
			{$longagent = "AOL's Browser v$ver[1].X";}
		elseif (strpos($agentl, 'bordermanager ') !== FALSE and preg_match("#BorderManager (\d)#i",$agent,$ver))
			{$longagent = "BorderManager v$ver[1].X";}
		elseif (strpos($agentl, 'avantgo ') !== FALSE and preg_match("#AvantGo (\d)#i",$agent,$ver))
			{$longagent = "AvantGo v$ver[1].X";}
		elseif (strpos($agentl, 'acorn phoenix ') !== FALSE and preg_match("#Acorn Phoenix (\d)#i",$agent,$ver))
			{$longagent = "Acorn Phoenix v$ver[1].X";}
		elseif (strpos($agentl, 'httrack ') !== FALSE and preg_match("#HTTrack (\d)#i",$agent,$ver))
			{$longagent = "HTTrack v$ver[1].X";}
		elseif (strpos($agentl, 'icab ') !== FALSE and preg_match("#iCab (\d)#i",$agent,$ver))
			{$longagent = "iCab v$ver[1].X";}
		elseif (strpos($agentl, 'voyager') !== FALSE)
			{$longagent = "Voyager";}
		else
			{$longagent = "Andre browsere";}
		}
	elseif(strpos($agentl, 'gecko') !== FALSE and preg_match("#Gecko#i",$agent,$ver))
		{
		if (strpos($agentl, 'chrome') !== FALSE)
			{$longagent = "Google Chrome";}
		elseif (strpos($agentl, 'safari') !== FALSE)
			{$longagent = "Safari";}
		elseif (strpos($agentl, 'netscape') !== FALSE and preg_match("#Netscape(\d+)#i",$agent,$ver))
			{$longagent = "Netscape v$ver[1].X";}
		elseif (strpos($agentl, 'netscape') !== FALSE and preg_match("#Netscape/(\d+)#i",$agent,$ver))
			{$longagent = "Netscape v$ver[1].X";}
		elseif (strpos($agentl, 'firefox') !== FALSE and preg_match("#Firefox/(\d+)#i",$agent,$ver))
			{$longagent = "Firefox v$ver[1].X";}
		elseif (strpos($agentl, 'galeon') !== FALSE and preg_match("#Galeon/(\d+)#i",$agent,$ver))
			{$longagent = "Galeon v$ver[1].X";}
		elseif (strpos($agentl, 'iceweasel') !== FALSE and preg_match("#Iceweasel/(\d+)#i",$agent,$ver))
			{$longagent = "Iceweasel v$ver[1].X";}
		elseif (strpos($agentl, 'iceape') !== FALSE and preg_match("#Iceape/(\d+)#i",$agent,$ver))
			{$longagent = "Iceape v$ver[1].X";}
		elseif (strpos($agentl, 'epiphany') !== FALSE and preg_match("#Epiphany/(\d+)#i",$agent,$ver))
			{$longagent = "Epiphany v$ver[1].X";}
		elseif (strpos($agentl, 'mozilla firebird') !== FALSE)
			{$longagent = "Mozilla Firebird";}
		else
			{$longagent = "Mozilla";}
		}
	elseif (strpos($agentl, 'netscape') !== FALSE and preg_match("#Netscape(\d+)#i",$agent,$ver))
		{$longagent = "Netscape v$ver[1].X";}
	elseif (strpos($agentl, 'netscape') !== FALSE and preg_match("#Netscape/(\d+)#i",$agent,$ver))
		{$longagent = "Netscape v$ver[1].X";}
	elseif (strpos($agentl, 'firefox') !== FALSE and preg_match("#Firefox/(\d+)#i",$agent,$ver))
		{$longagent = "Firefox v$ver[1].X";}
	elseif (strpos($agentl, 'galeon') !== FALSE and preg_match("#Galeon/(\d+)#i",$agent,$ver))
		{$longagent = "Galeon v$ver[1].X";}
	elseif (strpos($agentl, 'iceweasel') !== FALSE and preg_match("#Iceweasel/(\d+)#i",$agent,$ver))
		{$longagent = "Iceweasel v$ver[1].X";}
	elseif (strpos($agentl, 'iceape') !== FALSE and preg_match("#Iceape/(\d+)#i",$agent,$ver))
		{$longagent = "Iceape v$ver[1].X";}
	elseif (strpos($agentl, 'epiphany') !== FALSE and preg_match("#Epiphany/(\d+)#i",$agent,$ver))
		{$longagent = "Epiphany v$ver[1].X";}
	elseif (strpos($agentl, 'mozilla firebird') !== FALSE)
		{$longagent = "Mozilla Firebird";}
	elseif (strpos($agentl, 'opera ') !== FALSE and preg_match("#opera (\d+)#i",$agent,$ver))
		{$longagent = "Opera v$ver[1].X";}
	elseif (strpos($agentl, 'opera') !== FALSE and preg_match("#opera/(\d+)#i",$agent,$ver))
		{$longagent = "Opera v$ver[1].X";}
	elseif (strpos($agentl, "(playstation portable); ") !== FALSE and preg_match("#\\(PlayStation Portable\\); (\d)#i",$agent,$ver))
		{$longagent = "PlayStation Portable $ver[1].X";}
	else
		{$longagent = "Netscape v$verNN[1].X";}
	}
elseif (strpos($agentl, 'opera ') !== FALSE and preg_match("#opera (\d+)#i",$agent,$ver))
	{$longagent = "Opera v$ver[1].X";}
elseif (strpos($agentl, 'opera') !== FALSE and preg_match("#opera/(\d+)#i",$agent,$ver))
	{$longagent = "Opera v$ver[1].X";}
elseif (strpos($agentl, 'microsoft internet explorer/') !== FALSE and preg_match("#Microsoft Internet Explorer/(\d)#i",$agent,$ver))
	{$longagent = "MSIE v$ver[1].X";}
elseif (strpos($agentl, 'iweng/') !== FALSE and preg_match("#IWENG/(\d)#i",$agent,$ver))
	{$longagent = "AOL's Browser v$ver[1].X";}
elseif (strpos($agentl, 'aolbrowser/') !== FALSE and preg_match("#aolbrowser/(\d)#i",$agent,$ver))
	{$longagent = "AOL's Browser v$ver[1].X";}
elseif (strpos($agentl, 'lynx') !== FALSE)
	{$longagent = "Lynx";}
elseif (strpos($agentl, 'webexplorer') !== FALSE)
	{$longagent = "IBM WebExplorer";}
elseif (strpos($agentl, 'quarterdeck') !== FALSE and preg_match("#QuarterDeck#i",$agent,$ver))
	{$longagent = "QuarterDeck Mosaic";}
elseif (strpos($agentl, 'spry') !== FALSE)
	{$longagent = "Compuserve's SPRY Mosaic";}
elseif (strpos($agentl, 'enhanced_mosaic') !== FALSE)
	{$longagent = "NCSA Mosaic - Enhanced";}
elseif (strpos($agentl, 'mosaic') !== FALSE)
	{$longagent = "NCSA Mosaic";}
elseif (strpos($agentl, 'prodigy') !== FALSE)
	{$longagent = "Prodigy's Browser";}
elseif (strpos($agentl, 'konqueror/') !== FALSE and preg_match("#konqueror/(\d)#i",$agent,$ver))
	{$longagent = "Konqueror v$ver[1].X";}
elseif (strpos($agentl, 'konqueror') !== FALSE and preg_match("#konqueror (\d)#i",$agent,$ver))
	{$longagent = "Konqueror v$ver[1].X";}
elseif (strpos($agentl, 'internet ninja ') !== FALSE and preg_match("#Internet Ninja (\d)#i",$agent,$ver))
	{$longagent = "Internet Ninja v$ver[1].X";}
elseif (strpos($agentl, 'netbox/') !== FALSE and preg_match("#Netbox/(\d)#i",$agent,$ver))
	{$longagent = "Netbox v$ver[1].X";}
elseif (strpos($agentl, 'lotus-notes/') !== FALSE and preg_match("#Lotus-Notes/(\d)#i",$agent,$ver))
	{$longagent = "Lotus-Notes v$ver[1].X";}
elseif (strpos($agentl, 'php/') !== FALSE and preg_match("#PHP/(\d)#i",$agent,$ver))
	{$longagent = "PHP v$ver[1].X";}
elseif (strpos($agentl, 'wget/') !== FALSE and preg_match("#Wget/(\d)#i",$agent,$ver))
	{$longagent = "Wget v$ver[1].X";}
else
	{$longagent = "Andre browsere";}

return $longagent;
}

/**
 * Returnere styresystem p lselig form.
 * @param $agent den tekststreng styresystemet bliver identificeret p.
 */
function platform($agent)
{
	$agentl = strtolower($agent);
	if (strpos($agentl, "win95") !== FALSE)
		{$longplatform = "Windows 95";}
	elseif (strpos($agentl, "win 9x 4.9") !== FALSE)
		{$longplatform = "Windows ME";}
	elseif (strpos($agentl, "win98") !== FALSE)
		{$longplatform = "Windows 98";}
	elseif (strpos($agentl, "winnt") !== FALSE)
		{$longplatform = "Windows NT";}
	elseif (strpos($agentl, "win2000") !== FALSE)
		{$longplatform = "Windows 2000";}
	elseif (strpos($agentl, "windows me") !== FALSE)
		{$longplatform = "Windows ME";}
	elseif (strpos($agentl, "media center pc") !== FALSE and preg_match("#Wget/(\d)#i",$agent,$ver))
		{$longplatform = "Windows Media Center PC $ver[1].X";}
	elseif (strpos($agentl, "windows xp") !== FALSE or strpos($agentl, "windows nt 5.1") !== FALSE)
		{$longplatform = "Windows XP";}
	elseif (strpos($agentl, "windows vista") !== FALSE or strpos($agentl, "windows nt 5.2") !== FALSE)
		{$longplatform = "Windows Vista";}
	elseif (strpos($agentl, "windows 7") !== FALSE or strpos($agentl, "Windows nt 6.1") !== FALSE)
		{$longplatform = "Windows 7";}
	elseif (strpos($agentl, "windows 8") !== FALSE or strpos($agentl, "Windows nt 6.8") !== FALSE)
		{$longplatform = "Windows 8";}
	elseif (strpos($agentl, "winweb") !== FALSE)
		{$longplatform = "Windows 3.1/95 - 16 bit";}
	elseif (strpos($agentl, "windows 95") !== FALSE)
		{$longplatform = "Windows 95";}
	elseif (strpos($agentl, "windows 98") !== FALSE)
		{$longplatform = "Windows 98";}
	elseif (strpos($agentl, "indows nt 5.1") !== FALSE)
		{$longplatform = "Windows XP";}
	elseif (strpos($agentl, "windows nt 5") !== FALSE)
		{$longplatform = "Windows 2000";}
	elseif (strpos($agentl, "windows nt 6.1") !== FALSE)
		{$longplatform = "Windows 7";}
	elseif (strpos($agentl, "windows nt 6") !== FALSE)
		{$longplatform = "Windows Vista";}
	elseif (strpos($agentl, "windows nt") !== FALSE)
		{$longplatform = "Windows NT";}
	elseif (strpos($agentl, "windows 3.1") !== FALSE)
		{$longplatform = "Windows 3.1";}
	elseif (strpos($agentl, "win16") !== FALSE)
		{$longplatform = "Windows 3.1/95 - 16 bit";}
	elseif (strpos($agentl, "win32") !== FALSE)
		{$longplatform = "Windows 95/NT - 32 bit";}
	elseif (strpos($agentl, "windows") !== FALSE)
	{
		if (strpos($agentl, "32bit") !== FALSE)
			{$longplatform = "Windows 95/NT - 32 bit";}
		else
			{$longplatform = "Windows 3.1/95 - 16 bit";}
	}
	elseif (strpos($agentl, "window") !== FALSE)
		{$longplatform = "X Windows";}
	elseif (strpos($agentl, "mac") !== FALSE)
	{
		if (strpos($agentl, "mac os x") !== FALSE)
			{
				if (strpos($agentl, "intel mac os x") !== FALSE)
				{$longplatform = "Macintosh - Intel Mac OS X";}
				else if (strpos($agentl, "ppc mac os x") !== FALSE)
				 {$longplatform = "Macintosh - PowerPC Mac OS X";}
				else
				 {$longplatform = "Macintosh - OS X";}
			}
		elseif (strpos($agentl, "ppc") !== FALSE)
			{$longplatform = "Macintosh - PowerPC";}
		elseif (strpos($agentl, "powerpc") !== FALSE)
			{$longplatform = "Macintosh - PowerPC";}
		else
			{$longplatform = "Macintosh - 68K";}
	}
	elseif (strpos($agentl, "amiga") !== FALSE)
		{$longplatform = "Amiga";}
	elseif (strpos($agentl, "os/2") !== FALSE)
		{
			if (preg_match("#warp (\d+)#i",$agent,$ver))
				{$longplatform = "OS/2 Warp $ver[1].X";}
			else
				{ $longplatform = "OS-2";}
		}
	elseif (strpos($agentl, "os-2") !== FALSE)
		{$longplatform = "OS-2";}
	elseif (strpos($agentl, "x11") !== FALSE)
	{
		if (strpos($agentl, "hp-ux") !== FALSE)
			{$longplatform = "UNIX - HP-UX";}
		elseif (strpos($agentl, "linux") !== FALSE)
			{
				$distros = array("SuSE", "Ubuntu", "Fedora", "Debian", "Red Hat", "Mandriva", "Slackware", "Gentoo", "KNOPPIX", "MEPIS", "Kubuntu", "Freespire", "Linspire", "Xandros", "Arch", "Xubuntu", "KANOTIX", "Yellow Dog", "Edubuntu");
				$longplatform = "UNIX - Linux";
				foreach ($distros as $dist) {
					if (strpos($agentl, strtolower($dist)) !== FALSE) {
						$longplatform .= ", ".$dist;
						break;
					}
				}
			}
		elseif (strpos($agentl, "sunos") !== FALSE)
			{$longplatform = "UNIX - SunOS";}
		elseif (strpos($agentl, "irix") !== FALSE and preg_match("#IRIX (\d+)#i",$agent,$ver))
			{$longplatform = "UNIX - IRIX $ver[1].X";}
		else
			{$longplatform = "UNIX - Andre versioner";}
	}
	elseif (strpos($agentl, "iweng") !== FALSE)
		{$longplatform = "Windows 3.1 el. 95 - 16-bit";}
	elseif (strpos($agentl, "lynx") !== FALSE)
		{$longplatform = "UNIX - Andre versioner";}
	elseif (strpos($agentl, "webtv") !== FALSE)
		{$longplatform = "WebTV";}
	elseif (strpos($agentl, "(playstation portable); ") !== FALSE and preg_match("#\\(PlayStation Portable\\); (\d)#i",$agent,$ver))
		{$longplatform = "PlayStation Portable $ver[1].X";}
	elseif (strpos($agentl, "risc os") !== FALSE and preg_match("#RISC OS (\d+)#i",$agent,$ver))
		{$longplatform = "RISC OS $ver[1].X";}
	elseif (strpos($agentl, "beos") !== FALSE)
		{$longplatform = "BeOS";}
	elseif (strpos($agentl, "webtv") !== FALSE)
		{$longplatform = "WebTV";}
	elseif (strpos($agentl, "sonyericsson") !== FALSE)
	{
		if (preg_match("#SonyEricsson([\d[:alpha:]]+)/#i",$agent,$ver))
			{ $longplatform = "SonyEricsson$ver[1]";}
		else
			{ $longplatform = "SonyEricsson";}
	}
	elseif (strpos($agentl, "symbian os") !== FALSE or strpos($agentl, "symbianos") !== FALSE)
		{
			if (preg_match("#nokia (\d+)#i",$agent,$ver))
				{ $longplatform = "Symbian OS, Nokia $ver[1]";}
			elseif (preg_match("#symbianos/(\d+)#i",$agent,$ver))
				{ $longplatform = "Symbian OS $ver[1].X";}
			else 
				{ $longplatform = "Symbian OS";}
		}
	else
		{$longplatform = "Andre styresystemer";}

	return $longplatform;
}

function afkod($url)
{
	$keyStr = substr($url,strpos($url,"?")+1);

/*	$keyArray = explode("&",$keyStr);
	$keyValArray = array();
	foreach ($keyArray as $val) {
		$url = explode("=", $val);
		if (isset($url) and count($url) >= 2)
			$keyValArray[urldecode($url[0])] = urldecode($url[1]);
	}
	*/
	parse_str($keyStr, $keyValArray);
	return $keyValArray;
}

/**
 * Sets a (new) data source to use on this object and objects created by
 * this object.
 * 
 * @param $datasource the data source to use.
 * @returns void
 */
function setDataSource(&$datasource) {
	$this->datafil = &$datasource;

	//Old monthly/collective stats not used anymore - safe to delete
	//$this->mstat->setDataSource($datasource);
}

/**
 * Returns name of the search engine registered for the latest visit.
 *
 * @return name of the search engine registered for the latest visit.
 * @public
 */
function getLatestSearchEngine() {
	return $this->latestSearchEngine;
}
	
/**
 * Returns an array of the search words registered for the latest visit.
 *
 * @return an array of the search words registered for the latest visit.
 * @public
 */
function getLatestSearchWords() {
	//Make sure we always returns an array.
	if (! is_array($this->latestSearchWords)) {
		return array();
	}
	
	return $this->latestSearchWords;
}

/**
 * Returns the prefered language registered for the latest visit.
 *
 * @return the prefered language registered for the latest visit.
 * @public
 */
function getLatestPrefLanguage() {
	return $this->latestPrefLanguage;
}

/**
 * Return the top domain registered for the latest visit.
 *
 * @return the top domain registered for the latest visit.
 * @public
 */
function getLatestTopdom() {
	return $this->latestTopdom;
}

/** Applies the page counter to the array of urls and visits. The indecies of
 *  the arrays must match each other. The arrays, with the counters applied,
 *  will be returned.
 *
 *  The functionallity is pulled into a function to ease testing.
 *  The easiest way the call this function is:
 *  @code
 *  list($cntUrls, $cntVisits) = $this->applyPageCounter($cntUrls, $cntVisits, $max_counters);
 *  @endcode
 *
 *  The function only works if @c $this->url is set, but @c $this->counterNo
 *  and @c $this->counterName must be set if they are known.
 *
 *  @param $cntUrls   array of urls/counter names.
 *  @param $cntVisits array of visits. The indecies must match the ones in
 *                    the array of urls.
 *  @param $max_counters the largest amount of counters that may exist.
 *                    If another counter is required, but ther is not room for
 *                    one, counter no 0 will be counted up.
 *  @return the arrays with the page counter applied.
 *  @public
 */
function applyPageCounter($cntUrls, $cntVisits, $max_counters) {
	$url = $this->url;
	
	//Cut off the search part?
	if ($this->counterIgnoreQuery === true and strpos($url, '?') !== false) {
		$url = substr($url, 0, strpos($url, '?'));
	}
	
	//Both the old (counter name/numbers) and new scheme (urls) must work:
	//1.  If a counter number is given and a name/url exists for it: Use it.
	//1.1.  If a counter name is found, and it matches the url, replace the
	//      name with the url.
	//2.  If a counter name is given, search for that name.
	//2.1.   If a matching url is found: Use it.
	//2.1.2.   If a counter name is found, and it matches the url, replace 
	//         the name with the url.
	//2.2.     If not found: Ignore the name, continue:
	//
	//  At this place we have the url of the site.
	//3.  Find a registered url that matches the url of the site.
	//3.1.  If found: Use it.
	//3.2.  If not found: Try to find a counter that matches the url.
	//      (In 2. we had a counter name and wanted an url. Here we have an 
	//       url and wants a counter name.)
	//3.2.1.  If found: Use it and rename the counter to match the url.
	//3.2.2.  If not found: Make a new counter with that url.

	$urlCounted = false; //Has the URL been counted.
	
	//1.	If a counter number is given and a name/url exists for it: Use it.
	if (strlen($this->counterNo) > 0 and isset($cntUrls[$this->counterNo])) {
		//The number exists: Use it.
		$cntVisits = Html::addZeros(sizeof($cntUrls), $cntVisits);
		$cntVisits[$this->counterNo]++;
		$urlCounted = true;
		
		//1.1.  If a counter name is found, and it matches the url, replace the
		//      name with the url.
		$urlName = Html::taelnummer_url2name($url);
		if ($urlName === $cntUrls[$this->counterNo]) {
			//If the counter is registered by an url and not by a name, the
			//url will technically be replaced by the same url, so all in all
			//no harm is done and we don't need to take special care to not do
			//any replacement on urls.
			$cntUrls[$this->counterNo] = $url;
		}
	}
	
	//2.  If a counter name is given, search for that name.
	if (!$urlCounted and strlen($this->counterName) > 0) {
		for ($i = 0; $i < count($cntUrls); $i++) {
			if ($this->counterName === Html::taelnummer_url2name($cntUrls[$i])) {
				//2.1.   If a matching url is found: Use it.
				$cntVisits = Html::addZeros(sizeof($cntUrls), $cntVisits);
				$cntVisits[$i]++;
				$urlCounted = true;
				//2.1.2.   If a counter name is found, and it matches the url,
				//         replace the name with the url.
				$urlName = Html::taelnummer_url2name($url);
				if ($urlName === $cntUrls[$i]) {
					//If the counter is registered by an url and not by a name, the
					//url will technically be replaced by the same url, so all in all
					//no harm is done and we don't need to take special care to not do
					//any replacement on urls.
					$cntUrls[$i] = $url;
				}
				break;
			}
		} //End for
		//2.2.     If not found: Ignore the name, continue:
	}
	
	//  At this place we have the url of the site.
	if (!$urlCounted) {
		//3.  Find a registered url that matches the url of the site.
		$urlComparator = new UrlComparator();
		for ($i = 0; $i < count($cntUrls); $i++) {
			if ($urlComparator->equals($url, $cntUrls[$i])) {
				//3.1.  If found: Use it.
				$cntVisits = Html::addZeros(sizeof($cntUrls), $cntVisits);
				$cntVisits[$i]++;
				$urlCounted = true;
				break;
			}
		} //End for
	}
		
	//3.2.  If not found: Try to find a counter that matches the url.
	//      (In 2. we had a counter name and wanted an url. Here we have an url
	//       and wants a counter name.)
	if (!$urlCounted) {
		$urlCounterName = Html::taelnummer_url2name($url);
		for ($i = 0; $i < count($cntUrls); $i++) {
			if ($cntUrls[$i] === $urlCounterName) {
				//3.2.1.  If found: Use it and rename the counter to match the url.
				$cntVisits = Html::addZeros(sizeof($cntUrls), $cntVisits);
				$cntVisits[$i]++;
				$cntUrls[$i] = $url;
				$urlCounted = true;
				break;
			}
		} //End for
	}
	
	//3.2.2.  If not found: Make a new counter with that url.
	if (!$urlCounted) {
		//Count the number of actual urls (not just empty places).
		//Don't count no 0.
		$urlCount = 0;
		
		//Find the empty counter with the lowest number. -1 if none found.
		$lowestEmpty = -1;
		for ($i = 1; $i < count($cntUrls) or $i < $max_counters; $i++) {
			if (isset($cntUrls[$i]) and strlen($cntUrls[$i]) > 0) {
				$urlCount++;
			} else if ($lowestEmpty < 0) {
				//This is the lowest empty.
				$lowestEmpty = $i;
			}
		}
	
		//Only create a counter if there is room for one. Else use counter 0.
		if ($urlCount < $max_counters and $lowestEmpty > 0) {
			$newCounterNo = $lowestEmpty;
			$cntUrls[$newCounterNo] = $url;
			$cntVisits[$newCounterNo] = 1; //First visit on this one.
		} else {
			$cntVisits[0]++;
		}
		$urlCounted = true;
	}
	
	return array($cntUrls, $cntVisits);
} //End function applyPageCounter

	/**
	 * Sets if the query string of the url (from ? and the rest) should be
	 * cut off when dealing with the counters. @c true for yes, c false for
	 * no.
	 *
	 * @param $ignoreQuery if the query string of the url should be cut off
	 *                      when dealing with the counters.
	 * @public
	 */
	 function setCounterIgnoreQuery($ignoreQuery) {
		 $this->counterIgnoreQuery = $ignoreQuery;
	 }


} /*End of class ZipStatEngine*/

?>
