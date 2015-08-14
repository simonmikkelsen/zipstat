<?php
require_once(dirname(__FILE__)."/../../Html.php");
require_once(dirname(__FILE__)."/../testcase.php");
require_once(dirname(__FILE__)."/../../lib/ZipStatEngine.php");
require_once(dirname(__FILE__)."/../../lib/PasswordHash.php");

/**
 * Tests the PasswordHash class.
 */
class PasswordHashTest extends TestCase {

	/**
	 * Tests the Password hash class.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		$hash = new PasswordHash(8, true);
		$username = "foobar";
		$password = "password"; //Stupid password!
		
		$storedHash = $hash->HashPassword($password);
		if (!$hash->CheckPassword($password, $storedHash)) {
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
		return "PasswordHash";
	}

}
?>