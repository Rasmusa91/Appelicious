<?php
	define("FORMHELPER_VALIDATE_NOTEMPTY", 1);
	define("FORMHELPER_VALIDATE_YEAR", 2);
	define("FORMHELPER_VALIDATE_DIGIT", 4);
	define("FORMHELPER_VALIDATE_GREATERTHANZERO", 8);
	define("FORMHELPER_VALIDATE_IMAGE", 16);
	define("FORMHELPER_VALIDATE_URL", 32);
	
	abstract class CFormHelperInput
	{
		protected $mDescription;
		protected $mName;
		protected $mClass;
		protected $mValue;
		protected $mError;
		protected $mValidateFlags;
		
		function __construct($pDesc, $pName, $pValue = "", $pValidateFlags = null, $pClass = "")
		{
			$this->mDescription = $pDesc;
			$this->mName = $pName;
			$this->mClass = $pClass;
			$this->mValue = $pValue;
			$this->mError = "";
			$this->mValidateFlags = $pValidateFlags;
		}
		
		function GetValue()
		{
			return $this->$mValue;
		}
		
		function SetValue($pValue)
		{
			$this->mValue = $pValue;
		}
		
		function SetError($pError)
		{
			$this->mError = "<p class = \"error\">* " . $pError . "</p>";
		}
		
		abstract function GetInput();
		
		function Render()
		{
			$output = "";
			
			if(isset($this->mDescription)) {
				$output .= "<p>" . $this->mDescription . ($this->mValidateFlags & FORMHELPER_VALIDATE_NOTEMPTY ? " *" : "") . "</p>";
			}
			
			$output .= $this->GetInput();
			
			if(isset($this->mError) && !empty($this->mError)) {
				$output .= $this->mError;
			}
			
			return $output;
		}
		
		function Validate()
		{
			$errors = 0;
			
			if($this->mValidateFlags & FORMHELPER_VALIDATE_NOTEMPTY)
			{
				$errors += $this->ValidateNotEmpty();
			}
			if($this->mValidateFlags & FORMHELPER_VALIDATE_YEAR)
			{
				$errors += $this->ValidateYear();
			}
			if($this->mValidateFlags & FORMHELPER_VALIDATE_DIGIT)
			{
				$errors += $this->ValidateDigit();
			}
			if($this->mValidateFlags & FORMHELPER_VALIDATE_GREATERTHANZERO)
			{
				$errors += $this->ValidateGreaterThanZero();
			}
			if($this->mValidateFlags & FORMHELPER_VALIDATE_IMAGE)
			{
				$errors += $this->ValidateImage();
			}
			if($this->mValidateFlags & FORMHELPER_VALIDATE_URL)
			{
				$errors += $this->ValidateURL();
			}
			
			return $errors;
		}
		
		function ValidateNotEmpty()
		{
			$error = 0;
			
			if(empty($this->mValue) && $this->mValue !== '0')
			{
				$error = 1;
				$this->mError .= "<p class = \"error\">* Fältet får inte vara tomt</p>";
			}
			
			return $error;
		}
		
		function ValidateYear()
		{
			$error = 0;
			
			if(!empty($this->mValue) && (!is_numeric($this->mValue) || strlen($this->mValue) != 4))
			{
				$error = 1;
				$this->mError .= "<p class = \"error\">* Årtalet får endast innehålla siffror och måste ha exakt 4 tecken</p>";
			}
			
			return $error;
		}		
		
		function ValidateDigit()
		{
			$error = 0;
			
			if(!empty($this->mValue) && !is_numeric($this->mValue))
			{
				$error = 1;
				$this->mError .= "<p class = \"error\">* Fältet får endast innehålla siffror</p>";
			}
			
			return $error;
		}		
		
		function ValidateGreaterThanZero()
		{
			$error = 0;
			
			if(!empty($this->mValue) && is_numeric($this->mValue) && $this->mValue <= 0)
			{
				$error = 1;
				$this->mError .= "<p class = \"error\">* Siffran måste vara större än 0</p>";
			}
			
			return $error;
		}
		
		function ValidateImage()
		{
			$error = 0;
			
			if(!empty($this->mValue) && !in_array(ValidateValue(pathinfo($this->mValue, PATHINFO_EXTENSION), ""), array("jpeg", "jpg", "gif", "png")))
			{
				$error = 1;
				$this->mError .= "<p class = \"error\">* Bilden måste vara av typen jpg, gif eller png</p>";
			}
			
			return $error;
		}		
		
		function ValidateURL()
		{
			$error = 0;
			$headers = @get_headers($this->mValue);

			if(!empty($this->mValue) && ($headers[0] == null || $headers[0] == "HTTP/1.1 404 Not Found"))
			{
				$error = 1;
				$this->mError .= "<p class = \"error\">* URLn finns inte</p>";
			}
			
			return $error;
		}	
	}
