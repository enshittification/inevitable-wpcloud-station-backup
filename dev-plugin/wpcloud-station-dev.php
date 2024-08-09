<?php // phpcs:ignore
/**
 * Plugin Name:     WP Cloud Station Dev
 * Plugin URI:      https://github.com/Automattic/wpcloud-station
 * Description:     Developer plugin for WP Cloud Station.
 * Author:          Automattic
 * Author URI:      https://wp.cloud/
 * Text Domain:     wpcloud
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package        wpcloud-station-dev
 */

require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-dev-log.php';

add_action(
	'wp_enqueue_scripts',
	function (): void {
		wp_enqueue_script( 'wpcloud-station-dev', plugin_dir_url( __FILE__ ) . 'assets/js/logger.js', array(), '1.0.0', true );
	}
);

/**
 * Custom rewrite rule.
 */
function custom_rewrite_rule() {
	add_rewrite_rule( '^wpcloud-station-dev/sse([^/]*)/?', 'wp-content/plugins/wpcloud-station-dev/controllers/wpcloud-dev-sse-controller.php', 'top' );
}
add_action( 'init', 'custom_rewrite_rule', 10, 0 );
