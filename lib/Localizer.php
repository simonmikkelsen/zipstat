<?php

/**
 * This class provides localization.
 * A fall back language have not yet been implemented.
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class Localizer
{
	/**
	 * The array containing the localization mappings.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $localArray;

	/**
	 * The ISO code for the language.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $language;

	/**
	 * Creates an instance.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $language the ISO code for the language. Courrently only
	 *         <code>da</code> is supported.
	 * @param $siteContext an instance of the {@link SiteContext}.
	 */
	function Localizer($language,&$siteContext)
	{
		if ($language === 'da')
			$localeFile = $siteContext->getPath('languages')."/da.inc";
		elseif ($language === 'en')
			$localeFile = $siteContext->getPath('languages')."/en.inc";
		else /*Fall back*/
			$localeFile = $siteContext->getPath('languages')."/en.inc";

		require_once $localeFile;

		$this->localArray = getLocals();
	}

	/**
	 * Converts the given date to the locale format.
	 * In this vertion the parameter is just returned.
	 * In a future version the date is converted to Unix-time, no matter
	 * the format, and output in the locale format.
	 *
	 * <p>Valid values for the <code>$format</code> argument:<br>
	 * <code>0</code>: Full date and time<br>
	 * <code>1</code>: Only date and year<br>
	 * <code>2</code>: Only hour and minutes<br>
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $date the date that shall be converted.
	 * @param $format the format of the date. Refer to the table in the
	 *        description for valid values.
	 * @return String the date formated.
	 */
	function localizeDate($date, $format = 0)
	{
		if ($date === $date*1)
		{ /*This must be unix time*/
			if ($format === 0)
				return date($this->getLocale('dateLong'),$date);
			else if ($format === 1)
				return date($this->getLocale('dateDate'),$date);
			else //if ($format === 2)
				return date($this->getLocale('dateTime'),$date);
		}
		else
		{ /*Already formated, implement convertion to unix time later.*/
			return $date;
		}
	}

	/**
	 * Converts the given number of seconds to a readable number.
	 * E.g. 115 seconds will be 1 minute and 55 seconds.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $secs the seconds that shall be converted.
	 * @return String a locale formatted <code>String</code> containing the
	 *         converted seconds.
	 */
	function secsToReadable($secs)
	{
		$min = round(($secs/60)-0.5);
		$sec = $secs - $min*60;

		if ($min > 0)
			$minPart = $min." ".$this->getLocale('minuteShort')." ";
		else
			$minPart = "";

		return $minPart.$sec." ".$this->getLocale('secondsShort');
	}

	/**
	 * Returns the string corresponding to the given key.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $key the key the returned string shall correspond to.
	 * @return String the string corresponding to the given key.
	 */
	function getLocale($key)
	{
		if (false and !isset($this->localArray[$key])) {
			echo "Key $key is not defined in localizer:<br>\n";
			echo Debug::stacktrace();
		}
		return $this->localArray[$key];
	}
}

?>