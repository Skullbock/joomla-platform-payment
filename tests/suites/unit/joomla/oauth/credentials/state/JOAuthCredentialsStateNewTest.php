<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsStateNew.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateNewTest extends TestCase
{
	/**
	 * @var    JOAuthCredentialsStateNew  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Provides test data for credential state initialization.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getInitializeData()
	{
		// ClientKey, CallbackUrl
		return array(
			array('bfe29ee16854c8cad995a7d08f908873', 'http://domain.com/callback'),
			array('ef8e791492b932db27b836e5b8c01cf2', 'http://domain.com/callback2',),
			array('f4091c4f6e0382ff553db0b98103e426', 'http://domain.com/callback3',)
		);
	}

	/**
	 * Tests JOAuthCredentialsStateNew->authorize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateNew::authorize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testAuthorize()
	{
		$this->_instance->authorize(1);
	}

	/**
	 * Tests JOAuthCredentialsStateNew->convert()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateNew::convert
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testConvert()
	{
		$this->_instance->convert();
	}

	/**
	 * Tests JOAuthCredentialsStateNew->deny()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateNew::deny
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testDeny()
	{
		$this->_instance->deny();
	}

	/**
	 * Tests JOAuthCredentialsStateNew->initialize()
	 *
	 * @param   string   $clientKey
	 * @param   string   $callbackUrl
	 *
	 * @return  void
	 *
	 * @covers        JOAuthCredentialsStateNew::initialize
	 * @dataProvider  getInitializeData
	 * @since         12.1
	 */
	public function testInitialize($clientKey, $callbackUrl)
	{
		// Perform the initialization operation.
		$temporary = $this->_instance->initialize($clientKey, $callbackUrl);

		// Assert that we initialized the state.
		$this->assertInstanceOf('JOAuthCredentialsStateTemporary', $temporary);
		$this->assertEquals('0', $temporary->type);

		// Assert the values we gave it are OK.
		$this->assertEquals($clientKey, $temporary->client_key);
		$this->assertEquals($callbackUrl, $temporary->callback_url);

		// Assert that some values should be empty.
		$this->assertEmpty($temporary->resource_owner_id);
		$this->assertEmpty($temporary->verifier_key);
		$this->assertEmpty($temporary->expiration_date);

		// Assert that some values are not empty.
		$this->assertNotEmpty($temporary->key);
		$this->assertNotEmpty($temporary->secret);
	}

	/**
	 * Tests JOAuthCredentialsStateNew->revoke()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateNew::revoke
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testRevoke()
	{
		$this->_instance->revoke();
	}

	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->_instance = new JOAuthCredentialsStateNew($this->getMockDatabase());
	}

	/**
	 * Cleans up the environment after running a test.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function tearDown()
	{
		$this->_instance = null;

		parent::tearDown();
	}
}
