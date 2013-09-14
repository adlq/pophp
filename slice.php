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
