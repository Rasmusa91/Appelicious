<?php
	/**
	* Error handling
	*
	*/
	error_reporting(-1);
	ini_set("display_error", 1);
	ini_set("output_buffering", 0);

	/**
	* Paths
	*
	*/
	define("APPELICIOUS_INSTALL_PATH", __DIR__ . "/..");
	define("APPELICIOUS_RENDER_PATH", APPELICIOUS_INSTALL_PATH . "/theme/render.php");
	define("APPELICIOUS_WEBROOT_PATH", APPELICIOUS_INSTALL_PATH . "/webroot");
		
	/**
	* Functions
	*
	*/
	include(APPELICIOUS_INSTALL_PATH . "/src/bootstrap.php");
	
	/**
	* Serverpath
	*
	*/
	define("APPELICIOUS_SERVERPATH", getServerPath("appelicious"));
	
	/**
	* Session
	*
	*/
	session_name(preg_replace("/[^a-z\d]/i", "", __DIR__));
	session_start();
	
	$appelicious = array();
	
	/**
	* Theme
	*
	*/
	$appelicious["theme"] = "default";
	
	/**
	* Pagehandler
	* 
	*/
	$appelicious["defaultPage"] =  "hello";
	$appelicious["currentPage"] = (isset($_GET["p"]) ? $_GET["p"] : null);
	$appelicious["currentSubPage"] = (isset($_GET["subp"]) ? $_GET["subp"] : null);
	$appelicious["currentSubSubPage"] = (isset($_GET["subsubp"]) ? $_GET["subsubp"] : null);
	$appelicious["selectedPage"] = (isset($appelicious["currentPage"]) ? $appelicious["currentPage"] : $appelicious["defaultPage"]);	
	
	/**
	* Sitemap
	*
	*/
	$appelicious["sitemap"] = array(
		"hello" => array("name" => "Hello", "url" => APPELICIOUS_SERVERPATH . "hello/", "cat" => "default"),
		"dice" => array("name" => "Dice", "url" => APPELICIOUS_SERVERPATH . "dice/", "cat" => "default"),
		"source" => array("name" => "Source", "url" => APPELICIOUS_SERVERPATH . "source/", "cat" => "default")
	);
	
	/**
	* Head
	*
	*/
	$appelicious["lang"] = "sv";
	$appelicious["charset"] = "utf-8";
	$appelicious["titleExtension"] = " - Appelicious";
	$appelicious["favicon"] = APPELICIOUS_SERVERPATH . "webroot/img/favicon.jpg";
	
	/**
	* Stylesheets
	*
	*/		
	$appelicious["stylesheets"] = array();
	$appelicious["stylesheets"][] = APPELICIOUS_SERVERPATH . "webroot/css/stylesheet.css";
	
	if($appelicious["currentPage"] == "source") {
		$appelicious["stylesheets"][] = APPELICIOUS_SERVERPATH. "webroot/css/source.css";
	}
	
	/**
	* Javascript
	*
	*/
	$appelicious["modernizr"] = "../appelicious/webroot/js/modernizr.js";
	$appelicious["jquery"] = "//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js";
	$appelicious["javascripts"] = array();

	/**
	* Content
	*
	*/
	$appelicious["content"] = array();
	$appelicious["content"]["header"] = new CContent("header", APPELICIOUS_WEBROOT_PATH . "/pages/header.php", true);
	$appelicious["content"]["wrapper"] = new CContent("wrapper");
	$appelicious["content"]["wrapper"]->addChildren(new CContent("main",  APPELICIOUS_WEBROOT_PATH . "/pages/" . $appelicious["selectedPage"] . ".php", true));
	$appelicious["content"]["wrapper"]->addChildren(new CContent("footer", APPELICIOUS_WEBROOT_PATH . "/pages/footer.php", true));
	
	/**
	* Google
	*
	*/
	$appelicious["googleAnalyticsID"] = null;	
?>