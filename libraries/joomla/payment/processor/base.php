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
 * Joomla Payment Processor Base Class
 *
 * @package     Joomla.Platform
 * @subpackage  Payment
 * @since       12.1
 */
abstract class JPaymentProcessorBase implements JPaymentProcessor
{
	/**
	 * @var JPaymentRequest The payment data to be used for processing
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @param 	JPaymentRequest	$data The payment data to be used for processing
	 * @since   12.1
	 */
	public function __construct(JPaymentRequest $data);

	/**
	 * Process the payment
	 *
	 * @return JPaymentResponse An object representing the transaction
	 */
	public function process();
}