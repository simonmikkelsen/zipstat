<?php

/**
 * Abstract class for creating whole pages.
 *
 * <p><b>File:</b> PageGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class PageFactory
{
	/**
	 * The <code>SiteContext</code> object to use.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteContext;
	
	/**
	 * The mode to use.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $mode;

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext the instance of <code>SiteContext</code> to use.
	 * @param $mode the mode to create objects for. The supported modes is
	 *              returned by {@link #getSupportedModes}.
	 */
	function PageFactory(&$siteContext, $mode)
	{
		$this->siteContext = &$siteContext;
		
		if (in_array($mode, $this->getSupportedModes()))
		$this->mode = $mode;
	}
	
	/**
	 * Returns an array of the supported modes.
	 * Current modes are:<br>
	 * <code>html</code><br>
	 *
	 * @public
	 * @static
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] an array of the supported modes.
	 */
	function getSupportedModes() {
		$supportedModes[0] = 'html';
		return $supportedModes;
	}
	
	
	/**
	 * 
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $username the username of the user, or <code>null</code> if
	 *                  unknown.
	 * @return a
	 */
	function createLoginPage($username) {
		require "lib/PageGenerator/html/LoginPage.php";
	}

}



?>