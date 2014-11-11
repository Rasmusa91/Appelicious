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
  echo "Anax: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
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
	
	foreach($sitemap as $key => $value) 
	{
		$class = (isset($currPage) && isset($currClass) && $currPage == $key) ? "class = \"" . $currClass . "\"" : ""; 
		
		$output .= "<a $class href = \"" . $value["url"] . "\">" . $value["name"] . "</a>";
	}
	
	return $output;
}

function getServerPath($pDirectory)
{
	$url = "http";
	$url .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
	$url .= "://";
	$serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : (($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
	$url .= $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
	$url = str_replace(basename($url), "", $url);
	$url = str_replace(basename($url) . "/", "", $url);
	$url .= $pDirectory . "/";
	
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