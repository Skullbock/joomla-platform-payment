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
Step 1

   Before 'printer.example.com' can ask Jane to grant it access to the
   photos, it must first establish a set of temporary credentials with
   'photos.example.net' to identify the delegation request.  To do so,
   the client sends the following HTTPS [RFC2818] request to the server:

     POST /initialize HTTP/1.1
     Host: photos.example.net
     Authorization: OAuth realm="Photos",
        oauth_consumer_key="dpf43f3p2l4k3l03",
        oauth_signature_method="HMAC-SHA1",
        oauth_timestamp="137131200",
        oauth_nonce="wIjqoS",
        oauth_callback="http%3A%2F%2Fprinter.example.com%2Fready",
        oauth_signature="74KNZJeDHnMBp0EMJ9ZHt%2FXKycU%3D"

   The server validates the request and replies with a set of temporary
   credentials in the body of the HTTP response (line breaks are for
   display purposes only):

     HTTP/1.1 200 OK
     Content-Type: application/x-www-form-urlencoded

     oauth_token=hh5s93j4hdidpola&oauth_token_secret=hdhd0244k9j7ao03&
     oauth_callback_confirmed=true
 */

/**
 * OAuth Controller class for initiating temporary credentials for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthControllerinitialize
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

		// Generate temporary credentials for the client.
		$credentials = new JOAuthCredentials;
		$credentials->initialize($message->getClientKey(), $message->getCallback());

		// Build the response for the client.
		$response = array('oauth_token' => $credentials->getKey(), 'oauth_token_secret' => $credentials->getSecret());

		// Set the application response code and body.
		$this->app->setHeader('status', '200');
		$this->app->setBody(http_build_query($response));
	}
}
