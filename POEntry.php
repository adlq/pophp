<?php
/*
Copyright 2013 Duong Tuan Nghia

This file is part of Pophp.

Pophp is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Pophp is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Pophp.  If not, see <http://www.gnu.org/licenses/>.
*/

class POEntry
{
	public $comments;
	private $context;
	private $source;
	private $target;
	private $isFuzzy;
	private $hash;

  /**
   * Constructor method
   * @param string  $source   Source string (msgid)
   * @param string  $target   Target string (msgstr)
   * @param string  $context  The entry's context (msgctxt)
   * @param array   $comments An array containing the entry's comments
   */
	public function __construct($source, $target, $context = '', $comments = array())
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
		$this->hash = hash('sha256', $context . $source);
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
	 * Retrieve the entry's hash
	 * @return string This entry's hash
	 */
	public function getHash()
	{
		return $this->hash;
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
	 * @param string $folder The root folder
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

	public function setReferences($refs)
	{
		$this->comments['reference'] = $refs;
	}

	/**
	 * Modify the target string
	 *
	 * @param string $target The new target string
	 */
	public function setTarget($target)
	{
		$this->target = $target;
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
		$regex = "/.*\\*$folder\\*(.+)/";
		$match = array();

		// Determine the folder delimiter
		if (strpos($path, '/') !== false)
		{
			$regex = "/.*\/*$folder\/(.+)/";
		}

		if (preg_match($regex, $path, $match))
		{
			if (isset($match[1]))
				return $match[1];
		}

		return '';
	}

	/**
	 * Display the entry, in gettext format
	 * @return string The string representing the entry
	 */
	public function __toString()
	{
		$out = '';

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
						$out .= "# $translatorComment\n";
					}
					break;

				case "extracted":
					foreach ($comment as $extracted)
					{
						$out .= "#. $extracted\n";
					}
					break;

				case "reference":
					foreach ($comment as $ref)
					{
						$out .= "#: $ref\n";
					}
					break;

				case "flag":
					$out .= "#, ";
					foreach ($comments['flag'] as $id => $flag)
					{
						if ($id === 0)
						{
							$out .= "$flag";
						}
						else
						{
							$out .= ", $flag";
						}
					}
					$out .= "\n";
					break;

				default:
					foreach ($comment as $previous)
					{
						$out .= "#| $previous\n";
					}
					break;
			}
		}

		// Context
		$context = $this->getContext();
		if ($context != '')
			$out .= "msgctxt \"" . $context . "\"\n";

		// Source and target
		$out .= "msgid \"{$this->getSource()}\"\n";
		$out .= "msgstr \"{$this->getTarget()}\"\n\n";

		return $out;
	}
}