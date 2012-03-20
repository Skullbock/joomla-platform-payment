<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentials.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsTest extends TestCaseDatabase
{
	/**
	 * @var    JOAuthCredentials  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Provides test data for credential loading.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getLoadData()
	{
		// CredentialsId, Key, Secret, ClientKey, Type, CallbackUrl, VerifierKey, ResourceOwnerId, ExpirationDate
		return array(
			array(1, 'a17d986b2fc5f829c6e8f68b70fe04d8', '1488d5a4ea7533c48d51963d4affc6ee', 'bfe29ee16854c8cad995a7d08f908873', 0, 'http://domain.com/callback', '', '', '0000-00-00 00:00:00'),
			array(2, 'db3b08ea17a7efb0a74d579ea2cbd8c8', 'fec21a63737fea9d5a6a335beca51adf', 'bfe29ee16854c8cad995a7d08f908873', 2, '', '', 2, '0000-00-00 00:00:00'),
			array(3, 'e7001c4a1ab924a7236cc159c86e63a1', '73fc7a0bb4a7e089ffdda4548d19089d', '4832940dbabf8443c5488a9bfc1c49eb', 1, '', 'e6e62e0104cea39bc72de67b9a678eb7', 2, '0000-00-00 00:00:00')
		);
	}

	/**
	 * Tests JOAuthCredentials->__construct()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::__construct
	 * @since   12.1
	 */
	public function test__construct()
	{
		$this->assertInstanceOf('JOAuthCredentialsStateNew', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Tests JOAuthCredentials->authorize()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::authorize
	 * @since   12.1
	 */
	public function testAuthorize()
	{
		// Create a stub for the JOAuthCredentials class.
		$stub = $this->getMock('JOAuthCredentialsState');

		// Configure the stub.
		$stub->expects($this->any())
			->method('authorize')
			->will($this->returnValue('authorized'));

		// Set the stub as the object's state.
		TestReflection::setValue($this->_instance, '_state', $stub);

		$this->_instance->authorize(42);

		$this->assertEquals('authorized', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Tests JOAuthCredentials->convert()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::convert
	 * @since   12.1
	 */
	public function testConvert()
	{
		// Create a stub for the JOAuthCredentials class.
		$stub = $this->getMock('JOAuthCredentialsState');

		// Configure the stub.
		$stub->expects($this->any())
			->method('convert')
			->will($this->returnValue('converted'));

		// Set the stub as the object's state.
		TestReflection::setValue($this->_instance, '_state', $stub);

		$this->_instance->convert();

		$this->assertEquals('converted', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Tests JOAuthCredentials->deny()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::deny
	 * @since   12.1
	 */
	public function testDeny()
	{
		// Create a stub for the JOAuthCredentials class.
		$stub = $this->getMock('JOAuthCredentialsState');

		// Configure the stub.
		$stub->expects($this->any())
			->method('deny')
			->will($this->returnValue('denied'));

		// Set the stub as the object's state.
		TestReflection::setValue($this->_instance, '_state', $stub);

		$this->_instance->deny();

		$this->assertEquals('denied', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Tests JOAuthCredentials->getCallbackUrl()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getCallbackUrl
	 * @since   12.1
	 */
	public function testGetCallbackUrl()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->callback_url = 'http://domain.tld/path';

		$this->assertEquals('http://domain.tld/path', $this->_instance->getCallbackUrl());
	}

	/**
	 * Tests JOAuthCredentials->getClientKey()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getClientKey
	 * @since   12.1
	 */
	public function testGetClientKey()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->client_key = 'myClientKey';

		$this->assertEquals('myClientKey', $this->_instance->getClientKey());
	}

	/**
	 * Tests JOAuthCredentials->getKey()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getKey
	 * @since   12.1
	 */
	public function testGetKey()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->key = '123abc';

		$this->assertEquals('123abc', $this->_instance->getKey());
	}

	/**
	 * Tests JOAuthCredentials->getResourceOwnerId()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getResourceOwnerId
	 * @since   12.1
	 */
	public function testGetResourceOwnerId()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->resource_owner_id = 7;

		$this->assertEquals(7, $this->_instance->getResourceOwnerId());
	}

	/**
	 * Tests JOAuthCredentials->getSecret()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getSecret
	 * @since   12.1
	 */
	public function testGetSecret()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->secret = 'mySecureSecret';

		$this->assertEquals('mySecureSecret', $this->_instance->getSecret());
	}

	/**
	 * Tests JOAuthCredentials->getType()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getType
	 * @since   12.1
	 */
	public function testGetType()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->type = 42;

		$this->assertEquals(42, $this->_instance->getType());
	}

	/**
	 * Tests JOAuthCredentials->getVerifierKey()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::getVerifierKey
	 * @since   12.1
	 */
	public function testGetVerifierKey()
	{
		// Set the value in the credentials state object.
		TestReflection::getValue($this->_instance, '_state')->verifier_key = 'abc123';

		$this->assertEquals('abc123', $this->_instance->getVerifierKey());
	}

	/**
	 * Tests JOAuthCredentials->initialize()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::initialize
	 * @since   12.1
	 */
	public function testInitialize()
	{
		// Create a stub for the JOAuthCredentials class.
		$stub = $this->getMock('JOAuthCredentialsState');

		// Configure the stub.
		$stub->expects($this->any())
			->method('initialize')
			->will($this->returnValue('initialized'));

		// Set the stub as the object's state.
		TestReflection::setValue($this->_instance, '_state', $stub);

		$this->_instance->initialize('a17d986b2fc5f829c6e8f68b70fe04d8', 'http://domain.com/callback');

		$this->assertEquals('initialized', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Tests JOAuthCredentials->load()
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
	 * @covers        JOAuthCredentials::load
	 * @dataProvider  getLoadData
	 * @since         12.1
	 */
	public function testLoad($credentialsId, $key, $secret, $clientKey, $type, $callbackUrl, $verifierKey, $resourceOwnerId, $expirationDate)
	{
		$this->_instance->load($key);

		// Assert the values that should be loaded from the database.
		$this->assertEquals($credentialsId, TestReflection::getValue($this->_instance, '_state')->credentials_id);
		$this->assertEquals($key, TestReflection::getValue($this->_instance, '_state')->key);
		$this->assertEquals($secret, TestReflection::getValue($this->_instance, '_state')->secret);
		$this->assertEquals($clientKey, TestReflection::getValue($this->_instance, '_state')->client_key);
		$this->assertEquals($type, TestReflection::getValue($this->_instance, '_state')->type);
		$this->assertEquals($callbackUrl, TestReflection::getValue($this->_instance, '_state')->callback_url);
		$this->assertEquals($verifierKey, TestReflection::getValue($this->_instance, '_state')->verifier_key);
		$this->assertEquals($resourceOwnerId, TestReflection::getValue($this->_instance, '_state')->resource_owner_id);
		$this->assertEquals($expirationDate, TestReflection::getValue($this->_instance, '_state')->expiration_date);
	}

	/**
	 * Tests JOAuthCredentials->load()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::load
	 * @since   12.1
	 */
	public function testLoadWithInvalidKey()
	{
		// Load something that doesn't exist.
		$this->_instance->load('abc123');

		// Ensure that the credentials loaded are empty and new.
		$this->assertInstanceOf('JOAuthCredentialsStateNew', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Tests JOAuthCredentials->revoke()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentials::revoke
	 * @since   12.1
	 */
	public function testRevoke()
	{
		// Create a stub for the JOAuthCredentials class.
		$stub = $this->getMock('JOAuthCredentialsState');

		// Configure the stub.
		$stub->expects($this->any())
			->method('revoke')
			->will($this->returnValue('revoked'));

		// Set the stub as the object's state.
		TestReflection::setValue($this->_instance, '_state', $stub);

		$this->_instance->revoke();

		$this->assertEquals('revoked', TestReflection::getValue($this->_instance, '_state'));
	}

	/**
	 * Gets the data set to be loaded into the database during setup.
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_XmlDataSet
	 *
	 * @since   12.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/stubs/S02.xml');
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

		$this->_instance = new JOAuthCredentials(self::$driver);
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
