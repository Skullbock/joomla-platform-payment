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
 * Joomla Payment Processor Interface
 *
 * @package     Joomla.Platform
 * @subpackage  Payment
 * @since       12.1
 */
interface JPaymentProcessor
{
	/**
	 * Constructor.
	 *
	 * @since   12.1
	 */
	public function __construct();

	/**
	 * Verify the data before sending it to the payment processor
	 *
	 * @return JPaymentProcessor $this for chaining support
	 *
	 * @throws JPaymentException If the data is not valid
	 */
	public function verify();

	/**
	 * Process the payment
	 *
	 * @return JPaymentResponse An object representing the transaction
	 */
	public function process();

	/**
	 * Send the request to the processor url
	 *
	 * @return JHttpResponse The response from the url
	 */
	public function request();
}