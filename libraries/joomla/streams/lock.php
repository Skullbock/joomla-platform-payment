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
 * Joomla Stream Lock Interface.  This interface adds locking support to userland stream wrappers.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamLock
{
	/**
	 * Method to set locking for the resource.  This method is called in response to flock(),
	 * when file_put_contents() (when flags contains LOCK_EX), stream_set_blocking() and when
	 * closing the stream (LOCK_UN).
	 *
	 * @param   integer  $option  One of the following advisories:
	 *                            LOCK_SH to acquire a shared lock (reader).
	 *                            LOCK_EX to acquire an exclusive lock (writer).
	 *                            LOCK_UN to release a lock (shared or exclusive).
	 *                            LOCK_NB if you don't want flock() to block while locking.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     flock()
	 * @see     file_put_contents()
	 * @see     stream_set_blocking()
	 * @since   11.3
	 */
	public function stream_lock($option);
}
