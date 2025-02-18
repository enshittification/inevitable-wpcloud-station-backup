<?php
/**
 * WP Cloud theme.
 *
 * @package wpcloud-station
 */

/**
 * Enqueue the block styles.
 *
 * @return void
 */
function wpcloud_station_enqueue_block_styles() {
	foreach ( glob( __DIR__ . '/assets/blocks/*.css' ) as $block_style_sheet ) {
		$block = preg_replace( '/-/', '/', basename( $block_style_sheet, '.css' ), 1 );
		wp_enqueue_block_style(
			$block,
			array(
				'handle' => basename( $block_style_sheet, '.css' ),
				'src'    => get_theme_file_uri( 'assets/blocks/' . basename( $block_style_sheet ) ),
				'path'   => get_theme_file_path( 'assets/blocks/' . basename( $block_style_sheet ) ),
			)
		);
	}
}
add_action( 'init', 'wpcloud_station_enqueue_block_styles' );

// We don't want to show the admin bar in the front end.
add_filter( 'show_admin_bar', '__return_false' );


add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wpcloud-station', get_theme_file_uri( 'assets/styles/global.css' ), array(), '1.0.0' );
	}
);
