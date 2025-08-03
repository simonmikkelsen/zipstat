<?php

require_once dirname(__FILE__)."/SeriesStatGenerator.php";

/**
 * Represents any stat. generated. Provides a general interface for
 * retriving the result of the stat.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class StatGenerator
{
	/**
	 * The object used generate the final code.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteGenerator;

	/**
	 * An instance of the site context.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteContext;

	/**
	 * An instance of the data source.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $dataSource;

	/**
	 * An instance of the code library.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lib;

	/**
	 * An instance of the locale object.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $locale;

	/**
	 * A textual identifier of this stat.
	 * Only the letters a-z, A-Z and the numers 0-9 may be used.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $name;

	/**
	 * Creates a new instance witch fills in the given {@link SiteGenerator}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function __construct(&$siteContext, &$siteGenerator)
	{
		$this->siteContext = &$siteContext;
		$this->lib = &$this->siteContext->getCodeLib();
		$this->dataSource = &$this->lib->getDataSource();
		$this->locale = &$this->siteContext->getLocalizer();

		if (strtolower($siteGenerator->getParentClass()) == "sitegenerator")
			$this->setSiteGenerator($siteGenerator);
		else
		{
			echo "<b>Error:</b> The object provided to the constructor of <code>StatGenerator</code> and extending classes must be an instance of <code>SiteGenerator</code>.";
			exit;
		}
	}

	/**
	 * Returns the data source used for generating this stat.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return DataSource the datasource.
	 */
	function &getDataSource()
	{
		return $this->dataSource;
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
		echo "<b>Error:</b> <code>StatGenerator.generateStat(SiteGenerator)</code> must be overwritten by the extending class.<br>";
		exit;
	}

	/**
	 * Returns all headers needed for the represented stat for the given scheme.
	 * This method might do some heavy iteration, so try only to use it once.
	 * Due to the current implementation, this method cannot be invoked untill
	 * {@link #getCode} has been invoked - it will simply fail.
	 *
	 * @public
	 */
	function getHeadersRecursive($scheme) {
		if (isset($this->siteElement) and $this->siteElement != NULL) {
			return $this->siteElement->getHeadersRecursive($scheme);
		} else {
			die("This method cannot be invoked untill getCode has been invoked on the same object.");
		}
	}
	
	/**
	 * Returns the code represented by the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code represented by the object.
	 */
	function getCode()
	{
		$this->siteElement = &$this->generateStat();

		return $this->siteElement->getCode();
	}

	/**
	 * Returns the object used generate the final code.
	 * The object will be in a &quot;filled in&quot; state, where its
	 * ready to generate the code.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return SiteGenerator the object used generate the final code.
	 */
	function &getSiteGenerator()
	{
		if ($this->isAllnfoGiven() == 1)
			return $this->siteGenerator;
		else
		{
			echo "<b>Error:</b> Function <code>StatGenerator.getSiteGenerator()</code> may not be called before all the needed information have been provided to the extending class. Please refer to the documentation of the extending class for forther information.";
			exit;
		}
	}

	/**
	 * Sets the object used generate the final code.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $siteGenerator the object used generate the final code.
	 */
	function setSiteGenerator(&$siteGenerator)
	{
		$this->siteGenerator = &$siteGenerator;
	}

	/**
	 * Returns that the parent class of an extending class is
	 * {@link StatGenerator}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String that the parent class of an extending class is
	 *         {@link StatGenerator}.
	 */
	function getParentClass()
	{
		return "statgenerator";
	}

	/**
	 * Returns a textual identifier of this stat.
	 * Only the letters a-z, A-Z and the numers 0-9 are used.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String a textual identifier of this stat.
	 */
	function getName()
	{
		return $this->name;
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
		echo "<b>Error:</b> The function <code>getIdentifier</code> must always be overwritten when deriving the class <code>StatGenerator</code>, which is not done in this case.";
		exit;
	}

	/**
	 * Sets a textual identifier of this stat.
	 * Only the letters a-z, A-Z and the numers 0-9 may be used.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $name a textual identifier of this stat.
	 */
	function setName($name)
	{
		$this->name = $name;
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
		echo "<b>Error:</b> The function <code>getHeadlineKey()</code> must always be overwritten when deriving the class <code>StatGenerator</code>.";
		exit;
	}

} /*End of class StatGenerator*/

/**
 * Abstract class for a stat created width a graph.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class GraphStatGenerator extends StatGenerator
{
	/**
	 * An array of the headers for the graph.
	 * The indexes should correspond to the following:<br>
	 * <code>0</code>: Headline for the text<br>
	 * <code>1</code>: Headline for the numbers<br>
	 * <code>2</code>: Headline for the percents<br>
	 * <code>3</code>: Headline for the graph<br>
	 * <p>If a non array <code>String</code> is given this will be
	 * split/exploded with &quot;<code>::</code>&quot; as seperator.
	 * <p>This funtion must be overwritten be an extending class.</p>
	 *
	 * @private
	 * @since 0.0.1
	 */
	 var $headerArray;

	/**
	 * States if the graph sould be sorted.
	 * The value should be <code>1</code> if the graph should be sorted,
	 * else <code>0</code>.
	 *
	 * @private
	 * @since 0.0.1
	 */
	 var $sort;

	/**
	 * The row containing this exact text should be emphasized.
	 * Should be an empty <code>String</code> if no text should be emphasized.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $emphasize;

	/**
	 * An array containing the numbers for this stat.
	 * The indexes should match the indexes in {@link #$textArray}.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $numberArray;

	/**
	 * An array containing the text for this stat.
	 * The indexes should match the indexes in {@link #$numberArray}.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $textArray;

	/**
	 * The description of the stat.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $description;

	/**
	 * The main headline for the stat.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $mainHeadline;

	/**
	 * If set, this is used to wrap the texts.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $textWrapper = NULL;
	
	/**
	 * If numbers shall be shown on the graph. @c true for yes, @c false for no.
	 *
	 * @private
	 */
	var $showNumbers = true;
	
	/**
	 * Do not show rows with a percent less than this value.
	 * The value is the percent between 0 and 1.
	 *
	 * @private
	 */
	var $minPercent = 0;

	/**
	 * The with of the table. 100% is 1.0.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $tableWith = -1;

	/**
	 * Fills in the {@link SiteGenerator} so it is ready to generate
	 * code.
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
			echo "<b>Error:</b> The class <code>GraphStatGenerator</code> only acepts instances of <code>SiteGenerator</code>.<br>";
			exit;
		}

		//Get the locale object
		$locale = &$this->siteContext->getLocalizer();

		//Create graph
		$table = $this->siteGenerator->newElement("graphTable");
		$table->setElementClass("stattabel");
		//If a table width is given, set it.
		if ($this->tableWith !== -1) {
			$table->setTableWith($this->tableWith);
		}

		//Create the headline
		$headline = $this->siteGenerator->newElement("headline");
		$headline->setSize(2);
		$headline->setHeadline($this->getMainHeadline());
		$table->addHeadElement($headline);

		//Create the description
		$desc = $this->siteGenerator->newElement("text");

		$desc->addText($this->getDescription());
		$table->addHeadElement($desc);

		$lib = &$this->siteContext->getCodeLib();
		$dataSource = &$lib->getDataSource();

		//Create the graph and table
		$table->setEmphasize($this->getEmphasize());
		$table->setElementName($this->getName());
		$table->setShowNumbers($this->getShowNumbers() ? 1:0);
		$table->setMinPercent($this->getMinPercent());
		$table->setSorted($this->getSort());
		$table->setTextArray($this->getTextArray());
		$table->setNumArray($this->getNumberArray());
		$table->setHeaderArray($this->getHeaderArray());
		return $table;
	}
	
	/**
	 * Summarizes the internal values, set using {@link #setNumberArray}
	 * and {@link #setTextArray}, as described in the given schema. The schema
	 * must have the specified data structure or this method will make an unexpected
	 * result and the program may crash!
	 * 
	 * <h2>Schema definition</h2>
	 * The schema is a 2 dimentional indexed array.
	 * <pre>
	 * $schema = array(
	 * 	array('Opera', 'opera'),
	 * 	array('Mozilla', 'mozilla', 'netscape'),
	 * 	array('Microsoft', 'msie', 'microsoft', 'frontpage'),
	 * 	array('Safari', 'safari'),
	 * 	array('Konqueror', 'konqueror')
	 * 	);
	 * </pre>
	 * The above code is an example of a schema. This will summarize the data
	 * into 5 categories and one for data that cannot be categorized.
	 * It e.g. says: If the text of a text/number pair contains the text
	 * "mozilla" or "netscape", count the number in the "Mozilla" category.
	 * In this way both "Mozilla 1.0", "Mozilla 1.5" and "Netscape 6.2" will be
	 * counted in the "Mozilla" category.
	 * Note: Index 0 of the inner arrays is the label the user will see.
	 * Warning: The other values MUST be in lower case, or they will be ignored.
	 *
	 * @param $schema the schema as described above.
	 */
	function summarize($schema) {
		$num = $this->numberArray;
		$txt = $this->textArray;
		$sums = array_fill(0, count($schema), 0);
		$others = 0;
		//Iterate over the data.
		for ($i = 0; $i < count($num); $i++) {
			//Itererate over the makers.
			for ($c = 0; $c < count($schema); $c++) {
				//Iterate over the browsers.
				for ($b = 1; $b < count($schema[$c]); $b++) {
					if (strpos(strtolower($txt[$i]), $schema[$c][$b]) !== FALSE) {
						//We got a match!
						$sums[$c] += $num[$i];
            // Note to 25 years yunger self: Do not code like this!
						continue 3; //NOTE: Continue on the 3nd level!
					}
				} //for each browser.
			} //for each maker.
      if (is_numeric($num[$i])) {
			  $others += $num[$i];
      }
		} //for each data.

		//Make new numbers and texts.
		$newNum = array($others);
		$newTxt = array($this->locale->getLocale('sgLatestOthers'));
		for ($i = 0; $i < count($sums); $i++) {
			$newNum[] = $sums[$i];
			$newTxt[] = $schema[$i][0];
		}
		
		$this->numberArray = $newNum;
		$this->textArray = $newTxt;
	}

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
		//Do nothing
	}

	/**
	 * Get the with of the table. 100% is 1.0.
	 *
	 * @public
	 * @version 0.0.1
	 * @return long the with of the table.
	 * @since 0.0.1
	 */
	function getTableWith() {
		return $this->tableWith;
	}

	/**
	 * Set the with of the table. 100% is 1.0.
	 *
	 * @public
	 * @version 0.0.1
	 * @return void
	 * @param the with of the table.
	 * @since 0.0.1
	 */
	function setTableWith($tableWith) {
		$this->tableWith = $tableWith;
	}

	/**
	 * Returns an array of the headers for the graph.
	 * The indexes corresponds to the following:<br>
	 * <code>0</code>: Headline for the text<br>
	 * <code>1</code>: Headline for the numbers<br>
	 * <code>2</code>: Headline for the percents<br>
	 * <code>3</code>: Headline for the graph<br>
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] the array of headers.
	 */
	function getHeaderArray()
	{
		return $this->headerArray;
	}

	/**
	 * Sets an array of the headers for the graph.
	 * The indexes should correspond to the following:<br>
	 * <code>0</code>: Headline for the text<br>
	 * <code>1</code>: Headline for the numbers<br>
	 * <code>2</code>: Headline for the percents<br>
	 * <code>3</code>: Headline for the graph<br>
	 * <p>If a non array <code>String</code> is given this will be
	 * split/exploded with &quot;<code>::</code>&quot; as seperator.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $headerArray an array of headlines.
	 * @return void
	 */
	function setHeaderArray($headerArray)
	{
		if (is_array($headerArray))
			$this->headerArray = $headerArray;
		else
			$this->headerArray = explode("::", $headerArray);
	}


	/**
	 * Returns if the graph sould be sorted.
	 * Returns <code>1</code> if the graph should be sorted, else <code>0</code>.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int <code>1</code> if the graph should be sorted, else <code>0</code>.
	 */
	function getSort()
	{
		return $this->sort;
	}

	/**
	 * Sets if the graph sould be sorted.
	 * Set to <code>1</code> if the graph should be sorted, else <code>0</code>.
	 * Other values will produce an error.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $sort if the graph sould be sorted.
	 * @return void
	 */
	function setSort($sort)
	{
		$this->sort = $sort;
	}

	/**
	 * Returns the value text of the row which should be emphasized.
	 * An empty <code>String</code> is returned if no text should be
	 * emphasized.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the text that should be emphasized.
	 */
	function getEmphasize()
	{
		return $this->emphasize;
	}

	/**
	 * Sets the value text of the row which should be emphasized.
	 * An empty <code>String</code> should be giben if no text should be
	 * emphasized.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $emphasize the text to emphasize.
	 * @return void
	 */
	function setEmphasize($emphasize)
	{
		$this->emphasize = $emphasize;
	}

	/**
	 * Returns an array containing the numbers for this stat.
 	 * The indexes should match the indexes in the array given by
 	 * {@link #getTextArray}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Stirng[] the numbers in an array.
	 */
	function getNumberArray()
	{
		return $this->numberArray;
	}

	/**
	 * Returns an array containing the numbers for this stat.
 	 * The indexes should match the indexes in the array set by
 	 * {@link #setTextArray}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $numberArray the array fo the numbers for this stat.
	 */
	function setNumberArray($numberArray)
	{
		$this->numberArray = $numberArray;
	}

	/**
	 * Returns an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] the text in an array.
	 */
	function getTextArray()
	{
		return $this->textArray;
	}

	/**
	 * Sets an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $textArray the text array for this stat.
	 */
	function setTextArray($textArray)
	{
		$this->textArray = $textArray;
	}

	/**
	 * Returns the description of the stat.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
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
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $description the description of the stat.
	 */
	function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Returns the main headline for the stat.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
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
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $mainHeadline the headline.
	 */
	function setMainHeadline($mainHeadline)
	{
		$this->mainHeadline = $mainHeadline;
	}

	/**
	 * Returns if numbers shall be shown on the graph. @c true for yes, @c false for no.
	 *
	 * @public
	 * @return if numbers shall be shown on the graph.
	 */
	function getShowNumbers() {
		return $this->showNumbers;
	}

	/**
	 * Sets if numbers shall be shown on the graph. @c true for yes, @c false for no.
	 *
	 * @public
	 * @param $showNumbers if numbers shall be shown on the graph.
	 */
	function setShowNumbers($showNumbers) {
		$this->showNumbers = $showNumbers;
	}
	
	/**
	 * Sets the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
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

} //End of class GraphStatGenerator

/**
 * Generates the basic stats, or
 * &quot;Enkeltstende statistikker&quot; in danish.
 * The {@link SiteGenerator} must be an instance of the subclass
 * <code>SiteTable</code>.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class BasicStatsGenerator extends StatGenerator
{
	/**
	 * Fills in a {@link SiteElement} so it is ready to generate
	 * code.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return SiteElement
	 */
	function &generateStat()
	{
		if (strtolower($this->siteGenerator->getParentClass()) != "sitegenerator")
		{ /*It's not a SiteGenerator*/
			echo "<b>Error:</b> The class <code>BasicStatsGenerator</code> only acepts instances of <code>SiteGenerator</code>.<br>";
			exit;
		}

		$this->setName("BasicStat");

		//Creates a rounder for rounding numbers
		$rounder = &$this->lib->getRounder();
		$rounder->setGoForDecimalsVisible(2);
		$rounder->setMaxDecimalsVisible(2);
		$rounder->setZeroDotToPercent(0);
		$rounder->setAddPercent(0);

		//Get the locale object
		$locale = &$this->siteContext->getLocalizer();

		//Create a table
		$table = $this->siteGenerator->newElement("table");

		$tableHeadline = $this->siteGenerator->newElement("headline");
		$tableHeadline->setSize(2);
		$tableHeadline->setHeadline($locale->getLocale($this->getHeadlineKey()));

		$table->addHeadElement($tableHeadline);

		$table->setElementClass('enkelttabel');
		$table->setElementName('BasicStats');
		$table->setHeaderClass('enkeltA');
		$table->setHeadersAre(1); /*1 = The top row is header.*/
		$table->setColumnClassArray(array('enkeltB','enkeltB','enkeltB'));

		//Add the top headers
		$table->addRow(array(
			$locale->getLocale('basicStatsType'),
			$locale->getLocale('basicStatsData'),
			$locale->getLocale('basicStatsDate')
			));

		//Count up with this number
		$countUp = $this->dataSource->getLine(82);
		
		//Hits since start
		if ($countUp > 0) {
			$totalHits = $this->getHitsSinceStart() . $countUp;
			$totalText = $locale->getLocale('basicSHitsSStart')
			                                               . " (+ ".$countUp.")";
		} else {
			$totalHits = $this->getHitsSinceStart();
			$totalText = $locale->getLocale('basicSHitsSStart');
		}
		$table->addRow(array(
			$totalText,
			$totalHits,
			$this->getHitsSinceStartResetdate()
			));

		//Hits since
		if ($countUp > 0) {
			$totalHits = $this->getHitsSince() . $countUp;
			$totalText = $locale->getLocale('basicSHitsS')
			                                               . " (+ ".$countUp.")";
		} else {
			$totalHits = $this->getHitsSince() . $countUp;
			$totalText = $locale->getLocale('basicSHitsS');
		}
		$table->addRow(array(
			$totalText,
			$totalHits,
			$this->getHitsSinceResetdate()
			));

		//Uniqe hits
		$table->addRow(array(
			$locale->getLocale('basicSUniq'),
			$this->getHitsUniqe(),
			$this->getHitsUniqeResetdate(),
			));

		//Hits per user, in dahisn: "Hits pr. bruger"
		$table->addRow(array(
			$locale->getLocale('basicSHitsPrUser'),
			$rounder->formatNumber($this->lib->hpb()),
			""
			));

		//Add e.g. a line
		$table->addSeperator();

		//Maximum hits on a day
		$table->addRow(array(
			$locale->getLocale('basicSMaxHitsDay'),
			$this->getMaxHitsDay(),
			$this->getMaxHitsDayDate()
			));

		//Maximum number of hits in a month
		$table->addRow(array(
			$locale->getLocale('basicSMaxHitsMonth'),
			$this->getMaxHitsMonth(),
			$this->getMaxHitsMonthDate()
			));

		//Maximum uniqe hits on a day
		$table->addRow(array(
			$locale->getLocale('basicSMaxUniqHDay'),
			$this->getMaxUniqHitsDay(),
			$this->getMaxUniqHitsDayDate()
			));

		//Maximum uniqe hits on a month
		$table->addRow(array(
			$locale->getLocale('basicSMaxUniqHMonth'),
			$this->getMaxUniqHitsMonth(),
			$this->getMaxUniqHitsMonthDate()
			));

		//Add e.g. a line
		$table->addSeperator();

		//Max uniq hits today
		$table->addRow(array(
			$locale->getLocale('basicSUniqHToday'),
			$this->getUniqHitsToday(),
			""
			));

		//Uniqe hits this month
		$table->addRow(array(
			$locale->getLocale('baticSUIniqHThisMonth'),
			$this->getUniqHitsThisMonth(),
			""
			));

		//Uniqe hits per day
		$table->addRow(array(
			$locale->getLocale('basicSUniqPerDay'),
			$rounder->formatNumber($this->getUniqHPerDay()),
			""
			));

		//Uniqe hits per hour
		$table->addRow(array(
			$locale->getLocale('basicSUniqPerHour'),
			$rounder->formatNumber($this->getUniqeHPerHour()),
			""
			));

		//Add e.g. a line
		$table->addSeperator();

		//Hits per month calculated and hits per month inser here!

		//Hits per day
		$table->addRow(array(
			$locale->getLocale('basicSHitsDay'),
			$rounder->formatNumber($this->getHitsPerDay()),
			"",
		));

		//Hits per hour
		$table->addRow(array(
			$locale->getLocale('basicSHitsHour'),
			$rounder->formatNumber($this->getHitsPerHour()),
			""
			));

		//Each site is seen - the time each site is seen, in average.
		$table->addRow(array(
			$locale->getLocale('basicSeSiteSeen'),
			//$locale->secsToReadable(round($this->timePerVisitor())),
			$locale->secsToReadable(round($this->timePerVisitor())),
			""
			));

		//Number of visitors looking at the site
		$table->addRow(array(
			$locale->getLocale('basicSVisitorsNow'),
			$this->getVisitorsNow(),
			""
			));

		//$p = new Projection($this->siteContext, $this->siteGenerator);
		return $table;
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "BasicStats";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "basicStats";
	}

	/**
	 * Returns the average time in seconds a visitor is looking at one
	 * site (html file).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function timePerVisitor()
	{
		$secsVisitors = explode(":",$this->dataSource->getLine(73));
		if ($secsVisitors[1] != 0)
			$timePerVisitor = ($secsVisitors[0])/$secsVisitors[1]; /*Time per visitor*/
		else
			$timePerVisitor = 0;

		return $timePerVisitor;
	}

	/**
	 * Returns the number of visitors looking at the site
	 * &quot;right now&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int
	 */
	function getVisitorsNow()
	{
		$timePerVisitor = $this->timePerVisitor();

		$visitorsNow = 0;
		$iper = ":";
		$unixtimes = explode(":",$this->dataSource->getLine(72)); /*Unix time for line 45*/
		$ip = explode(":",$this->dataSource->getLine(45)); /*IP-adresses for visitors*/

		//Count how many IP-adresses that have visited the site in the time
		//the average visitor looks at a site.
		for ($i = 0;$i < sizeof($unixtimes);$i++)
		{
			if (is_numeric($unixtimes[$i]) and is_numeric($timePerVisitor) 
          and ($unixtimes[$i] + $timePerVisitor > time()) 
          and (strpos($iper,":".$ip[$i].":") === false)
          )
			{
				$visitorsNow++;
				$iper .= $ip[$i].":";
			}
		}
		return $visitorsNow;
	}

	/**
	 * Returns the number of hits per hour.
	 * In danish: &quot;Hits pr. time&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function getHitsPerHour()
	{
		return $this->getHitsPerDay()/24;
	}

	/**
	 * Returns the number of uniqe hits per hour.
	 * In danish: &quot;Unikke htis pr. time&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function getUniqeHPerHour()
	{
		return $this->getUniqHPerDay() / 24;
	}

	/**
	 * Returns the number of uniqe hits per day.
	 * In dahish: &quot;Unikke hits pr. dag&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function getUniqHPerDay()
	{
		$hitsPerDay = $this->getHitsPerDay();

		$hitsPerUser = $this->lib->hpb();

		if ($hitsPerUser > 0)
			return $hitsPerDay/$hitsPerUser;
		else
			return 0;
	}

	/**
	 * Returns the number of hits per day.
	 * In dahisn: &quot;Hits pr. dag&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function getHitsPerDay()
	{
		//Hits per day, start
		$n = 0;
		$mday = date('j'); /*Day of month*/
		$latest31days = explode("::",$this->dataSource->getLine(11));

		$max = -1;
		$min = -1;
		$hitsPerDay = 0;

		for ($i = 0; $i < 31;$i++)
		{
			if (($latest31days[$i] > 0) and ($i != $mday-1))
			{
				$hitsPerDay += $latest31days[$i];
				$n++;
				if (($max < $latest31days[$i]) or ($max == -1))
					{ $max = $latest31days[$i]; }
				if (($min > $latest31days[$i]) or ($min == -1))
					{$min = $latest31days[$i]; }
			}
		}

		if ($max > 0)
		{
			$hitsPerDay -= $max;
			$n--;
		}
		if ($min > 0)
		{
			$hitsPerDay -= $min;
			$n--;
		}

		if ($n > 0)
			{$hitsPerDay = $hitsPerDay / $n;}
		else {$hitsPerDay = 0;}

		return $hitsPerDay;
	}

	/**
	 * Returns the number of uniqe hits &quot;this month&quot;.
	 * In danish: &quot;Unikke hits denne mned&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getUniqHitsThisMonth()
	{
		return $this->dataSource->getLine(79);
	}

	/**
	 * Returns the number of uniqe hits &quot;today&quot;.
	 * In danish: &quot;Unikke hits i dag&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getUniqHitsToday()
	{
		return $this->dataSource->getLine(76);
	}

	/**
	 * Returns the date for the maximum number of uniqe hits that has
	 * occured in a month.
	 * In danish: &quot;Max unikke hits p en mned&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getMaxUniqHitsMonthDate()
	{
		return $this->locale->localizeDate($this->dataSource->getLine(81));
	}

	/**
	 * Returns the maximum number of uniqe hits that has occured in a month.
	 * In danish: &quot;Max unikke hits p en mned&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getMaxUniqHitsMonth()
	{
		return $this->dataSource->getLine(80);
	}

	/**
	 * Returns the date for the maximum number of uniqe hits that has
	 * occured in a day.
	 * In danish: &quot;Max unikke hits p en dag&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getMaxUniqHitsDayDate()
	{
		return $this->locale->localizeDate($this->dataSource->getLine(78));
	}

	/**
	 * Returns the maximum number of uniqe hits that has occured in a day.
	 * In danish: &quot;Max unikke hits p en dag&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getMaxUniqHitsDay()
	{
		return $this->dataSource->getLine(77);
	}

	/**
	 * Returns the date where the maximum number of hits came in a month.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getMaxHitsMonthDate()
	{
		return $this->locale->localizeDate($this->dataSource->getLine(19));
	}

	/**
	 * Returns the maximum number of hits that have ever occured on a month.
	 * Danish: &quot;Max hits p en mned&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int
	 */
	function getMaxHitsMonth()
	{
		return $this->dataSource->getLine(18);
	}

	/**
	 * Returns the date where the maximum number of hits came in a day.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String
	 */
	function getMaxHitsDayDate()
	{
		return $this->locale->localizeDate($this->dataSource->getLine(17));
	}

	/**
	 * Returns the maximum number of hits that have ever occured on a day.
	 * Danish: &quot;Max hits p en dag&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int
	 */
	function getMaxHitsDay()
	{
		return $this->dataSource->getLine(16);
	}

	/**
	 * Returns the value of the counter counting uniqer hits.
	 * In danish &quot;Heraf unikke&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int
	 */
	function getHitsUniqe()
	{
		return $this->dataSource->getLine(44);
	}

	/**
	 * Returns the reset date for {@link #getHitsUniqe}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the reset date for {@link #getHitsUniqe}.
	 */
	function getHitsUniqeResetdate()
	{
		$dates = $this->dataSource->getLineObj(51);
		return $dates->getDateString(44,$this->locale);
	}

	/**
	 * Returns the value of the counter that can be retset.
	 * In danish &quot;Hits siden&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int
	 */
	function getHitsSince()
	{
		return $this->dataSource->getLine(7);
	}

	/**
	 * Returns the formated date where the counter in
	 * {@link #getHitsSince} were reset.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the formated date where the counter in
	 *              {@link #getHitsSince} were reset.
	 */
	function getHitsSinceResetdate()
	{
		return $this->locale->localizeDate($this->dataSource->getLine(8));
	}

	/**
	 * Returns the value of the counter that can't be reset.
	 * In danish &quot;Hits siden start&quot;.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the value of the counter that can't be reset.
	 */
	function getHitsSinceStart()
	{
		return $this->dataSource->getLine(13);
	}

	/**
	 * Returns the formated date where the counter in
	 * {@link #getHitsSinceStart} were reset.
	 *
	 * @public
	 * @todo complete this. Only "" is returned now.
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the formated date where the counter in
	 *              {@link #getHitsSinceStart} were reset.
	 */
	function getHitsSinceStartResetdate()
	{
		return ""; //$this->locale->localizeDate($this->dataSource->getLine());
	}
}

/**
 * Calculates a number of projections on how many visitors wil visit
 * the site.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class Projection extends StatGenerator
{

	function &generateStat()
	{
		if (strtolower($this->siteGenerator->getParentClass()) != "sitegenerator")
		{ /*It's not a SiteGenerator*/
			echo "<b>Error:</b> The class <code>BasicStatsGenerator</code> only acepts instances of <code>SiteGenerator</code>.<br>";
			exit;
		}

		$this->setName("Projection");

		//Creates a rounder for rounding percents
		$percentRounder = &$this->lib->getRounder();
		$percentRounder->setGoForDecimalsVisible(2);
		$percentRounder->setMaxDecimalsVisible(2);
		$percentRounder->setZeroDotToPercent(1);
		$percentRounder->setAddPercent(1);

		//Creates a rounder for rounding numbers
		$rounder = $this->lib->getRounder();
		$rounder->setGoForDecimalsVisible(2);
		$rounder->setMaxDecimalsVisible(2);
		$rounder->setZeroDotToPercent(0);
		$rounder->setAddPercent(0);

		//Get the locale object
		$locale = &$this->siteContext->getLocalizer();

		//Create a table
		$text = $this->siteGenerator->newElement("text");

		$headline = $this->siteGenerator->newElement("headline");
		$headline->setSize(2);
		$headline->setHeadline($locale->getLocale($this->getHeadlineKey()));

		$text->addHeadElement($headline);

/*Eftersom %1$s
af sidens hits plejer at komme fr %2$s,
vil der komme ca. %3$s hits mere, hvilket bringer siden op p %4$s
hits i dag. Yderligere, vil der komme ca. %5$s hits mere i denne mned,
hvilket bringer siden op p i alt %6$s i denne mned.*/
		$text->addText(sprintf($locale->getLocale("projTheText"),
			$percentRounder->formatNumber($this->percentHits(1)),
			$this->locale->localizeDate(time(),2),
			$rounder->formatNumber($this->hitsToComeToday()),
			$rounder->formatNumber($this->hitsSumToday()),
			$rounder->formatNumber($this->hitsToComeMonth()),
			$rounder->formatNumber($this->hitsSumMonth())
			));

		return $text;
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "Projection";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "proj";
	}

	/**
	 * If 1 is given as parameter, the percentage of visits that normally
	 * have occured on this time of the day (<code>$ptn</code>). If 2 is given as parameter,
	 * the percentage of visitors that normally will come the rest of the day.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function percentHits($witch)
	{
		//Counts the sum of registered hits in hours and
		//the sum of hits in hours up to this time of the day

		$date = getdate();
		$hour = $date['hours'];
		$min = $date['minutes'];

		$hours = explode("::",$this->dataSource->getLine(14));
		$hrsToNow = 0;
		$sumHrs = 0;
		for ($i = 0;$i < 24;$i++)
		{
			if ($i < sizeof($hours)) {
				if ($i < $hour)
					$hrsToNow += $hours[$i];

				$sumHrs += $hours[$i];
			} //End if $i < sizeof..
		}

		//Interpolate the number of hits that normally would have come this hour.
		$hrsToNow += $hours[$hour]*(1-((60-$min)/60));

		//Calculates the percent of hits in hours hours left today ($ptt),
		//and the percent of hits in hours hours that have come undtil now ($hrsNow).
		if ($sumHrs != 0)
		{
			$hrsLeft = ( ($sumHrs - $hrsToNow) /$sumHrs);
			$hrsNow = 1 - $hrsLeft;
		}
		else
		{
			$hrsLeft = 0;
			$hrsNow = 0;
		}

		if ($witch === 1)
			return $hrsNow;
		elseif ($witch === 2)
			return $hrsLeft;
	}

	/**
	 * The number of hits that proberbly will come today.
	 * (<code>$ti</code>)
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function hitsToComeToday()
	{
		//Calculates the sum of hits the latest 31 days
		//Some dates are ignored in the sum, so the total number of
		//days counted is in $sumDays.

		$dates = getDate();
		$mday = $dates['mday'];
		$wday = $dates['wday'];

		$days = explode("::",$this->dataSource->getLine(11));
		$daysInSum = 0;
		$maxDay = 0;
		$minDay = 0;

		$sumDays = 0;
		for ($i = 0;$i < 31;$i++)
		{
			if (($i != $mday -1) and ($days[$i] != 0))
			{
				$sumDays += $days[$i];
				$daysInSum++;

				if ($days[$i] > $maxDay)
					$maxDay = $days[$i];
				if ($days[$i] < $minDay)
					$minDay = $days[$i];
			}
		} /*End for i...*/

		if ($daysInSum > 1)
		{
			if ($maxDay)
			{
				$sumDays -= $maxDay;
				$daysInSum--;
			}
			if ($minDay)
			{
				$sumDays -= $maxDay;
				$daysInSum--;
			}
		} /*End if $sumDays > 1*/

		//Calculates the percentage of hits in the week that comes in this day.
		$hitsInWeek = explode("::",$this->dataSource->getLine(15));
		$daysCounted = 0;
		$sumWeekHits = 0;
		for ($i = 0;$i < 7;$i++)
		{
			if ($hitsInWeek[$i] != 0)
			{
				$sumWeekHits += $hitsInWeek[$i];
				$daysCounted++;
			}
		}
		if ($sumWeekHits != 0)
			$percentWeekDay = 1 - ( ($sumWeekHits - $hitsInWeek[$wday - 1]) /$sumWeekHits);
		else
			$percentWeekDay = 0;

		//Calculates an average day, with week days taken in
		if ($daysInSum != 0)
			$avgDay = ( $sumDays /$daysInSum) * $daysCounted * $percentWeekDay;
		else
			$avgDay = 0;

		//Calculates the deviation from normal today,
		//based on hits from the latest 31 days
		$days = explode("::",$this->dataSource->getLine(11));
		$hrsNow = $this->percentHits(1);
		$hrsLeft = $this->percentHits(2);
		if (($avgDay * $hrsNow) != 0)
			$deviation = 1 - (  (($avgDay * $hrsNow) - $days[$mday-1]) / ($avgDay * $hrsNow)  );
		else
			$deviation = 0;

		//The number of users that will come today
		return $avgDay * $hrsLeft *(($deviation) / 1);
	}

	/**
	 * The estimated sum of total hits today.
	 * (<code>$hits_ialt_timer</code>)
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float the estimated sum of total hits today.
	 */
	function hitsSumToday()
	{
		$dates = getDate();
		$mday = $dates['mday'];

		//What came today + hitsToComeToday()
		$days = explode("::",$this->dataSource->getLine(11));

		return $days[$mday] + $this->hitsToComeToday();
	}

	/**
	 * The estimated number of hits that will come this month.
	 * (<code>$htm</code>)
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return float
	 */
	function hitsToComeMonth()
	{
		$dates = getDate();
		$mday = $dates['mday'];

		$days = explode("::",$this->dataSource->getLine(11));

		$n = 0;
		//Count the number of days we can use
		for ($i = 0;$i <= $mday;$i++)
			if ($days[$i] != 0)
				$n++;

		$months = explode("::",$this->dataSource->getLine(9));

		if ($n != 0)
		{
			return ($months[$dates['mon']] + $this->hitsToComeToday()) *
				(Html::lengthOfMont($dates['mon']) / $n);
		}
		else
			return $this->hitsToComeToday();
	}

	/**
	 * The estimated sum of total hits this month.
	 * (<code>$hits_ialt_maaned</code>)
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int the estimated sum of total hits this month.
	 */
	function hitsSumMonth()
	{
		$dates = getDate();
		//What comes the rest of this month + what have come.
		$months = explode("::",$this->dataSource->getLine(9));
		return $months[$dates['mon']-1] + $this->hitsToComeMonth();
	}

} /*End of class Projection*/

/**
 * Creates a stat of hits for every month for a year.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class MonthStats extends GraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("MonthStats");

		$locale = &$this->locale;
		$dataSource = $this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('month'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(0);

		$months = $locale->getLocale('months');
		$dates = getDate();
		$this->setEmphasize($months[$dates['mon']]);

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(9));
		}

		//Get the months
		//$months = $locale->getMonths();
		$text = $locale->getLocale('months');
		for ($i = 0; $i < sizeof($text); $i++)
		{
			//Makes first char upper case, rest lower
			$text[$i] = ucfirst(strtolower($text[$i]));
		}
		$this->setTextArray($text);

		$this->setDescription($locale->getLocale('sgMonthDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "MonthStats";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgMonthStat";
	}

} /*End of class MonthStats*/

/**
 * Creates a stat of hits for every day of the month.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HitsDay extends GraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsDay");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('day'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(0);

		/*day of the month without leading zeros; i.e. "1" to "31"*/
		$this->setEmphasize(date('j'));

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(11));
			$days = array();
			$numDays = date('t'); /* number of days in the given month; i.e. "28" to "31"*/
			for ($i = 1; $i <= $numDays; $i++) {
				$days[] = $i;
			}
			$this->setTextArray($days);
		}

		$this->setDescription($locale->getLocale('sgDayDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsDay";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgDayStat";
	}

} /*End of class HitsDay*/

/**
 * Creates a stat for every hour of the day.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HitsHour extends GraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsHour");

		$locale = $this->locale;
		$dataSource = $this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('hour'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(0);

		/*hour, 24-hour format; i.e. "00" to "23"*/
		$this->setEmphasize(date('H'));

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(14));
		}

		$hrs = array();
		for ($i = 1; $i <= 24; $i++)
		{
			if ($i <= 9)
				$hr = "0".$i;
			else
				$hr = $i;

			$hrs[] = $hr;
		} /*End for $i = 1*/
		$this->setTextArray($hrs);

		$this->setDescription($locale->getLocale('sgHourDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsHour";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgHourStat";
	}

} /*End of class HitsHour*/

/**
 * Creates a stat of the hits for every day og the week.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsWeek extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsWeek");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('week'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(0);

		$weekDays = $locale->getLocale('weekDays');
		/*w = day of week 0-6*/
		$this->setEmphasize($weekDays[date('w')]);

		if ($dataSource != NULL) {
			$wCounts = $dataSource->getLineAsArray(15);
		}
		
		$wdays = $locale->getLocale('weekDays');
		if ($locale->getLocale('weekStarts') === 1)
		{ /*Monday is first*/
			$sunday = array_shift($wdays);
			$wdays[] = $sunday;
			$sundayCnt = array_shift($wCounts);
			$wCounts[] = $sundayCnt;
		}
		$this->setNumberArray($wCounts);
		$this->setTextArray($wdays);

		$this->setDescription($locale->getLocale('sgWeekDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsWeek";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgWeekStat";
	}

} /*End of class HitsWeek*/

/**
 * Creates a stat of the users top domains.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsTopdomain extends GraphStatGenerator
{
	/**
	 * Inits the object.
	 *
	 * @param $locale an instance of the <code>Localizer</code>.
	 * @param $dataSource the data source to get the data from.
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsTopdomain");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('topdomain'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(23));
			$this->setTextArray($dataSource->getLineAsArray(22));
		}

		$this->setDescription($locale->getLocale('sgTopdomainDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsTopdomain";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgTopdomainStat";
	}

} /*End of class HitsTopdomain*/

/**
 * Creates a stat of the users domains.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsDomain extends GraphStatGenerator
{
	/**
	 * Inits the object.
	 *
	 * @param $locale an instance of the <code>Localizer</code>.
	 * @param $dataSource the data source to get the data from.
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsDomain");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('domain'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(21));
			$this->setTextArray($dataSource->getLineAsArray(20));
		}

		$this->setDescription($locale->getLocale('sgDomainDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsDomain";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgDomainStat";
	}

} /*End of class HitsDomain*/


/**
 * Creates a stat of the users browsers.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsBrowser extends GraphStatGenerator
{
	/**
	 * Inits the object.
	 *
	 * @param $locale an instance of the <code>Localizer</code>.
	 * @param $dataSource the data source to get the data from.
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsBrowser");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('browser'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(25));
			$this->setTextArray($dataSource->getLineAsArray(24));
		}

		$this->setDescription($locale->getLocale('sgBrowserDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsBrowser";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgBrowserStat";
	}

} /*End of class HitsBrowser*/

/**
 * Creates a stat of the makers of the users browsers.
 * This is basically the ordinary HitsBrowser stat, grouped into e.g.
 * Opera, Mozilla, Safari and a few minor makers etc.
 */
class HitsBrowserMaker extends GraphStatGenerator {

	/**
	 * Inits the object.
	 *
	 * @param $locale an instance of the <code>Localizer</code>.
	 * @param $dataSource the data source to get the data from.
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsBrowserMaker");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('browser'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(25));
			$this->setTextArray($dataSource->getLineAsArray(24));
		}
		
		//Transform the data.

		//We will hard code the categories - sorry.
		//Each inner array represents a maker. Index 0 of this is the label - sorry.
		//All values but the label MUST be lower case!!
		$schema = array(
			array('Opera', 'opera'),
			array('Mozilla', 'mozilla', 'netscape', 'firefox', 'galeon', 'iceweasel', 'iceape'),
			array('Microsoft', 'msie', 'microsoft', 'frontpage'),
			array('Safari', 'safari'),
			array('Google', 'chrome',  'chromium'),
			array('Konqueror', 'konqueror')
			);
		//Summarize the data.
		$this->summarize($schema);
		$this->setDescription($locale->getLocale('sgBrowserMakerDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsBrowserMaker";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgBrowserMakerStat";
	}

} /*End of class HitsBrowserMaker */
 
/**
 * Creates a stat of the users operating system.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsOs extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsOs");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('os'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(27));
			$this->setTextArray($dataSource->getLineAsArray(26));
		}

		$this->setDescription($locale->getLocale('sgOsDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsOs";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgOsStat";
	}

} /*End of class HitsOs*/

/**
 * Creates a stat of the users operating system, summarized into the makers
 * of the OSes.
 */
class HitsOsMaker extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsOsMaker");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('os'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(27));
			$this->setTextArray($dataSource->getLineAsArray(26));
		}
		
		//We will hard code the categories - sorry.
		//Each inner array represents a maker. Index 0 of this is the label - sorry.
		//All values but the label MUST be lower case!!
		$schema = array(
			array('Linux', 'linux'),
			array('Unix', 'unix'),
			array('Apple', 'macintosh', 'appel', 'mac'),
			array('Microsoft', 'win', 'windows')
			);
		//Summarize the data.
		$this->summarize($schema);

		$this->setDescription($locale->getLocale('sgOsMakerDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsOsMaker";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgOsMakerStat";
	}

} /*End of class HitsOsMaker*/

/**
 * Creates a stat of the users languages.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsLanguage extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsLanguage");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('lang'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource !== NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(30));
			$this->setTextArray($dataSource->getLineAsArray(29));
		}

		//Convert the cuntry codes into readable text

		if ($dataSource != NULL) {
			$this->setDescription($locale->getLocale('sgLangDesc'));
		}

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Sets an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * This method overwrites the implemention in the super class to provide
	 * transformation from internal to display text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $textArray the text array for this stat.
	 */
	function setTextArray($textArray)
	{
		//Convert the language codes to real languages.
		$languages = $this->locale->getLocale('languages');

		$otherLangs = $this->locale->getLocale('otherLangs');
		for ($i = 0; $i < sizeof($textArray); $i++)
		{
			if (isset($textArray[$i]) and array_key_exists($textArray[$i], $languages))
				$lang = $languages[$textArray[$i]];
			else
				$lang = '';

			if (strlen($lang) > 0)
				$textArray[$i] = $lang;
			else if (strlen($textArray[$i]) > 0)
				$textArray[$i] = $otherLangs." (".$textArray[$i].")";
		}
		
		//Now set the values in the super class method
		GraphStatGenerator::setTextArray($textArray);
	}
	
	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsLanguage";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgLangStat";
	}

} /*End of class HitsLanguage*/

/**
 * Creates a stat of the monitor resolutions.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsResolution extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsResolution");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('resolution'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(32));
			$this->setTextArray($dataSource->getLineAsArray(31));
		}

		$this->setDescription($locale->getLocale('sgResDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsResolution";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgResStat";
	}

} /*End of class HitsResolution*/

/**
 * Creates a stat of the numbers of supported colors.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsColor extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsColor");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('colours'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(34));
			$this->setTextArray($dataSource->getLineAsArray(33));
		}

		$this->setDescription($locale->getLocale('sgColoursDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsColor";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgColoursStat";
	}

} /*End of class HitsColor*/

/**
 * Creates a stat of the clients which supports Java (not Java-script).
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsJava extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsJava");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('enabledJava'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//Text 35, numbers 36
		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(36));
			$this->setTextArray($dataSource->getLineAsArray(35));
		}

		$this->setDescription($locale->getLocale('sgJavaDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}
	
	/**
	 * Sets an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * This method overwrites the implemention in the super class to provide
	 * transformation from internal to display text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $textArray the text array for this stat.
	 */
	function setTextArray($textArray)
	{
		//Convert true/false to local yes/no
		for ($i = 0; $i < sizeof($textArray); $i++)
		{
			$val = $this->lib->toBool($textArray[$i]);
			if ($val == 1)
				$textArray[$i] = $this->locale->getLocale('yes');
			elseif ($val == 0)
				$textArray[$i] = $this->locale->getLocale('no');
			else
				$textArray[$i] = $this->locale->getLocale('dontKnow');
		}
		
		//Now set the values in the super class method
		GraphStatGenerator::setTextArray($textArray);
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsJava";
	}
	
	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgJavaStat";
	}

} /*End of class HitsJava*/

/**
 * Creates a stat of the counters.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsCounter extends UrlGraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("Counters");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('counters'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//Text 35, numbers 36

		$this->setNumberArray($dataSource->getLineAsArray(37));

		$urlArray = $dataSource->getLineAsArray(38);
		if (strlen($urlArray[0]) === 0)
			$urlArray[0] = $locale->getLocale('sgStdCounter');
		$this->setUrlArray($urlArray);
		$this->setDescription(
			sprintf($locale->getLocale('sgCountersDesc'),
				$locale->getLocale('sgStdCounter')));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsCounter";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgCountersStat";
	}

} /*End of class HitsCounter*/

/**
 * Creates a stat of the clients which supports Java-script.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsJavaScript extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function init()
	{
		$this->setName("HitsJavaScript");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('enabledJavaScript'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//JAVA-script enabled text 39, numbers 40
		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(40));
			$this->setTextArray($dataSource->getLineAsArray(39));
		}

		$this->setDescription($locale->getLocale('sgJavaScriptDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Sets an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * This method overwrites the implemention in the super class to provide
	 * transformation from internal to display text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $textArray the text array for this stat.
	 */
	function setTextArray($textArray)
	{
		//Convert true/false to local yes/no
		for ($i = 0; $i < sizeof($textArray); $i++)
		{
			$val = $this->lib->toBool($textArray[$i]);
			if ($val == 1)
				$textArray[$i] = $this->locale->getLocale('yes');
			elseif ($val == 0)
				$textArray[$i] = $this->locale->getLocale('no');
			else
				$textArray[$i] = $this->locale->getLocale('dontKnow');
		}
		
		//Now set the values in the super class method
		GraphStatGenerator::setTextArray($textArray);
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsJavaScript";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgJavaScriptStat";
	}

} /*End of class HitsJavaScript*/

/**
 * Creates a stat of the reference pages
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HitsReferer extends StatGenerator
{

	function &generateStat()
	{
		$this->setName("HitsReferer");

		//Get the locale object
		$locale = &$this->siteContext->getLocalizer();

		//Create a table
		$list = $this->siteGenerator->newElement("list");

		$headline = $this->siteGenerator->newElement("headline");
		$headline->setSize(2);
		$headline->setHeadline($locale->getLocale($this->getHeadlineKey()));
		$list->addHeadElement($headline);

		$description = $this->siteGenerator->newElement("text");
		$description->addText($locale->getLocale("sgRefererDesc"));
		$list->addHeadElement($description);

		//Format: url;;hits
		$refMixed = $this->dataSource->getLineAsArray(46);
		$urlArray = array();
		$hitArray = array();
		for ($i = 0; $i < sizeof($refMixed); $i++)
		{
      if (! isset($refMixed[$i]) or strlen($refMixed[$i]) == 0) {
        continue; //Skip empty entries
      }
			list($urlArray[$i], $hitArray[$i]) = explode(";;", $refMixed[$i]);
		}

		array_multisort($hitArray, SORT_NUMERIC, SORT_DESC, $urlArray, SORT_ASC, SORT_STRING);

		for ($i = 0; $i < sizeof($hitArray); $i++)
		{
			$hiturl = $this->siteGenerator->newElement("hiturl");
			$hiturl->setHits($hitArray[$i]);
			$hiturl->setUrl("http://".$urlArray[$i]);

			$list->addListElement($hiturl);
		}

		return $list;
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsReferer";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgRefererStat";
	}

} /*End of class HitsReferer*/

/**
 * Extention of {@link GraphStatGenerator} to allow creation of stats
 * which have an url as text.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class UrlGraphStatGenerator extends GraphStatGenerator
{
	/**
	 * Sets the <code>$textArray</code> and converts every pice of text
	 * into an url using instances of {@link urlWrapper}.
	 * If the text array is set before this, it is used for texts in the
	 * urls, else the urls are used.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $urlArray the array of text to make urls and set.
	 * @return void
	 */
	function setUrlArray($urlArray)
	{
    $textArray = $this->getTextArray();
    if (! is_array($textArray) or sizeof($textArray) == 0) {
			$textArray = $urlArray;
    }

		//Create wrapper and text objects to use for all the entries to process.
		$urlCutWrapper = new UrlCutWrapper($this->siteContext);
		$urlCutWrapper->setMaxLength(30);
		$urlCutWrapper->setCutProtocol(0);
		$urlCutWrapper->setCutWww(2);
		$urlCutWrapper->setCutSearch(2);
		$urlCutWrapper->setCutUrl(2);

		$text = $this->siteGenerator->newElement("text");
		$text->setParagraph(0); /*Don't put the text in a paragraph*/

		$urlWrapper = $this->siteGenerator->newElement("urlWrapper");
		$urlWrapper->setWrapped($urlCutWrapper);
		$urlCutWrapper->setWrapped($text);

		//Iterate over all entries and process them.
		$resultAray = array();
		$urlForViewing = $this->siteGenerator->isUrlsForViewing();
		for ($i = 0; $i < sizeof($urlArray); $i++)
		{
			if ($urlArray[$i] == $textArray[$i] and ! $this->lib->okurl($urlArray[$i])) {
				//Url and text are equal, and is not a proper url anyway.
				//This will actually be true when displaying old counter names.
				$resultAray[$i] = $urlArray[$i];
			} else {
				//Make an url with link and text.
				$text->setText($textArray[$i]);
				$urlWrapper->setTitle($urlArray[$i]);
				if (! $urlForViewing)
					$url = "redir.php?url=".urlencode($urlArray[$i]);
				else
					$url = $urlArray[$i];
				$urlWrapper->setUrl($url);
				$resultAray[$i] = $urlWrapper->getCode();
			}
		}

		$this->setTextArray($resultAray);
	}

} /*End of class UrlGraphStatGenerator*/

/**
 * Creates a stat of the entry urls.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsEntryUrls extends UrlGraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function init()
	{
		$this->setName("HitsEntryUrls");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('sgEntryUrl'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);
		$this->setEmphasize("");

		//112-Entry urls, 113-Hits
		$this->setNumberArray($dataSource->getLineAsArray(113));
		$this->setUrlArray($dataSource->getLineAsArray(112));

		$this->setDescription($locale->getLocale('sgEntryDesc'));
		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsEntryUrls";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgEntryStat";
	}

} /*End of class HitsEntryUrls*/

/**
 * Creates a stat of the exit urls.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsExitUrls extends UrlGraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function init()
	{
		$this->setName("HitsExitUrls");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('sgExitUrl'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);
		$this->setEmphasize("");

		//114-Url, 115-hits
		$this->setNumberArray($dataSource->getLineAsArray(115));
		$this->setUrlArray($dataSource->getLineAsArray(114));

		$this->setDescription($locale->getLocale('sgExitDesc'));
		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsExitUrls";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgExitStat";
	}

} /*End of class HitsExitUrls*/

/**
 * Creates a stat of the movements to and from pages.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsMovements extends GraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function init()
	{
		$this->setName("HitsMovements");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('sgMovePages'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//Movements 74, hits 75
		$this->setNumberArray($dataSource->getLineAsArray(75));
		$this->setTextArray($dataSource->getLineAsArray(74));

		$this->setDescription($locale->getLocale('sgMoveDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsMovements";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgMoveStat";
	}

} /*End of class HitsMovements*/

/**
 * Creates a stat of the click counters.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsClickCounter extends UrlGraphStatGenerator
{

	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function init()
	{
		$this->setName("ClickCounters");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('sgClicks'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//69- hits
		//70- names
		//71- URLs
		$this->setNumberArray($dataSource->getLineAsArray(69));

		//Put the counter no. in front of the names
		$names = $dataSource->getLineAsArray(70);
		for ($i = 0; $i < sizeof($names); $i++)
		{
			if (strlen($names[$i]) > 0)
				$names[$i] = $i." ".$names[$i];
			else
				$names[$i] = $i." ".$locale->getLocale('sgNoName');
		} /*End for*/
		$this->setTextArray($names);

		$this->setUrlArray($dataSource->getLineAsArray(71));

		$this->setDescription($locale->getLocale('sgClickCountDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsClickCounter";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgClickCountStat";
	}

} /*End of class HitsClickCounter*/

/**
 * Creates a stat of the search keywords the visitors have used to find
 * the page.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsSearchWord extends GraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function init()
	{
		$this->setName("HitsSearchWord");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('sgSearchWord'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//Search words 47, hits 48
		if ($dataSource != NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(48));
			$this->setTextArray($dataSource->getLineAsArray(47));
		}

		$this->setDescription($locale->getLocale('sgSearchWordDesc'));

		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Sets an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * This method overwrites the implemention in the super class to provide
	 * transformation from internal to display text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $textArray the text array for this stat.
	 */
	function setTextArray($textArray)
	{
		//Clean known bad chars.
		$textArray = str_replace(array("æ", "ø", "å"), array("", "", ""), $textArray);
		
		//Now set the values in the super class method
		GraphStatGenerator::setTextArray($textArray);
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsSearchWord";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgSearchWordStat";
	}

} /*End of class HitsSearchWord*/

/**
 * Creates a stat of the search engines the visitors have used to find
 * the page.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsSearchEngines extends GraphStatGenerator
{
	/**
	 * Init the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function init()
	{
		$this->setName("HitsSearchEngines");

		$locale = &$this->locale;
		$dataSource = &$this->dataSource;

		$this->setHeaderArray(array(
			$locale->getLocale('sgSearchEngine'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
			));

		$this->setSort(1);

		$this->setEmphasize("");

		//Search engins 49, numbers 50
		if ($dataSource !== NULL) {
			$this->setNumberArray($dataSource->getLineAsArray(50));
			$this->setTextArray($dataSource->getLineAsArray(49));
		}

		$this->setDescription($locale->getLocale('sgSearchEngineDesc'));
		$this->setMainHeadline($locale->getLocale($this->getHeadlineKey()));
	}

	/**
	 * Sets an array containing the text for this stat.
	 * The indexes should match the indexes in the array returned by
	 * {@link #getNumberArray}.
	 *
	 * This method overwrites the implemention in the super class to provide
	 * transformation from internal to display text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $engineIds the text array for this stat.
	 */
	function setTextArray($engineIds)
	{
		$engines = new SearchEngines();

		$text = $this->siteGenerator->newElement("text");
		$text->setParagraph(0); /*Don't put the text in a paragraphw*/

		$urlWrapper = $this->siteGenerator->newElement("urlWrapper");

		//Translate the engine ids to urls and names.
		$urlForViewing = $this->siteGenerator->isUrlsForViewing();
		for ($i = 0; $i < sizeof($engineIds); $i++)
		{
			$engText = "";
			$engineUrls = $engines->getUrls($engineIds[$i]);
			$engineNames = $engines->getNames($engineIds[$i]);

			for ($n = 0; $n < sizeof($engineUrls); $n++)
			{
				$text->setText($engineNames[$n]);
				$urlWrapper->setWrapped($text);
				$urlWrapper->setTitle($engineUrls[$n]);
				if (! $urlForViewing)
					$url = "redir.php?url=".urlencode($engineUrls[$n]);
				else
					$url = $engineUrls[$n];
				$urlWrapper->setUrl($url);
				if ($n > 0)
					$engText .= " / ";
				$engText .= $urlWrapper->getCode();
			} /*End for $n...*/
			$engineIds[$i] = $engText;
		} /*End for $i...*/

		//Now set the values in the super class method
		GraphStatGenerator::setTextArray($engineIds);
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsSearchEngines";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgSearchEngineStat";
	}

} /*End of class HitsSearchEngines*/

/**
 * Creates a stat of each question and anwsers.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsVotes extends StatGenerator
{

	/**
   * Creates a new instance.
   *
   * @public
	 * @version 0.0.1
	 * @since 0.0.1
   */
	function __construct(&$siteContext, &$siteGenerator) {
		parent::__construct($siteContext, $siteGenerator);
		$this->setName("Votes");
	}

	/**
	 * Fills in the {@link SiteGenerator} so it is ready to generate
	 * code.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return SiteElement
	 */
	function &generateStat()
	{

		if (strtolower($this->siteGenerator->getParentClass()) != "sitegenerator")
		{ /*It's not a SiteGenerator*/
			echo "<b>Error:</b> The class <code>HitsVotes</code> only acepts instances of <code>SiteGenerator</code>.<br>";
			exit;
		}

		//Get the locale object
		$locale = &$this->siteContext->getLocalizer();

		/*41: Questions :: separated
		  42: Anwsers main groups ,, separated; each anwser in group
		      separated by ::. A group of anwsers corresponds to a question.
		  43: Hits for each anwser, separated as line 42.

		  Example:
		  41:This is question A?::This is question B?::This is question C?
		  42:Anwser 1 for question A.::Ans 2 for q A.,,Ans 1 for q B.::Ans 2 for q B.,,Ans 1 for q C.::Ans 2 for q C.::Ans 3 for q C.
		  43:12::23,,45::56,,67::78::89
		*/

		//The main container it all is put in
		$mainContainer = $this->siteGenerator->newElement("text");
		$mainContainer->setParagraph(0); /*Don't put the text in a paragraph*/

		//Create the headline
		$headline = $this->siteGenerator->newElement("headline");
		$headline->setSize(2);
		$headline->setHeadline($locale->getLocale($this->getHeadlineKey()));
		$mainContainer->addHeadElement($headline);

		$lib = &$this->siteContext->getCodeLib();
		$dataSource = &$lib->getDataSource();

		$qtnArr = explode("::", $dataSource->getLine(41));
		$ansArr = explode(",,", $dataSource->getLine(42));
		$hitArr = explode(",,", $dataSource->getLine(43));

		if (sizeof($qtnArr) === 0 or (sizeof($qtnArr) === 1 and strlen($qtnArr[0]) === 0))
		{
			$noPolls = $this->siteGenerator->newElement("text");
			$noPolls->addText($locale->getLocale('sgNoVotes'));
			$mainContainer->addHeadElement($noPolls);
		}
		//else

		for ($i = 0; $i < sizeof($qtnArr); $i++)
		{
			$table = $this->createTable($locale, $qtnArr[$i], $ansArr[$i], $hitArr[$i]);
			$table->setElementName($this->getName());
			$mainContainer->addHeadElement($table);
		} /*End for i (all questions)*/

		return $mainContainer;
	}

	/**
	 * Creates and returns a table representing a question/answer.
	 * This method should only be called from within this class.
	 *
	 * @private
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $locale the {@link Localizer} to use
	 * @param $questionStr the question as a stirng.
	 * @param $answers the answers separated by <code>::</code>s.
	 * @param $hits the hits separated by <code>::</code>s.
	 * @return SiteTable a table representing a question/answer
	 */
	function createTable(&$locale, $questionStr, $answers, $hits)
	{
		//if (strlen($questionStr) === 0)
		//	continue;

		//Create graph
		$table = $this->siteGenerator->newElement("graphTable");
		$table->setElementClass("stattabel");
		//Create the description
		$question = $this->siteGenerator->newElement("text");
		$question->setParagraph(1);
		$question->addText($questionStr);
		$table->addHeadElement($question);
		//Create the question list
		$aList = $this->siteGenerator->newElement("list");
		$aSiteElements = array();
		$aStrs = explode("::", $answers);
		$cutWrapper = new CutWrapper($this->siteContext);
		$cutWrapper->setMaxLength(25);
		for ($n = 0; $n < sizeof($aStrs); $n++)
		{
			$aSiteElements[$n] = $this->siteGenerator->newElement("text");
			$aSiteElements[$n]->setParagraph(0);
			$aSiteElements[$n]->setText(($n+1)." ".$aStrs[$n]);
			$cutWrapper->setWrapped($aSiteElements[$n]);
			$aStrs[$n] = $cutWrapper->getCode();
			$aList->addListElement($aSiteElements[$n]);
		}
		$table->addHeadElement($aList);

		//Create the graph and table
		$table->setEmphasize("");
		$table->setShowNumbers(1);
		$table->setSorted(1);
		$table->setTextArray($aStrs);
		$table->setNumArray(explode("::", $hits));
		$table->setHeaderArray(array(
			$locale->getLocale('sgQuestion'),
			$locale->getLocale('hits'),
			$locale->getLocale('percent'),
			$locale->getLocale('graph')
		));
		return $table;
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsVotes";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgVoteStat";
	}

} /*End of class HitsVotes*/

/**
 * Creates a stat over the latest visitors.
 * <p><b>File:</b> StatGenerator.php</p>
 *
 * @public
 * @version 0.0.1
 * @since 0.0.1
 */
class HitsLatestsVisits extends StatGenerator
{

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function __construct(&$siteContext, &$siteGenerator) {
		parent::__construct($siteContext, $siteGenerator);
		$this->setName("LatestsVisits");
	}

	/**
	 * Fills in the {@link SiteGenerator} so it is ready to generate
	 * code.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return SiteElement
	 */
	function &generateStat()
	{

		if (strtolower($this->siteGenerator->getParentClass()) != "sitegenerator")
		{ /*It's not a SiteGenerator*/
			echo "<b>Error:</b> The class <code>HitsVotes</code> only acepts instances of <code>SiteGenerator</code>.<br>";
			exit;
		}

		//Get the locale object
		$dataSource = &$this->dataSource;
		$locale = &$this->locale;

		//Create the table
		$table = $this->siteGenerator->newElement("table");
		$table->setHeadersAre(1); /*The top row is headers.*/
		$table->setHeaderClass('sinfo');

		//Create headline and description
		$headline = $this->siteGenerator->newElement("headline");
		$headline->setSize(2);
		$headline->setHeadline($locale->getLocale('sgLatestStat'));
		$table->addHeadElement($headline);

		$desc = $this->siteGenerator->newElement("text");
		$desc->setParagraph(1);
		$desc->setText($locale->getLocale('sgLatestDesc'));
		$table->addHeadElement($desc);
		$table->setElementName($this->getName());
		$table->setElementClass('stattabel');

		//Create the headlines for the columns
		       //column, row
		$tableContent[0][0] = $locale->getLocale('sgLatestTime');
		$tableContent[0][1] = $locale->getLocale('sgLatestBrowser');
		$tableContent[0][2] = $locale->getLocale('sgLatestOs');
		$tableContent[0][3] = $locale->getLocale('sgLatestRes');
		$tableContent[0][4] = $locale->getLocale('sgLatestColours');
		$tableContent[0][5] = $locale->getLocale('sgLatestLang');
		$tableContent[0][6] = $locale->getLocale('sgLatestPage');
		$tableContent[0][7] = $locale->getLocale('sgLatestRefpage');
		// $tableContent[0][8] = $locale->getLocale('sgLatestDomain');
		// $tableContent[0][9] = $locale->getLocale('sgLatestIp');

		//Set the class name for the columns
		$columnClass = array_fill(0, sizeof($tableContent[0]), 'sinfo');
/*		for ($i = 0; $i < sizeof($tableContent); $i++)
			$columnClass[] = 'sinfo';
*/
		$table->setColumnClassArray($columnClass);

		//Fill in the data
		/*Line 28:
			Each row separated by ::
			Each pice of data separated by ;;
			Order of pices:
			0: Browser
			1: Os
			// 2: IP-adr - empty as of GDPR
			// 3: Domain - empty as of GDPR
			4: Time
			5: Screen resolution
			6: Screen color depth (in bits)
			7: Language (iso code)
			8: Url of page with hit
			9: Referer url

			Display in the order:
			4, 0, 1, 2, 3, 5, 6, 7, 8, 9
		*/

		$displayOrder = array(4, 0, 1, 5, 6, 7, 8, 9);

		$rows = $dataSource->getLineAsArray(28);

		$urlCutWrapper = new UrlCutWrapper($this->siteContext);
		$urlCutWrapper->setMaxLength(25);
		$urlCutWrapper->setCutProtocol(0);
		$urlCutWrapper->setCutWww(2);
		$urlCutWrapper->setCutSearch(2);
		$urlCutWrapper->setCutUrl(2);

		$text = $this->siteGenerator->newElement("text");
		$text->setParagraph(0); /*Don't put the text in a paragraph*/

		$urlWrapper = $this->siteGenerator->newElement("urlWrapper");
		$urlWrapper->setWrapped($urlCutWrapper);
		$urlCutWrapper->setWrapped($text);
		
		//Set up cutting and presentation of domain
		$domainCutWrapper = new CutWrapper($this->siteContext);
		$domainCutWrapper->setMaxLength(15);
		$domainText = $this->siteGenerator->newElement("text");
		$domainText->setParagraph(0);
		$domainLabel = $this->siteGenerator->newElement("text");
		$domainLabel->setParagraph(-1);
		$domainCutWrapper->setWrapped($domainText);
		$domainCutWrapper->setCutFrom(1);

		//Set up parser for converting dates
		$dateParser = new LegacyDateParser();
		$dateFormatter = new DateFormatter($locale->getLocale('dateShort'));
		$dateFormatter->setCurrentYear(date("Y"), $locale->getLocale('dateReallyShort'));
		
		$urlForViewing = $this->siteGenerator->isUrlsForViewing();

		$rows = array_reverse($rows);
		for ($i = 0; $i < sizeof($rows); $i++)
		{
			$column = explode(",,", $rows[$i]);
			//Handle non existing data
			if (!isset($column[0]) or $column[0] === "Andre browsere")
				$column[0] = $locale->getLocale('sgLatestNA');
		
			if (! $urlForViewing and isset($column[3])) {
				//For cutting
				$domainText->setText($column[3]);
				
				//For label
				$domainLabel->setLabel($column[3]);
				$domainLabel->setText($domainCutWrapper->getCode());
				$column[3] = $domainLabel->getCode();
			}
			
			$column[4] = $dateParser->parseToView($column[4], $dateFormatter);
			
			$column[1] = str_replace("Windows", "Win", $column[1]);
			if (!isset($column[1]) or $column[1] === "Andre styresystemer")
				$column[1] = $locale->getLocale('sgLatestNA');
				
			if (!isset($column[6]) or $column[6] === "Andre bit")
				$column[6] = $locale->getLocale('sgLatestNA');
			if (!isset($column[7]) or $column[7] === "Andet (Andre)")
				$column[7] = $locale->getLocale('sgLatestNA');

			if (isset($column[6]) and $column[6] !== "")
				$column[6] .= " ".$locale->getLocale('sgLatestBit');

			for ($m = 8; $m <= 9; $m++)
			{
				if (strlen($column[$m]) > 0)
				{
					//Convert urls to links
					if ($this->lib->okUrl($column[$m]))
					{
						$text->setText($column[$m]);
						$urlWrapper->setTitle($column[$m]);
						if (! $urlForViewing)
							$url = "redir.php?url=".urlencode($column[$m]);
						else
							$url = $column[$m];
						$urlWrapper->setUrl($url);
						$column[$m] = $urlWrapper->getCode();
					}
				}
				else
				{
					$column[$m] = $locale->getLocale('sgLatestNA');
				}
			} /*End for $m = 8*/

			//Translate the language, if known
			$languages = $locale->getLocale('languages');
			if (isset($languages[$column[7]]) and strlen($languages[$column[7]]) > 0)
				$column[7] = $languages[$column[7]];
			else
				$column[7] = $locale->getLocale('sgLatestOthers')." (".$column[7].")";

			//Map between place in data and display
			for ($j = 0; $j < sizeof($displayOrder); $j++)
				$tableContent[$i+1][$j] = $column[$displayOrder[$j]];

		} /*End for $i*/

		$table->setTableContent($tableContent);
		return $table;
	}

	/**
	 * Returns a string which identifies this stat. This is always the same
	 * for this class, and can / shall not be set line in {@link #getName}.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String an identifier of this stat.
	 */
	function getIdentifier()
	{
		return "HitsLatestsVisits";
	}

	/**
	 * Returns the key used to get the headline from the {@link Localizer}.
	 *
	 * @public
	 * @version 0.0.1
	 * @return String the key used to get the headline from the {@link Localizer}.
	 */
	function getHeadlineKey()
	{
		return "sgLatestStat";
	}

} //End of class HitsLatestsVisits

?>
