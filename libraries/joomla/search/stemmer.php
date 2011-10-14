<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Search
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::discover('JSearchStemmer', JPATH_PLATFORM.'/joomla/search/stemmer', false);

/**
 * Stemmer base class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
abstract class JSearchStemmer
{
	/**
	 * @var    array  The array of JSearchStemmer objects.
	 * @since  12.1
	 */
	protected static $instances;

	/**
	 * @var    array  An internal cache of stemmed tokens.
	 * @since  12.1
	 */
	public $cache = array();

	/**
	 * Method to get a stemmer, creating it if necessary.
	 *
	 * @param   string  $type  The type of stemmer to load.
	 *
	 * @return  JSearchStemmer
	 *
	 * @since   12.1
	 * @throws  Exception on invalid stemmer.
	 */
	public static function getInstance($type)
	{
		// Only create one stemmer for each adapter.
		if (isset(self::$instances[$type])) {
			return self::$instances[$type];
		}

		// Derive the class name for the stemmer.
		$class = 'JSearchStemmer'.ucfirst(JFilterInput::clean($type, 'cmd'));

		// Check if a stemmer exists for the adapter.
		if (class_exists($class)) {
			self::$instances[$type] = new $class;
		}
		// Throw invalid stemmer exception.
		else {
			throw new Exception(JText::sprintf('LIB_SEARCH_INVALID_STEMMER', $type));
		}

		return self::$instances[$type];
	}

	/**
	 * Constructor.
	 *
	 * @return  JSearchStemmer
	 *
	 * @since   12.1
	 */
	protected function __construct()
	{
	}

	/**
	 * Method to stem a token and return the root.
	 *
	 * @param   string  $token  The token to stem.
	 * @param   string  $lang   The language of the token.
	 *
	 * @return  string  The root token.
	 *
	 * @since   12.1
	 */
	abstract public function stem($token, $lang);
}