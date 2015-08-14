<?php

/**
 * Extended by classes that performs tests.
 */
class LegacyDateParserTest {
	/**
	 * Runs the test cases.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		//Danish primer: lr is short for saturday (as sat) in danish.
	
		$legacyDate = "lr d. 24/9-2005 kl. 12:32";
		$legacyDateUnixtime = 1127557920;
		$closeDateFormat = "\\l\\r \\d. j/n-Y \\k\\l. G:i";
	
		$lp = new LegacyDateParser();
		
		if ($lp->attemptParse($legacyDate) === FALSE) {
			echo "Valid date ($legacyDate) did not parse.";
			return false;
		}
		
		if ($lp->parsedDate < 0) {
			echo "Stated that ($legacyDate) was parsed, but it was not.";
			return false;
		}
		
		$res = $lp->getParsedDate();
		if ($res != $legacyDateUnixtime) {
			echo "Parsed date ($legacyDate) but got ($res) and not the expected time ($legacyDateUnixtime).";
			return false;
		}
		
		$newDate = date($closeDateFormat, $res);
		if ($legacyDate !== $newDate) {
			echo "Expected ($legacyDate) but got ($newDate).";
			return false;
		}
		
		$badDate = "some date/foo bar";
		if ($lp->attemptParse($badDate) !== FALSE) {
			echo "Said OK for a date it should not be able to parse.";
			return false;
		}
		
		if ($lp->parsedDate >= 0) {
			echo "Said it could not parse a date, but saved it internally.";
			return false;
		}

		if ($lp->parseToView($badDate, $closeDateFormat) !== $badDate) {
			echo "Parsed a bad date to view, but did not return it again.";
			return false;
		}

		if ($lp->parseToView($legacyDate, "dummy format") == $legacyDate) {
			echo "Parsed a OK date to view ($legacyDate), but returned it again.";
			return false;
		}

		$res = $lp->parseToView($legacyDate, $closeDateFormat);
		if ($res !== $newDate) {
			echo "Parsed a OK date to view ($legacyDate), did not return the expected ($newDate).";
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
		return "LegacyDateParser";
	}
}

?>