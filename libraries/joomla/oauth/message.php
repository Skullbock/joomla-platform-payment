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
 * OAuth Message class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 * @since       12.1
 */
class JOAuthMessage
{
	/**
	 * @var    string  The HTTP request method for the message.
	 * @since  12.1
	 */
	private $_method;

	/**
	 * @var    array  Associative array of parameters for the OAuth message.
	 * @since  12.1
	 */
	private $_parameters = array();

	/**
	 * @var    array  List of OAuth possible parameters.
	 * @since  12.1
	 */
	private $_reserved = array(
		'oauth_callback',
		'oauth_consumer_key',
		'oauth_nonce',
		'oauth_signature',
		'oauth_signature_method',
		'oauth_timestamp',
		'oauth_token',
		'oauth_token_secret',
		'oauth_verifier',
		'oauth_version'
	);

	/**
	 * @var    string  The optional authorization realm for the message.
	 * @since  12.1
	 */
	private $_realm;

	/**
	 * @var    JURI  The request URI for the message.
	 * @since  12.1
	 */
	private $_uri;

	/**
	 * Get the OAuth callback URL for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getCallback($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_callback']) : $this->_parameters['oauth_callback'];
	}

	/**
	 * Get the OAuth consumer key for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getClientKey($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_consumer_key']) : $this->_parameters['oauth_consumer_key'];
	}

	/**
	 * Get the OAuth nonce for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getNonce($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_nonce']) : $this->_parameters['oauth_nonce'];
	}

	/**
	 * Get the OAuth authorization realm.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getRealm($encoded = false)
	{
		return $encoded ? $this->encode($this->_realm) : $this->_realm;
	}

	/**
	 * Get the OAuth signature for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getSignature($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_signature']) : $this->_parameters['oauth_signature'];
	}

	/**
	 * Get the OAuth signature method for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getSignatureMethod($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_signature_method']) : $this->_parameters['oauth_signature_method'];
	}

	/**
	 * Get the OAuth timestamp for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getTimestamp($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_timestamp']) : $this->_parameters['oauth_timestamp'];
	}

	/**
	 * Get the OAuth token for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getToken($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_token']) : $this->_parameters['oauth_token'];
	}

	/**
	 * Get the OAuth token secret for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getTokenSecret($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_token_secret']) : $this->_parameters['oauth_token_secret'];
	}

	/**
	 * Get the OAuth verfier for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getVerifier($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_verifier']) : $this->_parameters['oauth_verifier'];
	}

	/**
	 * Get the OAuth version for the message.
	 *
	 * @param   boolean  $encoded  True to encode the value.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getVersion($encoded = false)
	{
		return $encoded ? $this->encode($this->_parameters['oauth_version']) : $this->_parameters['oauth_version'];
	}

	/**
	 * Method to determine whether or not the message is signed and valid.
	 *
	 * @param   string  $clientSecret      The OAuth client's secret.
	 * @param   string  $credentialSecret  The OAuth credentials' secret.
	 *
	 * @return  boolean  True if the message is valid.
	 *
	 * @since   12.1
	 */
	public function isValid($clientSecret, $credentialSecret = null)
	{
		$signature = $this->sign($clientSecret, $credentialSecret);

		return ($signature == $this->getSignature());
	}

	/**
	 * Method to get the OAuth parameters for the current request. Parameters are retrieved from these locations
	 * in the order of precedence as follows:
	 *
	 *   - Authorization header
	 *   - POST variables
	 *   - GET query string variables
	 *
	 * @return  boolean  True if an OAuth message was found in the request.
	 *
	 * @since   12.1
	 */
	public function loadFromRequest()
	{
		// Initialize variables.
		$found = false;

		// First we look and see if we have an appropriate Authorization header.
		$header = $this->_fetchAuthorizationHeader();

		// If we have an Authorization header it gets first dibs.
		if ($header && $this->_processAuthorizationHeader($header))
		{
			$found = true;
		}

		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		if (!$found && $this->_processPostVars())
		{
			$found = true;
		}

		// If we didn't find anything in the POST variables either let's try the query string.
		if (!$found && $this->_processGetVars())
		{
			$found = true;
		}

		// If we found an OAuth message somewhere we need to set the URI and request method.
		if ($found)
		{
			$this->_uri = new JURI($this->_fetchRequestUrl());
			$this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		return $found;
	}

	/**
	 * Method to set the OAuth message parameters.  This will only set valid OAuth message parameters.  If non-valid
	 * parameters are in the input array they will be ignored.
	 *
	 * @param   array  $parameters  The OAuth message parameters to set.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setParameters($parameters)
	{
		// Ensure that only valid OAuth parameters are set if they exist.
		if (!empty($parameters))
		{
			foreach ($parameters as $k => $v)
			{
				if (in_array($k, $this->_reserved))
				{
					// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
					$this->_parameters[$k] = urldecode((string) $v);
				}
			}
		}
	}

	/**
	 * Get the message string complete and signed.
	 *
	 * @param   string  $clientSecret      The OAuth client's secret.
	 * @param   string  $credentialSecret  The OAuth credentials' secret.
	 *
	 * @return  string  The OAuth message signature.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function sign($clientSecret, $credentialSecret = null)
	{
		// Get a message signer object.
		$signer = $this->_fetchSigner();

		// Get the base string for signing.
		$baseString = $this->_fetchStringForSigning();

		return $signer->sign($baseString, $this->encode($clientSecret), $this->encode($credentialSecret));
	}

	/**
	 * Encode a string according to the RFC3986
	 *
	 * @param   string  $s  string to encode
	 *
	 * @return  string encoded string
	 *
	 * @link    http://www.ietf.org/rfc/rfc3986.txt
	 * @since   12.1
	 */
	public function encode($s)
	{
		return str_replace('%7E', '~', rawurlencode((string) $s));
	}

	/**
	 * Decode a string according to RFC3986.
	 * Also correctly decodes RFC1738 urls.
	 *
	 * @param   string  $s  string to decode
	 *
	 * @return  string  decoded string
	 *
	 * @link    http://www.ietf.org/rfc/rfc1738.txt
	 * @link    http://www.ietf.org/rfc/rfc3986.txt
	 * @since   12.1
	 */
	public function decode($s)
	{
		return rawurldecode((string) $s);
	}

	/**
	 * Get the HTTP request headers.  Header names have been normalized, stripping
	 * the leading 'HTTP_' if present, and capitalizing only the first letter
	 * of each word.
	 *
	 * @return  string  The Authorization header if it has been set.
	 */
	private function _fetchAuthorizationHeader()
	{
		// The simplest case is if the apache_request_headers() function exists.
		if (function_exists('apache_request_headers'))
		{
			$headers = apache_request_headers();
			if (isset($headers['Authorization']))
			{
				return trim($headers['Authorization']);
			}
		}
		// Otherwise we need to look in the $_SERVER superglobal.
		elseif (isset($_SERVER['HTTP_AUTHORIZATION']))
		{
			return trim($_SERVER['HTTP_AUTHORIZATION']);
		}
	}

	/**
	 * Method to detect and return the requested URI from server environment variables.
	 *
	 * @return  string  The requested URI
	 *
	 * @since   11.3
	 */
	private function _fetchRequestUrl()
	{
		// Initialise variables.
		$uri = '';

		// First we need to detect the URI scheme.
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
		{
			$scheme = 'https://';
		}
		else
		{
			$scheme = 'http://';
		}

		/*
		 * There are some differences in the way that Apache and IIS populate server environment variables.  To
		 * properly detect the requested URI we need to adjust our algorithm based on whether or not we are getting
		 * information from Apache or IIS.
		 */

		// If PHP_SELF and REQUEST_URI are both populated then we will assume "Apache Mode".
		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// The URI is built from the HTTP_HOST and REQUEST_URI environment variables in an Apache environment.
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		// If not in "Apache Mode" we will assume that we are in an IIS environment and proceed.
		else
		{
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

			// If the QUERY_STRING variable exists append it to the URI string.
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return trim($uri);
	}

	/**
	 * Method to get a message signer object based on the message's oauth_signature_method parameter.
	 *
	 * @return  JOAuthMessageSigner  The OAuth message signer object for the message.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	private function _fetchSigner()
	{
		switch ($this->getSignatureMethod())
		{
			case 'HMAC-SHA1':
				$signer = new JOAuthMessageSignerHMAC;
				break;
			case 'RSA-SHA1':
				$signer = new JOAuthMessageSignerRSA;
				break;
			case 'PLAINTEXT':
				$signer = new JOAuthMessageSignerPlaintext;
				break;
			default:
				throw new InvalidArgumentException('No valid signature method was found.');
				break;
		}

		return $signer;
	}

	/**
	 * Method to get the OAuth message string for signing.
	 *
	 * @return  string  The OAuth message string.
	 *
	 * @since   12.1
	 */
	private function _fetchStringForSigning()
	{
		// Start off building the base string by adding the request method and URI.
		$base = array(
			$this->encode(strtoupper($this->_method)),
			$this->encode(strtolower($this->_uri->toString(array('scheme', 'user', 'pass', 'host', 'port'))) . $this->_uri->toString(array('path')))
		);

		// Get the found parameters.
		$params = $this->_parameters;

		// Add the variables from the URI query string.
		foreach ($this->_uri->getQuery(true) as $k => $v)
		{
			if (strpos($k, 'oauth_') !== 0)
			{
				$params[$k] = $v;
			}
		}

		// Make sure that any found oauth_signature is not included.
		unset($params['oauth_signature']);

		// Ensure the parameters are in order by key.
		ksort($params);

		// Iterate over the keys to add properties to the base.
		foreach ($params as $key => $value)
		{
			// If we have multiples for the parameter let's loop over them.
			if (is_array($value))
			{
				// Don't want to do this more than once in the inner loop.
				$key = $this->encode($key);

				// Sort the value array and add each one.
				sort($value, SORT_STRING);
				foreach ($value as $v)
				{
					$base[] = $key . '=' . $this->encode($v);
				}
			}
			// The common case is that there is one entry per property.
			else
			{
				$base[] = $this->encode($key) . '=' . $this->encode($value);
			}
		}

		return implode('&', $base);
	}

	/**
	 * Parse an OAuth authorization header and set any found OAuth parameters.
	 *
	 * @param   string  $header  Authorization header.
	 *
	 * @return  boolean  True if OAuth parameters found.
	 *
	 * @since   12.1
	 */
	private function _processAuthorizationHeader($header)
	{
		// Initialize variables.
		$parameters = array();

		if (strncasecmp($header, 'OAuth ', 6) === 0)
		{
			$vs = explode(',', $header);
			foreach ($vs as $v)
			{
				if (strpos($v, '=') !== false)
				{
					$v = trim($v);
					list ($name, $value) = explode('=', $v, 2);
					if (!empty($value) && $value{0} == '"' && substr($value, -1) == '"')
					{
						$value = substr($value, 1, -1);
					}

					$parameters[$name] = $value;
				}
			}

			// If we have a realm from the authorization header, go ahead and set it.
			if (isset($parameters['realm']))
			{
				$this->_realm = $parameters['realm'];
				unset($parameters['realm']);
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters))
		{
			return false;
		}

		$this->setParameters($parameters);

		return true;
	}

	/**
	 * Parse the request query string for OAuth parameters.
	 *
	 * @return  boolean  True if OAuth parameters found.
	 *
	 * @since   12.1
	 */
	private function _processGetVars()
	{
		// Initialize variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the query string variables.
		foreach ($this->_reserved as $k)
		{
			if (isset($_GET[$k]))
			{
				$parameters[$k] = trim($_GET[$k]);
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters))
		{
			return false;
		}

		$this->setParameters($parameters);

		return true;
	}

	/**
	 * Parse the request POST variables for OAuth parameters.
	 *
	 * @return  boolean  True if OAuth parameters found.
	 *
	 * @since   12.1
	 */
	private function _processPostVars()
	{
		// If we aren't handling a post request with urlencoded vars then there is nothing to do.
		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || (strtolower($_SERVER['CONTENT_TYPE']) != 'application/x-www-form-urlencoded'))
		{
			return;
		}

		// Initialize variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the POST variables.
		foreach ($this->_reserved as $k)
		{
			if (isset($_POST[$k]))
			{
				$parameters[$k] = trim($_POST[$k]);
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters))
		{
			return false;
		}

		$this->setParameters($parameters);

		return true;
	}
}
