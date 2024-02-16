/**
 * Handle YITH Gutenberg Blocks Edit
 *
 * @var {Object} yithGutenbergBlocks The Gutenberg blocks object.
 */

/**
 * External dependencies
 */
import React                                                                                                                               from 'react';

/**
 * WordPress dependencies
 */
import { PanelBody, BaseControl, ToggleControl, SelectControl, TextControl, TextareaControl, CheckboxControl, RangeControl, RadioControl } from '@wordpress/components';
import { InspectorControls }                                                                                                               from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { Shortcode }                                                                                                                       from './components/shortcode';
import { checkForDeps }                                                                                                                    from './common';
import ColorPickerControl                                                                                                                  from './components/color-picker-control';
import ColorPaletteControl                                                                                                                 from './components/color-palette-control';

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
}

/**
 * Create edit function.
 *
 * @param {string} blockName The block name.
 * @param {Object} blockArgs The block arguments.
 * @returns {function({attributes?: *, className: *, setAttributes: *})}
 */
export const createEditFunction = ( blockName, blockArgs ) => {
	return function ( { attributes, className, setAttributes } ) {

		const onChangeHandler = ( new_value, attribute_name, controlType ) => {
			if ( ['colorpicker', 'color'].includes( controlType ) ) {
				new_value = new_value.color.getAlpha() < 1 ? new_value.color.toRgbString() : new_value.color.toHexString();
			}

			let updatedAttributes               = {};
			updatedAttributes[ attribute_name ] = new_value;
			setAttributes( updatedAttributes );
		}

		const getComponentControl = ( attributeName, attributeArgs ) => {
			const { controlType } = attributeArgs;
			const value           = attributes[ attributeName ];
			const helpMessage     = getHelpMessage( attributeArgs, value );
			let wrapperClassName  = `${blockName}__${attributeName}-field-wrapper`;
			const show            = checkForDeps( attributeArgs, attributes );

			if ( attributeArgs.wrapper_class ) {
				wrapperClassName += ' ' + attributeArgs.wrapper_class;
			}


			let componentControl = false;
			if ( show ) {
				switch ( controlType ) {
					case 'select':
						componentControl = <SelectControl
							className={wrapperClassName}
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							options={attributeArgs.options}
							selected={value}
							help={helpMessage}
							multiple={!!attributeArgs.multiple}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;

					case 'text':
						componentControl = <TextControl
							className={wrapperClassName}
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							help={helpMessage}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;

					case 'textarea':
						componentControl = <TextareaControl
							className={wrapperClassName}
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							help={helpMessage}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;

					case 'toggle':
						componentControl = <ToggleControl
							className={wrapperClassName}
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							help={helpMessage}
							checked={value}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;

					case 'checkbox':
						componentControl = <CheckboxControl
							className={wrapperClassName}
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							help={helpMessage}
							checked={value}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;

					case 'number':
					case 'range':
						componentControl = <RangeControl
							className={wrapperClassName}
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							help={helpMessage}
							min={attributeArgs.min}
							max={attributeArgs.max}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;

					case 'color':
					case 'colorpicker':
						componentControl = <ColorPickerControl
							className={wrapperClassName}
							key={attributeName}
							label={attributeArgs.label}
							help={helpMessage}
							value={value}
							disableAlpha={attributeArgs.disableAlpha}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}/>;
						break;

					case 'color-palette':
						componentControl = <ColorPaletteControl
							className={wrapperClassName}
							key={attributeName}
							label={attributeArgs.label}
							help={helpMessage}
							value={value}
							clearable={attributeArgs.clearable || false}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}/>;
						break;

					case 'radio':
						componentControl = <RadioControl
							key={attributeName}
							value={value}
							label={attributeArgs.label}
							options={attributeArgs.options}
							selected={value}
							checked={value}
							help={helpMessage}
							onChange={( newValue ) => {
								onChangeHandler( newValue, attributeName, controlType )
							}}
						/>;
						break;
					default:
						componentControl = false;
				}
			}
			return componentControl;
		}

		return (
			<>
				{!!blockArgs.attributes &&
				 <InspectorControls>
					 <PanelBody>
						 {Object.entries( blockArgs.attributes ).map( ( [attributeName, attributeArgs] ) => {
							 const ComponentControl = getComponentControl( attributeName, attributeArgs );

							 if ( ComponentControl ) {
								 return ( ComponentControl );
							 }
						 } )}
					 </PanelBody>
				 </InspectorControls>
				}
				{
					<Shortcode attributes={attributes} blockArgs={blockArgs}/>
				}
			</>
		);
	}
}