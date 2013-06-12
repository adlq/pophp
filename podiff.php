<?php
require_once("POFile.php");
require_once("POEntry.php");

$file1 = $argv[1];
$file2 = $argv[2];

$pofile1 = new POFile($file1);
$pofile2 = new POFile($file2);

// Compare both string arrays and retrieve the missing strings as well as the obsolete ones
$strings1not2 = compareEntries($pofile2, $pofile1);
$strings2not1 = compareEntries($pofile1, $pofile2);

displayStats("# of strings in $file1", count($pofile1->getEntries()));
displayStats("# of strings in $file2", count($pofile2->getEntries()));

$strings1not2Count = count($strings1not2);
$strings2not1Count = count($strings2not1);

// Output different things 
displayStats("In $file1 but not in $file2", $strings1not2Count);
displayEntryArray($strings1not2);

displayStats("In $file2 but not in $file1", $strings2not1Count);
displayEntryArray($strings2not1);

/**
 * Take one reference array and another array to compare to,
 * output their difference.
 *
 * @param	$refArray	the reference array
 *			$array		the array to compare it to
 * @return	the strings from $array that are not included in $refArray
 */
function compareEntries($file1, $file2)
{
	$diffArray = array();
	
	foreach ($file1->getEntries() as $entry) 
	{
		if (!$file2->getEntry($entry))
			array_push($diffArray, $entry);
	}
	
	return $diffArray;
}

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
?>
