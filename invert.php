<?php
require_once('POFile.php');
$file = new POFile('C:\Users\nduong\Downloads\from_en-GB_to_zh-CHS.po');

$entries = $file->getEntries();

$count = 0;

foreach ($entries as $entry)
{
	$count++;
	$src = $entry->getSource();
	$dest = $entry->getTarget();
	
	if ($count === 1)
	{
		echo "msgid \"$src\"\n";
		echo "msgstr \"$dest\"\n\n";
		continue;
	}

	if (!empty($dest))
	{
		echo "msgid \"$dest\"\n";
		echo "msgstr \"$src\"\n\n";
	}
}
?>
