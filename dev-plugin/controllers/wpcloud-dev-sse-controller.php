<?php
/**
 * WP Cloud Dev Log.
 *
 * @package wpcloud-dev
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

require_once rtrim( $_SERVER['DOCUMENT_ROOT'], '/' ) . '/wp-load.php'; // phpcs:ignore
require_once rtrim( $_SERVER['DOCUMENT_ROOT'], '/' ) . '/wp-includes/user.php'; // phpcs:ignore
require_once '../includes/class-wpcloud-dev-log.php'; // phpcs:ignore

if ( ! current_user_can( 'administrator' ) ) { // phpcs:ignore
	header( 'HTTP/1.0 403 Forbidden', true, 403 );
}

header( 'X-Accel-Buffering: no' );
header( 'Content-Type: text/event-stream' );
header( 'Cache-Control: no-cache' );


$wait = 1;
while ( true ) {
	sleep( $wait );

	$events = WPCLOUD_Dev_Log::flush_log();

	foreach ( $events as $event ) {
		echo 'event: wpcloud_event' . PHP_EOL;
		echo 'id: ' . wp_get_session_token() . PHP_EOL;
		echo 'data: ' . wp_json_encode( $event ) . PHP_EOL;
		echo PHP_EOL;
	}

	if ( empty( $events ) ) {
		echo 'event: wpcloud_event' . PHP_EOL;
		echo 'id: ' . wp_get_session_token() . PHP_EOL;
		echo 'data: ""' . PHP_EOL;
		echo PHP_EOL;
	}

	if ( ob_get_contents() ) {
		ob_end_flush();
	}
	flush();

	if ( connection_aborted() ) {
		error_log( 'Connection aborted' );
		if ( ! WPCLOUD_Dev_Log::unlink_log() ) {
			error_log( 'Failed to unlink log file' );
		}
		break;
	}
}
