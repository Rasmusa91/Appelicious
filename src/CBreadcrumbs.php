<?php

class CBreadcrumbs
{
	private $mCrumbs;
	private $mHomeImage;
	
	function __construct($pCrumbs, $pHomeImage = null)
	{
		$this->mCrumbs = $pCrumbs;
		$this->mHomeImage = $pHomeImage;
	}
	
	function Render()
	{
		if(!isset($this->mHomeImage)) {
			$output = "<a href = \"" . WORKSPACE_SERVERPATH . "\"><img src = \"" . APPELICIOUS_SERVERPATH . "webroot/img/breadcrumb.png\" alt = \"breadcrumbs\"></a>";
		}
		else {
			$output = $this->mHomeImage;
		}
		
		for($i = 0; $i < count($this->mCrumbs); $i++)
		{
			$output .= "<p><a " . (isset($this->mCrumbs[$i]["current"]) ? "class = \"current\"" : "") . " href = \"" . $this->mCrumbs[$i]["url"] . "\">" . $this->mCrumbs[$i]["name"] . "</a></p>";
			
			if($i < count($this->mCrumbs) - 1) {
				$output .= " / ";
			}
		}

		return $output;
	}
}