<?php
/**
 * People selector field in booking form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/persons/people-selector.php
 *
 * @var WC_Product_Booking $product      The booking product.
 * @var array              $person_types Person types.
 *
 * @package YITH\Booking\Modules\People\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$min_persons        = $product->get_minimum_number_of_people();
$max_persons        = $product->get_maximum_number_of_people();
$people_selector_id = 'yith-wcbk-people-selector-' . $product->get_id();
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-people-selector">
	<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label' for="<?php echo esc_attr( $people_selector_id ); ?>"><?php echo esc_html( apply_filters( 'yith_wcbk_people_label', yith_wcbk_get_label( 'people' ), $product ) ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<div
				id="<?php echo esc_attr( $people_selector_id ); ?>"
				class="yith-wcbk-people-selector"
				data-min="<?php echo esc_attr( $min_persons ); ?>"
			<?php if ( $max_persons > 0 ) : ?>
				data-max="<?php echo esc_attr( $max_persons ); ?>"
			<?php endif; ?>
		>
			<div class="yith-wcbk-people-selector__toggle-handler">
				<span class="yith-wcbk-people-selector__totals"></span>
			</div>
			<div class="yith-wcbk-people-selector__fields-container">
				<?php foreach ( $person_types as $person_type ) : ?>
					<?php
					$default_person_number = yith_wcbk_get_query_string_param( 'person_type_' . $person_type['id'] );
					$min                   = max( 0, $person_type['min'] );
					$max                   = $person_type['max'] ?? 0;
					$person_title          = yith_wcbk()->person_type_helper()->get_person_type_title( $person_type['id'] );
					$value                 = max( $min, $default_person_number );
					?>
					<div
							id="yith-wcbk-booking-persons-type-<?php echo esc_attr( $person_type['id'] ); ?>"
							class="yith-wcbk-people-selector__field yith-wcbk-clearfix"
							data-min="<?php echo esc_attr( $min ); ?>"
						<?php if ( $max > 0 ) : ?>
							data-max="<?php echo esc_attr( $max ); ?>"
						<?php endif; ?>
						<?php if ( $value ) : ?>
							data-value="<?php echo esc_attr( $value ); ?>"
						<?php endif; ?>
					>
						<div class="yith-wcbk-people-selector__field__title"><?php echo esc_html( $person_title ); ?></div>
						<div class="yith-wcbk-people-selector__field__totals">
						<span class="yith-wcbk-people-selector__field__minus">
							<span class="yith-wcbk-people-selector__field__minus-wrap">
								<?php yith_wcbk_print_svg( 'minus' ); ?>
							</span>
						</span>
							<span class="yith-wcbk-people-selector__field__total"></span>
							<span class="yith-wcbk-people-selector__field__plus">
							<span class="yith-wcbk-people-selector__field__plus-wrap">
								<?php yith_wcbk_print_svg( 'plus' ); ?>
							</span>
						</span>
						</div>

						<input type="hidden" name="person_types[<?php echo esc_attr( $person_type['id'] ); ?>]" class="yith-wcbk-people-selector__field__value yith-wcbk-booking-person-types" data-person-type-id="<?php echo esc_attr( $person_type['id'] ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
					</div>
				<?php endforeach; ?>
				<div class="yith-wcbk-people-selector__fields-container__footer yith-wcbk-clearfix">
					<span class="yith-wcbk-people-selector__close-handler"><?php esc_html_e( 'Close', 'yith-booking-for-woocommerce' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</div>
