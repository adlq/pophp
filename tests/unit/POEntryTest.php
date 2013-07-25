<?php
require_once("POEntry.php");

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
		$this->standardEntry = new POEntry('source1', 'target');
		$this->fuzzyEntry = new POEntry('source2', 'target', '', array('flag' => array('fuzzy')));
		$this->untranslatedEntry = new POEntry('source3', '');
		$this->fullEntry = new POEntry('source4', 'target4', 'Context',
			array(
				'translator' => array('Translator comment 1', 'Translator comment 2'),
				'extracted' => array('Extracted comment 1', 'Extracted comment 2'),
				'reference' => array(
					'C:\fooz\baz\foom\foo\bar\foobar1.php:321',
					'C:\fooz\baz\foom\foo\bar\foobar2.php:31',
					'C:\fooz\baz\foom\foo\bar\foobar2.php:34'),
				'flag' => array('Flag 1', 'Flag 2'),
				'previous' => array('Previous comment 1', 'Previous comment 2')
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
			'reference' => array(
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
			'reference' => array(
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
		$this->standardEntry->comments = array('reference' => array());

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
		$this->assertEquals($this->fuzzyEntry->__toString(), "#, fuzzy\nmsgid \"source2\"\nmsgstr \"target\"\n\n");
		$this->assertEquals($this->untranslatedEntry->__toString(),
		"msgid \"source3\"\nmsgstr \"\"\n\n");

		$this->assertEquals($this->fullEntry->__toString(),
			"# Translator comment 1\n
			# Translator comment 2\n
			#. Extracted comment 1\n
			#. Extracted comment 2\n
			#: C:\fooz\baz\foom\foo\bar\foobar1.php:321\n
			#: C:\fooz\baz\foom\foo\bar\foobar2.php:31\n
			#: C:\fooz\baz\foom\foo\bar\foobar2.php:34\n
			#, Flag 1, Flag 2\n
			#| Previous comment 1\n
			#| Previous comment 2\n
			msgctxt \"Context\"\n
			msgid \"source4\"\n
			msgstr \"target4\"\n");
		echo $this->fullEntry;
	}
}

?>
