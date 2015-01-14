<?php
	/**
	* Create a smaller version of the calendar
	*
	*/
	class CCalendarSmall extends CCalendar
	{
		private $canNavigate;
		
		function __construct($pCanNavigate = true) 
		{
			parent::__construct();
			
			$this->canNavigate = $pCanNavigate;
			
			// Extend ALL classes with the "small" keyword for the .css
			foreach($this->elements as $key => $val) {
				$this->elements[$key] = $val . " " . $val . "Small";  
			}
			
			$this->calendarData->shortenDayStrings();
		}
		
		/**
		* Override the render image function and void it to not show an image
		*
		*/
		function renderImage()
		{}
		
		/**
		* Override the render nav function
		*
		*/
		function renderNav($pShowSmallCalendars = true)
		{
			$output = "";
			
			// If user should be able to navigate just call the parent function
			if($this->canNavigate) {
				$output .= parent::renderNav(false);
			}
			// If not, just render the title
			else {
				$output .= getCell($this->elements["title"], $this->renderTitle());
			}
			
			return $output;
		}
		
		/**
		* Override the get day cell function to only print the number of the day
		*
		*/
		function getDayCell($pIndex)
		{			
			$output = "";
			
			extract($this->calendarData->getDayInfo($pIndex));
			
			// Check if it's a red day
			$redDay = (isset($redDay) ? $this->elements["red"] : "");
			
			// If it's a preview date, just print the day a bit smaller than the default one
			if($preview) {
				$output .= getCell($this->elements["preview"] . " " . $this->elements["number"] . " " . $redDay, $dayNumber);
			}
			else {
				$output .= getCell($this->elements["number"] . " " . $redDay, $dayNumber);
			}

			$output .= getCell("clear");
			
			return $output;
		}		
	}
?>