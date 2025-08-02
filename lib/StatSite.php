<?php

/**
 * Represents a stat site.
 *
 * @public
 * @author Simon Mikkelsen
 */
class StatSite
{
	/**
	 * The object which generates format specific code, e.g. HTML, text etc.
	 * The object must extend {@link SiteGenerator}.
	 *
	 * WARNING: It looks like this one is not used and $this->siteGenerator
	 *          should be used instead. Todo: Look into this.
	 *
	 * @private
	 * @since 0.0.1
	 * @see SiteGenerator
	 */
	var $generator;

	/**
	 * An instance of {@link SiteContext}, which contains
	 * values for this session.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteContext;
	
	/**
	 * Array of stat identifiers, which states what to show. If the array is
	 * not set or empty its values will be taken from the http parameter
	 * &quot;show&quot;.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $show;

	/**
	 * Array of stat identifiers, which states what to hide. If the array is
	 * not set or empty its values will be taken from the http parameter
	 * &quot;hide&quot;.
	 * Elements in this array will always take presedence over the once in
	 * the <code>$show</code> array.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $hide;
	
	/**
	 * States if the stat selector should be shown (1) or not (0).
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $showStatselector = 1;
	
	/**
	 * States if a bandwidth limitation should be ignored (true) or not (false).
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $sendByMail = false;
	
	/**
	 * The maximum number of stats to show, if &lt; 0 unlimited.
	 *
	 * @private
	 * @since 2.1.0
	 */
	var $maxStatsToShow = -1;

	/**
	 * Instantiates the class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext the courent {@link SiteContext} object.
	 * @param $generatortype text string which identifies the type of
	 *         generator to be used.
	 */
	function __construct(&$siteContext,$generatorType)
	{
		$this->site = "";
		if (strtolower(get_class($siteContext)) == 'sitecontext') {
			$this->siteContext = &$siteContext;
		} else {
			echo "<b>Error 283:</b> Param <code>\$siteContext</code> to contrusctor <code>StatSite()</code> must be an instance of the class <code>SiteContext</code>.";
			echo Debug::stacktrace();
			exit;
		}

		require_once dirname(__FILE__)."/SiteGenerator/SiteGenerator.php";
		require_once dirname(__FILE__)."/StatGenerator.php";
		$this->siteGenerator = SiteGenerator::getGenerator($generatorType,$siteContext);
		
		//Set status for low bandwidth
		if ($this->siteContext->getOption('low_bandwidth') === true) {
			$this->setMaxStatsToShow($this->siteContext->getOption('low_bandwidth_max_stats'));
		} else {
			$this->setMaxStatsToShow(-1);
		}
		
	}

	/**
	 * Generates the site.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code for the site.
	 */
	function generateSite($statReq = "")
	{
		$sg = &$this->siteGenerator;
		$locale = &$this->siteContext->getLocalizer();
		$lib = &$this->siteContext->getCodeLib();
		$data = &$lib->getDataSource();

		//Adds the main title
		if ($lib->pro() === 1 && strlen($data->getLine(59)) > 0)
		{
			$headline = $data->getLine(59);
			$headline = strip_tags($headline);
		}
		else
			$headline = sprintf($locale->getLocale('statSiteFor'), $data->getLine(4));

		//Array of stats which uses the GraphStatGenerator
		$graphStats = array();
		
		$mainTitle = $sg->newElement("headline");
		$mainTitle->setHeadline($headline);
		$mainTitle->setSize(1);
		$sg->addElement($mainTitle);

		//If low bandwidth mode: Show a message
		if ($this->maxStatsToShow >= 0) {
			$lowBwText = $sg->newElement("text");
			$lowBwText->setText(sprintf($locale->getLocale('lowBandwidthStatsite'),
			          $this->siteContext->getOption('low_bandwidth_max_stats')));
			$lowBwText->setParagraph(1);
			$sg->addElement($lowBwText);
		}
		
		//Add all the stats to this
		$statSel = new StatSelector($this->siteContext, $sg);
		$statSel->setMaxStatsToShow($this->getMaxStatsToShow());
		$statSel->setPassThroughParams(array('username', 'brugernavn', 'brugerkodeord', 'kodeord', 'password'));


		//Make the form wrapper
		$formWrapper = $sg->newElement('formwrapper');
		$formWrapper->setSubmitUrl($this->siteContext->getOption('urlStatsite'));
		$formWrapper->setMethod('GET');
		$formWrapper->addPassThroughParams('brugernavn');
		$formWrapper->addPassThroughParams('brugerkodeord');
		$formWrapper->addPassThroughParams('kodeord');
		$statSel->setFormWrapper($formWrapper);
		
		$basicStats = new BasicStatsGenerator($this->siteContext, $sg);
		$statSel->addStatKey($basicStats, 0);

		$projection = new Projection($this->siteContext, $sg);
		$statSel->addStatKey($projection, 0);

		$monthStats = new MonthStats($this->siteContext, $sg);
		$statSel->addStatKey($monthStats, 0);
		$graphStats[] =& $monthStats;

		$hitsDay = new HitsDay($this->siteContext, $sg);
		$statSel->addStatKey($hitsDay, 0);
		$graphStats[] =& $hitsDay;

		$hitsHour = new HitsHour($this->siteContext, $sg);
		$statSel->addStatKey($hitsHour, 0);
		$graphStats[] =& $hitsHour;

		$hitsWeek = new HitsWeek($this->siteContext, $sg);
		$statSel->addStatKey($hitsWeek, 0);
		$graphStats[] =& $hitsWeek;

		$hitsTopdomain = new HitsTopdomain($this->siteContext, $sg);
		$statSel->addStatKey($hitsTopdomain, 0);
		$graphStats[] =& $hitsTopdomain;

		$hitsDomain = new HitsDomain($this->siteContext, $sg);
		$statSel->addStatKey($hitsDomain, 0);
		$graphStats[] =& $hitsDomain;

		$hitsBrowser = new HitsBrowser($this->siteContext, $sg);
		$statSel->addStatKey($hitsBrowser, 0);
		$graphStats[] =& $hitsBrowser;

		$hitsBrowserMaker = new HitsBrowserMaker($this->siteContext, $sg);
		$statSel->addStatKey($hitsBrowserMaker, 0);
		$graphStats[] =& $hitsBrowserMaker;

		$hitsOs = new HitsOs($this->siteContext, $sg);
		$statSel->addStatKey($hitsOs, 0);
		$graphStats[] =& $hitsOs;

		$hitsOsMaker = new HitsOsMaker($this->siteContext, $sg);
		$statSel->addStatKey($hitsOsMaker, 0);
		$graphStats[] =& $hitsOsMaker;

		$hitsLanguage = new HitsLanguage($this->siteContext, $sg);
		$statSel->addStatKey($hitsLanguage, 0);
		$graphStats[] =& $hitsLanguage;

		$hitsResolution = new HitsResolution($this->siteContext, $sg);
		$statSel->addStatKey($hitsResolution, 0);
		$graphStats[] =& $hitsResolution;

		$hitsColor = new HitsColor($this->siteContext, $sg);
		$statSel->addStatKey($hitsColor, 0);
		$graphStats[] =& $hitsColor;

		$hitsJava = new HitsJava($this->siteContext, $sg);
		$statSel->addStatKey($hitsJava, 0);
		$graphStats[] =& $hitsJava;

		$hitsJavaScript = new HitsJavaScript($this->siteContext, $sg);
		$statSel->addStatKey($hitsJavaScript, 0);
		$graphStats[] =& $hitsJavaScript;

		$hitsCounter = new HitsCounter($this->siteContext, $sg);
		$statSel->addStatKey($hitsCounter, 0);
		$graphStats[] =& $hitsCounter;

		$hitsReferer = new HitsReferer($this->siteContext, $sg);
		$statSel->addStatKey($hitsReferer, 0);

		$hitsEntryUrls = new HitsEntryUrls($this->siteContext, $sg);
		$statSel->addStatKey($hitsEntryUrls, 0);
		$graphStats[] =& $hitsEntryUrls;

		$hitsExitUrls = new HitsExitUrls($this->siteContext, $sg);
		$statSel->addStatKey($hitsExitUrls, 0);
		$graphStats[] =& $hitsExitUrls;

		$hitsMovements = new HitsMovements($this->siteContext, $sg);
		$statSel->addStatKey($hitsMovements, 0);
		$graphStats[] =& $hitsMovements;

		$hitsClickCounter = new HitsClickCounter($this->siteContext, $sg);
		$statSel->addStatKey($hitsClickCounter, 0);
		$graphStats[] =& $hitsClickCounter;

		$hitsSearchWord = new HitsSearchWord($this->siteContext, $sg);
		$statSel->addStatKey($hitsSearchWord, 0);
		$graphStats[] =& $hitsSearchWord;

		$hitsSearchEngines = new HitsSearchEngines($this->siteContext, $sg);
		$statSel->addStatKey($hitsSearchEngines, 0);
		$graphStats[] =& $hitsSearchEngines;

		$hitsVotes = new HitsVotes($this->siteContext, $sg);
		$statSel->addStatKey($hitsVotes, 0);

		$hitsLatestsVisits = new HitsLatestsVisits($this->siteContext, $sg);
		$statSel->addStatKey($hitsLatestsVisits, 0);
		
		//Set the width of the tables where possible
		$tableWidth = $this->siteContext->getHttpVar('tableWidth');
		if (isset($tableWidth) and strlen($tableWidth) > 0) {
			//Is the last char a %?
			if (strpos($tableWidth, '%') === strlen($tableWidth)-1) {
				//Convert 100% to 1.0
				$tableWidth = substr($tableWidth, 0, -1)/100;
			}
			for ($i = 0; $i < count($graphStats); $i++) {
				$graphStats[$i]->setTableWith($tableWidth);
			}
		}
		
		//Add the selected stats to the site generator
/*		$statSelSize = $statSel->getSize();
		//$allStats = array();
		for ($i = 0; $i < $statSelSize; $i++)
		{
			$statElement = $statSel->getStat($i);
			//$allStats[$statElement->getIdentifier()] = &$StatElement;
			//if ($statSel->isSelected($i))
			//	$sg->addElement($statSel->getStat($i));
		}
*/		
		//Get the show array
		if (! isset($this->show) or ! is_array($this->show) or count($this->show) === 0) {
			$show = $this->siteContext->getHttpVar("show");
			if (! is_array($show))
				$show = array('all');
		} else {
			$show = $this->show;
		}

		//Sets which stats are selected
		foreach ($show as $val) {
			$statSel->setSelected($val, 1);
		}
		
		//Get the hide array
		if (! isset($this->hide) or ! is_array($this->hide) or count($this->hide) === 0) {
			$hide = $this->siteContext->getHttpVar("hide");
			if (! is_array($hide))
				$hide = array();
		} else {
			$hide = $this->hide;
		}
		
		//Sets which stats are selected
		foreach ($hide as $val) {
			$statSel->setSelected($val, 0);
		}

		//Adds the selected stats for showing
		$selStats = $statSel->getSelectedStats();
		for ($i = 0; $i < count($selStats); $i++) {
			$sg->addElement($selStats[$i]);
		}

		//Add the stat selector itself
		if ($this->getShowStatselector() and $sg->isMediaInteractive()) {
			$sg->addElement($statSel);
		}

		//If send my mail
		if ($this->sendByMail === true) {
			//Tell people why they get an e-mail
			$whyGettingText = $sg->newElement('text');
			$whyGettingText->setText(sprintf($locale->getLocale("statsiteWhyGettingMail"),
			                           $data->getUsername(), trim($data->getLine(6))));

			$whyGettingText->setParagraph(1);
			$sg->addElement($whyGettingText);
		}

		return $sg->getSite();
	}

	/**
	 * Output all the needed headers.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function outputHeaders()
	{
		$headers = $this->getHeaders();
		foreach ($headers as $val) {
			header($val);
		}
	}

	/**
	 * Returns a <code>String[]</code> containing the headers for the type
	 * of view used.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Strng[] the headers for this type of view.
	 */
	function getHeaders()
	{
		return $this->siteGenerator->getHeaders();
	}
	
	/**
	 * Sets that the stat identified by the given <code>$identifier</code>
	 * shall be shown.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $identifier identifies the stat to show.
	 */
	function setShow($identifier) {
		if (! in_array($identifier, $this->show)) {
			$this->show[] = $identifier;
		}
	}
	
	/**
	 * Sets that the stat identified by the given <code>$identifier</code>
	 * shall not be shown.
	 * This method always takes presedence over {@link setShow}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $identifier identifies the stat to hide.
	 */
	function setHide($identifier) {
		if (! in_array($identifier, $this->hide)) {
			$this->hide[] = $identifier;
		}
	}

	/**
	 * Returns if the stat selector should be shown (1) or not (0).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @returns boolean
	 * @return if the stat selector should be shown (1) or not (0).
	 */
	function getShowStatselector() {
		return $this->showStatselector;
	}
	
	/**
	 * Sets if the stat selector should be shown (1) or not (0).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $showStatselector if the stat selector should be shown (1)
	 *                          or not (0).
	 */
	function setShowStatselector($showStatselector) {
		$this->showStatselector = $showStatselector;
	}

	/**
	 * Returns if the stat is to be send by e-mail (true) or not (false).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @returns boolean
	 * @return if the stat is to be send by e-mail (true) or not (false).
	 */
	function getSendByMail() {
		return $this->sendByMail;
	}
	
	/**
	 * Sets if the stat is to be send by e-mail (true) or not (false).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $sendByMail is to be send by e-mail (true) or not (false).
	 */
	function setSendByMail($sendByMail) {
		$this->sendByMail = $sendByMail;
	}
	
	/**
	 * Returns the maximum number of stats to show, if &lt; 0 unlimited.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @returns int
	 * @return the maximum number of stats to show, if &lt; 0 unlimited.
	 */
	function getMaxStatsToShow() {
		return $this->maxStatsToShow;
	}
	
	/**
	 * Sets the maximum number of stats to show, if &lt; 0 unlimited.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $maxStatsToShow the maximum number of stats to show, if
	 *                        &lt; 0 unlimited.
	 */
	function setMaxStatsToShow($maxStatsToShow) {
		$this->maxStatsToShow = $maxStatsToShow;
	}
	
	/** Returns a page which states that the content is beeing generated.
	 *
	 *  @return a page which states that the content is beeing generated.
	 */
	function getGeneratingPage() {
		$sg = &$this->siteGenerator;
		$sg->setRefresh(5, ""); //Refresh the page in 5 seconds.
		$locale = &$this->siteContext->getLocalizer();
		$lib = &$this->siteContext->getCodeLib();

		//Don't let the browser cache this page.
		Html::outputNoCacheHeaders();

		//Adds the main title
		$headline = $locale->getLocale('collGeneratingHeadline');
		$mainTitle = $sg->newElement("headline");
		$mainTitle->setHeadline($headline);
		$mainTitle->setSize(1);
		$sg->addElement($mainTitle);
		
		//Tell the time span
		$timeSpanText = $sg->newElement("text");
		$timeSpanText->setText($locale->getLocale('collGeneratingText'));
		$timeSpanText->setParagraph(1);
		$sg->addElement($timeSpanText);
		return $sg->getSite();
	}
	
} //End of class StatSite


/**
 * Represents a site showing collective stats.
 *
 * @public
 * @author Simon Mikkelsen
 */
class CollectiveStatSite extends StatSite
{
	/**
	 * Instantiates the class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext the courent {@link SiteContext} object.
	 * @param $generatortype text string which identifies the type of
	 *         generator to be used.
	 */
	function __construct(&$siteContext, $generatorType)
	{
		parent::__construct($siteContext,$generatorType);
	}

	/**
	 * Generates the site.
	 *
	 * @public
	 * @param $statReq the request which states what data to show.
	 *                 Must be an instance of CollectiveStatRequest.
	 * @return String the code for the site.
	 */
	function generateSite($statReq = "")
	{
		$sg = &$this->siteGenerator;
		$locale = &$this->siteContext->getLocalizer();
		$lib = &$this->siteContext->getCodeLib();
		$options = &$lib->getStier();
		$datasource = DataSource::createCollectiveReader($options);
		$showNumbers = false;
		$minPercent = 0.001;
		$minPercentsSpecial = 0.0001;

		//Adds the main title
		$headline = $locale->getLocale('collHeadline');
		$mainTitle = $sg->newElement("headline");
		$mainTitle->setHeadline($headline);
		$mainTitle->setSize(1);
		$sg->addElement($mainTitle);
		
		//Tell the time span (or single day).
		$timeSpanText = $sg->newElement("text");
		$dateFormat = $locale->getLocale('dateDate');
		$startDate = date($dateFormat, $statReq->getStartDay());
		$endDate = date($dateFormat, $statReq->getEndDay());
		if ($startDate != $endDate) {
			$timeSpanText->setText(sprintf($locale->getLocale('collTimespan'),
							$startDate, $endDate));
		} else {
			$timeSpanText->setText(sprintf($locale->getLocale('collDay'),
							$startDate));
		}
		$timeSpanText->setParagraph(1);
		$sg->addElement($timeSpanText);

		//Array of stats which uses the GraphStatGenerator
		$graphStats = array();

//Disable low bandwidth for this one		
		//If low bandwidth mode: Show a message
/*		if ($this->maxStatsToShow >= 0) {
			$lowBwText = $sg->newElement("text");
			$lowBwText->setText(sprintf($locale->getLocale('lowBandwidthStatsite'),
			          $this->siteContext->getOption('low_bandwidth_max_stats')));
			$lowBwText->setParagraph(1);
			$sg->addElement($lowBwText);
		}
*/		
		//Add all the stats to this
		$statSel = new StatSelector($this->siteContext, $sg);

		//Make the form wrapper
		$formWrapper = $sg->newElement('formwrapper');
		$formWrapper->setSubmitUrl($options->getOption('urlTotal'));
		$formWrapper->setMethod('GET');
		$formWrapper->addPassThroughParams('day');
		$formWrapper->addPassThroughParams('type');
		$statSel->setFormWrapper($formWrapper);
		$statSel->setPassThroughParams(array('day', 'type',));
		
		//Disable low bandwidth for this one		
//		$statSel->setMaxStatsToShow($this->getMaxStatsToShow());

/*		$projection = new Projection($this->siteContext, $sg);
		$statSel->addStatKey($projection, 0);
*/

/*		$monthStats = new MonthStats($this->siteContext, $sg);
		$statSel->addStatKey($monthStats, 0);
		$graphStats[] =& $monthStats;
*/
		$nullDummy = NULL;
		//$lib = $this->siteContext->getCodeLib();
		$lib->setDatafil($nullDummy);
		$hitsDay = new HitsDay($this->siteContext, $sg);
		$statSel->addStatKey($hitsDay, 0);
		$statReq->setStat('day');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		//Fix the dates
		$currentYear = date('Y');
		for ($i = 0; $i < count($txts); $i++) {
			//The dates we get are:
			//2005090414
			//YYYYMMDDHH
			$year = substr($txts[$i], 0, 4);
			$month = substr($txts[$i], 4, 2);
			$day = substr($txts[$i], 6, 2);
			$hour = substr($txts[$i], 8, 2);
			$txts[$i] = "$day-$month";
			if ($year != $currentYear) {
				$txts[$i] .= "-$year";
			}
			
			if (strlen($hour) > 0) {
				$txts[$i] .= " ".$hour.":00";
			}
		}
		
		$hitsDay->setNumberArray($vals);
		$hitsDay->setTextArray($txts);
		$hitsDay->setShowNumbers($showNumbers);
		$hitsDay->setMinPercent(0); //Always show everything.
		$graphStats[] =& $hitsDay;

/*		$hitsHour = new HitsHour($this->siteContext, $sg);
		$statSel->addStatKey($hitsHour, 0);
		$graphStats[] =& $hitsHour;
*/

/*		$hitsWeek = new HitsWeek($this->siteContext, $sg);
		$statSel->addStatKey($hitsWeek, 0);
		$graphStats[] =& $hitsWeek;
*/
		$hitsTopdomain = new HitsTopdomain($this->siteContext, $sg);
		$statSel->addStatKey($hitsTopdomain, 0);
		$statReq->setStat('topdom');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsTopdomain->setNumberArray($vals);
		$hitsTopdomain->setTextArray($txts);
		$hitsTopdomain->setShowNumbers($showNumbers);
		$hitsTopdomain->setMinPercent($minPercent);
		$graphStats[] =& $hitsTopdomain;

		$hitsBrowser = new HitsBrowser($this->siteContext, $sg);
		$statSel->addStatKey($hitsBrowser, 0);
		$statReq->setStat('browser');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsBrowser->setNumberArray($vals);
		$hitsBrowser->setTextArray($txts);
		$hitsBrowser->setShowNumbers($showNumbers);
		$hitsBrowser->setMinPercent($minPercentsSpecial);
		$graphStats[] =& $hitsBrowser;

		$seriesBrowser = new SeriesBrowser($this->siteContext, $sg);
		$statSel->addStatKey($seriesBrowser, 0);
		$statReq->setStat('browser');
		$statReq->setGroupby('day');
		list($txts, $vals, $times) = $datasource->getStatArrays($statReq);
		$seriesBrowser->setRawDataSeries($txts, $vals, $times);
		$seriesBrowser->setMinPercent($minPercentsSpecial);
		$graphStats[] =& $seriesBrowser;
		$statReq->setGroupby('total');

		$hitsBrowserMaker = new HitsBrowserMaker($this->siteContext, $sg);
		$statSel->addStatKey($hitsBrowserMaker, 0);
		$statReq->setStat('browser');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsBrowserMaker->setNumberArray($vals);
		$hitsBrowserMaker->setTextArray($txts);
		$hitsBrowserMaker->setShowNumbers($showNumbers);
		$hitsBrowserMaker->setMinPercent($minPercentsSpecial);
		$graphStats[] =& $hitsBrowserMaker;

		$hitsOs = new HitsOs($this->siteContext, $sg);
		$statSel->addStatKey($hitsOs, 0);
		$statReq->setStat('os');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsOs->setNumberArray($vals);
		$hitsOs->setTextArray($txts);
		$hitsOs->setShowNumbers($showNumbers);
		$hitsOs->setMinPercent($minPercentsSpecial);
		$graphStats[] =& $hitsOs;

		$hitsOsMaker = new HitsOsMaker($this->siteContext, $sg);
		$statSel->addStatKey($hitsOsMaker, 0);
		$statReq->setStat('os');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsOsMaker->setNumberArray($vals);
		$hitsOsMaker->setTextArray($txts);
		$hitsOsMaker->setShowNumbers($showNumbers);
		$hitsOsMaker->setMinPercent($minPercentsSpecial);
		$graphStats[] =& $hitsOsMaker;

		$hitsLanguage = new HitsLanguage($this->siteContext, $sg);
		$statSel->addStatKey($hitsLanguage, 0);
		$statReq->setStat('language');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsLanguage->setNumberArray($vals);
		$hitsLanguage->setTextArray($txts);
		$hitsLanguage->setShowNumbers($showNumbers);
		$hitsLanguage->setMinPercent($minPercent);
		$graphStats[] =& $hitsLanguage;

		$hitsResolution = new HitsResolution($this->siteContext, $sg);
		$statSel->addStatKey($hitsResolution, 0);
		$statReq->setStat('screenres');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsResolution->setNumberArray($vals);
		$hitsResolution->setTextArray($txts);
		$hitsResolution->setShowNumbers($showNumbers);
		$hitsResolution->setMinPercent($minPercent);
		$graphStats[] =& $hitsResolution;

		$hitsColor = new HitsColor($this->siteContext, $sg);
		$statSel->addStatKey($hitsColor, 0);
		$statReq->setStat('color');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsColor->setNumberArray($vals);
		$hitsColor->setTextArray($txts);
		$hitsColor->setShowNumbers($showNumbers);
		$hitsColor->setMinPercent($minPercent);
		$graphStats[] =& $hitsColor;

		$hitsJava = new HitsJava($this->siteContext, $sg);
		$statSel->addStatKey($hitsJava, 0);
		$statReq->setStat('java');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsJava->setNumberArray($vals);
		$hitsJava->setTextArray($txts);
		$hitsJava->setShowNumbers($showNumbers);
		$hitsJava->setMinPercent($minPercent);
		$graphStats[] =& $hitsJava;

		$hitsJavaScript = new HitsJavaScript($this->siteContext, $sg);
		$statSel->addStatKey($hitsJavaScript, 0);
		$statReq->setStat('js');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsJavaScript->setNumberArray($vals);
		$hitsJavaScript->setTextArray($txts);
		$hitsJavaScript->setShowNumbers($showNumbers);
		$hitsJavaScript->setMinPercent($minPercent);
		$graphStats[] =& $hitsJavaScript;

		$hitsSearchEngines = new HitsSearchEngines($this->siteContext, $sg);
		$statSel->addStatKey($hitsSearchEngines, 0);
		$statReq->setStat('searchEngine');
		list($txts, $vals) = $datasource->getStatArrays($statReq);
		$hitsSearchEngines->setNumberArray($vals);
		$hitsSearchEngines->setTextArray($txts);
		$hitsSearchEngines->setShowNumbers($showNumbers);
		$hitsSearchEngines->setMinPercent($minPercentsSpecial);
		$graphStats[] =& $hitsSearchEngines;

		//Set the width of the tables where possible
		$tableWidth = $this->siteContext->getHttpVar('tableWidth');
		if (isset($tableWidth) and strlen($tableWidth) > 0) {
			//Is the last char a %?
			if (strpos($tableWidth, '%') === strlen($tableWidth)-1) {
				//Convert 100% to 1.0
				$tableWidth = substr($tableWidth, 0, -1)/100;
			}
			for ($i = 0; $i < count($graphStats); $i++) {
				$graphStats[$i]->setTableWith($tableWidth);
			}
		}
		
		//Get the show array
		if (! isset($this->show) or ! is_array($this->show) or count($this->show) === 0) {
			$show = $this->siteContext->getHttpVar("show");
			if (! is_array($show))
				$show = array('all'); //Show everything if not specified.
		} else {
			$show = $this->show;
		}

		//Sets which stats are selected
		foreach ($show as $val) {
			$statSel->setSelected($val, 1);
		}
		
		//Get the hide array
		if (! isset($this->hide) or ! is_array($this->hide) or count($this->hide) === 0) {
			$hide = $this->siteContext->getHttpVar("hide");
			if (! is_array($hide))
				$hide = array();
		} else {
			$hide = $this->hide;
		}
		
		//Sets which stats are selected
		foreach ($hide as $val) {
			$statSel->setSelected($val, 0);
		}

		//Adds the selected stats for showing
		$selStats = $statSel->getSelectedStats();
		for ($i = 0; $i < count($selStats); $i++) {
			$sg->addElement($selStats[$i]);
		}

		//Add the stat selector itself
		//Currently there is no cache for individual pages, so don't show the selector.
/*		if ($this->getShowStatselector() and $sg->isMediaInteractive()) {
			$sg->addElement($statSel);
		}
*/
		//If send my mail
		if ($this->sendByMail === true) {
			//Tell people why they get an e-mail
			$whyGettingText = $sg->newElement("statsiteWhyGettingMail");
			$whyGettingText->setText(sprintf(brugernavn,
								pwd));
			$whyGettingText->setParagraph(1);
			$sg->addElement($whyGettingText);
		}

		return $sg->getSite();
	}

	/**
	 * Generates the site but outputs a cached version if available.
	 *
	 * @public
	 * @param $statReq the request which states what data to show.
	 *                 Must be an instance of CollectiveStatRequest.
	 * @return String the code for the site.
	 */
	function generateSiteCached($statReq) {
		$startTime = array_sum(explode(' ', microtime()));
		
		$lib = &$this->siteContext->getCodeLib();
		$cache = DataSource::createContentCache($lib->getStier());
		$category = 'collective';
		$id = 'startDay='.$statReq->getStartDay()
		      .'&endDay='.$statReq->getEndDay()
		      .'&unique='.($statReq->getUnique() ? 'true' : 'false')
		      .'&groupby='.$statReq->getGroupby();
		
		$code = $cache->getCached($category, $id);
		if ($code !== NULL) {
			//We got some: Return it.
			return $code;
		}
		
		//Must we generate our own or wait?
		if (! $cache->lockGenerating($category, $id)) {
			//Somebody else is generating it.
			return $this->getGeneratingPage();
		}
		
		//Generate our own.
		$content = $this->generateSite($statReq);
		$endTime = array_sum(explode(' ', microtime()));
		$content .= "<!-- Generated in ".round($endTime - $startTime, 3)." seconds at ".date('r').". -->";
		
		//When does the cache expire?
		if ($statReq->getEndDay() < time()) {
			//It ends in the past: It never expires.
			$expires = -1;
		} else {
			//This is the current month, year etc. It expires tomorrow.
			$expires = $cache->getNextMidnightExpiration();
		}
		
		//Store it.
		$cache->setContents($category, $id, $content, $expires);
		
		//And return it.
		return $content;
	}

} //End of class CollectiveStatSite

/**
 * Represents a site showing which collective stats are available.
 *
 * @public
 * @author Simon Mikkelsen
 */
class CollectiveIndex extends StatSite
{
	/**
	 * Instantiates the class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext the courent {@link SiteContext} object.
	 * @param $generatortype text string which identifies the type of
	 *         generator to be used.
	 */
	function __construct(&$siteContext, $generatorType)
	{
		parent::__construct($siteContext,$generatorType);
	}

	/**
	 * Generates the site.
	 *
	 * @public
	 * @return the code for the site.
	 */
	function generateSite($statReq = "")
	{
		//Initialise
		$sg = &$this->siteGenerator;
		$locale = &$this->siteContext->getLocalizer();
		$lib = &$this->siteContext->getCodeLib();
		$options = &$lib->getStier();
		$collReader = DataSource::createCollectiveReader($lib->getStier());
		
		//Adds the main title
		$headline = $locale->getLocale('collIndexHeadline');
		$mainTitle = $sg->newElement('headline');
		$mainTitle->setHeadline($headline);
		$mainTitle->setSize(1);
		$sg->addElement($mainTitle);
		
		//Tell the time span
		$timeSpanText = $sg->newElement('text');
		$timeSpanText->setText($locale->getLocale('collIndexDesc'));
		$timeSpanText->setParagraph(1);
		$sg->addElement($timeSpanText);

		//Get the data.
		$collDates = $collReader->getDataDates();
		//And show it:
		$months = $locale->getLocale('months');
		$weekLabel = $locale->getLocale('colCalWeek');
		$weeks = $locale->getLocale('shortWeekDays');
		//Make week start monday.
		Html::array_rotate($weeks);

		//Insert an empty string a index 0 to comply with the class.
		array_unshift($months, "");
		array_unshift($weeks, "");

		//Make months upper case.
		foreach ($months as $i => $month) {
			$months[$i] = ucfirst($month);
		}
		
		$totalBaseurl = $options->getOption('urlTotal');
		$currentMonthNo = -1;
		$calMaker = NULL;
		$dayLinks = NULL;
		//Generate data and save it for each month.
		foreach ($collDates as $date) {
			//Print each month only once. For the other days, build the data for the month.
			$monthNo = date('n', $date);
			if ($monthNo !== $currentMonthNo) {
				$currentMonthNo = $monthNo;
				//Print the month.
				if ($calMaker !== NULL) {
					$calMaker->setDayLinks($dayLinks);
					$calMaker->setWeekLinks($weekLinks);
					$sg->addElement($calMaker);
				}
				
				//Start a new one.
				$calMaker = $sg->newElement('calendarMaker');
				$calMaker->setDayNames($weeks);
				$calMaker->setWeekLabel($weekLabel);
				$calMaker->setMonthNames($months);
				$calMaker->setMonthTimestamp($date);
				$calMaker->setMonthLink($totalBaseurl.date('/Y/m/', $date));
				$calMaker->setYearLink($totalBaseurl.date('/Y/', $date));
				
				//Make an empty string for each day and week.
				$dayLinks = array_pad(array(), date('t', $date) + 1, '');
				$weekLinks = array_fill(0, date('W', $date), '');
			}
			
			//And day and week links.
			$dayLinks[date('j', $date)] = $totalBaseurl.date('/Y/m/d/', $date);
			
			$weekDay = date('w', $date) - 1; //0-6, Sunday = -1
			if ($weekDay === -1)
				$weekDay = 6; //Monday = 0, Sunday = 6
			$weekStart = $date - $weekDay * 24*3600; //Always the start of the week.
			$weekLinks[date('W', $weekStart)] = $totalBaseurl.date('/Y/m/d/\w\e\e\k/', $weekStart);
		}

		return $sg->getSite();
	}

	/**
	 * Generates the site but outputs a cached version if available.
	 *
	 * @public
	 * @return String the code for the site.
	 */
	function generateSiteCached() {
		$startTime = array_sum(explode(' ', microtime()));
	
		$lib = &$this->siteContext->getCodeLib();
		$options = $lib->getStier();
		$cache = DataSource::createContentCache($lib->getStier());
		$category = 'collectiveIndex';
		$id = 'main';
		$code = $cache->getCached($category, $id);
		if ($code !== NULL) {
			//We got some: Return it.
			return $code;
		}
		
		//Must we generate our own or wait?
		if (! $cache->lockGenerating($category, $id)) {
			//Somebody else is generating it.
			return $this->getGeneratingPage();
		}
		
		//Generate our own.
		$content = $this->generateSite();
		
		$endTime = array_sum(explode(' ', microtime()));
		$content .= "<!-- Generated in ".round($endTime - $startTime, 3)." seconds at ".date('r').". -->";
		
		//The cache expires at next midnight:
		$expires = $cache->getNextMidnightExpiration();
		
		//Store it.
		$cache->setContents($category, $id, $content, $expires);
		
		//And return it.
		return $content;
	}

} //End of class CollectiveIndex

?>