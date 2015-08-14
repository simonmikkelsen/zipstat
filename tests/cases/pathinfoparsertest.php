<?php
require_once(dirname(__FILE__)."/../../Html.php");
require_once(dirname(__FILE__)."/../testcase.php");
require_once(dirname(__FILE__)."/../../lib/ZipStatEngine.php");

/**
 * Tests the method {@link Html::getDateFromPathinfo}.
 */
class PathInfoParserTest extends TestCase {

	/**
	 * Tests the method {@link Html::getDateFromPathinfo}.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		if (! count(Html::getDateFromPathinfo('')) === 0) {
			echo "Array must be empty but is not.";
			return false;
		}
		if (! count(Html::getDateFromPathinfo('/')) === 0) {
			echo "Array must be empty but is not (2).";
			return false;
		}
		
		/* @li @c year - contains the parsed year.
		 * @li @c month - contains the parsed month or <code>1</code>.
		 * @li @c day - the day or <code>1</code>.
		 * @li @c time - the unix time stamp of the parsed time. Currently for
		 *               midnight at the given day.
		 * @li @c misc - 0 indexed array of non date parameters.
		 */
		//echo strtotime('Mar 1 2007 00:00:00')."\n";
		$tests = array();
		$tests[] = array('input' => '/2007/03/26',
		                 'year' => 2007, 'month' => 3, 'day' => 26,
		                 'time' => 1174860000, 'end' => 1174946399, 'misc' => array());
		$tests[] = array('input' => '/2007/03/26/foo',
		                 'year' => 2007, 'month' => 3, 'day' => 26,
		                 'time' =>1174860000, 'end' => 1174946399, 'misc' => array('foo'));
		$tests[] = array('input' => '/2007',
		                 'year' => 2007, 'month' => 1, 'day' => 1,
		                 'time' =>1167606000, 'end' => 1199141999, 'misc' => array());
		$tests[] = array('input' => '/2007/foo',
		                 'year' => 2007, 'month' => 1, 'day' => 1,
		                 'time' =>1167606000, 'end' => 1199141999, 'misc' => array('foo'));
		$tests[] = array('input' => '/2007/foo/03',
		                 'year' => 2007, 'month' => 3, 'day' => 1,
		                 'time' =>1172703600, 'end' => 1175378399, 'misc' => array('foo'));

		$tests[] = array('input' => '/2007/03/26/',
		                 'year' => 2007, 'month' => 3, 'day' => 26,
		                 'time' => 1174860000, 'end' => 1174946399, 'misc' => array());
		$tests[] = array('input' => '/2007/03/26/foo/',
		                 'year' => 2007, 'month' => 3, 'day' => 26,
		                 'time' =>1174860000, 'end' => 1174946399, 'misc' => array('foo'));
		$tests[] = array('input' => '/2007//',
		                 'year' => 2007, 'month' => 1, 'day' => 1,
		                 'time' =>1167606000, 'end' => 1199141999, 'misc' => array());
		$tests[] = array('input' => '/2007/foo/',
		                 'year' => 2007, 'month' => 1, 'day' => 1,
		                 'time' =>1167606000, 'end' => 1199141999, 'misc' => array('foo'));
		$tests[] = array('input' => '/2007/foo/03/',
		                 'year' => 2007, 'month' => 3, 'day' => 1,
		                 'time' =>1172703600, 'end' => 1175378399, 'misc' => array('foo'));

		//The following was a real failure: With an ending / the function
		//interpretated it as /2007/0.
		$tests[] = array('input' => '/2007/',
		                 'year' => 2007, 'month' => 1, 'day' => 1,
		                 'time' =>1167606000, 'end' => 1199141999, 'misc' => array());
		$tests[] = array('input' => '/2007/99/88');

		foreach ($tests as $test) {
			if (!$this->isOK(Html::getDateFromPathinfo($test['input']), $test)) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Analyzes the given arrays to see if the content is as expected.
	 * The @c input key is ignored and only used for debug printing.
	 *
	 * @param $result the output from the tested method.
	 * @param $expected the expected result.
	 * @return if the parameters are equal for the relevant parameters.
	 */
	function isOK($result, $expected) {
		$keys = array('year', 'month', 'day', 'time', 'end', 'misc');
		foreach ($keys as $key) {
			switch ($key) {
				case 'misc':
					$diff = @array_diff($expected[$key], $result[$key]);
					if (count($diff) > 0) {
						echo "The misc array differes: ";
						print_r($diff);
						return false;
					}
					break;
				default:
					//The @'s are to avoid notices about undefined indecies.
					if (@$result[$key] !== @$expected[$key]) {
						echo "Mismatched values: Expected [".$expected[$key]. "] got [".$result[$key]."] for the input: ".$expected['input'];
		echo "\nRes: Time: ".date('r', $result['time'])."; End: ".date('r', $result['end'])."\n";
						print_r($result);
						return false;
					}
				break;
			} //End switch.
		} //End foreach.
		return true;
	}
	
	/**
	 * Returns the name of the test.
	 * 
	 * @public
	 * @return Returns the name of the test.
	 */
	function getName() {
		return "PathInfoParser";
	}
}

?>