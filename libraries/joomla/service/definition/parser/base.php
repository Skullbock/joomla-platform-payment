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
 * Abstract base class for all of the service definition element parsers.
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
abstract class JServiceDefinitionParserBase
{
	/**
	 * Parse the text and return a part of the service definition as an array.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	abstract public function parse($text);

	/**
	 * Parse and return a MarkDown fenced code block.
	 *
	 * @param   string  $text  The text from which to extract the code block.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	protected function fetchCodeData($text)
	{
		$matches = array();
		$found = preg_match('{
			^						        # Start of a line
				(```.*) \n					# $1: Header row (at least one pipe)

				(							# $3: Cells
					(?>
						.* \n		# Row content
					)*
				)
				(```.*) \n					# $1: Header row (at least one pipe)
			(?=\n|\Z)					# Stop at final double newline.
			}xm',
			$text,
			$matches
		);

		if (!$found)
		{
			return null;
		}

		return trim($matches[2]);
	}

	/**
	 * Parse and return a MarkDown table as an array of rows.
	 *
	 * @param   string  $text  The text from which to extract the tabular data.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function fetchTableData($text)
	{
		$matches = array();
		$found = preg_match('{
		^							        # Start of a line
				[ ]{0,3}	                # Allowed whitespace.
				(\S.*[|].*) \n				# $1: Header row (at least one pipe)

				[ ]{0,3}	                # Allowed whitespace.
				([-:]+[ ]*[|][-| :]*) \n	# $2: Header underline

				(							# $3: Cells
					(?>
						.* [|] .* \n		# Row content
					)*
				)
				(?=\n|\Z)					# Stop at final double newline.
			}xm',
			$text,
			$matches
		);

		if (!$found)
		{
			return null;
		}

		// Extract the header values.
		$header = array_map('strtolower', array_map('trim', explode('|', $matches[1])));

		// Extract the content rows and combine the header values.
		$rows = array();
		$lines = explode("\n", trim($matches[3]));
		foreach ($lines as $row)
		{
			$rows[] = array_combine($header, array_map('trim', explode('|', $row)));
		}

		return $rows;
	}
}
