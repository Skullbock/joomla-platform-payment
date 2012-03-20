<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Credentials state class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
abstract class JOAuthCredentialsState
{
	/**
	 * @var    JDatabaseDriver  Driver for persisting the client object.
	 * @since  12.1
	 */
	protected $db;

	/**
	 * @var    array  Credential property array.
	 * @since  12.1
	 */
	protected $properties = array(
		'credentials_id' => '',
		'callback_url' => '',
		'client_key' => '',
		'expiration_date' => '',
		'key' => '',
		'resource_owner_id' => '',
		'secret' => '',
		'type' => '',
		'verifier_key' => ''
	);

	/**
	 * Object constructor.
	 *
	 * @param   JDatabaseDriver  $db          The database driver to use when persisting the object.
	 * @param   array            $properties  A set of properties with which to prime the object.
	 *
	 * @codeCoverageIgnore
	 * @since   12.1
	 */
	public function __construct(JDatabaseDriver $db = null, array $properties = null)
	{
		// Setup the database object.
		$this->db = $db ? $db : JFactory::getDbo();

		// Iterate over any input properties and bind them to the object.
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				$this->$k = $v;
			}
		}
	}

	/**
	 * Method to get a property value.
	 *
	 * @param   string  $p  The name of the property for which to return the value.
	 *
	 * @return  mixed  The property value for the given property name.
	 *
	 * @since   12.1
	 */
	public function __get($p)
	{
		if (isset($this->properties[$p]))
		{
			return $this->properties[$p];
		}
	}

	/**
	 * Method to set a value for a property.
	 *
	 * @param   string  $p  The name of the property for which to set the value.
	 * @param   mixed   $v  The property value to set.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function __set($p, $v)
	{
		if (isset($this->properties[$p]))
		{
			$this->properties[$p] = $v;
		}
	}

	/**
	 * Method to authorize the credentials.  This will persist a temporary credentials set to be authorized by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	abstract public function authorize($resourceOwnerId);

	/**
	 * Method to convert a set of authorized credentials to token credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	abstract public function convert();

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	abstract public function deny();

	/**
	 * Method to initialize the credentials.  This will persist a temporary credentials set to be authorized by
	 * a resource owner.
	 *
	 * @param   string  $clientKey    The key of the client requesting the temporary credentials.
	 * @param   string  $callbackUrl  The callback URL to set for the temporary credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	abstract public function initialize($clientKey, $callbackUrl);

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	abstract public function revoke();

	/**
	 * Method to create the credentials in the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.1
	 */
	protected function create()
	{
		// Setup the object to be inserted.
		$object = (object) $this->properties;

		// Can't insert something that already has an ID.
		if ($object->credentials_id)
		{
			return false;
		}

		// Ensure we don't have an id to insert... use the auto-incrementor instead.
		unset($object->credentials_id);

		// Insert the object into the database.
		$success = $this->db->insertObject('#__oauth_credentials', $object, 'credentials_id');

		if ($success)
		{
			$this->properties['credentials_id'] = (int) $object->credentials_id;
		}

		return $success;
	}

	/**
	 * Method to delete the credentials from the database.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function delete()
	{
		// Build the query to delete the row from the database.
		$query = $this->db->getQuery(true);
		$query->delete('#__oauth_credentials')
			->where('credentials_id = ' . (int) $this->properties['credentials_id']);

		// Set and execute the query.
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * Generate a random (and optionally unique) key.
	 *
	 * @param   boolean  $unique  True to enforce uniqueness for the key.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	protected function randomKey($unique = false)
	{
		$str = md5(uniqid(rand(), true));
		if ($unique)
		{
			list ($u, $s) = explode(' ', microtime());
			$str .= dechex($u) . dechex($s);
		}
		return $str;
	}

	/**
	 * Method to update the credentials in the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.1
	 */
	protected function update()
	{
		// Setup the object to be inserted.
		$object = (object) $this->properties;

		if (!$object->credentials_id)
		{
			return false;
		}
		else
		{
			$object->credentials_id = (int) $object->credentials_id;
		}

		// Update the object into the database.
		return $this->db->updateObject('#__oauth_credentials', $object, 'credentials_id');
	}
}
