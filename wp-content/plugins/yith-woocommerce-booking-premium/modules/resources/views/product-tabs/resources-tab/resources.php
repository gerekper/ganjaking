<?php
/**
 * Resources tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 *
 * @package YITH\Booking\Modules\Resources\Views
 */

defined( 'YITH_WCBK' ) || exit;

$resources_data = ! ! $booking_product ? $booking_product->get_resources_data( 'edit' ) : array();
$has_resources  = ! ! $resources_data;

$classes = array( 'yith-wcbk-booking-product-resources' );

if ( $has_resources ) {
	$classes[] = 'has-resources';
}

$resource_ids = array_keys( $resources_data );

?>
<div id="yith-wcbk-booking-product-resources" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-resource-ids="<?php echo esc_attr( wp_json_encode( $resource_ids ) ); ?>">
	<?php
	yith_plugin_fw_get_component(
		array(
			'class'    => 'yith-wcbk-booking-product-resources__blank-state',
			'type'     => 'list-table-blank-state',
			'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
			'message'  => __( 'No resources set for this product!', 'yith-booking-for-woocommerce' ),
			'cta'      => array(
				'class' => 'yith-wcbk-booking-product-resources__add',
				'title' => __( 'Add resource', 'yith-booking-for-woocommerce' ),
			),
		),
		true
	);
	?>
	<div id="yith-wcbk-booking-product-resources__list" class="yith-wcbk-settings-section-box__sortable-container">
		<?php
		foreach ( $resources_data as $resource_data ) {
			$resource_id    = $resource_data->get_resource_id( 'edit' );
			$resource       = yith_wcbk_get_resource( $resource_id );
			$resource_name  = ! ! $resource ? $resource->get_name( 'edit' ) : '#' . $resource_id;
			$resource_image = ! ! $resource ? $resource->get_image() : wc_placeholder_img();
			$opened         = false;
			yith_wcbk_get_module_view( 'resources', 'product-tabs/resources-tab/resource.php', compact( 'resource_data', 'resource_id', 'resource_name', 'resource_image', 'opened' ) );
		}
		?>
	</div>

	<span class="yith-plugin-fw__button yith-plugin-fw__button--add yith-wcbk-booking-product-resources__add"><?php echo esc_html( __( 'Add resource', 'yith-booking-for-woocommerce' ) ); ?></span>

</div>
