<?php
	/**
	* Create a calendar
	*
	*/
	class CCalendar 
	{
		protected $calendarData;
		protected $elements;

		function __construct()
		{
			$this->calendarData = new CCalendarData();

			// Create the elements in an array so it's easy to manipulate them
			$this->elements = array();
			$this->elements["wrapper"] = "calendarWrapper";
			$this->elements["innerWrapper"] = "calendarInnerWrapper";
			$this->elements["table"] = "calendarTable";
			$this->elements["headerImage"] = "calendarImage";
			$this->elements["titleNav"] = "calendarTitleNav";
			$this->elements["titleCalPrev"] = "calendarTitleCalPrev";
			$this->elements["title"] = "calendarTitle";
			$this->elements["col"] = "calendarCol";
			$this->elements["row"] = "calendarRow";
			$this->elements["weekHead"] = "calendarWeekHead";
			$this->elements["weekDay"] = "calendarWeekDay";
			$this->elements["week"] = "calendarWeek";
			$this->elements["day"] = "calendarDay";
			$this->elements["preview"] = "calendarDayPreview";
			$this->elements["number"] = "calendarDayNumber";
			$this->elements["flag"] = "calendarDayFlag";
			$this->elements["moon"] = "calendarDayMoon";
			$this->elements["names"] = "calendarDayNames";
			$this->elements["red"] = "calendarRedDay";
		}
		
		/**
		* Get the calendar data
		*/
		function GetCalendarData()
		{
			return $this->calendarData;
		}
		
		/**
		* Call all render sections and return it for further use
		*
		*/
		function render() 
		{			
			return getCell($this->elements["wrapper"], getCell($this->elements["innerWrapper"], $this->renderNav() . $this->renderImage() . getCell($this->elements["table"],  $this->renderCalendar())));
		}
		
		/**
		* Render the image of the calendar
		*
		*/
		function renderImage()
		{
			return getCell($this->elements["headerImage"], $this->calendarData->getImage());
		}
		
		/**
		* Render the navigation, title and preview calendars
		*
		*/
		function renderNav($pShowSmallCalendars = true)
		{
			$output = "";
				
			// Get close dates for the preview calendars
			$nextDate = $this->calendarData->getNextDate();
			$prevDate = $this->calendarData->getPrevDate();
			
			if($pShowSmallCalendars)
			{
				// Get the prev and next calendars and force them to another date
				$smallCalendar = new CCalendarSmall(false);
				$smallCalendar->resetDate($this->calendarData->getNextDate()[1], $this->calendarData->getNextDate()[0]);
				$smallCalendar2 = new CCalendarSmall(false);
				$smallCalendar2->resetDate($this->calendarData->getPrevDate()[1], $this->calendarData->getPrevDate()[0]);
			}
			
			// Print the navigation and title into a div block for future use
			$titleNav = "<p><a href = \"?year=" . $prevDate[1] . "&month=" . $prevDate[0] . "\"><</a></p>";
			$titleNav .= "<p>" . $this->renderTitle() . "</p>";
			$titleNav .= "<p><a href = \"?year=" . $nextDate[1] . "&month=" . $nextDate[0] . "\">></a></p>";
			
			
			//$titleNav = getCell($this->elements["titleNav"] . " ", "<a href = \"?year=" . $prevDate[1] . "&month=" . $prevDate[0] . "\"><</a>");
			//$titleNav .= $this->renderTitle();
			//$titleNav .= getCell($this->elements["titleNav"] . " ",  "<a href = \"?year=" . $nextDate[1] . "&month=" . $nextDate[0] . "\">></a>");
			
			$titleNav .= getCell("clear", "");
			
			if($pShowSmallCalendars)
			{
				// Nestle the title and navigation with the preview calendars
				$output .= getCell("calendarTitleNavCalPrev left", $smallCalendar2->render());
				$output .= getCell("calendarTitleNavCalPrev right", $smallCalendar->render());
				$output .= getCell("clear", "");
				$output .= getCell("calendarTitleNavWrapper ", $titleNav);
				
			}
			else
			{
				$output .= getCell("calendarTitleNavWrapper", $titleNav);
			}
			
			$output .= getCell("clear");
			
			return $output;
		}
		
		/**
		* Force the calendar to show another date
		*
		*/
		function resetDate($pYear, $pMonth)
		{
			$this->calendarData->resetDate($pYear, $pMonth);
		}
		
		/**
		* Render the title (month - year)
		*
		*/
		function renderTitle()
		{
			return $this->calendarData->getCurrentMonthString() . " " . $this->calendarData->getCurrentYearString();
		}
		
		/**
		* Render the actual calendar
		*
		*/
		function renderCalendar()
		{			
			// Print the "v" in the left corner
			$row = getCell($this->elements["col"] . " " . $this->elements["weekHead"], "v");
			
			// Print weekdays from monday to sunday
			for($i = 0; $i < 7; $i++)  {
				$row .= getCell($this->elements["col"] . " " . $this->elements["weekDay"], $this->calendarData->getWeekDayString($i));
			}
			
			// Put the above information in ONE row
			$output = getCell($this->elements["row"] . "", $row);
		
			// Iterate the rest of the rows in the calendar
			for($i = 0; $i < 6; $i++)
			{
				// Print the week number of the current row
				$row = getCell($this->elements["col"] . " " . $this->elements["week"], $this->calendarData->getWeek($i));
				
				// Iterate all columns of the row for the day numbers
				for($j = 0; $j < 7; $j++) {
					// Create the cell and get all information associated to the day
					$row .= getCell($this->elements["col"] . " " . $this->elements["day"], $this->getDayCell(($j + 1) + $i * 7));
				}
				
				// Put the columns in a row
				$output .= getCell($this->elements["row"] . "", $row);
			}
			
			return $output;
		}
		
		/**
		* Get information of a day like red day, flag day, moon phase or names day
		*
		*/
		function getDayCell($pIndex)
		{			
			$output = "";
			
			// Get information from the external class Calendar Data
			extract($this->calendarData->getDayInfo($pIndex));
			
			// Check if it's a red day
			$redDayEl = (isset($redDay) ? $this->elements["red"] : "");
			
			// Check if it's a preview date (like prev and next month)
			if($preview) 
			{
				// If it's a preview just print the number, also show if it's a red day
				$output .= getCell("right " . $this->elements["preview"] . " " . $this->elements["number"] . " " . $redDayEl, $dayNumber);
				$output .= getCell("clear");
			}
			else 
			{
				$names = "";
				
				// Iterate and print all name days if any
				foreach($nameDays as $name) {
					$names .= "<p>" . $name . "</p>";
				}
				
				// Print the name of the red day if any
				$names .= (isset($redDay) && !empty($redDay) ? "<p class = \"calendarNameRed\">" . $redDay . "</p>" : "");

				// Print a flag if it's a flag day
				if($flagDay) {
					$output .= getCell($this->elements["flag"], "<img src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/calendar/sweflag.png" . "\">");
				}
				
				// Print a moon if it's a moon phase worth mentioning (new moon, ascending half moon, full moon, descending half moon)
				if($moonPhase >= 0) {
					$output .= getCell($this->elements["moon"], "<img src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/calendar/moon_" . $moonPhase . ".png" . "\">");
				}
								
				// Print the number of the day and make it red if it's a red day
				$output .= getCell("right " . $this->elements["number"] . " " . $redDayEl, $dayNumber);
				$output .= getCell("clear");
				
				// Print the names found
				$output .= getCell($this->elements["names"], $names);
			}
			
			return $output;
		}
	}
?>