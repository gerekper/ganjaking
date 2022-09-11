<?php
/**
 * Variable product template options for delivery schedules
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
 * @var int    $loop Current variation.
 * @var string $_ywsbs_override_delivery_schedule Check if the delivery schedules are override.
 * @var array  $_ywsbs_delivery_synch Delivery schedules options.
 * @var array  $time_options List of time options.
 * @var string $_ywsbs_delivery_sync_delivery_schedules Subscription synchronize delivery scheduled.
 *
 */

?>
<h4 class="ywsbs-title-section"><?php esc_html_e( 'Delivery schedules subscription settings', 'yith-woocommerce-subscription' ); ?></h4>
<div class="ywsbs_product_panel_row delivery-schedules-options">
	<div class="ywsbs_product_panel_row_element">
		<fieldset class="form-field yith-plugin-ui onoff _ywsbs_override_delivery_schedule">
			<legend
				for="_ywsbs_override_delivery_schedule"><?php esc_html_e( 'Override the delivery schedule settings', 'yith-woocommerce-subscription' ); ?></legend>
			<?php
			$args = array(
				'type'  => 'onoff',
				'id'    => 'variable_ywsbs_override_delivery_schedule_' . $loop,
				'name'  => 'variable_ywsbs_override_delivery_schedule[' . $loop . ']',
				'value' => $_ywsbs_override_delivery_schedule,
			);
			yith_plugin_fw_get_field( $args, true );
			?>
			<span
				class="description"><?php esc_html_e( 'Enable if you want to set a specific delivery schedule for this product.', 'yith-woocommerce-subscription' ); ?></span>
		</fieldset>
	</div>
</div>
<div class="ywsbs_product_panel_row"
	data-deps-on="variable_ywsbs_override_delivery_schedule[<?php echo esc_attr( $loop ); ?>]" data-deps-val="yes">
	<div class="ywsbs_product_panel_row_element ywsbs_delivery_sych">
		<p class="form-field _ywsbs_delivery_schedule"
			data-deps-on="variable_ywsbs_override_delivery_schedule[<?php echo esc_attr( $loop ); ?>]"
			data-deps-val="yes">
			<label
				for="variable_ywsbs_delivery_schedule"><?php esc_html_e( 'Deliver the subscription products', 'yith-woocommerce-subscription' ); ?></label>
			<span class="wrapper">

				<span class="wrap">
					<span><?php esc_html_e( 'Every', 'yith-woocommerce-subscription' ); ?></span>

					<input type="number" class="ywsbs-short"
						name="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][delivery_gap]"
						id="variable_ywsbs_delivery_synch_delivery_gap_<?php echo esc_attr( $loop ); ?>"
						value="<?php echo esc_attr( $_ywsbs_delivery_synch['delivery_gap'] ); ?>"/>

					<select id="variable_ywsbs_delivery_synch_delivery_period_<?php echo esc_attr( $loop ); ?>"
						name="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][delivery_period]"
						class="select ywsbs-with-margin yith-short-select delivery_period">

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

	</div>
</div>
<div class="form-field ywsbs_delivery_synch_wrapper"
	data-deps-on="variable_ywsbs_override_delivery_schedule[<?php echo esc_attr( $loop ); ?>]" data-deps-val="yes"
	type="checkbox">
	<fieldset class="form-field yith-plugin-ui onoff">
		<legend
			for="_ywsbs_delivery_sync_delivery_schedules"><?php esc_html_e( 'Synchronize delivery schedules', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => 'variable_ywsbs_delivery_synch_on_' . $loop,
			'name'  => 'variable_ywsbs_delivery_synch[' . $loop . '][on]',
			'value' => $_ywsbs_delivery_synch['on'],
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable if you want to ship the product on a specific day.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>
	<p class="form-field _ywsbs_delivery_schedule"
		data-deps-on="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][on]"
		data-deps-val="yes">
		<label
			for="variable_ywsbs_delivery_schedule"><?php esc_html_e( 'Synchronize delivery on', 'yith-woocommerce-subscription' ); ?></label>
		<span class="wrapper">
				<span class="wrap show-for show-for-weeks">

					<select id="variable_ywsbs_delivery_synch_sych_weeks_<?php echo esc_attr( $loop ); ?>"
						name="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][sych_weeks]"
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

					<select id="variable_ywsbs_delivery_synch_months_<?php echo esc_attr( $loop ); ?>"
						name="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][months]"
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

					<select id="variable_ywsbs_delivery_synch_years_day_<?php echo esc_attr( $loop ); ?>"
						name="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][years_day]"
						class="select ywsbs-with-margin yith-short-select">
							<?php
							$day_months = ywsbs_get_period_options( 'day_months' );
							foreach ( $day_months as $key => $value ) : ?>
								<option
									value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_delivery_synch['years_day'], $key, true ); ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
					</select>
					<select id="variable_ywsbs_delivery_synch_years_month_<?php echo esc_attr( $loop ); ?>"
						name="variable_ywsbs_delivery_synch[<?php echo esc_attr( $loop ); ?>][years_month]"
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