<?php
/**
 * Site Create block.
 *
 * @package wpcloud-block
 * @subpackage site-create
 */

/**
 * Add the site create fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_create_fields( array $fields ) {
	$site_create_fields = array( 'site_name', 'domain_name', 'php_version', 'data_center', 'site_owner_id', 'site_pass', 'site_email', 'admin_pass' );
	return array_merge( $fields, $site_create_fields );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_create', 'wpcloud_block_form_site_create_fields', 11, 1 );


/**
 * Process the form data for site create
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_create_handler( $response, $data ) {
	if ( ! isset( $data['site_owner_id'] ) ) {
		$data['site_owner_id'] = get_current_user_id();
	}

	if ( ! isset( $data['site_name'] ) ) {
		$data['site_name'] = $data['domain_name'];
	}

	$site = WPCloud_Site::create( $data );
	if ( is_wp_error( $site ) ) {
		$response['success'] = false;
		$response['message'] = $site->get_error_message();
		$response['status']  = 400;
		return $response;
	}
	if ( $site->error_message ) {
		$response['success'] = false;
		$response['message'] = $site->error_message;
		$response['status']  = 400;
		return $response;
	}

	$response['message']  = 'Site request successful.';
	$response['post_id']  = $site->ID;
	$response['redirect'] = '/sites';

	return $response;
}
add_filter( 'wpcloud_form_process_site_create', 'wpcloud_block_form_site_create_handler', 10, 2 );

/**
 * Render the site owner field with the available users.
 *
 * @param string $content The content of the field.
 * @return string The content of the field with the users.
 */
function wpcloud_block_form_render_field_site_owner_id( $content ) {
	$users        = get_users();
	$current_user = wp_get_current_user();
	$options      = '<option value="' . $current_user->ID . '">' . $current_user->display_name . '</option>';
	foreach ( $users as $user ) {
		if ( $user->ID === $current_user->ID ) {
			continue;
		}
		$options .= sprintf(
			'<option value="%d">%s</option>',
			$user->ID,
			$user->display_name
		);
	}

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	return preg_replace( $regex, '$1' . $options . '$2', $content );
}
add_filter( 'wpcloud_block_form_render_field_site_owner_id', 'wpcloud_block_form_render_field_site_owner_id' );



/**
 * Render the PHP version field with the available PHP versions.
 * We need to dynamically populate the PHP versions since they could change after the create site form is added to a page.
 *
 * @param string $content The content of the field.
 * @return string The content of the field with the PHP versions.
 */
function wpcloud_block_form_render_field_php_version( $content ): string {

	$php_versions = wpcloud_block_available_php_options();

	// if for some reason we don't have any php versions, return the content as is.
	if ( empty( $php_versions ) ) {
		return $content;
	}

	$options = array_reduce(
		array_keys( $php_versions ),
		function ( $options, $key ) use ( $php_versions ) {

			$options .= sprintf(
				'<option value="%s">%s</option>',
				$key,
				$php_versions[ $key ]
			);
			return $options;
		},
		''
	);

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	return preg_replace( $regex, '$1' . $options . '$2', $content );
}
add_action( 'wpcloud_block_form_render_field_php_version', 'wpcloud_block_form_render_field_php_version' );

/**
 * Render the data center field with the available data centers.
 *
 * @param string $content The content of the field.
 * @return string The content of the field with the data centers.
 */
function wpcloud_block_form_render_field_data_center( $content ): string {
	$dcs = wpcloud_block_available_datacenters_options();

	// if for some reason we don't have any data centers, return the content as is.
	if ( empty( $dcs ) ) {
		return $content;
	}

	$options = array_reduce(
		array_keys( $dcs ),
		function ( $options, $key ) use ( $dcs ) {
			$options .= sprintf(
				'<option value="%s">%s</option>',
				$key,
				$dcs[ $key ]
			);
			return $options;
		},
		''
	);

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	return preg_replace( $regex, '$1' . $options . '$2', $content );
}
add_action( 'wpcloud_block_form_render_field_data_center', 'wpcloud_block_form_render_field_data_center' );
