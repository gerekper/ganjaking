<?php
/**
 * Availability rule view.
 *
 * @var int                         $index
 * @var string                      $field_name
 * @var YITH_WCBK_Availability_Rule $availability_rule
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$default_toggle_class = is_numeric( $index ) ? 'yith-wcbk-settings-section-box--closed' : '';

$_field_name      = "{$field_name}[{$index}]";
$_field_id_prefix = "{$field_name}-id--{$index}__";

$_name = $availability_rule->get_name( 'edit' );
$_type = $availability_rule->get_type( 'edit' );

?>
<div class="yith-wcbk-settings-section-box yith-wcbk-availability-rule <?php echo esc_attr( $default_toggle_class ); ?>"
	data-index="<?php echo esc_attr( $index ); ?>"
>
	<?php
	yith_plugin_fw_get_field(
		array(
			'id'    => $_field_id_prefix . 'position',
			'type'  => 'hidden',
			'class' => 'yith-wcbk-settings-section-box__sortable-position',
			'name'  => $_field_name . '[position]',
			'value' => $index,
		),
		true,
		false
	);
	?>
	<div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
		<h3><?php echo esc_html( ! ! $_name ? $_name : __( 'Untitled', 'yith-booking-for-woocommerce' ) ); ?></h3>
		<span class="yith-wcbk-settings-section-box__toggle">
			<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"></path>
			</svg>
		</span>
		<span class="yith-wcbk-settings-section-box__enabled">
			<?php
			/**
			 * TODO: replace with plugin-fw onoff field.
			 * Note: need to edit the saving, since the fw field uses a checkbox that will not be passed in $_REQUEST if not checked
			 *
			 * @see YITH_WCBK_Settings::save_settings
			 */
			yith_wcbk_print_field(
				array(
					'id'    => $_field_id_prefix . 'enabled',
					'type'  => 'onoff',
					'name'  => $_field_name . '[enabled]',
					'value' => $availability_rule->get_enabled( 'edit' ),
				),
				true
			);
			?>
		</span>
	</div>
	<div class="yith-wcbk-settings-section-box__content">
		<?php
		yith_wcbk_form_field(
			array(
				'title'  => __( 'Rule name', 'yith-booking-for-woocommerce' ),
				'class'  => 'yith-wcbk-settings-section-box__edit-title',
				'fields' => array(
					array(
						'yith-field'                     => true,
						'id'                             => $_field_id_prefix . 'name',
						'type'                           => 'text',
						'class'                          => 'yith-wcbk-availability-rule__title-field',
						'value'                          => $_name,
						'name'                           => $_field_name . '[name]',
						'yith-wcbk-field-show-container' => false,
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Rule type', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					'yith-field' => true,
					'type'       => 'radio',
					'id'         => $_field_id_prefix . 'type',
					'class'      => 'yith-wcbk-availability-rule__type',
					'value'      => $_type,
					'default'    => 'month',
					'name'       => $_field_name . '[type]',
					'options'    => array(
						'specific' => esc_html__( 'Specific date', 'yith-booking-for-woocommerce' ) . '<div><small>' . esc_html__( 'Rules are valid only for the year selected.', 'yith-booking-for-woocommerce' ) . '<br />' . esc_html__( 'Example: you want to disable bookings from 14th August to 30th August of the current year.', 'yith-booking-for-woocommerce' ) . '</small></div>',
						'generic'  => esc_html__( 'Generic dates', 'yith-booking-for-woocommerce' ) . '<div><small>' . esc_html__( 'Rules are valid without any time limit, until you disable them.', 'yith-booking-for-woocommerce' ) . '<br />' . esc_html__( 'Example: you want to disable bookings for August each year, or all Sunday days of the year.', 'yith-booking-for-woocommerce' ) . '</small></div>',
					),
				),
			)
		);

		$date_ranges_html = '';
		$date_range_index = 1;
		$date_ranges      = $availability_rule->get_date_ranges( 'edit' );
		$date_ranges      = ! ! $date_ranges ? $date_ranges : array(
			array(
				'from' => '',
				'to'   => '',
			),
		);

		foreach ( $date_ranges as $date_range ) {

			ob_start();
			yith_wcbk_get_view(
				'product-tabs/utility/html-availability-rule-date-range.php',
				array(
					'field_name'       => $field_name,
					'index'            => $index,
					'date_range_index' => $date_range_index,
					'from'             => $date_range['from'],
					'to'               => $date_range['to'],
					'type'             => $availability_rule->get_type( 'edit' ),
				)
			);

			$date_ranges_html .= ob_get_clean();

			$date_range_index ++;
		}

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Apply rule', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					'type'   => 'section',
					'class'  => 'yith-wcbk-availability-rule__date-ranges',
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-availability-rule__date-ranges__list',
							'fields' => array(
								array(
									'type'  => 'html',
									'value' => $date_ranges_html,
								),
							),
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-availability-rule__date-ranges__actions',
							'fields' => array(
								array(
									'type'  => 'html',
									'value' => "<span class='yith-wcbk-admin-action-link yith-wcbk-availability-rule__date-ranges__add-range'>+ " . esc_html__( 'Add date range', 'yith-booking-for-woocommerce' ) . '</span>',
								),
							),
						),
					),
				),
			)
		);

		/**
		 * DO_ACTION: yith_wcbk_after_availability_rule_options
		 * Hook to output something in the "availability" tab in the bookable product edit page after the availability rule options.
		 *
		 * @param string                      $_field_name       The field name.
		 * @param string                      $_field_id_prefix  The field ID prefix.
		 * @param string                      $index             The index of the rule.
		 * @param YITH_WCBK_Availability_Rule $availability_rule The availability rule.
		 */
		do_action( 'yith_wcbk_after_availability_rule_options', $_field_name, $_field_id_prefix, $index, $availability_rule );

		$availabilities_html = '';
		$availabilities      = $availability_rule->get_availabilities( 'edit' );
		$availabilities      = ! ! $availabilities ? $availabilities : array( new YITH_WCBK_Availability() );
		$availability_index  = 0;
		foreach ( $availabilities as $availability ) {
			ob_start();
			yith_wcbk_get_view(
				'product-tabs/utility/html-availability.php',
				array(
					'main_class'   => 'yith-wcbk-availability-rule',
					'field_name'   => "{$field_name}[{$index}][availabilities]",
					'index'        => $availability_index,
					'availability' => $availability,
				)
			);

			$availabilities_html .= ob_get_clean();
			$availability_index ++;
		}

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Availability', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					'type'   => 'section',
					'class'  => 'yith-wcbk-availability-rule__availabilities',
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-availability-rule__availabilities__list',
							'fields' => array(
								array(
									'type'  => 'html',
									'value' => $availabilities_html,
								),
							),
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-availability-rule__availabilities__actions',
							'fields' => array(
								array(
									'type'  => 'html',
									'value' => "<span class='yith-wcbk-admin-action-link yith-wcbk-availability-rule__availabilities__add-availability'>+ " . esc_html__( 'Add options for specific days', 'yith-booking-for-woocommerce' ) . '</span>',
								),
							),
						),
					),
				),
			)
		);

		?>
		<div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
			<?php if ( ! empty( $add_button ) ) : ?>
				<span class="yith-plugin-fw__button--primary yith-plugin-fw__button--with-icon yith-wcbk-availability-rule__add-rule">
					<i class="yith-icon yith-icon-check"></i>
					<?php esc_html_e( 'Add rule', 'yith-booking-for-woocommerce' ); ?>
				</span>
			<?php endif; ?>
			<span class="yith-plugin-fw__button--trash yith-wcbk-availability-rule__delete-rule"><?php esc_html_e( 'Delete rule', 'yith-booking-for-woocommerce' ); ?></span>
		</div>
	</div>
</div>
