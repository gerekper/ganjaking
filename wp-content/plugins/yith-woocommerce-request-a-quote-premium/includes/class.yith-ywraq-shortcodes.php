<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_YWRAQ_Shortcodes class.
 *
 * @class    YITH_YWRAQ_Shortcodes
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
class YITH_YWRAQ_Shortcodes {

	/**
	 * Constructor for the shortcode class
	 */
	public function __construct() {

		add_shortcode( 'yith_ywraq_request_quote', array( $this, 'request_quote_page' ) );
		add_shortcode( 'yith_ywraq_myaccount_quote_list', array( $this, 'my_account_raq_shortcode' ) );
		add_shortcode( 'yith_ywraq_single_view_quote', array( $this, 'single_view_quote' ) );

		add_shortcode( 'yith_ywraq_myaccount_quote', array( $this, 'raq_shortcode_account' ) );
		add_shortcode( 'yith_ywraq_widget_quote', array( $this, 'widget_quote' ) );
		add_shortcode( 'yith_ywraq_mini_widget_quote', array( $this, 'mini_widget_quote' ) );
		add_shortcode( 'yith_ywraq_button_quote', array( $this, 'button_quote' ) );

		add_shortcode( 'yith_ywraq_number_items', array( $this, 'ywraq_number_items' ) );

	}

	/**
	 * View Quote Shortcode
	 *
	 * @return string
	 */
	public function raq_shortcode_account() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		global $wp;

		$view_quote = get_option( 'woocommerce_myaccount_view_quote_endpoint', 'view-quote' );

		if ( empty( $wp->query_vars[ $view_quote ] ) ) {
			return WC_Shortcodes::shortcode_wrapper( array( YITH_YWRAQ_Order_Request(), 'view_quote_list' ) );
		} else {
			return WC_Shortcodes::shortcode_wrapper( array( YITH_YWRAQ_Order_Request(), 'view_quote' ) );
		}
	}

	/**
	 * Request Quote Page Shortcode
	 *
	 * @param array $atts .
	 * @param null  $content .
	 *
	 * @return string
	 */
	public function request_quote_page( $atts, $content = null ) {

		$raq_content = YITH_Request_Quote()->get_raq_return();

		$args = shortcode_atts(
			array(
				'raq_content'   => $raq_content,
				'template_part' => 'view',
				'show_form'     => 'yes',
			),
			$atts
		);

		$args['args'] = apply_filters( 'ywraq_request_quote_page_args', $args, $raq_content );
		ob_start();

		wc_get_template( 'request-quote.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );

		return ob_get_clean();
	}

	/**
	 *
	 * Add To Quote Button Shortcode
	 *
	 * @param array $atts .
	 * @param null  $content .
	 *
	 * @return string
	 */
	public function button_quote( $atts, $content = null ) {

		$args = shortcode_atts(
			array(
				'product' => false,
			),
			$atts
		);

		ob_start();
		yith_ywraq_render_button( $args['product'] );

		return ob_get_clean();
	}

	/**
	 * Number Items Shortcode
	 *
	 * @param array $atts .
	 * @param null  $content .
	 *
	 * @return string
	 */
	public function ywraq_number_items( $atts, $content = null ) {

		$atts = shortcode_atts(
			array(
				'class'            => 'ywraq_number_items',
				'show_url'         => 'yes',
				'item_name'        => __( 'item', 'yith-woocommerce-request-a-quote' ),
				'item_plural_name' => __( 'items', 'yith-woocommerce-request-a-quote' ),
			),
			$atts
		);

		$num_items = YITH_Request_Quote()->get_raq_item_number();
		$raq_url   = esc_url( YITH_Request_Quote()->get_raq_page_url() );



		if ( 'yes' === $atts['show_url'] ) {
			$div = sprintf( '<div class="%s" data-show_url="%s" data-item_name="%s" data-item_plural_name="%s"><a href="%s">%d <span>%s</span></a></div>', $atts['class'], $atts['show_url'], $atts['item_name'], $atts['item_plural_name'], $raq_url, $num_items, _n( $atts['item_name'], $atts['item_plural_name'], $num_items, 'yith-woocommerce-request-a-quote' ) );
		} else {
			$div = sprintf( '<div class="%s" data-show_url="%s" data-item_name="%s" data-item_plural_name="%s">%d <span>%s</span></div>', $atts['class'], $atts['show_url'], $atts['item_name'], $atts['item_plural_name'], $num_items, _n( $atts['item_name'], $atts['item_plural_name'], $num_items, 'yith-woocommerce-request-a-quote' ) );
		}

		return $div;
	}

	/**
	 * Add Quotes section to my-account page
	 *
	 * @since   1.0.0
	 */
	public function my_account_raq_shortcode() {

		ob_start();
		wc_get_template( 'myaccount/my-quotes.php', null, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );

		return ob_get_clean();
	}

	/**
	 * View Quote Shortcode
	 *
	 * @param array $atts .
	 * @param null  $content .
	 *
	 * @return string
	 */
	public function single_view_quote( $atts, $content = null ) {

		$args = shortcode_atts(
			array(
				'order_id' => 0,
			),
			$atts
		);


		ob_start();
		wc_get_template(
			'myaccount/view-quote.php',
			array(
				'order_id'     => $args['order_id'],
				'current_user' => get_user_by( 'id', get_current_user_id() ),
			),
			'',
			YITH_YWRAQ_TEMPLATE_PATH . '/'
		);

		return ob_get_clean();
	}

	/**
	 * Quote List Widget
	 *
	 * @param array $atts .
	 * @param null  $content .
	 *
	 * @return string
	 */
	public function widget_quote( $atts, $content = null ) {


		$args = shortcode_atts(
			array(
				'title'           => esc_html__( 'Quote List', 'yith-woocommerce-request-a-quote' ),
				'show_thumbnail'  => true,
				'show_price'      => true,
				'show_quantity'   => true,
				'show_variations' => true,
			),
			$atts
		);

		$args['args'] = $args;

		ob_start();

		the_widget( 'YITH_YWRAQ_List_Quote_Widget', $args );

		return ob_get_clean();
	}

	/**
	 * Quote List Mini Widget
	 *
	 * @param array $atts .
	 * @param null  $content .
	 *
	 * @return string
	 */
	public function mini_widget_quote( $atts, $content = null ) {

		$args = shortcode_atts(
			array(
				'title'             => esc_html__( 'Quote List', 'yith-woocommerce-request-a-quote' ),
				'item_name'         => esc_html__( 'item', 'yith-woocommerce-request-a-quote' ),
				'item_plural_name'  => esc_html__( 'items', 'yith-woocommerce-request-a-quote' ),
				'show_thumbnail'    => 1,
				'show_price'        => 1,
				'show_quantity'     => 1,
				'show_variations'   => 1,
				'show_title_inside' => 0,
				'button_label'      => esc_html__( 'View list', 'yith-woocommerce-request-a-quote' ),
			),
			$atts
		);

		$args['args'] = $args;

		ob_start();

		the_widget( 'YITH_YWRAQ_Mini_List_Quote_Widget', $args );

		return ob_get_clean();
	}
}
