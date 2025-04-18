<?php
/**
 * Render the button block.
 *
 * @package wpcloud-block
 * @subpackage button
 */

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}


$classes           = array( 'wpcloud-block-button' );
$button_attributes = array();

$button_attributes['data-original-label'] = $attributes['label'] ?? '';

$block_type = $attributes['type'] ?? 'link';
$style      = $attributes['style'] ?? '';
$url        = '';

if ( 'button' === $style ) {
	$classes[] = 'wp-block-button__link wp-element-button';
	$classes[] = $attributes['isPrimary'] ?? true ? 'is-primary' : 'is-secondary';
} else {
	$classes[] = 'wpcloud-block-button__text';
}

switch ( $block_type ) {

	case 'link':
		$classes[] = 'wpcloud-block-button__link wp-element-button';
		$url       = $attributes['url'] ?? '/';
		break;

	case 'action':
		if ( isset( $attributes['action'] ) ) {
			$classes[]                                = 'wpcloud-block-button__action';
			$button_attributes['data-wpcloud-action'] = $attributes['action'];
		}
		break;

	case 'detail':
		$classes[] = 'wpcloud-block-button__detail';
		$detail    = wpcloud_get_site_detail( get_the_ID(), $attributes['name'] );
		if ( is_wp_error( $detail ) ) {
			error_log( $detail->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			break;
		}
		$button_attributes['data-wpcloud-detail'] = $attributes['name'];
		$url                                      = $detail;

		if ( wpcloud_should_refresh_detail( $attributes['name'] ) ) {
			$nonce                                  = wp_create_nonce( 'wpcloud_refresh_link' );
			$button_attributes['data-nonce']        = $nonce;
			$button_attributes['data-refresh-rate'] = $attributes['refreshRate'] ?? 10000;
			$button_attributes['data-site-id']      = get_the_ID();
		}
		break;

	case 'submit':
		$classes[]                 = 'wpcloud-block-button__submit';
		$button_attributes['type'] = 'submit';
		if ( isset( $attributes['action'] ) ) {
			$button_attributes['data-wpcloud-action'] = $attributes['action'];
		}
		break;

	default:
		$url = $attributes['url'] ?? '/';
}

$button_attributes['class'] = implode( ' ', $classes );

if ( $url ) {
	$dom = new DOMDocument();
	@$dom->loadHTML( $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	$xpath = new DOMXPath( $dom );
	$span  = $xpath->query( '//span[@class="wpcloud-block-button__label"]' )->item( 0 );

	if ( $span ) {
		$span_wrapper = $span->parentNode;
		$anchor       = $dom->createElement( 'a' );
		$anchor->setAttribute( 'href', $url );
		$anchor->appendChild( $span );

		// if there are multiple children, insert the anchor before the first child.
		if ( $span_wrapper->childNodes->length > 1 ) {
			$span_wrapper->insertBefore( $anchor, $span_wrapper->childNodes[0] );
		} else {
			$span_wrapper->appendChild( $anchor );
		}
	}
	$content = $dom->saveHTML();
}
$wrapper_attributes = get_block_wrapper_attributes( $button_attributes );

if ( 'button' === $style || 'submit' === $block_type ) {
	$wrapper = 'button';
} else {
	$wrapper = 'div';
}

printf( '<%1$s %2$s>%3$s</%1$s>', $wrapper, $wrapper_attributes, $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
