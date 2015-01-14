<?php

class CPage extends CPost
{
	function GetEditQuery($pData, &$pGeneratedSlug)
	{
		$pGeneratedSlug = slugify($pData["title"]) . '-' . time();

		$query = "	UPDATE " . $this->mTableStructure["tableName"] . " 
					SET " . $this->mTableStructure["slug"] . " 				= \"" . $pGeneratedSlug . "\",
						" . $this->mTableStructure["url"] . " 			= \"" . $pGeneratedSlug . "\",
						" . $this->mTableStructure["title"] . " 			= \"" . $pData["title"] . "\",
						" . $this->mTableStructure["content"] . "  			= \"" . $pData["content"] . "\",
						" . $this->mTableStructure["filter"] . "  			= \"" . $pData["filter"] . "\",
						" . $this->mTableStructure["publishedDate"] . "  	= \"" . $pData["publishedDate"] . "\",
						" . $this->mTableStructure["updatedDate"] . " 		= NOW()
					WHERE " . $this->mTableStructure["id"] . " = " . $pData["id"] . ";";
					
		return $query;
	}

	function GetRemoveQuery($pID)
	{
		$query = "	UPDATE " . $this->mTableStructure["tableName"] . " 
					SET " . $this->mTableStructure["deletedDate"] . " = NOW()
					WHERE " . $this->mTableStructure["id"] . " = " . $pID . ";";
					
		return $query;
	}
	
	static function GetAddQuery($pData, &$pGeneratedSlug, $pTableStructure)
	{
		$pGeneratedSlug = slugify($pData["title"]) . '-' . time();
		
		$query = "	INSERT INTO " . $pTableStructure["tableName"] . "
							(	
								" . $pTableStructure["slug"] . ",
								" . $pTableStructure["url"] . ",
								" . $pTableStructure["type"] . ",
								" . $pTableStructure["title"] . ", 
								" . $pTableStructure["content"] . ", 
								" . $pTableStructure["filter"] . ", 
								" . $pTableStructure["publishedDate"] . ", 
								" . $pTableStructure["createdDate"] . ",
								" . $pTableStructure["author"] . "
							) 
					VALUES	(	
								\"" . $pGeneratedSlug . "\",
								\"" . $pGeneratedSlug . "\",
								\"" . $pData["type"] . "\",
								\"" . $pData["title"] . "\",
								\"" . $pData["content"] . "\",
								\"" . $pData["filter"] . "\",
								\"" . $pData["publishedDate"] . "\",
								NOW(),
								\"" . $pData["author"] . "\"
							);";
	
		return $query;
	}	
}