<?php
/*
Plugin Name: Memcached Monitor
Version: 2.0
Plugin URI: http://www.thedolancompany.com/
Author: Dave Long
Author URI: http://www.thedolancompany.com
Description: Output debug log & session stats for object & session caches
*/

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

global $memmon;
$memmon = new memcached_monitor;

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
	function memcached_monitor() {
			// Setup for admin pages
			if( function_exists( 'is_admin' ) && is_admin() === true )
				add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_stylesheet' ) );
			// Setup for other pages
			else
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_stylesheet' ) );
	}

	/**
	* Check for Super Admin, enqueue stylesheet & javascript, and add shutdown
	 * hook for output.
	 * If not Super Admin, or unable to add js/css do nothing.
	 */
	function enqueue_stylesheet() {

		// Only show debug info to super admin users on pages where admin bar is also shown
		if ( is_super_admin() && is_admin_bar_showing() ){
			// Add required javascript and css
			wp_register_style( 'memcached-monitor-stylesheet', plugins_url( '/style.css', __FILE__  ) );
			wp_enqueue_style( 'memcached-monitor-stylesheet' );
			wp_register_script( 'memcached-monitor-js', plugins_url( '/toggle-ui.js', __FILE__ ) );
			wp_enqueue_script( 'memcached-monitor-js' );

			// Setup output - do so here so that output will not occur unless js & css are enqueued
			add_action( 'shutdown', array( $this, 'output_debug_log' ) );
		}
	}

	/**
	 * Output formatted debug data & stats from memcached
	 */
	function output_debug_log() {
		global $wp_object_cache;

		$data = $wp_object_cache->stats(); 
		?>
		<div id="memcache-debug">
			<h2>Memcached Debug</h2>
			<div class="memcache-toggle" onclick="toggle_hidden_div('memcache-stats')">
				<h3>Server & Session Stats</h3>
			</div>
			<div id="memcache-stats" class="memcache-hidden">
				<?php
					// Hide Server Stats if not available
					if( !empty( $data['server_list'] ) ) : ?>
						<table>
							<thead>
								<tr>
									<th>Server</th>
									<th>Port</th>
									<th>Weight</th>
									<th>Uptime</th>
									<th>Threads</th>
									<th>Current Items</th>
									<th>Total Items</th>
									<th>Current (MB)</th>
									<th>Limit (MB)</th>
									<th>Curr Connections</th>
									<th>Total Connections</th>
									<th>Cmd Get</th>
									<th>Cmd Set</th>
									<th>Get Hits</th>
									<th>Get Misses</th>
									<th>Hit Ratio</th>
									<th>Evictions</th>
									<th>Version</th>							
								</tr>
							</thead>
							<tbody>
								<?php
									foreach( $data['server_list'] as $server )
										$data['server_stats'][ $server['host'] . ':' . $server['port'] ]['weight'] = $server['weight'];
									$servers = array_keys( $data['server_stats'] );
									foreach( $servers as $server ) {
										$host = explode( ':', $server );
										$current_MB = round( (string)$data['server_stats'][ $server ]['bytes'] / ( 1024 * 1024 ), 3 );
										$max_MB = round( $data['server_stats'][ $server ]['limit_maxbytes'] / ( 1024 * 1024 ), 3 );
										$ratio =  round( ( $data['server_stats'][ $server ]['get_hits'] / $data['server_stats'][ $server ]['cmd_get'] * 100 ), 3 );
										echo( '<tr><td>' . $host[0] . '</td>' );
										echo( '<td>' . $host[1] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['weight'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['uptime'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['threads'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['curr_items'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['total_items'] . '</td>' );
										echo( '<td class="right">' . $current_MB . '</td>' );
										echo( '<td class="right">' . $max_MB . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['curr_connections'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['total_connections'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['cmd_get'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['cmd_set'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['get_hits'] . '</td>' );
										echo( '<td class="right">' . $data['server_stats'][ $server ]['get_misses'] . '</td>' );
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
			<div class="memcache-toggle" onclick="toggle_hidden_div('memcache-groups')">
				<h3>Groups (<?php echo( count( $data['map'] ) ); ?>)</h3>
			</div>
			<div id="memcache-groups" class="memcache-hidden">
				<?php
					$groups = array_keys( $data['map'] );
					foreach( $groups as $group ) {
						$items = $data['map'][ $group ];
						echo( '<table><thead onclick="toggle_hidden_tbody(\'group-' . $group . '\')"><tr><th>' . $group . ' (' . count( $items ) . ')' . '</th></tr></thead><tbody id="group-' . $group . '" class="memcache-hidden">' );
						foreach( $items as $item )
							echo( '<tr><td>' . var_export( $item, true) . '</td></tr>' );
						echo( '</tbody></table>' );
					}
				?>
			</div>
			<div class="memcache-toggle" onclick="toggle_hidden_div('memcache-log')">
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
		<?php
	}
}