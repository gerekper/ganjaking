/* globals bkBlocks */

import PropTypes  from 'prop-types';
import classnames from 'classnames';

import { addQueryArgs }                from '@wordpress/url';
import { useEffect, useRef, useState } from '@wordpress/element';
import { Spinner }                     from '@wordpress/components';

import './style.scss';

function FrameBlock( { className, title, attributes, context, block } ) {
	const iFrameRef               = useRef();
	const [isLoaded, setIsLoaded] = useState( false );

	const iFrameSRC = addQueryArgs( yithGutenberg.siteURL, {
		'yith-plugin-fw-block-preview'      : 1,
		'yith-plugin-fw-block-preview-nonce': yithGutenberg.previewNonce,
		attributes,
		context,
		block
	} );

	const classes = classnames(
		'yith-plugin-fw-blocks__edit-preview-iframe',
		!isLoaded && 'yith-plugin-fw-blocks__edit-preview-iframe--is-loading',
		className
	);

	const handleLoad = ( event ) => {
		// To hide the scrollbars of the preview frame for some edge cases,
		// such as negative margins in the Gallery Legacy Widget.
		event.target.contentDocument.body.style.overflow = 'hidden';

		setIsLoaded( true );
	};

	useEffect(
		() => {
			setIsLoaded( false );
		},
		[iFrameSRC]
	);

	useEffect(
		() => {
			if ( !iFrameRef || !iFrameRef.current || !isLoaded ) {
				return;
			}
			const iFrame = iFrameRef.current;

			const refresh = () => {
				const iFrame = iFrameRef.current;

				const height        = Math.max(
					iFrame.contentDocument.documentElement.offsetHeight,
					iFrame.contentDocument.body.offsetHeight
				);
				// Set height to show all the content.
				iFrame.style.height = `${height}px`;
			}

			const { IntersectionObserver, ResizeObserver } = iFrame.ownerDocument.defaultView;

			// Observe for intersections that might cause a change in the height of
			// the iframe, e.g. a Widget Area becoming expanded.
			const intersectionObserver = new IntersectionObserver(
				( [entry] ) => {
					if ( entry.isIntersecting ) {
						refresh();
					}
				},
				{
					threshold: 1
				}
			);
			intersectionObserver.observe( iFrame );

			// Observe for resizing that might cause a change in the height of
			// the iframe, e.g. when opening the "List View" of the elements or the "Settings" sidebar.
			const resizeObserver = !!ResizeObserver ? new ResizeObserver( () => refresh() ) : false;
			!!resizeObserver && resizeObserver.observe( iFrame );

			iFrame.addEventListener( 'load', refresh );

			return () => {
				intersectionObserver.disconnect();
				!!resizeObserver && resizeObserver.disconnect();
				iFrame.removeEventListener( 'load', refresh );
			}
		},
		[isLoaded]
	);

	return <>
		{!isLoaded && <div className="yith-plugin-fw-blocks__edit-preview-iframe__loading-placeholder"><Spinner style={{ width: 28, height: 28 }}/></div>}
		<iframe
			ref={iFrameRef}
			className={classes}
			title={title}
			src={iFrameSRC}
			onLoad={handleLoad}
			height={100}
		/>
	</>;
}

FrameBlock.propTypes = {
	className : PropTypes.string,
	title     : PropTypes.string.isRequired,
	block     : PropTypes.string.isRequired,
	attributes: PropTypes.object,
	context   : PropTypes.object
}

FrameBlock.defaultProps = {
	className : '',
	attributes: {},
	context   : {}
};

export default FrameBlock;