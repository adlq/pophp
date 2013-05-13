<?php
require_once("POFile.php");
require_once("POEntry.php");

$file1 = $argv[1];
$file2 = $argv[2];

$pofile1 = new POFile($file1);
$pofile2 = new POFile($file2);

// Get the appropriate old and new strings
$sources1 = $pofile1->getSourceStrings();
$sources2 = $pofile2->getSourceStrings();

// Compare both string arrays and retrieve the missing strings as well as the obsolete ones
$strings1not2 = compareStringArrays($sources2, $sources1);
$strings2not1 = compareStringArrays($sources1, $sources2);

// Output different things 
displayStats("In $file1 but not in $file2", 0);
displayStringArray($strings1not2);

displayStats("In $file2 but not in $file1", 0);
displayStringArray($strings2not1);

/**
 * Take one reference array and another array to compare to,
 * output their difference.
 *
 * @param	$refArray	the reference array
 *			$array		the array to compare it to
 * @return	the strings from $array that are not included in $refArray
 */
function compareStringArrays($refArray, $array)
{
	$diffArray = array();
	
	foreach ($array as $el) 
	{
		if (!in_array($el, $refArray))
		{
			array_push($diffArray, $el);
		}
	}
	
	return $diffArray;
}

/**
 * Appropriately display a string array in TortoiseHg's output log
 *
 * @param	$stringArray	The string array to be displayed
 */
function displayStringArray($stringArray)
{
	foreach ($stringArray as $string)
	{
		echo "\t- \"$string\"\n";
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
	echo "\n$msg: $stat\n\n";
}
?>
