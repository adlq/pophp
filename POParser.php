<?php

$po = new POFile("lang.po");

class POFile 
{
	private $entries;
	
	public function __construct($file)
	{
		$entries = array();
		POParser::parse($file);
	}
}

class POEntry 
{
	private $comments;
	private $context;
	private $source;
	private $target;
	
	public function __construct($source, $target, $context = "", $comments = array())
	{
		$this->comments = $comments;
		$this->context = $context;
		$this->source = $source;
		$this->target = $target;
	}	
}

class POParser
{
	private $state;
	
	public static function parse($file) 
	{	
		$regexes = array(
		"comment" 	=> 	"/^#(.+?)\n/",
		"quote"		=>	"/^\"(.+?)\"/",
		"msgctxt" 	=>	"/^msgctxt \"(.+?)?\"/",
		"msgid" 	=>	"/^msgid \"(.+?)?\"/",
		"msgstr" 	=>	"/^msgstr \"(.+?)?\"/");
	
		$lines = file($file);
		
		$buffers = array("source" => "", "target" => "", "context" => "");
		$state = "";
		$comments = array();
		
		$entries = array();
		
		foreach ($lines as $line) 
		{
			$match = array();
			
			if (preg_match($regexes["comment"], $line, $match)) 
			{
				switch ($match[1][0])
				{
					case ".":
						$comments["extracted"] = substr($match[1], 2);
						break;
					case ":":
						$comments["reference"] = substr($match[1], 2);
						break;
					case ",":
						$comments["flag"] = substr($match[1], 2);
						break;
					case " ":
						$comments["translator"] = substr($match[1], 1);
						break;
					case "|":
						$attr = array();
						preg_match("/(\w+)/", $match[1], $attr);
						$pos = strpos($match[1], "\"");
						$comments["old" . ucfirst($attr[0])] = substr($match[1], $pos);
						break;
				}
			}
			else if (preg_match($regexes["msgctxt"], $line, $match))
			{
				$state = "msgctxt";
				if (isset($match[1]))
				{
					$buffers["context"] .= $match[1];
				}
			} 
			else if (preg_match($regexes["msgid"], $line, $match))
			{
				$state = "msgid";
				if (isset($match[1]))
				{
					$buffers["source"] .= $match[1];
				}
			} 
			else if (preg_match($regexes["msgstr"], $line, $match))
			{
				$state = "msgstr";
				if (isset($match[1]))
				{
					$buffers["target"] .= $match[1];
				}
			} 
			else if (preg_match($regexes["quote"], $line, $match))
			{
				if ($state == "msgid")
				{
					$buffers["source"] .= $match[1];
				}
				else if ($state == "msgstr")
				{
					$buffers["target"] .= $match[1];
				}
			}
			else
			{
				if ($state == "msgstr")
				{
					array_push($entries, new POEntry($buffers["source"], $buffers["target"], $buffers["context"], $comments));
					$buffers = array("source" => "", "target" => "", "context" => "");
					$state = "";
					$comments = array();
				}
			}
		}
		array_push($entries, new POEntry($buffers["source"], $buffers["target"]));
		
		print_r($entries);
	}
}

abstract class ParserState
{
	
}

class CommentState extends ParserState
{

}

class MsgIdState extends ParserState
{

}

class MsgStrState extends ParserState
{

}

class BlankLineState extends ParserState
{

}

class Context 
{

}

?>