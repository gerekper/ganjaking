<?php
/**
 * WC_Help_Scout_Shortcodes
 *
 * @package  WC_Help_Scout_Shortcodes
 * Checks if WooCommerce is enabled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Help Scout Shortcodes.
 *
 * @package  WC_Help_Scout_Shortcodes
 */
class WC_Help_Scout_Shortcodes {

	/**
	 * Shortcodes actions.
	 */
	public function __construct() {
		add_shortcode( 'wc_help_scout_form', array( $this, 'form' ) );
	}

	/**
	 * Conversation form.
	 *
	 * @param  array $atts atrributes.
	 *
	 * @return string
	 */
	public function form( $atts ) {
		wp_enqueue_script( 'help-scout-form' );
		static $count = 0;
		$count++;
		$current_user_id = get_current_user_id();
		$orders_list     = array();

		if ( 0 < $current_user_id ) {
		
			if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
				/**
				* Action for woocommerce_help_scout_customer_args.
				*
				* @since  1.3.4
				*/
				$args = apply_filters(
					'woocommerce_help_scout_shortcode_form_user_orders_args',
					array(
						'type'      => 'shop_order',
						'status'    => array_keys( wc_get_order_statuses() ),
						'limit' => 20,
						'customer_id'     => (int) $current_user_id,
					)
				);
	
				$orders = wc_get_orders( $args );
			} else {
				/**
				* Action for woocommerce_help_scout_customer_args.
				*
				* @since  1.3.4
				*/
				$args = apply_filters(
					'woocommerce_help_scout_shortcode_form_user_orders_args',
					array(
						'post_type'      => 'shop_order',
						'post_status'    => array_keys( wc_get_order_statuses() ),
						'posts_per_page' => 20,
						'meta_query'     => array(
							array(
								'key'     => '_customer_user',
								'value'   => $current_user_id,
								'compare' => '=',
							),
						),
					)
				);
	
				$orders = get_posts( $args );
			}

			foreach ( $orders as $_order ) {
				if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
					$order = $_order;
				} else {
					$order = wc_get_order( $_order->ID );
				}
				$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );
				/* translators: $s: search term */
				$date = sprintf( _x( '%1$s at %2$s', 'date and time', 'woocommerce-help-scout' ), date_i18n( wc_date_format(), strtotime( $order_date ) ), date_i18n( wc_time_format(), strtotime( $order_date ) ) );
				/* translators: $s: search term */
				$orders_list[ $order->get_id() ] = sprintf( __( 'Order #%1$s - %2$s', 'woocommerce-help-scout' ), $order->get_order_number(), $date );
			}
		}

		$vars = array(
			'orders_list' => $orders_list,
			'counter' => $count,
		);

		$default_path = WC_Help_Scout::get_instance()->plugin_path() . '/templates/';

		return wc_get_template_html( 'shortcodes/form.php', $vars, 'woocommerce-help-scout', $default_path );
	}
}
