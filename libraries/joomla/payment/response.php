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
 * Class that represents a Payment done using a JPaymentProcessor
 * Stores any result and any transaction-related data
 *
 * @package     Joomla.Platform
 * @subpackage  Payment
 * @since       12.1
 */
class JPaymentResponse extends JRegistry
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
		$this->status 	= false;
	}
}