<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsStateRevoked.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateRevokedTest extends TestCase
{
	/**
	 * @var    JOAuthCredentialsStateRevoked  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Tests JOAuthCredentialsStateRevoked->authorize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateRevoked::authorize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testAuthorize()
	{
		$this->_instance->authorize(1);
	}

	/**
	 * Tests JOAuthCredentialsStateRevoked->convert()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateRevoked::convert
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testConvert()
	{
		$this->_instance->convert();
	}

	/**
	 * Tests JOAuthCredentialsStateRevoked->deny()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateRevoked::deny
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testDeny()
	{
		$this->_instance->deny();
	}

	/**
	 * Tests JOAuthCredentialsStateRevoked->initialize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateRevoked::initialize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testInitialize()
	{
		$this->_instance->initialize('bfe29ee16854c8cad995a7d08f908873', 'http://domain.com/callback');
	}

	/**
	 * Tests JOAuthCredentialsStateRevoked->revoke()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateRevoked::revoke
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

		$this->_instance = new JOAuthCredentialsStateRevoked($this->getMockDatabase());
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
