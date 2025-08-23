<?php

/**
 * Calculates if an event is to be repeated now.
 *
 * @author Simon Mikkelsen
 */
class EventCalculator {

	/**
	 * The time of the calculation. Set to @c time() at instantiation and
	 * usually only altered for testing purposes.
	 *
	 * @private
	 */
	var $calcTime;
	
	/**
	 * Creates a new instance.
	 *
	 * @param $currentTime the current time. This may be adjusted for time zone etc.
	 */
	function __construct($currentTime = NULL) {
		if ($currentTime === NULL) {
			$currentTime = time();
		}
		$this->setCalcTime($currentTime);
	}

	/**
	 * Calculates if an event is to be repeated now.
	 *
	 * @param $latestOccurence the latest time, in unix time, the
	 *                         event occurd.
	 * @param $schedule        array of the schedule. Each index must contain
	 *                         a specification of the day, then ;; and finally
	 *                         hour of the event.
	 *                         The day can be:
	 *                         man, tir, ons, tor, fre, lor or son for monday to sunday
	 *                         or 1-31 for the date. In a month with 28 days the 29-31 will
	 *                         count as the 1st the next month. An event is triggered after
	 *                         the time has parsed, so it can be triggered on such a
	 *                         non existing day.
	 *
	 * @public
	 * @return if an event is to be repeated now (<code>true</code>) or not
	 *         (<code>false</code>).
	 */
	function repeatNow($latestOccurence, $schedule) {
		for ($i = 0; $i < count($schedule); $i++)
		{
			$dayNTime = explode(";;",$schedule[$i]);
			if (sizeof($dayNTime) < 2) {
        return false;
			}
			$day = $dayNTime[0];
			$time = $dayNTime[1];
			
			//Handles names of week days.
			if (! preg_match("/\d/",$day) and $this->weekTime($day,$time) >= $latestOccurence) {
				return true;
			}
			
			//Handles dates.
			if (preg_match("/\d/",$day) and $this->dateTime($day,$time) >= $latestOccurence) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Returns the time (in unix time) of the latest occurence of the given
	 * hour ($timeOnDay) on the given date ($date) in this month.
	 *
	 * @public
	 * @param $date      The date in the month.
	 * @param $timeOnDay The time on the day.
	 * @return the time of the latest occurence of the given hour on the
	 *         given date in this month.
	 */
	function dateTime($date, $timeOnDay)
	{
    $timeOnDay = trim($timeOnDay);
    if (! is_numeric($timeOnDay)) {
      $timeOnDay = 0;
    }

		$dates = getDate($this->calcTime);
		$mday = $dates['mday'];
		$hour = $dates['hours'];
		$mon = $dates['mon'];
		$min = $dates['minutes'];
		$sec = $dates['seconds'];
		$wday = $dates['wday'];

		if (($date > $mday) or (($date == $mday) and ($timeOnDay > $hour)))
		{
			if ($date > Html::lengthOfMont($mon))
				$addition = Html::lengthOfMont($mon);
			else
				$addition = $date;

			return $this->calcTime - Html::lengthOfMont($mon)*24*3600 +
					($addition-$mday)*86400 - $hour*3600 - $min*60 - $sec +
					$timeOnDay*3600;
		} // + 3600 at summer time
		else
		{
			return $this->calcTime - ($mday-$date)*86400 - $hour*3600 - $min*60 - $sec +
				$timeOnDay*3600;
		} // + 3600 at summer time
	}
	
	/**
	 * Returns the time (in unix time) of the latest occurence of
	 * the given week day ($week) and hour in that day ($hourInDay).
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @param $week the week in danish text form.
	 * @param $hourInDay the hour in the given day of the week.
	 * @return int something i seconds.
	 */
	function weekTime($week, $hourInDay) {
    if (! is_numeric($hourInDay)) {
      $hourInDay = 0;
    } else {
      $hourInDay = intval($hourInDay);
    }
		//Simons note: The perl version was named ugetid
		$dates = getDate($this->calcTime);
		$mday = $dates['mday'];
		$hour = $dates['hours'];
		$mon = $dates['mon'];
		$min = $dates['minutes'];
		$sec = $dates['seconds'];
		$wday = $dates['wday'];

		$weekNo = 0;
		if ($week === "man") { $weekNo = 1;}
		elseif ($week === "tir") { $weekNo = 2;}
		elseif ($week === "ons") { $weekNo = 3;}
		elseif ($week === "tor") { $weekNo = 4;}
		elseif ($week === "fre") { $weekNo = 5;}
		elseif ($week === "lor") { $weekNo = 6;}
		elseif ($week === "hda") { /*Every day*/
			$weekNo = $wday;
		} else {
			$weekNo = 0;
		}

		$weekDiff = $wday - $weekNo;
		if ($weekDiff < 0) {
			$weekDiff += 7;
		}

		$out = $weekDiff*86400 + $hour*3600 + $min*60 + $sec - $hourInDay*3600;
		if ($out < 0) {
			if ($week === "hda")
				$out += 24*3600; //Sec in a day
			else
				$out += 604800; //Sec in a week
		}

		return $this->calcTime - $out;
	}
	
	/**
	 * Sets the time the event is calculated for.
	 * The value is set to @c time() at instantiation and usually only
	 * replaced for testing purposes.
	 *
	 * @param $calcTime the time in unix time the event is calculated for.
	 * @public
	 */
	function setCalcTime($calcTime) {
		$this->calcTime = $calcTime;
	}

} //End of class EventCalculator
?>
