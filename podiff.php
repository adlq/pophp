<?php
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
