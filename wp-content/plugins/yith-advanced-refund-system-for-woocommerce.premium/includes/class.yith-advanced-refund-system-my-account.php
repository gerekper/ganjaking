<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Advanced_Refund_System_My_Account
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System_My_Account' ) ) {
	/**
	 * Class YITH_Advanced_Refund_System_My_Account
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Advanced_Refund_System_My_Account {
		/**
		 * My Refund Requests endpoint name.
		 *
		 * @var string
		 */
		public static $my_refund_requests_endpoint = 'refund-requests';

		/**
		 * View Request endpoint name.
		 *
		 * @var string
		 */
		public static $view_request_endpoint = 'view-request';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function __construct() {
		    self::$my_refund_requests_endpoint = apply_filters( 'yith_ywcars_my_refund_requests_endpoint', self::$my_refund_requests_endpoint );
			$this->add_endpoints();
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_filter( 'the_title', array( $this, 'endpoint_title' ) );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_my_refund_requests_link' ) );
			add_action( 'woocommerce_account_' . self::$my_refund_requests_endpoint . '_endpoint', array( $this, 'my_refund_requests_content' ) );
			add_action( 'woocommerce_account_' . self::$view_request_endpoint . '_endpoint', array( $this, 'view_request_content' ) );
			add_shortcode( 'ywcars_refund_requests', array( $this, 'refund_requests_shortcode' ) );
			add_shortcode( 'ywcars_view_request', array( $this, 'view_request_shortcode' ) );
		}

		public function add_endpoints() {
			add_rewrite_endpoint( self::$my_refund_requests_endpoint, EP_ROOT | EP_PAGES );
			add_rewrite_endpoint( self::$view_request_endpoint, EP_ROOT | EP_PAGES );
		}

		public function add_query_vars( $vars ) {

			$vars[] = self::$my_refund_requests_endpoint;
			$vars[] = self::$view_request_endpoint;
			return $vars;
		}

		public function endpoint_title( $title ) {
			global $wp_query;

			$is_my_refund_requests = isset( $wp_query->query_vars[ self::$my_refund_requests_endpoint ] );
			$is_view_request = isset( $wp_query->query_vars[ self::$view_request_endpoint ] );

			if ( $is_my_refund_requests && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				$title = apply_filters( 'ywcars_my_refund_requests_text', esc_html__( 'My Refund Requests', 'yith-advanced-refund-system-for-woocommerce' ) );
				remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
			} else if ( $is_view_request && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				$request_id = ! empty( $wp_query->query_vars['view-request'] ) ? $wp_query->query_vars['view-request'] : '';
				$request = new YITH_Refund_Request( $request_id );
				if ( $request->exists() ) {
					$title = sprintf( esc_html__( 'Request #%s', 'yith-advanced-refund-system-for-woocommerce' ), $request_id );
					remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
				}
			}

			return $title;
		}

		public function add_my_refund_requests_link( $items ) {
			$items[self::$my_refund_requests_endpoint] = apply_filters( 'ywcars_my_refund_requests_text', esc_html__( 'My Refund Requests', 'yith-advanced-refund-system-for-woocommerce' ) );
			// If there is logout endpoint, unset it and set it again to place it the last
			if ( isset( $items['customer-logout'] ) ) {
			    $logout = $items['customer-logout'];
			    unset( $items['customer-logout'] );
				$items['customer-logout'] = $logout;
			}
			return $items;
		}

		public function my_refund_requests_content() {
			$request_ids = ywcars_get_requests_by_customer_id( get_current_user_id() );
			wc_get_template( 'myaccount/my-refund-requests.php',
				array( 'request_ids' => $request_ids ),
				'',
				YITH_WCARS_TEMPLATE_PATH . '/' );
		}

		public function refund_requests_shortcode( $atts ){
            $request_ids = ywcars_get_requests_by_customer_id( get_current_user_id() );
            ob_start();
            wc_get_template( 'myaccount/my-refund-requests.php',
                array( 'request_ids' => $request_ids ),
                '',
                YITH_WCARS_TEMPLATE_PATH . '/' );

            return ob_get_clean();
        }

		public function view_request_content() {
			global $wp_query;

			$request_id = ! empty( $wp_query->query_vars['view-request'] ) ? $wp_query->query_vars['view-request'] : '';
			$request = new YITH_Refund_Request( $request_id );
			if ( $request->exists() ) {

				if ( ! current_user_can( 'view_order', $request->order_id ) ) {
					echo '<div class="woocommerce-error">' . esc_html__( 'Invalid refund request.', 'yith-advanced-refund-system-for-woocommerce' ) . ' <a href="' . wc_get_page_permalink( 'myaccount' ) . '" class="wc-forward">' . esc_html__( 'My account', 'yith-advanced-refund-system-for-woocommerce' ) . '</a>' . '</div>';
					return;
				}

				wc_get_template( 'myaccount/view-request.php',
					array( 'request_id' => $request_id ),
					'',
					YITH_WCARS_TEMPLATE_PATH . '/' );
			} else {
				echo '<div class="woocommerce-error">' . esc_html__( 'Invalid refund request.', 'yith-advanced-refund-system-for-woocommerce' ) . ' <a href="' . wc_get_page_permalink( 'myaccount' ) . '" class="wc-forward">' . esc_html__( 'My account', 'yith-advanced-refund-system-for-woocommerce' ) . '</a>' . '</div>';
            }
		}

		public function view_request_shortcode( $atts ) {
			$fields = shortcode_atts( array(
				'id' => 0,
			), $atts );
			if ( ! empty( $fields['id'] ) ) {
				$request = new YITH_Refund_Request( $fields['id'] );

				// Check first if request exists or isn't at trash
                if ( ! $request->exists() || 'trash' == $request->status ) {
	                echo '<div class="woocommerce-error">' . esc_html__( 'Invalid refund request.', 'yith-advanced-refund-system-for-woocommerce' ) . ' <a href="' . wc_get_page_permalink( 'myaccount' ) . '" class="wc-forward">' . esc_html__( 'My account', 'yith-advanced-refund-system-for-woocommerce' ) . '</a>' . '</div>';
	                return;
                }

				$is_whole_order = $request->whole_order;
				$order = wc_get_order( $request->order_id );
				$order_link = $order->get_view_order_url();
                ?>

                <input type="hidden" id="ywcars_request_id" value="<?php echo $request->ID; ?>">

                <table class="ywcars_refund_info">
                    <tbody>
                    <tr>
                        <td>
                            <span><?php esc_html_e( 'Requested:', 'yith-advanced-refund-system-for-woocommerce' ) . ' '; ?></span>
		                    <?php if ( $is_whole_order ) : ?>
                                <span class="ywcars_bold"><?php esc_html_e( 'Whole order', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
		                    <?php else : ?>
			                    <?php
			                    $product = wc_get_product( $request->product_id );
			                    $product_name = $product->get_title();
			                    $product_link = '<a href="' . $product->get_permalink() . '">' . $product_name . '</a>';
			                    ?>
                                <span style="display: inline-block"><?php printf(
					                    _n( '%s (%d unit).', '%s (%d units).', $request->qty, 'yith-advanced-refund-system-for-woocommerce' ),
					                    $product_link,
					                    $request->qty ); ?></span>
		                    <?php endif; ?>
                        </td>
                        <td class="ywcars_refund_info_minor_cell">
                            <a class="ywcars_reduced_text_size" href="<?php echo $order_link; ?>">
                                <span style="font-size: 8pt;"><?php esc_html_e( 'View Order', 'yith-advanced-refund-system-for-woocommerce' ) . ' >'; ?></span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="ywcars_current_status <?php echo $request->status; ?>">
                                <span><?php esc_html_e( 'Current status: ', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
                                <span class="ywcars_bold"><?php echo 'ywcars-new' == $request->status ? esc_html__( 'Submitted', 'yith-advanced-refund-system-for-woocommerce' ) : ywcars_get_request_status_by_key( $request->status ); ?></span>
                            </span>
                            <div class="ywcars_reduced_text_size"><?php esc_html_e( 'Request date:', 'yith-advanced-refund-system-for-woocommerce' ); ?></div>

                            <div class="ywcars_reduced_text_size ywcars_bold"><?php echo ' ' . apply_filters('ywcars_datetime', get_post( $request->ID )->post_date) ?></div>

                        </td>
                    </tr>
                    <?php if ( 'ywcars-coupon' == $request->status && $request->coupon_id ) : ?>
	                    <?php
	                    $post = get_post( $request->coupon_id );
	                    $coupon_code = ! empty( $post->post_title ) ? $post->post_title : esc_html__( 'The coupon code does no longer exists', 'yith-advanced-refund-system-for-woocommerce' );
	                    $coupon_amount = get_post_meta( $request->coupon_id, 'coupon_amount', true );
	                    ?>
                        <tr>
                            <td>
                                <div class="ywcars_reduced_text_size"><?php esc_html_e( 'Coupon code:', 'yith-advanced-refund-system-for-woocommerce' ) ?></div>
                                <div class="ywcars_bold"><?php echo $coupon_code; ?></div>
                            </td>
                            <td class="ywcars_refund_info_minor_cell">
                                <div class="ywcars_reduced_text_size"><?php echo esc_html_x( 'value', 'Value of the coupon', 'yith-advanced-refund-system-for-woocommerce' ) ?></div>
                                <div class="ywcars_bold" style="font-size: 14pt;"><?php echo ! empty( $coupon_amount ) ? wc_price( $coupon_amount ) : esc_html_x( 'N/A', 'Coupon amount value', 'yith-advanced-refund-system-for-woocommerce' ); ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>



				<?php if ( ! $request->is_closed ) : ?>
                    <div class="ywcars_refund_info_add_mesage">
                        <form id="ywcars_form_my_account_new_message" method="post" enctype="multipart/form-data">
                            <div class="ywcars_refund_info_block_title"><?php esc_html_e( 'Add a message', 'yith-advanced-refund-system-for-woocommerce' ); ?></div>
                            <textarea id="ywcars_new_message" name="ywcars_new_message"
                                      title="<?php esc_html_e( 'Write a message you want to sent to the Shop Manager.',
                                          'yith-advanced-refund-system-for-woocommerce' ); ?>"
                                      rows="5"></textarea>
                            <div class="ywcars_block">
                                <input type="submit" class="button button-primary"
                                       id="ywcars_send_message" style="float: right;"
                                       value="<?php esc_html_e( 'Submit', 'yith-advanced-refund-system-for-woocommerce' ); ?>">
                                <?php do_action( 'ywcars_view_request_after_submit' ); ?>
                            </div>
                            <div class="ywcars_block">
                                <div class="ywcars_alert ywcars_success_alert">
                                    <span class="ywcars_close_alert">x</span>
                                    <span class="ywcars_alert_content"></span>
                                </div>
                                <div class="ywcars_alert ywcars_error_alert">
                                    <span class="ywcars_close_alert">x</span>
                                    <span class="ywcars_alert_content"></span>
                                </div>
                            </div>
                        </form>
                    </div>
				<?php else : ?>
                    <div>
                        <span><?php esc_html_e( 'This request is closed. You cannot send further messages.', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
                    </div>
				<?php endif; ?>

                <div class="ywcars_refund_info_messages_history">
                    <div class="ywcars_line_separator"></div>
                    <div id="ywcars_message_history" class="ywcars_refund_info_block_title">
                        <span><?php esc_html_e( 'Message history', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
                        <span class="ywcars_update_messages" title="<?php esc_html_e( 'Reload', 'yith-advanced-refund-system-for-woocommerce' ); ?>">&#x21bb;</span>
                    </div>
                    <div class="ywcars_messages_history_frame"><?php YITH_Advanced_Refund_System_Request_Manager::load_messages( $request->ID ); ?></div>
                </div>
				<?php
			}
		}

		/**
		 * Plugin install action.
		 * Flush rewrite rules to make our custom endpoint available.
		 */
		public static function install() {
			flush_rewrite_rules();
		}

	}
}