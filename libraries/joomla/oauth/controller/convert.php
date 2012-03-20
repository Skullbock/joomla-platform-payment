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
Step 3

   The callback request informs the client that Jane completed the
   authorization process.  The client then requests a set of token
   credentials using its temporary credentials (over a secure Transport
   Layer Security (TLS) channel):

     POST /token HTTP/1.1
     Host: photos.example.net
     Authorization: OAuth realm="Photos",
        oauth_consumer_key="dpf43f3p2l4k3l03",
        oauth_token="hh5s93j4hdidpola",
        oauth_signature_method="HMAC-SHA1",
        oauth_timestamp="137131201",
        oauth_nonce="walatlh",
        oauth_verifier="hfdp7dh39dks9884",
        oauth_signature="gKgrFCywp7rO0OXSjdot%2FIHF7IU%3D"

   The server validates the request and replies with a set of token
   credentials in the body of the HTTP response:

     HTTP/1.1 200 OK
     Content-Type: application/x-www-form-urlencoded

     oauth_token=nnch734d00sl2jdk&oauth_token_secret=pfkkdhi9sl3r4s00
 */

/**
 * OAuth Controller class for converting temporary credentials to token credentials for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthControllerConvert
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

		// Ensure the credentials are authorized.
		if ($credentials->getType() === JOAuthCredentials::TOKEN)
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('The token is not for a temporary credentials set.');
			return;
		}

		// Ensure the credentials are authorized.
		if ($credentials->getType() === JOAuthCredentials::TEMPORARY)
		{
			$this->app->setHeader('status', '400');
			$this->app->setBody('The token has not been authorized by the resource owner.');
			return;
		}

		// Convert the credentials to valid Token credentials for requesting protected resources.
		$credentials->convert();

		// Build the response for the client.
		$response = array('oauth_token' => $credentials->getKey(), 'oauth_token_secret' => $credentials->getSecret());

		// Set the application response code and body.
		$this->app->setHeader('status', '200');
		$this->app->setBody(http_build_query($response));
	}
}
