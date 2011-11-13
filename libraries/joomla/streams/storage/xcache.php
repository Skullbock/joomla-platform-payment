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
class JStreamStorageApc extends JStreamStorage
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
		if (!xcache_isset($key))
		{
			// Add the time value.
			xcache_set($key.'_time', time(), $ttl+1);

			return xcache_set($key, $value, $ttl);
		}

		return false;
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
		return xcache_get($key);
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
		return array(
			'size' => (int) strlen(xcache_get($key)),
			'time' => (int) xcache_get($key.'_time')
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
		return xcache_isset($key);
	}

	/**
	 * Method to get a lock on a storage entry.  We are going to bluff here so that locking can be
	 * used by other storage wrapper types consistently.  This shoiuldn't be any sort of issue
	 * because we expect XCache to be fairly atomic in writes.
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
		return true;
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
		// Delete the time value.
		xcache_unset($key.'_time');

		return xcache_unset($key);
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
		// Add the time value.
		xcache_set($key.'_time', time(), $ttl+1);

		return xcache_set($key, $value, $ttl);
	}

	/**
	 * Method to release a lock on a storage entry.  We are going to bluff here so that locking can be
	 * used by other storage wrapper types consistently.  This shoiuldn't be any sort of issue
	 * because we expect XCache to be fairly atomic in writes.
	 *
	 * @param   string   $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	protected function unlockStorageEntry($key)
	{
		return true;
	}
}