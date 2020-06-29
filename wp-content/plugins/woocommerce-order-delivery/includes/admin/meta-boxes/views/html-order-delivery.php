<?php
/**
 * Meta box view: Order delivery
 *
 * @package WC_OD/Admin/Meta Boxes
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variables.
 *
 * @var array    $fields
 * @var WC_Order $order
 */
?>
<div class="wc-od-order-delivery wc-od-metabox">
	<?php
	foreach ( $fields as $key => $field ) :
		// Backward compatibility.
		if ( ! isset( $field['id'] ) ) {
			$field['id'] = '_' . ltrim( '_', $key );
		}

		/**
		 * Filters the label of the order details field.
		 *
		 * The dynamic portion of the hook name, `$key`, refers to the field name. Since 1.4.0.
		 *
		 * @since 1.1.0
		 * @deprecated 1.4.0 Use the wc_od_admin_order_details_fields hook instead.
		 *
		 * @param string   $label The field label.
		 * @param WC_Order $order The order instance.
		 */
		$field['label'] = apply_filters( "wc_od_admin_{$key}_field_label", $field['label'], $order );

		wc_od_admin_field( $field );
	endforeach;
	?>
</div>
