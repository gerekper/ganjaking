/**
 * Color Palette Component
 */

/**
 * External dependencies
 */
import React                                                  from 'react';

/**
 * WordPress dependencies
 */
import { BaseControl, ColorIndicator, ColorPalette }          from '@wordpress/components';
import { __experimentalUseEditorFeature as useEditorFeature } from '@wordpress/block-editor';
import { useInstanceId }                                      from '@wordpress/compose';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * Visual Label Element
 *
 * @param {string} label The label.
 * @param {string} colorValue The color.
 * @returns {JSX.Element}
 * @constructor
 */
function VisualLabel( {
						  label,
						  colorValue
					  } ) {

	return (
		<>
			{label}
			{!!colorValue && (
				<ColorIndicator colorValue={colorValue}/>
			)}
		</>
	);
}

/**
 * Color Palette Control
 *
 * @param {string} className The CSS class name.
 * @param {string} label The label.
 * @param {function} onChange The function callback fired on value change.
 * @param {string} value The initial value.
 * @param {string} help The help message.
 * @param {array} palette Array of palette colors.
 * @param {bool} clearable Set true to allow clear.
 * @returns {JSX.Element}
 * @constructor
 */
export default function ColorPaletteControl( {
												 className,
												 label,
												 onChange,
												 value,
												 help,
												 palette,
												 clearable
											 } ) {

	palette = !!palette ? palette : useEditorFeature( 'color.palette' );

	const instanceId = useInstanceId( ColorPaletteControl );
	const id         = `inspector-yith-color-palette-control-${instanceId}`;

	return (
		<BaseControl
			id={id}
			className={`block-editor-yith-color-palette-control ${className}`}
			help={help}
		>
			<fieldset>
				<legend>
					<div className="block-editor-yith-color-palette-control__color-indicator">
						<BaseControl.VisualLabel>
							<VisualLabel colorValue={value} label={label}/>
						</BaseControl.VisualLabel>
					</div>
				</legend>

				<ColorPalette
					value={value}
					onChange={onChange}
					colors={palette}
					clearable={clearable}
				/>
			</fieldset>
		</BaseControl> );
}