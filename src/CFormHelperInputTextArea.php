<?php
	class CFormHelperInputTextArea extends CFormHelperInput
	{
		function GetInput()
		{
			return "<textarea name = \"" . $this->mName . "\" class = \"" . $this->mClass . "\">" . $this->mValue . "</textarea>";
		}
	}
?>