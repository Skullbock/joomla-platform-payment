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
 * Parser for the response section of a service definition.  The general structure can be found below.  There can be
 * multiple status code blocks within the response section.  Any text preceding the first status code block is
 * considered the description of the response section.
 *
 * ```
 * {DESCRIPTION}
 * ### {STATUS_CODE_NAME}
 * {STATUS_CODE_BLOCK}
 * ```
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserResponse extends JServiceDefinitionParserBase
{
	/**
	 * Parse the text and return an array of the definition response section.
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
		$start = 0;
		$matches = array();

		// Find where the status code sections start.  -- Hint: everything before is "description".
		if (preg_match('/^[\s]*\#\#\#[\s]+(.*)[\s]*$/i', $text, $matches))
		{
			$start = $matches[0][1];
		}

		$buffer['description'] = trim(substr($text, 0, $start));

		// Let's split up the status code sections.
		$tmp = preg_split('/^[\s]*\#\#\#[\s]+(.*)[\s]*$/mi', substr($text, $start), null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$count = floor(count($tmp) / 2);
		for ($i = 0; $i < $count; $i++)
		{
			$buffer[trim($tmp[$i * 2])] = $this->parseStatus($tmp[($i * 2) + 1]);
		}

		return $buffer;
	}

	/**
	 * Parse a status code block from a text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseStatus($text)
	{
		$parser = new JServiceDefinitionParserStatus;

		return $parser->parse($text);
	}
}
