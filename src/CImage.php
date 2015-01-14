<?php

define("DEFAULT_TYPE", "jpeg");
define("DEFAULT_QUALITY", 60);
define("MAX_IMG_SIZE", 2000);

class CImage
{
	private $mSupportedTypes = array("jpeg", "jpg", "png", "gif");
	private $mSupportedFilters = array("grayscale", "sepia");
	
	private $mSrc;
	private $mType;
	private $mQuality;
	private $mSharpen;
	private $mFilter;
	private $mVerbose;
	
	private $mImgPath;
	private $mImgPathInfo;
	private $mImgInfo;
	private $mImage;
	
	private $mCachePath;
	private $mCacheImgPath;
	private $mCacheImgInfo;
	private $mCacheIgnore;
	
	private $mImgWidth;
	private $mImgHeight;
	private $mCropFit;
	private $mCropWidth;
	private $mCropHeight;
	
	function __construct($pDir, $pSrc, $pType = null, $pWidth = null, $pHeight = null, $pCropFit = false, $pQuality = DEFAULT_QUALITY, $pSharpen = false, $pFilter = null, $pCacheIgnore = false, $pVerbose = false)
	{
		$this->mSrc = $pSrc;
		$this->mType = $pType;
		$this->mImgWidth = $pWidth;
		$this->mImgHeight = $pHeight;
		$this->mCropFit = $pCropFit;
		$this->mQuality = $pQuality;
		$this->mSharpen = $pSharpen;
		$this->mFilter = $pFilter;
		$this->mCacheIgnore = $pCacheIgnore;
		$this->mVerbose = $pVerbose;
		
		$this->mImgPath = ($pDir . $pSrc);		
		$this->mImgPathInfo = pathinfo($this->mImgPath);
		$this->mImgInfo = getimagesize($this->mImgPath);		
		
		$this->mCachePath = $pDir . "cache" . DIRECTORY_SEPARATOR;
		
		$this->Validate();
		$this->CalculateSize();
		$this->GetCacheImgPath();
		
		if(!$this->IsCacheUpToDate())
		{
			$this->Verbose("Cache not up to date");
					
			$this->OpenImage();
			$this->Resize();
			
			if($this->mSharpen) {
				$this->SharpenImage();
			}
			
			$this->AddFilters();
			
			$this->SaveImage();
		} 
		else {
			$this->Verbose("Cache not up to date");
		}
	}
	
	function Verbose($pVerbose)
	{
		if($this->mVerbose) {
			echo "<p>" . $pVerbose . "</p>";
		}
	}
	
	/**
	* Validate some parameters, if invalid: set default
	*/
	function Validate()
	{
		$this->Verbose("Started validating");
		
		/* Check the type */
		if(!isset($this->mType) || !in_array($this->mType, $this->mSupportedTypes)) 
		{
			if(in_array($this->mImgPathInfo["extension"], $this->mSupportedTypes)) {
				$this->mType = $this->mImgPathInfo["extension"];
			}
			else {
				$this->mType = DEFAULT_TYPE;
			}
			
			$this->Verbose("Image type not supported, using " . $this->mType . " instead");
		}
		
		/* Check the quality */
		if(!isset($this->mQuality) || $this->mQuality < 0 || $this->mQuality > 100) 
		{
			$this->mQuality = DEFAULT_QUALITY;
			$this->Verbose("Quality type not supported, using " . $this->mQuality . " instead");	
		}
		
		/* Check the filter */
		if(isset($this->mFilter) && !in_array($this->mFilter, $this->mSupportedFilters)) 
		{
			$this->mFilter = null;
			$this->Verbose("Filter type not supported, using none");
		}
		
		
		/* Clamp the width, if invalid set default from original img*/
		if(isset($this->mImgWidth) && is_numeric($this->mImgWidth)) 
		{
			if($this->mImgWidth > MAX_IMG_SIZE) {
				$this->mImgWidth = MAX_IMG_SIZE;
			}
			if($this->mImgWidth < 0) {
				$this->mImgWidth = 0;
			}
		}
		else 
		{
			$this->mImgWidth = null;
			$this->Verbose("Invalid image width, using original width");
		}
		
		/* Clamp the height, if invalid set default from original img*/
		if(isset($this->mImgHeight) && is_numeric($this->mImgHeight)) 
		{
			if($this->mImgHeight > MAX_IMG_SIZE) {
				$this->mImgHeight = MAX_IMG_SIZE;
			}
			if($this->mImgHeight < 0) {
				$this->mImgHeight = 0;
			}			
		}
		else 
		{
			$this->mImgHeight = null;
			$this->Verbose("Invalid image height, using original height");
		}
	}
	
	/**
	* Calculate the new size of the image
	*/
	function CalculateSize()
	{
		$this->Verbose("Started calculate resize");

		$orgWidth = $this->mImgInfo[0];
		$orgHeight = $this->mImgInfo[1];
		
		/* Focus width */ 
		if(isset($this->mImgWidth) && !isset($this->mImgHeight)) 
		{
			$this->mImgHeight = round(($orgHeight / $orgWidth) * $this->mImgWidth);
			$this->Verbose("Aspect ratio focusing on width");
		}
		/* Focus height */ 
		else if(!isset($this->mImgWidth) && isset($this->mImgHeight)) 
		{
			$this->mImgWidth = round(($orgWidth / $orgHeight) * $this->mImgHeight);
			$this->Verbose("Aspect ratio focusing on height");
		}
		/* Focus both */
		else if(isset($this->mImgWidth) && isset($this->mImgHeight)) 
		{
			if($this->mCropFit) 
			{	
				$this->mCropWidth = $this->mImgWidth;
				$this->mCropHeight = $this->mImgHeight;
				$this->Verbose("Crop to fit enabled");
				
				$rw = $this->mImgWidth / $orgWidth;
				$rh = $this->mImgHeight / $orgHeight;
				$ratio = $rw > $rh ? $rw : $rh;
				
				
				//$ratio = ($this->mImgWidth > $this->mImgHeight) ? $this->mImgWidth / $orgWidth : $this->mImgHeight / $orgHeight;
			}			
			else {
				$ratio = ($this->mImgWidth > $this->mImgHeight) ? $this->mImgWidth / $orgWidth : $this->mImgHeight / $orgHeight;
			}
				
			$this->mImgWidth = round($ratio * $orgWidth);
			$this->mImgHeight = round($ratio * $orgHeight);
				
			$this->Verbose("Aspect ratio focusing on both width and height");
		}	
		/* Focus none */
		else 
		{	
			$this->mImgWidth = $orgWidth;
			$this->mImgHeight = $orgHeight;		
			
			$this->Verbose("No aspect ratio");
		}	
	}		
	
	/**
	* Create the path for the cache image
	*/
	function GetCacheImgPath()
	{
		$this->Verbose("Started creating cache path");

		//base
		$path = $this->mCachePath . dirname($this->mSrc) . DIRECTORY_SEPARATOR . $this->mImgPathInfo["filename"];
		$path .= "__h_" . $this->mImgHeight;
		$path .= "__w_" . $this->mImgWidth;
		$path .= "__q_" . $this->mQuality;
		
		if($this->mCropFit) {
			$path .= "__c_fit";		
		}
		
		if($this->mSharpen) {
			$path .= "__s_true";		
		}

		if($this->mSharpen) {
			$path .= "__f_" . $this->mFilter;		
		}

		$path .= "." . $this->mType;	
		
		$this->mCacheImgPath = $path;
		
		$this->Verbose("Created cache path: " . $this->mCacheImgPath);
	}
	
	/**
	* Check if the cached image is up to date
	*/
	function IsCacheUpToDate()
	{
		$this->Verbose("Started checking if cache is up to date");

		$originalImgModified = filemtime($this->mImgPath);
		$cacheImgModified = file_exists($this->mCacheImgPath) ? filemtime($this->mCacheImgPath) : null;
		
		return (!$this->mCacheIgnore && file_exists($this->mCacheImgPath) && $originalImgModified < $cacheImgModified);
	}
	
	/**
	* Create an image object from the original image
	*/
	function OpenImage()
	{	
		$this->Verbose("Started opening image");

		switch($this->mImgPathInfo["extension"])
		{
			case "jpg":
			case "jpeg":
				$this->mImage = imagecreatefromjpeg($this->mImgPath);
				$this->Verbose("Opening image as jpeg");
			break;
			
			case "png":
				$this->mImage = imagecreatefrompng($this->mImgPath);
				$this->Verbose("Opening image as png");
			break;
			
			case "gif":
				$this->mImage = imagecreatefromgif($this->mImgPath);
				$this->Verbose("Opening image as gif");
			break;
		}
	}
	
	/**
	* Resize the image
	*/
	function Resize()
	{
		$this->Verbose("Started resizing image");

		$orgWidth = $this->mImgInfo[0];
		$orgHeight = $this->mImgInfo[1];

		if($this->mImgWidth != $orgWidth || $this->mImgHeight != $orgHeight) 
		{
			if($this->mCropFit) 
			{
				$widthDiff = $this->mImgWidth - $this->mCropWidth;
				$heightDiff = $this->mImgHeight - $this->mCropHeight;

				$this->Verbose("Calculating crop size");
			}
			else 
			{
				$widthDiff = 0;
				$heightDiff = 0;
			}
						
			$newImage = imagecreatetruecolor($this->mImgWidth - $widthDiff, $this->mImgHeight - $heightDiff);
			//$newImage = imagecreatetruecolor($this->mImgWidth, $this->mImgHeight);
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);  
			imagecopyresampled($newImage, $this->mImage, 0, 0, $widthDiff / 2, $heightDiff / 2, $this->mImgWidth, $this->mImgHeight, $orgWidth, $orgHeight);
			//imagecopyresampled($newImage, $this->mImage, 0, 0, 0, 0, $this->mImgWidth, $this->mImgHeight, $orgWidth, $orgHeight);
			
			if($this->mType == "gif")
			{
				$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
				imagefill($newImage, 0, 0, $transparent);			
				
				$this->Verbose("Creating transparency for gifs");
			}
			
			$this->mImage = $newImage;
		}
	}
	
	/**
	* Sharpen the image
	*/
	function SharpenImage()
	{
		$this->Verbose("Sharpening image");

		$sharpMatrix = array(
			array(-1, -1, -1),
			array(-1, 16, -1),
			array(-1, -1, -1)
		);
		
		$divisor = 8;
		$offset = 0;
		
		imageconvolution($this->mImage, $sharpMatrix, $divisor, $offset);
	}
	
	function AddFilters()
	{
		$this->Verbose("Started adding filters");

		switch($this->mFilter)
		{
			case "grayscale":
				imagefilter($this->mImage, IMG_FILTER_GRAYSCALE);
				
				$this->Verbose("Adding filter grayscale");
			break;
			
			case "sepia":
				imagefilter($this->mImage, IMG_FILTER_GRAYSCALE);
				imagefilter($this->mImage, IMG_FILTER_BRIGHTNESS, 40);
				imagefilter($this->mImage, IMG_FILTER_CONTRAST, -20);
				imagefilter($this->mImage, IMG_FILTER_COLORIZE, 120, 60, 0);

				$this->Verbose("Adding filter sepia");
			break;
		}
	}
	
	/**
	* Make sure that a directory exists, if not create it with its subfolders (default permission)
	*/
	function CheckDir($pDir)
	{
		$this->Verbose("Started checking if the directory exists");

		if(!file_exists($pDir)) 
		{
			mkdir($pDir, 0777, true);
			
			$this->Verbose("Directory did not exist, creating directory and subdirectories up to " . $pDir);
		}
	}
	
	/**
	* Save the image to the cache
	*/
	function SaveImage()
	{
		$this->Verbose("Started saving image");
		$this->CheckDir(dirname($this->mCacheImgPath));
		
		imagealphablending($this->mImage, false);
		imagesavealpha($this->mImage, true);  

		switch($this->mType)
		{
			case "jpg":
			case "jpeg":
				imagejpeg($this->mImage, $this->mCacheImgPath, $this->mQuality);
				$this->Verbose("Saving image as jpeg");
			break;
			
			case "png":
				imagepng($this->mImage, $this->mCacheImgPath);
				$this->Verbose("Saving image as png");
			break;
			
			case "gif":
				imagegif($this->mImage, $this->mCacheImgPath);
				$this->Verbose("Saving image as gif");
			break;
		}
		
		$this->mCacheImgInfo = getimagesize($this->mCacheImgPath);
	}
	
	/**
	* Render the image
	*/
	function Render()
	{
		$this->Verbose("Started rendering image");
		
		$cacheImgModified = filemtime($this->mCacheImgPath); 

		if(!$this->mVerbose) {
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $cacheImgModified) . " GMT");
		}
		
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $cacheImgModified) 
		{
			if(!$this->mVerbose) {
				header("HTTP/1.0 304 Not Modified");
			}
			
			$this->Verbose("Using header 304 Not Modified");
		}
		else 
		{  
			if(!$this->mVerbose) {
				header("Content-type: " . $this->mCacheImgInfo["mime"]);
				readfile($this->mCacheImgPath);
			}
			
			$this->Verbose("Everything is successfull, image have been rendered");
		}
	}
}