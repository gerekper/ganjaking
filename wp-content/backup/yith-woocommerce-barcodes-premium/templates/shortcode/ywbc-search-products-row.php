<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/** @var WC_Product $object */

$product_id = yit_get_prop( $object, 'id' );
?>
<tr class="ywbc-search-row">

    <td valign="top" style="width: 16%;">
        <?php echo $object->get_image(); ?>
    </td>

	<td valign="top" >
        <?php
        $url = $object->get_permalink();
        ?><a href="<?php echo $url ?>"><?php echo $object->get_name() . ' ' ?>
	</td>

	<td valign="top">
		<?php
		$barcode_instance = YITH_Barcode::get( $product_id );
		echo YITH_YWBC()->render_barcode( $barcode_instance );
		echo "<div style='text-align: center'>" . $barcode_instance->get_display_value() . "</div>"; ?>
	</td>

	<td valign="top">
		<div>
			<?php if ( yit_get_prop($object,'manage_stock') ):
				printf( esc_html__( "Stock units: %s", 'yith-woocommerce-barcodes' ), $object->get_stock_quantity() );
			else:
				esc_html_e( "Manage stock not enabled", 'yith-woocommerce-barcodes' );
			endif; ?>
		</div>
	</td>

	<td valign="top">
		<?php foreach ( $barcode_actions as $barcode_action ): ?>
			<button style="width: 100%; margin-bottom: 15px;"
			        class="ywbc-action btn btn-primary"
			        data-status="<?php echo sanitize_title_for_query( strtolower( $barcode_action ) ); ?>"
			        data-id="<?php echo $product_id; ?>"
			        data-type="product">
				<?php echo $barcode_action; ?>
			</button>
		<?php endforeach; ?>
	</td>
</tr>
