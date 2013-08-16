<?php
require_once('POFile.php');
require_once('POUtils.php');

$sliced = $argv[1];
$slicer = $argv[2];

$utils = new POUtils();

$diff = $utils->compare($sliced, $slicer);

$out = <<<HEADER
msgid ""
msgstr ""
"Project-Id-Version: EasyquizzServer\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: \\n"
"PO-Revision-Date: \\n"
"Language-Team: Epistema <translations@epistema.com>\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"


HEADER;

echo $out;

foreach ($diff['firstOnly'] as $entry)
{
	//$entry->setTarget('');
	echo($entry);
}