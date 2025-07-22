<?php

class Panellib {

	/**
	 * The instance of the site context.
	 * @private
	 */
	var $siteContext;

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @param $siteContext the instance of the site context.
	 */
	function __construct(&$siteContext) {
		$this->siteContext = &$siteContext;
	}

	/**
	 * Returns HTML for showing of all the panels.
	 *
	 * @public
	 * @returns void
	 * @param $paneler_pr_raekke the number of panels in each row.
	 * @param $showRadiobuttons if radiobuttons for each panel shall be
	 *                          shown, this param shall have a string of
	 *                          non zero length.
	 */
	function vis_alle_paneler($paneler_pr_raekke, $showRadiobuttons) {
		$panel = file($this->siteContext->getPath('zipstat_base').'/styles/panel_css.txt');

		$retur = "<table border=0>\n";

		$ant_vist = 0;
		for ($i = 0; $i < count($panel); $i++) {
			$panel[$i] = str_replace(array("\n", "\r"), "", $panel[$i]);
			if ($ant_vist == 0)
				$retur .= "<tr>\n";

			$retur .= "	<td style=\"padding: 1em;\">\n";
			if (isset($showRadiobuttons) and strlen($showRadiobuttons) > 0) {
				$sel = "";
				if ($panel[$i] === 'standard')
					$sel = " CHECKED";
				$retur .= "<input type=radio name=paneler value=\"$panel[$i]\"$sel>";
			}
			$retur .= $this->hentpanel($panel[$i],'','','alle');
			$retur .= "\n	</td>\n";

			$ant_vist++;
			if ($ant_vist === $paneler_pr_raekke) {
				$retur .= "</tr>\n";
				$ant_vist = $paneler_pr_raekke;
			}

		}
		$retur .= "</table>\n";
		return $retur;
	}

	//todo make doc and give params meaning full names
	function panel($p0, $p1, $p2, $p3) {
		$udad = $this->hentpanel($p0, $p1, $p2, $p3);
		$udad = $this->tiljs($udad);
		$udad = htmlentities($udad);
		return $udad;
	}

	/**
	 * Converts a mini stat template to it's javascript version.
	 *
	 * @param $panelCode the html code of the panel.
	 * @public
	 */
	function tiljs($panelCode) {
		$panel = explode("\n", $panelCode);
		$udpanel = "<!-- Start p ZIP Stat ministatistik -->\n";

		$udpanel .= $panel[0]."\n";
		
		$pSearch = array(
			"\n",
			"\r",
			"\"",
			">268",
			">2468",
			">159",
			">1284",
			">269",
			">3",
			"</"
		);
		
		$pReplace = array(
			"",
			"",
			"\\\"",
			">\"+taellere[denne_taellers_nr]+\"",
			">\"+hits_siden_2+\"",
			">\"+hits_pr_dag+\"",
			">\"+hits_pr_maaned_3mdr+\"",
			">\"+hits_pr_bruger+\"",
			">\"+personer_paa_siden_nu+\"",
			"<\"+\"/"
		);
		
		for ($i = 1; $i < count($panel); $i++) {
			if ((strpos($panel[$i], '<tr><td>') !== FALSE) and (strpos($panel[$i-1], '<tr><td>') === FALSE)) {
				$udpanel .= "<script language=\"JavaScript\" type=\"text/javascript\">\n<!-- \n";
			}

			if (strpos($panel[$i], '<tr><td>') !== FALSE) {
				$panel[$i] = str_replace($pSearch, $pReplace, $panel[$i]);
				$udpanel .= "document.write(\"".$panel[$i]."\");\n";
			}

			if ((strpos($panel[$i], '<tr><td>') === FALSE) and (strpos($panel[$i-1], '<tr><td>') !== FALSE)) {
				$udpanel .= "//-->\n</script>\n";
			}

			if (strpos($panel[$i], '<tr><td>') === FALSE) {
				$udpanel .= $panel[$i]."\n";
			}	
	
		} //End for

		$udpanel .= "<!-- Slut p ZIP Stat ministatistik -->\n";

		$udpanel = str_replace("+\"\"", "", $udpanel);

		return $udpanel;
	}


	/**
	 *
	 *
	 * @param $panel file name (without the .html extention) for the panel
	 *               to fetch.
	 * @param $urlToStatSite if given, will be used as url for the stat site.
	 * @param $linkText      text to use for the link in the bottom of the panel
	 * @param $p3
	 * @public
	 */
	function hentpanel($panel, $urlToStatSite = '', $linkText = '', $p3) {
		$panel = str_replace("\n", "", $panel);
		$fileToFetch = $this->siteContext->getPath('panelHtmlFolder').'/'.$panel.'.html';
		if (!file_exists($fileToFetch)) {
			$fileToFetch = $this->siteContext->getPath('panelHtmlFolder').'/standard.html';
		}
		$tmp = file($fileToFetch);

		$medtag = explode(':',$p3);

		$ud = "";

		for ($i = 0; $i < count($tmp); $i++) {
			$tester = "";
			if (     (strpos(strtolower($tmp[$i]), 'class=tal') === FALSE)
			   or (  (strpos(strtolower($tmp[$i]), 'class=tal') !== FALSE) and ($this->findesi($tmp[$i], $medtag)) )) {
				$tester = $tmp[$i];
			}
			$ud .= $tester;
		}

		if (strlen($urlToStatSite) > 0) {
			$ud = str_replace("javascript:alert('Dette bliver et link til statistiksiden');", htmlentities($urlToStatSite), $ud);
		}
		if (strlen($linkText) > 0) {
			$ud = str_replace('Flere...', $linkText, $ud);
		}

		$panelCssToFetch = $this->siteContext->getPath('panelCssFolder').'/'.$panel.'.zstyle';
		if (!file_exists($panelCssToFetch)) {
			$panelCssToFetch = $this->siteContext->getPath('panelCssFolder').'/standard.zstyle';
		}
		$panel_css = file($panelCssToFetch);

		for ($i = 1; $i < count($panel_css); $i++) {
			$panel_css[$i] = str_replace("\n", "", $panel_css[$i]);
			$tmp = explode(';;', $panel_css[$i]);
			//todo: When migrating to PHP5, use stri_replace here.
			$ud = str_replace(
				array("class=$tmp[0] ", "class=$tmp[0]>"),
				array("style=\"$tmp[1]\"", "style=\"$tmp[1]\">"),
				$ud);
		}

		return $ud;
	}

	/**
	 * States which stats to shw in a panel.
	 *
	 * One of the parameters states which stats shall be shown,
	 * and it looks like one states which can be shown.
	 * Unfortunally this module was obscure and undocumentated.
	 *
	 * @param $statPanelLine line from the source of the stat panel.
	 * @param $medtag array of content from the lines to show.
	 * @public
	 * @returns boolean
	 * @return 1 (show) or 0 (don't show)
	 */
	function findesi($statPanelLine, $medtag) {
		if ($medtag[0] === 'alle')
			return 1;

		for ($i = 0; $i < count($medtag); $i++) {
			if (isset($medtag[$i]) and strlen($medtag[$i]) > 0 and strpos($statPanelLine, $medtag[$i]) !== FALSE)
				return 1;
		}
		return 0;
	}



} //End class Panellib
?>
