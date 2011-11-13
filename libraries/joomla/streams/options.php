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
 * Joomla Stream Options Interface.  This interface adds the ability to set timeout, blocking and
 * write buffer size options for the userland stream wrapper.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamOptions
{
	/**
	 * Method to set options for the stream resource.
	 * Note: PHP 5.3+
	 *
	 * @param   integer  $option  The option to set for the stream.  One of the following:
	 *                            STREAM_OPTION_BLOCKING: In response to stream_set_blocking().
	 *                            STREAM_OPTION_READ_TIMEOUT: In response to stream_set_timeout();
	 *                            STREAM_OPTION_WRITE_BUFFER: In response to stream_set_write_buffer();
	 * @param   integer  $arg1    First argument to the option.  If the option is:
	 *                            STREAM_OPTION_BLOCKING: Blocking mode [1 = block or 0 = non-blocking].
	 *                            STREAM_OPTION_READ_TIMEOUT: Timeout in seconds.
	 *                            STREAM_OPTION_WRITE_BUFFER: Buffer mode [STREAM_BUFFER_NONE or STREAM_BUFFER_FULL].
	 * @param   integer  $arg2    Second argument to the option.  If the option is:
	 *                            STREAM_OPTION_BLOCKING: Not used.
	 *                            STREAM_OPTION_READ_TIMEOUT: Timeout in microseconds.
	 *                            STREAM_OPTION_WRITE_BUFFER: Buffer size.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     stream_set_blocking()
	 * @see     stream_set_timeout()
	 * @see     stream_set_write_buffer()
	 * @since   11.3
	 */
	public function stream_set_option($option, $arg1, $arg2);
}
