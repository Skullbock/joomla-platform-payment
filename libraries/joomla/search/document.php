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
 * Document class for the Joomla search package.
 *
 * This class uses magic __get() and __set() methods to prevent properties
 * being added that might confuse the system. All properties not explicitly
 * declared will be pushed into the elements array and can be accessed
 * explicitly using the getElement() method.
 *
 * @package     Joomla.Platform
 * @subpackage  Search
 * @since       12.1
 */
class JSearchDocument
{
	/**
	 * @var    integer  The title context identifier.
	 * @since  12.1
	 */
	const TITLE_CONTEXT = 1;

	/**
	 * @var    integer  The text context identifier.
	 * @since  12.1
	 */
	const TEXT_CONTEXT = 2;

	/**
	 * @var    integer  The meta context identifier.
	 * @since  12.1
	 */
	const META_CONTEXT = 3;

	/**
	 * @var    integer  The path context identifier.
	 * @since  12.1
	 */
	const PATH_CONTEXT = 4;

	/**
	 * @var    integer  The misc context identifier.
	 * @since  12.1
	 */
	const MISC_CONTEXT = 5;

	/**
	 * @var    array  An array of extra result properties.
	 * @since  12.1
	 */
	protected $elements = array();

	/**
	 * This array tells the indexer which properties should be indexed and what
	 * weights to use for those properties.
	 *
	 * @var    array  The default indexer processing instructions.
	 * @since  12.1
	 */
	protected $instructions = array(
		self::TITLE_CONTEXT	=> array('title', 'subtitle', 'id'),
		self::TEXT_CONTEXT	=> array('summary', 'body'),
		self::META_CONTEXT	=> array('meta', 'list_price', 'sale_price'),
		self::PATH_CONTEXT	=> array('path', 'alias'),
		self::MISC_CONTEXT	=> array('comments'),
	);

	/**
	 * The indexer will use this data to create taxonomy mapping entries for
	 * the item so that it can be filtered by type, label, category, section,
	 * or whatever.
	 *
	 * @var    array  The taxonomy data for the node.
	 * @since  12.1
	 */
	protected $taxonomy = array();

	/**
	 * @var    string  The content URL.
	 * @since  12.1
	 */
	public $url;

	/**
	 * @var    string  The content route.
	 * @since  12.1
	 */
	public $route;

	/**
	 * @var    string  The content title.
	 * @since  12.1
	 */
	public $title;

	/**
	 * @var    string  The content description.
	 * @since  12.1
	 */
	public $description;

	/**
	 * @var    integer  The size of the content data.
	 * @since  12.1
	 */
	public $size;

	/**
	 * @var    integer  The published state of the result.
	 * @since  12.1
	 */
	public $published;

	/**
	 * @var    integer  The content published state.
	 * @since  12.1
	 */
	public $state;

	/**
	 * @var    integer  The content access level.
	 * @since  12.1
	 */
	public $access;

	/**
	 * @var    string  The content language.
	 * @since  12.1
	 */
	public $language = 'en-GB';

	/**
	 * @var    string  The publishing start date.
	 * @since  12.1
	 */
	public $publish_start_date;

	/**
	 * @var    string  The publishing end date.
	 * @since  12.1
	 */
	public $publish_end_date;

	/**
	 * @var    string  The generic start date.
	 * @since  12.1
	 */
	public $start_date;

	/**
	 * @var    string  The generic end date.
	 * @since  12.1
	 */
	public $end_date;

	/**
	 * @var    mixed  The item list price.
	 * @since  12.1
	 */
	public $list_price;

	/**
	 * @var    mixed  The item sale price.
	 * @since  12.1
	 */
	public $sale_price;

	/**
	 * @var    integer  The item ordering.
	 * @since  12.1
	 */
	public $ordering;

	/**
	 * @var    integer  The item view count.
	 * @since  12.1
	 */
	public $views;

	/**
	 * @var    integer  The content type id. This is set by the adapter.
	 * @since  12.1
	 */
	public $type_id;

	/**
	 * @var    JSearch  The search API instance to use internally.
	 * @since  12.1
	 */
	protected $search;

	/**
	 * Method to construct the document object.
	 *
	 * @param   JSearch  $search  The search API instance to use internally.
	 *
	 * @return  JSearchDocument
	 *
	 * @since   12.1
	 */
	public function __construct(JSearch $search)
	{
		// Set the internal JSearch instance.
		$this->search = $search;
	}

	/**
	 * The magic set method is used to push aditional values into the elements
	 * array in order to preserve the cleanliness of the object.
	 *
	 * @param   string  $name   The name of the element.
	 * @param   mixed   $value  The value of the element.
	 *
	 * @return	void
	 *
	 * @since   12.1
	 */
	public function __set($name, $value)
	{
		$this->elements[$name] = $value;
	}

	/**
	 * The magic get method is used to retrieve additional element values
	 * from the elements array.
	 *
	 * @param   string  $name  The name of the element.
	 *
	 * @return  mixed  The value of the element if set, null otherwise.
	 *
	 * @since   12.1
	 */
	public function __get($name)
	{
		// Get the element value if set.
		if (array_key_exists($name, $this->elements)) {
			return $this->elements[$name];
		} else {
			return null;
		}
	}

	/**
	 * The magic isset method is used to check the state of additional element
	 * values in the elements array.
	 *
	 * @param   string   $name  The name of the element.
	 *
	 * @return  boolean  True if set, false otherwise.
	 *
	 * @since   12.1
	 */
	public function __isset($name)
	{
		return isset($this->elements[$name]);
	}

	/**
	 * The magic unset method is used to unset additional element values in the
	 * elements array.
	 *
	 * @param   string  $name  The name of the element.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function __unset($name)
	{
		unset($this->elements[$name]);
	}

	/**
	 * Method to get a document's unique signature as an md5 hash.
	 *
	 * @param   object  $options  The indexer's options.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function getSignature($options = null)
	{
		// Clone the item for signing.
		$data = clone $this;

		// Remove views information.
		unset($data->views);

		return md5(serialize(array($data, $options)));
	}

	/**
	 * Method to retrieve additional element values from the elements array.
	 *
	 * @param   string  $name  The name of the element.
	 *
	 * @return  mixed  The value of the element if set, null otherwise.
	 *
	 * @since   12.1
	 */
	public function getElement($name)
	{
		// Get the element value if set.
		if (array_key_exists($name, $this->elements)) {
			return $this->elements[$name];
		} else {
			return null;
		}
	}

	/**
	 * Method to set additional element values in the elements array.
	 *
	 * @param   string  $name   The name of the element.
	 * @param   mixed   $value  The value of the element.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setElement($name, $value)
	{
		$this->elements[$name] = $value;
	}

	/**
	 * Method to get all processing instructions.
	 *
	 * @return  array  An array of processing instructions.
	 *
	 * @since   12.1
	 */
	public function getInstructions()
	{
		return $this->instructions;
	}

	/**
	 * Method to add a processing instruction for an item property.
	 *
	 * @param   string  $group     The group to associate the property with.
	 * @param   string  $property  The property to process.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function addInstruction($group, $property)
	{
		// Check if the group exists. We can't add instructions for unknown groups.
		if (array_key_exists($group, $this->instructions))
		{
			// Check if the property exists in the group.
			if (!in_array($property, $this->instructions[$group]))
			{
				// Add the property to the group.
				$this->instructions[$group][] = $property;
			}
		}
	}

	/**
	 * Method to remove a processing instruction for an item property.
	 *
	 * @param   string  $group     The group to associate the property with.
	 * @param   string  $property  The property to process.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function removeInstruction($group, $property)
	{
		// Check if the group exists. We can't remove instructions for unknown groups.
		if (array_key_exists($group, $this->instructions))
		{
			// Search for the property in the group.
			$key = array_search($property, $this->instructions[$group]);

			// If the property was found, remove it.
			if ($key !== false) {
				unset($this->instructions[$group][$key]);
			}
		}
	}

	/**
	 * Method to get the taxonomy maps for an item.
	 *
	 * @param   string  $branch  The taxonomy branch to get.
	 *
	 * @return  array  An array of taxonomy maps.
	 *
	 * @since   12.1
	 */
	public function getTaxonomy($branch = null)
	{
		// Get the taxonomy branch if available.
		if ($branch !== null && isset($this->taxonomy[$branch]))
		{
			// Filter the input.
			if ($this->search->getUnicodeSupport()) {
				$branch	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $branch);
			} else {
				$quotes = html_entity_decode('&#8216;&#8217;&#39;', ENT_QUOTES, 'UTF-8');
				$branch	= preg_replace('#[^\w\d'.$quotes.'+-.,]+#mi', ' ', $branch);
			}

			return $this->taxonomy[$branch];
		}

		return $this->taxonomy;
	}

	/**
	 * Method to add a taxonomy map for an item.
	 *
	 * @param   string   $branch  The title of the taxonomy branch to add the node to.
	 * @param   string   $title   The title of the taxonomy node.
	 * @param   integer  $state   The published state of the taxonomy node.
	 * @param   integer  $access  The access level of the taxonomy node.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function addTaxonomy($branch, $title, $state = 1, $access = 0)
	{
		// Filter the input.
		if ($this->search->getUnicodeSupport()) {
			$branch	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $branch);
			$title	= preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $title);
		} else {
			$quotes = html_entity_decode('&#8216;&#8217;&#39;', ENT_QUOTES, 'UTF-8');
			$branch	= preg_replace('#[^\w\d'.$quotes.'+-.,]+#mi', ' ', $branch);
			$title	= preg_replace('#[^\w\d'.$quotes.'+-.,]+#mi', ' ', $title);
		}

		// Create the taxonomy node.
		$node = new stdClass;
		$node->title	= $title;
		$node->state	= (int) $state;
		$node->access	= (int) $access;

		// Add the node to the taxonomy branch.
		$this->taxonomy[$branch][$node->title] = $node;
	}

	/**
	 * Method to remove a taxonomy map for an item.
	 *
	 * @param   string  $branch  The title of the taxonomy branch to add the node to.
	 * @param   string  $title   The title of the taxonomy node.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function removeTaxonomy($branch, $title)
	{
		if (isset($this->taxonomy[$branch][$title])) {
			unset($this->taxonomy[$branch][$title]);
		}
	}
}