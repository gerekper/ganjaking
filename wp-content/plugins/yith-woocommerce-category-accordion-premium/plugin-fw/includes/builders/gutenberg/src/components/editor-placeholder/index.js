/* globals bkBlocks */

import React         from 'react';
import { RawHTML }   from '@wordpress/element';
import classnames    from 'classnames';

import './style.scss';
import { yith_icon } from '../../common';

function EditorPlaceholder( { blockArgs } ) {
	const { editor_placeholder, title, description } = blockArgs;
	const isDefaultPlaceholder                       = true === editor_placeholder;
	const classes                                    = classnames(
		'yith-plugin-fw-blocks__editor-placeholder',
		isDefaultPlaceholder && 'yith-plugin-fw-blocks__editor-placeholder--default'
	);

	if ( isDefaultPlaceholder ) {
		return <div className={classes}>
			<div className="yith-plugin-fw-blocks__editor-placeholder__title">{yith_icon}{title}</div>
			{description && <div className="yith-plugin-fw-blocks__editor-placeholder__description">{description}</div>}
		</div>
	}

	return <RawHTML className={classes}>{editor_placeholder}</RawHTML>;
}

export default EditorPlaceholder;