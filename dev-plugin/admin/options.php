<?php
/**
 * File description goes here.
 *
 * @package YourPackageName
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


?>
<style>
	.wp-cloud-settings {
		.description {
			width: 400px;
		}
	}
</style>
<div class="wrap wp-cloud-settings">
	<h1>WP Cloud Dev</h1>
	<form action="options.php" method="post">
			<?php
			settings_fields( 'wpcloud' );
			do_settings_sections( 'wpcloud' );
			submit_button( 'Save Settings' );
			?>
	</form>
