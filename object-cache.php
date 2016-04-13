<?php
/*
Plugin Name: WP Memcached
Description: Memcached backend for the WordPress Object Cache.
Version: 4.1.0
Author: Dave Long, Kris Linnell

Install this file to wp-content/object-cache.php
*/

// Initialize constants if not already set
if( !defined( 'WP_OBJECT_CACHE' ) ) {
	define( 'WP_OBJECT_CACHE', true );
}
if( !defined( 'DISABLE_FILE_PATH' ) ) {
	define( 'DISABLE_FILE_PATH', dirname( __FILE__ ) );
}
if( !defined( 'OBJECT_CACHE_EXPIRE' ) ) {
	define( 'OBJECT_CACHE_EXPIRE', 3600 );
}

// Include server config file if present
if ( file_exists( ABSPATH . "wp-content/memcached-servers.php" ) ) {
	include( ABSPATH . "wp-content/memcached-servers.php" );
}

/**
 * Adds data to the cache. Proxied to Memcached::set since
 * WordPress frequently uses Add when it should use Set()
 * resulting in old data getting 'stuck' in Memcache.
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::set()
 *
 * @param int|string $id The id used to identify this item in the cache
 * @param mixed $data The data to add to the cache
 * @param string $group The cache group to add this id to
 * @param int $expire When the cache data should be expired
 * @return bool Returns TRUE on success or FALSE on failure.
 */
function wp_cache_add( $id, $data, $group = 'default', $expire = 0 ) {
	global $wp_object_cache;

	return $wp_object_cache->set( $id, $data, $group, $expire );
}

/**
 * Added for potential legacy compatibility; doesn't do anything.
 *
 * @return bool Always returns True
 */
function wp_cache_close() {
	return true;
}

/**
 * Decrement numeric cache item's value
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::decr()
 *
 * @param int|string $id The cache id to increment
 * @param int $offset The amount by which to decrement the item's value.  Default is 1.
 * @param string $group The cache group this id is stored in.
 * @return bool|int Returns item's new value on success or FALSE on failure.
 */
function wp_cache_decr( $id, $n = 1, $group = 'default' ) {
	global $wp_object_cache;

	return $wp_object_cache->decr( $id, $n, $group );
}

/**
 * Removes the cache contents matching id and group.
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::delete()
 *
 * @param int|string $id The id used to identify this item in the cache
 * @param string $group The cache group this id is stored in.
 * @return bool True on successful removal, FALSE on failure.
 */
function wp_cache_delete( $id, $group = 'default' ) {
	global $wp_object_cache;

	return $wp_object_cache->delete( $id, $group );
}

/**
 * Removes all cache items from session cache.
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::flush()
 *
 * @return bool Always returns TRUE
 */
function wp_cache_flush() {
	global $wp_object_cache;

	return $wp_object_cache->flush();
}

/**
 * Retrieves the cache contents from the cache by id and group.
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::get()
 *
 * @param int|string $id The id used to identify this item in the cache
 * @param string $group The cache group this id is stored in.
 * @return bool|mixed Returns the value stored in the cache or FALSE on failure.
 */
function wp_cache_get( $id, $group = 'default' ) {
	global $wp_object_cache;
	
	return $wp_object_cache->get( $id, $group );
}

/**
 * Increment numeric cache item's value
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::incr()
 *
 * @param int|string $id The cache id to increment
 * @param int $offset The amount by which to increment the item's value.  Default is 1.
 * @param string $group The cache group this id is stored in.
 * @return bool|int Returns new item's value on success or FALSE on failure. 
 */
function wp_cache_incr( $id, $n = 1, $group = 'default' ) {
	global $wp_object_cache;

	return $wp_object_cache->incr( $id, $n, $group );
}

/**
 * Sets up Object Cache Global and assigns it.
 *
 * @global WP_Object_Cache $wp_object_cache WordPress Object Cache
 */
function wp_cache_init() {
	global $wp_object_cache;

	// Only create new if cache doesn't exist
	if( !isset( $wp_object_cache ) ) { 
		$wp_object_cache = new WP_Object_Cache();
	}
	else {
		//if it does exist, run its init sequence, which will check active and if not active attempt to become active
		$wp_object_cache->init();
	}
}

/**
 * Replaces the contents of the cache with new data.
 * Proxied to Memcached::set.
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::set()
 *
 * @param int|string $id The id used to identify this item in the cache
 * @param mixed $data The data to add to cache
 * @param string $group The cache group this id is stored in.
 * @param int $expire When the cache data should be expired
 * @return bool Returns TRUE on success or FALSE on failure.
 */
function wp_cache_replace( $id, $data, $group = 'default', $expire = 0 ) {
	global $wp_object_cache;
	
	return $wp_object_cache->set( $id, $data, $group, $expire );
}

/**
 * Saves the data to the cache.
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::set()
 *
 * @param int|string $id The id used to identify this item in the cache
 * @param mixed $data The data to add to cache
 * @param string $group The cache group this id is stored in.
 * @param int $expire When the cache data should be expired
 * @return bool Returns TRUE on success or FALSE on failure.
 */
function wp_cache_set( $id, $data, $group = 'default', $expire = 0 ) {
	global $wp_object_cache;

	return $wp_object_cache->set( $id, $data, $group, $expire );
}

/**
 * Returns array with log, counts, & group/key list
 *
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::set()
 *
 * @return mixed Returns array with stats for current page load
*/
function wp_cache_stats() {
	global $wp_object_cache;

	return $wp_object_cache->stats();
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 * @param string|array $groups A group or an array of groups to add
 */
function wp_cache_add_global_groups( $groups ) {
	global $wp_object_cache;

	$wp_object_cache->add_global_groups( $groups );
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 * @param string|array $groups A group or an array of groups to add
 */
function wp_cache_add_non_persistent_groups( $groups ) {
	global $wp_object_cache;

	$wp_object_cache->add_non_persistent_groups( $groups );
}

/**
 * WordPress Object Cache
 *
 * The WordPress Object Cache is a two-level peristent/non-persistent
 * cache consisting of a local session cache and an interface to a
 * Memcached instance.
 */
class WP_Object_Cache {
	// Class variables
	var $global_groups = array ( 'users', 'userlogins', 'usermeta', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss' );
	var $no_mc_groups = array( 'comment', 'counts' );
	var $autoload_groups = array ( 'options' );

	// Local variables
	var $cache = array();
	var $stats = array();
	var $default_expiration = 3600;

	// Initialize memcache pointer
	var $memcache_active = false;
	var $mc = null;
	var $active = 0;

	/**
	 * Constructor - Sets up object properties
	 *
	 * @return WP_Object_Cache
	 */
	function __construct() {
		global $memcached_servers;

		// Initialize local counters
		$this->stats['log'] = array();
		$this->stats['counts'] = array();
		$this->stats['servers'] = array();
		$this->stats['map'] = array();
		$this->stats['counts']['session_hits'] = 0;
		$this->stats['counts']['mc_hits'] = 0;
		$this->stats['counts']['misses'] = 0;
		$this->stats['counts']['excluded'] = 0;
		$this->stats['counts']['failures'] = 0;
		$this->stats['counts']['updates'] = 0;
		array_push( $this->stats['log'], 'Object Cache constructor ' . rand( 1000,9999 ) );

		// Only setup memcache if we're using it
		if( file_exists( DISABLE_FILE_PATH . '/no-cache.txt'  ) || !WP_OBJECT_CACHE ) {
			array_push( $this->stats['log'], 'Using session cache only - object cache has been deactivated.' );
		}
		elseif( class_exists( 'Memcached' ) ) {
			if ( isset( $memcached_servers ) ) {
				$servers = $memcached_servers;
			}
			else {
				$servers = array( '127.0.0.1:11211' );
			}

			$this->mc = new Memcached();
			foreach ( $servers as $server  ) {
				list ( $node, $port ) = explode( ':', $server );
				if ( !$port ) {
					$port = ini_get( 'memcache.default_port' );
				}
				$port = intval( $port );
				if ( !$port ) {
					$port = 11211;
				}
				$this->mc->addServer( $node, $port );
			}

			// Confirm memcached is working before setting active
			$status = $this->mc->set( 'memcache-test-value', 1, 1 );
			if( $status ) {
				$this->memcache_active = true;
				$this->mc->delete( 'memcache-test-value' );
			}
			else {
				array_push( $this->stats['log'], 'Using session cache only - error connecting to memcached.' );
			}
		}
		// Memcached has been deactivated
		else {
			array_push( $this->stats['log'], 'Using session cache only - object cache is not available.' );
		}
		// Run internal init sequence
		$this->init();
	}

	/* Begin Public Functions */

	/**
	 * Decrement numeric cache item's value
	 *
	 * @param int|string $id The cache id to decrement
	 * @param int $offset The amount by which to decrement the item's value.  Default is 1.
	 * @param string $group The cache group this id is stored in.
	 * @return bool|int Returns item's new value on success or FALSE on failure.
	 */
	public function decr( $id, $n, $group ) {
		$key = $this->key( $id, $group );
		array_push( $this->stats['log'], 'Decrementing ' . $group . ', ' . $id . ' in memcache.' );

		$this->cache[ $key ] = $this->mc->decrement( $key, $n );
		return $this->cache[ $key ];
	}

	/**
	 * Remove the contents of the cache id in the group
	 *
	 * @param int|string $id The id used to identify this item in the cache
	 * @param string $group The cache group this id is stored in.
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function delete( $id, $group ) {
		$key = $this->key( $id, $group );

		// Delete $key from non-persistent cache
		unset( $this->cache[ $key ] );
		// Return TRUE if in no_mc, not using memcache, or blog_id not defined and key not in a global group
		if ( in_array( $group, $this->no_mc_groups ) || ( !in_array( $group, $this->global_groups ) && $this->active != 1 ) || !$this->memcache_active ) {
			array_push( $this->stats['log'], 'Unable to delete ' . $group . ', ' . $id . ' from cache - key is in no_mc or memcache not available' );
			return true;
		}
		else {
			array_push( $this->stats['log'], 'Deleting ' . $group . ', ' . $id . ' from cache.' );
			return $this->mc->delete( $key );
		}
	}

	/**
	 * Clears the non-persistent cache of all data
	 *
	 * @return bool Always returns TRUE
	 */
	public function flush() {
		array_push( $this->stats['log'], 'Flushing session cache.' );

		// Clear session cache
		$this->cache = array();
		return true;
	}

	/**
	 * Retrieves item from the cache, if it exists
	 *
	 * Non-persistent cache is searched first; Then memcache is checked.
	 * The first found value is returned; a memcache get will update
	 * the non-persistent cache.
	 *
	 * @param int|string $id The id used to identify this item in the cache
	 * @param string $group The cache group this id is stored in.
	 * @return bool|mixed Returns the value stored in the cache or FALSE on failure.
	 */
	public function get( $id, $group = 'default' ) {
		$token = '';
		$key = $this->key( $id, $group );

		// Check non-persistent session cache first
		if ( isset( $this->cache[ $key ] ) ) {
			$value = $this->cache[ $key ];
			@++$this->stats['counts']['session_hits'];
			array_push( $this->stats['log'], 'Retrieved ' . $group . ', ' . $id . ' from session cache. Session hits:' . $this->stats['counts']['session_hits'] );
		}
		// Check for no_mc groups, inactive mecache or blog_id not defined and key not in a global group
		elseif ( in_array( $group, $this->no_mc_groups ) || ( !in_array( $group, $this->global_groups ) && $this->active != 1 ) || !$this->memcache_active ) {
			$value = false;
			if ( !$this->memcache_active ||  $this->active != 1 ) {
				@++$this->stats['counts']['misses'];
				array_push( $this->stats['log'], 'Unable to retrieve ' . $group . ', ' . $id . '; memcache not active. Misses: ' . $this->stats['counts']['misses'] );
			}
			else {
				@++$this->stats['counts']['excluded'];
				array_push( $this->stats['log'], 'Unable to retrieve ' . $id . '; ' . $group . ' is a no_mc group. Excluded Items:' . $this->stats['counts']['excluded'] );
			}
		}
		// If all else fails, check memcache
		else {
			$value = $this->mc->get( $key, NULL, $token );
			// Token will not be set if item is not in memcache
			if( empty( $token ) ) {
				$value = false;
				@++$this->stats['counts']['misses'];
				array_push( $this->stats['log'], 'Unable to retrieve ' . $group . ', ' . $id . '; memcache miss. Misses:' . $this->stats['counts']['misses'] );
			}	
			else {
				// Save valid value in session
				$this->cache[ $key ] = $value;
				@++$this->stats['counts']['mc_hits'];
				array_push( $this->stats['log'], 'Retrieved ' . $group . ', ' . $id . ' from memcache.  Memcache hits:' . $this->stats['counts']['mc_hits'] );
				if( !is_array( $this->stats['map'][ $group ] ) ) {
					$this->stats['map'][ $group ] = array();
				}
				if( !in_array( $id, $this->stats['map'][ $group ] ) ) {
					array_push( $this->stats['map'][ $group ], $id );
				}
			}
		}

		// Return cached item or false if not stored
		return $value;
	}

	/**
	 * Increment numeric cache item's value
	 *
	 * @param int|string $id The cache id to increment
	 * @param int $offset The amount by which to increment the item's value.  Default is 1.
	 * @param string $group The cache group this id is stored in.
	 * @return bool|int Returns item's new value on success or FALSE on failure.
	 */
	public function incr( $id, $n, $group ) {
		$key = $this->key( $id, $group );
		array_push( $this->stats['log'], 'Incrementing ' . $group . ', ' . $id . ' in memcache.' );

		$this->cache[ $key ] = $this->mc->increment( $key, $n );
		return $this->cache[ $key ];
	}

	/**
	* Function to prevent non-global writes to memcache when blog_id not defined
	* Also prevents multiple instances being built on a single page load
	*/
	public function init() {
		global $blog_id, $wpdb;

		if ( $this->active != 1 ) {
			// global $blog_id cannot be trusted until db_prefix has been set
			if( empty( $wpdb->prefix ) ) {
				array_push( $this->stats['log'], 'Init function: Unable to determine blog_id - only global keys will work.' );
			}
			else {
				$this->active = 1;
				$this->default_expiration = rand( OBJECT_CACHE_EXPIRE, 1.5 * OBJECT_CACHE_EXPIRE );
				array_push( $this->stats['log'], 'Init function: blog_id is ' . $blog_id . ' & default expiration is ' . $this->default_expiration . ' seconds. Setting active.' ); 
			}
		}
	}

	/**
	 * Sets the data contents into the cache
	 *
	 * The cache contents is grouped by the $group parameter followed by the
	 * $key. This allows for duplicate ids in unique groups. Therefore, naming of
	 * the group should be used with care and should follow normal function
	 * naming guidelines outside of core WordPress usage.
	 *
	 * @param int|string $id The id used to identify this item in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group The cache group this id is stored in.
	 * @param int $expire Seconds to retain $data in memcache; default 3600
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function set( $id, $data, $group = 'default', $expire = 0 ) {
		$token = '';
		$key = $this->key( $id, $group );
		if( $expire == 0 ) {
			$expire = $this->default_expiration;
		}

		// Don't write value to memcache if it's the same as one we already have
		if( isset( $this->cache[ $key ] ) && ( strcasecmp( serialize( $this->cache[ $key ] ), serialize( $data ) ) == 0 ) ) {
			array_push( $this->stats['log'], 'Not adding ' . $group . ', ' . $id . ' to cache - value already in session cache.' );
			return true;
		}

		// Return TRUE if in no_mc, not using memcache, or blog_id not defined and key not in a global group
		if ( in_array( $group, $this->no_mc_groups ) || ( !in_array( $group, $this->global_groups ) && $this->active != 1 ) || !$this->memcache_active ) {
			$this->cache[ $key ] = $data;
			if( !is_array( $this->stats['map'][ $group ] ) ) {
				$this->stats['map'][ $group ] = array();
			}
			if( !in_array( $id, $this->stats['map'][ $group ] ) ) {
				array_push( $this->stats['map'][ $group ], $id );
			}
			array_push( $this->stats['log'], 'Adding ' . $group . ', ' . $id . ' to session cache only - memcache not available or no_mc group.' );
			return true;
		}
		
		$cached = $this->mc->get( $key, NULL, $token );
		if( $expire == 0 ) {
			$expire = $this->default_expiration;
		}

		// Token will not be set if item is not in memcache; use add()
		if( empty( $token ) ) {
			if( $this->mc->add( $key, $data, $expire ) ) {
				$this->cache[ $key ] = $data;
				@++$this->stats['counts']['updates'];
				array_push( $this->stats['log'], 'Adding ' . $group . ', ' . $id . ' to cache with expiration ' . $expire . '.  Updates: ' . $this->stats['counts']['updates'] );
				if( !is_array( $this->stats['map'][ $group ] ) ) {
					$this->stats['map'][ $group ] = array();
				}
				if( !in_array( $id, $this->stats['map'][ $group ] ) ) {
					array_push( $this->stats['map'][ $group ], $id );
				}
				return true;
			}
			// Memcache write failure
			else {
				$this->mc->delete( $key );
				@++$this->stats['counts']['failures'];
				array_push( $this->stats['log'], 'Not adding ' . $group . ', ' . $id . ' to cache - memcache write failure.  Write failures:' . $this->stats['counts']['failures'] );
				return false;
			}
		}
		// If data is already in memcache, do not re-add it
		elseif ( strcasecmp( serialize( $cached ), serialize( $data ) ) == 0 ) {
			array_push( $this->stats['log'], 'Not adding ' . $group . ', ' . $id . ' to cache - value already in memcache.' );
			return true;
		}
		// We have a valid token, and the values are different.  Update it
		else {
			if( $this->mc->cas( $token, $key, $data, $expire ) ) {
				$this->cache[ $key ] = $data;
				if( !is_array( $this->stats['map'][ $group ] ) ) {
					$this->stats['map'][ $group ] = array();
				}
				if( !in_array( $id, $this->stats['map'][ $group ] ) ) {
					array_push( $this->stats['map'][ $group ], $id );
				}
				@++$this->stats['counts']['updates'];
				array_push( $this->stats['log'], 'Updating ' . $group . ', ' . $id . ' in cache with expiration ' . $expire . '.  Updates: ' . $this->stats['counts']['updates'] );
				return true;
			}
			// Memcache write failure
			else {
				$this->mc->delete( $key );
				@++$this->stats['counts']['failures'];
				array_push( $this->stats['log'], 'Not adding ' . $group . ', ' . $id . ' to cache - memcache write failure.  Write failures:' . $this->stats['counts']['failures'] );
				return false;
			}
		}
	}

	/**
	*	Function to retrieve single page load stats & log
	*
	*	@return mixed Returns Array containing stat / log info
	*/
	public function stats() {
		if( $this->memcache_active ) {
			$this->stats['server_stats'] = $this->mc->getStats();
			$this->stats['server_list'] = $this->mc->getServerList();
		}
		return $this->stats;
	}

	/**
	 * Add groups to store globally for the site
	 *
	 * @param mixed $groups one or more groups to add to global groups
	 */
	public function add_global_groups( $groups ) {
		array_push( $this->stats['log'], 'Adding ' . var_export( $groups, true ) . ' to global groups.' );

		if ( ! is_array( $groups ) ) {
			$groups = (array) $groups;
		}

		$this->global_groups = array_merge( $this->global_groups, $groups );
		$this->global_groups = array_unique( $this->global_groups );
	}

	/**
	 * Add groups to store in session cache only
	 *
	 * @param mixed $groups one or more groups to add to non-persistent groups
	 */
	public function add_non_persistent_groups( $groups ) {
		array_push( $this->stats['log'], 'Adding ' . var_export( $groups, true ) . ' to non-persistent groups.' );

		if ( ! is_array( $groups ) ) {
			$groups = (array) $groups;
		}

		$this->no_mc_groups = array_merge( $this->no_mc_groups, $groups );
		$this->no_mc_groups = array_unique( $this->no_mc_groups );
	}

	/* End Public Functions */

	/* Begin Private Functions */

	/**
	 * Private function to generate key from object $id and $group
	 *
	 * @param int|string $id The id used to identify this item in the cache
	 * @param string $group The cache group this id is stored in.
	 * @return string MD5 encoded keystring
	 */
	private function key( $id, $group = 'default' ) {
		global $blog_id;

		// Determine key string prefix
		if ( in_array( $group, $this->global_groups ) ) {
			// Use 'Global' for any global keys
			$prefix = 'global';
		}
		elseif( $this->active ) {
			// If MC is fully active, global $blog_id can be trusted
			$prefix = $blog_id;
		}
		else {
			// If we're only using session cache, use a special prefix
			$prefix = 'session';
		}

		$keystring = DB_NAME . "::$prefix::$group::$id";
		$keystring =  preg_replace( '/\s+/', '', $keystring );
		$hashed_key = md5( $keystring );
		array_push( $this->stats['log'], "Generated key $hashed_key from $keystring." );
		return $hashed_key;
	}

	/* End Private Functions */
}
?>