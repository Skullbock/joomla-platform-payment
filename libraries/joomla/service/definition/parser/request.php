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
 * Parser for the request section of a service definition.  The general structure can be found below.  The
 * order of the blocks is fixed though they are all considered optional.
 *
 * ```
 * {DESCRIPTION}
 * ### URI Parameters
 * {URI_PARAMETERS}
 * ### Headers
 * {HEADERS}
 * ### Payload
 * {PAYLOAD}
 * ```
 *
 * @package     Joomla.Platform
 * @subpackage  Service
 * @since       12.3
 */
class JServiceDefinitionParserRequest extends JServiceDefinitionParserBase
{
	/**
	 * @var    array  The parsed string buffer.
	 * @since  12.3
	 */
	protected $markers = array(
		'uri_before' => 0,
		'uri_after' => 0,
		'headers_before' => 0,
		'headers_after' => 0,
		'payload_before' => 0,
		'payload_after' => 0
	);

	/**
	 * Parse the text and return an array of the definition request section.
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
			'uri' => $this->parseUriParameters($text),
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
		if ($this->markers['uri_before'])
		{
			$limit = $this->markers['uri_before'];
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
	 * Isolate and parse the URI parameters block of the text string.
	 *
	 * @param   string  $text  The text to parse.
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	protected function parseUriParameters($text)
	{
		$parser = new JServiceDefinitionParserParameters;

		if ($this->markers['headers_before'])
		{
			$limit = $this->markers['headers_before'] - $this->markers['uri_after'];
		}
		elseif ($this->markers['payload_before'])
		{
			$limit = $this->markers['payload_before'] - $this->markers['uri_after'];
		}
		else
		{
			$limit = null;
		}

		return $parser->parse(substr($text, $this->markers['uri_after'], $limit));
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
		if (preg_match('/^[\s]*\#\#\#[\s]+URI\sParameters[\s]*$/mi', $text, $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['uri_before'] = $matches[0][1] + 1;
			$this->markers['uri_after'] = $matches[0][1] + strlen($matches[0][0]) - 1;
		}

		$matches = array();
		if (preg_match('/^[\s]*\#\#\#[\s]+Headers[\s]*$/mi', substr($text, $this->markers['uri_after']), $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['headers_before'] = $matches[0][1] + $this->markers['uri_after'] + 1;
			$this->markers['headers_after'] = $matches[0][1] + strlen($matches[0][0]) + $this->markers['uri_after'] - 1;
		}

		$matches = array();
		if (preg_match('/^[\s]*\#\#\#[\s]+Payload[\s]*$/mi', substr($text, $this->markers['headers_after']), $matches, PREG_OFFSET_CAPTURE))
		{
			$this->markers['payload_before'] = $matches[0][1] + $this->markers['headers_after'] + 1;
			$this->markers['payload_after'] = $matches[0][1] + strlen($matches[0][0]) + $this->markers['headers_after'] - 1;
		}
	}
}
