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
 * RESTful service class definition parser.
 *
 * This class looks at a class file, extracts it's documentation block, and
 * parses that documentation block to build a service definition structure.  This service definition is represented
 * as an associative array and can be then used to communicate documentation for a given service programmatically.
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParser
{
	/**
	 * Parse a class's documentation block and extract a service definition structure.
	 *
	 * @param   string  $className  The name of the class.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	public function parse($className)
	{
		$documentationBlock = $this->fetchDocumentationBlock($className);
		$documentationBlock = $this->cleanDocumentationBlock($documentationBlock);

		$parser = new JServiceDefinitionParserSections;

		return $parser->parse($documentationBlock);
	}

	/**
	 * Clean the documentation block from leading and trailing nonsense.
	 *
	 * @param   string  $text  The documentation block to clean.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	protected function cleanDocumentationBlock($text)
	{
		// Trim asterisks and leading whitespace from the beginning and whitespace from the end of lines.
		$text = trim(preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#', '$1', $text));

		// A little tidying up in case `*/` is found in a final single line.
		if (substr($text, -2) == '*/')
		{
			$text = trim(substr($text, 0, -2));
		}

		// Normalize line endings.
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		return $text;
	}

	/**
	 * Get a class documentation block.
	 *
	 * @param   string  $className  The name of the class.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	protected function fetchDocumentationBlock($className)
	{
		$c = new ReflectionClass($className);

		return $c->getDocComment();
	}
}
