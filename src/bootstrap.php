<?php
/**
 * Bootstrapping functions, essential and needed for Anax to work together with some common helpers. 
 *
 */

/**
 * Default exception handler.
 *
 */
function myExceptionHandler($exception) {
  echo "Appelicious: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
}
set_exception_handler('myExceptionHandler');


/**
 * Autoloader for classes.
 *
 */
function myAutoloader($class) {
  $path = APPELICIOUS_INSTALL_PATH . "/src/{$class}.php";
  if(is_file($path)) {
    include($path);
  }
  else {
    throw new Exception("Classfile '{$class}' does not exists.");
  }
}
spl_autoload_register('myAutoloader');

/**
* Debug an array
*
*/
function dump($array) {
  echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

/**
* Get all items in a sitemap by category
*
*/
function getSitemapByCat($sitemap, $cat) 
{
	$sitemapByCat = array();
	
	foreach($sitemap as $key => $value)
	{
		if($value["cat"] == $cat) {
			$sitemapByCat[$key] = $value;
		}
	}

	return $sitemapByCat;
}

/**
* Convert a sitemap to links
*
*/
function getLinks($sitemap, $currClass = null, $currPage = null)
{
	$output = "";
	
	$i = 0;
	foreach($sitemap as $key => $value) 
	{
		$class = isset($value["class"]) ? $value["class"] : "";
		$class .= (isset($currPage) && isset($currClass) && $currPage == $key ? " " . $currClass : "");
		
		if($i == 0) {
			$class .= " first";
		}
		
		if($i == count($sitemap) - 1) {
			$class .= " last";
		}

		$class = "class = \"" . $class . "\"";
		
		$output .= "<a $class href = \"" . $value["url"] . "\">" . $value["name"] . "</a>";
	
		$i++;
	}
	
	return $output;
}

/**
* Convert a sitemap to links with sublinks
*
*/
function getLinksSub($sitemap, $currClass = null, $currPage = null)
{
	$output = "<ul>";
	
	foreach($sitemap as $key => $value) 
	{	
			
		$class = (isset($currPage) && isset($currClass) && $currPage == $key) ? "class = \"" . $currClass . (isset($value["class"]) ? " " . $value["class"] : "") . "\"" : ""; 
		
		$output .= "<li><a $class href = \"" . $value["url"] . "\">" . $value["name"] . "</a>";
		
		if(isset($value["suburls"]) && count($value["suburls"]) > 0) 
		{
			$output .= "<ul>";
			
			foreach($value["suburls"] as $subKey => $subValue)
			{
				$output .= "<li><a $class href = \"" . $subValue["url"] . "\">" . $subValue["name"] . "</a></li>";
			}
			
			$output .= "</ul>";
		}
		
		$output .= "</li>";
	}
	
	$output .= "</ul>";
	
	return $output;
}

function getServerPath($pDirectory = null)
{
	$url = "http";
	$url .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
	$url .= "://";
	$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : (($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
	$url .= $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
	$url = str_replace(basename($url), "", $url);
	$url = str_replace(basename($url) . "/", "", $url);
	
	if(isset($pDirectory)) {
		$url .= $pDirectory . "/";
	}
	
	return $url;
}

// ===========================================================================================
//
// Author: Mikael Roos, mos@bth.se
//
// Change history:
// 
// 2011-02-04: 
// First try. Used as example code in htmlphp-kmom03.
//
// -------------------------------------------------------------------------------------------
//
// Get current url
//
function getCurrentUrl() {
  $url = "http";
  $url .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
  $url .= "://";
  $serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
    (($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
  $url .= $_SERVER["SERVER_NAME"] . $serverPort . htmlspecialchars($_SERVER["REQUEST_URI"]);
  return $url;
}


// -------------------------------------------------------------------------------------------
//
// Destroy a session
//
function destroySession() {
  // Unset all of the session variables.
  $_SESSION = array();
  
  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }
  
  // Finally, destroy the session.
  session_destroy();
}

function getURLQuery()
{
	$queries = explode("?", $_SERVER["REQUEST_URI"]);
	
	if(count($queries) > 1) {
		$queries = $queries[1];
	}
	else {
		$queries = "";
	}
	
	return $queries;
}

function makeURLQuery($pQueryArray)
{
	$query = "";
	
	foreach($pQueryArray as $key => $value) 
	{
		if(!empty($value)) {
			$query .= $key . "=" . $value . "&";
		}
	}
	
	$query = substr($query, 0, -1);

	return $query;
}

function extendURL($pExtension, $pQueries = null)
{
	if(!isset($pQueries)) {
		$queries = getURLQuery();
	}
	else {
		$queries = $pQueries;
	}
	
	if(!empty($queries)) {
		$queries = explode("&", $queries);
	}
	else {
		$queries = array();
	}

	if(isset($pExtension))
	{
		$pExtension = explode("&", $pExtension);
		if(is_array($pExtension)) {
			$queries = array_merge($queries, $pExtension);
		}
		else {
			array_push($queries, $pExtension);
		}
	}

	$tempQueries = array();
	foreach($queries as $q) 
	{
		$explodedQ = explode("=", $q);
		
		$tempQueries[$explodedQ[0]] = $explodedQ[1];
	}
	
	$queries = $tempQueries;

	$query = "?";
	
	foreach($queries as $key => $value) {
		$query .= $key . "=" . $value . "&";
	}
	
	$query = substr($query, 0, -1);
		
	return $query;
}

		
/**
* This class will easily make surround some content around a div with one or more class names
*
*/
function GetCell($pClass, $pContent = "")
{
	$output = "<div class = \"" . $pClass . "\">" . $pContent . "</div>";
	
	return $output;
}	

function slugify($str) 
{
	$str = str_replace(array('å','ä','ö', 'Å', 'Ä', 'Ö'), array('a','a','o', 'A', 'A', 'O'), $str);
	$str = mb_strtolower(trim($str));
	$str = preg_replace('/[^a-z0-9-]/', '-', $str);
	$str = trim(preg_replace('/-+/', '-', $str), '-');
	
	return $str;
}

function ValidateDate($pDate)
{
	$validDate = true;
	
	$explDate = explode(' ', $pDate);
	
	if(count($explDate) != 2) {
		$validDate = false;
	}
	
	if($validDate)
	{
		$ddmmyy = explode('-', $explDate[0]);
		$validDate = ((count($ddmmyy) == 3) && checkdate($ddmmyy[1], $ddmmyy[2], $ddmmyy[0]));
	}
	
	if($validDate)
	{
		$validDate = preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]/i", $explDate[1]);
	}	
	
	return $validDate;
}

function ValidateValue($pValue, $pDefault = null)
{
	return (isset($pValue) ? $pValue : $pDefault);
}

function ValidatePost($pName, $pDefault = null)
{
	return (isset($_POST[$pName]) ? $_POST[$pName] : $pDefault);
}

function ValidateGet($pName, $pDefault = null)
{
	return (isset($_GET[$pName]) ? $_GET[$pName] : $pDefault);
}

function ValidateFile($pName, $pDefault = null)
{
	return (isset($_FILES[$pName]["name"]) && !empty($_FILES[$pName]["name"]) ? $_FILES[$pName] : $pDefault);
}

function ValidateURL($pURL)
{
	$headers = @get_headers($pURL);
	return !($headers[0] == null || $headers[0] == "HTTP/1.1 404 Not Found");
}

function ValidateArrayKey($pKey, $pArray, $pDefault = null, $pPredecessor = "", $pSuccessor = "")
{
	return (array_key_exists($pKey, $pArray) ? $pPredecessor . $pArray[$pKey] . $pAdditional : $pDefault);
}