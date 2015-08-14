<?php
require_once(dirname(__FILE__)."/../../Html.php");
require_once(dirname(__FILE__)."/../testcase.php");

/**
 * Tests the class DateFormatter.
 */
class DateFormatterTest extends TestCase{

	/**
	 * Tests the class DateFormatter.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		$df = new DateFormatter("d-m Y");
		$df->disableCurrentYear();
		
		if (date("d-m Y") != $df->format(time())) {
			echo "Failed format d-m Y";
			return false;
		}
		
		$df->setCurrentYear(date("Y"), "d-m");
		
		if (date("d-m") != $df->format(time())) {
			echo "Failed format d-m";
			return false;
		}

		//Now it is last year
		$lastYear = time() - 366*24*3600;
		if (date("d-m Y", $lastYear) != $df->format($lastYear)) {
			echo "Failed format d-m Y using current year";
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
		return "DateFormatter";
	}
}

?>