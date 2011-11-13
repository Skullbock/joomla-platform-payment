<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Stream
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * Joomla Stream Interface.  This is the minimal interface to implement a userland stream wrapper.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
class JStreamStorageWinCache extends JStreamStorage
{
	/**
	 * Method to add a storage entry.
	 *
	 * @param   string   $key
	 * @param   string   $value
	 * @param   integer  $ttl
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function addStorageEntry($key, $value, $ttl)
	{
		return wincache_ucache_add($key, $value, $ttl);
	}

	/**
	 * Method to get a storage entry value from a key.
	 *
	 * @param   string  $key
	 *
	 * @return  string
	 *
	 * @since   11.3
	 */
	protected function getStorageEntry($key)
	{
		return wincache_ucache_get($key);
	}

	/**
	 * Method to get information about a storage entry for a key.
	 *
	 * @param   string  $key
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	protected function getStorageEntryInfo($key)
	{
		$data = wincache_ucache_info(false, $key);
		return array(
			'size' => (int) $data['ucache_entries'][1]['value_size'],
			'time' => (int) (time() - $data['ucache_entries'][1]['age_seconds'])
		);
	}

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string  $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function isStorageEntrySet($key)
	{
		return wincache_ucache_exists($key);
	}

	/**
	 * Method to get a lock on a storage entry.
	 *
	 * @param   string   $key
	 * @param   boolean  $exclusive
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function lockStorageEntry($key, $exclusive = false)
	{
		return wincache_lock($key, $exclusive);
	}

	/**
	 * Method to remove a storage entry for a key.
	 *
	 * @param   string  $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function removeStorageEntry($key)
	{
		return wincache_ucache_delete($key);
	}

	/**
	 * Method to set a value for a storage entry.
	 *
	 * @param   string   $key
	 * @param   string   $value
	 * @param   integer  $ttl
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function setStorageEntry($key, $value, $ttl)
	{
		return wincache_ucache_set($key, $value, $ttl);
	}

	/**
	 * Method to release a lock on a storage entry.
	 *
	 * @param   string   $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function unlockStorageEntry($key)
	{
		return wincache_unlock($key);
	}
}