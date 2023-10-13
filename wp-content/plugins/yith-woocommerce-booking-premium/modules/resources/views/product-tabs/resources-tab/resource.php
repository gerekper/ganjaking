<?php
/**
 * Resources tab in WC Product Panel
 *
 * @var YITH_WCBK_Resource_Data $resource_data  The resource data.
 * @var int                     $resource_id    The resource ID.
 * @var string                  $resource_name  The resource name.
 * @var string                  $resource_image The resource image URL.
 * @var bool                    $opened         Is opened?
 *
 * @package YITH\Booking\Modules\Resources\Views
 */

defined( 'YITH_WCBK' ) || exit;

$the_id    = $resource_id;
$prefix    = "_yith_booking_resources_data[$the_id]";
$id_prefix = "yith_booking_resource_data_{$the_id}_";
$opened    = $opened ?? true;

$classes = array(
	'yith-wcbk-booking-product-resource',
	'yith-wcbk-settings-section-box',
	! $opened ? 'yith-wcbk-settings-section-box--closed' : false,
);
$classes = implode( ' ', array_filter( $classes ) );
?>
<div class="<?php echo esc_attr( $classes ); ?>" data-id="<?php echo esc_attr( $the_id ); ?>">
	<?php
	yith_wcbk_print_field(
		array(
			'type'  => 'hidden',
			'name'  => $prefix . '[resource_id]',
			'value' => $the_id,
		)
	);
	?>

	<div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
		<h3>
			<?php
			echo wp_kses_post( $resource_image );

			echo sprintf(
				'<span>%s <small>#%s</small></span>', // Use %s instead of %d for the ID to allow JS templates.
				esc_html( $resource_name ),
				esc_html( $the_id )
			);
			?>
		</h3>
		<span class="yith-wcbk-settings-section-box__toggle">
			<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"></path>
			</svg>
		</span>
	</div>

	<div class="yith-wcbk-settings-section-box__content">
		<?php
		yith_wcbk_form_field(
			array(
				// translators: %s is the duration; ex: Base price for 1 day.
				'title'  => sprintf( __( 'Base price for %s', 'yith-booking-for-woocommerce' ), yith_wcbk_product_metabox_dynamic_duration() ),
				'fields' => array(
					array(
						'yith-field'        => true,
						'type'              => 'text',
						'value'             => wc_format_localized_price( $resource_data->get_base_price( 'edit' ) ),
						'name'              => $prefix . '[base_price]',
						'class'             => 'wc_input_price yith-wcbk-mini-field',
						'custom_attributes' => array(
							'autocomplete' => 'off',
						),
					),
					array(
						'type'   => 'section',
						'class'  => 'yith-wcbk-settings-checkbox-container bk_show_if_booking_has_persons',
						'fields' => array(
							array(
								'type'  => 'checkbox',
								'value' => wc_bool_to_string( $resource_data->get_multiply_base_price_per_person( 'edit' ) ),
								'id'    => $id_prefix . '_multiply_base_price_per_person',
								'name'  => $prefix . '[multiply_base_price_per_person]',
							),
							array(
								'type'  => 'label',
								'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
								'for'   => $id_prefix . '_multiply_base_price_per_person',
							),
						),
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					array(
						'yith-field'        => true,
						'type'              => 'text',
						'value'             => wc_format_localized_price( $resource_data->get_fixed_price( 'edit' ) ),
						'name'              => $prefix . '[fixed_price]',
						'class'             => 'wc_input_price yith-wcbk-mini-field',
						'custom_attributes' => array(
							'autocomplete' => 'off',
						),
					),
					array(
						'type'   => 'section',
						'class'  => 'yith-wcbk-settings-checkbox-container bk_show_if_booking_has_persons',
						'fields' => array(
							array(
								'type'  => 'checkbox',
								'value' => wc_bool_to_string( $resource_data->get_multiply_fixed_price_per_person( 'edit' ) ),
								'id'    => $id_prefix . '_multiply_fixed_price_per_person',
								'name'  => $prefix . '[multiply_fixed_price_per_person]',
							),
							array(
								'type'  => 'label',
								'value' => __( 'Multiply by the number of people', 'yith-booking-for-woocommerce' ),
								'for'   => $id_prefix . '_multiply_fixed_price_per_person',
							),
						),
					),
				),
			)
		);
		?>

		<div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
			<span class="yith-plugin-fw__button--trash yith-wcbk-booking-product-resource__remove"><?php echo esc_html( __( 'Remove resource', 'yith-booking-for-woocommerce' ) ); ?></span>
		</div>
	</div>

</div>
