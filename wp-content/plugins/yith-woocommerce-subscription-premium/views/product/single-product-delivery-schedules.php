<?php
/**
 * Single product template options for delivery schedules
 *
 * @package YITH WooCommerce Subscription
 * @since   2.2.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Vars used on this template.
 *
 * @var string $_ywsbs_override_delivery_schedule Subscription override delivery scheduled.
 * @var string $_ywsbs_delivery_synch Subscription override delivery scheduled.
 */

/**
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 */
global $wp_locale;
?>

<fieldset class="form-field yith-plugin-ui _ywsbs_override_delivery_schedule">
	<legend
		for="_ywsbs_override_delivery_schedule "><?php esc_html_e( 'Override the delivery schedule settings', 'yith-woocommerce-subscription' ); ?></legend>
	<?php
	$args = array(
		'type'  => 'onoff',
		'id'    => '_ywsbs_override_delivery_schedule',
		'name'  => '_ywsbs_override_delivery_schedule',
		'value' => $_ywsbs_override_delivery_schedule,
	);
	yith_plugin_fw_get_field( $args, true );
	?>
	<span
		class="description"><?php esc_html_e( 'Enable if you want to set a specific delivery schedule for this product.', 'yith-woocommerce-subscription' ); ?></span>
</fieldset>

<p class="form-field _ywsbs_delivery_schedule" data-deps-on="_ywsbs_override_delivery_schedule" data-deps-val="yes">
	<label for="_ywsbs_delivery_schedule"><?php esc_html_e( 'Deliver the subscription products every', 'yith-woocommerce-subscription' ); ?></label>
	<span class="wrapper">
		<span class="wrap">
					<span><?php esc_html_e( 'Every', 'yith-woocommerce-subscription' ); ?></span>
					<input type="number" class="ywsbs-short" name="_ywsbs_delivery_synch[delivery_gap]"
						id="_ywsbs_delivery_synch_delivery_gap"
						value="<?php echo esc_attr( $_ywsbs_delivery_synch['delivery_gap'] ); ?>"/>
					<select id="_ywsbs_delivery_synch_delivery_period"
						name="_ywsbs_delivery_synch[delivery_period]"
						class="select ywsbs-with-margin yith-short-select single-delivery-period">
						<?php foreach ( $time_options as $key => $value ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_delivery_synch['delivery_period'], $key, true ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					</select>
		</span>
	<span
		class="description"><?php esc_html_e( 'Set a delivery schedule for this product.', 'yith-woocommerce-subscription' ); ?></span>
	</span>
</p>
<div class="form-field ywsbs_delivery_synch_wrapper" data-deps-on="_ywsbs_override_delivery_schedule"
	data-deps-val="yes" data-type="checkbox">
	<fieldset class="form-field yith-plugin-ui _ywsbs_delivery_synch_on">
		<legend
			for="_ywsbs_delivery_synch_on"><?php esc_html_e( 'Synchronize delivery schedules', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_delivery_synch_on',
			'name'  => '_ywsbs_delivery_synch[on]',
			'value' => $_ywsbs_delivery_synch['on'],
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable if you want to ship the product on a specific day.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<p class="form-field _ywsbs_delivery_schedule _ywsbs_delivery_sync_delivery_schedules_info"
		data-deps-on="_ywsbs_delivery_synch[on]" data-deps-val="yes">
		<label
			for="_ywsbs_delivery_schedule"><?php esc_html_e( 'Synchronize delivery on', 'yith-woocommerce-subscription' ); ?></label>
		<span class="wrapper">
	<span class="wrap show-for show-for-weeks">

				<select id="_ywsbs_delivery_synch_sych_weeks"
					name="_ywsbs_delivery_synch[sych_weeks]"
					class="select ywsbs-with-margin yith-short-select">
						<?php
						$day_weeks = ywsbs_get_period_options( 'day_weeks' );
						foreach ( $day_weeks as $key => $value ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_delivery_synch['sych_weeks'], $key, true ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
				</select>
			</span>
	<span class="wrap show-for show-for-months">

				<select id="_ywsbs_delivery_synch_months"
					name="_ywsbs_delivery_synch[months]"
					class="select ywsbs-with-margin yith-short-select">
						<?php
						$day_months = ywsbs_get_period_options( 'day_months' );
						foreach ( $day_months as $key => $value ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_delivery_synch['months'], $key, true ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
				</select>
				<span><?php esc_html_e( 'of each month', 'yith-woocommerce-subscription' ); ?></span>
			</span>
	<span class="wrap show-for show-for-years">

				<select id="_ywsbs_delivery_synch_years_day"
					name="_ywsbs_delivery_synch[years_day]"
					class="select ywsbs-with-margin yith-short-select">
						<?php
						$day_months = ywsbs_get_period_options( 'day_months' );
						foreach ( $day_months as $key => $value ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_delivery_synch['years_day'], $key, true ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
				</select>
				<select id="_ywsbs_delivery_synch_years_month"
					name="_ywsbs_delivery_synch[years_month]"
					class="select ywsbs-with-margin yith-short-select">
						<?php
						$months = ywsbs_get_period_options( 'months' );
						foreach ( $months as $key => $value ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_delivery_synch['years_month'], $key, true ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
				</select>
			</span>

	<span
		class="description"><?php esc_html_e( 'Set a specific day for the delivery schedule.', 'yith-woocommerce-subscription' ); ?></span>
	</span>
	</p>
</div>