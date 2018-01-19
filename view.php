<?php

/**
 * Representerer HTML-siden, og udskriver standardkoder.
 *
 * <p><b>Fil: view.php</b></p>
 *
 * @version 0.0.1
 * @public
 * @author Simon Mikkelsen
 */
class HtmlSite
{
	/**
	 * Sidens titel.
	 *
	 * @since 0.0.1
	 * @private
	 */
	var $title;

	/**
	 * Angiver sidens type.
	 * Se metoden <code>setSideType(int)</code>.
	 *
	 * @since 0.0.1
	 * @private
	 */
	var $sideType;

	/**
	 * Indeholder den HTML der tilhrer kernen i siden.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $html;

	/**
	 * Indeholder css der er specifikt for siden.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $css;

	/**
	 * Indeholder stien til et style sheet.
	 *
	 * @private
	 * @sice 0.0.1
	 */
	 var $cssUrl;

	/**
	 * Angiver om der skal genereres en overskrift.
	 * Se metoden <code>setGenererOverskrift(int)</code> for ydligere specifikationer.
	 *
	 * @private
	 * @sice 0.0.1
	 */
	 var $genererOverskrift;

	 /**
	  * The context of this site.
	  *
	  * @private
	  * @since 0.0.1
	  */
	 var $siteContext;

	/**
	 * Instantierer klassen.
	 *
	 * @version 0.0.1
	 * @public
	 * @param $siteContext the siteContext of the application.
	 * @param $title sidens titel.
	 * @since 0.0.1
	 */
	function HtmlSite($siteContext, $title="")
	{
		$this->setSiteContext($siteContext);
		$this->setTitle($title);
		$this->clearHtml();
		//$this->setCss(Null);
		$this->setCssUrl("http://www.zipstat.dk/cgi-bin/zipstat/css.cgi2");
		//$this->setCssUrl("");
		$this->setSideType(0);
		$this->setGenererOverskrift(1); /*Der skal ikke vises en overskrift.*/
	}

	/**
	 * Returnerer hele siden's HTML-koder, klar til udskrivning.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @return String hele siden's HTML-koder.
	 */
	function getSite() {
		$sg = SiteGenerator::getGenerator('html', $this->siteContext);

		//Create a title if requested
		if ($this->getGenererOverskrift()) {
			$headline = $sg->newElement('headline');
			$headline->setHeadline($this->getTitle());
			$headline->setSize(1);
			$sg->addElement($headline);
		}

		//Medtager kernesiden.
		$mainSite = new CodeWrapper($this->siteContext);
		$mainSite->setWrapped($this->getHtml());
		$sg->addElement($mainSite);

                header('Content-Type: text/html; charset=utf-8'); //TODO: Put in a nicer location, but this works for now.
		return $sg->getSite();
	}

	/**
	 * Tilfjer et hjlpepunkt.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @param $tekst hjlpeteksten.
	 * @param $hjaelpTil hvad der gives hjlp til.
	 * @return void
	 */
	function addHelp($tekst,$hjaelpTil)
	{
		$this->addHtml("<a href=\"JAVAscript: alert('".$tekst."');\" title=\"".$this->siteContext->getLocale("htmlsHelpFor")
		.$hjaelpTil."...\"><img src=\"".$this->siteContext->getoption('imageURL')."/stegn2.gif\" width=9 height=14 "
		."border=0 alt=\"".$this->siteContext->getLocale("htmlsHelpFor")." ".$hjaelpTil."...\"></a>");
	}

	/**
	 * Stter sidens titel.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @param $title String, sidens titel.
	 * @return void
	 */
	function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Returnerer sidens titel.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @return String sidens titel.
	 */
	function getTitle()
	{
		return $this->title;
	}

	/**
	 * Stter sidens type.
	 * Benyttes pt. ikke.<br>
	 * Type 0: En almindelige side.<br>
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @param $sideType int, sidens type.
	 * @return void
	 */
	function setSideType($sideType)
	{
		if ($sideType == 0)
		$this->sideType = $sideType;
	}

	/**
	 * Returnerer sidens type.
	 * Benyttes pt. ikke.<br>
	 * Type 0: En almindelige side.<br>
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @return int
	 */
	function getSideType()
	{
		return $this->sideType;
	}

	/**
	 * Stter den HTML der tilhrer kernen i siden.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @param $html HTML der tilhrer kernen i siden.
	 * @return void
	 */
	function setHtml($html)
	{
		$this->html = $html;
	}

	/**
	 * Returnerer den HTML der tilhrer kernen i siden.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @return String den html der tilhrer kernen i siden.
	 */
	function getHtml()
	{
		return $this->html;
	}

	/**
	 * Tilfjer HTML der tilhrer kernen i siden.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @return void
	 * @param <code>String</code>, den HTML man nsker at tilfje.
	 */
	function addHtml($html)
	{
		$this->html .= $html;
	}

	/**
	 * Sletter den hidtil tilfjede HTML, der tilhrete kernen i siden.
	 *
	 * @since 0.0.1
	 * @public
	 * @version 0.0.1
	 * @return void
	 */
	function clearHtml()
	{
		$this->html = "";
	}

	/**
	 * Stter noget css der er specifikt for siden.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $css den css man nsker at stte.
	 */
	function setCss($css)
	{
		$this->css = $css;
	}

	/**
	 * Returnerer den css der er specifikt for siden.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String String, den css der er specifik for siden.
	 */
	function getCss()
	{
		return $this->css;
	}

	/**
	 * Tilfjer til den css der er specifikt for siden.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 * @param $css den css man nsker at tilfje.
	 */
	function addCss($css)
	{
		$this->css .= $css;
	}

	/**
	 * Sletter den hidtil tilfjede css der er specifikt for siden.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function clearCss()
	{
		$this->css = "";
	}

	/**
	 * Stter stien til et style sheet.
	 *
	 * @public
	 * @version 0.0.1
	 * @sice 0.0.1
	 * @return void
	 * @param $cssUrl String, stien til et style sheet.
	 */
	 function setCssUrl($cssUrl)
	 {
		 $this->cssUrl = $cssUrl;
	 }

	/**
	 * Returnerer stien til et style sheet.
	 *
	 * @public
	 * @version 0.0.1
	 * @sice 0.0.1
	 * @return String stien til et style sheet.
	 */
	 function getCssUrl()
	 {
		 return $this->cssUrl;
	 }

	/**
	 * Stter om der skal generes en overskrift.
	 *
	 * @public
	 * @version 0.0.1
	 * @sice 0.0.1
	 * @return void
	 * @param 1 hvis der skal genereres en overskrift, ellers 0.
	 */
	function setGenererOverskrift($bool)
	{
		if ($bool == 0 or $bool == 1)
			$this->genererOverskrift = $bool;
		else
		{
			echo "<b>Error:</b> Invalid parameter for method setGenererOverskrift(int) in class <code>HtmlSite</code>. Valid values only '1' and '0'.";
			exit;
		}
	}

	/**
	 * Returnerer om der skal generes en overskrift.
	 *
	 * @public
	 * @version 0.0.1
	 * @sice 0.0.1
	 * @return int 1 hvis der skal genereres en overskrift, ellers 0.
	 */
	function getGenererOverskrift()
	{
		return $this->genererOverskrift;
	}

	/**
	 * Tilfjer et skjult input-felt til html'en.
	 *
	 * @public
	 * @version 0.0.1
	 * @sice 0.0.1
	 * @return void
	 * @param $name name-atributten
	 * @param $value value-attributten
	 */
	function addInputHidden($name,$value)
	{
		addHtml("<input type=hidden name=$name value=\"".htmlentities($value)."\">\n");
	}

	/**
	 * Returns the context of this site.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return SiteContext the context of this site.
	 */
	function getSiteContext()
	{
		 return $this->siteContext;
	}

	/**
	 * Sets the context of this site.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $siteContext the context of this site.
	 */
	function setSiteContext($siteContext)
	{
	 $this->siteContext = $siteContext;
	}
}
?>
