<?php
require_once("POParser.php");
require_once("POEntry.php");

class POFile 
{
	private $entries;
	
	public function __construct($file)
	{
		$parser = new POParser();
		$this->entries = $parser->parse($file);
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
			$entry->display();
        }
    }
	
	public function getSourceStrings($files = array())
	{
		$entries = $this->getEntries($files);
		$sourceStrings = array();
		foreach ($entries as $entry) 
		{
			if ($entry->getSource() !== '')
                array_push($sourceStrings, $entry->getSource());
		}
		return $sourceStrings;
	}

	public function getFuzzyStrings()
	{
		$entries = $this->getEntries();
		$fuzzyStrings = array();
		
		foreach ($entries as $entry)
		{
			$comments = $entry->getComments();
			if (!empty($comments))
			{
				if (array_key_exists('flag', $comments))
				{
					foreach ($comments['flag'] as $flag)
					{
						if (trim($flag) == 'fuzzy')
							array_push($fuzzyStrings, $entry->getSource());
					}
				}
			}
		}
		return $fuzzyStrings;
	}
	
	public function getUntranslatedStrings()
	{
		$entries = $this->getEntries();
		$untranslatedStrings = array();
		
		foreach ($entries as $entry)
		{
			if (!$entry->isTranslated())
			{
				array_push($untranslatedStrings, $entry->getSource());
			}
		}
		
		return $untranslatedStrings;
	}
	
	public function getTranslatedEntries()
	{
		$entries = $this->getEntries();
		$translatedStrings = array();
		
		foreach ($entries as $entry)
		{
			if ($entry->isTranslated())
			{
				array_push($translatedStrings, $entry);
			}
		}
		
		return $translatedStrings;
	}
	
	public function getTranslation($str)
	{
		$entries = $this->getEntries();
		
		foreach($entries as $entry)
		{
			if ($entry->getSource() === $str && $entry->isTranslated())
			{
				return $entry->getTarget();
			}
		}
		
		return null;
	}
}

?>