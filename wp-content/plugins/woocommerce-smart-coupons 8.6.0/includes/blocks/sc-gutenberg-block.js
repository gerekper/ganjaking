/**
 * WooCommerce Smart Coupons Gutenberg
 *
 * @author      StoreApps
 * @since       4.0.0
 * @version     1.0
 *
 * @package woocommerce-smart-coupons/includes/blocks
 */

( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 *
	 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
	 */
	const {registerBlockType} = wp.blocks;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 *
	 * @see https://github.com/WordPress/gutenberg/tree/master/element#element
	 */
	const createElement = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 *
	 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
	 */
	const {__} = wp.i18n;

	/**
	 * Every block starts by registering a new block type definition.
	 *
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType(
		'woocommerce-smart-coupons/coupon',
		{
			/**
			 * This is the display title for our block, which can be translated with `i18n` functions.
			 * The block inserter will show this name.
			 */
			title: __( 'Smart Coupons', 'woocommerce-smart-coupons' ),

			/**
			 * This is the block description for our block, which can be translated with `il8n` functions.
			 */
			description: __( 'Show any WooCommerce coupon with Smart Coupons.' ),

			/**
			 * Blocks are grouped into categories to help users browse and discover them.
			 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
			 */
			category: 'embed',

			/**
			 * Define block icon for our block.
			 */
			icon: 'heart',

			/**
			 * This are the block keywords using which our block can be searched.
			 */
			keywords: [
			__( 'Smart' ),
			__( 'Coupons' ),
			__( 'Store Credit' ),
			],

			/**
			 * Optional block extended support features.
			 */
			supports: {
				// Removes support for an HTML mode.
				html: false,
				// align: [ 'wide', 'full' ],.
			},

			attributes: {
				coupon_code: {
					type: 'string',
				},
			},

			/**
			 * The edit function describes the structure of your block in the context of the editor.
			 * This represents what the editor will render when the block is used.
			 *
			 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
			 *
			 * @param {Object} [props] Properties passed from the editor.
			 * @return {Element}       Element to render.
			 */
			edit: function( props ){
				return createElement(
					wp.editor.RichText,
					{
						className: 'coupon-container gb-active-coupon',
						value: props.attributes.coupon_code,
						onChange: function( coupon_code ) {
							props.setAttributes( { coupon_code: coupon_code } );
						}
					}
				);
			},

			/**
			 * The save function defines the way in which the different attributes should be combined
			 * into the final markup, which is then serialized by Gutenberg into `post_content`.
			 *
			 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
			 *
			 * @return {Element}       Element to render.
			 */
			save:function( props ){
				return null;
			}
		}
	);
} )( window.wp );
