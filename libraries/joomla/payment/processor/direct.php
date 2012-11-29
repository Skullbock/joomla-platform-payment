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
 * Joomla Payment Processor Direct Base Class
 *
 * @package     Joomla.Platform
 * @subpackage  Payment
 * @since       12.1
 */
abstract class JPaymentProcessorDirect extends JPaymentProcessorBase
{
	/**
	 * @var JPaymentCard	The card to be processed
	 */
	protected $class;

	/**
	 * Constructor.
	 *
	 * @since   12.1
	 */
	public function __construct(JPaymentData $data, JPaymentCard $card) 
	{
		parent::__construct($data);

		$this->card = $card;
	}

	/**
	 * Verify the data before sending it to the payment processor
	 *
	 * @return JPaymentProcessor $this for chaining support
	 *
	 * @throws JPaymentException If the card data is not valid
	 */
	public function verify() 
	{
		if ($this->card && $this->card->isValid()) 
		{
			return $this;
		} 
		else 
		{
			throw new JPaymentException('Card not Valid');
		}
	}
}