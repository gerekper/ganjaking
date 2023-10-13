<?php
/**
 * People type in product panel.
 *
 * @var int   $people_type_id
 * @var array $people_type
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$default_args = array(
	'enabled'    => 'no',
	'id'         => $people_type_id,
	'min'        => 0,
	'max'        => 0,
	'base_cost'  => '',
	'block_cost' => '',
	'title'      => '',
);

$args = wp_parse_args( $people_type, $default_args );

list( $enabled, $the_id, $min, $max, $base_cost, $block_cost ) = yith_plugin_fw_extract( $args, 'enabled', 'id', 'min', 'max', 'base_cost', 'block_cost' );

$default_toggle_class = is_numeric( $the_id ) ? 'yith-wcbk-settings-section-box--closed' : '';
?>
<div class="yith-wcbk-settings-section-box <?php echo esc_attr( $default_toggle_class ); ?>">
	<?php
	yith_wcbk_print_field(
		array(
			'type'  => 'hidden',
			'name'  => "_yith_booking_person_types[$the_id][id]",
			'value' => $the_id,
		)
	);
	?>

	<div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
		<h3><?php echo esc_html( get_the_title( $the_id ) ); ?></h3>
		<span class="yith-wcbk-settings-section-box__toggle">
			<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"></path>
			</svg>
		</span>
		<span class="yith-wcbk-settings-section-box__enabled">
		<?php
		yith_wcbk_print_field(
			array(
				'type'  => 'onoff',
				'id'    => 'yith_booking_person_type-enabled-' . $the_id,
				'name'  => "_yith_booking_person_types[$the_id][enabled]",
				'value' => 'yes' === $enabled ? 'yes' : 'no',
			)
		);
		?>
			</span>
	</div>
	<div class="yith-wcbk-settings-section-box__content">
		<div class="yith-wcbk-settings-section-box__content__row">
			<label><?php esc_html_e( 'Minimum', 'yith-booking-for-woocommerce' ); ?></label>
			<?php
			yith_wcbk_print_field(
				array(
					'type'              => 'number',
					'class'             => 'yith-wcbk-mini-field',
					'custom_attributes' => 'step="1" min="0"',
					'name'              => "_yith_booking_person_types[$the_id][min]",
					'value'             => $min,
				)
			);
			?>

			<label><?php esc_html_e( 'Maximum', 'yith-booking-for-woocommerce' ); ?></label>
			<?php
			yith_wcbk_print_field(
				array(
					'type'              => 'number',
					'class'             => 'yith-wcbk-mini-field',
					'custom_attributes' => 'step="1" min="0"',
					'name'              => "_yith_booking_person_types[$the_id][max]",
					'value'             => $max,
				)
			);
			?>
		</div>
		<div class="yith-wcbk-settings-section-box__description">
			<?php esc_html_e( 'Enter a minimum number required and a maximum number available of this type. Example: you can set min 1 adult or leave it empty.', 'yith-booking-for-woocommerce' ); ?>
		</div>

		<div class="yith-wcbk-settings-section-box__content__row">
			<label><?php esc_html_e( 'Base Price', 'yith-booking-for-woocommerce' ); ?></label>
			<?php
			yith_wcbk_print_field(
				array(
					'type'  => 'text',
					'name'  => "_yith_booking_person_types[$the_id][block_cost]",
					'class' => 'wc_input_price yith-wcbk-mini-field',
					'value' => wc_format_localized_price( $block_cost ),
				)
			);
			?>

			<label><?php esc_html_e( 'Base Fee', 'yith-booking-for-woocommerce' ); ?></label>
			<?php
			yith_wcbk_print_field(
				array(
					'type'  => 'text',
					'name'  => "_yith_booking_person_types[$the_id][base_cost]",
					'class' => 'wc_input_price yith-wcbk-mini-field',
					'value' => wc_format_localized_price( $base_cost ),
				)
			);
			?>
		</div>
		<div class="yith-wcbk-settings-section-box__description">
			<?php esc_html_e( 'Enter a customized base price and fixed base fee for this type. These prices will override prices set in Booking Costs.', 'yith-booking-for-woocommerce' ); ?>
		</div>
	</div>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_booking_product_after_single_person_type_options
	 * Hook to render something after the single person type options.
	 *
	 * @param array $args The person type settings.
	 */
	do_action( 'yith_wcbk_booking_product_after_single_person_type_options', $args );
	?>
</div>
