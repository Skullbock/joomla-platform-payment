<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsStateToken.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateTokenTest extends TestCase
{
	/**
	 * @var    JOAuthCredentialsStateToken  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Provides test data for credential state revokation.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getRevokeData()
	{
		// CredentialsId, Key, Secret, ClientKey, Type, CallbackUrl, VerifierKey, ResourceOwnerId, ExpirationDate
		return array(
			array(1, 'a17d986b2fc5f829c6e8f68b70fe04d8', '1488d5a4ea7533c48d51963d4affc6ee', 'bfe29ee16854c8cad995a7d08f908873', 0, 'http://domain.com/callback', '', 0, '0000-00-00 00:00:00'),
			array(23, '536586d4ea9682dedfb8bbca5e5416d6', '1da38f375b605f9c4c8debfa9484a2fd', 'ef8e791492b932db27b836e5b8c01cf2', 0, 'http://domain.com/callback2', '', 0, '0000-00-00 00:00:00'),
			array(32, '8cbe98ecd548e16987f6d351926ab2ab', '8504027d9a5a1f0cf61918c25368da3c', 'f4091c4f6e0382ff553db0b98103e426', 0, 'http://domain.com/callback3', '', 0, '0000-00-00 00:00:00')
		);
	}

	/**
	 * Tests JOAuthCredentialsStateToken->authorize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateToken::authorize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testAuthorize()
	{
		$this->_instance->authorize(1);
	}

	/**
	 * Tests JOAuthCredentialsStateToken->convert()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateToken::convert
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testConvert()
	{
		$this->_instance->convert();
	}

	/**
	 * Tests JOAuthCredentialsStateToken->deny()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateToken::deny
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testDeny()
	{
		$this->_instance->deny();
	}

	/**
	 * Tests JOAuthCredentialsStateToken->initialize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateToken::initialize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testInitialize()
	{
		$this->_instance->initialize('bfe29ee16854c8cad995a7d08f908873', 'http://domain.com/callback');
	}

	/**
	 * Tests JOAuthCredentialsStateToken->revoke()
	 *
	 * @param   integer  $credentialsId
	 * @param   string   $key
	 * @param   string   $secret
	 * @param   string   $clientKey
	 * @param   integer  $type
	 * @param   string   $callbackUrl
	 * @param   string   $verifierKey
	 * @param   integer  $resourceOwnerId
	 * @param   string   $expirationDate
	 *
	 * @return  void
	 *
	 * @covers        JOAuthCredentialsStateToken::revoke
	 * @dataProvider  getRevokeData
	 * @since         12.1
	 */
	public function testRevoke($credentialsId, $key, $secret, $clientKey, $type, $callbackUrl, $verifierKey, $resourceOwnerId, $expirationDate)
	{
		// Setup the current instance with supplied data.
		$this->_instance->credentials_id = $credentialsId;
		$this->_instance->key = $key;
		$this->_instance->secret = $secret;
		$this->_instance->client_key = $clientKey;
		$this->_instance->type = $type;
		$this->_instance->callback_url = $callbackUrl;
		$this->_instance->verifier_key = $verifierKey;
		$this->_instance->resource_owner_id = $resourceOwnerId;
		$this->_instance->expiration_date = $expirationDate;

		// Keep a pre-revoked clone for verifying values.
		$preRevoked = clone($this->_instance);

		// Perform the revoke operation.
		$revoked = $this->_instance->revoke();

		// Assert that we converted the state.
		$this->assertInstanceOf('JOAuthCredentialsStateRevoked', $revoked);
		$this->assertEmpty($revoked->type);

		// Assert the values that should be empty after the denial operation.
		$this->assertEmpty($revoked->credentials_id);
		$this->assertEmpty($revoked->key);
		$this->assertEmpty($revoked->secret);
		$this->assertEmpty($revoked->client_key);
		$this->assertEmpty($revoked->callback_url);
		$this->assertEmpty($revoked->verifier_key);
		$this->assertEmpty($revoked->resource_owner_id);
		$this->assertEmpty($revoked->expiration_date);
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

		$this->_instance = new JOAuthCredentialsStateToken($this->getMockDatabase());
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
