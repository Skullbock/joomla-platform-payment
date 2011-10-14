<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Search
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::discover('JSearchParser', JPATH_PLATFORM.'/joomla/search/parser', false);

/**
 * Parser base class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
abstract class JSearchParser
{
	/**
	 * @var    array  The array of JSearchParser objects.
	 * @since  12.1
	 */
	protected static $instances;

	/**
	 * Method to get a parser, creating it if necessary.
	 *
	 * @param   string  $type  The type of parser to load.
	 *
	 * @return  JSearchParser
	 *
	 * @since   12.1
	 * @throws  Exception on invalid parser.
	 */
	public static function getInstance($type)
	{
		// Only create one parser for each adapter.
		if (isset(self::$instances[$type])) {
			return self::$instances[$type];
		}

		// Derive the class name for the parser.
		$class = 'JSearchParser'.ucfirst(JFilterInput::clean($type, 'cmd'));

		// Check if a parser exists for the adapter.
		if (class_exists($class)) {
			self::$instances[$type] = new $class;
		}
		// Throw invalid parser exception.
		else {
			throw new Exception(JText::sprintf('LIB_SEARCH_INVALID_PARSER', $type));
		}

		return self::$instances[$type];
	}

	/**
	 * Constructor.
	 *
	 * @return  JSearchParser
	 *
	 * @since   12.1
	 */
	protected function __construct()
	{
	}

	/**
	 * Method to parse input and extract the plain text. Because this method is
	 * called from both inside and outside the indexer, it needs to be able to
	 * batch out its parsing functionality to deal with the inefficiencies of
	 * regular expressions. We will parse recursively in 2KB chunks.
	 *
	 * @param   string  $input  The input to parse.
	 *
	 * @return  string  The plain text input.
	 *
	 * @since   12.1
	 */
	public function parse($input)
	{
		$return	= null;

		// Parse the input in batches if bigger than 2KB.
		if (strlen($input) > 2048)
		{
			$start	= 0;
			$end	= strlen($input);
			$chunk	= 2048;

			while ($start < $end)
			{
				// Setup the string.
				$string	= substr($input, $start, $chunk);

				// Find the last space character if we aren't at the end.
				$ls = (($start + $chunk) < $end ? strrpos($string, ' ') : false);

				// Truncate to the last space character.
				if ($ls !== false) {
					$string = substr($string, 0, $ls);
				}

				// Adjust the start position for the next iteration.
				$start += ($ls !== false ? ($ls+1 - $chunk) + $chunk : $chunk);

				// Parse the chunk.
				$return .= $this->process($string);
			}
		}
		// The input is less than 2KB so we can parse it efficiently.
		else
		{
			// Parse the chunk.
			$return .= $this->process($input);
		}

		return $return;
	}

	/**
	 * Method to process input and extract the plain text.
	 *
	 * @param   string  $input  The input to process.
	 *
	 * @return  string  The plain text input.
	 *
	 * @since   12.1
	 */
	abstract protected function process($input);
}