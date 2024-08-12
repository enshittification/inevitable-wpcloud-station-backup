<?php
/**
 * WP Cloud Station Dev
 *
 * @package wpcloud-station-dev
 */

declare( strict_types = 1 );

/**
 * Initialize dev settings
 */
function wpcloud_station_dev_settings_init(): void {
	register_setting( 'wpcloud', 'wpcloud_station_dev_settings' );

	add_settings_section(
		'wpcloud_station_dev_settings',
		__( 'WP Cloud Dev Settings', 'wpcloud' ),
		null,
		'wpcloud'
	);
	add_settings_field(
		'wpcloud_station_dev_field_dev_log',
		__( 'Dev Log', 'wpcloud' ),
		'wpcloud_station_dev_field_dev_log_render',
		'wpcloud',
		'wpcloud_station_dev_settings'
	);
}
add_action( 'admin_init', 'wpcloud_station_dev_settings_init' );

/**
 * Initialize submenu page
 */
function wpcloud_station_dev_options_page(): void {
	add_submenu_page(
		'wpcloud',
		__( 'WP Cloud Dev', 'wpcloud' ),
		__( 'WP Cloud Dev', 'wpcloud' ),
		'manage_options',
		'wpcloud-station-dev',
		'wpcloud_station_dev_admin_options_controller'
	);
}
add_action( 'admin_menu', 'wpcloud_options_page' );
/**
 * Controller for the admin options page
 */
function wpcloud_station_dev_admin_options_controller(): void {
	require_once plugin_dir_path( __FILE__ ) . 'options.php';
}


function wpcloud_station_dev_field_dev_log_render(): void {
	$options = get_option( 'wpcloud_station_dev_settings' );
	?>
	<input type="checkbox" name="wpcloud_station_dev_settings[dev_log]" value="1" <?php checked( $options['dev_log'] ?? 0, 1 ); ?> />
	<?php
}
