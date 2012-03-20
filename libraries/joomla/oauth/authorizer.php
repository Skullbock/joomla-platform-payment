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
 * OAuth Request Authorizer class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthAuthorizer
{
	/**
	 * Authorize an OAuth signed request for a protected resource.
	 *
	 * @return  integer  Identity ID for the identity that owns the verified token credentials.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function authorize()
	{
		// Get the OAuth message from the current request.
		$message = new JOAuthMessage;
		if (!$message->loadFromRequest())
		{
			throw new InvalidArgumentException('Not a valid OAuth request.');
		}

		// Get an OAuth client object and load it using the incoming client key.
		$client = new JOAuthClient;
		$client->loadByKey($message->getClientKey());

		// Validate the request signature.
		if (!$message->isValid($client->secret))
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('Invalid OAuth request signature.');
			return;
		}

		// Get the credentials for the request.
		$credentials = new JOAuthCredentials;
		$credentials->load($message->getToken());

		// Verify existing credentials.
		if (!$credentials->getKey())
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('Not a valid credentials token.');
			return;
		}

		// Verify that the consumer key matches for the request and credentials.
		if ($credentials->getClientKey() != $message->getClientKey())
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('The token is invalid.  Consumer key does not match.');
			return;
		}

		// Ensure the credentials are temporary.
		if ($credentials->getType() === JOAuthCredentials::TOKEN)
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('The token is not valid for a requesting protected resources.');
			return;
		}

		return $credentials->getResourceOwnerId();
	}
}
