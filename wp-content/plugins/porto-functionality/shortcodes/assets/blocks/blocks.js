/**
 * Porto Gutenberg blocks
 *
 * 1. Porto Recent Posts
 * 2. Porto Carousel
 * 3. Porto Blog
 * 4. Porto Google Map
 * 5. Porto Ultimate Heading
 * 6. Porto Info Box
 * 7. Porto Stat Counter
 * 8. Porto Icons
 * 9. Porto Single Icon
 * 10. Porto Interactive banner
 * 11. Porto Interactive banner layer
 * 12. Porto Woocommerce Products
 * 13. Porto Heading
 * 14. Porto Button
 * 15. Porto Section
 * 16. Porto Woocommerce Product Categories
 * 17. Porto Masonry Container
 * 18. Porto Products widget
 * 19. Porto Sidebar Menu
 * 20. Porto Hot Spot
 * 21. Porto Portfolios
 * 22. Porto Recent Portfolios (Porto Portfolios Carousel)
 * 23. Porto Members
 * 24. Porto Recent Members (Porto Members Carousel)
 * 25. Porto Svg Float
 */

import PortoImageChoose from './controls/image-choose';
import PortoTypographyControl from './controls/typography';
import PortoAjaxSelect2Control from './controls/ajaxselect2';

window.portoImageControl = PortoImageChoose;
window.portoTypographyControl = PortoTypographyControl;
window.portoAjaxSelect2Control = PortoAjaxSelect2Control;

const portoDestroyCarousel = function ( $slider ) {
	$slider.find( '.owl-stage' ).css( { 'transform': '', 'width': '', 'height': '', 'max-height': '' } ).off( '.owl.core' )
	jQuery( document ).off( '.owl.core' );
	$slider.off( '.owl.core' );
	$slider.children( '.owl-dots, .owl-nav' ).remove();
	$slider.removeClass( 'owl-drag owl-grab' );
	$slider.removeData( 'owl.carousel' );
};

if ('header' === porto_block_vars.builder_type || 'footer' === porto_block_vars.builder_type) {

	try {
		var targetNode = document.getElementById('editor'),
			config = { attributes: false, childList: true, subtree: true },
			callback = function(mutationsList, observer) {
			for (var mutation of mutationsList) {
				if (mutation.type == 'childList') {
					var $obj = jQuery('.editor-styles-wrapper');
					$obj.attr('id', porto_block_vars.builder_type);
					if ('header' === porto_block_vars.builder_type) {
						$obj.addClass('gutenberg-hb');
					}

					observer.disconnect();
					break;
				}
			}
		};
		var observer = new MutationObserver(callback);
		observer.observe(targetNode, config);
	} catch (e) {
	}
}

/**
 * 1. Porto Recent Posts
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		withSelect = wpData.withSelect,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		ServerSideRender = wp.serverSideRender,
		QueryControls = wpComponents.QueryControls,
		TextControl = wpComponents.TextControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		Spinner = wpComponents.Spinner,
		pickBy = lodash.pickBy,
		isUndefined = lodash.isUndefined,
		useEffect = wpElement.useEffect;

	registerBlockType( 'porto/porto-recent-posts', {
		title: 'Porto Recent Posts',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			view: {
				type: 'string',
			},
			author: {
				type: 'string',
			},
			btn_style: {
				type: 'string',
			},
			btn_size: {
				type: 'string',
			},
			btn_color: {
				type: 'string',
			},
			image_size: {
				type: 'string',
			},
			number: {
				type: 'int',
				default: 8,
			},
			cats: {
				type: 'string',
			},
			show_image: {
				type: 'boolean',
				default: true,
			},
			show_metas: {
				type: 'boolean',
				default: true,
			},
			excerpt_length: {
				type: 'int',
				default: 20,
			},
			items_desktop: {
				type: 'int',
				default: 4,
			},
			items_tablets: {
				type: 'int',
				default: 3,
			},
			items_mobile: {
				type: 'int',
				default: 2,
			},
			items_row: {
				type: 'int',
				default: 1,
			},
			slider_config: {
				type: 'boolean',
				default: false,
			},
			show_nav: {
				type: 'boolean',
				default: false,
			},
			show_nav_hover: {
				type: 'boolean',
				default: false,
			},
			nav_pos: {
				type: 'string',
			},
			nav_type: {
				type: 'string',
			},
			show_dots: {
				type: 'boolean',
				default: false,
			},
			dots_pos: {
				type: 'string',
			},
			animation_type: {
				type: 'string',
			},
			animation_duration: {
				type: 'string',
				default: '1000',
			},
			animation_delay: {
				type: 'string',
				default: '0',
			}
		},
		edit: withSelect( function ( select, props ) {
			var _select = select( 'core' ),
				getEntityRecords = _select.getEntityRecords;

			var attrs = props.attributes,
				category = attrs.cats,
				numberOfPosts = attrs.number;

			var recentPostsQuery = pickBy( {
				categories: category,
				per_page: numberOfPosts,
			}, function ( value ) {
				return !isUndefined( value );
			} );

			var categoriesListQuery = {
				per_page: 99
			};

			return {
				recentPosts: getEntityRecords( 'postType', 'post', recentPostsQuery ),
				categoriesList: getEntityRecords( 'taxonomy', 'category', categoriesListQuery ),
			};
		} )( function ( props ) {
			useEffect(
				() => {
					const clientId = props.clientId,
						$slider = jQuery( '#block-' + clientId + ' .owl-carousel' );

					$slider.find( '.owl-stage' ).css( { 'transform': '', 'width': '', 'height': '', 'max-height': '' } ).off( '.owl.core' )
					jQuery( document ).off( '.owl.core' );
					$slider.off( '.owl.core' );
					$slider.children( '.owl-dots, .owl-nav' ).remove();
					$slider.removeClass( 'owl-drag owl-grab' );
					$slider.removeData( 'owl.carousel' );

					$slider.owlCarousel( {
						items: attrs.items_desktop,
						navText: [ "", "" ],
					} );
				},
				[ props.categoriesList, props.attributes.number, props.attributes.items_desktop, props.attributes.cats, props.attributes.view ],
			);

			var attrs = props.attributes,
				author = attrs.author,
				view = attrs.view,
				show_image = attrs.show_image,
				show_metas = attrs.show_metas,
				clientId = props.clientId,
				widgetTitle = attrs.title;
			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __( 'Title', 'porto-functionality' ),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'View', 'porto-functionality' ),
					value: attrs.view,
					options: [ { label: __( 'Standard', 'porto-functionality' ), value: '' }, { label: __( 'Read More Link', 'porto-functionality' ), value: 'style-1' }, { label: __( 'Post Meta', 'porto-functionality' ), value: 'style-2' }, { label: __( 'Read More Button', 'porto-functionality' ), value: 'style-3' }, { label: __( 'Side Image', 'porto-functionality' ), value: 'style-4' }, { label: __( 'Post Cats', 'porto-functionality' ), value: 'style-5' } ],
					onChange: ( value ) => { props.setAttributes( { view: value } ); },
				} ),
				( view == 'style-1' || view == 'style-3' ) && el( SelectControl, {
					label: __( 'Author Name', 'porto-functionality' ),
					value: attrs.author,
					options: [ { label: __( 'Standard', 'porto-functionality' ), value: '' }, { label: __( 'Show', 'porto-functionality' ), value: 'show' }, { label: __( 'Hide', 'porto-functionality' ), value: 'hide' } ],
					onChange: ( value ) => { props.setAttributes( { author: value } ); },
				} ),
				view == 'style-3' && el( SelectControl, {
					label: __( 'Button Style', 'porto-functionality' ),
					value: attrs.btn_style,
					options: [ { label: __( 'Standard', 'porto-functionality' ), value: '' }, { label: __( 'Normal', 'porto-functionality' ), value: 'btn-normal' }, { label: __( 'Borders', 'porto-functionality' ), value: 'btn-borders' } ],
					onChange: ( value ) => { props.setAttributes( { btn_style: value } ); },
				} ),
				view == 'style-3' && el( SelectControl, {
					label: __( 'Button Size', 'porto-functionality' ),
					value: attrs.btn_size,
					options: [ { label: __( 'Standard', 'porto-functionality' ), value: '' }, { label: __( 'Normal', 'porto-functionality' ), value: 'btn-normal' }, { label: __( 'Small', 'porto-functionality' ), value: 'btn-sm' }, { label: __( 'Extra Small', 'porto-functionality' ), value: 'btn-xs' } ],
					onChange: ( value ) => { props.setAttributes( { btn_size: value } ); },
				} ),
				view == 'style-3' && el( SelectControl, {
					label: __( 'Button Color', 'porto-functionality' ),
					value: attrs.btn_color,
					options: [ { label: __( 'Standard', 'porto-functionality' ), value: '' }, { label: __( 'Default', 'porto-functionality' ), value: 'btn-default' }, { label: __( 'Primary', 'porto-functionality' ), value: 'btn-primary' }, { label: __( 'Secondary', 'porto-functionality' ), value: 'btn-secondary' }, { label: __( 'Tertiary', 'porto-functionality' ), value: 'btn-tertiary' }, { label: __( 'Quaternary', 'porto-functionality' ), value: 'btn-quaternary' }, { label: __( 'Dark', 'porto-functionality' ), value: 'btn-dark' }, { label: __( 'Light', 'porto-functionality' ), value: 'btn-light' } ],
					onChange: ( value ) => { props.setAttributes( { btn_color: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Image Size', 'porto-functionality' ),
					value: attrs.image_size,
					options: porto_block_vars.image_sizes,
					onChange: ( value ) => { props.setAttributes( { image_size: value } ); },
				} ),
				el( QueryControls, {
					numberOfItems: attrs.number,
					categoriesList: props.categoriesList || [],
					selectedCategoryId: attrs.cats,
					onCategoryChange: function onCategoryChange( value ) {
						return props.setAttributes( { cats: value !== '' ? value : undefined } );
					},
					onNumberOfItemsChange: function onNumberOfItemsChange( value ) {
						return props.setAttributes( { number: value } );
					}
				} ),
				el( ToggleControl, {
					label: __( 'Show Image', 'porto-functionality' ),
					checked: attrs.show_image,
					onChange: ( value ) => { props.setAttributes( { show_image: value } ); },
				} ),
				el( ToggleControl, {
					label: __( 'Show Post Metas', 'porto-functionality' ),
					checked: attrs.show_metas,
					onChange: ( value ) => { props.setAttributes( { show_metas: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Excerpt Length', 'porto-functionality' ),
					value: attrs.excerpt_length,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { excerpt_length: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Items to show on Desktop', 'porto-functionality' ),
					value: attrs.items_desktop,
					min: 1,
					max: 8,
					onChange: ( value ) => { props.setAttributes( { items_desktop: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Items to show on Tablets', 'porto-functionality' ),
					value: attrs.items_tablets,
					min: 1,
					max: 5,
					onChange: ( value ) => { props.setAttributes( { items_tablets: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Items to show on Mobile', 'porto-functionality' ),
					value: attrs.items_mobile,
					min: 1,
					max: 3,
					onChange: ( value ) => { props.setAttributes( { items_mobile: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Items Row', 'porto-functionality' ),
					value: attrs.items_row,
					min: 1,
					max: 3,
					onChange: ( value ) => { props.setAttributes( { items_row: value } ); },
				} ),
				el( ToggleControl, {
					label: __( 'Change Slider Config', 'porto-functionality' ),
					checked: attrs.slider_config,
					onChange: ( value ) => { props.setAttributes( { slider_config: value } ); },
				} ),
				attrs.slider_config && el( ToggleControl, {
					label: __( 'Show Nav', 'porto-functionality' ),
					checked: attrs.show_nav,
					onChange: ( value ) => { props.setAttributes( { show_nav: value } ); },
				} ),
				attrs.slider_config && attrs.show_nav && el( SelectControl, {
					label: __( 'Nav Position', 'porto-functionality' ),
					value: attrs.nav_pos,
					options: [ { label: __( 'Middle', 'porto-functionality' ), value: '' }, { label: __( 'Top', 'porto-functionality' ), value: 'show-nav-title' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'nav-bottom' } ],
					onChange: ( value ) => { props.setAttributes( { nav_pos: value } ); },
				} ),
				attrs.slider_config && attrs.show_nav && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos ) && el( SelectControl, {
					label: __( 'Nav Type', 'porto-functionality' ),
					value: attrs.nav_type,
					options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Rounded', 'porto-functionality' ), value: 'rounded-nav' }, { label: __( 'Big & Full Width', 'porto-functionality' ), value: 'big-nav' } ],
					onChange: ( value ) => { props.setAttributes( { nav_type: value } ); },
				} ),
				attrs.slider_config && attrs.show_nav && el( ToggleControl, {
					label: __( 'Show Nav on Hover', 'porto-functionality' ),
					checked: attrs.show_nav_hover,
					onChange: ( value ) => { props.setAttributes( { show_nav_hover: value } ); },
				} ),
				attrs.slider_config && el( ToggleControl, {
					label: __( 'Show Dots', 'porto-functionality' ),
					checked: attrs.show_dots,
					onChange: ( value ) => { props.setAttributes( { show_dots: value } ); },
				} ),
				attrs.slider_config && attrs.show_dots && el( SelectControl, {
					label: __( 'Dots Position', 'porto-functionality' ),
					value: attrs.dots_pos,
					options: [ { label: __( 'Outside', 'porto-functionality' ), value: '' }, { label: __( 'Besides Title', 'porto-functionality' ), value: 'show-dots-title' }, { label: __( 'Top right', 'porto-functionality' ), value: 'show-dots-title-right' } ],
					onChange: ( value ) => { props.setAttributes( { dots_pos: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Animation Type', 'porto-functionality' ),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
					help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
				} ),
				el(
					'p',
					{ style: { marginTop: -20 } },
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __( 'Animation Delay (ms)', 'porto-functionality' ),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Animation Duration (ms)', 'porto-functionality' ),
					value: attrs.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} ),

			);

			var hasPosts = Array.isArray( props.recentPosts ) && props.recentPosts.length;

			if ( !hasPosts ) {
				return [
					inspectorControls,
					el(
						Placeholder,
						{
							label: __( 'Porto Recent Posts Block', 'porto-functionality' )
						},
						!Array.isArray( props.recentPosts ) ? el( Spinner, null ) : __( 'No posts found!', 'porto-functionality' )
					)
				];
			}

			return [
				inspectorControls,

				el(
					'div',
					{ className: 'porto-recent-posts' + ( attrs.className ? ' ' + attrs.className : '' ) },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'post-carousel porto-carousel owl-carousel owl-loaded' },
						el(
							'div',
							{ className: 'owl-stage-outer' },
							el(
								'div',
								{ className: 'owl-stage' },
								props.recentPosts.map( function ( post, index ) {
									var featuredImageSrc = post.featured_image_src[ 'list' ][ 0 ];
									return el(
										'div',
										{ className: 'owl-item' },
										el(
											'div',
											{ className: 'post-item' },
											show_image && featuredImageSrc && el(
												'span',
												{ className: 'post-image thumb-info thumb-info-hide-wrapper-bg mb-3' },
												el(
													'span',
													{ className: 'thumb-info-wrapper' },
													el( 'img', { src: featuredImageSrc, alt: __( 'Post Image', 'porto-functionality' ) } )
												)
											),
											show_metas && el(
												'span',
												{ className: 'meta-date' },
												el(
													'i',
													{ className: 'far fa-calendar-alt' },
												),
												' ' + moment( post.date_gmt ).local().format( 'DD MMMM, Y' )
											),
											el(
												'h3',
												{},
												post.title.rendered
											),
											el(
												'div',
												{ className: 'post-excerpt', dangerouslySetInnerHTML: { __html: post.excerpt.rendered } }
											),
											el(
												'span',
												{ className: 'read-more' },
												el(
													'span',
													{},
													__( 'Read More', 'porto-functionality' ) + ' '
												),
												el(
													'i',
													{ className: 'fas fa-angle-right' },
												)
											)
										)
									)
								} )
							)
						)
					)
				),
			];
		} ),
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 2. Porto Carousel
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		QueryControls = wpComponents.QueryControls,
		TextControl = wpComponents.TextControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		Spinner = wpComponents.Spinner,
		pickBy = lodash.pickBy,
		isUndefined = lodash.isUndefined,
		useEffect = wpElement.useEffect;

	const PortoCarousel = function ( props ) {
		useEffect(
			() => {
				let attrs = props.attributes;
				const clientId = props.clientId;
				const $slider = jQuery( '#block-' + clientId ).find( '.block-editor-block-list__layout' ).eq( 0 );
				if ( !$slider.length ) {
					return;
				}
				if ( typeof $slider.data( 'owl.carousel' ) != 'undefined' ) {
					$slider.trigger( 'destroy.owl.carousel' );
				}
				let lg = attrs.items_lg ? attrs.items_lg : attrs.items,
					md = attrs.items_md ? attrs.items_md : attrs.items,
					sm = attrs.items_sm ? attrs.items_sm : attrs.items,
					xs = attrs.items_xs ? attrs.items_xs : attrs.items,
					items = attrs.items ? attrs.items : ( lg ? lg : 1 ),
					count = $slider.find( '> *' ).length;
				let responsive = {};
				responsive[ 1200 ] = { items: items, loop: ( attrs.loop && count > items ) ? true : false, mergeFit: attrs.mergeFit };
				if ( lg ) responsive[ 992 ] = { items: lg, loop: ( attrs.loop && count > lg ) ? true : false, mergeFit: attrs.mergeFit_lg };
				if ( md ) responsive[ 768 ] = { items: md, loop: ( attrs.loop && count > md ) ? true : false, mergeFit: attrs.mergeFit_md };
				if ( sm ) responsive[ 481 ] = { items: sm, loop: ( attrs.loop && count > sm ) ? true : false, mergeFit: attrs.mergeFit_sm };
				if ( xs ) responsive[ 0 ] = { items: xs, loop: ( attrs.loop && count > xs ) ? true : false, mergeFit: attrs.mergeFit_xs };

				let classes = 'porto-carousel owl-carousel';
				if ( attrs.stage_padding ) {
					classes += ' stage-margin';
				}
				if ( attrs.show_nav ) {
					if ( attrs.nav_pos ) classes += ' ' + attrs.nav_pos;
					if ( attrs.nav_type ) classes += ' ' + attrs.nav_type;
					if ( attrs.show_nav_hover ) classes += ' show-nav-hover';
				}
				classes += ' has-ccols';
				classes += ' ccols-' + parseInt( attrs.items, 10 );
				if ( attrs.className ) {
					classes += ' ' + attrs.className;
				}

				if ( attrs.show_dots && attrs.dots_pos ) {
					classes += ' ' + attrs.dots_pos + ' ' + attrs.dots_align;
				}

				if ( typeof prevProps != 'undefined' && prevProps.attributes ) {
					$slider.removeClass( 'ccols-' + prevProps.attributes.items );
				}
				const slider_obj = $slider.get( 0 );
				for ( var j = 0; j < slider_obj.classList.length; j++ ) {
					if ( 0 === slider_obj.classList.item( j ).indexOf( 'ccols-' ) ) {
						slider_obj.classList.remove( slider_obj.classList.item( j ) );
					}
				}
				$slider.addClass( classes );
				/*$slider.owlCarousel({
					stagePadding: attrs.stagePadding ? Number( attrs.stagePadding ) : 0,
					margin: Number( attrs.margin ),
					autoplay: attrs.autoplay,
					autoplayTimeout: attrs.autoplayTimeout,
					autoplayHoverPause: attrs.autoplayHoverPause,
					items: Number( attrs.items ),
					loop: (attrs.loop && count > items) ? true : false,
					responsive: responsive,
					onInitialized: function() {
						jQuery(this).find('.owl-stage-outer').css({
							'margin-left': Number( attrs.stagePadding ),
							'margin-right': Number( attrs.stagePadding )
						})
					},
					touchDrag: (count == 1) ? false : true,
					mouseDrag: (count == 1) ? false : true,
					nav: attrs.show_nav,
					dots: attrs.show_dots,
					animateIn: attrs.animate_in,
					animateOut: attrs.animate_out,
					center: attrs.center,
					video: attrs.video,
					lazyload: attrs.lazyload
				});*/
			}
		);

		var clientId = props.clientId,
			attrs = props.attributes;

		var inspectorControls = el( InspectorControls, {},
			el( RangeControl, {
				label: __( 'Stage Padding', 'porto-functionality' ),
				value: attrs.stage_padding,
				min: 0,
				max: 100,
				onChange: ( value ) => { props.setAttributes( { stage_padding: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Item Margin', 'porto-functionality' ),
				value: attrs.margin,
				min: 0,
				max: 40,
				onChange: ( value ) => { props.setAttributes( { margin: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Auto Play', 'porto-functionality' ),
				checked: attrs.autoplay,
				onChange: ( value ) => { props.setAttributes( { autoplay: value } ); },
			} ),
			attrs.autoplay && el( TextControl, {
				label: __( 'Auto Play Timeout', 'porto-functionality' ),
				value: attrs.autoplay_timeout,
				onChange: ( value ) => { props.setAttributes( { autoplay_timeout: value } ); },
			} ),
			attrs.autoplay && el( ToggleControl, {
				label: __( 'Pause on Mouse Hover', 'porto-functionality' ),
				checked: attrs.autoplay_hover_pause,
				onChange: ( value ) => { props.setAttributes( { autoplay_hover_pause: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Items', 'porto-functionality' ),
				value: attrs.items,
				min: 1,
				max: 10,
				onChange: ( value ) => { props.setAttributes( { items: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Items on Desktop', 'porto-functionality' ),
				value: attrs.items_lg,
				min: 1,
				max: 10,
				onChange: ( value ) => { props.setAttributes( { items_lg: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Items on Tablet', 'porto-functionality' ),
				value: attrs.items_md,
				min: 1,
				max: 8,
				onChange: ( value ) => { props.setAttributes( { items_md: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Items on Mobile', 'porto-functionality' ),
				value: attrs.items_sm,
				min: 1,
				max: 5,
				onChange: ( value ) => { props.setAttributes( { items_sm: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Items on Mini', 'porto-functionality' ),
				value: attrs.items_xs,
				min: 1,
				max: 3,
				onChange: ( value ) => { props.setAttributes( { items_xs: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Show Nav', 'porto-functionality' ),
				checked: attrs.show_nav,
				onChange: ( value ) => { props.setAttributes( { show_nav: value } ); },
			} ),
			attrs.show_nav && el( SelectControl, {
				label: __( 'Nav Position', 'porto-functionality' ),
				value: attrs.nav_pos,
				options: [ { label: __( 'Middle', 'porto-functionality' ), value: '' }, { label: __( 'Middle of Images', 'porto-functionality' ), value: 'nav-center-images-only' }, { label: __( 'Top', 'porto-functionality' ), value: 'show-nav-title' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'nav-bottom' } ],
				onChange: ( value ) => { props.setAttributes( { nav_pos: value } ); },
			} ),
			attrs.show_nav && '' == attrs.nav_pos && el( SelectControl, {
				label: __( 'Nav Inside?', 'porto-functionality' ),
				value: attrs.nav_pos2,
				options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Inside', 'porto-functionality' ), value: 'nav-pos-inside' }, { label: __( 'Outside', 'porto-functionality' ), value: 'nav-pos-outside' } ],
				onChange: ( value ) => { props.setAttributes( { nav_pos2: value } ); },
			} ),
			attrs.show_nav && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
				label: __( 'Nav Type', 'porto-functionality' ),
				value: attrs.nav_type,
				options: porto_block_vars.carousel_nav_types,
				onChange: ( value ) => { props.setAttributes( { nav_type: value } ); },
			} ),
			attrs.show_nav && el( ToggleControl, {
				label: __( 'Show Nav on Hover', 'porto-functionality' ),
				checked: attrs.show_nav_hover,
				onChange: ( value ) => { props.setAttributes( { show_nav_hover: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Show Dots', 'porto-functionality' ),
				checked: attrs.show_dots,
				onChange: ( value ) => { props.setAttributes( { show_dots: value } ); },
			} ),
			attrs.show_dots && el( SelectControl, {
				label: __( 'Dots Position', 'porto-functionality' ),
				value: attrs.dots_pos,
				options: [ { label: __( 'Outside', 'porto-functionality' ), value: '' }, { label: __( 'Inside', 'porto-functionality' ), value: 'nav-inside' }, { label: __( 'Besides Title', 'porto-functionality' ), value: 'show-dots-title' }, { label: __( 'Top right', 'porto-functionality' ), value: 'show-dots-title-right' } ],
				onChange: ( value ) => { props.setAttributes( { dots_pos: value } ); },
			} ),
			attrs.show_dots && ( 'nav-inside' == attrs.dots_pos ) && el( SelectControl, {
				label: __( 'Dots Align', 'porto-functionality' ),
				value: attrs.dots_align,
				options: [ { label: __( 'Right', 'porto-functionality' ), value: '' }, { label: __( 'Center', 'porto-functionality' ), value: 'nav-inside-center' }, { label: __( 'Left', 'porto-functionality' ), value: 'nav-inside-left' } ],
				onChange: ( value ) => { props.setAttributes( { dots_align: value } ); },
			} ),
			attrs.show_dots && el( SelectControl, {
				label: __( 'Dots Style', 'porto-functionality' ),
				value: attrs.dots_style,
				options: [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Circle inner dot', 'porto-functionality' ), 'value': 'dots-style-1' } ],
				onChange: ( value ) => { props.setAttributes( { dots_style: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Item Animation In', 'porto-functionality' ),
				value: attrs.animate_in,
				onChange: ( value ) => { props.setAttributes( { animate_in: value } ); },
				help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
			} ),
			el(
				'p',
				{ style: { marginTop: -20 } },
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
			el( TextControl, {
				label: __( 'Item Animation Out', 'porto-functionality' ),
				value: attrs.animate_out,
				onChange: ( value ) => { props.setAttributes( { animate_out: value } ); },
			} ),
			el(
				'p',
				{},
				__( 'Please check this url to see animation types.', 'porto-functionality' ),
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
			el( ToggleControl, {
				label: __( 'Infinite loop', 'porto-functionality' ),
				checked: attrs.loop,
				onChange: ( value ) => { props.setAttributes( { loop: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Full Screen', 'porto-functionality' ),
				checked: attrs.fullscreen,
				onChange: ( value ) => { props.setAttributes( { fullscreen: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Center Item', 'porto-functionality' ),
				checked: attrs.center,
				onChange: ( value ) => { props.setAttributes( { center: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Fetch Videos', 'porto-functionality' ),
				checked: attrs.video,
				onChange: ( value ) => { props.setAttributes( { video: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Lazy Load', 'porto-functionality' ),
				checked: attrs.lazyload,
				onChange: ( value ) => { props.setAttributes( { lazyload: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Merge Items', 'porto-functionality' ),
				checked: attrs.merge,
				onChange: ( value ) => { props.setAttributes( { merge: value } ); },
			} ),
			attrs.merge && el( ToggleControl, {
				label: __( 'Merge Fit', 'porto-functionality' ),
				checked: attrs.mergeFit,
				onChange: ( value ) => { props.setAttributes( { mergeFit: value } ); },
			} ),
			attrs.merge && el( ToggleControl, {
				label: __( 'Merge Fit on Desktop', 'porto-functionality' ),
				checked: attrs.mergeFit_lg,
				onChange: ( value ) => { props.setAttributes( { mergeFit_lg: value } ); },
			} ),
			attrs.merge && el( ToggleControl, {
				label: __( 'Merge Fit on Tablet', 'porto-functionality' ),
				checked: attrs.mergeFit_md,
				onChange: ( value ) => { props.setAttributes( { mergeFit_md: value } ); },
			} ),
			attrs.merge && el( ToggleControl, {
				label: __( 'Merge Fit on Mobile', 'porto-functionality' ),
				checked: attrs.mergeFit_sm,
				onChange: ( value ) => { props.setAttributes( { mergeFit_sm: value } ); },
			} ),
			attrs.merge && el( ToggleControl, {
				label: __( 'Merge Fit on Mini', 'porto-functionality' ),
				checked: attrs.mergeFit_xs,
				onChange: ( value ) => { props.setAttributes( { mergeFit_xs: value } ); },
			} ),


			el( TextControl, {
				label: __( 'Animation Type', 'porto-functionality' ),
				value: attrs.animation_type,
				onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
			} ),
			el(
				'p',
				{ style: { marginTop: -20 } },
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
			el( TextControl, {
				label: __( 'Animation Delay (ms)', 'porto-functionality' ),
				value: attrs.animation_delay,
				onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Animation Duration (ms)', 'porto-functionality' ),
				value: attrs.animation_duration,
				onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
			} ),
		);

		return [
			inspectorControls,

			el(
				'div',
				{ className: attrs.fullscreen ? 'fullscreen-carousel' : '' },
				el( InnerBlocks ),
			)
		];
	};

	registerBlockType( 'porto/porto-carousel', {
		title: 'Porto Carousel',
		icon: 'porto',
		category: 'porto',
		supports: {
			align: [ 'wide', 'full' ],
		},
		attributes: {
			stage_padding: {
				type: 'int',
				default: 40,
			},
			margin: {
				type: 'int',
				default: 10,
			},
			autoplay: {
				type: 'boolean',
				default: false,
			},
			autoplay_timeout: {
				type: 'int',
				default: 5000,
			},
			autoplay_hover_pause: {
				type: 'boolean',
				default: false,
			},
			items: {
				type: 'int',
				default: 6,
			},
			items_lg: {
				type: 'int',
				default: 4,
			},
			items_md: {
				type: 'int',
				default: 3,
			},
			items_sm: {
				type: 'int',
				default: 2,
			},
			items_xs: {
				type: 'int',
				default: 1,
			},
			show_nav: {
				type: 'boolean',
				default: false,
			},
			show_nav_hover: {
				type: 'boolean',
				default: false,
			},
			nav_pos: {
				type: 'string',
				default: '',
			},
			nav_pos2: {
				type: 'string',
			},
			nav_type: {
				type: 'string',
			},
			show_dots: {
				type: 'boolean',
				default: false,
			},
			dots_pos: {
				type: 'string',
			},
			dots_style: {
				type: 'string',
			},
			dots_align: {
				type: 'string',
			},
			animate_in: {
				type: 'string',
			},
			animate_out: {
				type: 'string',
			},
			loop: {
				type: 'boolean',
				default: false,
			},
			center: {
				type: 'boolean',
				default: false,
			},
			video: {
				type: 'boolean',
				default: false,
			},
			lazyload: {
				type: 'boolean',
				default: false,
			},
			fullscreen: {
				type: 'boolean',
				default: false,
			},
			merge: {
				type: 'boolean',
				default: false,
			},
			mergeFit: {
				type: 'boolean',
				default: true,
			},
			mergeFit_lg: {
				type: 'boolean',
				default: true,
			},
			mergeFit_md: {
				type: 'boolean',
				default: true,
			},
			mergeFit_sm: {
				type: 'boolean',
				default: true,
			},
			mergeFit_xs: {
				type: 'boolean',
				default: true,
			},
			animation_type: {
				type: 'string',
			},
			animation_duration: {
				type: 'int',
				default: 1000,
			},
			animation_delay: {
				type: 'int',
				default: 0,
			}
		},
		edit: PortoCarousel,
		save: function ( props ) {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 3. Porto Blog
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		withSelect = wpData.withSelect,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		QueryControls = wpComponents.QueryControls,
		TextControl = wpComponents.TextControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		Spinner = wpComponents.Spinner,
		pickBy = lodash.pickBy,
		isUndefined = lodash.isUndefined;

	registerBlockType( 'porto/porto-blog', {
		title: 'Porto Blog',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			post_layout: {
				type: 'string',
				default: 'timeline',
			},
			post_style: {
				type: 'string',
				default: 'default',
			},
			columns: {
				type: 'number',
				default: 3,
			},
			cats: {
				type: 'string',
			},
			post_in: {
				type: 'string',
			},
			number: {
				type: 'int',
				default: 8,
			},
			view_more: {
				type: 'boolean',
			},
			view_more_class: {
				type: 'string',
			},
			image_size: {
				type: 'string',
			},
			animation_type: {
				type: 'string',
			},
			animation_duration: {
				type: 'int',
				default: 1000,
			},
			animation_delay: {
				type: 'int',
				default: 0,
			}
		},
		edit: withSelect( function ( select, props ) {
			var _select = select( 'core' ),
				getEntityRecords = _select.getEntityRecords;

			var attrs = props.attributes,
				category = attrs.cats,
				numberOfPosts = attrs.number;

			var recentPostsQuery = pickBy( {
				categories: category,
				per_page: numberOfPosts,
			}, function ( value ) {
				return !isUndefined( value );
			} );

			var categoriesListQuery = {
				per_page: 99
			};

			return {
				recentPosts: getEntityRecords( 'postType', 'post', recentPostsQuery ),
				categoriesList: getEntityRecords( 'taxonomy', 'category', categoriesListQuery ),
			};
		} )( function ( props ) {
			var post_layout = props.attributes.post_layout,
				widgetTitle = props.attributes.title,
				attrs = props.attributes;
			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __( 'Title', 'porto-functionality' ),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Blog Layout', 'porto-functionality' ),
					value: attrs.post_layout,
					options: [ { label: __( 'Full', 'porto-functionality' ), value: 'full' }, { label: __( 'Large', 'porto-functionality' ), value: 'large' }, { label: __( 'Large Alt', 'porto-functionality' ), value: 'large-alt' }, { label: __( 'Medium', 'porto-functionality' ), value: 'medium' }, { label: __( 'Medium Alt', 'porto-functionality' ), value: 'medium-alt' }, { label: __( 'Grid', 'porto-functionality' ), value: 'grid' }, { label: __( 'Timeline', 'porto-functionality' ), value: 'timeline' }, { label: __( 'Slider', 'porto-functionality' ), value: 'slider' } ],
					onChange: ( value ) => { props.setAttributes( { post_layout: value } ); },
				} ),
				( post_layout == 'grid' || post_layout == 'masonry' || post_layout == 'timeline' ) && el( SelectControl, {
					label: __( 'Post Style', 'porto-functionality' ),
					value: attrs.post_style,
					options: [ { label: __( 'Default', 'porto-functionality' ), value: 'default' }, { label: __( 'Default - Date on Image', 'porto-functionality' ), value: 'date' }, { label: __( 'Default - Author Picture', 'porto-functionality' ), value: 'author' }, { label: __( 'Post Carousel Style', 'porto-functionality' ), value: 'related' }, { label: __( 'Hover Info', 'porto-functionality' ), value: 'hover_info' }, { label: __( 'No Margin & Hover Info', 'porto-functionality' ), value: 'no_margin' }, { label: __( 'With Borders', 'porto-functionality' ), value: 'padding' }, { label: __( 'Modern', 'porto-functionality' ), value: 'modern' } ],
					onChange: ( value ) => { props.setAttributes( { post_style: value } ); },
				} ),
				( post_layout == 'grid' || post_layout == 'masonry' ) && el( RangeControl, {
					label: __( 'Columns', 'porto-functionality' ),
					value: attrs.columns,
					min: 1,
					max: 6,
					onChange: ( value ) => { props.setAttributes( { columns: value } ); },
				} ),
				el( QueryControls, {
					categoriesList: props.categoriesList || [],
					selectedCategoryId: attrs.cats,
					numberOfItems: attrs.number,
					onCategoryChange: function onCategoryChange( value ) {
						return props.setAttributes( { cats: value !== '' ? value : undefined } );
					},
					onNumberOfItemsChange: function onNumberOfItemsChange( value ) {
						return props.setAttributes( { number: value } );
					}
				} ),
				el( TextControl, {
					label: __( 'Post IDs', 'porto-functionality' ),
					value: attrs.post_in,
					onChange: ( value ) => { props.setAttributes( { post_in: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Pagination Style', 'porto-functionality' ),
					value: attrs.view_more,
					options: [ { label: __( 'No Pagination', 'porto-functionality' ), value: '' }, { label: __( 'Show Pagination', 'porto-functionality' ), value: 'show' }, { label: __( 'Show Blog Page Link', 'porto-functionality' ), value: 'link' } ],
					onChange: ( value ) => { props.setAttributes( { view_more: value } ); },
				} ),
				( attrs.view_more == 'link' ) && el( TextControl, {
					label: __( 'Extra class name for Archive Link', 'porto-functionality' ),
					value: attrs.view_more_class,
					onChange: ( value ) => { props.setAttributes( { view_more_class: value } ); },
				} ),
				( post_layout === 'grid' || post_layout === 'masonry' || post_layout === 'timeline' || 'slider' === post_layout ) && el( SelectControl, {
					label: __( 'Image Size', 'porto-functionality' ),
					value: attrs.image_size,
					options: porto_block_vars.image_sizes,
					onChange: ( value ) => { props.setAttributes( { image_size: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Animation Type', 'porto-functionality' ),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
					help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
				} ),
				el(
					'p',
					{ style: { marginTop: -20 } },
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __( 'Animation Delay (ms)', 'porto-functionality' ),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Animation Duration (ms)', 'porto-functionality' ),
					value: attrs.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} ),

			);

			var hasPosts = Array.isArray( props.recentPosts ) && props.recentPosts.length;

			if ( !hasPosts ) {
				return [
					inspectorControls,
					el(
						Placeholder,
						{
							label: __( 'Porto Blog Block', 'porto-functionality' )
						},
						!Array.isArray( props.recentPosts ) ? el( Spinner, null ) : __( 'No posts found!', 'porto-functionality' )
					)
				];
			}

			var renderControls,
				postsRender,
				paginationRender = '',
				imageSize = 'medium',
				columns = attrs.columns,
				postMetaStyle = {};

			let postCls = 'post post-' + attrs.post_layout;

			if ( attrs.post_layout == 'full' || attrs.post_layout == 'large' || attrs.post_layout == 'large-alt' ) {
				imageSize = 'landsacpe';
				columns = 1;
			} else if ( attrs.post_layout == 'medium' ) {
				imageSize = 'list';
				columns = 1;
				postMetaStyle.clear = 'both';
			} else if ( attrs.post_layout == 'medium-alt' ) {
				imageSize = 'list';
				columns = 1;
			} else if ( 'timeline' === attrs.post_layout ) {
				imageSize = 'landsacpe';
				postCls += ' timeline-box';
			}

			postsRender = props.recentPosts.map( function ( post, index ) {
				var featuredImageSrc = post.featured_image_src[ imageSize ][ 0 ];
				return el(
					'article',
					{ className: postCls + ( 'timeline' === attrs.post_layout ? ( index % 2 ? ' right' : ' left' ) : '' ) },
					( 'large-alt' === attrs.post_layout ) && el(
						'div',
						{ className: 'post-date' },
						el(
							'span',
							{ className: 'day' },
							moment( post.date_gmt ).local().format( 'DD' )
						),
						el(
							'span',
							{ className: 'month' },
							moment( post.date_gmt ).local().format( 'MMM' )
						),
					),
					featuredImageSrc && el(
						'div',
						{ className: 'post-image' },
						el(
							'div',
							{ className: 'img-thumbnail' },
							el( 'img', { src: featuredImageSrc, alt: __( 'Post Image', 'porto-functionality' ) } )
						),
						( 'medium-alt' === attrs.post_layout ) && el(
							'div',
							{ className: 'blog-post-date background-color-primary text-color-light font-weight-bold' },
							moment( post.date_gmt ).local().format( 'DD' ),
							el(
								'span',
								{ className: 'month-year font-weight-light' },
								moment( post.date_gmt ).local().format( 'MMM-DD' )
							),
						)
					),
					( 'full' === attrs.post_layout || 'large' === attrs.post_layout ) && el(
						'div',
						{ className: 'post-date' },
						el(
							'span',
							{ className: 'day' },
							moment( post.date_gmt ).local().format( 'DD' )
						),
						el(
							'span',
							{ className: 'month' },
							moment( post.date_gmt ).local().format( 'MMM' )
						),
					),
					el(
						'div',
						{ className: 'post-content' },
						el(
							'h4',
							{ className: 'entry-title' },
							post.title.rendered
						),
						el(
							'p',
							{ className: 'post-excerpt', dangerouslySetInnerHTML: { __html: post.excerpt.rendered } },
						)
					),
					'medium-alt' === attrs.post_layout && el(
						'hr',
						{ className: 'solid' }
					),
					el(
						'div',
						{ className: 'post-meta', style: postMetaStyle },
						el(
							'span',
							{ className: 'meta-date' },
							el(
								'i',
								{ className: 'far fa-calendar-alt' },
							),
							' ' + moment( post.date_gmt ).local().format( 'DD MMMM, Y' )
						),
						'medium' === attrs.post_layout && el(
							'a',
							{ className: 'btn read-more d-block float-sm-end', href: post.link },
							el(
								'span',
								null,
								__( 'Read More', 'porto-functionality' )
							)
						)
					),
					'medium' !== attrs.post_layout && 'medium-alt' !== attrs.post_layout && el(
						'a',
						{ className: 'btn read-more' + ( attrs.post_layout === 'full' || attrs.post_layout === 'large' || attrs.post_layout === 'large-alt' ? ' float-sm-end' : '' ), href: post.link },
						el(
							'span',
							null,
							__( 'Read More', 'porto-functionality' )
						)
					),
					'medium-alt' === attrs.post_layout && el(
						'a',
						{ className: 'btn btn-lg d-inline-block btn-borders btn-primary custom-border-radius font-weight-semibold text-uppercase m-t-md' },
						el(
							'span',
							null,
							__( 'Read More', 'porto-functionality' )
						)
					)
				)
			} );

			if ( attrs.post_layout == 'timeline' ) {
				renderControls = el(
					'div',
					{ className: 'porto-blog' + ( attrs.className ? ' ' + attrs.className : '' ) },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'blog-posts posts-' + attrs.post_layout + ( attrs.post_style ? ' blog-posts-' + attrs.post_style : '' ) },
						el(
							'section',
							{ className: 'timeline' },
							el(
								'div',
								{ className: 'timeline-body' },
								postsRender,
							)
						)
					),
					paginationRender
				);
			} else if ( attrs.post_layout == 'grid' || attrs.post_layout == 'masonry' ) {
				renderControls = el(
					'div',
					{ className: 'porto-blog' + ( attrs.className ? ' ' + attrs.className : '' ) },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'blog-posts posts-' + attrs.post_layout + ( attrs.post_style ? ' blog-posts-' + attrs.post_style : '' ) },
						el(
							'div',
							{ className: 'posts-container ' + ' columns-' + columns },
							postsRender
						)
					),
					paginationRender
				);
			} else {
				renderControls = el(
					'div',
					{ className: 'porto-blog' + ( attrs.className ? ' ' + attrs.className : '' ) },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'blog-posts posts-' + attrs.post_layout + ( columns > 1 ? ' posts-container columns-' + columns : '' ) },
						postsRender
					),
					paginationRender
				);
			}

			return [
				inspectorControls,

				renderControls,
			];
		} ),
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 4. Porto Google Map
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		MediaUpload = wpBlockEditor.MediaUpload,
		el = wpElement.createElement,
		Component = wpElement.Component,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		IconButton = wpComponents.IconButton;

	let mapWillUpdate = null;

	class PortoMap extends Component {
		constructor() {
			super( ...arguments );
			this.state = {
				currentMap: null,
				currentMarker: null,
				currentInfo: null,
			};
			this.initMap = this.initMap.bind( this );
		}

		componentDidMount() {
			this.initMap();
		}

		componentDidUpdate( prevProps, prevState ) {
			var prevLat = prevProps.attributes.lat,
				prevLng = prevProps.attributes.lng,
				attrs = this.props.attributes;
			if ( prevState !== this.state )
				return null;

			if ( prevProps.attributes !== attrs ) {
				clearTimeout( mapWillUpdate );
				mapWillUpdate = setTimeout( this.initMap, 1000 );
			}
		}

		portoURLDecode( str ) {
			return decodeURIComponent( ( str + '' ).replace( /%(?![\da-f]{2})/gi, function () {
				return '%25'
			} ) );
		}

		initMap() {
			if ( typeof google === 'undefined' ) {
				return null;
			}
			var attrs = this.props.attributes,
				mapId = 'map_' + this.props.clientId,
				coordinateId = new google.maps.LatLng( attrs.lat, attrs.lng ),
				mapOptions = {
					scaleControl: true,
					streetViewControl: ( 'true' === attrs.streetviewcontrol ),
					mapTypeControl: ( 'true' === attrs.maptypecontrol ),
					panControl: ( 'true' === attrs.pancontrol ),
					zoomControl: ( 'true' === attrs.zoomcontrol ),
					scrollwheel: !attrs.scrollwheel,
					draggable: ( 'true' === attrs.dragging ),
					zoomControlOptions: {
						position: google.maps.ControlPosition[ attrs.zoomcontrolposition ]
					}
				},
				styledMap,
				mapObj = this.state.currentMap,
				markerObj = this.state.currentMarker,
				infowindow = this.state.currentInfo;
			if ( !attrs.map_style ) {
				mapOptions.mapTypeId = google.maps.MapTypeId[ attrs.map_type ];
			} else {
				mapOptions.mapTypeControlOptions = {
					mapTypeIds: [ google.maps.MapTypeId[ attrs.map_type ], 'map_style' ]
				};
				var styles = this.portoURLDecode( jQuery.base64.decode( attrs.map_style ) );
				styledMap = new google.maps.StyledMapType( styles, { name: "Styled Map" } );
			}
			//if (!mapObj) {
			mapObj = new google.maps.Map( document.getElementById( mapId ), mapOptions );
			this.setState( { currentMap: mapObj } );
			//}
			mapObj.setCenter( coordinateId );
			mapObj.setZoom( attrs.zoom );
			if ( attrs.map_style ) {
				mapObj.mapTypes.set( 'map_style', styledMap );
				mapObj.setMapTypeId( 'map_style' );
			}

			function toggleBounce() {
				if ( markerObj.getAnimation() != null ) {
					markerObj.setAnimation( null );
				} else {
					markerObj.setAnimation( google.maps.Animation.BOUNCE );
				}
			}

			if ( attrs.lat && attrs.lng ) {
				if ( !markerObj ) {
					markerObj = new google.maps.Marker( {
						position: new google.maps.LatLng( attrs.lat, attrs.lng ),
						animation: google.maps.Animation.DROP,
						map: mapObj,
						icon: attrs.icon_img_url
					} );
					this.setState( { currentMarker: markerObj } );
				}
				if ( typeof attrs.icon_img_url != 'undefined' ) {
					markerObj.setIcon( attrs.icon_img_url );
				}
				google.maps.event.addListener( markerObj, 'click', toggleBounce );

				if ( attrs.content.trim() !== "" ) {
					if ( !infowindow ) {
						infowindow = new google.maps.InfoWindow();
						this.setState( { currentInfo: infowindow } );
					}
					infowindow.setContent( '<div class="map_info_text" style="color:#000;">' + attrs.content.trim().replace( '/\s+/', ' ' ) + '</div>' );

					if ( attrs.infowindow_open == 'off' ) {
						infowindow.open( mapObj, markerObj );
					}

					google.maps.event.addListener( markerObj, 'click', function () {
						infowindow.open( mapObj, markerObj );
					} );

				}
			}
			google.maps.event.trigger( mapObj, 'resize' );

		}

		render() {
			var props = this.props,
				widgetTitle = props.attributes.title,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __( 'Width (in %)', 'porto-functionality' ),
					value: attrs.width,
					onChange: ( value ) => { props.setAttributes( { width: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Height (in px)', 'porto-functionality' ),
					value: attrs.height,
					onChange: ( value ) => { props.setAttributes( { height: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Map type', 'porto-functionality' ),
					value: attrs.map_type,
					options: [ { label: __( 'Roadmap', 'porto-functionality' ), value: 'ROADMAP' }, { label: __( 'Satellite', 'porto-functionality' ), value: 'SATELLITE' }, { label: __( 'Hybrid', 'porto-functionality' ), value: 'HYBRID' }, { label: __( 'Terrain', 'porto-functionality' ), value: 'TERRAIN' } ],
					onChange: ( value ) => { props.setAttributes( { map_type: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Latitude', 'porto-functionality' ),
					value: attrs.lat,
					onChange: ( value ) => { props.setAttributes( { lat: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Longitude', 'porto-functionality' ),
					value: attrs.lng,
					onChange: ( value ) => { props.setAttributes( { lng: value } ); },
				} ),
				el(
					'p',
					null,
					el(
						'a',
						{ href: 'http://universimmedia.pagesperso-orange.fr/geo/loc.htm', target: '_blank' },
						__( 'Here is a tool', 'porto-functionality' )
					),
					' ' + __( 'where you can find Latitude & Longitude of your location', 'porto-functionality' )
				),
				el( RangeControl, {
					label: __( 'Map Zoom', 'porto-functionality' ),
					value: attrs.zoom,
					min: 1,
					max: 20,
					onChange: ( value ) => { props.setAttributes( { zoom: value } ); },
				} ),
				el( ToggleControl, {
					label: __( 'Disable map zoom on mouse wheel scroll', 'porto-functionality' ),
					checked: attrs.scrollwheel,
					onChange: ( value ) => { props.setAttributes( { scrollwheel: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Info Window Text', 'porto-functionality' ),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Open on Marker Click', 'porto-functionality' ),
					value: attrs.infowindow_open,
					options: [ { label: __( 'Yes', 'porto-functionality' ), value: 'on' }, { label: __( 'No', 'porto-functionality' ), value: 'off' } ],
					onChange: ( value ) => { props.setAttributes( { infowindow_open: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Marker/Point icon', 'porto-functionality' ),
					value: attrs.marker_icon,
					options: [ { label: __( 'Use Google Default', 'porto-functionality' ), value: 'default' }, { label: __( 'Upload Custom', 'porto-functionality' ), value: 'custom' } ],
					onChange: ( value ) => { props.setAttributes( { marker_icon: value } ); },
				} ),
				'custom' == attrs.marker_icon && el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: attrs.icon_img,
					onSelect: function onSelect( image ) {
						return props.setAttributes( { icon_img_url: image.url, icon_img: image.id } );
					},
					render: function render( _ref ) {
						var open = _ref.open;
						return el( IconButton, {
							className: 'components-toolbar__control',
							label: __( 'Change image', 'porto-functionality' ),
							icon: 'edit',
							onClick: open
						} );
					}
				} ),
				'custom' == attrs.marker_icon && el( IconButton, {
					className: 'components-toolbar__control',
					label: __( 'Remove image', 'porto-functionality' ),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes( { icon_img_url: undefined, icon_img: undefined } );
					}
				} ),
				el( SelectControl, {
					label: __( 'Street view control', 'porto-functionality' ),
					value: attrs.streetviewcontrol,
					options: [ { label: __( 'Disable', 'porto-functionality' ), value: 'false' }, { label: __( 'Enable', 'porto-functionality' ), value: 'true' } ],
					onChange: ( value ) => { props.setAttributes( { streetviewcontrol: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Map type control', 'porto-functionality' ),
					value: attrs.maptypecontrol,
					options: [ { label: __( 'Disable', 'porto-functionality' ), value: 'false' }, { label: __( 'Enable', 'porto-functionality' ), value: 'true' } ],
					onChange: ( value ) => { props.setAttributes( { maptypecontrol: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Zoom control', 'porto-functionality' ),
					value: attrs.zoomcontrol,
					options: [ { label: __( 'Disable', 'porto-functionality' ), value: 'false' }, { label: __( 'Enable', 'porto-functionality' ), value: 'true' } ],
					onChange: ( value ) => { props.setAttributes( { zoomcontrol: value } ); },
				} ),
				( 'true' == attrs.zoomcontrol ) && el( SelectControl, {
					label: __( 'Zoom Control Position', 'porto-functionality' ),
					value: attrs.zoomcontrolposition,
					options: [ { label: __( 'Right Bottom', 'porto-functionality' ), value: 'RIGHT_BOTTOM' }, { label: __( 'Right Top', 'porto-functionality' ), value: 'RIGHT_TOP' }, { label: __( 'Right Center', 'porto-functionality' ), value: 'RIGHT_CENTER' }, { label: __( 'Left Top', 'porto-functionality' ), value: 'LEFT_TOP' }, { label: __( 'Left Center', 'porto-functionality' ), value: 'LEFT_CENTER' }, { label: __( 'Left Bottom', 'porto-functionality' ), value: 'LEFT_BOTTOM' } ],
					onChange: ( value ) => { props.setAttributes( { zoomcontrolposition: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Dragging on Mobile', 'porto-functionality' ),
					value: attrs.dragging,
					options: [ { label: __( 'Enable', 'porto-functionality' ), value: 'true' }, { label: __( 'Disable', 'porto-functionality' ), value: 'false' } ],
					onChange: ( value ) => { props.setAttributes( { dragging: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Top margin', 'porto-functionality' ),
					value: attrs.top_margin,
					options: [ { label: __( 'Page (small)', 'porto-functionality' ), value: 'page_margin_top' }, { label: __( 'Section (large)', 'porto-functionality' ), value: 'page_margin_top_section' }, { label: __( 'None', 'porto-functionality' ), value: 'none' } ],
					onChange: ( value ) => { props.setAttributes( { top_margin: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Map Width Override', 'porto-functionality' ),
					value: attrs.map_override,
					options: [ { label: __( 'Default Width', 'porto-functionality' ), value: '0' }, { label: __( 'Apply 1st parent element\'s width' ), value: '1' }, { label: __( 'Apply 2nd parent element\'s width' ), value: '2' }, { label: __( 'Apply 3rd parent element\'s width' ), value: '3' }, { label: __( 'Apply 4th parent element\'s width' ), value: '4' }, { label: __( 'Apply 5th parent element\'s width' ), value: '5' }, { label: __( 'Apply 6th parent element\'s width' ), value: '6' }, { label: __( 'Apply 7th parent element\'s width' ), value: '7' }, { label: __( 'Apply 8th parent element\'s width' ), value: '8' }, { label: __( 'Apply 9th parent element\'s width' ), value: '9' }, { label: __( 'Full Width', 'porto-functionality' ), value: 'full' }, { label: __( 'Maximum Full Width', 'porto-functionality' ), value: 'ex-full' } ],
					onChange: ( value ) => { props.setAttributes( { map_override: value } ); },
				} ),
				el(
					'p',
					{ style: { fontStyle: 'italic' } },
					__( 'By default, the map will be given to the Visual Composer row. However, in some cases depending on your theme\'s CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output.' ),
				),
				el( TextareaControl, {
					label: __( 'Google Styled Map JSON', 'porto-functionality' ),
					value: attrs.map_style,
					onChange: ( value ) => { props.setAttributes( { map_style: value } ); },
				} ),
				el( 'p',
					{ style: { fontStyle: 'italic' } },
					el( 'a', { target: '_blank', href: 'http://googlemaps.github.io/js-samples/styledmaps/wizard/index.html' }, __( 'Click here', 'porto-functionality' ) ),
					' ' + __( 'to get the style JSON code for styling your map.', 'porto-functionality' )
				),

			);

			var mapStyle = {};
			if ( attrs.width ) {
				let unitVal = attrs.width;
				const unit = unitVal.replace( /[0-9.]/g, '' );
				if ( !unit ) {
					unitVal += '%';
				}
				mapStyle.width = unitVal;
			}
			if ( attrs.height ) {
				let unitVal = attrs.height;
				const unit = unitVal.replace( /[0-9.]/g, '' );
				if ( !unit ) {
					unitVal += 'px';
				}
				mapStyle.height = unitVal;
			}
			var renderControls = el(
				'div', { id: 'wrap_' + clientId, className: 'porto-map-wrapper' + ( attrs.className ? ' ' + attrs.className : '' ) },
				el(
					'div', { id: 'map_' + clientId, className: 'porto_google_map', style: mapStyle },
				)
			);

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-google-map', {
		title: 'Porto Google Map',
		icon: 'porto',
		category: 'porto',
		attributes: {
			width: {
				type: 'string',
				default: '100%',
			},
			height: {
				type: 'string',
				default: '300px',
			},
			map_type: {
				type: 'string',
				default: 'ROADMAP',
			},
			lat: {
				type: 'string',
				default: '51.5074',
			},
			lng: {
				type: 'string',
				default: '0.1278',
			},
			zoom: {
				type: 'int',
				default: 14,
			},
			scrollwheel: {
				type: 'boolean',
			},
			streetviewcontrol: {
				type: 'string',
				default: 'false',
			},
			maptypecontrol: {
				type: 'string',
				default: 'false',
			},
			pancontrol: {
				type: 'string',
				default: 'false',
			},
			zoomcontrol: {
				type: 'string',
				default: 'false',
			},
			zoomcontrolposition: {
				type: 'string',
				default: 'RIGHT_BOTTOM',
			},
			dragging: {
				type: 'string',
				default: 'true',
			},
			marker_icon: {
				type: 'string',
				default: 'default',
			},
			icon_img: {
				type: 'int',
			},
			icon_img_url: {
				type: 'string',
			},
			top_margin: {
				type: 'string',
				default: 'page_margin_top',
			},
			map_override: {
				type: 'string',
				default: '0',
			},
			map_style: {
				type: 'string',
			},
			infowindow_open: {
				type: 'string',
				default: 'on',
			},
			content: {
				type: 'string',
			}
		},
		edit: PortoMap,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 5. Porto Ultimate heading
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		RichText = wpBlockEditor.RichText,
		BlockControls = wpBlockEditor.BlockControls,
		BlockAlignmentToolbar = wpBlockEditor.BlockAlignmentToolbar,
		el = wpElement.createElement,
		Component = wpElement.Component,
		PanelBody = wpComponents.PanelBody,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder;

	const PortoUltimateHeading = function ( props ) {
		var widgetTitle = props.attributes.title,
			attrs = props.attributes,
			clientId = props.clientId;

		var inspectorControls = el( InspectorControls, {},
			el( PanelBody, {
				title: __( 'Layout', 'porto-functionality' ),
				initialOpen: true,
			},
				el( TextareaControl, {
					label: __( 'Sub Heading (Optional)', 'porto-functionality' ),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Heading Tag', 'porto-functionality' ),
					value: attrs.heading_tag,
					options: [ { label: __( 'H1', 'PORTO-FUNCTIONALITY' ), value: 'h1' }, { label: __( 'H2', 'PORTO-FUNCTIONALITY' ), value: 'h2' }, { label: __( 'H3', 'PORTO-FUNCTIONALITY' ), value: 'h3' }, { label: __( 'H4', 'PORTO-FUNCTIONALITY' ), value: 'h4' }, { label: __( 'H5', 'PORTO-FUNCTIONALITY' ), value: 'h5' }, { label: __( 'H6', 'PORTO-FUNCTIONALITY' ), value: 'h6' } ],
					onChange: ( value ) => { props.setAttributes( { heading_tag: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Separator', 'porto-functionality' ),
					value: attrs.spacer,
					options: [ { label: __( 'No Separator', 'porto-functionality' ), value: 'no_spacer' }, { label: __( 'Line', 'porto-functionality' ), value: 'line_only' } ],
					onChange: ( value ) => { props.setAttributes( { spacer: value } ); },
				} ),
				el( 'p', { style: { fontStyle: 'italic' } }, __( 'Horizontal line, icon or image to divide sections', 'porto-functionality' ) ),
				attrs.spacer == 'line_only' && el( SelectControl, {
					label: __( 'Separator Position', 'porto-functionality' ),
					value: attrs.spacer_position,
					options: [ { label: __( 'Top', 'porto-functionality' ), value: 'top' }, { label: __( 'Between Heading & Sub-Heading', 'porto-functionality' ), value: 'middle' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'bottom' } ],
					onChange: ( value ) => { props.setAttributes( { spacer_position: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( RangeControl, {
					label: __( 'Line Width (optional)', 'porto-functionality' ),
					value: attrs.line_width,
					min: 0,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { line_width: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( RangeControl, {
					label: __( 'Line Height', 'porto-functionality' ),
					value: attrs.line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { line_height: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( PanelColorSettings, {
					title: __( 'Color Settings', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Line Color', 'porto-functionality' ),
						value: attrs.line_color,
						onChange: function onChange( value ) {
							return props.setAttributes( { line_color: value } );
						}
					} ]
				} )
			),
			el( PanelBody, {
				title: __( 'Style', 'porto-functionality' ),
				initialOpen: false,
			},
				el( PortoTypographyControl, {
					label: __( 'Main Heading Font', 'porto-functionality' ),
					value: { fontFamily: attrs.main_heading_font_family, fontSize: attrs.main_heading_font_size, fontWeight: attrs.main_heading_font_weight, textTransform: attrs.main_heading_text_transform, lineHeight: attrs.main_heading_line_height, letterSpacing: attrs.main_heading_letter_spacing, color: attrs.main_heading_color },
					options: {},
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { main_heading_font_family: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { main_heading_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { main_heading_font_weight: value.fontWeight } );
						}
						if ( typeof value.textTransform != 'undefined' ) {
							props.setAttributes( { main_heading_text_transform: value.textTransform } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { main_heading_line_height: value.lineHeight } );
						}
						if ( typeof value.letterSpacing != 'undefined' ) {
							props.setAttributes( { main_heading_letter_spacing: value.letterSpacing } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { main_heading_color: value.color } );
						} else {
							props.setAttributes( { main_heading_color: '' } );
						}
					},
				} ),
				el( RangeControl, {
					label: __( 'Heading Margin Bottom', 'porto-functionality' ),
					value: attrs.main_heading_margin_bottom,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { main_heading_margin_bottom: value } ); },
				} ),

				el( PortoTypographyControl, {
					label: __( 'Sub Heading Font', 'porto-functionality' ),
					value: { fontFamily: attrs.sub_heading_font_family, fontSize: attrs.sub_heading_font_size, fontWeight: attrs.sub_heading_font_weight, lineHeight: attrs.sub_heading_line_height, letterSpacing: attrs.sub_heading_letter_spacing, color: attrs.sub_heading_color },
					options: { textTransform: false },
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { sub_heading_font_family: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { sub_heading_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { sub_heading_font_weight: value.fontWeight } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { sub_heading_line_height: value.lineHeight } );
						}
						if ( typeof value.letterSpacing != 'undefined' ) {
							props.setAttributes( { sub_heading_letter_spacing: value.letterSpacing } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { sub_heading_color: value.color } );
						} else {
							props.setAttributes( { sub_heading_color: '' } );
						}
					},
				} ),
				el( RangeControl, {
					label: __( 'Sub Heading Margin Bottom', 'porto-functionality' ),
					value: attrs.sub_heading_margin_bottom,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { sub_heading_margin_bottom: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( RangeControl, {
					label: __( 'Separator Margin Bottom', 'porto-functionality' ),
					value: attrs.spacer_margin_bottom,
					min: 0,
					max: 50,
					onChange: ( value ) => { props.setAttributes( { spacer_margin_bottom: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Animation Type', 'porto-functionality' ),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
					help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
				} ),
				el(
					'p',
					{ style: { marginTop: -20 } },
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __( 'Animation Delay (ms)', 'porto-functionality' ),
					value: props.attributes.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Animation Duration (ms)', 'porto-functionality' ),
					value: props.attributes.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} )
			),
		);

		var wrapper_style = {}, line_style_inline = {}, icon_inline = '', main_heading_style_inline = {}, sub_heading_style_inline = {};

		if ( attrs.main_heading_font_family ) {
			main_heading_style_inline.fontFamily = attrs.main_heading_font_family;
		}
		if ( attrs.main_heading_font_weight ) {
			main_heading_style_inline.fontWeight = attrs.main_heading_font_weight;
		}
		if ( attrs.main_heading_color ) {
			main_heading_style_inline.color = attrs.main_heading_color;
		}
		if ( attrs.main_heading_margin_bottom ) {
			main_heading_style_inline.marginBottom = attrs.main_heading_margin_bottom + 'px';
		}
		if ( attrs.main_heading_font_size ) {
			let unit = attrs.main_heading_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.main_heading_font_size += 'px';
			}
			main_heading_style_inline.fontSize = attrs.main_heading_font_size;
		}
		if ( attrs.main_heading_line_height ) {
			let unit = attrs.main_heading_line_height.replace( /[0-9.]/g, '' );
			if ( !unit && attrs.main_heading_line_height > 3 ) {
				attrs.main_heading_line_height += 'px';
			}
			main_heading_style_inline.lineHeight = attrs.main_heading_line_height;
		}
		if ( attrs.main_heading_letter_spacing ) {
			main_heading_style_inline.letterSpacing = attrs.main_heading_letter_spacing;
		}
		if ( attrs.main_heading_text_transform ) {
			main_heading_style_inline.textTransform = attrs.main_heading_text_transform;
		}

		if ( attrs.sub_heading_font_family ) {
			sub_heading_style_inline.fontFamily = attrs.sub_heading_font_family;
		}
		if ( attrs.sub_heading_font_weight ) {
			sub_heading_style_inline.fontWeight = attrs.sub_heading_font_weight;
		}
		if ( attrs.sub_heading_color ) {
			sub_heading_style_inline.color = attrs.sub_heading_color;
		}
		if ( attrs.sub_heading_margin_bottom ) {
			sub_heading_style_inline.marginBottom = attrs.sub_heading_margin_bottom + 'px';
		}
		if ( attrs.sub_heading_font_size ) {
			let unit = attrs.sub_heading_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.sub_heading_font_size += 'px';
			}
			sub_heading_style_inline.fontSize = attrs.sub_heading_font_size;
		}
		if ( attrs.sub_heading_line_height ) {
			let unit = attrs.sub_heading_line_height.replace( /[0-9.]/g, '' );
			if ( !unit && attrs.sub_heading_line_height > 3 ) {
				attrs.sub_heading_line_height += 'px';
			}
			sub_heading_style_inline.lineHeight = attrs.sub_heading_line_height;
		}
		if ( attrs.sub_heading_letter_spacing ) {
			let unit = attrs.sub_heading_letter_spacing.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.sub_heading_letter_spacing += 'px';
			}
			sub_heading_style_inline.letterSpacing = attrs.sub_heading_letter_spacing;
		}

		if ( attrs.spacer && attrs.spacer_margin_bottom ) {
			wrapper_style = { marginBottom: attrs.spacer_margin_bottom + 'px' };
		}
		if ( 'line_only' == attrs.spacer ) {
			line_style_inline.borderStyle = attrs.line_style;
			line_style_inline.borderBottomWidth = attrs.line_height + 'px';
			line_style_inline.borderColor = attrs.line_color;
			line_style_inline.width = attrs.line_width + ( 'auto' == attrs.line_width ? '' : 'px' );
			wrapper_style.height = attrs.line_height + 'px';
			icon_inline = el(
				'span',
				{ className: 'porto-u-headings-line', style: line_style_inline }
			);
		}
		main_heading_style_inline.textAlign = attrs.alignment;

		var spacerRender = '';
		if ( attrs.spacer ) {
			spacerRender = el(
				'div',
				{ className: 'porto-u-heading-spacer ' + attrs.spacer, style: wrapper_style },
				icon_inline
			);
		}
		var renderControls = el(
			'div', { className: 'porto-u-heading' + ( attrs.className ? ' ' + attrs.className : '' ), style: { textAlign: attrs.alignment } },
			'top' == attrs.spacer_position && spacerRender,
			/*attrs.main_heading && el(
				'div',
				{className: 'porto-u-main-heading'},
				el(
					attrs.heading_tag,
					{style: main_heading_style_inline},
					attrs.main_heading
				)
			),*/
			el(
				RichText,
				{
					key: 'editable',
					tagName: attrs.heading_tag,
					className: 'porto-u-main-heading',
					style: main_heading_style_inline,//{ textAlign: attrs.alignment },
					onChange: function ( value ) {
						return props.setAttributes( { main_heading: value } );
					},
					value: attrs.main_heading,
				}
			),
			'middle' == attrs.spacer_position && spacerRender,
			attrs.content && el(
				'div',
				{ className: 'porto-u-sub-heading', style: sub_heading_style_inline, dangerouslySetInnerHTML: { __html: attrs.content.replace( /<script.*?\/script>/g, '' ) } }
			),
			'bottom' == attrs.spacer_position && spacerRender,
		);

		return [
			el( BlockControls, null,
				el( BlockAlignmentToolbar, {
					value: attrs.alignment, onChange: function onChange( value ) {
						return props.setAttributes( { alignment: value } );
					}
				} )
			),
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-ultimate-heading', {
		title: 'Porto Ultimate Heading',
		icon: 'porto',
		category: 'porto',
		attributes: {
			main_heading: {
				type: 'string',
			},
			main_heading_use_theme_fonts: {
				type: 'boolean',
				default: true,
			},
			main_heading_font_family: {
				type: 'string',
			},
			main_heading_font_size: {
				type: 'string',
			},
			main_heading_font_weight: {
				type: 'int',
			},
			main_heading_text_transform: {
				type: 'string',
			},
			main_heading_line_height: {
				type: 'string',
			},
			main_heading_letter_spacing: {
				type: 'string',
			},
			main_heading_color: {
				type: 'string',
			},
			main_heading_margin_bottom: {
				type: 'int',
			},
			content: {
				type: 'string',
			},
			sub_heading_use_theme_fonts: {
				type: 'boolean',
				default: true,
			},
			sub_heading_font_family: {
				type: 'string',
			},
			sub_heading_font_size: {
				type: 'string',
			},
			sub_heading_font_weight: {
				type: 'int',
			},
			sub_heading_line_height: {
				type: 'string',
			},
			sub_heading_letter_spacing: {
				type: 'string',
			},
			sub_heading_color: {
				type: 'string',
			},
			sub_heading_margin_bottom: {
				type: 'int',
			},
			spacer: {
				type: 'string',
				default: 'no_spacer',
			},
			spacer_position: {
				type: 'string',
				default: 'top',
			},
			line_style: {
				type: 'string',
				default: 'solid',
			},
			line_width: {
				type: 'string',
				default: 'auto',
			},
			line_height: {
				type: 'string',
				default: '1',
			},
			line_color: {
				type: 'string',
				default: '#ccc',
			},
			alignment: {
				type: 'string',
				default: 'center',
			},
			spacer_margin_bottom: {
				type: 'int',
			},
			heading_tag: {
				type: 'string',
				default: 'h2',
			},
			animation_type: {
				type: 'string',
			},
			animation_duration: {
				type: 'int',
				default: 1000,
			},
			animation_delay: {
				type: 'int',
				default: 0,
			}
		},
		edit: PortoUltimateHeading,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 6. Porto Info Box
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		MediaUpload = wpBlockEditor.MediaUpload,
		el = wpElement.createElement,
		Component = wpElement.Component,
		PanelBody = wpComponents.PanelBody,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		IconButton = wpComponents.IconButton;

	const PortoInfoBox = function ( props ) {
		var attrs = props.attributes,
			clientId = props.clientId;

		var inspectorControls = el( InspectorControls, {},
			el( PanelBody, {
				title: __( 'Layout', 'porto-functionality' ),
				initialOpen: true,
			},
				el( SelectControl, {
					label: __( 'Box Style', 'porto-functionality' ),
					value: attrs.pos,
					options: [ { label: __( 'Icon at Left with heading', 'porto-functionality' ), value: 'default' }, { label: __( 'Icon at Right with heading', 'porto-functionality' ), value: 'heading-right' }, { label: __( 'Icon at Left', 'porto-functionality' ), value: 'left' }, { label: __( 'Icon at Right', 'porto-functionality' ), value: 'right' }, { label: __( 'Icon at Top', 'porto-functionality' ), value: 'top' } ],
					onChange: ( value ) => { props.setAttributes( { pos: value } ); },
				} ),
				'top' === attrs.pos && el( SelectControl, {
					label: __( 'Horizontal Alignment', 'porto-functionality' ),
					value: attrs.h_align,
					options: [ { label: __( 'Left', 'porto-functionality' ), value: 'left' }, { label: __( 'Center', 'porto-functionality' ), value: 'center' }, { label: __( 'Right', 'porto-functionality' ), value: 'right' } ],
					onChange: ( value ) => { props.setAttributes( { h_align: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Icon to display', 'porto-functionality' ),
					value: attrs.icon_type,
					options: [ { label: __( 'Icon Font', 'porto-functionality' ), value: '' }, { label: __( 'Custom Image Icon', 'porto-functionality' ), value: 'custom' } ],
					onChange: ( value ) => { props.setAttributes( { icon_type: value } ); },
				} ),
				!attrs.icon_type && el( TextControl, {
					label: __( 'Icon Class', 'porto-functionality' ),
					value: attrs.icon,
					onChange: ( value ) => { props.setAttributes( { icon: value } ); },
					help: __( 'Please check this url to see icons which Porto supports. ', 'porto-functionality' ),
				} ),
				!attrs.icon_type && el(
					'p',
					{ style: { marginTop: -14 } },
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/icons/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/icons/'
					),
				),
				'custom' == attrs.icon_type && el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: attrs.icon_img,
					onSelect: function onSelect( image ) {
						return props.setAttributes( { icon_img_url: image.url, icon_img: image.id } );
					},
					render: function render( _ref ) {
						var open = _ref.open;
						return el( IconButton, {
							className: 'components-toolbar__control',
							label: __( 'Change image', 'porto-functionality' ),
							icon: 'edit',
							onClick: open
						} );
					}
				} ),
				'custom' == attrs.icon_type && el( IconButton, {
					className: 'components-toolbar__control',
					label: __( 'Remove image', 'porto-functionality' ),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes( { icon_img_url: undefined, icon_img: undefined } );
					}
				} ),
				'custom' == attrs.icon_type && el( RangeControl, {
					label: __( 'Image Width', 'porto-functionality' ),
					value: attrs.img_width,
					min: 16,
					max: 512,
					onChange: ( value ) => { props.setAttributes( { img_width: value } ); },
				} ),
				'custom' != attrs.icon_type && el( RangeControl, {
					label: __( 'Icon Size', 'porto-functionality' ),
					value: attrs.icon_size,
					min: 12,
					max: 72,
					onChange: ( value ) => { props.setAttributes( { icon_size: value } ); },
				} ),
				'custom' != attrs.icon_type && el( PanelColorSettings, {
					title: __( 'Color Settings', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Color', 'porto-functionality' ),
						value: attrs.icon_color,
						onChange: function onChange( value ) {
							return props.setAttributes( { icon_color: value } );
						}
					} ]
				} ),
				el( SelectControl, {
					label: __( 'Icon Style', 'porto-functionality' ),
					value: attrs.icon_style,
					options: [ { label: __( 'Simple', 'porto-functionality' ), value: 'none' }, { label: __( 'Circle Background', 'porto-functionality' ), value: 'circle' }, { label: __( 'Circle Image', 'porto-functionality' ), value: 'circle_img' }, { label: __( 'Square Background', 'porto-functionality' ), value: 'square' }, { label: __( 'Design your own', 'porto-functionality' ), value: 'advanced' } ],
					onChange: ( value ) => { props.setAttributes( { icon_style: value } ); },
				} ),
				'none' != attrs.icon_style && el( PanelColorSettings, {
					title: __( 'Background Color', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Background Color', 'porto-functionality' ),
						value: attrs.icon_color_bg,
						onChange: function onChange( value ) {
							return props.setAttributes( { icon_color_bg: value } );
						}
					} ]
				} ),
				( 'circle_img' == attrs.icon_style || 'advanced' == attrs.icon_style ) && el( SelectControl, {
					label: __( 'Icon Border Style', 'porto-functionality' ),
					value: attrs.icon_border_style,
					options: [ { label: __( 'None', 'porto-functionality' ), value: '' }, { label: __( 'Solid', 'porto-functionality' ), value: 'solid' }, { label: __( 'Dashed', 'porto-functionality' ), value: 'dashed' }, { label: __( 'Dotted', 'porto-functionality' ), value: 'dotted' }, { label: __( 'Double', 'porto-functionality' ), value: 'double' }, { label: __( 'Inset', 'porto-functionality' ), value: 'inset' }, { label: __( 'Outset', 'porto-functionality' ), value: 'outset' } ],
					onChange: ( value ) => { props.setAttributes( { icon_border_style: value } ); },
				} ),
				attrs.icon_border_style && el( PanelColorSettings, {
					title: __( 'Border Color', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Border Color', 'porto-functionality' ),
						value: attrs.icon_color_border,
						onChange: function onChange( value ) {
							return props.setAttributes( { icon_color_border: value } );
						}
					} ]
				} ),
				attrs.icon_border_style && el( RangeControl, {
					label: __( 'Border Width', 'porto-functionality' ),
					value: attrs.icon_border_size,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { icon_border_size: value } ); },
				} ),
				attrs.icon_border_style && el( RangeControl, {
					label: __( 'Border Radius', 'porto-functionality' ),
					value: attrs.icon_border_radius,
					min: 1,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_radius: value } ); },
				} ),
				( 'circle_img' == attrs.icon_style || 'advanced' == attrs.icon_style ) && el( RangeControl, {
					label: __( 'Background Size', 'porto-functionality' ),
					value: attrs.icon_border_spacing,
					min: 0,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_spacing: value } ); },
				} ),
				( 'circle_img' == attrs.icon_style || 'advanced' == attrs.icon_style ) && el( 'p', { style: { fontStyle: 'italic' } }, __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ) ),
				el( TextControl, {
					label: __( 'Icon Animation Type', 'porto-functionality' ),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
					help: __( 'Please check this url to see animation types. ', 'porto-functionality' ),
				} ),
				el(
					'p',
					{ style: { marginTop: -20 } },
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __( 'Title', 'porto-functionality' ),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Sub title', 'porto-functionality' ),
					value: attrs.subtitle,
					onChange: ( value ) => { props.setAttributes( { subtitle: value } ); },
				} ),
				el( TextareaControl, {
					label: __( 'Description', 'porto-functionality' ),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Apply link to:', 'porto-functionality' ),
					value: attrs.read_more,
					options: [ { label: __( 'No Link', 'porto-functionality' ), value: 'none' }, { label: __( 'Complete Box', 'porto-functionality' ), value: 'box' }, { label: __( 'Box Title', 'porto-functionality' ), value: 'title' }, { label: __( 'Display Read More', 'porto-functionality' ), value: 'more' } ],
					onChange: ( value ) => { props.setAttributes( { read_more: value } ); },
				} ),
				'none' != attrs.read_more && el( TextControl, {
					label: __( 'Add Link', 'porto-functionality' ),
					value: attrs.link,
					onChange: ( value ) => { props.setAttributes( { link: value } ); },
				} ),
				'more' == attrs.read_more && el( TextControl, {
					label: __( 'Read More Text', 'porto-functionality' ),
					value: attrs.read_text,
					onChange: ( value ) => { props.setAttributes( { read_text: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Select Hover Effect type', 'porto-functionality' ),
					value: attrs.hover_effect,
					options: [ { label: __( 'No Effect', 'porto-functionality' ), value: 'style_1' }, { label: __( 'Icon Zoom', 'porto-functionality' ), value: 'style_2' }, { label: __( 'Icon Bounce Up', 'porto-functionality' ), value: 'style_3' } ],
					onChange: ( value ) => { props.setAttributes( { hover_effect: value } ); },
				} ),
				el( 'h3', null, __( 'Title settings', 'porto-functionality' ) ),
				el( SelectControl, {
					label: __( 'Tag', 'porto-functionality' ),
					value: attrs.heading_tag,
					options: [ { label: __( 'H1', 'PORTO-FUNCTIONALITY' ), value: 'h1' }, { label: __( 'H2', 'PORTO-FUNCTIONALITY' ), value: 'h2' }, { label: __( 'H3', 'PORTO-FUNCTIONALITY' ), value: 'h3' }, { label: __( 'H4', 'PORTO-FUNCTIONALITY' ), value: 'h4' }, { label: __( 'H5', 'PORTO-FUNCTIONALITY' ), value: 'h5' }, { label: __( 'H6', 'PORTO-FUNCTIONALITY' ), value: 'h6' }, { label: __( 'div', 'porto-functionality' ), value: 'div' }, { label: __( 'p', 'porto-functionality' ), value: 'p' } ],
					onChange: ( value ) => { props.setAttributes( { heading_tag: value } ); },
				} ),
			),
			el( PanelBody, {
				title: __( 'Style', 'porto-functionality' ),
				initialOpen: false,
			},
				el( TextControl, {
					label: __( 'Icon Margin Right', 'porto-functionality' ),
					value: attrs.icon_margin_right,
					help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
					onChange: ( value ) => { props.setAttributes( { icon_margin_right: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Icon Margin Bottom', 'porto-functionality' ),
					value: attrs.icon_margin_bottom,
					help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
					onChange: ( value ) => { props.setAttributes( { icon_margin_bottom: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Title Margin Bottom', 'porto-functionality' ),
					value: attrs.title_margin_bottom,
					help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
					onChange: ( value ) => { props.setAttributes( { title_margin_bottom: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Sub Title Margin Bottom', 'porto-functionality' ),
					value: attrs.sub_title_margin_bottom,
					help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
					onChange: ( value ) => { props.setAttributes( { sub_title_margin_bottom: value } ); },
				} ),
				el( PortoTypographyControl, {
					label: __( 'Title Font', 'porto-functionality' ),
					value: { fontFamily: attrs.title_font, fontSize: attrs.title_font_size, fontWeight: attrs.title_font_style, textTransform: attrs.title_text_transform, lineHeight: attrs.title_font_line_height, letterSpacing: attrs.title_font_letter_spacing, color: attrs.title_font_color },
					options: {},
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { title_font: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { title_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { title_font_style: value.fontWeight } );
						}
						if ( typeof value.textTransform != 'undefined' ) {
							props.setAttributes( { title_text_transform: value.textTransform } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { title_font_line_height: value.lineHeight } );
						}
						if ( typeof value.letterSpacing != 'undefined' ) {
							props.setAttributes( { title_font_letter_spacing: value.letterSpacing } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { title_font_color: value.color } );
						} else {
							props.setAttributes( { title_font_color: '' } );
						}
					},
				} ),
				el( PortoTypographyControl, {
					label: __( 'Sub Title Font', 'porto-functionality' ),
					value: { fontSize: attrs.subtitle_font_size, fontWeight: attrs.subtitle_font_style, lineHeight: attrs.subtitle_font_line_height, letterSpacing: attrs.subtitle_font_letter_spacing, color: attrs.subtitle_font_color },
					options: { fontFamily: false, textTransform: false },
					onChange: ( value ) => {
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { subtitle_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { subtitle_font_style: value.fontWeight } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { subtitle_font_line_height: value.lineHeight } );
						}
						if ( typeof value.letterSpacing != 'undefined' ) {
							props.setAttributes( { subtitle_font_letter_spacing: value.letterSpacing } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { subtitle_font_color: value.color } );
						} else {
							props.setAttributes( { subtitle_font_color: '' } );
						}
					},
				} ),
				el( PortoTypographyControl, {
					label: __( 'Description Font', 'porto-functionality' ),
					value: { fontFamily: attrs.desc_font, fontSize: attrs.desc_font_size, fontWeight: attrs.desc_font_style, lineHeight: attrs.desc_font_line_height, letterSpacing: attrs.desc_font_letter_spacing, color: attrs.desc_font_color },
					options: { textTransform: false },
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { desc_font: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { desc_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { desc_font_style: value.fontWeight } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { desc_font_line_height: value.lineHeight } );
						}
						if ( typeof value.letterSpacing != 'undefined' ) {
							props.setAttributes( { desc_font_letter_spacing: value.letterSpacing } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { desc_font_color: value.color } );
						} else {
							props.setAttributes( { desc_font_color: '' } );
						}
					},
				} ),
			)
		);

		var ex_class = '',
			ic_class = '';
		var title_style = {}, subtitle_style = {}, desc_style = {};
		if ( attrs.pos ) {
			ex_class = attrs.pos + '-icon';
			ic_class = 'porto-sicon-' + attrs.pos;
			if ( 'default' === attrs.pos && attrs.content ) {
				ex_class += ' flex-wrap';
			} else if ( 'top' === attrs.pos && attrs.h_align && 'center' != attrs.h_align ) {
				ex_class += ' text-' + attrs.h_align;
			}
		}
		if ( attrs.className ) {
			ex_class += ' ' + attrs.className;
		}

		/* title */
		if ( attrs.title_font ) {
			title_style.fontFamily = attrs.title_font;
		}
		if ( attrs.title_font_style ) {
			title_style.fontWeight = Number( attrs.title_font_style );
		}
		if ( attrs.title_font_size ) {
			const unit = attrs.title_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.title_font_size += 'px';
			}
			title_style.fontSize = attrs.title_font_size;
		}
		if ( attrs.title_text_transform ) {
			title_style.textTransform = attrs.title_text_transform;
		}
		if ( attrs.title_font_line_height ) {
			const unit = ( '' + attrs.title_font_line_height ).replace( /[0-9.]/g, '' );
			if ( !unit && attrs.title_font_line_height > 3 ) {
				attrs.title_font_line_height += 'px';
			}
			title_style.lineHeight = attrs.title_font_line_height;
		}
		if ( attrs.title_font_letter_spacing ) {
			const unit = attrs.title_font_letter_spacing.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.title_font_letter_spacing += 'px';
			}
			title_style.letterSpacing = attrs.title_font_letter_spacing;
		}
		if ( attrs.title_font_color ) {
			title_style.color = attrs.title_font_color;
		}
		if ( attrs.title_margin_bottom || '0' === attrs.title_margin_bottom ) {
			const unit = attrs.title_margin_bottom.trim().replace( /[0-9.-]/g, '' );
			if ( !unit ) {
				attrs.title_margin_bottom += 'px';
			}
			title_style.marginBottom = attrs.title_margin_bottom;
		}

		/* sub title */
		if ( attrs.subtitle_font_style ) {
			subtitle_style.fontWeight = Number( attrs.subtitle_font_style );
		}
		if ( attrs.subtitle_font_size ) {
			const unit = attrs.subtitle_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.subtitle_font_size += 'px';
			}
			subtitle_style.fontSize = attrs.subtitle_font_size;
		}
		if ( attrs.subtitle_font_line_height ) {
			const unit = ( '' + attrs.subtitle_font_line_height ).replace( /[0-9.]/g, '' );
			if ( !unit && attrs.subtitle_font_line_height > 3 ) {
				attrs.subtitle_font_line_height += 'px';
			}
			subtitle_style.lineHeight = attrs.subtitle_font_line_height;
		}
		if ( attrs.subtitle_font_letter_spacing ) {
			const unit = attrs.subtitle_font_letter_spacing.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.subtitle_font_letter_spacing += 'px';
			}
			subtitle_style.letterSpacing = attrs.subtitle_font_letter_spacing;
		}
		if ( attrs.subtitle_font_color ) {
			subtitle_style.color = attrs.subtitle_font_color;
		}
		if ( attrs.sub_title_margin_bottom || '0' === attrs.sub_title_margin_bottom ) {
			const unit = attrs.sub_title_margin_bottom.trim().replace( /[0-9.-]/g, '' );
			if ( !unit ) {
				attrs.sub_title_margin_bottom += 'px';
			}
			subtitle_style.marginBottom = attrs.sub_title_margin_bottom;
		}

		/* description */
		if ( attrs.desc_font ) {
			desc_style.fontFamily = attrs.desc_font;
		}
		if ( attrs.desc_font_style ) {
			desc_style.fontWeight = attrs.desc_font_style;
		}
		if ( attrs.desc_font_size ) {
			let unit = attrs.desc_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.desc_font_size += 'px';
			}
			desc_style.fontSize = attrs.desc_font_size;
		}
		if ( attrs.desc_font_line_height ) {
			const unit = ( '' + attrs.desc_font_line_height ).replace( /[0-9.]/g, '' );
			if ( !unit && attrs.desc_font_line_height > 3 ) {
				attrs.desc_font_line_height += 'px';
			}
			desc_style.lineHeight = attrs.desc_font_line_height;
		}
		if ( attrs.desc_font_letter_spacing ) {
			let unit = attrs.desc_font_letter_spacing.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.desc_font_letter_spacing += 'px';
			}
			desc_style.fontSize = attrs.desc_font_letter_spacing;
		}
		if ( attrs.desc_font_color ) {
			desc_style.color = attrs.desc_font_color;
		}

		var bodyRender = null,
			renderControls,
			boxIcon,
			icon_align_style = {},
			boxIconStyle = {},
			elx_class = '';

		if ( attrs.icon_margin_bottom ) {
			let unit = attrs.icon_margin_bottom.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.icon_margin_bottom += 'px';
			}
			icon_align_style.marginBottom = attrs.icon_margin_bottom;
		}

		if ( attrs.icon_type == 'custom' ) {
			if ( attrs.icon_style !== 'none' && attrs.icon_color_bg ) {
				boxIconStyle.backgroundColor = attrs.icon_color_bg;
			}
			if ( attrs.icon_style == 'circle' ) {
				elx_class += ' porto-u-circle ';
			}
			if ( attrs.icon_style == 'circle_img' ) {
				elx_class += ' porto-u-circle-img ';
			}
			if ( attrs.icon_style == 'square' ) {
				elx_class += ' porto-u-square ';
			}
			if ( ( attrs.icon_style == 'advanced' || attrs.icon_style == 'circle_img' ) && attrs.icon_border_style ) {
				boxIconStyle.borderStyle = attrs.icon_border_style;
				if ( attrs.icon_color_border ) {
					boxIconStyle.borderColor = attrs.icon_color_border;
				}
				if ( attrs.icon_border_size ) {
					boxIconStyle.borderWidth = attrs.icon_border_size + 'px';
				}
				if ( attrs.icon_border_spacing ) {
					boxIconStyle.padding = attrs.icon_border_spacing + 'px';
				}
				if ( attrs.icon_border_radius ) {
					boxIconStyle.borderRadius = attrs.icon_border_radius + 'px';
				}
			}

			if ( attrs.icon_img_url ) {
				boxIconStyle.display = 'inline-block';
				boxIconStyle.fontSize = attrs.img_width + 'px';
				if ( attrs.icon_margin_right ) {
					const unit = attrs.icon_margin_right.replace( /[0-9.]/g, '' );
					if ( !unit ) {
						attrs.icon_margin_right += 'px';
					}
					if ( porto_block_vars.is_rtl ) {
						boxIconStyle.marginLeft = attrs.icon_margin_right;
					} else {
						boxIconStyle.marginRight = attrs.icon_margin_right;
					}
				}
				boxIcon = el(
					'div',
					{ className: 'porto-sicon-img' + elx_class, style: boxIconStyle },
					el( 'img', { src: attrs.icon_img_url, alt: '' } )
				);
			}
		} else {
			if ( attrs.icon_color )
				boxIconStyle.color = attrs.icon_color;
			if ( attrs.icon_style !== 'none' ) {
				if ( attrs.icon_color_bg !== '' ) {
					boxIconStyle.backgroundColor = attrs.icon_color_bg;
				}
			}
			if ( attrs.icon_style == 'advanced' ) {
				if ( attrs.icon_border_style ) {
					boxIconStyle.borderStyle = attrs.icon_border_style;
					if ( attrs.icon_color_border ) {
						boxIconStyle.borderColor = attrs.icon_color_border;
					}
					if ( attrs.icon_border_size ) {
						boxIconStyle.borderWidth = attrs.icon_border_size + 'px';
					}
				}
				boxIconStyle.width = attrs.icon_border_spacing + 'px';
				boxIconStyle.height = attrs.icon_border_spacing + 'px';
				boxIconStyle.lineHeight = attrs.icon_border_spacing + 'px';
				boxIconStyle.borderRadius = attrs.icon_border_radius + 'px';
			}
			if ( attrs.icon_size ) {
				boxIconStyle.fontSize = attrs.icon_size + 'px';
			}
			boxIconStyle.display = 'inline-block';
			if ( attrs.icon ) {
				if ( attrs.icon_margin_right ) {
					const unit = attrs.icon_margin_right.replace( /[0-9.]/g, '' );
					if ( !unit ) {
						attrs.icon_margin_right += 'px';
					}
					if ( porto_block_vars.is_rtl ) {
						boxIconStyle.marginLeft = attrs.icon_margin_right;
					} else {
						boxIconStyle.marginRight = attrs.icon_margin_right;
					}
				}
				boxIcon = el(
					'div',
					{ className: 'porto-icon ' + attrs.icon_style + ' ' + elx_class, style: boxIconStyle },
					el( 'i', { className: attrs.icon } )
				);
			}
		}
		boxIcon = el(
			'div',
			{ className: 'align-icon', style: icon_align_style },
			boxIcon
		);
		var internal_style = '';
		if ( attrs.icon_style == 'circle_img' && attrs.icon_type == 'custom' && attrs.icon_border_spacing ) {
			internal_style += '#porto-icon-' + clientId + ' .porto-sicon-img.porto-u-circle-img:before {';
			internal_style += 'border-width: ' + ( attrs.icon_border_spacing + 1 ) + 'px';
			if ( attrs.icon_color_bg ) {
				internal_style += 'border-color: ' + attrs.icon_color_bg;
			}
			internal_style += '}';
			internal_style = el(
				'style',
				null,
				internal_style
			);
		}
		boxIcon = el(
			'div',
			{ id: 'porto-icon-' + clientId, className: 'porto-just-icon-wrapper' },
			internal_style,
			boxIcon
		);


		if ( attrs.pos == 'heading-right' || attrs.pos == 'right' ) {
			if ( attrs.title ) {
				var titleRender = el(
					attrs.heading_tag,
					{ className: 'porto-sicon-title', style: title_style, dangerouslySetInnerHTML: { __html: attrs.title.replace( /<script.*?\/script>/g, '' ) } }
				);
				bodyRender = el(
					'div',
					{ className: 'porto-sicon-header' },
					attrs.link && attrs.read_more == 'title' && el(
						'a',
						{ className: 'porto-sicon-box-link', href: attrs.link },
						titleRender,
					),
					( !attrs.link || attrs.read_more != 'title' ) && titleRender,
					attrs.subtitle && el(
						'p',
						{ style: subtitle_style },
						attrs.subtitle,
					)
				);
			}
			bodyRender = el(
				'div',
				{ className: ( attrs.pos == 'right' ? 'porto-sicon-body' : 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) ) },
				bodyRender,
				attrs.pos != 'right' && ( attrs.icon || attrs.icon_img_url ) && el(
					'div',
					{ className: ic_class },
					boxIcon
				),
				attrs.content && el(
					'div',
					{ className: 'porto-sicon-description', style: desc_style, dangerouslySetInnerHTML: { __html: attrs.content.replace( /<script.*?\/script>/g, '' ) } },
					attrs.link && attrs.read_more == 'more' && el(
						'a',
						{ className: 'porto-sicon-read', href: attrs.link },
						attrs.read_text
					)
				)
			);

			if ( attrs.pos == 'right' ) {
				bodyRender = el(
					'div',
					{ className: 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) },
					bodyRender,
					el(
						'div',
						{ className: ic_class },
						boxIcon
					)
				);
			}

		} else {
			var titleRender = '',
				contentRender = '';
			if ( attrs.title ) {
				titleRender = el(
					attrs.heading_tag,
					{ className: 'porto-sicon-title', style: title_style, dangerouslySetInnerHTML: { __html: attrs.title.replace( /<script.*?\/script>/g, '' ) } }
				);
				titleRender = el(
					'div',
					{ className: 'porto-sicon-header' },
					attrs.link && attrs.read_more == 'title' && el(
						'a',
						{ className: 'porto-sicon-box-link', href: attrs.link },
						titleRender,
					),
					( !attrs.link || attrs.read_more != 'title' ) && titleRender,
					attrs.subtitle && el(
						'p',
						{ style: subtitle_style },
						attrs.subtitle,
					)
				);
			}
			if ( attrs.content ) {
				contentRender = el(
					'div',
					{ className: 'porto-sicon-description', style: desc_style, dangerouslySetInnerHTML: { __html: attrs.content.replace( /<script.*?\/script>/g, '' ) } },
					attrs.link && attrs.read_more == 'more' && el(
						'a',
						{ className: 'porto-sicon-read xx', href: attrs.link },
						attrs.read_text
					)
				)
			}
			if ( attrs.pos == 'left' ) {
				bodyRender = el(
					'div',
					{ className: 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) },
					( attrs.icon || attrs.icon_img_url ) && el(
						'div',
						{ className: ic_class },
						boxIcon
					),
					el(
						'div',
						{ className: 'porto-sicon-body' },
						titleRender,
						contentRender
					)
				);
			} else {
				bodyRender = el(
					'div',
					{ className: 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) },
					( attrs.icon || attrs.icon_img_url ) && el(
						'div',
						{ className: ic_class },
						boxIcon
					),
					titleRender,
					contentRender
				);
			}
		}


		if ( attrs.link && attrs.read_more == 'box' ) {
			renderControls = el(
				'a',
				{ className: 'porto-sicon-box-link', href: attrs.link },
				bodyRender,
			);
		} else {
			renderControls = bodyRender;
		}

		return [
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-info-box', {
		title: 'Porto Info Box',
		icon: 'porto',
		category: 'porto',
		attributes: {
			icon_type: { type: 'string' },
			icon: { type: 'string' },
			icon_img: { type: 'int' },
			icon_img_url: { type: 'string' },
			img_width: { type: 'int', default: 48 },
			icon_size: { type: 'int', default: 32 },
			icon_color: { type: 'string' },
			icon_style: { type: 'string', default: 'none' },
			icon_color_bg: { type: 'string' },
			icon_color_border: { type: 'string', default: '#333' },
			icon_border_style: { type: 'string' },
			icon_border_size: { type: 'int', default: 1 },
			icon_border_radius: { type: 'int', default: 500 },
			icon_border_spacing: { type: 'int', default: 50 },
			icon_animation: { type: 'string' },
			title: { type: 'string', default: 'Porto Icon Title' },
			subtitle: { type: 'string' },
			content: { type: 'string' },
			link: { type: 'string' },
			hover_effect: { type: 'string', default: 'style_1' },
			pos: { type: 'string', default: 'default' },
			h_align: { type: 'string', default: 'center' },
			read_more: { type: 'string', default: 'none' },
			read_text: { type: 'string', default: 'Read More' },
			heading_tag: { type: 'string', default: 'h3' },
			title_font: { type: 'string' },
			title_font_style: { type: 'int' },
			title_font_size: { type: 'string' },
			title_text_transform: { type: 'string' },
			title_font_line_height: {},
			title_font_letter_spacing: { type: 'string' },
			title_font_color: { type: 'string' },
			subtitle_font_style: { type: 'int' },
			subtitle_font_size: { type: 'string' },
			subtitle_font_line_height: {},
			subtitle_font_letter_spacing: { type: 'string' },
			subtitle_font_color: { type: 'string' },
			desc_font: { type: 'string' },
			desc_font_style: { type: 'int' },
			desc_font_size: { type: 'string' },
			desc_font_color: { type: 'string' },
			desc_font_line_height: {},
			desc_font_letter_spacing: { type: 'string' },
			icon_margin_right: { type: 'string' },
			icon_margin_bottom: { type: 'string' },
			title_margin_bottom: { type: 'string' },
			sub_title_margin_bottom: { type: 'string' },
			animation_type: { type: 'string' },
		},
		edit: PortoInfoBox,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 7. Porto Stat Counter
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		MediaUpload = wpBlockEditor.MediaUpload,
		el = wpElement.createElement,
		Component = wpElement.Component,
		PanelBody = wpComponents.PanelBody,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		IconButton = wpComponents.IconButton,
		useEffect = wpElement.useEffect;

	let counterWillUpdate = null, isFirstLoad = true;

	const PortoStatCounter = function ( props ) {
		useEffect(
			() => {
				const clientId = props.clientId;
				clearTimeout( counterWillUpdate );
				counterWillUpdate = setTimeout( function () {
					jQuery( document.body ).trigger( 'porto_refresh_vc_content', [ jQuery( '[data-block="' + clientId + '"]' ) ] );
				}, 1000 );
			},
			[ props.attributes.counter_title, props.attributes.counter_value, props.attributes.counter_sep, props.attributes.counter_suffix, props.attributes.counter_prefix, props.attributes.counter_decimal, props.attributes.speed ],
		);

		if ( isFirstLoad && typeof countUp == "undefined" ) {
			isFirstLoad = false;
			var c = document.createElement( "script" );
			c.src = ajaxurl.replace( '/wp-admin/admin-ajax.php', '/wp-content/plugins/porto-functionality/shortcodes/assets/js/countup.min.js' );
			if ( !jQuery( 'script[src="' + c.src + '"]' ).length ) {
				document.getElementsByTagName( "body" )[ 0 ].appendChild( c );
			}
			jQuery( c ).on( 'load', function () {
				c = document.createElement( "script" );
				c.src = ajaxurl.replace( '/wp-admin/admin-ajax.php', '/wp-content/plugins/porto-functionality/shortcodes/assets/js/countup-loader.min.js' );
				if ( !jQuery( 'script[src="' + c.src + '"]' ).length ) {
					document.getElementsByTagName( "body" )[ 0 ].appendChild( c );
				}
			} );
		}

		var attrs = props.attributes,
			clientId = props.clientId;

		var inspectorControls = el( InspectorControls, {},
			el( PanelBody, {
				title: __( 'Stat Counter', 'porto-functionality' ),
				initialOpen: true
			},
				el( SelectControl, {
					label: __( 'Icon to display', 'porto-functionality' ),
					value: attrs.icon_type,
					options: [ { label: __( 'Icon Font', 'porto-functionality' ), value: '' }, { label: __( 'Custom Image Icon', 'porto-functionality' ), value: 'custom' } ],
					onChange: ( value ) => { props.setAttributes( { icon_type: value } ); },
				} ),
				!attrs.icon_type && el( TextControl, {
					label: __( 'Icon Class', 'porto-functionality' ),
					value: attrs.icon,
					onChange: ( value ) => { props.setAttributes( { icon: value } ); },
				} ),
				'custom' == attrs.icon_type && el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: attrs.icon_img,
					onSelect: function onSelect( image ) {
						return props.setAttributes( { icon_img_url: image.url, icon_img: image.id } );
					},
					render: function render( _ref ) {
						var open = _ref.open;
						return el( IconButton, {
							className: 'components-toolbar__control',
							label: __( 'Change image', 'porto-functionality' ),
							icon: 'edit',
							onClick: open
						} );
					}
				} ),
				'custom' == attrs.icon_type && el( IconButton, {
					className: 'components-toolbar__control',
					label: __( 'Remove image', 'porto-functionality' ),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes( { icon_img_url: undefined, icon_img: undefined } );
					}
				} ),
				'custom' == attrs.icon_type && el( RangeControl, {
					label: __( 'Image Width', 'porto-functionality' ),
					value: attrs.img_width,
					min: 16,
					max: 512,
					onChange: ( value ) => { props.setAttributes( { img_width: value } ); },
				} ),
				'custom' != attrs.icon_type && el( RangeControl, {
					label: __( 'Icon Size', 'porto-functionality' ),
					value: attrs.icon_size,
					min: 12,
					max: 72,
					onChange: ( value ) => { props.setAttributes( { icon_size: value } ); },
				} ),
				'custom' != attrs.icon_type && el( PanelColorSettings, {
					title: __( 'Color Settings', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Color', 'porto-functionality' ),
						value: attrs.icon_color,
						onChange: function onChange( value ) {
							return props.setAttributes( { icon_color: value } );
						}
					} ]
				} ),
				el( SelectControl, {
					label: __( 'Icon Style', 'porto-functionality' ),
					value: attrs.icon_style,
					options: [ { label: __( 'Simple', 'porto-functionality' ), value: 'none' }, { label: __( 'Circle Background', 'porto-functionality' ), value: 'circle' }, { label: __( 'Square Background', 'porto-functionality' ), value: 'square' }, { label: __( 'Design your own', 'porto-functionality' ), value: 'advanced' } ],
					onChange: ( value ) => { props.setAttributes( { icon_style: value } ); },
				} ),
				'none' != attrs.icon_style && el( PanelColorSettings, {
					title: __( 'Background Color', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Background Color', 'porto-functionality' ),
						value: attrs.icon_color_bg,
						onChange: function onChange( value ) {
							return props.setAttributes( { icon_color_bg: value } );
						}
					} ]
				} ),
				( 'advanced' == attrs.icon_style ) && el( SelectControl, {
					label: __( 'Icon Border Style', 'porto-functionality' ),
					value: attrs.icon_border_style,
					options: [ { label: __( 'None', 'porto-functionality' ), value: '' }, { label: __( 'Solid', 'porto-functionality' ), value: 'solid' }, { label: __( 'Dashed', 'porto-functionality' ), value: 'dashed' }, { label: __( 'Dotted', 'porto-functionality' ), value: 'dotted' }, { label: __( 'Double', 'porto-functionality' ), value: 'double' }, { label: __( 'Inset', 'porto-functionality' ), value: 'inset' }, { label: __( 'Outset', 'porto-functionality' ), value: 'outset' } ],
					onChange: ( value ) => { props.setAttributes( { icon_border_style: value } ); },
				} ),
				attrs.icon_border_style && el( PanelColorSettings, {
					title: __( 'Border Color', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Border Color', 'porto-functionality' ),
						value: attrs.icon_color_border,
						onChange: function onChange( value ) {
							return props.setAttributes( { icon_color_border: value } );
						}
					} ]
				} ),
				attrs.icon_border_style && el( RangeControl, {
					label: __( 'Border Width', 'porto-functionality' ),
					value: attrs.icon_border_size,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { icon_border_size: value } ); },
				} ),
				attrs.icon_border_style && el( RangeControl, {
					label: __( 'Border Radius', 'porto-functionality' ),
					value: attrs.icon_border_radius,
					min: 1,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_radius: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( RangeControl, {
					label: __( 'Background Size', 'porto-functionality' ),
					value: attrs.icon_border_spacing,
					min: 0,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_spacing: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( 'p', { style: { fontStyle: 'italic' } }, __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ) ),
				el( TextControl, {
					label: __( 'Icon Animation Type', 'porto-functionality' ),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
					help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
				} ),
				el(
					'p',
					{ style: { marginTop: -20 } },
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( SelectControl, {
					label: __( 'Icon Position', 'porto-functionality' ),
					value: attrs.icon_position,
					options: [ { label: __( 'Top', 'porto-functionality' ), value: 'top' }, { label: __( 'Right', 'porto-functionality' ), value: 'right' }, { label: __( 'Left', 'porto-functionality' ), value: 'left' } ],
					onChange: ( value ) => { props.setAttributes( { icon_position: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Counter Title', 'porto-functionality' ),
					value: attrs.counter_title,
					onChange: ( value ) => { props.setAttributes( { counter_title: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Counter Value', 'porto-functionality' ),
					value: attrs.counter_value,
					onChange: ( value ) => { props.setAttributes( { counter_value: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Thousands Separator', 'porto-functionality' ),
					value: attrs.counter_sep,
					onChange: ( value ) => { props.setAttributes( { counter_sep: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Replace Decimal Point With', 'porto-functionality' ),
					value: attrs.counter_decimal,
					onChange: ( value ) => { props.setAttributes( { counter_decimal: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Counter Value Prefix', 'porto-functionality' ),
					value: attrs.counter_prefix,
					onChange: ( value ) => { props.setAttributes( { counter_prefix: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Counter Value Suffix', 'porto-functionality' ),
					value: attrs.counter_suffix,
					onChange: ( value ) => { props.setAttributes( { counter_suffix: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Counter rolling time', 'porto-functionality' ),
					value: attrs.speed,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { speed: value } ); },
				} )
			),
			el( PanelBody, {
				title: __( 'Typography', 'porto-functionality' ),
				initialOpen: false
			},
				el( PortoTypographyControl, {
					label: __( 'Counter Title Font', 'porto-functionality' ),
					value: { fontFamily: attrs.title_font, fontSize: attrs.title_font_size, fontWeight: attrs.title_font_style, lineHeight: attrs.title_font_line_height, color: attrs.counter_color_txt },
					options: { textTransform: false, letterSpacing: false },
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { title_font: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { title_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { title_font_style: value.fontWeight } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { title_font_line_height: value.lineHeight } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { counter_color_txt: value.color } );
						} else {
							props.setAttributes( { counter_color_txt: '' } );
						}
					},
				} ),
				el( PortoTypographyControl, {
					label: __( 'Counter Value Font', 'porto-functionality' ),
					value: { fontFamily: attrs.desc_font, fontSize: attrs.desc_font_size, fontWeight: attrs.desc_font_style, lineHeight: attrs.desc_font_line_height, color: attrs.desc_font_color },
					options: { textTransform: false, letterSpacing: false },
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { desc_font: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { desc_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { desc_font_style: value.fontWeight } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { desc_font_line_height: value.lineHeight } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { desc_font_color: value.color } );
						} else {
							props.setAttributes( { desc_font_color: '' } );
						}
					},
				} ),
				el( PortoTypographyControl, {
					label: __( 'Counter suffix-prefix Value Font', 'porto-functionality' ),
					value: { fontFamily: attrs.suf_pref_font, fontSize: attrs.suf_pref_font_size, fontWeight: attrs.suf_pref_font_style, lineHeight: attrs.suf_pref_line_height, color: attrs.suf_pref_font_color },
					options: { textTransform: false, letterSpacing: false },
					onChange: ( value ) => {
						if ( typeof value.fontFamily != 'undefined' ) {
							props.setAttributes( { suf_pref_font: value.fontFamily } );
						}
						if ( typeof value.fontSize != 'undefined' ) {
							props.setAttributes( { suf_pref_font_size: value.fontSize } );
						}
						if ( typeof value.fontWeight != 'undefined' ) {
							props.setAttributes( { suf_pref_font_style: value.fontWeight } );
						}
						if ( typeof value.lineHeight != 'undefined' ) {
							props.setAttributes( { suf_pref_line_height: value.lineHeight } );
						}
						if ( typeof value.color != 'undefined' ) {
							props.setAttributes( { suf_pref_font_color: value.color } );
						} else {
							props.setAttributes( { suf_pref_font_color: '' } );
						}
					},
				} )
			)
		);

		var boxIcon,
			icon_align_style = {},
			boxIconStyle = {},
			elx_class = '';

		if ( attrs.icon_type == 'custom' ) {
			if ( attrs.icon_style !== 'none' && attrs.icon_color_bg ) {
				boxIconStyle.backgroundColor = attrs.icon_color_bg;
			}
			if ( attrs.icon_style == 'circle' ) {
				elx_class += ' porto-u-circle ';
			}
			if ( attrs.icon_style == 'circle_img' ) {
				elx_class += ' porto-u-circle-img ';
			}
			if ( attrs.icon_style == 'square' ) {
				elx_class += ' porto-u-square ';
			}
			if ( ( attrs.icon_style == 'advanced' || attrs.icon_style == 'circle_img' ) && attrs.icon_border_style ) {
				boxIconStyle.borderStyle = attrs.icon_border_style;
				if ( attrs.icon_color_border ) {
					boxIconStyle.borderColor = attrs.icon_color_border;
				}
				if ( attrs.icon_border_size ) {
					boxIconStyle.borderWidth = attrs.icon_border_size + 'px';
				}
				if ( attrs.icon_border_spacing ) {
					boxIconStyle.padding = attrs.icon_border_spacing + 'px';
				}
				if ( attrs.icon_border_radius ) {
					boxIconStyle.borderRadius = attrs.icon_border_radius + 'px';
				}
			}

			if ( attrs.icon_img_url ) {
				boxIconStyle.display = 'inline-block';
				boxIconStyle.fontSize = attrs.img_width + 'px';
				boxIcon = el(
					'div',
					{ className: 'porto-sicon-img' + elx_class, style: boxIconStyle },
					el( 'img', { src: attrs.icon_img_url, alt: '' } )
				);
			}
		} else {
			if ( attrs.icon_color )
				boxIconStyle.color = attrs.icon_color;
			if ( attrs.icon_style !== 'none' ) {
				if ( attrs.icon_color_bg !== '' ) {
					boxIconStyle.backgroundColor = attrs.icon_color_bg;
				}
			}
			if ( attrs.icon_style == 'advanced' ) {
				if ( attrs.icon_border_style ) {
					boxIconStyle.borderStyle = attrs.icon_border_style;
					if ( attrs.icon_color_border ) {
						boxIconStyle.borderColor = attrs.icon_color_border;
					}
					if ( attrs.icon_border_size ) {
						boxIconStyle.borderWidth = attrs.icon_border_size + 'px';
					}
				}
				boxIconStyle.width = attrs.icon_border_spacing + 'px';
				boxIconStyle.height = attrs.icon_border_spacing + 'px';
				boxIconStyle.lineHeight = attrs.icon_border_spacing + 'px';
				boxIconStyle.borderRadius = attrs.icon_border_radius + 'px';
			}
			if ( attrs.icon_size )
				boxIconStyle.fontSize = attrs.icon_size + 'px';
			boxIconStyle.display = 'inline-block';
			if ( attrs.icon ) {
				boxIcon = el(
					'div',
					{ className: 'porto-icon ' + attrs.icon_style + ' ' + elx_class, style: boxIconStyle },
					el( 'i', { className: attrs.icon } )
				);
			}
		}
		boxIcon = el(
			'div',
			{ className: 'align-icon', style: icon_align_style },
			boxIcon
		);
		var internal_style = '';
		if ( attrs.icon_style == 'circle_img' && attrs.icon_type == 'custom' && attrs.icon_border_spacing ) {
			internal_style += '#porto-icon-' + clientId + ' .porto-sicon-img.porto-u-circle-img:before {';
			internal_style += 'border-width: ' + ( attrs.icon_border_spacing + 1 ) + 'px';
			if ( attrs.icon_color_bg ) {
				internal_style += 'border-color: ' + attrs.icon_color_bg;
			}
			internal_style += '}';
			internal_style = el(
				'style',
				null,
				internal_style
			);
		}
		boxIcon = el(
			'div',
			{ id: 'porto-icon-' + clientId, className: 'porto-just-icon-wrapper' },
			internal_style,
			boxIcon
		);

		var title_style = {}, desc_style = {}, counter_color = {}, suf_pref_style = {};

		/* title */
		if ( attrs.title_font ) {
			title_style.fontFamily = attrs.title_font;
		}
		if ( attrs.title_font_style ) {
			title_style.fontWeight = attrs.title_font_style;
		}
		if ( attrs.title_font_size ) {
			let unit = attrs.title_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.title_font_size += 'px';
			}
			title_style.fontSize = attrs.title_font_size;
		}
		if ( attrs.title_font_line_height ) {
			let unit = ( '' + attrs.title_font_line_height ).replace( /[0-9.]/g, '' );
			if ( !unit && attrs.title_font_line_height > 3 ) {
				attrs.title_font_line_height += 'px';
			}
			title_style.lineHeight = attrs.title_font_line_height;
		}

		/* description */
		if ( attrs.desc_font ) {
			desc_style.fontFamily = attrs.desc_font;
		}
		if ( attrs.desc_font_style ) {
			desc_style.fontWeight = attrs.desc_font_style;
		}
		if ( attrs.desc_font_size ) {
			let unit = attrs.desc_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.desc_font_size += 'px';
			}
			desc_style.fontSize = attrs.desc_font_size;
		}
		if ( attrs.desc_font_line_height ) {
			let unit = ( '' + attrs.desc_font_line_height ).replace( /[0-9.]/g, '' );
			if ( !unit && attrs.desc_font_line_height > 3 ) {
				attrs.desc_font_line_height += 'px';
			}
			desc_style.lineHeight = attrs.desc_font_line_height;
		}
		if ( attrs.desc_font_color || attrs.counter_color_txt ) {
			desc_style.color = attrs.desc_font_color ? attrs.desc_font_color : attrs.counter_color_txt;
		}
		if ( attrs.counter_color_txt ) {
			title_style.color = attrs.counter_color_txt;
		}

		/* Prefix && Suffix */
		if ( attrs.suf_pref_font ) {
			suf_pref_style.fontFamily = attrs.suf_pref_font;
		}
		if ( attrs.suf_pref_font_style ) {
			suf_pref_style.fontWeight = attrs.suf_pref_font_style;
		}
		if ( attrs.suf_pref_font_size ) {
			let unit = attrs.suf_pref_font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.suf_pref_font_size += 'px';
			}
			suf_pref_style.fontSize = attrs.suf_pref_font_size;
		}
		if ( attrs.suf_pref_line_height ) {
			let unit = ( '' + attrs.suf_pref_line_height ).replace( /[0-9.]/g, '' );
			if ( !unit && attrs.suf_pref_line_height > 3 ) {
				attrs.suf_pref_line_height += 'px';
			}
			suf_pref_style.lineHeight = attrs.suf_pref_line_height;
		}
		if ( attrs.suf_pref_font_color ) {
			suf_pref_style.color = attrs.suf_pref_font_color;
		}

		if ( attrs.counter_sep == '' ) {
			attrs.counter_sep = 'none';
		}
		if ( attrs.counter_decimal == '' ) {
			attrs.counter_decimal = 'none';
		}
		var renderControls = el(
			'div',
			{ className: 'stats-block stats-' + attrs.icon_position + ( attrs.className ? ' ' + attrs.className : '' ) },
			attrs.icon_position != 'right' && el( 'div', { className: 'porto-sicon-' + attrs.icon_position }, boxIcon ),
			el(
				'div',
				{ className: 'stats-desc' },
				attrs.counter_prefix && el(
					'div',
					{ className: 'counter_prefix mycust', style: suf_pref_style },
					attrs.counter_prefix
				),
				el(
					'div',
					{ id: 'counter_' + clientId, 'data-id': 'counter_' + clientId, className: 'stats-number', style: desc_style, 'data-speed': attrs.speed, 'data-counter-value': attrs.counter_value, 'data-separator': attrs.counter_sep, 'data-decimal': attrs.counter_decimal },
					'0'
				),
				attrs.counter_suffix && el(
					'div',
					{ className: 'counter_suffix mycust', style: suf_pref_style },
					attrs.counter_suffix
				),
				el(
					'div',
					{ className: 'stats-text', style: title_style },
					attrs.counter_title
				)
			),
			attrs.icon_position == 'right' && el( 'div', { className: 'porto-sicon-' + attrs.icon_position }, boxIcon ),
		);

		return [
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-stat-counter', {
		title: 'Porto Stat Counter',
		icon: 'porto',
		category: 'porto',
		attributes: {
			icon_type: { type: 'string' },
			icon: { type: 'string' },
			icon_img: { type: 'int' },
			icon_img_url: { type: 'string' },
			img_width: { type: 'int', default: 48 },
			icon_size: { type: 'int', default: 32 },
			icon_color: { type: 'string', default: '#333' },
			icon_style: { type: 'string', default: 'none' },
			icon_color_bg: { type: 'string', default: '#fff' },
			icon_color_border: { type: 'string', default: '#333' },
			icon_border_style: { type: 'string' },
			icon_border_size: { type: 'int', default: 1 },
			icon_border_radius: { type: 'int', default: 500 },
			icon_border_spacing: { type: 'int', default: 50 },
			icon_animation: { type: 'string' },
			icon_link: { type: 'string' },
			animation_type: { type: 'string' },
			counter_title: { type: 'string' },
			counter_value: { type: 'string', default: '1250' },
			counter_sep: { type: 'string', default: ',' },
			counter_suffix: { type: 'string' },
			counter_prefix: { type: 'string' },
			counter_decimal: { type: 'string', default: '.' },
			icon_position: { type: 'string', default: 'top' },
			speed: { type: 'int', default: 3 },
			counter_color_txt: { type: 'string' },
			title_font: { type: 'string' },
			title_font_style: { type: 'int' },
			title_font_size: { type: 'string' },
			title_font_line_height: {},
			desc_font: { type: 'string' },
			desc_font_style: { type: 'int' },
			desc_font_size: { type: 'string' },
			desc_font_color: { type: 'string' },
			desc_font_line_height: {},
			suf_pref_font: { type: 'string' },
			suf_pref_font_style: { type: 'int' },
			suf_pref_font_size: { type: 'string' },
			suf_pref_line_height: {},
			suf_pref_font_color: { type: 'string' },
		},
		edit: PortoStatCounter,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 8. Porto Icons
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		InspectorControls = wpBlockEditor.InspectorControls,
		BlockControls = wpBlockEditor.BlockControls,
		BlockAlignmentToolbar = wpBlockEditor.BlockAlignmentToolbar,
		el = wpElement.createElement,
		Component = wpElement.Component,
		SelectControl = wpComponents.SelectControl;

	const PortoIcons = function ( props ) {
		var attrs = props.attributes,
			clientId = props.clientId;

		var renderControls = el(
			'div',
			{ className: 'porto-u-icons' + ( attrs.align ? ' ' + attrs.align : '' ) + ( attrs.className ? ' ' + attrs.className : '' ) },
			el( InnerBlocks, { allowedBlocks: [ 'porto/porto-single-icon' ] } ),
		);

		return [
			el( BlockControls, null,
				el( BlockAlignmentToolbar, {
					value: attrs.align ? attrs.align.replace( 'porto-icons-', '' ) : attrs.align, onChange: function onChange( value ) {
						return props.setAttributes( { align: 'porto-icons-' + value } );
					}
				} )
			),
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-icons', {
		title: 'Porto Icons',
		icon: 'porto',
		category: 'porto',
		attributes: {
			align: { type: 'string' },
		},
		edit: PortoIcons,
		save: function ( props ) {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 9. Porto Single Icon
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		BlockControls = wpBlockEditor.BlockControls,
		BlockAlignmentToolbar = wpBlockEditor.BlockAlignmentToolbar,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		el = wpElement.createElement,
		Component = wpElement.Component,
		SelectControl = wpComponents.SelectControl,
		TextControl = wpComponents.TextControl,
		RangeControl = wpComponents.RangeControl;

	const PortoSingleIcon = function ( props ) {
		var attrs = props.attributes,
			clientId = props.clientId;

		var inspectorControls = el( InspectorControls, null,
			el( TextControl, {
				label: __( 'Icon Class', 'porto-functionality' ),
				value: attrs.icon,
				onChange: ( value ) => { props.setAttributes( { icon: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Icon Size', 'porto-functionality' ),
				value: attrs.icon_size,
				min: 12,
				max: 72,
				onChange: ( value ) => { props.setAttributes( { icon_size: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Space after Icon', 'porto-functionality' ),
				value: attrs.icon_margin,
				min: 0,
				max: 100,
				onChange: ( value ) => { props.setAttributes( { icon_margin: value } ); },
			} ),
			el( PanelColorSettings, {
				title: __( 'Icon Color Settings', 'porto-functionality' ),
				initialOpen: false,
				colorSettings: [ {
					label: __( 'Color', 'porto-functionality' ),
					value: attrs.icon_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { icon_color: value } );
					}
				} ]
			} ),
			el( SelectControl, {
				label: __( 'Icon Style', 'porto-functionality' ),
				value: attrs.icon_style,
				options: [ { label: __( 'Simple', 'porto-functionality' ), value: 'none' }, { label: __( 'Circle Background', 'porto-functionality' ), value: 'circle' }, { label: __( 'Square Background', 'porto-functionality' ), value: 'square' }, { label: __( 'Design your own', 'porto-functionality' ), value: 'advanced' } ],
				onChange: ( value ) => { props.setAttributes( { icon_style: value } ); },
			} ),
			'none' != attrs.icon_style && el( PanelColorSettings, {
				title: __( 'Background Color', 'porto-functionality' ),
				initialOpen: false,
				colorSettings: [ {
					label: __( 'Background Color', 'porto-functionality' ),
					value: attrs.icon_color_bg,
					onChange: function onChange( value ) {
						return props.setAttributes( { icon_color_bg: value } );
					}
				} ]
			} ),
			'advanced' == attrs.icon_style && el( SelectControl, {
				label: __( 'Icon Border Style', 'porto-functionality' ),
				value: attrs.icon_border_style,
				options: [ { label: __( 'None', 'porto-functionality' ), value: '' }, { label: __( 'Solid', 'porto-functionality' ), value: 'solid' }, { label: __( 'Dashed', 'porto-functionality' ), value: 'dashed' }, { label: __( 'Dotted', 'porto-functionality' ), value: 'dotted' }, { label: __( 'Double', 'porto-functionality' ), value: 'double' }, { label: __( 'Inset', 'porto-functionality' ), value: 'inset' }, { label: __( 'Outset', 'porto-functionality' ), value: 'outset' } ],
				onChange: ( value ) => { props.setAttributes( { icon_border_style: value } ); },
			} ),
			'advanced' == attrs.icon_style && attrs.icon_border_style && el( PanelColorSettings, {
				title: __( 'Border Color', 'porto-functionality' ),
				initialOpen: false,
				colorSettings: [ {
					label: __( 'Border Color', 'porto-functionality' ),
					value: attrs.icon_color_border,
					onChange: function onChange( value ) {
						return props.setAttributes( { icon_color_border: value } );
					}
				} ]
			} ),
			'advanced' == attrs.icon_style && attrs.icon_border_style && el( RangeControl, {
				label: __( 'Border Width', 'porto-functionality' ),
				value: attrs.icon_border_size,
				min: 1,
				max: 10,
				onChange: ( value ) => { props.setAttributes( { icon_border_size: value } ); },
			} ),
			'advanced' == attrs.icon_style && el( RangeControl, {
				label: __( 'Border Radius', 'porto-functionality' ),
				value: attrs.icon_border_radius,
				min: 1,
				max: 500,
				onChange: ( value ) => { props.setAttributes( { icon_border_radius: value } ); },
			} ),
			'advanced' == attrs.icon_style && el( RangeControl, {
				label: __( 'Background Size', 'porto-functionality' ),
				value: attrs.icon_border_spacing,
				min: 10,
				max: 500,
				onChange: ( value ) => { props.setAttributes( { icon_border_spacing: value } ); },
			} ),
			'advanced' == attrs.icon_style && el( 'p', { style: { fontStyle: 'italic' } }, __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ) ),
			el( TextControl, {
				label: __( 'Link', 'porto-functionality' ),
				value: props.attributes.icon_link,
				onChange: ( value ) => { props.setAttributes( { icon_link: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Animation Type', 'porto-functionality' ),
				value: props.attributes.animation_type,
				onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
			} ),
			el(
				'p',
				{ style: { marginTop: -20 } },
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
		);

		var boxIconStyle = {};
		if ( attrs.icon_color ) {
			boxIconStyle.color = attrs.icon_color;
		}
		if ( attrs.icon_style && attrs.icon_style != 'none' && attrs.icon_color_bg ) {
			boxIconStyle.backgroundColor = attrs.icon_color_bg;
		}
		if ( attrs.icon_style == 'advanced' ) {
			boxIconStyle.borderStyle = attrs.icon_border_style;
			boxIconStyle.borderColor = attrs.icon_color_border;
			boxIconStyle.borderWidth = attrs.icon_border_size + 'px';
			boxIconStyle.width = attrs.icon_border_spacing + 'px';
			boxIconStyle.height = attrs.icon_border_spacing + 'px';
			boxIconStyle.lineHeight = attrs.icon_border_spacing + 'px'
			boxIconStyle.borderRadius = attrs.icon_border_radius + 'px';
		}
		if ( attrs.icon_size ) {
			boxIconStyle.fontSize = attrs.icon_size + 'px';
		}
		if ( attrs.icon_margin ) {
			boxIconStyle.marginRight = attrs.icon_margin + 'px';
		}
		var renderControls = el(
			'div',
			{ className: 'porto-icon' + ( attrs.icon_style ? ' ' + attrs.icon_style : '' ) + ( attrs.className ? ' ' + attrs.className : '' ), style: boxIconStyle },
			el( 'i', { className: attrs.icon } ),
		);
		if ( attrs.icon_link ) {
			renderControls = el(
				'a',
				{ href: attrs.icon_link },
				renderControls
			);
		}

		return [
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-single-icon', {
		title: 'Porto Single Icon',
		icon: 'porto',
		category: 'porto',
		attributes: {
			icon: { type: 'string' },
			icon_size: { type: 'int', default: 32 },
			icon_margin: { type: 'int', default: 5 },
			icon_color: { type: 'string', default: '#333' },
			icon_style: { type: 'string' },
			icon_color_bg: { type: 'string', default: '#fff' },
			icon_border_style: { type: 'string' },
			icon_color_border: { type: 'string', default: '#333' },
			icon_border_size: { type: 'int', default: 1 },
			icon_border_radius: { type: 'int', default: 100 },
			icon_border_spacing: { type: 'int', default: 50 },
			icon_link: { type: 'string' },
			animation_type: { type: 'string' },
		},
		edit: PortoSingleIcon,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 10. Porto Interactive Banner
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		BlockControls = wpBlockEditor.BlockControls,
		BlockAlignmentToolbar = wpBlockEditor.BlockAlignmentToolbar,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		MediaUpload = wpBlockEditor.MediaUpload,
		el = wpElement.createElement,
		Component = wpElement.Component,
		SelectControl = wpComponents.SelectControl,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		IconButton = wpComponents.IconButton,
		PanelBody = wpComponents.PanelBody;

	const PortoInteractiveBanner = function ( props ) {
		var attrs = props.attributes,
			clientId = props.clientId;

		var inspectorControls = el( InspectorControls, null,
			el( PanelBody, {
				title: __( 'Deprecated', 'porto-functionality' ),
				initialOpen: false,
			},
				el( TextControl, {
					label: __( 'Title', 'porto-functionality' ),
					value: attrs.banner_title,
					onChange: ( value ) => { props.setAttributes( { banner_title: value } ); },
				} ),
				el( TextareaControl, {
					label: __( 'Description', 'porto-functionality' ),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} )
			),
			el( PanelBody, {
				title: __( 'Banner Settings', 'porto-functionality' ),
				initialOpen: true,
			},
				el(
					'div',
					{ className: 'components-base-control' },
					el(
						'p',
						{ className: 'mb-0' },
						__( 'Banner Image', 'porto-functionality' )
					),
					el( MediaUpload, {
						allowedTypes: [ 'image' ],
						label: 'Banner Image',
						value: attrs.banner_image,
						onSelect: function onSelect( image ) {
							return props.setAttributes( { banner_image_url: image.url, banner_image: image.id } );
						},
						render: function render( _ref ) {
							var open = _ref.open;
							return el( IconButton, {
								className: 'components-toolbar__control',
								label: __( 'Banner Image', 'porto-functionality' ),
								icon: 'edit',
								onClick: open
							} );
						}
					} ),
					el( IconButton, {
						className: 'components-toolbar__control',
						label: __( 'Remove image', 'porto-functionality' ),
						icon: 'no',
						onClick: function onClick() {
							return props.setAttributes( { banner_image_url: undefined, banner_image: undefined } );
						}
					} ),
				),
				attrs.banner_image && el( ToggleControl, {
					label: __( 'Lazy Load Image', 'porto-functionality' ),
					checked: props.attributes.lazyload,
					onChange: ( value ) => { props.setAttributes( { lazyload: value } ); },
					help: __( 'If you have this element in Porto Carousel, please check "Lazy Load" option in Porto Carousel element.', 'porto-functionality' )
				} ),
				el( ToggleControl, {
					label: __( 'Add Container', 'porto-functionality' ),
					checked: props.attributes.add_container,
					onChange: ( value ) => { props.setAttributes( { add_container: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Min Height', 'porto-functionality' ),
					value: props.attributes.min_height,
					onChange: ( value ) => { props.setAttributes( { min_height: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Parallax', 'porto-functionality' ),
					help: __( 'Enter parallax speed ratio if you want to use parallax effect. (Note: Default value is 1.5, min value is 1. Leave empty if you don\'t want.)', 'porto-functionality' ),
					value: attrs.parallax,
					onChange: ( value ) => { props.setAttributes( { parallax: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Banner Effect', 'porto-functionality' ),
					value: attrs.banner_effect,
					options: [ { label: __( 'None', 'porto-functionality' ), value: '' }, { label: __( 'kenBurnsToRight', 'porto-functionality' ), value: 'kenBurnsToRight' }, { label: __( 'kenBurnsToLeft', 'porto-functionality' ), value: 'kenBurnsToLeft' }, { label: __( 'kenBurnsToLeftTop', 'porto-functionality' ), value: 'kenBurnsToLeftTop' }, { label: __( 'kenBurnsToRightTop', 'porto-functionality' ), value: 'kenBurnsToRightTop' } ],
					onChange: ( value ) => { props.setAttributes( { banner_effect: value } ); },
				} ),
				'' != attrs.banner_effect && el( RangeControl, {
					label: __( 'Banner Effect Duration (s)', 'porto-functionality' ),
					value: attrs.effect_duration,
					min: 0,
					max: 1000,
					onChange: ( value ) => { props.setAttributes( { effect_duration: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Particle Effect', 'porto-functionality' ),
					value: attrs.particle_effect,
					options: [ { label: __( 'None', 'porto-functionality' ), value: '' }, { label: __( 'Snowfall', 'porto-functionality' ), value: 'snowfall' }, { label: __( 'Sparkle', 'porto-functionality' ), value: 'sparkle' } ],
					onChange: ( value ) => { props.setAttributes( { particle_effect: value } ); },
				} ),			
				el( TextControl, {
					label: __( 'Link', 'porto-functionality' ),
					value: attrs.banner_link,
					onChange: ( value ) => { props.setAttributes( { banner_link: value } ); },
				} ),
				attrs.banner_title && el( RangeControl, {
					label: __( 'Title Font Size', 'porto-functionality' ),
					value: attrs.banner_title_font_size,
					min: 12,
					max: 80,
					onChange: ( value ) => { props.setAttributes( { banner_title_font_size: value } ); },
				} ),
				el( SelectControl, {
					label: __( 'Hover Effect', 'porto-functionality' ),
					value: attrs.banner_style,
					options: [ 
						{ label: __( 'None', 'porto-functionality' ), value: '' }, 
						{ label: __( 'Zoom', 'porto-functionality' ), value: 'zoom' }, 
						{ label: __( 'Effect 1', 'porto-functionality' ), value: 'effect-1' },
						{ label: __( 'Effect 2', 'porto-functionality' ), value: 'effect-2' }, 
						{ label: __( 'Effect 3', 'porto-functionality' ), value: 'effect-3' }, 
						{ label: __( 'Effect 4', 'porto-functionality' ), value: 'effect-4' },
					],
					onChange: ( value ) => { props.setAttributes( { banner_style: value } ); },
				} ),
				attrs.banner_title && el( PanelColorSettings, {
					title: __( 'Title Color Settings', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Color', 'porto-functionality' ),
						value: attrs.banner_color_title,
						onChange: function onChange( value ) {
							return props.setAttributes( { banner_color_title: value } );
						}
					} ]
				} ),
				attrs.banner_desc && el( PanelColorSettings, {
					title: __( 'Description Color Settings', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Color', 'porto-functionality' ),
						value: attrs.banner_color_desc,
						onChange: function onChange( value ) {
							return props.setAttributes( { banner_color_desc: value } );
						}
					} ]
				} ),
				el( PanelColorSettings, {
					title: __( 'Background Color Settings', 'porto-functionality' ),
					initialOpen: false,
					colorSettings: [ {
						label: __( 'Color', 'porto-functionality' ),
						value: attrs.banner_color_bg,
						onChange: function onChange( value ) {
							return props.setAttributes( { banner_color_bg: value } );
						}
					} ]
				} ),
				el( RangeControl, {
					label: __( 'Image Opacity', 'porto-functionality' ),
					value: attrs.image_opacity,
					min: 0.0,
					max: 1.0,
					step: 0.1,
					onChange: ( value ) => { props.setAttributes( { image_opacity: value } ); },
				} ),
				el( RangeControl, {
					label: __( 'Image Opacity on Hover', 'porto-functionality' ),
					value: attrs.image_opacity_on_hover,
					min: 0.0,
					max: 1.0,
					step: 0.1,
					onChange: ( value ) => { props.setAttributes( { image_opacity_on_hover: value } ); },
				} ),
			)
		);

		var title_bg = {}, banner_style_inline = {}, img_style = {}, banner_title_style_inline = {}, banner_desc_style_inline = {};

		if ( attrs.banner_title_font_size ) {
			banner_title_style_inline.fontSize = attrs.banner_title_font_size;
		}
		if ( attrs.banner_color_bg ) {
			banner_style_inline.backgroundColor = attrs.banner_color_bg;
		}
		if ( attrs.min_height ) {
			let unit = attrs.min_height.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.min_height += 'px';
			}
			banner_style_inline.minHeight = attrs.min_height;
		}

		if ( attrs.banner_color_title ) {
			banner_title_style_inline.color = attrs.banner_color_title;
		}

		if ( attrs.banner_color_desc ) {
			banner_desc_style_inline.color = attrs.banner_color_desc;
		}

		if ( attrs.image_opacity && attrs.image_opacity != attrs.image_opacity_on_hover && attrs.image_opacity !== 1.0 ) {
			img_style.opacity = attrs.image_opacity;
		}

	    const backgroundStyle = { style: {} }
	    if ( attrs.banner_effect != '' || attrs.particle_effect == '' && attrs.banner_image_url ) {
	      backgroundStyle.style.backgroundImage = `url(${ attrs.banner_image_url })`
	      backgroundStyle.style.backgroundSize = `cover`
	      backgroundStyle.style.animationDuration = `${attrs.effect_duration}s`
	    }

		var wrapperAttrs = {
			className: 'porto-ibanner' + ( attrs.banner_style ? ' porto-ibe-' + attrs.banner_style : '' ) + ( attrs.className ? ' ' + attrs.className : '' ),
			style: banner_style_inline,
		};
		var renderControls = el(
			'div',
			wrapperAttrs,
			( attrs.banner_effect || attrs.particle_effect ) && el(
				'div',
				{ className: 'banner-effect-wrapper' },
				el(
					'div',
					{ className: `banner-effect${ attrs.banner_effect ? ' ' + attrs.banner_effect : ''}`, ...backgroundStyle },
					attrs.particle_effect && el(
						'div',
						{ className: `particle-effect${attrs.particle_effect ? ' ' + attrs.particle_effect : ''}` }
					)
				)
			),
			attrs.banner_image_url && el(
				'img',
				{ className: 'porto-ibanner-img', src: attrs.banner_image_url }
			),
			el(
				'div',
				{ className: 'porto-ibanner-desc', style: title_bg },
				attrs.banner_title && el(
					'h2',
					{ className: 'porto-ibanner-title', style: banner_title_style_inline },
					attrs.banner_title
				),
				attrs.content && el(
					'div',
					{ className: 'porto-ibanner-content', style: banner_desc_style_inline },
					attrs.content
				),
				el( InnerBlocks, { allowedBlocks: [ 'porto/porto-interactive-banner-layer' ] } ),
			),
			attrs.banner_link && el( 'a', { className: 'porto-ibanner-link', href: attrs.banner_link } )
		);

		return [
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-interactive-banner', {
		title: 'Porto Interactive Banner',
		icon: 'porto',
		category: 'porto',
		attributes: {
			banner_title: { type: 'string' },
			banner_desc: { type: 'string' },
			banner_image: { type: 'int' },
			banner_image_url: { type: 'string' },
			lazyload: { type: 'boolean' },
			add_container: { type: 'boolean' },
			min_height: { type: 'string' },
			parallax: { type: 'string', },
			banner_effect: { type: 'string', default: '' },
			effect_duration: { type: 'int', default: 30 },
			particle_effect: { type: 'string', default: '' },
			image_opacity: { type: 'float', default: 1 },
			image_opacity_on_hover: { type: 'float', default: 1 },
			banner_style: { type: 'string' },
			banner_title_font_size: { type: 'int' },
			banner_color_bg: { type: 'string' },
			banner_color_title: { type: 'string' },
			banner_color_desc: { type: 'string' },
			banner_link: { type: 'string' },
		},
		supports: {
			align: [ 'wide', 'full' ],
		},
		edit: PortoInteractiveBanner,
		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );


/**
 * 11. Porto Interactive Banner layer
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		el = wpElement.createElement,
		Component = wpElement.Component,
		useEffect = wpElement.useEffect,
		TextControl = wpComponents.TextControl,
		RangeControl = wpComponents.RangeControl;

	const PortoInteractiveBannerLayer = function ( props ) {
		useEffect(
			() => {
				const clientId = props.clientId,
					elem = document.getElementById( 'block-' + clientId ),
					inner_elem = elem.getElementsByClassName( 'block-editor-inner-blocks' );
				if ( inner_elem.length ) {
					inner_elem[ 0 ].style.width = '100%';
				}
			},
			[ props.attributes.width, props.attributes.height, props.attributes.horizontal, props.attributes.vertical ],
		);

		var attrs = props.attributes;

		var inspectorControls = el( InspectorControls, null,
			el( TextControl, {
				label: __( 'Width', 'porto-functionality' ),
				value: attrs.width,
				onChange: ( value ) => { props.setAttributes( { width: value } ); },
			} ),
			el(
				'p',
				{},
				__( 'For example: 50%, 100px, 100rem, 50vw, etc.', 'porto-functionality' ),
			),
			el( TextControl, {
				label: __( 'Height', 'porto-functionality' ),
				value: attrs.height,
				onChange: ( value ) => { props.setAttributes( { height: value } ); },
			} ),
			el(
				'p',
				{},
				__( 'For example: 50%, 100px, 100rem, 50vw, etc.', 'porto-functionality' ),
			),
			el( RangeControl, {
				label: __( 'Horizontal Position', 'porto-functionality' ),
				value: attrs.horizontal,
				min: -50,
				max: 150,
				step: 1,
				onChange: ( value ) => { props.setAttributes( { horizontal: value } ); },
			} ),
			el(
				'p',
				{},
				__( '50 is center, 0 is left and 100 is right.', 'porto-functionality' ),
			),
			el( RangeControl, {
				label: __( 'vertical Position', 'porto-functionality' ),
				value: attrs.vertical,
				min: -50,
				max: 150,
				step: 1,
				onChange: ( value ) => { props.setAttributes( { vertical: value } ); },
			} ),
			el(
				'p',
				{},
				__( '50 is middle, 0 is top and 100 is bottom.', 'porto-functionality' ),
			),
			el( TextControl, {
				label: __( 'Animation Type', 'porto-functionality' ),
				value: attrs.animation_type,
				onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
			} ),
			el(
				'p',
				{ style: { marginTop: -20 } },
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
			el( TextControl, {
				label: __( 'Animation Delay (ms)', 'porto-functionality' ),
				value: attrs.animation_delay,
				onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Animation Duration (ms)', 'porto-functionality' ),
				value: attrs.animation_duration,
				onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
			} )
		);

		let extra_style = {};
		if ( 50 == Number( attrs.horizontal ) ) {
			extra_style.display = 'flex';
			extra_style.justifyContent = 'center';
			extra_style.left = '0';
			extra_style.right = '';
			extra_style.width = '100%';
			if ( 50 == Number( attrs.vertical ) ) {
				extra_style.top = '0';
				extra_style.bottom = '0';
				extra_style.right = '0';
				extra_style.marginLeft = 'auto';
				extra_style.marginRight = 'auto';
				extra_style.alignItems = 'center';
			}
		} else if ( 50 > Number( attrs.horizontal ) ) {
			extra_style.left = Number( attrs.horizontal ) + '%';
			extra_style.right = '';
		} else {
			extra_style.right = ( 100 - Number( attrs.horizontal ) ) + '%';
			extra_style.left = '';
		}

		if ( 50 == Number( attrs.vertical ) ) {
			if ( 50 != Number( attrs.horizontal ) ) {
				extra_style.display = 'flex';
				extra_style.alignItems = 'center';
				extra_style.top = '0';
				extra_style.bottom = 0;
			}
		} else if ( 50 > Number( attrs.vertical ) ) {
			extra_style.top = Number( attrs.vertical ) + '%';
		} else {
			extra_style.bottom = ( 100 - Number( attrs.vertical ) ) + '%';
		}

		if ( attrs.width ) {
			extra_style.width = attrs.width;
		} else if ( !extra_style.width ) {
			extra_style.width = 'auto';
		}
		if ( attrs.height ) {
			extra_style.height = attrs.height;
		} else {
			extra_style.height = '';
		}

		var renderControls = el(
			'div',
			{ className: 'porto-ibanner-layer' + ( attrs.className ? ' ' + attrs.className : '' ), style: extra_style },
			el( InnerBlocks )
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-interactive-banner-layer', {
		title: 'Porto Interactive Banner Layer',
		icon: 'porto',
		category: 'porto',
		parent: [ 'porto/porto-interactive-banner' ],
		attributes: {
			width: { type: 'string' },
			height: { type: 'string' },
			horizontal: { type: 'int', default: 50 },
			vertical: { type: 'int', default: 50 },
			animation_type: { type: 'string' },
			animation_duration: { type: 'string' },
			animation_delay: { type: 'string' },
		},
		edit: PortoInteractiveBannerLayer,
		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 12. Porto Woocommerce Products
 */
function _makeConsumableArray( arr ) {
	if ( Array.isArray( arr ) ) {
		for ( var i = 0, arr2 = Array( arr.length ); i < arr.length; i++ ) {
			arr2[ i ] = arr[ i ];
		}
		return arr2;
	} else {
		return Array.from( arr );
	}
}
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash, apiFetch ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		BlockControls = wpBlockEditor.BlockControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		MediaUpload = wpBlockEditor.MediaUpload,
		el = wpElement.createElement,
		Component = wpElement.Component,
		SelectControl = wpComponents.SelectControl,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Toolbar = wpComponents.Toolbar,
		CheckboxControl = wpComponents.CheckboxControl,
		PanelBody = wpComponents.PanelBody;

	class PortoProducts extends Component {
		constructor() {
			super( ...arguments );

			this.state = {
				categoriesList: [],
				products: [],
				query: '',
			};

			this.initSlider = this.initSlider.bind( this );
			this.fetchProducts = this.fetchProducts.bind( this );
			this.getQuery = this.getQuery.bind( this );
		}

		componentDidMount() {
			this.fetchProducts();
		}

		componentDidUpdate( prevProps, prevState ) {
			const _this = this,
				categoriesList = _this.state.categoriesList,
				attrs = _this.props.attributes,
				clientId = _this.props.clientId,
				$wrap = jQuery( '#block-' + clientId + ' .products-container' );

			if ( 'selected' === attrs.category_type && 0 === categoriesList.length ) {
				wp.apiFetch( { path: '/wc/v2/products/categories?per_page=99' } ).then( function ( obj ) {
					_this.setState( { categoriesList: obj } );
				} );
			}

			if ( $wrap.data( 'owl.carousel' ) && ( 'products-slider' != attrs.view || _this.getQuery() !== _this.state.query ) ) {
				portoDestroyCarousel( $wrap );
			} else if ( $wrap.data( 'isotope' ) && ( 'creative' != attrs.view || _this.getQuery() !== _this.state.query ) ) {
				$wrap.isotope( 'destroy' );
			}

			if ( 'products-slider' == attrs.view && _this.state.products.length && ( 'products-slider' !== prevProps.attributes.view || prevState.products !== _this.state.products || attrs.columns !== prevProps.attributes.columns || attrs.navigation !== prevProps.attributes.navigation || attrs.pagination !== prevProps.attributes.pagination || attrs.dots_pos !== prevProps.attributes.dots_pos || attrs.nav_pos !== prevProps.attributes.nav_pos || attrs.nav_pos2 !== prevProps.attributes.nav_pos2 || attrs.nav_type !== prevProps.attributes.nav_type || attrs.count !== prevProps.attributes.count ) ) {
				if ( $wrap.data( 'owl.carousel' ) ) {
					portoDestroyCarousel( $wrap );
				}
				_this.initSlider();
			} else if ( 'creative' == attrs.view && _this.state.products.length && ( 'creative' !== prevProps.attributes.view || prevState.products !== _this.state.products || attrs.grid_layout !== prevProps.attributes.grid_layout || attrs.grid_height !== prevProps.grid_height || attrs.spacing !== prevProps.attributes.spacing ) ) {
				if ( $wrap.data( 'isotope' ) ) {
					$wrap.isotope( 'destroy' );
				}
				$wrap.children().each( function ( i ) {
					if ( !( this instanceof HTMLElement ) ) {
						Object.setPrototypeOf( this, HTMLElement.prototype );
					}
				} );
				$wrap.isotope( {
					itemSelector: '.product-col',
					masonry: { 'columnWidth': '.grid-col-sizer' }
				} );
				jQuery.ajax( {
					url: porto_block_vars.ajax_url,
					data: {
						action: 'porto_load_creative_layout_style',
						nonce: porto_block_vars.nonce,
						layout: attrs.grid_layout,
						grid_height: attrs.grid_height,
						spacing: attrs.spacing,
						selector: '#block-' + clientId
					},
					type: 'post',
					success: function ( res ) {
						$wrap.prev( 'style' ).remove();
						jQuery( res ).insertBefore( $wrap );
						$wrap.isotope( 'layout' );
					}
				} );
			}

			if ( _this.getQuery() !== _this.state.query ) {
				_this.fetchProducts();
			}
		}

		initSlider() {
			const attrs = this.props.attributes,
				clientId = this.props.clientId;
			jQuery( '#block-' + clientId + ' .products-container' ).owlCarousel( {
				items: Number( attrs.columns ),
				nav: attrs.navigation,
				dots: attrs.pagination,
				navText: [ "", "" ],
			} );
		}

		getQuery() {
			var attrs = this.props.attributes,
				columns = attrs.columns,
				status = attrs.status;

			var query = {};
			if ( attrs.count ) {
				query.per_page = attrs.count;
			} else if ( 'creative' == attrs.view && porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ] ) {
				query.per_page = porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ].length;
			}

			if ( attrs.category_type === 'selected' ) {
				query.category = attrs.categories.join( ',' );
			}
			if ( 'featured' === status ) {
				query.featured = 1;
			} else if ( 'on_sale' === status ) {
				query.on_sale = 1;
			} else if ( 'pre_order' === status ) {
				query.pre_order = 1;
			}

			if ( attrs.ids ) {
				query.include = attrs.ids.trim();
				query.orderby = 'include';
			} else {
				if ( 'total_sales' == attrs.orderby ) {
					query.orderby = 'popularity';
				} else {
					query.orderby = attrs.orderby;
				}
				query.order = attrs.order;
			}

			var query_string = '?',
				_iteratorNormalCompletion = true,
				_didIteratorError = false,
				_iteratorError = undefined;

			try {
				for ( var _iterator = Object.keys( query )[ Symbol.iterator ](), _step; !( _iteratorNormalCompletion = ( _step = _iterator.next() ).done ); _iteratorNormalCompletion = true ) {
					var key = _step.value;

					query_string += key + '=' + query[ key ] + '&';
				}
			} catch ( err ) {
				_didIteratorError = true;
				_iteratorError = err;
			} finally {
				try {
					if ( !_iteratorNormalCompletion && _iterator.return ) {
						_iterator.return();
					}
				} finally {
					if ( _didIteratorError ) {
						throw _iteratorError;
					}
				}
			}

			var endpoint = '/portowc/v1/products' + query_string;
			return endpoint;
		}

		setCategories( catID, isAdd ) {
			var props = this.props,
				attrs = props.attributes,
				setAttributes = props.setAttributes;
			var categories = attrs.categories;


			if ( isAdd ) {
				categories = [].concat( _makeConsumableArray( categories ), [ catID ] );
			} else {
				categories = categories.filter( function ( cat ) {
					return cat !== catID;
				} );
			}
			setAttributes( { category: categories.join( ',' ), categories: categories } );
		}

		fetchProducts() {
			var _this = this;
			var query = this.getQuery();

			_this.setState( {
				query: query
			} );
			apiFetch( { path: query } ).then( function ( products ) {
				_this.setState( {
					products: products,
				} );
			} );
		}

		render() {
			var _this = this,
				props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				categoriesList = this.state.categoriesList,
				setAttributes = props.setAttributes;

			var viewControls = [ {
				icon: 'grid-view',
				title: __( 'Grid', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'grid' } );
				},
				isActive: attrs.view === 'grid'
			}, {
				icon: 'list-view',
				title: __( 'List', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'list' } );
				},
				isActive: attrs.view === 'list'
			}, {
				icon: 'slides',
				title: __( 'Slider', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'products-slider' } );
				},
				isActive: attrs.view === 'products-slider'
			}, {
				icon: 'media-spreadsheet',
				title: __( 'Creative Grid', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'creative' } );
				},
				isActive: attrs.view === 'creative'
			} ];

			const grid_layouts = [];
			for ( var i = 1; i <= 14; i++ ) {
				grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
			}

			var inspectorControls = el( InspectorControls, null,

				el(
					PanelBody,
					{ title: __( 'Selector', 'porto-functionality' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Title', 'porto-functionality' ),
						value: attrs.title,
						onChange: function ( value ) { setAttributes( { title: value } ); },
					} ),
					attrs.title && el( SelectControl, {
						label: __( 'Title Border Style', 'porto-functionality' ),
						value: attrs.title_border_style,
						options: [ { label: __( 'No Border', 'porto-functionality' ), value: '' }, { label: __( 'Bottom Border', 'porto-functionality' ), value: 'border-bottom' }, { label: __( 'Middle Border', 'porto-functionality' ), value: 'border-middle' } ],
						onChange: ( value ) => { setAttributes( { title_border_style: value } ); },
					} ),
					el( SelectControl, {
						label: __( 'Product Status', 'porto-functionality' ),
						value: attrs.status,
						options: porto_block_vars.status_values,
						onChange: function onChange( value ) {
							return setAttributes( { status: value } );
						}
					} ),
					el( SelectControl, {
						label: __( 'Category', 'porto-functionality' ),
						value: attrs.category_type,
						options: [ { label: __( 'All', 'porto-functionality' ), value: '' }, { label: __( 'Selected', 'porto-functionality' ), value: 'selected' } ],
						onChange: function onChange( value ) {
							return setAttributes( { category_type: value } );
						}
					} ),
					el( PortoAjaxSelect2Control, {
						label: __( 'Product IDs', 'porto-functionality' ),
						value: attrs.ids,
						option: 'product',
						multiple: '1',
						onChange: function onChange( value ) {
							return setAttributes( { ids: value } );
						}
					} ),
					attrs.category_type === 'selected' && el(
						'div',
						{ className: 'porto-categories-list' },
						categoriesList.map( function ( cat, index ) {
							return el( CheckboxControl, {
								key: index,
								label: [ cat.name, el(
									'span',
									{ key: 'cat-count', style: { fontSize: 'small', color: '#888', marginLeft: 5 } },
									'(',
									cat.count,
									')'
								) ],
								checked: attrs.categories.indexOf( cat.id ) > -1,
								onChange: function onChange( checked ) {
									return _this.setCategories( cat.id, checked );
								}
							} );
						} )
					),
					el( RangeControl, {
						label: __( 'Per page', 'porto-functionality' ),
						value: attrs.count,
						min: 1,
						max: 100,
						onChange: ( value ) => { setAttributes( { count: value } ); },
					} ),
					el( SelectControl, {
						label: __( 'Order by', 'porto-functionality' ),
						value: attrs.orderby,
						options: porto_block_vars.orderby_values,
						onChange: ( value ) => { setAttributes( { orderby: value } ); },
					} ),
					attrs.orderby != 'rating' && el( SelectControl, {
						label: __( 'Order', 'porto-functionality' ),
						value: attrs.order,
						options: [ { label: __( 'Descending', 'porto-functionality' ), value: 'desc' }, { label: __( 'Ascending', 'porto-functionality' ), value: 'asc' } ],
						onChange: ( value ) => { setAttributes( { order: value } ); },
					} ),
				),
				el(
					PanelBody,
					{ title: __( 'Layout', 'porto-functionality' ), initialOpen: false },
					el( SelectControl, {
						label: __( 'Show Sort by', 'porto-functionality' ),
						value: attrs.show_sort,
						multiple: true,
						options: [ { label: __( 'All', 'porto-functionality' ), value: 'all' }, { label: __( 'Popular', 'porto-functionality' ), value: 'popular' }, { label: __( 'Date', 'porto-functionality' ), value: 'date' }, { label: __( 'Rating', 'porto-functionality' ), value: 'rating' }, { label: __( 'On Sale', 'porto-functionality' ), value: 'onsale' } ],
						onChange: ( value ) => { setAttributes( { show_sort: value } ); },
					} ),
					-1 !== attrs.show_sort.indexOf('popular') && el( TextControl, {
						label: __( 'Title for "Sort by Popular"', 'porto-functionality' ),
						value: attrs.show_sales_title,
						onChange: function ( value ) { setAttributes( { show_sales_title: value } ); },
					} ),
					-1 !== attrs.show_sort.indexOf('date') && el( TextControl, {
						label: __( 'Title for "Sort by Date"', 'porto-functionality' ),
						value: attrs.show_new_title,
						onChange: function ( value ) { setAttributes( { show_new_title: value } ); },
					} ),
					-1 !== attrs.show_sort.indexOf('rating') && el( TextControl, {
						label: __( 'Title for "Sort by Rating"', 'porto-functionality' ),
						value: attrs.show_rating_title,
						onChange: function ( value ) { setAttributes( { show_rating_title: value } ); },
					} ),
					-1 !== attrs.show_sort.indexOf('onsale') && el( TextControl, {
						label: __( 'Title for "On Sale"', 'porto-functionality' ),
						value: attrs.show_onsale_title,
						onChange: function ( value ) { setAttributes( { show_onsale_title: value } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Show category filter', 'porto-functionality' ),
						checked: attrs.category_filter,
						onChange: ( value ) => { setAttributes( { category_filter: value } ); },
					} ),
					( attrs.category_filter || attrs.show_sort.length > 0 ) && el( SelectControl, {
						label: __( 'Filter Style', 'porto-functionality' ),
						value: attrs.filter_style,
						options: [ { label: __( 'Vertical', 'porto-functionality' ), value: '' }, { label: __( 'Horizontal', 'porto-functionality' ), value: 'horizontal' } ],
						onChange: ( value ) => { setAttributes( { filter_style: value } ); },
					} ),
					attrs.view != 'products-slider' && el( SelectControl, {
						label: __( 'Pagination Style', 'porto-functionality' ),
						value: attrs.pagination_style,
						options: [ { label: __( 'No pagination', 'porto-functionality' ), value: '' }, { label: __( 'Default', 'porto-functionality' ), value: 'default' }, { label: __( 'Load more', 'porto-functionality' ), value: 'load_more' } ],
						onChange: ( value ) => { setAttributes( { pagination_style: value } ); },
					} ),
					( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( RangeControl, {
						label: __( 'Columns', 'porto-functionality' ),
						value: attrs.columns,
						min: 1,
						max: 8,
						onChange: ( value ) => { setAttributes( { columns: value } ); },
					} ),
					( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( SelectControl, {
						label: __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
						value: attrs.columns_mobile,
						options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: '1', value: '1' }, { label: '2', value: '2' }, { label: '3', value: '3' } ],
						onChange: ( value ) => { setAttributes( { columns_mobile: value } ); },
					} ),
					( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( SelectControl, {
						label: __( 'Column Width', 'porto-functionality' ),
						value: attrs.column_width,
						options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( '1/1 of content width', 'porto-functionality' ), value: '1' }, { label: __( '1/2 of content width', 'porto-functionality' ), value: '2' }, { label: __( '1/3 of content width', 'porto-functionality' ), value: '3' }, { label: __( '1/4 of content width', 'porto-functionality' ), value: '4' }, { label: __( '1/5 of content width', 'porto-functionality' ), value: '5' }, { label: __( '1/6 of content width', 'porto-functionality' ), value: '6' }, { label: __( '1/7 of content width', 'porto-functionality' ), value: '7' }, { label: __( '1/8 of content width', 'porto-functionality' ), value: '8' } ],
						onChange: ( value ) => { setAttributes( { column_width: value } ); },
					} ),
					'creative' == attrs.view && el(
						PortoImageChoose, {
						label: __( 'Creative Grid Layout', 'porto-functionality' ),
						options: grid_layouts,
						value: attrs.grid_layout,
						onChange: ( value ) => {
							setAttributes( { grid_layout: value } );
						}
					}
					),
					'creative' == attrs.view && el( TextControl, {
						label: __( 'Grid Height', 'porto-functionality' ),
						value: attrs.grid_height,
						onChange: ( value ) => { setAttributes( { grid_height: value } ); },
					} ),
					( 'creative' == attrs.view || 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( RangeControl, {
						label: __( 'Column Spacing (px)', 'porto-functionality' ),
						value: attrs.spacing,
						min: 0,
						max: 100,
						onChange: ( value ) => { setAttributes( { spacing: value } ); },
					} ),
					'list' != attrs.view && el( SelectControl, {
						label: __( 'Add Links Position', 'porto-functionality' ),
						value: 'creative' == attrs.view && 'onimage' != attrs.addlinks_pos && 'onimage2' != attrs.addlinks_pos && 'onimage3' != attrs.addlinks_pos ? 'onimage' : attrs.addlinks_pos,
						options: 'creative' == attrs.view ? [ { label: __( 'On Image', 'porto-functionality' ), value: 'onimage' }, { label: __( 'On Image with Overlay 1', 'porto-functionality' ), value: 'onimage2' }, { label: __( 'On Image with Overlay 2', 'porto-functionality' ), value: 'onimage3' } ] : porto_block_vars.product_layouts,
						onChange: ( value ) => { setAttributes( { addlinks_pos: value } ); },
					} ),
					( 'divider' == attrs.view || 'grid' == attrs.view || 'products-slider' == attrs.view || 'list' === attrs.view ) && el( SelectControl, {
						label: __( 'Image Size', 'porto-functionality' ),
						value: attrs.image_size,
						options: porto_block_vars.image_sizes,
						onChange: ( value ) => { setAttributes( { image_size: value } ); },
					} ),
					'products-slider' == attrs.view && el( ToggleControl, {
						label: __( 'Show Slider Navigation', 'porto-functionality' ),
						checked: attrs.navigation,
						onChange: ( value ) => { setAttributes( { navigation: value } ); },
					} ),
					'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
						label: __( 'Nav Position', 'porto-functionality' ),
						value: attrs.nav_pos,
						options: [ { label: __( 'Middle', 'porto-functionality' ), value: '' }, { label: __( 'Middle of Images', 'porto-functionality' ), value: 'nav-center-images-only' }, { label: __( 'Top', 'porto-functionality' ), value: 'show-nav-title' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'nav-bottom' } ],
						onChange: ( value ) => { setAttributes( { nav_pos: value } ); },
					} ),
					'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
						label: __( 'Nav Inside?', 'porto-functionality' ),
						value: attrs.nav_pos2,
						options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Inside', 'porto-functionality' ), value: 'nav-pos-inside' }, { label: __( 'Outside', 'porto-functionality' ), value: 'nav-pos-outside' } ],
						onChange: ( value ) => { setAttributes( { nav_pos2: value } ); },
					} ),
					'products-slider' == attrs.view && attrs.navigation && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
						label: __( 'Nav Type', 'porto-functionality' ),
						value: attrs.nav_type,
						options: porto_block_vars.carousel_nav_types,
						onChange: ( value ) => { setAttributes( { nav_type: value } ); },
					} ),
					'products-slider' == attrs.view && attrs.navigation && el( ToggleControl, {
						label: __( 'Show Nav on Hover', 'porto-functionality' ),
						checked: attrs.show_nav_hover,
						onChange: ( value ) => { setAttributes( { show_nav_hover: value } ); },
					} ),
					'products-slider' == attrs.view && el( ToggleControl, {
						label: __( 'Show Slider Pagination', 'porto-functionality' ),
						checked: attrs.pagination,
						onChange: ( value ) => { setAttributes( { pagination: value } ); },
					} ),
					'products-slider' == attrs.view && attrs.pagination && el( SelectControl, {
						label: __( 'Dots Position', 'porto-functionality' ),
						value: attrs.dots_pos,
						options: [ { label: __( 'Bottom', 'porto-functionality' ), value: '' }, { label: __( 'Top right', 'porto-functionality' ), value: 'show-dots-title-right' } ],
						onChange: ( value ) => { setAttributes( { dots_pos: value } ); },
					} ),
					'products-slider' == attrs.view && attrs.pagination && el( SelectControl, {
						label: __( 'Dots Style', 'porto-functionality' ),
						value: attrs.dots_style,
						options: [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Circle inner dot', 'porto-functionality' ), 'value': 'dots-style-1' } ],
						onChange: ( value ) => { setAttributes( { dots_style: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Animation Type', 'porto-functionality' ),
						value: attrs.animation_type,
						onChange: ( value ) => { setAttributes( { animation_type: value } ); },
						help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
					} ),
					el(
						'p',
						{ style: { marginTop: -20 } },
						el(
							'a',
							{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
							'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
						),
					),
					el( TextControl, {
						label: __( 'Animation Delay (ms)', 'porto-functionality' ),
						value: attrs.animation_delay,
						onChange: ( value ) => { setAttributes( { animation_delay: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Animation Duration (ms)', 'porto-functionality' ),
						value: attrs.animation_duration,
						onChange: ( value ) => { setAttributes( { animation_duration: value } ); },
					} )
				)
			);

			const product_layout = ( attrs.addlinks_pos ? attrs.addlinks_pos : porto_block_vars.product_type );
			let product_layout_cls = '';
			if ( 'list' != attrs.view ) {
				if ( 'creative' == attrs.view && 'onimage' != product_layout && 'onimage2' != product_layout && 'onimage3' != product_layout ) {
					product_layout_cls = 'onimage';
				} else if ( 'onhover' == product_layout ) {
					product_layout_cls = 'default show-links-hover';
				} else if ( 'outimage_aq_onimage2' == product_layout ) {
					product_layout_cls = 'outimage_aq_onimage with-padding';
				} else if ( 'quantity' == product_layout ) {
					product_layout_cls = 'wq_onimage';
				} else {
					product_layout_cls = product_layout;
				}
			}

			let classes = '',
				products_attrs = {};
			if ( 'grid' == attrs.view ) {
				classes += ' columns-' + attrs.columns + ' ' + attrs.view;
			} else if ( 'products-slider' == attrs.view ) {
				classes += ' products-slider owl-carousel owl-loaded';
				if ( attrs.navigation ) {
					if ( attrs.nav_pos ) classes += ' ' + attrs.nav_pos;
					if ( attrs.nav_type ) classes += ' ' + attrs.nav_type;
				}
				if ( attrs.pagination ) {
					if ( attrs.dots_pos ) {
						classes += ' ' + attrs.dots_pos;
					}
					if ( attrs.dots_style ) {
						classes += ' ' + attrs.dots_style;
					}
				}
				if ( attrs.navigation ) {
					if ( attrs.nav_pos2 ) {
						classes += ' ' + attrs.nav_pos2;
					}
				}
				if ( attrs.show_nav_hover ) {
					classes += ' show-nav-hover';
				}
			} else if ( 'creative' == attrs.view ) {
				classes += ' grid-creative';
			} else {
				classes += ' ' + attrs.view;
			}
			if ( 'list' != attrs.view ) {
				classes += ' pcols-ls-2 pcols-xs-' + Math.min( 3, attrs.columns ) + ' pcols-lg-' + attrs.columns;
			}
			if ( attrs.className ) {
				classes += ' ' + attrs.className;
			}
			products_attrs.className = 'products products-container ' + classes;

			let item_classes = porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ];

			const renderProducts = this.state.products.map( function ( product, index ) {
				let image = null, item_class = '';
				if ( product.images.length ) {
					image = el( 'img', { src: product.images[ 0 ].src } );
				} else if ( typeof porto_swatches_params != 'undefined' && porto_swatches_params.placeholder_src ) {
					image = el( 'img', { src: porto_swatches_params.placeholder_src } );
				}

				if ( 'creative' == attrs.view && typeof item_classes[ index % item_classes.length ] != 'undefined' ) {
					item_class += ' ' + item_classes[ index % item_classes.length ];
				}
				if ( 'products-slider' == attrs.view ) {
					item_class += ' owl-item';
				}

				const add_links_html = el( 'div', { className: 'add-links-wrap' },
					el(
						'div',
						{ className: 'add-links clearfix' },
						el(
							'span',
							{ className: 'add_to_cart_button button' },
							__( 'Add to cart', 'porto-functionality' )
						),
						porto_block_vars.product_show_wl && el(
							'div',
							{ className: 'yith-wcwl-add-to-wishlist' },
							el(
								'div',
								{},
								el( 'span', { className: 'add_to_wishlist' } )
							)
						),
						( 'list' == attrs.view || 'onimage2' != attrs.addlinks_pos ) && el(
							'span',
							{ className: 'quickview' },
							__( 'Quick View', 'porto-functionality' )
						)
					),
					'list' != attrs.view && 'onimage2' == attrs.addlinks_pos && el(
						'span',
						{ className: 'quickview' },
						__( 'Quick View', 'porto-functionality' )
					)
				);
				return el(
					'li',
					{ className: 'product-col product product-' + product_layout_cls + item_class },
					el(
						'div',
						{ className: 'product-inner' },
						el(
							'div',
							{ className: 'product-image' },
							el(
								'div',
								{ className: 'inner' },
								image
							),
							'list' != attrs.view && ( 'outimage_aq_onimage' == product_layout || 'outimage_aq_onimage2' == product_layout || 'awq_onimage' == product_layout || 'onimage2' == product_layout || 'onimage3' == product_layout || 'quantity' == product_layout ) && el(
								'div',
								{ className: 'links-on-image' },
								add_links_html
							)
						),
						el(
							'div',
							{ className: 'product-content' },
							'list' != attrs.view && porto_block_vars.product_show_wl && ( 'outimage_aq_onimage' === product_layout || 'outimage_aq_onimage2' == product_layout ) && el(
								'div',
								{ className: 'yith-wcwl-add-to-wishlist' },
								el( 'span', { className: 'add_to_wishlist' } )
							),
							porto_block_vars.product_show_cats && el(
								'div',
								{ className: 'category-list' },
								product.categories.map( function ( cat, i ) {
									return el(
										'span',
										{ dangerouslySetInnerHTML: { __html: ( i ? ', ' : '' ) + cat.name } }
									);
								} )
							),
							el(
								'h3',
								{ className: 'product-title' },
								product.name
							),
							el(
								'div',
								{ className: 'rating-wrap' },
								el(
									'div',
									{ className: 'star-rating', 'title': parseFloat( product.average_rating ) },
									el(
										'span',
										{ style: { width: parseFloat( product.average_rating ) * 20 + '%' } }
									)
								)
							),
							'list' == attrs.view && el(
								'div',
								{ className: 'description', dangerouslySetInnerHTML: { __html: product.short_description } }
							),
							el( 'div', { className: 'price', dangerouslySetInnerHTML: { __html: product.price_html } } ),
							( 'list' == attrs.view || 'default' == product_layout || 'onhover' == product_layout || 'outimage' == product_layout || 'onimage' == product_layout || 'quantity' == product_layout ) && add_links_html
						)
					)
				);
			} );
			let wrapCls = 'porto-products';
			if ( attrs.title_border_style ) {
				wrapCls += ' title-' + attrs.title_border_style;
			}
			if ( attrs.show_sort.length || attrs.category_filter ) {
				wrapCls += ' show-category';
				if ( attrs.filter_style ) {
					wrapCls += ' filter-' + attrs.filter_style;
				} else {
					wrapCls += ' filter-vertical';
				}
			}

			var renderControls = el(
				'div',
				{ className: wrapCls },
				attrs.title && el(
					'h2',
					{ className: 'products-title section-title', dangerouslySetInnerHTML: { __html: attrs.title.replace( /<script.*?\/script>/g, '' ) } }
				),
				( attrs.show_sort.length || attrs.category_filter ) && el(
					'div',
					{ className: 'products-filter' },
					el(
						'h4',
						{ className: 'section-title' },
						__( 'Sort By', 'porto-functionality' )
					),
					attrs.show_sort.length && el(
						'ul',
						{ className: 'product-categories' },
						-1 !== attrs.show_sort.indexOf( 'all' ) && el(
							'li',
							{ className: 'current' },
							el(
								'a',
								{},
								__( 'All', 'porto-functionality' )
							)
						),
						-1 !== attrs.show_sort.indexOf( 'popular' ) && el(
							'li',
							{},
							el(
								'a',
								{},
								attrs.show_sales_title ? attrs.show_sales_title : __( 'Best Seller', 'porto-functionality' )
							)
						),
						-1 !== attrs.show_sort.indexOf( 'date' ) && el(
							'li',
							{},
							el(
								'a',
								{},
								attrs.show_new_title ? attrs.show_new_title : __( 'New Arrivals', 'porto-functionality' )
							)
						),
						-1 !== attrs.show_sort.indexOf( 'rating' ) && el(
							'li',
							{},
							el(
								'a',
								{},
								attrs.show_rating_title ? attrs.show_rating_title : __( 'Best Rating', 'porto-functionality' )
							)
						),
						-1 !== attrs.show_sort.indexOf( 'onsale' ) && el(
							'li',
							{},
							el(
								'a',
								{},
								attrs.show_onsale_title ? attrs.show_onsale_title : __( 'Best Rating', 'porto-functionality' )
							)
						)
					)
				),
				el(
					'div',
					{ className: 'woocommerce' },
					el(
						'ul',
						products_attrs,
						'products-slider' === attrs.view && el(
							'div',
							{ className: 'owl-stage-outer' },
							el(
								'div',
								{ className: 'owl-stage' },
								renderProducts
							)
						),
						'products-slider' !== attrs.view && renderProducts,
						'creative' == attrs.view && el(
							'li',
							{ className: 'grid-col-sizer' }
						)
					)
				),
			);

			return [
				el(
					BlockControls,
					null,
					el( Toolbar, { controls: viewControls } )
				),
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-products', {
		title: 'Porto Products',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: { type: 'string' },
			title_border_style: { type: 'string' },
			view: { type: 'string', default: 'grid' },
			category_type: { type: 'string' },
			category: { type: 'string' },
			categories: { type: 'array', default: [] },
			ids: { type: 'string' },
			status: { type: 'string' },
			count: { type: 'int' },
			orderby: { type: 'string', default: 'title' },
			order: { type: 'string', default: 'asc' },
			columns: { type: 'int', default: 4 },
			columns_mobile: { type: 'string' },
			column_width: { type: 'string' },
			grid_layout: { type: 'int', default: 1 },
			grid_height: { type: 'string', default: '600px' },
			spacing: { type: 'int' },
			addlinks_pos: { type: 'string' },
			image_size: { type: 'string' },
			navigation: { type: 'boolean', default: true },
			show_nav_hover: { type: 'boolean', default: false },
			pagination: { type: 'boolean', default: false },
			nav_pos: { type: 'string', default: '' },
			nav_pos2: { type: 'string' },
			nav_type: { type: 'string' },
			dots_pos: { type: 'string' },
			dots_style: { type: 'string' },
			category_filter: { type: 'boolean' },
			pagination_style: { type: 'string' },
			animation_type: { type: 'string' },
			animation_duration: { type: 'int' },
			animation_delay: { type: 'int' },
			show_sort: { type: 'array', default: [] },
			show_sales_title: { type: 'string' },
			show_new_title: { type: 'string' },
			show_rating_title: { type: 'string' },
			show_onsale_title: { type: 'string' },
			filter_style: { type: 'string' },
		},
		edit: PortoProducts,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash, wp.apiFetch );


/**
 * 13. Porto Heading
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		RichText = wpBlockEditor.RichText,
		BlockControls = wpBlockEditor.BlockControls,
		BlockAlignmentToolbar = wpBlockEditor.BlockAlignmentToolbar,
		el = wpElement.createElement,
		Component = wpElement.Component,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		SelectControl = wpComponents.SelectControl;

	const PortoHeading = function ( props ) {
		var attrs = props.attributes,
			clientId = props.clientId;

		var inspectorControls = el( InspectorControls, {},
			el( TextareaControl, {
				label: __( 'Title', 'porto-functionality' ),
				value: attrs.title,
				onChange: ( value ) => { props.setAttributes( { title: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Enable typewriter effect', 'porto-functionality' ),
				checked: attrs.enable_typewriter,
				onChange: ( value ) => { props.setAttributes( { enable_typewriter: value } ); },
			} ),
			attrs.enable_typewriter && el( TextControl, {
				label: __( 'Animation Name e.g: typeWriter, fadeIn and so on.', 'porto-functionality' ),
				value: attrs.typewriter_animation,
				onChange: ( value ) => { props.setAttributes( { typewriter_animation: value } ); },
			} ),
			attrs.enable_typewriter && el( TextControl, {
				label: __( 'Start Delay(ms)', 'porto-functionality' ),
				value: attrs.typewriter_delay,
				onChange: ( value ) => { props.setAttributes( { typewriter_delay: value } ); },
			} ),
			attrs.enable_typewriter && el( TextControl, {
				label: __( 'Input min width that can work. (px)', 'porto-functionality' ),
				value: attrs.typewriter_width,
				onChange: ( value ) => { props.setAttributes( { typewriter_width: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Tag', 'porto-functionality' ),
				value: attrs.tag,
				options: [ { label: __( 'H1', 'PORTO-FUNCTIONALITY' ), value: 'h1' }, { label: __( 'H2', 'PORTO-FUNCTIONALITY' ), value: 'h2' }, { label: __( 'H3', 'PORTO-FUNCTIONALITY' ), value: 'h3' }, { label: __( 'H4', 'PORTO-FUNCTIONALITY' ), value: 'h4' }, { label: __( 'H5', 'PORTO-FUNCTIONALITY' ), value: 'h5' }, { label: __( 'H6', 'PORTO-FUNCTIONALITY' ), value: 'h6' }, { label: __( 'div', 'porto-functionality' ), value: 'div' }, { label: __( 'p', 'porto-functionality' ), value: 'p' }, { label: __( 'span', 'porto-functionality' ), value: 'span' } ],
				onChange: ( value ) => { props.setAttributes( { tag: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Link', 'porto-functionality' ),
				value: attrs.link,
				onChange: ( value ) => { props.setAttributes( { link: value } ); },
			} ),
			el( PortoTypographyControl, {
				label: __( 'Typography', 'porto-functionality' ),
				value: { fontFamily: attrs.font_family, fontSize: attrs.font_size, fontWeight: attrs.font_weight, textTransform: attrs.text_transform, lineHeight: attrs.line_height, letterSpacing: attrs.letter_spacing, color: attrs.color },
				options: {},
				onChange: ( value ) => {
					if ( typeof value.fontFamily != 'undefined' ) {
						props.setAttributes( { font_family: value.fontFamily } );
					}
					if ( typeof value.fontSize != 'undefined' ) {
						props.setAttributes( { font_size: value.fontSize } );
					}
					if ( typeof value.fontWeight != 'undefined' ) {
						props.setAttributes( { font_weight: value.fontWeight } );
					}
					if ( typeof value.textTransform != 'undefined' ) {
						props.setAttributes( { text_transform: value.textTransform } );
					}
					if ( typeof value.lineHeight != 'undefined' ) {
						props.setAttributes( { line_height: value.lineHeight } );
					}
					if ( typeof value.letterSpacing != 'undefined' ) {
						props.setAttributes( { letter_spacing: value.letterSpacing } );
					}
					if ( typeof value.color != 'undefined' ) {
						props.setAttributes( { color: value.color } );
					} else {
						props.setAttributes( { color: '' } );
					}
				},
			} ),
			el( TextControl, {
				label: __( 'Animation Type', 'porto-functionality' ),
				value: attrs.animation_type,
				help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
				onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
			} ),
			el(
				'p',
				{ style: { marginTop: -20 } },
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
			el( TextControl, {
				label: __( 'Animation Delay (ms)', 'porto-functionality' ),
				value: attrs.animation_delay,
				onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Animation Duration (ms)', 'porto-functionality' ),
				value: attrs.animation_duration,
				onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
			} ),
		);

		let wrapper_style = {}, style_inline = {};

		if ( attrs.font_family ) {
			style_inline.fontFamily = attrs.font_family;
		}
		if ( attrs.font_size ) {
			let unit = attrs.font_size.replace( /[0-9.]/g, '' );
			if ( !unit ) {
				attrs.font_size += 'px';
			}
			style_inline.fontSize = attrs.font_size;
		}
		if ( attrs.font_weight ) {
			style_inline.fontWeight = Number( attrs.font_weight );
		}
		if ( attrs.text_transform ) {
			style_inline.textTransform = attrs.text_transform;
		}
		if ( attrs.line_height ) {
			let unit = attrs.line_height.replace( /[0-9.]/g, '' );
			if ( !unit && attrs.line_height > 3 ) {
				attrs.line_height += 'px';
			}
			style_inline.lineHeight = attrs.line_height;
		}
		if ( attrs.letter_spacing ) {
			style_inline.letterSpacing = attrs.letter_spacing;
		}
		if ( attrs.color ) {
			style_inline.color = attrs.color;
		}
		style_inline.textAlign = attrs.alignment;
		var type_plugin = {};
		if( attrs.enable_typewriter ) {
			type_plugin = { 'data-plugin-animated-letters': '', 'data-plugin-options': { startDelay: 0, minWindowWidth: 0 } };
			if( attrs.typewriter_delay ) {
				type_plugin['data-plugin-options']['startDelay'] = parseInt( attrs.typewriter_delay );
			}
			if( attrs.typewriter_width ) {
				type_plugin['data-plugin-options']['minWindowWidth'] = parseInt( attrs.typewriter_width );	
			}
			if( attrs.typewriter_animation ) {
				type_plugin['data-plugin-options']['animationName'] = parseInt( attrs.typewriter_animation );		
			}
		}
		var renderControls = el(
			RichText,
			{
				key: 'editable',
				tagName: attrs.tag,
				className: 'porto-heading' + ( attrs.className ? ' ' + attrs.className : '' ),
				style: style_inline,
				...type_plugin,
				onChange: function ( value ) {
					return props.setAttributes( { title: value } );
				},
				value: attrs.title,
			}
		);

		return [
			el( BlockControls, null,
				el( BlockAlignmentToolbar, {
					value: attrs.alignment, onChange: function onChange( value ) {
						return props.setAttributes( { alignment: value } );
					}
				} )
			),
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-heading', {
		title: 'Porto Heading',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			enable_typewriter: {
				type: 'boolean',
			},
			typewriter_animation: {
				type: 'string',
				default: 'fadeIn',
			},
			typewriter_delay: {
				type: 'string',
			},
			typewriter_width: {
				type: 'string',
			},
			font_family: {
				type: 'string',
			},
			font_size: {
				type: 'string',
			},
			font_weight: {
				type: 'int',
			},
			text_transform: {
				type: 'string',
			},
			line_height: {
				type: 'string',
			},
			letter_spacing: {
				type: 'string',
			},
			color: {
				type: 'string',
			},
			tag: {
				type: 'string',
				default: 'h2',
			},
			link: {
				type: 'string',
			},
			alignment: {
				type: 'string',
			},
			animation_type: {
				type: 'string',
			},
			animation_duration: {
				type: 'int',
				default: 1000,
			},
			animation_delay: {
				type: 'int',
				default: 0,
			}
		},
		edit: PortoHeading,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 13. Porto Button
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		RichText = wpBlockEditor.RichText,
		BlockControls = wpBlockEditor.BlockControls,
		BlockAlignmentToolbar = wpBlockEditor.BlockAlignmentToolbar,
		el = wpElement.createElement,
		Component = wpElement.Component,
		TextControl = wpComponents.TextControl,
		ToggleControl = wpComponents.ToggleControl,
		SelectControl = wpComponents.SelectControl;

	const PortoButton = function ( props ) {
		var attrs = props.attributes;

		var inspectorControls = el( InspectorControls, {},
			el( TextControl, {
				label: __( 'Title', 'porto-functionality' ),
				value: attrs.title,
				onChange: ( value ) => { props.setAttributes( { title: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Link', 'porto-functionality' ),
				value: attrs.link,
				onChange: ( value ) => { props.setAttributes( { link: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Layout', 'porto-functionality' ),
				value: attrs.layout,
				options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Modern', 'porto-functionality' ), value: 'modern' }, { label: __( 'Outline', 'porto-functionality' ), value: 'borders' } ],
				onChange: ( value ) => { props.setAttributes( { layout: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Size', 'porto-functionality' ),
				value: attrs.size,
				options: [ { label: __( 'Extra Small', 'porto-functionality' ), value: 'xs' }, { label: __( 'Small', 'porto-functionality' ), value: 'sm' }, { label: __( 'Medium', 'porto-functionality' ), value: 'md' }, { label: __( 'Large', 'porto-functionality' ), value: 'lg' }, { label: __( 'Extra Large', 'porto-functionality' ), value: 'xl' } ],
				onChange: ( value ) => { props.setAttributes( { size: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Is Full Width?', 'porto-functionality' ),
				checked: attrs.is_block,
				onChange: ( value ) => { props.setAttributes( { is_block: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Skin', 'porto-functionality' ),
				value: attrs.skin,
				options: [ { label: __( 'Default', 'porto-functionality' ), value: 'default' }, { label: __( 'Primary', 'porto-functionality' ), value: 'primary' }, { label: __( 'Secondary', 'porto-functionality' ), value: 'secondary' }, { label: __( 'Tertiary', 'porto-functionality' ), value: 'tertiary' }, { label: __( 'Quaternary', 'porto-functionality' ), value: 'quaternary' }, { label: __( 'Dark', 'porto-functionality' ), value: 'dark' }, { label: __( 'Light', 'porto-functionality' ), value: 'light' } ],
				onChange: ( value ) => { props.setAttributes( { skin: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Icon Class (ex: fas fa-pencil-alt)', 'porto-functionality' ),
				value: attrs.icon_cls,
				onChange: ( value ) => { props.setAttributes( { icon_cls: value } ); },
			} ),
			attrs.icon_cls && el( SelectControl, {
				label: __( 'Icon Position', 'porto-functionality' ),
				value: attrs.icon_pos,
				options: [ { label: __( 'Left', 'porto-functionality' ), value: 'left' }, { label: __( 'Right', 'porto-functionality' ), value: 'right' } ],
				onChange: ( value ) => { props.setAttributes( { icon_pos: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Animation Type', 'porto-functionality' ),
				help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
				value: attrs.animation_type,
				onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
			} ),
			el(
				'p',
				{ style: { marginTop: -20 } },
				el(
					'a',
					{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
					'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
				),
			),
			el( TextControl, {
				label: __( 'Animation Delay (ms)', 'porto-functionality' ),
				value: attrs.animation_delay,
				onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
			} ),
			el( TextControl, {
				label: __( 'Animation Duration (ms)', 'porto-functionality' ),
				value: attrs.animation_duration,
				onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
			} ),
		);

		let style_inline = {};

		let btn_classes = ' btn-' + escape( attrs.size );
		if ( 'custom' != attrs.skin ) {
			btn_classes += ' btn-' + escape( attrs.skin );
		}
		if ( attrs.layout ) {
			btn_classes += ' btn-' + escape( attrs.layout );
		}

		if ( attrs.is_block ) {
			btn_classes += ' btn-block';
		}

		if ( attrs.icon_cls ) {
			btn_classes += ' btn-icon';
			if ( 'right' == attrs.icon_pos ) {
				btn_classes += ' btn-icon-right';
			}
		}
		style_inline.textAlign = attrs.align;

		var renderControls = el(
			'div',
			{ className: 'porto-button', style: style_inline },
			el(
				'button',
				{ className: 'btn' + btn_classes },
				attrs.icon_cls && 'left' == attrs.icon_pos && el(
					'i',
					{ className: attrs.icon_cls }
				),
				attrs.title,
				attrs.icon_cls && 'right' == attrs.icon_pos && el(
					'i',
					{ className: attrs.icon_cls }
				)
			),
		);

		return [
			el( BlockControls, null,
				el( BlockAlignmentToolbar, {
					value: attrs.align, onChange: function onChange( value ) {
						return props.setAttributes( { align: value } );
					}
				} )
			),
			inspectorControls,
			renderControls,
		];
	};

	registerBlockType( 'porto/porto-button', {
		title: 'Porto Button',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
				default: 'Click Here',
			},
			link: {
				type: 'string',
			},
			layout: {
				type: 'string',
			},
			size: {
				type: 'string',
				default: 'md',
			},
			is_block: {
				type: 'boolean',
			},
			skin: {
				type: 'string',
			},
			icon_cls: {
				type: 'string',
			},
			icon_pos: {
				type: 'string',
				default: 'left',
			},
			align: {
				type: 'string',
			},
			animation_type: { type: 'string' },
			animation_duration: { type: 'int' },
			animation_delay: { type: 'int' }
		},
		edit: PortoButton,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 15. Porto Section
 *
 * Section block which has background image, parallax image, background video, inner container, etc.
 *
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		MediaUpload = wpBlockEditor.MediaUpload,
		IconButton = wpComponents.IconButton,
		el = wpElement.createElement,
		Component = wpElement.Component,
		RangeControl = wpComponents.RangeControl,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		ToggleControl = wpComponents.ToggleControl,
		SelectControl = wpComponents.SelectControl;

	const PortoSection = function ( props ) {
		var attrs = props.attributes;

		var inspectorControls = el( InspectorControls, {},
			el( ToggleControl, {
				label: __( 'Add Container?', 'porto-functionality' ),
				checked: attrs.add_container,
				onChange: ( value ) => { props.setAttributes( { add_container: value } ); },
			} ),
			el( PanelColorSettings, {
				title: __( 'Background Color', 'porto-functionality' ),
				initialOpen: false,
				colorSettings: [ {
					label: __( 'Background Color', 'porto-functionality' ),
					value: attrs.bg_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { bg_color: value } );
					}
				} ]
			} ),
			el( MediaUpload, {
				allowedTypes: [ 'image' ],
				value: attrs.bg_img,
				onSelect: function onSelect( image ) {
					return props.setAttributes( { bg_img_url: image.url, bg_img: image.id } );
				},
				render: function render( _ref ) {
					var open = _ref.open;
					return el( IconButton, {
						className: 'components-toolbar__control',
						label: __( 'Background Image', 'porto-functionality' ),
						icon: 'edit',
						onClick: open
					} );
				}
			} ),
			attrs.bg_img && el( IconButton, {
				className: 'components-toolbar__control',
				label: __( 'Remove image', 'porto-functionality' ),
				icon: 'no',
				onClick: function onClick() {
					return props.setAttributes( { bg_img_url: undefined, bg_img: undefined } );
				}
			} ),
			el( TextControl, {
				label: __( 'Background Image URL', 'porto-functionality' ),
				value: attrs.bg_img_url,
				onChange: ( value ) => { props.setAttributes( { bg_img_url: value } ); },
			} ),
			attrs.bg_img_url && el( SelectControl, {
				label: __( 'Background Repeat', 'porto-functionality' ),
				value: attrs.bg_repeat,
				options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'No Repeat', 'porto-functionality' ), value: 'no-repeat' }, { label: __( 'Repeat', 'porto-functionality' ), value: 'repeat' }, { label: __( 'Repeat X', 'porto-functionality' ), value: 'repeat-x' }, { label: __( 'Repeat Y', 'porto-functionality' ), value: 'repeat-y' } ],
				onChange: ( value ) => { props.setAttributes( { bg_repeat: value } ); },
			} ),
			attrs.bg_img_url && el( SelectControl, {
				label: __( 'Background Position', 'porto-functionality' ),
				value: attrs.bg_pos,
				options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Center Center', 'porto-functionality' ), value: 'center center' }, { label: __( 'Center Left', 'porto-functionality' ), value: 'center left' }, { label: __( 'Center Right', 'porto-functionality' ), value: 'center right' }, { label: __( 'Top Center', 'porto-functionality' ), value: 'top center' }, { label: __( 'Top Left', 'porto-functionality' ), value: 'top left' }, { label: __( 'Top Right', 'porto-functionality' ), value: 'top right' }, { label: __( 'Bottom Center', 'porto-functionality' ), value: 'bottom center' }, { label: __( 'Bottom Left', 'porto-functionality' ), value: 'bottom left' }, { label: __( 'Bottom Right', 'porto-functionality' ), value: 'bottom right' } ],
				onChange: ( value ) => { props.setAttributes( { bg_pos: value } ); },
			} ),
			attrs.bg_img_url && el( SelectControl, {
				label: __( 'Background Size', 'porto-functionality' ),
				value: attrs.bg_size,
				options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Cover', 'porto-functionality' ), value: 'cover' }, { label: __( 'Contain', 'porto-functionality' ), value: 'contain' }, { label: __( 'Auto', 'porto-functionality' ), value: 'auto' }, { label: __( '100% auto', 'porto-functionality' ), value: '100% auto' }, { label: __( 'auto 100%', 'porto-functionality' ), value: 'auto 100%' } ],
				onChange: ( value ) => { props.setAttributes( { bg_size: value } ); },
			} ),
			attrs.bg_img_url && el( RangeControl, {
				label: __( 'Parallax Speed', 'porto-functionality' ),
				value: attrs.parallax_speed,
				min: 1.0,
				max: 3.0,
				step: 0.1,
				onChange: ( value ) => { props.setAttributes( { parallax_speed: value } ); },
			} ),
			attrs.bg_img_url && el(
				'p',
				{},
				__( 'Enter parallax speed ratio if you want to use parallax effect. (Note: Standard value is 1.5, min value is 1. Leave empty if you don\'t want.)' ),
			),
			el( TextControl, {
				label: __( 'Background Video URL (mp4)', 'porto-functionality' ),
				value: attrs.bg_video,
				onChange: ( value ) => { props.setAttributes( { bg_video: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Tag', 'porto-functionality' ),
				value: attrs.tag,
				options: [ { label: __( 'section', 'porto-functionality' ), value: 'section' }, { label: __( 'div', 'porto-functionality' ), value: 'div' }, { label: __( 'article', 'porto-functionality' ), value: 'article' } ],
				onChange: ( value ) => { props.setAttributes( { tag: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Shape Divider Position', 'porto-functionality' ),
				value: attrs.top_bottom,
				options: [ { label: __( 'Top', 'porto-functionality' ), value: 'top' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'bottom' } ],
				onChange: ( value ) => { props.setAttributes( { top_bottom: value } ); }
			} ),
			attrs.top_bottom == 'top' && el( SelectControl, {
				label: __( 'Top Divider Type', 'porto-functionality' ),
				value: attrs.top_divider_type,
				options: porto_block_vars.divider_type,
				onChange: ( value ) => { props.setAttributes( { top_divider_type: value } ); }
			} ),
			attrs.top_bottom == 'top' && attrs.top_divider_type == 'custom' && el( TextareaControl, {
				label: __( 'Please writer your svg code.', 'porto-functionality' ),
				value: attrs.top_divider_custom,
				onChange: ( value ) => { props.setAttributes( { top_divider_custom: value } ); }
			} ),
			attrs.top_bottom == 'top' && attrs.top_divider_type != 'none' && el( PanelColorSettings, {
				title: __( 'Divider Color', 'porto-functionality' ),
				initialOpen: false,
				colorSettings: [ {
					label: __( 'Color', 'porto-functionality' ),
					value: attrs.top_divider_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { top_divider_color: value } );
					}
				} ]
			} ),
			attrs.top_bottom == 'top' && attrs.top_divider_type != 'none' && el( TextControl, {
				label: __( 'Top Divider Height', 'porto-functionality' ),
				value: attrs.top_divider_height,
				help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
				onChange: ( value ) => { props.setAttributes( { top_divider_height: value } ); }
			} ),
			attrs.top_bottom == 'top' && attrs.top_divider_type != 'none' && el( ToggleControl, {
				label: __( 'Top Divider Flip', 'porto-functionality' ),
				checked: attrs.top_divider_flip,
				onChange: ( value ) => { props.setAttributes( { top_divider_flip: value } ); }
			} ),
			attrs.top_bottom == 'top' && attrs.top_divider_type != 'none' && el( ToggleControl, {
				label: __( 'Top Divider Invert', 'porto-functionality' ),
				checked: attrs.top_divider_invert,
				onChange: ( value ) => { props.setAttributes( { top_divider_invert: value } ); }
			} ),
			attrs.top_bottom == 'top' && attrs.top_divider_type != 'none' && el( TextControl, {
				label: __( 'Top Shape Divder Class', 'porto-functionality' ),
				value: attrs.top_divider_class,
				onChange: ( value ) => { props.setAttributes( { top_divider_class: value } ); }
			} ),

			attrs.top_bottom == 'bottom' && el( SelectControl, {
				label: __( 'Bottom Divider Type', 'porto-functionality' ),
				value: attrs.bottom_divider_type,
				options: porto_block_vars.divider_type,
				onChange: ( value ) => { props.setAttributes( { bottom_divider_type: value } ); }
			} ),
			attrs.top_bottom == 'bottom' && attrs.bottom_divider_type == 'custom' && el( TextareaControl, {
				label: __( 'Please writer your svg code.', 'porto-functionality' ),
				value: attrs.bottom_divider_custom,
				onChange: ( value ) => { props.setAttributes( { bottom_divider_custom: value } ); }
			} ),
			attrs.top_bottom == 'bottom' && attrs.bottom_divider_type != 'none' && el( PanelColorSettings, {
				title: __( 'Divider Color', 'porto-functionality' ),
				initialOpen: false,
				colorSettings: [ {
					label: __( 'Color', 'porto-functionality' ),
					value: attrs.bottom_divider_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { bottom_divider_color: value } );
					}
				} ]
			} ),
			attrs.top_bottom == 'bottom' && attrs.bottom_divider_type != 'none' && el( TextControl, {
				label: __( 'Bottom Divider Height', 'porto-functionality' ),
				value: attrs.bottom_divider_height,
				help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
				onChange: ( value ) => { props.setAttributes( { bottom_divider_height: value } ); }
			} ),
			attrs.top_bottom == 'bottom' && attrs.bottom_divider_type != 'none' && el( ToggleControl, {
				label: __( 'Bottom Divider Flip', 'porto-functionality' ),
				checked: attrs.bottom_divider_flip,
				onChange: ( value ) => { props.setAttributes( { bottom_divider_flip: value } ); }
			} ),
			attrs.top_bottom == 'bottom' && attrs.bottom_divider_type != 'none' && el( ToggleControl, {
				label: __( 'Bottom Divider Invert', 'porto-functionality' ),
				checked: attrs.bottom_divider_invert,
				onChange: ( value ) => { props.setAttributes( { bottom_divider_invert: value } ); }
			} ),
			attrs.top_bottom == 'bottom' && attrs.bottom_divider_type != 'none' && el( TextControl, {
				label: __( 'Bottom Shape Divder Class', 'porto-functionality' ),
				value: attrs.bottom_divider_class,
				onChange: ( value ) => { props.setAttributes( { bottom_divider_class: value } ); }
			} ),
		);

		let style_inline = {};
		if ( attrs.bg_color ) {
			style_inline.backgroundColor = attrs.bg_color;
		}
		if ( attrs.bg_img_url ) {
			style_inline.backgroundImage = 'url(' + attrs.bg_img_url + ')';
		}
		if ( attrs.bg_repeat ) {
			style_inline.backgroundRepeat = attrs.bg_repeat;
		}
		if ( attrs.bg_pos ) {
			style_inline.backgroundPosition = attrs.bg_pos;
		}
		if ( attrs.bg_size ) {
			style_inline.backgroundSize = attrs.bg_size;
		}

		style_inline.textAlign = attrs.align;

		const top_divider_attr = { style: {}, className: 'shape-divider' };
		if( attrs.top_divider_type != '' && attrs.top_divider_type != 'none' ) {
			if( attrs.top_divider_class ) 
				top_divider_attr.className += ` ${attrs.top_divider_class}`;
			if( attrs.top_divider_invert && attrs.top_divider_flip ) {
				top_divider_attr.className += ' shape-divider-reverse-xy';
			} else if( attrs.top_divider_invert ) {
				top_divider_attr.className += ' shape-divider-reverse-x';
			} else if( attrs.top_divider_flip ) {
				top_divider_attr.className += ' shape-divider-reverse-y';
			}
			if ( attrs.top_divider_height ) {
				let unit = attrs.top_divider_height.replace( /[0-9.]/g, '' );
				if ( !unit ) {
					attrs.top_divider_height += 'px';
				}
				top_divider_attr.style.height = attrs.top_divider_height;
			}
			if( attrs.top_divider_color ) {
				top_divider_attr.style.fill = attrs.top_divider_color;
			}
			if( attrs.top_divider_type == 'custom' ) {
				if( attrs.top_divider_custom )
					top_divider_attr.dangerouslySetInnerHTML = { __html: attrs.top_divider_custom };
			} else {
				top_divider_attr.dangerouslySetInnerHTML = { __html: porto_block_vars.shape_divider[attrs.top_divider_type] };
			}
		}

		const bottom_divider_attr = { style: {}, className: 'shape-divider shape-divider-bottom' };
		if( attrs.bottom_divider_type != '' && attrs.bottom_divider_type != 'none' ) {
			if( attrs.bottom_divider_class ) 
				bottom_divider_attr.className += ` ${attrs.bottom_divider_class}`;
			if( attrs.bottom_divider_invert && attrs.bottom_divider_flip ) {
				bottom_divider_attr.className += ' shape-divider-reverse-xy';
			} else if( attrs.bottom_divider_invert ) {
				bottom_divider_attr.className += ' shape-divider-reverse-x';
			} else if( attrs.bottom_divider_flip ) {
				bottom_divider_attr.className += ' shape-divider-reverse-y';
			}
			if ( attrs.bottom_divider_height ) {
				let unit = attrs.bottom_divider_height.replace( /[0-9.]/g, '' );
				if ( !unit ) {
					attrs.bottom_divider_height += 'px';
				}
				bottom_divider_attr.style.height = attrs.bottom_divider_height;
			}
			if( attrs.bottom_divider_color ) {
				bottom_divider_attr.style.fill = attrs.bottom_divider_color;
			}
			if( attrs.bottom_divider_type == 'custom' ) {
				if( attrs.bottom_divider_custom )
					bottom_divider_attr.dangerouslySetInnerHTML = { __html: attrs.bottom_divider_custom };
			} else {
				bottom_divider_attr.dangerouslySetInnerHTML = { __html: porto_block_vars.shape_divider[attrs.bottom_divider_type] };
			}
		}		

		const renderControls = el(
			attrs.tag,
			{ className: `vc_section porto-section${ ( attrs.top_divider_type != 'none' || attrs.bottom_divider_type != 'none' ) ? ' section-with-shape-divider' : '' }` + ( attrs.className ? ' ' + attrs.className : '' ), style: style_inline },
			( attrs.top_divider_type != '' && attrs.top_divider_type != 'none' ) && el(
				'div',
				top_divider_attr
			),
			! attrs.add_container && el( InnerBlocks ),
			attrs.add_container && el(
				'div',
				{ className: 'container' },
				el( InnerBlocks ),
			),
			( attrs.bottom_divider_type != '' && attrs.bottom_divider_type != 'none' ) && el(
				'div',
				bottom_divider_attr
			)			
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-section', {
		title: 'Porto Section',
		icon: 'porto',
		category: 'porto',
		attributes: {
			add_container: {
				type: 'boolean',
			},
			bg_color: {
				type: 'string',
			},
			bg_img: {
				type: 'int',
			},
			bg_img_url: {
				type: 'string',
			},
			bg_repeat: {
				type: 'string',
			},
			bg_pos: {
				type: 'string',
			},
			bg_size: {
				type: 'string',
			},
			parallax_speed: {
				type: 'float',
			},
			bg_video: {
				type: 'string',
			},
			tag: {
				type: 'string',
				default: 'section',
			},
			align: {
				type: 'string',
			},
			top_bottom: {
				type: 'string',
				default: 'top'
			},
			top_divider_type: {
				type: 'string',
				default: 'none'
			},
			top_divider_custom: {
				type: 'string'
			},
			top_divider_color: {
				type: 'string'
			},
			top_divider_height: {
				type: 'string'
			},
			top_divider_flip: {
				type: 'boolean'
			},
			top_divider_invert: {
				type: 'boolean'
			},
			top_divider_class: {
				type: 'string'
			},
			bottom_divider_type: {
				type: 'string',
				default: 'none'
			},
			bottom_divider_custom: {
				type: 'string'
			},
			bottom_divider_color: {
				type: 'string'
			},
			bottom_divider_height: {
				type: 'string'
			},
			bottom_divider_flip: {
				type: 'boolean'
			},
			bottom_divider_invert: {
				type: 'boolean'
			},
			bottom_divider_class: {
				type: 'string'
			}
		},
		supports: {
			align: [ 'full' ],
		},
		edit: PortoSection,
		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 16. Porto Woocommerce Product Categories
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash, apiFetch ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		BlockControls = wpBlockEditor.BlockControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		MediaUpload = wpBlockEditor.MediaUpload,
		el = wpElement.createElement,
		Component = wpElement.Component,
		PanelBody = wpComponents.PanelBody,
		SelectControl = wpComponents.SelectControl,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Toolbar = wpComponents.Toolbar,
		CheckboxControl = wpComponents.CheckboxControl;

	class PortoProductCategories extends Component {
		constructor() {
			super( ...arguments );

			this.state = {
				categoriesList: [],
				query: '',
			};

			this.initSlider = this.initSlider.bind( this );
			this.fetchCategories = this.fetchCategories.bind( this );
			this.getQuery = this.getQuery.bind( this );
		}

		fetchCategories() {
			let _this = this,
				query = _this.getQuery();

			wp.apiFetch( { path: query } ).then( function ( obj ) {
				_this.setState( { categoriesList: obj, query: query } );
			} );
		}

		componentDidMount() {
			this.fetchCategories();
			const _this = this;
			wp.apiFetch( { path: '/wc/v2/products/categories?per_page=99' } ).then( function ( obj ) {
				_this.setState( { allcategories: obj } );
			} );
		}

		componentDidUpdate( prevProps, prevState ) {
			const _this = this,
				categoriesList = _this.state.categoriesList,
				attrs = _this.props.attributes,
				clientId = _this.props.clientId,
				$wrap = jQuery( '#block-' + clientId + ' .products-container' );

			if ( $wrap.data( 'owl.carousel' ) && ( 'products-slider' != attrs.view || _this.getQuery() !== _this.state.query ) ) {
				portoDestroyCarousel( $wrap );
			} else if ( $wrap.data( 'isotope' ) && ( 'creative' != attrs.view || _this.getQuery() !== _this.state.query ) ) {
				$wrap.isotope( 'destroy' );
			}

			if ( 'products-slider' == attrs.view && categoriesList.length && ( 'products-slider' !== prevProps.attributes.view || prevState.categoriesList !== categoriesList || attrs.columns !== prevProps.attributes.columns || attrs.navigation !== prevProps.attributes.navigation || attrs.pagination !== prevProps.attributes.pagination || attrs.dots_pos !== prevProps.attributes.dots_pos || attrs.nav_pos !== prevProps.attributes.nav_pos || attrs.nav_pos2 !== prevProps.attributes.nav_pos2 || attrs.nav_type !== prevProps.attributes.nav_type || attrs.number !== prevProps.attributes.number ) ) {
				if ( $wrap.data( 'owl.carousel' ) ) {
					portoDestroyCarousel( $wrap );
				}
				_this.initSlider();
			} else if ( 'creative' == attrs.view && categoriesList.length && ( 'creative' !== prevProps.attributes.view || prevState.categoriesList !== categoriesList || attrs.grid_layout !== prevProps.attributes.grid_layout || attrs.grid_height !== prevProps.grid_height || attrs.spacing !== prevProps.attributes.spacing ) ) {
				if ( $wrap.data( 'isotope' ) ) {
					$wrap.isotope( 'destroy' );
				}
				$wrap.children().each( function ( i ) {
					if ( !( this instanceof HTMLElement ) ) {
						Object.setPrototypeOf( this, HTMLElement.prototype );
					}
				} );
				$wrap.isotope( {
					itemSelector: '.product-col',
					masonry: { 'columnWidth': '.grid-col-sizer' }
				} );
				jQuery.ajax( {
					url: porto_block_vars.ajax_url,
					data: {
						action: 'porto_load_creative_layout_style',
						nonce: porto_block_vars.nonce,
						layout: attrs.grid_layout,
						grid_height: attrs.grid_height,
						spacing: attrs.spacing,
						selector: '#block-' + clientId,
						item_selector: '.product-col'
					},
					type: 'post',
					success: function ( res ) {
						$wrap.prev( 'style' ).remove();
						jQuery( res ).insertBefore( $wrap );
						$wrap.isotope( 'layout' );
					}
				} );
			}

			if ( _this.getQuery() !== _this.state.query ) {
				_this.fetchCategories();
			}
		}

		initSlider() {
			const attrs = this.props.attributes,
				clientId = this.props.clientId;
			jQuery( '#block-' + clientId + ' .products-container' ).owlCarousel( {
				items: Number( attrs.columns ),
				nav: attrs.navigation,
				dots: attrs.pagination,
				navText: [ "", "" ],
			} );
		}

		getQuery() {
			let attrs = this.props.attributes,
				columns = attrs.columns,
				status = attrs.status,
				query_string = 'porto=1';

			if ( attrs.number ) {
				query_string += '&per_page=' + attrs.number;
			} else if ( 'creative' == attrs.view && porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ] ) {
				query_string += '&per_page=' + porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ].length;
			}
			if ( attrs.parent ) {
				query_string += '&parent=' + attrs.parent;
			}
			if ( attrs.ids ) {
				query_string += '&include=' + attrs.ids + '&orderby=include&order=asc';
			} else {
				if ( attrs.orderby ) {
					query_string += '&orderby=' + attrs.orderby;
				}
				query_string += '&order=' + attrs.order;
			}
			if ( attrs.image_size ) {
				query_string += '&image_size=' + attrs.image_size;
			}
			if ( attrs.hide_empty ) {
				query_string += '&hide_empty=1';
			}

			return '/wc/v3/products/categories?' + query_string;
		}

		render() {
			var _this = this,
				props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				categoriesList = this.state.categoriesList,
				setAttributes = props.setAttributes;

			var viewControls = [ {
				icon: 'grid-view',
				title: __( 'Grid', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'grid' } );
				},
				isActive: attrs.view === 'grid'
			}, {
				icon: 'slides',
				title: __( 'Slider', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'products-slider' } );
				},
				isActive: attrs.view === 'products-slider'
			}, {
				icon: 'media-spreadsheet',
				title: __( 'Creative Grid', 'porto-functionality' ),
				onClick: function onClick() {
					return setAttributes( { view: 'creative' } );
				},
				isActive: attrs.view === 'creative'
			} ];

			const grid_layouts = [];
			for ( var i = 1; i <= 14; i++ ) {
				grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
			}

			const allcategories = this.state.allcategories;
			const categories_options = [ { label: __( 'None', 'porto-functionality' ), value: '' }, { label: __( 'Display Top Level categories', 'porto-functionality' ), value: '0' } ];
			allcategories && allcategories.map( function ( cat, index ) {
				categories_options.push( { label: cat.name + ' (' + cat.count + ')', value: cat.id } );
			} );

			var inspectorControls = el( InspectorControls, null,
				el( PanelBody, {
					title: __( 'Categories Selector', 'porto-functionality' ),
					initialOpen: true,
				},
					el( TextControl, {
						label: __( 'Title', 'porto-functionality' ),
						value: attrs.title,
						onChange: function ( value ) { setAttributes( { title: value } ); },
					} ),
					el( SelectControl, {
						label: __( 'Parent Category', 'porto-functionality' ),
						value: attrs.parent,
						options: categories_options,
						onChange: ( value ) => { setAttributes( { parent: value } ); },
					} ),
					el(
						'p',
						{ className: '' },
						__( 'Categories', 'porto-functionality' )
					),
					el(
						'div',
						{ className: 'porto-categories-list' },
						allcategories && allcategories.map( function ( cat, index ) {
							if ( attrs.parent && parseInt( attrs.parent ) !== parseInt( cat.parent ) ) {
								return;
							}
							return el( CheckboxControl, {
								key: index,
								label: [ cat.name, el(
									'span',
									{ key: 'cat-count', style: { fontSize: 'small', color: '#888', marginLeft: 5 } },
									'(',
									cat.count,
									')'
								) ],
								checked: attrs.ids && attrs.ids.split( ',' ).indexOf( '' + cat.id ) > -1,
								onChange: ( is_add ) => {
									let ids_arr = attrs.ids ? attrs.ids.split( ',' ) : [];
									if ( is_add ) {
										ids_arr = [].concat( _makeConsumableArray( ids_arr ), [ cat.id ] );
									} else {
										ids_arr = ids_arr.filter( function ( c ) {
											return parseInt( c ) !== parseInt( cat.id );
										} );
									}
									return setAttributes( { ids: ids_arr.join( ',' ) } );
								}
							} );
						} )
					),
					el( RangeControl, {
						label: __( 'Number', 'porto-functionality' ),
						value: attrs.number,
						min: 1,
						max: 100,
						onChange: ( value ) => { setAttributes( { number: value } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Hide empty', 'porto-functionality' ),
						checked: attrs.hide_empty,
						onChange: ( value ) => { setAttributes( { hide_empty: value } ); },
					} ),
					el( SelectControl, {
						label: __( 'Order by', 'porto-functionality' ),
						value: attrs.orderby,
						options: [ { label: __( 'Title', 'porto-functionality' ), value: 'name' }, { label: __( 'ID', 'PORTO-FUNCTIONALITY' ), value: 'id' }, { label: __( 'Product Count', 'porto-functionality' ), value: 'count' }, { label: __( 'Description', 'porto-functionality' ), value: 'description' }, { label: __( 'Term Group', 'porto-functionality' ), value: 'term_group' } ],
						onChange: ( value ) => { setAttributes( { orderby: value } ); },
					} ),
					attrs.orderby != 'rating' && el( SelectControl, {
						label: __( 'Order', 'porto-functionality' ),
						value: attrs.order,
						options: [ { label: __( 'Descending', 'porto-functionality' ), value: 'desc' }, { label: __( 'Ascending', 'porto-functionality' ), value: 'asc' } ],
						onChange: ( value ) => { setAttributes( { order: value } ); },
					} )
				),
				el( PanelBody, {
					title: __( 'Categories Layout', 'porto-functionality' ),
					initialOpen: false,
				},
					'creative' == attrs.view && el(
						PortoImageChoose, {
						label: __( 'Creative Grid Layout', 'porto-functionality' ),
						options: grid_layouts,
						value: attrs.grid_layout,
						onChange: ( value ) => {
							setAttributes( { grid_layout: value } );
						}
					}
					),
					'creative' == attrs.view && el( TextControl, {
						label: __( 'Grid Height', 'porto-functionality' ),
						value: attrs.grid_height,
						onChange: ( value ) => { setAttributes( { grid_height: value } ); },
					} ),
					'creative' == attrs.view && el( RangeControl, {
						label: __( 'Column Spacing (px)', 'porto-functionality' ),
						value: attrs.spacing,
						min: 0,
						max: 100,
						onChange: ( value ) => { setAttributes( { spacing: value } ); },
					} ),
					( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( RangeControl, {
						label: __( 'Columns', 'porto-functionality' ),
						value: attrs.columns,
						min: 1,
						max: 8,
						onChange: ( value ) => { setAttributes( { columns: value } ); },
					} ),
					( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( SelectControl, {
						label: __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
						value: attrs.columns_mobile,
						options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: '1', value: '1' }, { label: '2', value: '2' }, { label: '3', value: '3' } ],
						onChange: ( value ) => { setAttributes( { columns_mobile: value } ); },
					} ),
					( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( SelectControl, {
						label: __( 'Column Width', 'porto-functionality' ),
						value: attrs.column_width,
						options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( '1/1 of content width', 'porto-functionality' ), value: '1' }, { label: __( '1/1 of content width', 'porto-functionality' ), value: '2' }, { label: __( '1/3 of content width', 'porto-functionality' ), value: '3' }, { label: __( '1/4 of content width', 'porto-functionality' ), value: '4' }, { label: __( '1/5 of content width', 'porto-functionality' ), value: '5' }, { label: __( '1/6 of content width', 'porto-functionality' ), value: '6' }, { label: __( '1/7 of content width', 'porto-functionality' ), value: '7' }, { label: __( '1/8 of content width', 'porto-functionality' ), value: '8' } ],
						onChange: ( value ) => { setAttributes( { column_width: value } ); },
					} ),

					el( SelectControl, {
						label: __( 'Text Position', 'porto-functionality' ),
						value: attrs.text_position,
						options: [ { label: __( 'Inner Middle Left', 'porto-functionality' ), value: 'middle-left' }, { label: __( 'Inner Middle Center', 'porto-functionality' ), value: 'middle-center' }, { label: __( 'Inner Middle Right', 'porto-functionality' ), value: 'middle-right' }, { label: __( 'Inner Bottom Left', 'porto-functionality' ), value: 'bottom-left' }, { label: __( 'Inner Bottom Center', 'porto-functionality' ), value: 'bottom-center' }, { label: __( 'Inner Bottom Right', 'porto-functionality' ), value: 'bottom-right' }, { label: __( 'Outside', 'porto-functionality' ), value: 'outside-center' } ],
						onChange: ( value ) => { setAttributes( { text_position: value } ); },
					} ),
					el( RangeControl, {
						label: __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
						value: attrs.overlay_bg_opacity,
						min: 0,
						max: 100,
						onChange: ( value ) => { props.setAttributes( { overlay_bg_opacity: value } ); },
					} ),
					el( SelectControl, {
						label: __( 'Text Color', 'porto-functionality' ),
						value: attrs.text_color,
						options: [ { label: __( 'Dark', 'porto-functionality' ), value: 'dark' }, { label: __( 'Light', 'porto-functionality' ), value: 'light' } ],
						onChange: ( value ) => { setAttributes( { text_color: value } ); },
					} ),

					el( SelectControl, {
						label: __( 'Media Type', 'porto-functionality' ),
						value: attrs.media_type,
						options: [ { label: __( 'Image', 'porto-functionality' ), value: '' }, { label: __( 'Icon', 'porto-functionality' ), value: 'icon' } ],
						onChange: ( value ) => { setAttributes( { media_type: value } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Display sub categories', 'porto-functionality' ),
						checked: attrs.show_sub_cats,
						onChange: ( value ) => { props.setAttributes( { show_sub_cats: value } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Display a featured product', 'porto-functionality' ),
						checked: attrs.show_featured,
						onChange: ( value ) => { props.setAttributes( { show_featured: value } ); },
						help: __( 'If you check this option, a featured product in each category will be displayed under the product category.', 'porto-functionality' ),
					} ),
					el( ToggleControl, {
						label: __( 'Hide products count', 'porto-functionality' ),
						checked: attrs.hide_count,
						onChange: ( value ) => { props.setAttributes( { hide_count: value } ); },
					} ),
					el( SelectControl, {
						label: __( 'Hover Effect', 'porto-functionality' ),
						value: attrs.hover_effect,
						options: [ { label: __( 'Normal', 'porto-functionality' ), value: '' }, { label: __( 'Display product count on hover', 'porto-functionality' ), value: 'show-count-on-hover' } ],
						onChange: ( value ) => { setAttributes( { hover_effect: value } ); },
					} ),
					'icon' != attrs.media_type && el( SelectControl, {
						label: __( 'Image Size', 'porto-functionality' ),
						value: attrs.image_size,
						options: porto_block_vars.image_sizes,
						onChange: ( value ) => { props.setAttributes( { image_size: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Animation Type', 'porto-functionality' ),
						value: attrs.animation_type,
						onChange: ( value ) => { setAttributes( { animation_type: value } ); },
						help: __( 'Please check this url to see animation types.', 'porto-functionality' ),
					} ),
					el(
						'p',
						{ style: { marginTop: -20 } },
						el(
							'a',
							{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
							'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
						),
					),
					el( TextControl, {
						label: __( 'Animation Delay (ms)', 'porto-functionality' ),
						value: attrs.animation_delay,
						onChange: ( value ) => { setAttributes( { animation_delay: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Animation Duration (ms)', 'porto-functionality' ),
						value: attrs.animation_duration,
						onChange: ( value ) => { setAttributes( { animation_duration: value } ); },
					} )
				),
				'products-slider' == attrs.view && el( PanelBody, {
					title: __( 'Slider Options', 'porto-functionality' ),
					initialOpen: false,
				},
					el( ToggleControl, {
						label: __( 'Show Slider Navigation', 'porto-functionality' ),
						checked: attrs.navigation,
						onChange: ( value ) => { setAttributes( { navigation: value } ); },
					} ),
					attrs.navigation && el( SelectControl, {
						label: __( 'Nav Position', 'porto-functionality' ),
						value: attrs.nav_pos,
						options: [ { label: __( 'Middle', 'porto-functionality' ), value: '' }, { label: __( 'Middle of Images', 'porto-functionality' ), value: 'nav-center-images-only' }, { label: __( 'Top', 'porto-functionality' ), value: 'show-nav-title' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'nav-bottom' } ],
						onChange: ( value ) => { setAttributes( { nav_pos: value } ); },
					} ),
					attrs.navigation && el( SelectControl, {
						label: __( 'Nav Inside?', 'porto-functionality' ),
						value: attrs.nav_pos2,
						options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Inside', 'porto-functionality' ), value: 'nav-pos-inside' }, { label: __( 'Outside', 'porto-functionality' ), value: 'nav-pos-outside' } ],
						onChange: ( value ) => { setAttributes( { nav_pos2: value } ); },
					} ),
					attrs.navigation && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
						label: __( 'Nav Type', 'porto-functionality' ),
						value: attrs.nav_type,
						options: porto_block_vars.carousel_nav_types,
						onChange: ( value ) => { setAttributes( { nav_type: value } ); },
					} ),
					attrs.navigation && el( ToggleControl, {
						label: __( 'Show Nav on Hover', 'porto-functionality' ),
						checked: attrs.show_nav_hover,
						onChange: ( value ) => { setAttributes( { show_nav_hover: value } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Show Slider Pagination', 'porto-functionality' ),
						checked: attrs.pagination,
						onChange: ( value ) => { setAttributes( { pagination: value } ); },
					} ),
					attrs.pagination && el( SelectControl, {
						label: __( 'Dots Position', 'porto-functionality' ),
						value: attrs.dots_pos,
						options: [ { label: __( 'Bottom', 'porto-functionality' ), value: '' }, { label: __( 'Top right', 'porto-functionality' ), value: 'show-dots-title-right' } ],
						onChange: ( value ) => { setAttributes( { dots_pos: value } ); },
					} ),
					attrs.pagination && el( SelectControl, {
						label: __( 'Dots Style', 'porto-functionality' ),
						value: attrs.dots_style,
						options: [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Circle inner dot', 'porto-functionality' ), 'value': 'dots-style-1' } ],
						onChange: ( value ) => { setAttributes( { dots_style: value } ); },
					} ),
				)
			);

			let classes = '';
			if ( 'grid' == attrs.view ) {
				classes += ' columns-' + attrs.columns + ' ' + attrs.view;
				classes += ' pcols-ls-2 pcols-xs-' + Math.min( 3, attrs.columns ) + ' pcols-lg-' + attrs.columns;
			} else if ( 'products-slider' == attrs.view ) {
				classes += ' products-slider owl-carousel owl-loaded pcols-lg-' + attrs.columns;
				if ( attrs.navigation ) {
					if ( attrs.nav_pos ) classes += ' ' + attrs.nav_pos;
					if ( ( !attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && attrs.nav_pos2 ) {
						classes += ' ' + attrs.nav_pos2;
					}
					if ( attrs.nav_type ) {
						classes += ' ' + attrs.nav_type;
					} else {
						classes += ' show-nav-middle';
					}
				}
				if ( attrs.pagination ) {
					if ( attrs.dots_pos ) {
						classes += ' ' + attrs.dots_pos
					}
					if ( attrs.dots_style ) {
						classes += ' ' + attrs.dots_style
					}
				}
			} else if ( 'creative' == attrs.view ) {
				classes += ' grid-creative';
			} else {
				classes += ' ' + attrs.view;
			}
			if ( attrs.className ) {
				classes += ' ' + attrs.className;
			}

			let category_view = 'category-pos-' + attrs.text_position.split( '-' )[ 0 ] + ( attrs.text_position.split( '-' ).length >= 2 ? ' category-text-' + attrs.text_position.split( '-' )[ 1 ] : '' ) + ( 'light' != attrs.text_color ? ' category-color-' + attrs.text_color : '' ),
				item_classes = porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ];

			const renderCats = this.state.categoriesList.map( function ( cat, index ) {
				let image = null, item_class = '';
				if ( cat.image && cat.image.catalog_src ) {
					image = el( 'img', { src: cat.image.catalog_src } );
				} else if ( typeof porto_swatches_params != 'undefined' && porto_swatches_params.placeholder_src ) {
					image = el( 'img', { src: porto_swatches_params.placeholder_src } );
				}

				if ( 'creative' == attrs.view && typeof item_classes[ index % item_classes.length ] != 'undefined' ) {
					item_class += ' ' + item_classes[ index % item_classes.length ];
				}
				if ( 'icon' == attrs.media_type && cat.cat_icon ) {
					item_class += ' cat-has-icon';
				}
				if ( 'products-slider' === attrs.view ) {
					item_class += ' owl-item';
				}

				return el(
					'li',
					{ className: 'product-col product-category' + item_class },
					el(
						'span',
						{ className: 'thumb-info align-center' },
						'icon' == attrs.media_type && cat.cat_icon && el(
							'i',
							{ className: cat.cat_icon },
						),
						( 'icon' != attrs.media_type || !cat.cat_icon ) && el(
							'span',
							{ className: 'thumb-info-wrapper tf-none' },
							image
						),
						'category-pos-outside' != category_view && el(
							'span',
							{ className: 'thumb-info-wrap' },
							el(
								'span',
								{ className: 'thumb-info-title' },
								el(
									'h3',
									{ className: 'sub-title thumb-info-inner', dangerouslySetInnerHTML: { __html: cat.name } }
								),
								!attrs.hide_count && cat.count > 0 && el(
									'span',
									{ className: 'thumb-info-type' },
									el( 'mark', {}, Number( cat.count ) ),
									' Products'
								)
							)
						)
					)
				);
			} );

			var renderControls = el(
				'div',
				{ className: 'porto-products' + ( attrs.hover_effect ? ' ' + attrs.hover_effect : '' ) + ( attrs.className ? ' ' + attrs.className.trim() : '' ) },
				attrs.title && el(
					'h2',
					{ className: 'products-title section-title', dangerouslySetInnerHTML: { __html: attrs.title.replace( /<script.*?\/script>/g, '' ) } }
				),
				el(
					'ul',
					{ className: 'products products-container' + classes + ' ' + category_view },
					'products-slider' === attrs.view && el(
						'div',
						{ className: 'owl-stage-outer' },
						el(
							'div',
							{ className: 'owl-stage' },
							renderCats
						)
					),
					'products-slider' !== attrs.view && renderCats,
					'creative' == attrs.view && el(
						'li',
						{ className: 'grid-col-sizer' }
					)
				)
			);

			return [
				el(
					BlockControls,
					null,
					el( Toolbar, { controls: viewControls } )
				),
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-product-categories', {
		title: 'Porto Product Categories',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: { type: 'string' },
			view: { type: 'string', default: 'grid' },
			number: { type: 'int' },
			columns: { type: 'int', default: 4 },
			columns_mobile: { type: 'string' },
			column_width: { type: 'string' },
			grid_layout: { type: 'int', default: 1 },
			grid_height: { type: 'string', default: '600px' },
			spacing: { type: 'int' },
			text_position: { type: 'string', default: 'middle-center' },
			overlay_bg_opacity: { type: 'int', default: 15 },
			text_color: { type: 'string', default: 'light' },
			orderby: { type: 'string', default: 'name' },
			order: { type: 'string', default: 'asc' },
			hide_empty: { type: 'boolean' },
			parent: { type: 'string' },
			ids: { type: 'string' },
			media_type: { type: 'string' },
			show_sub_cats: { type: 'boolean' },
			show_featured: { type: 'string' },
			hide_count: { type: 'boolean' },
			hover_effect: { type: 'string', default: '' },
			image_size: { type: 'string' },
			navigation: { type: 'boolean', default: true },
			nav_pos: { type: 'string', default: '' },
			nav_pos2: { type: 'string' },
			nav_type: { type: 'string' },
			show_nav_hover: { type: 'boolean' },
			pagination: { type: 'boolean', default: false },
			dots_pos: { type: 'string' },
			dots_style: {
				type: 'string',
			},
			animation_type: { type: 'string' },
			animation_duration: { type: 'int' },
			animation_delay: { type: 'int' },
		},
		edit: PortoProductCategories,
		save: function () {
			return null;
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash, wp.apiFetch );

/**
 * 17. Porto Masonry Container
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		Component = wpElement.Component,
		RangeControl = wpComponents.RangeControl,
		TextControl = wpComponents.TextControl,
		SelectControl = wpComponents.SelectControl;

	let counterWillUpdate = null;

	class PortoMasonryContainer extends Component {
		constructor() {
			super( ...arguments );
			this.initMasonry = this.initMasonry.bind( this );
		}

		initMasonry() {
			const props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				$wrap = jQuery( '#block-' + clientId ).find( '.block-editor-block-list__layout' ).eq( 0 );
			if ( !$wrap.children( '.grid-col-sizer' ).length ) {
				$wrap.append( '<div class="grid-col-sizer"></div>' );
			}
			$wrap.isotope( {
				masonry: { 'columnWidth': '.grid-col-sizer' }
			} );
			if ( 'preset' == attrs.layout ) {
				const item_classes = porto_block_vars.creative_layouts[ Number( attrs.grid_layout ) ];
				$wrap.data( 'grid_layout', attrs.grid_layout );
				const wrap_obj = $wrap.get( 0 );
				if ( wrap_obj.hasChildNodes ) {
					jQuery.ajax( {
						url: porto_block_vars.ajax_url,
						data: {
							action: 'porto_load_creative_layout_style',
							nonce: porto_block_vars.nonce,
							layout: attrs.grid_layout,
							grid_height: attrs.grid_height,
							spacing: attrs.gutter_size,
							selector: '#block-' + clientId
						},
						type: 'post',
						success: function ( res ) {
							const children = wrap_obj.childNodes;
							for ( var i = 0; i < children.length; i++ ) {
								const this_obj = children[ i ];
								if ( this_obj.classList.contains( 'grid-col-sizer' ) || this_obj.classList.contains( 'block-list-appender' ) ) {
									continue;
								}
								this_obj.style.width = '';
								this_obj.style.height = '';
								const cls_arr = item_classes[ i % item_classes.length ].split( ' ' );
								for ( var j = 0; j < this_obj.classList.length; j++ ) {
									if ( 0 === this_obj.classList.item( j ).indexOf( 'grid-col-' ) || 0 === this_obj.classList.item( j ).indexOf( 'grid-height-' ) ) {
										this_obj.classList.remove( this_obj.classList.item( j ) );
									}
								}
								cls_arr.map( function ( cls ) {
									this_obj.classList.add( cls );
								} );
							}

							$wrap.prev( 'style' ).remove();
							jQuery( res ).insertBefore( $wrap );
							$wrap.isotope( 'layout' );

							if ( counterWillUpdate ) {
								clearTimeout( counterWillUpdate );
							}
							counterWillUpdate = setTimeout( function () {
								for ( var i = 0; i < children.length; i++ ) {
									const this_obj = children[ i ];
									if ( this_obj.classList.contains( 'grid-col-sizer' ) || this_obj.classList.contains( 'block-list-appender' ) ) {
										continue;
									}
									this_obj.style.width = ( this_obj.offsetWidth / this_obj.parentNode.offsetWidth * 100 ).toFixed( 4 ) + '%';
									this_obj.style.height = this_obj.offsetHeight + 'px';
								}
							}, 500 );
						}
					} );
				}
			}
		}

		componentDidMount() {
			this.initMasonry();
			const clientId = this.props.clientId;
			setTimeout( () => {
				const $wrap = jQuery( '#block-' + clientId ).find( '.block-editor-block-list__layout' ).eq( 0 );
				$wrap.isotope( 'layout' );
			}, 100 );
		}

		componentDidUpdate( prevProps, prevState ) {
			const props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				$wrap = jQuery( '#block-' + clientId ).find( '.block-editor-block-list__layout' ).eq( 0 );
			if ( 'preset' == attrs.layout && ( attrs.layout != prevProps.attributes.layout || attrs.grid_layout != prevProps.attributes.grid_layout || attrs.grid_height != prevProps.attributes.grid_height ) ) {
				this.initMasonry();
			} else if ( 'preset' != attrs.layout ) {
				const wrap_obj = $wrap.get( 0 );
				if ( wrap_obj.previousSibling && 'alt' === wrap_obj.previousSibling.tagName.toLowerCase() ) {
					wrap_obj.parentNode.removeChild( wrap_obj.previousSibling );
				}
				if ( wrap_obj.hasChildNodes() ) {
					const children = wrap_obj.childNodes;
					for ( var i = 0; i < children.length; i++ ) {
						const this_obj = children[ i ];
						if ( this_obj.classList.contains( 'grid-col-sizer' ) || this_obj.classList.contains( 'block-list-appender' ) ) {
							continue;
						}
						var width_data = this_obj.getAttribute( 'data-width' );
						if ( width_data ) {
							this_obj.style.width = width_data;
						} else {
							this.style.width = '';
						}
						this_obj.style.height = '';
					}
				}
			}
			if ( $wrap.data( 'isotope' ) ) {
				$wrap.isotope( 'layout' );
			}
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			const grid_layouts = [];
			for ( var i = 1; i <= 14; i++ ) {
				grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
			}

			var inspectorControls = el( InspectorControls, {},
				el( SelectControl, {
					label: __( 'Layout', 'porto-functionality' ),
					value: attrs.layout,
					options: [ { label: __( 'Custom Masonry Layout', 'porto-functionality' ), value: '' }, { label: __( 'Predefined Grid Layout', 'porto-functionality' ), value: 'preset' } ],
					onChange: ( value ) => { props.setAttributes( { layout: value } ); },
				} ),
				'preset' == attrs.layout && el(
					PortoImageChoose, {
					label: __( 'Creative Grid Layout', 'porto-functionality' ),
					options: grid_layouts,
					value: attrs.grid_layout,
					onChange: ( value ) => {
						props.setAttributes( { grid_layout: value } );
					}
				}
				),
				'preset' == attrs.layout && el( TextControl, {
					label: __( 'Grid Height', 'porto-functionality' ),
					value: attrs.grid_height,
					onChange: ( value ) => { props.setAttributes( { grid_height: value } ); },
				} ),
				el( TextControl, {
					label: __( 'Column Spacing', 'porto-functionality' ),
					value: attrs.gutter_size,
					onChange: ( value ) => { props.setAttributes( { gutter_size: value } ); },
					help: __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ),
				} ),
			);

			const cont_cls = 'porto-grid-' + Math.ceil( Math.random() * 10000 );

			let gutter_style = '';
			if ( attrs.gutter_size ) {
				const gutter_size_number = attrs.gutter_size.replace( /[^.0-9]/g, '' ),
					gutter_size = attrs.gutter_size.replace( gutter_size_number, '' + parseFloat( gutter_size_number ) / 2 ),
					gutter_size_escaped = gutter_size.replace( /<script.*?\/script>/g, '' );
				gutter_style = '.' + cont_cls + ' > .block-editor-inner-blocks > .block-editor-block-list__layout > div { padding: ' + gutter_size_escaped + '}';
				gutter_style += '.' + cont_cls + ' > .block-editor-inner-blocks > .block-editor-block-list__layout{margin:-' + gutter_size_escaped + ' -' + gutter_size_escaped + ' ' + gutter_size_escaped + '}';
			}

			var renderControls = el(
				'div',
				{ className: 'porto-grid-container ' + cont_cls + ( 'preset' == attrs.layout ? ' porto-preset-layout' : '' ) + ( attrs.className ? ' ' + attrs.className : '' ), style: { minHeight: '200px' } },
				attrs.gutter_size && el(
					'style',
					{},
					gutter_style
				),
				el( InnerBlocks, { allowedBlocks: [ 'porto/porto-grid-item' ], initMasonry: this.initMasonry } ),
			);

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-grid-container', {
		title: 'Porto Masonry Container',
		icon: 'porto',
		category: 'porto',
		attributes: {
			layout: {
				type: 'string',
				default: '',
			},
			grid_layout: {
				type: 'int',
				default: 1,
			},
			grid_height: {
				type: 'string',
				default: '600px',
			},
			gutter_size: {
				type: 'string',
			}
		},
		edit: PortoMasonryContainer,
		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

/**
 * 17. Porto Masonry Item
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		Component = wpElement.Component,
		TextControl = wpComponents.TextControl;

	class PortoMasonryItem extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {
			const attrs = this.props.attributes,
				clientId = this.props.clientId,
				this_obj = document.getElementById( 'block-' + clientId ),
				$this = jQuery( this_obj );

			this_obj.classList.add( 'porto-grid-item' );
			if ( attrs.width ) {
				let unit = attrs.width.replace( /[0-9.]/g, '' );
				if ( !unit ) {
					attrs.width += 'px';
				}
				if ( !$this.closest( '.porto-preset-layout' ).length ) {
					this_obj.style.width = attrs.width;
				}
				this_obj.setAttribute( 'data-width', attrs.width );
			}
			this_obj.style.float = 'left';

			const $wrap = $this.closest( '.block-editor-block-list__layout' );
			if ( $this.closest( '.porto-preset-layout' ).length && $wrap.length && $wrap.data( 'isotope' ) ) {
				const item_classes = porto_block_vars.creative_layouts[ Number( $wrap.data( 'grid_layout' ) ) ],
					cls_arr = item_classes[ $this.index() % item_classes.length ].split( ' ' );
				cls_arr.map( function ( cls ) {
					this_obj.classList.add( cls );
				} );
				this_obj.style.width = ( this_obj.offsetWidth / this_obj.parentNode.offsetWidth * 100 ).toFixed( 4 ) + '%';
				this_obj.style.height = this_obj.offsetHeight + 'px';
			}
			if ( $wrap.data( 'isotope' ) ) {
				$wrap.isotope( 'destroy' );
				$wrap.isotope( {
					masonry: { 'columnWidth': '.grid-col-sizer' }
				} );
			}
		}

		componentWillUnmount() {
			const clientId = this.props.clientId,
				$this = jQuery( '#block-' + clientId ),
				$wrap = $this.closest( '.block-editor-block-list__layout' );
			if ( $wrap.data( 'isotope' ) ) {
				setTimeout( function () {
					$wrap.isotope( 'destroy' );
					$wrap.isotope( {
						masonry: { 'columnWidth': '.grid-col-sizer' }
					} );
				}, 200 );
			}
		}

		componentDidUpdate( prevProps, prevState ) {
			const attrs = this.props.attributes,
				clientId = this.props.clientId,
				this_obj = document.getElementById( 'block-' + clientId ),
				$this = jQuery( this_obj ),
				$iso_obj = $this.closest( '.porto-grid-container' ).find( '.block-editor-block-list__layout' ).eq( 0 );
			if ( attrs.width != prevProps.attributes.width ) {
				if ( attrs.width ) {
					let unit = attrs.width.replace( /[0-9.]/g, '' );
					if ( !unit ) {
						attrs.width += 'px';
					}
					this_obj.style.width = attrs.width;
				}
				if ( $iso_obj.data( 'isotope' ) ) {
					$iso_obj.isotope( 'layout' );
				}
				this_obj.setAttribute( 'data-width', attrs.width );
			}
			this_obj.classList.remove( 'porto-grid-item' );
			this_obj.classList.add( 'porto-grid-item' );
			this_obj.style.float = 'left';
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __( 'Width', 'porto-functionality' ),
					value: attrs.width,
					onChange: ( value ) => { props.setAttributes( { width: value } ); },
					help: __( 'Enter value including any valid CSS unit, ex: 25%.', 'porto-functionality' ),
				} )
			);

			let inline_style = {};
			var renderControls = el(
				'div',
				{ className: ( attrs.className ? ' ' + attrs.className : '' ), style: inline_style },
				el( InnerBlocks )
			);

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-grid-item', {
		title: 'Porto Masonry Item',
		icon: 'porto',
		category: 'porto',
		parent: [ 'porto/porto-grid-container' ],
		attributes: {
			width: {
				type: 'string',
			},
			initMasonry: {
				type: 'function'
			}
		},
		edit: PortoMasonryItem,
		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );

( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
	"use strict";
	const __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
		PanelBody = wpComponents.PanelBody,
		el = wpElement.createElement,
		useEffect = wpElement.useEffect,
		useState = wpElement.useState,
		TextControl = wpComponents.TextControl,
		TextareaControl = wpComponents.TextareaControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Disabled = wpComponents.Disabled,
		ServerSideRender = wp.serverSideRender;

	/**
	 * 18.Products widget
	 */
	const PortoProductsWidget = function ( props ) {

		const attrs = props.attributes,
			name = props.name;

		var inspectorControls = el( InspectorControls, {},
			el( TextControl, {
				label: __( 'Title', 'porto-functionality' ),
				value: attrs.title,
				onChange: ( value ) => { props.setAttributes( { title: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Show', 'porto-functionality' ),
				value: attrs.show,
				options: porto_block_vars.status_values,
				onChange: ( value ) => { props.setAttributes( { show: value } ); },
			} ),
			el( RangeControl, {
				label: __( 'Number', 'porto-functionality' ),
				value: attrs.number,
				min: 1,
				max: 10,
				onChange: ( value ) => { props.setAttributes( { number: value } ); },
			} ),
			'top_rated' !== attrs.show && 'recent_view' !== attrs.show && el( SelectControl, {
				label: __( 'Order by', 'porto-functionality' ),
				value: attrs.orderby,
				options: [ { label: __( 'Date', 'porto-functionality' ), value: 'date' }, { label: __( 'Price', 'porto-functionality' ), value: 'price' }, { label: __( 'Total Sales', 'porto-functionality' ), value: 'sales' }, { label: __( 'Random', 'porto-functionality' ), value: 'rand' } ],
				onChange: ( value ) => { props.setAttributes( { orderby: value } ); },
			} ),
			'top_rated' !== attrs.show && 'recent_view' !== attrs.show && el( SelectControl, {
				label: __( 'Order', 'porto-functionality' ),
				value: attrs.order,
				options: [ { label: __( 'Descending', 'porto-functionality' ), value: 'desc' }, { label: __( 'Ascending', 'porto-functionality' ), value: 'asc' } ],
				onChange: ( value ) => { props.setAttributes( { order: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Hide free products', 'porto-functionality' ),
				checked: attrs.hide_free,
				onChange: ( value ) => { props.setAttributes( { hide_free: value } ); },
			} ),
			el( ToggleControl, {
				label: __( 'Show hidden products', 'porto-functionality' ),
				checked: attrs.show_hidden,
				onChange: ( value ) => { props.setAttributes( { show_hidden: value } ); },
			} )
		);

		var renderControls = el(
			Disabled,
			{},
			el(
				ServerSideRender,
				{ block: name, attributes: attrs }
			)
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-products-widget', {
		title: 'Porto Products Widget',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string'
			},
			show: {
				type: 'string',
				default: ''
			},
			number: {
				type: 'int',
				default: 5
			},
			orderby: {
				type: 'string',
				default: 'date'
			},
			order: {
				type: 'string',
				default: 'desc'
			},
			hide_free: {
				type: 'boolean',
				default: false
			},
			show_hidden: {
				type: 'boolean',
				default: false
			}
		},
		edit: PortoProductsWidget,
		save: function () {
			return null;
		}
	} );

	/**
	 * 19. Porto Sidebar menu
	 */
	const PortoSidebarMenu = function ( props ) {
		const attrs = props.attributes,
			name = props.name;

		const [ menuList, setMenuList ] = useState( [ { 'label': __( 'Select a menu', 'porto-functionality' ), value: '' } ] );

		useEffect(
			() => {
				wp.apiFetch( { path: '/ajaxselect2/v1/nav_menu/' } ).then( function ( obj ) {
					if ( obj && obj.results ) {
						let menuOptions = [ { 'label': __( 'Select a menu', 'porto-functionality' ), value: '' } ];
						obj.results.map( function ( item, index ) {
							menuOptions.push( { label: item.text, value: item.id } );
						} );
						setMenuList( menuOptions );
					}
				} );
			},
			[],
		);

		var inspectorControls = el( InspectorControls, {},
			el( TextControl, {
				label: __( 'Title', 'porto-functionality' ),
				value: attrs.title,
				onChange: ( value ) => { props.setAttributes( { title: value } ); },
			} ),
			el( SelectControl, {
				label: __( 'Nav menu', 'porto-functionality' ),
				value: attrs.nav_menu,
				options: menuList,
				onChange: ( value ) => { props.setAttributes( { nav_menu: value } ); },
			} ),
		);

		attrs.el_class = attrs.className;

		var renderControls = el(
			Disabled,
			{},
			el(
				ServerSideRender,
				{ block: name, attributes: attrs }
			)
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-sidebar-menu', {
		title: 'Porto Sidebar Menu',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string'
			},
			nav_menu: {
				type: 'string',
				default: ''
			}
		},
		edit: PortoSidebarMenu,
		save: function () {
			return null;
		}
	} );

	/**
	 * 20. Porto Hot Spot
	 */
	const PortoHotSpot = ( props ) => {
		const attrs = props.attributes,
			name = props.name;

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'porto-functionality' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Content Type', 'porto-functionality' ) }
						value={ attrs.type }
						options={ [ { 'label': __( 'HTML', 'porto-functionality' ), 'value': 'html' }, { 'label': __( 'Product', 'porto-functionality' ), 'value': 'product' }, { 'label': __( 'Block', 'porto-functionality' ), 'value': 'block' } ] }
						onChange={ ( value ) => { props.setAttributes( { type: value } ); } }
					/>
					{ 'html' === attrs.type && (
						<TextareaControl
							label={ __( 'HTML Content', 'porto-functionality' ) }
							value={ attrs.content }
							onChange={ ( value ) => { props.setAttributes( { content: value } ); } }
						/>
					) }
					{ 'product' === attrs.type && (
						<PortoAjaxSelect2Control
							label={ __( 'Product', 'porto-functionality' ) }
							value={ attrs.id }
							option="product"
							onChange={ ( value ) => { props.setAttributes( { id: Number( value ) } ); } }
						/>
					) }
					{ 'product' === attrs.type && (
						<SelectControl
							label={ __( 'Add Links Position', 'porto-functionality' ) }
							value={ attrs.addlinks_pos }
							options={ porto_block_vars.product_layouts }
							onChange={ ( value ) => { setAttributes( { addlinks_pos: value } ); } }
						/>
					) }
					{ 'block' === attrs.type && (
						<PortoAjaxSelect2Control
							label={ __( 'Block', 'porto-functionality' ) }
							value={ attrs.block }
							option="porto_builder"
							onChange={ ( value ) => { props.setAttributes( { block: Number( value ) } ); } }
						/>
					) }
					<TextControl
						label={ __( 'Icon Class', 'porto-functionality' ) }
						value={ attrs.icon }
						onChange={ ( value ) => { props.setAttributes( { icon: value } ); } }
						help={ __( 'Please check this url to see icons which Porto supports. ', 'porto-functionality' ) }
					/>
					<p style={ { marginTop: -14 } }>
						<a href="https://www.portotheme.com/wordpress/porto/shortcodes/icons/" target="_blank">
							https://www.portotheme.com/wordpress/porto/shortcodes/icons/
						</a>
					</p>
					<SelectControl
						label={ __( 'Popup position', 'porto-functionality' ) }
						value={ attrs.pos }
						options={ [ { 'label': __( 'Top', 'porto-functionality' ), 'value': 'top' }, { 'label': __( 'Right', 'porto-functionality' ), 'value': 'right' }, { 'label': __( 'Bottom', 'porto-functionality' ), 'value': 'bottom' }, { 'label': __( 'Left', 'porto-functionality' ), 'value': 'left' } ] }
						onChange={ ( value ) => { props.setAttributes( { pos: value } ); } }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Style', 'porto-functionality' ) } initialOpen={ false }>
					<RangeControl
						label={ __( 'Horizontal Position (%)', 'porto-functionality' ) }
						value={ attrs.x }
						min="0"
						max="100"
						onChange={ ( value ) => { props.setAttributes( { x: value } ); } }
					/>
					<RangeControl
						label={ __( 'Vertical Position (%)', 'porto-functionality' ) }
						value={ attrs.y }
						min="0"
						max="100"
						onChange={ ( value ) => { props.setAttributes( { y: value } ); } }
					/>
					<TextControl
						label={ __( 'Spot Size', 'porto-functionality' ) }
						value={ attrs.size }
						help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
						onChange={ ( value ) => { props.setAttributes( { size: value } ); } }
					/>
					<TextControl
						label={ __( 'Icon Size', 'porto-functionality' ) }
						value={ attrs.icon_size }
						help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
						onChange={ ( value ) => { props.setAttributes( { icon_size: value } ); } }
					/>
					<PanelColorSettings
						title={ __( 'Color Settings', 'porto-functionality' ) }
						initialOpen={ false }
						colorSettings={ [
							{
								label: __( 'Icon Color', 'porto-functionality' ),
								value: attrs.color,
								onChange: function onChange( value ) {
									return props.setAttributes( { color: value } );
								}
							},
							{
								label: __( 'Background Color', 'porto-functionality' ),
								value: attrs.bg_color,
								onChange: function onChange( value ) {
									return props.setAttributes( { bg_color: value } );
								}
							}
						] }
					/>
				</PanelBody>
			</InspectorControls>
		)

		attrs.el_class = attrs.className;

		var renderControls = (
			<Disabled>
				<ServerSideRender block={ name } attributes={ attrs } />
			</Disabled>
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-hotspot', {
		title: 'Porto Hot Spot',
		icon: 'porto',
		category: 'porto',
		attributes: {
			type: {
				type: 'string',
				default: 'html',
			},
			content: {
				type: 'string',
				default: '',
			},
			id: {
				type: 'int',
			},
			addlinks_pos: {
				type: 'string',
				default: '',
			},
			block: {
				type: 'int',
			},
			icon: {
				type: 'string',
				default: ''
			},
			pos: {
				type: 'string',
				default: 'right'
			},
			x: {
				type: 'int',
			},
			y: {
				type: 'int',
			},
			size: {
				type: 'string',
			},
			icon_size: {
				type: 'string',
			},
			color: {
				type: 'string',
			},
			bg_color: {
				type: 'string',
			},
		},
		edit: PortoHotSpot,
		save: function () {
			return null;
		}
	} );

	/**
	 * 21. Porto Portfolios
	 */
	const PortoPortfolios = ( props ) => {
		const attrs = props.attributes,
			name = props.name;

		useEffect(
			() => {
				const clientId = props.clientId,
					$wrap = jQuery( '#block-' + clientId + ' .portfolio-row' ),
					$parent = $wrap.parent();

				if ( 'timeline' !== attrs.portfolio_layout ) {
					if ( $wrap.data( 'isotope' ) ) {
						$wrap.isotope( 'destroy' );
					}
					$wrap.children().each( function ( i ) {
						if ( !( this instanceof HTMLElement ) ) {
							Object.setPrototypeOf( this, HTMLElement.prototype );
						}
					} );
					let columnWidth;
					if ( 'creative' === attrs.portfolio_layout ) {
						columnWidth = '.grid-col-sizer';
					} else if ( !$parent.find( '.portfolio:not(.w2)' ).length ) {
						columnWidth = '.portfolio';
					} else {
						columnWidth = '.portfolio:not(.w2)';
					}
					$wrap.isotope( {
						itemSelector: '.portfolio',
						masonry: { 'columnWidth': columnWidth }
					} );
					$wrap.isotope( 'layout' );
					$wrap.isotope( 'on', 'layoutComplete', function () {
						console.log( 'aaa' );
						$parent.addClass( 'portfolio-iso-active' );
					} );
				}
			}
		);

		const grid_layouts = [];
		for ( var i = 1; i <= 14; i++ ) {
			grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
		}

		const infoColorSettings = [];
		if ( ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && 'left-info-no-bg' === attrs.info_view ) {
			infoColorSettings.push(
				{
					label: __( 'Info Color', 'porto-functionality' ),
					value: attrs.info_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { info_color: value } );
					}
				}
			);
			if ( attrs.custom_portfolios ) {
				infoColorSettings.push(
					{
						label: __( 'Info Color for custom portfolios', 'porto-functionality' ),
						value: attrs.info_color2,
						onChange: function onChange( value ) {
							return props.setAttributes( { info_color2: value } );
						}
					}
				);
			}
		}

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Portfolio Layout', 'porto-functionality' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Title', 'porto-functionality' ) }
						value={ attrs.title }
						onChange={ ( value ) => { props.setAttributes( { title: value } ); } }
					/>
					<SelectControl
						label={ __( 'Portfolio Layout', 'porto-functionality' ) }
						value={ attrs.portfolio_layout }
						options={ porto_block_vars.portfolio_layouts }
						onChange={ ( value ) => { props.setAttributes( { portfolio_layout: value } ); } }
					/>
					{ 'creative' == attrs.portfolio_layout && (
						<PortoImageChoose
							label={ __( 'Creative Grid Layout', 'porto-functionality' ) }
							options={ grid_layouts }
							value={ attrs.grid_layout }
							onChange={ ( value ) => {
								props.setAttributes( { grid_layout: value } );
							} }
						/>
					) }
					{ 'creative' == attrs.portfolio_layout && (
						<TextControl
							label={ __( 'Grid Height', 'porto-functionality' ) }
							value={ attrs.grid_height }
							onChange={ ( value ) => { props.setAttributes( { grid_height: value } ); } }
						/>
					) }
					{ ( 'creative' == attrs.portfolio_layout || 'masonry-creative' == attrs.portfolio_layout ) && (
						<RangeControl
							label={ __( 'Column Spacing (px)', 'porto-functionality' ) }
							value={ attrs.spacing }
							min="0"
							max="100"
							onChange={ ( value ) => { props.setAttributes( { spacing: value } ); } }
						/>
					) }
					{ 'masonry-creative' === attrs.portfolio_layout && (
						<SelectControl
							label={ __( 'Masonry Layout', 'porto-functionality' ) }
							value={ attrs.masonry_layout }
							options={ [ { label: '1', value: '1' } ] }
							onChange={ ( value ) => { props.setAttributes( { masonry_layout: value } ); } }
						/>
					) }
					{ ( 'large' == attrs.portfolio_layout || 'fullscreen' == attrs.portfolio_layout ) && (
						<TextControl
							label={ __( 'Content Animation', 'porto-functionality' ) }
							value={ attrs.content_animation }
							onChange={ ( value ) => { props.setAttributes( { content_animation: value } ); } }
							help={ __( 'Please check this url to see animation types.', 'porto-functionality' ) }
						/>
					) }
					{ ( 'large' == attrs.portfolio_layout || 'fullscreen' == attrs.portfolio_layout ) && (
						<p style={ { marginTop: -20 } }>
							<a href='https://www.portotheme.com/wordpress/porto/shortcodes/animations/' target='_blank'>
								https://www.portotheme.com/wordpress/porto/shortcodes/animations/
							</a>
						</p>
					) }
					{ ( attrs.portfolio_layout === 'grid' || attrs.portfolio_layout === 'masonry' ) && (
						<RangeControl
							label={ __( 'Columns', 'porto-functionality' ) }
							value={ attrs.columns }
							min="1"
							max="6"
							onChange={ ( value ) => { props.setAttributes( { columns: value } ); } }
						/>
					) }
					{ ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && (
						<SelectControl
							label={ __( 'View Type', 'porto-functionality' ) }
							value={ attrs.view }
							options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': 'classic' }, { 'label': __( 'Default', 'porto-functionality' ), 'value': 'default' }, { 'label': __( 'No Margin', 'porto-functionality' ), 'value': 'full' }, { 'label': __( 'Out of Image', 'porto-functionality' ), 'value': 'outimage' } ] }
							onChange={ ( value ) => { props.setAttributes( { view: value } ); } }
						/>
					) }
					{ ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && (
						<SelectControl
							label={ __( 'Info View Type', 'porto-functionality' ) }
							value={ attrs.info_view }
							options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Left Info', 'porto-functionality' ), 'value': 'left-info' }, { 'label': __( 'Left Info & No bg', 'porto-functionality' ), 'value': 'left-info-no-bg' }, { 'label': __( 'Centered Info', 'porto-functionality' ), 'value': 'centered-info' }, { 'label': __( 'Bottom Info', 'porto-functionality' ), 'value': 'bottom-info' }, { 'label': __( 'Bottom Info Dark', 'porto-functionality' ), 'value': 'bottom-info-dark' }, { 'label': __( 'Hide Info Hover', 'porto-functionality' ), 'value': 'hide-info-hover' }, { 'label': __( 'Plus Icon', 'porto-functionality' ), 'value': 'plus-icon' } ] }
							onChange={ ( value ) => { props.setAttributes( { info_view: value } ); } }
						/>
					) }
					{ ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && 'left-info-no-bg' === attrs.info_view && (
						<TextControl
							label={ __( 'Portfolio Indexes to use custom info color', 'porto-functionality' ) }
							value={ attrs.custom_portfolios }
							onChange={ ( value ) => { props.setAttributes( { custom_portfolios: value } ); } }
							help={ __( 'comma separated list of portfolio indexes', 'porto-functionality' ) }
						/>
					) }
					{ ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && 'left-info-no-bg' === attrs.info_view && (
						<PanelColorSettings
							title={ __( 'Color Settings', 'porto-functionality' ) }
							initialOpen={ false }
							colorSettings={ infoColorSettings }
						/>
					) }
					{ ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && (
						<SelectControl
							label={ __( 'Info View Type Style', 'porto-functionality' ) }
							value={ attrs.info_view_type_style }
							options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Alternate', 'porto-functionality' ), 'value': 'alternate-info' }, { 'label': __( 'Alternate with Plus', 'porto-functionality' ), 'value': 'alternate-with-plus' }, { 'label': __( 'No Style', 'porto-functionality' ), 'value': 'no-style' } ] }
							onChange={ ( value ) => { props.setAttributes( { info_view_type_style: value } ); } }
						/>
					) }
					<SelectControl
						label={ __( 'Image Size', 'porto-functionality' ) }
						value={ attrs.image_size }
						options={ porto_block_vars.image_sizes }
						onChange={ ( value ) => { props.setAttributes( { image_size: value } ); } }
					/>
					<SelectControl
						label={ __( 'Image Overlay Background', 'porto-functionality' ) }
						value={ attrs.thumb_bg }
						options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Darken', 'porto-functionality' ), 'darken': 'alternate-info' }, { 'label': __( 'Lighten', 'porto-functionality' ), 'value': 'lighten' }, { 'label': __( 'Transparent', 'porto-functionality' ), 'value': 'hide-wrapper-bg' } ] }
						onChange={ ( value ) => { props.setAttributes( { thumb_bg: value } ); } }
					/>
					<SelectControl
						label={ __( 'Hover Image Effect', 'porto-functionality' ) }
						value={ attrs.thumb_image }
						options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Zoom', 'porto-functionality' ), 'darken': 'zoom' }, { 'label': __( 'Slow Zoom', 'porto-functionality' ), 'value': 'slow-zoom' }, { 'label': __( 'No Zoom', 'porto-functionality' ), 'value': 'no-zoom' } ] }
						onChange={ ( value ) => { props.setAttributes( { thumb_image: value } ); } }
					/>
					{ ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" ) && (
						<SelectControl
							label={ __( 'Image Counter', 'porto-functionality' ) }
							value={ attrs.image_counter }
							options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Show', 'porto-functionality' ), 'value': 'show' }, { 'label': __( 'Hide', 'porto-functionality' ), 'value': 'hide' } ] }
							onChange={ ( value ) => { props.setAttributes( { image_counter: value } ); } }
						/>
					) }
					<SelectControl
						label={ __( 'Show Image Lightbox Icon', 'porto-functionality' ) }
						value={ attrs.show_lightbox_icon }
						options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Show', 'porto-functionality' ), 'value': 'show' }, { 'label': __( 'Hide', 'porto-functionality' ), 'value': 'hide' } ] }
						onChange={ ( value ) => { props.setAttributes( { show_lightbox_icon: value } ); } }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Portfolio Selector', 'porto-functionality' ) } initialOpen={ false }>
					<PortoAjaxSelect2Control
						label={ __( 'Categories', 'porto-functionality' ) }
						value={ attrs.cats }
						option="portfolio_cat"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { cats: value } ); } }
					/>
					<PortoAjaxSelect2Control
						label={ __( 'Portfolios', 'porto-functionality' ) }
						value={ attrs.post_in }
						option="portfolio"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { post_in: value } ); } }
					/>
					<SelectControl
						label={ __( 'Order by', 'porto-functionality' ) }
						value={ attrs.orderby }
						options={ [ { 'label': '', 'value': '' }, { 'label': __( 'Date', 'porto-functionality' ), 'value': 'date' }, { 'label': __( 'ID', 'porto-functionality' ), 'value': 'ID' }, { 'label': __( 'Author', 'porto-functionality' ), 'value': 'author' }, { 'label': __( 'Title', 'porto-functionality' ), 'value': 'title' }, { 'label': __( 'Modified', 'porto-functionality' ), 'value': 'modified' }, { 'label': __( 'Random', 'porto-functionality' ), 'value': 'rand' }, { 'label': __( 'Comment count', 'porto-functionality' ), 'value': 'comment_count' }, { 'label': __( 'Menu order', 'porto-functionality' ), 'value': 'menu_order' } ] }
						onChange={ ( value ) => { props.setAttributes( { orderby: value } ); } }
					/>
					<SelectControl
						label={ __( 'Order way', 'porto-functionality' ) }
						value={ attrs.order }
						options={ [ { 'label': '', 'value': '' }, { 'label': __( 'Descending', 'porto-functionality' ), 'value': 'desc' }, { 'label': __( 'Ascending', 'porto-functionality' ), 'value': 'asc' } ] }
						onChange={ ( value ) => { props.setAttributes( { order: value } ); } }
					/>
					<PortoAjaxSelect2Control
						label={ __( 'Slider on Portfolio', 'porto-functionality' ) }
						value={ attrs.slider }
						option="portfolio"
						multiple="1"
						help={ __( 'Will Only work with ajax on page settings', 'porto-functionality' ) }
						onChange={ ( value ) => { props.setAttributes( { slider: value } ); } }
					/>
					<RangeControl
						label={ __( 'Portfolio Count', 'porto-functionality' ) }
						value={ attrs.number }
						min="0"
						max="32"
						onChange={ ( value ) => { props.setAttributes( { number: value } ); } }
					/>
					<RangeControl
						label={ __( 'Excerpt Length', 'porto-functionality' ) }
						value={ attrs.excerpt_length }
						min="1"
						max="100"
						onChange={ ( value ) => { props.setAttributes( { excerpt_length: value } ); } }
					/>
					<SelectControl
						label={ __( 'Load More Posts', 'porto-functionality' ) }
						value={ attrs.load_more_posts }
						options={ [ { 'label': __( 'Select', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Pagination', 'porto-functionality' ), 'value': 'pagination' }, { 'label': __( 'Load More (Button)', 'porto-functionality' ), 'value': 'load-more-btn' } ] }
						onChange={ ( value ) => { props.setAttributes( { load_more_posts: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Show Archive Link', 'porto-functionality' ) }
						checked={ attrs.view_more }
						onChange={ ( value ) => { props.setAttributes( { view_more: value } ); } }
					/>
					{ attrs.view_more && (
						<TextControl
							label={ __( 'Extra class name for Archive Link', 'porto-functionality' ) }
							value={ attrs.view_more_class }
							onChange={ ( value ) => { props.setAttributes( { view_more_class: value } ); } }
						/>
					) }
					<ToggleControl
						label={ __( 'Show Filter', 'porto-functionality' ) }
						checked={ attrs.filter }
						onChange={ ( value ) => { props.setAttributes( { filter: value } ); } }
					/>
					{ attrs.filter && (
						<SelectControl
							label={ __( 'Filter Style', 'porto-functionality' ) }
							value={ attrs.filter_style }
							options={ [ { 'label': __( 'Style 1', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Style 2', 'porto-functionality' ), 'value': 'style-2' }, { 'label': __( 'Style 3', 'porto-functionality' ), 'value': 'style-3' } ] }
							onChange={ ( value ) => { props.setAttributes( { filter_style: value } ); } }
						/>
					) }
					<ToggleControl
						label={ __( 'Enable Ajax Load', 'porto-functionality' ) }
						checked={ attrs.ajax_load }
						onChange={ ( value ) => { props.setAttributes( { ajax_load: value } ); } }
					/>
					{ attrs.ajax_load && (
						<ToggleControl
							label={ __( 'Ajax Load on Modal', 'porto-functionality' ) }
							checked={ attrs.ajax_modal }
							onChange={ ( value ) => { props.setAttributes( { ajax_modal: value } ); } }
						/>
					) }
				</PanelBody>
			</InspectorControls>
		)

		attrs.el_class = attrs.className;

		var renderControls = (
			<Disabled>
				<ServerSideRender block={ name } attributes={ attrs } />
			</Disabled>
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-portfolios', {
		title: 'Porto Portfolios',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			portfolio_layout: {
				type: 'string',
				default: 'timeline',
			},
			grid_layout: {
				type: 'int',
				default: 1,
			},
			grid_height: {
				type: 'string',
				default: '600px',
			},
			spacing: {
				type: 'int',
			},
			masonry_layout: {
				type: 'int',
				default: 1,
			},
			content_animation: {
				type: 'string',
			},
			columns: {
				type: 'int',
				default: 3,
			},
			view: {
				type: 'string',
				default: 'classic',
			},
			info_view: {
				type: 'string',
				default: '',
			},
			info_color_2: {
				type: 'string',
			},
			custom_portfolios: {
				type: 'string',
			},
			info_color2: {
				type: 'string',
			},
			info_view_type_style: {
				type: 'string',
				default: '',
			},
			image_size: {
				type: 'string',
			},
			thumb_bg: {
				type: 'string',
				default: '',
			},
			thumb_image: {
				type: 'string',
				default: ''
			},
			image_counter: {
				type: 'string',
				default: '',
			},
			show_lightbox_icon: {
				type: 'string',
				default: '',
			},
			cats: {
				type: 'string',
			},
			post_in: {
				type: 'string',
			},
			orderby: {
				type: 'string',
			},
			order: {
				type: 'string',
			},
			slider: {
				type: 'string',
			},
			number: {
				type: 'int',
				default: 8,
			},
			excerpt_length: {
				type: 'int',
			},
			load_more_posts: {
				type: 'string',
				default: '',
			},
			view_more: {
				type: 'boolean',
			},
			view_more_class: {
				type: 'string',
			},
			filter: {
				type: 'boolean',
			},
			filter_style: {
				type: 'string',
				default: '',
			},
			ajax_load: {
				type: 'boolean',
			},
			ajax_modal: {
				type: 'boolean',
			},
		},
		edit: PortoPortfolios,
		save: function () {
			return null;
		}
	} );

	/**
	 * 22. Porto Recent Portfolios
	 */
	const PortoRecentPortfolios = ( props ) => {
		const attrs = props.attributes,
			name = props.name;

		const grid_layouts = [];
		for ( var i = 1; i <= 14; i++ ) {
			grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
		}

		const infoColorSettings = [];
		if ( ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && 'left-info-no-bg' === attrs.info_view ) {
			infoColorSettings.push(
				{
					label: __( 'Info Color', 'porto-functionality' ),
					value: attrs.info_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { info_color: value } );
					}
				}
			);
			if ( attrs.custom_portfolios ) {
				infoColorSettings.push(
					{
						label: __( 'Info Color for custom portfolios', 'porto-functionality' ),
						value: attrs.info_color2,
						onChange: function onChange( value ) {
							return props.setAttributes( { info_color2: value } );
						}
					}
				);
			}
		}

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Portfolio Layout', 'porto-functionality' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Title', 'porto-functionality' ) }
						value={ attrs.title }
						onChange={ ( value ) => { props.setAttributes( { title: value } ); } }
					/>
					<SelectControl
						label={ __( 'View Type', 'porto-functionality' ) }
						value={ attrs.view }
						options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': 'classic' }, { 'label': __( 'Default', 'porto-functionality' ), 'value': 'default' }, { 'label': __( 'No Margin', 'porto-functionality' ), 'value': 'full' }, { 'label': __( 'Out of Image', 'porto-functionality' ), 'value': 'outimage' } ] }
						onChange={ ( value ) => { props.setAttributes( { view: value } ); } }
					/>
					<SelectControl
						label={ __( 'Info View Type', 'porto-functionality' ) }
						value={ attrs.info_view }
						options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Left Info', 'porto-functionality' ), 'value': 'left-info' }, { 'label': __( 'Left Info & No bg', 'porto-functionality' ), 'value': 'left-info-no-bg' }, { 'label': __( 'Centered Info', 'porto-functionality' ), 'value': 'centered-info' }, { 'label': __( 'Bottom Info', 'porto-functionality' ), 'value': 'bottom-info' }, { 'label': __( 'Bottom Info Dark', 'porto-functionality' ), 'value': 'bottom-info-dark' }, { 'label': __( 'Hide Info Hover', 'porto-functionality' ), 'value': 'hide-info-hover' }, { 'label': __( 'Plus Icon', 'porto-functionality' ), 'value': 'plus-icon' } ] }
						onChange={ ( value ) => { props.setAttributes( { info_view: value } ); } }
					/>
					<SelectControl
						label={ __( 'Image Size', 'porto-functionality' ) }
						value={ attrs.image_size }
						options={ porto_block_vars.image_sizes }
						onChange={ ( value ) => { props.setAttributes( { image_size: value } ); } }
					/>
					<SelectControl
						label={ __( 'Image Overlay Background', 'porto-functionality' ) }
						value={ attrs.thumb_bg }
						options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Darken', 'porto-functionality' ), 'darken': 'alternate-info' }, { 'label': __( 'Lighten', 'porto-functionality' ), 'value': 'lighten' }, { 'label': __( 'Transparent', 'porto-functionality' ), 'value': 'hide-wrapper-bg' } ] }
						onChange={ ( value ) => { props.setAttributes( { thumb_bg: value } ); } }
					/>
					<SelectControl
						label={ __( 'Hover Image Effect', 'porto-functionality' ) }
						value={ attrs.thumb_image }
						options={ [ { 'label': __( 'Standard', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Zoom', 'porto-functionality' ), 'darken': 'zoom' }, { 'label': __( 'Slow Zoom', 'porto-functionality' ), 'value': 'slow-zoom' }, { 'label': __( 'No Zoom', 'porto-functionality' ), 'value': 'no-zoom' } ] }
						onChange={ ( value ) => { props.setAttributes( { thumb_image: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Enable Ajax Load', 'porto-functionality' ) }
						checked={ attrs.ajax_load }
						onChange={ ( value ) => { props.setAttributes( { ajax_load: value } ); } }
					/>
					{ attrs.ajax_load && (
						<ToggleControl
							label={ __( 'Ajax Load on Modal', 'porto-functionality' ) }
							checked={ attrs.ajax_modal }
							onChange={ ( value ) => { props.setAttributes( { ajax_modal: value } ); } }
						/>
					) }
					<RangeControl
						label={ __( 'Portfolio Count', 'porto-functionality' ) }
						value={ attrs.number }
						min="0"
						max="32"
						onChange={ ( value ) => { props.setAttributes( { number: value } ); } }
					/>
					<PortoAjaxSelect2Control
						label={ __( 'Categories', 'porto-functionality' ) }
						value={ attrs.cats }
						option="portfolio_cat"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { cats: value } ); } }
					/>
					<PortoAjaxSelect2Control
						label={ __( 'Portfolios', 'porto-functionality' ) }
						value={ attrs.post_in }
						option="portfolio"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { post_in: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Large Desktop', 'porto-functionality' ) }
						value={ attrs.items }
						min="1"
						max="8"
						onChange={ ( value ) => { props.setAttributes( { items: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Desktop', 'porto-functionality' ) }
						value={ attrs.items_desktop }
						min="1"
						max="8"
						onChange={ ( value ) => { props.setAttributes( { items_desktop: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Tablets', 'porto-functionality' ) }
						value={ attrs.items_tablets }
						min="1"
						max="6"
						onChange={ ( value ) => { props.setAttributes( { items_tablets: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Mobile', 'porto-functionality' ) }
						value={ attrs.items_mobile }
						min="1"
						max="4"
						onChange={ ( value ) => { props.setAttributes( { items_mobile: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items Row', 'porto-functionality' ) }
						value={ attrs.items_row }
						min="1"
						max="4"
						onChange={ ( value ) => { props.setAttributes( { items_row: value } ); } }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Slider Options', 'porto-functionality' ) } initialOpen={ false }>
					<ToggleControl
						label={ __( 'Change Slider Options', 'porto-functionality' ) }
						checked={ attrs.slider_config }
						onChange={ ( value ) => { props.setAttributes( { slider_config: value } ); } }
					/>
					{ attrs.slider_config && (
						<ToggleControl
							label={ __( 'Show Slider Navigation', 'porto-functionality' ) }
							checked={ attrs.show_nav }
							onChange={ ( value ) => { props.setAttributes( { show_nav: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && (
						<SelectControl
							label={ __( 'Nav Position', 'porto-functionality' ) }
							value={ attrs.nav_pos }
							options={ [ { 'label': __( 'Middle', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Middle of Images', 'porto-functionality' ), 'value': 'nav-center-images-only' }, { 'label': __( 'Top', 'porto-functionality' ), 'value': 'show-nav-title' }, { 'label': __( 'Bottom', 'porto-functionality' ), 'value': 'nav-bottom' } ] }
							onChange={ ( value ) => { props.setAttributes( { nav_pos: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && ( attrs.nav_pos === '' || attrs.nav_pos === 'nav-center-images-only' ) && (
						<SelectControl
							label={ __( 'Nav Inside/Outside?', 'porto-functionality' ) }
							value={ attrs.nav_pos2 }
							options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Inside', 'porto-functionality' ), 'value': 'nav-pos-inside' }, { 'label': __( 'Outside', 'porto-functionality' ), 'value': 'nav-pos-outside' } ] }
							onChange={ ( value ) => { props.setAttributes( { nav_pos2: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && ( attrs.nav_pos === '' || attrs.nav_pos === 'nav-bottom' || attrs.nav_pos === 'nav-center-images-only' ) && (
						<SelectControl
							label={ __( 'Nav Type', 'porto-functionality' ) }
							value={ attrs.nav_type }
							options={ porto_block_vars.carousel_nav_types }
							onChange={ ( value ) => { props.setAttributes( { nav_type: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && (
						<ToggleControl
							label={ __( 'Show Nav on Hover', 'porto-functionality' ) }
							checked={ attrs.show_nav_hover }
							onChange={ ( value ) => { props.setAttributes( { show_nav_hover: value } ); } }
						/>
					) }
					{ attrs.slider_config && (
						<ToggleControl
							label={ __( 'Show Slider Pagination', 'porto-functionality' ) }
							checked={ attrs.show_dots }
							onChange={ ( value ) => { props.setAttributes( { show_dots: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_dots && (
						<SelectControl
							label={ __( 'Dots Position', 'porto-functionality' ) }
							value={ attrs.dots_pos }
							options={ [ { 'label': __( 'Bottom', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Top right', 'porto-functionality' ), 'value': 'show-dots-title-right' } ] }
							onChange={ ( value ) => { props.setAttributes( { dots_pos: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_dots && (
						<SelectControl
							label={ __( 'Dots Style', 'porto-functionality' ) }
							value={ attrs.dots_style }
							options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Circle inner dot', 'porto-functionality' ), 'value': 'dots-style-1' } ] }
							onChange={ ( value ) => { props.setAttributes( { dots_style: value } ); } }
						/>
					) }
					{ attrs.slider_config && (
						<SelectControl
							label={ __( 'Auto Play', 'porto-functionality' ) }
							value={ attrs.autoplay }
							options={ [ { 'label': __( 'Theme Options', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Yes', 'porto-functionality' ), 'value': 'yes' }, { 'label': __( 'No', 'porto-functionality' ), 'value': 'no' } ] }
							onChange={ ( value ) => { props.setAttributes( { autoplay: value } ); } }
						/>
					) }
					{ attrs.slider_config && (
						<RangeControl
							label={ __( 'Auto Play Timeout (ms)', 'porto-functionality' ) }
							value={ attrs.autoplay_timeout }
							min="1000"
							max="20000"
							step="500"
							onChange={ ( value ) => { props.setAttributes( { autoplay_timeout: value } ); } }
						/>
					) }
				</PanelBody>
			</InspectorControls>
		)

		attrs.el_class = attrs.className;

		var renderControls = (
			<Disabled>
				<ServerSideRender block={ name } attributes={ attrs } />
			</Disabled>
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-recent-portfolios', {
		title: 'Porto Recent Portfolios',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			view: {
				type: 'string',
				default: 'classic',
			},
			info_view: {
				type: 'string',
				default: '',
			},
			image_size: {
				type: 'string',
			},
			thumb_bg: {
				type: 'string',
				default: '',
			},
			thumb_image: {
				type: 'string',
				default: ''
			},
			ajax_load: {
				type: 'boolean',
			},
			ajax_modal: {
				type: 'boolean',
			},
			number: {
				type: 'int',
				default: 8,
			},
			cats: {
				type: 'string',
			},
			post_in: {
				type: 'string',
			},
			items: {
				type: 'int',
			},
			items_desktop: {
				type: 'int',
				default: 4,
			},
			items_tablets: {
				type: 'int',
				default: 3,
			},
			items_mobile: {
				type: 'int',
				default: 2,
			},
			items_row: {
				type: 'int',
				default: 1,
			},
			slider_config: {
				type: 'boolean',
				default: false,
			},
			show_nav: {
				type: 'boolean',
				default: false,
			},
			show_nav_hover: {
				type: 'boolean',
				default: false,
			},
			nav_pos: {
				type: 'string',
				default: '',
			},
			nav_pos2: {
				type: 'string',
			},
			nav_type: {
				type: 'string',
			},
			show_dots: {
				type: 'boolean',
				default: false,
			},
			dots_pos: {
				type: 'string',
			},
			dots_style: {
				type: 'string',
			},
			autoplay: {
				type: 'boolean',
				default: false,
			},
			autoplay_timeout: {
				type: 'int',
				default: 5000,
			},
		},
		edit: PortoRecentPortfolios,
		save: function () {
			return null;
		}
	} );

	/**
	 * 23. Porto Members
	 */
	const PortoMembers = ( props ) => {
		const attrs = props.attributes,
			name = props.name;

		const grid_layouts = [];
		for ( var i = 1; i <= 14; i++ ) {
			grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
		}

		const infoColorSettings = [];
		if ( ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && 'left-info-no-bg' === attrs.info_view ) {
			infoColorSettings.push(
				{
					label: __( 'Info Color', 'porto-functionality' ),
					value: attrs.info_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { info_color: value } );
					}
				}
			);
			if ( attrs.custom_portfolios ) {
				infoColorSettings.push(
					{
						label: __( 'Info Color for custom portfolios', 'porto-functionality' ),
						value: attrs.info_color2,
						onChange: function onChange( value ) {
							return props.setAttributes( { info_color2: value } );
						}
					}
				);
			}
		}

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Member Layout', 'porto-functionality' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Title', 'porto-functionality' ) }
						value={ attrs.title }
						onChange={ ( value ) => { props.setAttributes( { title: value } ); } }
					/>
					<SelectControl
						label={ __( 'Style', 'porto-functionality' ) }
						value={ attrs.style }
						options={ [ { 'label': __( 'Baisc', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Advanced', 'porto-functionality' ), 'value': 'advanced' } ] }
						onChange={ ( value ) => { props.setAttributes( { style: value } ); } }
					/>
					{ '' === attrs.style && (
						<RangeControl
							label={ __( 'Columns', 'porto-functionality' ) }
							value={ attrs.columns }
							min="1"
							max="6"
							onChange={ ( value ) => { props.setAttributes( { columns: value } ); } }
						/>
					) }
					{ '' === attrs.style && (
						<SelectControl
							label={ __( 'View Type', 'porto-functionality' ) }
							value={ attrs.view }
							options={ porto_block_vars.member_layouts }
							onChange={ ( value ) => { props.setAttributes( { view: value } ); } }
						/>
					) }
					<SelectControl
						label={ __( 'Hover Image Effect', 'porto-functionality' ) }
						value={ attrs.hover_image_effect }
						options={ [ { 'label': __( 'Zoom', 'porto-functionality' ), 'value': 'zoom' }, { 'label': __( 'No_Zoom', 'porto-functionality' ), 'value': 'no_zoom' } ] }
						onChange={ ( value ) => { props.setAttributes( { hover_image_effect: value } ); } }
					/>
					{ '' === attrs.style && (
						<ToggleControl
							label={ __( 'Show Overview', 'porto-functionality' ) }
							checked={ attrs.overview }
							onChange={ ( value ) => { props.setAttributes( { overview: value } ); } }
						/>
					) }
					<ToggleControl
						label={ __( 'Show Social Links', 'porto-functionality' ) }
						checked={ attrs.socials }
						onChange={ ( value ) => { props.setAttributes( { socials: value } ); } }
					/>
					{ 'outimage_cat' === attrs.view && (
						<ToggleControl
							label={ __( 'Show Role', 'porto-functionality' ) }
							checked={ attrs.role }
							onChange={ ( value ) => { props.setAttributes( { role: value } ); } }
						/>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Members Selector', 'porto-functionality' ) } initialOpen={ false }>
					<PortoAjaxSelect2Control
						label={ __( 'Categories', 'porto-functionality' ) }
						value={ attrs.cats }
						option="member_cat"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { cats: value } ); } }
					/>
					<PortoAjaxSelect2Control
						label={ __( 'Members', 'porto-functionality' ) }
						value={ attrs.post_in }
						option="member"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { post_in: value } ); } }
					/>
					<RangeControl
						label={ __( 'Member Count', 'porto-functionality' ) }
						value={ attrs.number }
						min="0"
						max="32"
						onChange={ ( value ) => { props.setAttributes( { number: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Show Archive Link', 'porto-functionality' ) }
						checked={ attrs.view_more }
						onChange={ ( value ) => { props.setAttributes( { view_more: value } ); } }
					/>
					{ attrs.view_more && (
						<TextControl
							label={ __( 'Extra class name for Archive Link', 'porto-functionality' ) }
							value={ attrs.view_more_class }
							onChange={ ( value ) => { props.setAttributes( { view_more_class: value } ); } }
						/>
					) }
					<ToggleControl
						label={ __( 'Show Pagination', 'porto-functionality' ) }
						checked={ attrs.pagination }
						onChange={ ( value ) => { props.setAttributes( { pagination: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Show Filter', 'porto-functionality' ) }
						checked={ attrs.filter }
						onChange={ ( value ) => { props.setAttributes( { filter: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Enable Ajax Load', 'porto-functionality' ) }
						checked={ attrs.ajax_load }
						onChange={ ( value ) => { props.setAttributes( { ajax_load: value } ); } }
					/>
					{ attrs.ajax_load && (
						<ToggleControl
							label={ __( 'Ajax Load on Modal', 'porto-functionality' ) }
							checked={ attrs.ajax_modal }
							onChange={ ( value ) => { props.setAttributes( { ajax_modal: value } ); } }
						/>
					) }
				</PanelBody>
			</InspectorControls>
		)

		attrs.el_class = attrs.className;

		var renderControls = (
			<Disabled>
				<ServerSideRender block={ name } attributes={ attrs } />
			</Disabled>
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-members', {
		title: 'Porto Members',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			style: {
				type: 'string',
				default: '',
			},
			columns: {
				type: 'int',
				default: 4,
			},
			view: {
				type: 'string',
				default: 'classic',
			},
			hover_image_effect: {
				type: 'string',
				default: 'zoom',
			},
			overview: {
				type: 'boolean',
				default: true,
			},
			socials: {
				type: 'boolean',
				default: true,
			},
			role: {
				type: 'boolean',
			},
			cats: {
				type: 'string',
			},
			post_in: {
				type: 'string',
			},
			number: {
				type: 'int',
				default: 8,
			},
			view_more: {
				type: 'boolean',
			},
			view_more_class: {
				type: 'string',
			},
			pagination: {
				type: 'boolean',
			},
			filter: {
				type: 'boolean',
			},
			ajax_load: {
				type: 'boolean',
			},
			ajax_modal: {
				type: 'boolean',
			},
		},
		edit: PortoMembers,
		save: function () {
			return null;
		}
	} );

	/**
	 * 24. Porto Recent Members
	 */
	const PortoRecentMembers = ( props ) => {
		const attrs = props.attributes,
			name = props.name;

		const grid_layouts = [];
		for ( var i = 1; i <= 14; i++ ) {
			grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
		}

		const infoColorSettings = [];
		if ( ( attrs.portfolio_layout === "grid" || attrs.portfolio_layout === "masonry" || attrs.portfolio_layout === "timeline" || attrs.portfolio_layout === "creative" || attrs.portfolio_layout === "masonry-creative" ) && 'left-info-no-bg' === attrs.info_view ) {
			infoColorSettings.push(
				{
					label: __( 'Info Color', 'porto-functionality' ),
					value: attrs.info_color,
					onChange: function onChange( value ) {
						return props.setAttributes( { info_color: value } );
					}
				}
			);
			if ( attrs.custom_portfolios ) {
				infoColorSettings.push(
					{
						label: __( 'Info Color for custom portfolios', 'porto-functionality' ),
						value: attrs.info_color2,
						onChange: function onChange( value ) {
							return props.setAttributes( { info_color2: value } );
						}
					}
				);
			}
		}

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Member Layout', 'porto-functionality' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Title', 'porto-functionality' ) }
						value={ attrs.title }
						onChange={ ( value ) => { props.setAttributes( { title: value } ); } }
					/>
					<SelectControl
						label={ __( 'View Type', 'porto-functionality' ) }
						value={ attrs.view }
						options={ porto_block_vars.member_layouts }
						onChange={ ( value ) => { props.setAttributes( { view: value } ); } }
					/>
					<SelectControl
						label={ __( 'Hover Image Effect', 'porto-functionality' ) }
						value={ attrs.hover_image_effect }
						options={ [ { 'label': __( 'Zoom', 'porto-functionality' ), 'value': 'zoom' }, { 'label': __( 'No_Zoom', 'porto-functionality' ), 'value': 'no_zoom' } ] }
						onChange={ ( value ) => { props.setAttributes( { hover_image_effect: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Show Overview', 'porto-functionality' ) }
						checked={ attrs.overview }
						onChange={ ( value ) => { props.setAttributes( { overview: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Show Social Links', 'porto-functionality' ) }
						checked={ attrs.socials }
						onChange={ ( value ) => { props.setAttributes( { socials: value } ); } }
					/>
					{ attrs.socials && (
						<ToggleControl
							label={ __( 'Use Social Links Advance Style', 'porto-functionality' ) }
							checked={ attrs.socials_style }
							onChange={ ( value ) => { props.setAttributes( { socials_style: value } ); } }
						/>
					) }
					<RangeControl
						label={ __( 'Column Spacing (px)', 'porto-functionality' ) }
						value={ attrs.spacing }
						min="0"
						max="100"
						onChange={ ( value ) => { props.setAttributes( { spacing: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Large Desktop', 'porto-functionality' ) }
						value={ attrs.items }
						min="1"
						max="8"
						onChange={ ( value ) => { props.setAttributes( { items: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Desktop', 'porto-functionality' ) }
						value={ attrs.items_desktop }
						min="1"
						max="8"
						onChange={ ( value ) => { props.setAttributes( { items_desktop: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Tablets', 'porto-functionality' ) }
						value={ attrs.items_tablets }
						min="1"
						max="6"
						onChange={ ( value ) => { props.setAttributes( { items_tablets: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items to show on Mobile', 'porto-functionality' ) }
						value={ attrs.items_mobile }
						min="1"
						max="4"
						onChange={ ( value ) => { props.setAttributes( { items_mobile: value } ); } }
					/>
					<RangeControl
						label={ __( 'Items Row', 'porto-functionality' ) }
						value={ attrs.items_row }
						min="1"
						max="4"
						onChange={ ( value ) => { props.setAttributes( { items_row: value } ); } }
					/>
					<PortoAjaxSelect2Control
						label={ __( 'Categories', 'porto-functionality' ) }
						value={ attrs.cats }
						option="member_cat"
						multiple="1"
						onChange={ ( value ) => { props.setAttributes( { cats: value } ); } }
					/>
					<RangeControl
						label={ __( 'Members Count', 'porto-functionality' ) }
						value={ attrs.number }
						min="0"
						max="32"
						onChange={ ( value ) => { props.setAttributes( { number: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Enable Ajax Load', 'porto-functionality' ) }
						checked={ attrs.ajax_load }
						onChange={ ( value ) => { props.setAttributes( { ajax_load: value } ); } }
					/>
					{ attrs.ajax_load && (
						<ToggleControl
							label={ __( 'Ajax Load on Modal', 'porto-functionality' ) }
							checked={ attrs.ajax_modal }
							onChange={ ( value ) => { props.setAttributes( { ajax_modal: value } ); } }
						/>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Slider Options', 'porto-functionality' ) } initialOpen={ false }>
					<RangeControl
						label={ __( 'Stage Padding (px)', 'porto-functionality' ) }
						value={ attrs.stage_padding }
						min="0"
						max="100"
						onChange={ ( value ) => { props.setAttributes( { stage_padding: value } ); } }
					/>
					<ToggleControl
						label={ __( 'Change Slider Options', 'porto-functionality' ) }
						checked={ attrs.slider_config }
						onChange={ ( value ) => { props.setAttributes( { slider_config: value } ); } }
					/>
					{ attrs.slider_config && (
						<ToggleControl
							label={ __( 'Show Slider Navigation', 'porto-functionality' ) }
							checked={ attrs.show_nav }
							onChange={ ( value ) => { props.setAttributes( { show_nav: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && (
						<SelectControl
							label={ __( 'Nav Position', 'porto-functionality' ) }
							value={ attrs.nav_pos }
							options={ [ { 'label': __( 'Middle', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Middle of Images', 'porto-functionality' ), 'value': 'nav-center-images-only' }, { 'label': __( 'Top', 'porto-functionality' ), 'value': 'show-nav-title' }, { 'label': __( 'Bottom', 'porto-functionality' ), 'value': 'nav-bottom' } ] }
							onChange={ ( value ) => { props.setAttributes( { nav_pos: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && ( attrs.nav_pos === '' || attrs.nav_pos === 'nav-center-images-only' ) && (
						<SelectControl
							label={ __( 'Nav Inside/Outside?', 'porto-functionality' ) }
							value={ attrs.nav_pos2 }
							options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Inside', 'porto-functionality' ), 'value': 'nav-pos-inside' }, { 'label': __( 'Outside', 'porto-functionality' ), 'value': 'nav-pos-outside' } ] }
							onChange={ ( value ) => { props.setAttributes( { nav_pos2: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && ( attrs.nav_pos === '' || attrs.nav_pos === 'nav-bottom' || attrs.nav_pos === 'nav-center-images-only' ) && (
						<SelectControl
							label={ __( 'Nav Type', 'porto-functionality' ) }
							value={ attrs.nav_type }
							options={ porto_block_vars.carousel_nav_types }
							onChange={ ( value ) => { props.setAttributes( { nav_type: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_nav && (
						<ToggleControl
							label={ __( 'Show Nav on Hover', 'porto-functionality' ) }
							checked={ attrs.show_nav_hover }
							onChange={ ( value ) => { props.setAttributes( { show_nav_hover: value } ); } }
						/>
					) }
					{ attrs.slider_config && (
						<ToggleControl
							label={ __( 'Show Slider Pagination', 'porto-functionality' ) }
							checked={ attrs.show_dots }
							onChange={ ( value ) => { props.setAttributes( { show_dots: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_dots && (
						<SelectControl
							label={ __( 'Dots Position', 'porto-functionality' ) }
							value={ attrs.dots_pos }
							options={ [ { 'label': __( 'Bottom', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Top right', 'porto-functionality' ), 'value': 'show-dots-title-right' } ] }
							onChange={ ( value ) => { props.setAttributes( { dots_pos: value } ); } }
						/>
					) }
					{ attrs.slider_config && attrs.show_dots && (
						<SelectControl
							label={ __( 'Dots Style', 'porto-functionality' ) }
							value={ attrs.dots_style }
							options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Circle inner dot', 'porto-functionality' ), 'value': 'dots-style-1' } ] }
							onChange={ ( value ) => { props.setAttributes( { dots_style: value } ); } }
						/>
					) }
					{ attrs.slider_config && (
						<SelectControl
							label={ __( 'Auto Play', 'porto-functionality' ) }
							value={ attrs.autoplay }
							options={ [ { 'label': __( 'Theme Options', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Yes', 'porto-functionality' ), 'value': 'yes' }, { 'label': __( 'No', 'porto-functionality' ), 'value': 'no' } ] }
							onChange={ ( value ) => { props.setAttributes( { autoplay: value } ); } }
						/>
					) }
					{ attrs.slider_config && (
						<RangeControl
							label={ __( 'Auto Play Timeout (ms)', 'porto-functionality' ) }
							value={ attrs.autoplay_timeout }
							min="1000"
							max="20000"
							step="500"
							onChange={ ( value ) => { props.setAttributes( { autoplay_timeout: value } ); } }
						/>
					) }
				</PanelBody>
			</InspectorControls>
		)

		attrs.el_class = attrs.className;

		var renderControls = (
			<Disabled>
				<ServerSideRender block={ name } attributes={ attrs } />
			</Disabled>
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-recent-members', {
		title: 'Porto Members Carousel',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			view: {
				type: 'string',
				default: 'classic',
			},
			hover_image_effect: {
				type: 'string',
				default: 'zoom',
			},
			overview: {
				type: 'boolean',
				default: true,
			},
			socials: {
				type: 'boolean',
				default: true,
			},
			socials_style: {
				type: 'boolean',
				default: true,
			},
			spacing: {
				type: 'int',
			},
			items: {
				type: 'int',
			},
			items_desktop: {
				type: 'int',
				default: 4,
			},
			items_tablets: {
				type: 'int',
				default: 3,
			},
			items_mobile: {
				type: 'int',
				default: 2,
			},
			items_row: {
				type: 'int',
				default: 1,
			},
			cats: {
				type: 'string',
			},
			number: {
				type: 'int',
				default: 8,
			},
			ajax_load: {
				type: 'boolean',
			},
			ajax_modal: {
				type: 'boolean',
			},
			slider_config: {
				type: 'boolean',
				default: false,
			},
			show_nav: {
				type: 'boolean',
				default: false,
			},
			show_nav_hover: {
				type: 'boolean',
				default: false,
			},
			nav_pos: {
				type: 'string',
				default: '',
			},
			nav_pos2: {
				type: 'string',
			},
			nav_type: {
				type: 'string',
			},
			show_dots: {
				type: 'boolean',
				default: false,
			},
			dots_pos: {
				type: 'string',
			},
			dots_style: {
				type: 'string',
			},
			autoplay: {
				type: 'boolean',
				default: false,
			},
			autoplay_timeout: {
				type: 'int',
				default: 5000,
			},
		},
		edit: PortoRecentMembers,
		save: function () {
			return null;
		}
	} );


	/**
	 * 25. Porto SVG Floating
	 */
	const PortoSvgFloating = ( props ) => {
		const attrs = props.attributes;

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'General', 'porto-functionality' ) } initialOpen={ true }>
					<TextareaControl
						label={ __( 'Please writer your svg code.', 'porto-functionality' ) }
						value={ attrs.float_svg }
						onChange={ ( value ) => { props.setAttributes( { float_svg: value } ); } }
					/>
					<TextControl
						label={ __( 'Please write floating path id using comma. like #path1, #path2.', 'porto-functionality' ) }
						value={ attrs.float_path }
						onChange={ ( value ) => { props.setAttributes( { float_path: value } ); } }
					/>
					<RangeControl
						label={ __( 'Floating Duration', 'porto-functionality' ) }
						value={ attrs.float_duration }
						min="0"
						max="99999"
						onChange={ ( value ) => { props.setAttributes( { float_duration: value } ); } }
					/>			
					<SelectControl
						label={ __( 'Easing Method', 'porto-functionality' ) }
						value={ attrs.float_easing }
						options={ porto_block_vars.easing_methods }
						onChange={ ( value ) => { props.setAttributes( { float_easing: value } ); } }
					/>
					<RangeControl
						label={ __( 'Floating Repeat', 'porto-functionality' ) }
						value={ attrs.float_repeat }
						min="0"
						max="10000"
						onChange={ ( value ) => { props.setAttributes( { float_repeat: value } ); } }
					/>	
					<RangeControl
						label={ __( 'Repeat Delay', 'porto-functionality' ) }
						value={ attrs.float_repeat_delay }
						min="0"
						max="100000"
						onChange={ ( value ) => { props.setAttributes( { float_repeat_delay: value } ); } }
					/>											
					<ToggleControl
						label={ __( 'yoyo', 'porto-functionality' ) }
						checked={ attrs.float_yoyo }
						onChange={ ( value ) => { props.setAttributes( { float_yoyo: value } ); } }
					/>
				</PanelBody>
			</InspectorControls>
		)

		attrs.el_class = attrs.className;
		var float_path = attrs.float_path;
		var floatScript = 'jQuery(document).ready(function($) {if (typeof KUTE != \'undefined\') {';
		if( float_path && typeof float_path == 'string' ) {
			float_path = float_path.split( ',' );
			float_path.map( function(path) {
				if( path != '' ) {
					floatScript += `if( $('${path}').get(0) ) {`;
					floatScript += `var shape1 = KUTE.fromTo(${path},{`;
					floatScript += `'path': ${path},`;
					floatScript += `}, {`;
					floatScript += `'path': ${path}.replace('start',end)`;
					floatScript += `}, {`;
					floatScript += `'duration': ${attrs.float_duration},`;
					floatScript += `'easing': ${attrs.float_easing},`;
					floatScript += `'repeat': ${attrs.float_repeat},`;
					floatScript += `'repeatDelay': ${attrs.float_repeat_delay},`;
					floatScript += `'yoyo': ${attrs.float_yoyo},`;
					floatScript += `}).start();`;
					floatScript += '}';
				}
			});

		}
		floatScript += '}});'
		var renderControls = (
			<>
			<div className={attrs.el_class} dangerouslySetInnerHTML={ { __html: attrs.float_svg } } />
			{
				float_path && float_path.length > 0 && ( 
					<script dangerouslySetInnerHTML={{ __html: floatScript }} />
				)
			}
			</>
		);

		return [
			inspectorControls,
			renderControls,
		];
	}

	registerBlockType( 'porto/porto-svg-floating', {
		title: 'Porto Svg Floating',
		icon: 'porto',
		category: 'porto',
		attributes: {
			float_svg: {
				type: 'string',
			},
			float_path: {
				type: 'string',
			},
			float_duration: {
				type: 'int',
				default: 10000
			},
			float_easing: {
				type: 'string',
				default: 'easingQuadraticInOut'
			},
			float_repeat: {
				type: 'int',
				default: 20
			},
			float_repeat_delay: {
				type: 'int',
				default: 1000
			},
			float_yoyo: {
				type: 'boolean',
				default: true
			},
			page_builder: {
				type: 'string',
				default: 'gutenberg'
			}
		},
		edit: PortoSvgFloating,
		save: function () {
			return null;
		}
	} );

} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );
