<?php

require_once(dirname(__FILE__)."/../testcase.php");
require_once(dirname(__FILE__)."/../../Html.php");

/**
 * Extended by classes that performs tests.
 */
class UrlComparatorTest extends TestCase {
	/**
	 * Runs the test cases.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		
		//Make the object
		$comp = new UrlComparator();
		
		//Test stripHostname - ignore www
		$comp->setIgnoreSubdomain(array("www"));
		if ("zip.dk" !== $comp->stripHostname("www.zip.dk")) {
			echo "Said that: zip.dk !== www.zip.dk";
			return false;
		}
		if ("www.dk" !== $comp->stripHostname("www.www.dk")) {
			echo "Said that: www.dk !== www.www.dk - don't remove from domain";
			return false;
		}
		if ("zip.www" !== $comp->stripHostname("www.zip.www")) {
			echo "Said that: zip.www !== www.zip.www - don't remove from top domain";
			return false;
		}
		
		//Test splitPath
		$urls[] = "/foo/bar.html";
		$results[] = array("/foo/", "bar.html");
		
		$urls[] = "/foo/ba.r.html";
		$results[] = array("/foo/", "ba.r.html");
		
		$urls[] = "/fo.o/bar.html";
		$results[] = array("/fo.o/", "bar.html");
		
		$urls[] = "/fo.o/ba.r.html";
		$results[] = array("/fo.o/", "ba.r.html");
		
		$urls[] = "/foo/";
		$results[] = array("/foo/", "");
		
		$urls[] = "/";
		$results[] = array("/", "");
		
		$urls[] = "/.";
		$results[] = array("/", ".");
		
		$urls[] = "/..";
		$results[] = array("/", "..");
		
		$urls[] = "/.foo/bar";
		$results[] = array("/.foo/", "bar");
		
		$urls[] = "/.foo/";
		$results[] = array("/.foo/", "");
		
		$urls[] = "/.foo/bar.html";
		$results[] = array("/.foo/", "bar.html");
		
		$urls[] = "/foo";
		$results[] = array("/", "foo");
		
		$urls[] = "foo";
		$results[] = array("", "foo");
		
		$urls[] = "foo/";
		$results[] = array("foo/", "");
		
		$urls[] = "foo/bar.html";
		$results[] = array("foo/", "bar.html");
		
		$urls[] = "/.htaccess";
		$results[] = array("/", ".htaccess");
		
		$urls[] = "/.htaccess.";
		$results[] = array("/", ".htaccess.");
		
		$urls[] = "/foo/.htaccess";
		$results[] = array("/foo/", ".htaccess");
		
		$urls[] = "";
		$results[] = array("", "");
		
		if (count($urls) != count($results)) {
			echo "Bad test: not the same number of urls and results.";
			return false;
		}
		
		for ($i = 0; $i < count($urls); $i++) {
			list($path, $file, $ext) = $comp->splitPath($urls[$i]);
			
			if ($path != $results[$i][0] or $file != $results[$i][1] or $ext != $results[$i][2]) {
			if ($path != @$results[$i][0] or @$file != $results[$i][1] or $ext != @$results[$i][2]) {
				//The @'s are to avoid notices about undefined indecies.
				echo "splitPath: Mismatch: $urls[$i] became '$path', '$file', '$ext' and not '"
				     .$results[$i][0]."', '".$results[$i][1]."', '".$results[$i][2]."'";
				     .@$results[$i][0]."', '".@$results[$i][1]."', '".@$results[$i][2]."'";
				return false;
			}
		}
		
		//Test complete urls.
		
		//Make a bunch of url pairs
		//setIgnoreSubdomain
		//setIgnoreProtocols
		//setIgnorePathCase
		//setIndexFiles
		
		//For setIgnoreSubdomain
		$url1[0] = 'http://www.foo.dk/';
		$url2[0] = 'http://www.foo.dk/';
		
		$url1[1] = 'http://foo.dk/';
		$url2[1] = 'http://foo.dk/';
		
		$url1[2] = 'http://www.bar.foo.dk/';
		$url2[2] = 'http://www.bar.foo.dk/';
		
		$url1[3] = 'http://foo.dk/';
		$url2[3] = 'http://www.foo.dk/';
		
		$url1[4] = 'http://bar.foo.dk/';
		$url2[4] = 'http://www.bar.foo.dk/';
		
		//setIgnoreProtocols
		$url1[5] = 'https://www.foo.dk/';
		$url2[5] = 'http://www.foo.dk/';
		
		$url1[6] = 'http://foo.dk/';
		$url2[6] = 'http://foo.dk/';
		
		$url1[7] = 'https://www.bar.foo.dk/';
		$url2[7] = 'http://www.bar.foo.dk/';
		
		$url1[8] = 'http://foo.dk/';
		$url2[8] = 'https://www.foo.dk/';
		
		$url1[9] = 'http://bar.foo.dk/';
		$url2[9] = 'https://www.bar.foo.dk/';
		
		//setIgnorePathCase
		$url1[10] = 'http://www.foo.dk/index.shtml';
		$url2[10] = 'http://www.foo.dk/index.shtml';
		
		$url1[11] = 'http://WWW.FOO.DK/index.shtml';
		$url2[11] = 'http://www.foo.dk/index.shtml';
		
		$url1[11] = 'http://www.FOO.dk/bar/';
		$url2[11] = 'http://www.foo.dk/bar/';

		$url1[12] = 'http://www.foo.dk/index.shtml';
		$url2[12] = 'http://www.foo.dk/INDEX.SHTML';
		
		$url1[13] = 'http://www.FOO.DK/INDEX.HTML';
		$url2[13] = 'http://www.foo.dk/index.html';
		
		$url1[14] = 'http://www.foo.dk/INDEX.shtml';
		$url2[14] = 'http://www.foo.dk/index.html';
		
		//setIndexFiles
		$url1[15] = 'http://www.foo.dk/index.shtml';
		$url2[15] = 'http://www.foo.dk/';
		
		//Not equal - the are both index files, but it is actually different files. One of them are not the actual index file of the system.
		$url1[16] = 'http://www.foo.dk/index.shtml';
		$url2[16] = 'http://www.foo.dk/index.htm';
		
		$url1[17] = 'http://www.foo.dk/bar/index.shtml';
		$url2[17] = 'http://www.foo.dk/bar/index.htm';
		
		$url1[18] = 'http://www.foo.dk/bar/default.shtml';
		$url2[18] = 'http://www.foo.dk/bar/stuff.htm';
		
		//Incomplete urls
		$url1[19] = 'http://www.foo.dk/bar/stuff.shtml';
		$url2[19] = 'stuff.shtml';
		
		$url1[20] = 'http://www.foo.dk';
		$url2[20] = 'http://www.foo.dk/';
		
		$url1[21] = 'http://www.foo.dk/';
		$url2[21] = 'http://www.foo.dk';
		
		//Make comparators and results.
		
		//Default settings
		$comp = array();
		$comp[0] = new UrlComparator();
		$comp[0]->setIgnoreSubdomain(array("www"));
		$comp[0]->setIgnoreProtocols(true);
		$comp[0]->setIgnorePathCase(false);
		$comp[0]->setIndexFiles(array("index.htm", "index.html", "index.shtml",
	                        "index.php", "index.php3", "index.jsp",
	                        "default.htm", "default.html", "default.asp",
	                        "default.aspx"));
		$res[0][0] = true;
		$res[0][1] = true;
		$res[0][2] = true;
		$res[0][3] = true;
		$res[0][4] = true;
		$res[0][5] = true;
		$res[0][6] = true;
		$res[0][7] = true;
		$res[0][8] = true;
		$res[0][9] = true;
		$res[0][10] = true;
		$res[0][11] = true;
		$res[0][12] = false;
		$res[0][13] = false;
		$res[0][14] = false;
		$res[0][15] = true;
		$res[0][16] = false;
		$res[0][17] = false;
		$res[0][18] = false;
		$res[0][19] = false;
		$res[0][21] = true;
		
		//Default settings
		$comp[1] = new UrlComparator();
		$comp[1]->setIgnoreSubdomain(array("www"));
		$comp[1]->setIgnoreProtocols(true);
		$comp[1]->setIgnorePathCase(false);
		$comp[1]->setIndexFiles(array("index.html"));
		$res[1][0] = true;
		$res[1][1] = true;
		$res[1][2] = true;
		$res[1][3] = true;
		$res[1][4] = true;
		$res[1][5] = true;
		$res[1][6] = true;
		$res[1][7] = true;
		$res[1][8] = true;
		$res[1][9] = true;
		$res[1][10] = true;
		$res[1][11] = true;
		$res[1][12] = false;
		$res[1][13] = false;
		$res[1][14] = false;
		$res[1][15] = false;
		$res[1][16] = false;
		$res[1][17] = false;
		$res[1][18] = false;
		$res[1][19] = false;
		$res[0][20] = true;
		$res[0][21] = true;

		if (count($url1) !== count($url2) or count($url2) !== count($res[0])) {
			echo "Bad test: not the same number of url1, url2 and results: ";
			echo count($url1)." !== ".count($url2)." or ".count($url2)." !== ".count($res[0]).".";
			return false;
		}
		
		for ($n = 0; $n < count($res); $n++) {
			for ($i = 0; $i < count($url1); $i++) {
				//The @'s are to avoid notices about undefined indecies.
				if (! $comp[$n]->equals($url1[$i], @$url2[$i]) === @$res[$n][$i]) {
					echo "Compare url mismatch ($n, $i): '".$url1[$i]."' should "
					    .($res[$n][$i] === true ? "" : "not ")."be equal to '".$url2[$i]."'";
					return false;
				}
			} //End inner for.
			
			//Run it again, but switch urls.
			$urlTmp = $url1;
			$url1 = $url2;
			$url2 = $urlTmp;
		} //End outer for.
		
		//Test removeSearch
		$comp = new UrlComparator();
		$tests = array('http://www.foo.com/' => 'http://www.foo.com/',
		               'http://www.foo.com/some_file.html?' => 'http://www.foo.com/some_file.html',
		               'http://www.foo.com/?' => 'http://www.foo.com/',
		               'http://www.foo.com/?foo=bar' => 'http://www.foo.com/',
		               'http://www.foo.com/#part' => 'http://www.foo.com/',
		               'http://www.foo.com/#' => 'http://www.foo.com/',
		               'http://www.foo.com/file.html' => 'http://www.foo.com/file.html'
		);
		
		foreach ($tests as $in => $out) {
			$result = $comp->removeSearch($in);
			if ($result !== $out) {
			
				echo "Result mismatch: Expected '".htmlentities($out)."' but got '".htmlentities($result)."'.";
				return false;
			}
		}
		
		//Test ensureDirCount($refUrl, $changeUrl)
		$comp = new UrlComparator();
		                //Site                                Input               Output
		$tests = array(array('http://www.foo.com/', 'http://www.foo.com/bar/', 'http://www.foo.com/'),
		               array('http://www.foo.com/', 'http://foo.com/', 'http://foo.com/'),
		               array('http://www.foo.com/', 'http://foo.com/bar/i.html', 'http://foo.com/'),
		               array('http://www.foo.com/', 'http://foo.com/bar/godonk/i.html', 'http://foo.com/'),
		               array('http://www.foo.com/', 'https://foo.com/bar/godonk/i.html', 'https://foo.com/'),
		               array('http://www.foo.com/', 'https://foo.com/bar/godonk/i.html?zonyk', 'https://foo.com/'),
		               array('http://www.foo.com/foo/', 'http://foo.com/bar/', 'http://foo.com/bar/'),
		               array('http://www.foo.com/foo/', 'http://foo.com/foo/godonk', 'http://foo.com/foo/'),
		               array('www.foo.com/foo/', 'foo.com/foo/godonk', 'foo.com/foo/'),
		               array('http://www.foo.com/foo/', 'foo.com/foo/godonk', 'foo.com/foo/'),
		               array('www.foo.com/foo/', 'http://foo.com/foo/godonk', 'http://foo.com/foo/'),
		               array('http://www.foo.com/foo/', 'http://foo.com/foo/godonk/bar/', 'http://foo.com/foo/')
		);
		
		foreach ($tests as $i => $test) {
			list($site, $in, $out) = $test;
			$result = $comp->ensureDirCount($site, $in);
			if ($result !== $out) {
			
				echo "Result mismatch2 ($i): Expected '".htmlentities($out)."' but got '".htmlentities($result)."' for site '".htmlentities($site)."'.";
				return false;
			}
		}
		
		
		
		//Test isInSite($siteUrl, $url)
		$comp = new UrlComparator();
		                        //Site                     Input               Output
		$tests = array(array('http://www.foo.com/', 'http://www.foo.com/bar/', true),
		               array('http://www.foo.com/', 'http://foo.com/', true),
		               array('http://www.foo.com/', 'http://foo.com/bar/i.html', true),
		               array('http://www.foo.com/', 'http://foo.com/bar/godonk/i.html', true),
		               array('http://www.foo.com/', 'https://foo.com/bar/godonk/i.html', true),
		               array('http://www.foo.com/', 'https://foo.com/bar/godonk/i.html?zonyk', true),
		               array('http://www.foo.com/foo/', 'http://foo.com/bar/', false),
		               array('http://www.foo.com/foo/', 'http://foo.com/foo/godonk', true),
		               array('http://www.foo.dk/foo/', 'http://foo.com/foo/godonk', false),
		               array('https://www.foo.com/foo/', 'http://foo.com/foo/godonk', true),
		               array('http://www.foo.com/foo/', 'http://afoo.com/', false),
		               array('http://www.foo.com/foo/index.html', 'http://www.foo.com/foo/bar/', true),
		               array('http://www.foo.com', 'http://foo.com/', true),
		               array('http://foo.com', 'http://foo.com/', true),
		               array('http://www.foo.com', 'http://foo.com/bar.shtml', true),
		               array('http://foo.com', 'http://foo.com/bar.shtml', true),
		               array('http://foo.com', 'http://www.foo.com', true),
		               array('http://foo.com', 'www.foo.com', true),
		               array('foo1.com', 'http://www.foo1.com', true),
		               array('http://www.foo.com/foo/', 'http://foo.com/foo/godonk/bar/', true)
		);

		foreach ($tests as $i => $test) {
			list($site, $in, $out) = $test;
			$result = $comp->isInSite($site, $in);
			if ($result !== $out) {
				echo "Result mismatch3 ($i): Expected '".htmlentities($out)."' but got '".htmlentities($result)."' for site '".htmlentities($site)."'.";
				return false;
			}
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
		return "UrlComparator";
	}
}
                                                                   
?>