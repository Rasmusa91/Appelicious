<?php

if(isset($appelicious["theme"])) {
	$appelicious["stylesheets"][] = getServerPath("appelicious"). "webroot/css/themes/" . $appelicious["theme"] . "/stylesheet.css";
}

include(__DIR__ . "/functions.php");

/**
* Extract config variables
*
*/
extract($appelicious);

/**
 * Render
 *
 */
include(__DIR__ . "/index.tpl.php");
