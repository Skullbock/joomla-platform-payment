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
 * Joomla Stream Stat Interface.  This interface adds stat() functionality to a userland stream wrapper
 * which is used in many filesystem functions.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamStat
{
	/**
	 * Method to retrieve information about a URL.  This method is called in response to all stat()
	 * related functions.
	 *
	 * array(
	 *   0         => device number
	 *   'dev'     => device number
	 *   1         => inode number
	 *   'ino'     => inode number
	 *   2         => inode protection mode
	 *   'mode'    => inode protection node
	 *   3         => number of links
	 *   'nlink'   => number of links
	 *   4         => userid of owner
	 *   'uid'     => userid of owner
	 *   5         => groupid of owner
	 *   'gid'     => groupid of owner
	 *   6         => device type, if inode device
	 *   'rdev'    => device type, if inode device
	 *   7         => size in bytes
	 *   'size'    => size in bytes
	 *   8         => time of last access
	 *   'atime    => time of last access
	 *   9         => time of last modification
	 *   'mtime'   => time of last modification
	 *   10        => time of last inode change
	 *   'ctime'   => time of last inode change
	 *   11        => blocksize of filesystem IO
	 *   'blksize' => blocksize of filesystem IO
	 *   12        => number of blocks allocated
	 *   'blocks'  => number of blocks allocated
	 * );
	 *
	 * @param   string   $path     URL for which to have information returned.
	 * @param   integer  $options  A bitwise mask of options.  The available options are:
	 *                             STREAM_URL_STAT_LINK:   Only return information about the link.
	 *                             STREAM_URL_STAT_QUIET:  Do not raise any errors.
	 *
	 * @return  array  Array of resource information based on the response of stat().
	 *
	 * @see     stat()
	 * @since   11.3
	 */
	public function url_stat($path, $options);
}
