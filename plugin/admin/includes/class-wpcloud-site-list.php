<?php
/**
 * WP Cloud Station Site List Table
 *
 * @package class-wpcloud-site-list
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WP Cloud Station Site List Table
 *
 * @package class-wpcloud-site-list
 */
class WPCLOUD_Site_List extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function construct() {
		parent::__construct(
			array(
				'singular' => 'site',
				'plural'   => 'sites',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare items
	 *
	 * @param array $options Query options.
	 */
	public function prepare_items( array $options = array() ) {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$defaults = array(
			'posts_per_page' => 20,
			'post_type'      => 'wpcloud_site',
			'post_status'    => 'any',
			'orderby'        => 'id',
			'order'          => 'asc',
		);

		$options = wp_parse_args( $options, $defaults );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$results               = new WP_Query( $options );

		if ( is_wp_error( $results ) ) {
			error_log( $results->get_error_message() ); // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

		$this->items = $results->posts;
	}

	/**
	 * Get sortable columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'select'  => '<input type="checkbox" />',
			'name'    => __( 'Name', 'wpcloud' ),
			'owner'   => __( 'Owner', 'wpcloud' ),
			'status'  => __( 'Status', 'wpcloud' ),
			'created' => __( 'Created', 'wpcloud' ),
			'tags'    => __( 'Tags', 'wpcloud' ),
		);

		return $columns;
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_id( $item ) {
		return $item->ID;
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_name( $item ) {
		$domain  = wpcloud_get_site_detail( $item->ID, 'domain_name' );
		$actions = array(
			// translators: %s: The Edit link.
			'edit'   => sprintf( __( '<a href="%s">Edit</a>' ), get_permalink( $item ) ),
			// translators: %s: The Delete link.
			'delete' => sprintf( __( '<a href="%s">Delete</a>' ), get_delete_post_link( $item->ID, '', true ) ),
		);

		return sprintf( '<a href="https://%1$s" target="_blank">%1s</a> %2$s', $domain, $this->row_actions( $actions ) );
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_status( $item ) {
		return $item->post_status;
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_select( $item ) {
		return sprintf(
			'<input type="checkbox" name="site[]" value="%s" />',
			$item->ID
		);
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_created( $item ) {
		$dt = get_post_datetime( $item->ID );
		return $dt->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_owner( $item ) {
		$owner_id = get_post_field( 'post_author', $item->ID );
		$owner    = get_userdata( $owner_id );
		return $owner->display_name;
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @return array
	 */
	public function column_tags( $item ) {
		$tags = get_the_tags( $item->ID );
		if ( ! $tags ) {
			return '';
		}
		return implode( ', ', array_map( fn( $tag ) => $tag->name, $tags ) );
	}

	/**
	 * Get sortable columns
	 *
	 * @param WP_Post $item The current item.
	 * @param string  $column_name The current column name.
	 * @return array
	 */
	protected function column_default( $item, $column_name ) {
		return '';
	}
}
