<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Advanced Dynamic Pricing for WooCommerce Pro (AlogPlus).
 * https://algolplus.com/plugins/downloads/advanced-dynamic-pricing-woocommerce-pro/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_APD_PRO {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_APD_PRO|null
	 * @since 6.4.1
	 */
	protected static $instance = null;

	/**
	 * Flag the order if it has discounts
	 *
	 * @var string
	 * @since 6.4.1
	 */
	private $order_flag = 'tm_has_apd';

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_APD_PRO
	 * @since 6.4.1
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 6.4.1
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_compatibility' ] );
		add_filter( 'plugins_loaded', [ $this, 'add_compatibility_settings' ], 2, 1 );
	}

	/**
	 * Add compatibility settings
	 *
	 * @return void
	 */
	public function add_compatibility_settings() {
		add_filter( 'wc_epo_get_settings', [ $this, 'wc_epo_get_settings' ], 10, 1 );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-apd', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-apd.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @param array<mixed> $settings Array of settings.
	 * @return array<mixed>
	 * @since 6.4.1
	 */
	public function wc_epo_get_settings( $settings = [] ) {
		if ( $this->is_apd_enabled() ) {
			$settings['tm_epo_apd_enable']                      = [ $this->get_apd_enable(), $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_prefix']                      = [ '', $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_suffix']                      = [ '', $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_enable_pricing_table']        = [ 'no', $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_show_option_prices_in_order'] = [ 'no', $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_string_placement']            = [ '', $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_label_css_selector']          = [ '', $this, 'is_apd_enabled' ];
			$settings['tm_epo_apd_original_price_base']         = [ '', $this, 'is_apd_enabled' ];
		}

		return $settings;
	}

	/**
	 * Check if WooCommerce Dynamic Pricing & Discounts is enabled
	 *
	 * @return boolean
	 * @since 6.4.1
	 */
	public function is_apd_enabled() {
		return class_exists( '\ADP\BaseVersion\Includes\Compatibility\TmExtraOptionsCmp' ) && method_exists( '\ADP\BaseVersion\Includes\Compatibility\TmExtraOptionsCmp', 'calculateRulesForProduct' );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 6.4.1
	 */
	public function add_compatibility() {
		if ( ! $this->is_apd_enabled() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 4 );

		add_filter( 'wc_epo_adjust_price_before_calculate_totals', [ $this, 'wc_epo_adjust_price_before_calculate_totals' ] );
		add_filter( 'woocommerce_cart_item_price', [ $this, 'woocommerce_cart_item_price' ], 99999, 3 );
		add_action( 'wc_epo_order_item_meta', [ $this, 'wc_epo_order_item_meta' ], 10, 3 );
		add_filter( 'wc_epo_discounted_price', [ $this, 'get_dicounted_price' ], 10, 4 );
		add_filter( 'tm_epo_settings_headers', [ $this, 'tm_epo_settings_headers' ], 10, 1 );
		add_filter( 'tm_epo_settings_settings', [ $this, 'tm_epo_settings_settings' ], 10, 1 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_apd_enable', [ $this, 'sanitize_option_tm_epo_apd_enable' ], 10, 1 );
		add_filter( 'wc_epo_product_price_rules', [ $this, 'wc_epo_product_price_rules' ], 10, 2 );
		add_filter( 'wc_epo_template_args_tm_totals', [ $this, 'wc_epo_template_args_tm_totals' ], 10, 2 );
		add_action( 'wc_epo_template_tm_totals', [ $this, 'wc_epo_template_tm_totals' ], 10, 1 );
		add_action( 'woocommerce_tm_epo_price_compatibility', [ $this, 'woocommerce_tm_epo_price_compatibility' ], 10, 1 );
		add_filter( 'epo_can_show_order_price', [ $this, 'epo_can_show_order_price' ], 10, 2 );
		add_filter( 'wc_epo_original_price_type_mode', [ $this, 'wc_epo_original_price_type_mode' ], 1, 4 );
	}

	/**
	 * Select correct original price type mode
	 *
	 * @return false
	 * @since 6.4.1
	 */
	public function wc_epo_adjust_price_before_calculate_totals() {
		return false;
	}

	/**
	 * Replace cart html prices for WooCommerce Dynamic Pricing & Discounts
	 *
	 * @access public
	 *
	 * @param string       $item_price The item price.
	 * @param array<mixed> $cart_item The cart item.
	 * @param string       $cart_item_key The cart item key.
	 *
	 * @return string
	 */
	public function woocommerce_cart_item_price( $item_price = '', $cart_item = [], $cart_item_key = '' ) {
		if ( ! isset( $cart_item['tmcartepo'] ) ) {
			return $item_price;
		}
		if ( ! isset( $cart_item['adp'] ) ) {
			return $item_price;
		}

		if ( 'advanced' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_cart_field_display' ) ) {
			// Get price to display.
			$price = THEMECOMPLETE_EPO_CART()->get_price_for_cart( false, $cart_item, '', 1 );

			// Format price to display.
			$price_to_display = $price;

			$product = $cart_item['data'];

			$float_price_to_display = floatval( $product->get_price() );

			$original_price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $cart_item['tm_epo_product_original_price'], $cart_item, '', 1 );
			$float_original_price_to_display = floatval( $cart_item['tm_epo_product_original_price'] );
			if ( 'yes' === $this->get_apd_enable() ) {
				$price                  = $this->get_dicounted_price( $cart_item['tm_epo_product_original_price'], wc_get_product( themecomplete_get_id( $cart_item['data'] ) ), $cart_item_key );
				$price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $price, $cart_item, '', 1 );
				$float_price_to_display = floatval( $price );
			} else {
				$price                  = $cart_item['data']->get_price( 'edit' );
				$price                  = $price - $cart_item['tm_epo_options_prices'];
				$price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $price, $cart_item, '', 1 );
				$float_price_to_display = floatval( $price );
			}

			if ( (string) $float_price_to_display === (string) $float_original_price_to_display ) {
				return $item_price;
			}
			$item_price = '<span class="tc-epo-cart-price"><del>' . themecomplete_price( $float_original_price_to_display ) . '</del> <ins>' . themecomplete_price( $float_price_to_display ) . '</ins></span>';
			return $item_price;
		}
		return $item_price;
	}

	/**
	 * Select correct original price type mode
	 *
	 * @param float|string $price The price to convert.
	 * @param array<mixed> $post_data The posted data.
	 * @return mixed
	 * @since 6.4.1
	 */
	public function wc_epo_original_price_type_mode( $price = '', $post_data = [] ) {
		if ( function_exists( 'adp_functions' ) ) {
			$product            = wc_get_product( $post_data['add-to-cart'] );
			$qty                = $post_data['quantity'];
			$use_empty_cart     = true;
			$calculated_product = adp_functions()->calculateProduct( $product, $qty, $use_empty_cart );
			if ( $calculated_product ) {
				if ( 'undiscounted' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_original_price_base' ) && isset( $post_data['tcaddtocart'] ) ) {
					$price = $calculated_product->getOriginalPrice();
				} else {
					$price = $calculated_product->getCalculatedPrice();
				}
			}
		}

		return $price;
	}

	/**
	 * Check if we can show option prices in the order
	 *
	 * @param boolean      $can true or false.
	 * @param array<mixed> $item_meta The item meta.
	 * @return boolean
	 * @since 6.4.1
	 */
	public function epo_can_show_order_price( $can = true, $item_meta = [] ) {
		$order_flag = isset( $item_meta[ $this->order_flag ] ) ? $item_meta[ $this->order_flag ] : ( isset( $item_meta[ '_' . $this->order_flag ] ) ? $item_meta[ '_' . $this->order_flag ] : [] );
		if ( is_array( $order_flag ) && count( $order_flag ) > 0 ) {
			$order_flag = $order_flag[0];
		}
		$order_flag = themecomplete_maybe_unserialize( $order_flag );
		if ( is_array( $order_flag ) && count( $order_flag ) > 0 ) {
			$order_flag = $order_flag[0];
		}
		if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_show_option_prices_in_order' ) && ! empty( $order_flag ) ) {
			$can = false;
		}

		return $can;
	}

	/**
	 * Alter product price
	 *
	 * @param mixed $price The price to convert.
	 * @return mixed
	 * @since 6.4.1
	 */
	public function woocommerce_tm_epo_price_compatibility( $price ) {
		return $price;
	}

	/**
	 * Add extra html data attributes
	 *
	 * @param array<mixed> $args Array or arguments.
	 * @return void
	 * @since 6.4.1
	 */
	public function wc_epo_template_tm_totals( $args ) {
		$tm_epo_apd_prefix               = $args['tm_epo_apd_prefix'];
		$tm_epo_apd_suffix               = $args['tm_epo_apd_suffix'];
		$tm_epo_apd_enable_pricing_table = $args['tm_epo_apd_enable_pricing_table'];
		$tm_epo_apd_enable               = $args['tm_epo_apd_enable'];
		$tm_epo_apd_string_placement     = $args['tm_epo_apd_string_placement'];
		$tm_epo_apd_label_css_selector   = $args['tm_epo_apd_label_css_selector'];
		$tm_epo_apd_original_price_base  = $args['tm_epo_apd_original_price_base'];

		echo 'data-tm-epo-apd-enable-pricing-table="' . esc_attr( $tm_epo_apd_enable_pricing_table ) . '" data-tm-epo-apd-prefix="' . esc_attr( $tm_epo_apd_prefix ) . '" data-tm-epo-apd-suffix="' . esc_attr( $tm_epo_apd_suffix ) . '" ';

		echo 'data-tm-epo-apd-string-placement="' . esc_attr( $tm_epo_apd_string_placement ) . '" ';
		echo 'data-tm-epo-apd-label-css-selector="' . esc_attr( $tm_epo_apd_label_css_selector ) . '" ';
		echo 'data-tm-epo-apd-original-price-base="' . esc_attr( $tm_epo_apd_original_price_base ) . '" ';

		if ( class_exists( 'RP_WCDPD_Settings' ) && class_exists( 'RP_WCDPD_Promotion_Display_Price_Override' ) && RP_WCDPD_Settings::get( 'promo_display_price_override' ) ) {
			echo 'data-tm-epo-apd-price-override="1" ';
		}

		if ( defined( 'RP_WCDPD_VERSION' ) && version_compare( RP_WCDPD_VERSION, '2.2', '>=' ) ) {
			echo 'data-tm-epo-apd-product-price-discounted="1" ';
		}

		echo 'data-tm-epo-apd-attributes-to-id="' . esc_attr( $args['attributes_to_id'] ) . '" ';
		echo 'data-tm-epo-apd-enable="' . esc_attr( $tm_epo_apd_enable ) . '" ';
		if ( class_exists( 'RP_WCDPD_Settings' ) && RP_WCDPD_Settings::get( 'product_pricing_change_display_prices' ) ) {
			echo 'data-tm-epo-apd-change-display-prices="' . esc_attr( RP_WCDPD_Settings::get( 'product_pricing_change_display_prices' ) ) . '" ';
		}
	}

	/**
	 * Add extra arguments to the totals template
	 *
	 * @param array<mixed> $args Array or arguments.
	 * @param object       $product The product object.
	 * @return array<mixed>
	 * @since 6.4.1
	 */
	public function wc_epo_template_args_tm_totals( $args, $product ) {
		$args['tm_epo_apd_suffix']               = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_suffix' );
		$args['tm_epo_apd_prefix']               = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_prefix' );
		$args['tm_epo_apd_enable_pricing_table'] = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_enable_pricing_table' );
		$args['tm_epo_apd_enable']               = $this->get_apd_enable( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_enable' ) );
		$args['tm_epo_apd_string_placement']     = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_string_placement' );
		$args['tm_epo_apd_label_css_selector']   = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_label_css_selector' );
		$args['tm_epo_apd_original_price_base']  = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_original_price_base' );

		$args['fields_price_rules'] = ( 'no' === $args['tm_epo_apd_enable'] ) ? $args['fields_price_rules'] : 1;

		if ( '1' === $args['price_override'] ) {
			$args['fields_price_rules'] = 1;
		}

		$attributes_to_id = [];
		if ( 'variable' === themecomplete_get_product_type( $product ) ) {

			$attributes_ids = $product->get_attributes();

			$pid = themecomplete_get_id( $product );

			foreach ( $attributes_ids as $attkey => $attvalue ) {

				$terms = wp_get_post_terms( $pid, $attkey, [ 'fields' => 'all' ] );
				if ( ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$attributes_to_id[ 'attribute_' . $attkey ][ $term->slug ] = $term->term_id;
					}
				}
			}
		}

		$args['attributes_to_id'] = esc_html( (string) wp_json_encode( (array) $attributes_to_id ) );

		return $args;
	}

	/**
	 * Get product price rules
	 *
	 * @param array<mixed> $price Price array.
	 * @param object|null  $product The product object.
	 * @return array<mixed>
	 * @since 6.4.1
	 */
	public function wc_epo_product_price_rules( $price = [], $product = null ) {
		if ( $this->is_apd_enabled() ) {
			$check_price = $this->get_prodcut_price_rules( $product );
			if ( $check_price ) {
				$price['product'] = [];
				if ( $check_price['is_multiprice'] ) {
					foreach ( $check_price['rules'] as $variation_id => $variation_rule ) {
						foreach ( $variation_rule as $rulekey => $pricerule ) {
							$price['product'][ $variation_id ][] = [
								'min'        => $pricerule['min'],
								'max'        => INF !== $pricerule['max'] ? $pricerule['max'] : '',
								'value'      => ( 'percentage' !== $pricerule['type'] ) ? apply_filters( 'wc_epo_product_price', $pricerule['value'] ) : $pricerule['value'],
								'type'       => $pricerule['type'],
								'conditions' => [],
							];
						}
					}
				} else {
					foreach ( $check_price['rules'] as $rulekey => $pricerule ) {
						$price['product'][0][] = [
							'min'        => $pricerule['min'],
							'max'        => INF !== $pricerule['max'] ? $pricerule['max'] : '',
							'value'      => ( 'percentage' !== $pricerule['type'] ) ? apply_filters( 'wc_epo_product_price', $pricerule['value'] ) : $pricerule['value'],
							'type'       => $pricerule['type'],
							'conditions' => [],
						];
					}
				}
			}
			$price['price'] = apply_filters( 'woocommerce_tm_epo_price_compatibility', apply_filters( 'wc_epo_product_price', $product->get_price() ) );
		}

		return $price;
	}

	/**
	 * Add order item meta
	 *
	 * @param WC_Order_Item $item The order item object.
	 * @param string        $cart_item_key The cart item key.
	 * @param array<mixed>  $values Array of value.
	 * @return void
	 * @since 6.4.1
	 */
	public function wc_epo_order_item_meta( $item, $cart_item_key, $values ) {
		if ( ! empty( $values['tmcartepo'] ) ) {
			$item->add_meta_data( '_' . $this->order_flag, [ 1 ] );
		}
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @param array<mixed> $headers Array of settings.
	 * @return array<mixed>
	 * @since 6.4.1
	 */
	public function tm_epo_settings_headers( $headers = [] ) {
		$headers['apd'] = [ 'tcfa tcfa-percentage', esc_html__( 'Advanced Dynamic Pricing', 'woocommerce-tm-extra-product-options' ) ];

		return $headers;
	}

	/**
	 * Get default apd_enable setting
	 *
	 * @param string $default_value The default value.
	 * @return string
	 * @since 6.4.1
	 */
	public function get_apd_enable( $default_value = 'no' ) {
		$default_tm_epo_apd_enable = $default_value;
		if ( function_exists( 'adp_context' ) ) {
			$cnxt        = adp_context();
			$adp_setting = $cnxt->getCompatibilitySettings()->getOption( 'dont_apply_discount_to_addons' );
			if ( $adp_setting ) {
				$default_tm_epo_apd_enable = 'no';
			} else {
				$default_tm_epo_apd_enable = 'yes';
			}
		}
		return $default_tm_epo_apd_enable;
	}

	/**
	 * Returns the correct apd_enable value.
	 *
	 * @param string $value The value.
	 * @return string
	 * @since 1.0
	 */
	public function sanitize_option_tm_epo_apd_enable( $value ) {
		if ( function_exists( 'adp_context' ) ) {
			$cnxt = adp_context();
			if ( 'yes' === $value ) {
				$default_apd_enable = false;
			} else {
				$default_apd_enable = true;
			}
			$cnxt->getCompatibilitySettings()->set( 'dont_apply_discount_to_addons', $default_apd_enable );
			$cnxt->getCompatibilitySettings()->save();
		}
		return $value;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @param array<mixed> $settings Array of settings.
	 * @return array<mixed>
	 * @since 6.4.1
	 */
	public function tm_epo_settings_settings( $settings = [] ) {
		$label = esc_html__( 'Advanced Dynamic Pricing', 'woocommerce-tm-extra-product-options' );

		// Set correct tm_epo_apd_enable.
		$tm_epo_apd_enable = get_option( 'tm_epo_apd_enable' );
		$get_apd_enable    = $this->get_apd_enable();
		if ( $tm_epo_apd_enable !== $get_apd_enable ) {
			update_option( 'tm_epo_apd_enable', $get_apd_enable );
		}

		$settings['dpd'] = [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'    => esc_html__( 'Enable discounts on extra options', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will extend product discounts to include extra options. This configuration will automatically synchronize with the setting of the discount plugin.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_apd_enable',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => $get_apd_enable,
				'type'     => 'select',
				'options'  => [
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
				],
				'desc_tip' => false,
			],
			[
				'title'    => esc_html__( 'Base original price type according to', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Set to what price types will base their original price on.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_apd_original_price_base',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => '',
				'type'     => 'select',
				'options'  => [
					''             => esc_html__( 'No change', 'woocommerce-tm-extra-product-options' ),
					'undiscounted' => esc_html__( 'Undiscounted product price', 'woocommerce-tm-extra-product-options' ),
				],
				'desc_tip' => false,
			],
			[
				'title'   => esc_html__( 'Enable alteration of pricing table', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the inclusion of option prices to the pricing table.', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'id'      => 'tm_epo_apd_enable_pricing_table',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'    => esc_html__( 'Prefix label', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Display a prefix label on product page.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_apd_prefix',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => false,
			],
			[
				'title'    => esc_html__( 'Suffix label', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Display a suffix label on product page.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_apd_suffix',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => false,
			],
			[
				'title'    => esc_html__( 'Label CSS selector', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enter a CSS selector to override the default label where the discount string will be displayed.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_apd_label_css_selector',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => false,
			],
			[
				'title'    => esc_html__( 'Discount string placement', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Select the placement of the discounted string compared to the label.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_apd_string_placement',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'no',
				'type'     => 'select',
				'options'  => [
					'after'  => esc_html__( 'After', 'woocommerce-tm-extra-product-options' ),
					'before' => esc_html__( 'Before', 'woocommerce-tm-extra-product-options' ),
				],
				'desc_tip' => false,
			],
			[
				'title'   => esc_html__( 'Enable showing option prices in the Order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this at your own risk since it is not possible to calculate option discounts on the Order page.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_apd_show_option_prices_in_order',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],

		];

		return $settings;
	}

	/**
	 * Calculates the new price after applying a discount amount to the original price.
	 *
	 * @param float $price The original price.
	 * @param float $discount_amount The discount amount to be subtracted from the original price.
	 *
	 * @return float
	 */
	protected function make_discount_amount( $price, $discount_amount ) {
		return $this->check_discount( $price, (float) $price - (float) $discount_amount );
	}

	/**
	 * Calculates the new price after adding an overprice amount to the original price.
	 *
	 * @param float $price The original price.
	 * @param float $overprice_amount The overprice amount to be added to the original price.
	 *
	 * @return float
	 */
	protected function make_overprice_amount( $price, $overprice_amount ) {
		return $this->check_overprice( $price, (float) $price + (float) $overprice_amount );
	}

	/**
	 * Calculates the new price after applying a percentage discount or overprice to the original price.
	 *
	 * @param float $price The original price.
	 * @param float $percentage The percentage to be applied (positive for discount, negative for overprice).
	 *
	 * @return float
	 */
	protected function make_discount_percentage( $price, $percentage ) {
		if ( $percentage < 0 ) {
			return $this->check_overprice( $price, (float) $price * ( 1 - (float) $percentage / 100 ) );
		}

		return $this->check_discount( $price, (float) $price * ( 1 - (float) $percentage / 100 ) );
	}

	/**
	 * Ensures that the new price after an overprice is non-negative and not less than the original price.
	 *
	 * @param float $old_price The original price.
	 * @param float $new_price The new price after applying an overprice.
	 *
	 * @return float
	 */
	private function check_overprice( $old_price, $new_price ) {
		$new_price = max( $new_price, 0.0 );
		$new_price = max( $new_price, $old_price );

		return (float) $new_price;
	}

	/**
	 * Determines the final price by choosing between
	 * the original price and a fixed value,
	 * ensuring that it is the maximum of the two.
	 *
	 * @param float $price The original price.
	 * @param float $value The fixed value to compare with the original price.
	 *
	 * @return float
	 */
	protected function make_price_fixed( $price, $value ) {
		$value = floatval( $value );
		if ( $price < $value ) {
			return $this->check_overprice( $price, $value );
		}

		return $this->check_discount( $price, $value );
	}

	/**
	 * Ensures that the new price after a discount is non-negative
	 * and not greater than the original price.
	 *
	 * @param float $old_price The original price.
	 * @param float $new_price The new price after applying a discount.
	 *
	 * @return float
	 */
	private function check_discount( $old_price, $new_price ) {
		$new_price = max( $new_price, 0.0 );
		$new_price = min( $new_price, $old_price );

		return (float) $new_price;
	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price for options
	 * modified from get version from Pricing class
	 *
	 * @param float   $field_price The field price.
	 * @param string  $cart_item_key The cart item key.
	 * @param array   $pricing The pricing object.
	 * @param boolean $force true or false.
	 * @return mixed
	 * @since 6.4.1
	 */
	private function get_single_dicounted_price( $field_price, $cart_item_key, $pricing = [], $force = false ) {
		if ( empty( $cart_item_key ) || 'no' === $this->get_apd_enable( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_apd_enable' ) ) && ! $force ) {
			return $field_price;
		}

		// ADP does not return correct results for negative prices.
		$is_negative = false;
		if ( $field_price < 0 ) {
			$field_price = abs( $field_price );
			$is_negative = true;
		}

		$price          = $field_price;
		$original_price = $price;

		if ( is_array( $pricing ) ) {
			$cart_item = WC()->cart->cart_contents[ $cart_item_key ];
			$quantity  = isset( $cart_item['quantity'] ) ? (float) $cart_item['quantity'] : 1;
			$discount  = 'ADP\\BaseVersion\\Includes\\Core\\Rule\\Structures\\Discount';
			if ( class_exists( $discount ) ) {
				foreach ( $pricing as $rule ) {
					$operation_type  = $rule['type'];
					$operation_value = $rule['value'];
					if ( $quantity >= (float) $rule['min'] && ( $quantity <= (float) $rule['max'] || '' === $rule['max'] ) ) {
						if ( $discount::TYPE_FREE === $operation_type ) {
							$price = 0.0;
						} elseif ( $discount::TYPE_AMOUNT === $operation_type ) {
							if ( $operation_value > 0 ) {
								$price = $this->make_discount_amount( $price, $operation_value );
							} else {
								$price = $this->make_overprice_amount( $price, ( -1 ) * $operation_value );
							}
						} elseif ( $discount::TYPE_PERCENTAGE === $operation_type ) {
							$price = $this->make_discount_percentage( $price, $operation_value );
						} elseif ( $discount::TYPE_FIXED_VALUE === $operation_type ) {
							$price = $this->make_price_fixed( $price, $operation_value );
						}
					}
				}
			}
		}

		if ( $price !== $original_price ) {
			if ( $is_negative ) {
				$price = -1 * $price;
			}
			return $price;
		}

		if ( $is_negative ) {
			$field_price = -1 * $field_price;
		}
		return $field_price;
	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price rules
	 *
	 * @param float|null  $field_price The field price.
	 * @param mixed       $product The product object.
	 * @param string|null $cart_item_key The cart item key.
	 * @param boolean     $force true or false.
	 * @return mixed
	 */
	public function get_dicounted_price( $field_price = null, $product = null, $cart_item_key = null, $force = false ) {
		$price = null;

		if ( $this->is_apd_enabled() ) {
			$table_data = $this->get_prodcut_price_rules( $product );
			if ( is_array( $table_data ) && isset( $table_data['rules'] ) ) {
				$table_data = $table_data['rules'];
				if ( null !== $field_price && null !== $cart_item_key ) {
					return $this->get_single_dicounted_price( $field_price, $cart_item_key, $table_data, $force );
				}
			}
		}
		if ( null !== $field_price ) {
			$price = $field_price;
		}

		return $price;
	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price rules
	 *
	 * @param mixed $product The product object.
	 * @return mixed
	 */
	public function get_prodcut_price_rules( $product = null ) {
		$price = null;

		if ( $this->is_apd_enabled() ) {
			$price = [];

			$all_rules  = [];
			$table_data = [];

			if ( ! $product->is_type( 'variable' ) && ! $product->is_type( 'variation' ) ) {
				$price['is_multiprice'] = false;

				$adp_cmp       = new \ADP\BaseVersion\Includes\Compatibility\TmExtraOptionsCmp();
				$product_rules = $adp_cmp->calculateRulesForProduct( $product );
				if ( ! $product_rules ) {
					$product_rules = [];
				}

				$all_rules = [];
				foreach ( $product_rules as $rule ) {
					$all_rules[] = [
						'rule' => $rule,
					];
				}
			} else {
				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}
				$variation_rules = [];
				foreach ( $product->get_available_variations() as $variation_data ) {
					$variation = wc_get_product( $variation_data['variation_id'] );

					$adp_cmp       = new \ADP\BaseVersion\Includes\Compatibility\TmExtraOptionsCmp();
					$product_rules = $adp_cmp->calculateRulesForProduct( $variation );
					if ( ! $product_rules ) {
						$product_rules = [];
					}
					foreach ( $product_rules as $k => $rule ) {
						$variation_rules[ $variation_data['variation_id'] ][] = [
							'rule' => $rule,
						];
					}
				}

				$all_rules              = $variation_rules;
				$price['is_multiprice'] = true;
			}

			$keys_to_check = [ 'product_discount', 'bulk_discount', 'role_discounts' ];

			if ( ! $price['is_multiprice'] ) {
				foreach ( $all_rules as $single ) {
					$_rule = $single['rule'];
					if ( ! $_rule ) {
						continue;
					}

					$exclusive = isset( $_rule['rule_type'] ) && 'exclusive' === $_rule['rule_type'];

					if ( $exclusive ) {
						$table_data = [];
					}

					$intersection = array_intersect( $keys_to_check, array_keys( $_rule ) );
					$mode         = reset( $intersection );

					switch ( $mode ) {
						case 'product_discount':
							$table_data[] = [
								'min'        => 1,
								'max'        => '',
								'type'       => $_rule[ $mode ]['discount_type'],
								'value'      => $_rule[ $mode ]['value'],
								'conditions' => [],
							];
							break;
						case 'bulk_discount':
							$pricing_method  = $_rule[ $mode ]['discount_type'];
							$quantity_ranges = $_rule[ $mode ]['ranges'];
							foreach ( $quantity_ranges as $quantity_range ) {
								$table_data[] = [
									'min'        => $quantity_range['from'],
									'max'        => $quantity_range['to'],
									'type'       => $pricing_method,
									'value'      => $quantity_range['value'],
									'conditions' => [],
								];
							}
							break;
						case 'role_discounts':
							$quantity_ranges = $_rule[ $mode ];
							foreach ( $quantity_ranges as $quantity_range ) {
								$table_data[] = [
									'min'        => 1,
									'max'        => '',
									'type'       => $quantity_range['discount_type'],
									'value'      => $quantity_range['value'],
									'conditions' => [],
								];
							}
							break;
					}

					if ( $exclusive ) {
						break;
					}
				}
			} else {
				foreach ( $all_rules as $vid => $vidsingle ) {
					foreach ( $vidsingle as $single ) {
						$_rule = $single['rule'];
						if ( ! $_rule ) {
							continue;
						}

						$exclusive = isset( $_rule['rule_type'] ) && 'exclusive' === $_rule['rule_type'];

						if ( $exclusive ) {
							$table_data[ $vid ] = [];
						}

						$intersection = array_intersect( $keys_to_check, array_keys( $_rule ) );
						$mode         = reset( $intersection );

						switch ( $mode ) {
							case 'product_discount':
								$table_data[ $vid ][] = [
									'min'        => 1,
									'max'        => '',
									'type'       => $_rule[ $mode ]['discount_type'],
									'value'      => $_rule[ $mode ]['value'],
									'conditions' => [],
								];
								break;
							case 'bulk_discount':
								$pricing_method  = $_rule[ $mode ]['discount_type'];
								$quantity_ranges = $_rule[ $mode ]['ranges'];
								foreach ( $quantity_ranges as $quantity_range ) {
									$table_data[ $vid ][] = [
										'min'        => $quantity_range['from'],
										'max'        => $quantity_range['to'],
										'type'       => $pricing_method,
										'value'      => $quantity_range['value'],
										'conditions' => [],
									];
								}
								break;
							case 'role_discounts':
								$quantity_ranges = $_rule[ $mode ];
								foreach ( $quantity_ranges as $quantity_range ) {
									$table_data[ $vid ][] = [
										'min'        => 1,
										'max'        => '',
										'type'       => $quantity_range['discount_type'],
										'value'      => $quantity_range['value'],
										'conditions' => [],
									];
								}
								break;
						}

						if ( $exclusive ) {
							break;
						}
					}
				}
			}
			$price['rules'] = $table_data;
		}

		return $price;
	}
}
