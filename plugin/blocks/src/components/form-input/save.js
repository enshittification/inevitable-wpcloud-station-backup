/**
 * External dependencies
 */
import classNames from 'classnames';
import removeAccents from 'remove-accents';

/**
 * WordPress dependencies
 */
import {
	RichText,
	useBlockProps,
	InnerBlocks,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles, // eslint-disable-line @wordpress/no-unsafe-wp-apis
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/block-editor';
import { __unstableStripHTML as stripHTML } from '@wordpress/dom'; // eslint-disable-line @wordpress/no-unsafe-wp-apis


/**
 * Internal dependencies
 */

import { Text, Password, Select } from './fields';

/**
 * Get the name attribute from a content string.
 *
 * @param {string} content The block content.
 *
 * @return {string} Returns the slug.
 */
const getNameFromLabel = ( content ) => {
	return (
		removeAccents( stripHTML( content ) )
			// Convert anything that's not a letter or number to a hyphen.
			.replace( /[^\p{L}\p{N}]+/gu, '_' )
			// Convert to lowercase
			.toLowerCase()
			// Remove any remaining leading or trailing hyphens.
			.replace( /(^-+)|(-+$)/g, '' )
	);
};

function renderField( attributes ) {
	const { type, displayAsToggle, submitOnChange } = attributes;

	const borderProps = getBorderClassesAndStyles( attributes );
	const colorProps = getColorClassesAndStyles( attributes );

	attributes.inputStyle = {
		...borderProps.style,
		...colorProps.style,
	};

	attributes.name = attributes.name || getNameFromLabel( attributes.label );

	const inputClasses = classNames(
		'wpcloud-block-form-input__input',
		colorProps.className,
		borderProps.className,
		{
			'is-toggle': displayAsToggle,
			'submit-on-change': submitOnChange
		}
	);
	if ('password' === type) {
		return <Password attributes={attributes} className={inputClasses} />;
	}

	return 'select' === type
		? <Select attributes={ attributes } className={ classNames( inputClasses, 'wpcloud-station-form-input__select' ) } />
		: <Text attributes={ attributes } className={ inputClasses } />;
}


export default function save( { attributes } ) {
	const { type, label, name, value, inlineLabel, hideLabel, displayAsToggle, uniqueId } = attributes;
	const blockProps = useBlockProps.save();

	if ( 'hidden' === type ) {
		return <input type={ type } name={ name } value={ value } />;
	}

	const inputField = renderField( attributes );

	return (
		<div {...blockProps}
			className={ classNames( 'wpcloud-block-form--input', blockProps.className, `wpcloud-block-form--input--${(displayAsToggle ? 'toggle' : type)}` ) }
		>
			{ /* eslint-disable jsx-a11y/label-has-associated-control */}
			{ displayAsToggle && inputField }
			<label
				className={ classNames( 'wpcloud-block-form-input__label', {
					'is-label-inline': inlineLabel,
					'is-toggle': displayAsToggle,
				})}
				for={ uniqueId }
			>
				{ displayAsToggle && (<span className="toggle-container"></span>)}
				{ ! hideLabel && (
					<span className="wpcloud-block-form-input__label-content">
						<span className="wpcloud-block-form-input__label-text">
							<RichText.Content value={label} />
						</span>
						<InnerBlocks.Content />
					</span>
				) }
				{ ! displayAsToggle && inputField }
			</label>
			{ /* eslint-enable jsx-a11y/label-has-associated-control */ }
		</div>
	);
}
