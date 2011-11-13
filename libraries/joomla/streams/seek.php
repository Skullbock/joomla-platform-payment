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
 * Joomla Stream Seek Interface.  This interface adds the ability for the userland stream wrapper
 * to move forward in the data.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamSeek
{
	/**
	 * Method to seek to a specific position in the stream resource.  This method is called in
	 * response to fseek().  The read/write position of the stream should be updated according to
	 * the offset and whence.
	 *
	 * @param   integer  $offset  The byte offset within the stream to seek to.
	 * @param   integer  $whence  How to set the offset.  Possibilities are:
	 *                            SEEK_SET: Set position equal to offset bytes.
	 *                            SEEK_CUR: Set position to current location plus offset.
	 *                            SEEK_END: Set position to end-of-file plus offset.
	 *
	 * @return  boolean  True on success
	 *
	 * @see     fseek()
	 * @since   11.3
	 */
	public function stream_seek($offset, $whence = SEEK_SET);

	/**
	 * Method to retrieve the current position of a stream.  This method is called in response to
	 * fseek() to determine the current position.
	 *
	 * @return  integer  The current position of the stream.
	 *
	 * @see     fseek()
	 * @since   11.3
	 */
	public function stream_tell();
}
