/**
 * External dependencies
 */
import React             from 'react';

/**
 * WordPress dependencies
 */
import { BaseControl }   from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import MultipleSelect    from './multiple-select';

import './style.scss';

/**
 * Color Picker Control
 *
 * @param {string} className The CSS class name.
 * @param {string} label The label.
 * @param {function} onChange The function callback fired on value change.
 * @param {string} value The initial value.
 * @param {string} help The help message.
 * @param {bool} disableAlpha Set true to disable the alpha
 * @returns {MultipleSelectControl}
 * @constructor
 */
export default function MultipleSelectControl(
	{
		className,
		label,
		onChange,
		value,
		help,
		options,
		messages
	}
) {

	const instanceId = useInstanceId( MultipleSelectControl );
	const id         = `inspector-yith-multiple-select-control-${instanceId}`;

	return <BaseControl
		id={id}
		label={label}
		className={`block-editor-yith-multiple-select-control ${className}`}
		help={help}
	>
		<MultipleSelect
			id={id}
			value={value}
			options={options}
			onChange={onChange}
			messages={messages}
		/>
	</BaseControl>
}