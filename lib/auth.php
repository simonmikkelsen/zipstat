<?php

require_once(dirname(__FILE__)."/PasswordHash.php");

/**
 * Provides handy authentication adapted to make use with ZIP Stat easier.
 */
class ZipStatAuthentication {
	
	/**
	 * A datasource associated with the current user.
	 */
	var $datasource;
	
	/**
	 * Creates a new instance.
	 *
	 * @param $datasource a datasource associated with the current user.
	 */
	function ZipStatAuthentication(&$datasource) {
		$this->datasource = &$datasource;
	}
	
	/**
	 * Take a password and stores the corresponding hash in the users datasource.
	 *
	 * Important note: This function will use the username when making the
	 * hash, so: 1. If the username changes, the hash is unuseable.
	 *           2. Two users with the same password will get different hashes.
	 *
	 * @param $password the one to store.
	 */
	function setPassword($password) {
		$hasher = PasswordHash(8, true);
		$hash = $hasher->HashPassword($password);
	}
	
	/**
	 * Returns if the given password will authenticate the user (@c true)
	 * or not (@c false).
	 *
	 * @param $provided_password the password provided by the user.
	 * @return if the user can be authenticated or not.
	 */
	function authenticate($provided_password) {
		
	}
	
}

?>