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
 * HTML parser class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearchParserHtml extends JSearchParser
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
		// Strip all script tags.
		$input = preg_replace('#<script[^>]*>.*?</script>#si', ' ', $input);

		// Deal with spacing issues in the input.
		$input = str_replace('>', '> ', $input);
		$input = str_replace(array('&nbsp;', '&#160;'), ' ', $input);
		$input = trim(preg_replace('#\s+#u', ' ', $input));

		// Strip the tags from the input and decode entities.
		$input = strip_tags($input);
		$input = html_entity_decode($input, ENT_QUOTES, 'UTF-8');
		$input = trim(preg_replace('#\s+#u', ' ', $input));

		return $input;
	}
}