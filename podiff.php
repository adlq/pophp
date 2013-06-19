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

require_once("POUtils.php");

$file1 = $argv[1];
$file2 = $argv[2];

$utils = new POUtils();

$diff = $utils->compare($file1, $file2);

displayStats("# of strings in $file1", $diff['firstMsgCount']);
displayStats("# of strings in $file2", $diff['secondMsgCount']);

$strings1not2Count = count($diff['firstOnly']);
$strings2not1Count = count($diff['secondOnly']);

// Output different things 
displayStats("In $file1 but not in $file2", $strings1not2Count);
displayEntryArray($diff['firstOnly']);

displayStats("In $file2 but not in $file1", $strings2not1Count);
displayEntryArray($diff['secondOnly']);

/**
 * Appropriately display a string array in TortoiseHg's output log
 *
 * @param	$stringArray	The string array to be displayed
 */
function displayEntryArray($entryArray)
{
	foreach ($entryArray as $entry)
	{
		echo "\t- \"" . $entry->getSource() . "\"\n";
	}
}

/**
 * Appropriately display a message with the associated stats
 * in TortoiseHg's output log
 *
 * @param	$msg 	The message to be displayed
 *			$stat	The associated stat
 */
function displayStats($msg, $stat)
{
	echo "\n$msg: $stat\n\n-----\n\n";
}
