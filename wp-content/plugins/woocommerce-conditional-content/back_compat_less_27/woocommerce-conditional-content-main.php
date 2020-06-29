<?php


/**
 * The main class for Conditional Content
 */
class WC_Conditional_Content {

	private static $instance;

	/**
	 * Boots up the conditional content extension.
	 */
	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Conditional_Content();
		}
	}

	/**
	 * Creates a new instance of the WC_Conditional_Content class.
	 */
	public function __construct() {
		//include some defaults via our filters
		add_filter( 'wc_conditional_content_get_locations', array( &$this, 'default_locations' ), 0 );
		add_filter( 'wc_conditional_content_get_rule_types', array( &$this, 'default_rule_types' ), 0 );
		add_filter( 'wc_conditional_content_get_rule_operators', array( &$this, 'default_rule_operators' ), 0 );


		//Include some core functions
		include 'woocommerce-conditional-content-functions.php';


		//Include our default rule classes
		include 'classes/rules/base.php';
		include 'classes/rules/general.php';
		include 'classes/rules/products.php';
		include 'classes/rules/stock.php';
		include 'classes/rules/sales.php';
		include 'classes/rules/users.php';

		include 'classes/rules/cart.php';

		include 'classes/rules/geo.php';

		//Include and register the taxonomy for storing the content blocks and their rules.
		include 'classes/class-wc-conditional-content-taxonomy.php';
		WC_Conditional_Content_Taxonomy::register();

		if ( is_admin() || defined( 'DOING_AJAX' ) ) {
			include 'admin/class-wc-conditional-content-admin-controller.php';
			WC_Conditional_Content_Admin_Controller::register();

			//Include the admin interface builder
			include 'classes/class-wc-conditional-content-input-builder.php';

			include 'classes/inputs/html-always.php';
			include 'classes/inputs/text.php';
			include 'classes/inputs/date.php';
			include 'classes/inputs/select.php';
			include 'classes/inputs/product-select.php';
			include 'classes/inputs/cart-product-select.php';
			include 'classes/inputs/cart-category-select.php';
			include 'classes/inputs/chosen-select.php';
		} else {
			include 'classes/class-wc-conditional-content-display.php';
			WC_Conditional_Content_Display::register();
		}
	}

	/**
	 * Hooked into wc_conditional_content_get_rule_types to get the default list of rule types.
	 *
	 * @param array $types Current list, if any, of rule types.
	 *
	 * @return array the list of rule types.
	 */
	public function default_rule_types( $types ) {
		$types = array(
			__( 'General', 'wc_conditional_content' )    => array(
				'general_always' => __( 'Always', 'wc_conditional_content' )
			),
			__( "Product", 'wc_conditional_content' )    => array(
				'product_select'    => __( 'Products', 'wc_conditional_content' ),
				'product_type'      => __( "Product Type", 'wc_conditional_content' ),
				'product_category'  => __( "Product Category", 'wc_conditional_content' ),
				'product_attribute' => __( "Product Attributes", 'wc_conditional_content' ),
				'product_price'     => __( "Product Price", 'wc_conditional_content' ),
			),
			__( 'Stock', 'wc_conditional_content' )      => array(
				'stock_status' => __( 'Stock Status', 'wc_conditional_content' ),
				'stock_level'  => __( 'Stock Level', 'wc_conditional_content' )
			),
			__( 'Sales', 'wc_conditional_content' )      => array(
				'sale_schedule' => __( 'Sale Date', 'wc_conditional_content' ),
				'sale_status'   => __( 'Sale Status', 'wc_conditional_content' )
			),
			__( "Membership", 'wc_conditional_content' ) => array(
				'users_user' => __( "User", 'wc_conditional_content' ),
				'users_role' => __( "Role", 'wc_conditional_content' )
			),
			__( "Cart", 'wc_conditional_content' )       => array(
				'cart_total'    => __( "Cart Total", 'wc_conditional_content' ),
				'cart_product'  => __( "Cart Products", 'wc_conditional_content' ),
				'cart_category' => __( "Cart Categories", 'wc_conditional_content' )
			),
			__( 'Geography', 'wc_conditional_content' )  => array(
				'geo_country_code' => __( 'Country', 'wc_conditional_content' )
			)
		);

		return $types;
	}

	/**
	 * Hooked into wc_conditional_content_get_rule_operators.  Get's the default list of rule operators.
	 *
	 * @param array $operators The current list, if any, of operators for rule types.
	 *
	 * @return array
	 */
	public function default_rule_operators( $operators ) {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
		);

		return $operators;
	}

	/**
	 * Hooked into wc_conditional_content_get_locations.  Get's a list of actions and filters which can be used to
	 * output conditional content.
	 *
	 * @param array $locations The current list, if any, of configured action hooks and filters.
	 *
	 * @return array
	 */
	public function default_locations( $locations ) {
		$locations = array(
			'woocommerce'    => array(
				'title'       => __( 'WooCommerce', 'wc_conditional_content' ),
				'description' => __( 'The single product page', 'wc_conditional_content' ),
				'hooks'       => array(
					'woocommerce_before_main_content' => array(
						'action'      => 'woocommerce_before_main_content',
						'priority'    => 0,
						'title'       => __( 'Before main content', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_after_main_content'  => array(
						'action'      => 'woocommerce_after_main_content',
						'priority'    => 0,
						'title'       => __( 'After main content', 'wc_conditional_content' ),
						'description' => ''
					)
				)
			),
			'shop'           => array(
				'title'       => __( 'Shop', 'wc_conditional_content' ),
				'description' => __( 'The single product page', 'wc_conditional_content' ),
				'hooks'       => array(
					'woocommerce_before_shop_loop' => array(
						'action'      => 'woocommerce_before_shop_loop',
						'priority'    => 0,
						'title'       => __( 'Before shop loop', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_after_shop_loop'  => array(
						'action'      => 'woocommerce_after_shop_loop',
						'priority'    => 0,
						'title'       => __( 'After shop loop', 'wc_conditional_content' ),
						'description' => ''
					)
				)
			),
			'single-product' => array(
				'title'       => __( 'Single Product', 'wc_conditional_content' ),
				'description' => __( 'The single product page', 'wc_conditional_content' ),
				'hooks'       => array(
					'woocommerce_before_single_product_summary' => array(
						'action'      => 'woocommerce_before_single_product_summary',
						'priority'    => 0,
						'title'       => __( 'Before single product summary', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_after_single_product_summary'  => array(
						'action'      => 'woocommerce_after_single_product_summary',
						'priority'    => 0,
						'title'       => __( 'Before single product summary', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_show_product_sale_flash'       => array(
						'action'      => 'woocommerce_before_single_product_summary',
						'priority'    => 11,
						'title'       => __( 'After sale flash', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_show_product_images'           => array(
						'action'      => 'woocommerce_before_single_product_summary',
						'priority'    => 21,
						'title'       => __( 'After product images', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_template_single_title'         => array(
						'action'      => 'woocommerce_single_product_summary',
						'priority'    => 6,
						'title'       => __( 'After product title', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_template_single_price'         => array(
						'action'      => 'woocommerce_single_product_summary',
						'priority'    => 11,
						'title'       => __( 'After product price', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_template_single_excerpt'       => array(
						'action'      => 'woocommerce_single_product_summary',
						'priority'    => 21,
						'title'       => __( 'After product description', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_template_single_add_to_cart'   => array(
						'action'      => 'woocommerce_single_product_summary',
						'priority'    => 31,
						'title'       => __( 'After add to cart', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_template_single_meta'          => array(
						'action'      => 'woocommerce_single_product_summary',
						'priority'    => 41,
						'title'       => __( 'After product meta', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_template_single_sharing'       => array(
						'action'      => 'woocommerce_single_product_summary',
						'priority'    => 51,
						'title'       => __( 'After product sharing', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_output_product_data_tabs'      => array(
						'action'      => 'woocommerce_after_single_product_summary',
						'priority'    => 11,
						'title'       => __( 'After product data tabs', 'wc_conditional_content' ),
						'description' => ''
					),
					'woocommerce_output_related_products'       => array(
						'action'      => 'woocommerce_after_single_product_summary',
						'priority'    => 21,
						'title'       => __( 'After related products', 'wc_conditional_content' ),
						'description' => ''
					),
				)
			)
		);

		return $locations;
	}

	/** Helper Functions **************************************************************** */

	/**
	 * Return a nonce field.
	 *
	 * @access public
	 *
	 * @param mixed $action
	 * @param bool $referer (default: true)
	 * @param bool $echo (default: true)
	 *
	 * @return void
	 */
	public static function nonce_field( $action, $referer = true, $echo = true ) {
		return wp_nonce_field( 'wcccaction-' . $action, '_n', $referer, $echo );
	}

	/**
	 * Return a url with a nonce appended.
	 *
	 * @access public
	 *
	 * @param string $action
	 * @param string $url (default: '')
	 *
	 * @return string
	 */
	public static function nonce_url( $action, $url = '' ) {
		return add_query_arg( array( '_n'         => wp_create_nonce( 'wcccaction-' . $action ),
		                             'wcccaction' => $action
		), $url );
	}

	/**
	 * Verifies a nonce
	 *
	 * @param string $action
	 * @param string $method
	 *
	 * @return boolean
	 */
	public static function verify_nonce( $action ) {
		global $woocommerce;

		$name   = '_n';
		$action = 'wcccaction-' . $action;

		if ( isset( $_REQUEST[ $name ] ) && wp_verify_nonce( $_REQUEST[ $name ], $action ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the plugin url.
	 *
	 * @access public
	 * @return string
	 */
	public static function plugin_url() {
		return plugins_url( '', __FILE__);
	}

	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

/*
 * Register the main conditional content class.
 */
WC_Conditional_Content::register();


