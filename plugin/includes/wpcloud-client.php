<?php
/**
 * WP Cloud API client library.
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );

// Cache singleton for common client requests.
$wpcloud_client_cache = new stdClass();

/**
 * Get WP Cloud Client Name from settings.
 *
 * @return string|null Client Name on success. WP_Error on error.
 */
function wpcloud_get_client_name(): mixed {
	$wpcloud_settings = get_option( 'wpcloud_settings' );

	if ( ! $wpcloud_settings ) {
		return null;
	}

	return $wpcloud_settings['wpcloud_client'] ?? null;
}

/**
 * Get WP Cloud API Key from settings.
 *
 * @return string|null Client API Key on success. WP_Error on error.
 */
function wpcloud_get_client_api_key(): mixed {
	$wpcloud_settings = get_option( 'wpcloud_settings' );

	if ( ! $wpcloud_settings ) {
		return null;
	}

	return $wpcloud_settings['wpcloud_api_key'] ?? null;
}

/**
 * Get the IPs for a client. If a domain is included, get suggested IPs if possible for the client.
 *
 * @param integer|null $wpcloud_site_id Optional. The WP Cloud Site ID.
 * @param string       $domain          The domain for which to get the IP addresses.
 *
 * @return object|WP_Error Domain verification record. WP_Error on error.
 */
function wpcloud_client_domain_ip_addresses( ?int $wpcloud_site_id, string $domain = '' ): mixed {
	$client_name = wpcloud_get_client_name();

	return wpcloud_client_get( $wpcloud_site_id, "get-ips/{$client_name}/{$domain}" );
}

/**
 * Validate whether a domain is in use on WP Cloud.
 *
 * @param integer|null $wpcloud_site_id Optional. The WP Cloud Site ID.
 * @param string       $domain          The name of the domain to validate.
 *
 * @return bool|WP_Error True if domain can be used. False if domain is currently in use. WP_Error on error.
 */
function wpcloud_client_domain_validate( ?int $wpcloud_site_id, string $domain ): mixed {
	$client_name = wpcloud_get_client_name();

	$response = wpcloud_client_get( $wpcloud_site_id, "check-can-host-domain/{$client_name}/{$domain}" );

	// If an error is returned, check if it is for existing domain mapping.
	// If so, provide a friendly response with information about next steps.
	// Otherwise, return the error.
	if ( is_wp_error( $response ) ) {
		if ( ! strpos( $response->get_error_message(), 'Domain mapping record already exists' ) ) {
			$domain_verification_record = wpcloud_client_domain_verification_record( $wpcloud_site_id, $domain );

			return new WP_Error(
				'conflict',
				'Domain mapping already exists. To map domain to new site, add the domain verification record to DNS TXT records to provide proof of ownership.',
				array(
					'domain-verification-record' => $domain_verification_record,
					'status'                     => 409,
				)
			);
		}

		return $response;
	}

	return (bool) $response->allowed;
}

/**
 * Retrieve the domain verification record for a domain.
 *
 * @param integer|null $wpcloud_site_id Optional. The WP Cloud Site ID.
 * @param string       $domain          The domain for which to get the domain verification record.
 *
 * @return string|WP_Error Domain verification record. WP_Error on error.
 */
function wpcloud_client_domain_verification_record( ?int $wpcloud_site_id, string $domain ): mixed {
	$client_name = wpcloud_get_client_name();

	return wpcloud_client_get( $wpcloud_site_id, "get-domain-verification-code/{$client_name}/{$domain}" );
}

/**
 * Create a new site.
 *
 * When a new site is created, it returns a Job ID for the site provisioning job which can be checked for completion.
 *
 * @param string $admin_user     The WordPress admin username for the new site.
 * @param string $admin_email    The WordPress admin email for the new site.
 * @param array  $data           Optional. An array of optional site creation parameters. Default: array()
 *                                         domain        The domain name for the site.
 *                                         demo_domain   Generate a demo domain for the site.
 *                                         admin_pass    The password for the admin user.
 *                                         clone_from    WP Cloud Site ID of the site from which to clone.
 *                                         db_charset    The database character set.
 *                                                       Options:
 *                                                           latin1
 *                                                           utf8
 *                                                           utf8mb4
 *                                                       Default: latin1
 *                                         db_collate    The database collation.
 *                                                       Options:
 *                                                           latin1_swedish_ci
 *                                                           utf8_general_ci
 *                                                           utf8mb4_general_ci
 *                                                       Default: Corresponds to the appropriate collation for the character set.
 *                                         geo_affinity  if specified, the site will be assigned a primary pool server based on the preferred data_center.
 *                                                       Options:
 *                                                           ams Amsterdam
 *                                                           bur Burbank
 *                                                           dca Washington DC
 *                                                           dfw Dallas/Ft. Worth
 *                                         php_version   The desired PHP version.
 *                                                       Options:
 *                                                           7.4
 *                                                           8.1
 *                                                           8.2
 *                                                           8.3
 *                                                       Default: 8.1
 *                                                       Note: PHP version options are subject to change as new versions are released and old version retired.
 *                                         space_quota   The desired space quota. Default: 200G.
 * @param array  $software       Optional. An array of plugins and themes with actions to install and/or activate on site creation. Default: array().
 * @param array  $meta           Optional. An array of keys and values for meta to be set for a site. Default: array()
 *                               Supported keys:
 *                                   development_mode
 *                                   privacy_model
 *                                   photon_subsizes
 *                                   static_file_404.
 *
 * @return object|WP_Error Create site job details. WP_Error on error.
 */
function wpcloud_client_site_create( string $admin_user, string $admin_email, array $data = array(), array $software = array(), array $meta = array() ): mixed {
	$client_name = wpcloud_get_client_name();

	$default_data = array(
		'admin_user'  => $admin_user,
		'admin_email' => $admin_email,
		'software'    => $software,
		'meta'        => $meta,
	);

	$domain_name = $data['domain_name'] ?? '';
	// First validate if the domain if provided.
	if ( $domain_name ) {
		$validate_domain = wpcloud_client_domain_validate( null, $domain_name );
		if ( is_wp_error( $validate_domain ) ) {
			return $validate_domain;
		} elseif ( ! $validate_domain ) {
			return new WP_Error( 'forbidden', 'Domain cannot be hosted' );
		}
		$default_data['domain_name'] = $domain_name;
	}

	$data = array_merge(
		$data,
		$default_data
	);

	return wpcloud_client_post( null, "create-site/{$client_name}", $data );
}

/**
 * Delete a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return object|WP_Error Delete site job detals. WP_Error on error.
 */
function wpcloud_client_site_delete( int $wpcloud_site_id ): mixed {
	$client_name = wpcloud_get_client_name();

	return wpcloud_client_post( $wpcloud_site_id, "delete-site/{$client_name}/{$wpcloud_site_id}" );
}

/**
 * Get details of a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param bool    $extra           Include extra details.
 * @param bool    $use_cache       Optional. True to use cache. Default: true.
 *
 * @return object|WP_Error Site details. WP_Error on error.
 */
function wpcloud_client_site_details( int $wpcloud_site_id, bool $extra = false, bool $use_cache = true ): mixed {
	$path = "get-site/{$wpcloud_site_id}";

	if ( $extra ) {
		$path .= '/extra';
	}

	if ( $use_cache ) {
		return wpcloud_client_get_through_cache( $wpcloud_site_id, fn() => wpcloud_client_get( $wpcloud_site_id, $path ) );
	}

	return wpcloud_client_get( $wpcloud_site_id, $path );
}

/**
 * Add domain alias (CNAME) for a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $domain          The domain to add as an alias.
 *
 * @return array|WP_Error List of domain aliases. WP_Error on error.
 */
function wpcloud_client_site_domain_alias_add( int $wpcloud_site_id, string $domain ): mixed {
	$client_name = wpcloud_get_client_name();

	$response = wpcloud_client_get( $wpcloud_site_id, "site-alias/{$client_name}/{$wpcloud_site_id}/add/{$domain}" );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( ! isset( $response->domains ) ) {
		return array();
	}

	return $response->domains;
}

/**
 * Get a list of domain aliases for a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return array|WP_Error List of domain aliases. WP_Error on error.
 */
function wpcloud_client_site_domain_alias_list( int $wpcloud_site_id ): mixed {
	$client_name = wpcloud_get_client_name();

	$response = wpcloud_client_get( $wpcloud_site_id, "site-alias/{$client_name}/{$wpcloud_site_id}/list" );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( ! isset( $response->domains ) ) {
		return array();
	}

	return $response->domains;
}

/**
 * Remove domain alias (CNAME) for a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $domain          The domain to remove as an alias.
 *
 * @return array|WP_Error List of domain aliases. WP_Error on error.
 */
function wpcloud_client_site_domain_alias_remove( int $wpcloud_site_id, string $domain ): mixed {
	$client_name = wpcloud_get_client_name();

	$response = wpcloud_client_get( $wpcloud_site_id, "site-alias/{$client_name}/{$wpcloud_site_id}/remove/{$domain}" );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( ! isset( $response->domains ) ) {
		return array();
	}

	return $response->domains;
}

/**
 * Get SSL certificate information for site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $domain          Required. The new domain for the site.
 * @param bool    $keep            Optional. True to keep previous domain as an alias. Default: false.
 *
 * @return object|WP_Error SSL certificate information on success. WP_Error on error.
 */
function wpcloud_client_site_domain_primary_set( int $wpcloud_site_id, string $domain, bool $keep = false ): mixed {
	$client_name = wpcloud_get_client_name();

	$path = "update-site-domain/{$client_name}/{$wpcloud_site_id}/{$domain}";

	if ( $keep ) {
		$path .= '/keep';
	}

	return wpcloud_client_post( $wpcloud_site_id, $path );
}

/**
 * Get a list of sites for the client.
 *
 * @param string[] ...$meta_keys One or more meta keys to include in response.
 *                               Supported: wp_version, php_version, space_quota, db_file_size, static_file_404, suspended.
 *
 * @return array|WP_Error Site status details or error.
 */
function wpcloud_client_site_list( array ...$meta_keys ): mixed {
	$client_name = wpcloud_get_client_name();
	$path        = "get-sites/{$client_name}/";

	foreach ( $meta_keys as $meta_key ) {
		$path .= "{$meta_key}/";
	}

	return wpcloud_client_get( null, $path );
}

/**
 * Manages plugins/themes on WP Cloud site.
 *
 * Accepts an associative array of plugins/themes and their status.
 * Keys:
 *  - plugins/<plugin-slug>[/<version>] // Optional version. Discouraged. Defaults to latest.
 *  - themes/<theme-slug>
 * Possible statuses:
 *  - 'activate'   // Install if missing and activate.
 *  - 'install'    // Just install, don't activate.
 *  - 'deactivate' // Deactivate if active, but do not remove.
 *  - 'remove'     // Deactivate if active, and remove.
 *
 * Example:
 * $software = [
 *     'plugins/hello-dolly'                => 'remove',
 *     'themes/pub/akuta'                   => 'deactivate',
 *     'plugins/wordpress-seo/2.4.2'        => 'install',
 *     'themes/premium/caldwell'            => 'activate',
 * ];
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param array   $software        Array of plugins and themes.
 *
 * @return object|WP_Error Job ID on success. WP_Error on error.
 */
function wpcloud_client_site_manage_software( int $wpcloud_site_id, array $software ): mixed {
	$client_name = wpcloud_get_client_name();

	return wpcloud_client_post( $wpcloud_site_id, "site-manage-software/{$client_name}/{$wpcloud_site_id}", $software );
}

/**
 * Get PHPMyAdmin URL for site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return string|WP_Error PHPMyAdmin URL on success. WP_Error on error.
 */
function wpcloud_client_site_phpmyadmin_url( int $wpcloud_site_id ): mixed {
	$response = wpcloud_client_post( $wpcloud_site_id, "site-phpmyadmin/{$wpcloud_site_id}" );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	return $response->url;
}

/**
 * Get SSL certificate information for site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return object|WP_Error SSL certificate information on success. WP_Error on error.
 */
function wpcloud_client_site_ssl_info( int $wpcloud_site_id ): mixed {
	return wpcloud_client_post( $wpcloud_site_id, "ssl-info/{$wpcloud_site_id}" );
}

/**
 * Get IP addresses
 *
 * @param string $domain Optional. The domain for which to get the IP addresses.
 *
 * @return object|WP_Error IP address information on success. WP_Error on error.
 */
function wpcloud_client_site_ip_addresses( string $domain = '' ): mixed {
	$client_name = wpcloud_get_client_name();
	return wpcloud_client_get( null, "get-ips/$client_name/$domain" );
}

/**
 * Retry SSL certificate provisioning.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $domain          The domain of the site.
 *
 * @return bool|WP_Error True if retry queued. False if not queued.. WP_Error on error.
 */
function wpcloud_client_site_ssl_retry( int $wpcloud_site_id, string $domain ): mixed {
	$response = wpcloud_client_post( $wpcloud_site_id, "ssl-retry/{$domain}" );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	return (bool) $response->queued;
}

/**
 * Get PHP versions available for the client.
 *
 * @param bool $descending Optional. True to sort in descending order. Default: false.
 * @param bool $use_cache  Optional. True to use cache. Default: true.
 *
 * @return array|WP_Error Array of PHP versions available. WP_Error on error.
 */
function wpcloud_client_php_versions_available( bool $descending = false, bool $use_cache = true ): stdClass|WP_error {
	$get_php_version = function () use ( $descending ) {
		$client_name = wpcloud_get_client_name();
		$response    = wpcloud_client_get( null, "get-php-versions/$client_name" );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$result = array_reduce(
			$response,
			function ( $versions, $version ) {
				$versions[ $version ] = $version;
				return $versions;
			},
			array()
		);

		if ( $descending ) {
			arsort( $result );
		}

		return (object) $result;
	};

	return $use_cache ? wpcloud_client_get_through_cache( 0, $get_php_version, 'php_versions' ) : $get_php_version();
}

/**
 * Data center labels. These are used to display the data center in the UI.
 * Call wpcloud_client_data_centers_available() to get actual data centers available
 *
 * @return array Array of data center codes and names. WP_Error on error.
 */
function wpcloud_client_data_center_mapping(): array {
	return array(
		'ams' => __( 'Amsterdam, NL' ),
		'bur' => __( 'Los Angeles, CA' ),
		'dca' => __( 'Washington, D.C., USA' ),
		'dfw' => __( 'Dallas, TX, USA' ),
	);
}

/**
 * Get available datacenters for the client.
 *
 * @param bool $include_no_preference Optional. True to include an option for No Preference. Default: false.
 * @param bool $use_cache             Optional. True to use cache. Default: true.
 *
 * @return array|WP_Error List of datacenters available. WP_Error on error.
 */
function wpcloud_client_data_centers_available( bool $include_no_preference = false, bool $use_cache = true ): stdClass|WP_error {
	$get_data_centers = function () use ( $include_no_preference ) {
		$client_name = wpcloud_get_client_name();
		$response    = wpcloud_client_get( null, "get-available-datacenters/$client_name" );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$result = array_intersect_key( wpcloud_client_data_center_mapping(), array_flip( $response ) );

		if ( $include_no_preference ) {
			$result = array(
				'' => __( 'No Preference' ),
				...$result,
			);
		}

		return (object) $result;
	};

	return $use_cache ? wpcloud_client_get_through_cache( 0, $get_data_centers, 'data_centers' ) : $get_data_centers();
}

/**
 * Add an SSH user to a site.
 *
 * @param integer     $wpcloud_site_id The WP Cloud Site ID.
 * @param string      $user           The SSH user to add.
 * @param string      $pkey           The SSH public key for the user.
 * @param string|null $pass           Optional. The SSH password for the user.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_ssh_user_add( int $wpcloud_site_id, string $user, string $pkey = '', string|null $pass = null ): mixed {
	$client_name = wpcloud_get_client_name();

	$post = array(
		'pkey' => $pkey,
		'user' => $user,
	);
	if ( ! is_null( $pass ) ) {
		$post['pass'] = $pass;
	}

	$response = wpcloud_client_post( $wpcloud_site_id, "ssh-user/$client_name/$wpcloud_site_id/add", $post );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	return $response;
}

/**
 * Get a list of SSH users for a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return array|WP_Error List of SSH users. WP_Error on error.
 */
function wpcloud_client_ssh_user_list( int $wpcloud_site_id ): mixed {
	$client_name = wpcloud_get_client_name();
	return wpcloud_client_get( $wpcloud_site_id, "ssh-user/$client_name/$wpcloud_site_id/list" );
}

/**
 * Remove an SSH user from a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $user           The SSH user to remove.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_ssh_user_remove( int $wpcloud_site_id, string $user ): mixed {
	$client_name = wpcloud_get_client_name();
	return wpcloud_client_post( $wpcloud_site_id, "ssh-user/$client_name/$wpcloud_site_id/remove/$user" );
}

/**
 * Update an SSH user for a site.
 * If the user does not exist, it will be added.
 *
 * @param integer     $wpcloud_site_id The WP Cloud Site ID.
 * @param string      $user           The SSH user to update.
 * @param string      $pkey           The SSH public key for the user.
 * @param string|null $pass           Optional. The SSH password for the user.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_ssh_user_update( int $wpcloud_site_id, string $user, string $pkey = '', string|null $pass = null ): mixed {
	$client_name = wpcloud_get_client_name();

	$post = array(
		'pkey' => $pkey,
	);
	if ( ! is_null( $pass ) ) {
		$post['pass'] = $pass;
	}

	$response = wpcloud_client_post( $wpcloud_site_id, "ssh-user/$client_name/$wpcloud_site_id/update/$user", $post );
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	return $response;
}

/**
 * Disconnect all SSH users from a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_ssh_disconnect_all_users( int $wpcloud_site_id ): mixed {
	$client_name = wpcloud_get_client_name();
	return wpcloud_client_post( $wpcloud_site_id, "ssh-disconnect-all-users/$client_name/$wpcloud_site_id" );
}

/**
 * Get the SSH connection details for a site.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $access_type The type of access.
 *
 * @return object|WP_Error SSH connection details on success. WP_Error on error.
 */
function wpcloud_client_site_set_access_type( int $wpcloud_site_id, string $access_type ): mixed {
	$client_name = wpcloud_get_client_name();
	return wpcloud_client_get( $wpcloud_site_id, "site-set-access-type/$client_name/$wpcloud_site_id/$access_type" );
}

/**
 * Get the meta keys for a site.
 *
 * @return array The meta keys for a site.
 */
function wpcloud_client_site_meta_keys(): array {
	// @TODO move the labels to WPCLOUD_Site::get_meta_fields()
	return array(
		'db_charset'           => __( 'DB Charset' ),
		'db_collate'           => __( 'DB Collate' ),
		'suspended'            => __( 'Suspended Status Code' ),
		'suspend_after'        => __( 'Suspend After' ),
		'php_version'          => __( 'PHP Version' ),
		'wp_version'           => __( 'WP Version' ),
		'do_not_delete'        => __( 'Do Not Delete' ),
		'db_file_size'         => __( 'DB File Size' ),
		'space_quota'          => __( 'Space Quota' ),
		'max_space_quota'      => __( 'Max Space Quota (Gigabytes)' ),
		'photon_subsizes'      => __( 'Photon Subsizes' ),
		'privacy_model'        => __( 'Privacy Model' ),
		'static_file_404'      => __( 'Static File 404' ),
		'default_php_conns'    => __( 'Default PHP Connections' ),
		'burst_php_conns'      => __( 'Burst PHP Connections' ),
		'php_fs_permissions'   => __( 'PHP FS Permissions' ),
		'canonicalize_aliases' => __( 'Canonicalize Aliases' ),
		'ssh_port'             => __( 'SSH Port' ),
	);
}
/**
 *
 * Update site meta.
 *
 * Also handles updates for the WordPress version via the `site-wordpress-version` endpoint.
 *
 * @param integer     $wpcloud_site_id The WP Cloud Site ID.
 * @param string      $key            The meta key to update.
 * @param string|null $value       The value to set. If null, the key will be removed.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_update_site_meta( int $wpcloud_site_id, string $key, string|null $value ): mixed {
	if ( ! array_key_exists( $key, wpcloud_client_site_meta_keys() ) ) {
		return new WP_Error( 'bad_request', 'Invalid meta key', array( 'status' => 400 ) );
	}

	$endpoint = "site-meta/$wpcloud_site_id/$key/update";
	if ( 'wp_version' === $key ) {
		$endpoint = "site-wordpress-version/$wpcloud_site_id/$value";
	}
	if ( is_null( $value ) ) {
		$endpoint = "site-meta/$wpcloud_site_id/$key/remove";

	}
	return wpcloud_client_post( $wpcloud_site_id, $endpoint, array( 'value' => $value ) );
}

/**
 * Delete site meta.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $key            The meta key to delete.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_delete_site_meta( int $wpcloud_site_id, string $key ): mixed {
	if ( ! array_key_exists( $key, wpcloud_client_site_meta_keys() ) ) {
		return new WP_Error( 'bad_request', 'Invalid meta key', array( 'status' => 400 ) );
	}

	$endpoint = "site-meta/$wpcloud_site_id/$key/remove";
	return wpcloud_client_post( $wpcloud_site_id, $endpoint );
}

/**
 * Get site meta.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 * @param string  $key            The meta key to get.
 * @param bool    $use_cache      Optional. True to use cache. Default: true.
 *
 * @return stdClass | WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_get_site_meta( int $wpcloud_site_id, string $key, bool $use_cache = true ): stdClass|WP_Error {
	$get_meta = function () use ( $wpcloud_site_id, $key ) {
		if ( ! array_key_exists( $key, wpcloud_client_site_meta_keys() ) ) {
			return new WP_Error( 'bad_request', 'Invalid meta key', array( 'status' => 400 ) );
		}

		$endpoint = "site-meta/$wpcloud_site_id/$key/get";
		$result   = wpcloud_client_get( $wpcloud_site_id, $endpoint );

		// Normalize the result to be an object with the key as the property.
		if ( ! is_object( $result ) ) {
			$result = (object) array( $key => $result );
		}
		return $result;
	};

	return $use_cache ? wpcloud_client_get_through_cache( $wpcloud_site_id, $get_meta, $key ) : $get_meta();
}

/**
 * Get client meta by key
 *
 * @param string $key The meta key to get.
 *
 * @return stdClass | WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_get_client_meta( string $key ): stdClass|WP_Error {
	$client_name = wpcloud_get_client_name();
	$endpoint    = "client-meta/$client_name/$key/get";

	$result = wpcloud_client_get( 0, $endpoint );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	// Normalize the result to be an object with the key as the property.
	if ( ! is_object( $result ) ) {
		$result = (object) array( $key => $result );
	}
	return $result;
}

/**
 * Set client meta by key
 *
 * @param string $key The meta key to set.
 * @param string $value The value to set.
 *
 * @return stdClass | WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_set_client_meta( string $key, string $value ): stdClass|WP_Error {
	$client_name = wpcloud_get_client_name();
	$endpoint    = "client-meta/$client_name/$key/update";

	return (object) wpcloud_client_post( 0, $endpoint, array( 'value' => $value ) );
}

/**
 * Remove client meta by key
 *
 * @param string $key The meta key to remove.
 *
 * @return stdClass | WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_remove_client_meta( string $key ): stdClass|WP_Error {
	$client_name = wpcloud_get_client_name();
	$endpoint    = "client-meta/$client_name/$key/remove";

	return (object) wpcloud_client_get( 0, $endpoint );
}

/**
 * Get the status of a job.
 *
 * @param integer $job_id The job id for which to get the status.
 *
 * @return string|WP_Error "success", "failure", "queued". WP Error on error.
 */
function wpcloud_client_job_status( int $job_id ) {
	return wpcloud_client_get( null, "job-completion/{$job_id}" );
}

/**
 * Make a GET request the WP Cloud API.
 *
 * @param integer|null $wpcloud_site_id Optional. The WP Cloud Site ID.
 * @param string       $path            The request path without host. e.g. 'get-site/example.com'.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_get( ?int $wpcloud_site_id, string $path ): mixed {
	do_action( 'wpcloud_client_request', $wpcloud_site_id, 'GET', $path );
	return wpcloud_client_request( $wpcloud_site_id, 'GET', $path );
}

/**
 * Make a POST request the WP Cloud API.
 *
 * @param integer|null $wpcloud_site_id Optional. The WP Cloud Site ID.
 * @param string       $path            The request path without host. e.g. 'get-site/example.com'.
 * @param array        $body            The body of the request as an array.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_post( ?int $wpcloud_site_id, string $path, array $body = array() ): mixed {
	do_action( 'wpcloud_client_request', $wpcloud_site_id, 'POST', $path, $body );
	return wpcloud_client_request( $wpcloud_site_id, 'POST', $path, $body );
}

/**
 * Make a request to WP Cloud API.
 *
 * @param integer|null $wpcloud_site_id Optional. The WP Cloud Site ID.
 * @param string       $method          HTTP Request method. 'GET' or 'POST'.
 * @param string       $path            The request path without host. e.g. 'get-site/example.com'.
 * @param array        $body            The body of the request as an array.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_request( ?int $wpcloud_site_id, string $method, string $path, array $body = array() ): mixed {
	$api_key     = wpcloud_get_client_api_key();
	$client_name = wpcloud_get_client_name();

	if ( empty( $api_key ) ) {
		return new WP_Error( 'unauthorized', 'Please provide a WP Cloud API Key in Settings', array( 'status' => 401 ) );
	}
	if ( empty( $client_name ) ) {
		return new WP_Error( 'unauthorized', 'Please provide a WP Cloud Client Name in Settings', array( 'status' => 401 ) );
	}

	$scheme   = 'https';
	$hostname = 'atomic-api.wordpress.com';
	$url      = "{$scheme}://{$hostname}/api/v1.0/{$path}";

	$args = array(
		'redirection' => 0,
		'timeout'     => 10,
		'body'        => $body,
		'headers'     => array(
			'auth'       => $api_key,
			'user-agent' => $client_name,
			'host'       => $hostname,
		),
	);

	switch ( $method ) {
		case 'GET':
			$response = wp_remote_get( $url, $args );
			break;
		case 'POST':
			$response = wp_remote_post( $url, $args );
			break;
		default:
			return new WP_Error( 'method_not_supported', 'Request method must be GET or POST', array( 'status' => 400 ) );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$response_code    = wp_remote_retrieve_response_code( $response );
	$response_body    = wp_remote_retrieve_body( $response );
	$result           = json_decode( $response_body );
	$response_message = $result->message ?? '';

	switch ( $response_code ) {
		case 200:
		case 201:
		case 202:
		case 204:
			break;
		case 400:
			$message = 'Request was not properly formed';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'bad_request', $response_message, array( 'status' => 400 ) );
			break;
		case 401:
			$result = new WP_Error( 'unauthorized', 'Request is unauthorized', array( 'status' => 401 ) );
			break;
		case 403:
			$result = new WP_Error( 'forbidden', 'Request is not allowed', array( 'status' => 403 ) );
			break;
		case 404:
			$result = new WP_Error( 'not_found', 'Resource not found', array( 'status' => 404 ) );
			break;
		case 409:
			$message = 'Request conflicts with expectations';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'conflict', $message, array( 'status' => 409 ) );
			break;
		case 500:
			$message = 'Internal server error while executing the request';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'internal_server_error', $message, array( 'status' => 500 ) );
			break;
		case 501:
			$result = new WP_Error( 'not_implemented', 'Request is not implemented', array( 'status' => 501 ) );
			break;
		case 504:
			$result = new WP_Error( 'timeout', 'Request did not complete in the allowed time period', array( 'status' => 504 ) );
			break;
		default:
			$message = 'Error while executing the request';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( $response_code, $message, array( 'status' => 500 ) );
	}

	if ( is_wp_error( $result ) ) {
		/**
		 * Action triggered when an error occurs during a WP Cloud API request.
		 *
		 * @param integer|null   The WP Cloud Site ID.
		 * @param string         HTTP Request method. 'GET' or 'POST'.
		 * @param string         The request path without host. e.g. 'get-site/example.com'.
		 * @param array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. WP_Error on failure.
		 */
		do_action( 'wpcloud_client_response_error', $wpcloud_site_id, $method, $path, $response );

		return $result;
	}

	/**
	 * Action triggered on successful WP Cloud API request.
	 *
	 * @param integer|null   The WP Cloud Site ID.
	 * @param string         HTTP Request method. 'GET' or 'POST'.
	 * @param string         The request path without host. e.g. 'get-site/example.com'.
	 * @param array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. WP_Error on failure.
	 */
	do_action( 'wpcloud_client_response_success', $wpcloud_site_id, $method, $path, $response );

	// Return data if provided.
	if ( is_object( $result ) && isset( $result->data ) ) {
		return $result->data;
	}

	return $result;
}

/**
 * Read through cache.
 *
 * @param int      $wpcloud_site_id The site ID.
 * @param callable $call_client The client function to call if the cache is empty.
 * @param string   $key The key to get from the cache.
 * @return stdClass | WP_Error The result of the client function or WP_Error if the client function fails.
 */
function wpcloud_client_get_through_cache( int $wpcloud_site_id, callable $call_client, $key = '' ): stdClass|WP_Error {

	// Check if the client cache is enabled.
	$is_enabled = get_option( 'wpcloud_settings', array() )['client_cache'] ?? false;
	if ( ! $is_enabled ) {
		return $call_client();
	}
	global $wpcloud_client_cache;
	$cached = $wpcloud_client_cache->$wpcloud_site_id ?? null;

	$cache_hit = $cached && ( ! $key || ( $key && isset( $cached?->$key ) ) );

	if ( $cache_hit ) {
		return $key ? $cached->$key : $cached;
	}

	$cache_item = $call_client();
	if ( is_wp_error( $cache_item ) ) {
		return $cache_item;
	}

	// If we are not looking for a key we can cache the whole result and return it.
	if ( ! $key ) {
		$wpcloud_client_cache->$wpcloud_site_id = $cache_item;
		return $cache_item;
	}

	// If the cache was completely empty we need to create the object to cache the key.
	if ( is_null( $cached ) ) {
		$wpcloud_client_cache->$wpcloud_site_id = new stdClass();
	}

	$wpcloud_client_cache->$wpcloud_site_id->$key = $cache_item;

	return $cache_item;
}
