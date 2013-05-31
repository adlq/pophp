<?php
require_once("POParser.php");
require_once("POEntry.php");

class POFile 
{
	private $entries;
	public $entriesHash;
	
	/**
	 * Constructor method
	 * 
	 * @param	$file	the PO/POT file to construct from
	 */
	public function __construct($file)
	{
		$parser = new POParser();
		$this->entries = $parser->parse($file);
		
		foreach ($this->entries as $id => $entry)
		{
			$this->entriesHash[$entry->getSource()] = $id;
		}
	}

	/**
	 * Retrieve the file's entries
	 * 
	 * @param	$fromFiles	(Optional) Parameter to filter the entries 
	 *			with respect to the files they come from
	 * @param string $rootFolder The root folder of the original files
	 * @return	An array containing the PO/POT file's entries
	 */
    public function getEntries($fromFiles = array(), $rootFolder = '')
    {
			$result = array();
			$match = array();

			if (!empty($fromFiles) && $rootFolder !== '') 
			{
				foreach ($this->entries as $entry) 
				{

					foreach ($entry->getReferences($rootFolder) as $reference)
					{
						// Retrieve the referenced file path
						if (preg_match("/(.+):/", $reference, $match))
						{
							if (isset($match[1]))
							{
								$referencePath = $match[1];
								// If the file is included in the filter, we keep the string
								if (in_array($referencePath, $fromFiles))
								{
									array_push($result, $entry);
									// Break out of foreach
									break;
								}
							}
						}
					}
				}
				return $result;
			}
		
			// If there's no filter, return all the entries
			return $this->entries;
    }

	/**
	 * Retrieve the file's source strings
	 * 
	 * @param	$fromFiles	(Optional) Parameter to filter the entries 
	 *			with respect to the files they come from
	 * @return 	the file's source strings
	 */
	public function getSourceStrings($fromFiles = array(), $rootFolder = '')
	{
		$entries = $this->getEntries($fromFiles, $rootFolder);
		$sourceStrings = array();
		foreach ($entries as $entry) 
		{
			if ($entry->getSource() !== '')
                array_push($sourceStrings, $entry->getSource());
		}
		return $sourceStrings;
	}

	/**
	 * Retrieve the file's fuzzy strings
	 * 
	 * @return	An array containing the file's fuzzy strings
	 */
	public function getFuzzyStrings()
	{
		$entries = $this->getEntries();
		$fuzzyStrings = array();
		
		foreach ($entries as $entry)
		{
			// Extract the comments from each entry
			if ($entry->isFuzzy())
				array_push($fuzzyStrings, $entry->getSource());
		}
		return $fuzzyStrings;
	}
	
	/**
	 * Retrieve the file's untranslated strings
	 * 
	 * @return	An array containing the file's untranslated strings
	 */
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
	
	/**
	 * Retrieve the file's translated strings
	 * 
	 * @return	An array containing the file's translated strings
	 */
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
	
	/**
	 * Returns a PO entry, given its source string (msgid)
	 * 
	 * @param string $src The source string
	 * @return POEntry The corresponding PO entry
	 */
	public function getEntryBySource($src)
	{
		return $this->entries[$this->entriesHash[$src]];
	}
	
	/**
	 * Retrieve the translation for a specified source string (or msgid)
	 * 
	 * @param	$str 	The msgid string
	 * @return	null if the string is not translated, 
	 *			its translation otherwise
	 */	
	public function getTranslation($str)
	{
		return $this->getEntryBySource($str)->getTarget();
	}
	
	
	/**
	 * Output a raw representation of the PO/POT file or 
	 * the specified entries
	 * 
	 * @param	$entries	(Optional) The entries to output
	 */
    public function display($entries = array())
    {
		// If no entries are specified as parameter, display all of them
		$entries = empty($entries) ? $this->entries : $entries;
        foreach ($this->entries as $entry)
        {
			// Call the display() method of each entry
			$entry->display();
        }
    }
	
}

?>