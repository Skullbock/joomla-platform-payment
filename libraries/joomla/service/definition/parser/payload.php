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
 * Parser for a payload block of a service definition.  A payload block can contain a description, an example
 * payload string, and a table of payload parameters with descriptions.  A possible structure can be seen below.
 *
 * ```
 * {DESCRIPTION}
 *
 * {EXAMPLE}
 *
 * {PARAMETERS}
 * ```
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserPayload extends JServiceDefinitionParserBase
{
	/**
	 * Parse the text and return an array of the definition payload block.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	public function parse($text)
	{
		$buffer = array();

		// Parse out the description if there is one.
		$buffer['description'] = '';

		// Extract the payload example if it exists.
		$buffer['example'] = $this->fetchCodeData($text);

		// Extract the MarkDown table data for the section.
		$buffer['parameters'] = $this->fetchTableData($text);

		return $buffer;
	}
}
