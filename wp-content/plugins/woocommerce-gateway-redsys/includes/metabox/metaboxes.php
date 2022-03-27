<?php

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
function add_redsys_meta_box() {
	if ( WCRed()->is_redsys_order( get_the_ID() ) ) {

		$date         = WCRed()->get_order_date( get_the_ID() );
		$hour         = WCRed()->get_order_hour( get_the_ID() );
		$auth         = WCRed()->get_order_auth( get_the_ID() );
		$number       = WCRed()->get_order_mumber( get_the_ID() );
		$paygold_link = WCRed()->get_order_pay_gold_link( get_the_ID() );

		echo '<h4>' . esc_html__( 'Payment Details', 'woocommerce-redsys' ) . '</h4>';
		echo '<p><strong>' . esc_html__( 'Paid with', 'woocommerce-redsys' ) . ': </strong><br />' . WCRed()->get_gateway( get_the_ID() ) . '</p>';
		if ( $number ) {
			echo '<p><strong>' . esc_html__( 'Redsys Order Number', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $number ) . '</p>';
		}
		if ( $date ) {
			echo '<p><strong>' . esc_html__( 'Redsys Date', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $date ) . '</p>';
		}

		if ( $hour ) {
			echo '<p><strong>' . esc_html__( 'Redsys Hour', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $hour ) . '</p>';
		}

		if ( $auth ) {
			echo '<p><strong>' . esc_html__( 'Redsys Authorisation Code', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $auth ) . '</p>';
		}
		if ( $paygold_link ) {
			echo '<p><strong>' . esc_html__( 'PayGold Link', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $paygold_link ) . '</p>';
		}
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' );

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
function redsys_tab( $tabs ) {
	$tabs['redsys'] = array(
		'label'  => __( 'Redsys', 'woocommerce-redsys' ),
		'target' => 'redsys',
		'class'  => array(),
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'redsys_tab' );

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
// Adding content to custom panel
function redsys_tab_panel() {
	?>
	<div id="redsys" class="panel woocommerce_options_panel">
		<div class="options_group">
			<?php
			$field = array(
				'id'          => '_redsystokenr',
				'label'       => __( 'Get Redsys Token', 'woocommerce-redsys' ),
				'description' => __( 'This option will get a special token that doesn\'t need the customer verification. Only get it if you need it. For example, you need to change orders created by you.', 'woocommerce-redsys' ),
			);
			woocommerce_wp_checkbox( $field );
			?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'redsys_tab_panel' );

// Saving data
/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
function save_redsys_product( $post_id ) {

	$product      = wc_get_product( $post_id );
	$redsystokenr = isset( $_POST['_redsystokenr'] ) ? $_POST['_redsystokenr'] : 'no';
	$product->update_meta_data( '_redsystokenr', $redsystokenr );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_redsys_product' );

// PayGold
/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
function paygold_metabox() {
	global $post, $typenow;

	if ( 'shop_order' !== $typenow ) {
		return;
	}

	$status   = get_post_status( $post );
	$order    = new WC_Order( $post );
	$gateway  = $order->get_payment_method();
	$order_id = $post->ID;

	if ( WCRed()->is_gateway_enabled( 'paygold' ) && ! WCRed()->is_paid( $order_id ) && 'paygold' === $gateway ) {
		add_meta_box( 'paygold', __( 'Send PayGold Link', 'woocommerce-redsys' ), 'paygold_meta_box_content', 'shop_order', 'normal', 'core' );
	}
}
add_action( 'add_meta_boxes', 'paygold_metabox' );

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
function paygold_meta_box_content( $post ) {

	$check = 'off';
	if ( WCRed()->check_soap( 'real' ) || ! function_exists( 'SimpleXMLElement' ) ) {
		?>
		<div id="order_data" class="panel woocommerce-order-data">	
			<h2 class="woocommerce-order-data__heading">
				<?php _e( 'This is for send a Pay Gold Link.', 'woocommerce-redsys' ); ?>
			</h2>
			<p class="woocommerce-order-data__meta order_number">
				<?php _e( 'Please, fill the fields and the link will be sent by email or SMS. Only fill one field and then press "Update"', 'woocommerce-redsys' ); ?>
			</p>
			<p class="woocommerce-order-data__meta order_number">
				<?php _e( 'The link will be saved and shown in the Order Details metabox.', 'woocommerce-redsys' ); ?>
			</p>
			<p class="woocommerce-order-data__meta order_number">
				<?php _e( 'If you need to send a new link, fill again all fields, and press "Update" again.', 'woocommerce-redsys' ); ?>
			</p>
			<p class="form-field form-field-wide">
				<input type="checkbox" id="paygold_send_link" name="paygold_send_link" <?php checked( $check, 'on' ); ?> />  
				<label for="paygold_send_link"><?php _e( 'Send New PayGold Link', 'woocommerce-redsys' ); ?></label>
			</p>
			<p class="form-field form-field-wide">
				<label for="select_paygold_type"><?php _e( 'What do you want to send?', 'woocommerce-redsys' ); ?></label>
				<select name="select_paygold_type" id="select_paygold_type">  
					<option value="mail"><?php _e( 'Send Email', 'woocommerce-redsys' ); ?></option>  
					<option value="SMS"><?php _e( 'Send SMS', 'woocommerce-redsys' ); ?></option>
				</select>
			</p>
			<p class="form-field form-field-wide">
				<label for="sms_email_send_paygold"><?php _e( 'Type the Email or the Mobile Number:', 'woocommerce-redsys' ); ?></label>
				<input type="text" name="sms_email_send_paygold" id="sms_email_send_paygold" value="" />
			</p>
		</div>
		<?php
		wp_nonce_field( 'paygold_meta_box_nonce', 'paygold_box_nonce' );
	} else {
		?>
		<p class="woocommerce-order-data__meta order_number">
			<?php _e( 'Please contact with your hosting provider and ask for SOAP and SimpleSML. WooCommerce Redsys Gateway cannot contact via SOAP with https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl or read the response with SimpleXML so is not possible to use PayGold', 'woocommerce-redsys' ); ?>
		</p>
		<?php
	}
}

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2022 José Conti
 */
function paygold_metabox_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['paygold_box_nonce'] ) || ! wp_verify_nonce( $_POST['paygold_box_nonce'], 'paygold_meta_box_nonce' ) ) {
		return;
	}

	if ( isset( $_POST['paygold_send_link'] ) ) {
		$check = $_POST['paygold_send_link'];
	} else {
		$check = 'off';
	}

	if ( 'off' === $check ) {
		return;
	}

	if ( isset( $_POST['select_paygold_type'] ) ) {
		$type = esc_attr( $_POST['select_paygold_type'] );
		update_post_meta( $post_id, '_paygold_link_type', $type );
	}

	if ( isset( $_POST['sms_email_send_paygold'] ) ) {
		$send_to = esc_attr( $_POST['sms_email_send_paygold'] );
		update_post_meta( $post_id, '_paygold_link_send_to', $send_to );
	}

	$result = WCRed()->send_paygold_link( $post_id );
	WCRed()->set_order_paygold_link( $post_id, $result );
}
add_action( 'save_post', 'paygold_metabox_save' );
