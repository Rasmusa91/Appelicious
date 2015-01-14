<?php
	class CHTMLTable 
	{
		protected $mData;
		protected $mServerPath;
		protected $mTableData;
		protected $mMaxRows;
		private $mLimit;
		private $mPagination;
		protected $mImageArrows;
		
		function __construct($pData, $pMaxRows = null, $pLimit = null, $pPagination = null, $pServerPath = "")
		{
			$this->mData = $pData;
			$this->mMaxRows = (isset($pMaxRows) ? $pMaxRows : count($this->mData));
			$this->mLimit = (isset($pLimit) ? $pLimit : 8);
			$this->mPagination = (isset($pPagination) ? $pPagination : 1);
			$this->mServerPath = $pServerPath;
		
			$this->mImageArrows = false;
		
			$this->GetTableData();
		}
		
		function SetUseImageArrows($pState)
		{
			$this->mImageArrows = $pState;
		}
		
		function Render()
		{
			$content = $this->GetShowAmount();
			$content = $this->GetTableHeader();
			$content .= $this->GetTableContents();
			
			echo getCell("HTMLTableWrapper", $this->GetShowAmount() . getCell("HTMLTableTable", $content) . $this->GetPagination());
		}		
		
		function GetShowAmount()
		{
			return getCell("HTMLTableShowAmount", $this->mMaxRows . " träffar. Träffar per sida: <a href = \"" . extendURL("limit=2") . "\">2</a> <a href = \"" . extendURL("limit=4") . "\">4</a> <a href = \"" . extendURL("limit=8") . "\">8</a>");
		}
		
		function GetPagination()
		{
			$innerPagination = "";
			$maxPagination = ceil($this->mMaxRows / $this->mLimit);
			
			for($i = 1; $i <= $maxPagination; $i++) 
			{
				if($i != $this->mPagination)
				{
					$innerPagination .= "<a href = \"" . extendURL("pagination=" . $i . "") . "\">" . ceil($i) . "</a> ";
				}
				else
				{
					$innerPagination .= "<b>$i</b> ";
				}
			}
			
			return getCell("HTMLTablePagination",	getCell("arrow", "<a href = \"" . extendURL("pagination=1") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_double_left.png" . "\">" : "<<") . "</a>") .
													getCell("arrow", "<a href = \"" . extendURL("pagination=" . ($this->mPagination - 1 >= 1 ? $this->mPagination - 1 : 1) . "") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_single_left.png" . "\">" : "<") . "</a>") .
													getCell("innerPagination", $innerPagination) .
													getCell("arrow", "<a href = \"" . extendURL("pagination=" . ($this->mPagination + 1 < $maxPagination ? $this->mPagination + 1 : $maxPagination) . "") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_single.png" . "\">" : ">") . "</a>") . 
													getCell("arrow", "<a href = \"" . extendURL("pagination=" . $maxPagination . "") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_double.png" . "\">" : ">>") . "</a>"));
		}
		
		
		
		function GetTableData()
		{
			$this->mTableData = array();
			
			foreach($this->mData as $data)
			{
				foreach($data as $key => $value)
				{
					if(isset($value) && !empty($value))
					{
						if(!isset($this->mTableData[$key])) {
							$this->mTableData[$key] = array();
						}
						
						array_push($this->mTableData[$key], $value);
					}
				}
			}
		}
		
		function GetTableHeader()
		{
			$header = "";
			
			foreach($this->mTableData as $key => $value) {
				$header .= getCell("HTMLTableCol HTMLTableHead", "<p>" . $key . "</p>" . " <a href = \"" . extendURL("orderBy=" . strtolower($key) . "&orderType=asc") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_single_down.png" . "\">" : "\/") . "</a>  <a href = \"" . extendURL("orderBy=" . strtolower($key) . "&orderType=desc") . "\">" . ($this->mImageArrows ? "<img class = \"arrow\" src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/arrow_single_up.png" . "\">" : "/\\") . "</a>");
			}
			
			return getCell("HTMLTableRow", $header);
		}
		
		function GetTableContentsStructured()
		{
			$data = array();
			foreach($this->mTableData as $key => $value)
			{
				for($i = 0; $i < count($value); $i++)
				{
					if(!isset($data[$i])) {
						$data[$i] = array();
					}

					$data[$i][$key] = $value[$i];
				}				
			}			
			
			return $data;
		}
		
		function GetTableContents()
		{
			$data = $this->GetTableContentsStructured();
			$content = "";
			
			foreach($data as $value)
			{
				$row = "";
				
				foreach($value as $value2) 
				{
					$value2 = $this->CheckImage($value2);
					$row .= getCell("HTMLTableCol", $value2);	
				}
				
				$content .= getCell("HTMLTableRow", $row);
			}
			
			return $content;
		}
				
		function CheckImage($value)
		{
			$output = "";
			$words = explode(' ', $value);
			$images = array("jpg", "jpeg", "png", "gif");
			
			foreach($words as $word) 
			{			
				if(in_array(pathinfo($word, PATHINFO_EXTENSION), $images)) {
					//$output .= "<a href = \"" . $this->mServerPath . $value . "\"><img src = \"" . $this->mServerPath.$value . "\" title = \"" . basename($value) . "\"></a> ";			
					$output .= "<a href = \"" . $this->mServerPath . $value . "\"><img src = \"" . WORKSPACE_SERVERPATH . "img/?src=" . str_replace("img/", "", $value) . "&width=100&height=75&crop-fit\"></a> ";			
				}
				else {
					$output .= $word . " ";
				}
			}
			return $output;
		}	
	}
?>