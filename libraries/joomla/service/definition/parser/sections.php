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
 * Parser for the top level sections of a service definition.  The general structure can be found below.  The
 * order of the sections is fixed.
 *
 * ```
 * {DESCRIPTION}
 * ## Request
 * {REQUEST}
 * ## Response
 * {RESPONSE}
 * ```
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserSections extends JServiceDefinitionParserBase
{
	protected $markers = array(
		'request_before' => 0,
		'request_after' => 0,
		'response_before' => 0,
		'response_after' => 0
	);

	/**
	 * Parse the text and return an array of the definition sections.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	public function parse($text)
	{
		$this->_findMarkers($text);

		$buffer = array(
			'description' => $this->parseDescription($text),
			'request' => $this->parseRequest($text),
			'response' => $this->parseResponse($text)
		);

		return $buffer;
	}

	/**
	 * Isolate and parse the description section of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseDescription($text)
	{
		$parser = new JServiceDefinitionParserDescription;

		$limit = ($this->markers['request_before'] > 0) ? $this->markers['request_before'] : $this->markers['response_before'];

		return $parser->parse(substr($text, 0, $limit));
	}

	/**
	 * Isolate and parse the request section of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseRequest($text)
	{
		$parser = new JServiceDefinitionParserRequest;

		return $parser->parse(substr($text, $this->markers['request_after'], $this->markers['response_before'] - $this->markers['request_after']));
	}

	/**
	 * Isolate and parse the response section of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseResponse($text)
	{
		$parser = new JServiceDefinitionParserResponse;

		return $parser->parse(substr($text, $this->markers['response_after']));
	}

	/**
	 * Search a text string for relevant markers indicating sections to separate.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	private function _findMarkers($text)
	{
		$matches = array();
		if (preg_match('/^[\s]*\#\#[\s]+Request[\s]*$/mi', $text, $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['request_before'] = $matches[0][1] + 1;
			$this->markers['request_after'] = $matches[0][1] + strlen($matches[0][0]) - 1;
		}

		$matches = array();
		if (preg_match('/^[\s]*\#\#[\s]+Response[\s]*$/mi', substr($text, $this->markers['request_after']), $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['response_before'] = $matches[0][1] + $this->markers['request_after'] + 1;
			$this->markers['response_after'] = $matches[0][1] + strlen($matches[0][0]) + $this->markers['request_after'] - 1;
		}
	}
}
