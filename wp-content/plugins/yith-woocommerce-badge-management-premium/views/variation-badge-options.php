<?php
/**
 * Variation Badge Options
 *
 * @var int[]  $badges        The badges.
 * @var string $schedule      If the badges are scheduled.
 * @var int    $schedule_from Schedule from date.
 * @var int    $schedule_to   Schedule to date.
 * @var int    $loop          The variation loop index.
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\Views
 *
 * @since   2.0
 */

$fields = array(
	'badges'        => array(
		'label'    => __( 'Badges', 'yith-woocommerce-badges-management' ),
		'type'     => 'ajax-posts',
		'name'     => 'yith_wcbm_badge_options[' . absint( $loop ) . '][badges]',
		'multiple' => true,
		'data'     => array(
			'placeholder' => __( 'Search Badges...', 'yith-woocommerce-badges-management' ),
			'post_type'   => YITH_WCBM_Post_Types::$badge,
		),
		'value'    => $badges,
	),
	'schedule'      => array(
		'label'    => __( 'Schedule', 'yith-woocommerce-badges-management' ),
		'type'     => 'onoff',
		'name'     => 'yith_wcbm_badge_options[' . absint( $loop ) . '][schedule]',
		'id'       => 'yith-wcbm-badge-options-' . absint( $loop ) . '-schedule',
		'multiple' => true,
		'data'     => array(
			'placeholder' => __( 'Search Badges...', 'yith-woocommerce-badges-management' ),
			'post_type'   => YITH_WCBM_Post_Types::$badge,
		),
		'value'    => $schedule,
	),
	'schedule-from' => array(
		'label'             => __( 'From', 'yith-woocommerce-badges-management' ),
		'type'              => 'datepicker',
		'name'              => 'yith_wcbm_badge_options[' . absint( $loop ) . '][schedule_from]',
		'custom_attributes' => array(
			'placeholder' => __( 'YYYY-MM-DD', 'yith-woocommerce-badges-management' ),
		),
		'data'              => array(
			'date-format' => 'yy-mm-dd',
			'min-date'    => 0,
		),
		'value'             => $schedule_from ? date_i18n( 'Y-m-d', $schedule_from ) : '',

	),
	'schedule-to'   => array(
		'label'             => __( 'To', 'yith-woocommerce-badges-management' ),
		'type'              => 'datepicker',
		'name'              => 'yith_wcbm_badge_options[' . absint( $loop ) . '][schedule_to]',
		'custom_attributes' => array(
			'placeholder' => __( 'YYYY-MM-DD', 'yith-woocommerce-badges-management' ),
		),
		'data'              => array(
			'date-format' => 'yy-mm-dd',
			'min-date'    => 0,
		),
		'value'             => $schedule_to ? date_i18n( 'Y-m-d', $schedule_to ) : '',
	),
);

?>

<div class="yith-wcbm-variation-badge-options <?php echo esc_attr( yith_set_wrapper_class() ); ?>">
	<div class="yith-wcbm-variation-badge-options__title">
		<?php echo esc_html__( 'Badge Options', 'yith-woocommerce-badges-management' ); ?>
	</div>
	<?php wp_nonce_field( 'yith_wcbm_badge_options_in_variation_' . $loop, 'yith_wcbm_badge_options[' . $loop . '][security]' ); ?>
	<?php foreach ( $fields as $field_id => $field ) : ?>
		<div class="yith-wcbm-variation-field-row yith-wcbm-variation-field-row__<?php echo esc_attr( $field_id ); ?>">

			<?php if ( isset( $field['label'] ) ) : ?>
				<div class="yith-wcbm-variation-field__title">
					<?php echo esc_html( $field['label'] ); ?>
				</div>
			<?php endif; ?>

			<div class="yith-wcbm-variation-field">
				<?php yith_plugin_fw_get_field( $field, true ); ?>
			</div>

		</div>
	<?php endforeach; ?>
</div>
