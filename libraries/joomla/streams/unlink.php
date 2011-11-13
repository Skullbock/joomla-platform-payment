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
 * Joomla Stream Unlink Interface.  This interface adds unlink() and rename() functionality
 * for the userland stream wrapper.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamUnlink
{
	/**
	 * Method to rename a file or directory.  This method is called in response to rename().
	 *
	 * @param   string  $source       URL for the file or folder to be renamed.
	 * @param   string  $destination  URL to which the file or folder should be renamed.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     rename()
	 * @since   11.3
	 */
	public function rename($source, $destination);

	/**
	 * Method to remove a resource.  This method is called in response to unlink().
	 *
	 * @param   string   $path  URL for the resource which should be removed.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     unlink()
	 * @since   11.3
	 */
	public function unlink($path);
}
