<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsStateDenied.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateDeniedTest extends TestCase
{
	/**
	 * @var    JOAuthCredentialsStateDenied  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Tests JOAuthCredentialsStateDenied->authorize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateDenied::authorize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testAuthorize()
	{
		$this->_instance->authorize(1);
	}

	/**
	 * Tests JOAuthCredentialsStateDenied->convert()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateDenied::convert
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testConvert()
	{
		$this->_instance->convert();
	}

	/**
	 * Tests JOAuthCredentialsStateDenied->deny()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateDenied::deny
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testDeny()
	{
		$this->_instance->deny();
	}

	/**
	 * Tests JOAuthCredentialsStateDenied->initialize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateDenied::initialize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testInitialize()
	{
		$this->_instance->initialize('bfe29ee16854c8cad995a7d08f908873', 'http://domain.com/callback');
	}

	/**
	 * Tests JOAuthCredentialsStateDenied->revoke()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateDenied::revoke
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

		$this->_instance = new JOAuthCredentialsStateDenied($this->getMockDatabase());
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
