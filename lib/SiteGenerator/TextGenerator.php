<?php

/**
 * Use this class to generate a text file.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextGenerator extends SiteGenerator
{
	/**
	 * The maximum width of charchars a line may contain.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth = 75;

	/**
	 * Returnerer en ny instans af et <code>SiteElement</code>.
	 * Dette passer til den type side man har valgt. F?lgende liste
	 * angiver elementtyper der altid er underst?ttet. De enkelte
	 * generatorer kan derudover v?lge at underst?tte andre elementer.
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
			$textElement = new TextTable($this->siteContext);
		elseif ($siteElementType == 'graphTable')
			$textElement = new TextTableGraph($this->siteContext);
		elseif ($siteElementType == 'headline')
			$textElement = new TextHeadline($this->siteContext);
		elseif ($siteElementType == 'text')
			$textElement = new TextText($this->siteContext);
		elseif ($siteElementType == 'list')
			$textElement = new TextSiteList($this->siteContext);
		elseif ($siteElementType == 'hiturl')
			$textElement = new TextSiteHitUrl($this->siteContext);
		elseif ($siteElementType == 'urlWrapper')
			$textElement = new TextUrlWrapper($this->siteContext);
		elseif ($siteElementType == 'loginForm')
			$textElement = new TextLoginForm($this->siteContext);
		elseif ($siteElementType == 'checkbox')
			$textElement = new TextCheckBox($this->siteContext);
		elseif ($siteElementType == 'formwrapper')
			$textElement = new TextFormWrapper($this->siteContext);
		elseif ($siteElementType == 'submitButton')
			$textElement = new TextSubmitButton($this->siteContext);
		elseif ($siteElementType == 'typeSelector')
			$textElement = new TextTypeSelector($this->siteContext);
		else
		{
			echo "<b>Error:</b> Unsupported site element type ($siteElementType) given to function <code>newElement()</code> in class <code>TextGenerator</code>.";
			exit;
		}

		$textElement->setLineWidth($this->lineWidth);
		return $textElement;
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
		$filename = $this->siteContext->getPath("templates")."/TextStatSite.txt";
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

} /*End of class TextGenerator*/

/****************/

/**
 * Represents a text headline.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextHeadline extends SiteHeadline
{
	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

	/**
	 * Returnerer koden elementet repr?senterer.
	 * Denne funtion skal overskrives, og skal altid
	 * give et gyldigt resultat.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String koden elementet repr?senterer.
	 */
	function getCode()
	{
		$out = "\n".$this->getHeadCode();

		$lines = "";
		if ($this->size === 1)
			$lineChar = "=";
		else
			$lineChar = "-";

		for ($i = 0; $i < strlen($this->headline) -2; $i++)
			$lines .= $lineChar;
		$lines .= "";

		if ($this->size === 1 or $this->size === 2)
			$out .= " /".$lines."\\\n";

		$out .= "|".$this->headline."|\n";

		if ($this->size === 1 or $this->size === 2)
		{
			$out .= " \\".$lines."/\n";
			if ($this->size === 1)
				$out .= "\n";
		}

		$out .= $this->getTailCode();

		return $out;
	}

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}

} /*Slut p? class TextHeadline*/

/********************/

/**
 * Represents a graph made with text
 *
 * @file TextGenerator.php
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextTableGraph extends SiteGraph
{

	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		$rounder->setMaxDecimalsVisible(4);
		$rounder->setPoint(".");
		$rounder->setZeroDotToPercent(1);

		$this->makeInputReady();

		//Sort the data
		if ($this->sorted == 1)
			$this->sortInput();

		$headers = $this->getHeaderArray();

		$outCode = $this->getHeadCode();

		$showNumbers = $this->getShowNumbers();
		//The table headers
		          // Numbers (1)     percents (2)      text (3)
		$headline = "";
		if ($showNumbers !== 0) {
			$headline .= $headers[1]."  ";
		}
		
		$headline .= $headers[2]."  ".$headers[0]."\n";
		$outCode .= $headline;

		for ($i = 0; $i < strlen($headline); $i++)
			$outCode .= "-";
		$outCode.= "\n";

		$textArray = $this->getTextArray();
		$numArray = $this->getNumArray();
		$percentsTwoArray = $this->getPercents(); /*Calculate percents for the graph*/
			$percents = $percentsTwoArray[0];
			$relativePercents = $percentsTwoArray[1]; //Not used in text (yet)
		$showNumbers = $this->getShowNumbers();
		$emText = $this->getEmphasize(); //Not used in text (yet)

		//Find the widest number
		$maxWidth = 0;
		for ($i = 0;$i < sizeof($textArray);$i++)
		{
			if (strlen($numArray[$i]) > $maxWidth)
				$maxWidth = strlen($numArray[$i]);
		}

		//Iterates the arrays
		for ($i = 0;$i < sizeof($textArray);$i++)
		{
			if ($showNumbers !== 0) {
				$outCode .= $numArray[$i]; /*Don't use this in monthly stats*/
				//Add the proper amount of spaces
				for ($n = strlen($numArray[$i]); $n < $maxWidth + 1; $n++)
					$outCode .= " ";
			}

			$outCode .= $rounder->formatNumber($percents[$i])."  ";
			$outCode .= $textArray[$i]."\n";
		}

		$outCode .= "\n";

		$outCode .= $this->getTailCode();

		return $outCode;
	} /*End of function getCode*/

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}


} /*End of class TextTableGraph*/

/**
 * Generates a normal table.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextTable extends SiteTable
{
	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		$out = $this->getHeadCode();

		//The number of columns in the row with the most columns.
		$numCols = -1;

		//Iterates the rows
		for ($i = 0; $i < sizeof($this->tableContent); $i++)
		{
			if (sizeof($this->tableContent[$i]) > $numCols)
				$numCols = sizeof($this->tableContent[$i]);
		}

		//Find the max width of each column
		$maxWidths = array();
		//Iterate the rows
		for ($i = 0; $i < sizeof($this->tableContent); $i++)
		{ //Iterate the cols
			for ($n = 0; $n < sizeof($this->tableContent[$i]); $n++)
			{
				if (!isset($maxWidths[$n]) or strlen($this->tableContent[$i][$n]) > $maxWidths[$n])
					$maxWidths[$n] = strlen($this->tableContent[$i][$n]);
			}
		}

		$tableWidth = array_sum($maxWidths) + 2*sizeof($maxWidths);

		$tableLine = "";
		for ($i = 0; $i < $tableWidth; $i++)
			$tableLine .= "-";
		$tableLine .= "\n";

		//Generate the cells
		 //Iterates the rows
		for ($i = 0;$i < sizeof($this->tableContent); $i++)
		{
			//Iterates the cols.
			for ($n = 0;$n < sizeof($this->tableContent[$i]); $n++)
			{
				$out .= $this->tableContent[$i][$n];
				if ($n +1 !== sizeof($this->tableContent[$i]))
				{
					//$out .= "[".(strlen($this->tableContent[$i][$n]) - $maxWidths[$n])."]";
					for ($k = -1; $k < $maxWidths[$n] - strlen($this->tableContent[$i][$n]); $k++)
						$out .= " ";
				}
			}
			//Ends the table row
			$out .= "\n";
		}

		//$out .= $tableLine;

		$out .= $this->getTailCode();

		$out .= "\n";

		return $out;
	}

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}
}


/**
 * Represents a pice of text, witch will be wraped into a paragraph.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextText extends SiteText
{
	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		$out = $this->getHeadCode();

		/*
		  -1: Output the plain text without any extra formating
		   0: Just output the plain text
	      1: Show as a new paragraph
	      2: Show on a new line (newline before the text)
	      3: End with a new line (newline after the text)
     */
		if ($this->getParagraph() === 1 or $this->getParagraph() === 2)
			$out .= "\n";

		$out .= wordwrap($this->getText(),$this->lineWidth);

		if ($this->getParagraph() === 1 or $this->getParagraph() === 3)
			$out .= "\n";
		$out .= $this->getTailCode();

		return $out;
	}



	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}

}

/**
 * Represents a list in plain text.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
Class TextSiteList extends SiteList
{
	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		$list = $this->getList();

		$out = $this->getHeadCode();

		for ($i = 0; $i < sizeof($list); $i++)
			$out .= "  *".$list[$i]->getCode()."\n";

		$out .= $this->getTailCode();

		return $out;
	}

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}

} /*End of class TextSiteList*/

/**
 * Represents a number of hits and an url, which is to be represented in
 * plain text.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextSiteHitUrl extends SiteHitUrl
{
	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		return $this->getHits()." ".$this->getUrl()."\n";
	}

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}

} /*End of class TextSiteHitUrl*/

/**
 * Represents an {@link SiteElement} as a textual emulation of a link.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextUrlWrapper extends UrlWrapper
{
	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		$wrapped = $this->getWrapped();
		$text = $wrapped->getCode();
		$url = $this->getUrl();
		if (strpos($url, $text) !== false) {
			//The url contains the text: No point in showing both.
			return $url;
		} else {
			return $wrapped->getCode().": ". $this->getUrl();
		}
	}

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}

} /*End of class TextUrlWrapper*/

/**
 * Represents a form wrapper, which has nothing to do in a textual
 * context.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextFormWrapper extends FormWrapper {
	/**
	 * Does nothing - has no use for this element.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
	}

} /*End of class TextFormWrapper*/

/**
 * Represents a loginform as text.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class TextLoginForm extends LoginForm
{

	/**
	 * The maximum line width.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $lineWidth;

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
		$out = wordwrap($this->siteContext->getLocale("loginNotText"), $this->getLineWidth())."\n";
		$out .= $this->siteContext->getLocale("username").": [".$this->getUsername()."]\n";
		$out .= $this->siteContext->getLocale("password").": [___________]\n";
		return $out;
	}

	/**
	 * Returns the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return int the maximum line width.
	 */
	function getLineWidth()
	{
		return $this->lineWidth;
	}

	/**
	 * Sets the maximum line width.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
	{
		$this->lineWidth = $lineWidth;
	}

} /* End of class TextLoginForm*/

/**
 * Represents a checkbox.
 */
class TextCheckBox extends CheckBox {

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
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
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
class TextSubmitButton extends SubmitButton {

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
	 * @param $lineWidth the maximum line width.
	 */
	function setLineWidth($lineWidth)
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
class TextStatSelector extends StatSelector
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

} /* End of class TextStatSelector */

/**
 * Creates a selector to select between the different types of stat sites.
 * This version does nothing because the functionallity can't be made
 * in this view type.
 */
class TextTypeSelector extends TypeSelector {
	/**
	 * Does nothing.
	 */
	function setLineWidth($lineWidth) {}
}
?>