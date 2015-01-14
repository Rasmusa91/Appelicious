<?php
	class CFormHelperInputFile extends CFormHelperInput
	{
		function GetInput()
		{
			return "<input type = \"file\" value = \"" . $this->mValue . "\" name = \"" . $this->mName . "\" class = \"" . $this->mClass . "\" accept = \"image/*\"></input>";
		}
	}
?>