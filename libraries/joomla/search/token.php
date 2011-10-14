<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Search
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JSearch', JPATH_PLATFORM.'/joomla/search/search.php');

/**
 * Token class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearchToken
{
	/**
	 * This is the term that will be referenced in the terms table and the
	 * mapping tables.
	 *
	 * @var    string  The token term.
	 * @since  12.1
	 */
	public $term;

	/**
	 * The stem is used to match the root term and produce more potential
	 * matches when searching the index.
	 *
	 * @var    string  The stemmed term.
	 * @since  12.1
	 */
	public $stem;

	/**
	 * If the token is numeric, it is likely to be short and uncommon so the
	 * weight is adjusted to compensate for that situation.
	 *
	 * @var    boolean  Flag for numeric tokens.
	 * @since  12.1
	 */
	public $numeric;

	/**
	 * If the token is a common term, the weight is adjusted to compensate for
	 * the higher frequency of the term in relation to other terms.
	 *
	 * @var    boolean  Flag for common tokens.
	 * @since  12.1
	 */
	public $common;

	/**
	 * @var    boolean  Flag for phrase tokens.
	 * @since  12.1
	 */
	public $phrase;

	/**
	 * The length is used to calculate the weight of the token.
	 *
	 * @var    integer  Length of token term.
	 * @since  12.1
	 */
	public $length;

	/**
	 * The weight is calculated based on token size and whether the token is
	 * considered a common term.
	 *
	 * @var    integer  The relative weight of the token.
	 * @since  12.1
	 */
	public $weight;

	/**
	 * Method to construct the token object.
	 *
	 * @param   mixed   The term as a string for words or an array for phrases.
	 * @param   string  The simple language identifier.
	 * @param   string  The space separator for phrases.
	 *
	 * @return  JSearchToken
	 *
	 * @since   12.1
	 */
	public function __construct($term, $lang, $spacer = ' ')
	{
		// Tokens can be a single word or an array of words representing a phrase.
		if (is_array($term))
		{
			// Populate the token instance.
			$this->term		= implode($spacer, $term);
			$this->stem		= implode($spacer, array_map(array('JSearch', 'stem'), $term, array($lang)));
			$this->numeric	= false;
			$this->common	= false;
			$this->phrase	= true;
			$this->length	= JString::strlen($this->term);

			/*
			 * Calculate the weight of the token.
			 *
			 * 1. Length of the token up to 30 and divide by 30, add 1.
			 * 2. Round weight to 4 decimal points.
			 */
			$this->weight	= (($this->length >= 30 ? 30 : $this->length) / 30) + 1;
			$this->weight	= round($this->weight, 4);
		}
		else
		{
			// Populate the token instance.
			$this->term		= $term;
			$this->stem		= $this->search->stem($this->term, $lang);
			$this->numeric	= (is_numeric($this->term) || (bool) preg_match('#^[0-9,.\-\+]+$#', $this->term));
			$this->common	= $this->numeric ? false : $this->search->isCommonToken($this->term, $lang);
			$this->phrase	= false;
			$this->length	= JString::strlen($this->term);

			/*
			 * Calculate the weight of the token.
			 *
			 * 1. Length of the token up to 15 and divide by 15.
			 * 2. If common term, divide weight by 8.
			 * 3. If numeric, mutiply weight by 1.5.
			 * 4. Round weight to 4 decimal points.
			 */
			$this->weight	= (($this->length >= 15 ? 15 : $this->length) / 15);
			$this->weight	= ($this->common == true ? $this->weight / 8 : $this->weight);
			$this->weight	= ($this->numeric == true ? $this->weight * 1.5 : $this->weight);
			$this->weight	= round($this->weight, 4);
		}
	}
}