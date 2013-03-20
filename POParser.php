<?php
class POParser
{
	private $state;
	
	public static function parse($file) 
	{	
		$regexes = array(
		"comment" 	=> 	"/^#(.+?)\n/",
		"quote"		=>	"/^\"(.+?)\"/",
		"msgctxt" 	=>	"/^msgctxt \"(.*)?\"/",
		"msgid" 	=>	"/^msgid \"(.*)?\"/",
		"msgstr" 	=>	"/^msgstr \"(.*)?\"/");
	
		$lines = file($file);
		
		$buffers = array("source" => "", "target" => "", "context" => "");
		$state = "";
		$comments = array();
		
		$entries = array();
		
		foreach ($lines as $line) 
		{
			$match = array();
			
			if (preg_match($regexes["comment"], $line, $match) && $state != "obsolete") 
			{
				switch ($match[1][0])
				{
					case ".":
						$comments["extracted"][] = trim(substr($match[1], 2));
						break;
					case ":":
						$comments["reference"][] = trim(str_replace('\\', '/', substr($match[1], 2)));
						break;
					case ",":
						$flags = explode(", ", substr($match[1], 2));
						foreach ($flags as $flag)
						{
							$comments["flag"][] = trim($flag);
						}
						break;
					case " ":
						$comments["translator"][] = trim(substr($match[1], 1));
						break;
					case "|":
						$attr = array();
						preg_match("/(\w+)/", $match[1], $attr);
						$pos = strpos($match[1], "\"");
						$comments["old" . ucfirst($attr[0])] = trim(substr($match[1], $pos));
						break;
					case "~":
						$state = "obsolete";
						break;
				}
			}
			else if (preg_match($regexes["msgctxt"], $line, $match) && $state != "obsolete")
			{
				$state = "msgctxt";
				if (isset($match[1]))
				{
					$buffers["context"] .= $match[1];
				}
			} 
			else if (preg_match($regexes["msgid"], $line, $match) && $state != "obsolete")
			{
				$state = "msgid";
				if (isset($match[1]))
				{
					$buffers["source"] .= $match[1];
				}
			} 
			else if (preg_match($regexes["msgstr"], $line, $match) && $state != "obsolete")
			{
				$state = "msgstr";
				if (isset($match[1]))
				{
					$buffers["target"] .= $match[1];
				}
			} 
			else if (preg_match($regexes["quote"], $line, $match) && $state != "obsolete")
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
		array_push($entries, new POEntry($buffers["source"], $buffers["target"], $buffers["context"], $comments));
        return $entries;
	}
}
?>
