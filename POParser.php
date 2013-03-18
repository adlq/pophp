<?php

class POFile 
{
	private $entries;
	
	public function __construct($file)
	{
		$this->entries = POParser::parse($file);
	}

    public function getEntries($files = array())
    {
		$result = array();
		if (!empty($files)) 
		{
			foreach ($this->entries as $entry) 
			{
				$comments = $entry->getComments();
				if (array_key_exists("reference", $comments))
				{
					// Retrieve the referenced file path
					foreach ($comments["reference"] as $reference)
					{
						if (preg_match("/(.+):/", $reference, $match))
						{
							$referencePath = $match[1];
							if (in_array($referencePath, $files))
							{
								array_push($result, $entry);
								break;
							}
						}
					}
				}
			}
			return $result;
		}
        return $this->entries;
    }

    public function display($entries = array())
    {
		$entries = empty($entries) ? $this->entries : $entries;
        foreach ($this->entries as $entry)
        {
            // Display comments first
            $comments = $entry->getComments();            
            foreach ($comments as $type => $comment) 
            {
                // Display comments
                switch ($type)
                {
                    case "translator":
                        foreach ($comment as $translatorComment)
                        {
                            echo "# $translatorComment\n";
                        }
                        break;
                    case "extracted":
                        echo "#. $comment\n";
                        break;
                    case "reference":
                        foreach ($comment as $ref)
                        {
                            echo "#: $ref\n";
                        }
                        break;
                    case "flag":
                        echo "#, $comment\n";
                        break;
                    default:
                        echo "#| $comment\n";
                        break;
                }
            }

            // Context
            $context = $entry->getContext();
            if ($context != "") 
                echo "msgctxt \"" . $context . "\"\n";

            // msgid
            $source = $entry->getSource();
            $this->displayWithLineBreak($source);

            // msgstr 
            $target = $entry->getTarget();
            $this->displayWithLineBreak($target);

            echo "\n";
        }
    }

    private function displayWithLineBreak($str)
    {
	
        $offset = 0;
        $break = strpos($str, '\n', $offset) != false;
        echo "msgstr ";
        if ($break == false)
        {
            echo "\"$str\"\n";
        }
        else 
        {
            
            while ($break != false)
            {
                $break = strpos($str, '\n', $offset);
                echo "\"" . substr($str, $offset, $break - $offset) . '\n' . "\"\n";
                $padding = strlen('\n');
                $offset = $break + $padding > strlen($str) ? strlen($target) - 1 : $break + $padding; 
            }
        }
    } 
	
	public function getSourceStrings($files = array())
	{
		$entries = $this->getEntries($files);
		$sourceStrings = array();
		foreach ($entries as $entry) 
		{
			array_push($sourceStrings, $entry->getSource());
		}
		return $sourceStrings;
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

    public function getComments()
    {
        return $this->comments;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTarget()
    {
        return $this->target;
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
			
			if (preg_match($regexes["comment"], $line, $match)) 
			{
				switch ($match[1][0])
				{
					case ".":
						$comments["extracted"][] = substr($match[1], 2);
						break;
					case ":":
						$comments["reference"][] = str_replace('\\', '/', substr($match[1], 2));
						break;
					case ",":
						$comments["flag"][] = substr($match[1], 2);
						break;
					case " ":
						$comments["translator"][] = substr($match[1], 1);
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
		array_push($entries, new POEntry($buffers["source"], $buffers["target"], $buffers["context"], $comments));
        return $entries;
	}
}
?>
