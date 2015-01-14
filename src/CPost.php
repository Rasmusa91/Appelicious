<?php

class CPost
{
	public $mID;
	public $mSlug;
	public $mTitle;
	public $mContent;
	public $mFilter;
	public $mDate;
	public $mAuthor;
	public $mAuthorName;
	public $mCategories;
	
	private $mData;
	protected $mTableStructure;
	private $mDatabaseInfo;
	
	function __construct($pData, $pDatabaseInfo, $pTableStructure)
	{
		$this->mData = $pData;
		$this->mDatabaseInfo = $pDatabaseInfo;
		$this->mTableStructure = $pTableStructure;
		
		if(isset($pData)) {
			$this->GetData();
		}
	}
	
	function GetData()
	{
		$this->mID = $this->mData[$this->mTableStructure["id"]];
		$this->mSlug = $this->mData[$this->mTableStructure["slug"]];
		$this->mTitle = $this->mData[$this->mTableStructure["title"]];
		$this->mContent = $this->mData[$this->mTableStructure["content"]];
		$this->mFilter = $this->mData[$this->mTableStructure["filter"]];
		$this->mDate = $this->mData[$this->mTableStructure["publishedDate"]];
		$this->mAuthor = $this->mData[$this->mTableStructure["author"]];
		$this->mAuthorName = $this->mData[$this->mTableStructure["authorName"]];
		$this->mAuthorName = "<a href = \"" . WORKSPACE_SERVERPATH . "profile/" . $this->mAuthorName . "/\">" . $this->mAuthorName . "</a>";
		$this->mCategories = (isset($this->mData[$this->mTableStructure["categories"]]) ? $this->mData[$this->mTableStructure["categories"]] : null);
		$this->mCategories = explode(",", str_replace(" ", "", $this->mCategories));
	}
	
	function GetCategories($pBaseURL)
	{
		$output = "";
		
		foreach($this->mCategories as $categoriesValue)
		{
			$output .= "<a href = \"" . $pBaseURL . "?c=$categoriesValue\">" . $categoriesValue . "</a> ";
		}
		
		return $output;
	}
	
	function GetCategoriesClean()
	{
		$output = "";
		
		foreach($this->mCategories as $categoriesValue)
		{
			$output .= $categoriesValue . ", ";
		}
		
		$output = substr($output, 0, -2);
		
		return $output;
	}	
	
	function Edit($pData)
	{
		$db = new CDatabase($this->mDatabaseInfo);
		$generatedSlug = null;
		$query = $this->GetEditQuery($pData, $generatedSlug);
		$res = $db->ExecuteQuery($query);
		static::AddCategoriesDatabase($db, $this->mTableStructure, $pData["categories"], $pData["id"]);

		return ($res ? $generatedSlug : null);
	}
	
	function GetEditQuery($pData, &$pGeneratedSlug)
	{
		echo "Override me";
		$pGeneratedSlug = null;
		
		return false;
	}
	
	function Remove()
	{
		$db = new CDatabase($this->mDatabaseInfo);
		$query = $this->GetRemoveQuery($this->mID);
		$res = $db->ExecuteQuery($query);
		//static::RemoveRelatedCategories($db, $this->mTableStructure, $this->mID);
		
		return $res;		
	}

	function GetRemoveQuery($pID)
	{
		echo "Override me";

		return false;
	}
	
	static function RemoveRelatedCategories($pDatabase, $pTableStructure, $pID)
	{
		// Remove old categories
		$query = "
			DELETE FROM " . $pTableStructure["categoryContentRelationsTable"] . "
			WHERE " . $pTableStructure["categoryContentRelationsContentID"] . " = ?;
		";
		$res = $pDatabase->ExecuteQuery($query, array($pID));	
	}
	
	static function AddCategoriesDatabase($pDatabase, $pTableStructure, $pCategories, $pID)
	{
		$categories = explode(",", str_replace(" ", "", $pCategories));
		$categories = array_unique($categories);		
		
		static::RemoveRelatedCategories($pDatabase, $pTableStructure, $pID);
		
		foreach($categories as $categoriesValue)
		{
			// Category
			$query = "
				INSERT INTO " . $pTableStructure["categoriesTable"] . "
					(" . $pTableStructure["categoriesName"] . ")
				VALUES (?);
			";
			$res = $pDatabase->ExecuteQuery($query, array($categoriesValue));
						
			// Relation
			$query = "	
						INSERT INTO " . $pTableStructure["categoryContentRelationsTable"] . " 
						(
							" . $pTableStructure["categoryContentRelationsContentID"] . ", 
							" . $pTableStructure["categoryContentRelationsIDCategoryID"] . "
						) 
						SELECT ?, " . $pTableStructure["categoriesID"] . " 
						FROM " . $pTableStructure["categoriesTable"] . " 
						WHERE name = ?;
					";
			$res = $pDatabase->ExecuteQuery($query, array($pID, $categoriesValue));
		}
	}
	
	static function AddPost($pData, $pDatabase, $pTableStructure)
	{
		$db = new CDatabase($pDatabase);
		$generatedSlug = null;
		$query = static::GetAddQuery($pData, $generatedSlug, $pTableStructure);
		$res = $db->ExecuteQuery($query);
		static::AddCategoriesDatabase($db, $pTableStructure, $pData["categories"], $db->GetLastID());

		return ($res ? $generatedSlug : null);
	}
	
	static function GetAddQuery($pData, &$pGeneratedSlug, $pTableStructure)
	{
		echo "Override me";
		$pGeneratedSlug = null;
		
		return false;
	}
}