<?php

require_once(dirname(__FILE__)."/../testcase.php");
require_once(dirname(__FILE__)."/../../lib/StatMail.php");

/**
 * Extended by classes that performs tests.
 */
class EventCalculatorTest extends TestCase {
	/**
	 * Runs the test cases.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		//Test that strtotime() works as expected (the date is tryls time of birth).
		if (strtotime("12 Jan 1980 13:35") !== 316528500) {
			echo "strtotime does not seem to work properly.";
			return false;
		}
		
		//Make test data.
		$latest = array();
		$now = array();
		$schedule = array();
		$result = array();
		
		$latest[] = "8 Mar 2006 21:33"; //Wed
		$now[] = "11 Mar 2006 21:33"; //Sat
		$schedule[] = array("11;;22", "sun;;10");
		$result[] = false;
		
		//Make a lot of data.
		for ($i = 0; $i <= 32; $i++) {
			$latest[] = "3 Mar 2006 21:33 + $i days";
			$now[] = "11 Mar 2006 21:33 + $i days";
			$schedule[] = array("11;;22", "sun;;10");
			$result[] = true;
		}
		
		//Go across febuary.
		for ($i = 0; $i <= 320; $i++) {
			$latest[] = "12 Jan 2006 21:33 + $i days";
			$now[] = "21 Jan 2006 21:33 + $i days";
			$schedule[] = array("11;;22", "sun;;10");
			$result[] = true;
		}
		
		//Go across febuary.
		for ($i = 0; $i <= 0; $i++) {
			$latest[] = "20 Jan 2006 21:33 + $i days";
			$now[] = "21 Jan 2006 09:33 + $i days";
			$schedule[] = array("11;;12", "sun;;10");
			$result[] = false;
		}
		
		//Test the class itself.
		for ($i = 0; $i < count($latest); $i++) {
			$eventCalc = new EventCalculator();
			$eventCalc->setCalcTime(strtotime($now[$i]));
			if ($eventCalc->repeatNow(strtotime($latest[$i]), $schedule[$i]) !== $result[$i]) {
				echo "($i) Did not return ".$result[$i]." for ".$latest[$i]." &lt; (".$schedule[$i][0].") &lt; ".$now[$i];
				return false;
			}
		}

		//Real world example that failed, that actually just cought that some
		//other code used true/false while some other used 1/0.
		//Now it's all true/false.
		$eventCalc = new EventCalculator();
		//Now:  Fri, 21 Jul 2006 21:21:35 +0200
		$eventCalc->setCalcTime(1153509695);
		
		//Last: Fri, 21 Jul 2006 12:00:38 +0200
		if ($eventCalc->repeatNow(1153476038, array("hda;;10", "hda;;20", "hda;;21", "hda;;23")) !== true) {
			echo "(adsadsf) Did not return true.";
			return false;
		}
			
		return true;
	}
	
	/**
	 * Returns the name of the test.
	 * 
	 * @public
	 * @return Returns the name of the test.
	 */
	function getName() {
		return "EventCalculator";
	}
}

?>