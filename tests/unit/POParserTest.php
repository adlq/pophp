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

class POParserTest extends PHPUnit_Framework_TestCase
{
	const testPo = 'tests/unit/test.po';

	public function setUp()
	{
		$this->parser = new POParser();
	}

  /**
   * @covers POParser::parse()
   */
  public function testParse()
  {
    $this->assertEquals(self::getEntries(), $this->parser->parse(POParserTest::testPo));
  }

	public static function getEntries()
	{
    return array(
    // First entry
    new POEntry('Source 1', 'Target 1', 'Context 1',
        array(
          POParser::COMMENT_TRANSLATOR_KEY => array('Translator comment 1', 'Translator comment 2'),
          POParser::COMMENT_EXTRACTED_KEY => array('Extracted comment 1', 'Extracted comment 2'),
          POParser::COMMENT_REFERENCE_KEY => array(
					'C:\fooz\baz\foom\foo\bar\foobar1.php:321',
					'C:\fooz\baz\foom\foo\bar\foobar2.php:31',
					'C:\fooz\baz\foom\foo\bar\foobar2.php:34'),
          POParser::COMMENT_FLAG_KEY => array('Flag 1', 'Flag 2'),
          'msgid' => array('Previous msgid'),
          'msgctxt' => array('Previous msgctxt'),
        )),
    // Second entry
    new POEntry('Source 2', 'Target 2', '',
    	array(
    	POParser::COMMENT_REFERENCE_KEY => array('C:\fooz\baz\foom\foo\bar\foobar3.php:34'))),
		// Third entry
		new POEntry('Source 3', 'Fuzzy Target 3', '',
			array(
			POParser::COMMENT_FLAG_KEY => array('fuzzy'))),
		// Fourth entry
		new POEntry('Source 4', '')
    );
	}

}
?>
