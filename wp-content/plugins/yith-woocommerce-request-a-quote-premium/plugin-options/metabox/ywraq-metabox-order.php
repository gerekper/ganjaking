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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$customer_name            = '';
$customer_message         = '';
$customer_email           = '';
$additional_field         = '';
$additional_field_2       = '';
$additional_field_3       = '';
$additional_email_content = '';
$customer_attachments     = '';
$status                   = ''; //phpcs:ignore
$button_disabled          = '';
$pdf_file                 = '';
$attachment_text          = '';

$billing_address = '';
$billing_phone   = '';
$billing_vat     = '';


if ( isset( $_REQUEST['post'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	// phpcs:disable
	$customer_name            = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_name', true );
	$customer_message         = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_message', true );
	$request_response         = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_request_response', true );
	$request_response_after   = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_request_response_after', true );
	$customer_email           = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_email', true );
	$additional_field         = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_additional_field', true );
	$additional_field_2       = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_additional_field_2', true );
	$additional_field_3       = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_additional_field_3', true );
	$customer_attachments     = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_customer_attachment', true );
	$additional_email_content = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_other_email_content', true );
	$billing_address          = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_billing_address', true );
	$billing_phone            = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_billing_phone', true );
	$billing_vat              = get_post_meta( wp_unslash( $_REQUEST['post'] ), 'ywraq_billing_vat', true );
	// phpcs:enable

	if ( '' !== $billing_address ) {
		$additional_email_content .= sprintf( '<strong>%s</strong>: %s</br>', esc_html__( 'Billing Address', 'yith-woocommerce-request-a-quote' ), $billing_address );
	}

	if ( '' !== $billing_phone ) {
		$additional_email_content .= sprintf( '<strong>%s</strong>: %s</br>', esc_html__( 'Billing Phone', 'yith-woocommerce-request-a-quote' ), $billing_phone );
	}

	if ( '' !== $billing_vat ) {
		$additional_email_content .= sprintf( '<strong>%s</strong>: %s</br>', esc_html__( 'Billing Vat', 'yith-woocommerce-request-a-quote' ), $billing_vat );
	}

	if ( '' !== $customer_message ) {
		$customer_message = '<strong>' . esc_html__( 'Message', 'yith-woocommerce-request-a-quote' ) . '</strong>: ' . $customer_message;
	}

	if ( '' !== $additional_field ) {
		$additional_field = '<strong>' . get_option( 'ywraq_additional_text_field_label' ) . '</strong>: ' . $additional_field;
	}

	if ( '' !== $additional_field_2 ) {
		$additional_field_2 = '<strong>' . get_option( 'ywraq_additional_text_field_label_2' ) . '</strong>: ' . $additional_field_2;
	}

	if ( '' !== $additional_field_3 ) {
		$additional_field_3 = '<strong>' . get_option( 'ywraq_additional_text_field_label_3' ) . '</strong>: ' . $additional_field_3;
	}


	if ( ! empty( $customer_attachments ) ) {
		if ( isset( $customer_attachments['url'] ) ) {
			$attachment_text = '<strong>' . esc_html__( 'Attachment', 'yith-woocommerce-request-a-quote' ) . '</strong>:  <a href="' . $customer_attachments['url'] . '" target="_blank">' . $customer_attachments['url'] . '</a>';
		} else {
			foreach ( $customer_attachments as $key => $item ) {
				$attachment_text .= '<div><strong>' . $key . '</strong>:  <a href="' . $item . '" target="_blank">' . $item . '</a></div>';
			}
		}
	}


	$order_id          = intval( wp_unslash( $_REQUEST['post'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$order             = wc_get_order( $order_id ); //phpcs:ignore
	$accepted_statuses = apply_filters( 'ywraq_quote_accepted_statuses_send', array( 'ywraq-new', 'ywraq-rejected' ) );

	if ( ! empty( $order ) ) {
		$_status = $order->get_status();
		if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) && ! $order->has_status( $accepted_statuses ) ) {
			$button_disabled = 'disabled="disabled"';
		}
		if ( file_exists( YITH_Request_Quote_Premium()->get_pdf_file_path( $order_id ) ) ) {
			$pdf_file = YITH_Request_Quote_Premium()->get_pdf_file_url( $order_id );
		}
	}
}

$order_meta = array(
	'label'    => esc_html__( 'Request a Quote Order Settings', 'yith-woocommerce-request-a-quote' ),
	'pages'    => 'shop_order', //or array( 'post-type1', 'post-type2')
	'context'  => 'normal', //('normal', 'advanced', or 'side')
	'priority' => 'high',
	'tabs'     => array(
		'settings' => array(
			'label' => esc_html__( 'Settings', 'yith-woocommerce-request-a-quote' ),
		),
	),
);

$fields = array(

	'ywraq_customer_name'    => array(
		'label'   => esc_html__( 'Customer\'s name', 'yith-woocommerce-request-a-quote' ),
		'desc'    => '',
		'private' => false,
		'type'    => 'text',
	),

	'ywraq_customer_email'   => array(
		'label'   => esc_html__( 'Customer\'s email', 'yith-woocommerce-request-a-quote' ),
		'desc'    => '',
		'private' => false,
		'type'    => 'text',
	),

	'ywraq_customer_message' => array(
		'label'   => esc_html__( 'Customer\'s message', 'yith-woocommerce-request-a-quote' ),
		'desc'    => '',
		'type'    => 'textarea',
		'private' => false,
	),
);


if ( ! empty( $additional_email_content ) ) {
	$fields['ywraq_additional_email_content_title'] = array(
		'label' => esc_html__( 'Additional email content', 'yith-woocommerce-request-a-quote' ),
		'desc'  => '<strong>' . esc_html__( 'Additional email content', 'yith-woocommerce-request-a-quote' ) . '</strong>',
		'type'  => 'simple-text',
	);

	$fields['ywraq_customer_additional_email_content'] = array(
		'label' => esc_html__( 'Additional email content', 'yith-woocommerce-request-a-quote' ),
		'desc'  => $additional_email_content,
		'type'  => 'simple-text',
	);
}

if ( ! empty( $additional_field ) ) {
	$fields['ywraq_customer_additional_field'] = array(
		'label' => esc_html__( 'Customer\'s additional field', 'yith-woocommerce-request-a-quote' ),
		'desc'  => $additional_field,
		'type'  => 'simple-text',
	);
}

if ( ! empty( $additional_field ) ) {
	$fields['ywraq_customer_additional_field_2'] = array(
		'label' => esc_html__( 'Customer\'s additional field', 'yith-woocommerce-request-a-quote' ),
		'desc'  => $additional_field_2,
		'type'  => 'simple-text',
	);
}

if ( ! empty( $additional_field_3 ) ) {
	$fields['ywraq_customer_additional_field_3'] = array(
		'label' => esc_html__( 'Customer\'s additional field', 'yith-woocommerce-request-a-quote' ),
		'desc'  => $additional_field_3,
		'type'  => 'simple-text',
	);
}

if ( ! empty( $attachment_text ) ) {
	$fields['ywraq_customer_attachment'] = array(
		'label' => esc_html__( 'Customer\'s attachment', 'yith-woocommerce-request-a-quote' ),
		'desc'  => $attachment_text,
		'type'  => 'simple-text',
	);
}


$group_2 = array(

	'ywraq_customer_sep'            => array(
		'type' => 'sep',
	),
	// @since 1.3.0
	'ywcm_request_response'         => array(
		'label' => esc_html__( 'Attach message to the quote before the table list (optional)', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'textarea',
		'desc'  => esc_html__( 'Write a message that will be attached to the quote', 'yith-woocommerce-request-a-quote' ),
		'std'   => '',
	),

	// @since 1.3.0
	'ywraq_request_response_after'  => array(
		'label' => esc_html__( 'Attach message to the quote after the table list (optional)', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'textarea',
		'desc'  => esc_html__( 'Write a message that will be attached to the quote after the list', 'yith-woocommerce-request-a-quote' ),
		'std'   => '',
	),

	// @since 1.3.0
	'ywraq_optional_attachment'     => array(
		'label' => esc_html__( 'Optional Attachment', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'upload',
		'desc'  => esc_html__( 'Use this field to add additional attachment to the email', 'yith-woocommerce-request-a-quote' ),
		'std'   => '',
	),

	'ywcm_request_expire'           => array(
		'label' => esc_html__( 'Expire date (optional)', 'yith-woocommerce-request-a-quote' ),
		'desc'  => esc_html__( 'Set an expiration date for this quote', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'datepicker',
		'std'   => apply_filters( 'ywraq_set_default_expire_date', '' ),
	),
	'ywraq_customer_sep1'           => array(
		'type' => 'sep',
	),

	// @since 1.6.3
	'ywraq_pay_quote_now'           => array(
		'label' => esc_html__( 'Send the customer to "Pay for Quote"', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'onoff',
		'desc'  => esc_html__( 'If billing and shipping fields are filled, you can send the customer to Pay for Quote Page. In this page, neither billing nor shipping information will be requested.', 'yith-woocommerce-request-a-quote' ),
		'std'   => apply_filters( 'ywraq_set_default_pay_quote_now', 'no' ),
	),

	// @since 1.6.3
	'ywraq_checkout_info'           => array(
		'label'   => esc_html__( 'Override checkout fields', 'yith-woocommerce-request-a-quote' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'desc'    => esc_html__( 'Select an option if you want to override checkout fields.', 'yith-woocommerce-request-a-quote' ),
		'std'     => '',
		'options' => array(
			''         => esc_html__( 'Do not override Billing and Shipping Info', 'yith-woocommerce-request-a-quote' ),
			'both'     => esc_html__( 'Override Billing and Shipping Info', 'yith-woocommerce-request-a-quote' ),
			'billing'  => esc_html__( 'Override Billing Info', 'yith-woocommerce-request-a-quote' ),
			'shipping' => esc_html__( 'Override Shipping Info', 'yith-woocommerce-request-a-quote' ),
		),
	),

	// @since 1.6.3
	'ywraq_lock_editing'            => array(
		'label' => esc_html__( 'Lock the editing of fields selected above', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'onoff',
		'desc'  => esc_html__( 'Check this option if you want to disable the editing of the checkout fields.', 'yith-woocommerce-request-a-quote' ),
		'std'   => 'no',
	),


	// @since 1.6.3
	'ywraq_disable_shipping_method' => array(
		'label' => esc_html__( 'Override shipping', 'yith-woocommerce-request-a-quote' ),
		'type'  => 'onoff',
		'desc'  => esc_html__( 'Check this option if you want to use only the shipping method in the quote.', 'yith-woocommerce-request-a-quote' ),
		'std'   => apply_filters( 'override_shipping_option_default_value', 'yes' ),
	),

	'ywraq_customer_sep2'           => array(
		'type' => 'sep',
	),

	'ywraq_safe_submit_field'       => array(
		'desc' => esc_html__( 'Set an expiration date for this quote', 'yith-woocommerce-request-a-quote' ),
		'type' => 'hidden',
		'std'  => '',
		'val'  => '',
	),

	'ywraq_raq'                     => array(
		'desc'    => '',
		'type'    => 'hidden',
		'private' => false,
		'std'     => 'no',
		'val'     => 'no',
	),
);


$fields = array_merge( $fields, $group_2 );

if ( 'yes' === get_option( 'ywraq_enable_pdf', 'yes' ) ) {
	$button_create            = '<input type="button" class="button button-secondary" id="ywraq_pdf_button" value="' . esc_html__( 'Create PDF', 'yith-woocommerce-request-a-quote' ) . '">';
	$button_preview           = '<a class="button button-secondary" id="ywraq_pdf_preview" target="_blank" href="' . esc_url( $pdf_file ) . '">' . esc_html__( 'View PDF', 'yith-woocommerce-request-a-quote' ) . '</a>';
	$pdf_buttons              = ( '' !== $pdf_file ) ? $button_create . ' ' . $button_preview : $button_create;
	$fields['ywraq_pdf_file'] = array(
		'label'   => esc_html__( 'View Pdf', 'yith-woocommerce-request-a-quote' ),
		'private' => false,
		'desc'    => $pdf_buttons,
		'type'    => 'simple-text',
	);
}


if ( ! empty( $customer_email ) && ! empty( $customer_name ) ) {
	$fields['ywraq_submit_button'] = array(
		'desc' => '<input type="submit" class="button button-primary" id="ywraq_submit_button" value="' . esc_html__( 'Send Quote', 'yith-woocommerce-request-a-quote' ) . '" ' . $button_disabled . '>',
		'type' => 'simple-text',
	);
}

$order_meta['tabs']['settings']['fields'] = apply_filters( 'ywraq_order_metabox', $fields );

return $order_meta;
