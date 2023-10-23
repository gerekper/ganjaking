/**
 * Handle YITH Gutenberg Blocks Edit
 *
 * @var {Object} yithGutenbergBlocks The Gutenberg blocks object.
 */

/**
 * External dependencies
 */
import React                 from 'react';

/**
 * WordPress dependencies
 */
import {
	Disabled,
	PanelBody,
	ToggleControl,
	SelectControl,
	TextControl,
	TextareaControl,
	CheckboxControl,
	RangeControl,
	RadioControl,
	Spinner
}                            from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';


/**
 * Internal dependencies
 */
import { Shortcode }         from './components/shortcode';
import { checkForDeps }      from './common';
import ColorPickerControl    from './components/color-picker-control';
import ColorPaletteControl   from './components/color-palette-control';
import MultipleSelectControl from './components/multiple-select-control';
import classNames            from 'classnames';
import FrameBlock            from './components/frame-block';
import EditorPlaceholder     from './components/editor-placeholder';
import ServerSideBlock       from './components/server-side-block';

/**
 * Retrieve an help message from arguments.
 *
 * @param {Object} args The arguments.
 * @param {bool} value The value.
 * @returns {string}
 */
const getHelpMessage = ( args, value ) => {
	let helpMessage = '';
	if ( args.helps && args.helps.checked && args.helps.unchecked ) {
		helpMessage = !!value ? args.helps.checked : args.helps.unchecked;
	} else if ( args.help ) {
		helpMessage = args.help;
	}
	return helpMessage;
};

const ComponentControl = ( { attributeName, attributeArgs, attributes, onChange, blockName } ) => {
	const { controlType, label, wrapper_class } = attributeArgs;
	const value                                 = attributes[ attributeName ];
	const helpMessage                           = getHelpMessage( attributeArgs, value );
	const show                                  = checkForDeps( attributeArgs, attributes );
	const wrapperClass                          = classNames(
		`${blockName}__${attributeName}-field-wrapper`,
		wrapper_class
	);

	let componentControl = false;
	if ( show ) {
		switch ( controlType ) {
			case 'select':
				if ( !attributeArgs.multiple ) {
					componentControl = <SelectControl
						className={wrapperClass}
						value={value}
						label={label}
						options={attributeArgs?.options ?? []}
						help={helpMessage}
						onChange={onChange}
					/>;
				} else {
					componentControl = <MultipleSelectControl
						className={wrapperClass}
						value={value}
						label={label}
						options={attributeArgs?.options ?? []}
						help={helpMessage}
						onChange={onChange}
						messages={attributeArgs?.messages ?? {}}
					/>;
				}
				break;

			case 'text':
				componentControl = <TextControl
					className={wrapperClass}
					key={attributeName}
					value={value}
					label={label}
					help={helpMessage}
					onChange={onChange}
				/>;
				break;

			case 'textarea':
				componentControl = <TextareaControl
					className={wrapperClass}
					key={attributeName}
					value={value}
					label={label}
					help={helpMessage}
					onChange={onChange}
				/>;
				break;

			case 'toggle':
				componentControl = <ToggleControl
					className={wrapperClass}
					key={attributeName}
					label={label}
					help={helpMessage}
					checked={value}
					onChange={onChange}
				/>;
				break;

			case 'checkbox':
				componentControl = <CheckboxControl
					className={wrapperClass}
					key={attributeName}
					label={label}
					help={helpMessage}
					checked={value}
					onChange={onChange}
				/>;
				break;

			case 'number':
			case 'range':
				componentControl = <RangeControl
					className={wrapperClass}
					key={attributeName}
					value={value}
					label={label}
					help={helpMessage}
					min={attributeArgs?.min}
					max={attributeArgs?.max}
					onChange={onChange}
				/>;
				break;

			case 'color':
			case 'colorpicker':
				componentControl = <ColorPickerControl
					className={wrapperClass}
					key={attributeName}
					label={label}
					help={helpMessage}
					value={value}
					disableAlpha={attributeArgs?.disableAlpha ?? false}
					onChange={onChange}/>;
				break;

			case 'color-palette':
				componentControl = <ColorPaletteControl
					className={wrapperClass}
					key={attributeName}
					label={label}
					help={helpMessage}
					value={value}
					clearable={attributeArgs?.clearable ?? false}
					onChange={onChange}/>;
				break;

			case 'radio':
				componentControl = <RadioControl
					key={attributeName}
					label={label}
					options={attributeArgs?.options ?? []}
					selected={value}
					help={helpMessage}
					onChange={onChange}
				/>;
				break;
			default:
				componentControl = false;
		}
	}
	return componentControl;
}

/**
 * Create edit function.
 *
 * @param {string} blockName The block name.
 * @param {Object} blockArgs The block arguments.
 * @returns {function({attributes?: *, className: *, setAttributes: *})}
 */
export const createEditFunction = ( blockName, blockArgs ) => {
	return function ( { context, attributes, className, setAttributes } ) {

		const onChangeHandler = ( updatedValue, attributeName, controlType ) => {
			if ( ['colorpicker', 'color'].includes( controlType ) ) {
				if ( 'rgb' in updatedValue && 'hex' in updatedValue ) {
					const { r, g, b, a } = updatedValue.rgb;
					updatedValue         = a < 1 ? `rgba(${r}, ${g}, ${b}, ${a})` : updatedValue.hex;
				} else {
					updatedValue = updatedValue.color.getAlpha() < 1 ? updatedValue.color.toRgbString() : updatedValue.color.toHexString();
				}
			}

			setAttributes( { [ attributeName ]: updatedValue } );
		}

		let renderType = 'shortcode';

		if ( blockArgs.render_callback ) {
			renderType = blockArgs.use_frontend_preview ? 'frontend' : 'ssr';
		}

		if ( blockArgs.editor_placeholder ) {
			renderType = 'placeholder';
		}

		const disabledByDefault = renderType !== 'shortcode';
		const shouldUseDisabled = Boolean( blockArgs.should_use_disabled ?? disabledByDefault );
		const MaybeDisabled     = shouldUseDisabled ? Disabled : Fragment;

		return (
			<>
				{!!blockArgs.attributes &&
				 <InspectorControls>
					 <PanelBody>
						 {Object.entries( blockArgs.attributes ).map( ( [attributeName, attributeArgs] ) => {
							 const { controlType } = attributeArgs;
							 return <ComponentControl
								 key={attributeName}
								 attributeArgs={attributeArgs}
								 attributeName={attributeName}
								 attributes={attributes}
								 blockName={blockName}
								 onChange={_ => onChangeHandler( _, attributeName, controlType )}
							 />
						 } )}
					 </PanelBody>
				 </InspectorControls>
				}
				<MaybeDisabled>
					{
						'shortcode' === renderType &&
						<Shortcode attributes={attributes} blockArgs={blockArgs} context={context}/>
					}
					{
						'frontend' === renderType &&
						<FrameBlock attributes={attributes} block={blockName} title={blockArgs.title} context={context}/>
					}
					{
						'ssr' === renderType &&
						<ServerSideBlock block={`yith/${blockName}`} attributes={attributes}/>
					}
					{
						'placeholder' === renderType &&
						<EditorPlaceholder blockArgs={blockArgs}/>
					}
				</MaybeDisabled>
			</>
		);
	}
}