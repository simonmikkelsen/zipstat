<?php
require_once(dirname(__FILE__)."/../testcase.php");
require_once(dirname(__FILE__)."/../../Html.php");
require_once(dirname(__FILE__)."/../../lib/ZipStatEngine.php");

/**
 * Tests the class ZipStatEngine.
 */
class ZipStatEngineTest extends TestCase {

	/**
	 * Tests the class ZipStatEngine.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		//Test the page counter function.
		$dummyLib = NULL; //We actually not needs the lib for this test.
		$max_counters = 10;
		$engine = new ZipStatEngine($dummyLib);
		$engine->counterNo = '';
		$engine->counterName = '';
		$engine->url = '';
		
		//Normal with only urls.
		$cntUrls[0] = 'default';
		$cntVisits[0] = '11';
		
		$cntUrls[1] = 'http://foo.dk/bar/';
		$cntVisits[1] = '22';
		
		$cntUrls[2] = 'http://www.foo.dk/bar/pi.html';
		$cntVisits[2] = '33';
		
		
		$engine->url = 'http://foo.dk/bar/';
		list($cntUrls, $cntVisits) = $engine->applyPageCounter($cntUrls, $cntVisits, $max_counters);
		
		if ($cntVisits[0] != 11 or $cntVisits[1] != 23 or $cntVisits[2] != 33) {
			echo "Failed (".__LINE__."): ".$cntVisits[0]." != 11 or "
			               .$cntVisits[1]." != 23 or ".$cntVisits[2]." != 33";
			return false;
		}
		
		$engine->url = 'http://FOO.dk/bar/';
		list($cntUrls, $cntVisits) = $engine->applyPageCounter($cntUrls, $cntVisits, $max_counters);
		if ($cntVisits[1] != 24) {
			echo "Failed (".__LINE__."): ".$cntVisits[1]." != 24";
			return false;
		}
		
		//Test new url: Should be added as index 3.
		$engine->url = 'http://foo.dk/new/';
		list($cntUrls, $cntVisits) = $engine->applyPageCounter($cntUrls, $cntVisits, $max_counters);
		if ($cntVisits[3] != 1 or $cntUrls[3] != 'http://foo.dk/new/') {
			echo "Failed (".__LINE__."): ".$cntVisits[3]." != 1 or ".$cntUrls[3]." != 'http://foo.dk/new/'";
			return false;
		}
		
		//Test when an url shall match a counter name and convert.
		$cntUrls[4] = 'countername';
		$cntVisits[4] = '44';
		$engine->url = 'http://foo.dk/countername/';
		list($cntUrls, $cntVisits) = $engine->applyPageCounter($cntUrls, $cntVisits, $max_counters);
		if ($cntVisits[4] != 45 or $cntUrls[4] != 'http://foo.dk/countername/') {
			echo "Failed (".__LINE__."): ".$cntVisits[4]." != 45 or ".$cntUrls[4]." != 'http://foo.dk/countername/'";
			return false;
		}
		
		//Test registration of a hit where the url is registered but here a
		//counter name is given.
		$engine->url = 'http://foo.dk/countername/';
		$engine->counterName = 'countername';
		$cntUrls[4] = 'http://foo.dk/countername/';
		list($cntUrls, $cntVisits) = $engine->applyPageCounter($cntUrls, $cntVisits, $max_counters);
		if ($cntVisits[4] != 46 or $cntUrls[4] != 'http://foo.dk/countername/') {
			echo "Failed (".__LINE__."): ".$cntVisits[4]." != 46 or ".$cntUrls[4]." != 'http://foo.dk/countername/'";
			return false;
		}

		$engine->url = 'http://foo.dk/countername/';
		$engine->counterName = 'countername';
		$cntUrls[4] = 'countername';
		list($cntUrls, $cntVisits) = $engine->applyPageCounter($cntUrls, $cntVisits, $max_counters);
		if ($cntVisits[4] != 47 or $cntUrls[4] != 'http://foo.dk/countername/') {
			echo "Failed (".__LINE__."): ".$cntVisits[4]." != 47 or ".$cntUrls[4]." != 'http://foo.dk/countername/'";
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
		return "ZipStatEngine";
	}
}

?>