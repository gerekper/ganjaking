<?php
/**
 * Resource Data Meta-box
 *
 * @var YITH_WCBK_Resource $resource The resource.
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div id="resource-data" class="yith-plugin-ui yith-wcbk-meta-box-wrapper">
	<?php
	yith_wcbk_form_field(
		array(
			'title'  => __( 'Image', 'yith-booking-for-woocommerce' ),
			'desc'   => __( 'Select the resource image.', 'yith-booking-for-woocommerce' ),
			'class'  => 'align-items-start',
			'fields' =>
				array(
					'yith-field' => true,
					'type'       => 'media',
					'value'      => $resource->get_image_id( 'edit' ),
					'id'         => 'image_id',
					'name'       => 'image_id',
					'store_as'   => 'id',
				),
		)
	);
	?>

	<?php
	yith_wcbk_form_field(
		array(
			'title'  => __( 'Available quantity', 'yith-booking-for-woocommerce' ),
			'desc'   => __( 'Select this resource\'s maximum available quantity. Set 0 (zero) for unlimited.', 'yith-booking-for-woocommerce' ),
			'fields' =>
				array(
					'yith-field'        => true,
					'type'              => 'number',
					'value'             => $resource->get_available_quantity( 'edit' ),
					'id'                => 'available_quantity',
					'name'              => 'available_quantity',
					'custom_attributes' => array(
						'step' => 1,
						'min'  => 0,
					),
				),
		)
	);

	$default_availabilities = $resource->get_default_availability( 'edit' );
	$default_availabilities = ! ! $default_availabilities ? $default_availabilities : array( new YITH_WCBK_Availability() );
	$field_name             = 'default_availability';
	ob_start();
	yith_wcbk_get_view( 'product-tabs/utility/html-default-availabilities.php', compact( 'default_availabilities', 'field_name' ) );
	$default_availabilities_html = ob_get_clean();

	yith_wcbk_form_field(
		array(
			'title'  => __( 'Set default availability', 'yith-booking-for-woocommerce' ),
			'desc'   => __( 'Set the default availability for this resource. You can override these options by using the additional availability rules below.', 'yith-booking-for-woocommerce' ),
			'fields' => array(
				'type'  => 'html',
				'value' => $default_availabilities_html,
			),
		)
	);

	$availability_rules = $resource->get_availability_rules( 'edit' );
	$field_name         = 'availability_rules';
	ob_start();
	yith_wcbk_get_view( 'product-tabs/utility/html-availability-rules.php', compact( 'availability_rules', 'field_name' ) );
	$availability_rules_html = ob_get_clean();

	yith_wcbk_form_field(
		array(
			'class'  => 'yith-wcbk-resource-data__availability-rules-row',
			'title'  => __( 'Additional availability rules', 'yith-booking-for-woocommerce' ),
			'fields' => array(
				'type'  => 'html',
				'value' => $availability_rules_html,
			),
		)
	);

	?>
</div>
