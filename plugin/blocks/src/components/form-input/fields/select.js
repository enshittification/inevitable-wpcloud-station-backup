/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function SelectField( {
	attributes,
	className,
	onValueChange,
} ) {
	const { name, value } = attributes;
	let { options } = attributes;

	if (!options) {
		options = [{ label: `{ ${name} } `, value: '' }];
	}

	return (
			<select
				className={ classNames(
					className,
					'wpcloud-station-form-input__select'
				) }
				aria-label={ __( 'Select' ) }
				value={ value }
				onChange={ ( event ) => onValueChange( event.target.value ) }
			>
				{ options.map( ( option ) => (
					<option key={ option.value } value={ option.value }>
						{ option.label }
					</option>
				) ) }
			</select>
	);
}
