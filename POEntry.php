<?php 

class POEntry 
{
	public $comments;
	private $context;
	private $source;
	private $target;
	private $isFuzzy;
	
  /**
   * Constructor method
   * @param string  $source   Source string (msgid)
   * @param string  $target   Target string (msgstr)
   * @param string  $context  The entry's context (msgctxt)
   * @param array   $comments An array containing the entry's comments
   */
	public function __construct($source, $target, $context = "", $comments = array())
	{
		$this->comments = $comments;

		$this->isFuzzy = false;
		
		// Extract the comments from each entry
		if (!empty($comments))
		{
			// Extract the translation state from the comment
			// The fuzzy status is specified amongst the flags
			if (array_key_exists('flag', $comments))
			{
				// Examine each flag
				foreach ($comments['flag'] as $flag)
				{
					if (trim($flag) == 'fuzzy')
					{
            $this->isFuzzy = true;
            break;
          }
				}
			}
		}

		$this->context = $context;
		$this->source = $source;
		$this->target = $target;
	}

	/**
	 * Does this entry have a translation? (fuzzy or not)
	 *
	 * @return	True is the entry is translated, False otherwise
	 */
	public function isTranslated()
	{
		return ($this->getTarget() !== '');
	}
	
	/**
	 * Retrieve the fuzzy state of an entry
	 *
	 * @return True if the entry is fuzzy, False otherwise
	 */
	public function isFuzzy()
	{
    return $this->isFuzzy;
	}
	
	/**
	 * Retrieve the comments associated to an entry
	 *
	 * @return	An array containing the entry's comments
	 */
	public function getComments()
	{
			return $this->comments;
	}

	/**
	 * Retrieve an entry's context
	 *
	 * @return	The entry's context, in string format
	 */
	public function getContext()
	{
			return $this->context;
	}

	/**
	 * Retrieve the source string (msgid) of an entry
	 *
	 * @return	The msgid, in string format
	 */
	public function getSource()
	{
			return $this->source;
	}

	/**
	 * Retrieve the target string (msgstr) of an entry
	 *
	 * @return	The msgstr, in string format
	 */
	public function getTarget()
	{
			return $this->target;
	}
		
	/**
	 * Retrieve the list of references for an entry
	 * 
	 * @param string $rootFolder The root folder 
	 * @return array The list of references for the entry
	 */
	public function getReferences($folder)
	{
		$comments = $this->comments;
		$references = array();
		
		
		if (!empty($comments))
		{
			// Extract the reference for each entry
			// If there's reference information 
			if (array_key_exists("reference", $comments))
			{
				// Loop over all the references
				foreach ($comments["reference"] as $reference)
				{
					// Extract the relevant reference
					$finalReference = $this->extractRelevantReference($reference, $folder);
					if ($finalReference !== '')
					{
							// Push the reference onto the result array
							array_push($references, $finalReference);
					}
				}
			}
		}
		
		return $references;
	}
	
	/**
	 * Extract the relevant part of the PO reference with respect
	 * to the main folder of the code
	 * 
	 * @param string $path The original path
	 * @param string $folder The main folder for the code
	 * @return string The final path
	 */
	public function extractRelevantReference($path, $folder)
	{
		$regex = "/.+\\\\$folder\\\\(.+)/";
		$match = array();
		
		// Determine the folder delimiter
		if (strpos($path, '/') !== false)
		{
			$regex = "/.+\/$folder\/(.+)/";
		}

		if (preg_match($regex, $path, $match))
		{
			if (isset($match[1]))
				return $match[1];
		}
		
		return '';
	}
	
	
	/**
	 * Display the entry, in standard gettext format
	 */
	public function display() 
	{
		// Display comments first
		$comments = $this->getComments();            
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
					foreach ($comment as $extracted)
					{
						echo "#. $extracted\n";
					}
					break;
				case "reference":
					foreach ($comment as $ref)
					{
						echo "#: " . str_replace('/', '\\', $ref) . "\n";
					}
					break;
				case "flag":
					echo "#, ";
					foreach ($comments['flag'] as $id => $flag)
					{
						if ($id === 0)
						{
							echo "$flag"; 
						} 
						else 
						{
							echo ", $flag";
						}
					}
					echo "\n";
					break;
				default:
					foreach ($comment as $old)
					{
						echo "#| $old\n";
					}
					break;
			}
		}

		// Context
		$context = $this->getContext();
		if ($context != "") 
			echo "msgctxt \"" . $context . "\"\n";

		// msgid
		$source = $this->getSource();
		echo 'msgid ';
		$this->displayWithLineBreak($source);

		// msgstr 
		$target = $this->getTarget();
		echo 'msgstr ';
		if ($this->isTranslated())
		{
			$this->displayWithLineBreak($target);
		} 
		else 
		{
			echo "\"\"\n";
		}
		echo "\n";
	}
	
	
	private function displayWithLineBreak($str)
	{
		// Only perform this if the string is not empty
		if ($str !== '')
		{
			// Offset to be used with strpos
			$offset = 0;

			// Find first occurence of line break in the string
			$break = strpos($str, '\n', $offset) !== false;

			// If there is no line break, simply print out the string
			if ($break == false)
			{
				echo "\"$str\"\n";
			}
			else 
			{
				// Otherwise, we break lines till there are no more to break
				while ($break !== false)
				{
					$break = strpos($str, '\n', $offset);
					if ($break !== false)
					{
						echo "\"" . substr($str, $offset, $break - $offset) . '\n' . "\"\n";
						$padding = strlen('\n');
						$offset = $break + $padding > strlen($str) ? strlen($target) - 1 : $break + $padding; 
					}
				}
				if ($offset !== strlen($str))
					echo "\"" . substr($str, $offset) . "\"\n";
			}
		}
	} 
}

?>
