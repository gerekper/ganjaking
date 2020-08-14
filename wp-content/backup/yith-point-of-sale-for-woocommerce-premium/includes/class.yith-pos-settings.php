<?php
 ! defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( ! class_exists( 'YITH_POS_Settings' ) ) {
	/**
	 * Class YITH_POS_Settings
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Settings {

		/** @var YITH_POS_Settings */
		private static $_instance;

		/** @var array */
		private $_settings = array();

		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Settings
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS_Settings constructor.
		 */
		private function __construct() {
		}

		/** -------------------------------------------------------
		 * Public Getters
		 */

		/**
		 * Get the common settings
		 *
		 * @return array
		 */
		public function get_common_settings() {
			return $this->_get_settings( 'common' );
		}

		/**
		 * Get the admin settings
		 *
		 * @return array
		 */
		public function get_admin_settings() {
			return $this->_get_settings( 'admin' );
		}

		/**
		 * Get the frontend settings
		 *
		 * @return array
		 */
		public function get_frontend_settings() {
			return $this->_get_settings( 'frontend' );
		}

		/** -------------------------------------------------------
		 * Private Getters
		 */

		/**
		 * Get settings
		 *
		 * @param string $type the type of settings; possible values are 'common', 'admin', 'frontend'
		 *
		 * @return mixed
		 */
		private function _get_settings( $type = 'frontend' ) {
			$type = in_array( $type, array( 'common', 'admin', 'frontend' ) ) ? $type : 'common';
			if ( ! isset( $this->_settings[ $type ] ) ) {
				$getter                   = "_get_{$type}_settings";
				$this->_settings[ $type ] = apply_filters( 'yith_pos_components_settings', $this->$getter(), $type );
				$this->_settings[ $type ] = apply_filters( "yith_pos_components_{$type}_settings", $this->_settings[ $type ] );
			}

			return $this->_settings[ $type ];
		}

		/**
		 * get the Common Settings
		 *
		 * @return array
		 */
		private function _get_common_settings() {
			$pos_url  = yith_pos_get_pos_page_url();
			$base_url = str_replace( network_site_url(), '', $pos_url );

			$settings = array(
				'wc'             => self::get_wc_data(),
				'dateFormat'     => wc_date_format(),
				'timeFormat'     => wc_time_format(),
				'posUrl'         => $pos_url,
				'baseUrl'        => $base_url,
				'siteLocale'     => esc_attr( get_bloginfo( 'language' ) ),
				'language'       => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : get_bloginfo( 'language' ),
				'siteTitle'      => get_bloginfo( 'name' ),
				'adminUrl'       => admin_url(),
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'assetsUrl'      => YITH_POS_ASSETS_URL,
				'paymentMethods' => array(
					'enabledIds' => yith_pos_get_enabled_gateways_option(),
				),
			);

			return $settings;
		}

		/**
		 * get the Admin Settings
		 *
		 * @return array
		 */
		private function _get_admin_settings() {
			$common_settings = $this->_get_common_settings();

			$settings = array(
				'stores' => array_map(
					function ( $id ) {
						return array(
							'id'   => $id,
							'name' => yith_pos_get_store_name( $id ),
						);
					},
					yith_pos_get_stores()
				),
			);

			$settings = array_merge( $common_settings, $settings );

			return $settings;
		}

		/**
		 * get the Frontend Settings
		 *
		 * @return array
		 */
		private function _get_frontend_settings() {
			$common_settings = $this->_get_common_settings();
			$settings        = $common_settings;

			if ( is_yith_pos() && ( $register_id = yith_pos_register_logged_in() ) ) {
				$register                                = yith_pos_get_register( $register_id );
				$register_data                           = $register->get_current_data();
				$register_data['query_options']          = $register->get_inclusion_query_options();
				$register_data['category_query_options'] = $register->get_category_query_options();
				$register_data['session']                = YITH_POS_Register_Session::get_session_object( $register->get_current_session() );

				if ( isset( $register_data['session'] ) ) {
					$register_data['session']->nonce = wp_create_nonce( 'yith-pos-register-session-update-' . $register_data['session']->id );
				}

				$store                           = $register->get_store();
				$store_data                      = $store->get_current_data();
				$store_data['formatted_address'] = $store->get_formatted_address();

				$receipt      = $register->get_receipt();
				$receipt_data = ! ! $receipt ? $receipt->get_current_data() : false;

				$pos_url = yith_pos_get_pos_page_url();

				$settings = array(
					'register'                            => $register_data,
					'store'                               => $store_data,
					'receipt'                             => $receipt_data,
					'user'                                => self::get_user_data(),
					'tax'                                 => self::get_tax_data( $store ),
					'color_scheme'                        => self::get_color_scheme(),
					'loggerEnabled'                       => isset( $_GET['logger-enabled'] ),
					'addressFormat'                       => yith_pos_get_format_address( $store->get_country() ),
					'adminUrl'                            => admin_url(),
					'logoutUrl'                           => add_query_arg( array( 'yith-pos-user-logout' => true ), $pos_url ),
					'registerLogoutUrl'                   => add_query_arg( array( 'yith-pos-register-logout' => true ), $pos_url ),
					'closeRegisterUrl'                    => add_query_arg(
						array(
							'yith-pos-register-close-nonce' => wp_create_nonce( 'yith-pos-register-close-' . $register_id ),
							'register' => $register_id,
						),
						$pos_url
					),
					'logoUrl'                             => get_option( 'yith_pos_login_logo', '' ),
					'numericControllerDiscountPresets'    => get_option( 'yith_pos_numeric_controller_discount_presets', array( 5, 10, 15, 20 ) ),
					'feeAndDiscountPresets'               => get_option( 'yith_pos_fee_and_discount_presets', array( 5, 10, 15, 20, 50 ) ),
					'audioEnabled'                        => get_option( 'yith_pos_audio_enabled', 'yes' ),
					'heartbeat'                           => array(
						'nonce'    => wp_create_nonce( 'yith-pos-heartbeat' ),
						'interval' => 30,
					),
					'notifyNoStockAmount'                 => get_option( 'woocommerce_notify_no_stock_amount', 0 ),
					'multistockEnabled'                   => get_option( 'yith_pos_multistock_enabled', 'no' ),
					'multistockCondition'                 => get_option( 'yith_pos_multistock_condition', 'allowed' ),
					'showStockOnPOS'                      => get_option( 'yith_pos_show_stock_on_pos', 'no' ),
					'closeModalsWhenClickingOnBackground' => get_option( 'yith_pos_close_modals_when_clicking_on_background', 'yes' ),
					'errorMessages'                       => yith_pos_get_error_message_capabilities(),
					'barcodeMeta'                         => yith_pos_get_barcode_meta(),
				);

				$settings = array_merge( $common_settings, $settings );
			}

			return $settings;
		}

		/** -------------------------------------------------------
		 * Public Static Getters - to get specific settings
		 */

		/**
		 * get the WC data
		 *
		 * @return array
		 */
		public static function get_wc_data() {
			$currency_code = get_woocommerce_currency();

			// $payment_gateways = WC()->payment_gateways()->payment_gateways();
			$payment_gateways = yith_pos_get_active_payment_methods();

			$wc_settings = array(
				'currency'                  => array(
					'code'               => $currency_code,
					'precision'          => wc_get_price_decimals(),
					'symbol'             => html_entity_decode( get_woocommerce_currency_symbol( $currency_code ) ),
					'position'           => get_option( 'woocommerce_currency_pos' ),
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'price_format'       => html_entity_decode( get_woocommerce_price_format() ),
				),
				'placeholderImageSrc'       => wc_placeholder_img_src(),
				'stockStatuses'             => wc_get_product_stock_status_options(),
				'dataEndpoints'             => array(),
				'couponTypes'               => wc_get_coupon_types(),
				'calcDiscountsSequentially' => 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ),
				'paymentGateways'           => $payment_gateways,
				'paymentGatewaysIdTitle'    => wp_list_pluck( $payment_gateways, 'title', 'id' ),
				'orderStatuses'             => wc_get_order_statuses(),
				'autoGeneratePassword'      => get_option( 'woocommerce_registration_generate_password', 'yes' ),
			);

			return $wc_settings;
		}

		/**
		 * get the current user data
		 *
		 * @return array
		 */
		private static function get_user_data() {
			$user_id   = get_current_user_id();
			$user      = array( 'id' => $user_id );
			$user_data = get_userdata( $user_id );

			if ( ! ! $user_data ) {
				if ( $user_data->first_name || $user_data->last_name ) {
					$user['fullName'] = esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_data->first_name ), ucfirst( $user_data->last_name ) ) );
				} else {
					$user['fullName'] = esc_html( ucfirst( $user_data->display_name ) );
				}

				$user['firstName']   = $user_data->first_name;
				$user['lastName']    = $user_data->last_name;
				$user['displayName'] = $user_data->display_name;
			}
			$user['avatarURL'] = get_avatar_url( (int) $user_id, array( 'size' => 140 ) );
			$user['posCaps']   = yith_pos_get_current_user_pos_capabilities();

			return $user;
		}

		/**
		 * get the Tax data
		 *
		 * @param YITH_POS_Store|bool $store
		 *
		 * @return array
		 */
		private static function get_tax_data( $store = false ) {
			$tax_classes_and_rates = array();
			$tax_classes           = array();
			$tax_classes_labels    = array();
			if ( wc_tax_enabled() && $store ) {
				$tax_classes        = WC_Tax::get_tax_class_slugs();
				$tax_classes_labels = WC_Tax::get_tax_classes();
				$tax_classes[]      = '';
				foreach ( $tax_classes as $tax_class ) {
					$tax_classes_and_rates[ $tax_class ] = WC_Tax::find_rates(
						array(
							'country'   => $store->get_country(),
							'state'     => $store->get_state(),
							'postcode'  => $store->get_postcode(),
							'city'      => $store->get_city(),
							'tax_class' => $tax_class,
						)
					);
				}
			}

			$show_including_tax = 'incl' === get_option( 'woocommerce_tax_display_cart' );

			$showItemizedTaxInReceipt       = apply_filters( 'yith_pos_show_itemized_tax_in_receipt', false );
			$showPriceIncludingTaxInReceipt = apply_filters( 'yith_pos_show_price_including_tax_in_receipt', $show_including_tax );
			$showTaxRowInReceipt            = apply_filters( 'yith_pos_show_tax_row_in_receipt', wc_tax_enabled() && ! $showPriceIncludingTaxInReceipt );

			$data = array(
				'enabled'                        => wc_tax_enabled(),
				'priceIncludesTax'               => wc_prices_include_tax(),
				'showPriceIncludingTaxInShop'    => 'incl' === get_option( 'woocommerce_tax_display_shop' ),
				'showPriceIncludingTax'          => $show_including_tax,
				'classesAndRates'                => $tax_classes_and_rates,
				'classes'                        => $tax_classes,
				'classesLabels'                  => $tax_classes_labels,
				'roundAtSubtotal'                => 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ),
				'shippingTaxClass'               => get_option( 'woocommerce_shipping_tax_class' ),

				// todo: add global option to change these values.
				'showItemizedTaxInReceipt'       => $showItemizedTaxInReceipt,
				'showPriceIncludingTaxInReceipt' => $showPriceIncludingTaxInReceipt,
				'showTaxRowInReceipt'            => $showTaxRowInReceipt,
			);

			return $data;
		}

		/**
		 * Return the list of colors by options.
		 *
		 * @return string
		 */
		private static function get_color_scheme() {
			$color_list_key = array(
				'primary'                   => '#09adaa',
				'secondary'                 => '#c65338',
				'products_background'       => '#eaeaea',
				'save_cart_background'      => '#e09914',
				'pay_button_background'     => '#a0a700',
				'note_button_background'    => '#4d4d4d',
				'header_bar_background'     => '#435756',
				'products_title_background' => 'rgba(67, 67, 67, .75)',
			);

			$color_options = array();
			foreach ( $color_list_key as $color => $default_value ) {
				$color_code = get_option( 'yith_pos_registers_' . $color, $default_value );
				if ( yith_pos_validate_hex( $color_code ) ) {
					$color_options[ '--' . $color ] = $color_code;
					$hsl                            = yith_pos_hex2hsl( $color_code );

					if ( in_array( $color, array( 'primary' ) ) ) {
						$color_options[ '--dark_' . $color ]   = yith_pos_hsl2hex(
							array(
								$hsl[0],
								$hsl[1],
								$hsl[2] * .9,
							)
						);
						$color_options[ '--darker_' . $color ] = yith_pos_hsl2hex(
							array(
								$hsl[0],
								$hsl[1],
								$hsl[2] * .7,
							)
						);
					}
				} elseif ( strpos( $color_code, 'rgba' ) !== - 1 ) {
					$color_options[ '--' . $color ] = $color_code;

					$rgba = sscanf( $color_code, 'rgba(%d, %d, %d, %f)' );
					$hsl  = yith_pos_rgb2hsl( array( $rgba[0], $rgba[1], $rgba[2] ) );

					if ( in_array( $color, array( 'primary' ) ) ) {
						$dark_rgb                              = yith_pos_hsl2rgb(
							array(
								$hsl[0],
								$hsl[1],
								$hsl[2] * .9,
							)
						);
						$color_options[ '--dark_' . $color ]   = sprintf( 'rgba(%d, %d, %d, %f)', $dark_rgb[0], $dark_rgb[1], $dark_rgb[2], $rgba[3] );
						$darker_rgb                            = yith_pos_hsl2rgb(
							array(
								$hsl[0],
								$hsl[1],
								$hsl[2] * .7,
							)
						);
						$color_options[ '--darker_' . $color ] = sprintf( 'rgba(%d, %d, %d, %f)', $darker_rgb[0], $darker_rgb[1], $darker_rgb[2], $rgba[3] );
					}
				}
			}

			return $color_options;
		}

	}

	/**
	 * Unique access to instance of YITH_POS_Settings class
	 *
	 * @return YITH_POS_Settings
	 * @since 1.0.0
	 */
	function YITH_POS_Settings() {
		return YITH_POS_Settings::get_instance();
	}
}
