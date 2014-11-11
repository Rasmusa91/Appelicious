<?php
	class CDice {
		private $mFaces;
		
		function CDice($pFaces = 6) {
			$this->mFaces = $pFaces;
		}
		
		function roll() {
			return rand(1, $this->mFaces);
		}
	}
?>