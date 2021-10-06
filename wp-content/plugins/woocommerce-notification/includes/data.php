<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get WooCommerce Notification Data Setting
 * Class VI_WNOTIFICATION_Data
 */
class VI_WNOTIFICATION_Data {
	private $params;

	/**
	 * WOOMULTI_CURRENCY_Data constructor.
	 * Init setting
	 */
	public function __construct() {

		global $woocommerce_notification_settings;

		if ( ! $woocommerce_notification_settings ) {
			$woocommerce_notification_settings = get_option( 'wnotification_params', array() );
		}
		$this->params = $woocommerce_notification_settings;
		$args         = array(
			'enable'                         => 0,
			'enable_mobile'                  => 0,
			'enable_rtl'                     => 0,
			'highlight_color'                => '#212121',
			'text_color'                     => '#212121',
			'background_color'               => '#ffffff',
			'background_image'               => 0,
			'image_position'                 => 0,
			'position'                       => 0,
			'border_radius'                  => 0,
			'show_close_icon'                => 0,
			'time_close'                     => 0,
			'image_redirect'                 => 0,
			'image_redirect_target'          => 0,
			'message_display_effect'         => 'fade-in',
			'message_hidden_effect'          => 'fade-out',
			'custom_css'                     => '',
			'message_purchased'              => array(),
			'custom_shortcode'               => '{number} people seeing this product right now',
			'min_number'                     => '100',
			'max_number'                     => '200',
			'archive_page'                   => 0,
			'select_categories'              => array(),
			'cate_exclude_products'          => array(),
			'limit_product'                  => 50,
			'exclude_products'               => array(),
			'order_threshold_num'            => 30,
			'order_threshold_time'           => 0,
			'order_statuses'                 => array( 'wc-processing', 'wc-completed' ),
			'archive_products'               => array(),
			'virtual_name'                   => '',
			'virtual_time'                   => 10,
			'country'                        => 0,
			'virtual_city'                   => '',
			'virtual_country'                => '',
			'ipfind_auth_key'                => '',
			'product_sizes'                  => 'shop_thumbnail',
			'non_ajax'                       => 0,
			'enable_single_product'          => 0,
			'enable_out_of_stock_product'    => 0,
			'notification_product_show_type' => 0,
			'show_variation'                 => 0,
			'loop'                           => 0,
			'next_time'                      => 0,
			'notification_per_page'          => 30,
			'initial_delay_random'           => 0,
			'initial_delay_min'              => 0,
			'initial_delay'                  => 0,
			'display_time'                   => 0,
			'sound_enable'                   => 0,
			'sound'                          => 'cool.mp3',
			'is_home'                        => 0,
			'is_checkout'                    => 0,
			'is_cart'                        => 0,
			'conditional_tags'               => '',
			'save_logs'                      => 0,
			'history_time'                   => 30,
			'key'                            => '',
			'product_link'                   => 0,
			'rounded_corner'                 => 0,
			'loop_session'                   => 0,
			'loop_session_duration'          => 1,
			'loop_session_duration_unit'     => 'h',
			'loop_session_total'             => '60',
			'image_padding'                  => '0',
			'close_icon_color'               => '#000000',

			//new option
			'change_virtual_time_enable'     => '',
			'change_message_number_enable'   => '',
			'product_visibility'   => array('visible','catalog','search'),
			'enable_current_category'   =>0,
		);
		$this->params = apply_filters( 'woonotification_settings_args', wp_parse_args( $this->params, $args ) );
	}

    public function get_params( $name = "" ) {
        if ( ! $name ) {
            return $this->params;
        } elseif ( isset( $this->params[ $name ] ) ) {
            return apply_filters( 'woonotification_' . $name, $this->params[ $name ] );
        } else {
            return false;
        }
    }

	/**
	 * @return mixed|void
	 */
	public function change_message_number_enable() {
		return apply_filters( 'woonotification_change_message_number_enable', $this->params['change_message_number_enable'] );
	}

	/**
	 * @return mixed|void
	 */
	public function change_virtual_time_enable() {
		return apply_filters( 'woonotification_change_virtual_time_enable', $this->params['change_virtual_time_enable'] );
	}

	/**
	 * @return mixed|void
	 */
	public function close_icon_color() {
		return apply_filters( 'woonotification_get_close_icon_color', $this->params['close_icon_color'] );
	}

	/**
	 * @return mixed|void
	 */
	public function image_padding() {
		return apply_filters( 'woonotification_get_image_padding', $this->params['image_padding'] );
	}

	/**
	 * @return mixed|void
	 */
	public function loop_session_total() {
		return apply_filters( 'woonotification_get_loop_session_total', $this->params['loop_session_total'] );
	}

	/**
	 * @return mixed|void
	 */
	public function loop_session_duration_unit() {
		return apply_filters( 'woonotification_get_loop_session_duration_unit', $this->params['loop_session_duration_unit'] );
	}

	/**
	 * @return mixed|void
	 */
	public function loop_session_duration() {
		return apply_filters( 'woonotification_get_loop_session_duration', $this->params['loop_session_duration'] );
	}

	/**
	 * @return mixed|void
	 */
	public function loop_session() {
		return apply_filters( 'woonotification_get_loop_session', $this->params['loop_session'] );
	}

	/**
	 * @return mixed|void
	 */
	public function rounded_corner() {
		return apply_filters( 'woonotification_get_rounded_corner', $this->params['rounded_corner'] );
	}

	/**
	 * Get time close cookie
	 * @return mixed|void
	 */
	public function get_time_close() {
		return apply_filters( 'woonotification_get_time_close', $this->params['time_close'] );
	}

	/**
	 * Enable RTL
	 * @return mixed|void
	 */
	public function enable_rtl() {
		return is_rtl();
	}

	/**
	 * Check External product
	 * @return mixed|void
	 */
	public function product_link() {
		return apply_filters( 'woonotification_product_link', $this->params['product_link'] );
	}

	/**
	 * Check enable plugin
	 * @return mixed|void
	 */
	public function enable() {
		return apply_filters( 'woonotification_enable', $this->params['enable'] );
	}

	/**
	 * Check enable mobile
	 * @return mixed|void
	 */
	public function enable_mobile() {
		return apply_filters( 'woonotification_enable_mobile', $this->params['enable_mobile'] );
	}

	/**
	 * Get Highlight Color
	 * @return mixed|void
	 */
	public function get_highlight_color() {
		return apply_filters( 'woonotification_get_highlight_color', $this->params['highlight_color'] );
	}

	/**
	 * Get Text Color
	 * @return mixed|void
	 */
	public function get_text_color() {
		return apply_filters( 'woonotification_get_text_color', $this->params['text_color'] );
	}

	/**
	 * Get Background Color
	 * @return mixed|void
	 */
	public function get_background_color() {
		return apply_filters( 'woonotification_get_background_color', $this->params['background_color'] );
	}

	/**
	 * Get Background Image
	 * @return mixed|void
	 */
	public function get_background_image() {
		return apply_filters( 'woonotification_get_background_image', $this->params['background_image'] );
	}

	/**
	 * Get Image Position
	 * @return mixed|void
	 */
	public function get_image_position() {
		return apply_filters( 'woonotification_get_image_position', $this->params['image_position'] );
	}

	/**
	 * Get position
	 * @return mixed|void
	 */
	public function get_position() {
		return apply_filters( 'woonotification_get_position', $this->params['position'] );
	}

	/**
	 * Get border radius
	 * @return mixed|void
	 */
	public function get_border_radius() {
		return apply_filters( 'woonotification_get_border_radius', $this->params['border_radius'] );
	}

	/**
	 * Check show close icon
	 * @return mixed|void
	 */
	public function show_close_icon() {
		return apply_filters( 'woonotification_image_redirect', $this->params['show_close_icon'] );
	}

	/**
	 * Check image clickable
	 * @return mixed|void
	 */
	public function image_redirect() {
		return apply_filters( 'woonotification_image_redirect', $this->params['image_redirect'] );
	}

	public function image_redirect_target() {
		return apply_filters( 'woonotification_image_redirect_target', $this->params['image_redirect_target'] );
	}

	/**
	 * Get Display Effect
	 * @return mixed|void
	 */
	public function get_display_effect() {
		return apply_filters( 'woonotification_get_message_display_effect', $this->params['message_display_effect'] );
	}

	/**
	 * Get Hidden Effect
	 * @return mixed|void
	 */
	public function get_hidden_effect() {
		return apply_filters( 'woonotification_get_message_hidden_effect', $this->params['message_hidden_effect'] );
	}

	/**
	 * Get custom CSS
	 * @return mixed|void
	 */
	public function get_custom_css() {
		return apply_filters( 'woonotification_get_custom_css', $this->params['custom_css'] );
	}

	/**
	 * Get message purchased with shortcode
	 * @return mixed|void
	 */
	public function get_message_purchased() {
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$current_lang = wpml_get_current_language();
			if ( isset( $this->params[ 'message_purchased_' . $current_lang ] ) ) {
				return apply_filters( 'woonotification_get_message_purchased_' . $current_lang, $this->params[ 'message_purchased_' . $current_lang ] );
			}
		} elseif ( class_exists( 'Polylang' ) ) {
			$current_lang = pll_current_language( 'slug' );
			if ( isset( $this->params[ 'message_purchased_' . $current_lang ] ) ) {
				return apply_filters( 'woonotification_get_message_purchased_' . $current_lang, $this->params[ 'message_purchased_' . $current_lang ] );
			}
		}

		return apply_filters( 'woonotification_get_message_purchased', $this->params['message_purchased'] );
	}

	/**
	 * Get custom shortcode
	 * @return mixed|void
	 */
	public function get_custom_shortcode() {
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$current_lang = wpml_get_current_language();
			if ( isset( $this->params[ 'custom_shortcode_' . $current_lang ] ) ) {
				return apply_filters( 'woonotification_get_custom_shortcode_' . $current_lang, $this->params[ 'custom_shortcode_' . $current_lang ] );
			} elseif ( class_exists( 'Polylang' ) ) {
				$current_lang = pll_current_language( 'slug' );
				if ( isset( $this->params[ 'message_purchased_' . $current_lang ] ) ) {
					return apply_filters( 'woonotification_get_message_purchased_' . $current_lang, $this->params[ 'message_purchased_' . $current_lang ] );
				}
			}
		}

		return apply_filters( 'woonotification_get_custom_shortcode', $this->params['custom_shortcode'] );
	}

	/**
	 * Get min number in shortcode
	 * @return mixed|void
	 */
	public function get_min_number() {
		return apply_filters( 'woonotification_get_min_number', $this->params['min_number'] );
	}

	/**
	 * Get max number in shortcode
	 * @return mixed|void
	 */
	public function get_max_number() {
		return apply_filters( 'woonotification_get_max_number', $this->params['max_number'] );
	}

	/**
	 * Check notification data type to get
	 * @return mixed|void
	 */
	public function archive_page() {
		return apply_filters( 'woonotification_get_archive_page', $this->params['archive_page'] );
	}

	/**
	 * Get list categories
	 * @return mixed|void
	 */
	public function get_categories() {
		return apply_filters( 'woonotification_get_select_categories', $this->params['select_categories'] );
	}

	/**
	 * Get exclude products of Categories
	 * @return mixed|void
	 */
	public function get_cate_exclude_products() {
		return apply_filters( 'woonotification_get_cate_exclude_products', $this->params['cate_exclude_products'] );
	}

	/**
	 * Get limit products
	 * @return mixed|void
	 */
	public function get_limit_product() {
		return apply_filters( 'woonotification_get_limit_product', $this->params['limit_product'] );
	}

	/**
	 * Get exclude products of get product from billing
	 * @return mixed|void
	 */
	public function get_exclude_products() {
		return apply_filters( 'woonotification_get_exclude_products', $this->params['exclude_products'] );
	}

	/**
	 * Get threshold number
	 * @return mixed|void
	 */
	public function get_order_threshold_num() {
		return apply_filters( 'woonotification_get_order_threshold_num', $this->params['order_threshold_num'] );
	}

	/**
	 * Get threshold type
	 * @return mixed|void
	 */
	public function get_order_threshold_time() {
		return apply_filters( 'woonotification_get_order_threshold_time', $this->params['order_threshold_time'] );
	}

	/**
	 * Get order status
	 * @return mixed|void
	 */
	public function get_order_statuses() {
		return apply_filters( 'woonotification_get_order_statuses', $this->params['order_statuses'] );
	}

	/**
	 * Get list products
	 * @return mixed|void
	 */
	public function get_products() {
		return apply_filters( 'woonotification_get_archive_products', $this->params['archive_products'] );
	}

	/**
	 * Check address type
	 * @return mixed|void
	 */
	public function country() {
		return apply_filters( 'woonotification_country', $this->params['country'] );
	}

	/**
	 * Get Virtual Time
	 * @return mixed|void
	 */
	public function get_virtual_name() {
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$current_lang = wpml_get_current_language();
			if ( isset( $this->params[ 'virtual_name_' . $current_lang ] ) ) {
				return apply_filters( 'woonotification_get_virtual_name_' . $current_lang, $this->params[ 'virtual_name_' . $current_lang ] );
			} elseif ( class_exists( 'Polylang' ) ) {
				$current_lang = pll_current_language( 'slug' );
				if ( isset( $this->params[ 'message_purchased_' . $current_lang ] ) ) {
					return apply_filters( 'woonotification_get_message_purchased_' . $current_lang, $this->params[ 'message_purchased_' . $current_lang ] );
				}
			}
		}

		return apply_filters( 'woonotification_get_virtual_name', $this->params['virtual_name'] );
	}

	/**
	 * Get Virtual Time
	 * @return mixed|void
	 */
	public function get_virtual_time() {
		return apply_filters( 'woonotification_get_virtual_time', $this->params['virtual_time'] );
	}

	/**
	 * Get Virtual City
	 * @return mixed|void
	 */
	public function get_virtual_city() {
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$current_lang = wpml_get_current_language();
			if ( isset( $this->params[ 'virtual_city_' . $current_lang ] ) ) {
				return apply_filters( 'woonotification_get_virtual_city_' . $current_lang, $this->params[ 'virtual_city_' . $current_lang ] );
			} elseif ( class_exists( 'Polylang' ) ) {
				$current_lang = pll_current_language( 'slug' );
				if ( isset( $this->params[ 'message_purchased_' . $current_lang ] ) ) {
					return apply_filters( 'woonotification_get_message_purchased_' . $current_lang, $this->params[ 'message_purchased_' . $current_lang ] );
				}
			}
		}

		return apply_filters( 'woonotification_get_virtual_city', $this->params['virtual_city'] );
	}

	/**
	 * Get Virtual Country
	 * @return mixed|void
	 */
	public function get_virtual_country() {
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$current_lang = wpml_get_current_language();
			if ( isset( $this->params[ 'virtual_country_' . $current_lang ] ) ) {
				return apply_filters( 'woonotification_get_virtual_country_' . $current_lang, $this->params[ 'virtual_country_' . $current_lang ] );
			} elseif ( class_exists( 'Polylang' ) ) {
				$current_lang = pll_current_language( 'slug' );
				if ( isset( $this->params[ 'message_purchased_' . $current_lang ] ) ) {
					return apply_filters( 'woonotification_get_message_purchased_' . $current_lang, $this->params[ 'message_purchased_' . $current_lang ] );
				}
			}
		}

		return apply_filters( 'woonotification_get_virtual_country', $this->params['virtual_country'] );
	}


	/**
	 * Get product image size
	 * @return mixed|void
	 */
	public function get_product_sizes() {
		return apply_filters( 'woonotification_get_product_sizes', $this->params['product_sizes'] );
	}

	/**
	 * Check turn off Ajax
	 * @return mixed|void
	 */
	public function non_ajax() {
		return apply_filters( 'woonotification_non_ajax', $this->params['non_ajax'] );
	}

	/**
	 * Enable notification in single product page
	 * @return mixed|void
	 */
	public function enable_single_product() {
		return apply_filters( 'woonotification_enable_single_product', $this->params['enable_single_product'] );
	}

	public function enable_out_of_stock_product() {
		return apply_filters( 'woonotification_enable_out_of_stock_product', $this->params['enable_out_of_stock_product'] );
	}

	/**
	 * Get notification type show in single product
	 * @return mixed|void
	 */
	public function get_notification_product_show_type() {
		return apply_filters( 'woonotification_get_notification_product_show_type', $this->params['notification_product_show_type'] );
	}

	/**
	 * Check show variation
	 * @return mixed|void
	 */
	public function show_variation() {
		return apply_filters( 'woonotification_show_variation', $this->params['show_variation'] ??'' );
	}

	/**
	 * Check loop
	 * @return mixed|void
	 */
	public function loop() {
		return apply_filters( 'woonotification_loop', $this->params['loop'] );
	}

	/**
	 * Get next time.
	 * @return mixed|void
	 */
	public function get_next_time() {
		return apply_filters( 'woonotification_get_next_time', $this->params['next_time'] );
	}

	/**
	 * Get notification show on page
	 * @return mixed|void
	 */
	public function get_notification_per_page() {
		return apply_filters( 'woonotification_get_notification_per_page', $this->params['notification_per_page'] );
	}

	/**
	 * Check random init time
	 * @return mixed|void
	 */
	public function initial_delay_random() {
		return apply_filters( 'woonotification_initial_delay_random', $this->params['initial_delay_random'] );
	}

	/**
	 * Get time delay minimum. It will random from initial_delay_min to initial_delay.
	 * @return mixed|void
	 */
	public function get_initial_delay_min() {
		return apply_filters( 'woonotification_get_initial_delay_min', $this->params['initial_delay_min'] );
	}

	/**
	 * Get time delay to display notification
	 * @return mixed|void
	 */
	public function get_initial_delay() {
		return apply_filters( 'woonotification_get_initial_delay', $this->params['initial_delay'] );
	}

	/**
	 * Get time display of notification
	 * @return mixed|void
	 */
	public function get_display_time() {
		return apply_filters( 'woonotification_get_display_time', $this->params['display_time'] );
	}

	/**
	 * Check enable sound
	 * @return mixed|void
	 */
	public function sound_enable() {
		return apply_filters( 'woonotification_sound_enable', $this->params['sound_enable'] );
	}

	/**
	 * Get Sound file
	 * @return mixed|void
	 */
	public function get_sound() {
		return apply_filters( 'woonotification_get_sound', $this->params['sound'] );
	}

	/**
	 * Check hidden on Homepage
	 * @return mixed|void
	 */
	public function is_home() {
		return apply_filters( 'woonotification_is_home', $this->params['is_home'] );
	}

	/**
	 * Check hidden on Checkout page
	 * @return mixed|void
	 */
	public function is_checkout() {
		return apply_filters( 'woonotification_is_checkout', $this->params['is_checkout'] );
	}

	/**
	 * Check hidden on Cart page
	 * @return mixed|void
	 */
	public function is_cart() {
		return apply_filters( 'woonotification_is_cart', $this->params['is_cart'] );
	}

	/**
	 * Get Conditional Tags
	 * @return mixed|void
	 */
	public function get_conditional_tags() {
		return apply_filters( 'woonotification_get_conditional_tags', $this->params['conditional_tags'] );
	}

	/**
	 * Check save log
	 * @return mixed|void
	 */
	public function save_logs() {
		return apply_filters( 'woonotification_save_logs', $this->params['save_logs'] );
	}

	/**
	 * Get Time to clear log
	 * @return mixed|void
	 */
	public function get_history_time() {
		return apply_filters( 'woonotification_get_history_time', $this->params['history_time'] );
	}

	/**
	 * Get purchased code
	 * @return mixed|void
	 */
	public function get_geo_api() {
		return apply_filters( 'woonotification_get_key', $this->params['key'] );
	}


}

new VI_WNOTIFICATION_Data();