<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Help Scout Shortcodes.
 *
 * @package  WC_Help_Scout_Shortcodes
 * @category Shortcodes
 * @author   WooThemes
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
	 * @param  array $atts
	 *
	 * @return string
	 */
	public function form( $atts ) {
		wp_enqueue_script( 'help-scout-form' );

		$current_user_id = get_current_user_id();
		$orders_list     = array();

		if ( 0 < $current_user_id ) {
			$args = apply_filters( 'woocommerce_help_scout_shortcode_form_user_orders_args', array(
				'post_type'      => 'shop_order',
				'post_status'    => array_keys( wc_get_order_statuses() ),
				'posts_per_page' => 20,
				'meta_query'     => array(
					array(
						'key'     => '_customer_user',
						'value'   => $current_user_id,
						'compare' => '='
					)
				)
			) );

			$orders = get_posts( $args );

			foreach ( $orders as $_order ) {
				$order = wc_get_order( $_order->ID );
				$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );
				$date = sprintf( _x( '%1$s at %2$s', 'date and time', 'woocommerce-help-scout' ), date_i18n( wc_date_format(), strtotime( $order_date ) ), date_i18n( wc_time_format(), strtotime( $order_date ) ) );

				$orders_list[ $order->id ] = sprintf( __( 'Order #%s - %s', 'woocommerce-help-scout' ), $order->get_order_number(), $date );
			}
		}

		$vars = array(
			'orders_list' => $orders_list,
		);

		$default_path = WC_Help_Scout::get_instance()->plugin_path() . '/templates/';

		return wc_get_template_html( 'shortcodes/form.php', $vars, 'woocommerce-help-scout', $default_path );
	}
}
