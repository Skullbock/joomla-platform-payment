<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Search
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Text parser class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearchParserTxt extends JSearchParser
{
	/**
	 * Method to process input and extract the plain text.
	 *
	 * @param   string  $input  The input to process.
	 *
	 * @return  string  The plain text input.
	 *
	 * @since   12.1
	 */
	protected function process($input)
	{
		return $input;
	}
}