<?php
/**
 * Person types fields in booking form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/persons/person-types.php
 *
 * @var WC_Product_Booking $product      The booking product.
 * @var array              $person_types Person types.
 *
 * @package YITH\Booking\Modules\People\Templates
 */

defined( 'YITH_WCBK' ) || exit;
?>

<?php foreach ( $person_types as $person_type ) : ?>
	<?php
	$person_type_id        = $person_type['id'];
	$default_person_number = yith_wcbk_get_query_string_param( 'person_type_' . $person_type_id );
	$min                   = max( 0, $person_type['min'] );
	$max                   = $person_type['max'] ?? 0;
	?>
	<div class="yith-wcbk-form-section yith-wcbk-form-section-person-types">
		<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( yith_wcbk()->person_type_helper()->get_person_type_title( $person_type_id ) ); ?></label>
		<div class="yith-wcbk-form-section__content">
			<?php
			$custom_attributes = array(
				'step' => 1,
				'min'  => $min,
			);

			if ( $max > 0 ) {
				$custom_attributes['max'] = $max;
			}

			yith_wcbk_print_field(
				array(
					'type'              => 'number',
					'id'                => 'yith-wcbk-booking-persons-type-' . $person_type_id,
					'name'              => "person_types[$person_type_id]",
					'data'              => array(
						'person-type-id' => $person_type_id,
					),
					'custom_attributes' => $custom_attributes,
					'value'             => max( $min, $default_person_number ),
					'class'             => 'yith-wcbk-booking-person-types yith-wcbk-number-minifield',
				)
			);
			?>
		</div>
	</div>
<?php endforeach; ?>
