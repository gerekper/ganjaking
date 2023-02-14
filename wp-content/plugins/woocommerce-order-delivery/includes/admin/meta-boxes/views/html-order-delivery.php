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
	foreach ( $fields as $field ) :
		wc_od_admin_field( $field, $order );
	endforeach;
	?>
</div>
