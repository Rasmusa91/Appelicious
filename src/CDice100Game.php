<?php
	class GameState
	{
		const ChoosePlayersAmount = 0;
		const InitPlayers = 1;
		const Turn = 2;
		const Victory = 3;
	}

	class CDice100Game 
	{
		const MinPlayers = 1, MaxPlayers = 4, MinCPUs = 0, MaxCPUs = 4, RollToLose = 1;
	
		private $mGameState;
		private $mPlayersAmount;
		private $mCPUsAmount;
		private $mPlayers;
		private $mCurrentTurn;
		private $mOneOnOne;
		private $mAdditionalWinMessage;
		
		function __construct($pOneOnOne = false) {
			$this->mOneOnOne = $pOneOnOne;
			
			$this->initialize();
		}
		
		function SetAdditionalWinMessage($pMsg)
		{
			$this->mAdditionalWinMessage = $pMsg;
		}
		
		/**
		* Reset the game
		*
		*/	
		function initialize()
		{
			if(!$this->mOneOnOne) {
				$this->mGameState = GameState::ChoosePlayersAmount;
				$this->mPlayersAmount = 0;
			}
			else {
				$this->mGameState = GameState::InitPlayers;
				$this->mPlayersAmount = 1;			
				$this->mCPUsAmount = 1;			
			}
			
			$this->mPlayers = array();
			$this->mCurrentTurn = null;
		}
	
		
		/**
		* Check the state of the game and call functions thereafter
		*
		*/
		function play() 
		{
			$this->checkExit();
			
			switch($this->mGameState) 
			{
				// Choose the amount of players
				case GameState::ChoosePlayersAmount:
					$output = $this->choosePlayersAmount();
				break;
				
				// Initialize the player names
				case GameState::InitPlayers:
					$output = $this->initializePlayers();
				break;
				
				// Play the game
				case GameState::Turn:
					$output = $this->turn();
				break;
				
				// End the game
				case GameState::Victory:
					$output = $this->printVictoryScreen();
				break;				
			}
			
			$this->render($output);
		}
		
		/**
		* Render the game based on the content from the play function
		*
		*/
		function render($pContent) 
		{
			if(!empty($pContent))
			{
				// Description
				echo "<h2>Först till 100 vinner, kastar du en 1:a så nollställs nuvarande rundas poäng.</h2>";
				
				// Score if any
				for($i = 0; $i < $this->mPlayersAmount + $this->mCPUsAmount; $i++) 
				{
					if(isset($this->mPlayers[$i]))
					{
						echo "<p>";
						echo "<b>" . $this->mPlayers[$i]->getName() . "</b>: " . $this->mPlayers[$i]->getPoints() . " poäng.";
						echo "</p>";
					}
				}
				
				// Content
				echo $pContent;
			}
		}

		/**
		* Check if the exit button was pressed
		*
		*/
		function checkExit() 
		{
			// If the button was pressed, reset the game
			if(isset($_POST["gameDice100Exit"])) {
				$this->initialize();
			}
		}
		
		/**
		* Get the next player to play
		*
		*/
		function getNextPlayer()
		{
			// Current player
			$id = $this->mCurrentTurn->getPlayer()->getID();
			
			// Check if someone has won
			if(!$this->checkWin()) 
			{
				// Get the next player
				$id++;

				// If the list of players has come to its end, go back to the first player
				if($id >= $this->mPlayersAmount + $this->mCPUsAmount) {
					$id = 0;
				}
			}
			
			return $this->mPlayers[$id];
		}
		
		/***********************************************************************
		* Choose players amount
		* Start
		*/
		function choosePlayersAmount() 
		{
			$output = "";
			
			// If the button has not been pressed print the form
			if(!isset($_POST["gameDice100Continue"])) {		
				$output .= $this->printPlayerAmountForm();
			}
			else {
				// Chck the form
				$errorMessage = $this->handlePlayerAmountForm();
				
				// If any errors
				if(isset($errorMessage)) 
				{
					//Re-print form
					$output .= $this->printPlayerAmountForm();
					
					// Print the errors
					$output .= "<p>";
					$output .= $errorMessage;
					$output .= "</p>";
				}
			}
			
			return $output;
		}
		
		/**
		* Print the form to choose the amount of players
		*
		*/
		function printPlayerAmountForm()
		{
			$output = "";
			
			$output .= "<h3>Du har inget spel aktivt</h3>";
			$output .= "<form method = \"post\">";
			
			$output .= "<h5>Hur många spelare vill du spela med?</h5>";
			$output .= "<input value = \"" . CDice100Game::MinPlayers . "\" name = \"gameDice100Players\"> <em>(" . CDice100Game::MinPlayers . "-" . CDice100Game::MaxPlayers . ")</em>";
			
			$output .= "<h5>Hur många datorer vill du spela mot?</h5>";
			$output .= "<input value = \"" . CDice100Game::MinCPUs . "\" name = \"gameDice100CPUs\"> <em>(" . CDice100Game::MinCPUs . "-" . CDice100Game::MaxCPUs . ")</em>";
			
			$output .= "<p><input name = \"gameDice100Continue\" type = \"submit\" value = \"Fortsätt\" class = \"button\"></p>";
			$output .= "</form>";
			
			return $output;
		}
		
		/**
		* Handle the form to choose the amount of players
		*
		*/		
		function handlePlayerAmountForm()
		{
			$errorMessage = "";
			$playersAmount = (isset($_POST["gameDice100Players"]) ? $_POST["gameDice100Players"] : null);
			$cpusAmount = (isset($_POST["gameDice100CPUs"]) ? $_POST["gameDice100CPUs"] : null);
			
			// Validate the input
			if(isset($playersAmount) && is_numeric($playersAmount)) 
			{
				if($playersAmount >= CDice100Game::MinPlayers && $playersAmount <= CDice100Game::MaxPlayers)
				{
					$this->mPlayersAmount = intval($playersAmount);
				}
				else {
					$errorMessage .= "<p>Ange antal spelare inom intervallet " . CDice100Game::MinPlayers . "-" . CDice100Game::MaxPlayers . "!</p>";
				}
			}
			else {
				$errorMessage .= "<p>Ange antal spelare som ett heltal!</p>";
			}
			
			// Validate the input
			if(isset($cpusAmount) && is_numeric($cpusAmount)) 
			{
				if($cpusAmount >= CDice100Game::MinCPUs && $cpusAmount <= CDice100Game::MaxCPUs)
				{
					$this->mCPUsAmount = intval($cpusAmount);
				}
				else {
					$errorMessage .= "<p>Ange antal datorer inom intervallet " . CDice100Game::MinCPUs . "-" . CDice100Game::MaxCPUs . "!</p>";
				}
			}
			else {
				$errorMessage .= "<p>Ange antal datorer som ett heltal!</p>";
			}
			
			if(empty($errorMessage))
			{
				$this->mGameState = GameState::InitPlayers;
				$_POST["gameDice100Continue"] = null;
				$this->play();			
			}
			
			return (!empty($errorMessage) ? $errorMessage : null);
		}
		/**
		* Choose players amount
		* End
		************************************************************************/
		
		/************************************************************************
		* Initialize players
		* Start
		*/
		function initializePlayers()
		{		
			$output = "";
			
			if(!isset($_POST["gameDice100Continue"])) {		
				$output .= $this->printInitializePlayersForm();
			}
			else 
			{
				$errorMessage = $this->handleInitializePlayersForm();
				
				if(isset($errorMessage) && !empty($errorMessage)) 
				{
					$output .= $this->printInitializePlayersForm();
					
					$output .= $errorMessage;
				}
			}			
			
			return $output;
		}
		
		function printInitializePlayersForm()
		{
			$output = "";
			
			$output .= "<h3>Ange namn för samtliga spelare</h3>";
			$output .= "<form method = \"post\">";
			for($i = 0; $i < $this->mPlayersAmount; $i++)
			{
				$output .= "<h5>Spelare " . ($i + 1) . ":</h5>";
				$output .= "<input name = \"gameDice100Player" . ($i + 1) . "\" value = \"" . (isset($this->mPlayers[$i]) && $this->mPlayers[$i]->getName() !== null ? $this->mPlayers[$i]->getName() : "") . "\">";
			}
			$output .= "<p>
					<input name = \"gameDice100Continue\" type = \"submit\" value = \"Fortsätt\" class = \"button\">
					<input name = \"gameDice100Exit\" type = \"submit\" value = \"Avsluta\" class = \"button\">
				</p>";
			$output .= "</form>";
			
			return $output;
		}
		
		function handleInitializePlayersForm()
		{
			$errorMessage = "";
			
			for($i = 0; $i < $this->mPlayersAmount; $i++) 
			{
				$player = (isset($_POST["gameDice100Player" . ($i + 1)]) ? $_POST["gameDice100Player" . ($i + 1)] : null);
				$this->mPlayers[$i] = new CDice100Player($i, $player);
								
				if(empty($player)) {
					$errorMessage .= "<p>Ange ett namn för spelare " . ($i + 1) . "</p>";
				}
			}
			
			if(empty($errorMessage))
			{
				for($i = 0; $i < $this->mCPUsAmount; $i++) {
					$this->mPlayers[$i + $this->mPlayersAmount] = new CDice100CPU($i + $this->mPlayersAmount, "CPU " . ($i + 1));
				}
			
				$_POST["gameDice100Continue"] = null;
				$this->mGameState = GameState::Turn;
				$this->mCurrentTurn = new CDice100Turn($this->mPlayers[0]);
				$this->play();
			}
			
			return $errorMessage;
		}
		/**
		* Initialize players
		* End
		************************************************************************/

		/************************************************************************
		* Make turns
		* Start
		*/
		function turn()
		{
			$output = "";
			
			if(isset($_POST["gameDice100Pass"])) {
				$this->newTurn();
			}
			
			$output .= "<h2>Det är " . ucfirst($this->mCurrentTurn->getPlayer()->getName()) . "/s tur</h2>";

			if(get_class($this->mCurrentTurn->getPlayer()) == "CDice100Player") {
				$output .= $this->turnPlayer();
			}
			else 
			{
				$output .= $this->turnCPU();
				$output .= $this->newTurn();
			}
			
			return $output;
		}
		
		function newTurn() 
		{
			$this->mCurrentTurn->getPlayer()->addPoints($this->mCurrentTurn->getPoints());
			$this->mCurrentTurn = new CDice100Turn($this->GetNextPlayer());		
		}
		
		function turnPlayer() 
		{
			$output = "";
			
			if(isset($_POST["gameDice100Roll"])) {
				$currRoll = $this->mCurrentTurn->roll();
			}	
		
			if(!isset($currRoll) || (isset($currRoll) && $currRoll != CDice100Game::RollToLose))
			{
				if(isset($currRoll)) {
					$output .= "<p>Du kastade tärningen och fick <b>" . $currRoll . "</b>.</p>";
				}
				else {
					$output .= "<p><em>Du har ännu inte kastat tärningen den här rundan.</em></p>";
				}
				
				$output .= "<h3>Poäng den här rundan: <b>" . $this->mCurrentTurn->getPoints() . ".</b></h3>";
				$output .= "<h3>Poäng totalt: <b>" . $this->mCurrentTurn->getPlayer()->getPoints() . "</b> (<b>" . ($this->mCurrentTurn->getPlayer()->getPoints() + $this->mCurrentTurn->getPoints()) . "</b>).</h3>";
				$output .= "<h2><b>Vad vill du göra?</b></h2>";
				$output .= "<form method = \"post\">";
				$output .= "<input name = \"gameDice100Roll\" value = \"Kasta\" type = \"submit\" class = \"button\"> ";
				$output .= "<input name = \"gameDice100Pass\" value = \"Passa\" type = \"submit\" class = \"button\"> ";
				$output .= "<input name = \"gameDice100Exit\" value = \"Avsluta\" type = \"submit\" class = \"button\"> ";
				$output .= "</form>";
			}
			else 
			{
				$this->mCurrentTurn->resetPoints();
				
				$output .= "<p>Du kastade tärningen och fick <b>" . CDice100Game::RollToLose . "</b> och har därför förlorat alla poäng den här rundan.</p>";
				$output .= "<p>Du har totalt <b>" . $this->mCurrentTurn->getPlayer()->getPoints() . "</b> poäng.</p>";
				$output .= "<form method = \"post\">";
				$output .= "<input name = \"gameDice100Pass\" value = \"Fortsätt\" type = \"submit\" class = \"button\"> ";
				$output .= "<input name = \"gameDice100Exit\" value = \"Avsluta\" type = \"submit\" class = \"button\"> ";
				$output .= "</form>";				
			}
			
			return $output;
		}
		
		function turnCPU()
		{
			$output = "";
			$canContinue = true;
			$rolls = 0;
			
			while($this->mCurrentTurn->getPlayer()->wantsRoll($rolls++) && $canContinue)
			{
				$currRoll = $this->mCurrentTurn->roll();
				
				$output .= "<p class = \"noSpaces smallFont\"><b><em>Kast " . ($rolls) . "</em></b></p>";
					
				if($currRoll != CDice100Game::RollToLose)
				{
					$output .= "<p class = \"noSpaces smallFont\"><em>Datorns poäng den här rundan: <b>" . $this->mCurrentTurn->getPoints() . ".</b></em></p>";
					$output .= "<p class = \"spacesBottom smallFont\"><em>Datorns poäng totalt: <b>" . $this->mCurrentTurn->getPlayer()->getPoints() . "</b> (<b>" . ($this->mCurrentTurn->getPlayer()->getPoints() + $this->mCurrentTurn->getPoints()) . "</b>).</em></p>";
				}
				else
				{
					$canContinue = false;
					$this->mCurrentTurn->resetPoints();
					
					$output .= "<p class = \"noSpaces smallFont\"><em>Datorn kastade tärningen och fick <b>" . CDice100Game::RollToLose . "</b> och har därför förlorat alla poäng den här rundan.</em></p>";
					$output .= "<p class = \"spacesBottom smallFont\"><em>Datorn har totalt <b>" . $this->mCurrentTurn->getPlayer()->getPoints() . "</b> poäng.</em></p>";
				}
			}
			
			$output .= "<p class = \"noSpaces\"><b>Totalt</b></p>";
			$output .= "<p class = \"noSpaces\">Datorns poäng den här rundan: <b>" . $this->mCurrentTurn->getPoints() . ".</b></p>";
			$output .= "<p class = \"spacesBottom\">Datorns poäng totalt: <b>" . $this->mCurrentTurn->getPlayer()->getPoints() . "</b> (<b>" . ($this->mCurrentTurn->getPlayer()->getPoints() + $this->mCurrentTurn->getPoints()) . "</b>).</em></p>";
			
			$output .= "<form method = \"post\">";
			$output .= "<input name = \"gameDice100Continue\" value = \"Fortsätt\" type = \"submit\" class = \"button\"> ";
			$output .= "<input name = \"gameDice100Exit\" value = \"Avsluta\" type = \"submit\" class = \"button\"> ";
			$output .= "</form>";
			
			return $output;
		}
		/**
		* Make turns
		* End
		************************************************************************/
		
		/************************************************************************
		* Victory
		* Start
		*/
		function checkWin()
		{
			$didWin = false;
			
			if($this->mCurrentTurn->getPlayer()->getPoints() >= 100) 
			{
				$didWin = true;
				$this->mGameState = GameState::Victory;
			}
			
			return $didWin;
		}
		
		function printVictoryScreen()
		{
			$output = "";
			
			$output .= "<h3>" . ucfirst($this->mCurrentTurn->getPlayer()->getName()) . "/s har mer än 100 poäng och därför vunnit!</h3>";
			$output .= "<form method = \"post\">";
			$output .= "<input name = \"gameDice100Exit\" value = \"Avsluta\" type = \"submit\" class = \"button\"> ";
			$output .= "</form>";	
			
			if(get_class($this->mCurrentTurn->getPlayer()) == "CDice100Player") {
				$output .= $this->mAdditionalWinMessage;
			}
			
			$this->initialize();
			
			return $output;
		}
		/**
		* Victory
		* End
		************************************************************************/		
	}
?>