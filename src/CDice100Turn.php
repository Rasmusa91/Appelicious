<?php
	class CDice100Turn 
	{
		private $mPlayer;
		private $mDice;
		private $mPoints;
		
		function __construct($pPlayer) 
		{
			$this->mPlayer = & $pPlayer;
			$this->mPoints = 0;
			$this->mDice = new CDice(6);
		}
		
		function roll() {
			$roll = $this->mDice->roll();
			$this->mPoints += $roll;
			
			return $roll;
		}	
		
		function getPlayer() {
			return $this->mPlayer;
		}		
		
		function getPoints() {
			return $this->mPoints;
		}
		
		function resetPoints() {
			$this->mPoints = 0;
		}		
	}
?>