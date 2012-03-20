<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthCredentialsState.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateTest extends TestCaseDatabase
{
	/**
	 * @var    JOAuthCredentialsState  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Tests JOAuthCredentialsState->__get()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::__get
	 * @since   12.1
	 */
	public function test__get()
	{
		// Prime the properties array.
		TestReflection::setValue($this->_instance, 'properties', array('foo' => 'bar'));

		// Since 'foo' = 'bar' in the properties array we expect 'bar'.
		$this->assertEquals('bar', $this->_instance->__get('foo'));

		// Since 'bar' is not in the properties array we expect null.
		$this->assertEquals(null, $this->_instance->__get('bar'));
	}

	/**
	 * Tests JOAuthCredentialsState->__set()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::__set
	 * @since   12.1
	 */
	public function test__set()
	{
		// Should be ignored.
		$this->_instance->__set('foo', 'bar');
		$this->_instance->__set('credential_id', 7);

		// Should be added.
		$this->_instance->__set('credentials_id', 42);
		$this->_instance->__set('client_key', '2893798dss989a7dsf9z8');

		// Build the expected array.
		$expected = array(
			'credentials_id' => 42,
			'callback_url' => '',
			'client_key' => '2893798dss989a7dsf9z8',
			'expiration_date' => '',
			'key' => '',
			'resource_owner_id' => '',
			'secret' => '',
			'type' => '',
			'verifier_key' => ''
		);

		// Prime the properties array.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'properties'));
	}

	/**
	 * Tests JOAuthCredentialsState->create()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::create
	 * @since   12.1
	 */
	public function testCreate()
	{
		// Setup the instance data for updating the database.
		$this->_instance->key = '12329ee16854c8cad654a7d08f908873';
		$this->_instance->secret = 'abcf9654c43218e94bed3815b5346590';
		$this->_instance->client_key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->type = 0;
		$this->_instance->callback_url = 'http://domain.com/callback';
		$this->_instance->verifier_key = '';
		$this->_instance->resource_owner_id = '';
		$this->_instance->expiration_date = '0000-00-00 00:00:00';

		// Create the row in the database.
		TestReflection::invoke($this->_instance, 'create');

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_credentials');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E01.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthCredentialsState->create()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::create
	 * @since   12.1
	 */
	public function testCreateWithPrimaryKey()
	{
		// Setup the instance data for updating the database.
		$this->_instance->credentials_id = 3;
		$this->_instance->key = '12329ee16854c8cad654a7d08f908873';
		$this->_instance->secret = 'abcf9654c43218e94bed3815b5346590';
		$this->_instance->client_key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->type = 0;
		$this->_instance->callback_url = 'http://domain.com/callback';
		$this->_instance->verifier_key = '';
		$this->_instance->resource_owner_id = '';
		$this->_instance->expiration_date = '0000-00-00 00:00:00';

		// Create the row in the database.
		$result = TestReflection::invoke($this->_instance, 'create');

		// Verify that the create operation failed.
		$this->assertFalse($result);

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_credentials');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E04.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthCredentialsState->delete()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::delete
	 * @since   12.1
	 */
	public function testDelete()
	{
		// Delete credentials where id = 1.
		$this->_instance->credentials_id = 1;
		TestReflection::invoke($this->_instance, 'delete');

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_credentials');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E02.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthCredentialsState->randomKey()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::randomKey
	 * @since   12.1
	 */
	public function testRandomKey()
	{
		// Initialize the old key to an empty string.
		$key = '';

		// Run the test 20 times.
		for ($i = 0, $n = 20; $i < 20; $i++)
		{
			$old = $key;

			// Generate a random key.
			$key = TestReflection::invoke($this->_instance, 'randomKey');

			// Ensure the last generated key is not the same as the current one.
			$this->assertNotEquals($old, $key);
		}
	}

	/**
	 * Tests JOAuthCredentialsState->randomKey()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::randomKey
	 * @since   12.1
	 */
	public function testRandomKeyWithUniqueFlag()
	{
		// Initialize the old key to an empty string.
		$key = '';

		// Run the test 20 times.
		for ($i = 0, $n = 20; $i < 20; $i++)
		{
		$old = $key;

		// Generate a random key.
		$key = TestReflection::invoke($this->_instance, 'randomKey', array(true));

		// Ensure the last generated key is not the same as the current one.
		$this->assertNotEquals($old, $key);
		}
		}

	/**
	 * Tests JOAuthCredentialsState->update()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::update
	 * @since   12.1
	 */
	public function testUpdate()
	{
		// Setup the instance data for updating the database.
		$this->_instance->credentials_id = 1;
		$this->_instance->key = 'a17d986b2fc5f829c6e8f68b70fe04d8';
		$this->_instance->secret = '1488d5a4ea7533c48d51963d4affc6ee';
		$this->_instance->client_key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->type = 0;
		$this->_instance->callback_url = 'http://domain.com/callback';
		$this->_instance->verifier_key = '';
		$this->_instance->resource_owner_id = 1;
		$this->_instance->expiration_date = '0000-00-00 00:00:00';

		// Change two fields.
		$this->_instance->type = 1;
		$this->_instance->verifier_key = '235f9654c4ee18e94bed3815b5346590';

		// Update the row in the database.
		TestReflection::invoke($this->_instance, 'update');

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_credentials');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E03.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthCredentialsState->update()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthCredentialsState::update
	 * @since   12.1
	 */
	public function testUpdateWithoutPrimaryKey()
	{
		// Setup the instance data for updating the database.
		$this->_instance->key = 'a17d986b2fc5f829c6e8f68b70fe04d8';
		$this->_instance->secret = '1488d5a4ea7533c48d51963d4affc6ee';
		$this->_instance->client_key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->type = 0;
		$this->_instance->callback_url = 'http://domain.com/callback';
		$this->_instance->verifier_key = '';
		$this->_instance->resource_owner_id = 1;
		$this->_instance->expiration_date = '0000-00-00 00:00:00';

		// Change two fields.
		$this->_instance->type = 1;
		$this->_instance->verifier_key = '235f9654c4ee18e94bed3815b5346590';

		// Update the row in the database.
		$result = TestReflection::invoke($this->_instance, 'update');

		// Verify that the update operation failed.
		$this->assertFalse($result);

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_credentials');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E04.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
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
		return $this->createXMLDataSet(__DIR__ . '/stubs/S01.xml');
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

		$this->_instance = $this->getMockForAbstractClass('JOAuthCredentialsState', array(self::$driver));
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
