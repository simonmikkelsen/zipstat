<?php

/**
 * Use this class to generate a comma separated file.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvGenerator extends SiteGenerator
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator = NULL;

	/**
	 * Returnerer en ny instans af et <code>SiteElement</code>.
	 * Dette passer til den type side man har valgt. Flgende liste
	 * angiver elementtyper der altid er understttet. De enkelte
	 * generatorer kan derudover vlge at understtte andre elementer.
	 * <dt>
	 * 	<dt><code>table</code></dt>
	 * 		<dd>Genererer en tabel.</dd>
	 * 	<dt><code>graphTable</code></dt>
	 * 		<dd>Genererer en tabel med grafer.</dd>
	 * 	<dt><code>headline</code></dt>
	 * 		<dd>Genererer en overskrift.</dd>
	 * 	<dt><code>text</code></dt>
	 * 		<dd>Genererer en tekst.</dd>
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
	 * @param $siteElementType en tekststreng der identificerer objekttypen
	 *        (se tabellen i beskrivelsen).
	 * @return SiteElement en ny instans af et <code>SiteElement</code>.
	 */
	function newElement($siteElementType)
	{
		if ($siteElementType == 'table')
			$csvElement = new CsvTable($this->siteContext);
		elseif ($siteElementType == 'graphTable')
			$csvElement = new CsvTableGraph($this->siteContext);
		elseif ($siteElementType == 'headline')
			$csvElement = new CsvHeadline($this->siteContext);
		elseif ($siteElementType == 'text')
			$csvElement = new CsvText($this->siteContext);
		elseif ($siteElementType == 'list')
			$csvElement = new CsvSiteList($this->siteContext);
		elseif ($siteElementType == 'hiturl')
			$csvElement = new CsvSiteHitUrl($this->siteContext);
		elseif ($siteElementType == 'urlWrapper')
			$csvElement = new CsvUrlWrapper($this->siteContext);
		elseif ($siteElementType == 'loginForm')
			$csvElement = new CsvLoginForm($this->siteContext);
		elseif ($siteElementType == 'checkbox')
			$csvElement = new CsvCheckBox($this->siteContext);
		elseif ($siteElementType == 'formwrapper')
			$csvElement = new CsvFormWrapper($this->siteContext);
		elseif ($siteElementType == 'submitButton')
			$csvElement = new CsvSubmitButton($this->siteContext);
		elseif ($siteElementType == 'typeSelector')
			$csvElement = new CsvTypeSelector($this->siteContext);
		else
		{
			echo "<b>Error:</b> Unsupported site element type ($siteElementType) given to function <code>newElement()</code> in class <code>CsvGenerator</code>.";
			exit;
		}

		if ($this->separator == NULL)
			$this->separator = new CsvHandler(',');
		$csvElement->setSeparator($this->separator);
		$csvElement->setElementJoiner($this->separator);
		return $csvElement;
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
		return array('Content-type: text/plain');
		return array('Content-type: text/csv');
	}

	/**
	 * Retrives the main part of the site via <code>getMainSite</code>,
	 * wraps it into a template and returns the finished site.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the finished site.
	 */
	function getSite()
	{
		$filename = $this->siteContext->getPath("templates")."/CsvStatSite.txt";
		$fd = fopen ($filename, "r");
		$template = fread ($fd, filesize ($filename));
		fclose ($fd);

		$out = str_replace("%mainSite%",$this->getMainSite(),$template);

		return $out;
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
		return true;
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
		return false;
	}
	
} /*Slut p class CsvGenerator*/

/****************/
class CsvHandler extends ElementJoiner
{

	/**
	 * The char which shall be used to separate the values.
	 * 
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	 var $separator;

	/**
	 * Creates a new instance.
	 * 
	 * @param $separator the char which shall be used to separate the values.
	 */
	function __construct($separator) {
		$this->separator = $separator;
	}
	
	/**
	 * Joins the given <code>$elements</code>.
	 * Implemented for the <code>ElementJoiner</code>.
	 * 
	 * @param $elements a one dimentional array which shall be joined.
	 */
	function joinElements($elements)
	{
		$out = '';
		for ($i = 0; $i < count($elements); $i++) {
			$out .= $elements[$i];
		}
		
		return $out;
	}
	
	/**
	 * Turns the <code>$data</code> into Csv coded data.
	 */
	function makeCsv($data) {
		$out = "";
		
		for ($i = 0; $i < count($data); $i++) {
			for ($n = 0; $n < count($data[$i]); $n++) {
				//Add the data - remove all newlines and add slashes before quotes
				$out .= '"'.addslashes(str_replace(array("\n", "\r"), '', $data[$i][$n])).'"';
				if ($n+1 < count($data[$i]))
					$out .= $this->separator;
			} //End for $n
			$out .= "\n";
		} //End for $i
		return $out;
	}
	
	/**
	 * If <code>$data</code> contains an array it is added to 
	 * <code>$out</code> on a new line <code>$out</code>, else
	 * <code>$out</code> is just returned.
	 * 
	 * @param $out the array to add data to
	 * @param $data the one to add, if exists
	 */
/*	function addIfExists($out, $data) {
		if ($data === NULL)
			return;
		
		//Do we have something to add?
		if (is_array($data) and count($data) > 0) {
			for ($i = 0; $i < count($data); $i++) {
				for ($n = 0; $n < count($data[$i]); $n++) {
				
				} //End for $n
			} //End for $i
		} else if (strlen($data) > 0) {
			$out[] = array($data);
		}
	
	}
*/

	/**
	 * Returns the char which shall be used to separate the values.
	 * 
	 * @return the char which shall be used to separate the values.
	 */
	function getSeparator()
	{
		return $this->separator;
	}
}

/****************/

/**
 * Represents a text headline.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvHeadline extends SiteHeadline
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Returnerer koden elementet reprsenterer.
	 * Denne funtion skal overskrives, og skal altid
	 * give et gyldigt resultat.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String koden elementet reprsenterer.
	 */
	function getCode()
	{
		$out = array();
		$headCode = $this->getHeadCode();
		if (strlen($headCode) > 0)
			$out[] = array($headCode);
	
		//Pr. mned i et r
		if (strlen($this->headline) > 0)
			$out[] = array($this->headline);

		$tailCode = $this->getTailCode();
		if (strlen($tailCode) > 0)
			$out[] = array($tailCode);

		//return $this->headline;
		return $this->separator->makeCsv($out);
		//return $out;
/*
		$out = array();
		$headCode = $this->getHeadCode();
		if (strlen($headCode) > 0)
			$out[] = array($headCode);

		$out[] = array($this->headline); 

		$tailCode = $this->getTailCode();
		if (strlen($tailCode) > 0)
			$out[] = array($tailCode);

		return $this->separator->makeCsv($out);
*/
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}

} /*Slut p class CsvHeadline*/

/********************/

/**
 * Represents a graph made with text
 *
 * @file CsvGenerator.php
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvTableGraph extends SiteGraph
{

	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Gets the text from the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String objektets kode
	 */
	function getCode()
	{
		//Creating and configuring rounder
		$lib = $this->siteContext->getCodeLib();
		$rounder = $lib->getRounder();
		$rounder->setAddPercent(1);
		$rounder->setGoForDecimalsVisible(4);
		$rounder->setMaxDecimalsVisible(6);
		$rounder->setPoint(".");
		$rounder->setZeroDotToPercent(1);

		$this->makeInputReady();

		//Sort the data
		if ($this->sorted == 1)
			$this->sortInput();

		$out = array();
		//Pr. mned i et r"Denne mned er markeret"
		$headCode = $this->getHeadCode();
		if (strlen($headCode) == 0)
			$headCode = '';

		$showNumbers = $this->getShowNumbers();
		//The table headers
		if ($showNumbers !== 0) {
			$out[] = $this->getHeaderArray();
		} else {
			list($numbers, $percents, $text) = $this->getHeaderArray();
			$out[] = array($percents, $text);
		}

		$textArray = $this->getTextArray();
		$numArray = $this->getNumArray();
		list($percents, $unused) = $this->getPercents();

		//Iterates the arrays
		for ($i = 0;$i < sizeof($textArray);$i++)
		{
			if ($showNumbers !== 0) {
				$out[] = array($numArray[$i],
											 $rounder->formatNumber($percents[$i]),
											 $textArray[$i]);
			} else {
				$out[] = array($rounder->formatNumber($percents[$i]),
											 $textArray[$i]);
			}
		}

		$tailCode = $this->getTailCode();
		if (strlen($tailCode))
			$out[] = array($tailCode);
			
		return $headCode.$this->separator->makeCsv($out);
	} /*End of function getCode*/

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}


} /*End of class CsvTableGraph*/

/**
 * Generates a normal table.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvTable extends SiteTable
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Returns the code that is represented.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code that is represented.
	 */
	function getCode()
	{
		$out = array();
		$headCode = $this->getHeadCode();
		if (strlen($headCode) === 0)
			$headCode = ''; //$out[] = array($headCode);

		//Add each row seperately 
		for ($i = 0;$i < sizeof($this->tableContent); $i++)
		{
			$out[] = $this->tableContent[$i];
		}

		$tailCode = $this->getTailCode();
		if (strlen($tailCode))
			$out[] = array($tailCode);

		return $headCode.$this->separator->makeCsv($out);
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}
}


/**
 * Represents a pice of text, witch will be wraped into a paragraph.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvText extends SiteText
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Returns the text wraped as a paragraph.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the text wraped as a paragraph.
	 */
	function getCode()
	{
		$out = array();
		$headCode = $this->getHeadCode();
		if (strlen($headCode) === 0)
			$headCode = ''; //$out[] = array($headCode);

		//Denne mned er markeret
		$out[] = array($this->getText());

		$tailCode = $this->getTailCode();
		if (strlen($tailCode))
			$out[] = array($tailCode);

		return $headCode.$this->separator->makeCsv($out);
		//return $headCode.$this->separator->makeCsv(array($this->getText())).$tailCode;
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}

}

/**
 * Represents a list in plain text.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
Class CsvSiteList extends SiteList
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Returns the code represented by this class, a list as plain text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code represented by this class, a list as plain text.
	 */
	function getCode()
	{
		$out = array();
		$headCode = $this->getHeadCode();
		if (strlen($headCode) === 0)
			$headCode = '';

		$list = $this->getList();

		$code = '';
		for ($i = 0; $i < sizeof($list); $i++)
			$code .= $list[$i]->getCode();

		$tailCode = $this->getTailCode();
		if (strlen($tailCode))
			$out[] = array($tailCode);

		return $headCode.$code.$this->separator->makeCsv($out);
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}

} /*End of class CsvSiteList*/

/**
 * Represents a number of hits and an url, which is to be represented in
 * plain text.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvSiteHitUrl extends SiteHitUrl
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Returns the code represented by this class, a list as plain text.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code represented by this class, a list as plain text.
	 */
	function getCode()
	{
		$out = array();
		$out[] = array($this->getHits(), $this->getUrl());
		return $this->separator->makeCsv($out);
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}

} /*End of class CsvSiteHitUrl*/

/**
 * Represents an {@link SiteElement} as a textual emulation of a link.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvUrlWrapper extends UrlWrapper
{
	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

	/**
	 * Returns the code represented by this class, a the text from the
	 * wrapped object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code represented by this class.
	 */
	function getCode()
	{
		return $this->getUrl();
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}

} /*End of class CsvUrlWrapper*/

/**
 * Represents a form wrapper, which has nothing to do in a textual
 * context.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvFormWrapper extends FormWrapper {
	/**
	 * Does nothing - has no use for this element.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
	}

} /*End of class CsvFormWrapper*/

/**
 * Represents a loginform as text.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvLoginForm extends LoginForm
{

	/**
	 * The object which handles wrapping of data into the CSV format.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $separator;

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
		$out = array();
		$out[] = array($this->siteContext->getLocale("loginNotText"));
		$out[] = array($this->siteContext->getLocale("username"), $this->getUsername());
		$out[] = array($this->siteContext->getLocale("password"));
		return $this->separator->makeCsv($out);
	}

	/**
	 * Returns the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return String the object which handles wrapping of data into the CSV format.
	 */
	function &getSeparator()
	{
		return $this->separator;
	}

	/**
	 * Sets the object which handles wrapping of data into the CSV format.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
		$this->separator = $separator;
	}

} /* End of class CsvLoginForm*/

/**
 * Represents a checkbox.
 */
class CsvCheckBox extends CheckBox {

	/**
	 * Returns an empty string. The functionallity of this class does not
	 * apply to text mode.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getCode()
	{
		return "";
	}

	/**
	 * Does nothing - has no use for this element.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
	}
	
}

/**
 * Represnets a submit button. However in text mode it will not have any purpose.
 * 
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvSubmitButton extends SubmitButton {

	/**
	 * Returns an empty string. The functionallity of this class does not
	 * apply to text mode.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getCode()
	{
		return "";
	}

	/**
	 * Does nothing - has no use for this element.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $separator the object which handles wrapping of data into the CSV format.
	 */
	function setSeparator($separator)
	{
	}

}

/**
 * Originally enables the user to select a number of stats.
 * However in text mode this is not possible, so the {@link #getCode}
 * function just returns an empty string.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class CsvStatSelector extends StatSelector
{

	/**
	 * Returns an empty string. The functionallity of this class does not
	 * apply to text mode.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getCode()
	{
		return "";
	}

} /* End of class CsvStatSelector */

/**
 * Creates a selector to select between the different types of stat sites.
 * This version does nothing because the functionallity can't be made
 * in this view type.
 */
class CsvTypeSelector extends TypeSelector {
	/**
	 * Does nothing.
	 */
	function setSeparator() {}
}
?>