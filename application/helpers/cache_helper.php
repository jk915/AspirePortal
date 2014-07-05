<?php
	/***
	* @helper cache_helper 
	* @author Andrew Chapman
	* @abstract The cache helper is used to read, write and delete cache entries
	* from the cache folder
	*/
	
	/***
	* Checks the cache for an existing entry with the key specified
	* 
	* @param string $key The name of the cache entry to check for
	* @return bool Returns true if a cache entry with the specified key name exists, false if not.
	*/
	function cache_exists($key)
	{
		$path = ABSOLUTE_PATH . "cache/" . $key;
		
		return file_exists($path);
	}
	
	/***
	* Writes a cache entry with the specified key
	* 
	* @param string $key The cache entry key
	* @param string $value The cache entry value
	*/
	function cache_write($key, $value)
	{
		$path = ABSOLUTE_PATH . "cache/" . $key;
		
		if(!file_put_contents($path, $value))
		{
			show_error("Unable to write cache entry.  Please check file permissions on the cache directory");	
		}
	}
	
	/***
	* Reads a cache entry from the cache with the specified key
	* 
	* @param string $key The cache key
	* @return string Returns the cache entry, boolean false on failure.
	*/
	function cache_read($key)
	{
		$path = ABSOLUTE_PATH . "cache/" . $key;
		
		if(file_exists($path))
		{
			return file_get_contents($path);
		}
		else
		{
			return false;
		}
	}
	
	/***
	* Deletes the specified cache entry
	* 
	* @param string $key The key of the cache entry to delete
	*/
	function cache_delete($key)
	{
		$path = ABSOLUTE_PATH . "cache/" . $key;
		
		if(file_exists($path))
		{
			@unlink($path);
		}	
	}
	
	/***
	* Delete all entries out of the cache
	*/
	function cache_flush()
	{
		// Open a directory handle to the cache directory
		$dir_path = ABSOLUTE_PATH . "cache/";
		
		$dh = opendir($dir_path);
		if(!$dh)
		{
			show_error("The cache directory does not exist.  Please ensure that the cache director exists and is writable by the web server.");
		}
		
		// Loop through all directory entries
		while($key = readdir($dh))
		{
			if(($key == ".") || ($key == "..")) continue; 
			
			// Delete this cache entry
 			cache_delete($key);
		}
		
		// Close the directory handle.
		closedir($dh);
	}
	   
