<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$order_id     = yit_get_prop( $object, 'id' );
$item_count   = $object->get_item_count();
$order_date   = yit_get_prop( $object, 'order_date' );
$order_status = $object->get_status();
?>
<tr class="ywbc-search-row">
	<td valign="top">
		<?php //    Order title
		if ( $object->get_customer_id() ) {
			$user     = get_user_by( 'id', $object->get_customer_id() );
			$user_display_name = is_object($user) ? $user->display_name : '';
			$username = '<a href="user-edit.php?user_id=' . absint( $object->get_customer_id() ) . '">';
			$username .= esc_html( ucwords( $user_display_name ) );
			$username .= '</a>';
		} elseif ( $object->get_billing_first_name() || $object->get_billing_last_name() ) {
			/* translators: 1: first name 2: last name */
			$username = trim( sprintf( esc_html_x( '%1$s %2$s', 'full name', 'yith-woocommerce-barcodes' ), $object->get_billing_first_name(), $object->get_billing_last_name() ) );
		} elseif ( $object->get_billing_company() ) {
			$username = trim( $object->get_billing_company() );
		} else {
			$username = esc_html__( 'Guest', 'yith-woocommerce-barcodes' );
		}
		
		/* translators: 1: order and number (i.e. Order #13) 2: user name */
		printf(
			esc_html__( '%1$s by %2$s', 'yith-woocommerce-barcodes' ),
			'<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $object->get_order_number() ) . '</strong></a>',
			$username );
		
		echo '<br>(' . date_i18n( get_option( 'date_format' ), strtotime( $order_date ) ) . ')';
		
		if ( $object->get_billing_email() ) : ?>
			<br>
			<small class="meta email">
				<a href="<?php echo esc_url( 'mailto:' . $object->get_billing_email() ); ?>"><?php echo esc_html( $object->get_billing_email() ); ?></a>
			</small>
		<?php endif; ?>
	</td>
	<td valign="top">
		<?php printf( esc_html_x( 'Status: %1$s', 'Order status label', 'yith-woocommerce-barcodes' ), '<span class="ywbc-order-status">' . wc_get_order_status_name( $object->get_status() ) . '</span>' ); ?>
		<br>
		<?php printf( esc_html__( _nx( 'Total: %s for %s item', 'Total:%s for %s items', $item_count, 'Order amount', 'yith-woocommerce-barcodes' ) ), $object->get_formatted_order_total(), $item_count ); ?>
	</td>
	<td valign="top">
		<?php
		$barcode_instance = YITH_Barcode::get( $order_id );
		echo YITH_YWBC()->render_barcode( $barcode_instance );
		echo "<div style='text-align: center'>" . $barcode_instance->get_display_value() . "</div>"; ?>
	</td>

    <?php if ( !empty($barcode_actions)) { ?>

	<td valign="top">
		<?php foreach ( $barcode_actions as $barcode_action ): ?>
			<button style="width: 100%; margin-bottom: 15px;"
			        class="ywbc-action btn btn-primary"
			        data-status="<?php echo sanitize_title_for_query( strtolower( $barcode_action ) ); ?>"
			        data-type="order"
			        data-id="<?php echo $order_id; ?>"
				<?php echo ( $order_status == $barcode_action ) ? 'disabled' : ''; ?>>
				<?php echo $barcode_action; ?>
			</button>
		<?php endforeach; ?>
	</td>
    <?php } ?>
</tr>
