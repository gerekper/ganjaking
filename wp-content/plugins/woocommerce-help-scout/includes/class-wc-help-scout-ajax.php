<?php
/**
 * WC_Help_Scout_Ajax
 *
 * @package  WC_Help_Scout_Ajax
 * Checks if WooCommerce is enabled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Help Scout Ajax.
 *
 * @package  WC_Help_Scout_Ajax
 */
class WC_Help_Scout_Ajax {

	/**
	 * Ajax actions.
	 */
	public function __construct() {
		// Create conversations.
		add_action( 'wp_ajax_wc_help_scout_create_conversation', array( $this, 'create_conversation' ) );
		add_action( 'wp_ajax_nopriv_wc_help_scout_create_conversation', array( $this, 'create_conversation' ) );

		// Get conversation.
		add_action( 'wp_ajax_wc_help_scout_get_conversation', array( $this, 'get_conversation' ) );
		add_action( 'wp_ajax_nopriv_wc_help_scout_get_conversation', array( $this, 'get_conversation' ) );

		// Create threads.
		add_action( 'wp_ajax_wc_help_scout_create_thread', array( $this, 'create_thread' ) );
		add_action( 'wp_ajax_nopriv_wc_help_scout_create_thread', array( $this, 'create_thread' ) );

	}

	/**
	 * Create conversations.
	 *
	 * @return string JSON data.
	 */
	public function create_conversation() {
		if ( ! check_ajax_referer( 'woocommerce_help_scout_ajax', 'security', false ) ) {
			wp_send_json(
				array(
					'id'     => 0,
					'number' => 0,
					'status' => __( 'There was security vulnerability issues in your request.', 'woocommerce-help-scout' ),
				)
			);
			exit;
		}

		$filedata = array();

		if ( isset( $_FILES['images'] ) && ! empty( $_FILES['images']['name'][0] ) ) {
			$filetypes = array( 'jpg', 'jpeg', 'png', 'pdf' );
			$total = count( $_FILES['images']['name'] );
			$filedata = array();
			$erroruploadmsg = '';
			if ( ! empty( $_FILES['images'] ) ) {
				for ( $i = 0; $i < $total; $i++ ) {
					if ( isset( $_FILES['images']['name'][ $i ] ) ) {

						$fileupload = isset( $_FILES['images']['name'][ $i ] ) ? sanitize_text_field( wp_unslash( $_FILES['images']['name'][ $i ] ) ) : '';
						$tempfileupload = isset( $_FILES['images']['tmp_name'][ $i ] ) ? sanitize_text_field( wp_unslash( $_FILES['images']['tmp_name'][ $i ] ) ) : '';
						if ( isset( $fileupload ) ) {
							$ext = pathinfo( sanitize_text_field( $fileupload ) );
							$fileext = strtolower( $ext['extension'] );
							if ( ! in_array( $fileext, $filetypes ) ) {
								$erroruploadmsg .= $ext['extension'] . ' not allowed file type. ';
							}
							$data = file_get_contents( $tempfileupload );
							$filename = basename( $fileupload );
							$filetype = mime_content_type( $tempfileupload );
							$base64 = stripslashes( base64_encode( $data ) );
							$filedata[] = array(
								'name' => $filename,
								'type' => $filename,
								'data' => $base64,
							);
						}
					}
				}
			}
		}
		$integration = new WC_Help_Scout_Integration();
		// Return if Authrorization has failed.
		if ( ! $integration->check_authorization_still_valid() ) {
			wp_send_json(
				array(
					'id'     => 0,
					'number' => 0,
					'status' => __( 'Invalid Authorization! <br/>Please re-validate plugin with your helpscout account from settings.', 'woocommerce-help-scout' ),
				)
			);
			return false;
		}
		// Sets the conversation params.
		$order_id    = isset( $_POST['order_id'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) ) : ( isset( $_POST['conversation_order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_order_id'] ) ) : '' );
		$subject     = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : ( isset( $_POST['conversation_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_subject'] ) ) : '' );
		$description = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : ( isset( $_POST['conversation_description'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_description'] ) ) : '' );

		if ( isset( $_POST['customer_name'] ) ) {
			$customer_name = sanitize_text_field( wp_unslash( $_POST['customer_name'] ) );
		} elseif ( isset( $_POST['conversation_customer_name'] ) ) {
			$customer_name = sanitize_text_field( wp_unslash( $_POST['conversation_customer_name'] ) );
		} else {
			$customer_name = '';
		}

		if ( isset( $_POST['email'] ) ) {
			$customer_email = sanitize_text_field( wp_unslash( $_POST['email'] ) );
		} elseif ( isset( $_POST['conversation_email'] ) ) {
			$customer_email = sanitize_text_field( wp_unslash( $_POST['conversation_email'] ) );
		} else {
			$customer_email = '';
		}

		$current_user = wp_get_current_user();
		if ( trim( $customer_name ) === '' ) {
			$customer_name = esc_html( $current_user->display_name ) ? esc_html( $current_user->display_name ) : esc_html( $current_user->user_firstname ) . ' ' . esc_html( $current_user->user_lastname );
		}
		if ( trim( $customer_email ) === '' ) {
			$customer_email = esc_html( $current_user->user_email );
		}

		if ( isset( $_POST['from'] ) ) {
			$from = sanitize_text_field( wp_unslash( $_POST['from'] ) );
		} elseif ( isset( $_POST['conversation_from'] ) ) {
			$from = sanitize_text_field( wp_unslash( $_POST['conversation_from'] ) );
		} else {
			$from = '';
		}

		$first_name  = null;
		$last_name   = null;

		// Valid file types.
		if ( ! empty( $erroruploadmsg ) ) {
			wp_send_json(
				array(
					'id'     => 0,
					'number' => 0,
					'status' => esc_html( $erroruploadmsg ),
				)
			);
		}

		// Valid the order_id field.
		if ( '' === $order_id ) {
			wp_send_json(
				array(
					'id'     => 0,
					'number' => 0,
					'status' => __( 'There was an error in the request, please reload this page and try again.', 'woocommerce-help-scout' ),
				)
			);
		}

		// Valid the subject field.
		if ( empty( $subject ) ) {
			wp_send_json(
				array(
					'id'     => 0,
					'number' => 0,
					'status' => __( 'Subject is a required field.', 'woocommerce-help-scout' ),
				)
			);
		}

		// Valid the description field.
		if ( empty( $description ) ) {
			wp_send_json(
				array(
					'id'     => 0,
					'number' => 0,
					'status' => __( 'Description is a required field.', 'woocommerce-help-scout' ),
				)
			);
		}


		/**
		 * Action for woocommerce_help_scout_create_conversation_ajax.
		 *
		 * @since  1.3.4
		 */
		do_action( 'woocommerce_help_scout_create_conversation_ajax' );

		if ( 0 < $order_id ) {
			// Get the order data.
			$order = new WC_Order( intval( $order_id ) );
			if ( empty( $order ) ) {
				wp_send_json(
					array(
						'id'     => 0,
						'number' => 0,
						'status' => __( 'Invalid order ID.', 'woocommerce-help-scout' ),
					)
				);
			}

			$description .= $integration->generate_order_data( $order );
			$subject     .= ' - ' . __( 'Order', 'woocommerce-help-scout' ) . ' ' . $order->get_order_number();
			$user_id      = version_compare( WC_VERSION, '3.0', '<' ) ? $order->user_id : $order->get_user_id();
			$user_email   = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();

			// If the user exists in WP use its email address instead.
			if ( ! empty( $user_id ) ) {
				$user       = get_user_by( 'id', absint( $user_id ) );
				$user_email = $user->user_email;
			}
		}

		if ( ! empty( $from ) && 'customer' !== $from ) {
			$user       = get_user_by( 'id', absint( $from ) );
			$user_id    = $user->ID;
			$user_email = $user->user_email;
		}

		if ( ! empty( $customer_name ) && ! empty( $customer_email ) ) {
			$user_id    = 0;
			$name       = explode( ' ', sanitize_text_field( $customer_name ) );
			$first_name = $name[0];
			unset( $name[0] );
			$last_name  = trim( implode( ' ', $name ) );
			$user_email = sanitize_email( $customer_email );
		}
		if ( $user_id <= 0 ) {
			$user_id = $current_user->ID;
		}
		$customer_id = $integration->get_customer_id( $user_id, $user_email, $first_name, $last_name );

		$response    = $integration->create_conversation( $subject, $description, $customer_id, $user_email, $filedata, $user_id );

		wp_send_json( $response );
	}

	/**
	 * Get conversations.
	 *
	 * @return string JSON data.
	 */
	public function get_conversation() {

		// Get the conversation data.
		$integration = new WC_Help_Scout_Integration();
		// return if Authrorization has failed.
		if ( ! $integration->check_authorization_still_valid() ) {
			if ( wp_doing_ajax() ) {
				wp_send_json(
					array(
						'threads' => array(),
						'subject' => '',
						'error' => __( 'Invalid Authorization! <br/>Please re-validate plugin with your helpscout account from settings.', 'woocommerce-help-scout' ),
					)
				);
			}
			return false;
		}
		
		if ( ! check_ajax_referer( 'woocommerce_help_scout_ajax', 'security', false ) ) {
			wp_send_json(
				array(
					'threads' => array(),
					'subject' => '',
					'error' => __( 'There was security vulnerability issues in your request.', 'woocommerce-help-scout' ),
				)
			);
			exit;
		}
		
		// Sets the conversation params.
		$conversation_id = isset( $_GET['conversation_id'] ) ? intval( $_GET['conversation_id'] ) : 0;

		// Valid the order_id field.
		if ( 0 >= $conversation_id ) {
			wp_send_json(
				array(
					'threads' => array(),
					'subject' => '',
					'error'   => __( 'There was an error in the request, please reload this page and try again.', 'woocommerce-help-scout' ),
				)
			);
		}

		$conversation = $integration->get_conversation( $conversation_id );

		$threads      = array();

		if ( isset( $conversation['_embedded']['threads'] ) && ! empty( $conversation['_embedded']['threads'] ) ) {
			$data_format = wc_date_format() . ' ' . __( '\a\t', 'woocommerce-help-scout' ) . ' ' . wc_time_format();
			foreach ( $conversation['_embedded']['threads'] as $thread ) {
				if ( ( 'customer' !== $thread['type'] && 'message' !== $thread['type'] ) ||
					   'published' !== $thread['state'] ) {
					continue;
				}

				$threads[] = array(
					'author'  => sanitize_text_field( $thread['createdBy']['first'] . ' ' . $thread['createdBy']['last'] ),
					'message' => wp_kses_post( wpautop( $thread['body'] ) ),
					'date'    => date_i18n( $data_format, strtotime( $thread['createdAt'] ) ),
				);
			}

			$response = array(
				'threads' => $threads,
				'subject' => '',
				'error'   => '',
			);
		} else {
			$response = array(
				'threads' => $threads,
				'subject' => sanitize_text_field( $conversation['_embedded']['subject'] ),
				'error'   => __( 'This conversation has no comments yet', 'woocommerce-help-scout' ),
			);
		}

		wp_send_json( $response );
	}

	/**
	 * Create threads.
	 *
	 * @return string JSON data.
	 */
	public function create_thread() {
		if ( ! check_ajax_referer( 'woocommerce_help_scout_ajax', 'security', false ) ) {
			wp_send_json(
				array(
					'error'     => 0,
					'message' => __( 'There was security vulnerability issues in your request.', 'woocommerce-help-scout' ),
				)
			);
			exit;
		}

		$filedata = array();

		if ( isset( $_FILES['images'] ) && ! empty( $_FILES['images']['name'][0] ) ) {
			$filetypes = array( 'jpg', 'jpeg', 'png', 'pdf' );
			$total = count( $_FILES['images']['name'] );
			$erroruploadmsg = '';

			if ( ! empty( $_FILES['images'] ) ) {
				for ( $i = 0; $i < $total; $i++ ) {
					$ima = isset( $_FILES['images']['name'][ $i ] ) ? sanitize_text_field( wp_unslash( $_FILES['images']['name'][ $i ] ) ) : '';
					$tempima = isset( $_FILES['images']['tmp_name'][ $i ] ) ? sanitize_text_field( wp_unslash( $_FILES['images']['tmp_name'][ $i ] ) ) : '';
					$ext = pathinfo( $ima );
					$fileext = strtolower( $ext['extension'] );
					if ( ! in_array( $fileext, $filetypes ) ) {
						$erroruploadmsg .= $ext['extension'] . ' not allowed file type. ';
					}
					$data = file_get_contents( $tempima );
					$filename = basename( $ima );
					$filetype = mime_content_type( $tempima );
					$base64 = stripslashes( base64_encode( $data ) );
					$filedata[] = array(
						'name' => $filename,
						'type' => $filename,
						'data' => $base64,
					);
				}
			}
		}

		// Valid file types.
		if ( ! empty( $erroruploadmsg ) ) {
			wp_send_json(
				array(
					'error' => 0,
					'message' => esc_html( $erroruploadmsg ),
				)
			);
		}

		// Get the conversation data.
		$integration = new WC_Help_Scout_Integration();
		// return if Authrorization has failed.
		if ( ! $integration->check_authorization_still_valid() ) {
			wp_send_json(
				array(
					'error' => 0,
					'message' => __( 'Invalid Authorization! <br/>Please re-validate plugin with your helpscout account from settings.', 'woocommerce-help-scout' ),
				)
			);
			return false;
		}
		// Sets the conversation params.
		$conversation_id      = isset( $_POST['conversation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_id'] ) ) : '';
		$conversation_message = isset( $_POST['conversation_message'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_message'] ) ) : '';
		$user_id              = isset( $_POST['user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';

		// Valid the order_id field.
		if ( empty( $conversation_id ) || empty( $user_id ) ) {
			wp_send_json(
				array(
					'error'   => 0,
					'message' => __( 'There was an error in the request, please reload this page and try again.', 'woocommerce-help-scout' ),
				)
			);
		}

		// Valid the subject field.
		if ( empty( $conversation_message ) ) {
			wp_send_json(
				array(
					'error'   => 0,
					'message' => __( 'Message is a required field.', 'woocommerce-help-scout' ),
				)
			);
		}

		$user_data   = get_userdata( $user_id );
		$customer_id = $integration->get_customer_id( $user_data->ID, $user_data->user_email );
		$thread      = $integration->create_thread( $conversation_id, $conversation_message, $customer_id, $user_data->user_email, $filedata );

		if ( $thread ) {
			$response = array(
				'error'   => 1,
				'message' => __( 'Reply sent successfully!', 'woocommerce-help-scout' ),
			);
		} else {
			$response = array(
				'error'   => 0,
				'message' => __( 'Failed to send the response, please try again or contact us for help.', 'woocommerce-help-scout' ),
			);
		}

		wp_send_json( $response );
	}
}
