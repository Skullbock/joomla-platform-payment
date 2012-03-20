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
 * OAuth Credentials base class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentials
{
	/**
	 * @var    integer  Indicates temporary credentials.  These are ready to be authorized.
	 * @since  12.1
	 */
	const TEMPORARY = 0;

	/**
	 * @var    integer  Indicates authorized temporary credentials.  These are ready to be converted to token credentials.
	 * @since  12.1
	 */
	const AUTHORIZED = 1;

	/**
	 * @var    integer  Indicates token credentials.  These are ready to be used for accessing protected resources.
	 * @since  12.1
	 */
	const TOKEN = 2;

	/**
	 * @var    JDatabaseDriver  Driver for persisting the client object.
	 * @since  12.1
	 */
	private $_db;

	/**
	 * @var    JOAuthCredentialsState  The current credential state.
	 * @since  12.1
	 */
	private $_state;

	/**
	 * Object constructor.
	 *
	 * @param   JDatabaseDriver  $db  The database driver to use when persisting the object.
	 *
	 * @since   12.1
	 */
	public function __construct(JDatabaseDriver $db = null)
	{
		// Setup the database object.
		$this->_db = $db ? $db : JFactory::getDbo();

		// Assume the base state for any credentials object to be new.
		$this->_state = new JOAuthCredentialsStateNew($this->_db);
	}

	/**
	 * Method to authorize the credentials.  This will persist a temporary credentials set to be authorized by
	 * a resource owner.
	 *
	 * @param   integer  $resourceOwnerId  The id of the resource owner authorizing the temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function authorize($resourceOwnerId)
	{
		$this->_state = $this->_state->authorize($resourceOwnerId);
	}

	/**
	 * Method to convert a set of authorized credentials to token credentials.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function convert()
	{
		$this->_state = $this->_state->convert();
	}

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function deny()
	{
		$this->_state = $this->_state->deny();
	}

	/**
	 * Get the callback url associated with this token.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getCallbackUrl()
	{
		return $this->_state->callback_url;
	}

	/**
	 * Get the consumer key associated with this token.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getClientKey()
	{
		return $this->_state->client_key;
	}

	/**
	 * Get the credentials key value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getKey()
	{
		return $this->_state->key;
	}

	/**
	 * Get the ID of the user this token has been issued for.  Not all tokens
	 * will have known users.
	 *
	 * @return  integer
	 *
	 * @since   12.1
	 */
	public function getResourceOwnerId()
	{
		return $this->_state->resource_owner_id;
	}

	/**
	 * Get the token secret.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getSecret()
	{
		return $this->_state->secret;
	}

	/**
	 * Get the credentials type.
	 *
	 * @return  integer
	 *
	 * @since   12.1
	 */
	public function getType()
	{
		return $this->_state->type;
	}

	/**
	 * Get the credentials verifier key.
	 *
	 * @return  integer
	 *
	 * @since   12.1
	 */
	public function getVerifierKey()
	{
		return $this->_state->verifier_key;
	}

	/**
	 * Method to initialize the credentials.  This will persist a temporary credentials set to be authorized by
	 * a resource owner.
	 *
	 * @param   string  $clientKey    The key of the client requesting the temporary credentials.
	 * @param   string  $callbackUrl  The callback URL to set for the temporary credentials.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function initialize($clientKey, $callbackUrl)
	{
		$this->_state = $this->_state->initialize($clientKey, $callbackUrl);
	}

	/**
	 * Method to load a set of credentials by key.
	 *
	 * @param   string  $key  The key of the credentials set to load.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function load($key)
	{
		// Build the query to load the row from the database.
		$query = $this->_db->getQuery(true);
		$query->select('*')
			->from('#__oauth_credentials')
			->where('key = ' . $this->_db->quote($key));

		// Set and execute the query.
		$this->_db->setQuery($query);
		$properties = $this->_db->loadAssoc();

		// If nothing was found we will setup a new credential state object.
		if (empty($properties))
		{
			$this->_state = new JOAuthCredentialsStateNew($this->_db);
			return;
		}

		// Cast the type for validation.
		$properties['type'] = (int) $properties['type'];

		// If we are loading a temporary set of credentials load that state.
		if ($properties['type'] === self::TEMPORARY)
		{
			$this->_state = new JOAuthCredentialsStateTemporary($this->_db);
		}
		// If we are loading a authorized set of credentials load that state.
		elseif ($properties['type'] === self::AUTHORIZED)
		{
			$this->_state = new JOAuthCredentialsStateAuthorized($this->_db);
		}
		// If we are loading a token set of credentials load that state.
		elseif ($properties['type'] === self::TOKEN)
		{
			$this->_state = new JOAuthCredentialsStateToken($this->_db);
		}
		// Unknown OAuth credential type.
		// @codeCoverageIgnoreStart
		else
		{
			throw new InvalidArgumentException('OAuth credentials not found.');
		}
		// @codeCoverageIgnoreEnd

		// Iterate over any input properties and bind them to the object.
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				$this->_state->$k = $v;
			}
		}
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function revoke()
	{
		$this->_state = $this->_state->revoke();
	}
}
