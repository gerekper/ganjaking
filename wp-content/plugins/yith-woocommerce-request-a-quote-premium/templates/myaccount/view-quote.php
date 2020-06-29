<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Woocommerce Request A Quote
 */

/**
 * Quote Detail
 *
 * Shows recent orders on the account page
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 *
 * @var $order_id int
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
YITH_YWRAQ_Order_Request()->is_expired( $order_id );

$order = wc_get_order( $order_id );
add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

if ( ! $order ) {
	esc_html_e( 'This Quote doesn\'t exist.', 'yith-woocommerce-request-a-quote' );

	return;
}

$user_email       = yit_get_prop( $order, 'ywraq_customer_email', true );
$customer_message = yit_get_prop( $order, 'ywraq_customer_message', true );
$af4              = yit_get_prop( $order, 'ywraq_other_email_fields', true );
$admin_message    = yit_get_prop( $order, '_ywcm_request_response', true );
$exdata           = yit_get_prop( $order, '_ywcm_request_expire', true );
$order_date       = strtotime( yit_get_prop( $order, 'date_created', true ) );

if ( $order->get_user_id() !== $current_user->ID ) {
	esc_html_e( 'You do not have permission to read the quote.', 'yith-woocommerce-request-a-quote' );

	return;
}

if ( $order->get_status() === 'trash' ) {
	esc_html_e( 'This Quote was deleted by administrator.', 'yith-woocommerce-request-a-quote' );

	return;
}

$show_price        = ! ( get_option( 'ywraq_hide_price' ) === 'yes' && $order->get_status() === 'ywraq-new' );
$show_total_column = ! ( get_option( 'ywraq_hide_total_column', 'yes' ) === 'yes' && $order->get_status() === 'ywraq-new' );
$colspan           = $show_total_column ? 1 : 2;

if ( $order->get_status() === 'ywraq-new' ) {

	if ( catalog_mode_plugin_enabled() ) {

		foreach ( $order->get_items() as $item_id => $item ) {
			/* @var $_product WC_Product */
			$_product   = $item->get_product();
			$hide_price = apply_filters( 'yith_ywraq_hide_price_template', WC()->cart->get_product_subtotal( $_product, $item['qty'] ), $_product->get_id(), $item );
			if ( '' === $hide_price ) {
				$show_price = false;
			}
		}
	}
}

?>

<p>
	<strong><?php esc_html_e( 'Request date', 'yith-woocommerce-request-a-quote' ); ?></strong>: <?php echo esc_html( date_i18n( wc_date_format(), $order_date ) ); ?>
</p>
<?php

$accept_button_text = ( YITH_Request_Quote()->enabled_checkout() && $order->get_status() !== 'ywraq-pending' ) ? __( 'Checkout', 'yith-woocommerce-request-a-quote' ) : ywraq_get_label( 'accept' );

$pdf_file = false;

if ( file_exists( YITH_Request_Quote_Premium()->get_pdf_file_path( $order_id ) ) ) {
	$pdf_file = YITH_Request_Quote_Premium()->get_pdf_file_url( $order_id );
}
$print_button_pdf = get_option( 'ywraq_pdf_in_myaccount' ) === 'yes' && $pdf_file;

if ( in_array( $order->get_status(), array( 'ywraq-pending' ) ) ) :
	?>
	<p class="ywraq-buttons">
		<?php
		if ( $print_button_pdf ) {
			?>
			<a class="ywraq-big-button ywraq-pdf-file"
											  href="<?php echo esc_url( $pdf_file ); ?>"
											  target="_blank"><?php esc_html_e( 'Download PDF', 'yith-woocommerce-request-a-quote' ); ?></a><?php } ?>
		<?php
		if ( get_option( 'ywraq_show_accept_link' ) !== 'no' ) :
			?>
			<a class="ywraq-big-button ywraq-accept"  href="<?php echo esc_url( ywraq_get_accepted_quote_page( $order ) ); ?>"><?php echo esc_html( $accept_button_text ); ?></a><?php endif ?>
		<?php
		if ( get_option( 'ywraq_show_reject_link' ) !== 'no' ) :
			?>
			 <a class="ywraq-big-button ywraq-reject"
																			href="<?php echo esc_url( ywraq_get_rejected_quote_page( $order ) ); ?>"><?php esc_html( ywraq_get_label( 'reject', true ) ); ?></a><?php endif ?>
	</p>

<?php elseif ( $order->get_status() === 'ywraq-accepted' ) : ?>
	<p class="ywraq-buttons">
		<?php
		if ( $print_button_pdf ) {
			?>
			<a class="ywraq-big-button ywraq-pdf-file"
											  href="<?php echo esc_url( $pdf_file ); ?>"
											  target="_blank"><?php esc_html_e( 'Download PDF', 'yith-woocommerce-request-a-quote' ); ?></a><?php } ?>
		<?php
		if ( get_option( 'ywraq_show_accept_link' ) !== 'no' && YITH_Request_Quote()->enabled_checkout() ) :
			?>
			<a class="ywraq-big-button ywraq-accept" href="
			<?php
			echo esc_url(
				add_query_arg(
					array(
						'request_quote' => $order_id,
						'status'        => 'accepted',
						'raq_nonce'     => ywraq_get_token( 'accept-request-quote', $order_id, $user_email ),
						'lang'          => get_post_meta( $order_id, 'wpml_language', true ),
					),
					YITH_Request_Quote()->get_raq_page_url()
				)
			)
			?>
			"><?php echo esc_html( $accept_button_text ); ?>
			</a>
		<?php endif ?>
	</p>
	<?php
else :
	?>
	<p>
		<strong><?php echo esc_html__( 'Order Status:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
	</p>
	<?php if ( $order->has_status( 'ywraq-rejected' ) && $order->get_customer_note() ) : ?>
	<p>
		<strong><?php echo esc_html( __( 'Customer reason:', 'yith-woocommerce-request-a-quote' ) ); ?></strong> <?php echo esc_html( $order->get_customer_note() ); ?>
	</p>
<?php endif; ?>
	<?php
	if ( $print_button_pdf ) {
		?>
		<p class="ywraq-buttons"><a class="ywraq-big-button ywraq-pdf-file"
																   href="<?php echo esc_url( $pdf_file ); ?>"
																   target="_blank"><?php esc_html_e( 'Download PDF', 'yith-woocommerce-request-a-quote' ); ?></a>
	</p><?php } ?>
<?php endif ?>
<h2><?php esc_html_e( 'Quote Details', 'yith-woocommerce-request-a-quote' ); ?></h2>

<?php if ( '' !== $exdata ) : ?>
	<p>
		<strong><?php esc_html_e( 'Expiration date', 'yith-woocommerce-request-a-quote' ); ?></strong>: <?php echo esc_html( date_i18n( wc_date_format(), strtotime( $exdata ) ) ); ?>
	</p>
<?php endif ?>

<table class="shop_table order_details">
	<thead>
	<tr>
		<th class="product-name"
			colspan="<?php echo esc_attr( $colspan ); ?>"><?php esc_html_e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php if ( $show_total_column && $show_price ) : ?>
			<th class="product-total"><?php esc_html_e( 'Total', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php endif ?>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( count( $order->get_items() ) > 0 ) {

		foreach ( $order->get_items() as $item_id => $item ) {
			/** @var $_product WC_Product */
			$_product = $item->get_product();

			// retro compatibility.
			$item_meta = false;
			$title     = $_product ? $_product->get_title() : $item->get_name();

			if ( $_product && $_product->get_sku() !== '' && get_option( 'ywraq_show_sku' ) === 'yes' ) {
				$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
				$title .= ' ' . apply_filters( 'ywraq_sku_label_html', $sku, $_product );
			}

			if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) :
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<td class="product-name">
						<?php

						if ( ! $_product || ( $_product && ! $_product->is_visible() ) || ! apply_filters( 'ywraq_list_show_product_permalinks', true, 'view_quote' ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $title, $item, false ) );
						} else {
							echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $item['product_id'] ) ), esc_html( $title ) ), $item, true ); //phpcs:ignore
						}

						echo wp_kses_post( apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', esc_html( $item['qty'] ) ) . '</strong>', $item ) );

						// Allow other plugins to add additional product information here.
						do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

						if ( $item_meta ) {
							$item_meta->display();
						} else {
							wc_display_item_meta( $item );
						}

						// Allow other plugins to add additional product information here.
						do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
						?>
					</td>
					<?php if ( $show_price ) : ?>
						<td class="product-total">
							<?php

							echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) );

							?>
						</td>
					<?php endif ?>
				</tr>
				<?php

			endif;

			if ( $order->has_status( array( 'completed', 'processing' ) ) && $_product->get_purchase_note() ) :
				?>
				<tr class="product-purchase-note">
					<td colspan="3"><?php echo wpautop( is_callable( 'apply_shortcodes' ) ? apply_shortcodes( wp_kses_post( $_product->get_purchase_note() ) ) : do_shortcode( wp_kses_post( $_product->get_purchase_note() ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<?php
			endif;
		}
	}

	do_action( 'woocommerce_order_items_table', $order );
	?>
	</tbody>
	<tfoot>
	<?php
	$has_refund = false;

	if ( $order->get_total_refunded() ) {
		$has_refund = true;
	}

	$totals = $order->get_order_item_totals();
	if ( $show_total_column && $totals ) {
		foreach ( $totals as $key => $total ) {
			$value = $total['value'];

			?>
			<?php if ( $show_price ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
					<td><?php echo wp_kses_post( $value ); ?></td>
				</tr>
			<?php endif ?>
			<?php
		}
	}
	?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<header>
	<h2><?php esc_html_e( 'Customer\'s details', 'yith-woocommerce-request-a-quote' ); ?></h2>
</header>
<table class="shop_table shop_table_responsive customer_details">

	<?php

	$user_name         = yit_get_prop( $order, 'ywraq_customer_name', true );
	$billing_name      = yit_get_prop( $order, '_billing_first_name', true );
	$billing_surname   = yit_get_prop( $order, '_billing_last_name', true );
	$billing_company   = yit_get_prop( $order, '_billing_company', true );
	$billing_address_1 = yit_get_prop( $order, '_billing_address_1', true );
	$billing_address_2 = yit_get_prop( $order, '_billing_address_2', true );
	$billing_city      = yit_get_prop( $order, '_billing_city', true );
	$billing_postcode  = yit_get_prop( $order, '_billing_postcode', true );
	$billing_state     = yit_get_prop( $order, '_billing_state', true );
	$billing_country   = yit_get_prop( $order, '_billing_country', true );
	$billing_email     = yit_get_prop( $order, '_billing_email', true );
	$billing_email     = empty( $billing_email ) ? $user_email : $billing_email;
	$billing_phone     = yit_get_prop( $order, '_billing_phone', true );
	$billing_phone     = empty( $billing_phone ) ? yit_get_prop( $order, 'ywraq_billing_phone', true ) : $billing_phone;
	$billing_vat       = yit_get_prop( $order, 'ywraq_billing_vat', true );
	$billing_vat       = empty( $billing_vat ) ? yit_get_prop( $order, '_billing_vat', true ) : $billing_vat;


	$content = ( empty( $billing_name ) && empty( $billing_surname ) ) ? $user_name : $billing_name . ' ' . $billing_surname;
	printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Name:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Name:', 'yith-woocommerce-request-a-quote' ), esc_html( $content ) );

	if ( $billing_company ) {
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Company:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Company:', 'yith-woocommerce-request-a-quote' ), esc_html( $billing_company ) );
	}

	if ( $billing_address_1 || $billing_address_2 ) {
		$content = $billing_address_1 . ( $billing_address_1 ? '<br />' : '' ) . $billing_address_2;
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Address:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Address:', 'yith-woocommerce-request-a-quote' ), esc_html( $content ) );
	}

	if ( $billing_city ) {
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'City:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'City:', 'yith-woocommerce-request-a-quote' ), esc_html( $billing_city ) );
	}

	if ( $billing_postcode ) {
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Postcode:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Postcode:', 'yith-woocommerce-request-a-quote' ), esc_html( $billing_postcode ) );
	}

	if ( $billing_state ) {
		$states  = WC()->countries->get_states( $billing_country );
		$state   = $states[ $billing_state ];
		$content = ( '' === $state ) ? $billing_state : $state;
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'State/Province:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'State/Province:', 'yith-woocommerce-request-a-quote' ), esc_html( $content ) );
	}

	if ( $billing_country ) {
		$countries = WC()->countries->get_countries();
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Country:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Country:', 'yith-woocommerce-request-a-quote' ), esc_html( $countries[ $billing_country ] ) );
	}

	if ( $billing_email ) {
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Email:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Email:', 'yith-woocommerce-request-a-quote' ), esc_html( $billing_email ) );
	}

	if ( $billing_phone ) {
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'Telephone:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'Telephone:', 'yith-woocommerce-request-a-quote' ), esc_html( $billing_phone ) );
	}

	if ( $billing_vat ) {
		printf( '<tr><th>%s</th><td data-title="%s">%s</td></tr>', esc_html__( 'VAT:', 'yith-woocommerce-request-a-quote' ), esc_attr__( 'VAT:', 'yith-woocommerce-request-a-quote' ), esc_html( $billing_vat ) );
	}

	// Additional customer details hook.
	do_action( 'woocommerce_order_details_after_customer_details', $order );
	?>
</table>

<?php

if ( '' !== $customer_message || ! empty( $af4 ) || '' !== $admin_message ) :
	?>
	<header>
		<h2><?php esc_html_e( 'Additional Information', 'yith-woocommerce-request-a-quote' ); ?></h2>
	</header>
	<table class="shop_table shop_table_responsive customer_details">
		<?php
			// Check for customer note.
		if ( '' !== $customer_message ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Customer\'s Message:', 'yith-woocommerce-request-a-quote' ); ?></th>
				<td data-title="<?php esc_html_e( 'Customer\'s Message', 'yith-woocommerce-request-a-quote' ); ?>"><?php echo wp_kses_post( wptexturize( $customer_message ) ); ?></td>
			</tr>
			<?php
			endif;


		if ( ! empty( $af4 ) ) :
			foreach ( $af4 as $key => $value ) :
				?>
				<tr>
					<th scope="row"><?php echo wp_kses_post( ucwords( str_replace( '-', ' ', $key ) ) ); ?></th>
					<td data-title="<?php echo esc_attr( ucwords( str_replace( '-', ' ', $key ) ) ); ?>"><?php echo wp_kses_post( $value ); ?></td>
				</tr>
				<?php
			endforeach;
		endif;

		if ( '' !== $admin_message ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Administrator\'s Message:', 'yith-woocommerce-request-a-quote' ); ?></th>
				<td data-title="<?php esc_html_e( 'Administrator\'s Message', 'yith-woocommerce-request-a-quote' ); ?>"><?php echo wp_kses_post( wptexturize( $admin_message ) ); ?></td>
			</tr>
		<?php endif; ?>

	</table>
<?php endif ?>
<div class="clear"></div>
