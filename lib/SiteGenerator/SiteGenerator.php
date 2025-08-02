<?php

/**
 * Abstract class for the modules to be used to generate a site.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteGenerator
{
	/**
	 * The <code>SiteContext</code> object to use.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteContext;

	/**
	 * Array of the elements the page is made up off.
	 * This is only instances of {@link StatGenerator}.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $elements;

	/**
	 * If non negative, refresh the page to the url in {@link #$refreshTo}
	 * after this amount of seconds.
	 *
	 * @private
	 */
	var $refreshInSec = -1;

	/**
	 * If enabled by {@link #$refreshInSec}, refresh the page to this url.
	 *
	 * @private
	 */
	var $refreshTo;

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param the instance of <code>SiteContext</code> to use.
	 */
	function __construct(&$siteContext)
	{
		$this->siteContext = &$siteContext;
	}

	/**
	 * Adds an {@link SiteElement}.
	 * <code>$element</code> must be an instance of {@link SiteElement}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $element an instance of {@link SiteElement}.
	 * @see SiteElement
	 */
	function addElement($element)
	{
		$parentClass = strtolower($element->getParentClass());
		if (is_subclass_of($element, 'SiteElement') or is_subclass_of($element, 'StatGenerator')) {
			//($parentClass == 'siteelement' or $parentClass == 'statgenerator')
			$this->elements[] = &$element;
		} else {
			die("<b>Error:</b> Function <code>addElement()</code> in super class <code>SiteGenerator</code> only acepts instances of classes that extends class <code>SiteElement</code>. The class is: ".get_class($element));
		}
	}

	/**
	 * Retrives the main part of the site via <code>getMainSite</code>,
	 * wraps it into a template and returns the finished site.
	 * This function must be overwritten by the extending class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the finished site.
	 */
	function getSite()
	{
		echo "Function <code>SiteGenerator.getSite</code> not overwritten.<br>";
		exit;
	}

	/**
	 * Returns all headers, represented by instances of {@link SiteHeader},
	 * for all elements in this site.
	 * Note: This function will walk the whole tree of elements on the site,
	 * so call this method with care!
	 * @public
	 */
	function collectHeaders($scheme) {
		$headers = array();
		// Iterate over the StatGenerator objects.
		foreach ($this->elements as $ele) {
			$headers = array_merge($headers, $ele->getHeadersRecursive($scheme));
		}
		return $headers;
	}
	
	/**
	 * Returns the number of seconds to refresh the page to the url in
	 * {@link #getRefreshTo}. If negative no refreshing should be done.
	 *
	 * @return the number of seconds to refresh the page.
	 * @public
	 */
	function getRefreshInSec() {
		return $this->refreshInSec;
	}

	/**
	 * Returns the url to refresh the page to, in the time returned by
	 * {@link #getRefreshInSec}.
	 *
	 * @return the url to refresh the page to.
	 * @public
	 */
	function getRefreshTo() {
		return $this->refreshTo;
	}

	/**
	 * Sets that the page should be refreshed to the given url in the given
	 * number of seconds. If the number of seconds is negative no refreshing
	 * is done.
	 * This method returns <code>true</code> if the refreshing is actually
	 * possible (it is not supported by all media) and <code>false</code> if
	 * it is not possible.
	 *
	 * @param $inSec the number of seconds the page should be refreshed in.
	 * @param $toUrl the url to refresh to.
	 * @return if refresh is supported.
	 * @public
	 */
	function setRefresh($inSec, $toUrl) {
		$this->refreshInSec = $inSec;
		$this->refreshTo = $toUrl;

		//This is usually the correct choice.
		//If in doubt a SiteGenerator can always extend this method to make
		//sure it is correct.
		return $this->isMediaInteractive();
	}

	/**
	 * Returns if urls are for viewing (<code>true</code>) or for a link
	 * (<code>false</code>).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return boolean if the urls are for viewing or for a link.
	 */
	function isUrlsForViewing()
	{
		return false;
	}

	/**
	 * Returns if the viewing medium is interactive i.e. if the user can
	 * e.g. click anythere.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return boolean if the medium is interactive.
	 */
	function isMediaInteractive()
	{
		return true;
	}

	/**
	 * Returns an array of the supported modes.
	 * Current modes are:<br>
	 * 0: text<br>
	 * 1: csv<br>
	 * 2: html<br>
	 *
	 * @public
	 * @static
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] an array of the supported modes.
	 */
	static function getSupportedModes() {
		$supportedModes[0] = 'text';
		$supportedModes[1] = 'csv';
		$supportedModes[2] = 'html';
		return $supportedModes;
	}

	/**
	 * Returns the code for the main part of the site.
	 *
	 * @public
	 * @version 0.0.2
	 * @since 0.0.1
	 * @return String the code for the main part of the site.
	 */
	function getMainSite()
	{
		$out = "";
		// Iterate over the StatGenerator objects.
		for ($i = 0; $i < count($this->elements); $i++) {
       $out .= $this->elements[$i]->getCode();
		}
		return $out;
	}

	/**
	 * Returns the name of this class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String navnet p� den klasse der er nedarvet.
	 */
	function getParentClass()
	{
		return 'SiteGenerator';
	}

	/**
	 * Returns a new instance of a <code>SiteElement</code>.
	 * This is creates acording to the type of site witch have been chosen.
	 * The following list states witch element types are always are
	 * supported. Specific generators may support aditional elements.
	 * <dt>
	 * 	<dt><code>table</code></dt>
	 * 		<dd>A table.</dd>
	 * 	<dt><code>graphTable</code></dt>
	 * 		<dd>A table with graphs.</dd>
	 * 	<dt><code>seriesGraph</code></dt>
	 * 		<dd>A graphs for showing series.</dd>
	 * 	<dt><code>headline</code></dt>
	 * 		<dd>A headline.</dd>
	 * 	<dt><code>text</code></dt>
	 * 		<dd>A text.</dd>
	 *    <dt><code>list</code></dt>
	 *       <dd>A list.</dt>
	 *    <dt><code>hiturl</code></dt>
	 *       <dd>Generates a representation of a hit and an url.</dd>
	 *    <dt><code>urlWrapper</code></dt>
	 *       <dd>Wraps the code of a <code>SiteElement</code> and produces a link</dd>
	 *    <dt><code>loginForm</code></dt>
	 *       <dd>Generates a login form.</dd>
	 *    <dt><code>checkbox</code></dt>
	 *       <dd>Generates a checkbox for use in a form.</dd>
	 *    <dt><code>formwrapper</code></dt>
	 *       <dd>Generates a form wrapper.</dd>
	 *    <dt><code>submitButton</code></dt>
	 *       <dd>Generates a submit button.</dd>
	 *    <dt><code>typeSelector</code></dt>
	 *       <dd>Generates a selector for different types of stat sites.</dd>
	 * </dt>
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteElementType a text string witch identifies the object
	 *        (see the table above).
	 * @return SiteElement an instance of <code>SiteElement</code>.
	 */
	function newElement($siteElementType)
	{
		echo "<b>Error:</b> Function <code>newElement()</code> not over written in super class <code>SiteGenerator</code>.";
		exit;
	}

	/**
	 * Returns a {@link SiteGenerator} for the given <code>$type</code>.
	 * Et is recommended to parse the returned obect as a reference
	 * (put <code>&amp;</code> in front of the variable name).
	 * This method must be invoked static, e.g. <code>SiteGenerator::getGenerator()</code>.
	 * <p>Valid types:<br>
	 * <code>text</code>: Plain text<br>
	 * <code>csv</code>: ;-separated text for import in spread sheet<br>
	 * <code>htmlgraphs</code>: HTML 4.0 with graphs.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext an instance of {@link SiteContext}.
	 * @return SiteGenerator a {@link SiteGenerator}.
	 * @see SiteContext
	 */
	static function getGenerator($type, &$siteContext)
	{
		$supportedModes = SiteGenerator::getSupportedModes();
		if ($type === $supportedModes[0]) /*Plain text*/
		{
			require_once dirname(__FILE__)."/TextGenerator.php";
			return new TextGenerator($siteContext);
		}
		elseif ($type === $supportedModes[1]) /*Import in spread sheet*/
		{
			require_once dirname(__FILE__)."/CsvGenerator.php";
			return new CsvGenerator($siteContext);
		}
		else /*if ($type === $supportedModes[2])*/ /*HTML*/
		{
			require_once dirname(__FILE__)."/HtmlGenerator.php";
			return new HtmlGenerator($siteContext);
		}
	}

	/**
	 * Returns a <code>String[]</code> containing the headers for the type
	 * of view used.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Strng[] the headers for this type of view.
	 */
	function getHeaders()
	{
		echo "<b>Error:</b> Function <code>getHeaders()</code> not over written in super class <code>SiteGenerator</code>.";
		exit;
	}

} /*End of class SiteGenerator*/

/************/

/**
 * Abstract class that represents an element in a stat site.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteElement
{
	/**
	 * The instance of {@link SiteContext} to use.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteContext;

	/**
	 * Name to idenfity the element.
	 * This is not required, but may be used for HTML, style sheet,
	 * XML etc, so it would preferable to specify it.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $elementName;

	/**
	 * Name to identify a group of the element.
	 * This is not required, but may be used for HTML, style sheet,
	 * XML etc, so it would preferable to specify it.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $elementClass;

	/**
	 * Array of headers to use for at site.
	 * @private
	 */
	var $headers = array();

	/**
	 * An array of {@link SiteElement} which output will be placed
	 * before this objects output.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $headElements = array();

	/**
	 * An array of {@link SiteElement} which output will be placed
	 * after this objects output.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $tailElements = array();

	/**
	 * String that shall separate elements when joined. Set to an empty
	 * string to disable. Primary added for the implementation of CSV, but
	 * may be used for other purposes.
	 *
	 * @private
	 * @since 0.0.1
	 * @todo: Replace this with a polymophic version of CsvHandler
	 *       (an "element joiner").
	 */
	var $elementJoiner = '';

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext The instance of {@link SiteContext} to use.
	 */
	function __construct(&$siteContext)
	{
		$this->siteContext = &$siteContext;
		$this->elementName = "";
		$this->elementClass = "";
	}

	/**
	 * Returns the name of this class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the name of this class.
	 */
	function getParentClass()
	{
		return "SiteElement";
	}

	/**
	 * Returns the code representation of this object.
	 * This function must be overwritten.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code representation of this object.
	 */
	function getCode()
	{
		echo "<b>Error:</b> Funtion <code>getCode()</code> in super class <code>SiteElement</code> must be over written.";
		exit;
	}

	/**
	 * Adds a {@link SiteElement} to the array of
	 * <code>SiteElement</code> witch output will be placed before this
	 * objects output.
	 * <p>If multiple objets are added the first object will be placed
	 * topmost.</p>
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteElement the <code>SiteElement</code> witch output shall
	 *        be placed before this object.
	 * @return void
	 */
	function addHeadElement(&$siteElement)
	{
		if (strtolower($siteElement->getParentClass()) != "siteelement")
		{
			echo "<b>Error:</b> <code>SiteGenerator.addHeadElement(SiteElement)</code> only acepts instances of classes witch hav extended <code>SiteElement</code>.<br>";
			exit;
		}

		$this->headElements[] = &$siteElement;
	}

	/**
	 * Adds a {@link SiteElement} to the array of
	 * <code>SiteElement</code> witch output will be placed after this
	 * objects output.
	 * <p>If multiple objets are added the first object will be placed
	 * topmost.</p>
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteElement the <code>SiteElement</code> witch output shall
	 *        be placed after this object.
	 * @return void
	 */
	function addTailElement(&$siteElement)
	{
		if (strtolower($siteElement->getParentClass()) != "siteelement")
		{
			echo "<b>Error:</b> <code>SiteGenerator.addTailElement(SiteElement)</code> only acepts instances of classes witch hav extended <code>SiteElement</code>.<br>";
			exit;
		}

		$this->tailElements[] = &$siteElement;
	}

	/**
	 * Returns the code that shall be placed before the output of this
	 * object.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code that shall be placed before the output of this
	 *         object.
	 */
	function getHeadCode()
	{
		//Create an element joiner if none exists.
		if ($this->elementJoiner == NULL)
			$this->elementJoiner = new ElementJoiner();

		$out = array();
		//Pull the code out of the elements
		for ($i = 0; $i < sizeof($this->headElements); $i++)
			$out[] = $this->headElements[$i]->getCode();

		return $this->elementJoiner->joinElements($out);
	}

	/**
	 * Returns the code that shall be placed after the output of this
	 * object.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code that shall be placed after the output of this
	 *         object.
	 */
	function getTailCode()
	{
		$out = "";

		//Iterate the tail elements.
		for ($i = 0; $i < sizeof($this->tailElements); $i++) {
			$out .= $this->tailElements[$i]->getCode();
		}

		return $out;
	}

	/**
	 * Adds the given header to the list of headers to use for this site.
	 * Only instances of {@link SiteHeader} must be added.
	 * @public
	 */
	function addHeader($header) {
		if (! is_a($header, 'SiteHeader')) {
			die("Non SiteHeader given. Got: ".get_class($header));
		}
		$this->headers[] = $header;
	}
	
	/**
	 * Returns the list of the headers to use for this site.
	 * Only instances of {@link SiteHeader} are returned.
	 *
	 */
	function getHeaders() {
		return $this->headers;
	}
	
	/**
	 * Returns all headers for the current site element and all its
	 * head and tail elements for the given scheme. Classes that derives
	 * from this class and makes it possible to add site elements in other
	 * ways must overwrite this method in order to make sure everything
	 * gets returned.
	 * As with all ofther iteration over site elements, cyclic references
	 * are not taken into care, so do not make them.
	 * @public
	 */
	function getHeadersRecursive($scheme) {
		$allHeaders = array();
		
		// All our own header.
		foreach ($this->headers as $header) {
			if ($header->getScheme() === $scheme) {
				$allHeaders[] = $header;
			}
		}
		
		// All kinds of elements we know off.
		$allKnownElements = array_merge($this->headElements, $this->tailElements);
		foreach ($allKnownElements as $element) {
			$allHeaders = array_merge($allHeaders, $element->getHeadersRecursive($scheme));
		}
		return $allHeaders;
	}

	/*
	 * Returns the array containing the text.
	 *
	 * @private
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] the array containing the text.
	 */
/*	function getTextArray()
	{
		return $this->textArray;
	}
*/
	/*
	 * Returns the array containing the numbers.
	 *
	 * @private
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int[] the array containing the numbers.
	 */
/*	function getNumArray()
	{
		return $this->numArray;
	}
*/

	/**
	 * Sets the object that shall be used to elements when joined. Set to
	 * <code>null</code> to disable. Primary added for the implementation of
	 * CSV, but may be used for other purposes. The object must extend
	 * <code>ElementJoiner</code>.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $elementJoiner joins elements.
	 * @return void
	 */
	function setElementJoiner(&$elementJoiner)
	{
			$this->elementJoiner = &$elementJoiner;
	}

	/**
	 * Returns the object that shall be used to elements when joined. Returns
	 * <code>null</code> to disable. Primary added for the implementation of
	 * CSV, but may be used for other purposes.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return separate elements when joined.
	 */
	function &getElementJoiner()
	{
			return $this->elementJoiner;
	}

	/**
	 * Sets the name to idenfity the element.
	 * This is not required, but may be used for HTML, style sheet,
	 * XML etc, so it would preferable to specify it.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $elementName the name to idenfity the element.
	 * @return void
	 */
	function setElementName($elementName)
	{
		$this->elementName = $elementName;
	}

	/**
	 * Sets the name to identify a group of the element.
	 * This is not required, but may be used for HTML, style sheet,
	 * XML etc, so it would preferable to specify it.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $elementclass the name to identify a group of the element.
	 * @return void
	 */
	function setElementClass($elementClass)
	{
		$this->elementClass = $elementClass;
	}

	/**
	 * Returns the name to idenfity the element.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String Et navn der identificerer elementet.
	 */
	function getElementName()
	{
		return $this->elementName;
	}

	/**
	 * Returns the name to identify a group of the element.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String Et navn der identificerer en gruppe af elementer.
	 */
	function getElementClass()
	{
		return $this->elementClass;
	}

	/**
	 * Returns a <code>String</code> containing all the html attributes that
	 * is properly defined. If none are defined an empty <code>String</code>
	 * is returned. The <code>String</code> is returned in
	 * &quot;html style&quot;, preformated for use in HTML.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the html attributes in one <code>String</code>.
	 */
	function getHtmlAttribs()
	{
		$out = "";

		//The class attribute
		if (strlen($this->getElementClass()) > 0)
			$out .= " class=\"".htmlentities($this->getElementClass())."\"";

		//Get the legasy mapper
		$legasyMapper = $this->siteContext->getLegasyMapper();

		//The name attribute
		if (strlen($this->getElementName()) > 0 and $legasyMapper !== NULL) {
			$out .= " name=\"".htmlentities($legasyMapper->mapElementName($this->getElementName()))."\"";
		}

		return $out;
	}

	/**
	 * Returns if this is an instance of the class given in
	 * <code>$className</code>.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $className the name of the class to test for.
	 * @return boolean if this is an instance of <code>$className</code>.
	 */
/*	function isInstanceOf($className)
	{
		return in_array(strtolower($className), $this->$instanceOf);
	}
*/
	/**
	 * Tells that this is an instance of <code>$className</code>.
	 * Class names which differ only in case should not be given
	 * (and should never be created). This function should only be invoked
	 * by classes which extend this class (or a subclass of this).
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $className the name of the class.
	 * @return void
	 */
/*	function addInstanceOf($className)
	{
		$this->$instanceOf[] = strtolower($className);
	}
*/


} /*End of class SiteElement*/

/*************/

/**
 * Wraps a {@link SiteElement} or a <code>Wrapper</code> to
 * alter the original result. Note that a <code>Wrapper</code> is also a
 * <code>SiteElement</code>.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class Wrapper extends SiteElement
{
	/**
	 * The {@link SiteElement} or <code>Wrapper</code> which is
	 * wrapped.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $wrapped;

	/**
	 * Creates a new instnace.
	 *
	 * @param $siteContext The instance of {@link SiteContext} to use.
	 * @public
	 */
	function __construct(&$siteContext) {
		parent::__construct($siteContext);
	}

	/**
	 * Returns if the wrapped object also is a <code>Wrapper</code>. If no object is
	 * wrapped <code>false</code> is returned.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return boolean if the wrapped object is a <code>Wrapper</code>.
	 */
/*	function isWrappedWrapper()
	{

	}
*/

	/**
	 * Returns the {@link SiteElement} or {@link Wrapper} which is
	 * wrapped.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return Object the {@link SiteElement} or {@link Wrapper} which is wrapped.
	 */
	function getWrapped()
	{
		return $this->wrapped;
	}

	/**
	 * Returns the code produced by the wrapped object.
	 * This method should only be invoked from classes which derive this.
	 *
	 * @protected
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code produced by the wrapped object.
	 */
	function getWrappedCode()
	{
		return $this->wrapped->getCode();
	}

	/**
	 * Sets the {@link SiteElement} or {@link Wrapper} which is
	 * wrapped.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $wrapped the {@link SiteElement} or {@link Wrapper} which is wrapped.
	 */
	function setWrapped(&$wrapped)
	{
		$this->wrapped = &$wrapped;
	}

	/**
	 * Sets a copy of the {@link SiteElement} or {@link Wrapper} which is
	 * wrapped.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $wrapped the {@link SiteElement} or {@link Wrapper} which a copy off is wrapped.
	 */
	function setWrappedCopy($wrapped)
	{
		$this->wrapped = $wrapped;
	}

} /*End of class Wrapper*/

/**
 * Wraps a pice of code to represent it as an object.
 * Should normally not be used, as code should be generated by the display
 * independent generators, but can be used to make older code work.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CodeWrapper extends Wrapper {

	/**
	 * Returns the wraped code.
	 */
	function getCode() {
		return $this->getWrapped();
	}

}

/**
 * Cuts a pice of text to a specified length.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CutWrapper extends Wrapper
{
	/**
	 * The maximum length of the text to cut.
	 * Set to <code>-1</code> for no restriction.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $maxLength = -1;

	/**
	 * The side to cut (remove) from. @c 0 for right, @c 1 for left.
	 *
	 * @private
	 */
	var $cutFrom = 0;

	function getCode()
	{
		$text = $this->getWrappedCode();
		if ($this->maxLength !== -1 and strlen($text) > $this->maxLength) {
			if ($this->getCutFrom() === 0) {
				//Cut from right
				return substr($text, 0, $this->maxLength)."...";
			} else {
				//Cut from left
				return "...".substr($text, strlen($text) - $this->maxLength);
			}
		} else
			return $text;
	}

	/**
	 * Returns the side to cut (remove) from. @c 0 for right, @c 1 for left.
	 *
	 * @public
	 * @return the side to cut (remove) from. @c 0 for right, @c 1 for left.
	 */
	function getCutFrom() {
		return $this->cutFrom;
	}

	/**
	 * Sets the side to cut (remove) from. @c 0 for right, @c 1 for left.
	 *
	 * @public
	 * @param $cutFrom the side to cut (remove) from. @c 0 for right, @c 1 for left.
	 */
	function setCutFrom($cutFrom) {
		$this->cutFrom = $cutFrom;
	}

	/**
	 * Returns the maximum length of the text to cut.
	 * Returns <code>-1</code> for no restriction.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum length of the text to cut.
	 */
	function getMaxLength()
	{
		return $this->maxLength;
	}

	/**
	 * Sets the maximum length of the text to cut.
	 * Set to <code>-1</code> for no restriction.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $maxLength the maximum length of the text to cut.
	 */
	function setMaxLength($maxLength)
	{
		$this->maxLength = $maxLength;
	}
} /*End of class CutWrapper*/

/**
 * Wraps an url and cuts it acording to specified rules.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class UrlCutWrapper extends CutWrapper
{
	/**
	 * States if the protocol part shall be removed.
	 *
	 *
	 * @private
	 * @since 0.0.1
	 * @see #setCutProtocol for specifications on values.
	 */
	var $cutProtocol;

	/**
	 * Remove the &quot;<code>www.</code>&quot; if the url contains it
	 * and cut protocol is set to &quot;always&quot;.
	 *
	 * @private
	 * @since 0.0.1
	 * @see #setCutWww for specifications on values.
	 */
	var $cutWww;

	/**
	 * Remove the text after the <code>?</code> in the url
	 * (if this exists). Values:<br>
	 * 0: Always<br>
	 * 1: Never<br>
	 * 2: Only when url is too long. This is done after cut protocol and
	 *    cut www.<br>
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $cutSearch;

	/**
	 * Cut the rest of the url. Values:<br>
	 * 0: Cut one level at the time from the left (e.g.
	 *    <code>zipstat.dk/help/index.html</code> will become
	 *    <code>/help/index.html</code> or
	 *    <code>/index.html</code>.<br>
	 * 1: Cut one level at the time from the right.
	 *    E.g. <code>zipstat.dk/help/index.html</code> will become
	 *    <code>zipstat.dk/help</code> or
	 *    <code>zipstat.dk</code>.<br>
	 * 2: Cut the needed charaters from the left. E.g.
	 *    <code>zipstat.dk/help/index.html</code> may become
	 *    <code>zipstat.dk/help/inde</code>.<br>
	 * 3: Cut the needed charaters from the right. E.g.
	 *    <code>zipstat.dk/help/index.html</code> may become
	 *    <code>at.dk/help/index.html</code>.<br>
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $cutUrl;

	/**
	 * When cut protocol, cut www and cut search have been applied, then
	 * if this is not an empty string, always remove this pice of text
	 * from the left. This is only done if both cut protocol, cut www is
	 * always applied.
	 * <p>For an example if this is <code>zipstat.dk</code> then
	 * <code>zipstat.dk/help/index.html</code> will become
	 * <code>/help/index.html</code> or if it is
	 * <code>image.dk/~tryl/</code> then
	 * <code>image.dk/~tryl/taeske/index.html</code> will become
	 * <code>/taeske/index.html</code>.</p>
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $cutAlwaysThis;

	/**
	 * Returns the code representation of this object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code representation of this object.
	 */
	function getCode()
	{
		$cutProtocolDone = 0;
		$cutWwwDone = 0;

		$wrapped = $this->getWrapped();
		$url = $wrapped->getCode();

		 /* $this->cutProtocol
		  * 0: Always<br>
		  * 1: Never<br>
		  * 2: Only when url is too long. This is then the first thing to cut.<br>
		  */
		if ($this->cutProtocol === 0 or
			($this->cutProtocol === 2 and strlen($url) > $this->getMaxLength()))
		{
			$pIndex = strpos($url, "://");
			//Do we have a protocol?
			if ($pIndex !== false)
				$url = substr($url, $pIndex + 3);
			$cutProtocolDone = 1;
		} /*End if $this->cutProtocol....*/

		 /* $this->cutWww
		  * 0: Always<br>
		  * 1: Never<br>
		  * 2: Only when the the url is too long, after cut protocol have been
		  *    applied.
		  */
		if ($this->cutWww === 0 or
			($this->cutWww === 2 and strlen($url) > $this->getMaxLength()))
		{
			if (strpos(strtolower($url), "www.") === 0)
				$url = substr($url, 4);
			$cutWwwDone = 1;
		} /*End of if $this->cutWww...*/

		//$cutAlwaysThis
		if ($cutProtocolDone === 1 and $cutWwwDone === 1 and
			strlen($this->cutAlwaysThis) > 0 and
			strpos($url, $this->cutAlwaysThis) === 0)
		{
			$url = substr($url, strlen($this->cutAlwaysThis));
		}

		/* $this->cutSearch
		 * 0: Always<br>
		 * 1: Never<br>
		 * 2: Only when url is too long. This is done after cut protocol and
		 *    cut www.<br>
		 */
		if ($this->cutSearch === 0 or
			($this->cutSearch === 2 and strlen($url) > $this->getMaxLength()))
		{
			$cutFrom = strpos($url, "?");
			if ($cutFrom < $this->getMaxLength())
				$cutFrom = $this->getMaxLength();

			$url = substr($url, 0, $cutFrom);
		} /*End of if $this->cutSearch...*/

		/*	$this->cutUrl
		 * 0: Cut one level at the time from the left (e.g.
		 *    <code>zipstat.dk/help/index.html</code> will become
		 *    <code>/help/index.html</code> or
		 *    <code>/index.html</code>.<br>
		 * 1: Cut one level at the time from the right.
		 *    E.g. <code>zipstat.dk/help/index.html</code> will become
		 *    <code>zipstat.dk/help</code> or
		 *    <code>zipstat.dk</code>.<br>
		 * 2: Cut the needed charaters from the left. E.g.
		 *    <code>zipstat.dk/help/index.html</code> may become
		 *    <code>zipstat.dk/help/inde</code>.<br>
		 * 3: Cut the needed charaters from the right. E.g.
		 *    <code>zipstat.dk/help/index.html</code> may become
		 *    <code>at.dk/help/index.html</code>.<br>
		 */
		 if (strlen($url) > $this->getMaxLength())
		 {
			if ($this->cutUrl === 0)
			{
				$cutFrom = strpos($url, "/", $this->getMaxLength());
				if ($cutFrom !== false)
					$url = substr($url, $cutFrom);
			} elseif ($this->cutUrl === 1)
			{
				$url = substr($url, 0, $this->getMaxLength());
				$cutTo = strrpos($url, "/");
				if ($cutTo !== false)
					$url = substr($url, 0, $cutTo);
			} elseif ($this->cutUrl === 2)
			{
				$url = substr($url, (strlen($url) - $this->getMaxLength()));
			} elseif ($this->cutUrl === 3)
			{
				$url = substr($url, 0, $this->getMaxLength());
			}
		 } /*End if strlen($url) ... */

		return $url;
	}

	/**
	 * Returns if the protocol part shall be removed.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int states if the protocol part shall be removed.
	 * @see #setCutProtocol for specifications on values.
	 */
	function getCutProtocol()
	{
		return $this->cutProtocol;
	}

	/**
	 * Sets if the protocol (e.g. <code>http://</code>) shall be removed.
	 * Values: <br>
	 * 0: Always<br>
	 * 1: Never<br>
	 * 2: Only when url is too long. This is then the first thing to cut.<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $cutProtocol states if the protocol part shall be removed.
	 */
	function setCutProtocol($cutProtocol)
	{
		$this->cutProtocol = $cutProtocol;
	}

	/**
	 * Returns states if the &quot;www<code>www</code>&quot; part should
	 * be removed.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int states if the &quot;www<code>www</code>&quot; part should be
	 *         removed.
	 * @see #setCutWww for specifications on values.
	 */
	function getCutWww()
	{
		return $this->cutWww;
	}

	/**
	 * Sets states if the &quot;www<code>www</code>&quot; part should be
	 * removed. This is only applied if the cut protocol have been applied.
	 * Values:<br>
	 * 0: Always<br>
	 * 1: Never<br>
	 * 2: Only when the the url is too long, after cut protocol have been
	 *    applied.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $cutWww states if the &quot;www<code>www</code>&quot; part
	 *                should be removed.
	 */
	function setCutWww($cutWww)
	{
		$this->cutWww = $cutWww;
	}

	/**
	 * Returns when to remove the text after the <code>?</code>
	 * in the url (if this exists).
	 * Values:<br>
	 * 0: Always<br>
	 * 1: Never<br>
	 * 2: Only when url is too long. This is done after cut protocol and
	 *    cut www.<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int when to remove the text after the <code>?</code> in the url.
	 */
	function getCutSearch()
	{
		return $this->cutSearch;
	}

	/**
	 * Sets when to remove the text after the <code>?</code> in the url
	 * (if this exists).
	 * Values:<br>
	 * 0: Always<br>
	 * 1: Never<br>
	 * 2: Only when url is too long. This is done after cut protocol and
	 *    cut www.<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $cutSearch whtn to remove the text after the <code>?</code>
	 *                   in the url.
	 */
	function setCutSearch($cutSearch)
	{
		$this->cutSearch = $cutSearch;
	}

	/**
	 * Returns how to cut the rest of the url.
	 * Values:<br>
	 * 0: Cut one level at the time from the left (e.g.
	 *    <code>zipstat.dk/help/index.html</code> will become
	 *    <code>/help/index.html</code> or
	 *    <code>/index.html</code>.<br>
	 * 1: Cut one level at the time from the right.
	 *    E.g. <code>zipstat.dk/help/index.html</code> will become
	 *    <code>zipstat.dk/help</code> or
	 *    <code>zipstat.dk</code>.<br>
	 * 2: Cut the needed charaters from the left. E.g.
	 *    <code>zipstat.dk/help/index.html</code> may become
	 *    <code>zipstat.dk/help/inde</code>.<br>
	 * 3: Cut the needed charaters from the right. E.g.
	 *    <code>zipstat.dk/help/index.html</code> may become
	 *    <code>at.dk/help/index.html</code>.<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String how to cut the rest of the url.
	 */
	function getCutUrl()
	{
		return $this->cutUrl;
	}

	/**
	 * Sets how to cut the rest of the url.
	 * Values:<br>
	 * 0: Cut one level at the time from the left (e.g.
	 *    <code>zipstat.dk/help/index.html</code> will become
	 *    <code>/help/index.html</code> or
	 *    <code>/index.html</code>.<br>
	 * 1: Cut one level at the time from the right.
	 *    E.g. <code>zipstat.dk/help/index.html</code> will become
	 *    <code>zipstat.dk/help</code> or
	 *    <code>zipstat.dk</code>.<br>
	 * 2: Cut the needed charaters from the left. E.g.
	 *    <code>zipstat.dk/help/index.html</code> may become
	 *    <code>zipstat.dk/help/inde</code>.<br>
	 * 3: Cut the needed charaters from the right. E.g.
	 *    <code>zipstat.dk/help/index.html</code> may become
	 *    <code>at.dk/help/index.html</code>.<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $cutUrl how to cut the rest of the url.
	 */
	function setCutUrl($cutUrl)
	{
		$this->cutUrl = $cutUrl;
	}

	/**
	 * Returns cut this text from the left.
	 * When cut protocol, cut www and cut search have been applied, then
	 * if this is not an empty string, always remove this pice of text
	 * from the left. This is only done if both cut protocol, cut www is
	 * always applied.
	 * <p>For an example if this is <code>zipstat.dk</code> then
	 * <code>zipstat.dk/help/index.html</code> will become
	 * <code>/help/index.html</code> or if it is
	 * <code>image.dk/~tryl/</code> then
	 * <code>image.dk/~tryl/taeske/index.html</code> will become
	 * <code>/taeske/index.html</code>.</p>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String cut this text from the left.
	 */
	function getCutAlwaysThis()
	{
		return $this->cutAlwaysThis;
	}

	/**
	 * Sets cut this text from the left.
	 * When cut protocol, cut www and cut search have been applied, then
	 * if this is not an empty string, always remove this pice of text
	 * from the left. This is only done if both cut protocol, cut www is
	 * always applied.
	 * <p>For an example if this is <code>zipstat.dk</code> then
	 * <code>zipstat.dk/help/index.html</code> will become
	 * <code>/help/index.html</code> or if it is
	 * <code>image.dk/~tryl/</code> then
	 * <code>image.dk/~tryl/taeske/index.html</code> will become
	 * <code>/taeske/index.html</code>.</p>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $cutAlwaysThis cut this text from the left.
	 */
	function setCutAlwaysThis($cutAlwaysThis)
	{
		$this->cutAlwaysThis = $cutAlwaysThis;
	}

} /*End of class UrlCutWrapper*/

/**
 * Wrapes an url for display.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class UrlWrapper extends Wrapper
{

	/**
	 * If the url is active (value <code>1</code>) the link is shown
	 * normally, else only the text is shown (value <code>0</code>).
	 */
	var $active = 1;

	/**
	 * The url to wrap.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $url;

	/**
	 * The title text for the url, if supported.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $title;

	/**
	 * Returns the url to wrap.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the url to wrap.
	 */
	function getUrl()
	{
		return $this->url;
	}

	/**
	 * Sets the url to wrap.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $url the url to wrap.
	 */
	function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Returns the title text for the url, if supported.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the title text for the url, if supported.
	 */
	function getTitle()
	{
		return $this->title;
	}

	/**
	 * Sets the title text for the url, if supported.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $title the title text for the url, if supported.
	 */
	function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Returns if the url is active.
	 *
	 * If the url is active (value <code>1</code>) the link is shown
	 * normally, else only the text is shown (value <code>0</code>).
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int if the url is active.
	 */
	function isActive() {
		return $this->active;
	}

	/**
	 * Sets if the url is active.
	 *
	 * If the url is active (value <code>1</code>) the link is shown
	 * normally, else only the text is shown (value <code>0</code>).
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $active if the url is active.
	 */
	function setActive($active) {
		$this->active = $active;
	}


} /*End of class UrlWrapper*/

/**
 * Represents a form.
 */
class FormWrapper extends Wrapper {

	/**
	 * The url to submit the form to.
	 */
	var $submitUrl;

	/**
	 * The method to use for the submission. Valid values are
	 * POST and GET.
	 */
	var $method;

	/**
	 * The keys of the parameters which shall just be passed through on
	 * submission.
	 */
	var $passThroughParams;

	/**
	 * Creates a new instance.
	 *
	 * @param $siteContext The instance of {@link SiteContext} to use.
	 * @public
	 */
	function __construct(&$siteContext) {
		parent::__construct($siteContext);
		$this->passThroughParams = array();
	}

	/**
	 * Returns keys of the parameters which shall just be passed through on
	 * submission.
	 *
	 * @public
	 * @returns String[]
	 * @return keys of the parameters which shall just be passed through on
	 *         submission.
	 */
	function getPassThroughParams() {
		return $this->passThroughParams;
	}

	/**
	 * Adds a parameter to the parameters which shall just be passed through on
	 * submission.
	 *
	 * @public
	 * @returns void
	 * @param $param a parameter which shall be added.
	 */
	function addPassThroughParams($param) {
		$this->passThroughParams[] = $param;
	}

	/**
	 * Returns the url to submit the form to.
	 *
	 * @returns String
	 * @return the url to submit the form to.
	 * @public
	 */
	function getSubmitUrl() {
		return $this->submitUrl;
	}

	/**
	 * Sets the url to submit the form to.
	 *
	 * @returns void
	 * @param $submitUrl the url to submit the form to.
	 * @public
	 */
	function setSubmitUrl($submitUrl) {
		$this->submitUrl = $submitUrl;
	}

	/**
	 * Returns the method to use for the submission. Valid values are
	 * POST and GET.
	 *
	 * @returns String
	 * @return the method to use for the submission.
	 * @public
	 */
	function getMethod() {
		return $this->method;
	}

	/**
	 * Sets the method to use for the submission. Valid values are
	 * POST and GET.
	 *
	 * @returns void
	 * @param $method
	 * @public
	 */
	function setMethod($method) {
		$this->method = $method;
	}

}

/**
 * Represents the headline of a site.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteHeadline extends SiteElement
{
	/**
	 * The text of the headline.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $headline;

	/**
	 * The size of the headline.
	 * Valid values: 1 - 6, where 1 is largest and 6 is smallest.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $size;

	/**
	 * Sets the text of the headline.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $headline The text of the headline.
	 * @return void
	 */
	function setHeadline($headline)
	{
		$this->headline = $headline;
	}

	function setSize($size)
	{
		if ($size == $size*1 and $size >= 1 and $size <= 6)
			$this->size = $size;
		else
		{
			echo "<b>Error:</b> Function <code>setSize()</code> in super class <code>SiteHeadline</code> only acept the integers 1 - 6 as parameters.";
			exit;
		}
	}

	/**
	 * Returns the code representation of this object.
	 * This function must be overwritten.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code representation of this object.
	 */
	function getCode()
	{
		echo "<b>Error:</b> Funtion <code>getCode()</code> in super class <code>SiteHeadline</code> must be over written.";
		exit;
	}
} /*End of class SiteHeadline*/

/**
 * Abstract class for create a graph.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteGraph extends SiteElement
{
	/**
	 * Array of <code>String</code> containing the text the graph should be
	 * generated for.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $textArray;

	/**
	 * Array of <code>int</code> containing the hits corresponding to
	 * <code>$textArray</code>.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $numArray;

	/**
	 * Array of <code>String</code> containing headers for the graph.
	 * The headers should correspond to the following:<br>
	 * <code>0</code>: Headline for the text<br>
	 * <code>1</code>: Headline for the numbers<br>
	 * <code>2</code>: Headline for the percents<br>
	 * <code>3</code>: Headline for the graph<br>
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $headerArray;

	/**
	 * Shall the numbers be shown on the graph?
	 * If <code>1</code> they shall be shown, if <code>0</code> not.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $showNumbers = 1;

	/**
	 * Do not show rows with a percent less than this value.
	 * The value is the percent between 0 and 1.
	 *
	 * @private
	 */
	var $minPercent = 0;

	/**
	 * Angiver om v�rdierne skal repr�senteres sorteret.
	 * <code>1</code> angiver de skal repr�senteres sorteret,
	 * <code>0</code> angiver de <em>ikke</em> skal repr�senters
	 * sorteret.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $sorted;

	/**
	 * En tekst der skal fremh�ves.
	 * Alle punkter p� grafen hvis tekst er lig med denne skal om
	 * muligt fremh�ves.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $emphasize;

	/**
	 * The with of the table.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $tableWith;

	/**
	 * Create a new instance.
	 * As a minimum the text, numbers and headers should be set using the
	 * functions <code>setTextArray(String[])</code>, <code>setNumArray(String[])/code>
	 * and <code>setHeaderArray(String[])</code>.
	 * As default a non sorted graph, with no emphasized text, numbers are
	 * showing and the width is <code>0.8</code> (80% in human terms).
	 *
	 * @public
	 * @param $siteContext an instance of the <code>SiteContext</code>.
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function __construct(&$siteContext)
	{
		parent::__construct($siteContext);
		$this->sorted = 0; /*Default: Not sorted*/
		$this->emphasize = ""; /*Default: None emphasized*/
		$this->showNumbers = 1; /*Default: Show the numbers on the graphs*/
		$this->tableWith = 0.8; /*Default: 80% in with*/
	}

	/**
	 * Sets the array containing the headers for the table.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $headerArray array of <code>String</code> containing headers
	 *        for the graph.
	 *        The indexes should correspond to the following:<br>
	 *        <code>0</code>: Headline for the text<br>
	 *        <code>1</code>: Headline for the numbers<br>
	 *        <code>2</code>: Headline for the percents<br>
	 *        <code>3</code>: Headline for the graph<br>
	 *        <p>If a non array <code>String</code> is given this will be
	 *         split/exploded with &quot;<code>::</code>&quot; as seperator.
	 * @return void
	 */
	function setHeaderArray($headerArray)
	{
		//If input is a String, split it.
		if (!is_array($headerArray))
			$this->headerArray = explode("::",$headerArray);
		else
			$this->headerArray = $headerArray;

		//Makes first letter in heach header upper case
		for ($i = 0; $i < sizeof($this->headerArray); $i++)
			$this->headerArray[$i] = ucfirst($this->headerArray[$i]);

		//Is the array valid?
		if (sizeof($headerArray) !== 4)
		{
			echo "<b>Error:</b> The array of table headers given to function <code>getHeaderArray(String)</code> in class <code>SiteGraph</code> on line ".__LINE__." does not contain the required four elements.";
			exit;
		}
	}

	/**
	 * Sets the array of numbers.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $numArray array  of <code>int</code> containing the hits
	 *        corresponding to the elements given in
	 *        <code>$textArray</code>. If a <code>Stirng</code> is given it
	 *        will be split using &quot;<code>::</code>&quot;
	 *        as seperator.
	 * @return void
	 */
	function setNumArray($numArray)
	{
		//If input is a String, split it.
		if (!is_array($numArray))
			$this->numArray = explode("::",$numArray);
		else
			$this->numArray = $numArray;
	}

	/**
	 * Sets the text array.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $textArray array of <code>String</code> containing the text
	 *        that is represented. If a <code>Stirng</code> is given it
	 *        will be split using &quot;<code>::</code>&quot;
	 *        as seperator.
	 * @return void
	 */
	function setTextArray($textArray)
	{
		//If input is a String, split it.
		if (!is_array($textArray))
			$this->textArray = explode("::",$textArray);
		else
			$this->textArray = $textArray;
	}

	/**
	 * Set if the numbers shall be shown.<br>
	 * <code>1</code>: They shall be shown<br>
	 * <code>0</code>: They shall not be shown.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $showNumbers
	 * @return void
	 */
	function setShowNumbers($showNumbers)
	{
		$this->showNumbers = $showNumbers;
	}

	/**
	 * Get if the numbers shall be shown.
	 * <br><code>1</code>: They shall be shown<br>
	 *     <code>0</code>: They shall not be shown.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return boolean if the numbers shall be shown.
	 */
	function getShowNumbers()
	{
		return $this->showNumbers;
	}

	/**
	 * Get the with of the table.
	 *
	 * @public
	 * @version 0.0.1
	 * @return long the with of the table.
	 * @since 0.0.1
	 */
	function getTableWith()
	{
		return $this->tableWith;
	}

	/**
	 * Set the with of the table.
	 *
	 * @public
	 * @version 0.0.1
	 * @return void
	 * @param the with of the table.
	 * @since 0.0.1
	 */
	function setTableWith($tableWith)
	{
		$this->tableWith = $tableWith;
	}

	/**
	 * Returns the array containing headers for the graph.
	 * The headers in each index is described in the constructor for the class.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] the array containing headers for the graph.
	 */
	function getHeaderArray()
	{
		return $this->headerArray;
	}

	/**
	 * Sets the text to emplasize.
	 * Alle punkter p� grafen hvis tekst er lig med denne skal om
	 * muligt fremh�ves.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param emphasize en tekst der skal fremh�ves.
	 * @return void
	 */
	function setEmphasize($emphasize)
	{
		$this->emphasize = $emphasize;
	}

	/**
	 * Returnerer en tekst der skal fremh�ves.
	 * Alle punkter p� grafen hvis tekst er lig med denne skal om
	 * muligt fremh�ves.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String en tekst der skal fremh�ves.
	 */
	function getEmphasize()
	{
		return $this->emphasize;
	}

	/**
	 * Angiver om de informationer der skal repr�senteres skal vises i sorteret form.
	 * <code>1</code> angiver de skal vises sorteret, <code>0</code> angiver at de
	 * ikke skal vises sorteret.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $sorted if the information shall be sorted.
	 * @return void
	 */
	function setSorted($sorted)
	{
		if (!($sorted == 1 or $sorted == 0))
		{
			echo "<b>Error:</b> Input to function <code>setSorted()</code> in class <code>HtmlTableGraph</code> must be <code>1</code> or <code>0</code>.";
			exit;
		}
		$this->sorted = $sorted;
	}

	/**
	 * Sorts the data data.
	 *
	 * @private
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function sortInput()
	{
		array_multisort(
			$this->numArray,SORT_DESC,SORT_NUMERIC,
			$this->textArray,SORT_DESC,SORT_STRING);
	}

	/**
	 * Make the input ready by removing invalid text/number pairs and
	 * possibly data as specified by {@link #getMinPercent()}.
	 *
	 * @private
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function makeInputReady()
	{
		//Remove invalid data.
		$out = Html::removeInvalidData($this->textArray, $this->numArray);
		$this->textArray = $out[0];
		$this->numArray  = $out[1];

		//Remove data with too few visits.
		$minPercent = $this->getMinPercent();
		if ($minPercent <= 0) {
			//Nothing to do.
			return;
		}

		list($percent, $foobar) = $this->getPercents();
		$newNum = array();
		$newTex = array();
		for ($i = 0; $i < count($this->numArray); $i++) {
			if (strlen($this->numArray[$i]) > 0and strlen($this->textArray[$i]) > 0
			    and $percent[$i] >= $minPercent) {
				$newNum[] = $this->numArray[$i];
				$newTex[] = $this->textArray[$i];
			}
		} //End for.
		$this->textArray = $newTex;
		$this->numArray  = $newNum;
	}

	/**
	 * Returns a two dimentional array containing percents (index 0) and relative percents (index 1)
	 * calculated from the numbers. In relative percents the largest number is
	 * 100% (1).<br>
	 * The numbers is not rounded, and 1 is equal to 100%, 0.5 is 50% etc.<br>
	 * Call this funtion in the last moment. If you use manipulating functions
	 * like <code>makeInputReady()</code> after calling this funtion, the data
	 * returned will not be valid anymore.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int[] an array containing percents calculates from the numbers.
	 */
	function getPercents()
	{
		//Gets the data
		$numArray = $this->numArray;
		//$textArray = $this->textArray;

		//Calculates the max and sum
		$sum = array_sum($numArray);
		if (count($numArray) > 0)
			$max = max($numArray);
		else
			$max = 0;
		/*$max = 0;
		for ($i = 0;$i < sizeof($numArray);$i++)
		{
			if ($numArray[$i] > $max)
				$max = $numArray[$i];
		}*/

		$percents = array();
		$relativePercents = array();

		//Calculates the percents
		if ($sum > 0 and $max > 0)
			for ($i = 0;$i < sizeof($numArray);$i++)
			{
				$percents[] = $numArray[$i]/$sum;
				$relativePercents[] = $numArray[$i]/$max;
			}
		return array($percents,$relativePercents);
	}

	/**
	 * Returns array of <code>String</code> containing the text the graph
	 * should be generated for.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String[] array of <code>String</code> containing the text the graph
	 *         should be generated for.
	 */
	function getTextArray()
	{
		return $this->textArray;
	}

	/**
	 * Returns array of <code>int</code> containing the hits corresponding
	 * to <code>$textArray</code>.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int[] array of <code>int</code> containing the hits corresponding
	 *         to <code>$textArray</code>.
	 */
	function getNumArray()
	{
		return $this->numArray;
	}

	/**
	 * Sets the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
	 *
	 * @public
	 * @param $minPercent the lowest value in percent of rows to show.
	 */
	function setMinPercent($minPercent) {
		$this->minPercent = $minPercent;
	}

	/**
	 * Returns the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
	 *
	 * @public
	 * @return the lowest value in percent of rows to show.
	 */
	function getMinPercent() {
		return $this->minPercent;
	}

	/**
	 * Sets array of <code>int</code> containing the hits corresponding to
	 * <code>$textArray</code>.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $numArray array of <code>int</code> containing the hits
	 *        corresponding to <code>$textArray</code>.
	 */
/* 	function setNumArray($numArray)
	{
		$this->numArray = $numArray;
	}
 */
} /*End of class SiteGraph*/

/**
 * Abstract class for create a series graph.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @author Simon Mikkelsen
 */
class SiteSeriesGraph extends SiteElement {
	/**
	 * The data series as the format:
	 * array('Text' => array(unixtime => value, ... ), ... )
	 *
	 * @private
	 */
	var $dataSeries = null;
	
	/**
	 * Do not show rows with a percent less than this value.
	 * The value is the percent between 0 and 1.
	 *
	 * @private
	 */
	var $minPercent = 0;
	
	/**
	 * Create a new instance.
	 * As a minimum the data series should be set using the
	 * function <code>setDataSeries(String[])</code>.
	 *
	 * @public
	 * @param $siteContext an instance of the <code>SiteContext</code>.
	 */
	function __construct(&$siteContext)
	{
		$this->SiteElement($siteContext);
	}
	
	/**
	 * Sets the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
	 * 
	 * @public
	 * @param $minPercent the lowest value in percent of rows to show.
	 */
	function setMinPercent($minPercent) {
		$this->minPercent = $minPercent;
	}

	/**
	 * Returns the lowest value in percent (between 0 and 1) of rows to
	 * show. Rows with a value lesser than this one will not be shown.
	 * 
	 * @public
	 * @return the lowest value in percent of rows to show.
	 */
	function getMinPercent() {
		return $this->minPercent;
	}
	
	/**
	 * Sets the data series as the format described in
	 * {@link SeriesStatGenerator#setDataSeries}.
	 * 
	 *
	 * @public $dataSeries the data series.
	 * @public
	 */
	 function setDataSeries($dataSeries) {
		 $this->dataSeries = $dataSeries;
	 }
	
	/**
	 * Returns the data series as the format described in
	 * {@link SeriesStatGenerator#setDataSeries}
	 * or <code>null</code> if not set yet.
	 *
	 * @return the data series.
	 * @public
	 */
	 function getDataSeries() {
		 return $this->dataSeries;
	 }
	 
	 /**
	  * Returns the data from {@link #getDataSeries} prepared in the following way:
	  * * All time stamps are aligned as requested, to the start of the hour or day.
		* * A percent value, which indicates the values corresponding percent, within the given
		*   hour or day (as specified).
		* * The array have the following structure:
	  *    array('Text' => array('META-INF' =>array(key => value, ...), unixtime => array(value, percent), ... ), ... )
		*   The META-INF array carries meta information, currently the following keys are used:
		*   totalHits: The total number of hits for the given text.
		* 
		* @public
	  */
	 function getDataSeriesPrepared($align) {
		 if ($align === 'day') {
			 $timeAligner = 'alignToDay'; // The name of a function in this class.
		 } elseif ($align === 'hour') {
			 $timeAligner = 'alignToHour'; // The name of a function in this class.
		 } else {
			 die("Illegal date aligner requested: \"$align\". It must be in lower case.");
		 }
		 
		 // Align times and find max values.
		 $dataSeries = $this->dataSeries;
		 $sumValuesTime = array();
		 $sumValuesLabel = array();
		 foreach ($dataSeries as $label => $values) {
			 foreach ($values as $unixtime => $hitCount) {
				 unset($dataSeries[$label][$unixtime]);
				 $unixTimeAligned = $this->$timeAligner($unixtime); // Call the function which name is stored in $timeAligner.
				 $dataSeries[$label][$unixTimeAligned] = $hitCount;
				 if (! isset($sumValuesTime[$unixTimeAligned])) {
					 $sumValuesTime[$unixTimeAligned] = 0;
				 }
				 $sumValuesTime[$unixTimeAligned] += $hitCount;
				 if (! isset($sumValuesLabel[$label])) {
					 $sumValuesLabel[$label] = 0;
				 }
				 $sumValuesLabel[$label] += $hitCount;
			 }
		 }

		 // Calculate percents.
		 foreach ($dataSeries as $label => $values) {
			 foreach ($values as $unixtime => $hitCount) {
				 // TODO don't calculate percents here. Let the sumValuesLabel be used at the sink.
				 if (isset($sumValuesTime[$unixtime]) and $sumValuesTime[$unixtime] != 0) {
					 $dataSeries[$label][$unixtime] = array($hitCount, $hitCount / $sumValuesTime[$unixtime]);
				 } else {
					 $dataSeries[$label][$unixtime] = array($hitCount, 0);
				 }
			 }
			 $dataSeries[$label]['META-INF'] = array('totalHits' => $sumValuesLabel[$label]);
		 }
		 return $dataSeries;
	 }
	 
	 /**
	  * Aligns the given Unix timestamp to the current hour and returns it.
		*
	  * @private
	  */
	 function alignToHour($unixtime) {
		 //             Hour Minute Second Month Day Year
		 return mktime(date($unixtime, "H"), 0, 0, date($unixtime, "n"), date($unixtime, "j"),
			 date($unixtime, "Y"));
	 }
	 
	 /**
	  * Aligns the given Unix timestamp to the current day and returns it.
		*
	  * @private
	  */
	 function alignToDay($unixtime) {
		 //             Hour Minute Second Month Day Year
		 return mktime(0, 0, 0, date("n", $unixtime), date("j", $unixtime),
			 date("Y", $unixtime));
	 }
 }


/**
 * Represents a table.
 * The number of columns in the table is decided by the number of elements
 * in the row that has the most columns.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteTable extends SiteElement
{
	/**
	 * Describes where the headers are. The following values are valid:<br>
	 * <code>0</code>: No headers<br>
	 * <code>1</code>: The top row are headers.
	 * <code>2</code>: The left column are headers.
	 * <code>3</code>: Both the top row and the leftcolumn are headers.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $headersAre;

	/**
	 * A name that defines the headers.
	 * E.g. in HTML it would be the value of the <code>class</code>
	 * attribute.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $headerClass;

	/**
	 * Specifies a specific class, a name that can define a column, for
	 * each column. E.g. it could i HTML be the value of the
	 * <code>class</code> attribute.
	 * Index 0 is the leftmost column.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $columnClassArray;

	/**
	 * A <code>String[][]</code> (&quot;x,y&quot;) witch define the content
	 * of the table.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $tableContent;

	/**
	 * Adds a row to the table.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param String[] the row that shall be added to
	 *        the table.
	 * @return void
	 */
	function addRow($row)
	{
		if (is_array($row))
			$this->tableContent[] = $row;
		else
		{
			echo "<b>Error:</b> Parameter for SiteTable.addRow must be an array. The value of the parameter is: (begin)<pre>$row</pre>(end)<br>";
			exit;
		}
	}

	/**
	 * Adds the rows to the table.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param String[][] the rows that shall be added to
	 *        the table.
	 * @return void
	 */
	function addRows($rows)
	{
		if (is_array($rows))
		{
			for ($i = 0; $i < sizeof($rows); $i++)
				$this->tableContent[] = $rows[$i];
		}
		else
		{
			echo "<b>Error:</b> Parameter for SiteTable.addRow must be an array. The value of the parameter is: (begin)<pre>$row</pre>(end)<br>";
			exit;
		}
	}

	/**
	 * Adds a seperator for the table, that seperates two parts visually.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function addSeperator()
	{
		//Not implemented
	}

	/**
	 * Returns the describtion where the headers are.
 	 * The following values are used:<br>
	 * <code>0</code>: No headers<br>
	 * <code>1</code>: The top row are headers.
	 * <code>2</code>: The left column are headers.
	 * <code>3</code>: Both the top row and the leftcolumn are headers.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int describes where the headers are.
	 */
	function getHeadersAre()
	{
		return $this->headersAre;
	}

	/**
	 * Sets the describtion where the headers are.
 	 * The following values are valid:<br>
	 * <code>0</code>: No headers<br>
	 * <code>1</code>: The top row are headers.<br>
	 * <code>2</code>: The left column are headers.<br>
	 * <code>3</code>: Both the top row and the leftcolumn are headers.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param int $headersAre describes where the headers are.
	 */
	function setHeadersAre($headersAre)
	{
		if ($headersAre >= 0 and $headersAre <= 3)
			$this->headersAre = $headersAre;
		else
		{
			echo "<b>Error:</b> SiteTable.setHeadersAre only acepts the numbers 0-3 as parameter.";
			exit;
		}
	}

	/**
	 * Returns a name that defines the headers.
	 * E.g. in HTML it would be the value of the <code>class</code>
	 * attribute.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String a name that defines the headers.
	 */
	function getHeaderClass()
	{
		return $this->headerClass;
	}

	/**
	 * Sets a name that defines the headers.
	 * E.g. in HTML it would be the value of the <code>class</code>
	 * attribute.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param String $headerClass a name that defines the headers.
	 */
	function setHeaderClass($headerClass)
	{
		$this->headerClass = $headerClass;
	}

	/**
	 * Returns a value that specifies a specific class, a name that can
	 * define a column, for each column.
	 * Index 0 is the leftmost column.
	 * E.g. it could i HTML be the value of the <code>class</code>
	 * attribute.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int a value that specifies a specific class, a name that can
	 *         define a column, for each column.
	 */
	function getColumnClassArray()
	{
		return $this->columnClassArray;
	}

	/**
	 * Sets a value that specifies a specific class, a name that can
	 * define a column, for each column.
	 * Index 0 is the leftmost column.
	 * E.g. it could i HTML be the value of the <code>class</code>
	 * attribute.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param String[] $columnClassArray specifies a specific class, a name that
	 *        can define a column, for each column.
	 */
	function setColumnClassArray($columnClassArray)
	{
		$this->columnClassArray = $columnClassArray;
	}

	/**
	 * Returns a <code>String[][]</code> (&quot;x,y&quot;) witch define the
	 * content of the table.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String[][] a <code>String[][]</code> (&quot;x,y&quot;) witch define the
	 *         content of the table.
	 */
	function getTableContent()
	{
		return $this->tableContent;
	}

	/**
	 * Sets a <code>String[][]</code> (&quot;x,y&quot;) witch define the
	 * content of the table.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param String[][] $tableContent defines the content of the table (&quot;x,y&quot;).
	 */
	function setTableContent($tableContent)
	{
		$this->tableContent = $tableContent;
	}

}

/**
 * Represents a pice of text.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteText extends SiteElement
{
	/**
	 * The represented text.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $text;

	/**
	 * Text to be inserted before text that should be emphasized.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $emphasizeStart = "";

	/**
	 * Text to be inserted after text that should be emphasized.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $emphasizeEnd = "";

	/**
	 * States how the text should be shown. The following values are
	 * valid:<br>
	 * 0: Just output the plain text<br>
	 * 1: Show as a new paragraph<br>
	 * 2: Show on a new line (newline before the text)<br>
	 * 3: End with a new line (newline after the text)<br>
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $paragraph = 1;

	/**
	 * Text that can be shown as a label if supported.
	 *
	 * @private
	 */
	var $label;

	/**
	 * Returns text that can be shown as a label if supported.
	 *
	 * @public
	 * @return text that can be shown as a label if supported.
	 */
	function getLabel() {
		return $this->label;
	}

	/**
	 * Sets text that can be shown as a label if supported.
	 *
	 * @public
	 * @param $label text that can be shown as a label if supported.
	 */
	function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * Returns states how the text should be shown.
	 * The following values are valid:<br>
	 * 0: Just output the plain text<br>
	 * 1: Show as a new paragraph<br>
	 * 2: Show on a new line (newline before the text)<br>
	 * 3: End with a new line (newline after the text)<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int states how the text should be shown.
	 */
	function getParagraph()
	{
		return $this->paragraph;
	}

	/**
	 * Sets states how the text should be shown.
	 * The following values are valid:<br>
	 *	-1: Output the plain text without any extra formating
	 * 0: Just output the plain text<br>
	 * 1: Show as a new paragraph<br>
	 * 2: Show on a new line (newline before the text)<br>
	 * 3: End with a new line (newline after the text)<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $paragraph states how the text should be shown.
	 */
	function setParagraph($paragraph)
	{
		$this->paragraph = $paragraph;
	}

	/**
	 * Returns the represented text.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the represented text.
	 */
	function getText()
	{
		return $this->text;
	}

	/**
	 * Sets the represented text.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $text the represented text.
	 */
	function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * Adds the text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $text the text that should be added.
	 * @return void
	 */
	function addText($text)
	{
		$this->text .= $text;
	}

	/**
	 * Adds the text, but display it in an emphasized fashion, if supported
	 * by the used generator.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $text the text that should be added.
	 * @return void
	 */
	function addEmphasizedText($text)
	{
		$this->addText($this->getEmphasizeStart().$text.$this->getEmphasizeEnd());
	}

	/**
	 * Returns the text to be inserted before text that should be emphasized.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the text to be inserted before text that should be emphasized.
	 */
	function getEmphasizeStart()
	{
		return $this->emphasizeStart;
	}

	/**
	 * Sets the text to be inserted before text that should be emphasized.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $emphasizeStart the text to be inserted before text that should be emphasized.
	 */
	function setEmphasizeStart($emphasizeStart)
	{
		$this->emphasizeStart = $emphasizeStart;
	}

	/**
	 * Returns the text to be inserted after text that should be emphasized.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the text to be inserted after text that should be emphasized.
	 */
	function getEmphasizeEnd()
	{
		return $this->emphasizeEnd;
	}

	/**
	 * Sets the text to be inserted after text that should be emphasized.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $emphasizeEnd the text to be inserted after text that should be emphasized.
	 */
	function setEmphasizeEnd($emphasizeEnd)
	{
		$this->emphasizeEnd = $emphasizeEnd;
	}

	/**
	 * Returns the code representation of this object.
	 * This standard implementation may be overwritten.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code representation of this object.
	 */
	function getCode()
	{
		return $this->getText();
	}

}

/**
 * Represents a list.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteList extends SiteElement
{
	/**
	 * The represented list. The first element in this is the top element
	 * of the list.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $list = array();

	/**
	 * Adds an <code>element</code> to the list; which must be an instance
	 * of {@link SiteElement}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $element the {@link SiteElement} to add to the list.
	 * @return void
	 */
	function addListElement($element)
	{
		if (strtolower($element->getParentClass()) !== "siteelement")
		{
			echo "Parameter for StatList.addListElement is not an instance of SiteElement, which it must be.";
			exit;
		}

		$this->list[] = $element;
	}

	/**
	 * Returns the represented list. The first element in this is the top
	 * element of the list.
	 *
	 * @public
	 * @since   0.0.1
	 * @version 0.0.1
	 * @return  SiteElement[] the represented list. The first element in this is the top
	 *          element of the list.
	 */
	function getList()
	{
		return $this->list;
	}

	/**
	 * Sets the represented list. The first element in this is the top
	 * element of the list.
	 *
	 * @public
	 * @since   0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param   $list the represented list. The first element in this is
	 *          the top element of the list.
	 */
	function setList($list)
	{
		$this->list = $list;
	}

} /*End of class SiteList*/

/**
 * Represents a number of hits and an url.
 *
 * <p><b>File:</b> SiteGenrator.php</p>
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteHitUrl extends SiteElement
{
	/**
	 * The represented url.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $url;

	/**
	 * The hits of the url.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $hits;


	/**
	 * Returns the represented url.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the represented url.
	 */
	function getUrl()
	{
		return $this->url;
	}

	/**
	 * Sets the represented url.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $url the represented url.
	 */
	function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Returns the hits of the url.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the hits of the url.
	 */
	function getHits()
	{
		return $this->hits;
	}

	/**
	 * Sets the hits of the url.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $hits the hits of the url.
	 */
	function setHits($hits)
	{
		$this->hits = $hits;
	}

} /*End of class SiteHitUrl*/

/**
 * Represents a checkbox.
 */
class CheckBox extends SiteElement {

	/**
	 * States if the checkbox is selected (1) or not (0).
	 */
	var $selected;

	/**
	 * Value used when checked. If not given the views default value is
	 * used.
	 */
	var $value;

	/**
	 * Returns the value used when checked. If not given the views default
	 * value is used.
	 *
	 * @returns String
	 * @reutrn the value used when checked.
	 * @public
	 */
	function getValue() {
		if (isset($this->value))
			return $this->value;
		else
			return '';
	}

	/**
	 * Sets the value used when checked. If not given the views default
	 * value is used.
	 *
	 * @returns void
	 * @param $value the value used when checked.
	 * @public
	 */
	function setValue($value) {
		$this->value = $value;
	}

	/**
	 * States if the checkbox is selected.
	 *
	 * @public
	 * @returns boolean
	 * @return if the checkbox is selected.
	 */
	function isSelected() {
		return ($this->selected === 1);
	}

	/**
	 * Sets if the checkbox is selected.
	 *
	 * @param $selected if the checkbox is selected. Use 1 for selected and
	 *                  0 for not selected.
	 * @public
	 * @returns void
	 */
	function setSelected($selected) {
		$this->selected = $selected;
	}

} /*End of class CheckBox*/

/**
 * Represents a submit button.
 */
class SubmitButton extends SiteElement {

	/**
	 * The text on the button.
	 */
	var $text;

	/**
	 * Shall a reset button be shown (1) or not (0).
	 */
	var $showResetButton;

	/**
	 * The text for the reset button, if any.
	 */
	var $resetButtonText;

	/**
	 * Returns the text on the button.
	 *
	 * @public
	 * @returns String
	 * @return the text on the button.
	 */
	function getButtonText() {
		return $this->text;
	}

	/**
	 * Sets the text on the button.
	 *
	 * @param $text the text to set.
	 * @returns void
	 * @public
	 */
	function setText($text) {
		$this->text = $text;
	}

	/**
	 * Returns if a reset button shall be shown (1) or not (0).
	 *
	 * @public
	 * @returns boolean
	 * @return if a reset button shall be shown.
	 */
	function getShowResetButton() {
		return $this->showResetButton;
	}

	/**
	 * Sets if a reset button shall be shown (1) or not (0).
	 *
	 * @public
	 * @returns void
	 * @param $showResetButton if a reset button shall be shown.
	 */
	function setShowResetButton($showResetButton) {
		$this->showResetButton = $showResetButton;
	}

	/**
	 * Returns the text for the reset button, if any.
	 *
	 * @public
	 * @returns String
	 * @return the text for the reset button, if any.
	 */
	function getResetButtonText() {
		return $this->resetButtonText;
	}

	/**
	 * Sets the text for the reset button, if any.
	 *
	 * @public
	 * @returns void
	 * @param $resetButtonText the text for the reset button, if any.
	 */
	function setResetButtonText($resetButtonText) {
		$this->resetButtonText = $resetButtonText;
	}

} /*End of class SubmitButton*/

/**
 * Represents a login form.
 */
class LoginForm extends SiteElement
{
	/**
	 * The url to submit the form to.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $url;

	/**
	 * The key to use associate the username with.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $keyUsername;

	/**
	 * The key to use associate the password with.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $keyPassword;

	/**
	 * The method to submit the form.
	 * Use either &quot;<code>POST</code>&quot; or &quot;<code>GET</code>&quot;.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $submitMethod;

	/**
	 * If the username is known in advance, it may be set here.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $username;

	/**
	 * Returns the url to submit the form to.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the url to submit the form to.
	 */
	function getUrl()
	{
		return $this->url;
	}

	/**
	 * Sets the url to submit the form to.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $url the url to submit the form to.
	 */
	function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Returns the key to use associate the username with.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the key to use associate the username with.
	 */
	function getKeyUsername()
	{
		return $this->keyUsername;
	}

	/**
	 * Sets the key to use associate the username with.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $keyUsername the key to use associate the username with.
	 */
	function setKeyUsername($keyUsername)
	{
		$this->keyUsername = $keyUsername;
	}

	/**
	 * Returns the key to use associate the password with.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the key to use associate the password with.
	 */
	function getKeyPassword()
	{
		return $this->keyPassword;
	}

	/**
	 * Sets the key to use associate the password with.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $keyPassword the key to use associate the password with.
	 */
	function setKeyPassword($keyPassword)
	{
		$this->keyPassword = $keyPassword;
	}

	/**
	 * Returns the method to submit the form.
	 * Returns either &quot;<code>POST</code>&quot;,
	 * &quot;<code>GET</code>&quot; or an empty string.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the method to submit the form.
	 */
	function getSubmitMethod()
	{
		return $this->submitMethod;
	}

	/**
	 * Sets the method to submit the form.
	 * Use either &quot;<code>POST</code>&quot; or
	 * &quot;<code>GET</code>&quot;.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $submitMethod the method to submit the form.
	 */
	function setSubmitMethod($submitMethod)
	{
		$this->submitMethod = $submitMethod;
	}

	/**
	 * Returns if the username is known in advance, it may be set here.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String if the username is known in advance, it may be set here.
	 */
	function getUsername()
	{
		return $this->username;
	}

	/**
	 * Sets if the username is known in advance, it may be set here.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $username if the username is known in advance, it may be set here.
	 */
	function setUsername($username)
	{
		$this->username = $username;
	}


} /*End of class LoginForm*/

/**
 * Enables the user to select a number of stats.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class StatSelector extends SiteElement
{

	/**
	 * The number of columns in selector. This only affects the main part of
	 * the selector, not the headers.
	 */
	var $columns = 4;

	/**
	 * Array of the selectable stats. Theese must all extend
	 * {@link StatGenerator}.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $stats;

	/**
	 * States if the stats in {@link $stats} are selected. The indexes
	 * of the two must correspond. <code>1</code> if the stat are selected
	 * now, else <code>0</code>.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $statsSelected;

	/**
	 * The {@link StatGenerator} used to instantiate this.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $statGenerator;

	/**
	 * States how many stats are selected.
	 */
	var $selectionCount = 0;

	/**
	 * The maximum number of stats to show, if &lt; 0 unlimited.
	 *
	 * @private
	 * @since 2.1.0
	 */
 	var $maxStatsToShow = -1;

	/**
	 * The object to handle forms. Must be an instance of @c FormWrapper.
	 * If not set one is created when required.
	 *
	 * @private
	 */
	var $formWrapper = NULL;

	/**
	 * An object that allowes a time span to be selected.
	 * Only used if set. Must be an instance of @c TimeSelector.
	 *
	 * @private
	 */
	var $timeSelector = NULL;

	/**
	 * If set, an associative array of http params that must be
	 * passed along with all urls.
	 *
	 * @private
	 */
	var $passThroughParams = NULL;

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $siteContext the instance of {@link SiteContext} to use.
	 * @param $statGenerator the {@link SiteGenerator} used to instantiate this.
	 */
	function __construct(&$siteContext, &$siteGenerator)
	{
		parent::__construct($siteContext);
		$this->siteGenerator = &$siteGenerator;
		$this->passThroughParams = array();
	}

	/**
	 * Parses the <code>$HTTP_VARS</code> and applies the selection information
	 * to this object.
	 */
	function applySelectionInformation($HTTP_VARS)
	{
		echo "applySelectionInformation is not yet implemented";
	}

	/**
	 * Returns the represented code.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getCode()
	{
		$table = $this->siteGenerator->newElement('table');
		$table->addRow($this->getHeadRows());
		$table->addRows($this->getMainRows());

		if ($this->formWrapper !== NULL) {
			$formWrapper = $this->formWrapper;
		} else {
			$formWrapper = $this->siteGenerator->newElement('formwrapper');
			$formWrapper->setSubmitUrl($this->siteContext->getOption('urlStatsite'));
			$formWrapper->setMethod('GET');
			foreach ($this->passThroughParams as $param) {
				$formWrapper->addPassThroughParams($param);
			}
		}

		//Add type selector
		$typeSel = $this->siteGenerator->newElement('typeSelector');
		$typeSel->setSelectedType($this->siteContext->getHttpVar('type'));
		$table->addTailElement($typeSel);

		//Add time selector, if requested.
		if ($this->timeSelector !== NULL) {
			$table->addTailElement($this->timeSelector);
		}

		//Add buttons
		$submitButton = $this->siteGenerator->newElement('submitButton');
		$submitButton->setText($this->siteContext->getLocale('sgShowSel'));
		$submitButton->setShowResetButton(1);
		$submitButton->setResetButtonText($this->siteContext->getLocale('sgResetSel'));
		$table->addTailElement($submitButton);

		$formWrapper->setWrapped($table);
		return $formWrapper->getWrappedCode();
	}

	/**
	 * Returns if all stats are selected. <code>1</code> for <code>true</code>,
	 * <code>0</code> for <code>false</code>.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return boolean if all stats are selected.
	 */
	function isAllSelected() {
		//In low bandwidth mode all stats can usually not be selected.
		if ($this->maxStatsToShow >= 0)
			return 0;

		$cnt = $this->getSize();
		for ($i = 0; $i < $cnt; $i++) {
			if ($this->isSelected($i) === 0)
				return 0;
		}
		return 1;
	}

	/**
	 * Returns the rows of all the registered stats.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[][] an array of the rows.
	 */
	function getMainRows()
	{
		$returnArr = array();

		$codelib = $this->siteContext->getCodeLib();
		$dataSource = &$codelib->getDataSource();
		if ($dataSource !== NULL) {
			$username = $dataSource->getUsername();
		}

		//Just list them all
		$noStats = $this->getSize();
		$row = $this->columns;
		$currentRow = array();
		for ($i = 0; $i < $noStats; $i++) {
			$currentStatElement = $this->getStat($i);

			$currentStatUrlBuilder = $this->siteContext->getUrlBuilder("statsite");
			$currentStatUrlBuilder->setStatVisible($currentStatElement->getIdentifier());

			//Add the http parameters to pass through. E.g. username (danish: brugernavn).
			$this->addPassThroughParamsUrlBuilder($currentStatUrlBuilder);
/*			foreach ($this->passThroughParams as $param) {
				if ($param === 'brugernavn') {
					$currentStatUrlBuilder->setParameter($param, $username);
				} else {
					$value = $this->siteContext->getHttpVar($param);
					if (strlen($value) > 0) {
						$currentStatUrlBuilder->setParameter($param, $value);
					}
				}
			}*/
			$currentStatText = $this->siteGenerator->newElement('text');
			$currentStatText->setParagraph(0);
			$currentStatText->setText($this->siteContext->getLocale($currentStatElement->getHeadlineKey()));
			$currentStatUrl = $this->siteGenerator->newElement('urlWrapper');
			$currentStatUrl->setUrl($currentStatUrlBuilder->createUrl());
			$currentStatCheckbox = $this->siteGenerator->newElement('checkbox');
			$currentStatCheckbox->setElementName('show[]');
			$currentStatCheckbox->setValue($currentStatElement->getIdentifier());
			$currentStatCheckbox->setSelected($this->isSelected($i) ? 1 : 0);
			$currentStatUrl->addHeadElement($currentStatCheckbox);

			//The selection is only inactive if only this stat is selected
			if ($this->getSelectionCount() === 1 and $this->isSelected($i))
				$currentStatUrl->setActive(0);
			else
				$currentStatUrl->setActive(1);

			$currentStatUrl->setWrapped($currentStatText);

			$currentRow[] = $currentStatUrl->getCode();
			$row--;
			if ($row === 0 | $i === $noStats - 1) {
				$row = $this->columns;
				$returnArr[] = $currentRow;
				$currentRow = array();
			}
		} //End for

		return $returnArr;
	}

	/**
	 * Returns an array containing the head row.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] the head row.
	 */
	function getHeadRows()
	{
		$prevStat = -1;
		$nextStat = -1;

		//Find the index of previous and next
		$noStats = $this->getSize();
		//Find the first selected stat
		$firstSelected = -1;
		for ($i = 0; $i < $noStats and $firstSelected == -1; $i++)
		{

			if ($this->isSelected($i) === 1)
				$firstSelected = $i;
		}

		//Find the previous
		if ($firstSelected === -1) {
			$prevStat = -1;
		} else {
			//Find the previous
			$prevFirstSelected = $firstSelected -1;
			if ($prevFirstSelected < 0)
				$prevFirstSelected = $noStats-1;
			$prevStat = -1;
			for ($i = $prevFirstSelected; $i >= 0 and $prevStat === -1; $i--) {
				if ($this->isSelected($i) === 0)
					$prevStat = $i;
			}
			for ($i = $noStats-1; $i > $prevFirstSelected and $prevStat === -1; $i--) {
				if ($this->isSelected($i) === 0)
					$prevStat = $i;
			}

			//Find the next
			$nextStat = -1;
			for ($i = $firstSelected; $i < $noStats and $nextStat === -1; $i++) {
				if ($this->isSelected($i) === 0)
					$nextStat = $i;
			}
			for ($i = 0; $i < $firstSelected and $nextStat === -1; $i++) {
				if ($this->isSelected($i) === 0)
					$nextStat = $i;
			}
		}

		//Find the next
		//$nextStatCandidate = $this->getStat($firstSelected + 1);
//			if ($firstSelected === -1)
/*		if (is_object($nextStatCandidate)) {
			$nextStat = $firstSelected + 1;
		} else {
			$nextStat = -1;
		}
*/
		//Find the username of the user
		$codelib = &$this->siteContext->getCodeLib();
		$dataSource = &$codelib->getDataSource();

		//Only parse on username and pwd if given - and that requires a data source.
/*		$username = "";
		$userPassword = "";
		if ($dataSource !== NULL) {
			$username = $dataSource->getUsername();
			$userPassword = $this->siteContext->getHttpVar('brugerkodeord');
		}
*/
		//Previous
		$headArray = array();
		if ($prevStat != -1)
		{
			$prevStatElement = $this->getStat($prevStat);
			$prevStatUrlBuilder = &$this->siteContext->getUrlBuilder("statsite");
			$this->addPassThroughParamsUrlBuilder($prevStatUrlBuilder);
			//$prevStatUrlBuilder->setParameter('brugernavn', $username);
			//$prevStatUrlBuilder->setParameter('brugerkodeord', $userPassword);
			$prevStatUrlBuilder->setStatVisible($prevStatElement->getIdentifier());
			$prevStatText = $this->siteGenerator->newElement('text');
			$prevStatText->setParagraph(0);
			$prevStatText->setText($this->siteContext->getLocale("sgPrevious"));
			$prevStatUrl = $this->siteGenerator->newElement('urlWrapper');
			$prevStatUrl->setUrl($prevStatUrlBuilder->createUrl());
			$prevStatUrl->setWrapped($prevStatText);
			$headArray[] = $prevStatUrl->getCode();
		} else {
			$prevStatText = $this->siteGenerator->newElement('text');
			$prevStatText->setParagraph(0);
			$prevStatText->setText($this->siteContext->getLocale("sgPrevious"));
			$headArray[] = $prevStatText->getCode();
		}

		//All
		$allStatUrlBuilder = &$this->siteContext->getUrlBuilder("statsite");
		$this->addPassThroughParamsUrlBuilder($allStatUrlBuilder);
		//$allStatUrlBuilder->setParameter('brugernavn', $username);
		//$allStatUrlBuilder->setParameter('brugerkodeord', $userPassword);
		$allStatUrlBuilder->setShowall();
		$allStatText = $this->siteGenerator->newElement('text');
		$allStatText->setParagraph(0);
		$allStatText->setText($this->siteContext->getLocale("sgAll"));
		$allStatUrl = $this->siteGenerator->newElement('urlWrapper');
		$allStatUrl->setUrl($allStatUrlBuilder->createUrl());
		$allStatUrl->setActive($this->isAllSelected()?0:1);
		$allStatUrl->setWrapped($allStatText);
		$headArray[] = $allStatUrl->getCode();

		//The rest
		$restStatUrlBuilder = &$this->siteContext->getUrlBuilder("statsite");
		$this->addPassThroughParamsUrlBuilder($restStatUrlBuilder);
		//$restStatUrlBuilder->setParameter('brugernavn', $username);
		//$restStatUrlBuilder->setParameter('brugerkodeord', $userPassword);
		for ($i = 0; $i < $noStats; $i++)
		{
			if ($this->isSelected($i) === 0) {
				$restStatElement = $this->getStat($i);
				$restStatUrlBuilder->setStatVisible($restStatElement->getIdentifier());
			}
		}
		$restStatText = $this->siteGenerator->newElement('text');
		$restStatText->setParagraph(0);
		$restStatText->setText($this->siteContext->getLocale("sgRest"));
		$restStatUrl = $this->siteGenerator->newElement('urlWrapper');
		$restStatUrl->setUrl($restStatUrlBuilder->createUrl());
		$restStatUrl->setActive($this->isAllSelected()?0:1);
		$restStatUrl->setWrapped($restStatText);
		$headArray[] = $restStatUrl->getCode();

		//Next
		if ($nextStat !== -1)
		{
			$nextStatElement = $this->getStat($nextStat);
			$nextStatUrlBuilder = &$this->siteContext->getUrlBuilder("statsite");
			$this->addPassThroughParamsUrlBuilder($nextStatUrlBuilder);
			//$nextStatUrlBuilder->setParameter('brugernavn', $username);
			//$nextStatUrlBuilder->setParameter('brugerkodeord', $userPassword);
			$nextStatUrlBuilder->setStatVisible($nextStatElement->getIdentifier());
			$nextStatText = $this->siteGenerator->newElement('text');
			$nextStatText->setParagraph(0);
			$nextStatText->setText($this->siteContext->getLocale("sgNext"));
			$nextStatUrl = $this->siteGenerator->newElement('urlWrapper');
			$nextStatUrl->setUrl($nextStatUrlBuilder->createUrl());
			$nextStatUrl->setWrapped($nextStatText);
			$headArray[] = $nextStatUrl->getCode();
		} else {
			$nextStatText = $this->siteGenerator->newElement('text');
			$nextStatText->setParagraph(0);
			$nextStatText->setText($this->siteContext->getLocale("sgNext"));
			$headArray[] = $nextStatText->getCode();
		}

		return $headArray;
	}

	/**
	 * Adds the pass through parameters with values to the given instance of
	 * {@link StatSiteUrlBuilder}.
	 *
	 * @param &amp;$urlBuilder the one to add to. The object is parsed as a
	 *                         reference, so it will be changed.
	 */
	function addPassThroughParamsUrlBuilder(&$urlBuilder) {
		foreach ($this->passThroughParams as $param) {
			$value = $this->siteContext->getHttpVar($param);
			if (strlen($value) > 0) {
				$urlBuilder->setParameter($param, $value);
			}
		}
	}

	/**
	 * Returns an array of http params that must be passed along with all urls.
	 *
	 * @return an array of http params that must be passed along with all urls.
	 * @private
	 */
	function getPassThroughParams() {
		return $this->passThroughParams;
	}

	/**
	 * Sets an array of http params that must be passed along with all urls.
	 *
	 * @param $passThroughParams an array of http params that must be passed
	 *                           along with all urls.
	 * @private
	 */
	function setPassThroughParams($passThroughParams) {
		$this->passThroughParams = $passThroughParams;
	}

	/**
	 * Returns the number of keys.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int the number of keys.
	 */
	function getSize()
	{
		return sizeof($this->stats);
	}

	/**
	 * Returns stat number <code>$n</code>. The first stat is number
	 * <code>0</code>.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $n the number of the stat to return.
	 * @return StatGenerator the <code>$n</code>'th stat.
	 */
	function &getStat($n)
	{
		return $this->stats[$n];
	}

	/**
	 * Returns if the <code>$n</code>'th key is selected.
	 * <code>1</code> is returned for true, <code>0</code> is returned for
	 * false.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $n the number of the key to return info about.
	 * @return int if the <code>$n</code>'th key is selected.
	 */
	function isSelected($n)
	{
		//In low bandwidth mode only return the n first
		if ($this->maxStatsToShow >= 0) {
			if (!$this->statsSelected[$n])
				return 0;

			$selCount = 0;
			for ($i = 0; $i < count($this->statsSelected); $i++) {
				if ($this->statsSelected[$i]) {
					$selCount++;
					if ($i === $n and $selCount > $this->maxStatsToShow) {
						return 0;
					} //End if is this the requested and is it ok to view it
				} //End if selected
			} //End for
		} //End is we in low bw mode

		return $this->statsSelected[$n];
	}

	/**
	 * Returns how many stats are selected.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return int how many stats are selected.
	 */
	function getSelectionCount() {
		//In low band width mode, return not more than the max stats to show
		if ($this->siteContext->getOption('low_bandwidth')) {
			if ($this->selectionCount >
			          $this->siteContext->getOption('low_bandwidth_max_stats')) {
				return $this->siteContext->getOption('low_bandwidth_max_stats');
			}
		}

		return $this->selectionCount;
	}

	/**
	 * Sets if the stat given by the <code>$statKey</code> shall be
	 * selected as stated. If <code>0</code> is given as selection value
	 * the stat will not be selected, if <code>1</code> is given it
	 * will be selected.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $statKey identifies the stat.
	 * @param $isSelected states if the stat should be selected
	 *                 (<code>1</code>) or not (<code>0</code>).
	 */
	function setSelected($statKey, $isSelected) {
		//If all selected - select all

		if ($statKey === StatSiteUrlBuilder::getKeyAll()) {
			$this->statsSelected = array_fill(0, count($this->statsSelected), $isSelected);
			$this->selectionCount = count($this->statsSelected);
			return;
		}

		foreach ($this->stats as $n => $statGenerator) {
			if ($statKey === $statGenerator->getIdentifier()) {
				if ($isSelected != $this->isSelected($n)) {
					if ($isSelected === 1) {
						$this->selectionCount++;
					} else {
						$this->selectionCount--;
					}
				$this->statsSelected[$n] = $isSelected;
				} //End does the selection differ from the current?

				return;
			}
		} //End foreach
	}

	/**
	 * Returns the stats which are selected.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return the stats which are selected.
	 * @returns StatGenerator[]
	 */
	function getSelectedStats() {
		$noStats = $this->getSize();
		$stats = array();
		for ($i = 0; $i < $noStats; $i++) {
			if ($this->isSelected($i)) {
				$stats[] = $this->getStat($i);
			} //End if
		} //End for
		return $stats;
	}

	/**
	 * Adds a <code>$stat</code> which can be <code>$selected</code> or not.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $stat the stat to add. This must extend {@link StatGenerator}.
	 * @param $selected <code>1</code> if the stat are selected now, else
	 *                  <code>0</code>.
	 * @return void
	 */
	function addStatKey(&$stat, $selected)
	{
		if (! is_subclass_of($stat, 'StatGenerator'))//(strtolower($stat->getParentClass()) != "statgenerator")
		{
			die("<b>Error:</b> Parameter \$stat is not an instance of a class derived form <code>StatGenerator</code>, which it must be. It is: ".get_class($stat));
		}

		if ($selected != 0 and $selected != 1)
		{
			die("<b>Error:</b> Parameter \$selected for method StatSelector->addStatKey must be either 0 or 1, but it is ".$selected.".");
		}

		$this->stats[] = &$stat;
		$this->statsSelected[] = $selected;
		if ($selected === 1)
			$this->selectionCount++;
	}

	/**
	 * Returns the maximum number of stats to show, if &lt; 0 unlimited.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @returns int
	 * @return the maximum number of stats to show, if &lt; 0 unlimited.
	 */
	function getMaxStatsToShow() {
		return $this->maxStatsToShow;
	}

	/**
	 * Sets the maximum number of stats to show, if &lt; 0 unlimited.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $maxStatsToShow the maximum number of stats to show, if
	 *                        &lt; 0 unlimited.
	 */
	function setMaxStatsToShow($maxStatsToShow) {
		$this->maxStatsToShow = $maxStatsToShow;
	}

	/**
	 * Sets the object to handle forms. Must be an instance of
	 * @c FormWrapper.
	 *
	 * @param $formWrapper the object to handle forms.
	 * @public
	 */
	 function setFormWrapper(&$formWrapper) {
		 $this->formWrapper = &$formWrapper;
	 }

	/**
	 * Returns the object to handle forms. Must be an instance of
	 * @c FormWrapper.
	 * May be @c NULL.
	 *
	 * @param $formWrapper the object to handle forms.
	 * @public
	 */
	 function &getFormWrapper() {
		 return $this->formWrapper;
	 }

	/**
	 * Sets an object that allowes a time span to be selected.
	 * Must be an instance of @c TimeSelector.
	 *
	 * @param $timeSelector an object that allowes a time span to be
	 *                      selected.
	 * @public
	 */
	function setTimeSelector(&$timeSelector) {
		if (! is_a($timeSelector, 'TimeSelector')) {
			echo "Error: The object given to StatSelector::setTimeSelector must be an instance of TimeSelector, which it is not.";
			exit();
		}

		$this->timeSelector = &$timeSelector;
	}

	/**
	 * Returns an object that allowes a time span to be selected or @c NULL.
	 * Only used if set. Must be an instance of @c TimeSelector.
	 *
	 * @return an object that allowes a time span to be selected.
	 * @public
	 */
	function &getTimeSelector() {
		return $this->timeSelector;
	}

} /* End of class StatSelector*/

/**
 * Creates a selector to select between the different types of stat sites.
 */
class TypeSelector extends SiteElement {
	 //*    <dt><code>typeSelector</code></dt>
	 //*       <dd>Generates a selector for different types of stat sites.</dd>
	 /**
	  * The instance of the SiteGenerator used to create this one.
		*/
	 var $siteGenerator;

	 /**
	  * The text string (as defined in StatGenerator) which identifies the
		* type of stat site to show.
		*/
	 var $selectedType;

	 /**
	  * Sets the instance of the site generator used to create this object.
		*/
	 function setSiteGenerator(&$siteGenerator) {
		 $this->siteGenerator = &$siteGenerator;
	 }

	 /**
	  * Sets the text string (as defined in StatGenerator) which identifies
		* the type of stat site to show.
		*/
	 function setSelectedType($selectedType) {
		 $this->selectedType = $selectedType;
	 }

	 /**
	  * Empty default for instances which does not create any code.
		*/
	 function getCode() {
			return "";
	 }
} /* End of class TypeSelector */

/**
 * General purpose class for making a visible calendar spanning over 1 month.
 *
 * @author Simon Mikkelsen
 */
class CalendarMaker extends SiteElement {

	/**
	 * A unix time stamp that falls within the month the calendar
	 * is made for.
	 */
	var $month = NULL;

	/**
	 * Array of links that are active for the days.
	 * Indeces without link can contain an empty string.
	 * Index 0 must always contain an empty string.
	 * Index 1 is the 1st day of the month.
	 */
	var $dayLinks;

	/**
	 * Array of links that are active for the weeks.
	 * Indeces without link can contain an empty string.
	 * Index 0 must always contain an empty string.
	 * Index 1 is the first month of the year.
	 */
	var $weekLinks;

	/**
	 * The link on the months name or an empty string.
	 */
	var $monthLink;

	/**
	 * The link on the year or an empty string.
	 */
	var $yearLink;

	/**
	 * Show the year after the name of the month.
	 */
	var $yearAfterMonth = true;

	/**
	 * Array of the full names of the months.
	 * If not given, a default english list will be used.
	 * Index 0 must be an empty string, the months must have the index
	 * of their number.
	 */
	var $monthNames = array('', 'January', 'February', 'March', 'April',
	                        'May', 'June', 'July', 'August',
				'September', 'October', 'November', 'December');

	/**
	 * Array of one or two letter short names of the week days.
	 * If not given, a default english list wil be used.
	 * Index 0 must be an empty string. Index 1 is Monday and index
	 * 7 is Sunday.
	 */
	var $dayNames = array('', 'm', 't', 'w', 't', 'f', 's', 's');

	/**
	 * The label used for the week column.
	 */
	var $weekLabel = 'week';

	/**
	 * Creates a new instance.
	 *
	 * @param $month a unix time stamp within the month to generate code for.
	 */
	function __construct($month) {
		$this->setMonthTimestamp($month);
	}

	/**
	 * Returns the code representation of this object.
	 * This function must be overwritten.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code representation of this object.
	 */
	function getCode()
	{
		return $this->draw();
	}

	/**
	 * Output the code of the calendar.
	 */
	function draw() {
		if ($this->month === NULL)
			die("The month must have been set using setMonthTimestamp() (".__FILE__." line ".__LINE__.").");

		//Find a timestamp within the first day of
		//the month we are in.
		//hour minute second month day year is_dst
		$firstDay = mktime(0, 0, 0, date('n', $this->month), 1,
			date('Y', $this->month));

		//Find the first week day of that month (0-6).
		$dfMonth = date('w', $firstDay);

		//PHP starts Sunday - we starts Monday:
		$dfMonth--;
		if ($dfMonth == -1) {
			$dfMonth = 6;
		}

		$code = $this->makeStart();
		//Make the days before this month.
		for ($i = 0; $i < $dfMonth; $i++) {
			$ctime = $firstDay - ($dfMonth - $i)*24*3600;
			$day = mktime(0, 0, 0,
			       date('n', $ctime),
			       date('j', $ctime),
			       date('Y', $ctime),
			       date('I', $ctime));
			if (date('w', $day) == 1) {
				$code .= $this->makeWeekStart($day);
			}
			$code .= $this->makeDay($day, true);
		}

		//Make currente month.
		$cmonth = date('n', $firstDay);
		$lastDay = -1;
		for ($day = $firstDay; $cmonth == date('n', $day);
		                                   $day += 24*3600) {
			if (date('w', $day) == 1) {
				$code .= $this->makeWeekStart($day);
			}

			$code .= $this->makeDay($day);

			if (date('w', $day) == 0) {
				$code .= $this->makeWeekEnd();
			}
			$lastDay = $day;
		}

		//Make the remaining days, from thenext month.
		for ($day = $lastDay + 24*3600; date('w', $day) != 1; $day += 24*3600) {
			$code .= $this->makeDay($day, true);
			if (date('w', $day) == 0) {
				$code .= $this->makeWeekEnd();
			}
		}

		$code .= $this->makeEnd();
		return $code;
	}

	/**
	 * Returns the code of the main headline and the names of the days.
	 * E.g.
	 *     January
	 *   m t w t s s
	 *
	 * @param $month unix time stamp within the month.
	 */
	function makeStart() {
		die("This function in ".__FILE__." line ".__LINE__." must be derived.");
	}

	/**
	 * Returns the end of the calendar.
	 * Unless there are a border or a table ending, this function
	 * can return nothing.
	 *
	 * @return the end of the calendar.
	 */
	function makeEnd() {
		die("This function in ".__FILE__." line ".__LINE__." must be derived.");
	}

	/**
	 * Returns the code to make a day.
	 *
	 * @param $date unix time stamp within the day.
	 * @param $otherMonth is the day in anothter
	 *                    month?
	 * @return the code to start a day.
	 */
	function makeDay($date, $otherMonth = false) {
		die("This function in ".__FILE__." line ".__LINE__." must be derived.");
	}

	/**
	 * Returns the code to start a week.
	 *
	 * @param $week unix time stamp within the week in question.
	 * @return the code to start a week.
	 */
	function makeWeekStart($week) {
		die("This function in ".__FILE__." line ".__LINE__." must be derived.");
	}

	/**
	 * Returns the code to end a week.
	 *
	 * @return the code to end a week.
	 */
	function makeWeekEnd() {
		die("This function in ".__FILE__." line ".__LINE__." must be derived.");
	}

	/**
	 * Sets an array of links that are active for the days.
	 * Indeces without link can contain an empty string.
	 * Index 0 must always contain an empty string.
	 * Index 1 is the 1st day of the month.
	 *
	 * @param $dayLinks array of links that are active for
	 *                  the days.
	 */
	function setDayLinks($dayLinks) {
		$this->dayLinks = $dayLinks;
	}

	/**
	 * Sets an array of links that are active for the weeks.
	 * Indeces without link can contain an empty string.
	 * Index 0 must always contain an empty string.
	 * Index 1 is the first month of the year.
	 *
	 * @param $weekLinks array of links that are active for the
	 *                   weeks.
	 */
	function setWeekLinks($weekLinks) {
		$this->weekLinks = $weekLinks;
	}

	/**
	 * Sets a unix time stamp that falls within the month that
	 * the calendar is made for.
	 *
	 * @param $month unix time stamp that falls within the month
	 *               that the calendar is made for.
	 */
	function setMonthTimestamp($month) {
		$this->month = $month;
	}

	/**
	 * Sets the link on the months name or an empty string.
	 *
	 * @param $monthLink the link on the months name or an
	 *                   empty string.
	 */
	function setMonthLink($monthLink) {
		$this->monthLink = $monthLink;
	}

	/**
	 * Sets if the year shall be shown after the name of the month
	 * (<code>true</code> or <code>false</code>). This is required for
	 * {@link #setYearLink} to be used.
	 *
	 *
	 * @param $yearAfterMonth if the year shall be shown after the name of the month.
	 */
	function setYearAfterMonth($yearAfterMonth) {
		$this->yearAfterMonth = $yearAfterMonth;
	}

	/**
	 * Sets the link for the year, or an empty string.
	 * Setting {@link #setYearAfterMonth} to <code>false</code> disables
	 * this feature.
	 *
	 * @param $yearLink the link for the year.
	 */
	function setYearLink($yearLink) {
		$this->yearLink = $yearLink;
	}

	/**
	 * Sets an array of one or two letter short names of the week days.
	 * If not set, a default english list wil be used.
	 * Index 0 must be an empty string. Index 1 is Monday and index
	 * 7 is Sunday.
	 *
	 * @param $dayNames array of short names of the week days.
	 */
	function setDayNames($dayNames) {
		$this->dayNames = $dayNames;
	}

	/**
	 * Sets the label used for the week column.
	 *
	 * @param the label used for the week column.
	 */
	function setWeekLabel($weekLabel) {
		$this->weekLabel = $weekLabel;
	}

	/**
	 * Sets an array of names of the months.
	 * If not set, a default english list wil be used.
	 * Index 0 must be an empty string. Index 1 is January and index
	 * 12 is December.
	 *
	 * @param $monthNames array of names months.
	 */
	function setMonthNames($monthNames) {
		$this->monthNames = $monthNames;
	}
}


/**
 * Used to join elements.
 */
class ElementJoiner
{
	/**
	 * Joins the given <code>$elements</code>.
	 * 
	 * @param $elements a one dimentional array which shall be joined.
	 */
	function joinElements($elements)
	{
		$out = '';
		for ($i = 0; $i < count($elements); $i++)
			$out .= $elements[$i];
		return $out;
	}
}

/**
 * Represents an additional system header for a site.
 */
class SiteHeader {
	/**
	 * The raw code of the header.
	 * @private
	 */
	var $code;
	
	/**
	 * What scheme to use the header for.
	 * See {@link #setScheme()}.
	 
	 * @private
	 */
	var $scheme;
	
	/**
	 * Creates a new instance.
	 *
	 * @scheme The scheme of the header, see {@link #setScheme()}.
	 * @code The raw code of the header.
	 * @public
	 */
	function __construct($scheme, $code) {
		$this->scheme = $scheme;
		$this->code = $code;
	}
	
	/**
	 * Sets the raw code of the header.
	 * @public
	 */
	function setCode($code) {
		$this->code = $code;
	}
	 
	/**
	 * Returns the raw code of the header.
	 * @public
	 */
	function getCode() {
		return $this->code;
	}

 	/**
	 * Sets what scheme to use the header for. The following values are currently
	 * supoprted:
	 * @code http: Http header, code must be in the form Key: value.
	 * @code html: HTML header, code must be possible to insert between the
	 *                          head tags.
	 * @code text: Text header: Not defined.
	 * @code csv:  Semi colon separated output: Not defined.
	 * @public
	 */
	function setScheme($scheme) {
		$this->scheme = $scheme;
	}

 	/**
	 * Returns what scheme to use the header for, see {@link #setScheme()}.
	 * @public
	 */
	function getScheme() {
		return $this->scheme; 
	}

}
?>