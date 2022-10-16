<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Vendor_Woocommerce
 *
 * @since 4.4
 * @todo move to separate file and dir.
 */
class Vc_Vendor_Woocommerce {
	protected static $product_fields_list = false;
	protected static $order_fields_list = false;

	/**
	 * @since 4.4
	 */
	public function load() {
		if ( class_exists( 'WooCommerce' ) ) {

			add_action( 'vc_after_mapping', array(
				$this,
				'mapShortcodes',
			) );

			add_action( 'vc_backend_editor_render', array(
				$this,
				'enqueueJsBackend',
			) );

			add_action( 'vc_frontend_editor_render', array(
				$this,
				'enqueueJsFrontend',
			) );
			add_filter( 'vc_grid_item_shortcodes', array(
				$this,
				'mapGridItemShortcodes',
			) );
			add_action( 'vc_vendor_yoastseo_filter_results', array(
				$this,
				'yoastSeoCompatibility',
			) );

			add_filter( 'woocommerce_product_tabs', array(
				$this,
				'addContentTabPageEditable',
			) );

			add_filter( 'woocommerce_shop_manager_editable_roles', array(
				$this,
				'addShopManagerRoleToEditable',
			) );
		}
	}

	/**
	 * @param $rules
	 * @return array
	 */
	public function addShopManagerRoleToEditable( $rules ) {
		$rules[] = 'shop_manager';

		return array_unique( $rules );
	}

	/**
	 * @param $tabs
	 * @return mixed
	 */
	public function addContentTabPageEditable( $tabs ) {
		if ( vc_is_page_editable() ) {
			// Description tab - shows product content
			$tabs['description'] = array(
				'title' => esc_html__( 'Description', 'woocommerce' ),
				'priority' => 10,
				'callback' => 'woocommerce_product_description_tab',
			);
		}

		return $tabs;
	}

	/**
	 * @since 4.4
	 */
	public function enqueueJsBackend() {
		wp_enqueue_script( 'vc_vendor_woocommerce_backend', vc_asset_url( 'js/vendors/woocommerce.js' ), array( 'vc-backend-min-js' ), '1.0', true );
	}

	/**
	 * @since 4.4
	 */
	public function enqueueJsFrontend() {
		wp_enqueue_script( 'vc_vendor_woocommerce_frontend', vc_asset_url( 'js/vendors/woocommerce.js' ), array( 'vc-frontend-editor-min-js' ), '1.0', true );
	}

	/**
	 * Add settings for shortcodes
	 *
	 * @param $tag
	 *
	 * @return array
	 * @since 4.9
	 *
	 */
	public function addShortcodeSettings( $tag ) {
		$args = array(
			'type' => 'post',
			'child_of' => 0,
			'parent' => '',
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'number' => '',
			'taxonomy' => 'product_cat',
			'pad_counts' => false,

		);
		$order_by_values = array(
			'',
			esc_html__( 'Date', 'js_composer' ) => 'date',
			esc_html__( 'ID', 'js_composer' ) => 'ID',
			esc_html__( 'Author', 'js_composer' ) => 'author',
			esc_html__( 'Title', 'js_composer' ) => 'title',
			esc_html__( 'Modified', 'js_composer' ) => 'modified',
			esc_html__( 'Random', 'js_composer' ) => 'rand',
			esc_html__( 'Comment count', 'js_composer' ) => 'comment_count',
			esc_html__( 'Menu order', 'js_composer' ) => 'menu_order',
			esc_html__( 'Menu order & title', 'js_composer' ) => 'menu_order title',
			esc_html__( 'Include', 'js_composer' ) => 'include',
			esc_html__( 'Custom post__in', 'js_composer' ) => 'post__in',
		);

		$order_way_values = array(
			'',
			esc_html__( 'Descending', 'js_composer' ) => 'DESC',
			esc_html__( 'Ascending', 'js_composer' ) => 'ASC',
		);
		$settings = array();
		switch ( $tag ) {
			case 'woocommerce_cart':
				$settings = array(
					'name' => esc_html__( 'Cart', 'js_composer' ),
					'base' => 'woocommerce_cart',
					'icon' => 'icon-wpb-woocommerce',
					'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					'description' => esc_html__( 'Displays the cart contents', 'js_composer' ),
					'show_settings_on_create' => false,
					'php_class_name' => 'Vc_WooCommerce_NotEditable',
				);
				break;
			case 'woocommerce_checkout':
				/**
				 * @shortcode woocommerce_checkout
				 * @description Used on the checkout page, the checkout shortcode displays the checkout process.
				 * @no_params
				 * @not_editable
				 */ $settings = array(
					 'name' => esc_html__( 'Checkout', 'js_composer' ),
					 'base' => 'woocommerce_checkout',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Displays the checkout', 'js_composer' ),
					 'show_settings_on_create' => false,
					 'php_class_name' => 'Vc_WooCommerce_NotEditable',
				 );
				break;
			case 'woocommerce_order_tracking':
				/**
				 * @shortcode woocommerce_order_tracking
				 * @description Lets a user see the status of an order by entering their order details.
				 * @no_params
				 * @not_editable
				 */ $settings = array(
					 'name' => esc_html__( 'Order Tracking Form', 'js_composer' ),
					 'base' => 'woocommerce_order_tracking',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Lets a user see the status of an order', 'js_composer' ),
					 'show_settings_on_create' => false,
					 'php_class_name' => 'Vc_WooCommerce_NotEditable',
				 );
				break;
			case 'woocommerce_my_account':
				/**
				 * @shortcode woocommerce_my_account
				 * @description Shows the ‘my account’ section where the customer can view past orders and update their information.
				 * You can specify the number or order to show, it’s set by default to 15 (use -1 to display all orders.)
				 *
				 * @param order_count integer
				 * Current user argument is automatically set using get_user_by( ‘id’, get_current_user_id() ).
				 */ $settings = array(
					 'name' => esc_html__( 'My Account', 'js_composer' ),
					 'base' => 'woocommerce_my_account',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Shows the "my account" section', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Order count', 'js_composer' ),
							 'value' => 15,
							 'save_always' => true,
							 'param_name' => 'order_count',
							 'description' => esc_html__( 'You can specify the number or order to show, it\'s set by default to 15 (use -1 to display all orders.)', 'js_composer' ),
						 ),
					 ),
				 );
				break;
			case 'recent_products':
				/**
				 * @shortcode recent_products
				 * @description Lists recent products – useful on the homepage. The ‘per_page’ shortcode determines how many products
				 * to show on the page and the columns attribute controls how many columns wide the products should be before wrapping.
				 * To learn more about the default ‘orderby’ parameters please reference the WordPress Codex: http://codex.wordpress.org/Class_Reference/WP_Query
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 */ $settings = array(
					 'name' => esc_html__( 'Recent products', 'js_composer' ),
					 'base' => 'recent_products',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Lists recent products', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Per page', 'js_composer' ),
							 'value' => 12,
							 'save_always' => true,
							 'param_name' => 'per_page',
							 'description' => esc_html__( 'The "per_page" shortcode determines how many products to show on the page', 'js_composer' ),
						 ),
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Columns', 'js_composer' ),
							 'value' => 4,
							 'param_name' => 'columns',
							 'save_always' => true,
							 'description' => esc_html__( 'The columns attribute controls how many columns wide the products should be before wrapping.', 'js_composer' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Order by', 'js_composer' ),
							 'param_name' => 'orderby',
							 'value' => $order_by_values,
							 'std' => 'date',
							 // default WC value for recent_products
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Sort order', 'js_composer' ),
							 'param_name' => 'order',
							 'value' => $order_way_values,
							 'std' => 'DESC',
							 // default WC value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
					 ),
				 );
				break;
			case 'featured_products':
				/**
				 * @shortcode featured_products
				 * @description Works exactly the same as recent products but displays products which have been set as “featured”.
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 */ $settings = array(
					 'name' => esc_html__( 'Featured products', 'js_composer' ),
					 'base' => 'featured_products',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Display products set as "featured"', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Per page', 'js_composer' ),
							 'value' => 12,
							 'param_name' => 'per_page',
							 'save_always' => true,
							 'description' => esc_html__( 'The "per_page" shortcode determines how many products to show on the page', 'js_composer' ),
						 ),
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Columns', 'js_composer' ),
							 'value' => 4,
							 'param_name' => 'columns',
							 'save_always' => true,
							 'description' => esc_html__( 'The columns attribute controls how many columns wide the products should be before wrapping.', 'js_composer' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Order by', 'js_composer' ),
							 'param_name' => 'orderby',
							 'value' => $order_by_values,
							 'std' => 'date',
							 // default WC value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Sort order', 'js_composer' ),
							 'param_name' => 'order',
							 'value' => $order_way_values,
							 'std' => 'DESC',
							 // default WC value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="s://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
					 ),
				 );
				break;
			case 'product':
				/**
				 * @shortcode product
				 * @description Show a single product by ID or SKU.
				 *
				 * @param id integer
				 * @param sku string
				 * If the product isn’t showing, make sure it isn’t set to Hidden in the Catalog Visibility.
				 * To find the Product ID, go to the Product > Edit screen and look in the URL for the postid= .
				 */ $settings = array(
					 'name' => esc_html__( 'Product', 'js_composer' ),
					 'base' => 'product',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Show a single product by ID or SKU', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'autocomplete',
							 'heading' => esc_html__( 'Select identificator', 'js_composer' ),
							 'param_name' => 'id',
							 'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'js_composer' ),
						 ),
						 array(
							 'type' => 'hidden',
							 // This will not show on render, but will be used when defining value for autocomplete
							 'param_name' => 'sku',
						 ),
					 ),
				 );
				break;
			case 'products':
				$settings = array(
					'name' => esc_html__( 'Products', 'js_composer' ),
					'base' => 'products',
					'icon' => 'icon-wpb-woocommerce',
					'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					'description' => esc_html__( 'Show multiple products by ID or SKU.', 'js_composer' ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Columns', 'js_composer' ),
							'value' => 4,
							'param_name' => 'columns',
							'save_always' => true,
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order by', 'js_composer' ),
							'param_name' => 'orderby',
							'value' => $order_by_values,
							'std' => 'title',
							// Default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s. Default by Title', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Sort order', 'js_composer' ),
							'param_name' => 'order',
							'value' => $order_way_values,
							'std' => 'ASC',
							// default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s. Default by ASC', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'autocomplete',
							'heading' => esc_html__( 'Products', 'js_composer' ),
							'param_name' => 'ids',
							'settings' => array(
								'multiple' => true,
								'sortable' => true,
								'unique_values' => true,
								// In UI show results except selected. NB! You should manually check values in backend
							),
							'save_always' => true,
							'description' => esc_html__( 'Enter List of Products', 'js_composer' ),
						),
						array(
							'type' => 'hidden',
							'param_name' => 'skus',
						),
					),
				);
				break;
			case 'add_to_cart':
				/**
				 * @shortcode add_to_cart
				 * @description Show the price and add to cart button of a single product by ID (or SKU).
				 *
				 * @param id integer
				 * @param sku string
				 * @param style string
				 * If the product isn’t showing, make sure it isn’t set to Hidden in the Catalog Visibility.
				 */ $settings = array(
					 'name' => esc_html__( 'Add to cart', 'js_composer' ),
					 'base' => 'add_to_cart',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Show multiple products by ID or SKU', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'autocomplete',
							 'heading' => esc_html__( 'Select identificator', 'js_composer' ),
							 'param_name' => 'id',
							 'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'js_composer' ),
						 ),
						 array(
							 'type' => 'hidden',
							 'param_name' => 'sku',
						 ),
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Wrapper inline style', 'js_composer' ),
							 'param_name' => 'style',
						 ),
					 ),
				 );
				break;
			case 'add_to_cart_url':
				/**
				 * @shortcode add_to_cart_url
				 * @description Print the URL on the add to cart button of a single product by ID.
				 *
				 * @param id integer
				 * @param sku string
				 */ $settings = array(
					 'name' => esc_html__( 'Add to cart URL', 'js_composer' ),
					 'base' => 'add_to_cart_url',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Show URL on the add to cart button', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'autocomplete',
							 'heading' => esc_html__( 'Select identificator', 'js_composer' ),
							 'param_name' => 'id',
							 'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'js_composer' ),
						 ),
						 array(
							 'type' => 'hidden',
							 'param_name' => 'sku',
						 ),
					 ),
				 );
				break;
			case 'product_page':
				/**
				 * @shortcode product_page
				 * @description Show a full single product page by ID or SKU.
				 *
				 * @param id integer
				 * @param sku string
				 */ $settings = array(
					 'name' => esc_html__( 'Product page', 'js_composer' ),
					 'base' => 'product_page',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'Show single product by ID or SKU', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'autocomplete',
							 'heading' => esc_html__( 'Select identificator', 'js_composer' ),
							 'param_name' => 'id',
							 'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'js_composer' ),
						 ),
						 array(
							 'type' => 'hidden',
							 'param_name' => 'sku',
						 ),
					 ),
				 );
				break;
			case 'product_category':
				/**
				 * @shortcode product_category
				 * @description Show multiple products in a category by slug.
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 * @param category string
				 * Go to: WooCommerce > Products > Categories to find the slug column.
				 */ // All this move to product
				$categories = get_categories( $args );

				$product_categories_dropdown = array();
				$this->getCategoryChildsFull( 0, $categories, 0, $product_categories_dropdown );
				$settings = array(
					'name' => esc_html__( 'Product category', 'js_composer' ),
					'base' => 'product_category',
					'icon' => 'icon-wpb-woocommerce',
					'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					'description' => esc_html__( 'Show multiple products in a category', 'js_composer' ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Limit', 'js_composer' ),
							'value' => 12,
							'save_always' => true,
							'param_name' => 'per_page',
							'description' => esc_html__( 'How much items to show', 'js_composer' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Columns', 'js_composer' ),
							'value' => 4,
							'save_always' => true,
							'param_name' => 'columns',
							'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order by', 'js_composer' ),
							'param_name' => 'orderby',
							'value' => $order_by_values,
							'std' => 'menu_order title',
							// Default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="s://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Sort order', 'js_composer' ),
							'param_name' => 'order',
							'value' => $order_way_values,
							'std' => 'ASC',
							// default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Category', 'js_composer' ),
							'value' => $product_categories_dropdown,
							'param_name' => 'category',
							'save_always' => true,
							'description' => esc_html__( 'Product category list', 'js_composer' ),
						),
					),
				);
				break;
			case 'product_categories':
				$settings = array(
					'name' => esc_html__( 'Product categories', 'js_composer' ),
					'base' => 'product_categories',
					'icon' => 'icon-wpb-woocommerce',
					'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					'description' => esc_html__( 'Display product categories loop', 'js_composer' ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Number', 'js_composer' ),
							'param_name' => 'number',
							'description' => esc_html__( 'The `number` field is used to display the number of products.', 'js_composer' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order by', 'js_composer' ),
							'param_name' => 'orderby',
							'value' => $order_by_values,
							'std' => 'name',
							// default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Sort order', 'js_composer' ),
							'param_name' => 'order',
							'value' => $order_way_values,
							'std' => 'ASC',
							// default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Columns', 'js_composer' ),
							'value' => 4,
							'param_name' => 'columns',
							'save_always' => true,
							'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Number', 'js_composer' ),
							'param_name' => 'hide_empty',
							'description' => esc_html__( 'Hide empty', 'js_composer' ),
						),
						array(
							'type' => 'autocomplete',
							'heading' => esc_html__( 'Categories', 'js_composer' ),
							'param_name' => 'ids',
							'settings' => array(
								'multiple' => true,
								'sortable' => true,
							),
							'save_always' => true,
							'description' => esc_html__( 'List of product categories', 'js_composer' ),
						),
					),
				);
				break;
			case 'sale_products':
				/**
				 * @shortcode sale_products
				 * @description List all products on sale.
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 */ $settings = array(
					 'name' => esc_html__( 'Sale products', 'js_composer' ),
					 'base' => 'sale_products',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'List all products on sale', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Limit', 'js_composer' ),
							 'value' => 12,
							 'save_always' => true,
							 'param_name' => 'per_page',
							 'description' => esc_html__( 'How much items to show', 'js_composer' ),
						 ),
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Columns', 'js_composer' ),
							 'value' => 4,
							 'save_always' => true,
							 'param_name' => 'columns',
							 'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Order by', 'js_composer' ),
							 'param_name' => 'orderby',
							 'value' => $order_by_values,
							 'std' => 'title',
							 // default WC value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Sort order', 'js_composer' ),
							 'param_name' => 'order',
							 'value' => $order_way_values,
							 'std' => 'ASC',
							 // default WC value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
					 ),
				 );
				break;
			case 'best_selling_products':
				/**
				 * @shortcode best_selling_products
				 * @description List best selling products on sale.
				 *
				 * @param per_page integer
				 * @param columns integer
				 */ $settings = array(
					 'name' => esc_html__( 'Best Selling Products', 'js_composer' ),
					 'base' => 'best_selling_products',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'List best selling products on sale', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Limit', 'js_composer' ),
							 'value' => 12,
							 'param_name' => 'per_page',
							 'save_always' => true,
							 'description' => esc_html__( 'How much items to show', 'js_composer' ),
						 ),
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Columns', 'js_composer' ),
							 'value' => 4,
							 'param_name' => 'columns',
							 'save_always' => true,
							 'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						 ),
					 ),
				 );
				break;
			case 'top_rated_products':
				/**
				 * @shortcode top_rated_products
				 * @description List top rated products on sale.
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 */ $settings = array(
					 'name' => esc_html__( 'Top Rated Products', 'js_composer' ),
					 'base' => 'top_rated_products',
					 'icon' => 'icon-wpb-woocommerce',
					 'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					 'description' => esc_html__( 'List all products on sale', 'js_composer' ),
					 'params' => array(
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Limit', 'js_composer' ),
							 'value' => 12,
							 'param_name' => 'per_page',
							 'save_always' => true,
							 'description' => esc_html__( 'How much items to show', 'js_composer' ),
						 ),
						 array(
							 'type' => 'textfield',
							 'heading' => esc_html__( 'Columns', 'js_composer' ),
							 'value' => 4,
							 'param_name' => 'columns',
							 'save_always' => true,
							 'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Order by', 'js_composer' ),
							 'param_name' => 'orderby',
							 'value' => $order_by_values,
							 'std' => 'title',
							 // default WC value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
						 array(
							 'type' => 'dropdown',
							 'heading' => esc_html__( 'Sort order', 'js_composer' ),
							 'param_name' => 'order',
							 'value' => $order_way_values,
							 'std' => 'ASC',
							 // Default WP Value
							 'save_always' => true,
							 'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						 ),
					 ),
				 );
				break;
			case 'product_attribute':
				/**
				 * @shortcode product_attribute
				 * @description List products with an attribute shortcode.
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 * @param attribute string
				 * @param filter string
				 */ $attributes_tax = wc_get_attribute_taxonomies();
				$attributes = array();
				foreach ( $attributes_tax as $attribute ) {
					$attributes[ $attribute->attribute_label ] = $attribute->attribute_name;
				}
				$settings = array(
					'name' => esc_html__( 'Product Attribute', 'js_composer' ),
					'base' => 'product_attribute',
					'icon' => 'icon-wpb-woocommerce',
					'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					'description' => esc_html__( 'List products with an attribute shortcode', 'js_composer' ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Limit', 'js_composer' ),
							'value' => 12,
							'param_name' => 'per_page',
							'save_always' => true,
							'description' => esc_html__( 'How much items to show', 'js_composer' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Columns', 'js_composer' ),
							'value' => 4,
							'param_name' => 'columns',
							'save_always' => true,
							'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order by', 'js_composer' ),
							'param_name' => 'orderby',
							'value' => $order_by_values,
							'std' => 'title',
							// default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Sort order', 'js_composer' ),
							'param_name' => 'order',
							'value' => $order_way_values,
							'std' => 'ASC',
							// Default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Attribute', 'js_composer' ),
							'param_name' => 'attribute',
							'value' => $attributes,
							'save_always' => true,
							'description' => esc_html__( 'List of product taxonomy attribute', 'js_composer' ),
						),
						array(
							'type' => 'checkbox',
							'heading' => esc_html__( 'Filter', 'js_composer' ),
							'param_name' => 'filter',
							'value' => array( 'empty' => 'empty' ),
							'save_always' => true,
							'description' => esc_html__( 'Taxonomy values', 'js_composer' ),
							'dependency' => array(
								'callback' => 'vcWoocommerceProductAttributeFilterDependencyCallback',
							),
						),
					),
				);
				break;
			case 'related_products':
				/**
				 * @shortcode related_products
				 * @description List related products.
				 *
				 * @param per_page integer
				 * @param columns integer
				 * @param orderby array
				 * @param order array
				 */ /* we need to detect post type to show this shortcode */ global $post, $typenow, $current_screen;
				$post_type = '';

				if ( $post && $post->post_type ) {
					//we have a post so we can just get the post type from that
					$post_type = $post->post_type;
				} elseif ( $typenow ) {
					//check the global $typenow - set in admin.php
					$post_type = $typenow;
				} elseif ( $current_screen && $current_screen->post_type ) {
					//check the global $current_screen object - set in sceen.php
					$post_type = $current_screen->post_type;

				} elseif ( isset( $_REQUEST['post_type'] ) ) {
					//lastly check the post_type querystring
					$post_type = sanitize_key( $_REQUEST['post_type'] );
					//we do not know the post type!
				}

				$settings = array(
					'name' => esc_html__( 'Related Products', 'js_composer' ),
					'base' => 'related_products',
					'icon' => 'icon-wpb-woocommerce',
					'content_element' => 'product' === $post_type,
					// disable showing if not product type
					'category' => esc_html__( 'WooCommerce', 'js_composer' ),
					'description' => esc_html__( 'List related products', 'js_composer' ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Per page', 'js_composer' ),
							'value' => 12,
							'save_always' => true,
							'param_name' => 'per_page',
							'description' => esc_html__( 'Please note: the "per_page" shortcode argument will determine how many products are shown on a page. This will not add pagination to the shortcode. ', 'js_composer' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Columns', 'js_composer' ),
							'value' => 4,
							'save_always' => true,
							'param_name' => 'columns',
							'description' => esc_html__( 'How much columns grid', 'js_composer' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Order by', 'js_composer' ),
							'param_name' => 'orderby',
							'value' => $order_by_values,
							'std' => 'rand',
							// default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => esc_html__( 'Sort order', 'js_composer' ),
							'param_name' => 'order',
							'value' => $order_way_values,
							'std' => 'DESC',
							// Default WC value
							'save_always' => true,
							'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
					),
				);
				break;
		}

		return $settings;
	}

	/**
	 * Add woocommerce shortcodes and hooks/filters for it.
	 * @since 4.4
	 */
	public function mapShortcodes() {
		add_action( 'wp_ajax_vc_woocommerce_get_attribute_terms', array(
			$this,
			'getAttributeTermsAjax',
		) );
		$tags = array(
			'woocommerce_cart',
			'woocommerce_checkout',
			'woocommerce_order_tracking',
			'woocommerce_my_account',
			'recent_products',
			'featured_products',
			'product',
			'products',
			'add_to_cart',
			'add_to_cart_url',
			'product_page',
			'product_category',
			'product_categories',
			'sale_products',
			'best_selling_products',
			'top_rated_products',
			'product_attribute',
			'related_products',
		);
		while ( $tag = current( $tags ) ) {
			vc_lean_map( $tag, array(
				$this,
				'addShortcodeSettings',
			) );
			next( $tags );
		}

		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_product_id_callback', array(
			$this,
			'productIdAutocompleteSuggester',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_product_id_render', array(
			$this,
			'productIdAutocompleteRender',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		//For param: ID default value filter
		add_filter( 'vc_form_fields_render_field_product_id_param_value', array(
			$this,
			'productIdDefaultValue',
		), 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_products_ids_callback', array(
			$this,
			'productIdAutocompleteSuggester',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_products_ids_render', array(
			$this,
			'productIdAutocompleteRender',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		//For param: ID default value filter
		add_filter( 'vc_form_fields_render_field_products_ids_param_value', array(
			$this,
			'productsIdsDefaultValue',
		), 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

		//Filters For autocomplete param: Exactly Same as "product" shortcode
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_add_to_cart_id_callback', array(
			$this,
			'productIdAutocompleteSuggester',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_add_to_cart_id_render', array(
			$this,
			'productIdAutocompleteRender',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		//For param: ID default value filter
		add_filter( 'vc_form_fields_render_field_add_to_cart_id_param_value', array(
			$this,
			'productIdDefaultValue',
		), 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

		//Filters For autocomplete param: Exactly Same as "product" shortcode
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_add_to_cart_url_id_callback', array(
			$this,
			'productIdAutocompleteSuggester',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_add_to_cart_url_id_render', array(
			$this,
			'productIdAutocompleteRender',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		//For param: ID default value filter
		add_filter( 'vc_form_fields_render_field_add_to_cart_url_id_param_value', array(
			$this,
			'productIdDefaultValue',
		), 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

		//Filters For autocomplete param: Exactly Same as "product" shortcode
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_product_page_id_callback', array(
			$this,
			'productIdAutocompleteSuggester',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_product_page_id_render', array(
			$this,
			'productIdAutocompleteRender',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		//For param: ID default value filter
		add_filter( 'vc_form_fields_render_field_product_page_id_param_value', array(
			$this,
			'productIdDefaultValue',
		), 10, 4 ); // Defines default value for param if not provided. Takes from other param value.

		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_product_category_category_callback', array(
			$this,
			'productCategoryCategoryAutocompleteSuggesterBySlug',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_product_category_category_render', array(
			$this,
			'productCategoryCategoryRenderBySlugExact',
		), 10, 1 ); // Render exact category by Slug. Must return an array (label,value)

		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_product_categories_ids_callback', array(
			$this,
			'productCategoryCategoryAutocompleteSuggester',
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_product_categories_ids_render', array(
			$this,
			'productCategoryCategoryRenderByIdExact',
		), 10, 1 ); // Render exact category by id. Must return an array (label,value)

		//For param: "filter" param value
		//vc_form_fields_render_field_{shortcode_name}_{param_name}_param
		add_filter( 'vc_form_fields_render_field_product_attribute_filter_param', array(
			$this,
			'productAttributeFilterParamValue',
		), 10, 4 ); // Defines default value for param if not provided. Takes from other param value.
	}

	/**
	 * @param array $shortcodes
	 * @return array|mixed
	 */
	/**
	 * @param array $shortcodes
	 * @return array|mixed
	 */
	public function mapGridItemShortcodes( array $shortcodes ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/woocommerce/class-vc-gitem-woocommerce-shortcode.php' );
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/woocommerce/grid-item-attributes.php' );
		$wc_shortcodes = include vc_path_dir( 'VENDORS_DIR', 'plugins/woocommerce/grid-item-shortcodes.php' );

		return $shortcodes + $wc_shortcodes;
	}

	/**
	 * Defines default value for param if not provided. Takes from other param value.
	 * @param array $param_settings
	 * @param $current_value
	 * @param $map_settings
	 * @param $atts
	 *
	 * @return array
	 * @since 4.4
	 *
	 */
	public function productAttributeFilterParamValue( $param_settings, $current_value, $map_settings, $atts ) {
		if ( isset( $atts['attribute'] ) ) {
			$value = $this->getAttributeTerms( $atts['attribute'] );
			if ( is_array( $value ) && ! empty( $value ) ) {
				$param_settings['value'] = $value;
			}
		}

		return $param_settings;
	}

	/**
	 * Get attribute terms hooks from ajax request
	 * @since 4.4
	 */
	public function getAttributeTermsAjax() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

		$attribute = vc_post_param( 'attribute' );
		$values = $this->getAttributeTerms( $attribute );
		$param = array(
			'param_name' => 'filter',
			'type' => 'checkbox',
		);
		$param_line = '';
		foreach ( $values as $label => $v ) {
			$param_line .= ' <label class="vc_checkbox-label"><input id="' . $param['param_name'] . '-' . $v . '" value="' . $v . '" class="wpb_vc_param_value ' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . '> ' . $label . '</label>';
		}
		die( wp_json_encode( $param_line ) );
	}

	/**
	 * Get attribute terms suggester
	 * @param $attribute
	 *
	 * @return array
	 * @since 4.4
	 *
	 */
	public function getAttributeTerms( $attribute ) {
		$terms = get_terms( 'pa_' . $attribute ); // return array. take slug
		$data = array();
		if ( ! empty( $terms ) && empty( $terms->errors ) ) {
			foreach ( $terms as $term ) {
				$data[ $term->name ] = $term->slug;
			}
		}

		return $data;
	}

	/**
	 * Get lists of categories.
	 * @param $parent_id
	 * @param array $array
	 * @param $level
	 * @param array $dropdown - passed by  reference
	 * @return array
	 * @since 4.5.3
	 *
	 */
	protected function getCategoryChildsFull( $parent_id, $array, $level, &$dropdown ) {
		$keys = array_keys( $array );
		$i = 0;
		while ( $i < count( $array ) ) {
			$key = $keys[ $i ];
			$item = $array[ $key ];
			$i ++;
			if ( $item->category_parent == $parent_id ) {
				$name = str_repeat( '- ', $level ) . $item->name;
				$value = $item->slug;
				$dropdown[] = array(
					'label' => $name . '(' . $item->term_id . ')',
					'value' => $value,
				);
				unset( $array[ $key ] );
				$array = $this->getCategoryChildsFull( $item->term_id, $array, $level + 1, $dropdown );
				$keys = array_keys( $array );
				$i = 0;
			}
		}

		return $array;
	}

	/**
	 * Replace single product sku to id.
	 * @param $current_value
	 * @param $param_settings
	 * @param $map_settings
	 * @param $atts
	 *
	 * @return bool|string
	 * @since 4.4
	 *
	 */
	public function productIdDefaultValue( $current_value, $param_settings, $map_settings, $atts ) {
		$value = trim( $current_value );
		if ( strlen( trim( $current_value ) ) === 0 && isset( $atts['sku'] ) && strlen( $atts['sku'] ) > 0 ) {
			$value = $this->productIdDefaultValueFromSkuToId( $atts['sku'] );
		}

		return $value;
	}

	/**
	 * Replaces product skus to id's.
	 * @param $current_value
	 * @param $param_settings
	 * @param $map_settings
	 * @param $atts
	 *
	 * @return string
	 * @since 4.4
	 *
	 */
	public function productsIdsDefaultValue( $current_value, $param_settings, $map_settings, $atts ) {
		$value = trim( $current_value );
		if ( strlen( trim( $value ) ) === 0 && isset( $atts['skus'] ) && strlen( $atts['skus'] ) > 0 ) {
			$data = array();
			$skus = $atts['skus'];
			$skus_array = explode( ',', $skus );
			foreach ( $skus_array as $sku ) {
				$id = $this->productIdDefaultValueFromSkuToId( trim( $sku ) );
				if ( is_numeric( $id ) ) {
					$data[] = $id;
				}
			}
			if ( ! empty( $data ) ) {
				$values = explode( ',', $value );
				$values = array_merge( $values, $data );
				$value = implode( ',', $values );
			}
		}

		return $value;
	}

	/**
	 * Suggester for autocomplete by id/name/title/sku
	 * @param $query
	 *
	 * @return array - id's from products with title/sku.
	 * @since 4.4
	 *
	 */
	public function productIdAutocompleteSuggester( $query ) {
		global $wpdb;
		$product_id = (int) $query;
		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.ID AS id, a.post_title AS title, b.meta_value AS sku
					FROM {$wpdb->posts} AS a
					LEFT JOIN ( SELECT meta_value, post_id  FROM {$wpdb->postmeta} WHERE `meta_key` = '_sku' ) AS b ON b.post_id = a.ID
					WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.meta_value LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", $product_id > 0 ? $product_id : - 1, stripslashes( $query ), stripslashes( $query ) ), ARRAY_A );

		$results = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$data['label'] = esc_html__( 'Id', 'js_composer' ) . ': ' . $value['id'] . ( ( strlen( $value['title'] ) > 0 ) ? ' - ' . esc_html__( 'Title', 'js_composer' ) . ': ' . $value['title'] : '' ) . ( ( strlen( $value['sku'] ) > 0 ) ? ' - ' . esc_html__( 'Sku', 'js_composer' ) . ': ' . $value['sku'] : '' );
				$results[] = $data;
			}
		}

		return $results;
	}

	/**
	 * Find product by id
	 * @param $query
	 *
	 * @return bool|array
	 * @since 4.4
	 *
	 */
	public function productIdAutocompleteRender( $query ) {
		$query = trim( $query['value'] ); // get value from requested
		if ( ! empty( $query ) ) {
			// get product
			$product_object = wc_get_product( (int) $query );
			if ( is_object( $product_object ) ) {
				$product_sku = $product_object->get_sku();
				$product_title = $product_object->get_title();
				$product_id = $product_object->get_id();

				$product_sku_display = '';
				if ( ! empty( $product_sku ) ) {
					$product_sku_display = ' - ' . esc_html__( 'Sku', 'js_composer' ) . ': ' . $product_sku;
				}

				$product_title_display = '';
				if ( ! empty( $product_title ) ) {
					$product_title_display = ' - ' . esc_html__( 'Title', 'js_composer' ) . ': ' . $product_title;
				}

				$product_id_display = esc_html__( 'Id', 'js_composer' ) . ': ' . $product_id;

				$data = array();
				$data['value'] = $product_id;
				$data['label'] = $product_id_display . $product_title_display . $product_sku_display;

				return ! empty( $data ) ? $data : false;
			}

			return false;
		}

		return false;
	}

	/**
	 * Return ID of product by provided SKU of product.
	 * @param $query
	 *
	 * @return bool
	 * @since 4.4
	 *
	 */
	public function productIdDefaultValueFromSkuToId( $query ) {
		$result = $this->productIdAutocompleteSuggesterExactSku( $query );

		return isset( $result['value'] ) ? $result['value'] : false;
	}

	/**
	 * Find product by SKU
	 * @param $query
	 *
	 * @return bool|array
	 * @since 4.4
	 *
	 */
	public function productIdAutocompleteSuggesterExactSku( $query ) {
		global $wpdb;
		$query = trim( $query );
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", stripslashes( $query ) ) );
		$product_data = get_post( $product_id );
		if ( 'product' !== $product_data->post_type ) {
			return '';
		}

		$product_object = wc_get_product( $product_data );
		if ( is_object( $product_object ) ) {

			$product_sku = $product_object->get_sku();
			$product_title = $product_object->get_title();
			$product_id = $product_object->get_id();

			$product_sku_display = '';
			if ( ! empty( $product_sku ) ) {
				$product_sku_display = ' - ' . esc_html__( 'Sku', 'js_composer' ) . ': ' . $product_sku;
			}

			$product_title_display = '';
			if ( ! empty( $product_title ) ) {
				$product_title_display = ' - ' . esc_html__( 'Title', 'js_composer' ) . ': ' . $product_title;
			}

			$product_id_display = esc_html__( 'Id', 'js_composer' ) . ': ' . $product_id;

			$data = array();
			$data['value'] = $product_id;
			$data['label'] = $product_id_display . $product_title_display . $product_sku_display;

			return ! empty( $data ) ? $data : false;
		}

		return false;
	}

	/**
	 * Autocomplete suggester to search product category by name/slug or id.
	 * @param $query
	 * @param bool $slug - determines what output is needed
	 *      default false - return id of product category
	 *      true - return slug of product category
	 *
	 * @return array
	 * @since 4.4
	 *
	 */
	public function productCategoryCategoryAutocompleteSuggester( $query, $slug = false ) {
		global $wpdb;
		$cat_id = (int) $query;
		$query = trim( $query );
		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.term_id AS id, b.name as name, b.slug AS slug
						FROM {$wpdb->term_taxonomy} AS a
						INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
						WHERE a.taxonomy = 'product_cat' AND (a.term_id = '%d' OR b.slug LIKE '%%%s%%' OR b.name LIKE '%%%s%%' )", $cat_id > 0 ? $cat_id : - 1, stripslashes( $query ), stripslashes( $query ) ), ARRAY_A );

		$result = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data = array();
				$data['value'] = $slug ? $value['slug'] : $value['id'];
				$data['label'] = esc_html__( 'Id', 'js_composer' ) . ': ' . $value['id'] . ( ( strlen( $value['name'] ) > 0 ) ? ' - ' . esc_html__( 'Name', 'js_composer' ) . ': ' . $value['name'] : '' ) . ( ( strlen( $value['slug'] ) > 0 ) ? ' - ' . esc_html__( 'Slug', 'js_composer' ) . ': ' . $value['slug'] : '' );
				$result[] = $data;
			}
		}

		return $result;
	}

	/**
	 * Search product category by id
	 * @param $query
	 *
	 * @return bool|array
	 * @since 4.4
	 *
	 */
	public function productCategoryCategoryRenderByIdExact( $query ) {
		$query = $query['value'];
		$cat_id = (int) $query;
		$term = get_term( $cat_id, 'product_cat' );

		return $this->productCategoryTermOutput( $term );
	}

	/**
	 * Suggester for autocomplete to find product category by id/name/slug but return found product category SLUG
	 * @param $query
	 *
	 * @return array - slug of products categories.
	 * @since 4.4
	 *
	 */
	public function productCategoryCategoryAutocompleteSuggesterBySlug( $query ) {
		$result = $this->productCategoryCategoryAutocompleteSuggester( $query, true );

		return $result;
	}

	/**
	 * Search product category by slug.
	 * @param $query
	 *
	 * @return bool|array
	 * @since 4.4
	 *
	 */
	public function productCategoryCategoryRenderBySlugExact( $query ) {
		$query = $query['value'];
		$query = trim( $query );
		$term = get_term_by( 'slug', $query, 'product_cat' );

		return $this->productCategoryTermOutput( $term );
	}

	/**
	 * Return product category value|label array
	 *
	 * @param $term
	 *
	 * @return array|bool
	 * @since 4.4
	 */
	protected function productCategoryTermOutput( $term ) {
		$term_slug = $term->slug;
		$term_title = $term->name;
		$term_id = $term->term_id;

		$term_slug_display = '';
		if ( ! empty( $term_slug ) ) {
			$term_slug_display = ' - ' . esc_html__( 'Sku', 'js_composer' ) . ': ' . $term_slug;
		}

		$term_title_display = '';
		if ( ! empty( $term_title ) ) {
			$term_title_display = ' - ' . esc_html__( 'Title', 'js_composer' ) . ': ' . $term_title;
		}

		$term_id_display = esc_html__( 'Id', 'js_composer' ) . ': ' . $term_id;

		$data = array();
		$data['value'] = $term_id;
		$data['label'] = $term_id_display . $term_title_display . $term_slug_display;

		return ! empty( $data ) ? $data : false;
	}

	/**
	 * @return array
	 */
	/**
	 * @return array
	 */
	public static function getProductsFieldsList() {
		return array(
			esc_html__( 'SKU', 'woocommerce' ) => 'sku',
			esc_html__( 'ID', 'woocommerce' ) => 'id',
			esc_html__( 'Price', 'woocommerce' ) => 'price',
			esc_html__( 'Regular price', 'woocommerce' ) => 'regular_price',
			esc_html__( 'Sale price', 'woocommerce' ) => 'sale_price',
			esc_html__( 'Price html', 'woocommerce' ) => 'price_html',
			esc_html__( 'Reviews count', 'woocommerce' ) => 'reviews_count',
			esc_html__( 'Short description', 'woocommerce' ) => 'short_description',
			esc_html__( 'Dimensions', 'woocommerce' ) => 'dimensions',
			esc_html__( 'Rating count', 'woocommerce' ) => 'rating_count',
			esc_html__( 'Weight', 'woocommerce' ) => 'weight',
			esc_html__( 'Is on sale', 'woocommerce' ) => 'on_sale',
			esc_html__( 'Custom field', 'woocommerce' ) => '_custom_',
		);
	}

	/**
	 * @param $key
	 * @return string
	 */
	/**
	 * @param $key
	 * @return string
	 */
	public static function getProductFieldLabel( $key ) {
		if ( false === self::$product_fields_list ) {
			self::$product_fields_list = array_flip( self::getProductsFieldsList() );
		}

		return isset( self::$product_fields_list[ $key ] ) ? self::$product_fields_list[ $key ] : '';
	}

	/**
	 * @return array
	 */
	/**
	 * @return array
	 */
	public static function getOrderFieldsList() {
		return array(
			esc_html__( 'ID', 'js_composer' ) => 'id',
			esc_html__( 'Order number', 'js_composer' ) => 'order_number',
			esc_html__( 'Currency', 'js_composer' ) => 'order_currency',
			esc_html__( 'Total', 'js_composer' ) => 'total',
			esc_html__( 'Status', 'js_composer' ) => 'status',
			esc_html__( 'Payment method', 'js_composer' ) => 'payment_method',
			esc_html__( 'Billing address city', 'js_composer' ) => 'billing_address_city',
			esc_html__( 'Billing address country', 'js_composer' ) => 'billing_address_country',
			esc_html__( 'Shipping address city', 'js_composer' ) => 'shipping_address_city',
			esc_html__( 'Shipping address country', 'js_composer' ) => 'shipping_address_country',
			esc_html__( 'Customer Note', 'js_composer' ) => 'customer_note',
			esc_html__( 'Customer API', 'js_composer' ) => 'customer_api',
			esc_html__( 'Custom field', 'js_composer' ) => '_custom_',
		);
	}

	/**
	 * @param $key
	 * @return string
	 */
	/**
	 * @param $key
	 * @return string
	 */
	public static function getOrderFieldLabel( $key ) {
		if ( false === self::$order_fields_list ) {
			self::$order_fields_list = array_flip( self::getOrderFieldsList() );
		}

		return isset( self::$order_fields_list[ $key ] ) ? self::$order_fields_list[ $key ] : '';
	}

	public function yoastSeoCompatibility() {
		if ( function_exists( 'WC' ) ) {
			// WC()->frontend_includes();
			include_once( WC()->plugin_path() . '/includes/wc-template-functions.php' );
			// include_once WC()->plugin_path() . '';
		}
	}
}

/**
 * Removes EDIT button in backend and frontend editor
 * Class Vc_WooCommerce_NotEditable
 * @since 4.4
 */
class Vc_WooCommerce_NotEditable extends WPBakeryShortCode {
	/**
	 * @since 4.4
	 * @var array
	 */
	protected $controls_list = array(
		'clone',
		'delete',
	);
}

