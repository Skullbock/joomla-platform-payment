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
 * RTF parser class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearchParserRtf extends JSearchParser
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
		// Remove embeded pictures.
		$input = preg_replace('#{\\\pict[^}]*}#mis', '', $input);

		// Remove control characters.
		$input = str_replace(array('{', '}', "\\\n"), array(' ', ' ', "\n"), $input);
		$input = preg_replace ('#\\\([^;]+?);#mis', ' ', $input);
		$input = preg_replace ('#\\\[\'a-zA-Z0-9]+#mis', ' ', $input);

		return $input;
	}
}