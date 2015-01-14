<?php
	class CCalendarData
	{
		// Get some static values and dates
		private $daysArray = array("Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag", "Söndag");
		private $monthsArray = array("Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December");
		private $redDays = array("1/1" => "Nyårsdagen", "6/1" => "Trettondedag jul", "1/5" => "Första maj", "6/6" => "Sveriges nationaldag", "25/12" => "Juldagen", "26/12" => "Annandag jul");
		private $flagDays = array("1/1", "28/1", "12/3", "30/4", "1/5", "6/6", "7/14", "8/8", "24/10", "6/11", "10/12", "23/12", "25/12", "26/12");
		
		private $currentMonth;
		private $currentYear;
		private $startWeek;
		private $startDay;
		private $daysInMonth;
		private $daysInLastMonth;
		
		private $swedishNameDays;
		private $mCustomImage;
		
		function __construct()
		{
			// Check the url for date
			$this->currentMonth = (isset($_GET["month"]) ? $_GET["month"] : date("n"));
			$this->currentYear = (isset($_GET["year"]) ? $_GET["year"] : date("Y"));

			$this->initialize();
		
			// Check the session for namedays, if none is found: get them
			if(!isset($_SESSION["calendar"]["nameDays"])) 
			{
				$this->swedishNameDays = $this->getSwedishNameDays();
				$_SESSION["calendar"]["nameDays"] = $this->swedishNameDays;
			}
			else {
				$this->swedishNameDays = $_SESSION["calendar"]["nameDays"];			
			}
		}
		
		/**
		* Set a custom image
		*/
		function SetCustomImage($pCustomImage)
		{
			$this->mCustomImage = $pCustomImage;
		}
		
		/**
		* Get the current year and month
		*/
		function GetCurrentMonthAndYear()
		{
			return array("month" => $this->currentMonth, "year" => $this->currentYear);
		}
		
		/**
		* Override the date of the calendar
		*
		*/
		function resetDate($pYear, $pMonth)
		{
			$this->currentMonth = $pMonth;
			$this->currentYear = $pYear;
			
			$this->initialize();
		}
		
		/**
		* Get some important date information
		*
		*/
		function initialize()
		{			
			$this->startWeek = date("W", strtotime($this->currentYear . "-" . $this->currentMonth . "-" . "01"));
			$this->startDay = date("N", strtotime($this->currentYear . "-" . $this->currentMonth . "-" . "01"));
			
			$this->daysInMonth = date("t", strtotime($this->currentYear . "-" . $this->currentMonth));
			$this->daysInLastMonth = date("t", strtotime($this->currentYear . "-" . ($this->currentMonth - 1)));
			
			$this->getDynamicRedFlagDays();
		}
		
		/**
		* Get all dynamic red and flag days
		*
		*/
		function getDynamicRedFlagDays()
		{
			$this->redDays[date("j/n", easter_date($this->currentYear) - 86400 * 2)] = "Långfredagen"; // good friday
			$this->redDays[date("j/n", easter_date($this->currentYear))] = "Påsk"; // easter
			$this->redDays[date("j/n", easter_date($this->currentYear) + 86400)] = "Annandag påsk"; // easter monday
			$this->redDays[date("j/n", easter_date($this->currentYear) + 86400 * 39)] = "Kristi Himmelfärdsdag"; //feast of the ascension
			$this->redDays[(20 + (6 - date("N", strtotime($this->currentYear . "-06-20")))) . "/6"] = "Midsommar"; //midsummer
			
			// All saints date
			$allSaintsMonth = 10;
			$allSaintsDay = (31 + 6 - date("N", strtotime("2014-10-31")));
			if($allSaintsDay > 31) 
			{
				$allSaintsDay -= 31;
				$allSaintsMonth = 11;
			}
			
			$this->redDays[$allSaintsDay . "/" . $allSaintsMonth] = "Alla helgons dag";
			
			$this->flagDays[] = date("j/n", easter_date($this->currentYear)); // easter
			$this->flagDays[] = (20 + (6 - date("N", strtotime($this->currentYear . "-06-20")))) . "/6"; //midsummer			
			$this->flagDays[] = date("j/n", easter_date($this->currentYear) + 86400 * 7 * 7); // whitsuntide
		}
		
		/**
		* Get the next month and year
		*
		*/
		function getNextDate()
		{
			$nextMonth = $this->currentMonth + 1;
			$nextYear = $this->currentYear;
			
			if($nextMonth > 12)
			{
				$nextMonth = 1;
				$nextYear++;
			}
			
			return array($nextMonth, $nextYear);
		}
		
		/**
		* Get the previous month and year
		*
		*/
		function getPrevDate()
		{
			$prevMonth = $this->currentMonth - 1;
			$prevYear = $this->currentYear;
			
			if($prevMonth < 1)
			{
				$prevMonth = 12;
				$prevYear--;
			}
			
			return array($prevMonth, $prevYear);
		}
		
		/**
		* Print the mont as a readable string (January, February etc.)
		*
		*/
		function getCurrentMonthString()
		{
			return $this->monthsArray[$this->currentMonth - 1];
		}
		
		/**
		* Get the current year
		*
		*/
		function getCurrentYearString()
		{
			return $this->currentYear;
		}

		/**
		* Get the image associated with the month
		*
		*/
		function getImage()
		{
			if(!isset($this->mCustomImage)) {
				$img = APPELICIOUS_SERVERPATH . "webroot/img/calendar/" . $this->currentMonth . ".png";
				$img = "<a href = \"" . $img . "\"><img src = \"" . $img . "\"></a>";
			}
			else {
				$img = $this->mCustomImage;
			}
			
			return $img;
		}
		
		/**
		* Get the week number relevant to the start week
		*
		*/
		function getWeek($pIndex)
		{	
			$week = $pIndex + $this->startWeek;
			
			if($week < 0) {
				$week += 52;
			}
			
			if($week > 52) {
				$week -= 52;
			}
			
			return $week;
		}
		
		/**
		* Print the day as a readable string (Monday, tuesday etc.)
		*
		*/
		function getWeekDayString($pIndex)
		{
			return $this->daysArray[$pIndex];
		}
		
		/**
		* Get relevant information for the current date
		*
		*/
		function getDayInfo($pIndex)
		{
			$dayInfo = array();
			
			// Check if it's a preview date
			$dayInfo["preview"] = (($pIndex < $this->startDay) || ($pIndex >= ($this->daysInMonth + $this->startDay)));
			
			// Get the day number
			$dayInfo["dayNumber"] = $this->daysInLastMonth - $this->startDay + 1 + $pIndex;
			$month = $this->getPrevDate()[0];
			if($dayInfo["dayNumber"] > $this->daysInLastMonth) 
			{
				$dayInfo["dayNumber"] = $pIndex - $this->startDay + 1;
				$month = $this->currentMonth;
				
				if($dayInfo["dayNumber"] > $this->daysInMonth) 
				{
					$dayInfo["dayNumber"] = $pIndex - ($this->daysInMonth + $this->startDay - 1);
					$month = $this->getNextDate()[0];
				}
			}
			
			if(!$dayInfo["preview"]) 
			{
				// Get the name days associated with this date
				$dayInfo["nameDays"] = $this->findNameDay($this->currentMonth, $dayInfo["dayNumber"]);
				
				// Check if it's a flag date
				$dayInfo["flagDay"] = $this->isFlagDay($dayInfo["dayNumber"], $this->currentMonth);
				
				// Check the moon phase of the date
				$dayInfo["moonPhase"] = $this->getMoonPhase($this->currentYear, $this->currentMonth, $dayInfo["dayNumber"]);
			}
			
			// Check it it's a red day
			$dayInfo["redDay"] = $this->isRedDay($pIndex, $dayInfo["dayNumber"], $month);
		
			return $dayInfo;
		}
		
		/**
		* Check if the flag date array contains a date
		*
		*/
		function isFlagDay($pDay, $pMonth)
		{
			return in_array($pDay . "/" . $pMonth, $this->flagDays);
		}
		
		/**
		* Check if a date is a red day
		*
		*/
		function isRedDay($pWeekDay, $pDay, $pMonth)
		{	
			$redDay = null;
			
			// If it's a sunday
			if($pWeekDay % 7 == 0) {
				$redDay = "";
			}
			
			// If the date array contains the date
			$redDay = (isset($this->redDays[$pDay . "/" . $pMonth]) ? $this->redDays[$pDay . "/" . $pMonth] : $redDay);
			
			return $redDay;
		}
		
		/**
		* Get swedish name day the easy way
		*
		*/
		function getSwedishNameDays()
		{
			// Find a site with all names
			$site = file_get_contents("http://www.dagensnamnsdag.nu/namnsdagar/");
			// Create a regex to extract the names and dates from the site
			$re = "/<a href=\"\/namn\/.+?\/\">(.+?)<\/a>(.+?)<br \/>/s"; 

			preg_match_all($re, $site, $matches);
			
			// Remove irrelevant information
			unset($matches[0]);
			unset($matches[1][0]);
			unset($matches[2][0]);
			
			$nameDays = array();
			for($i = 1; $i < count($matches[1]) + 1; $i++) 
			{
				// Remove all inofficial names
				if(strpos($matches[2][$i], "inofficiell") === false)
				{
					$nameDays[$i]["name"] = $matches[1][$i];
					$nameDays[$i]["date"] = preg_replace("/\s+/", "", $matches[2][$i]);
				}
			}
			
			return $nameDays;
		}
		
		/**
		* Find name days associated with a date
		*
		*/
		function findNameDay($pMonth, $pDay)
		{
			$nameDay = array();
			
			// Iterate all names
			foreach($this->swedishNameDays as $nameDate)
			{
				//Extract all names with the matching date
				if($nameDate["date"] == $pDay . strtolower($this->monthsArray[$pMonth - 1])) 
				{
					// Remove possible dublicates
					if(!in_array($nameDate["name"], $nameDay)) {
						$nameDay[] = $nameDate["name"];
					}
				}
			}

			return $nameDay;
		}
		
		/**
		* Check the moon phase for a date
		*
		*/
		function getMoonPhase($y, $m, $d)
		{
			// Include an external library
			require_once("moonphase.inc.php");
			
			// Check the whole day
			$start = strtotime("$y-$m-$d 00:00:00 CET");
			$stop = strtotime("$y-$m-$d 23:59:59 CET");			
			
			// New moon, half moon, descending and ascending moon are the only relevant phases for us
			$phase = -1;
			$phases = array (1, 2, 3, 4);
			$times = phaselist($start, $stop);
			
			// Iterate and check the the whole interval
			foreach ($times as $time)  
			{
				if ($time == $times[0]) {
					$phase = $phases[$times[0]];
				}
			}
			
			return $phase;
		}
		
		/**
		* For the small calendar, make it easy to shorten the days
		*
		*/
		function shortenDayStrings()
		{
			foreach($this->daysArray as $key => $val) {
				$this->daysArray[$key] = substr($val, 0, 3);
			}
		}
	}
?>