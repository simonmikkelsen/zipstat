<?php

/**
 * Generates a html site.
 * Only HTML 4.01 code is used, witch validates as strict.
 * A layout is put in style sheets, however tables are used.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlGenerator extends SiteGenerator
{
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
	 *    <dt><code>calendarMaker</code></dt>
	 *       <dd>Generates an object that can make a calendar.</dd>
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
		if ($siteElementType === 'table')
			return new HtmlTable($this->siteContext);
		elseif ($siteElementType === 'graphTable')
			return new HtmlTableGraph($this->siteContext);
		elseif ($siteElementType === 'seriesGraph')
			return new HtmlSeriesGraph($this->siteContext);
		elseif ($siteElementType === 'headline')
			return new HtmlHeadline($this->siteContext);
		elseif ($siteElementType === 'text')
			return new HtmlText($this->siteContext);
		elseif ($siteElementType === 'list')
			return new HtmlSiteList($this->siteContext);
		elseif ($siteElementType === 'hiturl')
			return new HtmlSiteHitUrl($this->siteContext);
		elseif ($siteElementType === 'urlWrapper')
			return new HtmlUrlWrapper($this->siteContext);
		elseif ($siteElementType === 'loginForm')
			return new HtmlLoginForm($this->siteContext);
		elseif ($siteElementType == 'checkbox')
			return new HtmlCheckBox($this->siteContext);
		elseif ($siteElementType == 'formwrapper')
			return new HtmlFormWrapper($this->siteContext);
		elseif ($siteElementType == 'submitButton')
			return new HtmlSubmitButton($this->siteContext);
		elseif ($siteElementType == 'calendarMaker')
			return new HtmlCalendarMaker($this->siteContext);
		elseif ($siteElementType == 'typeSelector') {
			$tSel = new HtmlTypeSelector($this->siteContext);
			$tSel->setSiteGenerator($this);
			return $tSel;
		} else
		{
			echo "<b>Error:</b> Unsupported site element type ($siteElementType) given to function <code>newElement()</code> in class <code>HtmlGenerator</code>.";
			exit;
		}
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
		$filename = $this->siteContext->getPath("templates")."/HtmlStatSite.txt";
		$fd = fopen ($filename, "r");
		$template = fread ($fd, filesize ($filename));
		fclose ($fd);
		
		//Shall the menu be shown?
		$showMenu = $this->siteContext->getHttpVar('menu');
		if (isset($showMenu) and $showMenu === 'hide') {
			//Remove it!
			$template = substr($template, 0, strpos($template, "%startMenu%"))
			           .substr($template, strpos($template, "%endMenu%")+strlen("%endMenu%"));
		
		} else {
			//Just remove the menu tags
			$template = str_replace(array("%startMenu%", "%endMenu%"), "", $template);
		}
		
		// Generate the site - this must be done before we collect headers
		// because they are not known untill it all is put together.
		$mainSiteHTML = $this->getMainSite();
		
		// Collect and assemble HTML headers.
		$moreHeaders = "";
		$refreshSec = $this->getRefreshInSec();
		if ($refreshSec >= 0) {
			$moreHeaders = "\t<meta http-equiv=\"refresh\" content=\""
			               .$refreshSec.";url="
			               .htmlentities($this->getRefreshTo())."\">\n";
		}
		$headers = $this->collectHeaders('html');
		foreach ($headers as $header) {
			$moreHeaders .= "\t".$header->getCode()."\n";
		}

		//Insert urls etc.
		$tokens = array('%title%' => $this->getTitle(),
		                '%css%' => $this->getCssUrl(),
		                '%bodyTagAttribs%'=> $this->getExtraBodyAttribs(),
		                '%frontpage_url%' => $this->siteContext->getOption('ZSHomePage'),
		                '%signup_url%'    => $this->siteContext->getOption('urlSignup'),
		                '%userarea_url%'  => $this->siteContext->getOption('urlUserArea'),
		                '%contact_url%'   => $this->siteContext->getOption('urlContact'),
		                '%help_url%'      => $this->siteContext->getOption('urlHelp'),
		                '%service_email%' => $this->siteContext->getOption('adminEMail'),
		                '%admin_name%'    => $this->siteContext->getOption('adminName'),
		                '%service_name%'  => $this->siteContext->getOption('name_of_service'),
		                '%more_headers%'  => $moreHeaders
		                );
		$template = strtr($template, $tokens);
		
		$out = str_replace("%mainSite%",$mainSiteHTML,$template);

		return $out;
	}
	
	/**
	 * Returns the users own attribtes for the body tag, if the user has
	 * ZIP Stat Pro.<br>
	 * If the user does not have ZIP Stat Pro an empty string is returned.
	 *
	 * @public
	 * @return the users own attribtes for the body tag.
	 */
	function getExtraBodyAttribs() {
		$lib = $this->siteContext->getCodeLib();
		if ($lib != NULL and $lib->pro()) {
			$datasource = $lib->getDataSource();
			return $datasource->getLine(56);
		} else {
			return '';
		}
	}
	
	/**
	 * Returns the users own title for the stat site, if the user has
	 * ZIP Stat Pro.<br>
	 * If the user does not have ZIP Stat Pro an empty string is returned.
	 *
	 * @public
	 * @return the users own title for the stat site.
	 */
	function getTitle() {
		$lib = $this->siteContext->getCodeLib();
		if ($lib !== NULL and $lib->pro()) {
			$datasource = $lib->getDataSource();
			return $datasource->getLine(59);
		} else {
			return '';
		}
	}

	/**
	 * Returns the url for style sheet to use.
	 *
	 * @public
	 * @return the url for the style sheet to use.
	 */
	function getCssUrl() {
		return $this->siteContext->getOption('urlCss');
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
		return array('Content-type: text/html');
	}

} /*End of class HtmlGenerator*/

/****************/

/**
 * Represents a headline in a html site.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlHeadline extends SiteHeadline
{
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
		$out = $this->getHeadCode();
		$out .= "<h".$this->size.">".$this->headline."</h".$this->size.">\n";
		$out .= $this->getTailCode();

		return $out;
	}

} /*End of class HtmlHeadline*/

/********************/

/**
 * Represents a series graph created using HTML, CSS and Javascript.
 *
 * @file HtmlGenerator.php
 * @public
 * @author Simon Mikkelsen
 */
class HtmlSeriesGraph extends SiteSeriesGraph
{
	/**
	 * Gets the html code from the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String objektets kode
	 */
	function getCode()
	{
		//Creating and configuring rounder
		$lib = $this->siteContext->getCodeLib();;
/*		$rounder = $lib->getRounder();
		$rounder->setAddPercent(1);
		$rounder->setGoForDecimalsVisible(4);
		$rounder->setMaxDecimalsVisible(4);
		$rounder->setPoint(".");
		$rounder->setZeroDotToPercent(1);
*/
//		$this->makeInputReady();

		$this->addHeader(new SiteHeader('html', '<!--[if IE]><script language="javascript" type="text/javascript" src="/zipstat/flot/excanvas.pack.js"></script><![endif]-->'));
		$this->addHeader(new SiteHeader('html', '<script language="javascript" type="text/javascript" src="/zipstat/flot/jquery.js"></script>'));
		$this->addHeader(new SiteHeader('html', '<script language="javascript" type="text/javascript" src="/zipstat/flot/jquery.flot.js"></script>'));

		// Start making code and get the data to make it from.
		$outCode = $this->getHeadCode();
		$dataSeries = $this->getDataSeriesPrepared('day');
		
		// Find out the sort order based on total hits.
		$label2Hits = array();
		foreach ($dataSeries as $label => $dataSet) {
			isset($dataSet['META-INF']) or die("META-INF must be set for \"$label\".");
			$meta = $dataSet['META-INF'];
			isset($meta['totalHits']) or die("totalHits must be set for \"$label\".");
			$totalHits = $meta['totalHits'];
			$label2Hits[$label] = $totalHits;
		}
		arsort($label2Hits); // Sort the array by the values.
		
		// Now make the funny Java Script and stuff data into it.
		$outCode .= <<<EOJAVASCRIPT
		<table><tr>
			<td><div id=placeholder style=width:600px;height:300px;></div></td>
			<td><div id='legend'></div></td>
		</tr></table>
		    <p>
				Toggle: Browser vendors | <a id="formatPublishingToggle">Formater til publisering</a><br />
				Show: &lt; More popular | lesser popular &gt; | <a id="selectAll">All</a> | <a id="selectNone">None</a>
				</p>
		<script id=source language=javascript type=text/javascript>

		$(function () {
		var datasets = {
EOJAVASCRIPT;

		$first = true;
		foreach ($label2Hits as $label => $totalHits) {
			$dataSet = $dataSeries[$label];
			if ($first) {
				$first = false;
			} else {
				$outCode .= ",\n"; //Add a , to the end of last set, i.e. add it to the end of every but the last set.
			}
			$outCode .= "\"".addslashes($label)."\": {\n";
			$outCode .= "	label: \"".addslashes($label)."\",\n";
			$outCode .= "	data: [";
			$innerFirst = true;
			foreach ($dataSet as $unixtime => $hitData) {
				if ($unixtime === 'META-INF') {
					continue;
				}
				//$hits = $hitData[0];
				$percents = round($hitData[1], 5) * 100;
				if ($innerFirst) {
					$innerFirst = false;
				} else {
					$outCode .= ","; //Add a , to the end of last set, i.e. add it to the end of every but the last set.
				}
				$outCode .= "[".($unixtime*1000).",".$percents."]";
			}
			$outCode .= "]\n";
			$outCode .= "	}";
		}
		$outCode .= "\n};\n";

$outCode .= <<< EOJAVASCRIPT
    // hard-code color indices to prevent them from shifting as
    // countries are turned on/off';
    var i = 0;
    $.each(datasets, function(key, val) {
      val.color = i;
      ++i;
    });

    // Used to make sure the lengend is only drawn the first time.
    var updateLegendNow = true;
    var legendType = "select"; //select or publish
    var prevLegendType = null;
    var prevSelectedLabels = null;
    var legendObj = $("#legend");
    
    // Redraw everything.
    function plotAccordingToChoices() {
      var data = [];
			var allData = [];
      var selectedLabels = [];
      // If there NOT is a select legend.
      if (prevLegendType == null || prevLegendType != "select") {
        $.each(datasets, function(key, val) {
          // Make the legend from scratch.
          data.push(val);
          selectedLabels.push(key);
        });
      } else {
        // Look in the legend and only include what has been checked off.
        legendObj.find("input:checked").each(function () {
          var key = $(this).attr("name");
          if (key && datasets[key]) {
            data.push(datasets[key]);
            selectedLabels.push(key);
					}
        });
      }

    if (data.length > 0) {
      // Add the proper legend defintion.
      if (updateLegendNow) {
          legendDefinition = { container: legendObj, noColumns: 3,
          labelFormatter: function(label) {
            if (legendType == "select") {
              var found = false;
              for (var i in prevSelectedLabels) {
                if (label == prevSelectedLabels[i]) {
                  found = true;
                  break;
                }
              }
              if (found || prevSelectedLabels == null) {
                checkedCode = ' checked="checked"';
              } else {
                checkedCode = '';
              }
              return '<td><input type="checkbox" name="'
                + label + '"'+checkedCode+'>' + label + '</input></td>';
            } else if (legendType == "publish") {
              return label;
            } else {
              alert("Unknown legend type: "+legendType);
            }
          }
				};
      } else {
        legendDefinition = { show: false };
      }
          
      // Plot graph and legend.
      plot = $.plot($("#placeholder"), data, {
        legend: legendDefinition,
        yaxis: { min: 0 },
        xaxis: { mode: "time", timeformat: "%d.%m" }
      });
            
      // Make the check boxes in the legend update the graph.
      if (updateLegendNow) {
        legendObj.find("input").click(plotAccordingToChoices);
        updateLegendNow = false;
      }
    } // End if data.length > 0
      
      prevLegendType = legendType;
      prevSelectedLabels = selectedLabels;
  } // End function plotAccordingToChoices
    
    function customSelectNone(  ) {
        legendObj.find("input:checked").attr("checked", "");
        //plotAccordingToChoices(); // TODO The library cannot plot nothing - it crashes.
    }
    $("#selectNone").click(customSelectNone);
    
    function customSelectAll() {
        legendObj.find("input").attr("checked", "checked");
        plotAccordingToChoices();
    }
    $("#selectAll").click(customSelectAll);
    
    function customFormatPublishingToggle() {
      updateLegendNow = true;
      if (legendType == "select") { 
        legendType = "publish";
				plotAccordingToChoices();
      } else {
        legendType = "select";
				// Hack work: The first will draw the legends and all data.
				plotAccordingToChoices();
				// The second will only draw the data that was selected previously.
				plotAccordingToChoices();
      }
    }
    $("#formatPublishingToggle").click(customFormatPublishingToggle);
    
    plotAccordingToChoices();
});
</script>
EOJAVASCRIPT;

		//TODO Vis vores data med procent i stedet for tal.
		//TODO Put vores data ind, s kan man sl browsere fra.
		//TODO Find og af om der ikke er for f data.,
		//TODO F alle headers ind i den nye mekanisme: Bde HTML og HTTP
		//TODO Srg for at en absolut sti kan sttes ind i headers, til vores deroppe.
		
//		$outCode .= "<pre>".print_r($dataSeries, true)."</pre>";
		$outCode .= $this->getTailCode();

		return $outCode;
	} /*End of function getCode*/

} /*End of class HtmlSeriesGraph*/


/********************/

/**
 * Represents a graph, created solely using HTML and style sheets.
 *
 * @file HtmlGenerator.php
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlTableGraph extends SiteGraph
{
	/**
	 * Gets the html code from the object.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String objektets kode
	 */
	function getCode()
	{
		//Creating and configuring rounder
		$lib = $this->siteContext->getCodeLib();;
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

		$outCode .= "<table". $this->getHtmlAttribs()
			. " width=\"".$rounder->formatNumber($this->getTableWith())
			."\" cellspacing=0 cellpadding=0>\n";

		$showNumbers = $this->getShowNumbers();
		//The table headers
		$outCode .= "<tr>\n";
		$outCode .= "<th class=thA WIDTH=\"1%\">".$headers[0]."</th>"; //Text
		if ($showNumbers !== 0) {
			$outCode .= "<th class=thA WIDTH=\"1%\">".$headers[1]."</th>"; //Numbers
		}
		$outCode .= "<th class=thA WIDTH=\"1%\">".$headers[2]."</th>"; //Percents
		$outCode .= "<th class=thB width=\"98%\">".$headers[3]."</th>"; //Graph
		$outCode .= "</tr>\n";

		$textArray = $this->getTextArray();
		$numArray = $this->getNumArray();
		$percentsTwoArray = $this->getPercents(); /*Calculate percents for the graph*/
			$percents = $percentsTwoArray[0];
			$relativePercents = $percentsTwoArray[1];
		$showNumbers = $this->getShowNumbers();
		$emText = $this->getEmphasize();

		//Iterates the arrays
		for ($i = 0;$i < sizeof($textArray);$i++)
		{
			//Shall this text be emphasized?
			if ($textArray[$i] === $emText)
			{
				$emTextStart = "<span class=\"markeret\">";
				$emTextEnd = "</span>";
			}
			else
			{
				$emTextStart = "";
				$emTextEnd = "";
			}

			$outCode .= "<tr>";
			$outCode .= "<td class=tdA nowrap>".$emTextStart.$textArray[$i].$emTextEnd."</td>";
			if ($showNumbers !== 0) {
				$outCode .= "<td class=tdA>".$emTextStart.$numArray[$i].$emTextEnd."</td>"; //Numbers
			}
			$outCode .= "<td class=tdA>".$emTextStart.$rounder->formatNumber($percents[$i]).$emTextEnd."</td>";
			$outCode .= "<td class=tdB>";
			$outCode .= "<div class=GrafVenstre style=\"width:".$rounder->formatNumber($relativePercents[$i]).";\">".$emTextStart."&nbsp;".$emTextEnd."</div>";
			$outCode .= "</td></tr>\n";
		}

		$outCode .= "</table>\n\n";

		$outCode .= $this->getTailCode();

		return $outCode;
	} /*End of function getCode*/

} /*End of class HtmlTableGraph*/


/**
 * Generates a normal table.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlTable extends SiteTable
{
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

		//The beginning of the table
		$out .= $this->getTableStart();

		//Generate the cells
		 //Iterates the rows
		for ($row = 0;$row < sizeof($this->tableContent); $row++)
		{
			//Creates a table row
			$out .= "<tr";
			if ($row < sizeof($this->columnClassArray) and
			  strlen($this->columnClassArray[$row]) > 0) {
				$out .= " class=\"".htmlentities($this->columnClassArray[$row])."\"";
			}
			$out .= ">\n";

			//Iterates the cols.
			for ($col = 0;$col < sizeof($this->tableContent[$row]); $col++)
			{
				//Start the tag, which will be put in $thisTag
				$out .= "\t<";

				//Get what's headers, see SiteTable->setHeadersAre for info
				$headersAre = $this->getHeadersAre();
				if ( (($headersAre === 1 or $headersAre === 3) and $row === 0)
				  or (($headersAre === 2 or $headersAre === 3) and $col === 0))
				{ /*The headers*/
					$thisTag = "th";
					$out .= $thisTag;
					if (strlen($this->getHeaderClass()) > 0)
						$out .= " class=\"".htmlentities($this->getHeaderClass())."\"";
				}
				else
				{ /*The data*/
					$thisTag = "td";
					$out .= $thisTag;
					if (strlen($this->columnClassArray[$col]) > 0)
						$out .= " class=\"".htmlentities($this->columnClassArray[$col])."\"";
				}

				//The last column, if all the columns arn't used
				if ($col+1 == sizeof($this->tableContent[$row]) and $col+1 < $numCols)
					$out .= " colspan=\"".($numCols-($col+1))."\"";

				$out .= ">".$this->tableContent[$row][$col]."</".$thisTag.">\n";
			}
			//Ends the table row
			$out .= "</tr>\n";
		}

		$out .= "</table>\n";

		$out .= $this->getTailCode();

		return $out;
	}

	/**
	 * Returns the beginning of the table.
	 *
	 * @private
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the beginning of the table.
	 */
	function getTableStart()
	{
		$out = "<table";
		$out .= $this->getHtmlAttribs();
		$out .= ">\n";

		return $out;
	}

} /*End of class HtmlTable*/


/**
 * Represents a pice of text, witch will be wraped into a paragraph.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlText extends SiteText
{
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
		/*
		  -1: Output the plain text without any extra formating
		   0: Just output the plain text with attributes
	      1: Show as a new paragraph
	      2: Show on a new line (newline before the text)
	      3: End with a new line (newline after the text)
     */

		$this->setEmphasizeStart("<b>");
		$this->setEmphasizeEnd("</b>");

		$out = $this->getHeadCode();

		$label = $this->getLabel();
		if (strlen($label) > 0) {
			$label = " title=\"".htmlentities($label)."\"";
		} else {
			$label = "";
		}

		if ($this->getParagraph() === 1)
		{
			$out .= "<p";
			$out .= $this->getHtmlAttribs();
			$out .= $label;
			$out .= ">";
		}
		else if ($this->getParagraph() !== 0)
		{
			if ($this->getParagraph() === 2)
				$out .= "<br>\n";

			$out .= "<span";
			$out .= $this->getHtmlAttribs();
			$out .= $label;
			$out .= ">";
		}

		$out .= htmlentities($this->getText());

		if ($this->getParagraph() === 1)
			$out .= "</p>\n";
		else if ($this->getParagraph() !== 0)
		{
			if ($this->getParagraph() === 3)
				$out .= "<br>\n";
			$out .= "</span>\n";
		}

		$out .= $this->getTailCode();

		return $out;
	}

}

/**
 * Represents a list in HTML code.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlSiteList extends SiteList
{

	/**
	 * Returns the code represented by this class, as a list.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code represented by this class.
	 */
	function getCode()
	{
		$list = $this->getList();

		$out = $this->getHeadCode();

		$out .= "<ul";
		$out .= $this->getHtmlAttribs();
		$out .= ">\n";

		for ($i = 0; $i < sizeof($list); $i++)
			$out .= "\t<li>".$list[$i]->getCode()."\n";

		$out .= "</ul>\n";
		$out .= $this->getTailCode();

		return $out;
	}
} /*End of class HtmlSiteList*/

/**
 * Represents a number of hits and an url, which is to be represented in
 * html.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlSiteHitUrl extends SiteHitUrl
{
	/**
	 * Returns the code represented by this class, as a list.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the code represented by this class.
	 */
	function getCode()
	{
		return $this->getHits()." <a href=\"redir.php?url=".htmlentities(urlencode($this->getUrl()))."\" target=\"_blank\">"
					. htmlentities($this->getUrl())."</a>\n";
	}

} /*End of class SiteHitUrl*/

/**
 * Represents an {@link SiteElement} as a link.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlUrlWrapper extends UrlWrapper
{

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
		$code = $this->getHeadCode();
		
		$wrapped = $this->getWrapped();
		
		if ($this->isActive() === 1) {
			$code .= "<a href=\"".htmlentities($this->getUrl())
					."\" title=\"".htmlentities($this->getTitle())."\">".
					$wrapped->getCode()."</a>";
		} else {
			$code .= $wrapped->getCode();
		}
		
		$code .= $this->getTailCode();
		return $code;
	}

} /*End of class HtmlUrlWrapper*/

/**
 * Represents a form wrapper.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlFormWrapper extends FormWrapper {

	/**
	 * Returns the represented code.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getWrappedCode() {
		$code = $this->getHeadCode();
		
		$code .= '<form';
		
		if (strlen($this->getSubmitUrl()) > 0) {
			$code .= ' action="'.htmlentities($this->getSubmitUrl()).'"';
		}
		
		if (strlen($this->getMethod()) > 0) {
			$code .= ' method="'.htmlentities($this->getMethod()).'"';
		}
		
		$code .= '>';
		
		$wrapped = $this->getWrapped();
		$code .= $wrapped->getCode();
		
		$passThroughParams = $this->getPassThroughParams();
		foreach ($passThroughParams as $param) {
			$this->siteContext !== NULL and is_a($this->siteContext, 'SiteContext') or die ('No site context.');
			$paramVal = $this->siteContext->getHttpVar($param);
			if (strlen($paramVal) > 0) {
				$code .= '<input type="hidden"';
				$code .= ' name="'.htmlentities($param).'"';
				$code .= ' value="'.htmlentities($paramVal).'"';
				$code .= ' />';
			}
		}
		
		$code .= '</form>';
		
		$code .= $this->getTailCode();
		return $code;
	}

} /*End of class HtmlFormWrapper*/

/**
 * Represents a loginform as HTML.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlLoginForm extends LoginForm
{

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
		$out = "<form action=\"".htmlentities($this->getUrl())."\"";
		if (strlen($this->getSubmitMethod()) > 0) {
			$out .= " method=\"".$this->getSubmitMethod()."\"";
		}
		$out .= ">\n";
		$out .= "<table border=0>\n";
		$out .= "	<tr><td>".htmlentities($this->siteContext->getLocale("username")).
					"</td><td><input type=text name=\"".htmlentities($this->getKeyUsername())."\" value=\"".htmlentities($this->getUsername())."\"></td></tr>\n";
		$out .= "	<tr><td>".htmlentities($this->siteContext->getLocale("password")).
					"</td><td><input type=password name=\"".htmlentities($this->getKeyPassword())."\"></td></tr>\n";
		//Can we send mail?
		if ($this->siteContext->getOption('send_mail') == "1") {
			$out .= "	<tr><td></td><td><a href=\"mailpwd.php?username=".htmlentities($this->getUsername())."\">".htmlentities($this->siteContext->getLocale("forgottenPwd"))."</a></td></tr>\n";
		}
		$out .= "	<tr><td></td><td><input type=submit value=\"".htmlentities($this->siteContext->getLocale("login"))."\"></td></tr>";
		$out .= "</table>\n";
		$out .= "</form>\n";
		
		return $out;
	}

} /* End of class HtmlLoginForm*/

/**
 * Represents a checkbox.
 */
class HtmlCheckBox extends CheckBox {

	/**
	 * Returns the represented code.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getCode() {
		$code = '<input type="checkbox" name="'.htmlentities($this->getElementName()).'"';
		if ($this->isSelected())
			$code .= ' CHECKED';
		
		if (strlen($this->getValue()) > 0) {
			$code .= ' value="'.htmlentities($this->getValue()).'"';
		}
		$code .= ' />';
		return $code;
	}

} /* End of class HtmlCheckBox*/

/**
 * Represents a submit button.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlSubmitButton extends SubmitButton {

	/**
	 * Returns the represented code.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String the represented code.
	 */
	function getCode() {
		$code = $this->getHeadCode();
		
		$code .= '<input type="submit"';
		
		if (strlen($this->getElementName()) > 0) {
			$code .= ' name="'.htmlentities($this->getElementName()).'"';
		}
		
		if (strlen($this->getButtonText()) > 0) {
			$code .= ' value="'.htmlentities($this->getButtonText()).'"';
		}
		
		$code .= ' />';
		
		if ($this->getShowResetButton() === 1) {
			$code .= ' <input type="reset"';
			if (strlen($this->getResetButtonText()) > 0) {
				$code .= ' value="'.htmlentities($this->getResetButtonText()).'"';
			}
			$code .= ' />';
		}
		
		$code .= $this->getTailCode();
		return $code;
	}

}

/**
 * Enables the user to select a number of stats.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class HtmlStatSelector extends StatSelector
{
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
		$cols = 4; /*No less than 4 cols! */
		$out = "";

		$noKeys = $this->getSize();
	}

	/**
	 * Returns the html code for the head
	 * This function currently contains no implementation and returns void.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $cols !!Add doc!!
	 * @return SiteElement[] !!Set this return type to the correct!!!
	 */
	function getHead($cols)
	{
		//Set this methods return type and param correctly!!
	}

}

/**
 * Creates a selector to select between the different types of stat sites.
 */
class HtmlTypeSelector extends TypeSelector {

	/**
	 * Creates the generated code.
	 */
	function getCode() {
		$out = $this->getHeadCode();
		$locale = $this->siteContext->getLocalizer();

		$out .= $locale->getLocale('sgTypeText').": ";
		$out .= "<select name=\"type\" size=\"1\">\n";
		
		$supModes = $this->siteGenerator->getSupportedModes();
		for ($i = count($supModes)-1; $i >= 0; $i--) {
			$selected = "";
			if (strlen($this->selectedType) > 0 && $this->selectedType === $supModes[$i])
				$selected = " selected=\"selected\"";
			$out .= "	<option value=\"".$supModes[$i]."\"".$selected.">".htmlentities($locale->getLocale('sgType_'.$supModes[$i]))."</option>\n";
		}
		
		$out .= "</select>\n";
		$out .= "<br />\n";
		$out .= $this->getTailCode();
		return $out;
	}

}

/**
 * Draws a calendar in HTML.
 *
 * @author Simon Mikkelsen
 */
class HtmlCalendarMaker extends CalendarMaker {

	/**
	 * Creates a new instance.
	 *
	 * @param $month a unix time stamp that falls within the month
	 *               to make a calendar for.
	 */
	function HtmlCalendarMaker($month) {
		parent::CalendarMaker($month);
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
		$html = "<table class=\"calendar\">\n";
		$html .= "\t<tr>\n\t\t<th colspan=\"".count($this->dayNames)."\">";

		if (strlen($this->monthLink) > 0)
			$html .= "<a href=\"".htmlentities($this->monthLink)."\">";
	
		$html .= htmlentities($this->monthNames[date('n', $this->month)]);

		if (strlen($this->monthLink) > 0)
			$html .= "</a>";
			
		if ($this->yearAfterMonth !== false) {
			$html .= ' - ';
			if (strlen($this->yearLink) > 0)
				$html .= "<a href=\"".htmlentities($this->yearLink)."\">";

			$html .= htmlentities(date('Y', $this->month));

			if (strlen($this->yearLink) > 0)
				$html .= "</a>";
		}

		$html .= "</th>\n\t</tr>\n";
		$html .= "\t<tr>\n";
		
		$html .= "\t\t<td class=\"dayNames\">".htmlentities($this->weekLabel)."</td>\n";
		for ($i = 1; $i < count($this->dayNames); $i++)
		{
			$html .= "\t\t<td class=\"dayNames\">".htmlentities($this->dayNames[$i])."</td>\n";
		}
		
		$html .= "\t</tr>\n";
		return $html;
	}

	/**
	 * Returns the end of the calendar.
	 *
	 * @return the end of the calendar.
	 */
	function makeEnd() {
		return "</table>\n";
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
		$html = "\t\t<td class=\"";
		$html .= ($otherMonth ? 'other' : 'day');
		$html .= "\">";

		$day = date('j', $date);	
		$link = '';
		if (isset($this->dayLinks[$day]) and strlen($this->dayLinks[$day]) > 0
		    and $otherMonth === false) {
			$link = $this->dayLinks[$day];
		}
		$makeLink = (strlen($link) > 0);

		if ($makeLink) {
			$html .= "<a href=\""
			      .htmlentities($link)
			      ."\">";
		}
		
		$html .= $day; 

		if ($makeLink) {
			$html .= "</a>";
		}
		$html .= "</td>\n";
		return $html;
	}

	/**
	 * Returns the code to start a week.
	 * 
	 * @param $week unix time stamp within the week in question.
	 * @return the code to start a week.
	 */
	function makeWeekStart($week) {
		$html = "\t<tr>\n\t\t<td class=\"week\">";
		
		$weekNo = date('W', $week);
		$link = '';
		if (isset($this->weekLinks[$weekNo])) {
			$link = $this->weekLinks[$weekNo];
		}
		$makeLink = (strlen($link) > 0);
		
		if ($makeLink) {
			$html .= "<a href=\""
			      . htmlentities($link)
			      . "\">";
		}

		$html .= $weekNo;

		if ($makeLink) {
			$html .= "</a>";
		}
		$html  .= "</td>\n";
		return $html;
	}

	/**
	 * Returns the code to end a week.
	 * 
	 * @return the code to end a week.
	 */
	function makeWeekEnd() {
		return "\t</tr>\n";
	}
	
}



?>
