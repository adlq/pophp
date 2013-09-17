<?php
/*
Copyright 2013 Duong Tuan Nghia

This file is part of Pophp.

Pophp is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Pophp is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with Pophp.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once("POParser.php");
require_once("POEntry.php");

class POFile
{
	private $entries;
	private $entryHashTable;

	/**
	 * Constructor method
	 *
	 * @param	string $file	the PO/POT file to construct from
	 */
	public function __construct($file = '')
	{
		if ($file !== '')
		{
			$parser = new POParser();
			$this->entries = $parser->parse($file);

			foreach ($this->entries as $id => $entry)
				$this->entryHashTable[$entry->getHash()] = $id;
		}
		else
		{
			$this->entries = array();
		}
	}

	/**
	 * Add a POEntry to the POFile
	 *
	 * @param POEntry $entry
	 */
	public function addEntry(POEntry $entry)
	{
		array_push($this->entries, $entry);
		$this->entryHashTable[$entry->getHash()] = count($this->entryHashTable) > 0 ? count($this->entryHashTable) - 1 : 0;
	}

	/**
	 * Retrieve the file's entries
	 *
	 * @param	$fromFiles	(Optional) Parameter to filter the entries
	 *			with respect to the files they come from
	 * @param string $rootFolder The highest folder from which the PO entries
	 * are extracted from.
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
						if (preg_match("/(.+):/", $reference, $match) && isset($match[1]))
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
	 * Attempt to retrieve a gettext entry.
	 *
	 * @param mixed $query The source string (msgid) or a POEntry object
	 * @param string $context (Optionnal) The context (msgctxt)
	 * @return mixed POEntry if the corresponding PO entry is found,
	 * False otherwise
	 */
	public function getEntry($query, $context = '')
	{
		// If the query is not a POEntry, we create a temp object to
		// retrieve its hash
		if (gettype($query) === "object" && get_class($query) === 'POEntry')
		{
			$temp = $query;
		}
		else
		{
			// Create a temp entry to calculate the hash key we're looking for
			$temp = new POEntry($query, '', $context);
		}

		$key = $temp->getHash();

		if (array_key_exists($key, $this->entryHashTable))
			return $this->entries[$this->entryHashTable[$key]];
		return false;
	}

	/**
	 * Retrieve the translation for a specified source string (or msgid)
	 *
	 * @param	string $str The msgid string
	 * @return mixed False if the string is not translated,
	 * its translation (string) otherwise
	 */
	public function getTranslation($str, $context = '')
	{
		$entry = $this->getEntry($str, $context);
		if ($entry !== false)
			return $entry->getTarget();
		return false;
	}

	public function getEntryHashTable()
	{
		return $this->entryHashTable;
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
		foreach ($entries as $entry)
			echo $entry;
	}

	/**
	 * Output the raw representation of the full
	 * PO/POT file
	 */
	public function __toString()
	{
		foreach ($this->entries as $entry)
			echo $entry;
	}

}
