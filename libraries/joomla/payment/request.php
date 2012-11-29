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
 * Placeholder object to store data to pass to a payment processor
 *
 * @package     Joomla.Platform
 * @subpackage  Payment
 * @since       12.1
 */
class JPaymentRequest extends JRegistry
{
	/**
	 * Constructor
	 *
	 * @param   mixed  $data  The data to bind to the new JRegistry object.
	 *
	 * @since   11.1
	 */
	public function __construct($data = null)
	{
		// Construct JRegistry
		parent::__construct($data);

		// Set default data
		$this->amount 	= 0;
		$this->subtotal = 0;
		$this->taxes	= 0;
		$this->total 	= 0;
		$this->currency = 'USD';
	}
}