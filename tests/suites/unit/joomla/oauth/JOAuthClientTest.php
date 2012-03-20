<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

/**
 * Test case class for JOAuthClient.
 *
 * @package     Joomla.UnitTest
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthClientTest extends TestCaseDatabase
{
	/**
	 * @var    JOAuthClient  The instance to test.
	 * @since  12.1
	 */
	private $_instance;

	/**
	 * Provides test data for client loading.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getLoadData()
	{
		// ClientId, Key, Alias, Secret, Title
		return array(
			array(1, 'bfe29ee16854c8cad995a7d08f908873', 'client1', 'a9350571fdf39840bcb8e9b86bdd855d', 'Client Application 1'),
			array(2, '179e78686b04e5ac92c1a4066bb68937', 'client2', '97be26b1e9d662d12c904c538319dad7', 'Client Application 2')
		);
	}

	/**
	 * Tests JOAuthClient->__get()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::__get
	 * @since   12.1
	 */
	public function test__get()
	{
		// Prime the properties array.
		TestReflection::setValue($this->_instance, '_properties', array('foo' => 'bar'));

		// Since 'foo' = 'bar' in the properties array we expect 'bar'.
		$this->assertEquals('bar', $this->_instance->__get('foo'));

		// Since 'bar' is not in the properties array we expect null.
		$this->assertEquals(null, $this->_instance->__get('bar'));
	}

	/**
	 * Tests JOAuthClient->__set()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::__set
	 * @since   12.1
	 */
	public function test__set()
	{
		// Should be ignored.
		$this->_instance->__set('foo', 'bar');
		$this->_instance->__set('clients_id', 7);

		// Should be added.
		$this->_instance->__set('client_id', 42);
		$this->_instance->__set('key', '2893798dss989a7dsf9z8');

		// Build the expected array.
		$expected = array(
			'client_id' => 42,
			'alias' => '',
			'key' => '2893798dss989a7dsf9z8',
			'secret' => '',
			'title' => ''
		);

		// Prime the properties array.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, '_properties'));
	}

	/**
	 * Tests JOAuthClient->create()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::create
	 * @since   12.1
	 */
	public function testCreate()
	{
		// Setup the instance data for updating the database.
		$this->_instance->alias = 'my-new-client';
		$this->_instance->key = '29f5d8aeda6c98e4dcbe0720022b4c8c';
		$this->_instance->secret = '997ba64a212e3bb5ed82687efa2eb056';
		$this->_instance->title = 'My New Client';

		// Create the row in the database.
		$this->_instance->create();

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_clients');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E01.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthClient->create()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::create
	 * @since   12.1
	 */
	public function testCreateWithPrimaryKey()
	{
		// Setup the instance data for updating the database.
		$this->_instance->client_id = 3;
		$this->_instance->alias = 'my-new-client';
		$this->_instance->key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->secret = 'abcf9654c43218e94bed3815b5346590';
		$this->_instance->title = 'My New Client';

		// Create the row in the database.
		$result = $this->_instance->create();

		// Verify that the create operation failed.
		$this->assertFalse($result);

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_clients');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E02.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthClient->delete()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::delete
	 * @since   12.1
	 */
	public function testDelete()
	{
		// Delete client where id = 1.
		$this->_instance->client_id = 1;
		$this->_instance->delete();

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_clients');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E03.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthClient->load()
	 *
	 * @param   integer  $clientId
	 * @param   string   $key
	 * @param   string   $alias
	 * @param   string   $secret
	 * @param   string   $title
	 *
	 * @return  void
	 *
	 * @covers        JOAuthClient::load
	 * @dataProvider  getLoadData
	 * @since         12.1
	 */
	public function testLoad($clientId, $key, $alias, $secret, $title)
	{
		$this->_instance->load($clientId);

		// Assert the values that should be loaded from the database.
		$this->assertEquals($clientId, $this->_instance->client_id);
		$this->assertEquals($key, $this->_instance->key);
		$this->assertEquals($alias, $this->_instance->alias);
		$this->assertEquals($secret, $this->_instance->secret);
		$this->assertEquals($title, $this->_instance->title);
	}

	/**
	 * Tests JOAuthClient->loadByKey()
	 *
	 * @param   integer  $clientId
	 * @param   string   $key
	 * @param   string   $alias
	 * @param   string   $secret
	 * @param   string   $title
	 *
	 * @return  void
	 *
	 * @covers        JOAuthClient::loadByKey
	 * @dataProvider  getLoadData
	 * @since         12.1
	 */
	public function testLoadByKey($clientId, $key, $alias, $secret, $title)
	{
		$this->_instance->loadByKey($key);

		// Assert the values that should be loaded from the database.
		$this->assertEquals($clientId, $this->_instance->client_id);
		$this->assertEquals($key, $this->_instance->key);
		$this->assertEquals($alias, $this->_instance->alias);
		$this->assertEquals($secret, $this->_instance->secret);
		$this->assertEquals($title, $this->_instance->title);
	}

	/**
	 * Tests JOAuthClient->update()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::update
	 * @since   12.1
	 */
	public function testUpdate()
	{
		// Setup the instance data for updating the database.
		$this->_instance->client_id = 1;
		$this->_instance->alias = 'client1';
		$this->_instance->key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->secret = 'a9350571fdf39840bcb8e9b86bdd855d';
		$this->_instance->title = 'Client Application 1';

		// Change two fields.
		$this->_instance->title = 'Renamed Client Application 1';
		$this->_instance->key = '235f9654c4ee18e94bed3815b5346590';

		// Update the row in the database.
		$this->_instance->update();

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_clients');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E04.xml');

		// Verify that the data sets are equal.
		$this->assertDataSetsEqual($expected, $actual);
	}

	/**
	 * Tests JOAuthClient->update()
	 *
	 * @return  void
	 *
	 * @covers  JOAuthClient::update
	 * @since   12.1
	 */
	public function testUpdateWithoutPrimaryKey()
	{
		// Setup the instance data for updating the database.
		$this->_instance->alias = 'client1';
		$this->_instance->key = 'bfe29ee16854c8cad995a7d08f908873';
		$this->_instance->secret = 'a9350571fdf39840bcb8e9b86bdd855d';
		$this->_instance->title = 'Client Application 1';

		// Change two fields.
		$this->_instance->title = 'Renamed Client Application 1';
		$this->_instance->key = '235f9654c4ee18e94bed3815b5346590';

		// Update the row in the database.
		$result = $this->_instance->update();

		// Verify that the update operation failed.
		$this->assertFalse($result);

		// Get the actual dataset from the database.
		$actual = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
		$actual->addTable('jos_oauth_clients');

		// Get the expected database from XML.
		$expected = $this->createXMLDataSet(__DIR__.'/stubs/S01E02.xml');

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

		$this->_instance = new JOAuthClient(self::$driver);
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
