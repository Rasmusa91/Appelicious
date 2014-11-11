<?php

class CContent 
{
	public $mName;
	public $mContent;
	public $mInclude;
	public $mChildren;
	
	function CContent($pName, $pContent = "", $pInclude = false, $pChildren = array()) 
	{
		$this->mName = $pName;
		$this->mContent = $pContent;
		$this->mInclude = $pInclude;
		$this->mChildren = $pChildren;
	}
	
	function AddChildren($pChild)
	{
		$this->mChildren[$pChild->mName] = $pChild;
	}
	
	function render($pAppelicious) 
	{
		extract($pAppelicious);
	
		echo "<div id = \"" . $this->mName . "\">";
		
		foreach($this->mChildren as $child) {
			echo $child->render($pAppelicious);	
		}
		
		if($this->mInclude) {
			include($this->mContent);	
		}
		else {
			echo $this->mContent;
		}
		
		echo "</div>";
	}
}