/**
 * Porto Gutenberg blocks
 *
 * 1. Porto Recent Posts
 * 2. Porto Carousel
 * 3. Porto Blog
 * 4. Porto Ultimate Heading
 * 5. Porto Info Box
 * 6. Porto Stat Counter
 * 7. Porto Google Map
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
 * /

/**
 * 1. Porto Recent Posts
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		withSelect = wpData.withSelect,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		ServerSideRender = wpComponents.ServerSideRender,
		QueryControls = wpComponents.QueryControls,
		TextControl = wpComponents.TextControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		Spinner = wpComponents.Spinner,
		pickBy = lodash.pickBy,
		isUndefined = lodash.isUndefined;

	var prevNumber = null, prevColumns = null, prevCats = null, prevView = null, refresh_timer;


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
		edit: withSelect( function( select, props ) {
			var _select = select('core'),
				getEntityRecords = _select.getEntityRecords;

			var attrs = props.attributes,
				category = attrs.cats,
				numberOfPosts = attrs.number;

			var recentPostsQuery = pickBy({
				categories: category,
				per_page: numberOfPosts,
			}, function (value) {
				return !isUndefined(value);
			});

			var categoriesListQuery = {
				per_page: 99
			};

			return {
				recentPosts: getEntityRecords('postType', 'post', recentPostsQuery),
				categoriesList: getEntityRecords( 'taxonomy', 'category', categoriesListQuery ),
			};
		} )( function( props ) {
			var attrs = props.attributes,
				author = attrs.author,
				view = attrs.view,
				show_image = attrs.show_image,
				show_metas = attrs.show_metas,
				clientId = props.clientId,
				widgetTitle = attrs.title;
			if ( ! props.categoriesList ) {
				props.categoriesList = [];
			}
			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __('Title'),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( SelectControl, {
					label: __('View'),
					value: attrs.view,
					options: [{ label: __('Standard'), value: '' }, { label: __('Read More Link'), value: 'style-1' }, { label: __('Post Meta'), value: 'style-2' }, { label: __('Read More Button'), value: 'style-3' }, { label: __('Side Image'), value: 'style-4' }, { label: __('Post Cats'), value: 'style-5' }],
					onChange: ( value ) => { props.setAttributes( { view: value } ); },
				} ),
				( view == 'style-1' || view == 'style-3' ) && el( SelectControl, {
					label: __('Author Name'),
					value: attrs.author,
					options: [{ label: __('Standard'), value: '' }, { label: __('Show'), value: 'show' }, { label: __('Hide'), value: 'hide' }],
					onChange: ( value ) => { props.setAttributes( { author: value } ); },
				} ),
				view == 'style-3' && el( SelectControl, {
					label: __('Button Style'),
					value: attrs.btn_style,
					options: [{ label: __('Standard'), value: '' }, { label: __('Normal'), value: 'btn-normal' }, { label: __('Borders'), value: 'btn-borders' }],
					onChange: ( value ) => { props.setAttributes( { btn_style: value } ); },
				} ),
				view == 'style-3' && el( SelectControl, {
					label: __('Button Size'),
					value: attrs.btn_size,
					options: [{ label: __('Standard'), value: '' }, { label: __('Normal'), value: 'btn-normal' }, { label: __('Small'), value: 'btn-sm' }, { label: __('Extra Small'), value: 'btn-xs' }],
					onChange: ( value ) => { props.setAttributes( { btn_size: value } ); },
				} ),
				view == 'style-3' && el( SelectControl, {
					label: __('Button Color'),
					value: attrs.btn_color,
					options: [{ label: __('Standard'), value: '' }, { label: __('Default'), value: 'btn-default' }, { label: __('Primary'), value: 'btn-primary' }, { label: __('Secondary'), value: 'btn-secondary' }, { label: __('Tertiary'), value: 'btn-tertiary' }, { label: __('Quaternary'), value: 'btn-quaternary' }, { label: __('Dark'), value: 'btn-dark' }, { label: __('Light'), value: 'btn-light' }],
					onChange: ( value ) => { props.setAttributes( { btn_color: value } ); },
				} ),
				el( SelectControl, {
					label: __('Image Size'),
					value: attrs.image_size,
					options: porto_block_vars.image_sizes,
					onChange: ( value ) => { props.setAttributes( { image_size: value } ); },
				} ),
				el( QueryControls, {
					numberOfItems: attrs.number,
					categoriesList: props.categoriesList,
					selectedCategoryId: attrs.cats,
					onCategoryChange: function onCategoryChange(value) {
						return props.setAttributes({ cats: value !== '' ? value : undefined });
					},
					onNumberOfItemsChange: function onNumberOfItemsChange(value) {
						return props.setAttributes({ number: value });
					}
				} ),
				el( ToggleControl, {
					label: __('Show Image'),
					checked: attrs.show_image,
					onChange: ( value ) => { props.setAttributes( { show_image: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Show Post Metas'),
					checked: attrs.show_metas,
					onChange: ( value ) => { props.setAttributes( { show_metas: value } ); },
				} ),
				el( RangeControl, {
					label: __('Excerpt Length'),
					value: attrs.excerpt_length,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { excerpt_length: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items to show on Desktop'),
					value: attrs.items_desktop,
					min: 1,
					max: 8,
					onChange: ( value ) => { props.setAttributes( { items_desktop: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items to show on Tablets'),
					value: attrs.items_tablets,
					min: 1,
					max: 5,
					onChange: ( value ) => { props.setAttributes( { items_tablets: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items to show on Mobile'),
					value: attrs.items_mobile,
					min: 1,
					max: 3,
					onChange: ( value ) => { props.setAttributes( { items_mobile: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items Row'),
					value: attrs.items_row,
					min: 1,
					max: 3,
					onChange: ( value ) => { props.setAttributes( { items_row: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Change Slider Config'),
					checked: attrs.slider_config,
					onChange: ( value ) => { props.setAttributes( { slider_config: value } ); },
				} ),
				attrs.slider_config && el( ToggleControl, {
					label: __('Show Nav'),
					checked: attrs.show_nav,
					onChange: ( value ) => { props.setAttributes( { show_nav: value } ); },
				} ),
				attrs.slider_config && attrs.show_nav && el( SelectControl, {
					label: __('Nav Position'),
					value: attrs.nav_pos,
					options: [{ label: __('Middle'), value: '' }, { label: __('Top'), value: 'show-nav-title' }, { label: __('Bottom'), value: 'nav-bottom' }],
					onChange: ( value ) => { props.setAttributes( { nav_pos: value } ); },
				} ),
				attrs.slider_config && attrs.show_nav && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos ) && el( SelectControl, {
					label: __('Nav Type'),
					value: attrs.nav_type,
					options: [{ label: __('Default'), value: '' }, { label: __('Rounded'), value: 'rounded-nav' }, { label: __('Big & Full Width'), value: 'big-nav' }],
					onChange: ( value ) => { props.setAttributes( { nav_type: value } ); },
				} ),
				attrs.slider_config && attrs.show_nav && el( ToggleControl, {
					label: __('Show Nav on Hover'),
					checked: attrs.show_nav_hover,
					onChange: ( value ) => { props.setAttributes( { show_nav_hover: value } ); },
				} ),
				attrs.slider_config && el( ToggleControl, {
					label: __('Show Dots'),
					checked: attrs.show_dots,
					onChange: ( value ) => { props.setAttributes( { show_dots: value } ); },
				} ),
				attrs.slider_config && attrs.show_dots && el( SelectControl, {
					label: __('Dots Position'),
					value: attrs.dots_pos,
					options: [{ label: __('Outside'), value: '' }, { label: __('Besides Title'), value: 'show-dots-title' }, { label: __('Top right'), value: 'show-dots-title-right' }],
					onChange: ( value ) => { props.setAttributes( { dots_pos: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
					value: attrs.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} ),

			);

			var hasPosts = Array.isArray(props.recentPosts) && props.recentPosts.length;

			if (!hasPosts) {
				return [
					inspectorControls,
					el(
						Placeholder,
						{
							label: __('Porto Recent Posts Block')
						},
						!Array.isArray(props.recentPosts) ? el(Spinner, null) : __('No posts found!')
					)
				];
			}

			const $slider = jQuery('#block-' + clientId + ' .owl-carousel');
			if (!$slider.data('owl.carousel') || prevNumber == null || prevNumber != attrs.number || prevColumns != attrs.items_desktop || prevCats != attrs.cats || prevView != attrs.view) {
				if ($slider.data('owl.carousel')) {
					$slider.trigger('destroy.owl.carousel');
				}
				if (refresh_timer) {
					clearTimeout(refresh_timer);
				}
				refresh_timer = setTimeout(function() {
					$slider.owlCarousel({
						items: attrs.items_desktop,
						navText: ["", ""],
					});
				}, 500);
			}

			prevNumber = attrs.number;
			prevColumns = attrs.items_desktop;
			prevCats = attrs.cats;
			prevView = attrs.view;

			return [
				inspectorControls,

				el(
					'div',
					{ className: 'porto-recent-posts' + (attrs.className ? ' ' + attrs.className : '') },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'post-carousel porto-carousel owl-carousel' },
						props.recentPosts.map(function(post, index) {
							var featuredImageSrc = post.featured_image_src['list'][0];
							return el(
								'div',
								{ className: 'post-item' },
								show_image && featuredImageSrc && el(
									'span',
									{ className: 'post-image thumb-info thumb-info-hide-wrapper-bg mb-3' },
									el(
										'span',
										{ className: 'thumb-info-wrapper' },
										el('img', { src: featuredImageSrc, alt: __('Post Image') })
									)
								),
								show_metas && el(
									'span',
									{ className: 'meta-date' },
									el(
										'i',
										{ className: 'far fa-clock' },
									),
									' ' + moment(post.date_gmt).local().format('DD MMMM, Y')
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
										__('Read More') + ' '
									),
									el(
										'i',
										{ className: 'fas fa-angle-right' },
									)
								)

							)
						} )
					)
				),
			];
		}),
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 2. Porto Carousel
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		InspectorControls = wpBlockEditor.InspectorControls,
		el = wpElement.createElement,
		ServerSideRender = wpComponents.ServerSideRender,
		QueryControls = wpComponents.QueryControls,
		TextControl = wpComponents.TextControl,
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder,
		Spinner = wpComponents.Spinner,
		pickBy = lodash.pickBy,
		isUndefined = lodash.isUndefined;

	class PortoCarousel extends wpElement.Component {
		constructor() {
			super( ...arguments );
			this.initCarousel = this.initCarousel.bind(this);
		}

		initCarousel( prevProps ) {
			let attrs = this.props.attributes;
			const clientId = this.props.clientId;
			const $slider = jQuery('#block-' + clientId).find('.block-editor-block-list__layout').eq(0);
			if ( !$slider.length ) {
				return;
			}
			if (typeof $slider.data('owl.carousel') != 'undefined') {
				$slider.trigger('destroy.owl.carousel');
			}
			let lg = attrs.items_lg ? attrs.items_lg : attrs.items,
			md = attrs.items_md ? attrs.items_md : attrs.items,
			sm = attrs.items_sm ? attrs.items_sm : attrs.items,
			xs = attrs.items_xs ? attrs.items_xs : attrs.items,
			items = attrs.items ? attrs.items : (lg ? lg : 1),
			count = $slider.find('> *').length;
			let responsive = {};
			responsive[1200] = { items: items, loop: (attrs.loop && count > items) ? true : false, mergeFit: attrs.mergeFit };
			if (lg) responsive[992] = { items: lg, loop: (attrs.loop && count > lg) ? true : false, mergeFit: attrs.mergeFit_lg };
			if (md) responsive[768] = { items: md, loop: (attrs.loop && count > md) ? true : false, mergeFit: attrs.mergeFit_md };
			if (sm) responsive[481] = { items: sm, loop: (attrs.loop && count > sm) ? true : false, mergeFit: attrs.mergeFit_sm };
			if (xs) responsive[0] = { items: xs, loop: (attrs.loop && count > xs) ? true : false, mergeFit: attrs.mergeFit_xs };


			let classes = 'porto-carousel owl-carousel';
			if (attrs.stage_padding) {
				classes += ' stage-margin';
			}
			if (attrs.show_nav) {
				if (attrs.nav_pos) classes += ' ' + attrs.nav_pos;
				if (attrs.nav_type) classes += ' ' + attrs.nav_type;
				if (attrs.show_nav_hover) classes += ' show-nav-hover';
			}
			classes += ' has-ccols';
			classes += ' ccols-' + parseInt(attrs.items, 10);
			if ( attrs.className ) {
				classes += ' ' + attrs.className;
			}

			if (attrs.show_dots && attrs.dots_pos) {
				classes += ' ' + attrs.dots_pos + ' ' + attrs.dots_align;
			}

			if ( typeof prevProps != 'undefined' && prevProps.attributes ) {
				$slider.removeClass('ccols-' + prevProps.attributes.items);
			}
			$slider.addClass(classes);
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

		componentDidMount() {
			this.initCarousel();
		}

		componentDidUpdate( prevProps, prevState ) {
			this.initCarousel( prevProps );
		}

		render() {
			var props = this.props,
				clientId = props.clientId,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, {},
				el( RangeControl, {
					label: __('Stage Padding'),
					value: attrs.stage_padding,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { stage_padding: value } ); },
				} ),
				el( RangeControl, {
					label: __('Item Margin'),
					value: attrs.margin,
					min: 0,
					max: 40,
					onChange: ( value ) => { props.setAttributes( { margin: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Auto Play'),
					checked: attrs.autoplay,
					onChange: ( value ) => { props.setAttributes( { autoplay: value } ); },
				} ),
				attrs.autoplay && el( TextControl, {
					label: __('Auto Play Timeout'),
					value: attrs.autoplay_timeout,
					onChange: ( value ) => { props.setAttributes( { autoplay_timeout: value } ); },
				} ),
				attrs.autoplay && el( ToggleControl, {
					label: __('Pause on Mouse Hover'),
					checked: attrs.autoplay_hover_pause,
					onChange: ( value ) => { props.setAttributes( { autoplay_hover_pause: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items'),
					value: attrs.items,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { items: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items on Desktop'),
					value: attrs.items_lg,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { items_lg: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items on Tablet'),
					value: attrs.items_md,
					min: 1,
					max: 8,
					onChange: ( value ) => { props.setAttributes( { items_md: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items on Mobile'),
					value: attrs.items_sm,
					min: 1,
					max: 5,
					onChange: ( value ) => { props.setAttributes( { items_sm: value } ); },
				} ),
				el( RangeControl, {
					label: __('Items on Mini'),
					value: attrs.items_xs,
					min: 1,
					max: 3,
					onChange: ( value ) => { props.setAttributes( { items_xs: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Show Nav'),
					checked: attrs.show_nav,
					onChange: ( value ) => { props.setAttributes( { show_nav: value } ); },
				} ),
				attrs.show_nav && el( SelectControl, {
					label: __('Nav Position'),
					value: attrs.nav_pos,
					options: [{ label: __('Middle'), value: '' }, { label: __('Middle of Images'), value: 'nav-center-images-only' }, { label: __('Top'), value: 'show-nav-title' }, { label: __('Bottom'), value: 'nav-bottom' }],
					onChange: ( value ) => { props.setAttributes( { nav_pos: value } ); },
				} ),
				attrs.show_nav && '' == attrs.pos && el( SelectControl, {
					label: __('Nav Inside?'),
					value: attrs.nav_pos2,
					options: [{ label: __('Default'), value: '' }, { label: __('Inside'), value: 'nav-pos-inside' }, { label: __('Outside'), value: 'nav-pos-outside' }],
					onChange: ( value ) => { props.setAttributes( { nav_pos2: value } ); },
				} ),
				attrs.show_nav && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
					label: __('Nav Type'),
					value: attrs.nav_type,
					options: porto_block_vars.carousel_nav_types,
					onChange: ( value ) => { props.setAttributes( { nav_type: value } ); },
				} ),
				attrs.show_nav && el( ToggleControl, {
					label: __('Show Nav on Hover'),
					checked: attrs.show_nav_hover,
					onChange: ( value ) => { props.setAttributes( { show_nav_hover: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Show Dots'),
					checked: attrs.show_dots,
					onChange: ( value ) => { props.setAttributes( { show_dots: value } ); },
				} ),
				attrs.show_dots && el( SelectControl, {
					label: __('Dots Position'),
					value: attrs.dots_pos,
					options: [{ label: __('Outside'), value: '' }, { label: __('Inside'), value: 'nav-inside' }, { label: __('Besides Title'), value: 'show-dots-title' }, { label: __('Top right'), value: 'show-dots-title-right' }],
					onChange: ( value ) => { props.setAttributes( { dots_pos: value } ); },
				} ),
				attrs.show_dots && ('nav-inside' == attrs.dots_pos) && el( SelectControl, {
					label: __('Dots Align'),
					value: attrs.dots_align,
					options: [{ label: __('Right'), value: '' }, { label: __('Center'), value: 'nav-inside-center' }, { label: __('Left'), value: 'nav-inside-left' }],
					onChange: ( value ) => { props.setAttributes( { dots_align: value } ); },
				} ),
				el( TextControl, {
					label: __('Item Animation In'),
					value: attrs.animate_in,
					onChange: ( value ) => { props.setAttributes( { animate_in: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types.'),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Item Animation Out'),
					value: attrs.animate_out,
					onChange: ( value ) => { props.setAttributes( { animate_out: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types.'),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( ToggleControl, {
					label: __('Infinite loop'),
					checked: attrs.loop,
					onChange: ( value ) => { props.setAttributes( { loop: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Full Screen'),
					checked: attrs.fullscreen,
					onChange: ( value ) => { props.setAttributes( { fullscreen: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Center Item'),
					checked: attrs.center,
					onChange: ( value ) => { props.setAttributes( { center: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Fetch Videos'),
					checked: attrs.video,
					onChange: ( value ) => { props.setAttributes( { video: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Lazy Load'),
					checked: attrs.lazyload,
					onChange: ( value ) => { props.setAttributes( { lazyload: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Merge Items'),
					checked: attrs.merge,
					onChange: ( value ) => { props.setAttributes( { merge: value } ); },
				} ),
				attrs.merge && el( ToggleControl, {
					label: __('Merge Fit'),
					checked: attrs.mergeFit,
					onChange: ( value ) => { props.setAttributes( { mergeFit: value } ); },
				} ),
				attrs.merge && el( ToggleControl, {
					label: __('Merge Fit on Desktop'),
					checked: attrs.mergeFit_lg,
					onChange: ( value ) => { props.setAttributes( { mergeFit_lg: value } ); },
				} ),
				attrs.merge && el( ToggleControl, {
					label: __('Merge Fit on Tablet'),
					checked: attrs.mergeFit_md,
					onChange: ( value ) => { props.setAttributes( { mergeFit_md: value } ); },
				} ),
				attrs.merge && el( ToggleControl, {
					label: __('Merge Fit on Mobile'),
					checked: attrs.mergeFit_sm,
					onChange: ( value ) => { props.setAttributes( { mergeFit_sm: value } ); },
				} ),
				attrs.merge && el( ToggleControl, {
					label: __('Merge Fit on Mini'),
					checked: attrs.mergeFit_xs,
					onChange: ( value ) => { props.setAttributes( { mergeFit_xs: value } ); },
				} ),


				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types.'),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
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
		}
	}

	registerBlockType( 'porto/porto-carousel', {
		title: 'Porto Carousel',
		icon: 'porto',
		category: 'porto',
		supports: {
			align: ['wide', 'full'],
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
		save: function( props ) {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 3. Porto Blog
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
		edit: withSelect( function( select, props ) {
			var _select = select('core'),
				getEntityRecords = _select.getEntityRecords;

			var attrs = props.attributes,
				category = attrs.cats,
				numberOfPosts = attrs.number;

			var recentPostsQuery = pickBy({
				categories: category,
				per_page: numberOfPosts,
			}, function (value) {
				return !isUndefined(value);
			});

			var categoriesListQuery = {
				per_page: 99
			};

			return {
				recentPosts: getEntityRecords('postType', 'post', recentPostsQuery),
				categoriesList: getEntityRecords( 'taxonomy', 'category', categoriesListQuery ),
			};
		} )( function( props ) {
			var post_layout = props.attributes.post_layout,
				widgetTitle = props.attributes.title,
				attrs = props.attributes;
			if ( ! props.categoriesList ) {
				props.categoriesList = [];
			}
			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __('Title'),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( SelectControl, {
					label: __('Blog Layout'),
					value: attrs.post_layout,
					options: [{ label: __('Full'), value: 'full' }, { label: __('Large'), value: 'large' }, { label: __('Large Alt'), value: 'large-alt' }, { label: __('Medium'), value: 'medium' }, { label: __('Medium Alt'), value: 'medium-alt' }, { label: __('Grid'), value: 'grid' }, { label: __('Timeline'), value: 'timeline' }],
					onChange: ( value ) => { props.setAttributes( { post_layout: value } ); },
				} ),
				( post_layout == 'grid' || post_layout == 'masonry' || post_layout == 'timeline' ) && el( SelectControl, {
					label: __('Post Style'),
					value: attrs.post_style,
					options: [{ label: __('Default'), value: 'default' }, { label: __('Post Carousel Style'), value: 'related' }, { label: __('Hover Info'), value: 'hover_info' }, { label: __('No Margin & Hover Info'), value: 'no_margin' }, { label: __('With Borders'), value: 'padding' }],
					onChange: ( value ) => { props.setAttributes( { post_style: value } ); },
				} ),
				( post_layout == 'grid' || post_layout == 'masonry' ) && el( RangeControl, {
					label: __('Columns'),
					value: attrs.columns,
					min: 1,
					max: 6,
					onChange: ( value ) => { props.setAttributes( { columns: value } ); },
				} ),
				el( QueryControls, {
					categoriesList: props.categoriesList,
					selectedCategoryId: attrs.cats,
					numberOfItems: attrs.number,
					onCategoryChange: function onCategoryChange(value) {
						return props.setAttributes({ cats: value !== '' ? value : undefined });
					},
					onNumberOfItemsChange: function onNumberOfItemsChange(value) {
						return props.setAttributes({ number: value });
					}
				} ),
				el( TextControl, {
					label: __('Post IDs'),
					value: attrs.post_in,
					onChange: ( value ) => { props.setAttributes( { post_in: value } ); },
				} ),
				el( SelectControl, {
					label: __('Pagination Style'),
					value: attrs.view_more,
					options: [{ label: __('No Pagination'), value: '' }, { label: __('Show Pagination'), value: 'show' }, { label: __('Show Blog Page Link'), value: 'link' }],
					onChange: ( value ) => { props.setAttributes( { view_more: value } ); },
				} ),
				( attrs.view_more == 'link' ) && el( TextControl, {
					label: __('Extra class name for Archive Link'),
					value: attrs.view_more_class,
					onChange: ( value ) => { props.setAttributes( { view_more_class: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
					value: attrs.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} ),

			);

			var hasPosts = Array.isArray(props.recentPosts) && props.recentPosts.length;

			if (!hasPosts) {
				return [
					inspectorControls,
					el(
						Placeholder,
						{
							label: __('Porto Blog Block')
						},
						!Array.isArray(props.recentPosts) ? el(Spinner, null) : __('No posts found!')
					)
				];
			}


			var renderControls,
				postsRender,
				paginationRender = '',
				imageSize = 'medium',
				columns = attrs.columns;

			if (attrs.post_layout == 'full' || attrs.post_layout == 'large' || attrs.post_layout == 'large-alt') {
				imageSize = 'landsacpe';
				columns = 1;
			} else if (attrs.post_layout == 'medium' || attrs.post_layout == 'medium-alt') {
				imageSize = 'list';
				columns = 1;
			}

			postsRender = props.recentPosts.map(function(post, index) {
				var featuredImageSrc = post.featured_image_src[imageSize][0];
				return el(
					'article',
					{ className: 'post post-' + attrs.post_layout },
					featuredImageSrc && el(
						'div',
						{ className: 'post-image' },
						el(
							'a',
							{ href: post.link },
							el(
								'div',
								{ className: 'img-thumbnail' },
								el('img', { src: featuredImageSrc, alt: __('Post Image') })
							)
						)
					),
					el(
						'div',
						{ className: 'post-content' },
						el(
							'h4',
							{ className: 'entry-title' },
							el(
								'a',
								{ href: post.link },
								post.title.rendered
							)
						),
						el(
							'p',
							{ className: 'post-excerpt' },
							post.post_excerpt_stackable
						)
					),
					el(
						'div',
						{ className: 'post-meta' },
						el(
							'span',
							{ className: 'meta-date' },
							el(
								'i',
								{ className: 'far fa-clock' },
							),
							' ' + moment(post.date_gmt).local().format('DD MMMM, Y')
						),
					),
					el(
						'a',
						{ className: 'btn read-more', href: post.link },
						el(
							'span',
							null,
							__('Read More')
						)
					)
				)
			} );

			if (attrs.post_layout == 'timeline') {
				renderControls = el(
					'div',
					{ className: 'porto-blog' + (attrs.className ? ' ' + attrs.className : '') },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'porto-blog-posts posts-' + attrs.post_layout + (attrs.post_style ? ' blog-posts-' + attrs.post_style : '') },
						el(
							'section',
							{ className: 'timeline' },
							el(
								'div',
								{ className: 'timeline-body posts-container' + ' columns-2' },
								postsRender,
							)
						)
					),
					paginationRender
				);
			} else if (attrs.post_layout == 'grid' || attrs.post_layout == 'masonry') {
				renderControls = el(
					'div',
					{ className: 'porto-blog' + (attrs.className ? ' ' + attrs.className : '') },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'porto-blog-posts posts-' + attrs.post_layout + (attrs.post_style ? ' blog-posts-' + attrs.post_style : '') },
						el(
							'div',
							{ className: 'posts-container row' + ' columns-' + columns },
							postsRender
						)
					),
					paginationRender
				);
			} else {
				renderControls = el(
					'div',
					{ className: 'porto-blog' + (attrs.className ? ' ' + attrs.className : '') },
					widgetTitle && el(
						'h4',
						{},
						widgetTitle,
					),
					el(
						'div',
						{ className: 'porto-blog-posts posts-container posts-' + attrs.post_layout + ' columns-' + columns },
						postsRender
					),
					paginationRender
				);
			}

			return [
				inspectorControls,

				renderControls,
			];
		}),
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 4. Porto Google Map
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
			this.initMap = this.initMap.bind(this);
		}

		componentDidMount() {
			this.initMap();
		}

		componentDidUpdate(prevProps, prevState) {
			var prevLat = prevProps.attributes.lat,
				prevLng = prevProps.attributes.lng,
				attrs = this.props.attributes;
			if (prevState !== this.state)
				return null;

			if (prevProps.attributes !== attrs) {
				clearTimeout(mapWillUpdate);
				mapWillUpdate = setTimeout(this.initMap, 1000);
			}
		}

		portoURLDecode(str) {
			return decodeURIComponent( (str + '').replace(/%(?![\da-f]{2})/gi, function () {
			  return '%25'
			}) );
		}

		initMap() {
			if (typeof google === 'undefined') {
				return null;
			}
			var attrs = this.props.attributes,
				mapId = 'map_' + this.props.clientId,
				coordinateId = new google.maps.LatLng(attrs.lat, attrs.lng),
				mapOptions = {
					scaleControl: true,
					streetViewControl: ('true' === attrs.streetviewcontrol),
					mapTypeControl: ('true' === attrs.maptypecontrol),
					panControl: ('true' === attrs.pancontrol),
					zoomControl: ('true' === attrs.zoomcontrol),
					scrollwheel: !attrs.scrollwheel,
					draggable: ('true' === attrs.dragging),
					zoomControlOptions: {
						position: google.maps.ControlPosition[attrs.zoomcontrolposition]
					}
				},
				styledMap,
				mapObj = this.state.currentMap,
				markerObj = this.state.currentMarker,
				infowindow = this.state.currentInfo;
			if (!attrs.map_style) {
				mapOptions.mapTypeId = google.maps.MapTypeId[attrs.map_type];
			} else {
				mapOptions.mapTypeControlOptions = {
					mapTypeIds: [google.maps.MapTypeId[attrs.map_type], 'map_style']
				};
				var styles = this.portoURLDecode(jQuery.base64.decode(attrs.map_style));
				styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});
			}
			//if (!mapObj) {
				mapObj = new google.maps.Map(document.getElementById(mapId), mapOptions);
				this.setState( { currentMap: mapObj } );
			//}
			mapObj.setCenter(coordinateId);
			mapObj.setZoom(attrs.zoom);
			if (attrs.map_style) {
				mapObj.mapTypes.set('map_style', styledMap);
				mapObj.setMapTypeId('map_style');
			}

			function toggleBounce() {
				if (markerObj.getAnimation() != null) {
					markerObj.setAnimation(null);
				} else {
					markerObj.setAnimation(google.maps.Animation.BOUNCE);
				}
			}

			if (attrs.lat && attrs.lng) {
				if (!markerObj) {
					markerObj = new google.maps.Marker({
						position: new google.maps.LatLng(attrs.lat, attrs.lng),
						animation: google.maps.Animation.DROP,
						map: mapObj,
						icon: attrs.icon_img_url
					});
					this.setState( { currentMarker: markerObj } );
				}
				if (typeof attrs.icon_img_url != 'undefined') {
					markerObj.setIcon(attrs.icon_img_url);
				}
				google.maps.event.addListener(markerObj, 'click', toggleBounce);

				if ( jQuery.trim(attrs.content) !== "" ) {
					if (!infowindow) {
						infowindow = new google.maps.InfoWindow();
						this.setState( { currentInfo: infowindow } );
					}
					infowindow.setContent('<div class="map_info_text" style="color:#000;">' + jQuery.trim(attrs.content.replace('/\s+/', ' ')) + '</div>');

					if(attrs.infowindow_open == 'off') {
						infowindow.open(mapObj, markerObj);
					}

					google.maps.event.addListener(markerObj, 'click', function() {
						infowindow.open(mapObj, markerObj);
					});

				}
			}
			google.maps.event.trigger(mapObj, 'resize');

		}

		render() {
			var props = this.props,
				widgetTitle = props.attributes.title,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __('Width (in %)'),
					value: attrs.width,
					onChange: ( value ) => { props.setAttributes( { width: value } ); },
				} ),
				el( TextControl, {
					label: __('Height (in px)'),
					value: attrs.height,
					onChange: ( value ) => { props.setAttributes( { height: value } ); },
				} ),
				el( SelectControl, {
					label: __('Map type'),
					value: attrs.map_type,
					options: [{ label: __('Roadmap'), value: 'ROADMAP' }, { label: __('Satellite'), value: 'SATELLITE' }, { label: __('Hybrid'), value: 'HYBRID' }, { label: __('Terrain'), value: 'TERRAIN' }],
					onChange: ( value ) => { props.setAttributes( { map_type: value } ); },
				} ),
				el( TextControl, {
					label: __('Latitude'),
					value: attrs.lat,
					onChange: ( value ) => { props.setAttributes( { lat: value } ); },
				} ),
				el( TextControl, {
					label: __('Longitude'),
					value: attrs.lng,
					onChange: ( value ) => { props.setAttributes( { lng: value } ); },
				} ),
				el(
					'p',
					null,
					el(
						'a',
						{ href: 'http://universimmedia.pagesperso-orange.fr/geo/loc.htm', target: '_blank' },
						__('Here is a tool')
					),
					' ' + __('where you can find Latitude & Longitude of your location')
				),
				el( RangeControl, {
					label: __('Map Zoom'),
					value: attrs.zoom,
					min: 1,
					max: 20,
					onChange: ( value ) => { props.setAttributes( { zoom: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Disable map zoom on mouse wheel scroll'),
					checked: attrs.scrollwheel,
					onChange: ( value ) => { props.setAttributes( { scrollwheel: value } ); },
				} ),
				el( TextControl, {
					label: __('Info Window Text'),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} ),
				el( SelectControl, {
					label: __('Open on Marker Click'),
					value: attrs.infowindow_open,
					options: [{ label: __('Yes'), value: 'on' }, { label: __('No'), value: 'off' }],
					onChange: ( value ) => { props.setAttributes( { infowindow_open: value } ); },
				} ),
				el( SelectControl, {
					label: __('Marker/Point icon'),
					value: attrs.marker_icon,
					options: [{ label: __('Use Google Default'), value: 'default' }, { label: __('Upload Custom'), value: 'custom' }],
					onChange: ( value ) => { props.setAttributes( { marker_icon: value } ); },
				} ),
				'custom' == attrs.marker_icon && el( MediaUpload, {
					allowedTypes: ['image'],
					value: attrs.icon_img,
					onSelect: function onSelect(image) {
						return props.setAttributes({ icon_img_url: image.url, icon_img: image.id });
					},
					render: function render(_ref) {
						var open = _ref.open;
						return el(IconButton, {
							className: 'components-toolbar__control',
							label: __('Change image'),
							icon: 'edit',
							onClick: open
						});
					}
				}),
				'custom' == attrs.marker_icon && el(IconButton, {
					className: 'components-toolbar__control',
					label: __('Remove image'),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes({ icon_img_url: undefined, icon_img: undefined });
					}
				}),
				el( SelectControl, {
					label: __('Street view control'),
					value: attrs.streetviewcontrol,
					options: [{ label: __('Disable'), value: 'false' }, { label: __('Enable'), value: 'true' }],
					onChange: ( value ) => { props.setAttributes( { streetviewcontrol: value } ); },
				} ),
				el( SelectControl, {
					label: __('Map type control'),
					value: attrs.maptypecontrol,
					options: [{ label: __('Disable'), value: 'false' }, { label: __('Enable'), value: 'true' }],
					onChange: ( value ) => { props.setAttributes( { maptypecontrol: value } ); },
				} ),
				el( SelectControl, {
					label: __('Zoom control'),
					value: attrs.zoomcontrol,
					options: [{ label: __('Disable'), value: 'false' }, { label: __('Enable'), value: 'true' }],
					onChange: ( value ) => { props.setAttributes( { zoomcontrol: value } ); },
				} ),
				( 'true' == attrs.zoomcontrol ) && el( SelectControl, {
					label: __('Zoom Control Position'),
					value: attrs.zoomcontrolposition,
					options: [{ label: __('Right Bottom'), value: 'RIGHT_BOTTOM' }, { label: __('Right Top'), value: 'RIGHT_TOP' }, { label: __('Right Center'), value: 'RIGHT_CENTER' }, { label: __('Left Top'), value: 'LEFT_TOP' }, { label: __('Left Center'), value: 'LEFT_CENTER' }, { label: __('Left Bottom'), value: 'LEFT_BOTTOM' }],
					onChange: ( value ) => { props.setAttributes( { zoomcontrolposition: value } ); },
				} ),
				el( SelectControl, {
					label: __('Dragging on Mobile'),
					value: attrs.dragging,
					options: [{ label: __('Enable'), value: 'true' }, { label: __('Disable'), value: 'false' }],
					onChange: ( value ) => { props.setAttributes( { dragging: value } ); },
				} ),
				el( SelectControl, {
					label: __('Top margin'),
					value: attrs.top_margin,
					options: [{ label: __('Page (small)'), value: 'page_margin_top' }, { label: __('Section (large)'), value: 'page_margin_top_section' }, { label: __('None'), value: 'none' }],
					onChange: ( value ) => { props.setAttributes( { top_margin: value } ); },
				} ),
				el( SelectControl, {
					label: __('Map Width Override'),
					value: attrs.map_override,
					options: [{ label: __('Default Width'), value: '0' }, { label: __('Apply 1st parent element\'s width'), value: '1' }, { label: __('Apply 2nd parent element\'s width'), value: '2' }, { label: __('Apply 3rd parent element\'s width'), value: '3' }, { label: __('Apply 4th parent element\'s width'), value: '4' }, { label: __('Apply 5th parent element\'s width'), value: '5' }, { label: __('Apply 6th parent element\'s width'), value: '6' }, { label: __('Apply 7th parent element\'s width'), value: '7' }, { label: __('Apply 8th parent element\'s width'), value: '8' }, { label: __('Apply 9th parent element\'s width'), value: '9' }, { label: __('Full Width'), value: 'full' }, { label: __('Maximum Full Width'), value: 'ex-full' }],
					onChange: ( value ) => { props.setAttributes( { map_override: value } ); },
				} ),
				el(
					'p',
					{ style: { fontStyle: 'italic' } },
					__('By default, the map will be given to the Visual Composer row. However, in some cases depending on your theme\'s CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output.'),
				),
				el( TextareaControl, {
					label: __('Google Styled Map JSON'),
					value: attrs.map_style,
					onChange: ( value ) => { props.setAttributes( { map_style: value } ); },
				} ),
				el( 'p',
					{ style: { fontStyle: 'italic' } },
					el( 'a', { target: '_blank', href: 'http://googlemaps.github.io/js-samples/styledmaps/wizard/index.html' }, __('Click here') ),
					' ' + __('to get the style JSON code for styling your map.')
				),

			);

			var mapStyle = {};
			if ( attrs.width ) {
				mapStyle.width = attrs.width;
			}
			if ( attrs.height ) {
				mapStyle.height = attrs.height;
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
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 5. Porto Ultimate heading
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
		SelectControl = wpComponents.SelectControl,
		RangeControl = wpComponents.RangeControl,
		ToggleControl = wpComponents.ToggleControl,
		Placeholder = wpComponents.Placeholder;

	class PortoUltimateHeading extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {
			
		}

		componentDidUpdate(prevProps, prevState) {
			
		}

		render() {
			var props = this.props,
				widgetTitle = props.attributes.title,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, {},
				el( 'h3', { className: 'components-base-control' }, __('Heading Settings') ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.main_heading_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { main_heading_use_theme_fonts: value } ); },
				} ),
				!attrs.main_heading_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.main_heading_font_family,
					onChange: ( value ) => { props.setAttributes( { main_heading_font_family: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.main_heading_font_size,
					onChange: ( value ) => { props.setAttributes( { main_heading_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.main_heading_font_weight,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { main_heading_font_weight: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.main_heading_color,
						onChange: function onChange(value) {
							return props.setAttributes({ main_heading_color: value });
						}
					}]
				}),
				el( TextControl, {
					label: __('Line Height'),
					value: attrs.main_heading_line_height,
					onChange: ( value ) => { props.setAttributes( { main_heading_line_height: value } ); },
				} ),
				el( RangeControl, {
					label: __('Heading Margin Bottom'),
					value: attrs.main_heading_margin_bottom,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { main_heading_margin_bottom: value } ); },
				} ),
				el( SelectControl, {
					label: __('Tag'),
					value: attrs.heading_tag,
					options: [{ label: __('H1'), value: 'h1' }, { label: __('H2'), value: 'h2' }, { label: __('H3'), value: 'h3' }, { label: __('H4'), value: 'h4' }, { label: __('H5'), value: 'h5' }, { label: __('H6'), value: 'h6' }],
					onChange: ( value ) => { props.setAttributes( { heading_tag: value } ); },
				} ),
				el( 'h3', null, __('Sub Heading Settings') ),
				el( TextareaControl, {
					label: __('Sub Heading (Optional)'),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.sub_heading_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { sub_heading_use_theme_fonts: value } ); },
				} ),
				!attrs.sub_heading_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.sub_heading_font_family,
					onChange: ( value ) => { props.setAttributes( { sub_heading_font_family: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.sub_heading_font_size,
					onChange: ( value ) => { props.setAttributes( { sub_heading_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.sub_heading_font_weight,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { sub_heading_font_weight: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.sub_heading_color,
						onChange: function onChange(value) {
							return props.setAttributes({ sub_heading_color: value });
						}
					}]
				}),
				el( TextControl, {
					label: __('Line Height'),
					value: attrs.sub_heading_line_height,
					onChange: ( value ) => { props.setAttributes( { sub_heading_line_height: value } ); },
				} ),
				el( RangeControl, {
					label: __('Sub Heading Margin Bottom'),
					value: attrs.sub_heading_margin_bottom,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { sub_heading_margin_bottom: value } ); },
				} ),

				el( SelectControl, {
					label: __('Separator'),
					value: attrs.spacer,
					options: [{ label: __('No Separator'), value: 'no_spacer' }, { label: __('Line'), value: 'line_only' }],
					onChange: ( value ) => { props.setAttributes( { spacer: value } ); },
				} ),
				el( 'p', { style: { fontStyle: 'italic' } }, __('Horizontal line, icon or image to divide sections') ),
				attrs.spacer == 'line_only' && el( SelectControl, {
					label: __('Separator Position'),
					value: attrs.spacer_position,
					options: [{ label: __('Top'), value: 'top' }, { label: __('Between Heading & Sub-Heading'), value: 'middle' }, { label: __('Bottom'), value: 'bottom'}],
					onChange: ( value ) => { props.setAttributes( { spacer_position: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( RangeControl, {
					label: __('Line Width (optional)'),
					value: attrs.line_width,
					min: 0,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { line_width: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( RangeControl, {
					label: __('Line Height'),
					value: attrs.line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { line_height: value } ); },
				} ),
				attrs.spacer == 'line_only' && el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Line Color'),
						value: attrs.line_color,
						onChange: function onChange(value) {
							return props.setAttributes({ line_color: value });
						}
					}]
				}),
				attrs.spacer == 'line_only' && el( RangeControl, {
					label: __('Separator Margin Bottom'),
					value: attrs.spacer_margin_bottom,
					min: 0,
					max: 50,
					onChange: ( value ) => { props.setAttributes( { spacer_margin_bottom: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: props.attributes.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
					value: props.attributes.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} ),

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
				if ( ! unit ) {
					attrs.main_heading_font_size += 'px';
				}
				main_heading_style_inline.fontSize = attrs.main_heading_font_size;
			}
			if ( attrs.main_heading_line_height ) {
				let unit = attrs.main_heading_line_height.replace( /[0-9.]/g, '' );
				if ( ! unit && attrs.main_heading_line_height > 3 ) {
					attrs.main_heading_line_height += 'px';
				}
				main_heading_style_inline.lineHeight = attrs.main_heading_line_height;
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
				if ( ! unit ) {
					attrs.sub_heading_font_size += 'px';
				}
				sub_heading_style_inline.fontSize = attrs.sub_heading_font_size;
			}
			if ( attrs.sub_heading_line_height ) {
				let unit = attrs.sub_heading_line_height.replace( /[0-9.]/g, '' );
				if ( ! unit && attrs.sub_heading_line_height > 3 ) {
					attrs.sub_heading_line_height += 'px';
				}
				sub_heading_style_inline.lineHeight = attrs.sub_heading_line_height;
			}

			if ( attrs.spacer && attrs.spacer_margin_bottom ) {
				wrapper_style = { marginBottom: attrs.spacer_margin_bottom + 'px' };
			}
			if ('line_only' == attrs.spacer) {
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
			if (attrs.spacer) {
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
						onChange: function( value ) {
							return props.setAttributes({ main_heading: value });
						},
						value: attrs.main_heading,
					}
				),
				'middle' == attrs.spacer_position && spacerRender,
				attrs.content && el(
					'div',
					{className: 'porto-u-sub-heading', style: sub_heading_style_inline},
					attrs.content
				),
				'bottom' == attrs.spacer_position && spacerRender,
			);

			return [
				el( BlockControls, null,
					el( BlockAlignmentToolbar, { value: attrs.alignment, onChange: function onChange(value) {
						return props.setAttributes({ alignment: value });
					} } )
				),
				inspectorControls,
				renderControls,
			];
		}
	}

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
			main_heading_line_height: {
				type: 'string',
			},
			main_heading_color: {
				type: 'string',
			},
			main_heading_margin_bottom: {
				type: 'string',
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
			sub_heading_color: {
				type: 'string',
			},
			sub_heading_margin_bottom: {
				type: 'string',
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
				type: 'string',
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
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 6. Porto Info Box
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
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

	class PortoInfoBox extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {
			
		}

		componentDidUpdate(prevProps, prevState) {
			
		}

		render() {
			var props = this.props,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, {},
				el( SelectControl, {
					label: __('Box Style'),
					value: attrs.pos,
					options: [{ label: __('Icon at Left with heading'), value: 'default' }, { label: __('Icon at Right with heading'), value: 'heading-right' }, { label: __('Icon at Left'), value: 'left' }, { label: __('Icon at Right'), value: 'right' }, { label: __('Icon at Top'), value: 'top' }],
					onChange: ( value ) => { props.setAttributes( { pos: value } ); },
				} ),
				el( SelectControl, {
					label: __('Icon to display'),
					value: attrs.icon_type,
					options: [{label: __('Icon Font'), value: ''}, {label: __('Custom Image Icon'), value: 'custom'}],
					onChange: ( value ) => { props.setAttributes( { icon_type: value } ); },
				} ),
				!attrs.icon_type && el( TextControl, {
					label: __('Icon Class'),
					value: attrs.icon,
					onChange: ( value ) => { props.setAttributes( { icon: value } ); },
				} ),
				'custom' == attrs.icon_type && el( MediaUpload, {
					allowedTypes: ['image'],
					value: attrs.icon_img,
					onSelect: function onSelect(image) {
						return props.setAttributes({ icon_img_url: image.url, icon_img: image.id });
					},
					render: function render(_ref) {
						var open = _ref.open;
						return el(IconButton, {
							className: 'components-toolbar__control',
							label: __('Change image'),
							icon: 'edit',
							onClick: open
						});
					}
				}),
				'custom' == attrs.icon_type && el(IconButton, {
					className: 'components-toolbar__control',
					label: __('Remove image'),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes({ icon_img_url: undefined, icon_img: undefined });
					}
				}),
				'custom' == attrs.icon_type && el( RangeControl, {
					label: __('Image Width'),
					value: attrs.img_width,
					min: 16,
					max: 512,
					onChange: ( value ) => { props.setAttributes( { img_width: value } ); },
				} ),
				'custom' != attrs.icon_type && el( RangeControl, {
					label: __('Icon Size'),
					value: attrs.icon_size,
					min: 12,
					max: 72,
					onChange: ( value ) => { props.setAttributes( { icon_size: value } ); },
				} ),
				'custom' != attrs.icon_type && el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.icon_color,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color: value });
						}
					}]
				}),
				el( SelectControl, {
					label: __('Icon Style'),
					value: attrs.icon_style,
					options: [{ label: __('Simple'), value: 'none' }, { label: __('Circle Background'), value: 'circle' }, { label: __('Circle Image'), value: 'circle_img' }, { label: __('Square Background'), value: 'square' }, { label: __('Design your own'), value: 'advanced' }],
					onChange: ( value ) => { props.setAttributes( { icon_style: value } ); },
				} ),
				'none' != attrs.icon_style && el( PanelColorSettings, {
					title: __('Background Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Background Color'),
						value: attrs.icon_color_bg,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color_bg: value });
						}
					}]
				}),
				('circle_img' == attrs.icon_style || 'advanced' == attrs.icon_style) && el( SelectControl, {
					label: __('Icon Border Style'),
					value: attrs.icon_border_style,
					options: [{ label: __('None'), value: '' }, { label: __('Solid'), value: 'solid' }, { label: __('Dashed'), value: 'dashed' }, { label: __('Dotted'), value: 'dotted' }, { label: __('Double'), value: 'double' }, { label: __('Inset'), value: 'inset' }, { label: __('Outset'), value: 'outset' }],
					onChange: ( value ) => { props.setAttributes( { icon_border_style: value } ); },
				} ),
				attrs.icon_border_style && el( PanelColorSettings, {
					title: __('Border Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Border Color'),
						value: attrs.icon_color_border,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color_border: value });
						}
					}]
				}),
				attrs.icon_border_style && el( RangeControl, {
					label: __('Border Width'),
					value: attrs.icon_border_size,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { icon_border_size: value } ); },
				} ),
				attrs.icon_border_style && el( RangeControl, {
					label: __('Border Radius'),
					value: attrs.icon_border_radius,
					min: 1,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_radius: value } ); },
				} ),
				('circle_img' == attrs.icon_style || 'advanced' == attrs.icon_style) && el( RangeControl, {
					label: __('Background Size'),
					value: attrs.icon_border_spacing,
					min: 0,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_spacing: value } ); },
				} ),
				('circle_img' == attrs.icon_style || 'advanced' == attrs.icon_style) && el( 'p', {style: {fontStyle: 'italic'}}, __('Spacing from center of the icon till the boundary of border / background') ),
				el( TextControl, {
					label: __('Icon Animation Type'),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Title'),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( TextControl, {
					label: __('Sub title'),
					value: attrs.subtitle,
					onChange: ( value ) => { props.setAttributes( { subtitle: value } ); },
				} ),
				el( TextareaControl, {
					label: __('Description'),
					value: attrs.content,
					onChange: ( value ) => { props.setAttributes( { content: value } ); },
				} ),
				el( SelectControl, {
					label: __('Apply link to:'),
					value: attrs.read_more,
					options: [{ label: __('No Link'), value: 'none' }, { label: __('Complete Box'), value: 'box' }, { label: __('Box Title'), value: 'title' }, { label: __('Display Read More'), value: 'more' }],
					onChange: ( value ) => { props.setAttributes( { read_more: value } ); },
				} ),
				'none' != attrs.read_more && el( TextControl, {
					label: __('Add Link'),
					value: attrs.link,
					onChange: ( value ) => { props.setAttributes( { link: value } ); },
				} ),
				'more' == attrs.read_more && el( TextControl, {
					label: __('Read More Text'),
					value: attrs.read_text,
					onChange: ( value ) => { props.setAttributes( { read_text: value } ); },
				} ),
				el( SelectControl, {
					label: __('Select Hover Effect type'),
					value: attrs.hover_effect,
					options: [{ label: __('No Effect'), value: 'style_1' }, { label: __('Icon Zoom'), value: 'style_2' }, { label: __('Icon Bounce Up'), value: 'style_3' }],
					onChange: ( value ) => { props.setAttributes( { hover_effect: value } ); },
				} ),
				el( 'h3', null, __('Title settings') ),
				el( SelectControl, {
					label: __('Tag'),
					value: attrs.heading_tag,
					options: [{ label: __('H1'), value: 'h1' }, { label: __('H2'), value: 'h2' }, { label: __('H3'), value: 'h3' }, { label: __('H4'), value: 'h4' }, { label: __('H5'), value: 'h5' }, { label: __('H6'), value: 'h6' }, { label: __('div'), value: 'div' }, { label: __('p'), value: 'p' }],
					onChange: ( value ) => { props.setAttributes( { heading_tag: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.title_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { title_use_theme_fonts: value } ); },
				} ),
				!attrs.title_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.title_font,
					onChange: ( value ) => { props.setAttributes( { title_font: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.title_font_size,
					onChange: ( value ) => { props.setAttributes( { title_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.title_font_style,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { title_font_style: value } ); },
				} ),
				el( RangeControl, {
					label: __('Line Height (px)'),
					value: attrs.title_font_line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { title_font_line_height: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.title_font_color,
						onChange: function onChange(value) {
							return props.setAttributes({ title_font_color: value });
						}
					}]
				}),
				el( 'h3', null, __('Sub Title settings') ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.subtitle_font_size,
					onChange: ( value ) => { props.setAttributes( { subtitle_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.subtitle_font_style,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { subtitle_font_style: value } ); },
				} ),
				el( RangeControl, {
					label: __('Line Height (px)'),
					value: attrs.subtitle_font_line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { subtitle_font_line_height: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.subtitle_font_color,
						onChange: function onChange(value) {
							return props.setAttributes({ subtitle_font_color: value });
						}
					}]
				}),
				el( 'h3', null, __('Description settings') ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.desc_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { desc_use_theme_fonts: value } ); },
				} ),
				!attrs.desc_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.desc_font,
					onChange: ( value ) => { props.setAttributes( { desc_font: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.desc_font_size,
					onChange: ( value ) => { props.setAttributes( { desc_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.desc_font_style,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { desc_font_style: value } ); },
				} ),
				el( RangeControl, {
					label: __('Line Height (px)'),
					value: attrs.desc_font_line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { desc_font_line_height: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.desc_font_color,
						onChange: function onChange(value) {
							return props.setAttributes({ desc_font_color: value });
						}
					}]
				}),

			);


			var ex_class = '',
				ic_class = '';
			var title_style = {}, subtitle_style = {}, desc_style = {};
			if ( attrs.pos ) {
				ex_class = attrs.pos + '-icon';
				ic_class = 'porto-sicon-' + attrs.pos;
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
				let unit = attrs.title_font_size.replace( /[0-9.]/g, '' );
				if ( ! unit ) {
					attrs.title_font_size += 'px';
				}
				title_style.fontSize = attrs.title_font_size;
			}
			if ( attrs.title_font_line_height ) {
				title_style.lineHeight = attrs.title_font_line_height + 'px';
			}
			if ( attrs.title_font_color ) {
				title_style.color = attrs.title_font_color;
			}

			/* sub title */
			if ( attrs.subtitle_font_style ) {
				subtitle_style.fontWeight = Number( attrs.subtitle_font_style );
			}
			if ( attrs.subtitle_font_size ) {
				let unit = attrs.subtitle_font_size.replace( /[0-9.]/g, '' );
				if ( ! unit ) {
					attrs.subtitle_font_size += 'px';
				}
				subtitle_style.fontSize = attrs.subtitle_font_size;
			}
			if ( attrs.subtitle_font_line_height ) {
				subtitle_style.lineHeight = attrs.subtitle_font_line_height + 'px';
			}
			if ( attrs.subtitle_font_color ) {
				subtitle_style.color = attrs.subtitle_font_color;
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
				if ( ! unit ) {
					attrs.desc_font_size += 'px';
				}
				desc_style.fontSize = attrs.desc_font_size;
			}
			if ( attrs.desc_font_line_height ) {
				desc_style.lineHeight = attrs.desc_font_line_height + 'px';
			}
			if ( attrs.desc_font_color ) {
				desc_style.color = attrs.desc_font_color;
			}

			var bodyRender = null,
				renderControls,
				boxIcon,
				icon_align_style = {textAlign: 'center'},
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
						{className: 'porto-sicon-img' + elx_class, style: boxIconStyle},
						el( 'img', {src: attrs.icon_img_url, alt: ''} )
					);
				}
			} else {
				if ( attrs.icon_color )
					boxIconStyle.color = attrs.icon_color;
				if ( attrs.icon_style !== 'none' ){
					if (attrs.icon_color_bg !== '') {
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
						{className: 'porto-icon ' + attrs.icon_style + ' ' + elx_class, style: boxIconStyle},
						el( 'i', {className: attrs.icon} )
					);
				}
			}
			boxIcon = el(
				'div',
				{className: 'align-icon', style: icon_align_style},
				boxIcon
			);
			var internal_style = '';
			if ( attrs.icon_style == 'circle_img' && attrs.icon_type == 'custom' && attrs.icon_border_spacing ) {
				internal_style += '#porto-icon-' + clientId + ' .porto-sicon-img.porto-u-circle-img:before {';
					internal_style += 'border-width: ' + (attrs.icon_border_spacing + 1) + 'px';
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
				{id: 'porto-icon-' + clientId, className: 'porto-just-icon-wrapper'},
				internal_style,
				boxIcon
			);


			if ( attrs.pos == 'heading-right' || attrs.pos == 'right' ) {
				if ( attrs.title ) {
					var titleRender = el(
						attrs.heading_tag,
						{className: 'porto-sicon-title', style: title_style, dangerouslySetInnerHTML: { __html: attrs.title.replace(/<script.*?\/script>/g, '') } }
					);
					bodyRender = el(
						'div',
						{ className: 'porto-sicon-header' },
						attrs.link && attrs.read_more == 'title' && el(
							'a',
							{className: 'porto-sicon-box-link', href: attrs.link},
							titleRender,
						),
						(!attrs.link || attrs.read_more != 'title') && titleRender,
						attrs.subtitle && el(
							'p',
							{style: subtitle_style},
							attrs.subtitle,
						)
					);
				}
				bodyRender = el(
					'div',
					{className: (attrs.pos == 'right' ? 'porto-sicon-body' : 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ))},
					bodyRender,
					attrs.pos != 'right' && (attrs.icon || attrs.icon_img_url) && el(
						'div',
						{className: ic_class},
						boxIcon
					),
					attrs.content && el(
						'div',
						{className: 'porto-sicon-description', style: desc_style, dangerouslySetInnerHTML: { __html: attrs.content.replace(/<script.*?\/script>/g, '') }},
						attrs.link && attrs.read_more == 'more' && el(
							'a',
							{className: 'porto-sicon-read', href: attrs.link},
							attrs.read_text
						)
					)
				);

				if (attrs.pos == 'right') {
					bodyRender = el(
						'div',
						{ className: 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) },
						bodyRender,
						el(
							'div',
							{className: ic_class},
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
						{className: 'porto-sicon-title', style: title_style, dangerouslySetInnerHTML: { __html: attrs.title.replace(/<script.*?\/script>/g, '') } }
					);
					titleRender = el(
						'div',
						{className: 'porto-sicon-header'},
						attrs.link && attrs.read_more == 'title' && el(
							'a',
							{className: 'porto-sicon-box-link', href: attrs.link},
							titleRender,
						),
						(!attrs.link || attrs.read_more != 'title') && titleRender,
						attrs.subtitle && el(
							'p',
							{style: subtitle_style},
							attrs.subtitle,
						)
					);
				}
				if ( attrs.content ) {
					contentRender = el(
						'div',
						{className: 'porto-sicon-description', style: desc_style, dangerouslySetInnerHTML: { __html: attrs.content.replace(/<script.*?\/script>/g, '') }},
						attrs.link && attrs.read_more == 'more' && el(
							'a',
							{className: 'porto-sicon-read xx', href: attrs.link},
							attrs.read_text
						)
					)
				}
				if (attrs.pos == 'left') {
					bodyRender = el(
						'div',
						{ className: 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) },
						(attrs.icon || attrs.icon_img_url) && el(
							'div',
							{className: ic_class},
							boxIcon
						),
						el(
							'div',
							{className: 'porto-sicon-body'},
							titleRender,
							contentRender
						)
					);
				} else {
					bodyRender = el(
						'div',
						{ className: 'porto-sicon-box' + ( ex_class ? ' ' + ex_class : '' ) },
						(attrs.icon || attrs.icon_img_url) && el(
							'div',
							{className: ic_class},
							boxIcon
						),
						titleRender,
						contentRender
					);
				}
			}


			if (attrs.link && attrs.read_more == 'box') {
				renderControls = el(
					'a',
					{className: 'porto-sicon-box-link', href: attrs.link},
					bodyRender,
				);
			} else {
				renderControls = bodyRender;
			}

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

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
			icon_color: { type: 'string', default: '#333' },
			icon_style: { type: 'string', default: 'none' },
			icon_color_bg: { type: 'string' },
			icon_color_border: { type: 'string', default: '#333' },
			icon_border_style: { type: 'string' },
			icon_border_size: { type: 'int', default: 1 },
			icon_border_radius: { type: 'int', default: 500 },
			icon_border_spacing: { type: 'int', default: 50 },
			icon_animation: { type: 'string' },
			title: { type: 'string' },
			subtitle: { type: 'string' },
			content: { type: 'string' },
			link: { type: 'string' },
			hover_effect: { type: 'string', default: 'style_1' },
			pos: { type: 'string', default: 'default' },
			read_more: { type: 'string', default: 'none' },
			read_text: { type: 'string', default: 'Read More' },
			heading_tag: { type: 'string', default: 'h3' },
			title_use_theme_fonts: { type: 'boolean', default: true },
			title_font: { type: 'string' },
			title_font_style: { type: 'int' },
			title_font_size: { type: 'string' },
			title_font_line_height: { type: 'int' },
			title_font_color: { type: 'string' },
			subtitle_font_style: { type: 'int' },
			subtitle_font_size: { type: 'string' },
			subtitle_font_line_height: { type: 'int' },
			subtitle_font_color: { type: 'string' },
			desc_use_theme_fonts: { type: 'boolean', default: true },
			desc_font: { type: 'string' },
			desc_font_style: { type: 'int' },
			desc_font_size: { type: 'string' },
			desc_font_color: { type: 'string' },
			desc_font_line_height: { type: 'int' },
			animation_type: { type: 'string' },
		},
		edit: PortoInfoBox,
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 7. Porto Stat Counter
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		PanelColorSettings = wpBlockEditor.PanelColorSettings,
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

	let counterWillUpdate = null;

	class PortoStatCounter extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {
			if (typeof countUp == "undefined") {
				var c = document.createElement("script");
				c.src = ajaxurl.replace('/wp-admin/admin-ajax.php', '/wp-content/plugins/porto-functionality/shortcodes/assets/js/countup.min.js');
				if (!jQuery('script[src="' + c.src + '"]').length) {
					document.getElementsByTagName("body")[0].appendChild(c);
				}
				jQuery(c).on('load', function() {
					c = document.createElement("script");
					c.src = ajaxurl.replace('/wp-admin/admin-ajax.php', '/wp-content/plugins/porto-functionality/shortcodes/assets/js/countup-loader.min.js');
					if (!jQuery('script[src="' + c.src + '"]').length) {
						document.getElementsByTagName("body")[0].appendChild(c);
					}
				});
			}
		}

		componentDidUpdate(prevProps, prevState) {
			var prevAttrs = prevProps.attributes, attrs = this.props.attributes;
			if (prevAttrs.counter_title != attrs.counter_title || prevAttrs.counter_value != attrs.counter_value || prevAttrs.counter_sep != attrs.counter_sep || prevAttrs.counter_suffix != attrs.counter_suffix || prevAttrs.counter_prefix != attrs.counter_prefix || prevAttrs.counter_decimal != attrs.counter_decimal || prevAttrs.speed != attrs.speed) {
				var clientId = this.props.clientId;
				clearTimeout(counterWillUpdate);
				counterWillUpdate = setTimeout(function() {
					jQuery(document.body).trigger('porto_refresh_vc_content', [jQuery('[data-block="' + clientId + '"]')]);
				}, 1000);
			}
		}

		render() {
			var props = this.props,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, {},
				el( SelectControl, {
					label: __('Icon to display'),
					value: attrs.icon_type,
					options: [{label: __('Icon Font'), value: ''}, {label: __('Custom Image Icon'), value: 'custom'}],
					onChange: ( value ) => { props.setAttributes( { icon_type: value } ); },
				} ),
				!attrs.icon_type && el( TextControl, {
					label: __('Icon Class'),
					value: attrs.icon,
					onChange: ( value ) => { props.setAttributes( { icon: value } ); },
				} ),
				'custom' == attrs.icon_type && el( MediaUpload, {
					allowedTypes: ['image'],
					value: attrs.icon_img,
					onSelect: function onSelect(image) {
						return props.setAttributes({ icon_img_url: image.url, icon_img: image.id });
					},
					render: function render(_ref) {
						var open = _ref.open;
						return el(IconButton, {
							className: 'components-toolbar__control',
							label: __('Change image'),
							icon: 'edit',
							onClick: open
						});
					}
				}),
				'custom' == attrs.icon_type && el(IconButton, {
					className: 'components-toolbar__control',
					label: __('Remove image'),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes({ icon_img_url: undefined, icon_img: undefined });
					}
				}),
				'custom' == attrs.icon_type && el( RangeControl, {
					label: __('Image Width'),
					value: attrs.img_width,
					min: 16,
					max: 512,
					onChange: ( value ) => { props.setAttributes( { img_width: value } ); },
				} ),
				'custom' != attrs.icon_type && el( RangeControl, {
					label: __('Icon Size'),
					value: attrs.icon_size,
					min: 12,
					max: 72,
					onChange: ( value ) => { props.setAttributes( { icon_size: value } ); },
				} ),
				'custom' != attrs.icon_type && el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.icon_color,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color: value });
						}
					}]
				}),
				el( SelectControl, {
					label: __('Icon Style'),
					value: attrs.icon_style,
					options: [{ label: __('Simple'), value: 'none' }, { label: __('Circle Background'), value: 'circle' }, { label: __('Square Background'), value: 'square' }, { label: __('Design your own'), value: 'advanced' }],
					onChange: ( value ) => { props.setAttributes( { icon_style: value } ); },
				} ),
				'none' != attrs.icon_style && el( PanelColorSettings, {
					title: __('Background Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Background Color'),
						value: attrs.icon_color_bg,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color_bg: value });
						}
					}]
				}),
				('advanced' == attrs.icon_style) && el( SelectControl, {
					label: __('Icon Border Style'),
					value: attrs.icon_border_style,
					options: [{ label: __('None'), value: '' }, { label: __('Solid'), value: 'solid' }, { label: __('Dashed'), value: 'dashed' }, { label: __('Dotted'), value: 'dotted' }, { label: __('Double'), value: 'double' }, { label: __('Inset'), value: 'inset' }, { label: __('Outset'), value: 'outset' }],
					onChange: ( value ) => { props.setAttributes( { icon_border_style: value } ); },
				} ),
				attrs.icon_border_style && el( PanelColorSettings, {
					title: __('Border Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Border Color'),
						value: attrs.icon_color_border,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color_border: value });
						}
					}]
				}),
				attrs.icon_border_style && el( RangeControl, {
					label: __('Border Width'),
					value: attrs.icon_border_size,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { icon_border_size: value } ); },
				} ),
				attrs.icon_border_style && el( RangeControl, {
					label: __('Border Radius'),
					value: attrs.icon_border_radius,
					min: 1,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_radius: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( RangeControl, {
					label: __('Background Size'),
					value: attrs.icon_border_spacing,
					min: 0,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_spacing: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( 'p', {style: {fontStyle: 'italic'}}, __('Spacing from center of the icon till the boundary of border / background') ),
				el( TextControl, {
					label: __('Icon Animation Type'),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( SelectControl, {
					label: __('Icon Position'),
					value: attrs.icon_position,
					options: [{ label: __('Top'), value: 'top' }, { label: __('Right'), value: 'right' }, { label: __('Left'), value: 'left' }],
					onChange: ( value ) => { props.setAttributes( { icon_position: value } ); },
				} ),
				el( TextControl, {
					label: __('Counter Title'),
					value: attrs.counter_title,
					onChange: ( value ) => { props.setAttributes( { counter_title: value } ); },
				} ),
				el( TextControl, {
					label: __('Counter Value'),
					value: attrs.counter_value,
					onChange: ( value ) => { props.setAttributes( { counter_value: value } ); },
				} ),
				el( TextControl, {
					label: __('Thousands Separator'),
					value: attrs.counter_sep,
					onChange: ( value ) => { props.setAttributes( { counter_sep: value } ); },
				} ),
				el( TextControl, {
					label: __('Replace Decimal Point With'),
					value: attrs.counter_decimal,
					onChange: ( value ) => { props.setAttributes( { counter_decimal: value } ); },
				} ),
				el( TextControl, {
					label: __('Counter Value Prefix'),
					value: attrs.counter_prefix,
					onChange: ( value ) => { props.setAttributes( { counter_prefix: value } ); },
				} ),
				el( TextControl, {
					label: __('Counter Value Suffix'),
					value: attrs.counter_suffix,
					onChange: ( value ) => { props.setAttributes( { counter_suffix: value } ); },
				} ),
				el( RangeControl, {
					label: __('Counter rolling time'),
					value: attrs.speed,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { speed: value } ); },
				} ),
				el( 'h3', null, __('Counter Title settings') ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.title_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { title_use_theme_fonts: value } ); },
				} ),
				!attrs.title_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.title_font,
					onChange: ( value ) => { props.setAttributes( { title_font: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.title_font_size,
					onChange: ( value ) => { props.setAttributes( { title_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.title_font_style,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { title_font_style: value } ); },
				} ),
				el( RangeControl, {
					label: __('Line Height'),
					value: attrs.title_font_line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { title_font_line_height: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Counter Title Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.counter_color_txt,
						onChange: function onChange(value) {
							return props.setAttributes({ counter_color_txt: value });
						}
					}]
				}),
				el( 'h3', null, __('Counter Value settings') ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.desc_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { desc_use_theme_fonts: value } ); },
				} ),
				!attrs.desc_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.desc_font,
					onChange: ( value ) => { props.setAttributes( { desc_font: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.desc_font_size,
					onChange: ( value ) => { props.setAttributes( { desc_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.desc_font_style,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { desc_font_style: value } ); },
				} ),
				el( RangeControl, {
					label: __('Line Height'),
					value: attrs.desc_font_line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { desc_font_line_height: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Counter Value Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.desc_font_color,
						onChange: function onChange(value) {
							return props.setAttributes({ desc_font_color: value });
						}
					}]
				}),
				el( 'h3', null, __('Counter suffix-prefix Value settings') ),
				el( ToggleControl, {
					label: __('Use theme default font family?'),
					checked: attrs.suf_pref_use_theme_fonts,
					onChange: ( value ) => { props.setAttributes( { suf_pref_use_theme_fonts: value } ); },
				} ),
				!attrs.suf_pref_use_theme_fonts && el( TextControl, {
					label: __('Font Family'),
					value: attrs.suf_pref_font,
					onChange: ( value ) => { props.setAttributes( { suf_pref_font: value } ); },
				} ),
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.suf_pref_font_size,
					onChange: ( value ) => { props.setAttributes( { suf_pref_font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.suf_pref_font_style,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { suf_pref_font_style: value } ); },
				} ),
				el( RangeControl, {
					label: __('Line Height'),
					value: attrs.suf_pref_line_height,
					min: 1,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { suf_pref_line_height: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Counter suffix-prefix Value Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Select text color for counter prefix and suffix.'),
						value: attrs.suf_pref_font_color,
						onChange: function onChange(value) {
							return props.setAttributes({ suf_pref_font_color: value });
						}
					}]
				}),

			);


			var boxIcon,
				icon_align_style = {textAlign: 'center'},
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
						{className: 'porto-sicon-img' + elx_class, style: boxIconStyle},
						el( 'img', {src: attrs.icon_img_url, alt: ''} )
					);
				}
			} else {
				if ( attrs.icon_color )
					boxIconStyle.color = attrs.icon_color;
				if ( attrs.icon_style !== 'none' ){
					if (attrs.icon_color_bg !== '') {
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
						{className: 'porto-icon ' + attrs.icon_style + ' ' + elx_class, style: boxIconStyle},
						el( 'i', {className: attrs.icon} )
					);
				}
			}
			boxIcon = el(
				'div',
				{className: 'align-icon', style: icon_align_style},
				boxIcon
			);
			var internal_style = '';
			if ( attrs.icon_style == 'circle_img' && attrs.icon_type == 'custom' && attrs.icon_border_spacing ) {
				internal_style += '#porto-icon-' + clientId + ' .porto-sicon-img.porto-u-circle-img:before {';
					internal_style += 'border-width: ' + (attrs.icon_border_spacing + 1) + 'px';
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
				{id: 'porto-icon-' + clientId, className: 'porto-just-icon-wrapper'},
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
				title_style.fontSize = attrs.title_font_size + 'px';
			}
			if ( attrs.title_font_line_height ) {
				title_style.lineHeight = attrs.title_font_line_height + 'px';
			}

			/* description */
			if ( attrs.desc_font ) {
				desc_style.fontFamily = attrs.desc_font;
			}
			if ( attrs.desc_font_style ) {
				desc_style.fontWeight = attrs.desc_font_style;
			}
			if ( attrs.desc_font_size ) {
				desc_style.fontSize = attrs.desc_font_size + 'px';
			}
			if ( attrs.desc_font_line_height ) {
				desc_style.lineHeight = attrs.desc_font_line_height + 'px';
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
				suf_pref_style.fontSize = attrs.suf_pref_font_size + 'px';
			}
			if ( attrs.suf_pref_line_height ) {
				suf_pref_style.lineHeight = attrs.suf_pref_line_height + 'px';
			}
			if ( attrs.suf_pref_font_color ) {
				suf_pref_style.color = attrs.suf_pref_font_color;
			}

			if (attrs.counter_sep == '') {
				attrs.counter_sep = 'none';
			}
			if (attrs.counter_decimal == '') {
				attrs.counter_decimal = 'none';
			}
			var renderControls = el(
				'div',
				{className: 'stats-block stats-' + attrs.icon_position + (attrs.className ? ' ' + attrs.className : '')},
				attrs.icon_position != 'right' && el( 'div', {className: 'porto-sicon-' + attrs.icon_position}, boxIcon ),
				el(
					'div',
					{className: 'stats-desc'},
					attrs.counter_prefix && el(
						'div',
						{className: 'counter_prefix mycust', style: suf_pref_style},
						attrs.counter_prefix
					),
					el(
						'div',
						{id: 'counter_' + clientId, 'data-id': 'counter_' + clientId, className: 'stats-number', style: desc_style, 'data-speed': attrs.speed, 'data-counter-value': attrs.counter_value, 'data-separator': attrs.counter_sep, 'data-decimal': attrs.counter_decimal},
						'0'
					),
					attrs.counter_suffix && el(
						'div',
						{className: 'counter_suffix mycust', style: suf_pref_style},
						attrs.counter_suffix
					),
					el(
						'div',
						{className: 'stats-text', style: title_style},
						attrs.counter_title
					)
				),
				attrs.icon_position == 'right' && el( 'div', {className: 'porto-sicon-' + attrs.icon_position}, boxIcon ),
			);

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

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
			title_use_theme_fonts: { type: 'boolean' },
			title_font: { type: 'string' },
			title_font_style: { type: 'int' },
			title_font_size: { type: 'string' },
			title_font_line_height: { type: 'int' },
			desc_use_theme_fonts: { type: 'boolean', default: true },
			desc_font: { type: 'string' },
			desc_font_style: { type: 'int' },
			desc_font_size: { type: 'string' },
			desc_font_color: { type: 'string' },
			desc_font_line_height: { type: 'int' },
			suf_pref_font: { type: 'string' },
			suf_pref_use_theme_fonts: { type: 'boolean', default: true },
			suf_pref_font_style: { type: 'int' },
			suf_pref_font_size: { type: 'string' },
			suf_pref_line_height: { type: 'int' },
			suf_pref_font_color: { type: 'string' },
		},
		edit: PortoStatCounter,
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 8. Porto Icons
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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

	class PortoIcons extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {}

		componentDidUpdate(prevProps, prevState) {}

		render() {
			var props = this.props,
				attrs = props.attributes,
				clientId = props.clientId;

			var renderControls = el(
				'div',
				{className: 'porto-u-icons' + (attrs.align ? ' ' + attrs.align : '') + (attrs.className ? ' ' + attrs.className : '') },
				el( InnerBlocks, { allowedBlocks: [ 'porto/porto-single-icon' ] } ),
			);

			return [
				el( BlockControls, null,
					el( BlockAlignmentToolbar, { value: attrs.align ? attrs.align.replace('porto-icons-', '') : attrs.align, onChange: function onChange(value) {
						return props.setAttributes({ align: 'porto-icons-' + value });
					} } )
				),
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-icons', {
		title: 'Porto Icons',
		icon: 'porto',
		category: 'porto',
		attributes: {
			align: { type: 'string' },
		},
		edit: PortoIcons,
		save: function( props ) {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 9. Porto Single Icon
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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

	class PortoSingleIcon extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {}

		componentDidUpdate(prevProps, prevState) {}

		render() {
			var props = this.props,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, null,
				el( TextControl, {
					label: __('Icon Class'),
					value: attrs.icon,
					onChange: ( value ) => { props.setAttributes( { icon: value } ); },
				} ),
				el( RangeControl, {
					label: __('Icon Size'),
					value: attrs.icon_size,
					min: 12,
					max: 72,
					onChange: ( value ) => { props.setAttributes( { icon_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Space after Icon'),
					value: attrs.icon_margin,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { icon_margin: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Icon Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.icon_color,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color: value });
						}
					}]
				}),
				el( SelectControl, {
					label: __('Icon Style'),
					value: attrs.icon_style,
					options: [{ label: __('Simple'), value: 'none' }, { label: __('Circle Background'), value: 'circle' }, { label: __('Square Background'), value: 'square' }, { label: __('Design your own'), value: 'advanced' }],
					onChange: ( value ) => { props.setAttributes( { icon_style: value } ); },
				} ),
				'none' != attrs.icon_style && el( PanelColorSettings, {
					title: __('Background Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Background Color'),
						value: attrs.icon_color_bg,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color_bg: value });
						}
					}]
				}),
				'advanced' == attrs.icon_style && el( SelectControl, {
					label: __('Icon Border Style'),
					value: attrs.icon_border_style,
					options: [{ label: __('None'), value: '' }, { label: __('Solid'), value: 'solid' }, { label: __('Dashed'), value: 'dashed' }, { label: __('Dotted'), value: 'dotted' }, { label: __('Double'), value: 'double' }, { label: __('Inset'), value: 'inset' }, { label: __('Outset'), value: 'outset' }],
					onChange: ( value ) => { props.setAttributes( { icon_border_style: value } ); },
				} ),
				'advanced' == attrs.icon_style && attrs.icon_border_style && el( PanelColorSettings, {
					title: __('Border Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Border Color'),
						value: attrs.icon_color_border,
						onChange: function onChange(value) {
							return props.setAttributes({ icon_color_border: value });
						}
					}]
				}),
				'advanced' == attrs.icon_style && attrs.icon_border_style && el( RangeControl, {
					label: __('Border Width'),
					value: attrs.icon_border_size,
					min: 1,
					max: 10,
					onChange: ( value ) => { props.setAttributes( { icon_border_size: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( RangeControl, {
					label: __('Border Radius'),
					value: attrs.icon_border_radius,
					min: 1,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_radius: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( RangeControl, {
					label: __('Background Size'),
					value: attrs.icon_border_spacing,
					min: 10,
					max: 500,
					onChange: ( value ) => { props.setAttributes( { icon_border_spacing: value } ); },
				} ),
				'advanced' == attrs.icon_style && el( 'p', {style: {fontStyle: 'italic'}}, __('Spacing from center of the icon till the boundary of border / background') ),
				el( TextControl, {
					label: __('Link'),
					value: props.attributes.icon_link,
					onChange: ( value ) => { props.setAttributes( { icon_link: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: props.attributes.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
			);
			
			var boxIconStyle = {};
			if (attrs.icon_color) {
				boxIconStyle.color = attrs.icon_color;
			}
			if (attrs.icon_style && attrs.icon_style != 'none' && attrs.icon_color_bg) {
				boxIconStyle.backgroundColor = attrs.icon_color_bg;
			}
			if (attrs.icon_style == 'advanced') {
				boxIconStyle.borderStyle = attrs.icon_border_style;
				boxIconStyle.borderColor = attrs.icon_color_border;
				boxIconStyle.borderWidth = attrs.icon_border_size + 'px';
				boxIconStyle.width = attrs.icon_border_spacing + 'px';
				boxIconStyle.height = attrs.icon_border_spacing + 'px';
				boxIconStyle.lineHeight = attrs.icon_border_spacing + 'px'
				boxIconStyle.borderRadius = attrs.icon_border_radius + 'px';
			}
			if (attrs.icon_size) {
				boxIconStyle.fontSize = attrs.icon_size + 'px';
			}
			if (attrs.icon_margin) {
				boxIconStyle.marginRight = attrs.icon_margin + 'px';
			}
			var renderControls = el(
				'div',
				{className: 'porto-icon' + (attrs.icon_style ? ' ' + attrs.icon_style : '') + (attrs.className ? ' ' + attrs.className : ''), style: boxIconStyle },
				el( 'i', {className: attrs.icon} ),
			);
			if (attrs.icon_link) {
				renderControls = el(
					'a',
					{href: attrs.icon_link},
					renderControls
				);
			}

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

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
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 10. Porto Interactive Banner
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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

	class PortoInteractiveBanner extends Component {
		constructor() {
			super( ...arguments );
		}

		componentDidMount() {}

		componentDidUpdate(prevProps, prevState) {}

		render() {
			var props = this.props,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, null,
				el( PanelBody, {
						title: __('Deprecated'),
						initialOpen: false,
					},
					el( TextControl, {
						label: __('Title'),
						value: attrs.banner_title,
						onChange: ( value ) => { props.setAttributes( { banner_title: value } ); },
					} ),
					el( TextareaControl, {
						label: __('Description'),
						value: attrs.content,
						onChange: ( value ) => { props.setAttributes( { content: value } ); },
					} )
				),
				el( MediaUpload, {
					allowedTypes: ['image'],
					value: attrs.banner_image,
					onSelect: function onSelect(image) {
						return props.setAttributes({ banner_image_url: image.url, banner_image: image.id });
					},
					render: function render(_ref) {
						var open = _ref.open;
						return el(IconButton, {
							className: 'components-toolbar__control',
							label: __('Banner Image'),
							icon: 'edit',
							onClick: open
						});
					}
				}),
				el(IconButton, {
					className: 'components-toolbar__control',
					label: __('Remove image'),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes({ banner_image_url: undefined, banner_image: undefined });
					}
				}),
				attrs.banner_image && el( ToggleControl, {
					label: __('Lazy Load Image'),
					checked: props.attributes.lazyload,
					onChange: ( value ) => { props.setAttributes( { lazyload: value } ); },
				} ),
				attrs.banner_image && el(
					'p',
					{style: {fontStyle: 'italic'}},
					__('If you have this element in Porto Carousel, please check "Lazy Load" option in Porto Carousel element.')
				),
				el( ToggleControl, {
					label: __('Add Container'),
					checked: props.attributes.add_container,
					onChange: ( value ) => { props.setAttributes( { add_container: value } ); },
				} ),
				el( TextControl, {
					label: __('Min Height'),
					value: props.attributes.min_height,
					onChange: ( value ) => { props.setAttributes( { min_height: value } ); },
				} ),
				el( TextControl, {
					label: __('Link'),
					value: attrs.banner_link,
					onChange: ( value ) => { props.setAttributes( { banner_link: value } ); },
				} ),
				attrs.banner_title && el( RangeControl, {
					label: __('Title Font Size'),
					value: attrs.banner_title_font_size,
					min: 12,
					max: 80,
					onChange: ( value ) => { props.setAttributes( { banner_title_font_size: value } ); },
				} ),
				el( SelectControl, {
					label: __('Styles'),
					value: attrs.banner_style,
					options: [{ label: __('Default'), value: '' }, { label: __('Style 1'), value: 'style1' }, { label: __('Style 2'), value: 'style2' }],
					onChange: ( value ) => { props.setAttributes( { banner_style: value } ); },
				} ),
				attrs.banner_style == 'style2' && el( PanelColorSettings, {
					title: __('Title Background Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.banner_title_bg,
						onChange: function onChange(value) {
							return props.setAttributes({ banner_title_bg: value });
						}
					}]
				}),
				attrs.banner_title && el( PanelColorSettings, {
					title: __('Title Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.banner_color_title,
						onChange: function onChange(value) {
							return props.setAttributes({ banner_color_title: value });
						}
					}]
				}),
				attrs.banner_desc && el( PanelColorSettings, {
					title: __('Description Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.banner_color_desc,
						onChange: function onChange(value) {
							return props.setAttributes({ banner_color_desc: value });
						}
					}]
				}),
				el( PanelColorSettings, {
					title: __('Background Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Color'),
						value: attrs.banner_color_bg,
						onChange: function onChange(value) {
							return props.setAttributes({ banner_color_bg: value });
						}
					}]
				}),
				el( RangeControl, {
					label: __('Image Opacity'),
					value: attrs.image_opacity,
					min: 0.0,
					max: 1.0,
					step: 0.1,
					onChange: ( value ) => { props.setAttributes( { image_opacity: value } ); },
				} ),
				el( RangeControl, {
					label: __('Image Opacity on Hover'),
					value: attrs.image_opacity_on_hover,
					min: 0.0,
					max: 1.0,
					step: 0.1,
					onChange: ( value ) => { props.setAttributes( { image_opacity_on_hover: value } ); },
				} ),
			);
			
			var title_bg = {}, banner_style_inline = {}, img_style = {}, banner_title_style_inline = {}, banner_desc_style_inline = {};

			if ( attrs.banner_title_bg && attrs.banner_style == 'style2' ){
				title_bg.backgroundColor = attrs.banner_title_bg;
			}

			if ( attrs.banner_title_font_size ) {
				banner_title_style_inline.fontSize = attrs.banner_title_font_size;
			}
			if ( attrs.banner_color_bg ) {
				banner_style_inline.backgroundColor = attrs.banner_color_bg;
			}
			if ( attrs.min_height ) {
				let unit = attrs.min_height.replace( /[0-9.]/g, '' );
				if ( ! unit ) {
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

			var wrapperAttrs = {
				className: 'porto-ibanner' + (attrs.banner_style ? ' porto-ibanner-effect-' + attrs.banner_style : '') + (attrs.className ? ' ' + attrs.className : ''),
				style: banner_style_inline,
			};
			var renderControls = el(
				'div',
				wrapperAttrs,
				attrs.banner_image_url && el(
					'img',
					{className: 'porto-ibanner-img', src: attrs.banner_image_url }
				),
				el(
					'div',
					{className: 'porto-ibanner-desc', style: title_bg},
					attrs.banner_title && el(
						'h2',
						{className: 'porto-ibanner-title', style: banner_title_style_inline},
						attrs.banner_title
					),
					attrs.content && el(
						'div',
						{className: 'porto-ibanner-content', style: banner_desc_style_inline},
						attrs.content
					),
					el( InnerBlocks, { allowedBlocks: [ 'porto/porto-interactive-banner-layer' ] } ),
				),
				attrs.banner_link && el('a', {className: 'porto-ibanner-link', href: attrs.banner_link})
			);

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

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
			image_opacity: { type: 'float', default: 1 },
			image_opacity_on_hover: { type: 'float', default: 1 },
			banner_style: { type: 'string' },
			banner_title_font_size: { type: 'int' },
			banner_color_bg: { type: 'string' },
			banner_color_title: { type: 'string' },
			banner_color_desc: { type: 'string' },
			banner_title_bg: { type: 'string' },
			banner_link: { type: 'string' },
		},
		supports: {
			align: ['wide', 'full'],
		},
		edit: PortoInteractiveBanner,
		save: function() {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);


/**
 * 11. Porto Interactive Banner layer
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
	"use strict";

	var __ = wpI18n.__,
		registerBlockType = wpBlocks.registerBlockType,
		InspectorControls = wpBlockEditor.InspectorControls,
		InnerBlocks = wpBlockEditor.InnerBlocks,
		el = wpElement.createElement,
		Component = wpElement.Component,
		TextControl = wpComponents.TextControl,
		RangeControl = wpComponents.RangeControl;

	class PortoInteractiveBannerLayer extends Component {
		constructor() {
			super( ...arguments );
			this.initLayer = this.initLayer.bind(this);
		}

		initLayer() {
			const attrs = this.props.attributes;

			let extra_style = {};
			if (50 == Number(attrs.horizontal)) {
				extra_style.display = 'flex';
				extra_style.justifyContent = 'center';
				extra_style.left = '0';
				extra_style.right = '';
				extra_style.width = '100%';
				if (50 == Number(attrs.vertical)) {
					extra_style.top = '0';
					extra_style.bottom = '0';
					extra_style.alignItems = 'center';
				}
			} else if (50 > Number(attrs.horizontal)) {
				extra_style.left = Number(attrs.horizontal) + '%';
				extra_style.right = '';
			} else {
				extra_style.right = (100 - Number(attrs.horizontal)) + '%';
				extra_style.left = '';
			}

			if (50 == Number(attrs.vertical)) {
				if (50 != Number(attrs.horizontal)) {
					extra_style.display = 'flex';
					extra_style.alignItems = 'center';
					extra_style.top = '0';
					extra_style.bottom = 0;
				}
			} else if (50 > Number(attrs.vertical)) {
				extra_style.top = Number(attrs.vertical) + '%';
			} else {
				extra_style.bottom = (100 - Number(attrs.vertical)) + '%';
			}

			if (attrs.width) {
				extra_style.width = attrs.width;
			} else if ( ! extra_style.width ) {
				extra_style.width = 'auto';
			}
			if (attrs.height) {
				extra_style.height = attrs.height;
			} else {
				extra_style.height = '';
			}
			extra_style.position = 'absolute';

			let clientId = this.props.clientId;
			jQuery('#block-' + clientId).addClass('porto-ibanner-layer').css(extra_style);
			jQuery('#block-' + clientId).children('.block-editor-inner-blocks').css('width', '100%');
		}

		componentDidMount() {
			const _this = this;
			setTimeout(function() {
				_this.initLayer();
			}, 100);
		}

		componentDidUpdate(prevProps, prevState) {
			const prev_attrs = prevProps.attributes,
				attrs = this.props.attributes;
			if ( prev_attrs.width !== attrs.width || prev_attrs.height !== attrs.height || prev_attrs.horizontal !== attrs.horizontal || prev_attrs.vertical !== attrs.vertical ) {
				this.initLayer();
			}
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, null,
				el( TextControl, {
					label: __('Width'),
					value: attrs.width,
					onChange: ( value ) => { props.setAttributes( { width: value } ); },
				} ),
				el(
					'p',
					{},
					__('For example: 50%, 100px, 100rem, 50vw, etc.'),
				),
				el( TextControl, {
					label: __('Height'),
					value: attrs.height,
					onChange: ( value ) => { props.setAttributes( { height: value } ); },
				} ),
				el(
					'p',
					{},
					__('For example: 50%, 100px, 100rem, 50vw, etc.'),
				),
				el( RangeControl, {
					label: __('Horizontal Position'),
					value: attrs.horizontal,
					min: -50,
					max: 150,
					step: 1,
					onChange: ( value ) => { props.setAttributes( { horizontal: value } ); },
				} ),
				el(
					'p',
					{},
					__('50 is center, 0 is left and 100 is right.'),
				),
				el( RangeControl, {
					label: __('vertical Position'),
					value: attrs.vertical,
					min: -50,
					max: 150,
					step: 1,
					onChange: ( value ) => { props.setAttributes( { vertical: value } ); },
				} ),
				el(
					'p',
					{},
					__('50 is middle, 0 is top and 100 is bottom.'),
				),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay (ms)'),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration (ms)'),
					value: attrs.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} )
			);

			var renderControls = el( InnerBlocks );

			return [
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-interactive-banner-layer', {
		title: 'Porto Interactive Banner Layer',
		icon: 'porto',
		category: 'porto',
		parent: ['porto/porto-interactive-banner'],
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
		save: function() {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 12. Porto Woocommerce Products
 */
function _makeConsumableArray(arr) {
	if (Array.isArray(arr)) {
		for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
			arr2[i] = arr[i];
		}
		return arr2;
	} else {
		return Array.from(arr);
	}
}
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash, apiFetch) {
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
		CheckboxControl = wpComponents.CheckboxControl;

	class PortoProducts extends Component {
		constructor() {
			super( ...arguments );

			this.state = {
				categoriesList: [],
				products: [],
				query: '',
			};

			this.initSlider = this.initSlider.bind(this);
			this.fetchProducts = this.fetchProducts.bind(this);
		}

		componentDidMount() {
			this.fetchProducts();
		}

		componentDidUpdate(prevProps, prevState) {
			const _this = this,
				categoriesList = _this.state.categoriesList,
				attrs = _this.props.attributes,
				clientId = _this.props.clientId,
				$wrap = jQuery('#block-' + clientId + ' .products-container');

			if ('selected' === attrs.category_type && 0 === categoriesList.length) {
				wp.apiFetch({ path: '/wc/v2/products/categories?per_page=99' }).then(function(obj) {
					_this.setState({ categoriesList: obj });
				});
			}

			if ($wrap.data('owl.carousel') && ('products-slider' != attrs.view || _this.getQuery() !== _this.state.query)) {
				$wrap.trigger('destroy.owl.carousel');
			} else if ($wrap.data('isotope') && ('creative' != attrs.view || _this.getQuery() !== _this.state.query)) {
				$wrap.isotope('destroy');
			}

			if ('products-slider' == attrs.view && _this.state.products.length && ('products-slider' !== prevProps.attributes.view || prevState.products !== _this.state.products || attrs.columns !== prevProps.attributes.columns || attrs.navigation !== prevProps.attributes.navigation || attrs.pagination !== prevProps.attributes.pagination || attrs.dots_pos !== prevProps.attributes.dots_pos || attrs.nav_pos !== prevProps.attributes.nav_pos || attrs.nav_pos2 !== prevProps.attributes.nav_pos2 || attrs.nav_type !== prevProps.attributes.nav_type)) {
				if ($wrap.data('owl.carousel')) {
					$wrap.trigger('destroy.owl.carousel');
				}
				_this.initSlider();
			} else if ('creative' == attrs.view && _this.state.products.length && ('creative' !== prevProps.attributes.view || prevState.products !== _this.state.products || attrs.grid_layout !== prevProps.attributes.grid_layout || attrs.grid_height !== prevProps.grid_height || attrs.spacing !== prevProps.attributes.spacing) ) {
				if ($wrap.data('isotope')) {
					$wrap.isotope('destroy');
				}
				$wrap.children().each(function(i) {
					if (!(this instanceof HTMLElement)) {
						Object.setPrototypeOf(this, HTMLElement.prototype);
					}
				});
				$wrap.isotope({
					itemSelector: '.product-col',
					masonry: {'columnWidth': '.grid-col-sizer'}
				});
				jQuery.ajax({
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
					success: function(res) {
						$wrap.prev('style').remove();
						jQuery(res).insertBefore($wrap);
						$wrap.isotope('layout');
					}
				});
			}

			if (_this.getQuery() !== _this.state.query) {
				_this.fetchProducts();
			}
		}

		initSlider() {
			const attrs = this.props.attributes,
				clientId = this.props.clientId;
			jQuery('#block-' + clientId + ' .products-container').owlCarousel({
				items: Number( attrs.columns ),
				nav: attrs.navigation,
				dots: attrs.pagination,
				navText: ["", ""],
			});
		}

		getQuery() {
			var attrs = this.props.attributes,
				columns = attrs.columns,
				status = attrs.status;

			var query = {};
			if (attrs.count) {
				query.per_page = attrs.count;
			} else if ('creative' == attrs.view && porto_block_vars.creative_layouts[Number(attrs.grid_layout)]) {
				query.per_page = porto_block_vars.creative_layouts[Number(attrs.grid_layout)].length;
			}

			if (attrs.category_type === 'selected') {
				query.category = attrs.categories.join(',');
			}
			if (status == 'featured') {
				query.featured = 1;
			}
			if (status == 'on_sale') {
				query.on_sale = 1;
			}
			if ('total_sales' == attrs.orderby) {
				query.orderby = 'popularity';
			} else {
				query.orderby = attrs.orderby;
			}
			query.order = attrs.order;

			var query_string = '?',
				_iteratorNormalCompletion = true,
				_didIteratorError = false,
				_iteratorError = undefined;

			try {
				for (var _iterator = Object.keys(query)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
					var key = _step.value;

					query_string += key + '=' + query[key] + '&';
				}
			} catch (err) {
				_didIteratorError = true;
				_iteratorError = err;
			} finally {
				try {
					if (!_iteratorNormalCompletion && _iterator.return) {
						_iterator.return();
					}
				} finally {
					if (_didIteratorError) {
						throw _iteratorError;
					}
				}
			}

			var endpoint = '/portowc/v1/products' + query_string;
			return endpoint;
		}

		setCategories(catID, isAdd) {
			var props = this.props,
				attrs = props.attributes,
				setAttributes = props.setAttributes;
			var categories = attrs.categories;


			if (isAdd) {
				categories = [].concat(_makeConsumableArray(categories), [catID]);
			} else {
				categories = categories.filter( function (cat) {
					return cat !== catID;
				} );
			}
			setAttributes( { category: categories.join(','), categories: categories } );
			this.fetchProducts();
		}

		fetchProducts() {
			var _this = this;
			var query = this.getQuery();

			_this.setState({
				query: query
			});

			apiFetch({ path: query }).then(function (products) {
				_this.setState({
					products: products,
				});
			});
		}

		render() {
			var _this = this,
				props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				categoriesList = this.state.categoriesList,
				setAttributes = props.setAttributes;

			var viewControls = [{
				icon: 'grid-view',
				title: __('Grid'),
				onClick: function onClick() {
					return setAttributes({ view: 'grid' });
				},
				isActive: attrs.view === 'grid'
			}, {
				icon: 'list-view',
				title: __('List'),
				onClick: function onClick() {
					return setAttributes({ view: 'list' });
				},
				isActive: attrs.view === 'list'
			}, {
				icon: 'slides',
				title: __('Slider'),
				onClick: function onClick() {
					return setAttributes({ view: 'products-slider' });
				},
				isActive: attrs.view === 'products-slider'
			}, {
				icon: 'media-spreadsheet',
				title: __('Creative Grid'),
				onClick: function onClick() {
					return setAttributes({ view: 'creative' });
				},
				isActive: attrs.view === 'creative'
			}];

			var inspectorControls = el( InspectorControls, null,
				el( TextControl, {
					label: __('Title'),
					value: attrs.title,
					onChange: function(value) { setAttributes( { title: value } ); },
				} ),
				attrs.title && el( SelectControl, {
					label: __('Title Border Style'),
					value: attrs.title_border_style,
					options: [{ label: __('No Border'), value: '' }, { label: __('Bottom Border'), value: 'border-bottom' }, { label: __('Middle Border'), value: 'border-middle' }],
					onChange: ( value ) => { setAttributes( { title_border_style: value } ); },
				} ),
				el( SelectControl, {
					label: __('Product Status'),
					value: attrs.status,
					options: [{ label: __('All'), value: '' }, { label: __('Featured'), value: 'featured' }, { label: __('On Sale'), value: 'on_sale' }],
					onChange: function onChange(value) {
						return setAttributes({ status: value });
					}
				}),
				el( SelectControl, {
					label: __('Category'),
					value: attrs.category_type,
					options: [{ label: __('All'), value: '' }, { label: __('Selected'), value: 'selected' }],
					onChange: function onChange(value) {
						return setAttributes({ category_type: value });
					}
				}),
				attrs.category_type === 'selected' && el(
					'div',
					{ className: 'porto-categories-list' },
					categoriesList.map(function (cat, index) {
						return el( CheckboxControl, {
							key: index,
							label: [cat.name, el(
								'span',
								{ key: 'cat-count', style: { fontSize: 'small', color: '#888', marginLeft: 5 } },
								'(',
								cat.count,
								')'
							)],
							checked: jQuery.inArray(cat.id, attrs.categories) > -1,
							onChange: function onChange(checked) {
								return _this.setCategories(cat.id, checked);
							}
						});
					})
				),
				el( RangeControl, {
					label: __('Per page'),
					value: attrs.count,
					min: 1,
					max: 100,
					onChange: ( value ) => { setAttributes( { count: value } ); },
				} ),
				el( SelectControl, {
					label: __('Order by'),
					value: attrs.orderby,
					options: [{ label: __('Date'), value: 'date' }, { label: __('Price'), value: 'price' },{ label: __('Rating'), value: 'rating' },{ label: __('Total Sales'), value: 'total_sales' },{ label: __('Popularity'), value: 'popularity' },{ label: __('ID'), value: 'id' }, { label: __('Title'), value: 'title' }, { label: __('Random'), value: 'rand' }, { label: __('Menu order'), value: 'menu_order' }],
					onChange: ( value ) => { setAttributes( { orderby: value } ); },
				} ),
				attrs.orderby != 'rating' && el( SelectControl, {
					label: __('Order'),
					value: attrs.order,
					options: [{ label: __('Descending'), value: 'desc' }, { label: __('Ascending'), value: 'asc' }],
					onChange: ( value ) => { setAttributes( { order: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Show category filter'),
					checked: attrs.category_filter,
					onChange: ( value ) => { setAttributes( { category_filter: value } ); },
				} ),
				attrs.view != 'products-slider' && el( SelectControl, {
					label: __('Pagination Style'),
					value: attrs.pagination_style,
					options: [{ label: __('No pagination'), value: '' }, { label: __('Default'), value: 'default' }, { label: __('Load more'), value: 'load_more' }],
					onChange: ( value ) => { setAttributes( { pagination_style: value } ); },
				} ),
				('grid' == attrs.view || 'products-slider' == attrs.view) && el( RangeControl, {
					label: __('Columns'),
					value: attrs.columns,
					min: 1,
					max: 8,
					onChange: ( value ) => { setAttributes( { columns: value } ); },
				} ),
				('grid' == attrs.view || 'products-slider' == attrs.view) && el( SelectControl, {
					label: __('Columns on mobile ( <= 575px )'),
					value: attrs.columns_mobile,
					options: [{ label: __('Default'), value: '' }, { label: '1', value: '1' }, { label: '2', value: '2' }, { label: '3', value: '3' }],
					onChange: ( value ) => { setAttributes( { columns_mobile: value } ); },
				} ),
				('grid' == attrs.view || 'products-slider' == attrs.view) && el( SelectControl, {
					label: __('Column Width'),
					value: attrs.column_width,
					options: [{ label: __('Default'), value: '' }, { label: __('1/1 of content width'), value: '1' }, { label: __('1/2 of content width'), value: '2' }, { label: __('1/3 of content width'), value: '3' }, { label: __('1/4 of content width'), value: '4' }, { label: __('1/5 of content width'), value: '5' }, { label: __('1/6 of content width'), value: '6' }, { label: __('1/7 of content width'), value: '7' }, { label: __('1/8 of content width'), value: '8' }],
					onChange: ( value ) => { setAttributes( { column_width: value } ); },
				} ),
				'creative' == attrs.view && el( RangeControl, {
					label: __('Creative Grid Layout'),
					value: attrs.grid_layout,
					min: 1,
					max: 12,
					onChange: ( value ) => { setAttributes( { grid_layout: value } ); },
				} ),
				'creative' == attrs.view && el( TextControl, {
					label: __('Grid Height'),
					value: attrs.grid_height,
					onChange: ( value ) => { setAttributes( { grid_height: value } ); },
				} ),
				('creative' == attrs.view || 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( RangeControl, {
					label: __('Column Spacing (px)'),
					value: attrs.spacing,
					min: 0,
					max: 100,
					onChange: ( value ) => { setAttributes( { spacing: value } ); },
				} ),
				'list' != attrs.view && el( SelectControl, {
					label: __('Add Links Position'),
					value: 'creative' == attrs.view && 'onimage' != attrs.addlinks_pos && 'onimage2' != attrs.addlinks_pos && 'onimage3' != attrs.addlinks_pos ? 'onimage' : attrs.addlinks_pos,
					options: 'creative' == attrs.view ? [{ label: __('On Image'), value: 'onimage' }, { label: __('On Image with Overlay 1'), value: 'onimage2' }, { label: __('On Image with Overlay 2'), value: 'onimage3' }] : porto_block_vars.product_layouts,
					onChange: ( value ) => { setAttributes( { addlinks_pos: value } ); },
				} ),
				'products-slider' == attrs.view && el( ToggleControl, {
					label: __('Show Slider Navigation'),
					checked: attrs.navigation,
					onChange: ( value ) => { setAttributes( { navigation: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
					label: __('Nav Position'),
					value: attrs.nav_pos,
					options: [{ label: __('Middle'), value: '' }, { label: __('Middle of Images'), value: 'nav-center-images-only' }, { label: __('Top'), value: 'show-nav-title' }, { label: __('Bottom'), value: 'nav-bottom' }],
					onChange: ( value ) => { setAttributes( { nav_pos: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
					label: __('Nav Inside?'),
					value: attrs.nav_pos2,
					options: [{ label: __('Default'), value: '' }, { label: __('Inside'), value: 'nav-pos-inside' }, { label: __('Outside'), value: 'nav-pos-outside' }],
					onChange: ( value ) => { setAttributes( { nav_pos2: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
					label: __('Nav Type'),
					value: attrs.nav_type,
					options: porto_block_vars.carousel_nav_types,
					onChange: ( value ) => { setAttributes( { nav_type: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && el( ToggleControl, {
					label: __('Show Nav on Hover'),
					checked: attrs.show_nav_hover,
					onChange: ( value ) => { setAttributes( { show_nav_hover: value } ); },
				} ),
				'products-slider' == attrs.view && el( ToggleControl, {
					label: __('Show Slider Pagination'),
					checked: attrs.pagination,
					onChange: ( value ) => { setAttributes( { pagination: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.pagination && el( SelectControl, {
					label: __('Dots Position'),
					value: attrs.dots_pos,
					options: [{ label: __('Bottom'), value: '' }, { label: __('Top right'), value: 'show-dots-title-right' }],
					onChange: ( value ) => { setAttributes( { dots_pos: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
					value: attrs.animation_duration,
					onChange: ( value ) => { setAttributes( { animation_duration: value } ); },
				} ),
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
				classes += ' products-slider owl-carousel';
				if (attrs.navigation) {
					if (attrs.nav_pos) classes += ' ' + attrs.nav_pos;
					if (attrs.nav_type) classes += ' ' + attrs.nav_type;
				}
				if (attrs.pagination && attrs.dots_pos) {
					classes += ' ' + attrs.dots_pos
				}
				if (attrs.navigation) {
					if (attrs.nav_pos2) {
						classes += ' ' + attrs.nav_pos2;
					}
				}
				if (attrs.show_nav_hover) {
					classes += ' show-nav-hover';
				}
			} else if ( 'creative' == attrs.view ) {
				classes += ' grid-creative';
			} else {
				classes += ' ' + attrs.view;
			}
			classes += ' pcols-ls-2 pcols-xs-' + Math.min( 3, attrs.columns ) + ' pcols-lg-' + attrs.columns;
			if (attrs.className) {
				classes += ' ' + attrs.className;
			}
			products_attrs.className = 'products products-container ' + classes;

			let item_classes = porto_block_vars.creative_layouts[Number(attrs.grid_layout)];

			var renderControls = el(
				'div',
				{ className: 'porto-products' + ( attrs.title_border_style ? ' title-' + attrs.title_border_style : '' ) },
				attrs.title && el(
					'h2',
					{ className: 'products-title section-title', dangerouslySetInnerHTML: { __html: attrs.title.replace(/<script.*?\/script>/g, '') } }
				),
				el(
					'ul',
					products_attrs,
					this.state.products.map(function (product, index) {
						let image = null, item_class = '';
						if (product.images.length) {
							image = el('img', { src: product.images[0].src });
						} else if (porto_swatches_params && porto_swatches_params.placeholder_src) {
							image = el('img', { src: porto_swatches_params.placeholder_src });
						}

						if ('creative' == attrs.view && typeof item_classes[index % item_classes.length] != 'undefined') {
							item_class += ' '  + item_classes[index % item_classes.length];
						}

						const add_links_html = el( 'div', { className: 'add-links-wrap' },
							el(
								'div',
								{ className: 'add-links clearfix' },
								el(
									'span',
									{ className: 'add_to_cart_button button' },
									__('Add to cart')
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
								('list' == attrs.view || 'onimage2' != attrs.addlinks_pos) && el(
									'span',
									{ className: 'quickview' },
									__('Quick View')
								)
							),
							'list' != attrs.view && 'onimage2' == attrs.addlinks_pos && el(
								'span',
								{ className: 'quickview' },
								__('Quick View')
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
										product.categories.map(function(cat, i) {
											return el(
												'span',
												{ dangerouslySetInnerHTML: { __html: (i ? ', ' : '') + cat.name } }
											);
										})
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
											{ className: 'star-rating', 'title': parseFloat(product.average_rating) },
											el(
												'span',
												{ style: { width: parseFloat(product.average_rating) * 20 + '%' } }
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
					}),
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
			navigation: { type: 'boolean', default: true },
			show_nav_hover: { type: 'boolean', default: false },
			pagination: { type: 'boolean', default: false },
			nav_pos: { type: 'string', default: '' },
			nav_pos2: { type: 'string' },
			nav_type: { type: 'string' },
			dots_pos: { type: 'string' },
			category_filter: { type: 'boolean' },
			pagination_style: { type: 'string' },
			animation_type: { type: 'string' },
			animation_duration: { type: 'int' },
			animation_delay: { type: 'int' },
		},
		edit: PortoProducts,
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash, wp.apiFetch);


/**
 * 13. Porto Heading
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
		RangeControl = wpComponents.RangeControl,
		SelectControl = wpComponents.SelectControl;

	class PortoHeading extends Component {
		constructor() {
			super( ...arguments );
		}

		render() {
			var props = this.props,
				attrs = props.attributes,
				clientId = props.clientId;

			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __('Font Size'),
					value: attrs.font_size,
					onChange: ( value ) => { props.setAttributes( { font_size: value } ); },
				} ),
				el( RangeControl, {
					label: __('Font Weight'),
					value: attrs.font_weight,
					min: 100,
					max: 900,
					step: 100,
					onChange: ( value ) => { props.setAttributes( { font_weight: value } ); },
				} ),
				el( TextControl, {
					label: __('Line Height'),
					value: attrs.line_height,
					onChange: ( value ) => { props.setAttributes( { line_height: value } ); },
				} ),
				el( TextControl, {
					label: __('Letter Spacing'),
					value: attrs.letter_spacing,
					onChange: ( value ) => { props.setAttributes( { letter_spacing: value } ); },
				} ),
				el( SelectControl, {
					label: __('Tag'),
					value: attrs.tag,
					options: [{ label: __('H1'), value: 'h1' }, { label: __('H2'), value: 'h2' }, { label: __('H3'), value: 'h3' }, { label: __('H4'), value: 'h4' }, { label: __('H5'), value: 'h5' }, { label: __('H6'), value: 'h6' }, { label: __('div'), value: 'div' }, { label: __('p'), value: 'p' }, { label: __('span'), value: 'span' }],
					onChange: ( value ) => { props.setAttributes( { tag: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Color Settings'),
					initialOpen: false,
					colorSettings: [{
						label: __('Font Color'),
						value: attrs.color,
						onChange: function onChange(value) {
							return props.setAttributes({ color: value });
						}
					}]
				}),
				el( TextControl, {
					label: __('Link'),
					value: attrs.link,
					onChange: ( value ) => { props.setAttributes( { link: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
					value: attrs.animation_duration,
					onChange: ( value ) => { props.setAttributes( { animation_duration: value } ); },
				} ),

			);

			let wrapper_style = {}, style_inline = {};

			if ( attrs.font_size ) {
				let unit = attrs.font_size.replace( /[0-9.]/g, '' );
				if ( ! unit ) {
					attrs.font_size += 'px';
				}
				style_inline.fontSize = attrs.font_size;
			}
			if ( attrs.font_weight ) {
				style_inline.fontWeight = Number( attrs.font_weight );
			}
			if ( attrs.line_height ) {
				let unit = attrs.line_height.replace( /[0-9.]/g, '' );
				if ( ! unit && attrs.line_height > 3 ) {
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

			var renderControls = el(
				RichText,
				{
					key: 'editable',
					tagName: attrs.tag,
					className: 'porto-heading' + ( attrs.className ? ' ' + attrs.className : '' ),
					style: style_inline,
					onChange: function( value ) {
						return props.setAttributes({ title: value });
					},
					value: attrs.title,
				}
			);

			return [
				el( BlockControls, null,
					el( BlockAlignmentToolbar, { value: attrs.alignment, onChange: function onChange(value) {
						return props.setAttributes({ alignment: value });
					} } )
				),
				inspectorControls,
				renderControls,
			];
		}
	}

	registerBlockType( 'porto/porto-heading', {
		title: 'Porto Heading',
		icon: 'porto',
		category: 'porto',
		attributes: {
			title: {
				type: 'string',
			},
			font_size: {
				type: 'string',
			},
			font_weight: {
				type: 'int',
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
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 13. Porto Button
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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

	class PortoButton extends Component {
		constructor() {
			super( ...arguments );
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __('Title'),
					value: attrs.title,
					onChange: ( value ) => { props.setAttributes( { title: value } ); },
				} ),
				el( TextControl, {
					label: __('Link'),
					value: attrs.link,
					onChange: ( value ) => { props.setAttributes( { link: value } ); },
				} ),
				el( SelectControl, {
					label: __('Layout'),
					value: attrs.layout,
					options: [{ label: __('Default'), value: '' }, { label: __('Modern'), value: 'modern' }, { label: __('Outline'), value: 'borders' }],
					onChange: ( value ) => { props.setAttributes( { layout: value } ); },
				} ),
				el( SelectControl, {
					label: __('Size'),
					value: attrs.size,
					options: [{ label: __('Extra Small'), value: 'xs' }, { label: __('Small'), value: 'sm' }, { label: __('Medium'), value: 'md' }, { label: __('Large'), value: 'lg' }, { label: __('Extra Large'), value: 'xl' }],
					onChange: ( value ) => { props.setAttributes( { size: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Is Full Width?'),
					checked: attrs.is_block,
					onChange: ( value ) => { props.setAttributes( { is_block: value } ); },
				} ),
				el( SelectControl, {
					label: __('Skin'),
					value: attrs.skin,
					options: [{ label: __('Default'), value: 'default' }, { label: __('Primary'), value: 'primary' }, { label: __('Secondary'), value: 'secondary' }, { label: __('Tertiary'), value: 'tertiary' }, { label: __('Quaternary'), value: 'quaternary' }, { label: __('Dark'), value: 'dark' }, { label: __('Light'), value: 'light' }],
					onChange: ( value ) => { props.setAttributes( { skin: value } ); },
				} ),
				el( TextControl, {
					label: __('Icon Class (ex: fas fa-pencil-alt)'),
					value: attrs.icon_cls,
					onChange: ( value ) => { props.setAttributes( { icon_cls: value } ); },
				} ),
				attrs.icon_cls && el( SelectControl, {
					label: __('Icon Position'),
					value: attrs.icon_pos,
					options: [{ label: __('Left'), value: 'left' }, { label: __('Right'), value: 'right' }],
					onChange: ( value ) => { props.setAttributes( { icon_pos: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { props.setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { props.setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
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
				{className: 'porto-button', style: style_inline},
				el(
					'button',
					{className: 'btn' + btn_classes},
					attrs.icon_cls && 'left' == attrs.icon_pos && el(
						'i',
						{className: attrs.icon_cls}
					),
					attrs.title,
					attrs.icon_cls && 'right' == attrs.icon_pos && el(
						'i',
						{className: attrs.icon_cls}
					)
				),
			);

			return [
				el( BlockControls, null,
					el( BlockAlignmentToolbar, { value: attrs.align, onChange: function onChange(value) {
						return props.setAttributes({ align: value });
					} } )
				),
				inspectorControls,
				renderControls,
			];
		}
	}

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
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 15. Porto Section
 *
 * Section block which has background image, parallax image, background video, inner container, etc.
 *
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
		ToggleControl = wpComponents.ToggleControl,
		SelectControl = wpComponents.SelectControl;

	class PortoSection extends Component {
		constructor() {
			super( ...arguments );
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, {},
				el( ToggleControl, {
					label: __('Add Container?'),
					checked: attrs.add_container,
					onChange: ( value ) => { props.setAttributes( { add_container: value } ); },
				} ),
				el( PanelColorSettings, {
					title: __('Background Color'),
					initialOpen: false,
					colorSettings: [{
						label: __('Background Color'),
						value: attrs.bg_color,
						onChange: function onChange(value) {
							return props.setAttributes({ bg_color: value });
						}
					}]
				}),
				el( MediaUpload, {
					allowedTypes: ['image'],
					value: attrs.bg_img,
					onSelect: function onSelect(image) {
						return props.setAttributes({ bg_img_url: image.url, bg_img: image.id });
					},
					render: function render(_ref) {
						var open = _ref.open;
						return el(IconButton, {
							className: 'components-toolbar__control',
							label: __('Background Image'),
							icon: 'edit',
							onClick: open
						});
					}
				}),
				attrs.bg_img && el(IconButton, {
					className: 'components-toolbar__control',
					label: __('Remove image'),
					icon: 'no',
					onClick: function onClick() {
						return props.setAttributes({ bg_img_url: undefined, bg_img: undefined });
					}
				}),
				el( TextControl, {
					label: __('Background Image URL'),
					value: attrs.bg_img_url,
					onChange: ( value ) => { props.setAttributes( { bg_img_url: value } ); },
				} ),
				attrs.bg_img_url && el( SelectControl, {
					label: __('Background Repeat'),
					value: attrs.bg_repeat,
					options: [{ label: __('Default'), value: '' }, { label: __('No Repeat'), value: 'no-repeat' }, { label: __('Repeat'), value: 'repeat' }, { label: __('Repeat X'), value: 'repeat-x' }, { label: __('Repeat Y'), value: 'repeat-y' }],
					onChange: ( value ) => { props.setAttributes( { bg_repeat: value } ); },
				} ),
				attrs.bg_img_url && el( SelectControl, {
					label: __('Background Position'),
					value: attrs.bg_pos,
					options: [{ label: __('Default'), value: '' }, { label: __('Center Center'), value: 'center center' }, { label: __('Center Left'), value: 'center left' }, { label: __('Center Right'), value: 'center right' }, { label: __('Top Center'), value: 'top center' }, { label: __('Top Left'), value: 'top left' }, { label: __('Top Right'), value: 'top right' }, { label: __('Bottom Center'), value: 'bottom center' }, { label: __('Bottom Left'), value: 'bottom left' }, { label: __('Bottom Right'), value: 'bottom right' }],
					onChange: ( value ) => { props.setAttributes( { bg_pos: value } ); },
				} ),
				attrs.bg_img_url && el( SelectControl, {
					label: __('Background Size'),
					value: attrs.bg_size,
					options: [{ label: __('Default'), value: '' }, { label: __('Cover'), value: 'cover' }, { label: __('Contain'), value: 'contain' }, { label: __('Auto'), value: 'auto' }, { label: __('100% auto'), value: '100% auto' }, { label: __('auto 100%'), value: 'auto 100%' }],
					onChange: ( value ) => { props.setAttributes( { bg_size: value } ); },
				} ),
				attrs.bg_img_url && el( RangeControl, {
					label: __('Parallax Speed'),
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
					label: __('Background Video URL (mp4)'),
					value: attrs.bg_video,
					onChange: ( value ) => { props.setAttributes( { bg_video: value } ); },
				} ),
				el( SelectControl, {
					label: __('Tag'),
					value: attrs.tag,
					options: [{ label: __('section'), value: 'section' }, { label: __('div'), value: 'div' }, { label: __('article'), value: 'article' }],
					onChange: ( value ) => { props.setAttributes( { tag: value } ); },
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

			var renderControls = el(
				attrs.tag,
				{className: 'vc_section porto-section', style: style_inline},
				el( InnerBlocks ),
			);

			return [
				inspectorControls,
				renderControls,
			];
		}
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
		},
		supports: {
			align: ['full'],
		},
		edit: PortoSection,
		save: function() {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 16. Porto Woocommerce Product Categories
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash, apiFetch) {
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
		CheckboxControl = wpComponents.CheckboxControl;

	class PortoProductCategories extends Component {
		constructor() {
			super( ...arguments );

			this.state = {
				categoriesList: [],
				query: '',
			};

			this.initSlider = this.initSlider.bind(this);
			this.fetchCategories = this.fetchCategories.bind(this);
		}

		fetchCategories() {
			let _this = this,
				query = _this.getQuery();

			wp.apiFetch({ path: query }).then(function(obj) {
				_this.setState({ categoriesList: obj, query: query });
			});
		}

		componentDidMount() {
			this.fetchCategories();
		}

		componentDidUpdate(prevProps, prevState) {
			const _this = this,
				categoriesList = _this.state.categoriesList,
				attrs = _this.props.attributes,
				clientId = _this.props.clientId,
				$wrap = jQuery('#block-' + clientId + ' .products-container');

			if ($wrap.data('owl.carousel') && ('products-slider' != attrs.view || _this.getQuery() !== _this.state.query)) {
				$wrap.trigger('destroy.owl.carousel');
			} else if ($wrap.data('isotope') && ('creative' != attrs.view || _this.getQuery() !== _this.state.query)) {
				$wrap.isotope('destroy');
			}

			if ('products-slider' == attrs.view && categoriesList.length && ('products-slider' !== prevProps.attributes.view || prevState.categoriesList !== categoriesList || attrs.columns !== prevProps.attributes.columns || attrs.navigation !== prevProps.attributes.navigation || attrs.pagination !== prevProps.attributes.pagination || attrs.dots_pos !== prevProps.attributes.dots_pos || attrs.nav_pos !== prevProps.attributes.nav_pos || attrs.nav_pos2 !== prevProps.attributes.nav_pos2 || attrs.nav_type !== prevProps.attributes.nav_type)) {
				if ($wrap.data('owl.carousel')) {
					$wrap.trigger('destroy.owl.carousel');
				}
				_this.initSlider();
			} else if ('creative' == attrs.view && categoriesList.length && ('creative' !== prevProps.attributes.view || prevState.categoriesList !== categoriesList || attrs.grid_layout !== prevProps.attributes.grid_layout || attrs.grid_height !== prevProps.grid_height || attrs.spacing !== prevProps.attributes.spacing) ) {
				if ($wrap.data('isotope')) {
					$wrap.isotope('destroy');
				}
				$wrap.children().each(function(i) {
					if (!(this instanceof HTMLElement)) {
						Object.setPrototypeOf(this, HTMLElement.prototype);
					}
				});
				$wrap.isotope({
					itemSelector: '.product-col',
					masonry: {'columnWidth': '.grid-col-sizer'}
				});
				jQuery.ajax({
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
					success: function(res) {
						$wrap.prev('style').remove();
						jQuery(res).insertBefore($wrap);
						$wrap.isotope('layout');
					}
				});
			}

			if (_this.getQuery() !== _this.state.query) {
				_this.fetchCategories();
			}
		}

		initSlider() {
			const attrs = this.props.attributes,
				clientId = this.props.clientId;
			jQuery('#block-' + clientId + ' .products-container').owlCarousel({
				items: Number( attrs.columns ),
				nav: attrs.navigation,
				dots: attrs.pagination,
				navText: ["", ""],
			});
		}

		getQuery() {
			let attrs = this.props.attributes,
				columns = attrs.columns,
				status = attrs.status,
				query_string = 'porto=1';

			if (attrs.count) {
				query_string += '&per_page=' + attrs.count;
			} else if ('creative' == attrs.view && porto_block_vars.creative_layouts[Number(attrs.grid_layout)]) {
				query_string += '&per_page=' + porto_block_vars.creative_layouts[Number(attrs.grid_layout)].length;
			}
			if (attrs.parent) {
				query_string += '&parent=' + attrs.parent;
			}
			if (attrs.ids) {
				query_string += '&include=' + attrs.ids + '&orderby=include&order=asc';
			} else {
				if (attrs.orderby) {
					query_string += '&orderby=' + attrs.orderby;
				}
				query_string += '&order=' + attrs.order;
			}
			if (attrs.image_size) {
				query_string += '&image_size=' + attrs.image_size;
			}
			if (attrs.hide_empty) {
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

			var viewControls = [{
				icon: 'grid-view',
				title: __('Grid'),
				onClick: function onClick() {
					return setAttributes({ view: 'grid' });
				},
				isActive: attrs.view === 'grid'
			}, {
				icon: 'slides',
				title: __('Slider'),
				onClick: function onClick() {
					return setAttributes({ view: 'products-slider' });
				},
				isActive: attrs.view === 'products-slider'
			}, {
				icon: 'media-spreadsheet',
				title: __('Creative Grid'),
				onClick: function onClick() {
					return setAttributes({ view: 'creative' });
				},
				isActive: attrs.view === 'creative'
			}];

			var inspectorControls = el( InspectorControls, null,
				el( TextControl, {
					label: __('Title'),
					value: attrs.title,
					onChange: function(value) { setAttributes( { title: value } ); },
				} ),
				el( RangeControl, {
					label: __('Number'),
					value: attrs.number,
					min: 1,
					max: 100,
					onChange: ( value ) => { setAttributes( { number: value } ); },
				} ),
				('grid' == attrs.view || 'products-slider' == attrs.view) && el( RangeControl, {
					label: __('Columns'),
					value: attrs.columns,
					min: 1,
					max: 8,
					onChange: ( value ) => { setAttributes( { columns: value } ); },
				} ),
				('grid' == attrs.view || 'products-slider' == attrs.view) && el( SelectControl, {
					label: __('Columns on mobile ( <= 575px )'),
					value: attrs.columns_mobile,
					options: [{ label: __('Default'), value: '' }, { label: '1', value: '1' }, { label: '2', value: '2' }, { label: '3', value: '3' }],
					onChange: ( value ) => { setAttributes( { columns_mobile: value } ); },
				} ),
				('grid' == attrs.view || 'products-slider' == attrs.view) && el( SelectControl, {
					label: __('Column Width'),
					value: attrs.column_width,
					options: [{ label: __('Default'), value: '' }, { label: __('1/1 of content width'), value: '1' }, { label: __('1/1 of content width'), value: '2' }, { label: __('1/3 of content width'), value: '3' }, { label: __('1/4 of content width'), value: '4' }, { label: __('1/5 of content width'), value: '5' }, { label: __('1/6 of content width'), value: '6' }, { label: __('1/7 of content width'), value: '7' }, { label: __('1/8 of content width'), value: '8' }],
					onChange: ( value ) => { setAttributes( { column_width: value } ); },
				} ),
				'creative' == attrs.view && el( RangeControl, {
					label: __('Creative Grid Layout'),
					value: attrs.grid_layout,
					min: 1,
					max: 12,
					onChange: ( value ) => { setAttributes( { grid_layout: value } ); },
				} ),
				'creative' == attrs.view && el( TextControl, {
					label: __('Grid Height'),
					value: attrs.grid_height,
					onChange: ( value ) => { setAttributes( { grid_height: value } ); },
				} ),
				'creative' == attrs.view && el( RangeControl, {
					label: __('Column Spacing (px)'),
					value: attrs.spacing,
					min: 0,
					max: 100,
					onChange: ( value ) => { setAttributes( { spacing: value } ); },
				} ),
				el( SelectControl, {
					label: __('Text Position'),
					value: attrs.text_position,
					options: [{ label: __('Inner Middle Left'), value: 'middle-left' }, { label: __('Inner Middle Center'), value: 'middle-center' }, { label: __('Inner Middle Right'), value: 'middle-right' }, { label: __('Inner Bottom Left'), value: 'bottom-left' }, { label: __('Inner Bottom Center'), value: 'bottom-center' }, { label: __('Inner Bottom Right'), value: 'bottom-right' }, { label: __('Outside'), value: 'outside-center' }],
					onChange: ( value ) => { setAttributes( { text_position: value } ); },
				} ),
				el( RangeControl, {
					label: __('Overlay Background Opacity (%)'),
					value: attrs.overlay_bg_opacity,
					min: 0,
					max: 100,
					onChange: ( value ) => { props.setAttributes( { overlay_bg_opacity: value } ); },
				} ),
				el( SelectControl, {
					label: __('Text Color'),
					value: attrs.text_color,
					options: [{ label: __('Dark'), value: 'dark' }, { label: __('Light'), value: 'light' }],
					onChange: ( value ) => { setAttributes( { text_color: value } ); },
				} ),
				el( SelectControl, {
					label: __('Order by'),
					value: attrs.orderby,
					options: [{ label: __('Title'), value: 'name' }, { label: __('ID'), value: 'id' },{ label: __('Product Count'), value: 'count' }, { label: __('Description'), value: 'description' }, { label: __('Term Group'), value: 'term_group' }],
					onChange: ( value ) => { setAttributes( { orderby: value } ); },
				} ),
				attrs.orderby != 'rating' && el( SelectControl, {
					label: __('Order'),
					value: attrs.order,
					options: [{ label: __('Descending'), value: 'desc' }, { label: __('Ascending'), value: 'asc' }],
					onChange: ( value ) => { setAttributes( { order: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Hide empty'),
					checked: attrs.hide_empty,
					onChange: ( value ) => { props.setAttributes( { hide_empty: value } ); },
				} ),
				el( TextControl, {
					label: __('Parent Category ID'),
					value: attrs.parent,
					onChange: ( value ) => { setAttributes( { parent: value } ); },
				} ),
				el( TextControl, {
					label: __('Categories'),
					value: attrs.ids,
					onChange: ( value ) => { setAttributes( { ids: value } ); },
				} ),
				el( SelectControl, {
					label: __('Media Type'),
					value: attrs.media_type,
					options: [{ label: __('Image'), value: '' }, { label: __('Icon'), value: 'icon' }],
					onChange: ( value ) => { setAttributes( { media_type: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Display a featured product'),
					checked: attrs.show_featured,
					onChange: ( value ) => { props.setAttributes( { show_featured: value } ); },
				} ),
				el( ToggleControl, {
					label: __('Hide products count'),
					checked: attrs.hide_count,
					onChange: ( value ) => { props.setAttributes( { hide_count: value } ); },
				} ),
				el( SelectControl, {
					label: __('Hover Effect'),
					value: attrs.hover_effect,
					options: [{ label: __('Normal'), value: '' }, { label: __('Display product count on hover'), value: 'show-count-on-hover' }],
					onChange: ( value ) => { setAttributes( { hover_effect: value } ); },
				} ),
				'icon' != attrs.media_type && el( SelectControl, {
					label: __('Image Size'),
					value: attrs.image_size,
					options: porto_block_vars.image_sizes,
					onChange: ( value ) => { props.setAttributes( { image_size: value } ); },
				} ),
				'products-slider' == attrs.view && el( ToggleControl, {
					label: __('Show Slider Navigation'),
					checked: attrs.navigation,
					onChange: ( value ) => { setAttributes( { navigation: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
					label: __('Nav Position'),
					value: attrs.nav_pos,
					options: [{ label: __('Middle'), value: '' }, { label: __('Middle of Images'), value: 'nav-center-images-only' }, { label: __('Top'), value: 'show-nav-title' }, { label: __('Bottom'), value: 'nav-bottom' }],
					onChange: ( value ) => { setAttributes( { nav_pos: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
					label: __('Nav Inside?'),
					value: attrs.nav_pos2,
					options: [{ label: __('Default'), value: '' }, { label: __('Inside'), value: 'nav-pos-inside' }, { label: __('Outside'), value: 'nav-pos-outside' }],
					onChange: ( value ) => { setAttributes( { nav_pos2: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.navigation && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
					label: __('Nav Type'),
					value: attrs.nav_type,
					options: porto_block_vars.carousel_nav_types,
					onChange: ( value ) => { setAttributes( { nav_type: value } ); },
				} ),
				'products-slider' == attrs.view && el( ToggleControl, {
					label: __('Show Slider Pagination'),
					checked: attrs.pagination,
					onChange: ( value ) => { setAttributes( { pagination: value } ); },
				} ),
				'products-slider' == attrs.view && attrs.pagination && el( SelectControl, {
					label: __('Dots Position'),
					value: attrs.dots_pos,
					options: [{ label: __('Bottom'), value: '' }, { label: __('Top right'), value: 'show-dots-title-right' }],
					onChange: ( value ) => { setAttributes( { dots_pos: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Type'),
					value: attrs.animation_type,
					onChange: ( value ) => { setAttributes( { animation_type: value } ); },
				} ),
				el(
					'p',
					{},
					__('Please check this url to see animation types. '),
					el(
						'a',
						{ href: 'https://www.portotheme.com/wordpress/porto/shortcodes/animations/', target: '_blank' },
						'https://www.portotheme.com/wordpress/porto/shortcodes/animations/'
					),
				),
				el( TextControl, {
					label: __('Animation Delay'),
					value: attrs.animation_delay,
					onChange: ( value ) => { setAttributes( { animation_delay: value } ); },
				} ),
				el( TextControl, {
					label: __('Animation Duration'),
					value: attrs.animation_duration,
					onChange: ( value ) => { setAttributes( { animation_duration: value } ); },
				} ),
			);

			let classes = '';
			if ( 'grid' == attrs.view ) {
				classes += ' columns-' + attrs.columns + ' ' + attrs.view;
				classes += ' pcols-ls-2 pcols-xs-' + Math.min( 3, attrs.columns ) + ' pcols-lg-' + attrs.columns;
			} else if ( 'products-slider' == attrs.view ) {
				classes += ' products-slider owl-carousel pcols-lg-' + attrs.columns;
				if (attrs.navigation) {
					if (attrs.nav_pos) classes += ' ' + attrs.nav_pos;
					if ((!attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos) && attrs.nav_pos2) {
						classes += ' ' + attrs.nav_pos2;
					}
					if (attrs.nav_type) {
						classes += ' ' + attrs.nav_type;
					} else {
						classes += ' show-nav-middle';
					}
				}
				if (attrs.pagination && attrs.dots_pos) {
					classes += ' ' + attrs.dots_pos
				}
			} else if ( 'creative' == attrs.view ) {
				classes += ' grid-creative';
			} else {
				classes += ' ' + attrs.view;
			}
			if (attrs.className) {
				classes += ' ' + attrs.className;
			}

			let category_view = 'category-pos-' + attrs.text_position.split('-')[0] + ( attrs.text_position.split('-').length >= 2 ? ' category-text-' + attrs.text_position.split('-')[1] : '' ) + ( 'light' != attrs.text_color ? ' category-color-' + attrs.text_color : '' ),
				item_classes = porto_block_vars.creative_layouts[Number(attrs.grid_layout)];

			var renderControls = el(
				'div',
				{ className: 'porto-products' + ( attrs.hover_effect ? ' ' + attrs.hover_effect : '' ) },
				attrs.title && el(
					'h2',
					{ className: 'products-title section-title', dangerouslySetInnerHTML: { __html: attrs.title.replace(/<script.*?\/script>/g, '') } }
				),
				el(
					'ul',
					{ className: 'products products-container' + classes + ' ' + category_view },
					this.state.categoriesList.map(function (cat, index) {
						let image = null, item_class = '';
						if (cat.image && cat.image.catalog_src) {
							image = el('img', { src: cat.image.catalog_src });
						} else if (porto_swatches_params && porto_swatches_params.placeholder_src) {
							image = el('img', { src: porto_swatches_params.placeholder_src });
						}

						if ('creative' == attrs.view && typeof item_classes[index % item_classes.length] != 'undefined') {
							item_class += ' '  + item_classes[index % item_classes.length];
						}
						if ('icon' == attrs.media_type && cat.cat_icon) {
							item_class += ' cat-has-icon';
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
								('icon' != attrs.media_type || !cat.cat_icon) && el(
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
											el( 'mark', {}, Number(cat.count) ),
											' Products'
										)
									)
								)
							)
						);
					}),
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
			show_featured: { type: 'string' },
			hide_count: { type: 'string' },
			hover_effect: { type: 'string', default: '' },
			image_size: { type: 'string' },
			navigation: { type: 'boolean', default: true },
			nav_pos: { type: 'string', default: '' },
			nav_pos2: { type: 'string' },
			nav_type: { type: 'string' },
			pagination: { type: 'boolean', default: false },
			dots_pos: { type: 'string' },
			animation_type: { type: 'string' },
			animation_duration: { type: 'int' },
			animation_delay: { type: 'int' },
		},
		edit: PortoProductCategories,
		save: function() {
			return null;
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash, wp.apiFetch);

/**
 * 17. Porto Masonry Container
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
			this.initMasonry = this.initMasonry.bind(this);
		}

		initMasonry() {
			const props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				$wrap = jQuery('#block-' + clientId).find('.block-editor-block-list__layout').eq(0);
			if (!$wrap.children('.grid-col-sizer').length) {
				$wrap.append('<div class="grid-col-sizer"></div>');
			}
			$wrap.isotope({
				masonry: {'columnWidth': '.grid-col-sizer'}
			});
			if ('preset' == attrs.layout) {
				const item_classes = porto_block_vars.creative_layouts[Number(attrs.grid_layout)];
				$wrap.data('grid_layout', attrs.grid_layout);
				$wrap.children().each(function(index) {
					if (jQuery(this).hasClass('grid-col-sizer') || jQuery(this).hasClass('block-list-appender')) {
						return;
					}
					jQuery(this).css('width', '');
					jQuery(this).addClass(item_classes[index % item_classes.length]);
				});
				jQuery.ajax({
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
					success: function(res) {
						$wrap.prev('style').remove();
						jQuery(res).insertBefore($wrap);
						$wrap.isotope('layout');
						if (counterWillUpdate) {
							clearTimeout(counterWillUpdate);
						}
						counterWillUpdate = setTimeout(function() {
							$wrap.children().each(function(index) {
								if (jQuery(this).hasClass('grid-col-sizer') || jQuery(this).hasClass('block-list-appender')) {
									return;
								}
								jQuery(this).css('width', (jQuery(this).width() / jQuery(this).parent().width() * 100) + '%').css('height', jQuery(this).height());
							});
						}, 500);
					}
				});
			}
		}

		componentDidMount() {
			this.initMasonry();
		}

		componentDidUpdate(prevProps, prevState) {
			const props = this.props,
				attrs = props.attributes,
				clientId = props.clientId,
				$wrap = jQuery('#block-' + clientId).find('.block-editor-block-list__layout').eq(0);
			if ('preset' == attrs.layout && (attrs.layout != prevProps.attributes.layout || attrs.grid_layout != prevProps.attributes.grid_layout || attrs.grid_height != prevProps.attributes.grid_height )) {
				this.initMasonry();
			} else if ('preset' != attrs.layout) {
				$wrap.prev('style').remove();
				$wrap.children().each(function(index) {
					if (jQuery(this).hasClass('grid-col-sizer') || jQuery(this).hasClass('block-list-appender')) {
						return;
					}
					if (jQuery(this).data('width')) {
						jQuery(this).css('width', jQuery(this).data('width'));
					} else {
						jQuery(this).css('width', '');
					}
					jQuery(this).css('height', '');
				});
			}
			if ($wrap.data('isotope')) {
				$wrap.isotope('layout');
			}
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, {},
				el( SelectControl, {
					label: __('Layout'),
					value: attrs.layout,
					options: [{ label: __('Custom Masonry Layout'), value: '' }, { label: __('Predefined Grid Layout'), value: 'preset' }],
					onChange: ( value ) => { props.setAttributes( { layout: value } ); },
				} ),
				'preset' == attrs.layout && el( RangeControl, {
					label: __('Creative Grid Layout'),
					value: attrs.grid_layout,
					min: 1,
					max: 12,
					onChange: ( value ) => { props.setAttributes( { grid_layout: value } ); },
				} ),
				'preset' == attrs.layout && el( TextControl, {
					label: __('Grid Height'),
					value: attrs.grid_height,
					onChange: ( value ) => { props.setAttributes( { grid_height: value } ); },
				} ),
				el( TextControl, {
					label: __('Column Spacing'),
					value: attrs.gutter_size,
					onChange: ( value ) => { props.setAttributes( { gutter_size: value } ); },
				} )
			);

			var renderControls = el(
				'div',
				{className: 'porto-grid-container' + ( 'preset' == attrs.layout ? ' porto-preset-layout' : '' ) + ( attrs.className ? ' ' + attrs.className : '' ), style: {minHeight: '200px'}},
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
		save: function() {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);

/**
 * 18. Porto Masonry Item
 */
(function (wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash) {
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
				$this = jQuery('#block-' + clientId);

			$this.addClass('porto-grid-item');
			if (attrs.width) {
				let unit = attrs.width.replace( /[0-9.]/g, '' );
				if ( ! unit ) {
					attrs.width += 'px';
				}
				if (!$this.closest('.porto-preset-layout').length) {
					$this.css('width', attrs.width);
				}
				$this.data('width', attrs.width);
			}
			$this.css({float: 'left'});
			
			const $wrap = $this.closest('.block-editor-block-list__layout');
			if ($this.closest('.porto-preset-layout').length && $wrap.length && $wrap.data('isotope')) {
				const item_classes = porto_block_vars.creative_layouts[Number($wrap.data('grid_layout'))];
				$this.addClass(item_classes[$this.index() % item_classes.length]);
				$this.css('width', ($this.width() / $this.parent().width() * 100) + '%').css('height', $this.height());
			}
			if ($wrap.data('isotope')) {
				$wrap.isotope('destroy');
				$wrap.isotope({
					masonry: {'columnWidth': '.grid-col-sizer'}
				});
			}
		}

		componentWillUnmount() {
			const clientId = this.props.clientId,
				$this = jQuery('#block-' + clientId),
				$wrap = $this.closest('.block-editor-block-list__layout');
			if ($wrap.data('isotope')) {
				setTimeout(function() {
					$wrap.isotope('destroy');
					$wrap.isotope({
						masonry: {'columnWidth': '.grid-col-sizer'}
					});
				}, 200);
			}
		}

		componentDidUpdate( prevProps, prevState ) {
			const attrs = this.props.attributes,
				clientId = this.props.clientId,
				$this = jQuery('#block-' + clientId),
				$iso_obj = $this.closest('.porto-grid-container').find('.block-editor-block-list__layout').eq(0);
			if ( attrs.width != prevProps.attributes.width ) {
				if ($iso_obj.data('isotope')) {
					$iso_obj.isotope('layout');
				}
				if (attrs.width) {
					let unit = attrs.width.replace( /[0-9.]/g, '' );
					if ( ! unit ) {
						attrs.width += 'px';
					}
					$this.css('width', attrs.width);
				}
			}
			$this.addClass('porto-grid-item').css('float', 'left');
		}

		render() {
			var props = this.props,
				attrs = props.attributes;

			var inspectorControls = el( InspectorControls, {},
				el( TextControl, {
					label: __('Width'),
					value: attrs.width,
					onChange: ( value ) => { props.setAttributes( { width: value } ); },
				} )
			);

			let inline_style = {};
			var renderControls = el(
				'div',
				{className: ( attrs.className ? ' ' + attrs.className : '' ), style: inline_style},
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
		parent: ['porto/porto-grid-container'],
		attributes: {
			width: {
				type: 'string',
			},
			initMasonry: {
				type: 'function'
			}
		},
		edit: PortoMasonryItem,
		save: function() {
			return el( InnerBlocks.Content );
		}
	});
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash);
