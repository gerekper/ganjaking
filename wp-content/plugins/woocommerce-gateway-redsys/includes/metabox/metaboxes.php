<?php
/**
 * Metaboxes
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Automattic\WooCommerce\Utilities\OrderUtil;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

/**
 * Get QR Error Code
 *
 * @param string $error Error code.
 */
function redsys_qr_get_error( $error ) {
	/**
	 * Error-1 = No hay usuario.
	 * Error-2 = No existe el usuario.
	 * Error-3 = EL usuario no tiene la cuenta activa.
	 * Error-4 = No se puede conectar con la API, prueba de nuevo más tarde.
	 */
	$redsys_errors = array(
		'Error-1' => __( 'Error-1: No user of redsys.joseconti.com in Settings', 'woocommerce-redsys' ),
		'Error-2' => __( 'Error-2: The user does not exist', 'woocommerce-redsys' ),
		'Error-3' => __( 'Error-3: The user does not have an active account in', 'woocommerce-redsys' ),
		'Error-4' => __( 'Error-4: Unable to connect to the API, try again later.', 'woocommerce-redsys' ),
		'Error-5' => __( 'Error-5: The plugin license is not active for website.', 'woocommerce-redsys' ),
	);
	$error_sig     = __( 'Error: Unkonwn', 'woocommerce-redsys' );
	foreach ( $redsys_errors as $key => $value ) {
		if ( $error === $key ) {
			$error_sig = $value;
		}
	}
	return $error_sig;
}
/**
 * Register metaboxes.
 */
function redsys_register_qr_meta_boxes() {

	$is_active = get_option( 'redsys_qr_active', 'no' );
	$screen    = get_current_screen()->post_type;
	if ( 'product' !== $screen ) {
		return;
	}
	if ( 'yes' !== $is_active ) {
		return;
	}
	add_meta_box(
		'redsysqrmetabox',
		__( 'Redsys QR Code ', 'woocommerce-redsys' ),
		'redsys_qr_metabox_callback',
		$screen,
		'side',
		'low'
	);
}
add_action( 'add_meta_boxes', 'redsys_register_qr_meta_boxes', 999 );

/**
 * Metabox display callback.
 *
 * @param WP_Post $post Current post object.
 */
function redsys_qr_metabox_callback( $post ) {

	$is_active = get_option( 'redsys_qr_active', 'no' );

	if ( 'yes' !== $is_active ) {
		return;
	}

	$has_qr      = get_post_meta( $post->ID, '_redsys_qr', true );
	$post_status = get_post_status( $post->ID );
	$regenerate  = admin_url( 'post.php?post=' . $post->ID . '&action=edit&redsys_qr=reg&redsys_nonce=' . wp_create_nonce( 'redsys_qr_nonce' ) );

	if ( 'publish' !== $post_status ) {
		?>
		<p><?php esc_html_e( 'QR Code will be generated when you publish the product', 'woocommerce-redsys' ); ?></p>
		<?php
		return;
	}

	if ( isset( $_GET['redsys_qr'] ) && 'reg' === $_GET['redsys_qr'] ) {
		if ( ! isset( $_GET['redsys_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['redsys_nonce'] ) ), 'redsys_qr_nonce' ) ) {
			wp_die( esc_html__( 'Nonce error', 'woocommerce-redsys' ) );
		}
		delete_post_meta( $post->ID, '_redsys_qr' );
		$has_qr = false;
	}

	if ( ! $has_qr ) {
		$qr          = new Redsys_QR_Codes();
		$product_id  = $post->ID;
		$product_url = get_permalink( $post->ID );
		$file_image  = $qr->get_qr( $product_url, '#link', $product_id );
		if (
			'Error-1' === $file_image ||
			'Error-2' === $file_image ||
			'Error-3' === $file_image ||
			'Error-4' === $file_image ||
			'Error-5' === $file_image ||
			empty( $file_image )
			) {

			$has_qr = redsys_qr_get_error( $file_image );
			?>
			<p><?php echo esc_html( $has_qr ); ?></p>
			<?php
			return;
		} else {
			update_post_meta( $post->ID, '_redsys_qr', $file_image );
			$has_qr = $file_image;
		}
	}
	?>
	<div id="redsys_qr_content_metabox"> 
	<?php

	if ( ! $has_qr ) {
		?>
		<?php
	} else {
		?>
		<style>
			.redsys-qr-code {
				max-width: 250px;
			}
		</style>
		<?php
		echo '<img class="redsys-qr-code" src="' . esc_url( $has_qr ) . '" />';
		echo '<p><a href="' . esc_url( $has_qr ) . '" target="_blank">' . esc_html__( 'Visit QR Code', 'woocommerce-redsys' ) . '</a></p>';
		echo '<p><a href="' . esc_url( $regenerate ) . '" style="color:#b32d2e;">' . esc_html__( 'Regenerate QR Code', 'woocommerce-redsys' ) . '</a></p>';

	}
	?>
</div>
	<?php
}
/**
 * Add Redsys metabox to order page
 *
 * @param WP_Post $post_or_order_object Post object.
 */
function add_redsys_meta_box( $post_or_order_object ) {

	$order_id = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;

	if ( WCRed()->is_redsys_order( $order_id ) ) {

		$date         = WCRed()->get_order_date( $order_id );
		$hour         = WCRed()->get_order_hour( $order_id );
		$auth         = WCRed()->get_order_auth( $order_id );
		$number       = WCRed()->get_order_mumber( $order_id );
		$auth_refund  = WCRed()->get_order_auth_refund( $order_id );
		$paygold_link = WCRed()->get_order_pay_gold_link( $order_id );

		echo '<h4>' . esc_html__( 'Payment Details', 'woocommerce-redsys' ) . '</h4>';
		echo '<p><strong>' . esc_html__( 'Paid with', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( WCRed()->get_gateway( $order_id ) ) . '</p>';
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
		if ( $auth_refund ) {
			echo '<p><strong>' . esc_html__( 'Redsys Authorisation Code Refund', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $auth_refund ) . '</p>';
		}
		if ( $paygold_link ) {
			echo '<p><strong>' . esc_html__( 'PayGold Link', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $paygold_link ) . '</p>';
		}
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' );

/**
 * Add Redsys metabox to order page
 *
 * @param Array $tabs Tabs.
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
 * Adding tabs to custom panel.
 */
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
		<div class="options_group">
			<?php
			$field = array(
				'id'          => '_redsyspreauth',
				'label'       => __( 'Preauthorization', 'woocommerce-redsys' ),
				'description' => __( 'This product requires preauthorization. Check this option and Redsys will preauthorize the order.', 'woocommerce-redsys' ),
			);
			woocommerce_wp_checkbox( $field );
			?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'redsys_tab_panel' );

/**
 * Save Redsys metabox to order page
 *
 * @param Int $post_id Post ID.
 */
function save_redsys_product( $post_id ) {

	if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return;
	}

	$product       = wc_get_product( $post_id );
	$redsystokenr  = isset( $_POST['_redsystokenr'] ) ? sanitize_text_field( wp_unslash( $_POST['_redsystokenr'] ) ) : 'no';
	$redsyspreauth = isset( $_POST['_redsyspreauth'] ) ? sanitize_text_field( wp_unslash( $_POST['_redsyspreauth'] ) ) : 'no';
	$product->update_meta_data( '_redsystokenr', $redsystokenr );
	$product->update_meta_data( '_redsyspreauth', $redsyspreauth );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_redsys_product' );

// PayGold.
/**
 * Add PayGold metabox to order page
 *
 * @param Object $post_or_order_object Order Object.
 */
function paygold_metabox( $post_or_order_object ) {

	$debug  = new WC_Logger();
	$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
		? wc_get_page_screen_id( 'shop-order' )
		: 'shop_order';

	if ( ! isset( $_GET['id'] ) && ! isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( isset( $_GET['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order_id = sanitize_text_field( wp_unslash( $_GET['id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} else {
		$order_id = sanitize_text_field( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	}
	$debug->add( 'metabox', '$order_id: ' . $order_id );
	if ( WCRed()->order_exist( $order_id ) ) {
		$debug->add( 'metabox', 'Is shop_order' );
		$order   = WCRed()->get_order( $order_id );
		$gateway = $order->get_payment_method();

		$debug->add( 'metabox', '$gateway: ' . $gateway );

		if ( WCRed()->is_gateway_enabled( 'paygold' ) && ! WCRed()->is_paid( $order_id ) && 'paygold' === $gateway ) {
			add_meta_box(
				'paygold',
				__( 'Send PayGold Link', 'woocommerce-redsys' ),
				'paygold_meta_box_content',
				$screen,
				'normal',
				'core'
			);
		}
	} else {
		$debug->add( 'metabox', 'Is NOT shop_order' );
	}
}
add_action( 'add_meta_boxes', 'paygold_metabox' );

/**
 * Add PayGold metabox content to order page
 *
 * @param Object $post Post Object.
 */
function paygold_meta_box_content( $post ) {

	$check = 'off';
	if ( WCRed()->check_soap( 'real' ) || ! function_exists( 'SimpleXMLElement' ) ) {
		?>
		<div id="order_data" class="panel woocommerce-order-data">	
			<h2 class="woocommerce-order-data__heading">
				<?php esc_html_e( 'This is for send a Pay Gold Link.', 'woocommerce-redsys' ); ?>
			</h2>
			<p class="woocommerce-order-data__meta order_number">
				<?php esc_html_e( 'Please, fill the fields and the link will be sent by email or SMS. Only fill one field and then press "Update"', 'woocommerce-redsys' ); ?>
			</p>
			<p class="woocommerce-order-data__meta order_number">
				<?php esc_html_e( 'The link will be saved and shown in the Order Details metabox.', 'woocommerce-redsys' ); ?>
			</p>
			<p class="woocommerce-order-data__meta order_number">
				<?php esc_html_e( 'If you need to send a new link, fill again all fields, and press "Update" again.', 'woocommerce-redsys' ); ?>
			</p>
			<p class="form-field form-field-wide">
				<input type="checkbox" id="paygold_send_link" name="paygold_send_link" <?php checked( $check, 'on' ); ?> />  
				<label for="paygold_send_link"><?php esc_html_e( 'Send New PayGold Link', 'woocommerce-redsys' ); ?></label>
			</p>
			<p class="form-field form-field-wide">
				<label for="select_paygold_type"><?php esc_html_e( 'What do you want to send?', 'woocommerce-redsys' ); ?></label>
				<select name="select_paygold_type" id="select_paygold_type">  
					<option value="mail"><?php esc_html_e( 'Send Email', 'woocommerce-redsys' ); ?></option>  
					<option value="SMS"><?php esc_html_e( 'Send SMS', 'woocommerce-redsys' ); ?></option>
				</select>
			</p>
			<p class="form-field form-field-wide">
				<label for="sms_email_send_paygold"><?php esc_html_e( 'Type the Email or the Mobile Number:', 'woocommerce-redsys' ); ?></label>
				<input type="text" name="sms_email_send_paygold" id="sms_email_send_paygold" value="" />
			</p>
		</div>
		<?php
		wp_nonce_field( 'paygold_meta_box_nonce', 'paygold_box_nonce' );
	} else {
		?>
		<p class="woocommerce-order-data__meta order_number">
			<?php esc_html_e( 'Please contact with your hosting provider and ask for SOAP and SimpleSML. WooCommerce Redsys Gateway cannot contact via SOAP with https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl or read the response with SimpleXML so is not possible to use PayGold', 'woocommerce-redsys' ); ?>
		</p>
		<?php
	}
}
/**
 * Save the metabox data
 *
 * @param int $order_id Order ID.
 */
function paygold_metabox_save( $order_id ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$debug = new WC_Logger();
		$debug->add( 'metabox', 'arrive to $paygold_metabox_save' );
	}

	if ( ! isset( $_POST['paygold_box_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['paygold_box_nonce'] ), 'paygold_meta_box_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return;
	}

	if ( isset( $_POST['paygold_send_link'] ) ) {
		$check = sanitize_text_field( wp_unslash( $_POST['paygold_send_link'] ) );
	} else {
		$check = 'off';
	}

	if ( 'off' === $check ) {
		return;
	}

	if ( isset( $_POST['select_paygold_type'] ) && isset( $_POST['sms_email_send_paygold'] ) ) {
		$type    = sanitize_text_field( wp_unslash( $_POST['select_paygold_type'] ) );
		$send_to = sanitize_text_field( wp_unslash( $_POST['sms_email_send_paygold'] ) );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug->add( 'metabox', '$order_id: ' . $order_id );
			$debug->add( 'metabox', '$type: ' . $type );
			$debug->add( 'metabox', '$send_to: ' . $send_to );
		}
		$data   = array(
			'user_id'     => '',
			'token_type'  => '',
			'send_type'   => $type,
			'send_to'     => $send_to,
			'description' => $description,
		);
		$result = WCRed()->send_paygold_link( $order_id, $data );
		WCRed()->set_order_paygold_link( $order_id, $result );
	}
}
add_action( 'woocommerce_process_shop_order_meta', 'paygold_metabox_save' );
