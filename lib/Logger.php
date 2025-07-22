<?php

class Logger {
	
	/**
	 * The instance of LogDatasource.
	 */
	var $logDatasource;
	
	/**
	 * The instance of Stier.
	 */
	var $options;
	
	/**
	 * The current logline.
	 */
	var $logline;
	
	/**
	 * States if only a single record is to be written
	 * (TRUE) or not (FALSE). If TRUE opening and closing
	 * of the data source will be handled.
	 */
	var $singleLine;
	
	/**
	 * An instance of ZipStatEngine to use when processing a log file.
	 */
	var $processor;
	
	/**
	 * Creates a new instance.
	 * 
	 * @param $options The instance of Stier.
	 * @param $singleLine states if only a single record is to be written
	 *                    (TRUE) or not (FALSE). If TRUE opening and closing
	 *                    of the data source will be handled.
	 */
	function __construct(&$options, $singleLine = TRUE) {
		$this->options = $options;
		$this->singleLine = $singleLine;
		$this->logline = '';
		$this->logDatasource = new LogDatasource($options);
	}
	
	/**
	 * Marks that the log line has been build and should be written to the
	 * log file.
	 */
	function writeLogLine() {
		if ($this->singleLine)
			$this->logDatasource->openSource();

		$this->logDatasource->addRecord($this->logline);

		if ($this->singleLine)
			$this->logDatasource->closeSource();

		$this->logline = '';
	}
	
	/**
	 * Logs the visit represented by the parameters.
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
	 * @param $username the username to log for
	 * @param $visitorId an id unique to each visitor.
	 * @return void
	 */
	function logVisit($time, $screen_res, $referer,
	                 $colors, $javasupport, $counterNo, $counterName, $jsSupport,
	                 $useragent, $ipAddr, $lang, $url, $username, $visitorId = "") {
		$this->addToLogline($username);
		$this->addToLogline($time);
		$this->addToLogline($url);
		$this->addToLogline($referer);
		$this->addToLogline($ipAddr);
		$this->addToLogline($lang);
		$this->addToLogline($useragent);
		$this->addToLogline($screen_res);
		$this->addToLogline($colors);
		$this->addToLogline($javasupport);
		$this->addToLogline($jsSupport);
		$this->addToLogline($visitorId);
		$this->addToLogline($counterNo);
		$this->addToLogline($counterName);
	}
	
	/**
	 * Adds <code>$toAdd</code> to the current logline.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $toAdd the one to add.
	 * @returns void
	 */
	function addToLogline($toAdd) {
		$this->logline .= "\"".addslashes($toAdd)."\"";
	}
	
	/**
	 * Processes the log using the processor set using setProcessor().
	 */
	function doProcess() {
		$processIntoEach = $this->options->getOption('processIntoEach') !== 0;
		$processAllIntoOne = $this->options->getOption('processAllInOne') !== 0;

		//Stores the sources to process for each iteration.
		$processSources = array();
		//Gives the index in above where the user source is/should be located
		//If "All into one" is enabled it should be 1, else 0
		$userSourceIndex = 0;
		
		if ($processIntoEach) {
				//Cache to store "into each" sources in
				$datasources = array();
		}
		
		if ($processAllIntoOne) {
			$allUsername = $this->options->getOption('processAllInOneUsername');
			$allDatasource = DataSource::createInstance($allUsername, $this->options);
			$allDatasource->hentFil();
			$processSources[0] = &$allDatasource;
			$userSourceIndex = 1;
		}

		$this->logDatasource->openForProcessing();
		while (($record = $this->logDatasource->nextRecord()) !== NULL) {
			//Retrive and parse the log data
			$record = substr($record, 1, -1);
			//The @ is there so we don't get 13 warnings about undefined vars.
 			@list($username, $time, $url, $referer, $ipAddr, $lang, $useragent,
 			     $screen_res, $colors, $javasupport, $jsSupport, $visitorId,
 			     $counterNo, $counterName) = explode("\"\"", $record);

 			//Is a data source for the user loaded
 			if (isset($username) and strlen($username) > 0) {
				if (!isset($datasources[$username])) {
					$userSource = DataSource::createInstance($username, $this->options);
					$userSource->hentFil();
					$datasources[$username] =& $userSource;
					$processSources[$userSourceIndex] =& $userSource;
				} else {
					$processSources[$userSourceIndex] =& $datasources[$username];
				}
 			} else {
 				unset($processSources[$userSourceIndex]);
 				$processSources[$userSourceIndex] = NULL;
 			}
 			
 			//Do the processing
 			for ($i = 0; $i < count($processSources); $i++) {
 				if ($processSources[$i] === NULL) {
 					continue;
 				}
				//Set the data source to use
				$this->processor->setDataSource($processSources[$i]);
				//And do the processing
				$this->processor->process($time, $screen_res, $referer,
										 $colors, $javasupport, $counterNo, $counterName, $jsSupport,
										 $useragent, $ipAddr, $lang, $url);
			} //End foreach
		} //End while
		$this->logDatasource->closeSource();

		if ($processAllIntoOne) {
			$processSources[0]->gemFil();
		}
		
		//Close the user data sources.
		if ($processIntoEach) {
			foreach ($datasources as $key=>$source) {
				if ($source === NULL) {
					continue;
				}
				$source->gemFil();
			}
		}
	}

	/**
	 * Sets an instance of ZipStatEngine to use when processing a log file.
	 */
	function setProcessor(&$processor) {
		$this->processor = &$processor;
	}
}

?>