<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Parser for the description section of a service definition.  The first empty new line is considered a separator
 * between the title and the description of the service.  Everything beyond the first empty new line is part of the
 * service description.
 *
 * ```
 * {TITLE}
 *
 * {DESCRIPTION}
 * ```
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserDescription extends JServiceDefinitionParserBase
{
	/**
	 * Parse the text and return an array of the description section.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	public function parse($text)
	{
		$tmp = preg_split("/[\n]+/", $text, 2);

		$buffer = array(
			'title' => trim($tmp[0]),
			'description' => isset($tmp[1]) ? trim($tmp[1]) : ''
		);

		return $buffer;
	}
}
