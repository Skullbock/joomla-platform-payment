<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsStateTemporary.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateTemporaryTest extends TestCase
{
	/**
	 * @var    JOAuthCredentialsStateTemporary  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Provides test data for credential state authorization.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getAuthorizeData()
	{
		// AuthorizerId, CredentialsId, Key, Secret, ClientKey, Type, CallbackUrl, VerifierKey, ResourceOwnerId, ExpirationDate
		return array(
			array(42, 1, 'a17d986b2fc5f829c6e8f68b70fe04d8', '1488d5a4ea7533c48d51963d4affc6ee', 'bfe29ee16854c8cad995a7d08f908873', 0, 'http://domain.com/callback', '', 0, '0000-00-00 00:00:00'),
			array(456, 23, '536586d4ea9682dedfb8bbca5e5416d6', '1da38f375b605f9c4c8debfa9484a2fd', 'ef8e791492b932db27b836e5b8c01cf2', 0, 'http://domain.com/callback2', '', 0, '0000-00-00 00:00:00'),
			array(79567, 32, '8cbe98ecd548e16987f6d351926ab2ab', '8504027d9a5a1f0cf61918c25368da3c', 'f4091c4f6e0382ff553db0b98103e426', 0, 'http://domain.com/callback3', '', 0, '0000-00-00 00:00:00')
		);
	}

	/**
	 * Provides test data for credential state denial.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getDenyData()
	{
		// CredentialsId, Key, Secret, ClientKey, Type, CallbackUrl, VerifierKey, ResourceOwnerId, ExpirationDate
		return array(
			array(1, 'a17d986b2fc5f829c6e8f68b70fe04d8', '1488d5a4ea7533c48d51963d4affc6ee', 'bfe29ee16854c8cad995a7d08f908873', 0, 'http://domain.com/callback', '', 0, '0000-00-00 00:00:00'),
			array(23, '536586d4ea9682dedfb8bbca5e5416d6', '1da38f375b605f9c4c8debfa9484a2fd', 'ef8e791492b932db27b836e5b8c01cf2', 0, 'http://domain.com/callback2', '', 0, '0000-00-00 00:00:00'),
			array(32, '8cbe98ecd548e16987f6d351926ab2ab', '8504027d9a5a1f0cf61918c25368da3c', 'f4091c4f6e0382ff553db0b98103e426', 0, 'http://domain.com/callback3', '', 0, '0000-00-00 00:00:00')
		);
	}

	/**
	 * Tests JOAuthCredentialsStateTemporary->authorize()
	 *
	 * @param   integer  $authorizerId
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
	 * @covers        JOAuthCredentialsStateTemporary::authorize
	 * @dataProvider  getAuthorizeData
	 * @since         12.1
	 */
	public function testAuthorize($authorizerId, $credentialsId, $key, $secret, $clientKey, $type, $callbackUrl, $verifierKey, $resourceOwnerId, $expirationDate)
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

		// Keep a pre-converted clone for verifying values.
		$preAuthorized = clone($this->_instance);

		// Perform the authorization operation.
		$authorized = $this->_instance->authorize($authorizerId);

		// Assert that we converted the state.
		$this->assertInstanceOf('JOAuthCredentialsStateAuthorized', $authorized);
		$this->assertEquals(1, $authorized->type);

		// Assert the values that should be the same for both the authorized and pre-authorized state objects.
		$this->assertEquals($preAuthorized->credentials_id, $authorized->credentials_id);
		$this->assertEquals($preAuthorized->client_key, $authorized->client_key);
		$this->assertEquals($preAuthorized->expiration_date, $authorized->expiration_date);
		$this->assertEquals($preAuthorized->key, $authorized->key);
		$this->assertEquals($preAuthorized->secret, $authorized->secret);
		$this->assertEquals($preAuthorized->callback_url, $authorized->callback_url);

		// Assert the values that should be different for both the authorized and pre-authorized state objects.
		$this->assertNotEquals($preAuthorized->resource_owner_id, $authorized->resource_owner_id);
		$this->assertNotEquals($preAuthorized->verifier_key, $authorized->verifier_key);

		// Assert some values that should have been set in the authorization routine.
		$this->assertEquals($authorizerId, $authorized->resource_owner_id);
		$this->assertNotEmpty($authorized->verifier_key);
	}

	/**
	 * Tests JOAuthCredentialsStateTemporary->convert()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateTemporary::convert
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testConvert()
	{
		$this->_instance->convert();
	}

	/**
	 * Tests JOAuthCredentialsStateTemporary->deny()
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
	 * @covers        JOAuthCredentialsStateTemporary::deny
	 * @dataProvider  getDenyData
	 * @since         12.1
	 */
	public function testDeny($credentialsId, $key, $secret, $clientKey, $type, $callbackUrl, $verifierKey, $resourceOwnerId, $expirationDate)
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

		// Keep a pre-converted clone for verifying values.
		$preDenied = clone($this->_instance);

		// Perform the deny operation.
		$denied = $this->_instance->deny();

		// Assert that we converted the state.
		$this->assertInstanceOf('JOAuthCredentialsStateDenied', $denied);
		$this->assertEmpty($denied->type);

		// Assert the values that should be empty after the denial operation.
		$this->assertEmpty($denied->credentials_id);
		$this->assertEmpty($denied->key);
		$this->assertEmpty($denied->secret);
		$this->assertEmpty($denied->client_key);
		$this->assertEmpty($denied->callback_url);
		$this->assertEmpty($denied->verifier_key);
		$this->assertEmpty($denied->resource_owner_id);
		$this->assertEmpty($denied->expiration_date);
	}

	/**
	 * Tests JOAuthCredentialsStateTemporary->initialize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateTemporary::initialize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testInitialize()
	{
		$this->_instance->initialize('bfe29ee16854c8cad995a7d08f908873', 'http://domain.com/callback');
	}

	/**
	 * Tests JOAuthCredentialsStateTemporary->revoke()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateTemporary::revoke
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

		$this->_instance = new JOAuthCredentialsStateTemporary($this->getMockDatabase());
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
