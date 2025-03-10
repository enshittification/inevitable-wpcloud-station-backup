/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';
import save from './save';

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
