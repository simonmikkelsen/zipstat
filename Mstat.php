<?php

/**
 * Handles summery of stats.
 */
class Mstat
{

	/**
	 * Instance of Datasource, for saving data.
	 */
	var $datasource;
	
	/**
	 * The instance of the code lib (Html).
	 */
	var $lib;
	
	/**
	 * States if the function is enabled (1) or not (0).
	 */
	var $enabled;

	function Mstat(&$lib) {
		$this->lib = &$lib;
		$this->datasource = &$lib->getDataSource();
		$options = $this->lib->getStier();
		$this->enabled = $options->getOption('reg_mstats');
	}
	
	/**
	 * In the stat represented by the $statNumber, the $index is counted up.
	 * 
	 * @param $statNumber the stat to count up.
	 * @param $index      the index in the stat to count up.
	 * @returns void
	 */
	function mstat_denneop($statNumber, $index) {
		if ($this->enabled !== 1)
			return;

		$statData = explode("::", $this->datasource->getLine($statNumber));
		if (isset($statData) and isset($statData[$index])) {
			$statData[$index]++;
		} else {
			$statData = Html::addZeros($index,$statData);
			$statData[$index] = 1;
		}
		$this->datasource->setLine($statNumber, implode("::", $statData));
	}
	
	/**
	 * Alias for ZipStatEngine::stringstat with the modification that it
	 * only modifies data when this class is enabled (using the
	 * Mstat::enabled attribute).
	 * 
	 * @param $newText       the new text element.
	 * @param $textIndex
	 * @param $numbersIndex
	 * @return array of the new text and numbers.
	 * @returns String[]
	 */
	function stringstat($newText, $textIndex, $numbersIndex) {
		if ($this->enabled === 1) {
			$this->datasource->setLines(
					ZipStatEngine::stringstat(
						$newText,
						$this->datasource->getLine($textIndex),
						$this->datasource->getLine($numbersIndex)
					),
			$textIndex, $numbersIndex);
			//return ZipStatEngine::stringstat($newText,$existingText,$numbers);
		} else {
			return array($textIndex, $numbersIndex);
		}
	}
	
	/**
	 * Sets a (new) data source to use on this object and objects created by
	 * this object.
	 * 
	 * @param $datasource the data source to use.
	 * @returns void
	 */
	function setDataSource(&$datasource) {
		$this->datasource = $datasource;
	}

}

?>