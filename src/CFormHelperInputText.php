<?php
	class CFormHelperInputText extends CFormHelperInput
	{
		function GetInput()
		{
			return "<input value = \"" . $this->mValue . "\" name = \"" . $this->mName . "\" class = \"" . $this->mClass . "\"></input>";
		}		
	}
?>