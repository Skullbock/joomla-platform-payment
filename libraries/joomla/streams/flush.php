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
 * Joomla Stream Flush Interface.  This interface should be implemented if at all possible as it
 * adds support for writing cached data to the stream.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamFlush
{
	/**
	 * Method to flush stream output.  This method is called in response to fflush().  If you have
	 * cached data in your stream but not yet stored it into the underlying storage, you should
	 * do so now.
	 *
	 * @return  boolean  Boolean true if the cached data was successfully stored (or if there was
	 *                   no data to store).
	 *
	 * @see     fflush()
	 * @since   11.3
	 */
	public function stream_flush();
}
