<?php
	function getTitle($pAppelicious) {
		$title = isset($pAppelicious["sitemap"][$pAppelicious["selectedPage"]]["name"]) ? $pAppelicious["sitemap"][$pAppelicious["selectedPage"]]["name"] : ucfirst($pAppelicious["currentPage"]);
		$title .= (isset($pAppelicious["titleExtension"]) ? $pAppelicious["titleExtension"] : "");
		
		return $title;
	}