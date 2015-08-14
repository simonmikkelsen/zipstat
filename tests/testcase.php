<?php

/**
 * Extended by classes that performs tests.
 */
class TestCase {
	/**
	 * Runs the test cases.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		echo "The function test() MUST be implemented.";
		exit;
	}
	
	/**
	 * Returns the name of the test.
	 * 
	 * @public
	 * @return Returns the name of the test.
	 */
	function getName() {
		echo "The function getName() MUST be implemented.";
		exit;
	}
}

?>