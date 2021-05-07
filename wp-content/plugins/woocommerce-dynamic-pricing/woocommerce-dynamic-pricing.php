<?php

/*
 * Plugin Name: WooCommerce Dynamic Pricing
 * Woo: 18643:9a41775bb33843f52c93c922b0053986
 * Plugin URI: https://woocommerce.com/products/dynamic-pricing/
 * Description: WooCommerce Dynamic Pricing lets you configure dynamic pricing rules for products, categories and members.
 * Version: 3.1.25
 * Author: Element Stark
 * Author URI: https://elementstark.com
 * Requires at least: 3.3
 * Tested up to: 5.7
 * Text Domain: woocommerce-dynamic-pricing
 * Domain Path: /i18n/languages/
 * Copyright: © 2009-2021 Element Stark LLC.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 5.2
 */


/**
 * Required functions
 */
if ( !function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '9a41775bb33843f52c93c922b0053986', '18643' );

if ( is_woocommerce_active() ) {

	/**
	 * Boot up dynamic pricing
	 */
	WC_Dynamic_Pricing::init();
}

class WC_Dynamic_Pricing {

	/**
	 * @var WC_Dynamic_Pricing
	 */
	private static $instance;

	public static function init() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing();
		}
	}

	/**
	 * @return WC_Dynamic_Pricing The instance of the plugin.
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::init();
		}

		return self::$instance;
	}

	private $cached_adjustments = array();

	public $modules = array();

	public $db_version = '2.1';

	public function __construct() {

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );


		if ( is_admin() ) {
			require 'admin/admin-init.php';

			//Include and boot up the installer.
			include 'classes/class-wc-dynamic-pricing-installer.php';
			WC_Dynamic_Pricing_Installer::init();

		}

		//Include additional integrations
		if ( wc_dynamic_pricing_is_groups_active() ) {
			include 'integrations/groups/groups.php';
		}

		if ( wc_dynamic_pricing_is_memberships_active() ) {
			include 'integrations/memberships/memberships.php';
		}


		//Paypal express
		include 'integrations/paypal-express.php';
		include 'integrations/woocommerce-product-bundles.php';
		include 'classes/class-wc-dynamic-pricing-compatibility.php';

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized


		if ( ( !is_admin() || defined( 'DOING_AJAX' ) ) && !defined( 'DOING_CRON' ) && !$is_rest_api_request ) {
			$this->load_front_end();
		} elseif ( $is_rest_api_request ) {
			$this->load_rest_api_front_end();
		}

		if ( isset( $_POST['createaccount'] ) ) {
			add_filter( 'woocommerce_dynamic_pricing_is_rule_set_valid_for_user', array(
				$this,
				'new_account_overrides'
			), 10, 2 );
		}

		add_filter( 'woocommerce_dynamic_pricing_get_rule_amount', array( $this, 'convert_decimals' ), 99, 4 );
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-dynamic-pricing' );
		$dir    = trailingslashit( WP_LANG_DIR );

		load_textdomain( 'woocommerce-dynamic-pricing', $dir . 'woocommerce-dynamic-pricing/woocommerce-dynamic-pricing-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-dynamic-pricing', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages/' );
	}

	public function load_rest_api_front_end() {
		//Include helper classes
		include 'classes/class-wc-dynamic-pricing-context.php';
		include 'classes/class-wc-dynamic-pricing-counter.php';
		include 'classes/class-wc-dynamic-pricing-tracker.php';

		//Include the collectors.
		include 'classes/collectors/class-wc-dynamic-pricing-collector.php';
		include 'classes/collectors/class-wc-dynamic-pricing-collector-category.php';
		include 'classes/collectors/class-wc-dynamic-pricing-collector-category-inclusive.php';

		//Include the adjustment sets.
		include 'classes/class-wc-dynamic-pricing-adjustment-set.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-category.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-product.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-totals.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-taxonomy.php';


		//The base pricing module.
		include 'classes/modules/class-wc-dynamic-pricing-module-base.php';

		//Include the advanced pricing modules.
		include 'classes/modules/class-wc-dynamic-pricing-advanced-base.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-product.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-category.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-totals.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-taxonomy.php';

		//Include the simple pricing modules.
		include 'classes/modules/class-wc-dynamic-pricing-simple-base.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-product.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-category.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-membership.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-taxonomy.php';



		//Boot up the instances of the pricing modules
		$modules['advanced_product']  = WC_Dynamic_Pricing_Advanced_Product::instance();
		$modules['advanced_category'] = WC_Dynamic_Pricing_Advanced_Category::instance();

		$modules['simple_product']    = WC_Dynamic_Pricing_Simple_Product::instance();
		$modules['simple_category']   = WC_Dynamic_Pricing_Simple_Category::instance();
		$modules['simple_membership'] = WC_Dynamic_Pricing_Simple_Membership::instance();

		if ( wc_dynamic_pricing_is_groups_active() ) {
			include 'integrations/groups/class-wc-dynamic-pricing-simple-group.php';
			$modules['simple_group'] = WC_Dynamic_Pricing_Simple_Group::instance();
		}

		if ( wc_dynamic_pricing_is_memberships_active() ) {
			include 'integrations/woocommerce-memberships.php';
			WC_Dynamic_Pricing_Memberships_Integration::register();
		}


		$this->modules = apply_filters( 'wc_dynamic_pricing_load_modules', $modules );

		/* Boot up required classes */
		WC_Dynamic_Pricing_Context::register();

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 0 );
		add_filter( 'woocommerce_product_is_on_sale', array( $this, 'on_get_product_is_on_sale' ), 10, 2 );
		add_filter( 'woocommerce_composite_get_price', array( $this, 'on_get_composite_price' ), 10, 2 );
		add_filter( 'woocommerce_composite_get_base_price', array( $this, 'on_get_composite_base_price' ), 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'check_cart_coupon_is_valid' ), 99, 2 );
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'check_coupon_is_valid' ), 99, 4 );
		add_filter( 'woocommerce_get_variation_prices_hash', array(
			$this,
			'on_woocommerce_get_variation_prices_hash'
		), 99, 1 );
	}

	public function load_front_end() {
		//Include helper classes
		include 'classes/class-wc-dynamic-pricing-context.php';
		include 'classes/class-wc-dynamic-pricing-counter.php';
		include 'classes/class-wc-dynamic-pricing-tracker.php';
		include 'classes/class-wc-dynamic-pricing-cart-query.php';

		//Include the collectors.
		include 'classes/collectors/class-wc-dynamic-pricing-collector.php';
		include 'classes/collectors/class-wc-dynamic-pricing-collector-category.php';
		include 'classes/collectors/class-wc-dynamic-pricing-collector-category-inclusive.php';

		//Include the adjustment sets.
		include 'classes/class-wc-dynamic-pricing-adjustment-set.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-category.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-product.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-totals.php';
		include 'classes/class-wc-dynamic-pricing-adjustment-set-taxonomy.php';


		//The base pricing module.
		include 'classes/modules/class-wc-dynamic-pricing-module-base.php';

		//Include the advanced pricing modules.
		include 'classes/modules/class-wc-dynamic-pricing-advanced-base.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-product.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-category.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-totals.php';
		include 'classes/modules/class-wc-dynamic-pricing-advanced-taxonomy.php';

		//Include the simple pricing modules.
		include 'classes/modules/class-wc-dynamic-pricing-simple-base.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-product.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-category.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-membership.php';
		include 'classes/modules/class-wc-dynamic-pricing-simple-taxonomy.php';


		//Include the UX module - This controls the display of discounts on cart items and products.
		include 'classes/class-wc-dynamic-pricing-frontend-ux.php';


		//Boot up the instances of the pricing modules
		$modules['advanced_product']  = WC_Dynamic_Pricing_Advanced_Product::instance();
		$modules['advanced_category'] = WC_Dynamic_Pricing_Advanced_Category::instance();

		$modules['simple_product']    = WC_Dynamic_Pricing_Simple_Product::instance();
		$modules['simple_category']   = WC_Dynamic_Pricing_Simple_Category::instance();
		$modules['simple_membership'] = WC_Dynamic_Pricing_Simple_Membership::instance();

		if ( wc_dynamic_pricing_is_groups_active() ) {
			include 'integrations/groups/class-wc-dynamic-pricing-simple-group.php';
			$modules['simple_group'] = WC_Dynamic_Pricing_Simple_Group::instance();
		}

		if ( wc_dynamic_pricing_is_memberships_active() ) {
			include 'integrations/woocommerce-memberships.php';
			WC_Dynamic_Pricing_Memberships_Integration::register();
		}

		$modules['advanced_totals'] = WC_Dynamic_Pricing_Advanced_Totals::instance();

		$this->modules = apply_filters( 'wc_dynamic_pricing_load_modules', $modules );


		/* Boot up required classes */
		WC_Dynamic_Pricing_Context::register();

		//Initialize the dynamic pricing counter.  Records various counts when items are restored from session.
		WC_Dynamic_Pricing_Counter::register();

		//Initialize the FrontEnd UX modifications
		WC_Dynamic_Pricing_FrontEnd_UX::init();

		add_action( 'wp_loaded', array( $this, 'on_wp_loaded' ), 0 );

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 0 );


		add_filter( 'woocommerce_product_is_on_sale', array( $this, 'on_get_product_is_on_sale' ), 10, 2 );


		add_filter( 'woocommerce_composite_get_price', array( $this, 'on_get_composite_price' ), 10, 2 );
		add_filter( 'woocommerce_composite_get_base_price', array( $this, 'on_get_composite_base_price' ), 10, 2 );

		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'check_cart_coupon_is_valid' ), 99, 2 );
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'check_coupon_is_valid' ), 99, 4 );

		add_filter( 'woocommerce_get_variation_prices_hash', array(
			$this,
			'on_woocommerce_get_variation_prices_hash'
		), 99, 1 );

		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'on_cart_loaded_from_session' ), 98, 1 );

		//Add the actions dynamic pricing uses to trigger price adjustments
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'on_calculate_totals' ), 98, 1 );
	}


	public function on_woocommerce_get_variation_prices_hash( $price_hash ) {

		//Get a key based on role, since all rules use roles.
		$result     = is_array( $price_hash ) ? $price_hash : array( $price_hash );
		$session_id = null;

		$roles = array();
		if ( is_user_logged_in() ) {
			$user = new WP_User( get_current_user_id() );
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role ) {
					$roles[ $role ] = $role;
				}
			}
		}

		if ( !empty( $roles ) ) {
			$session_id = implode( '', $roles );
		} else {
			$session_id = 'norole';
		}

		$result[] = $session_id;
		return $result;

	}


	/**
	 * Add the price filters back in after mini-cart is done.
	 *
	 * @since 2.10.2
	 */
	public function add_price_filters() {

		if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {
			//Filters the regular variation price

			add_filter( 'woocommerce_product_variation_get_price', array(
				$this,
				'on_get_product_variation_price'
			), 10, 2 );

			//Filters the regular product get price.
			add_filter( 'woocommerce_product_get_price', array( $this, 'on_get_price' ), 10, 2 );
		} else {
			add_filter( 'woocommerce_get_price', array( $this, 'on_get_price' ), 10, 2 );
		}

	}

	/**
	 * Remove the price filter when mini-cart is triggered.
	 *
	 * @since 2.10.2
	 */
	public function remove_price_filters() {
		if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {
			//Filters the regular variation price
			remove_filter( 'woocommerce_product_variation_get_price', array(
				$this,
				'on_get_product_variation_price'
			), 10, 2 );


			//Filters the regular product get price.
			remove_filter( 'woocommerce_product_get_price', array( $this, 'on_get_price' ), 10, 2 );
		} else {
			remove_filter( 'woocommerce_get_price', array( $this, 'on_get_price' ), 10, 2 );
		}

	}


	public function on_wp_loaded() {
		// Force calculation of totals so that they are updated in mini-cart
		if ( WC_Dynamic_Pricing_Compatibility::is_wc_version( '3.2.5' ) || WC_Dynamic_Pricing_Compatibility::is_wc_version( '3.2.4' ) || WC_Dynamic_Pricing_Compatibility::is_wc_version( '3.2.3' ) ) {
			if ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX && !empty( $_REQUEST['wc-ajax'] ) && ( $_REQUEST['wc-ajax'] === 'get_refreshed_fragments' || $_REQUEST['wc-ajax'] === 'add_to_cart' || $_REQUEST['wc-ajax'] == 'remove_from_cart' ) ) {
				WC()->session->set( 'cart_totals', null );
			}
		}
	}


	public function on_plugins_loaded() {

		require_once 'classes/class-wc-dynamic-pricing-compatibility-functions.php';
		add_filter( 'woocommerce_variation_prices_price', array( $this, 'on_get_variation_prices_price' ), 10, 3 );

		if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {
			$this->add_price_filters();
		} else {
			add_filter( 'woocommerce_get_variation_price', array( $this, 'on_get_variation_price' ), 10, 4 );
			add_filter( 'woocommerce_get_price', array( $this, 'on_get_price' ), 10, 2 );
		}


		$additional_taxonomies = apply_filters( 'wc_dynamic_pricing_get_discount_taxonomies', array() );

		if ( $additional_taxonomies ) {
			foreach ( $additional_taxonomies as $additional_taxonomy ) {

				$simple_taxonomy_module = apply_filters( 'wc_dynamic_pricing_get_taxonomy_simple_class', 'WC_Dynamic_Pricing_Simple_Taxonomy', $additional_taxonomy );
				if ( $simple_taxonomy_module && class_exists( $simple_taxonomy_module ) ) {
					$this->modules[ 'simple_taxonomy_' . $additional_taxonomy ] = $simple_taxonomy_module::instance( $additional_taxonomy );
				}

				$advanced_taxonomy_module = apply_filters( 'wc_dynamic_pricing_get_taxonomy_advanced_class', 'WC_Dynamic_Pricing_Advanced_Taxonomy', $additional_taxonomy );
				if ( $advanced_taxonomy_module && class_exists( $advanced_taxonomy_module ) ) {
					$this->modules[ 'advanced_taxonomy_' . $additional_taxonomy ] = $advanced_taxonomy_module::instance( $additional_taxonomy );
				}

			}
		}
	}

	public function check_coupon_is_valid( $valid, $product, $coupon, $values ) {

		if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {

			if ( !apply_filters( 'wc_dynamic_pricing_check_coupons', true ) ) {
				return $valid;
			}

			if ( $coupon->get_exclude_sale_items() && isset( $values['discounts'] ) && isset( $values['discounts']['applied_discounts'] ) && !empty( $values['discounts']['applied_discounts'] ) ) {
				$valid = false;
			}
		} else {
			if ( $coupon->exclude_sale_items() && isset( $values['discounts']['applied_discounts'] ) && !empty( $values['discounts']['applied_discounts'] ) ) {
				$valid = false;
			}
		}


		return $valid;
	}

	/**
	 * @param bool $valid
	 * @param WC_Coupon $coupon
	 *
	 * @return bool
	 */
	public function check_cart_coupon_is_valid( $valid, $coupon ) {
		if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {
			if ( $coupon->get_exclude_sale_items() ) {

				if ( !apply_filters( 'wc_dynamic_pricing_check_coupons', true ) ) {
					return $valid;
				}

				foreach ( WC()->cart->get_cart() as $values ) {
					if ( isset( $values['discounts'] ) && isset( $values['discounts']['applied_discounts'] ) && !empty( $values['discounts']['applied_discounts'] ) ) {
						return false;
					}
				}
			}
		} else {
			if ( $coupon->exclude_sale_items() ) {
				foreach ( WC()->cart->get_cart() as $values ) {
					if ( isset( $values['discounts'] ) && isset( $values['discounts']['applied_discounts'] ) && !empty( $values['discounts']['applied_discounts'] ) ) {
						return false;
					}
				}
			}
		}


		return $valid;
	}

	public function new_account_overrides( $result, $condition ) {
		switch ( $condition['type'] ) {
			case 'apply_to':
				if ( is_array( $condition['args'] ) && isset( $condition['args']['applies_to'] ) ) {
					if ( $condition['args']['applies_to'] == 'everyone' ) {
						$result = 1;
					} elseif ( $condition['args']['applies_to'] == 'unauthenticated' ) {
						$result = 1; //The user wasn't logged in, but now will be.  Hardcode to true
					} elseif ( $condition['args']['applies_to'] == 'authenticated' ) {
						$result = 0; //The user wasn't logged in previously.
					} elseif ( $condition['args']['applies_to'] == 'roles' && isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) ) {
						$result = 0;
					}
				}
				break;
			default:
				$result = 0;
				break;
		}

		return $result;
	}

	public function convert_decimals( $amount, $rule, $cart_item, $module ) {
		if ( function_exists( 'wc_format_decimal' ) ) {
			$amount = wc_format_decimal( str_replace( get_option( 'woocommerce_price_thousand_sep' ), '', $amount ) );
		}

		return $amount;
	}

	public function on_cart_loaded_from_session( $cart ) {
		$sorted_cart = array();
		if ( sizeof( $cart->cart_contents ) > 0 ) {
			foreach ( $cart->cart_contents as $cart_item_key => &$values ) {
				if ( $values === null ) {
					continue;
				}

				if ( isset( $cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {
					unset( $cart->cart_contents[ $cart_item_key ]['discounts'] );
				}

				$sorted_cart[ $cart_item_key ] = &$values;
			}
		}

		if ( empty( $sorted_cart ) ) {
			return;
		}


		//Sort the cart so that the lowest priced item is discounted when using block rules.
		uasort( $sorted_cart, 'WC_Dynamic_Pricing_Cart_Query::sort_by_price' );

		$modules = apply_filters( 'wc_dynamic_pricing_load_modules', $this->modules );
		foreach ( $modules as $module ) {
			$module->adjust_cart( $sorted_cart );
		}

		// Force calculation of totals so that they are updated in mini-cart
		if ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX && !empty( $_REQUEST['wc-ajax'] ) && ( $_REQUEST['wc-ajax'] === 'get_refreshed_fragments' || $_REQUEST['wc-ajax'] === 'add_to_cart' || $_REQUEST['wc-ajax'] == 'remove_from_cart' ) ) {
			if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_lte( '3.2.1' ) ) {
				$cart->subtotal = false;
			}
		}

	}

	public function on_calculate_totals( $cart ) {
		$sorted_cart = array();
		if ( sizeof( $cart->cart_contents ) > 0 ) {
			foreach ( $cart->cart_contents as $cart_item_key => $values ) {
				if ( $values != null ) {
					$sorted_cart[ $cart_item_key ] = $values;
				}
			}
		}

		if ( empty( $sorted_cart ) ) {
			return;
		}

		//Sort the cart so that the lowest priced item is discounted when using block rules.
		uasort( $sorted_cart, 'WC_Dynamic_Pricing_Cart_Query::sort_by_price' );

		$modules = apply_filters( 'wc_dynamic_pricing_load_modules', $this->modules );
		foreach ( $modules as $module ) {
			$module->adjust_cart( $sorted_cart );
		}

	}

	public function on_get_composite_price( $base_price, $_product ) {
		return $this->on_get_price( $base_price, $_product );
	}

	public function on_get_composite_base_price( $base_price, $_product ) {
		return $this->on_get_price( $base_price, $_product );
	}


	public function on_get_product_variation_price( $base_price, $_product ) {
		return $this->on_get_price( $base_price, $_product, false );
	}

	/**
	 * @param mixed $source_price
	 * @param WC_Product $_product
	 * @param bool $force_calculation
	 * @return float
	 * @since 2.6.1
	 *
	 */
	public function on_get_price( $source_price, $_product, $force_calculation = false ) {
		$composite_ajax = did_action( 'wp_ajax_woocommerce_show_composited_product' ) | did_action( 'wp_ajax_nopriv_woocommerce_show_composited_product' ) | did_action( 'wc_ajax_woocommerce_show_composited_product' );


		$base_price = floatval($source_price);
		if ( empty( $_product) || $source_price === '') {
			return $source_price;
		}


		if ( class_exists( 'WCS_ATT_Product' ) && WCS_ATT_Product::is_subscription( $_product ) ) {
			return $base_price;
		}

		$result_price = $base_price;

		if ( !$force_calculation ) {
			//Cart items are discounted when loaded from session, check to see if the call to get_price is from a cart item,
			//if so, return the price on the cart item as it currently is.
			$cart_item = WC_Dynamic_Pricing_Context::instance()->get_cart_item_for_product( $_product );

			if ( $cart_item ) {

				//If no discounts applied just return the price passed to us.
				//This is to solve subscriptions passing the sign up fee though this filter.
				if ( !isset( $cart_item['discounts'] ) ) {
					return $base_price;
				}

				//Make sure not to override the deposit amount.  It's already been configured when the cart was loaded from session.
				if ( isset( $cart_item['is_deposit'] ) ) {
					return $base_price;
				}

				$this->remove_price_filters();

				if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {
					$cart_price = $cart_item['data']->get_price( 'edit' );
				} else {
					//Use price directly since 3.0.8 so extensions do not re-filter this value.
					//https://woothemes.zendesk.com/agent/tickets/564481
					$cart_price = $cart_item['data']->price;
				}

				$this->add_price_filters();

				return $cart_price;
			}
		}

		if ( is_object( $_product ) ) {
			$cache_id = $_product->get_id() . spl_object_hash( $_product );

			if ( !$force_calculation ) {
				if ( isset( $this->cached_adjustments[ $cache_id ] ) && $this->cached_adjustments[ $cache_id ] === false ) {
					return $base_price;
				} elseif ( isset( $this->cached_adjustments[ $cache_id ] ) && !empty( $this->cached_adjustments[ $cache_id ] ) ) {
					return $this->cached_adjustments[ $cache_id ];
				}
			}

			$adjustment_applied = false;
			$discount_price     = false;
			$working_price      = $base_price;

			$modules = apply_filters( 'wc_dynamic_pricing_load_modules', $this->modules );

			$fake_cart_item = array( 'data' => $_product );


			foreach ( $modules as $module ) {

				if ( $module->module_type == 'simple' ) {
					//Make sure we are using the price that was just discounted.

					$working_price = $module->get_product_working_price( $working_price, $_product );
					if ( $working_price !== false ) {
						$discount_price = $module->get_discounted_price_for_shop( $_product, $working_price );

						if ( ( $discount_price === 0 || $discount_price === 0.0 || $discount_price ) && $discount_price != $working_price ) {
							$working_price      = $discount_price;
							$adjustment_applied = true;

							if ( !apply_filters( 'woocommerce_dynamic_pricing_is_cumulative', true, $module->module_id, $fake_cart_item, '' ) ) {
								break;
							}
						}

					}
				}
			}

			if ( $adjustment_applied && $discount_price !== false && $discount_price != $base_price ) {
				$result_price = $discount_price;
				if ( !$force_calculation ) {
					$this->cached_adjustments[ $cache_id ] = $result_price;
				}
			} else {
				$result_price = $base_price;
				if ( !$force_calculation ) {
					$this->cached_adjustments[ $cache_id ] = false;
				}
			}


		}


		return $result_price;
	}

	/**
	 * @param type $base_price
	 * @param WC_Product $_product
	 *
	 * @return float
	 * @since 2.9.8
	 *
	 */
	private function get_discounted_price( $base_price, $_product ) {

		$id             = $_product->get_id();
		$discount_price = false;
		$working_price  = isset( $this->discounted_products[ $id ] ) ? $this->discounted_products[ $id ] : $base_price;

		$modules = apply_filters( 'wc_dynamic_pricing_load_modules', $this->modules );


		foreach ( $modules as $module ) {
			if ( $module->module_type == 'simple' ) {
				//Make sure we are using the price that was just discounted.
				$working_price = $discount_price !== false ? $discount_price : $base_price;
				$working_price = $module->get_product_working_price( $working_price, $_product );
				if ( floatval( $working_price ) || intval($working_price) == 0 ) {
					$discount_price = $module->get_discounted_price_for_shop( $_product, $working_price );
					$cumulative     = apply_filters( 'woocommerce_dynamic_pricing_is_cumulative', true, $module->module_id, array( 'data' => $_product ), '' );
					if ($discount_price !== false && $discount_price != $base_price && !$cumulative ) {
						break;
					}
				}
			}
		}

		if ( $discount_price !== false ) {
			return $discount_price;
		} else {
			return $base_price;
		}
	}

	/**
	 * Filters the variation price from WC_Product_Variable->get_variation_prices()
	 *
	 * @param float $price
	 * @param WC_Product_Variation $variation
	 *
	 * @return float
	 * @since 2.11.1
	 *
	 */
	public function on_get_variation_prices_price( $price, $variation ) {
		if ($price !== '') {
			$price = $this->get_discounted_price( $price, $variation );
		}
		return $price;
	}

	/**
	 * @param float $price
	 * @param WC_Product $product
	 * @param string $min_or_max
	 * @param string $display
	 *
	 * @return float|mixed|string
	 */
	public function on_get_variation_price( $price, $product, $min_or_max, $display ) {

		$min_price        = $price;
		$max_price        = $price;
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

		$children = $product->get_children();
		if ( isset( $children ) && !empty( $children ) ) {
			foreach ( $children as $variation_id ) {
				if ( $display ) {
					$variation = wc_get_product( $variation_id );
					if ( $variation ) {
						$this->remove_price_filters();

						$base_price     = $tax_display_mode == 'incl' ? wc_get_price_including_tax( $variation ) : wc_get_price_excluding_tax( $variation );
						$calc_price     = $base_price;
						$discount_price = $this->get_discounted_price( $base_price, $variation );
						if ( $discount_price && $base_price != $discount_price ) {
							$calc_price = $discount_price;
						}

						$this->add_price_filters();
					} else {
						$calc_price = '';
					}
				} else {
					$variation  = wc_get_product( $variation_id );
					$calc_price = $variation->get_price( 'view' );
				}


				if ( $min_price == null || $calc_price < $min_price ) {
					$min_price = $calc_price;
				}

				if ( $max_price == null || $calc_price > $max_price ) {
					$max_price = $calc_price;
				}
			}
		}

		if ( $min_or_max == 'min' ) {
			return $min_price;
		} elseif ( $min_or_max == 'max' ) {
			return $max_price;
		} else {
			return $price;
		}
	}

	/**
	 * Overrides the default woocommerce is on sale to ensure sale badges show properly.
	 *
	 * @param bool $is_on_sale
	 * @param WC_Product $product
	 *
	 * @return bool
	 * @since 2.10.8
	 *
	 */
	public function on_get_product_is_on_sale( $is_on_sale, $product ) {

		if ( !apply_filters( 'wc_dynamic_pricing_flag_is_on_sale', true, $product ) ) {
			return $is_on_sale;
		}

		if ( $is_on_sale ) {
			return $is_on_sale;
		}

		//TODO:  Review bundles and sales
		//if ( $product->is_type( 'bundle' ) && $product->per_product_pricing_active ) {
		//return $is_on_sale;
		//}

		if ( $product->is_type( 'variable' ) ) {
			$is_on_sale = false;

			$prices = $product->get_variation_prices();

			$regular       = array_map( 'strval', $prices['regular_price'] );
			$actual_prices = array_map( 'strval', $prices['price'] );

			$diff = array_diff_assoc( $regular, $actual_prices );

			if ( !empty( $diff ) ) {
				$is_on_sale = true;
			}
		} else {

			//3.1.19 - just get the price sent though the regular get_price filter.
			$dynamic_price = $product->get_price('view');

			$regular_price = $product->get_regular_price( 'view' );


			if ( empty( $regular_price ) || (empty( $dynamic_price ) && floatval($dynamic_price) !== 0.00) ) {
				return $is_on_sale;
			} else {
				$is_on_sale = $regular_price != $dynamic_price;
			}
		}

		return $is_on_sale;
	}

	//Helper functions to modify the woocommerce cart.  Called from the individual modules.
	public static function apply_cart_item_adjustment( $cart_item_key, $original_price, $adjusted_price, $module, $set_id ) {

		do_action( 'wc_memberships_discounts_disable_price_adjustments' );
		WC_Dynamic_Pricing::instance()->remove_price_filters();

		$adjusted_price = apply_filters( 'wc_dynamic_pricing_apply_cart_item_adjustment', $adjusted_price, $cart_item_key, $original_price, $module );

		//Allow extensions to stop processing of applying the discount.  Added for subscriptions signup fee compatibility
		if ( $adjusted_price === false ) {
			return;
		}


		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && !empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {


			$_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];

			if ( apply_filters( 'wc_dynamic_pricing_get_use_sale_price', true, $_product ) ) {
				$display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product ) : wc_get_price_including_tax( $_product );
			} else {
				$display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product, array( 'price' => $original_price ) ) : wc_get_price_including_tax( $_product, array( 'price' => $original_price ) );
			}

			WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $adjusted_price );

			if ( $_product->get_type() == 'composite' ) {
				WC()->cart->cart_contents[ $cart_item_key ]['data']->base_price = $adjusted_price;
			}

			if ( !isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {

				$discount_data                                           = array(
					'by'                => array( $module ),
					'set_id'            => $set_id,
					'price_base'        => $original_price,
					'display_price'     => $display_price,
					'price_adjusted'    => $adjusted_price,
					'applied_discounts' => array(
						array(
							'by'             => $module,
							'set_id'         => $set_id,
							'price_base'     => $original_price,
							'price_adjusted' => $adjusted_price
						)
					)
				);
				WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $discount_data;
			} else {

				$existing = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];

				$discount_data = array(
					'by'                => $existing['by'],
					'set_id'            => $set_id,
					'price_base'        => $original_price,
					'display_price'     => $existing['display_price'],
					'price_adjusted'    => $adjusted_price,
					'applied_discounts' => $existing['applied_discounts']
				);

				WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $discount_data;


				array_push( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'], $module );
				WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'][] = array(
					'by'             => $module,
					'set_id'         => $set_id,
					'price_base'     => $original_price,
					'price_adjusted' => $adjusted_price
				);
			}
		}
		do_action( 'wc_memberships_discounts_enable_price_adjustments' );
		do_action( 'woocommerce_dynamic_pricing_apply_cartitem_adjustment', $cart_item_key, $original_price, $adjusted_price, $module, $set_id );
		WC_Dynamic_Pricing::instance()->add_price_filters();

	}

	/** Helper functions ***************************************************** */

	/**
	 * Get the plugin url.
	 *
	 * @access public
	 * @return string
	 */
	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
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

/* Helper Functions */

function wc_dynamic_pricing_is_within_date_range( $from = '', $to = '' ) {
	// Check date range
	$from_date = empty( $from ) ? false : wc_dynamic_pricing_wp_strtotime( $from . ' ' . '00:00:00' );
	$to_date   = empty( $to ) ? false : wc_dynamic_pricing_wp_strtotime( $to . ' ' . '23:59:00' );
	$now       = current_time( 'timestamp' );

	$execute_rules = true;
	if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
		$execute_rules = false;
	} elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
		$execute_rules = false;
	} elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
		$execute_rules = false;
	}

	return $execute_rules;
}

function wc_dynamic_pricing_wp_strtotime( $str ) {
	// This function behaves a bit like PHP's StrToTime() function, but taking into account the Wordpress site's timezone
	// CAUTION: It will throw an exception when it receives invalid input - please catch it accordingly
	// From https://mediarealm.com.au/
	$tz_string = get_option( 'timezone_string' );
	$tz_offset = get_option( 'gmt_offset', 0 );
	if ( !empty( $tz_string ) ) {
		// If site timezone option string exists, use it
		$timezone = $tz_string;
	} elseif ( $tz_offset == 0 ) {
		// get UTC offset, if it isn’t set then return UTC
		$timezone = 'UTC';
	} else {
		$timezone = $tz_offset;
		if ( substr( $tz_offset, 0, 1 ) != "-" && substr( $tz_offset, 0, 1 ) != "+" && substr( $tz_offset, 0, 1 ) != "U" ) {
			$timezone = "+" . $tz_offset;
		}
	}
	$datetime = new DateTime( $str, new DateTimeZone( $timezone ) );

	return $datetime->format( 'U' );
}

function wc_dynamic_pricing_is_groups_active() {
	$result = false;
	$result = in_array( 'groups/groups.php', (array) get_option( 'active_plugins', array() ) );
	if ( !$result && is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins' );
		$result  = isset( $plugins['groups/groups.php'] );
	}

	return $result;
}

function wc_dynamic_pricing_is_memberships_active() {
	$result = false;
	$result = in_array( 'woocommerce-memberships/woocommerce-memberships.php', (array) get_option( 'active_plugins', array() ) );
	if ( !$result && is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins' );
		$result  = isset( $plugins['woocommerce-memberships/woocommerce-memberships.php'] );
	}

	return $result;
}

function wc_dynamic_pricing_is_brands_active() {
	$result = false;
	$result = in_array( 'woocommerce-brands/woocommerce-brands.php', (array) get_option( 'active_plugins', array() ) );
	if ( !$result && is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins' );
		$result  = isset( $plugins['woocommerce-brands/woocommerce-brands.php'] );
	}

	return $result;
}


add_filter( 'wc_dynamic_pricing_get_discount_taxonomies', 'wc_dynamic_pricing_maybe_load_product_brands' );
function wc_dynamic_pricing_maybe_load_product_brands( $taxonomies ) {
	if ( wc_dynamic_pricing_is_brands_active() ) {
		$taxonomies[] = 'product_brand';
	}

	return $taxonomies;
}
