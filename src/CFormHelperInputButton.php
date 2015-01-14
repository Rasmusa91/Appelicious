<?php
	class CFormHelperInputButton extends CFormHelperInput
	{
		function GetInput()
		{
			return getCell("formInputButton", "<input type = \"submit\" value = \"" . $this->mValue . "\" name = \"" . $this->mName . "\" class = \"" . $this->mClass . "\">");
		}
	}
?>