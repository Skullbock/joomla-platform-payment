<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/*
Step 2

   The client redirects Jane's user-agent to the server's Resource Owner
   Authorization endpoint to obtain Jane's approval for accessing her
   private photos:

     https://photos.example.net/authorize?oauth_token=hh5s93j4hdidpola

   The server requests Jane to sign in using her username and password
   and if successful, asks her to approve granting 'printer.example.com'
   access to her private photos.  Jane approves the request and her
   user-agent is redirected to the callback URI provided by the client
   in the previous request (line breaks are for display purposes only):

     http://printer.example.com/ready?
     oauth_token=hh5s93j4hdidpola&oauth_verifier=hfdp7dh39dks9884

 */

/**
 * OAuth Controller class for authorizing temporary credentials for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthControllerAuthorize extends JController
{
	/**
	 * Handle the request.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function execute()
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
		if ($credentials->getType() === JOAuthCredentials::TEMPORARY)
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('The token is not for a temporary credentials set.');
			return;
		}

		// Verify that we have a signed in user.
		$user = JFactory::getUser();
		if ($user->guest)
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('You must first sign in.');
			return;
		}

		// Attempt to authorize the credentials for the current user.
		$credentials->authorize($user->get('id'));

		if ($credentials->getCallbackUrl())
		{
			$this->app->redirect($credentials->getCallbackUrl());
			return;
		}

		$this->app->setBody('Credentials authorized.');
	}
}
