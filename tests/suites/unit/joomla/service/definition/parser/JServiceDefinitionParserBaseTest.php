<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JServiceDefinitionParserBase.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserBaseTest extends TestCase
{
	/**
	 * @var    JServiceDefinitionParserBase  The object to be tested.
	 * @since  12.3
	 */
	private $_instance;

	/**
	 * Tests the parse method.
	 *
	 * @return  void
	 *
	 * @covers  JServiceDefinitionParserBase::parse
	 * @since   12.3
	 */
	public function testParse()
	{
		$this->markTestIncomplete('Not yet implemented.');
	}

	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	protected function setUp()
	{
		parent::setUp();

		// Create a stub for the abstract class.
		$stub = $this->getMockForAbstractClass('JServiceDefinitionParserBase');
		$stub->expects($this->any())->method('parse')->will($this->returnValue(array()));

		$this->_instance = $stub;
	}

	/**
	 * Cleans up the environment after running a test.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	protected function tearDown()
	{
		$this->_instance = null;

		parent::tearDown();
	}
}
