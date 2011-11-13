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
 * Joomla Stream Permissions Interface.  This interface adds the ability for the userland stream
 * wrapper to support changing of ownership and permissions.  This is only supported in PHP 5.4+
 *
 * @package     Joomla.Platform
 * @subpackage  Stream
 * @since       11.3
 */
interface JStreamPermissions
{
	/**
	 * Method to create a directory.  This method is called in response to mkdir().
	 * Note: PHP 5.4+
	 *
	 * @param   string   $path    URL for the file or folder for which stream metadata should be set.
	 * @param   integer  $option  One of the following metadata types:
	 *                            PHP_STREAM_META_TOUCH
	 *                            PHP_STREAM_META_OWNER_NAME
	 *                            PHP_STREAM_META_OWNER
	 *                            PHP_STREAM_META_GROUP_NAME
	 *                            PHP_STREAM_META_GROUP
	 *                            PHP_STREAM_META_ACCESS
	 * @param   integer  $args    If the $option is:
	 *                            PHP_STREAM_META_TOUCH: array of arguments to the touch() function.
	 *                            PHP_STREAM_META_OWNER_NAME: string name of the owner.
	 *                            PHP_STREAM_META_GROUP_NAME: string name of the group.
	 *                            PHP_STREAM_META_OWNER: integer value of the owner.
	 *                            PHP_STREAM_META_GROUP: integer value of the group.
	 *                            PHP_STREAM_META_ACCESS: integer value of the chmod() function.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     touch()
	 * @see     chmod()
	 * @see     chown()
	 * @see     chgrp()
	 * @since   11.3
	 */
	public function stream_metadata($path, $option, $args);
}
