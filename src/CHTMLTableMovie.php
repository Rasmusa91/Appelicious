<?php
	class CHTMLTableMovie extends CHTMLTable 
	{
		private $mTableStructure;
		private $mOrders;
	
		function __construct($pData, $pTableStructure, $pMaxRows = null, $pLimit = null, $pPagination = null, $pServerPath = "", $pOrders = null)
		{
			parent::__construct($pData, $pMaxRows, $pLimit, $pPagination, $pServerPath);
			
			$this->mTableStructure = $pTableStructure;
			$this->mOrders = $pOrders;			
		}
		
		function Render()
		{
			//$data = $this->GetTableContentsStructured();
			$data = $this->mData;
			$output = "";
			
			$orderByDropdownSuburls = array();
			foreach($this->mOrders as $orderKey => $orderValue)
			{
				$orderByDropdownSuburls[] = array("name" => ucfirst($orderValue), "url" => extendURL("orderBy=" . strtolower($orderKey)));
			}
			
			$orderByDropdown = array( "kursmoment" => array("name" => "Sortera efter...", "url" => "", "suburls" => $orderByDropdownSuburls, "cat" => "topNav"));
			
			$headerLeft = getCell("HTMLTableMovieHeaderLeftDropdown", getCell("customDropdown", getLinksSub($orderByDropdown, "topNav")));
																												
			$headerLeft .= "<a href = \"" . extendURL("orderType=asc") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_single_down.png" . "\">" : "\/") . "</a>  <a href = \"" . extendURL("orderType=desc") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_single_up.png" . "\">" : "/\\") . "</a>";
			$headerLeft = getCell("HTMLTableMovieHeaderLeft", $headerLeft);
			
			$headerRight = getCell("HTMLTableMovieHeaderRight", $this->GetShowAmount());
			
			$output .= getCell("HTMLTableMovieHeader", $headerLeft . $headerRight . getCell("clear", ""));
			
			foreach($data as $key => $value)
			{				
				if(array_key_exists($this->mTableStructure["image"], $value)) {	
					$innerOutputLeft = getCell("HTMLTableMovieImage", "<a href = \"" . WORKSPACE_SERVERPATH . "img/movie/" . $value[$this->mTableStructure["image"]] . "\"><img src = \"" . WORKSPACE_SERVERPATH . "img/?src=movie/" . $value[$this->mTableStructure["image"]] . "&width=175&height=250&crop-fit=true\"></a>");
				}
				else {
					$innerOutputLeft = getCell("HTMLTableMovieImage", "<a href = \"" . WORKSPACE_SERVERPATH . "img/image404.jpg\"><img src = \"" . WORKSPACE_SERVERPATH . "img/?src=image404.jpg&width=175&height=250&crop-fit=true\"></a>");				
				}
				
				$innerOutputRight = "";
				$innerOutputRight .= getCell("HTMLTableMovieTitle",  "<a href = \"" . $this->mServerPath . $value[$this->mTableStructure["slug"]] . "/\">" . $value[$this->mTableStructure["title"]] . "</a> (" . $value[$this->mTableStructure["year"]] . ")");
				$innerOutputRight .= getCell("HTMLTableMoviePrice", "Pris: " . $value[$this->mTableStructure["price"]] . " SEK");
				$innerOutputRight .= getCell("clear", "");
				
				$genres = explode(",", str_replace(" ", "", $value[$this->mTableStructure["genre"]]));
				$genre = "";
				foreach($genres as $genreValue)
				{
					$genre .= "<a href = \"" . extendURL("genre=" . $genreValue) . "\">" . $genreValue . "</a>, ";
				}
				$genre = substr($genre, 0, -2);
				
				$innerOutputRight .= getCell("HTMLTableMovieGenre",  $value[$this->mTableStructure["length"]] . " min - " . $genre);
				
				$plot = $value[$this->mTableStructure["plot"]];
				$substrAmount = 400;
				if(strlen($plot) > $substrAmount) {
					$plot = substr($plot, 0, strpos($plot, " ", $substrAmount)) . "...";
				}
				
				$innerOutputRight .= getCell("HTMLTableMoviePlot", $plot);
				$innerOutputRight .= getCell("HTMLTableMovieDirector", "<b>Director:</b> " . $value[$this->mTableStructure["director"]]);
				$innerOutputRight .= getCell("HTMLTableMovieDirector", "<b>Stars:</b> " . $value[$this->mTableStructure["actors"]]);
				
				$innerOutputRight = getCell("HTMLTableMovieRight",  $innerOutputRight);
				
				$innerOutput = $innerOutputLeft . $innerOutputRight . getCell("clear", "");
				$output .= getCell("HTMLTableMovie", $innerOutput);
			}
			
			$output .= $this->GetPagination();
			echo getCell("HTMLTableWrapper", getCell("HTMLTableMovieWrapper", $output));
		}
	}
?>