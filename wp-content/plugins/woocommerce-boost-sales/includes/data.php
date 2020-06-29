<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Data {
	private $params, $coupon, $detect;

	/**
	 * VI_WBOOSTSALES_Data constructor.
	 * Init setting
	 */
	public function __construct() {
		global $wbs_settings;
		if ( ! $wbs_settings ) {
			$wbs_settings = get_option( '_woocommerce_boost_sales', array() );
		}
		$this->params = $wbs_settings;
		$args         = array(
			'enable'                                 => 0,
			'enable_mobile'                          => 0,
			/*Upsell*/
			'enable_upsell'                          => 0,
			'hide_on_single_product_page'            => 0,
			'hide_on_cart_page'                      => 0,
			'hide_on_checkout_page'                  => 0,
			'show_recently_viewed_products'          => 0,
			'hide_products_added'                    => 0,
			'show_with_category'                     => 0,
			'show_upsells_checkbox'                  => 0,
			'exclude_product'                        => array(),
			'upsell_exclude_products'                => array(),
			'exclude_categories'                     => array(),
			'upsell_exclude_categories'              => array(),
			'sort_product'                           => 0,
			'ajax_button'                            => 0,
			'hide_view_more_button'                  => 0,
			'show_with_subcategory'                  => 0,
			'hide_out_stock'                         => 0,
			'go_to_cart'                             => 0,
			'ajax_add_to_cart_for_upsells'           => 0,
			/*Cross-sells*/
			'crosssell_enable'                       => 0,
			'crosssells_hide_on_single_product_page' => 0,
			'crosssell_display_on'                   => 0,
			'crosssell_display_on_slide'             => 0,
			'hide_cross_sell_archive'                => 0,
			'enable_cart_page'                       => 0,
			'cart_page_option'                       => 1,
			'enable_checkout_page'                   => 0,
			'bundle_added'                           => 0,
			'checkout_page_option'                   => 1,
			'display_saved_price'                    => 0,
			'override_products_on_cart'              => 0,
			'ajax_add_to_cart_for_crosssells'        => 0,
			'hide_out_of_stock'                      => 0,
			'product_bundle_name'                    => 'Bundle of {product_title}',
			'bundle_price_from'                      => array( 0 ),
			'bundle_price_discount_value'            => array( 0 ),
			'bundle_price_discount_type'             => array( 'percent' ),
			'bundle_categories'                      => array(),
			/*Discount bar*/
			'enable_discount'                        => 0,
			'discount_always_show'                   => 1,
			'coupon'                                 => '',
			/*Design*/
			/*Discount bar*/
			/*Button*/
			'button_color'                           => '#111111',
			'button_bg_color'                        => '#bdbdbd',
			/*Crosssell*/
			'coupon_position'                        => 0,
			'text_color_discount'                    => '#111111',
			'process_color'                          => '#111111',
			'process_background_color'               => '#bdbdbd',
			'coupon_desc'                            => '',
			'enable_thankyou'                        => 0,
			'message_congrats'                       => '',
			'enable_checkout'                        => 0,
			'redirect_after_second'                  => 5,
			/*Upsells*/
			'item_per_row'                           => 4,
			'limit'                                  => 8,
			'select_template'                        => 1,
			'message_bought'                         => '',
			'upsell_mobile_template'                 => 'slider',
			'continue_shopping_title'                => 'Continue Shopping',
			'continue_shopping_action'               => 'stay',
			/*Cross-sell*/
			'crosssell_description'                  => '',
			'init_delay'                             => 0,
			'enable_cross_sell_open'                 => 0,
			'icon_position'                          => 0,
			'icon'                                   => 0,
			'icon_color'                             => '#555',
			'icon_bg_color'                          => '#fff',
			'hide_gift'                              => 0,
			'bg_color_cross_sell'                    => '#fff',
			'bg_image_cross_sell'                    => '',
			'text_color_cross_sell'                  => '#9e9e9e',
			'price_text_color_cross_sell'            => '#111111',
			'save_price_text_color_cross_sell'       => '#111111',
			'crosssell_mobile_template'              => 'slider',
			'custom_css'                             => '',
			/*Update*/
			'key'                                    => '',

		);
		$this->params = apply_filters( 'wbs_settings_args', wp_parse_args( $this->params, $args ) );
	}

	/**
	 * Process coupon
	 */
	public function get_coupon( $field_name = '' ) {
		global $wbs_coupon;
		if ( $wbs_coupon && ! $field_name ) {
			return true;
		} elseif ( ! $field_name ) {
			return false;
		}
		if ( $this->get_option( 'coupon' ) && ! $wbs_coupon ) {
			$status = get_post_status( $this->get_option( 'coupon' ) );
			if ( $status == 'publish' ) {
				$coupon = new WC_Coupon( $this->get_option( 'coupon' ) );
				$data   = array(
					'amount' => $coupon->get_amount(),
					'type'   => $coupon->get_discount_type(),
					'min'    => $coupon->get_minimum_amount()
				);

				$wbs_coupon = $data;
			}
		}

		if ( $wbs_coupon && $field_name ) {
			return isset( $wbs_coupon[ $field_name ] ) ? $wbs_coupon[ $field_name ] : false;
		}
	}

	public function get_detect() {
		if ( $this->detect === null ) {
			$detect = new VillaTheme_Mobile_Detect();
			if ( $detect->isMobile() && ! $detect->isTablet() ) {
				$this->detect = 'mobile';
			} elseif ( $detect->isTablet() ) {
				$this->detect = 'tablet';
			} else {
				$this->detect = 'desktop';
			}
		}

		return $this->detect;
	}

	/**
	 * @param $field_name
	 * @param string $language
	 *
	 * @return bool|mixed|void
	 */
	public function get_option( $field_name, $language = '' ) {
		if ( $language ) {
			$field_name_language = $field_name . '_' . $language;
			if ( array_key_exists( $field_name_language, $this->params ) ) {
				return apply_filters( 'wbs_get_' . $field_name_language, $this->params[ $field_name_language ] );
			} elseif ( array_key_exists( $field_name, $this->params ) ) {
				return apply_filters( 'wbs_get_' . $field_name_language, $this->params[ $field_name ] );
			} else {
				return false;
			}
		} else {
			if ( array_key_exists( $field_name, $this->params ) ) {
				return apply_filters( 'wbs_get_' . $field_name, $this->params[ $field_name ] );
			} else {
				return false;
			}
		}
	}

	public function enable() {
		$enble        = $this->get_option( 'enable' );
		$enble_mobile = $this->get_option( 'enable_mobile' );
		// Any mobile device (phones or tablets).
		// Include and instantiate the class.
		$detect = new VillaTheme_Mobile_Detect();
		if ( $detect->isMobile() && ! $detect->isTablet() ) {
			$this->detect = 'mobile';
		} elseif ( $detect->isTablet() ) {
			$this->detect = 'tablet';
		} else {
			$this->detect = 'desktop';
		}
		if ( $detect->isMobile() ) {
			if ( ! $enble_mobile || ! $enble ) {
				return false;
			}
		}

		return $enble;
	}

	public static function search_product_statuses() {
		return apply_filters( 'woocommerce_boost_sales_search_product_statuses', current_user_can( 'edit_private_products' ) ? array(
			'private',
			'publish'
		) : array( 'publish' ) );
	}
}

new VI_WBOOSTSALES_Data();