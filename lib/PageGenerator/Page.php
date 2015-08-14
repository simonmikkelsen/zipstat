<?php

/**
 * Defines an interface for a page. It must be extended by all concrete
 * implementations.
 */
class Page {
	
	/**
	 * The title of the page.
	 */
	var $title;
	
	/**
	 * Sets the title of the page.
	 */
	function setTitle($title){
		$this->title = $title;
	}

	/**
	 * Sends the whole page to the output using the PHP echo function.
	 */
	function echoPage() {}
}


?>