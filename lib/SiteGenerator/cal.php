<html>
<body>
<?php

/**
 * Draws a calendar in HTML.
 *
 * @author Simon Mikkelsen
 */
class CalDraw {

	/**
	 * A unix time stamp that falls within the month the calendar
	 * is made for.
	 */
	var $month;

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
	 * Creates a new instance.
	 *
	 * @param $month a unix time stamp that falls within the month
	 *               to make a calendar for.
	 */
	function __construct($month) {
		$this->month = $month;
	}

	/**
	 * Output the HTML of the calendar.
	 */
	function draw() {
		//Find a timestamp within the first day of
		//the month we are in.
		//hour minute second month day year is_dst
		$firstDay = mktime(0, 0, 0, date('n', $this->month), 1,
		   date('Y', $this->month), date('I', $this->month));

		//Find the first week day of that month (0-6).
		$dfMonth = date('w', $firstDay);

		//PHP starts Sunday - we starts Monday:
		$dfMonth--;
		if ($dfMonth == -1) {
			$dfMonth = 6;
		}

		$html = $this->makeStart();
		//Make the days before this month.
		for ($i = 0; $i < $dfMonth; $i++) {
			$ctime = $firstDay - ($dfMonth - $i)*24*3600;
			$day = mktime(0, 0, 0,
			       date('n', $ctime),
			       date('j', $ctime),
			       date('Y', $ctime),
			       date('I', $ctime));
			if (date('w', $day) == 1) {
				$html .= $this->makeWeekStart($day);
			}
			$html .= $this->makeDay($day, true);
		}

		//Make currente month.
		$cmonth = date('n', $firstDay);
		$lastDay = -1;
		for ($day = $firstDay; $cmonth == date('n', $day);
		                                   $day += 24*3600) {
			if (date('w', $day) == 1) {
				$html .= $this->makeWeekStart($day);
			}
			
			$html .= $this->makeDay($day);

			if (date('w', $day) == 0) {
				$html .= $this->makeWeekEnd();
			}
			$lastDay = $day;
		}
		
		//Make the remaining days, from thenext month.
		for ($day = $lastDay + 24*3600; date('w', $day) != 1; $day += 24*3600) {
			$html .= $this->makeDay($day, true);
			if (date('w', $day) == 0) {
				$html .= $this->makeWeekEnd();
			}
		}
		
		$html .= $this->makeEnd();
		return $html;		
	}

	/**
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

		$html .= "</th>\n\t</tr>\n";
		$html .= "\t<tr>\n";
		
		for ($i = 0; $i < count($this->dayNames); $i++)
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
		if (isset($this->dayLinks[$day]) and $otherMonth === false) {
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
		$this->montn = $month;
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
	 * Sets the link for the year, or an empty string.
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
}


$cal = new CalDraw(time());
$cal->setDayLinks(array("", "http://www.1.dk"));
echo $cal->draw();
?>
</body>
</html>
