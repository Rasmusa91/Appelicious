<?php
	class CMovieSearch 
	{
		private $mGenre;
		private $mQueries;
		private $mSearchQueries;
		
		function __construct($pGenre = null, $pQueries = array(), $pSearchQueries)
		{
			$this->mGenre = $pGenre;
			$this->mQueries = $pQueries;
			$this->mSearchQueries = $pSearchQueries;
		}
		
		function Render()
		{
			$output = "<form method = \"get\">";
			
			foreach($this->mQueries as $key => $value) 
			{
				if(isset($value)) {
					$output .= "<input type = \"hidden\" name = \"" . $key . "\" value = \"" . $value ."\">";
				}
			}
			
			$output .= getCell("MovieSearchRow", getCell("MovieSearchCol leftCol", "Titel:") . getCell("MovieSearchCol rightCol", "<input name = \"title\" value = \"" . $this->mSearchQueries["title"] . "\">"));
			
			if(isset($this->mGenre)) 
			{
				$genres = "";
				
				foreach($this->mGenre as $value) {
					$genres .= "<a href = \"" . extendURL("genre=" . $value . "") . "\">" . $value . "</a>, ";
				}
				
				$genres = substr($genres, 0, -2);
				
				$output .= getCell("MovieSearchRow", getCell("MovieSearchCol leftCol", "Välj genre: "). getCell("MovieSearchCol rightCol", $genres));
			}
			
			$output .= getCell("MovieSearchRow", getCell("MovieSearchCol leftCol", "Skapad mellan åren:") . getCell("MovieSearchCol rightCol smallInput", "<input name = \"yearStart\" value = \"" . $this->mSearchQueries["yearStart"] . "\"> - <input name = \"yearEnd\" value = \"" . $this->mSearchQueries["yearEnd"] . "\">"));
			
			$tempQueries = $this->mQueries;
			unset($tempQueries["genre"]);

			$output .= getCell("MovieSearchRow", getCell("MovieSearchCol leftCol", "<a href = \"" . extendURL(null, makeURLQuery($tempQueries)) . "\">Visa alla</a>") . getCell("MovieSearchCol rightCol", "<input class = \"button\" type = \"submit\" value = \"Sök\">"));
			
			$output .= "</form>";
			$output = getCell("MovieSearchTable", $output);
			$output = getCell("MovieSearchWrapper", getCell("MovieSearchTitle", "Sök") . $output);
			
			echo $output;
		}
	}
?>