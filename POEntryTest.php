<?php
require_once("POEntry.php");

class POEntryTest extends PHPUnit_Framework_TestCase
{
	public function testHasRightAttributes()
	{
		$this->assertClassHasAttribute('source', 'POEntry');
		$this->assertClassHasAttribute('target', 'POEntry');
		$this->assertClassHasAttribute('context', 'POEntry');
		$this->assertClassHasAttribute('comments', 'POEntry');
	}
	
	public function testNewEntry()
	{
		$entry = new POEntry('source', 'target');
		$this->assertEquals('source', $entry->getSource());
		$this->assertEquals('target', $entry->getTarget());
	}
}

?>