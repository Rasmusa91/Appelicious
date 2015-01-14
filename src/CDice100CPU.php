<?php
	class CDice100CPU extends CDice100Player 
	{
		function __construct($pID, $pName, $pPoints = 0) 
		{
			parent::__construct($pID, $pName, $pPoints);
		}
		
		function wantsRoll($pRolls)
		{
			return (rand(0, 100) <= 100 * (1 - (0.1 * $pRolls)));
		}
	}
?>