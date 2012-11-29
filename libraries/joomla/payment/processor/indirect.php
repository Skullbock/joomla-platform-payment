<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Payment
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Payment Processor Indirect Base Class
 *
 * @package     Joomla.Platform
 * @subpackage  Payment
 * @since       12.1
 */
abstract class JPaymentProcessorIndirect extends JPaymentProcessorBase
{
	/**
	 * HTTP method to use to send the data to the processor url. Default: POST
	 *
	 * @var string
	 */
	public $method = 'POST';

	/**
	 * Get the url to which we should redirect the user
	 *
	 * @return string The url to which we should redirect to
	 */
	public function getUrl();

	/**
	 * Get the data to send to the processor url
	 *
	 * @return JPaymentRequest the data to send to the processor url
	 */
	public function getData();

	/**
	 * Process the data that we got back from the gateway
	 *
	 * @return JPaymentResponse The payment object populated with the transaciton informations
	 */
	public function process();

	/**
	 * Verify the data before sending it to the payment processor
	 *
	 * @return JPaymentProcessor $this for chaining support
	 *
	 * @throws JPaymentException If the url is not valid
	 */
	public function verify()
	{
		if ($url = $this->getUrl()) 
		{
			$uri = JURI::getInstance($url);
			if ($uri !== false) 
			{
				return $this;
			}
		}

		throw new JPaymentException('Url is not valid');
	}

	/**
	 * Send the request to the processor url
	 *
	 * @return JHttpResponse The response from the url
	 */
	public function request()
	{
		$this->verify();

		$http = new JHttp();

		if ($this->method == 'GET')
		{
			return $http->get($this->getUrl(), $this->getData());
		}
		else 
		{
			return $http->post($this->getUrl(), $this->getData());
		}
	}
}