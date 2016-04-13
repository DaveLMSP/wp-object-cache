<?php
/*
Plugin Name: Memcached Monitor
Version: 3.0
Author: Dave Long
Description: Output debug log & session stats for object & session caches
*/

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

global $memcached_monitor;
$memcached_monitor = new memcached_monitor;

/**
 * Memcached Debug Class
 *
 * Outputs stats & log info for WordPress Object Cache
 */
class memcached_monitor {

	/**
	 * Constructor - Sets up action hooks
	 *
	 * @return WP_Object_Cache
	 */
	public function __construct() {

		// Setup for admin pages
		if( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_stylesheet' ) );
		}
		// Setup for other pages
		else {
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_stylesheet' ) );
		}

		// Add menu to admin bar for top display
		add_action( 'admin_bar_menu', array( &$this, 'header_debug_log' ), 1000 );
	}

	function header_debug_log() {
		global $wp_admin_bar, $current_user, $template;

		// Only show to super admins if admin bar is active
		if ( isset( $current_user->ID ) && is_admin_bar_showing() ) {
			$myuser = get_userdata( $current_user->ID );
			if( in_array( 'administrator', $myuser->roles ) || is_super_admin() ){
				/* Add the main siteadmin menu item */
				$wp_admin_bar->add_menu( array( 'id' => 'memcache-show-debug', 'title' => __( 'Memcached Debug', 'textdomain' ), 'href' => FALSE ) );
			}
		}
	}

	/**
	* Check for Super Admin, enqueue stylesheet & javascript, and add shutdown
	 * hook for output.
	 * If not Super Admin, or unable to add js/css do nothing.
	 */
	public function enqueue_stylesheet() {

		// Only show debug info to super admin users on pages where admin bar is also shown
		if ( is_super_admin() && is_admin_bar_showing() ){
			wp_register_style( 'memcached-monitor-stylesheet', plugins_url( '/style.css', __FILE__  ) );
			wp_enqueue_style( 'memcached-monitor-stylesheet' );

			wp_register_script( 'memcached-monitor-js', plugins_url( '/toggle-ui.js', __FILE__ ), array( 'jquery-ui-dialog' ), false, true );
			wp_enqueue_script( 'memcached-monitor-js' );

			// Setup output - do so here so that output will not occur unless js & css are enqueued
			add_action( 'shutdown', array( $this, 'output_debug_log' ) );
		}
	}

	/**
	 * Output formatted debug data & stats from memcached
	 */
	public function output_debug_log() {
		global $wp_object_cache;

		$data = $wp_object_cache->stats(); 
		?>
		<div id="memcache-wrap">
		<div id="memcache-debug">
			<div id="toggle-stats" class="memcache-toggle">
				<h3>Server & Session Stats</h3>
			</div>
			<div id="memcache-stats" class="memcache-hidden">
				<?php
					// Hide Server Stats if not available
					if( !empty( $data['server_list'] ) ) : ?>
						<table id="server-stats">
							<thead>
								<tr>
									<th>Server</th>
									<th>Port</th>
									<th>Uptime</th>
									<th class="drop">Threads</th>
									<th>Current Items</th>
									<th class="drop">Total Items</th>
									<th>Current (MB)</th>
									<th>Limit (MB)</th>
									<th>Curr Connections</th>
									<th class="drop">Total Connections</th>
									<th class="drop">Cmd Get</th>
									<th class="drop">Cmd Set</th>
									<th class="drop">Get Hits</th>
									<th class="drop">Get Misses</th>
									<th class="drop">MB Read</th>
									<th class="drop">MB Written</th>
									<th>Hit Ratio</th>
									<th>Evictions</th>
									<th>Version</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$servers = array_keys( $data['server_stats'] );
									foreach( $servers as $server ) {
										$host = explode( ':', $server );
										$current_MB = round( (string)$data['server_stats'][ $server ]['bytes'] / ( 1024 * 1024 ), 3 );
										$mb_read = round( (string)$data['server_stats'][ $server ]['bytes_read'] / ( 1024 * 1024 ), 3 );
										$mb_written = round( (string)$data['server_stats'][ $server ]['bytes_written'] / ( 1024 * 1024 ), 3 );
										
										$max_MB = round( $data['server_stats'][ $server ]['limit_maxbytes'] / ( 1024 * 1024 ), 3 );
										$ratio =  round( ( $data['server_stats'][ $server ]['get_hits'] / $data['server_stats'][ $server ]['cmd_get'] * 100 ), 3 );
										echo( '<tr><td>' . $host[0] . '</td>' );
										echo( '<td>' . $host[1] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['uptime'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['threads'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['curr_items'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['total_items'] . '</td>' );
										echo( '<td class="right">' . $current_MB . '</td>' );
										echo( '<td class="right">' . $max_MB . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['curr_connections'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['total_connections'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['cmd_get'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['cmd_set'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['get_hits'] . '</td>' );
										echo( '<td class="right drop">' . $data['server_stats'][ $server ]['get_misses'] . '</td>' );
										echo( '<td class="right drop">' . $mb_read . '</td>' );
										echo( '<td class="right drop">' . $mb_written . '</td>' );
										echo( '<td class="right">' . $ratio . '%</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['evictions'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['version'] . '</td>' );
										echo( '</tr>' );
									}
								?>
							</tbody>
						</table>
				<?php else: ?>
					<div class="memcache-inactive">
						Memcached Is Inactive
					</div>
				<?php endif; ?>
				<table>
					<thead>
						<tr>
							<th colspan="2">Session Stats</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Session Cache Hits:</td>
							<td class="right"><?php echo( $data['counts']['session_hits'] ); ?></td>
						</tr>
						<tr class="alternate">
							<td>Memcache Hits:</td>
							<td class="right"><?php echo( $data['counts']['mc_hits'] ); ?></td>
						</tr>
						<tr>
							<td>Misses:</td>
							<td class="right"><?php echo( $data['counts']['misses'] ); ?></td>
						</tr>
						<tr>
							<td>Excluded Items:</td>
							<td class="right"><?php echo( $data['counts']['excluded'] ); ?></td>
						</tr>
						<tr class="alternate">
							<td>Memcache Updates:</td>
							<td class="right"><?php echo( $data['counts']['updates'] ); ?></td>
						</tr>			
						<tr>
							<td>Memcache Write Failures:</td>
							<td class="right"><?php echo( $data['counts']['failures'] ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="toggle-groups" class="memcache-toggle">
				<h3>Groups (<?php echo( count( $data['map'] ) ); ?>)</h3>
			</div>
			<div id="memcache-groups" class="memcache-hidden">
				<?php
					$groups = array_keys( $data['map'] );
					foreach( $groups as $group ) {
						$items = $data['map'][ $group ];
						echo( '<table><thead id="toggle-group-' . $group .'" class="memcache-toggle"><tr><th>' . $group . ' (' . count( $items ) . ')' . '</th></tr></thead><tbody id="memcache-group-' . $group . '" class="memcache-hidden">' );
						foreach( $items as $item )
							echo( '<tr><td>' . var_export( $item, true) . '</td></tr>' );
						echo( '</tbody></table>' );
					}
				?>
			</div>
			<div id="toggle-log" class="memcache-toggle">
				<h3>Session Log (<?php echo( count( $data['log'] ) ); ?>)</h3>
			</div>
			<div id="memcache-log" class="memcache-hidden">
				<table>
					<thead>
						<tr>
							<th>Memcache Log</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$row_ct = 0;
							foreach( $data['log'] as $log_item ) {
								$row_ct++;
								if( $row_ct % 2 == 0 )
									echo( '<tr class="alternate"><td>' . $log_item . '</td></tr>');
								else
									echo( '<tr><td>' . $log_item . '</td></tr>');
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		</div>
		<?php
	}
}