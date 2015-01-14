<?php
	/**
	* Create a gallery
	*/
	class CGallery 
	{
		private $mSupportedImages = array("jpg", "jpeg", "png", "gif");
		
		private $mImageDir;
		private $mImageSubDir;
		private $mPagePath;
		private $mAdditionalPath;
		private $mOutput;
		
		function __construct($pImageDir, $pImageSubDir, $pPagePath, $pAdditionalPath = null)
		{
			$this->mImageDir = $pImageDir;
			$this->mImageSubDir = $pImageSubDir;
			$this->mPagePath = $pPagePath;
			$this->mAdditionalPath = $pAdditionalPath;			
			
			$this->GetOutput();
		}
		
		/**
		* Decide if to scan directory or single image
		*/
		function GetOutput()
		{
			if(isset($this->mAdditionalPath)) 
			{
				if(is_dir($this->mImageDir . $this->mImageSubDir . $this->mAdditionalPath)) {
					$this->ScanDirectory($this->mAdditionalPath . DIRECTORY_SEPARATOR);
				}
				else if(is_file($this->mImageDir . $this->mImageSubDir . $this->mAdditionalPath)) {
					$this->ScanFile($this->mAdditionalPath);
				}
			}
			else {
				$this->ScanDirectory();			
			}
		}
		
		/**
		* Scan a directory
		*/
		function ScanDirectory($pAdditionalPath = "")
		{
			$this->mOutput = "";
			$files = glob($this->mImageDir . $this->mImageSubDir . $pAdditionalPath . "*");
			
			foreach($files as $file) 
			{
				$pathInfo = pathinfo($file);
				
				if(is_dir($file))
				{
					$link = $this->mPagePath . "?path=" . $pAdditionalPath . $pathInfo["filename"];
					$imgSrc = "folder.png"; 
					$caption = $pathInfo["filename"];
				}
				else if(is_file($file) && in_array($pathInfo["extension"], $this->mSupportedImages))
				{
					$link = $this->mPagePath . "?path=" . $pAdditionalPath . $pathInfo["basename"];
					$imgSrc = $this->mImageSubDir . $pAdditionalPath . $pathInfo["basename"];
					$caption = $pathInfo["basename"];
				}
				
				if(isset($imgSrc))
				{
					$this->mOutput .=	"<figure>";
					$this->mOutput .= 		"<a href = \"" . $link . "\">";
					$this->mOutput .=			"<img src = \"" . WORKSPACE_SERVERPATH . "img/?src=" . $imgSrc . "&width=100&height=75&crop-fit\">";
					$this->mOutput .=		"</a>";
					$this->mOutput .=		"<figurecaption>";
					$this->mOutput .=			$caption;
					$this->mOutput .=		"</figurecaption>";
					$this->mOutput .=	"</figure>";				
				}
			}
		}
		
		/**
		* Scan a file
		*/
		function ScanFile($pAdditionalPath)
		{
			$imageUrlPath = WORKSPACE_SERVERPATH . "img/" . $this->mImageSubDir . $pAdditionalPath;
			$imageDirPath = $this->mImageDir . $this->mImageSubDir . $pAdditionalPath;
			$imgInfo = getimagesize($imageDirPath);
		
			$this->mOutput = "	<a href = \"" . $imageUrlPath . "\">
									<img src = \"" . WORKSPACE_SERVERPATH . "img/?src=" . $this->mImageSubDir . "/" . $pAdditionalPath . "&width=900\">
								</a>";
								
			$this->mOutput .= "	<div>
									<p>
										Original dimensions: <b>" . $imgInfo[0] . "x" . $imgInfo[1] . "</b>
									</p>
									<p>
										File size: <b>" . filesize($imageDirPath) . "kb</b>
									</p>
									<p>
										Mimetype: <b>" . $imgInfo["mime"] . "</b>
									</p>
									<p>
										Last modified:  <b>" . gmdate("D, d M Y H:i:s", filemtime($imageDirPath)) . "</b>
									</p>
								</div>";
		}
		
		function Render()
		{
			echo $this->mOutput;
		}
	}
?>