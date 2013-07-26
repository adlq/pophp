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

require_once('POFile.php');

/**
 * Description of POUtil
 *
 * @author nduong
 */
class POUtils
{

	private $gettextHeader = <<<HEADER
# SOME DESCRIPTIVE TITLE.
# Copyright (C) YEAR THE PACKAGE'S COPYRIGHT HOLDER
# This file is distributed under the same license as the PACKAGE package.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
msgid ""
msgstr ""
"Project-Id-Version: EasyquizzServer\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: 2006-09-01 18:31+0200\\n"
"PO-Revision-Date: 2006-11-01 09:27+0100\\n"
"Last-Translator: Bertrand Gorge <bertrand.gorge@epistema.com>\\n"
"Language-Team: Epistema <translations@epistema.com>\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"X-Poedit-Language: English\\n"
"X-Poedit-SourceCharset: utf-8\\n"
"X-Poedit-KeywordsList: EpiLang\\n"
"X-Poedit-Basepath: ..\\n"


HEADER;

	public function __construct()
	{

	}

	/**
	 * Compare 2 gettext files
	 *
	 * @param string $file1 Path to the first file
	 * @param string $file2 Path to the second file
	 * @param array $fromFiles List of source files to extract the entries from
	 * @return array An array containing 2 sub arrays, indicating
	 * entries exclusive to each file
	 * @throws Exception
	 */
	public function compare($file1, $file2, $fromFiles = array(), $rootFolder = '')
	{
		$result = array('first' => array(), 'second' => array());

		if (!file_exists($file1) || !file_exists($file2))
			throw new Exception();

		$po1 = new POFile($file1);
		$po2 = new POFile($file2);

		$result['firstMsgCount'] = count($po1->getEntries($fromFiles, $rootFolder));
		$result['secondMsgCount'] = count($po2->getEntries($fromFiles, $rootFolder));

		$result['firstOnly'] = $this->diffEntries($po1, $po2, $fromFiles, $rootFolder);
		$result['secondOnly'] = $this->diffEntries($po2, $po1, $fromFiles, $rootFolder);

		$result['common'] = $this->commonEntries($po1, $po2, $fromFiles, $rootFolder);

		return $result;
	}

	/**
	 * Compare 2 POFile objects
	 *
	 * @param POFile $po1 The first POFile
	 * @param POFile $po2 The second POFile
	 * @param array $fromFiles List of source files to extract the entries from
	 * @return array The diff array, containg entries in the first POFile
	 * but not in the second one
	 */
	private function diffEntries($po1, $po2, $fromFiles = array(), $rootFolder = '')
	{
		$diffArray = array();

		foreach ($po1->getEntries($fromFiles, $rootFolder) as $entry)
		{
			if (!$po2->getEntry($entry))
				array_push($diffArray, $entry);
		}

		return $diffArray;
	}

	/**
	 * Find common entries from 2 POFile objects
	 *
	 * @param POFile $po1 The first POFile
	 * @param POFile $po2 The second POFile
	 * @param array $fromFiles List of source files to extract the entries from
	 * @return array The common entries
	 */
	private function commonEntries($po1, $po2, $fromFiles = array(), $rootFolder = '')
	{
		$commonArray = array();

		foreach ($po1->getEntries($fromFiles, $rootFolder) as $entry)
		{
			if ($po2->getEntry($entry))
				array_push($commonArray, $entry);
		}

		return $commonArray;
	}

	/**
	 * Initiate a gettext file (header to define)
	 *
	 * @param string $file Path to the file
	 */
	public function initGettextFile($file)
	{
		file_put_contents($file, $this->gettextHeader);
	}

	/**
	 * Return the Gettext header used to initialize PO files
	 *
	 * @return string The header
	 */
	public function getGettextHeader()
	{
		return $this->gettextHeader;
	}
}

