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
interface JStream
{
	/**
	 * Method to close a resource.  This method is called in response to fclose().  All resources
	 * that were locked, or allocated, by the wrapper should be released.
	 *
	 * @return  void
	 *
	 * @see     fclose()
	 * @since   11.3
	 */
	public function stream_close();

	/**
	 * Method to test for end-of-file on a resource.  This method is called in response to feof().
	 *
	 * @return  boolean  Boolean true if the read/write position is at the end of the stream and
	 *                   if no more data is available to be read.
	 *
	 * @see     feof()
	 * @since   11.3
	 */
	public function stream_eof();

	/**
	 * Method to open a stream resource.  This method is called immediately after the wrapper is
	 * initialized (i.e. by fopen() and file_get_contents()).
	 *
	 * You are responsible for checking that mode is valid for the path requested.  If STREAM_USE_PATH
	 * is set then use the include_path.  If STREAM_REPORT_ERRORS is set then use trigger_error()
	 * when errors are found.
	 *
	 * @param   string   $path         The URL for the resource to be opened.
	 * @param   integer  $mode         The mode used to open the file, as detailed for fopen().
	 * @param   integer  $options      A bitwise mask of options for the resource.
	 * @param   string   &$openedPath  If the path is opened successfully, and STREAM_USE_PATH is
	 *                                 set in options, opened_path should be set to the full path
	 *                                 of the file/resource that was actually opened.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     fopen()
	 * @see     file_get_contents()
	 * @since   11.3
	 */
	public function stream_open($path, $mode, $options, &$openedPath);

	/**
	 * Method to read data from the resource.  This method is called in response to fread()
	 * and fgets().
	 *
	 * fread() and fgets() - return up-to count bytes of data from the current
	 * read/write position as a string.  If no more data is available, return
	 * either FALSE or an empty string. Update the read/write position of the
	 * stream by the number of bytes that were successfully read.
	 *
	 * @param   integer  $count  The number of bytes that should be returned from the current position.
	 *
	 * @return  mixed  If there are less than count bytes available, return as many as are
	 *                 available. If no more data is available, return either false or an empty string.
	 *
	 * @see     fread()
	 * @see     fgets()
	 * @since   11.3
	 */
	public function stream_read($count);

	/**
	 * Method to retrieve information about a stream resource.  This method is called in response
	 * to fstat().
	 *
	 * @return  array  Array of resource information based on the response of stat().  At least with
	 *                 offsets [dev, ino, mode, nlink, uid, gid, size, atime, mtime, ctime].
	 *
	 * @see     fstat()
	 * @since   11.3
	 */
	public function stream_stat();

	/**
	 * Method to write data to the resource.  This method is called in response to fwrite().
	 *
	 * @param   string  $data  Data to be stored to the stream.
	 *
	 * @return  integer  The number of bytes that were successfully stored, or 0 if none could be stored.
	 *
	 * @see     fwrite()
	 * @since   11.3
	 */
	public function stream_write($data);
}
