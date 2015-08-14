<?php

require_once(dirname(__FILE__).'/StatGenerator.php');

/**
 * Represents a stat. generated for series, e.g. time series.
 * <p><b>File:</b> SeriesStatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SeriesStatGenerator extends StatGenerator
{
	/**
	 * The data series as the format:
	 * array('Text' => array(unixtime => value, ... ), ... )
	 *
	 * @private
	 */
	var $dataSeries = null;
	
	/**
	 * The description of the stat.
	 *
	 * @private
	 *
	var $description;
	
	/**
	 * The smallest percent to show (100% is 1.0).
	 * Default is 0.0;
	 *
	 * @private
	 */
	var $minPercent = 0;
	
	/**
	 * The main headline for the stat.
	 *
	 * @private
	 */
	var $mainHeadline;
	
	/**
	 * Creates a new instance witch fills in the given {@link SeriesStatGenerator}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function SeriesStatGenerator(&$siteContext, &$siteGenerator)
	{
		StatGenerator::StatGenerator($siteContext, $siteGenerator);
/*		if (is_subclass_of($siteGenerator, 'SiteGenerator'))
			$this->setSiteGenerator($siteGenerator);
		else
		{
			die("<b>Error:</b> The object provided to the constructor of <code>StatGenerator</code> and extending classes must be an instance of <code>SeriesSiteGenerator</code>. The class is: ".get_class($siteGenerator));
		}*/
	}

	/**
	 * Returns that the parent class of an extending class is
	 * {@link SeriesStatGenerator}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String that the parent class of an extending class is
	 *         {@link StatGenerator}.
	 */
	function getParentClass()
	{
		return "seriesstatgenerator";
	}
	
	/**
	 * Returns the main headline for the stat.
	 *
	 * @public
	 * @return String the headline.
	 */
	function getMainHeadline()
	{
		return $this->mainHeadline;
	}

	/**
	 * Sets the main headline for the stat.
	 *
	 * @public
	 * @return void
	 * @param $mainHeadline the headline.
	 */
	function setMainHeadline($mainHeadline)
	{
		$this->mainHeadline = $mainHeadline;
	}
	
	/**
	 * Sets the data series as a raw data set. The indecies of all the
	 * arrays must correspond. This method will on the fly convert these
	 * data to the format of {@link #setDataSeries()} and then set the
	 * data using that method.
	 *
	 * @public
	 * @param $txts array of the texts, e.g. the name of a browser.
	 *              The same text will usually be repeated many times.
	 * @param $values array of the values, e.g. the number of hits from
	 *                each browser.
	 * @param $times  array of the corresponding times in unix time,
	 *                i.e. times on the day the given browser got the
	 *                given number of hits.
	 */
	function setRawDataSeries($txts, $vals, $times) {
		$cTxts = count($txts);
		$cVals = count($vals);
		$cTimes = count($times);
		
		// Test data for sanity.
		if ($cTxts != $cVals or $cVals != $cTimes) {
			echo "Error: The input arrays to setRawDataSeries does not have the same length: $cTxts, $cVals, $cTimes.";
			exit;
		}
		
		$result = array();
		for ($i = 0; $i < $cTxts; $i++) {
			if (!isset($result[$txts[$i]])) {
				$result[$txts[$i]] = array();
			}
			
			$result[$txts[$i]][$times[$i]] = $vals[$i];
		}
		$this->setDataSeries($result);
	}
	
	/**
	 * Sets the data series as the format:
	 * array('Text' => array(unixtime => value, ... ), ... )
	 *
	 * @public $dataSeries the data series.
	 * @public
	 */
	 function setDataSeries($dataSeries) {
		 $this->dataSeries = $dataSeries;
	 }
	
	/**
	 * Returns the data series as the format described in {@link #setDataSeries()}
	 * or <code>null</code> if not set yet.
	 *
	 * @return the data series.
	 * @public
	 */
	 function getDataSeries() {
		 return $this->dataSeries;
	 }
	
	/**
	 * Returns the description of the stat.
	 *
	 * @public
	 * @return String the description.
	 */
	function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets the description of the stat.
	 *
	 * @public
	 * @return void
	 * @param $description the description of the stat.
	 */
	function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * Sets the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
	 * Default is 0, which means everything will be shown.
	 * 
	 * @public
	 * @param $minPercent the lowest value in percent of rows to show.
	 */
	function setMinPercent($minPercent) {
		$this->minPercent = $minPercent;
	}
	
	/**
	 * Returns the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
	 * 
	 * @public
	 * @return the lowest value in percent of rows to show.
	 */
	function getMinPercent() {
		return $this->minPercent;
	}

	
	/**
	 * May be overwritten to be called just before the stats are generated.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		//Do nothing
	}

	/**
	 * Fills in the {@link SiteGenerator} so it is ready to generate
	 * code.
	 * <p>Must be overwritten by the extending class.</p>
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return SiteElement
	 */
	function &generateStat()
	{
		$this->init();
		
		if (strtolower($this->siteGenerator->getParentClass()) != "sitegenerator")
		{ /*It's not a SiteGenerator*/
			echo "<b>Error:</b> The class <code>SeriesStatGenerator</code> only acepts instances of <code>SeriesStatGenerator</code>.<br>";
			exit;
		}
		
		//Get the locale object
		$locale = &$this->siteContext->getLocalizer();

		//Create graph
		$graph = &$this->siteGenerator->newElement('seriesGraph');
		
		//Create the headline
		$headline = &$this->siteGenerator->newElement("headline");
		$headline->setSize(2);
		$headline->setHeadline($this->getMainHeadline());
		$graph->addHeadElement($headline);

		//Create the description
		$desc = &$this->siteGenerator->newElement("text");

		$desc->addText($this->getDescription());
		$graph->addHeadElement($desc);
		
		$lib = &$this->siteContext->getCodeLib();
		$dataSource = &$lib->getDataSource();
		$graph->setMinPercent($this->getMinPercent());
		$graph->setDataSeries($this->getDataSeries());
		return $graph;
	}


} /*End of class StatGenerator*/

class SeriesBrowser extends SeriesStatGenerator {

	/**
	 * May be overwritten to be called just before the stats are generated.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("SeriesBrowser");
		$locale = &$this->locale;
		$dataSource = $this->dataSource;
		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(9));
		}
		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 * This function must always be overwritten when deriving this class.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return 'SeriesBrowser';
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 * This function must always be overwritten when deriving this class.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return 'sgSeriesBrowser';
	}
	
}


?>