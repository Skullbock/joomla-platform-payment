<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Search
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JSearchDocument', JPATH_PLATFORM.'/joomla/search/document.php');
JLoader::register('JSearchQuery', JPATH_PLATFORM.'/joomla/search/query');
JLoader::register('JSearchToken', JPATH_PLATFORM.'/joomla/search/token.php');

/**
 * Search API class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearch
{
	/**
	 * @var    boolean  True if PREG supports full unicode and utf-8 support.
	 * @since  12.1
	 */
	protected static $unicodeSupport;

	/**
	 * @var    array  Cached array of content types.
	 * @since  12.1
	 */
	protected static $types;

	/**
	 * @var    array  Cached array of languages and primary language codes.
	 * @since  12.1
	 */
	protected static $languages;

	/**
	 * @var    array  Cached array of common tokens.
	 * @since  12.1
	 */
	protected static $common;

	/**
	 * @var    JDatabase  The database connection to use.
	 * @since  12.1
	 */
	protected $db;

	/**
	 * @var    JSearchTaxonomy  The search index taxonomy class.
	 * @since  12.1
	 */
	protected $taxonomy;

	/**
	 * Constructor.
	 *
	 * @param   mixed      $options  A value that can be imported by JRegistry's constructor.
	 * @param   JDatabase  $db       An optional database connection.
	 *
	 * @return  JSearch
	 *
	 * @since   12.1
	 */
	public function __construct($options, $db = null)
	{
		// Setup the options registry.
		$this->options = new JRegistry($options);

		// Get the stemming information from the options.
		$this->options->def('stem', 1);
		$this->options->def('stemmer', 'porter_en');

		// Get the weighting information from the options.
		$this->options->def('weight.title_multiplier', 1.7);
		$this->options->def('weight.text_multiplier', 0.7);
		$this->options->def('weight.meta_multiplier', 1.2);
		$this->options->def('weight.path_multiplier', 2.0);
		$this->options->def('weight.misc_multiplier', 0.3);

		// Some legacy stuff that should probably live somewhere else.
		$this->options->get('memory_table_limit', 30000);

		// Setup dependency injection for the database connection object.
		if ($db instanceof JDatabase) {
			$this->db = $db;
		}
		else {
			$this->db = JFactory::getDBO();
		}
	}

	/**
	 * Method to get unicode support flag for perl regular expressions.
	 *
	 * @return  boolean
	 *
	 * @since   12.1
	 */
	public function getUnicodeSupport()
	{
		// Detect if we have full UTF-8 and unicode support.
		if (self::$unicodeSupport === null) {
			self::$unicodeSupport = (bool) @preg_match('/\pL/u', 'a');
		}

		return self::$unicodeSupport;
	}

	/**
	 * Method to get a query object given a string and set of options.
	 *
	 * @param   string  $input    The query string for the search request.
	 * @param   array   $options  Additional options for the query object.
	 *
	 * @return  JSearchQuery
	 *
	 * @since   12.1
	 */
	public function getQuery($input = null, $options = array())
	{
		// Make sure the input string is added to the options array.
		$options['input'] = $input;

		return new JSearchQuery($options, $this);
	}

	/**
	 * Method to get the JSearchTaxonomy object for this JSearch instance.
	 *
	 * @return  JSearchTaxonomy
	 *
	 * @since   12.1
	 */
	public function getTaxonomy()
	{
		if ($this->taxonomy instanceof JSearchTaxonomy) {
			return $this->taxonomy;
		}

		// If we do not have a taxonomy object create one.
		$this->taxonomy = new JSearchTaxonomy($this, $this->db);

		return $this->taxonomy;
	}

	/**
	 * Method to parse a language/locale key and return a simple language string.
	 *
	 * @param   string  $lang  The language/locale key, for example: en-GB.
	 *
	 * @return  string  The simple language string, for example: en.
	 *
	 * @since   12.1
	 */
	public function getPrimaryLanguage($lang)
	{
		// Only parse the identifier if necessary.
		if (!isset(self::$languages[$lang])) {
			if (is_callable(array('Locale', 'getPrimaryLanguage'))) {
				// Get the language key using the Locale package.
				self::$languages[$lang] = Locale::getPrimaryLanguage($lang);
			} else {
				// Get the language key using string position.
				self::$languages[$lang] = JString::substr($lang, 0, JString::strpos($lang, '-'));
			}
		}

		return self::$languages[$lang];
	}

	/**
	 * Method to check if a token is common in a language.
	 *
	 * @param   string  $token  The token to test.
	 * @param   string  $lang   The language to reference.
	 *
	 * @return  boolean  True if common, false otherwise.
	 *
	 * @since   12.1
	 */
	public function isCommonToken($token, $lang)
	{
		// Load the common tokens for the language if necessary.
		if (!isset(self::$common[$lang])){
			self::$common[$lang] = FinderIndexerHelper::getCommonWords($lang);
		}

		// Check if the token is in the common array.
		if (in_array($token, self::$common[$lang])) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Method to get the base word of a token.
	 *
	 * @param   string  $token  The token to stem.
	 * @param   string  $lang   The language of the token.
	 * @param   string  $type   The stemmer type to use.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function stem($token, $lang, $type = 'porter_en')
	{
		// Trim apostrophes at either end of the token.
		$token = JString::trim($token, '\'');

		// Trim everything after any apostrophe in the token.
		if (($pos = JString::strpos($token, '\'')) !== false) {
			$token = JString::substr($token, 0, $pos);
		}

		try {
			$stem = JSearchStemmer::getInstance($type)->stem($token, $lang);
		}
		catch (Exception $e) {
			// Perhaps we should log this?
			return $token;
		}

		return $stem;
	}

	/**
	 * Method to parse input into plain text.
	 *
	 * @param   string  $input   The raw input.
	 * @param   string  $format  The format of the input.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 * @throws  Exception on invalid parser.
	 */
	public function parse($input, $format = 'html')
	{
		// Get a parser for the specified format and parse the input.
		return JSearchParser::getInstance($format)->parse($input);
	}

	/**
	 * Method to add a JSearchDocument object to the index.
	 *
	 * @param   JSearchDocument  $doc     The content item to index.
	 * @param   string           $format  The optional format of the content.
	 *
	 * @return  integer  The id of the record in the index.
	 *
	 * @since   12.1
	 * @throws  Exception on database error.
	 */
	public function addDocument(JSearchDocument $doc, $format = 'html')
	{
		// Mark beforeIndexing in the profiler.
		self::$profiler ? self::$profiler->mark('beforeIndexing') : null;

		// Check if the item is in the database.
		$query = $this->db->getQuery(true);
		$query->select('`link_id`, `md5sum`, `views`');
		$query->from($query->qn('#__search_links'));
		$query->where('`url` = '.$query->q($doc->url));

		// Execute the query and check for a database error.
		$this->db->setQuery($query)->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Get the link object if it exists.
		$link = $this->db->loadObject();

		// Get the signatures of the item.
		$curSig = $doc->getSignature($this->options);
		$oldSig = isset($link->md5sum) ? $link->md5sum : null;

		// Get the other item information.
		$linkId = empty($link->link_id) ? null : $link->link_id;
		$isNew	= empty($link->link_id) ? true : false;

		// Check the signatures. If they match, the item is up to date.
		if (!$isNew && $curSig == $oldSig)
		{
			// Check the view count.
			if (!empty($doc->views) && $doc->views > $link->views)
			{
				// Update the view count.
				$this->db->setQuery(
					'UPDATE #__search_links SET views = '.(int) $doc->views.
					' WHERE link_id = '.(int) $linkId
				);
				$this->db->query();

				// Check for a database error.
				if ($this->db->getErrorNum()) {
					throw new Exception($this->db->getErrorMsg(), 500);
				}
			}

			return $linkId;
		}

		/*
		 * If the link already exists, flush all the term maps for the item.
		 * Maps are stored in 16 tables so we need to iterate through and flush
		 * each table one at a time.
		 */
		if (!$isNew)
		{
			for ($i = 0; $i <= 15; $i++)
			{
				// Flush the maps for the link.
				$this->db->setQuery(
					'DELETE FROM `#__search_links_terms'.dechex($i).'`' .
					' WHERE `link_id` = '.(int) $linkId
				);
				$this->db->query();

				// Check for a database error.
				if ($this->db->getErrorNum()) {
					throw new Exception($this->db->getErrorMsg(), 500);
				}
			}

			// Remove the taxonomy maps.
			$this->getTaxonomy()->removeMaps($linkId);
		}

		// Mark afterUnmapping in the profiler.
		self::$profiler ? self::$profiler->mark('afterUnmapping') : null;

		// Perform cleanup on the item data.
		$nd = $this->db->getNullDate();
		$doc->publish_start_date	= intval($doc->publish_start_date) != 0 ? $doc->publish_start_date : $nd;
		$doc->publish_end_date		= intval($doc->publish_end_date) != 0 ? $doc->publish_end_date : $nd;
		$doc->start_date			= intval($doc->start_date) != 0 ? $doc->start_date : $nd;
		$doc->end_date				= intval($doc->end_date) != 0 ? $doc->end_date : $nd;

		// Prepare the item description.
		$doc->description	= FinderIndexerHelper::parse($doc->summary);

		/*
		 * Now, we need to enter the item into the links table. If the item
		 * already exists in the database, we need to use an UPDATE query.
		 * Otherwise, we need to use an INSERT to get the link id back.
		 */
		if ($isNew)
		{
			// Insert the link.
			$this->db->setQuery(
				'INSERT INTO `#__search_links`'
				. ' SET url = '.$this->db->quote($doc->url)
				. ', route = '.$this->db->quote($doc->route)
				. ', title = '.$this->db->quote($doc->title)
				. ', author = '.$this->db->quote($doc->author)
				. ', description = '.$this->db->quote($doc->description)
				. ', indexdate = NOW()'
				. ', size = '.(int) $doc->size
				. ', published = 1'
				. ', state = '.(int) $doc->state
				. ', access = '.(int) $doc->access
				. ', language = '.$this->db->quote($doc->language)
				. ', type_id = '.(int) $doc->type_id
				. ', object = '.$this->db->quote(serialize($doc))
				. ', publish_start_date = '.$this->db->quote($doc->publish_start_date)
				. ', publish_end_date = '.$this->db->quote($doc->publish_end_date)
				. ', start_date = '.$this->db->quote($doc->start_date)
				. ', end_date = '.$this->db->quote($doc->end_date)
				. ', list_price = '.$this->db->quote($doc->list_price)
				. ', sale_price = '.$this->db->quote($doc->sale_price)
				. ', ordering = '.(int) $doc->ordering
				. ', views = '.(int) $doc->views
			);
			$this->db->query();

			// Check for a database error.
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}

			// Get the link id.
			$linkId = (int) $this->db->insertid();
		}
		else
		{
			// Update the link.
			$this->db->setQuery(
				'UPDATE `#__search_links`'
				. ' SET route = '.$this->db->quote($doc->route)
				. ', title = '.$this->db->quote($doc->title)
				. ', author = '.$this->db->quote($doc->author)
				. ', description = '.$this->db->quote($doc->description)
				. ', indexdate = NOW()'
				. ', size = '.(int) $doc->size
				. ', state = '.(int) $doc->state
				. ', access = '.(int) $doc->access
				. ', language = '.$this->db->quote($doc->language)
				. ', type_id = '.(int) $doc->type_id
				. ', object = '.$this->db->quote(serialize($doc))
				. ', publish_start_date = '.$this->db->quote($doc->publish_start_date)
				. ', publish_end_date = '.$this->db->quote($doc->publish_end_date)
				. ', start_date = '.$this->db->quote($doc->start_date)
				. ', end_date = '.$this->db->quote($doc->end_date)
				. ', list_price = '.$this->db->quote($doc->list_price)
				. ', sale_price = '.$this->db->quote($doc->sale_price)
				. ', ordering = '.(int) $doc->ordering
				. ', views = '.(int) $doc->views
				. ' WHERE link_id = '.(int) $linkId
			);
			$this->db->query();

			// Check for a database error.
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}
		}

		// Set up the variables we will need during processing.
		$tokens = array();
		$count	= 0;

		// Mark afterLinking in the profiler.
		self::$profiler ? self::$profiler->mark('afterLinking') : null;

		// Truncate the tokens tables and check for a database error.
		$this->db->setQuery('TRUNCATE TABLE `#__search_tokens`')->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Truncate the tokens aggregate table and check for a database error.
		$this->db->setQuery('TRUNCATE TABLE `#__search_tokens_aggregate`')->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		/*
		 * Process the item's content. The items can customize their
		 * processing instructions to define extra properties to process
		 * or rearrange how properties are weighted.
		 */
		foreach ($doc->getInstructions() as $group => $properties)
		{
			// Iterate through the properties of the group.
			foreach ($properties as $property)
			{
				// Check if the property exists in the item.
				if (empty($doc->$property)) {
					continue;
				}

				// Tokenize the property.
				if (is_array($doc->$property))
				{
					// Tokenize an array of content and add it to the database.
					foreach ($doc->$property as $ip)
					{
						// If the group is path, we need to a few extra processing
						// steps to strip the extension and convert slashes and dashes
						// to spaces.
						if ($group === self::PATH_CONTEXT) {
							$ip = JFile::stripExt($ip);
							$ip = str_replace('/', ' ', $ip);
							$ip = str_replace('-', ' ', $ip);
						}

						// Tokenize a string of content and add it to the database.
						$count += FinderIndexer::tokenizeToDB($ip, $group, $doc->language, $format);

						// Check if we're approaching the memory limit of the token table.
						if ($count > $this->options->get('memory_table_limit', 30000)) {
							FinderIndexer::toggleTables(false);
						}
					}
				}
				else
				{
					// If the group is path, we need to a few extra processing
					// steps to strip the extension and convert slashes and dashes
					// to spaces.
					if ($group === self::PATH_CONTEXT) {
						$doc->$property = JFile::stripExt($doc->$property);
						$doc->$property = str_replace('/', ' ', $doc->$property);
						$doc->$property = str_replace('-', ' ', $doc->$property);
					}

					// Tokenize a string of content and add it to the database.
					$count += FinderIndexer::tokenizeToDB($doc->$property, $group, $doc->language, $format);

					// Check if we're approaching the memory limit of the token table.
					if ($count > $this->options->get('memory_table_limit', 30000)) {
						FinderIndexer::toggleTables(false);
					}
				}
			}
		}

		/*
		 * Process the item's taxonomy. The items can customize their
		 * taxonomy mappings to define extra properties to map.
		 */
		foreach ($doc->getTaxonomy() as $branch => $nodes)
		{
			// Iterate through the nodes and map them to the branch.
			foreach ($nodes as $node)
			{
				// Add the node to the tree.
				$nodeId = $this->getTaxonomy()->addNode($branch, $node->title, $node->state, $node->access);

				// Add the link => node map.
				$this->getTaxonomy->addMap($linkId, $nodeId);

				// Tokenize the node title and add them to the database.
				$count += FinderIndexer::tokenizeToDB($node->title, self::META_CONTEXT, $doc->language, $format);
			}
		}

		// Mark afterProcessing in the profiler.
		self::$profiler ? self::$profiler->mark('afterProcessing') : null;

		/*
		 * At this point, all of the item's content has been parsed, tokenized
		 * and inserted into the #__search_tokens table. Now, we need to
		 * aggregate all the data into that table into a more usable form. The
		 * aggregated data will be inserted into #__search_tokens_aggregate
		 * table.
		 */
		$query	= 'INSERT INTO `#__search_tokens_aggregate`' .
				' (`term_id`, `term`, `stem`, `common`, `phrase`, `term_weight`, `context`, `context_weight`)' .
				' SELECT' .
				' t.term_id, t1.term, t1.stem, t1.common, t1.phrase, t1.weight, t1.context,' .
				' ROUND( t1.weight * COUNT( t2.term ) * %F, 8 ) AS context_weight' .
				' FROM (' .
				'   SELECT DISTINCT t1.term, t1.stem, t1.common, t1.phrase, t1.weight, t1.context' .
				'   FROM `#__search_tokens` AS t1' .
				'   WHERE t1.context = %d' .
				' ) AS t1' .
				' JOIN `#__search_tokens` AS t2 ON t2.term = t1.term' .
				' LEFT JOIN `#__search_terms` AS t ON t.term = t1.term' .
				' WHERE t2.context = %d' .
				' GROUP BY t1.term' .
				' ORDER BY t1.term DESC';

		// Iterate through the contexts and aggregate the tokens per context.
		foreach ($state->weights as $context => $multiplier)
		{
			// Execute the query to aggregate the tokens for this context..
			$this->db->setQuery(sprintf($query, $multiplier, $context, $context))->query();
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}
		}

		// Mark afterAggregating in the profiler.
		self::$profiler ? self::$profiler->mark('afterAggregating') : null;

		/*
		 * When we pulled down all of the aggregate data, we did a LEFT JOIN
		 * over the terms table to try to find all the term ids that
		 * already exist for our tokens. If any of the rows in the aggregate
		 * table have a term of 0, then no term record exists for that
		 * term so we need to add it to the terms table.
		 */
		$this->db->setQuery(
			'INSERT IGNORE INTO `#__search_terms`' .
			' (`term`, `stem`, `common`, `phrase`, `weight`, `soundex`)' .
			' SELECT ta.term, ta.stem, ta.common, ta.phrase, ta.term_weight, SOUNDEX(ta.term)' .
			' FROM `#__search_tokens_aggregate` AS ta' .
			' WHERE ta.term_id = 0' .
			' GROUP BY ta.term'
		);
		$this->db->query();

		// Check for a database error.
		if ($this->db->getErrorNum())
		{
			$this->db->setQuery(
				'REPLACE INTO `#__search_terms`' .
				' (`term`, `stem`, `common`, `phrase`, `weight`, `soundex`)' .
				' SELECT ta.term, ta.stem, ta.common, ta.phrase, ta.term_weight, SOUNDEX(ta.term)' .
				' FROM `#__search_tokens_aggregate` AS ta' .
				' WHERE ta.term_id = 0' .
				' GROUP BY ta.term'
			);
			$this->db->query();

			// Check for a database error.
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}
		}

		/*
		 * Now, we just inserted a bunch of new records into the terms table
		 * so we need to go back and update the aggregate table with all the
		 * new term ids.
		 */
		$this->db->setQuery(
			'UPDATE `#__search_tokens_aggregate` AS ta' .
			' JOIN `#__search_terms` AS t ON t.term = ta.term' .
			' SET ta.term_id = t.term_id' .
			' WHERE ta.term_id = 0'
		);
		$this->db->query();

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Mark afterTerms in the profiler.
		self::$profiler ? self::$profiler->mark('afterTerms') : null;

		/*
		 * After we've made sure that all of the terms are in the terms table
		 * and the aggregate table has the correct term ids, we need to update
		 * the links counter for each term by one.
		 */
		$this->db->setQuery(
			'UPDATE `#__search_terms` AS t' .
			' INNER JOIN `#__search_tokens_aggregate` AS ta ON ta.term_id = t.term_id' .
			' SET t.links = t.links + 1'
		);
		$this->db->query();

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Mark afterTerms in the profiler.
		self::$profiler ? self::$profiler->mark('afterTerms') : null;

		/*
		 * Before we can insert all of the mapping rows, we have to figure out
		 * which mapping table the rows need to be inserted into. The mapping
		 * table for each term is based on the first character of the md5 of
		 * the first character of the term. In php, it would be expressed as
		 * substr(md5(substr($token, 0, 1)), 0, 1)
		 */
		$this->db->setQuery(
			'UPDATE `#__search_tokens_aggregate`' .
			' SET `map_suffix` = SUBSTR(MD5(SUBSTR(`term`, 1, 1)), 1, 1)'
		);
		$this->db->query();

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		/*
		 * At this point, the aggregate table contains a record for each
		 * term in each context. So, we're going to pull down all of that
		 * data while grouping the records by term and add all of the
		 * sub-totals together to arrive at the final total for each token for
		 * this link. Then, we insert all of that data into the appropriate
		 * mapping table.
		 */
		for ($i = 0; $i <= 15; $i++)
		{
			// Get the mapping table suffix.
			$suffix = dechex($i);

			/*
			 * We have to run this query 16 times, one for each link => term
			 * mapping table.
			 */
			$this->db->setQuery(
				'INSERT INTO `#__search_links_terms'.$suffix.'`' .
				' (`link_id`, `term_id`, `weight`)' .
				' SELECT '.(int)$linkId.', `term_id`,' .
				' ROUND(SUM(`context_weight`), 8)' .
				' FROM `#__search_tokens_aggregate`' .
				' WHERE `map_suffix` = '.$this->db->quote($suffix) .
				' GROUP BY `term`' .
				' ORDER BY `term` DESC'
			);
			$this->db->query();

			// Check for a database error.
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}
		}

		// Mark afterMapping in the profiler.
		self::$profiler ? self::$profiler->mark('afterMapping') : null;

		// Update the signature.
		$this->db->setQuery(
			'UPDATE `#__search_links`'
			. ' SET md5sum = '.$this->db->quote($curSig)
			. ' WHERE link_id = '.(int)$linkId
		);
		$this->db->query();

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Mark afterSigning in the profiler.
		self::$profiler ? self::$profiler->mark('afterSigning') : null;

		// Truncate the tokens tables and check for a database error.
		$this->db->setQuery('TRUNCATE TABLE `#__search_tokens`')->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Truncate the tokens aggregate table and check for a database error.
		$this->db->setQuery('TRUNCATE TABLE `#__search_tokens_aggregate`')->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Toggle the token tables back to memory tables.
		FinderIndexer::toggleTables(true);

		// Mark afterTruncating in the profiler.
		self::$profiler ? self::$profiler->mark('afterTruncating') : null;

		return $linkId;
	}

	/**
	 * Method to remove a JSearchDocument object from the index by id.
	 *
	 * @param   integer  $docId  The id of the document to remove.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  Exception on database error.
	 */
	public function removeDocument($docId)
	{
		// Update the link counts and remove the mapping records.
		for ($i = 0; $i <= 15; $i++)
		{
			// Remove all records from the mapping tables.
			$query = $this->db->getQuery(true);
			$query->update($query->qn('#__search_terms').' AS t');
			$query->innerJoin($query->qn('#__search_links_terms'.dechex($i)).' AS m ON m.term_id = t.term_id');
			$query->set('t.links = t.links - 1');
			$query->where($query->qn('link_id').' = '.(int) $docId);

			// Execute the query and check for a database error.
			$this->db->setQuery($query)->query();
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}

			// Remove all records from the mapping tables.
			$query = $this->db->getQuery(true);
			$query->delete($query->qn('#__search_links_terms'.dechex($i)));
			$query->where($query->qn('link_id').' = '.(int) $docId);

			// Execute the query and check for a database error.
			$this->db->setQuery($query)->query();
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}
		}

		// Delete all orphaned terms.
		$query = $this->db->getQuery(true);
		$query->delete($query->qn('#__search_terms'));
		$query->where($query->qn('links').' <= 0');

		// Execute the query and check for a database error.
		$this->db->setQuery($query)->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Delete the document from the index.
		$query = $this->db->getQuery(true);
		$query->delete($query->qn('#__search_links'));
		$query->where($query->qn('link_id').' = '.(int) $docId);

		// Execute the query and check for a database error.
		$this->db->setQuery($query)->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Remove the taxonomy maps.
		$this->getTaxonomy()->removeMaps($docId);

		// Remove the oprhaned taxonomy nodes.
		$this->getTaxonomy()->removeOrphanNodes();
	}

	/**
	 * Method to optimize the index.  This method performs any routine maintenance
	 * to keep the index performing at peak levels.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  Exception on database error.
	 */
	public function optimize()
	{
		// Delete all orphaned terms.
		$query = $this->db->getQuery(true);
		$query->delete($query->qn('#__search_terms'));
		$query->where($query->qn('links').' <= 0');

		// Execute the query and check for a database error.
		$this->db->setQuery($query)->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Optimize the terms table, and check for a database error.
		$this->db->setQuery('OPTIMIZE TABLE '.$query->qn('#__search_terms'))->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Optimize the links table, and check for a database error.
		$this->db->setQuery('OPTIMIZE TABLE '.$query->qn('#__search_links'))->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Optimize all the terms mapping tables.
		for ($i = 0; $i <= 15; $i++)
		{
			// Optimize the terms mapping table, and check for a database error.
			$this->db->setQuery('OPTIMIZE TABLE '.$query->qn('#__search_links_terms'.dechex($i)))->query();
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}
		}

		// Remove the oprhaned taxonomy nodes.
		$this->getTaxonomy()->removeOrphanNodes();

		// Optimize the taxonomy mapping table, and check for a database error.
		$this->db->setQuery('OPTIMIZE TABLE '.$query->qn('#__search_taxonomy_map'))->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}
	}

	/**
	 * Method to add a content type to the index.
	 *
	 * @param   string  $title  The type of content. For example: PDF.
	 * @param   string  $mime   The mime type of the content. For example: pdf.
	 *
	 * @return  integer  The id of the content type.
	 *
	 * @since   12.1
	 * @throws  Exception on database error.
	 */
	public function addContentType($title, $mime = null)
	{
		// Check if the types are loaded.
		if (empty(self::$types)) {

			// Build the query to get the types.
			$query = $this->db->getQuery(true);
			$query->select('*');
			$query->from($query->qn('#__search_types'));

			// Execute the query and check for a database error.
			$this->db->setQuery($query)->query();
			if ($this->db->getErrorNum()) {
				throw new Exception($this->db->getErrorMsg(), 500);
			}

			// Load the type objects.
			self::$types = $this->db->loadObjectList('title');
		}

		// Check if the type already exists.
		if (isset(self::$types[$title])) {
			return (int) self::$types[$title]->id;
		}

		// Build the query to add the type.
		$query = $this->db->getQuery(true);
		$query->insert($query->qn('#__search_types'));
		$query->set($query->qn('title').' = '.$query->q($title));
		$query->set($query->qn('mime').' = '.$query->q($mime));

		// Execute the insert and check for a database error.
		$this->db->setQuery($query)->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Return the new id.
		return (int) $this->db->insertid();
	}

	/**
	 * Method to get an array of common terms for a language.
	 *
	 * @param   string  $lang  The language to use.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 * @throws  Exception on database error.
	 */
	protected function getCommonTerms($lang)
	{
		// Create the query to load all the common terms for the language.
		$query = $this->db->getQuery(true);;
		$query->select('term');
		$query->from('#__search_terms_common');
		$query->where($query->qn('language').' = '.$query->q($lang));

		// Execute the query and check for a database error.
		$this->db->setQuery($query)->query();
		if ($this->db->getErrorNum()) {
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Load all of the common terms for the language.
		return $this->db->loadColumn();
	}
}
