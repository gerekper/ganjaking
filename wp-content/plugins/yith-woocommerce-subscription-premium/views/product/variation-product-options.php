<?php
/**
 * Variable product template options
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Vars used on this template.
 *
 * @var string $_ywsbs_price_time_option Period (days, weeks ..).
 * @var int    $_ywsbs_price_is_per Duration.
 * @var array  $max_lengths Limit of time foreach period.
 * @var int    $_ywsbs_max_length Max duration of the subscrition.
 * @var int    $_ywsbs_trial_per Duration of the trial.
 * @var int    $_ywsbs_trial_time_option Period (days, weeks ..).
 * @var float  $_ywsbs_fee Fee value.
 * @var int    $_ywsbs_max_pause Max number of pause.
 * @var int    $_ywsbs_max_pause_duration Max period of pause.
 * @var string $_ywsbs_enable_max_length Enable or not the max length.
 * @var string $_ywsbs_enable_pause Enable or not the pause.
 * @var string $_ywsbs_limit Subscription limit.
 * @var string $_ywsbs_switchable Switchable option value.
 * @var string $_ywsbs_prorate_length Prorate option value.
 * @var string $_ywsbs_gap_payment Gap payment option value.
 * @var int    $loop Current variation.
 * @var string $_ywsbs_override_pause_settings Subscription override pause settings.
 * @var string $_ywsbs_override_cancellation_settings Subscription override cancelling settings.
 * @var string $_ywsbs_can_be_cancelled Subscription can be cancelled.
 * @var int    $num_variations Variations total number.
 * @var int    $_ywsbs_switchable_priority Priority number.
 * @var string $_ywsbs_prorate_recurring_payment Recurring payment.
 * @var string $_ywsbs_prorate_fee Charge Fee.
 * @var bool   $_ywsbs_enable_limit Enable or not the limit.
 * @var bool   $_ywsbs_enable_fee Check if enable the fee
 * @var bool   $_ywsbs_enable_trial Check is enable the trial.
 */

/**
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 */
global $wp_locale;

$time_opt     = ( $_ywsbs_price_time_option ) ? $_ywsbs_price_time_option : 'days';
$time_options = ywsbs_get_time_options();
?>
<div class="options_group ywsbs-general-section ywsbs_subscription_variation_products">
	<h4 class="ywsbs-title-section"><?php esc_html_e( 'Subscription Settings', 'yith-woocommerce-subscription' ); ?></h4>

	<div class="ywsbs_product_panel_row">
		<div class="ywsbs_product_panel_row_element">
			<p class="variable_ywsbs_price_is_per">
				<label
					for="_ywsbs_price_is_per_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Users will pay every', 'yith-woocommerce-subscription' ); ?></label>
				<span class="wrap">
						<input type="number" class="ywsbs-short"
							name="variable_ywsbs_price_is_per[<?php echo esc_attr( $loop ); ?>]"
							id="_ywsbs_price_is_per_<?php echo esc_attr( $loop ); ?>"
							value="<?php echo esc_attr( $_ywsbs_price_is_per ); ?>"
							min="0" />
						<select id="variable_ywsbs_price_time_option_<?php echo esc_attr( $loop ); ?>"
							name="variable_ywsbs_price_time_option[<?php echo esc_attr( $loop ); ?>]"
							class="select ywsbs-with-margin yith-short-select ywsbs_price_time_option">
						<?php foreach ( $time_options as $key => $value ) : ?>
							<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_price_time_option, $key, true ); ?>  data-max="<?php echo esc_attr( $max_lengths[ $key ] ); ?>"
								data-text="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
				<span
					class="description"><?php esc_html_e( 'Set the length of each recurring subscription period to daily, weekly, monthly or annually.', 'yith-woocommerce-subscription' ); ?></span>
			</p>
		</div>
		<div class="ywsbs_product_panel_row_element">
			<fieldset class="form-field yith-plugin-ui variable_ywsbs_enable_trial onoff">
				<legend
					for="_ywsbs_enable_trial"><?php esc_html_e( 'Offer a trial period', 'yith-woocommerce-subscription' ); ?></legend>
				<?php
				$args = array(
					'type'  => 'onoff',
					'id'      => 'variable_ywsbs_enable_trial_' . $loop,
					'name'    => 'variable_ywsbs_enable_trial[' . $loop . ']',
					'value' => $_ywsbs_enable_trial,
				);
				yith_plugin_fw_get_field( $args, true );
				?>
				<span
					class="description"><?php esc_html_e( 'Enable to offer a trial period when the subscription is purchased.', 'yith-woocommerce-subscription' ); ?></span>
			</fieldset>
			<p class="form-field variable_ywsbs_trial_per" data-deps-on="variable_ywsbs_enable_trial[<?php echo esc_attr( $loop ); ?>]" data-deps-val="yes">
				<label
					for="variable_ywsbs_trial_per_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Offer a free trial of', 'yith-woocommerce-subscription' ); ?></label>

				<input type="number" class="ywsbs-short"
					name="variable_ywsbs_trial_per[<?php echo esc_attr( $loop ); ?>]"
					id="variable_ywsbs_trial_per_<?php echo esc_attr( $loop ); ?>"
					value="<?php echo esc_attr( $_ywsbs_trial_per ); ?>" min="0"/>

				<select id="_ywsbs_trial_time_option_<?php echo esc_attr( $loop ); ?>"
					name="variable_ywsbs_trial_time_option[<?php echo $loop; ?>]"
					class="select ywsbs-with-margin yith-short-select">
					<?php foreach ( $time_options as $key => $value ) : ?>
						<option
							value="<?php echo esc_attr( $key ); ?>" <?php selected( $_ywsbs_trial_time_option, $key ); ?>><?php echo esc_html( $value ); ?></option>
					<?php
					endforeach;
					?>
				</select>
				<span
					class="description"><?php esc_html_e( 'You can offer a free trial of this subscription. In this way the user can purchase the subscription and will pay when the trial period expires.', 'yith-woocommerce-subscription' ); ?></span>
			</p>
		</div>
	</div>

	<div class="ywsbs_product_panel_row">
		<?php if ( YWSBS_Subscription_Synchronization()->is_synchronizable( $variation ) ):
		$show = 'days' === $_ywsbs_price_time_option ? false : $_ywsbs_price_time_option;

		?>
		<div class="ywsbs_product_panel_row_element">

				<p class="form-field ywsbs-synchronize-info <?php echo ( $show ) ? '' : 'hide'; ?> ">
					<label
						for="_ywsbs_synchronize_info"><?php esc_html_e( 'Synchronize recurring payments on', 'yith-woocommerce-subscription' ); ?></label>
					<span
						class="synch_section ywsbs-inline-fields <?php echo ( $show && 'weeks' === $show ) ? '' : 'hide'; ?>"
						data-synch="weeks">
			<?php $val = isset( $_ywsbs_synchronize_info['weeks'] ) ? $_ywsbs_synchronize_info['weeks'] : get_option( 'start_of_week' ); ?>
			<select id="_ywsbs_synchronize_info_week_<?php echo $loop; ?>"
				name="variable_ywsbs_synchronize_info[<?php echo $loop; ?>][weeks]"
				class="select ywsbs-with-margin yith-short-select">
				<?php
				for ( $day_index = 0; $day_index <= 6; $day_index++ ) :
					$selected = ( $val == $day_index ) ? 'selected="selected"' : '';
					echo "\n\t<option value='" . esc_attr( $day_index ) . "' $selected>" . $wp_locale->get_weekday( $day_index ) . '</option>';
				endfor;
				?>
			</select>
		</span>

					<span
						class="synch_section ywsbs-inline-fields  <?php echo ( $show && 'months' === $show ) ? '' : 'hide'; ?>"
						data-synch="months">
			<?php $val = isset( $_ywsbs_synchronize_info['months'] ) ? $_ywsbs_synchronize_info['months'] : 1; ?>
			<span class="ywsbs-inline-fields">
						<select id="_ywsbs_synchronize_info_months_<?php echo $loop; ?>"
							name="variable_ywsbs_synchronize_info[<?php echo $loop; ?>][months]"
							class="select ywsbs-with-margin yith-short-select">
							<?php
							for ( $day_index = 1; $day_index <= 28; $day_index++ ) :
								$selected = ( $val == $day_index ) ? 'selected="selected"' : '';
								echo "\n\t<option value='" . esc_attr( $day_index ) . "' $selected>" . esc_html__( 'Day', 'yith-woocommerce-subscription' ) . ' ' . $day_index . '</option>';
							endfor;
							?>
							<option
								value="end" <?php selected( $val, 'end' ); ?>><?php echo esc_html_x( 'End of month', 'Admin product select option', 'yith-woocommerce-subscription' ); ?></option>
						</select>
						<span
							class="ywsbs-synch-month-description"><?php esc_html_e( 'of each month', 'yith-woocommerce-subscription' ); ?></span>
					</span>
		</span>
					<span
						class="synch_section ywsbs-inline-fields  <?php echo ( $show && 'years' === $show ) ? '' : 'hide'; ?>"
						data-synch="years">
						<?php $val = isset( $_ywsbs_synchronize_info['years'] ) ? $_ywsbs_synchronize_info['years'] : array( 'month' => 1, 'day' => 1 ); ?>
						<span class="ywsbs-inline-fields">
							<select id="_ywsbs_synchronize_info_month_<?php echo $loop; ?>"
								name="variable_ywsbs_synchronize_info[<?php echo $loop; ?>][years][month]"
								class="select ywsbs-with-margin yith-short-select ywsbs_synchronize_info_month">
							<?php
							for ( $day_index = 1; $day_index <= 12; $day_index++ ) :
								$selected = ( $val['month'] == $day_index ) ? 'selected="selected"' : '';
								echo "\n\t<option value='" . esc_attr( $day_index ) . "' $selected>" . $wp_locale->get_month( $day_index ) . '</option>';
							endfor;
							?>
							</select>
							<select id="_ywsbs_synchronize_info_years_day"
								name="variable_ywsbs_synchronize_info[<?php echo $loop; ?>][years][day]"
								class="select ywsbs-with-margin yith-short-select">
								<?php
								for ( $day_index = 1; $day_index <= 28; $day_index++ ) :
									$selected = ( $val['day'] == $day_index ) ? 'selected="selected"' : '';
									echo "\n\t<option value='" . esc_attr( $day_index ) . "' $selected>" . esc_html__( 'Day', 'yith-woocommerce-subscription' ) . ' ' . $day_index . '</option>';
								endfor;
								?>
								<option
									value="end" <?php selected( $val['day'], 'end' ); ?>><?php echo esc_html_x( 'End of month', 'Admin product select option', 'yith-woocommerce-subscription' ); ?></option>
							</select>
						</span>
		</span>
					<span
						class="description"><?php esc_html_e( 'Set a specific payment date for all user that purchase this subscription.', 'yith-woocommerce-subscription' ); ?></span>

				</p>


		</div>
		<?php endif; ?>
		<div class="ywsbs_product_panel_row_element">
			<?php
			$args = array(
				'label'   => esc_html__( 'Subscription ends', 'yith-woocommerce-subscription' ),
				'value'   => $_ywsbs_enable_max_length,
				'id'      => 'variable_ywsbs_enable_max_length_' . $loop,
				'name'    => 'variable_ywsbs_enable_max_length[' . $loop . ']',
				'default' => 'no',
				'options' => array(
					'no'  => esc_html__( 'Never', 'yith-woocommerce-subscription' ),
					'yes' => esc_html__( 'Set an end time', 'yith-woocommerce-subscription' ),
				),
			);
			woocommerce_wp_radio( $args );
			?>
			<p class="form-field ywsbs_max_length_variable"
				data-deps-on="variable_ywsbs_enable_max_length[<?php echo esc_attr( $loop ); ?>]"
				data-deps-val="yes"
				data-type="radio">
				<label for="variable_ywsbs_max_length_<?php echo esc_attr( $loop ); ?>"></label>
				<span class="ywsbs-inline-fields">
			<span><?php esc_html_e( 'Subscription will end after', 'yith-woocommerce-subscription' ); ?></span>
			<input
				type="number" class="ywsbs-short" name="variable_ywsbs_max_length[<?php echo esc_attr( $loop ); ?>]"
				id="variable_ywsbs_max_length_<?php echo esc_attr( $loop ); ?>"
				value="<?php echo esc_attr( $_ywsbs_max_length ); ?>" min="0"/>
			<span
				class="max-length-time-opt"><?php echo esc_html( $time_options[ $time_opt ] ); ?></span>
		</span></p>
			<p class="form-field ywsbs_max_length-description"><span
					class="description"><?php esc_html_e( 'Choose if the subscription has an end time or not.', 'yith-woocommerce-subscription' ); ?></span>
			</p>
		</div>

	</div>

	<div class="ywsbs_product_panel_row">
		<div class="ywsbs_product_panel_row_element ywsbs_fee">
			<fieldset class="form-field yith-plugin-ui variable_ywsbs_enable_fee onoff">
				<legend
					for="_ywsbs_enable_fee"><?php esc_html_e( 'Request a signup fee', 'yith-woocommerce-subscription' ); ?></legend>
				<?php
				$args = array(
					'type'  => 'onoff',
					'id'      => 'variable_ywsbs_enable_fee_' . $loop,
					'name'    => 'variable_ywsbs_enable_fee[' . $loop . ']',
					'value' => $_ywsbs_enable_fee,
				);
				yith_plugin_fw_get_field( $args, true );
				?>
				<span
					class="description"><?php esc_html_e( 'Enable to request a signup fee when the subscription is purchased.', 'yith-woocommerce-subscription' ); ?></span>
			</fieldset>
			<div class="form-field variable_ywsbs_fee" data-deps-on="variable_ywsbs_enable_fee[<?php echo esc_attr( $loop ); ?>]" data-deps-val="yes">

			<?php

			$args = array(
				'id'          => 'variable_ywsbs_fee_' . $loop,
				'name'        => 'variable_ywsbs_fee[' . $loop . ']',
				'value'       => esc_attr( wc_format_localized_price( $_ywsbs_fee ) ),
				'description' => esc_html__( 'The sign-up fee will be charged when the subscription is purchased.', 'yith-woocommerce-subscription' ),
				// translators: placeholder currency label.
				'label'       => sprintf( esc_html_x( 'Sign-up fee (%s)', 'currency value', 'yith-woocommerce-subscription' ), get_woocommerce_currency_symbol() ),
				'type'        => 'text',
				'data_type'   => 'decimal',
				'class'       => 'ywsbs-short ywsbs_fee wc_input_price',
			);
			woocommerce_wp_text_input( $args );

			?>
			</div>
		</div>
		<div class="ywsbs_product_panel_row_element">
			<fieldset class="form-field yith-plugin-ui onoff">
				<legend
					for="variable_ywsbs_override_pause_settings_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Override global pausing settings', 'yith-woocommerce-subscription' ); ?></legend>
				<?php
				$args = array(
					'type'  => 'onoff',
					'id'    => 'variable_ywsbs_override_pause_settings_' . $loop,
					'name'  => 'variable_ywsbs_override_pause_settings[' . $loop . ']',
					'class' => 'ywsbs_override_pause_settings',
					'value' => $_ywsbs_override_pause_settings,
				);
				yith_plugin_fw_get_field( $args, true );
				?>
				<span
					class="description"><?php esc_html_e( 'Enable to set custom pausing rules for this product. This option overrides the option in the general settings', 'yith-woocommerce-subscription' ); ?></span>
			</fieldset>

			<?php
			$args = array(
				'label'         => esc_html__( 'Allow users to pause this subscription', 'yith-woocommerce-subscription' ),
				'description'   => esc_html__( 'Choose if a user can pause a subscription, and if so, to do so with or without limits.', 'yith-woocommerce-subscription' ),
				'value'         => $_ywsbs_enable_pause,
				'default'       => 'no',
				'id'            => 'variable_ywsbs_enable_pause_' . $loop,
				'name'          => 'variable_ywsbs_enable_pause[' . $loop . ']',
				'wrapper_class' => '_ywsbs_enable_pause_field',
				'options'       => array(
					'no'      => esc_html__( 'No, never', 'yith-woocommerce-subscription' ),
					'yes'     => esc_html__( 'Yes, user can pause without limits', 'yith-woocommerce-subscription' ),
					'limited' => esc_html__( 'Yes, user can pause with certain limits', 'yith-woocommerce-subscription' ),
				),
			);
			woocommerce_wp_radio( $args );
			?>
			<p class="form-field ywsbs_pause_options"
				data-deps-on="variable_ywsbs_enable_pause[<?php echo esc_attr( $loop ); ?>]" data-deps-val="limited"
				data-type="radio">
				<label
					for="variable_ywsbs_max_pause_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Subscription pausing limits', 'yith-woocommerce-subscription' ); ?></label>
				<span class="ywsbs-inline-fields">
					<span><?php esc_html_e( 'The user can pause this subscription for a max of', 'yith-woocommerce-subscription' ); ?></span>
					<input type="number" class="ywsbs-short "
						name="variable_ywsbs_max_pause[<?php echo esc_attr( $loop ); ?>]"
						id="variable_ywsbs_max_pause_<?php echo esc_attr( $loop ); ?>"
						value="<?php echo esc_attr( $_ywsbs_max_pause ); ?>" min="0" /> <?php esc_html_e( 'times', 'yith-woocommerce-subscription' ); ?>;
				</span>
				<span class="ywsbs-inline-fields padding-10">
					<span><?php esc_html_e( 'Each pause can have a duration for a max of', 'yith-woocommerce-subscription' ); ?></span>
					<input type="number" class="ywsbs-short"
						name="variable_ywsbs_max_pause_duration[<?php echo esc_attr( $loop ); ?>]"
						id="variable_ywsbs_max_pause_duration_<?php echo esc_attr( $loop ); ?>"
						value="<?php echo esc_attr( $_ywsbs_max_pause_duration ); ?>" min="0"/>
						<span><?php esc_html_e( 'days.', 'yith-woocommerce-subscription' ); ?><br></span>
						<span class="inline-fields-desc"><?php esc_html_e( 'Then automatically the subscription will be reactivated.', 'yith-woocommerce-subscription' ); ?></span>
				</span>
			</p>

		</div>

	</div>

	<div class="ywsbs_product_panel_row">
		<div class="ywsbs_product_panel_row_element">
			<fieldset class="form-field yith-plugin-ui onoff">
				<legend
					for="_ywsbs_override_cancellation_settings"><?php esc_html_e( 'Override global cancellation settings', 'yith-woocommerce-subscription' ); ?></legend>
				<?php
				$args = array(
					'type'  => 'onoff',
					'id'    => 'variable_ywsbs_override_cancellation_settings_' . $loop,
					'name'  => 'variable_ywsbs_override_cancellation_settings[' . $loop . ']',
					'value' => $_ywsbs_override_cancellation_settings,
				);
				yith_plugin_fw_get_field( $args, true );
				?>
				<span
					class="description"><?php esc_html_e( 'Enable to set specific cancellation options for this product. It will override the cancellation option in the general settings.', 'yith-woocommerce-subscription' ); ?></span>
			</fieldset>

			<fieldset class="form-field yith-plugin-ui ywsbs_can_be_cancelled"
				data-deps-on="variable_ywsbs_override_cancellation_settings[<?php echo esc_attr( $loop ); ?>]"
				data-deps-val="yes">
				<legend
					for="_ywsbs_can_be_cancelled"><?php esc_html_e( 'Allow users to cancel this subscription', 'yith-woocommerce-subscription' ); ?></legend>
				<?php
				$args = array(
					'type'  => 'onoff',
					'id'    => 'variable_ywsbs_can_be_cancelled_' . $loop,
					'name'  => 'variable_ywsbs_can_be_cancelled[' . $loop . ']',
					'value' => $_ywsbs_can_be_cancelled,
				);
				yith_plugin_fw_get_field( $args, true );
				?>
				<span
					class="description"><?php esc_html_e( 'Enable if you want to allow users to cancel this subscription. This option override the option in general settings.', 'yith-woocommerce-subscription' ); ?></span>
			</fieldset>
		</div>
		<div class="ywsbs_product_panel_row_element ywsbs_limit">
			<fieldset class="form-field yith-plugin-ui variation_ywsbs_enable_limit onoff">
				<legend
					for="variation_ywsbs_enable_limit"><?php esc_html_e( 'Apply subscription limits', 'yith-woocommerce-subscription' ); ?></legend>
				<?php
				$args = array(
					'type'  => 'onoff',
					'id'    => 'variable_ywsbs_enable_limit_' . $loop,
					'name'  => 'variable_ywsbs_enable_limit[' . $loop . ']',
					'value' => $_ywsbs_enable_limit,
				);
				yith_plugin_fw_get_field( $args, true );
				?>
				<span
					class="description"><?php esc_html_e( 'Enable to apply limits to the customer purchasing this subscription.', 'yith-woocommerce-subscription' ); ?></span>
			</fieldset>
			<div data-deps-on="variable_ywsbs_enable_limit[<?php echo esc_attr( $loop ); ?>]" data-deps-val="yes" class="form-field">
			<?php
			$args = array(
				'label'       => esc_html__( 'Limit subscription', 'yith-woocommerce-subscription' ),
				'description' => esc_html__(
					'Set optional limits for this product subscription.',
					'yith-woocommerce-subscription'
				),
				'value'       => $_ywsbs_limit,
				'id'          => 'variable_ywsbs_limit_' . $loop,
				'name'        => 'variable_ywsbs_limit[' . $loop . ']',
				'options'     => array(
					'one-active' => esc_html__(
						'Limit user to allow only one active subscription',
						'yith-woocommerce-subscription'
					),
					'one'        => esc_html__(
						'Limit user to allow only one subscription of any status, either active or not',
						'yith-woocommerce-subscription'
					),
				),
			);
			woocommerce_wp_radio( $args );
			?>
			</div>
		</div>
	</div>

	<?php if ( YWSBS_Subscription_Delivery_Schedules()->has_delivery_scheduled( $variation ) ): ?>
		<?php @include('variation-delivery-schedules.php'); ?>
	<?php endif; ?>

	<?php @include('variation-switch.php'); ?>

</div>
