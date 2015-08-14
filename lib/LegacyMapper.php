<?php

/**
 * Maps old parameters to newer ones.
 */
class LegacyMapper {

	/**
	 * The instance of the site context.
	 *
	 * @private
	 */
	var $siteContext;
	
	/**
	 * If &gt;= 0 the version of ZIP Stat the legasy mapper has guessed or
	 * have been ordered to emulate. The guess is done pased onthe http
	 * parameters.
	 *
	 * @private
	 */
	var $zipstatver = -1;
	
	/**
	 * Creates a new instance.
	 * 
	 * @public
	 */
	function LegacyMapper() {
	}
	
	/**
	 * Guesses the version of ZIP Stat to emulate based on the http
	 * parameters, and stores the result in the private variable
	 * <code>zipstatver</code>.
	 * Currently the following versions are detected:
	 * &quot;1&quot; and "quot;2.0&quot;. 
	 *
	 * This method requres an instance of the site context set using
	 * <code>setSiteContext</code>.
	 *
	 * @private
	 */
	function guessVersion() {
		if ($this->siteContext->getHttpVar('version') == "1") {
			$this->zipstatver = "1";
		} else {
			$this->zipstatver = "2.0";
		}
	}
	
	/**
	 * Sets the instance of the site context and passes the instance of this
	 * legasy mapper on to that site context instance.
	 * The site context is required by some functions, which is documented in
	 * their documentation.
	 * 
	 * @param $siteContext the instance of the site context.
	 */
	function setSiteContext(&$siteContext) {
		$this->siteContext = &$siteContext;
		$this->siteContext->setLegasyMapper($this);

		//Guess the ZIP Stat version
		$this->guessVersion();
	}
	
	/**
	 * Translates the $httpPars from the old to the new format.
	 * This method must be overwritten.
	 * The old values will not be deleted.
	 * 
	 * @public
	 * @param $httpPars the ones to translate.
	 * @returns String[]
	 * @return the translated params.
	 */
	function applyMapping($httpPars) {
		trigger_error("The method LegacyMapper::applyMapping must be overwritten, which it is not.", E_ERROR);
	}
	
	/**
	 * Maps the $currentName to the version which the user should expect to
	 * get, based on the input given to the legasy mapper.
	 * This function requires that the site context has been set using
	 * <code>setSiteContext</code>.
	 * 
	 * @param $currentName the name to map.
	 */
	function mapElementName($currentName) {
				trigger_error("The method LegacyMapper::mapElementName must be overwritten, which it is not.", E_ERROR);
	}
	
	/**
	 * Maps the keys as decribed in the $mapping.
	 * The $mapping is an associative array, where the keys correspond to
	 * the old key value, and the values correspond to the new key values.
	 * 
	 * @protected
	 * @param $mapping describes how to map
	 * @param $pars    the parameters to map
	 * @returns String[]
	 * @return the mapped parameters.
	 */
	function mapKeys($mapping, $pars) {
		//Find out which pars to map
		foreach ($pars as $key => $val) {
			if (array_key_exists($key, $mapping)) {
				//Map this one
				$pars[$mapping[$key]] = $val;
			}
		} //End foreach
		return $pars;
	}

}

/**
 * Maps keys for the stat site.
 * 
 * 
 */
class StatSiteLegacyMapper extends LegacyMapper {

	/**
	 * Translates the $httpPars from the old to the new format.
	 * This method must be overwritten.
	 * 
	 * @public
	 * @param $httpPars the ones to translate.
	 * @returns String[]
	 * @return the translated params.
	 */
	function applyMapping($httpPars) {
		//old => new
		$keyMap = array(
			'brugernavn' => 'username',
			'kodeord' => 'password',
			'brugerkodeord' => 'userpassword',
			'tabelBrede' => 'tableWidth',
			'statGemICookie' => 'statSelSaveCookie'	//Not implemented in new version
		);

		$httpPars = $this->mapKeys($keyMap, $httpPars);

		if (isset($httpPars['menu']) and $httpPars['menu'] === 'skjul') {
			$httpPars['menu'] = 'hide';
		}
		
		if (isset($httpPars['type'])) {
			if ($httpPars['type'] === 'tekst')
				$httpPars['type'] = 'text';
			else if ($httpPars['type'] === 'export')
				$httpPars['type'] = 'csv';
			else if ($httpPars['type'] === 'grafisk')
				$httpPars['type'] = 'html';
		}
		
		/*todo:
			ikkedomaene - located in ZipStatEngine.php
			*/
			//old name => new name
		$showMap = array(
			'alle' => 'all',
			'enkeltstat' => 'BasicStats',
			'prognoser' => 'Projection',
			'maaned_i_aar' => 'MonthStats',
			'sidste_31_dage' => 'HitsDay',
			'timer_hits' => 'HitsHour',
			'ugedag_hits' => 'HitsWeek',
			'top_domain' => 'HitsTopdomain',
			'domaene_hits' => 'HitsDomain',
			'hits_browser' => 'HitsBrowser',
			'hits_os' => 'HitsOs',
			'hits_sprog' => 'HitsLanguage',
			'hits_opl' => 'HitsResolution',
			'hits_farver' => 'HitsColor',
			'java_support' => 'HitsJava',
			'taellere' => 'HitsCounter',
			'js' => 'HitsJavaScript',
			'ref' => 'HitsReferer',
			'zipklik' => 'HitsClickCounter',
			'sord' => 'HitsSearchWord',
			'smask' => 'HitsSearchEngines',
			'spoergs' => 'HitsVotes',
			'info20' => 'HitsLatestsVisits',
			'bev' => 'HitsMovements',
			'indgang' => 'HitsEntryUrls',
			'udgang' => 'HitsExitUrls'
		);
		
		$show = array();
		foreach ($httpPars as $key => $val) {
			if (isset($showMap[$key])) {
				//Map this one
				$show[] = $showMap[$key];
			}
		} //End foreach
		if (count($show) > 0)
			$httpPars["show"] = $show;
		
		return $httpPars;
	}

	/**
	 * Maps the $currentName to the version which the user should expect to
	 * get, based on the input given to the legasy mapper.
	 * This function requires that the site context has been set using
	 * <code>setSiteContext</code>.
	 * 
	 * @param $currentName the name to map.
	 */
	function mapElementName($currentName) {
//		echo "map: '$this->zipstatver'";
		if ($this->zipstatver !== "1")
			return $currentName;
		
		//Map to version 1

		//New name => old name
		$showMap = array(
			'BasicStats' => 'enkeltstat',
			'MonthStats' => 'prDagMaaned',
			'HitsDay' => 'prDagMaaned',
			'HitsHour' => 'prTime',
			'HitsWeek' => 'prUgedag',
			'HitsTopdomain' => 'topDomaener',
			'HitsDomain' => 'domaener',
			'HitsBrowser' => 'browsere',
			'HitsOs' => 'styresystemer',
			'HitsLanguage' => 'sprog',
			'HitsResolution' => 'oploesning',
			'HitsColor' => 'antalFarver',
			'HitsJava' => 'javaSupport',
			'HitsCounter' => 'taellere',
			'HitsJavaScript' => 'jsSupport',
			'HitsClickCounter' => 'klikTaellere',
			'HitsSearchWord' => 'sOrd',
			'HitsSearchEngines' => 'sMask',
			'HitsLatestsVisits' => 'senesteHits',
			'HitsMovements' => 'bevaegelser',
			'HitsEntryUrls' => 'indgangssider',
			'HitsExitUrls' => 'udgangssider'
		);

		if (isset($showMap[$currentName])) {
			return $showMap[$currentName];
		} else {
			return $currentName;
		}
	}

}

?>