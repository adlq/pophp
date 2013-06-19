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
	
	public function __construct()
	{
		
	}
	
	/**
	 * Compare 2 gettext files
	 * 
	 * @param string $file1 Path to the first file
	 * @param string $file2 Path to the second file
	 * @return array An array containing 2 sub arrays, indicating 
	 * entries exclusive to each file
	 * @throws Exception
	 */
	public function compare($file1, $file2)
	{
		$result = array('first' => array(), 'second' => array());
		
		if (!file_exists($file1) || !file_exists($file2))
			throw new Exception();
		
		$po1 = new POFile($file1);
		$po2 = new POFile($file2);
		
		$result['firstMsgCount'] = count($po1->getEntries());
		$result['secondMsgCount'] = count($po2->getEntries());
		
		$result['firstOnly'] = $this->compareEntries($po1, $po2);
		$result['secondOnly'] = $this->compareEntries($po2, $po1);
		
		return $result;
	}
	
	/**
	 * Compare 2 POFile objects
	 * 
	 * @param POFile $po1 The first POFile
	 * @param POFile $po2 The second POFile
	 * @return array The diff array, containg entries in the first POFile 
	 * but not in the second one
	 */
	private function compareEntries($po1, $po2)
	{
		$diffArray = array();

		foreach ($po1->getEntries() as $entry) 
		{
			if (!$po2->getEntry($entry))
				array_push($diffArray, $entry);
		}

		return $diffArray;
	}
}

