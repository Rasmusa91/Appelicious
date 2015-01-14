<?php
	class CDice100Player 
	{
		private $mID;
		private $mName;
		private $mPoints;

		function __construct($pID, $pName, $pPoints = 0) 
		{
			$this->mID = $pID;
			$this->mName = $pName;
			$this->mPoints = $pPoints;
		}
		
		function getID() {
			return $this->mID;
		}
		
		function getName() {
			return $this->mName;
		}

		function getPoints() {
			return $this->mPoints;
		}	

		function addPoints($pPoints) {
			$this->mPoints += $pPoints;
		}
	}
?>