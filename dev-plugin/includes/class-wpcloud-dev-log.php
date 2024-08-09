<?php
/**
 * WP Cloud Dev Log.
 *
 * @package wpcloud-dev
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents

declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Dev_Log' ) ) {

	add_action(
		'init',
		function () {
			$dev_controller = new WPCLOUD_Dev_Log();
			$dev_controller->register_hooks();
		}
	);

	add_action(
		'template_redirect',
		function () {
			global $post;
			if ( ! $post ) {
				return;
			}
			WPCLOUD_Dev_Log::log_event(
				array(
					'event' => 'PAGE_VIEW',
					'data'  => array(
						'url' => get_page_link(), // phpcs:ignore
					),
				)
			);
		}
	);


	/**
	 * Class WPCLOUD_Dev_Log.
	 */
	class WPCLOUD_Dev_Log {

		/**
		 * The file prefix.
		 *
		 * @var string
		 */
		public static $log_path = WP_CONTENT_DIR . '/wpcloud_log/';



		/**
		 * Register hooks.
		 */
		public function register_hooks(): void {
			add_action( 'wpcloud_webhook', array( $this, 'log_webhook' ), 10, 4 );
			add_action( 'wpcloud_client_request', array( $this, 'log_api_request' ), 10, 4 );
			add_action( 'wpcloud_client_response_error', array( $this, 'log_api_response' ), 10, 4 );
			add_action( 'wpcloud_client_response_success', array( $this, 'log_api_response' ), 10, 4 );
		}


		/**
		 * Log webhook event.
		 *
		 * @param string $event           The event name.
		 * @param int    $timestamp       The timestamp.
		 * @param int    $wpcloud_site_id The site ID.
		 * @param array  $data            The data.
		 *
		 * @return void
		 */
		public function log_webhook( $event, $timestamp, $wpcloud_site_id, $data ) {
			self::log_event(
				array(
					'event' => 'WEBHOOK',
					'data'  => array(
						'webhook_event' => $event,
						'timestamp'     => $timestamp,
						'site_id'       => $wpcloud_site_id,
						'data'          => $data,
					),
				)
			);
		}

		/**
		 * Log API request event.
		 *
		 * @param int    $wpcloud_site_id The site ID.
		 * @param string $method          The method.
		 * @param string $path            The path.
		 * @param array  $body            The body.
		 */
		public function log_api_request( $wpcloud_site_id, $method, $path, $body = array() ) {
			self::log_event(
				array(
					'event' => 'API_REQUEST',
					'data'  => array(
						'site_id' => $wpcloud_site_id,
						'method'  => $method,
						'path'    => $path,
						'body'    => $body,
					),
				)
			);
		}

		/**
		 * Log API response event.
		 *
		 * @param int    $wpcloud_site_id The site ID.
		 * @param string $method          The method.
		 * @param string $path            The path.
		 * @param array  $response        The response.
		 */
		public function log_api_response( $wpcloud_site_id, $method, $path, $response ) {
			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_body    = wp_remote_retrieve_body( $response );
			$result           = json_decode( $response_body );
			$response_message = $result->message ?? '';

			self::log_event(
				array(
					'event' => 'API_RESPONSE',
					'data'  => array(
						'site_id'  => $wpcloud_site_id,
						'method'   => $method,
						'path'     => $path,
						'response' => array(
							'code'    => $response_code,
							'message' => $response_message,
							'result'  => $result->data ?? array(),
						),
					),
				)
			);
		}

		/**
		 * Get log file.
		 *
		 * @return string The log file.
		 */
		public static function log_file(): string {
			$id = wp_get_session_token();
			if ( ! $id ) {
				debug_log( 'No session token' );
				return '';
			}

			return self::$log_path . $id . '.log';
		}

		/**
		 * Log session event.
		 *
		 * @param array $event The event.
		 */
		public static function log_event( array $event ) {
			$log_file = self::log_file();
			if ( ! file_exists( self::$log_path ) ) {
				mkdir( self::$log_path, 0755, true ); // phpcs:ignore
			}

			$event['timestamp'] = microtime( true );

			if ( ! $log_file ) {
				return;
			}

			// @TODO: make this configurable.
			$truncate_time = 3600; // 1 hour

			if ( file_exists( $log_file ) && ( time() - filemtime( $log_file ) > $truncate_time ) ) {
				file_put_contents( $log_file, '' ); // phpcs:ignore
			}
			file_put_contents( $log_file, json_encode( $event ) . PHP_EOL, FILE_APPEND | LOCK_EX ); // phpcs:ignore
		}

		/**
		 * Flush log.
		 *
		 * @return array The events.
		 */
		public static function flush_log(): array {
			$events   = array();
			$log_file = self::log_file();
			if ( ! $log_file ) {
				return $events;
			}

			if ( ! file_exists( $log_file ) ) {
				debug_log( 'No log file' );
				return $events;
			}

			$count        = 0;
			$timeout_secs = 3;
			$got_lock     = true;

			$fp = fopen( $log_file, 'r+' ); // phpcs:ignore
			while ( ! flock( $fp, LOCK_EX | LOCK_NB, $wouldblock ) ) {
				if ( $wouldblock && $count++ < $timeout_secs ) {
					error_log( 'waiting on file lock' );
					sleep( 1 );
				} else {
					$got_lock = false;
					break;
				}
			}
			if ( $got_lock ) {
				$file_size = max( 0, filesize( $log_file ) );
				if ( $file_size > 0 ) {
					$log    = fread( $fp, $file_size ); // phpcs:ignore
					$events = explode( PHP_EOL, $log );
					ftruncate( $fp, 0 );
				}
				flock( $fp, LOCK_UN );
			}

			return $events;
		}

		/**
		 * Unlink log.
		 *
		 * @return bool Whether the log was unlinked.
		 */
		public static function unlink_log(): bool {
			$log_file = self::log_file();

			if ( file_exists( $log_file ) ) {
				return unlink( $log_file );
			}

			return false;
		}
	}
}
