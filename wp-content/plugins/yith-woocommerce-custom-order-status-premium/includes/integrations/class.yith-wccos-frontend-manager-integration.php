<?php
/**
 * Frontend Manager integration.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

/**
 * YITH_WCCOS_Frontend_Manager_Integration class.
 *
 * @since 1.2.6
 */
class YITH_WCCOS_Frontend_Manager_Integration {

	/**
	 * The single instance
	 *
	 * @var YITH_WCCOS_Frontend_Manager_Integration
	 */
	private static $instance;

	/**
	 * Singleton implementation
	 *
	 * @return YITH_WCCOS_Frontend_Manager_Integration
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCCOS_Frontend_Manager_Integration constructor.
	 */
	private function __construct() {
		if ( $this->is_enabled() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99 );
		}
	}

	/**
	 * Is the plugin enabled?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return defined( 'YITH_WCFM_PREMIUM' ) && defined( 'YITH_WCFM_VERSION' ) && version_compare( YITH_WCFM_VERSION, '1.6.16', '>=' );
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		if ( function_exists( 'YITH_Frontend_Manager' ) && ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() ) {
			wp_add_inline_style( 'yith-wcfm-product_orders', $this->get_inline_css() );
		}
	}

	/**
	 * Get inline CSS:
	 *
	 * @return string
	 */
	public function get_inline_css() {
		$css        = '';
		$status_ids = yith_wccos_get_statuses();

		foreach ( $status_ids as $status_id ) {
			$name         = get_post_meta( $status_id, 'slug', true );
			$label        = get_the_title( $status_id );
			$color        = get_post_meta( $status_id, 'color', true );
			$icon         = get_post_meta( $status_id, 'icon', true );
			$graphicstyle = get_post_meta( $status_id, 'graphicstyle', true );
			$icon         = is_array( $icon ) ? $icon : array();

			$icon_data      = $icon['icon'] ?? 'FontAwesome:genderless';
			$is_custom_icon = isset( $icon['select'] ) && 'none' !== $icon['select'];
			$icon_data      = explode( ':', $icon_data, 2 );
			if ( count( $icon_data ) === 2 ) {
				$icon_font = $icon_data[0];
				$icon_name = $icon_data[1];
			} else {
				$icon_font = 'FontAwesome';
				$icon_name = 'genderless';
			}

			$icons    = YIT_Icons()->get_icons();
			$icon_key = array_key_exists( $icon_font, $icons ) ? array_search( $icon_name, $icons[ $icon_font ], true ) : '';

			$text_color = yith_wccos_light_or_dark( $color, 'rgba(0,0,0,0.9)', '#ffffff' );
			$bg_color   = $color;

			$action_name = 'completed' !== $name ? $name : 'complete';

			if ( 'text' === $graphicstyle ) {

				$css .= ".order_actions .button.{$action_name}{
                                    background: {$bg_color} !important;
                                    color: {$text_color} !important;
                                    text-decoration: none !important;
                                    text-indent: 0 !important;
                                    width: auto !important;
                                	height: auto !important;
                                	padding: 5px 16px !important;
                        }";

				$css .= "mark.order-status.{$name}{
                                background: {$bg_color} !important;
                                color: {$text_color} !important;
                                text-indent: 0 !important;
                                width: auto !important;
                                height: auto !important;
                                padding: 3px 10px !important;
								line-height: 1.4 !important;
								font-size: 1em !important;
								text-transform: uppercase !important;
								font-weight: 800 !important;
								border-radius: 100px !important;
                        }";

				$css .= ".order_actions .button.{$action_name}::after, mark.order-status.{$name}::after{
                                display: none !important;
                        }";

			} else {
				$wc_status = array( 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' );

				if ( ! $is_custom_icon && in_array( $name, $wc_status, true ) ) {

					$css .= "mark.{$name}::after{
		                                color: {$color} !important;
		                            }";

					$css .= ".order_actions .button.$action_name{
		                                background-color: {$color} !important;
		                            }";

					$css .= ".order_actions .button.$action_name::after{
		                               color: $text_color !important;
		                            }";
				} else {
					$css .= ".order_actions .button.{$action_name}, mark.order-status.{$name}{
		                               text-indent: -9999px !important;
		                               background-color: {$color} !important;
		                           }";

					$css .= ".order_actions .$action_name::after, mark.order-status.{$name}::after {
		                               content:'$icon_key' !important;
		                               color: $text_color !important;
		                               font-family: $icon_font !important;
		                           }";

					$css .= "mark.order-status.{$name}{
									   background: {$bg_color} !important;
                                	   color: {$text_color} !important;
									   padding: 0 !important;
									   width: 30px !important;
									   height: 30px !important;
									   line-height: 1em !important;
									   border-radius: 100px !important;
									   font-size: 1.2em !important;
		                           }";

					$css .= "mark.order-status.{$name}::after {
		                               padding-top: 0px !important;
		                               top: 50% !important;
		                               transform: translateY(-50%) !important;
									   height: auto !important;
		                           }";
				}
			}
		}

		return $css;
	}


}

return YITH_WCCOS_Frontend_Manager_Integration::get_instance();
