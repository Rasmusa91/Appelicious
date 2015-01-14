<?php
use \Michelf\MarkdownExtra;

class CTextFilter
{
	private $mText;
	private $mFilter = array();
	
	private $supportedFilters = array("bbcode", "link", "markdown", "nl2br", "typography", "purify");
	
	function __construct($pText, $pFilter)
	{
		$this->mText = $pText;
		$this->mFilter = explode(',', str_replace(' ', '', $pFilter));
		
		foreach($this->mFilter as $key => $value)
		{
			if(!in_array($value, $this->supportedFilters))
			{
				unset($this->mFilter[$key]);
			}
		}
	}
	
	function Filter()
	{
		foreach($this->mFilter as $value)
		{
			switch($value)
			{
				case "link":
					$this->CreateLinks();
				break;
				case "nl2br":
					$this->mText = nl2br($this->mText);
				break;
				case "bbcode":
					$this->CreateFromBBCode();
				break;
				case "markdown":
					$this->CreateFromMarkdown();
				break;
				case "typography":
					$this->CreateTypographer();
				break;
				case "purify":
					$this->Purify();
				break;
			}
		}
		
		return $this->mText;
	}
	
	function CreateLinks()
	{
		$pattern = "/(https?:\/\/[^\s]+)/i";
		$replacement = "<a href = \"$1\">$1</a>";
		
		$this->mText = preg_replace($pattern, $replacement, $this->mText);
	}
	
	function CreateFromBBCode()
	{
		$patterns = array(	"/\[[b|B]\](.+)\[\/[[b|B]\]/i",
							"/\[[i|I]\](.+)\[\/[[i|I]\]/i",
							"/\[[u|U]\](.+)\[\/[[u|U]\]/i",
							"/\[img\](.+)\[\/img\]/i",
							"/\[relimg\](.+)\[\/relimg\]/i",
							"/\[url\](https?.+)\[\/url\]/i",
							"/\[url.*=.*(https?.+)\](.+)\[\/url\]/i",
							"/\[relurl.*=.*(.+)\](.+)\[\/relurl\]/i");
		
		$replacements = array(	"<b>$1</b>",
								"<em>$1</em>",
								"<u>$1</u>",
								"<a href = \"$1\"><img src = \"$1\" alt = \"$1\"></a>",
								"<a href = \"" . WORKSPACE_SERVERPATH . "$1\"><img src = \"" . WORKSPACE_SERVERPATH . "$1\" alt = \"" . WORKSPACE_SERVERPATH . "$1\"></a>",
								"<a href = \"$1\">$1</a>",
								"<a href = \"$1\">$2</a>",
								"<a href = \"$1\">" . WORKSPACE_SERVERPATH . "$2</a>");
	
		$this->mText = preg_replace($patterns, $replacements, $this->mText);
	}
	
	function CreateFromMarkdown()
	{
		require_once(APPELICIOUS_INSTALL_PATH . "/src/Michelf/Markdown.inc.php");
		require_once(APPELICIOUS_INSTALL_PATH . "/src/Michelf/MarkdownExtra.inc.php");

		$this->mText = MarkdownExtra::defaultTransform($this->mText);
	}
	
	function CreateTypographer()
	{
		require_once(APPELICIOUS_INSTALL_PATH . "/src/Typographer/smartypants.php");
		$this->mText = SmartyPants($this->mText);
	}
	
	function Purify()
	{
		require_once(APPELICIOUS_INSTALL_PATH . "/src/Purifier/HTMLPurifier.standalone.php");
		$config = HTMLPurifier_Config::createDefault();
		$config->set("Cache.DefinitionImpl", null);
		
		$purifier = new HTMLPurifier($config);
		
		$this->mText = $purifier->purify($this->mText);
	}
}

