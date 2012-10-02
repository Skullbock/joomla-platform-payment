<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * Parser for a status code block of a service definition response section.  The general structure can be found
 * below.  The order of the blocks is fixed though they are all considered optional.
 *
 * ```
 * {DESCRIPTION}
 * #### Exceptions
 * {EXCEPTIONS}
 * #### Headers
 * {HEADERS}
 * #### Payload
 * {PAYLOAD}
 * ```
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserStatus extends JServiceDefinitionParserBase
{
	/**
	 * @var    array  The parsed string buffer.
	 * @since  12.3
	 */
	protected $markers = array(
		'exceptions_before' => 0,
		'exceptions_after' => 0,
		'headers_before' => 0,
		'headers_after' => 0,
		'payload_before' => 0,
		'payload_after' => 0
	);

	/**
	 * Parse the text and return an array of the definition response status code block.
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
			'exceptions' => $this->parseExceptions($text),
			'headers' => $this->parseHeaders($text),
			'payload' => $this->parsePayload($text)
		);

		return $buffer;
	}

	/**
	 * Isolate and parse the description block of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseDescription($text)
	{
		if ($this->markers['exceptions_before'])
		{
			$limit = $this->markers['exceptions_before'];
		}
		elseif ($this->markers['headers_before'])
		{
			$limit = $this->markers['headers_before'];
		}
		else
		{
			$limit = $this->markers['payload_before'];
		}

		return substr($text, 0, $limit);
	}

	/**
	 * Isolate and parse the exceptions block of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseExceptions($text)
	{
		$parser = new JServiceDefinitionParserParameters;

		if ($this->markers['headers_before'])
		{
			$limit = $this->markers['headers_before'] - $this->markers['exceptions_after'];
		}
		elseif ($this->markers['payload_before'])
		{
			$limit = $this->markers['payload_before'] - $this->markers['exceptions_after'];
		}
		else
		{
			$limit = null;
		}

		return $parser->parse(substr($text, $this->markers['exceptions_after'], $limit));
	}

	/**
	 * Isolate and parse the headers block of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseHeaders($text)
	{
		$parser = new JServiceDefinitionParserParameters;

		if ($this->markers['payload_before'])
		{
			$limit = $this->markers['payload_before'] - $this->markers['headers_after'];
		}
		else
		{
			$limit = null;
		}

		return $parser->parse(substr($text, $this->markers['headers_after'], $limit));
	}

	/**
	 * Isolate and parse the payload block of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parsePayload($text)
	{
		$parser = new JServiceDefinitionParserPayload;

		return $parser->parse(substr($text, $this->markers['payload_after']));
	}

	/**
	 * Search a text string for relevant markers indicating blocks to separate.
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
		if (preg_match('/^[\s]*\#\#\#\#[\s]+Exceptions[\s]*$/mi', $text, $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['exceptions_before'] = $matches[0][1] + 1;
			$this->markers['exceptions_after'] = $matches[0][1] + strlen($matches[0][0]) - 1;
		}

		$matches = array();
		if (preg_match('/^[\s]*\#\#\#\#[\s]+Headers[\s]*$/mi', substr($text, $this->markers['exceptions_after']), $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['headers_before'] = $matches[0][1] + $this->markers['exceptions_after'] + 1;
			$this->markers['headers_after'] = $matches[0][1] + strlen($matches[0][0]) + $this->markers['exceptions_after'] - 1;
		}

		$matches = array();
		if (preg_match('/^[\s]*\#\#\#\#[\s]+Payload[\s]*$/mi', substr($text, $this->markers['headers_after']), $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['payload_before'] = $matches[0][1] + $this->markers['headers_after'] + 1;
			$this->markers['payload_after'] = $matches[0][1] + strlen($matches[0][0]) + $this->markers['headers_after'] - 1;
		}
	}
}
