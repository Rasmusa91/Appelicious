<?php
	function getTitle($pAppelicious) {
		return $pAppelicious["sitemap"][$pAppelicious["selectedPage"]]["name"] . (isset($pAppelicious["titleExtension"]) ? $pAppelicious["titleExtension"] : "");
	}