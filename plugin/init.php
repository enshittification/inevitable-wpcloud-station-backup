<?php
/**
 * Plugin instantiation.
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Requires
 */
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-domains-controller.php';
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-sites-controller.php';
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-webhook-controller.php';

require_once plugin_dir_path( __FILE__ ) . 'custom-post-types/wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/wpcloud-client.php';
require_once plugin_dir_path( __FILE__ ) . 'blocks/init.php';
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-cli.php';
}

if ( ! is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'assets/js/build/index.asset.php';
	add_action(
		'wp_enqueue_scripts',
		function (): void {
			wp_enqueue_script( 'wpcloud', plugin_dir_url( __FILE__ ) . 'assets/js/build/index.js', array( 'wp-hooks' ), '1.0.0', true );
		}
	);
}

/**
 * Get the api key.
 */
function wpcloud_get_api_key(): string {

	// Try to find the key in the ENV or config.
	$api_key = getenv( 'WP_CLOUD_API_KEY' );
	if ( defined( 'WP_CLOUD_API_KEY' ) ) {
		$api_key = WP_CLOUD_API_KEY;
	}

	$api_key = apply_filters( 'wpcloud_api_key', $api_key );

	if ( ! empty( $api_key ) ) {
		return $api_key;
	}

	// Else check the local options.
	$options = get_option( 'wpcloud_options' ) ?? array();
	return $options['wpcloud_api_key'] ?? '';
}

/**
 * Set up the plugin's capabilities.
 */
function wpcloud_add_capabilities(): void {
	$role = get_role( 'administrator' );
	$role->add_cap( WPCLOUD_CAN_MANAGE_SITES );
}

/**
 * Register REST API controllers.
 */
function wpcloud_register_controllers(): void {
	$webhook_controller = new WPCLOUD_Webhook_Controller();
	$webhook_controller->register_routes();

	$domains_controller = new WPCLOUD_Domains_Controller();
	$domains_controller->register_routes();
}
add_action( 'rest_api_init', 'wpcloud_register_controllers' );

/**
 * Set up categories for WP Cloud specific pages
 */
function wpcloud_setup_categories(): void {
	// Verify that the core pages categories exists.
	wp_insert_term(
		'WP Cloud Private Page',
		'category',
		array(
			'description' => 'Private category for WP Cloud specific pages.',
			'slug'        => WPCLOUD_CATEGORY_PRIVATE,
		)
	);

	wp_insert_term(
		'WP Cloud Core Page',
		'category',
		array(
			'description' => 'Core category for WP Cloud specific pages.',
			'slug'        => WPCLOUD_CATEGORY_CORE,
		)
	);

	// Allow adding categories to pages.
	register_taxonomy_for_object_type( 'category', 'page' );
}

/**
 * Verify logged in for WP Cloud specific pages
 */
function wpcloud_verify_logged_in(): void {
	if ( is_admin() ) {
		return;
	}

	$categories = array_reduce(
		get_the_category(),
		function ( $categories, $category ) {
			$categories[] = $category->slug;
			return $categories;
		},
		array()
	);

	$is_wpcloud_site_archive = is_post_type_archive( 'wpcloud_site' );
	$is_wpcloud_private_page = array_search( WPCLOUD_CATEGORY_PRIVATE, $categories, true ) !== false;

	if ( $is_wpcloud_site_archive || $is_wpcloud_private_page ) {
		if ( ! is_user_logged_in() ) {
			global $wp;
			$url = add_query_arg( array( 'ref' => $wp->request ), '/login' );
			wp_safe_redirect( $url );
			exit();
		}
	}
}

/**
 * Initialize the plugin.
 */
function wpcloud_init(): void {
	wpcloud_add_capabilities();
	wpcloud_register_site_post_type();
	wpcloud_setup_categories();

	// Set up ACL for Station pages.
	add_filter( 'template_redirect', 'wpcloud_verify_logged_in' );
}
add_action( 'init', 'wpcloud_init' );
