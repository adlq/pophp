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

class POFileTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->file = new POFile(POParserTest::testPo);
		$this->expectedEntries = POParserTest::getEntries();

		$this->expectedSourceStrings = array();
		foreach($this->expectedEntries as $entry)
			array_push($this->expectedSourceStrings, $entry->getSource());
	}

	/**
	 * @covers POFile::getEntries()
	 */
	public function testGetEntries()
	{

		$this->assertEquals($this->expectedEntries, $this->file->getEntries());
		$this->assertEquals(array($this->expectedEntries[1]), $this->file->getEntries(array('baz\foom\foo\bar\foobar3.php'), 'fooz'));
		$this->assertEquals(array($this->expectedEntries[0]), $this->file->getEntries(array('baz\foom\foo\bar\foobar2.php'), 'fooz'));
	}

	/**
	 * @covers POFile::getSourceStrings()
	 */
	public function testGetSourceStrings()
	{
		$this->assertEquals($this->expectedSourceStrings, $this->file->getSourceStrings());

		$this->assertEquals(array($this->expectedSourceStrings[1]), $this->file->getSourceStrings(array('baz\foom\foo\bar\foobar3.php'), 'fooz'));
		$this->assertEquals(array($this->expectedSourceStrings[0]), $this->file->getSourceStrings(array('baz\foom\foo\bar\foobar2.php'), 'fooz'));
	}

	/**
	 * @covers POFile::getFuzzyStrings()
	 */
	public function testGetFuzzyStrings()
	{
		$this->assertEquals(array($this->expectedSourceStrings[2]), $this->file->getFuzzyStrings());
	}

	/**
	 * @covers POFile::getUntranslatedStrings()
	 */
	public function testGetUntranslatedStrings()
	{
		$this->assertEquals(array($this->expectedSourceStrings[3]), $this->file->getUntranslatedStrings());
	}

	/**
	 * @covers POFile::getTranslatedEntries()
	 */
	public function testGetTranslatedEntries()
	{
		$this->assertEquals(array_slice($this->expectedEntries, 0, 3), $this->file->getTranslatedEntries());
	}
}

?>
