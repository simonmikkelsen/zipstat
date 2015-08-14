<?php
require_once(dirname(__FILE__)."/../../Html.php");
require_once(dirname(__FILE__)."/../testcase.php");

/**
 * Tests the class DateFormatter.
 */
class ArrayRotateTest extends TestCase{

	/**
	 * Tests this trick for switching weeks in one line.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		
		//Take 'sun' and move it to the end of the array.
		//This test ensures that this works on future PHP implementations.
		$weeks = array('sun', 'mon', 'tue');
		//$sun = array_shift($weeks);
		//$weeks[count($weeks)] = $sun;
		
		Html::array_rotate($weeks);
		
		if ($weeks[0] !== 'mon' or $weeks[1] !== 'tue' or $weeks[2] !== 'sun') {
			echo "\nMust be mon, tue, sun.\n";
			print_r($weeks);
			return false;
		} else
			return true;
	}
	
	/**
	 * Returns the name of the test.
	 * 
	 * @public
	 * @return Returns the name of the test.
	 */
	function getName() {
		return "ArrayRotate";
	}
}

?>