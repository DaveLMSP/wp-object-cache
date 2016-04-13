=== WP Memcached ===
Contributors: davelmsp
Tags: cache, memcache, memcached, object cache, object caching, smart cache
Requires at least: 3.5
Tested up to: 4.4.2
Stable tag: 4.1.0
License: GPLv3

Smart, multi-level implementation of WordPress Object Cache using memcache, pecl-memcached, and a PHP session cache.

== Description ==
WP Memcached provides a multi-level smart back end for the WordPress Object Cache.  Persistent caching is implemented via a memcached server and the PECL memcached extension, while a volatile PHP session cache stores frequently-used items during the current page load.  In the event that memcached is unavailable or disabled, session cache remains operational.  Smart caching examines existing data to avoid accidentally over-writing new data with older values and also provides a mechanism to prevent data corruption in the event of a key collision.  A Memcached Monitor plugin is included to view server status, memcache groups & keys stored, and a complete log of all interactions with object cache on the most recent page load.

== Installation ==
1. Install memcached from your distribution's repository or [directly](http://danga.com/memcached) on at least one server. Note the connection info. The default is `127.0.0.1:11211`.

2. Install the [PECL memcached extension](http://pecl.php.net/package/memcached).  There are two PECL extensions, memcache and memcached.  This implementation requires the newer memcached extension to make use of CAS (check and set) functionality.

3. Copy object-cache.php to wp-content.

4. Configure servers & assign WP Memcached constants if desired (see FAQ for details).

5. Optionally install & activate the Memcached Monitor plugin.

== Frequently Asked Questions ==

= How can I manually specify the memcached server(s)? =

By default WP Memcached will attempt to connect to a local memcached server on port 11211.  Alternate / additional server(s) can be specified by adding something similar to the following to wp-config.php above `/* That's all, stop editing! Happy blogging. */`:

`
global $memcached_servers = array(
	'default' => array(
		'10.10.10.20:11211',
		'10.10.10.30:11211'
	)
);
`

= Does WP Memcached provide a way to fine-tune caching behavior? =

Yes. WP Memcached uses 3 definable constants to control the interface with memcached.

1. The **WP_OBJECT_CACHE** constant determines whether the memcached interface will be made available to WordPress.  This defaults to true, but it may be desirable to disable caching in a development environment.  Session cache remains operational when memcached is unavailable or deactivated.

2. The **DISABLE_FILE_PATH** constant specifies where WP Memcached will look for no-cache.txt.  If the file exists, the memcached interface is disabled.  This allows for quick cache bypass for trouble-shooting.  By default this is set to wp-content.

3. The **OBJECT_CACHE_EXPIRE** constant sets the minimum expiration time in seconds for memcached keys.  On each page load the object cache will randomly select a value between 1 and 1.5 times this constant and use it for memcached expiration for that page load.  Randomly setting expiration time prevents a thundering herd problem with large quantities of keys expiring simultaneously.  Default is 3600 (1 hour).

= In the event of Memcached problems, is it possible to temporarily disable the memcached interface? =

Yes.  To temporarily disable memcache (but not session caching) create a file called 'no-cache.txt' in the **DISABLE_FILE_PATH** (default is wp-content).  Once the disable file is removed, memcached functionality will resume.

== Screenshots ==

1. Memcached Monitor.

== Changelog ==

= 4.1.0 =
* Release date: April 13, 2016
* Fixed memcached available test; less chance of false positives
* Updated Memcached Monitor to version 3.0
	* Now uses a jQuery lightbox dialog in place of display in the site footer.
	* Fewer theme compatibility issues
	* Improved Memcached stats reporting

= 4.0.0 =
* Initial public version of WP Memcached uploaded to GitHub.  
* Updated for compatibility with WordPress 3.5