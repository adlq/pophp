<?php
require_once("POEntry.php");

/**
 * @covers POEntry
 */
class POEntryTest extends PHPUnit_Framework_TestCase
{
  /**
   * @covers 
   */
	public function testHasRightAttributes()
	{
		$this->assertClassHasAttribute('source', 'POEntry');
		$this->assertClassHasAttribute('target', 'POEntry');
		$this->assertClassHasAttribute('context', 'POEntry');
		$this->assertClassHasAttribute('comments', 'POEntry');
	}
	
  /**
   * @covers POEntry::__constructor
   * @test Constructor does the right thing
   */
	public function testNewEntry()
	{
		$entry = new POEntry('source', 'target');
		$this->assertEquals('source', $entry->getSource());
		$this->assertEquals('target', $entry->getTarget());
	}
}

?>
