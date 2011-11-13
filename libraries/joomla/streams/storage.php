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
abstract class JStreamStorage implements JStream, JStreamUnlink, JStreamSeek, JStreamLock, JStreamFlush
{
	/**
	 * @var    integer  The current position in the stream.
	 * @since  11.3
	 */
	public $pos = 0;

	/**
	 * @var    string  The name of the storage key.
	 * @since  11.3
	 */
	public $key;

	/**
	 * @var    integer  The time to live for the storage value.
	 * @since  11.3
	 */
	public $ttl;

	/**
	 * @var    string  The data assigned to the storage key.
	 * @since  11.3
	 */
	public $data;

	/**
	 * @var    array  The stat data for the storage entry.  Cached for speed.
	 * @since  11.3
	 */
	public $stat = array();

	/**
	 * @var    boolean  Has the value been changed?
	 * @since  11.3
	 */
	public $changed = false;

	/**
	 * @var    boolean  Is the storage entry readable?
	 * @since  11.3
	 */
	public $read = false;

	/**
	 * @var    boolean  Is the storage entry writeable?
	 * @since  11.3
	 */
	public $write = false;

	/**
	 * Method to close a resource.  This method is called in response to fclose().  All resources
	 * that were locked, or allocated, by the wrapper should be released.
	 *
	 * @return  void
	 *
	 * @see     fclose()
	 * @since   11.3
	 */
	public function stream_close()
	{
		$this->stream_flush();
	}

	/**
	 * Method to test for end-of-file on a resource.  This method is called in response to feof().
	 *
	 * @return  boolean  Boolean true if the read/write position is at the end of the stream and
	 *                   if no more data is available to be read.
	 *
	 * @see     feof()
	 * @since   11.3
	 */
	public function stream_eof()
	{
		return ($this->pos == (strlen($this->data)));
	}

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
	public function stream_open($path, $mode, $options, &$openedPath)
	{
		// Get the key from the path.
		$url = parse_url($path);
		$this->key = $url['host'];

		// Get the ttl from the path.
		if (isset($url['port']))
		{
			$this->ttl = intval($url['port']);
		}
		else
		{
			$this->ttl = 0;
		}

		// Neat trick because isset is faster than in_array().
		$mode = str_split($mode);
		$mode = array_flip($mode);

		// If we have an r or a + we know that we have read access.
		if (isset($mode['+']) || isset($mode['r']))
		{
			$this->read = true;
		}

		// If we do not have an r or an r with a + means we have write access.
		if (!isset($mode['r']) || (isset($mode['r']) && isset($mode['+'])))
		{
			$this->write = true;
		}

		// If we have an explicit read access set then the key must exist.
		if ($this->read && !$this->write)
		{
			if (!$this->isStorageEntrySet($this->key))
			{
				// If we are supposed to report errors then trigger one.
				if ($options & STREAM_REPORT_ERRORS)
				{
					// @codeCoverageIgnoreStart
					trigger_error(__METHOD__ . ' ' . $this->key . ' does not exist', E_USER_ERROR);
					// @codeCoverageIgnoreEnd
				}

				return false;
			}
		}
		// If we are opening for writing only and create then the key must not exist.
		elseif (isset($mode['x']))
		{
			if ($this->isStorageEntrySet($this->key))
			{
				// If we are supposed to report errors then trigger one.
				if ($options & STREAM_REPORT_ERRORS)
				{
					// @codeCoverageIgnoreStart
					trigger_error(__METHOD__ . ' ' . $this->key . ' already exists', E_USER_ERROR);
					// @codeCoverageIgnoreEnd
				}

				return false;
			}
		}
		// Always truncate the entry if it exists and create it if it does not.
		elseif (isset($mode['w']))
		{
			$this->setStorageEntry($this->key, '', $this->ttl);
		}
		// Add the entry if it doesn't exist.
		else
		{
			$this->addStorageEntry($this->key, '', $this->ttl);
		}

		// Get the storage data and cache it locally.
		$this->data = $this->getStorageEntry($this->key);

		// If set to append, place the position at the end of the stream.
		if (isset($mode['a']))
		{
			$this->pos = strlen($this->data);
		}
		// Otherwise set the position at the beginning of the stream.
		else
		{
			$this->pos = 0;
		}

		return true;
	}

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
	public function stream_read($count)
	{
		// If we cannot read the stream simply return false.
		if (!$this->read)
		{
			return false;
		}

		// If there is nothing left in the stream there isn't anything we can do.
		if ($count + $this->pos > strlen($this->data))
		{
			$count = strlen($this->data) - $this->pos;
		}

		// Get the data from the stream to read.
		$data = substr($this->data, $this->pos, $count);

		// Increment the stream position.
		$this->pos += strlen($data);

		return $data;
	}

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
	public function stream_stat()
	{
		// Flush the stream if necessary.
		$this->stream_flush();

		// Only get the stat if not cached.
		if (empty($this->stat))
		{
			// Get the storage entry information.
			$info = $this->getStorageEntryInfo($this->key);

			// Get the stat array for the URL.
			$stat = array(
				'dev' => 0,
				'ino' => 0,
				// World readable file.
				'mode' => 0100000 | 0777,
				'nlink' => 0,
				'uid' => 0,
				'gid' => 0,
				'rdev' => 0,
				'size' => $info['size'],
				'atime' => $info['time'],
				'mtime' => $info['time'],
				'ctime' => $info['time'],
				'blksize' => -1,
				'blocks' => -1
			);

			// Fun trick to get associative and numeric combined array... and let's cache it.
			$this->stat = $stat + array_values($stat);
		}

		return $this->stat;
	}

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
	public function stream_write($data)
	{
		// If we cannot write to the stream simply return false.
		if (!$this->write)
		{
			return false;
		}

		// Set the changed state.
		$this->changed = true;

		// Get the number of bytes in the data to write.
		$length = strlen($data);

		// Append the data to the stream.
		$this->data .= $data;

		// Increment the stream position.
		$this->pos += $length;

		return $length;
	}

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
	public function stream_seek($offset, $whence)
	{
		// Get the number of bytes in the data.
		$size = strlen($this->data);

		// Validate the offset boundary and set the cursor position.
		switch ($whence)
		{
			case SEEK_SET:
				if (($offset >= 0) && ($offset < $size))
				{
					$this->pos = $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;
			case SEEK_CUR:
				if (($offset >= 0) && (($this->pos + $offset) < $size))
				{
					$this->pos += $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;
			case SEEK_END:
				if (($size + $offset) >= 0)
				{
					$this->pos = $size + $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;
			default:
				return false;
		}
	}

	/**
	 * Method to retrieve the current position of a stream.  This method is called in response to
	 * fseek() to determine the current position.
	 *
	 * @return  integer  The current position of the stream.
	 *
	 * @see     fseek()
	 * @since   11.3
	 */
	public function stream_tell()
	{
		return $this->pos;
	}

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
	public function stream_flush()
	{
		if ($this->changed)
		{
			// Clear the stat cache.
			$this->stat = array();
			$this->changed = false;

			return $this->setStorageEntry($this->key, $this->data, $this->ttl);
		}

		return true;
	}

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
	public function stream_lock($option)
	{
		if ($option == LOCK_SH)
		{
			return $this->lockStorageEntry($this->key);
		}
		elseif ($option == LOCK_EX)
		{
			return $this->lockStorageEntry($this->key, true);
		}
		else
		{
			return $this->unlockStorageEntry($this->key);
		}
	}

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
	public function rename($source, $destination)
	{
		// Get the key from the source path.
		$url = parse_url($source);
		$source = $url['host'];

		// Get the key from the destination path.
		$url = parse_url($destination);
		$destination = $url['host'];

		// Get the time to live for the source entry.
		$meta = $this->getStorageEntryInfo($source);
		$ttl = (int) (time() - $meta['time']);

		// Get the data for the source entry.
		$data = $this->getStorageEntry($source);

		// Remove the source entry.
		$this->removeStorageEntry($source);

		return $this->setStorageEntry($destination, $data, $ttl);
	}

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
	public function unlink($path)
	{
		// Get the key from the path.
		$url = parse_url($path);
		$key = $url['host'];

		return $this->removeStorageEntry($key);
	}

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
	public function url_stat($path, $options)
	{
		// Get the key from the path.
		$url = parse_url($path);
		$key = $url['host'];

		// Get the storage entry information.
		$info = $this->getStorageEntryInfo($key);

		// Get the stat array for the URL.
		$stat = array(
			'dev' => 0,
			'ino' => 0,
			// World readable file.
			'mode' => 0100000 | 0777,
			'nlink' => 0,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => $info['size'],
			'atime' => $info['time'],
			'mtime' => $info['time'],
			'ctime' => $info['time'],
			'blksize' => -1,
			'blocks' => -1
		);

		// Fun trick to get associative and numeric combined array.
		return $stat + array_values($stat);
	}

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
	abstract protected function addStorageEntry($key, $value, $ttl);

	/**
	 * Method to get a storage entry value from a key.
	 *
	 * @param   string  $key
	 *
	 * @return  string
	 *
	 * @since   11.3
	 */
	abstract protected function getStorageEntry($key);

	/**
	 * Method to get information about a storage entry for a key.
	 *
	 * @param   string  $key
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	abstract protected function getStorageEntryInfo($key);

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string  $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	abstract protected function isStorageEntrySet($key);

	/**
	 * Method to get a lock on a storage entry.
	 *
	 * @param   string   $key
	 * @param   boolean  $exclusive
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	abstract protected function lockStorageEntry($key, $exclusive = false);

	/**
	 * Method to remove a storage entry for a key.
	 *
	 * @param   string  $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	abstract protected function removeStorageEntry($key);

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
	abstract protected function setStorageEntry($key, $value, $ttl);

	/**
	 * Method to release a lock on a storage entry.
	 *
	 * @param   string   $key
	 *
	 * @return  boolean
	 *
	 * @since   11.3
	 */
	abstract protected function unlockStorageEntry($key);
}