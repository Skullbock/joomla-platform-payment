<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JServiceDefinitionParserResponse.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserResponseTest extends TestCase
{
	/**
	 * @var    JServiceDefinitionParserResponse  The object to be tested.
	 * @since  12.3
	 */
	private $_instance;

	/**
	 * Tests the parse method.
	 *
	 * @return  void
	 *
	 * @covers  JServiceDefinitionParserResponse::parse
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

		$this->_instance = new JServiceDefinitionParserResponse;
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
