<?php
/**
 * WooCommerce Chase Paymentech
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_1 as Framework;

/**
 * Handle the Chase certification process.
 *
 * @since 1.8.0
 */
class WC_Chase_Paymentech_Certification_Handler {


	/** @var \WC_Gateway_Chase_Paymentech the gateway object **/
	protected $gateway;


	/**
	 * Construct the class.
	 *
	 * @since 1.8.0
	 * @param \WC_Gateway_Chase_Paymentech $gateway the gateway object
	 */
	public function __construct( WC_Gateway_Chase_Paymentech $gateway ) {

		$this->gateway = $gateway;

		/** Checkout **/

		// determine if orders need payment in certification mode
		add_filter( 'woocommerce_order_needs_payment', array( $this, 'order_needs_payment' ), 10, 3 );

		// mark orders "Completed" directly after payment
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'complete_order' ), 10, 2 );

		// display the certification test results on the Thank You page
		add_action( 'woocommerce_before_template_part', array( $this, 'display_test_results' ), 10, 4 );

		// remove the "order received" text on the "Thank You" page
		add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'remove_order_received_text' ), 10, 2 );

		/** My Payment Methods **/

		// add the Customer Ref # header to the My Payment Methods table
		add_filter( 'wc_' . $this->get_gateway()->get_plugin()->get_id() . '_my_payment_methods_table_headers', array( $this, 'add_payment_method_ref_number_header' ), 10 );

		// display the Customer Ref # in the My Payment Methods table
		add_filter( 'wc_' . $this->get_gateway()->get_plugin()->get_id() . '_my_payment_methods_table_body_row_data', array( $this, 'add_payment_method_ref_number_data' ), 10, 2 );

		/** Admin **/

		// enqueue the admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		// add custom admin order list table columns
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'admin_add_columns' ), 15 );

		// display the custom admin order list table column content
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'admin_display_column_content' ), 5 );

		// add the certification test ID to the admin search fields
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'admin_add_order_search_fields' ) );
	}


	/** Checkout Methods ******************************************************/


	/**
	 * Display the test case options as payment fields at checkout.
	 *
	 * @since 1.8.0
	 */
	public function display_payment_fields() {

		echo '<p>' . strtoupper( esc_html__( 'Orbital Certification Mode', 'woocommerce-gateway-chase-paymentech' ) ) . '</p>';

		echo '<p>' . __( 'Please fill in the test case details below:', 'woocommerce-gateway-chase-paymentech' ) . '</p>';

		$id = 'wc-' . $this->get_gateway()->get_id_dasherized();

		// get the previous test's data
		$test_details = WC()->session->get( 'wc_chase_paymentech_certification_test_details', array(
			'label'            => '',
			'transaction_type' => '',
			'amount'           => 0,
		) );

		?>

		<p class="form-row">
			<label for="<?php echo esc_attr( $id . '-test-transaction-type' ); ?>" style="display:inline;"><?php esc_html_e( 'Transaction Type', 'woocommerce-gateway-chase-paymentech' ); ?> <abbr class="required" title="required">*</abbr></label>
			<select name="<?php echo esc_attr( $id . '-test-transaction-type' ); ?>" id="<?php echo esc_attr( $id . '-test-transaction-type' ); ?>">
				<option value="auth_only" <?php selected( 'auth_only', $test_details['transaction_type'] ); ?>><?php esc_html_e( 'Auth Only', 'woocommerce-gateway-chase-paymentech' ); ?></option>
				<option value="auth_capture" <?php selected( 'auth_capture', $test_details['transaction_type'] ); ?>><?php esc_html_e( 'Auth/Capture', 'woocommerce-gateway-chase-paymentech' ); ?></option>
			</select>
		</p>

		<p class="form-row">
			<label for="<?php echo esc_attr( $id . '-test-amount' ); ?>"><?php esc_html_e( 'Test Amount', 'woocommerce-gateway-chase-paymentech' ); ?> <abbr class="required" title="required">*</abbr></label>
			<input type="text" id="<?php echo esc_attr( $id . '-test-amount' ); ?>" name="<?php echo esc_attr( $id . '-test-amount' ); ?>" value="<?php echo Framework\SV_WC_Helper::number_format( $test_details['amount'] ); ?>" />
		</p>

		<p class="form-row">
			<label for="<?php echo esc_attr( $id . '-test-label' ); ?>"><?php esc_html_e( 'Test Label', 'woocommerce-gateway-chase-paymentech' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $id . '-test-label' ); ?>" name="<?php echo esc_attr( $id . '-test-label' ); ?>" value="<?php echo esc_html( $test_details['label'] ); ?>" />
		</p>

		<?php

	}


	/**
	 * Determine if orders need payment in certification mode.
	 *
	 * This bypasses WC's need for an order total > $0
	 *
	 * @since 1.8.0
	 *
	 * @param bool $needs_payment whether the order needs payment.
	 * @param \WC_Order $order the order object.
	 * @param array $valid_order_statuses the order statuses that need payment.
	 * @return bool
	 */
	public function order_needs_payment( $needs_payment, $order, $valid_order_statuses ) {

		if ( $this->get_gateway()->get_id() === $order->get_payment_method( 'edit' ) ) {
			$needs_payment = $order->has_status( $valid_order_statuses );
		}

		return $needs_payment;
	}


	/**
	 * Mark orders "Completed" directly after payment.
	 *
	 * This allows the tester to quickly and easily use the "Order Again" functionality to move on
	 * to the next test.
	 *
	 * @since 1.8.0
	 * @param string $order_status the default order status to change the order to
	 * @param int $order_id the order ID
	 * @return string the (maybe) modified order status to change to
	 */
	public function complete_order( $order_status, $order_id ) {

		$order = wc_get_order( $order_id );

		// if the payment method was Paymentech, complete the order
		if ( $order->get_payment_method( 'edit' ) === $this->get_gateway()->get_id() ) {
			return 'completed';
		}

		return $order_status;
	}


	/**
	 * Displays the certification test results on the Thank You page.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string $template_name template file name
	 * @param string $template_path template file path
	 * @param string $located the located template file path
	 * @param array $args template args
	 */
	public function display_test_results( $template_name, $template_path, $located, $args ) {

		// bail if not viewing the Thank You page
		if ( 'checkout/thankyou.php' !== $template_name || ! isset( $args['order'] ) ) {
			return;
		}

		$order = $args['order'];

		// bail if not this gateway
		if ( $order->get_payment_method( 'edit' ) !== $this->get_gateway()->get_id() ) {
			return;
		}

		$test_details = WC()->session->get( 'wc_chase_paymentech_certification_test_details' );

		// set the timestamp
		try {

			// get datetime object from site timezone
			$datetime = new DateTime( $this->get_gateway()->get_order_meta( $order, 'trans_date' ), new DateTimeZone( 'EST' ) );

			// get the unix timestamp (adjusted for the site's timezone already)
			$timestamp = $datetime->format( 'Y-m-d H:i:s' ) . ' EST';

		} catch ( Exception $e ) {

			$timestamp = __( 'N/A', 'woocommerce-gateway-chase-paymentech' );
		}

		// add the test case data that is relevant for all tests
		$table_data = array(
			__( 'Test Case',      'woocommerce-gateway-chase-paymentech' )  => $test_details['label'],
			__( 'Date &amp; Time', 'woocommerce-gateway-chase-paymentech' ) => $timestamp,
		);

		// if a the transaction was tokenized
		if ( $token = $this->get_gateway()->get_order_meta( $order, 'payment_token' ) ) {
			$table_data[ __( 'Customer Profile ID / Ref Number', 'woocommerce-gateway-chase-paymentech' ) ] = $token;
		}

		// if there was an error code
		if ( isset( $test_details['error']['code'] ) ) {
			$table_data[ __( 'Decline Code', 'woocommerce-gateway-chase-paymentech' ) ] = $test_details['error']['code'];
		}

		// if there was an error message
		if ( isset( $test_details['error']['message'] ) ) {
			$table_data[ __( 'Decline Message', 'woocommerce-gateway-chase-paymentech' ) ] = $test_details['error']['message'];
		}

		// if the test generated an authorization code
		if ( $auth_code = $this->get_gateway()->get_order_meta( $order, 'authorization_code' ) ) {
			$table_data[ __( 'Auth Code', 'woocommerce-gateway-chase-paymentech' ) ] = $auth_code;
		}

		// add the order ID
		$table_data[ __( 'Order ID', 'woocommerce-gateway-chase-paymentech' ) ] = ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce-gateway-chase-paymentech' ) );

		// if the test generated a transaction ID
		if ( $transaction_id = $this->get_gateway()->get_order_meta( $order, 'trans_id' ) ) {
			$table_data[ __( 'TxRefNum', 'woocommerce-gateway-chase-paymentech' ) ] = $transaction_id;
		}

		?>

		<p><?php esc_html_e('Please copy the relevant test results below into your Orbital Test Cases document:', 'woocommerce-gateway-chase-paymentech' ); ?></p>

		<table class="shop_table">
			<tbody>
				<?php foreach ( $table_data as $label => $value ) : ?>
					<tr><td><strong><?php echo esc_html( $label ); ?>:</strong></td><td><?php echo esc_html( $value ); ?></td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<p class="order-again">
			<a href="<?php echo esc_url( $this->get_next_test_url( $order ) ); ?>" class="button"><?php esc_html_e( 'Next Test', 'woocommerce-gateway-chase-paymentech' ); ?></a>
		</p>

		<?php

	}


	/**
	 * Get the URL for the next test.
	 *
	 * At present, this URL automatically adds the previous test order's product to the cart and
	 * redirects the user right to checkout.
	 *
	 * @since 1.8.0
	 * @param \WC_Order $order the order object
	 * @return string
	 */
	protected function get_next_test_url( WC_Order $order ) {

		$items = $order->get_items();
		$item  = reset( $items );

		return add_query_arg( 'add-to-cart', $item['product_id'], wc_get_checkout_url() );
	}


	/**
	 * Remove the "order received" text on the "Thank You" page.
	 *
	 * @since 1.8.0
	 * @param string $text the "order received" text
	 * @param \WC_Order $order the order object
	 * @return string
	 */
	public function remove_order_received_text( $text, $order ) {

		return '';
	}


	/** My Payment Methods ******************************************************/


	/**
	 * Add the Customer Ref # header to the My Payment Methods table.
	 *
	 * @since 1.8.0
	 * @param array $headers the existing table headers
	 * @return array
	 */
	public function add_payment_method_ref_number_header( $headers ) {

		$_headers = array();

		foreach ( $headers as $id => $label ) {

			// add the ref number before the actions
			if ( 'actions' === $id ) {
				$_headers['certification_customer_ref_number'] = __( 'Customer Ref #', 'woocommerce-gateway-chase-paymentech' );
			}

			$_headers[ $id ] = $label;
		}

		return $_headers;
	}


	/**
	 * Display the Customer Ref # in the My Payment Methods table.
	 *
	 * @since 1.8.0
	 * @param array $data the payment method data
	 * @param Framework\SV_WC_Payment_Gateway_Payment_Token $token the method's token object
	 * @return array
	 */
	public function add_payment_method_ref_number_data( $data, $token ) {

		$data['certification_customer_ref_number'] = $token->get_id();

		return $data;
	}


	/** Admin Methods ******************************************************/


	/**
	 * Enqueue the admin styles.
	 *
	 * @since 1.8.0
	 */
	public function enqueue_admin_styles() {

		$handle = 'wc-' . $this->get_gateway()->get_id_dasherized();

		wp_enqueue_style( $handle, $this->get_gateway()->get_plugin()->get_plugin_url() . '/assets/css/admin/' . $handle . '-admin.min.css', array(), $this->get_gateway()->get_plugin()->get_version() );
	}


	/**
	 * Add custom admin order list table columns.
	 *
	 * @since 1.8.0
	 * @param array $columns the existing columns
	 * @return array
	 */
	public function admin_add_columns( $columns ) {

		// get all columns up to and excluding the 'order_actions' column
		$new_columns = array();

		foreach ( $columns as $name => $value ) {

			if ( $name === 'order_title' ) {
				prev( $columns );
				break;
			}

			$new_columns[ $name ] = $value;
		}

		$new_columns[ 'certification_test_case' ] = __( 'Test Case', 'woocommerce-gateway-chase-paymentech' );

		// add the remaining columns
		foreach ( $columns as $name => $value ) {
			$new_columns[ $name ] = $value;
		}

		return $new_columns;
	}


	/**
	 * Display the custom admin order list table column content.
	 *
	 * @since 1.8.0
	 * @param string $column the column name
	 */
	public function admin_display_column_content( $column ) {

		if ( 'certification_test_case' === $column ) {
			echo esc_html( get_post_meta( get_the_ID(), '_wc_chase_paymentech_certification_test', true ) );
		}
	}


	/**
	 * Add the certification test ID to the admin search fields.
	 *
	 * @since 1.8.0
	 * @param array $search_fields the post meta fields to search
	 * @return array
	 */
	public function admin_add_order_search_fields( $search_fields ) {

		array_push( $search_fields, '_wc_chase_paymentech_certification_test' );

		return $search_fields;
	}


	/**
	 * Get the gateway object.
	 *
	 * @since 1.8.0
	 * @return \WC_Gateway_Chase_Paymentech the gateway object
	 */
	protected function get_gateway() {

		return $this->gateway;
	}
}
