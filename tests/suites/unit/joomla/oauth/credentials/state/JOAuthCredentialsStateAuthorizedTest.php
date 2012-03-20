<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsStateAuthorized.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateAuthorizedTest extends TestCase
{
	/**
	 * @var    JOAuthCredentialsStateAuthorized  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Provides test data for credential state conversion.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getConvertData()
	{
		// CredentialsId, Key, Secret, ClientKey, Type, CallbackUrl, VerifierKey, ResourceOwnerId, ExpirationDate
		return array(
			array(1, 'a17d986b2fc5f829c6e8f68b70fe04d8', '1488d5a4ea7533c48d51963d4affc6ee', 'bfe29ee16854c8cad995a7d08f908873', 1, 'http://domain.com/callback', '235f9654c4ee18e94bed3815b5346590', 1, '0000-00-00 00:00:00'),
			array(23, '536586d4ea9682dedfb8bbca5e5416d6', '1da38f375b605f9c4c8debfa9484a2fd', 'ef8e791492b932db27b836e5b8c01cf2', 1, 'http://domain.com/callback2', '1f653589161145de1d92647ca0a2517f', 7, '0000-00-00 00:00:00'),
			array(23847, '8cbe98ecd548e16987f6d351926ab2ab', '8504027d9a5a1f0cf61918c25368da3c', 'f4091c4f6e0382ff553db0b98103e426', 1, 'http://domain.com/callback3', 'c63de56084bb60c56fef3968985394cc', 42, '0000-00-00 00:00:00')
		);
	}

	/**
	 * Tests JOAuthCredentialsStateAuthorized->authorize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateAuthorized::authorize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testAuthorize()
	{
		$this->_instance->authorize(1);
	}

	/**
	 * Tests JOAuthCredentialsStateAuthorized->convert()
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
	 * @covers        JOAuthCredentialsStateAuthorized::convert
	 * @dataProvider  getConvertData
	 * @since         12.1
	 */
	public function testConvert($credentialsId, $key, $secret, $clientKey, $type, $callbackUrl, $verifierKey, $resourceOwnerId, $expirationDate)
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
		$preConverted = clone($this->_instance);

		// Perform the revoking operation.
		$converted = $this->_instance->convert();

		// Assert that we converted the state.
		$this->assertInstanceOf('JOAuthCredentialsStateToken', $converted);
		$this->assertEquals(2, $converted->type);

		// Assert the values that should be the same for both the database and the returned state object.
		$this->assertEquals($preConverted->credentials_id, $converted->credentials_id);
		$this->assertEquals($preConverted->client_key, $converted->client_key);
		$this->assertEquals($preConverted->resource_owner_id, $converted->resource_owner_id);
		$this->assertEquals($preConverted->expiration_date, $converted->expiration_date);

		// Assert the values that should not be the same for both the database and returned state object.
		$this->assertNotEquals($preConverted->key, $converted->key);
		$this->assertNotEquals($preConverted->secret, $converted->secret);

		// Assert that values which should have been cleared are actually cleared during conversion.
		$this->assertEquals('', $converted->verifier_key);
		$this->assertEquals('', $converted->callback_url);
	}

	/**
	 * Tests JOAuthCredentialsStateAuthorized->deny()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateAuthorized::deny
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testDeny()
	{
		$this->_instance->deny();
	}

	/**
	 * Tests JOAuthCredentialsStateAuthorized->initialize()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateAuthorized::initialize
	 * @expectedException  LogicException
	 * @since              12.1
	 */
	public function testInitialize()
	{
		$this->_instance->initialize('bfe29ee16854c8cad995a7d08f908873', 'http://domain.com/callback');
	}

	/**
	 * Tests JOAuthCredentialsStateAuthorized->revoke()
	 *
	 * @return  void
	 *
	 * @covers             JOAuthCredentialsStateAuthorized::revoke
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

		$this->_instance = new JOAuthCredentialsStateAuthorized($this->getMockDatabase());
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
