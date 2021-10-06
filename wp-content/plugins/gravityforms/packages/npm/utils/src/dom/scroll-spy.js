/**
 * @module
 * @description A vanilla js scrollspy
 */

import debounce from '../events/debounce';
import assign from '../data/object-assign';
import isFunction from '../data/is-function';
import trigger from '../events/trigger';
import convertElements from './convert-elements';

export default function( options ) {
	const defaults = {
		min: 0,
		max: 0,
		debounce: 50,
		elements: null,
		mode: 'vertical',
		buffer: 0,
		container: window,
		onEnter: options.onEnter ? options.onEnter : [],
		onLeave: options.onLeave ? options.onLeave : [],
		onTick: options.onTick ? options.onTick : [],
	};

	const opts = assign( defaults, options );

	if ( ! opts.elements ) {
		return;
	}

	const elements = convertElements( opts.elements );

	const o = opts;
	const mode = o.mode;
	const buffer = o.buffer;
	let enters = 0;
	let leaves = enters;
	let inside = false;

	elements.forEach( ( element ) => {
		o.container.addEventListener(
			'scroll',
			debounce(
				() => {
					const position = {
						top: element.scrollTop,
						left: element.scrollLeft,
					};

					const xy =
						mode === 'vertical'
							? position.top + buffer
							: position.left + buffer;
					let max = o.max;
					let min = o.min;

					/* fix max */
					if ( isFunction( o.max ) ) {
						max = o.max();
					}

					/* fix max */
					if ( isFunction( o.min ) ) {
						min = o.min();
					}

					if ( parseInt( max, 10 ) === 0 ) {
						max =
							mode === 'vertical'
								? o.container.offsetHeight
								: o.container.offsetWidth + element.offsetWidth;
					}

					/* if we have reached the minimum bound but are below the max ... */
					if ( xy >= min && xy <= max ) {
						/* trigger enter event */
						if ( ! inside ) {
							inside = true;
							enters += 1;

							/* fire enter event */
							trigger( {
								el: element,
								event: 'scrollEnter',
								native: false,
								data: {
									position,
								},
							} );
							if ( isFunction( o.onEnter ) ) {
								o.onEnter( element, position );
							}
						}

						/* trigger tick event */
						trigger( {
							el: element,
							event: 'scrollTick',
							native: false,
							data: {
								position,
								inside,
								enters,
								leaves,
							},
						} );
						if ( isFunction( o.onTick ) ) {
							o.onTick(
								element,
								position,
								inside,
								enters,
								leaves
							);
						}
					} else if ( inside ) {
						inside = false;
						leaves += 1;

						trigger( {
							el: element,
							event: 'scrollLeave',
							native: false,
							data: {
								position,
								leaves,
							},
						} );

						if ( isFunction( o.onLeave ) ) {
							o.onLeave( element, position );
						}
					}
				},
				o.debounce,
				false
			)
		);
	} );
};
