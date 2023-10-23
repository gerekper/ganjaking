/**
 * Color Picker Component
 */

/**
 * External dependencies
 */
import React                        from 'react';

/**
 * WordPress dependencies
 */
import { BaseControl, ColorPicker } from '@wordpress/components';
import { useInstanceId }            from '@wordpress/compose';

/**
 * Color Picker Control
 *
 * @param {string} className The CSS class name.
 * @param {string} label The label.
 * @param {function} onChange The function callback fired on value change.
 * @param {string} value The initial value.
 * @param {string} help The help message.
 * @param {bool} disableAlpha Set true to disable the alpha
 * @returns {ColorPickerControl}
 * @constructor
 */
export default function ColorPickerControl( {
												className,
												label,
												onChange,
												value,
												help,
												disableAlpha,
											} ) {

	const instanceId = useInstanceId( ColorPickerControl );
	const id         = `inspector-yith-color-picker-control-${instanceId}`;

	return (
		<BaseControl
			id={id}
			label={label}
			className={`block-editor-yith-color-control ${className}`}
			help={help}
		>
			<ColorPicker
				color={value}
				disableAlpha={disableAlpha}
				onChangeComplete={onChange}
			/>
		</BaseControl> );
}