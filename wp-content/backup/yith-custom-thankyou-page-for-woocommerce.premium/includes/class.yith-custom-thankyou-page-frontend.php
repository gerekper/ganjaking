<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Custom_Thankyou_Page_Frontend' ) ) {
	/**
	 * Frontend class
	 *
	 * The class manage all the frontend behaviors.
	 *
	 * @class      YITH_Custom_Thankyou_Page_Frontend
	 * @package    YITH Custom ThankYou Page for Woocommerce
	 * @since      1.0.0
	 * @author     YITH
	 */
	class YITH_Custom_Thankyou_Page_Frontend {
		/**
		 * WooCommerce version.
		 *
		 * @var string current wc version
		 * @since      1.0.0
		 */
		public $yith_ctw_wc_version = '';

		/**
		 * General ThankYou page ID
		 *
		 * @var int general thankyou page id
		 * @since      1.0.0
		 */
		public $ctpw_general_page = '';

		/**
		 * Initialize frontend class
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->yith_ctw_wc_version = $this->yith_ctpw_check_woocommerce_version();

			// register the style
			// the file is loaded by the function yith_custom_thankyou_page so it load only on custom thank you page.
			wp_register_style( 'yith-ctpw-style', YITH_CTPW_ASSETS_URL . 'css/style.css', null, true, 'all' );

			// get general page id.
			$this->ctpw_general_page = ( get_option( 'yith_ctpw_general_page' ) ) ? get_option( 'yith_ctpw_general_page' ) : 0;

			// set the thank you page redirect.
			add_action( 'woocommerce_thankyou', array( $this, 'yith_ctpw_redirect_after_purchase' ) );

			// Add the content and body class filters only after the redirect so we are on custom thank you page for sure.
			if ( isset( $_GET['order-received'] ) && isset( $_GET['key'] ) && isset( $_GET['ctpw'] ) ) { //phpcs:ignore
				// set the_content filter to customize the page if it is selected as thank you page.
				add_filter( 'the_content', array( $this, 'yith_custom_thankyou_page' ) );
				add_filter( 'body_class', array( $this, 'body_class_front' ) );
			}

			// add woocommerce parts: header, order table, customer details.
			if ( apply_filters( 'yith_ctpw_show_header_filter', true ) ) {
				add_action( 'yith_ctpw_successful_ac', array( $this, 'yith_ctpw_header' ), 10 );
			}
			if ( apply_filters( 'yith_ctpw_show_table_filter', true ) ) {
				add_action( 'yith_ctpw_successful_ac', array( $this, 'yith_ctpw_table' ), 20 );
			}
			if ( apply_filters( 'yith_ctpw_show_details_filter', true ) ) {
				add_action( 'yith_ctpw_successful_ac', array( $this, 'yith_ctpw_customer_details' ), 30 );
			}

				// load the failed template.
				add_action( 'yith_ctpw_failed_ac', array( $this, 'yith_ctpw_failed' ), 10 );

		}

		/**
		 * Initialize frontend class
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 */
		public function yith_ctpw_check_woocommerce_version() {
				global $woocommerce;
				return $woocommerce->version;
		}

		/**
		 * Redirect Function to Custom Thank you page
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 * @return void
		 * @param integer $order Order id.
		 */
		public function yith_ctpw_redirect_after_purchase( $order ) {

			// if no global custom thank you page is set or no single product custom thank you page is set, not redirect needed.
			if ( 0 !== $this->ctpw_general_page ) {
				// get order object.
				$check_order = wc_get_order( intval( $order ) );

				$thankyoupage = get_permalink( get_option( 'yith_ctpw_general_page' ) );

				$selected_thankyou_page = get_option( 'yith_ctpw_general_page' );

				// making the url redirect.
				$order_key = wc_clean( $_GET['key'] ); //phpcs:ignore
				$redirect  = $thankyoupage;
				$redirect .= get_option( 'permalink_structure' ) === '' ? '&' : '?';
				$redirect .= 'order=' . absint( $order ) . '&key=' . $order_key . '&ctpw=' . $selected_thankyou_page;

				wp_safe_redirect( $redirect );
				exit();
			}
		}

		/**
		 * Custom Thank You Page Filter function
		 *
		 * Filter the_content and if it is the custom selected page add the templates
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 * @param string $content Page Content.
		 * @return string $content Managed Page Content
		 */
		public function yith_custom_thankyou_page( $content ) {
			global $is_ctpw_page;
			$is_ctpw_page = false;
			// check if the order ID exists and if is set the order key.
			if (! isset($_GET['order-received']) || ! isset($_GET['key'])) { //phpcs:ignore
				return $content;
			}

			// get order object; intval() ensures that we use an integer value for the order ID.
			$order = wc_get_order( intval( $_GET['order-received'] ) ); //phpcs:ignore

			// order exists check the order key passed is isset and if it is the same of the order.
			if ( version_compare( $this->yith_ctw_wc_version, '2.7', '<' ) ) {
				$ctpw_order_key = $order->order_key;
			} else {
				$ctpw_order_key = $order->get_order_key();
			}

			if (isset($_GET['key']) && $_GET['key'] !== $ctpw_order_key ) { //phpcs:ignore
				return $content;
			}

			// check if the custom thank yuo page ID exists.
			if (! isset($_GET['ctpw'])) { //phpcs:ignore
				return $content;
			}

			// Check if is the correct page: general thankyou or product related custom thankyou page.
			if ( get_post_type( $_GET['ctpw'] ) !== 'page' ) {//phpcs:ignore
				return $content;
			}

			// we are on custom thank you page.

			$is_ctpw_page = true;


			// load the plugin style.
			wp_enqueue_style( 'yith-ctpw-style' );

			ob_start();



			// Check that the order is valid.
			if ( ! $order ) {
				// The order can't be returned by WooCommerce - Just say thank you.
				?>
				<p>
				<?php
					// APPLY_FILTER: woocommerce_thankyou_order_received_text: Change the Order Received Text.
					echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); //phpcs:ignore XSS.
				?>
				</p>
				<?php
			} else {
				if ( $order->has_status( 'failed' ) ) {
					// Order failed - Print error messages and ask to pay again.
					/**
					* Order Failed Hook.
					*
					* @hooked wc_custom_thankyou_failed - 10
					*/
					// DO_ACTION yith_ctpw_failed_ac: hook on failed operation: provide $order object.
					do_action( 'yith_ctpw_failed_ac', $order );
				} else {
					// The order is successfull - print section as selected in admin settings.
					/**
					* Succesfull Checkout Hooks
					*
					* @hooked yith_ctpw_header - 10
					* @hooked yith_ctpw_table - 20
					* @hooked yith_ctpw_customer_details - 30
					* @hooked yith_ctpw_social_box - 40
					*/
					// DO_ACTION yith_ctpw_successful_ac: hook successful checkout: provide $order object.
					do_action( 'yith_ctpw_successful_ac', $order );
				}
			}

			// if the order is fail we avoid the custom page content.
			if ( $order->has_status( 'failed' ) ) {
				$content = ob_get_contents();
			} else {
				$content .= ob_get_contents();
			}

			ob_end_clean();

			// if there's custom style we add it to the page.
			if ( get_option( 'yith_ctpw_custom_style' ) !== '' ) {
				$content .= '<style>' . esc_html( get_option( 'yith_ctpw_custom_style' ) ) . '</style>';
			}

			return $content;
		} // end content filter function.


		/**
		 * Load the Order Header Table template file
		 *
		 * @param object $order woocommerce order.
		 * @since 1.0.0
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @return void
		 */
		public function yith_ctpw_header( $order ) {
			wc_get_template( 'yith_ctpw_header.php', array( 'order' => $order ), '', YITH_CTPW_TEMPLATE_PATH . 'woocommerce/' );
		}

		/**
		 * Load the Order Cart Table template file
		 *
		 * @param object $order woocommerce order.
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 */
		public function yith_ctpw_table( $order ) {
			wc_get_template( 'yith_ctpw_table.php', array( 'order' => $order ), '', YITH_CTPW_TEMPLATE_PATH . 'woocommerce/' );
		}

		/**
		 * Load the Order Cutomer Details Table template file
		 *
		 * @param object $order woocommerce order.
		 * @since 1.0.0
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @return void
		 */
		public function yith_ctpw_customer_details( $order ) {
			wc_get_template( 'yith_ctpw_customer_details.php', array( 'order' => $order ), '', YITH_CTPW_TEMPLATE_PATH . 'woocommerce/' );
		}

		/**
		 * Load the Order Failed template file
		 *
		 * @param object $order woocommerce order.
		 * @since 1.0.0
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @return void
		 */
		public function yith_ctpw_failed( $order ) {
			wc_get_template( 'yith_ctpw_failed.php', array( 'order' => $order ), '', YITH_CTPW_TEMPLATE_PATH . 'woocommerce/' );
		}

		/**
		 * Add a woocommerce checkout body class(es) to Custom Thank you page
		 *
		 * @param array $classes .
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.0.0
		 * @return array $classes
		 */
		public function body_class_front( $classes ) {
			$ctpw_classes = array( 'yith-ctpw-front', 'woocommerce', 'woocommerce-checkout', 'woocommerce-order-received', 'woocommerce-page' );

			$classes = array_merge( $classes, $ctpw_classes );
			return $classes;
		}
	} // end class.

}
