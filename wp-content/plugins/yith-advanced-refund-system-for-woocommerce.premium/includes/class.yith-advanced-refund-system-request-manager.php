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
 * @class      YITH_Advanced_Refund_System_Request_Manager
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Advanced_Refund_System_Request_Manager' ) ) {
    /**
     * Class YITH_Advanced_Refund_System_Request_Manager
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Advanced_Refund_System_Request_Manager {

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
	        add_action( 'wp_ajax_ywcars_open_request_window', array( $this, 'create_request_window' ) );
	        add_action( 'wp_ajax_ywcars_submit_request', array( $this, 'submit_request' ) );
	        add_action( 'wp_ajax_ywcars_submit_message', array( $this, 'submit_message' ) );
	        add_action( 'wp_ajax_ywcars_update_messages', array( $this, 'update_messages' ) );
	        add_action( 'wp_ajax_ywcars_change_status', array( $this, 'change_status' ) );
	        add_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10, 2 );
	        add_action( 'ywcars_process_automatic_request', array( $this, 'process_automatic_request' ) );
        }

	    public function create_request_window() {
		    if ( ! $_GET['order_id'] && ! $_GET['target'] && ! $_GET['line_total'] ) {
			    die();
		    }
		    $is_whole_order = false;
		    $order_id = $_GET['order_id'];
		    $line_total = $_GET['line_total'];
		    $product_id = false;
		    $product = null;
		    $_GET['target'] == 'whole_order' ? $is_whole_order = true : $product_id = $_GET['target'];
		    if ( $product_id && is_numeric( $product_id ) ) {
			    $product = wc_get_product( $product_id );
		    }
		    ?>
		    <form id="ywcars_form" method="post" enctype="multipart/form-data">
			    <h2 class="ywcars_order_title"><?php
				    if ( $is_whole_order ) {
				        $order = wc_get_order( $order_id );
				        $message = $order->get_id() == $order->get_order_number() ? esc_html__( 'Refund the entire order #%s', 'yith-advanced-refund-system-for-woocommerce' ) : esc_html__( 'Refund the entire order %s', 'yith-advanced-refund-system-for-woocommerce' );
					    printf( $message, $order->get_order_number() );
				    } else if ( $product ) {
					    $product_name = $product->get_title();
					    echo apply_filters( 'ywcars_refund_for_text', sprintf( esc_html__( 'Refund for %s', 'yith-advanced-refund-system-for-woocommerce' ), $product_name ), $product_id, $product_name, $order_id );
				    }
				    ?></h2>
			    <input type="hidden" name="ywcars_form_order_id"    value="<?php echo esc_attr( $order_id ); ?>">
			    <input type="hidden" name="ywcars_form_whole_order" value="<?php echo esc_attr( $is_whole_order ); ?>">
			    <input type="hidden" name="ywcars_form_product_id"  value="<?php echo esc_attr( $product_id ); ?>">
			    <input type="hidden" name="ywcars_form_line_total"  value="<?php echo esc_attr( $line_total ); ?>">
			    <?php
			    do_action( 'ywcars_request_form_before_reason', $order_id, $is_whole_order );
			    ?>
			    <div class="ywcars_block">
				    <label for="ywcars_form_reason"><?php
                        echo apply_filters( 'ywcars_reason_block_text', esc_html__( 'Please, write the reason why you are asking for a refund:',
						    'yith-advanced-refund-system-for-woocommerce' ) ); ?></label>
				    <textarea id="ywcars_form_reason" name="ywcars_form_reason" rows="8"></textarea>
			    </div>
			    <?php
			    do_action( 'ywcars_request_form_after_reason', $order_id, $is_whole_order );
			    ?>
			    <div class="ywcars_block">
				    <div class="ywcars_alert ywcars_error_alert">
					    <span class="ywcars_close_alert">x</span>
					    <span class="ywcars_alert_content"></span>
				    </div>
			    </div>
			    <div class="ywcars_block">
				    <input id="ywcars_submit_button" type="submit" value="<?php esc_html_e( 'Submit', 'yith-advanced-refund-system-for-woocommerce' ); ?>">
			    </div>
		    </form>
		    <?php

		    die();
	    }

	    public function submit_request() {
		    check_ajax_referer( 'ywcars-submit-request', 'security' );
		    // Create the request object with all $_POST data and save
		    $order_id     = ! empty( $_POST['ywcars_form_order_id'] )    ? $_POST['ywcars_form_order_id']    : false;
		    $whole_order  = ! empty( $_POST['ywcars_form_whole_order'] ) ? $_POST['ywcars_form_whole_order'] : false;
		    $product_id   = ! empty( $_POST['ywcars_form_product_id'] )  ? $_POST['ywcars_form_product_id']  : false;
		    $item_id      = ! empty( $_POST['ywcars_form_item_id'] )     ? $_POST['ywcars_form_item_id']     : false;
		    $qty          = ! empty( $_POST['ywcars_form_qty'] )         ? $_POST['ywcars_form_qty']         : false;
		    $max_qty      = ! empty( $_POST['ywcars_form_max_qty'] )     ? $_POST['ywcars_form_max_qty']     : false;
		    $qty_total    = ! empty( $_POST['ywcars_form_qty_total'] )   ? $_POST['ywcars_form_qty_total']   : false;
		    $line_total   = ! empty( $_POST['ywcars_form_line_total'] )  ? $_POST['ywcars_form_line_total']  : false;

		    try {
		        $demo = apply_filters( 'ywcars_demo_mode', false );
		        if ( $demo ) {
			        throw new Exception( esc_html__( 'You cannot submit new requests on this Live Demo. Click on "Launch Admin Demo" to unblock this and fully test the plugin.',
				        'yith-advanced-refund-system-for-woocommerce' ) );
                }
			    if ( $whole_order ) {
				    if ( ! $order_id || ! $line_total ) {
					    throw new Exception( esc_html_x( 'Exception on submitting request: missing data.',
                            'Exception description. Technical error message.',
                            'yith-advanced-refund-system-for-woocommerce' ) );
				    }
			    } else {
				    if ( ! $order_id || ! $product_id || ! $item_id || ! $qty || ! $qty_total || ! $line_total ) {
					    throw new Exception( esc_html_x( 'Exception on submitting request: missing data.',
                            'Exception description. Technical error message.',
                            'yith-advanced-refund-system-for-woocommerce' ) );
				    }
				    if ( $qty > $max_qty || $qty > $qty_total ) {
				        throw new Exception( esc_html_x( 'Exception on submitting request: quantity limit exceeded.',
                            'Exception description. Technical error message.',
                            'yith-advanced-refund-system-for-woocommerce' ) );
                    }
			    }

			    $request = new YITH_Refund_Request();

			    $request->order_id        = $order_id;
			    $request->whole_order     = $whole_order;
			    $request->refund_id       = 0;
			    $request->refunded_amount = 0;
			    $request->coupon_id       = 0;

			    $order = wc_get_order( $request->order_id );

			    // Check first if all uploaded files are OK
			    $error_code = (int) $this->check_uploaded_files();
			    if ( YITH_WCARS_UPLOAD_ERR_ALL_FILES_OK != $error_code && UPLOAD_ERR_NO_FILE != $error_code ) {
				    throw new YITH_Upload_Exception( $error_code );
			    }

			    // Save order item meta for each item
			    if ( $request->whole_order ) {
				    $request->refund_total = $line_total;
				    $items = $order->get_items();
				    foreach ( $items as $item_id => $item ) {
					    wc_update_order_item_meta( $item_id, '_ywcars_item_requested', 'yes' );
					    wc_update_order_item_meta( $item_id, '_ywcars_requested_qty', $item['qty'] );
				    }
			    } else {
				    $request->product_id = $product_id;
				    $request->item_id    = $item_id;
				    $request->item_total = $line_total;
				    $request->qty        = $qty;
				    $request->qty_total  = $qty_total;
				    $request->item_value = $request->item_total / $request->qty_total;

				    $line_tax = wc_get_order_item_meta( $item_id, '_line_tax' );
				    $request->tax_value = empty( $line_tax ) ? 0 : $line_tax / $request->qty_total;
				    $request->tax_total = $request->tax_value * $request->qty_total;
				    $request->tax_refund_total = $request->tax_value * $request->qty;
				    $line_tax_data = maybe_unserialize( wc_get_order_item_meta( $item_id, '_line_tax_data' ) );
				    if ( $line_tax_data ) {
					    foreach ( $line_tax_data['total'] as &$tax ) {
						    $single_tax_value = $tax / $request->qty_total;
						    $tax = $single_tax_value * $request->qty;
					    }
				    }

				    $request->item_tax_data = $line_tax_data ? $line_tax_data['total'] : ''; // Save only 'total' array.
				    $request->item_refund_total  = $request->item_value * $request->qty;
				    $request->refund_total  = $request->item_refund_total + $request->tax_refund_total;

				    wc_update_order_item_meta( $request->item_id, '_ywcars_item_requested', 'yes' );
				    wc_update_order_item_meta( $request->item_id, '_ywcars_requested_qty', $request->qty );
			    }

			    $request->save();

			    // Save the request message with their meta data
			    if ( $request->exists() ) {
				    $message = new YITH_Request_Message();

				    $this->new_message( $message, $request->ID, $_POST['ywcars_form_reason'] );
			    }

			    if ( 'yes' == get_option( 'yith_wcars_automatic_refunds' ) && ! self::request_has_physical_products( $request ) ) {
				    wp_schedule_single_event( strtotime( '+1 minutes' ), 'ywcars_process_automatic_request' , array( $request->ID ) );
			    }

			    WC()->mailer();
			    do_action( 'ywcars_send_new_request_user', $request->ID );
			    do_action( 'ywcars_send_new_request_admin', $request->ID );
			    wc_add_notice( sprintf( apply_filters( 'ywcars_request_submitted_text', esc_html__( 'Request #%s submitted successfully', 'yith-advanced-refund-system-for-woocommerce' ), $request->ID ), $request->ID ) );
			    wp_send_json_success( 'ywcars_request_created' );
		    } catch ( YITH_Upload_Exception $e ) {
			    wp_send_json_error( $e->getMessage() );
		    } catch ( Exception $e ) {
			    wp_send_json_error( $e->getMessage() );
		    }

	    }

	    public function submit_message() {
		    check_ajax_referer( 'ywcars-submit-message', 'security' );

		    $request_id      = ! empty( $_POST['request_id'] )         ? $_POST['request_id']         : false;
		    $message_content = ! empty( $_POST['ywcars_new_message'] ) ? $_POST['ywcars_new_message'] : false;

		    try {
			    $demo = apply_filters( 'ywcars_demo_mode', false );
			    if ( $demo ) {
				    throw new Exception( esc_html__( 'You cannot submit new messages on this Live Demo. Click on "Launch Admin Demo" to unblock this and fully test the plugin.',
					    'yith-advanced-refund-system-for-woocommerce' ) );
			    }

		    	if ( ! $request_id || ! $message_content ) {
				    throw new Exception( esc_html_x( 'Exception on submitting message: missing data.', 'Exception description. Technical error message.',
                        'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    $message = new YITH_Request_Message();

			    $this->new_message( $message, $_POST['request_id'], $_POST['ywcars_new_message'] );

			    WC()->mailer();
			    if ( current_user_can( apply_filters( 'ywcars_can_manage_requests', 'manage_woocommerce' ) ) ) {
				    do_action( 'ywcars_new_message_from_admin_to_user', $message->ID );
			    } else {
				    do_action( 'ywcars_new_message_from_user_to_admin', $message->ID );
                }

			    wp_send_json_success( 'ywcars_message_submitted_correctly' );
		    } catch ( Exception $e ) {
			    wp_send_json_error( $e->getMessage() );
		    }
	    }

	    public function new_message( $message, $request_id, $message_content ) {
		    if ( ! ( $message instanceof YITH_Request_Message ) ) {
		    	return;
		    }

		    $error_code = (int) $this->check_uploaded_files();
		    if ( YITH_WCARS_UPLOAD_ERR_ALL_FILES_OK != $error_code && UPLOAD_ERR_NO_FILE != $error_code ) {
			    throw new YITH_Upload_Exception( $error_code );
		    }

		    $message->request = $request_id;
		    $message->message = $message_content;
		    $message->author  = get_current_user_id();

		    $message->save();

		    if ( isset( $_FILES['ywcars_form_attachment'] ) && $_FILES['ywcars_form_attachment'] ) {
		    	try {
				    foreach ( $_FILES['ywcars_form_attachment']['error'] as $key => $error ) {
					    if ( $error == UPLOAD_ERR_OK || $error == UPLOAD_ERR_NO_FILE ) {
						    $tmp_name  = $_FILES['ywcars_form_attachment']['tmp_name'][$key];
						    $name      = basename( $_FILES['ywcars_form_attachment']['name'][$key] );
						    $extension = pathinfo( $name, PATHINFO_EXTENSION );
						    $id_name   = uniqid( 'ywcars_' ) . '.' . $extension;
						    $result    = move_uploaded_file( $tmp_name, YITH_WCARS_UPLOADS_DIR . $id_name );
						    if ( $result ) {
							    $message->add_message_meta( $name, YITH_WCARS_UPLOADS_URL . $id_name );
						    }
					    } else {
						    throw new YITH_Upload_Exception( (int) $error );
					    }
				    }
			    } catch ( YITH_Upload_Exception $e ) {
				    wp_send_json_error( $e->getMessage() );
			    }
		    }
	    }

	    public function check_uploaded_files() {
        	if ( ! isset( $_FILES['ywcars_form_attachment'] ) || empty( $_FILES['ywcars_form_attachment'] ) ) {
        		return UPLOAD_ERR_NO_FILE;
	        }
		    foreach ( $_FILES['ywcars_form_attachment']['error'] as $key => $error ) {
		    	if ( $error == UPLOAD_ERR_NO_FILE ) {
		    		return $error;
			    } elseif ( $error == UPLOAD_ERR_OK ) {
				    if ( 'yes' == get_option( 'yith_wcars_enable_only_images', 'no' ) ) {
					    $info = getimagesize( $_FILES['ywcars_form_attachment']['tmp_name'][$key] );
					    if ( ! $info ) {
						    return YITH_WCARS_UPLOAD_ERR_NOT_A_IMAGE;
					    }
					    if ( ( $info[2] !== IMAGETYPE_GIF ) && ( $info[2] !== IMAGETYPE_JPEG ) && ( $info[2] !== IMAGETYPE_PNG ) ) {
						    return YITH_WCARS_UPLOAD_ERR_WRONG_IMAGE_FORMAT;
					    }
				    }
			    } else {
				    return $error;
			    }
		    }
		    return YITH_WCARS_UPLOAD_ERR_ALL_FILES_OK;
	    }

	    public function update_messages() {
            check_ajax_referer( 'ywcars-update-messages', 'security' );
            $request = '';
		    if ( isset( $_POST['request_id'] ) && ! empty( $_POST['request_id'] ) && is_numeric( $_POST['request_id'] ) ) {
			    $request = new YITH_Refund_Request( $_POST['request_id'] );
		    }
		    if ( ! $request->exists() ) {
			    die();
		    }
		    wc_get_template( 'admin/ywcars-load-messages.php',
			    array( 'request' => $request ),
			    '',
			    YITH_WCARS_TEMPLATE_PATH . '/' );

		    die();
        }

	    public function change_status() {
		    check_ajax_referer( 'change-status', 'security' );
		    $request_id = ! empty( $_POST['ywcars_request_id'] ) ? $_POST['ywcars_request_id'] : false;
		    $status     = ! empty( $_POST['ywcars_status'] )     ? $_POST['ywcars_status']     : false;

		    try {
			    if ( ! $request_id || ! $status ) {
				    throw new Exception( esc_html__( 'Empty data.', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    if ( ! current_user_can( apply_filters( 'ywcars_can_manage_requests', 'manage_woocommerce' ) ) ) {
				    throw new Exception( esc_html__( 'You do not have permission to change any request status.', 'yith-advanced-refund-system-for-woocommerce'
                    ) );
			    }

			    $request = new YITH_Refund_Request( $request_id );
			    if ( ! $request->exists() ) {
				    throw new Exception( esc_html__( 'Refund request does not exist.', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    switch ( $status ) {
				    case 'ywcars-rejected' :
					    if ( 'ywcars-rejected' != $request->status )
					    	$request->set_rejected();
					    break;
				    case 'ywcars-processing' :
					    if ( 'ywcars-processing' != $request->status )
						    $request->set_processing();
					    break;
				    case 'ywcars-on-hold' :
				    	if ( 'ywcars-on-hold' != $request->status )
				    		$request->set_on_hold();
					    break;
				    case 'ywcars-close-request' :
                        $request->close_request();
					    break;
				    default :
					    throw new Exception( esc_html__( 'Wrong status code.', 'yith-advanced-refund-system-for-woocommerce' ) );
					    break;
			    }
			    wp_send_json_success();

		    } catch ( Exception $e ) {
			    wp_send_json_error( array( 'error' => $e->getMessage() ) );
		    }

	    }


	    public function order_refunded( $order_id, $refund_id ) {
		    $ywcars_request_id = ! empty( $_POST['ywcars_request_id'] ) ? $_POST['ywcars_request_id'] : false;
		    if ( $ywcars_request_id && $refund_id && $order_id ) {
		    	$this->process_refund( $ywcars_request_id, $refund_id, $order_id );
		    }
	    }

	    public function process_refund( $ywcars_request_id, $refund_id, $order_id ) {
		    $request = new YITH_Refund_Request( $ywcars_request_id );
		    if ( ! ( $request instanceof YITH_Refund_Request && $request->exists() ) ) {
			    return;
		    }

		    $refund = new WC_Order_Refund( $refund_id );
		    $order = wc_get_order( $order_id );

		    $amount = version_compare( WC()->version, '3.0.0', '<' ) ? $refund->get_refund_amount() : $refund->get_amount();

		    $order_refunds = yit_get_prop( $order, '_order_ywcars_refunds', true );
		    if ( ! empty( $order_refunds ) && is_array( $order_refunds ) ) {
			    $order_refunds[] = array( 'wc_refund_id' => $refund_id, 'ywcars_request_id' => $request->ID, 'amount' => $amount );
		    } else {
			    $order_refunds = array( array( 'wc_refund_id' => $refund_id, 'ywcars_request_id' => $request->ID, 'amount' => $amount ) );
		    }

		    // Save metas
		    yit_save_prop( $order, '_order_ywcars_refunds', $order_refunds );
		    yit_save_prop( $refund, '_ywcars_refund', 'yes' );
		    $request->refund_id       = $refund_id;
		    $request->refunded_amount = $amount;

		    // Save order item metas
		    if ( $request->whole_order ) {
			    $items = $order->get_items();
			    foreach ( $items as $item_id => $item ) {
				    wc_update_order_item_meta( $item_id, '_ywcars_item_refunded', 'yes' );
			    }
		    } else {
			    wc_update_order_item_meta( $request->item_id, '_ywcars_item_refunded', 'yes' );
		    }


		    $request->set_approved();
	    }

	    public static function load_messages( $request_id ) {
		    $request = new YITH_Refund_Request( $request_id );
		    if ( ! $request->exists() ) {
			    return;
		    }
		    wc_get_template( 'admin/ywcars-load-messages.php',
			    array( 'request' => $request ),
			    '',
			    YITH_WCARS_TEMPLATE_PATH . '/' );
	    }

	    public static function request_has_physical_products( $request ) {
		    $has_physical_products = false;
		    if ( $request->whole_order ) {
			    $order = wc_get_order( $request->order_id );
			    $items = $order->get_items();
			    foreach ( $items as $item ) {
				    $product = wc_get_product( $item['product_id'] );
				    if ( $product->exists() && ! $product->is_virtual() ) {
					    $has_physical_products = true;
					    break;
				    }
			    }
		    } else {
			    $product = wc_get_product( $request->product_id );
			    if ( $product->exists() && ! $product->is_virtual() ) {
				    $has_physical_products = true;
			    }
		    }
		    return $has_physical_products;
	    }

	    public static function order_has_wc_refunds( $order ) {
		    $refunds = $order->get_refunds();

		    if ( ! $refunds ) {
			    return false;
		    }
		    foreach ( $refunds as $refund ) {
			    $is_a_ywcars_refund = yit_get_prop( $refund, '_ywcars_refund', true );
			    if ( $is_a_ywcars_refund && 'yes' == $is_a_ywcars_refund ) {
				    continue;
			    } else {
				    return true;
			    }
		    }
		    return false;
	    }

	    public function process_automatic_request( $request_id ) {

		    $request = new YITH_Refund_Request( $request_id );
		    if ( ! $request->exists() ) {
			    return;
		    }
		    $open_request = apply_filters( 'ywcars_open_request', ( 'ywcars-approved' != $request->status && 'ywcars-rejected' != $request->status && 'trash' != $request->status ), $request );

		    if ( ! $open_request ) {
			    return;
		    }

		    $request_is_whole_order = $request->whole_order;
		    $order_id               = $request->order_id;
		    $order                  = wc_get_order( $order_id );
		    $order_items            = $order->get_items();
		    $payment_gateway        = wc_get_payment_gateway_by_order( $order );
		    $supports_refunds       = false !== $payment_gateway && $payment_gateway->supports( 'refunds' );
		    $refund_reason          = esc_html__( 'Automatic refund', 'yith-advanced-refund-system-for-woocommerce' );
		    $api_refund             = !! $supports_refunds;
		    $restock_refunded_items = !! ( 'yes' == get_option( 'yith_wcars_restock_items', 'no' ) );
		    $refund                 = false;
		    $response_data          = array();
		    $line_item_qtys         = array();
		    $line_item_totals       = array();
		    $line_item_tax_totals   = array();

		    if ( $request_is_whole_order ) {
			    foreach ( $order_items as $item_id => $item ) {
				    if ( 'line_item' != $item['type'] ) {
					    continue;
				    }
				    $line_item_qtys[ $item_id ]   = $item['qty'];
				    $line_item_totals[ $item_id ] = $item['line_total'];
				    if ( ! wc_tax_enabled() ) {
					    continue;
				    }
				    $tax_data = maybe_unserialize( isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '' );
				    if ( $tax_data && isset( $tax_data['total'] ) ) {
					    $line_item_tax_totals[ $item_id ] = $tax_data['total'];
				    }
			    }
			    $refund_amount = wc_format_decimal( sanitize_text_field( $request->refund_total ), wc_get_price_decimals() );
		    } else {
			    $line_item_qtys[ $request->item_id ]   = $request->qty;
			    $line_item_totals[ $request->item_id ] = $request->item_refund_total;
			    if ( wc_tax_enabled() ) {
				    $line_item_tax_totals[ $request->item_id ] = $request->item_tax_data;
			    }
			    $taxes_sum = 0;
			    foreach ( $request->item_tax_data as $tax ) {
				    $taxes_sum += (int) $tax;
			    }
			    $refund_amount = wc_format_decimal( sanitize_text_field( $request->refund_total ), wc_get_price_decimals() );
		    }

		    try {
			    // Validate that the refund can occur
			    $max_refund  = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

			    if ( ! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount ) {
				    throw new exception( esc_html__( 'Invalid refund amount', 'yith-advanced-refund-system-for-woocommerce' ) );
			    }

			    // Prepare line items which we are refunding
			    $line_items = array();
			    $item_ids   = array_unique( array_merge( array_keys( $line_item_qtys, $line_item_totals ) ) );

			    foreach ( $item_ids as $item_id ) {
				    $line_items[ $item_id ] = array( 'qty' => 0, 'refund_total' => 0, 'refund_tax' => array() );
			    }
			    foreach ( $line_item_qtys as $item_id => $qty ) {
				    $line_items[ $item_id ]['qty'] = max( $qty, 0 );
			    }
			    foreach ( $line_item_totals as $item_id => $total ) {
				    $line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			    }
			    foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				    $line_items[ $item_id ]['refund_tax'] = array_map( 'wc_format_decimal', $tax_totals );
			    }

			    if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
				    // Create the refund object
				    $refund = wc_create_refund( array(
					    'amount'     => $refund_amount,
					    'reason'     => $refund_reason,
					    'order_id'   => $order_id,
					    'line_items' => $line_items,
				    ) );
			    } else {
				    $refund = wc_create_refund( array(
					    'amount'         => $refund_amount,
					    'reason'         => $refund_reason,
					    'order_id'       => $order_id,
					    'line_items'     => $line_items,
					    'refund_payment' => $api_refund,
					    'restock_items'  => $restock_refunded_items,
				    ) );
			    }

			    if ( is_wp_error( $refund ) ) {
				    throw new Exception( $refund->get_error_message() );
			    }

			    if ( version_compare( WC()->version, '3.0.0', '<' ) ) {

				    // Refund via API
				    if ( $api_refund ) {
					    if ( WC()->payment_gateways() ) {
						    $payment_gateways = WC()->payment_gateways->payment_gateways();
					    }
					    if ( isset( $payment_gateways[ $order->payment_method ] ) && $payment_gateways[ $order->payment_method ]->supports( 'refunds' ) ) {
						    $result = $payment_gateways[ $order->payment_method ]->process_refund( $order_id, $refund_amount, $refund_reason );

						    do_action( 'woocommerce_refund_processed', $refund, $result );

						    if ( is_wp_error( $result ) ) {
							    throw new Exception( $result->get_error_message() );
						    } elseif ( ! $result ) {
							    throw new Exception( esc_html__( 'Refund failed', 'yith-advanced-refund-system-for-woocommerce' ) );
						    }
					    }
				    }

				    // restock items
				    foreach ( $line_item_qtys as $item_id => $qty ) {
					    if ( $restock_refunded_items && $qty && isset( $order_items[ $item_id ] ) ) {
						    $order_item = $order_items[ $item_id ];
						    $_product   = $order->get_product_from_item( $order_item );

						    if ( $_product && $_product->exists() && $_product->managing_stock() ) {
							    $old_stock    = wc_stock_amount( $_product->stock );
							    $new_quantity = $_product->increase_stock( $qty );

							    $order->add_order_note( sprintf( esc_html__( 'Item #%s stock increased from %s to %s.', 'yith-advanced-refund-system-for-woocommerce' ), $order_item['product_id'], $old_stock, $new_quantity ) );

							    do_action( 'woocommerce_restock_refunded_item', $_product->id, $old_stock, $new_quantity, $order, $_product );
						    }
					    }
				    }

				    // Trigger notifications and status changes
				    if ( $order->get_remaining_refund_amount() > 0 || ( $order->has_free_item() && $order->get_remaining_refund_items() > 0 ) ) {
					    /**
					     * woocommerce_order_partially_refunded.
					     *
					     * @since 2.4.0
					     * Note: 3rd arg was added in err. Kept for bw compat. 2.4.3.
					     */
					    do_action( 'woocommerce_order_partially_refunded', $order_id, $refund->id, $refund->id );
				    } else {
					    do_action( 'woocommerce_order_fully_refunded', $order_id, $refund->id );

					    $order->update_status( apply_filters( 'woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund->id ) );
					    $response_data['status'] = 'fully_refunded';
				    }

				    // Clear transients
				    wc_delete_shop_order_transients( $order_id );
			    } else {
				    if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
					    $response_data['status'] = 'fully_refunded';
				    }
			    }
			    $refund_id = version_compare( WC()->version, '3.0.0', '<' ) ? $refund->id : $refund->get_id();
			    $this->process_refund( $request_id, $refund_id, $order_id );

		    } catch ( Exception $e ) {
			    if ( $refund && is_a( $refund, 'WC_Order_Refund' ) ) {
				    $refund_id = version_compare( WC()->version, '3.0.0', '<' ) ? $refund->id : $refund->get_id();
				    wp_delete_post( $refund_id, true );
			    }
		    }
	    }

    }
}