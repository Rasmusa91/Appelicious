<?php

class CContentViewer
{
	private $mDatabaseInformation;
	private $mPosts;
	private $mUser;
	private $mBaseURL;
	
	function __construct($pDBInfo, $pPosts, $pBaseURL = null, $pUser = null)
	{
		$this->mDatabaseInformation = $pDBInfo;
		$this->mPosts = $pPosts;
		$this->mBaseURL = $pBaseURL;
		$this->mUser = $pUser;
	}

	function SetBaseURL($pBaseURL)
	{
		$this->mBaseURL = $pBaseURL;	
	}
	
	function GetPosts()
	{
		return $this->mPosts;
	}
	
	function FindPost($pSlug)
	{
		$post = null;
		foreach($this->mPosts as $value)
		{			
			if($value->mSlug == $pSlug) {
				$post = $value;
			}
		}

		return $post;
	}
	
	function FindPostByID($pID)
	{
		$post = null;
		foreach($this->mPosts as $value)
		{
			if($value->mID == $pID) {
				$post = $value;
			}
		}

		return $post;
	}

	function PostExists($pSlug)
	{
		$p = $this->FindPost($pSlug);
		return isset($p);
	}
	
	function ShowAll()
	{		
		$output = "";
		$admin = "";
		$title = "Nyheter";
		
		if(isset($this->mUser) && $this->mUser->HasAccess())
		{
			$admin .= getCell("admin right", "<p><a href = \"" . $this->mBaseURL . "add/\">Lägg till</a></p>");
		}
		
		for($i = 0; $i < count($this->mPosts); $i++)
		{	
			$output .= getCell("contentPreview " . ($i % 2 == 0 ? "gray" : "gray2"), $this->RenderPreview($this->mPosts[$i]));
		}
		
		return getCell("contentViewerOverview", getCell("titleOverview", "Nyheter") . $admin . getCell("clear", "") . $output);
	}
		
	function RenderPreview($pPost)
	{		
		$head = "<p class = \"noSpaces\">Publicerad vid <b>" . $pPost->mDate . "</b></p>";
		$head .= "<p class = \"noSpaces\">Författare <b>" . ucfirst(strtolower($pPost->mAuthorName)) . "</b></p>";
		
		if(isset($pPost->mCategories)) {
			$head .= "<p class = \"noSpaces\">Kategorier <b>" . $pPost->GetCategories($this->mBaseURL) . "</b></p>";
		}
		
		$content = getCell("title", $pPost->mTitle);
		$content .= getCell("date", $head);
	
		$data = $pPost->mContent;
		$substrAmount = 200;
		if(strlen($data) > $substrAmount){
			$data =  substr($data, 0, strpos($data, " ", $substrAmount)) . "...";
		}
			
		$content .= getCell("content", htmlentities($data));
		$content .= getCell("show", "<a href = \"" . $this->mBaseURL . $pPost->mSlug . "/\">Visa hela...</a>");
		
		return $content;
	}

	function ShowSingle($pSlug, $pRemove = false)
	{
		$post = $this->FindPost($pSlug);
		$content = "";
		if(isset($post)) {
			$content .= $this->RenderSingle($this->FindPost($pSlug), $pSlug, $pRemove);
		}
		
		return $content;
	}	

	function RenderSingle($pPost, $pSlug = null, $pRemove = false)
	{
		$content = "";
		$admin = "";
		
		if($this->mUser->HasAccess($pPost->mAuthor))
		{		
			if(!$pRemove)
			{
				$admin .= getCell("admin right", "<p><a href = \"" . $this->mBaseURL . $pPost->mSlug . "/remove/\">Ta bort</a></p> <p><a href = \"" . $this->mBaseURL . $pPost->mSlug . "/edit/\">Redigera</a></p>");
			}
			else {
				$admin .= getCell("admin right", $this->Remove($pSlug));
			}
		}
		
		$head = "<p class = \"noSpaces\">Publicerad vid <b>" . $pPost->mDate . "</b></p>";
		$head .= "<p class = \"noSpaces\">Författare <b>" . ucfirst(strtolower($pPost->mAuthorName)) . "</b></p>";
		
		if(isset($pPost->mCategories)) {
			$head .= "<p class = \"noSpaces\">Kategorier <b>" . $pPost->GetCategories($this->mBaseURL) . "</b></p>";
		}
		
		$title = getCell("title left", $pPost->mTitle);
		$content .= getCell("date", $head);

		$textFilter = new CTextFilter($pPost->mContent, $pPost->mFilter);
		$content .= getCell("content", $textFilter->Filter());

		return getCell("contentViewerSingle", $title . $admin . getCell("clear", "") . getCell("contentViewerSingleInnerWrapper", $content));
	}
	
	function Remove($pSlug)
	{
		if(isset($_POST["removeContentSubmit"])) {
			$this->DoRemove();
		}

		$post = $this->FindPost($pSlug);
		
		if(isset($post))
		{
			$content = getCell("title", "Är du säker?");
			$content .= getCell("buttons", "<form method = \"post\" action = \"" . $this->mBaseURL . $pSlug . "/remove/\">
												<input name = \"contentID\" type = \"hidden\" value = \"" . $post->mID . "\">
												<input name = \"removeContentSubmit\" type = \"submit\" class = \"button\" value = \"Ja\"> 
											</form>
											
											<form method = \"post\" action = \"" . $this->mBaseURL . $pSlug . "/\">
												<input type = \"submit\" class = \"button\" value = \"Nej\">
											</form> ");
		}
		else {
			$content = "<p>Något gick fel vid borttagningen</p>";
		}
		
		return $content;
	}	
	
	function Edit($pSlug)
	{	
		if(isset($_POST["editContentSubmit"])) {
			$errors = $this->DoEdit($pSlug);
		}	
		
		$title = getCell("title", "Redigera inlägg");		
		$content = $this->RenderContentForm("editContentSubmit", $pSlug);
		
		if(isset($errors)) {
			$content .= getCell("error", $errors);
		}
		
		return getCell("contentViewerEdit", $title . getCell("contentViewerSingleInnerWrapper", $content));
	}
	
	function RenderContentForm($pSubmitName = "", $pSlug = null)
	{			
		if(isset($pSlug)) {
			$post = $this->FindPost($pSlug);
		}
		
		if($pSlug == null || isset($post))
		{
			$content = "<form method = \"post\" action = \"" . $this->mBaseURL . (isset($pSlug) ? $pSlug . "/edit" : "add") . "/\">";
			
			$content .= "<input name = \"contentID\" type = \"hidden\" value = \"" . (isset($post->mID) ? $post->mID : "") . "\">";
			
			$content .= getCell("inputTitle", "Titel");
			$content .= getCell("input", "<input name = \"contentTitle\" value = \"" . (isset($_POST["contentTitle"]) ? $_POST["contentTitle"] : (isset($post->mTitle) ? $post->mTitle : "")) . "\">");

			$content .= getCell("inputTitle", "Data");
			$content .= getCell("input", "<textarea name = \"contentData\">" . (isset($_POST["contentData"]) ? $_POST["contentData"] : (isset($post->mContent) ? $post->mContent : "")) . "</textarea>");
			
			$content .= getCell("inputTitle", "Filter (urskilj med ',')");
			$content .= getCell("input", "<input name = \"contentFilter\" value = \"" . (isset($_POST["contentFilter"]) ? $_POST["contentFilter"] : (isset($post->mFilter) ? $post->mFilter : "")) . "\">");
			
			$content .= getCell("inputTitle", "Publicera vid (yyyy-mm-dd hh:mm:ss)");
			$content .= getCell("input", "<input name = \"contentPublished\" value = \"" . (isset($_POST["contentPublished"]) ? $_POST["contentPublished"] : (isset($post->mDate) ? $post->mDate : date("Y-m-d H:i:s"))) . "\">");

			$content .= getCell("inputTitle", "Kategorier (urskilj med ',')");
			$content .= getCell("input", "<input name = \"contentCategories\" value = \"" . (isset($_POST["contentCategories"]) ? $_POST["contentCategories"] : (isset($post->mCategories) ? $post->GetCategoriesClean() : "")) . "\">");		
			
			$content .= getCell("submit", "<input name = \"" . $pSubmitName . "\" type = \"submit\" class = \"button\" value = \"Skicka\">");
			$content .= "</form>";
		}
		else
		{
			$content = "<p>Inlägget hittades inte och kan därför inte redigeras</p>";
		}
		
		return $content;		
	}

	function ValidateContentPosts(&$pData, $pWantID = false)
	{
		$errors = null;
		
		$pData = array(
			"id" => (isset($_POST["contentID"]) ? addslashes($_POST["contentID"]) : null),
			"title" => (isset($_POST["contentTitle"]) ? addslashes($_POST["contentTitle"]) : null),
			"content" => (isset($_POST["contentData"]) ? addslashes($_POST["contentData"]) : ""),
			"filter" => (isset($_POST["contentFilter"]) ? addslashes($_POST["contentFilter"]) : ""),
			"publishedDate" => (isset($_POST["contentPublished"]) ? addslashes($_POST["contentPublished"]) : null),
			"categories" => (isset($_POST["contentCategories"]) ? addslashes($_POST["contentCategories"]) : "")
		);
		
		if($pWantID && (!isset($pData["id"]) || empty($pData["id"]))) {
			$errors .= "<p>* Ogiltigt inlägg</p>";
		}
		if(!isset($pData["title"]) || empty($pData["title"])) {
			$errors .= "<p>* Ange en titel</p>";
		}
		if(!ValidateDate($pData["publishedDate"])) {
			$errors .= "<p>* Ogiltigt datum</p>";
		}

		return $errors;
	}
	
	function DoEdit($pSlug)
	{
		$data = array();
		$errors = $this->ValidateContentPosts($data, true);
	
		if(!isset($errors))
		{
			$post = $this->FindPostByID($data["id"]);
			
			if(isset($post)) {
				$generatedSlug = $post->Edit($data);
			}
			
			if(isset($generatedSlug)) 
			{
				echo "Redirecting to <a href = \"" . $this->mBaseURL . $generatedSlug . "\"/>" . $this->mBaseURL . $generatedSlug . "</a>";
				echo "<script>window.location.replace(\"" . $this->mBaseURL . $generatedSlug . "/\");</script>";
			}
			else {
				$errors = "<p>* Något gick fel</p>";
			}
		}
		
		return $errors;
	}	
	
	function DoRemove()
	{
		$postID = (isset($_POST["contentID"]) ? addslashes($_POST["contentID"]) : null);
		
		if(isset($postID))
		{
			if($this->FindPostByID($postID)->Remove()) 
			{
				echo "Redirecting to <a href = \"" . $this->mBaseURL . "\"/>" . $this->mBaseURL . "</a>";
				echo "<script>window.location.replace(\"" . $this->mBaseURL . "\");</script>";
			}
			else {
				$errors = "<p>* Något gick fel</p>";
			}
		}
	}	
	
	function Add($pAddFunction)
	{
		if(isset($_POST["addContentSubmit"])) {
			$errors = $this->DoAdd($pAddFunction);
		}	

		$title = getCell("title", "Lägg till");
		$content = $this->RenderContentForm("addContentSubmit");
		
		if(isset($errors)) {
			$content .= getCell("error", $errors);
		}
		
		return getCell("contentViewerEdit", $title . getCell("contentViewerSingleInnerWrapper", $content));
	}
	
	function DoAdd($pAddFunction)
	{
		$data = array();
		$errors = $this->ValidateContentPosts($data);
		
		if(!isset($errors))
		{
			$generatedSlug = $pAddFunction($data);
						
			if(isset($generatedSlug)) 
			{
				echo "Redirecting to <a href = \"" . $this->mBaseURL . $generatedSlug . "\"/>" . $this->mBaseURL . $generatedSlug . "</a>";
				echo "<script>window.location.replace(\"" . $this->mBaseURL . $generatedSlug . "/\");</script>";
			}
			else {
				$errors = "<p>* Något gick fel</p>";
			}
		}
		
		return $errors;
	}
}