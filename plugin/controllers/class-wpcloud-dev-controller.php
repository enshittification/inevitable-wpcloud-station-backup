<?php
/**
 * WP Cloud Dev Log.
 *
 * @package wpcloud-station
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Dev_Controller' ) ) {

	/**
 * Register the REST API routes.
 *
 * We only include this file if in dev mode so we register the routes and hooks here
 */
	add_action(
		'rest_api_init',
		function () {
			$dev_controller = new WPCLOUD_Dev_Controller();
			$dev_controller->register_routes();
		}
	);

	add_action(
		'init',
		function () {
			$dev_controller = new WPCLOUD_Dev_Controller();
			$dev_controller->register_hooks();
		}
	);
	/**
	 * Class WPCLOUD_Dev_Log.
	 */
	class WPCLOUD_Dev_Controller extends WP_REST_Controller {
		/**
		 * The namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wpcloud/v1';

		/**
		 * Rest base for the current object.
		 *
		 * @var string
		 */
		protected $rest_base = 'dev';


		/**
		 * Event name.
		 *
		 * @var string
		 */
		protected $log_event_name = 'wpcloud_station_dev_log';

		/**
		 * Message queue.
		 *
		 * @var array
		 */
		protected $message_queue = array();
		/**
		 * Register the routes.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/log',
				array(
					array(
						'methods'  => WP_REST_Server::READABLE,
						'callback' => array( $this, 'dev_log' ),
					),
				)
			);
		}

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
			$this->log_session_event(
				'webhook',
				array(
					'webhook_event' => $event,
					'timestamp'     => $timestamp,
					'site_id'       => $wpcloud_site_id,
					'data'          => $data,
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
			$this->log_event(
				'api_request',
				array(
					'site_id' => $wpcloud_site_id,
					'method'  => $method,
					'path'    => $path,
					'body'    => $body,
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
			$this->log_event(
				'api_response',
				array(
					'site_id' => $wpcloud_site_id,
					'method'  => $method,
					'path'    => $path,
				)
			);
		}

		/**
		 * GET Dev log.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function dev_log( $request ): null|WP_HTTP_Response {
			$response = null;
			$headers  = array(
				'Cache-Control'     => 'no-cache',
				'Connection'        => 'keep-alive',
				'X-Accel-Buffering' => 'no',
			);
			// Passing in the header content type is not overriding the default application/json.
			header( 'Content-Type: text/event-stream' );
			do {
				error_log( 'flushing message queue' );
				$response = new WP_HTTP_Response( $this->flush_message_queue(), 200, $headers );
				if ( connection_aborted() ) {
					error_log( 'Connection closed' );
					$response = new WP_HTTP_Response( 'data: {connection: "closed"}' . "\n\n", 503 );
					session_destroy();
					break;
				}

				sleep( 2 );
			} while ( true );

			return $response;
		}

		/**
		 * Flush message queue.
		 *
		 * @return  void
		 */
		public function flush_message_queue() {
			$message_queue = $this->get_events();

			ob_start();

			if ( empty( $message_queue ) ) {
				echo 'event: ' . $this->log_event_name . PHP_EOL;
				echo 'data: { "event": "" }' . PHP_EOL;
				echo 'retry: 10000' . PHP_EOL;
				echo PHP_EOL;
			}

			foreach ( $message_queue as $message ) {
				echo 'event: ' . $this->log_event_name . PHP_EOL;
				echo 'data: ' . wp_json_encode( $message ) . PHP_EOL;
				echo 'retry: 10000' . PHP_EOL;
				echo PHP_EOL;
			}

			ob_flush();
			flush();
		}


		/**
		 * Get messages.
		 */
		private function get_events() {
			return array();
		}

				/**
				 * Log session event.
				 *
				 * @param string $event The event name.
				 * @param array  $data  The data.
				 */
		private function log_event( $event, $data ) {
			error_log( 'logging session event: ' . $event );
			return;
		}
	}
}
