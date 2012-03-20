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
 * OAuth Denied Credentials class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthCredentialsStateDenied extends JOAuthCredentialsState
{
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
	public function authorize($resourceOwnerId)
	{
		throw new LogicException('Only temporary credentials can be authorized.');
	}

	/**
	 * Method to convert a set of authorized credentials to token credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function convert()
	{
		throw new LogicException('Only authorized credentials can be converted.');
	}

	/**
	 * Method to deny a set of temporary credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function deny()
	{
		throw new LogicException('Only temporary credentials can be denied.');
	}

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
	public function initialize($clientKey, $callbackUrl)
	{
		throw new LogicException('Only new credentials can be initialized.');
	}

	/**
	 * Method to revoke a set of token credentials.
	 *
	 * @return  JOAuthCredentialsState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function revoke()
	{
		throw new LogicException('Only token credentials can be revoked.');
	}
}
