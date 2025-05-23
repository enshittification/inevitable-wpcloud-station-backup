/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType, registerBlockVariation } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

registerBlockVariation( 'core/list', {
	name: 'wpcloud/site-list-columns',
	title: 'Site List Header',
	description: 'Displays the header for a list of WP Cloud Sites',
	isActive: ( { namespace } ) => namespace === 'wpcloud/site-list--header',
	attributes: {
		namespace: 'wpcloud/site-list--header',
		className: 'wpcloud-site-list--header',
	},
	innerBlocks: [
		[ 'core/list-item', { content: __( 'Site', 'wpcloud' ) } ],
		[ 'core/list-item', { content: __( 'Owner', 'wpcloud' ) } ],
		[ 'core/list-item', { content: __( 'Created', 'wpcloud' ) } ],
		[ 'core/list-item', { content: __( 'PHP', 'wpcloud' ) } ],
		[ 'core/list-item', { content: __( 'WP Version', 'wpcloud' ) } ],
		[ 'core/list-item', { content: __( 'IP', 'wpcloud' ) } ],
		[ 'core/list-item', { content: __( 'Actions', 'wpcloud' ) } ],
	],
} );

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
