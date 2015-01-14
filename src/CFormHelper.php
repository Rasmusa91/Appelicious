<?php
	class CFormHelper 
	{
		private $mInputs;
		private $mWrapperClass;
				
		function __construct($pInputs, $pWrapperClass = "")
		{
			$this->mInputs = $pInputs;
			$this->mWrapperClass = $pWrapperClass;
		}
		
		function Render()
		{
			$output = "";
			
			foreach($this->mInputs as $input)
			{
				$output .= $input->Render();
			}

			return getCell($this->mWrapperClass, $output);
		}
		
		function Validate()
		{
			$errors = 0;
			
			foreach($this->mInputs as $value)
			{
				$errors += $value->Validate();
			}
			
			return $errors;
		}
	}
?>