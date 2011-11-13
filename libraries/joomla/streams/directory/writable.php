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
 * Joomla Stream Writeable Directory Interface.  This interface adds rmdir() and mkdir() functionality
 * for the userland stream wrapper.
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamDirectoryWriteable extends JStreamDirectory
{
	/**
	 * Method to create a directory.  This method is called in response to mkdir().
	 *
	 * @param   string   $path     URL for the directory which should be created.
	 * @param   integer  $mode     The value passed to mkdir().
	 * @param   integer  $options  A bitwise mask of options for creating the directory.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     mkdir()
	 * @since   11.3
	 */
	public function mkdir($path, $mode, $options);

	/**
	 * Method to remove a directory.  This method is called in response to rmdir().
	 *
	 * @param   string   $path     URL for the directory which should be removed.
	 * @param   integer  $options  A bitwise mask of options for removing the directory.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     rmdir()
	 * @since   11.3
	 */
	public function rmdir($path, $options);
}
