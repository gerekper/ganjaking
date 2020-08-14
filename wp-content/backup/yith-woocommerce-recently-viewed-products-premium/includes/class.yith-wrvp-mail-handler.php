<?php
/**
 * Mail Handler class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP_Mail_Handler' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WRVP_Mail_Handler {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WRVP_Mail_Handler
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WRVP_VERSION;

		/**
		 * User meta exclude from mailing list
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $_user_meta_exclude = 'yith_wrvp_exclude_mail';

		/**
		 * User meta failed attempts email
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $_failed_attempts = 'yith_wrvp_failed_emails';

		/**
		 * User meta mail sent
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $_mail_sent = 'yith_wrvp_mail_sent';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WRVP_Mail_Handler
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			// unsubscribe user from mailing list
			add_action( 'init', array( $this, 'unsubscribe_from_mailing_list' ), 1 );

			// save user last visit time
			add_action( 'init', array( $this, 'update_user_metas' ), 1 );

			add_action( 'init', array( $this, 'mail_setup_schedule' ), 20 );
			add_action( 'yith_wrvp_mail_action_schedule', array( $this, 'mail_do_action_schedule' ) );

			// Email Templates custom Styles
			add_action( 'yith_wcet_after_email_styles', array( $this, 'email_templates_custom_css' ),10,3 );

			// add test mail
			add_action( 'woocommerce_email_settings_after', array( $this, 'add_test_mail' ), 10, 1 );
			add_action( 'yith_wrvp_mail_after_save_option', array( $this, 'send_test_mail' ), 10 );

			add_action( 'yith_wrvp_mail_sent_correctly', array( $this, 'set_meta_mail_sent' ), 10 );
			add_action( 'yith_wrvp_mail_sent_error', array( $this, 'set_meta_mail_error' ), 10 );

            add_action( 'wp_ajax_yith_wrvp_validate_coupon', array( $this, 'validate_coupon' ) );
		}

		/**
		 * Add custom styles for Email Templates
		 *
		 * @param int $premium_style
		 * @param array $meta
		 * @param WC_Email $current_email
		 */
		public function email_templates_custom_css( $premium_style, $meta, $current_email ) {
			if ( empty( $current_email ) || $current_email->id != 'yith_wrvp_mail' ) {
                return;
            }

			$args = array( 'hide_style_for_email_templates' => true );
			wc_get_template( 'ywrvp-mail-style.php', $args, '', YITH_WRVP_TEMPLATE_PATH . '/email/' );
		}

		/**
		 * Update user metas based on last active timestamp
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function update_user_metas() {
			$user_id    = get_current_user_id();
			$now        = time();
			$last_login = get_user_meta( $user_id, 'wc_last_active', true );

			if( ! $user_id || ( intval( $last_login ) + DAY_IN_SECONDS ) > $now ) {
				return;
			}

            version_compare( '3.4.0', WC()->version ) === 1 && update_user_meta( $user_id, 'wc_last_active', $now ); // backward compatibility with version pre 3.4.0
			delete_user_meta( $user_id, $this->_failed_attempts );
			// reset mail sent
			delete_user_meta( $user_id, $this->_mail_sent );
		}

		/**
		 * Set user meta mail sent
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $customer
		 * @author Francesco Licandro
		 */
		public function set_meta_mail_sent( $customer ) {
		    // get customer if needed
            ! ( $customer instanceof WP_User ) && $customer = get_user_by( 'email', $customer );
			if( ! $customer ) {
				return;
			}

			update_user_meta( $customer->ID, $this->_mail_sent, true );
            delete_user_meta( $customer->ID, $this->_failed_attempts );
		}

		/**
		 * Set user meta failed mail attempts, if greater then 3 set mail sent and delete failed attempts
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $customer
		 * @author Francesco Licandro
		 */
		public function set_meta_mail_error( $customer ) {
            // get customer if needed
            ! ( $customer instanceof WP_User ) && $customer = get_user_by( 'email', $customer );
			if( ! $customer ) {
				return;
			}

			$c = get_user_meta( $customer->ID, $this->_failed_attempts, true );
			if( intval( $c ) < 3 ) {
			    update_user_meta( $customer->ID, $this->_failed_attempts, ++$c );
            }
            else {
                $this->set_meta_mail_sent( $customer );
            }
		}

		/**
		 * Schedule event to send mail to users
		 *
		 * @return void
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function mail_setup_schedule() {
			if ( ! wp_next_scheduled( 'yith_wrvp_mail_action_schedule' ) ) {
                wp_unschedule_hook( 'mail_action_schedule' );
				wp_schedule_event( time(), 'hourly', 'yith_wrvp_mail_action_schedule' );
			}
		}

		/**
		 * Action send mail to users
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function mail_do_action_schedule() {

			global $wpdb;

			$data = array();

			// first get users and products list
			$query = $this->build_query();
			$results = $wpdb->get_results( implode( ' ', $query ) );

			if( empty( $results ) ) {
				return;
			}

			foreach( $results as $result ) {

				if( user_can( $result->ID, 'administrator' ) || apply_filters( 'yith_wrvp_customer_skip_mail', false, $result->ID ) ){
                    update_user_meta( $result->ID, $this->_mail_sent, true ); // skip this email and set it as sent
					continue;
				}

				$data[ $result->user_email ] = maybe_unserialize( $result->meta_value );
			}

			do_action( 'send_yith_wrvp_mail', $data );
		}

		/**
		 * Get users mail and products list from DB
		 *
		 * @access protected
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		protected function build_query(){

			global $wpdb;

			// period
			$period = absint( get_option( 'yith-wrvp-email-period', 7 ) );
			$time = time() - ( $period * DAY_IN_SECONDS );

			$query              = array();
			$query['fields']    = "SELECT a.ID, a.user_email, b.meta_value";
			$query['from']      = "FROM {$wpdb->users} AS a";
			$query['join']      = "INNER JOIN {$wpdb->usermeta} AS b ON ( a.ID = b.user_id )";
			$query['join']      .= "INNER JOIN {$wpdb->usermeta} AS c ON ( c.user_id = b.user_id AND c.meta_key = 'wc_last_active' )";
			$query['where']     = "WHERE a.ID NOT IN ( SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$this->_mail_sent}' OR meta_key = '{$this->_user_meta_exclude}')";
			$query['where']     .= "AND b.meta_key = 'yith_wrvp_products_list'";
			$query['where']     .= "AND b.meta_value NOT LIKE 'a:0:{}'";
			$query['where']     .= "AND c.meta_value > 0 AND c.meta_value < {$time}";
			$query['limit']     = "LIMIT 20";

			$query['group'] = "";

			return $query;
		}

		/**
		 * Add test mail box
		 *
		 * @access public
		 * @since 1.0.0
		 * @param $mail
		 * @author Francesco Licandro
		 */
		public function add_test_mail( $mail ) {

			if( ! $mail || $mail->id != 'yith_wrvp_mail' ){
				return;
			}

			ob_start();
			?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="yith-wrvp-test-mail"><?php esc_html_e( 'Test Email', 'yith-woocommerce-recently-viewed-products' ) ?></label>
						</th>
						<td>
							<input type="email" id="yith-wrvp-test-mail" name="yith-wrvp-test-mail"
								   placeholder="<?php esc_html_e( 'Type an email address to send a test email', 'yith-woocommerce-recently-viewed-products' ) ?>" />
							<input type="hidden" name="is-yith-wrvp-test-mail" value="">
							<button type="submit" class="button-secondary ywrvp-send-test-email"><?php esc_html_e( 'Send email', 'yith-woocommerce-recently-viewed-products' ) ?></button>
						</td>
					</tr>
				</tbody>
			</table>

			<?php

			echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Send test mail action
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function send_test_mail(){

			if( ! ( isset( $_POST['yith-wrvp-test-mail'] ) && $_POST['yith-wrvp-test-mail'] != '' )
				|| ! ( isset( $_POST['is-yith-wrvp-test-mail' ] ) && $_POST['is-yith-wrvp-test-mail'] == 'true' ) ) {
				return;
			}

			// check also if is mail correct
			if( ! is_email( $_POST['yith-wrvp-test-mail'] ) ) {
				return;
			}

			$data[ $_POST['yith-wrvp-test-mail'] ] = array();

			do_action( 'send_yith_wrvp_mail', $data );

		}

		/**
		 * Create coupon for the email
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $user
		 * @param array $products
		 * @param string $expire
		 * @param string $value
		 * @return string
		 * @author Francesco Licandro
		 */
		public function add_coupon_to_mail( $user, $products, $expire, $value ) {

			if( ! $value || ! $expire ) {
				return '';
			}

			// make sure expire and value is number positive
			$value 	= abs( $value );
			$expire = abs( $expire );

			$prefix = apply_filters( 'yith_wrvp_prefix_coupon', 'ywrvp_' );
			$coupon_code   = uniqid( $prefix ); // Code

			if( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$coupon_data = array(
					'code'                      => $coupon_code,
					'amount'                    => $value,
					'date_expires'              => date( 'Y-m-d', $expire ),
					'discount_type'             => 'percent',
					'individual_use'            => apply_filters('yith_wrvp_coupon_individual_use',true),
					'product_ids'               => implode( ',', $products ),
					'usage_limit'               => 1,
					'limit_usage_to_x_items'    => 1
				);
				$coupon_object = new WC_Coupon( $coupon_code );
				$coupon_object->read_manual_coupon( $coupon_code, $coupon_data );
				$coupon_object->save();
			}
			else {

				$new_coupon_id = wp_insert_post( array(
					'post_title' => $coupon_code,
					'post_content' => '',
					'post_status' => 'publish',
					'post_author' => 1,
					'post_type'		=> 'shop_coupon'
				) );

				update_post_meta( $new_coupon_id, 'discount_type', 'percent_product' );
				update_post_meta( $new_coupon_id, 'coupon_amount', $value );
				update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
				update_post_meta( $new_coupon_id, 'product_ids', implode( ',', $products ) );
				update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
				update_post_meta( $new_coupon_id, 'usage_limit', '1' );
				update_post_meta( $new_coupon_id, 'usage_limit_per_user', '0' );
				update_post_meta( $new_coupon_id, 'limit_usage_to_x_items', '1' );
				update_post_meta( $new_coupon_id, 'expiry_date', date( 'Y-m-d', $expire ) );
				update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
			}

			return $coupon_code;

		}

		/**
		 * Add success/fail message after send test mail
		 *
		 * @access public
		 * @since 1.0.0
		 * @param object $email
		 * @author Francesco Licandro
		 */
		public function add_test_mail_message( $email ){

			if( ! isset( $_GET['page'] ) || $_GET['page'] != 'yith_wrvp_panel' ) {
				return;
			}

			$type = $email->_test_msg_type;

			$notice = $type == 'success' ? __( 'Test email sent correctly!', 'yith-woocommerce-recently-viewed-products' ) : __( 'An error has occurred. Please try again.', 'yith-woocommerce-recently-viewed-products' );

			$notice = apply_filters( 'yith_wrvp_test_mail_' . $type . '_notice', $notice );

			echo '<div class="notice notice-' . esc_attr( $type ) . '"><p>' . esc_html( $notice ) . '</p></div>';
		}

		/**
		 * Parse args with default options for options type
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $data
		 * @return array
		 * @author Francesco Licandro
		 * @deprecated Use function ywrvp_parse_with_default instead
		 */
		public function parse_with_default( $data ) {
			return ywrvp_parse_with_default( $data );
		}

		/**
		 * Print select products html for email options
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $key
		 * @param array $data
		 * @param object $email
		 * @return string
		 * @author Francesco Licandro
		 * @deprecated Use function ywrvp_email_select_products_html instead
		 */
		public function select_products_html( $key, $data, $email ) {
			return ywrvp_email_select_products_html( $key, $data, $email );
		}

		/**
		 * Print textarea editor html for email options
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $key
		 * @param array $data
		 * @param object $email
		 * @return string
		 * @author Francesco Licandro
		 * @deprecated Use function ywrvp_email_textarea_editor_html instead
		 */
		public function textarea_editor_html( $key, $data, $email ) {
			return ywrvp_email_textarea_editor_html( $key, $data, $email );
		}

		/**
		 * Print upload type html for email options
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $key
		 * @param array $data
		 * @param object $email
		 * @return string
		 * @author Francesco Licandro
		 * @deprecated Use function ywrvp_email_upload_html instead
		 */
		public function upload_html( $key, $data, $email ) {
			return ywrvp_email_upload_html( $key, $data, $email );
		}

		/**
		 * Unsubscribe user from mailing list
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function unsubscribe_from_mailing_list(){
			if( ! isset( $_GET['action'] ) || $_GET['action'] !== 'yith_wrvp_unsubscribe_from_list' || ! isset( $_GET['customer'] ) ){
				return;
			}

			$user_id = $this->find_user_md5( $_GET['customer'] );

			if( is_null( $user_id ) ){
				return;
			}

			update_user_meta( $user_id, $this->_user_meta_exclude, true );

            wc_add_notice( __( 'You have been unsubscribed from our mailing list successfully.', 'yith-woocommerce-recently-viewed-products' ), 'success' );
			$url = is_user_logged_in() ? wc_get_page_permalink( 'myaccount' ) : wc_get_page_permalink( 'shop' );

			wp_safe_redirect( $url );
			exit();
		}

		/**
		 * Find user by md5 id
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function find_user_md5( $md5_id ) {

			global $wpdb;

			$query           = array();
			$query['fields'] = "SELECT a.ID FROM {$wpdb->users} a";
			$query['where'] = " WHERE MD5(a.ID) = '$md5_id'";

			$query = apply_filters( 'yith_wrvp_query_md5user_param', $query );

			$results = $wpdb->get_var( implode( ' ', $query ) );

			return $results;
		}

		/**
		 * Get products list html
		 *
		 * @access public
		 * @since 1.2.0
		 * @param array $products
		 * @param bool $is_custom
		 * @param string|bool $cat_id
		 * @param object $email
		 * @return mixed
		 * @author Francesc Licandro
		 */
		public function get_products_list_html( $products, $is_custom, $cat_id, $email ) {

			$args = apply_filters('yith_wrvp_similar_products_template_args', array(
				'post_type' => 'product',
				'ignore_sticky_posts' => 1,
				'post_status' => 'publish',
				'no_found_rows' => 1,
				'posts_per_page' => $email->get_option( 'number_products', '5' ),
				'order' => 'DESC'
			));

			if( ! empty( $products ) ) {
				$args['post__in'] = $products;
			}

			if( $cat_id ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'id',
						'terms' => $cat_id
					)
				);
			}

			// hide free
			if( get_option( 'yith-wrvp-hide-free' ) == 'yes' ) {
				$args['meta_query'] = array(
					array(
						'key'     => '_price',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'DECIMAL'
					)
				);
			}

			if( get_option( 'yith-wrvp-hide-out-of-stock' ) == 'yes' ) {
				$args['meta_query'][] = array(
					array(
						'key' 		=> '_stock_status',
						'value' 	=> 'instock',
						'compare' 	=> '='
					)
				);
			}

			$order = $email->get_option( 'products_order', 'rand' );

			switch( $order ) {
				case 'sales':
					$args['meta_key'] = 'total_sales';
					$args['orderby']  = 'meta_value_num';
					break;
				case 'newest':
					$args['orderby'] = 'date';
					break;
				case 'high-low':
					$args['meta_key'] = '_price';
					$args['orderby']  = 'meta_value_num';
					break;
				case 'low-high':
					$args['meta_key'] = '_price';
					$args['orderby']  = 'meta_value_num';
					$args['order'] = 'ASC';
					break;
				default:
					$args['orderby']  = 'rand';
					break;
			}

			// visibility query condition
			$args = yit_product_visibility_meta( $args );

			$products = new WP_Query( $args );

			$template_name = $is_custom ? 'ywrvp-mail-custom-products-list.php' : 'ywrvp-mail-products-list.php';

			ob_start();

			if ( $products->have_posts() ) {

				wc_get_template( $template_name, array( 'products' => $products ), '', YITH_WRVP_TEMPLATE_PATH . '/email/' );
			}

			return ob_get_clean();
		}

		/**
		 * Get unsubscribe from mailing list link
		 *
		 * @access public
		 * @since 1.2.0
		 * @param $customer_mail
		 * @param $label
		 * @param $is_test
		 * @return string
		 * @author Francesco Licandro
		 */
		public function get_unsubscribe_link( $customer_mail, $label = '', $is_test = false ){

			$customer = get_user_by( 'email', $customer_mail );
			// if customer not exists return empty string
			if( ! $customer || $is_test ) {
				return '<a href="#">' . $label . '</a>';
			}

			$id = md5( $customer->ID );
			$url = apply_filters( 'yith_wrvp_unsubscribe_link_url', home_url() );

			$url = esc_url_raw( add_query_arg( array(
				'action' => 'yith_wrvp_unsubscribe_from_list',
				'customer' => $id ), $url ) );

			return '<a href="' . $url .'">' . $label . '</a>';
		}

		/**
         * Validate coupon in ajax
         *
         * @since 1.4.2
         * @author Francesco Licandro
         * @return void
         */
		public function validate_coupon(){
		    if( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != 'yith_wrvp_validate_coupon'
                || empty( $_REQUEST['code'] ) || ! class_exists( 'WC_Coupon' ) ) {
		        die();
            }

            $code   = wc_format_coupon_code( $_REQUEST['code'] );
            $id     = wc_get_coupon_id_by_code( $code );
            $coupon = new WC_Coupon( $code );
            $expire = $coupon->get_date_expires();

            wp_send_json( array(
                'valid' => $id && ( is_null( $expire ) || $expire->getTimestamp() > time() )
            ) );
        }
	}
}
/**
 * Unique access to instance of YITH_WRVP_Mail_Handler class
 *
 * @return \YITH_WRVP_Mail_Handler
 * @since 1.0.0
 */
function YITH_WRVP_Mail_Handler(){
	return YITH_WRVP_Mail_Handler::get_instance();
}