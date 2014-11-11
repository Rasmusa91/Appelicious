<?php
	function getTitle($pAppelicious) {
		return $pAppelicious["sitemap"][$pAppelicious["selectedPage"]]->mName;
	}