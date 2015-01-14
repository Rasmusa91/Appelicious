<?php
	class CFormHelperInputPassword extends CFormHelperInput
	{
		function GetInput()
		{
			return "<input type = \"password\" value = \"" . $this->mValue . "\" name = \"" . $this->mName . "\" class = \"" . $this->mClass . "\">";
		}		
	}
?>