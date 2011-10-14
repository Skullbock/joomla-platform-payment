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
JLoader::register('JSearchToken', JPATH_PLATFORM.'/joomla/search/token.php');

/**
 * Tokenizer class for the Joomla search package.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearchTokenizer
{
	/**
	 * @var    array  An array of already tokenized strings to prevent double work.
	 * @since  12.1
	 */
	protected static $cache;

	/**
	 * @var    string  A list of quotes to be used in string sanitization.
	 * @since  12.1
	 */
	protected $quotes;

	/**
	 * @var    JSearch  The search API instance to use internally.
	 * @since  12.1
	 */
	protected $search;

	/**
	 * Method to construct the tokenizer object.
	 *
	 * @param   JSearch  $search  The search API instance to use internally.
	 *
	 * @return  JSearchTokenizer
	 *
	 * @since   12.1
	 */
	public function __construct(JSearch $search)
	{
		// Set the internal JSearch instance.
		$this->search = $search;

		// Setup the basic quote characters to be used in input sanitization.
		$this->quotes = html_entity_decode('&#8216;&#8217;&#39;', ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Method to tokenize a text string.
	 *
	 * @param   string   $input   The input to tokenize.
	 * @param   string   $lang    The language of the input.
	 * @param   boolean  $phrase  Flag to indicate whether input could be a phrase.
	 *
	 * @return  array  An array of FinderIndexerToken objects.
	 *
	 * @since   12.1
	 */
	public function process($input, $lang, $phrase = false)
	{
		// Create a cache id for the string to be tokenized.
		$store = JString::strlen($input) < 128 ? md5($input.'::'.$lang.'::'.$phrase) : null;

		// Check if the string has been tokenized already.
		if ($store && isset(self::$cache[$store])) {
			return self::$cache[$store];
		}

		// Get the simple language key.
		$lang = $this->search->getPrimaryLanguage($lang);

		// Sanitize the string before tokenization.
		$input = $this->sanitizeInput($input);

		// Explode the normalized string to get the terms.
		$terms	= array();
		$terms = explode(' ', $input);

		/*
		 * If we have Unicode support and are dealing with Chinese text, Chinese
		 * has to be handled specially because there are not necessarily any spaces
		 * between the "words". So, we have to test if the words belong to the Chinese
		 * character set and if so, explode them into single glyphs or "words".
		 */
		if ($lang === 'zh' && $this->search->getUnicodeSupport()) {
			$terms = $this->handleChineseCharacters($terms);
		}

		/*
		 * If we have to handle the input as a phrase, that means we don't
		 * tokenize the individual terms and we do not create the two and three
		 * term combinations. The phrase must contain more than one word!
		 */
		$tokens	= array();
		if (($phrase === true) && (count($terms) > 1)) {
			// Create tokens from the phrase.
			$tokens[] = new JSearchToken($terms, $lang);
		}
		else {
			// Create tokens from the terms.
			for ($i = 0, $n = count($terms); $i < $n; $i++)
			{
				$tokens[] = new JSearchToken($terms[$i], $lang);
			}

			// Create two and three word phrase tokens from the individual words.
			for ($i = 0, $n = count($tokens); $i < $n; $i++)
			{
				// Setup the phrase positions.
				$i2 = $i + 1;
				$i3 = $i + 2;

				// Create the two word phrase.
				if ($i2 < $n && isset($tokens[$i2])) {
					// Tokenize the two word phrase.
					$token = new JSearchToken(array($tokens[$i]->term, $tokens[$i2]->term), $lang, $lang === 'zh' ? '' : ' ');
					$token->derived = true;

					// Add the token to the stack.
					$tokens[] = $token;
				}

				// Create the three word phrase.
				if ($i3 < $n && isset($tokens[$i3])) {
					// Tokenize the three word phrase.
					$token = new JSearchToken(array($tokens[$i]->term, $tokens[$i2]->term, $tokens[$i3]->term), $lang, $lang === 'zh' ? '' : ' ');
					$token->derived = true;

					// Add the token to the stack.
					$tokens[] = $token;
				}
			}
		}

		if ($store) {
			self::$cache[$store] = count($tokens) > 1 ? $tokens : array_shift($tokens);
			return self::$cache[$store];
		}
		else {
			return count($tokens) > 1 ? $tokens : array_shift($tokens);
		}
	}

	/**
	 * Parsing the string input into terms is a multi-step process.
	 *
	 * Regexes:
	 *	1. Remove everything except letters, numbers, quotes, apostrophe, plus, dash, period, and comma.
	 *	2. Remove plus, dash, period, and comma characters located before letter characters.
	 *  3. Remove plus, dash, period, and comma characters located after other characters.
	 *  4. Remove plus, period, and comma characters enclosed in alphabetical characters. Ungreedy.
	 *  5. Remove orphaned apostrophe, plus, dash, period, and comma characters.
	 *  6. Remove orphaned quote characters.
	 *  7. Replace the assorted single quoation marks with the ASCII standard single quotation.
	 *	8. Remove multiple space chracters and replaces with a single space.
	 *
	 * @param   string  $input  The input string to sanitize for tokenization.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	protected function sanitizeInput($input)
	{
		if ($this->search->getUnicodeSupport()) {
			$input	= JString::strtolower($input);
			$input	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $input);
			$input	= preg_replace('#(^|\s)[+-.,]+([\pL\pM]+)#mui', ' $1', $input);
			$input	= preg_replace('#([\pL\pM\pN]+)[+-.,]+(\s|$)#mui', '$1 ', $input);
			$input	= preg_replace('#([\pL\pM]+)[+.,]+([\pL\pM]+)#muiU', '$1 $2', $input); // Ungreedy
			$input	= preg_replace('#(^|\s)[\'+-.,]+(\s|$)#mui', ' ', $input);
			$input	= preg_replace('#(^|\s)[\p{Pi}\p{Pf}]+(\s|$)#mui', ' ', $input);
			$input	= preg_replace('#['.$this->quotes.']+#mui', '\'', $input);
			$input	= preg_replace('#\s+#mui', ' ', $input);
			$input	= JString::trim($input);
		} else {
			$input	= JString::strtolower($input);
			$input	= preg_replace('#[^\w\d'.$this->quotes.'+-.,]+#mi', ' ', $input);
			$input	= preg_replace('#(^|\s)[+-.,]+([\w]+)#mi', ' $1', $input);
			$input	= preg_replace('#([\w\d]+)[+-.,]+(\s|$)#mi', '$1 ', $input);
			$input	= preg_replace('#([^\d]+)[+.,]+([^\d]+)#miU', '$1 $2', $input); // Ungreedy
			$input	= preg_replace('#(^|\s)[\'+-.,]+(\s|$)#mi', ' ', $input);
			$input	= preg_replace('#(^|\s)['.$this->quotes.']+(\s|$)#mi', ' ', $input);
			$input	= preg_replace('#['.$this->quotes.']+#mi', '\'', $input);
			$input	= preg_replace('#\s+#mi', ' ', $input);
			$input	= JString::trim($input);
		}

		return $input;
	}

	protected function handleChineseCharacters($terms)
	{
		// Verify proper unicode support.
		if (!$this->search->getUnicodeSupport()) {
			throw new Exception('Unable to process chinese chars without full unicode support.', 500);
		}

		// Iterate through the terms and test if they contain Chinese.
		for ($i = 0, $n = count($terms); $i < $n; $i++)
		{
			$charMatches	= array();
			$charCount		= preg_match_all('#[\p{Han}]#mui', $terms[$i], $charMatches);

			// Split apart any groups of Chinese chracters.
			for ($j = 0; $j < $charCount; $j++)
			{
				$tSplit	= JString::str_ireplace($charMatches[0][$j], '', $terms[$i], false);
				if (!empty($tSplit)) {
					$terms[$i] = $tSplit;
				} else {
					unset($terms[$i]);
				}

				$terms[] = $charMatches[0][$j];
			}
		}

		// Reset array keys.
		$terms = array_values($terms);

		return $terms;
	}
}