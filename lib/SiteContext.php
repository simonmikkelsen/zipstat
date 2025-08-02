<?php

/**
 * Contains all relevant information about this request.
 * Used for parsing this information along to various classes.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class SiteContext
{
	/**
	 * Contains the instance of {@link Html}.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $codeLib;

	/**
	 * Contains the instance of {@link Stier}.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $options;

	/**
	 * Contains an associative array of the http parameters for the request.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $HTTP_VARS;

	/**
	 * The {@link Localizer} bound to the users language.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $locale;
	
	/**
	 * The language to use when lazy initializing the localizer.
	 * 
	 * @private
	 * @since 0.0.1
	 */
	var $language;
	
	/**
	 * An instance of the legasy mapper.
	 */
	var $legasyMapper = NULL;

	/**
	 * Creates a new instance.
	 * All instances should be given as references
	 * (put <code>&amp;</code> before the name of the variable).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $codeLib   the instance of {@link Html}.
	 * @param $options   the instance of {@link Stier}.
	 * @param $http_vars an associative array of the http parameters
	 *                   for the request.
	 * @param $language  the ISO code for the users prefered language.
	 */
	function __construct(&$codeLib,&$options,$HTTP_VARS,$language)
	{
		$this->codeLib = &$codeLib;
		$this->options = &$options;
		$this->HTTP_VARS = $HTTP_VARS;
		$this->locale = NULL; //new Localizer($language,$this);
		$this->language = $language;
	}

	/**
	 * Returns an instance of {@link Html}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Html an instance of {@link Html}.
	 */
	function &getCodeLib()
	{
		return $this->codeLib;
	}


	/**
	 * Sets an instance of {@link Html}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Html an instance of {@link Html}.
	 */
	function setCodeLib(&$codeLib)
	{
		$this->codeLib = &$codeLib;
	}

	/**
	 * Returns the instance of {@link Stier}.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Stier the instance of {@link Stier}.
	 */
	function &getOptions()
	{
		return $this->options;
	}

	/**
	 * Returns the option corresponding to the key given as parameter.
	 * See {@link Stier} for specifications.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $option the key to the wanted option.
	 * @return String the option corresponding to the given key.
	 * @see Stier
	 */
	function getOption($option)
	{
		return $this->options->getOption($option);
	}

  function setOptions(&$options) {
    $this->options = &$options;
  }

	/**
	 * Returns the path corresponding to the given key.
	 * See the class {@link Stier} for specifications.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $options the key that corresponds to the wanted path.
	 * @return String the path corresponding to the given key.
	 * @see Stier
	 */
	function getPath($path)
	{
		return $this->options->getPath($path);
	}

	/**
	 * Returns the associative array of the http parameters for the request.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return String[] the associative array of the http parameters for the request.
	 */
	function getHTTP_VARS()
	{
		return $this->HTTP_VARS;
	}

	/**
	 * Returns the value corresponding to the given
	 * <code>$key</code>, from the url parameters.
	 *
	 * @public
	 * @version 0.0.1
	 * @since   0.0.1
	 * @param   $key the key to the wanted url parameter.
	 * @return  String the value corresponding to the given <code>$key</code>,
	 *          from the url parameters.
	 */
	function getHttpVar($key)
	{
		if (isset($this->HTTP_VARS) and is_array($this->HTTP_VARS) and isset($this->HTTP_VARS[$key]))
			return $this->HTTP_VARS[$key];
		else
			return "";
	}

	/**
	 * Returns the {@link Localizer} that is tied to the users language.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return Localizer the {@link Localizer} that is tied to the users language.
	 */
	function &getLocalizer()
	{
		if ($this->locale === NULL)
			$this->locale = new Localizer($this->language,$this);
		return $this->locale;
	}

	/**
	 * Returns the localized text for the given key.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $key the key for witch the text is wanted.
	 * @return String the localized text for the given key.
	 */
	function getLocale($key)
	{
		//Initialize the localizer if it isn't
		if ($this->locale === NULL)
			$this->getLocalizer();
		return $this->locale->getLocale($key);
	}
	
	/**
	 * Returns the {@link UrlBuilder} which matches the <code>$key</code>.
	 * The following keys are currently supported:
	 * <code>statsite</code>: Builds the url for the statsite.<br>
	 *
	 * @public
	 * @since 0.0.1
	 * @param $key the key which represents the wanted urlbuilder.
	 * @return UrlBuilder the wanted <code>UrlBuilder</code>.
	 */
	function getUrlBuilder($key)
	{
		$key = strtolower($key);
		if ($key === "statsite")
		{
			return new StatSiteUrlBuilder($this);
		}
		else
		{
			echo "<b>Error:</b> The key &quot;$key&quot; is not supported by <code>SiteContext-&gt;getUrlBuilder</code>.";
			exit;
		}
	}

	/**
	 * Sets an instance of the legasy mapper.
	 * 
	 * @public
	 * @param $legasyMapper an instance of the legasy mapper.
	 */
	function setLegasyMapper(&$legasyMapper) {
		$this->legasyMapper = &$legasyMapper;
	}
	
	/**
	 * Sets an instance of the legasy mapper.
	 * 
	 * @public
	 * @returns LegasyMapper an instance of the legasy mapper.
	 */
	function &getLegasyMapper() {
		return $this->legasyMapper;
	}

}

/**
 * Version of the {@link SiteContext} which does not require the code
 * lib in the constructor.
 */
class ShortSiteContext extends SiteContext {

	/**
	 * Creates a new instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $options   the instance of {@link Stier}.
	 * @param $http_vars an associative array of the http parameters
	 *                   for the request.
	 * @param $language  the ISO code for the users prefered language.
	 */
	function __construct(&$options,$HTTP_VARS,$language) {
		$this->options = &$options;
		$this->HTTP_VARS = $HTTP_VARS;
		$this->locale = NULL; //new Localizer($language,$this);
		$this->language = $language;
	}

}

?>
