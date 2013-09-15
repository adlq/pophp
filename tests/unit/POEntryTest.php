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

require_once("POEntry.php");
require_once('POParser.php');

/**
 * @covers POEntry
 */
class POEntryTest extends PHPUnit_Framework_TestCase
{
	private $standardEntry;
	private $fuzzyEntry;
	private $untranslatedEntry;

	public function setUp()
	{
		$this->standardEntry = new POEntry('source1', 'target1');
		$this->fuzzyEntry = new POEntry('source2', 'target2', '', array(POParser::COMMENT_FLAG_KEY => array('fuzzy')));
		$this->untranslatedEntry = new POEntry('source3', '');
		$this->fullEntry = new POEntry('source4', 'target4', 'Context',
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
			));
	}

	/**
	 * @covers POEntry::extractRelevantPath
	 */
	public function testExtractRelevantPath()
	{

		$this->assertEquals('foo/bar/foobar.php:123',
				$this
				->standardEntry
				->extractRelevantReference('/fooz/baz/foom/foo/bar/foobar.php:123', 'foom'));
		$this->assertEquals('foo\bar\foobar.php:321',
				$this
				->standardEntry
				->extractRelevantReference('C:\fooz\baz\foom\foo\bar\foobar.php:321', 'foom'));
		$this->assertEmpty($this
				->standardEntry
				->extractRelevantReference('/fooz/baz/foom/foo/bar/foobar.php:123', 'foob'));
		$this->assertEmpty($this
				->standardEntry
				->extractRelevantReference('C:\fooz\baz\foom\foo\bar\foobar.php:321', 'foob'));
	}

	/**
	 * @covers POEntry::getReferences
	 */
	public function testGetReferences()
	{
		// Unix folder delimiter
		$this->standardEntry->comments = array(
			POParser::COMMENT_REFERENCE_KEY => array(
				'/fooz/baz/foom/foo/bar/foobar1.php:123',
				'/fooz/baz/foom/foo/bar/foobar2.php:13',
				'/fooz/baz/foom/foo/bar/foobar2.php:134'
			)
		);

		$this->assertEquals(array(
			'foo/bar/foobar1.php:123',
			'foo/bar/foobar2.php:13',
			'foo/bar/foobar2.php:134'
		), $this->standardEntry->getReferences('foom'));

		// Win folder delimiter
		$this->standardEntry->comments = array(
			POParser::COMMENT_REFERENCE_KEY => array(
				'C:\fooz\baz\foom\foo\bar\foobar1.php:321',
				'C:\fooz\baz\foom\foo\bar\foobar2.php:31',
				'C:\fooz\baz\foom\foo\bar\foobar2.php:34'
			)
		);

		$this->assertEquals(array(
			'foo\bar\foobar1.php:321',
			'foo\bar\foobar2.php:31',
			'foo\bar\foobar2.php:34'
		), $this->standardEntry->getReferences('foom'));

		// No references
		$this->standardEntry->comments = array(POParser::COMMENT_REFERENCE_KEY => array());

		$this->assertEmpty($this->standardEntry->getReferences('foo'));
	}

	/**
	 * @covers POEntry::isTranslated
	 */
	public function testIsTranslated()
	{
		$this->assertFalse($this->untranslatedEntry->isTranslated());
		$this->assertTrue($this->standardEntry->isTranslated());
	}

	/**
	 * @covers POEntry::isFuzzy
	 */
	public function testIsFuzzy()
	{
		$this->assertTrue($this->fuzzyEntry->isFuzzy());
		$this->assertFalse($this->standardEntry->isFuzzy());
	}

	/**
	 * @covers POEntry::__toString
	 */
	public function testToString()
	{
		$this->assertEquals("#, fuzzy\nmsgid \"source2\"\nmsgstr \"target2\"\n\n", $this->fuzzyEntry->__toString());
		$this->assertEquals("msgid \"source3\"\nmsgstr \"\"\n\n", $this->untranslatedEntry->__toString());

    $expectedFullEntryString = "# Translator comment 1\n";
    $expectedFullEntryString .= "# Translator comment 2\n";
    $expectedFullEntryString .= "#. Extracted comment 1\n";
    $expectedFullEntryString .= "#. Extracted comment 2\n";
    $expectedFullEntryString .= "#: C:\\fooz\\baz\\foom\\foo\\bar\\foobar1.php:321\n";
    $expectedFullEntryString .= "#: C:\\fooz\\baz\\foom\\foo\\bar\\foobar2.php:31\n";
    $expectedFullEntryString .= "#: C:\\fooz\\baz\\foom\\foo\\bar\\foobar2.php:34\n";
    $expectedFullEntryString .= "#, Flag 1, Flag 2\n";
    $expectedFullEntryString .= "#| msgid \"Previous msgid\"\n";
    $expectedFullEntryString .= "#| msgctxt \"Previous msgctxt\"\n";
    $expectedFullEntryString .= "msgctxt \"Context\"\n";
    $expectedFullEntryString .= "msgid \"source4\"\n";
    $expectedFullEntryString .= "msgstr \"target4\"\n\n";
    
    $this->assertEquals($expectedFullEntryString, $this->fullEntry->__toString());
	}
  
  /**
   * @covers POEntry::getComments
   */
  public function testGetComments()
  {
    $expectedFuzzyComments = array('flags' => array('fuzzy'));
    $this->assertEquals($expectedFuzzyComments, $this->fuzzyEntry->getComments());
  }
  
}

?>
