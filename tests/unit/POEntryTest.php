<?php
require_once("POEntry.php");

/**
 * @covers POEntry
 */
class POEntryTest extends PHPUnit_Framework_TestCase
{
	private $entry;
	
	public function setUp()
	{
		$this->entry = new POEntry('source', 'target');
	}
	
  /**
   * @covers POEntry::__constructor
   * @test Constructor does the right thing
   */
	public function testNewEntry()
	{
		$this->assertEquals('source', $this->entry->getSource());
		$this->assertEquals('target', $this->entry->getTarget());
	}
	
	/**
	 * @covers POEntry::extractRelevantPath
	 */
	public function testExtractRelevantPath()
	{
		
		$this->assertEquals('foo/bar/foobar.php:123', 
				$this
				->entry
				->extractRelevantReference('/fooz/baz/foom/foo/bar/foobar.php:123', 'foom'));
		$this->assertEquals('foo\bar\foobar.php:321',
				$this
				->entry
				->extractRelevantReference('C:\fooz\baz\foom\foo\bar\foobar.php:321', 'foom'));
		$this->assertEmpty($this
				->entry
				->extractRelevantReference('/fooz/baz/foom/foo/bar/foobar.php:123', 'foob'));
		$this->assertEmpty($this
				->entry
				->extractRelevantReference('C:\fooz\baz\foom\foo\bar\foobar.php:321', 'foob'));
	}
	
	/**
	 * @covers POEntry::getReferences
	 */
	public function testGetReferences()
	{
		// Unix folder delimiter
		$this->entry->comments = array(
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
		), $this->entry->getReferences('foom'));
		
		// Win folder delimiter
		$this->entry->comments = array(
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
		), $this->entry->getReferences('foom'));
		
		// No references
		$this->entry->comments = array('reference' => array());
		
		$this->assertEmpty($this->entry->getReferences('foo'));
	}
}

?>
