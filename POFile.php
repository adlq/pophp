<?php
require_once("POParser.php");

class POFile 
{
	private $entries;
	
	public function __construct($file)
	{
		$this->entries = POParser::parse($file);
		foreach($this->entries as $id => $entry) 
		{
			if ($entry->getSource() === "")
			{
				unset($this->entries[$id]);
			}
		}
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

	public function getFuzzyStrings()
	{
		$result = array();
		
		foreach ($this->entries as $entry)
		{
			$comments = $entry->getComments();
			if (!empty($comments))
			{
				
				if (array_key_exists('flag', $comments))
				{
					foreach ($comments['flag'] as $flag)
					{
						if (trim($flag) == 'fuzzy')
							array_push($result, $entry->getSource());
					}
				}
			}
		}
		return $result;
	}
	
	public function getUntranslatedStrings()
	{
		$result = array();
		
		foreach ($this->entries as $entry)
		{
			if ($entry->getTarget() === "")
			{
				array_push($result, $entry->getSource());
			}
		}
		
		return $result;
	}
}

?>