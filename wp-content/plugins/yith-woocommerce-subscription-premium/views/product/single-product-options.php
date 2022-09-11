<?php
/**
 * Single product template options
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
 * @var int    $_ywsbs_max_length Max duration of the subscription.
 * @var bool   $_ywsbs_enable_trial Check is enable the trial.
 * @var int    $_ywsbs_trial_per Duration of the trial.
 * @var int    $_ywsbs_trial_time_option Period (days, weeks ..).
 * @var bool   $_ywsbs_enable_fee Check if enable the fee
 * @var float  $_ywsbs_fee Fee value.
 * @var int    $_ywsbs_max_pause Max number of pause.
 * @var int    $_ywsbs_max_pause_duration Max period of pause.
 * @var string $_ywsbs_enable_max_length Enable or not the max length.
 * @var string $_ywsbs_enable_pause Enable or not the pause.
 * @var bool   $_ywsbs_enable_limit Enable or not the limit.
 * @var string $_ywsbs_limit Subscription limit.
 * @var string $_ywsbs_override_pause_settings Subscription override pause settings.
 * @var string $_ywsbs_override_cancellation_settings Subscription override cancelling settings.
 * @var string $_ywsbs_can_be_cancelled Subscription can be cancelled.
 * @var string $_ywsbs_override_delivery_schedule Subscription override delivery scheduled.
 * @var string $_ywsbs_delivery_synch Subscription override delivery scheduled.
 */

/**
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 */
global $wp_locale;

$time_opt     = ( $_ywsbs_price_time_option ) ? $_ywsbs_price_time_option : 'days';
$time_options = ywsbs_get_time_options();
?>
<div class="options_group show_if_simple ywsbs-general-section">
	<h4 class="ywsbs-title-section"><?php esc_html_e( 'Subscription Settings', 'yith-woocommerce-subscription' ); ?></h4>

	<p class="form-field ywsbs_price_is_per">
		<label
			for="_ywsbs_price_is_per"><?php esc_html_e( 'Users will pay every', 'yith-woocommerce-subscription' ); ?></label>
		<span class="wrap">
						<input type="number" class="ywsbs-short" name="_ywsbs_price_is_per" id="_ywsbs_price_is_per" min="0"
							value="<?php echo esc_attr( $_ywsbs_price_is_per ); ?>"/>
						<select id="_ywsbs_price_time_option" name="_ywsbs_price_time_option"
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

	<?php if ( YWSBS_Subscription_Synchronization()->is_synchronizable( $product ) ):
		$show = 'days' === $_ywsbs_price_time_option ? false : $_ywsbs_price_time_option;

		?>
		<p class="form-field ywsbs-synchronize-info <?php echo( $show ? '' : 'hide' ); ?>">
			<label for="_ywsbs_synchronize_info"><?php esc_html_e( 'Synchronize recurring payments on', 'yith-woocommerce-subscription' ); ?></label>
			<span
				class="synch_section ywsbs-inline-fields <?php echo ( $show && 'weeks' === $show ) ? '' : 'hide'; ?>"
				data-synch="weeks">
			<?php $val = isset( $_ywsbs_synchronize_info['weeks'] ) ? $_ywsbs_synchronize_info['weeks'] : get_option( 'start_of_week' ); ?>
			<select id="_ywsbs_synchronize_info_weeks" name="_ywsbs_synchronize_info[weeks]"
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
						<select id="_ywsbs_synchronize_info_months" name="_ywsbs_synchronize_info[months]"
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
						<span><?php esc_html_e( 'of each month', 'yith-woocommerce-subscription' ); ?></span>
					</span>
		</span>
			<span
				class="synch_section ywsbs-inline-fields  <?php echo ( $show && 'years' === $show ) ? '' : 'hide'; ?>"
				data-synch="years">
			<?php $val = isset( $_ywsbs_synchronize_info['years'] ) ? $_ywsbs_synchronize_info['years'] : array( 'month' => 1, 'day' => 1 ); ?>
			<span class="ywsbs-inline-fields">
							<select id="_ywsbs_synchronize_info_years_month"
								name="_ywsbs_synchronize_info[years][month]"
								class="select ywsbs-with-margin yith-short-select">
							<?php
							for ( $day_index = 1; $day_index <= 12; $day_index++ ) :
								$selected = ( $val['month'] == $day_index ) ? 'selected="selected"' : '';
								echo "\n\t<option value='" . esc_attr( $day_index ) . "' $selected>" . $wp_locale->get_month( $day_index ) . '</option>';
							endfor;
							?>
							</select>
							<select id="_ywsbs_synchronize_info_years_day" name="_ywsbs_synchronize_info[years][day]"
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

	<?php endif; ?>
	<?php
	$args = array(
		'label'   => esc_html__( 'Subscription ends', 'yith-woocommerce-subscription' ),
		'value'   => $_ywsbs_enable_max_length,
		'id'      => '_ywsbs_enable_max_length',
		'name'    => '_ywsbs_enable_max_length',
		'default' => 'no',
		'options' => array(
			'no'  => esc_html__( 'Never', 'yith-woocommerce-subscription' ),
			'yes' => esc_html__( 'Set an end time', 'yith-woocommerce-subscription' ),
		),
	);
	woocommerce_wp_radio( $args );
	?>

	<p class="form-field ywsbs_max_length" data-deps-on="_ywsbs_enable_max_length" data-deps-val="yes"
		data-type="radio">
		<label for="_ywsbs_max_length"></label>
		<span class="ywsbs-inline-fields">
			<span><?php esc_html_e( 'Subscription will end after', 'yith-woocommerce-subscription' ); ?></span>
			<input
				type="number" class="ywsbs-short" name="_ywsbs_max_length" id="_ywsbs_max_length"
				value="<?php echo esc_attr( $_ywsbs_max_length ); ?>" min="0" />
			<span class="max-length-time-opt"><?php echo esc_html( $time_options[ $time_opt ] ); ?></span>
		</span>

	</p>
	<p class="form-field ywsbs_max_length-description"><span
			class="description"><?php esc_html_e( 'Choose if the subscription has an end time or not.', 'yith-woocommerce-subscription' ); ?></span>
	</p>

	<fieldset class="form-field yith-plugin-ui ywsbs_enable_trial onoff">
		<legend
			for="_ywsbs_enable_trial"><?php esc_html_e( 'Offer a trial period', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_enable_trial',
			'name'  => '_ywsbs_enable_trial',
			'value' => $_ywsbs_enable_trial,
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable to offer a trial period when the subscription is purchased.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<p class="form-field ywsbs_trial_per" data-deps-on="_ywsbs_enable_trial" data-deps-val="yes">
		<label
			for="_ywsbs_trial_per"><?php esc_html_e( 'Offer a free trial of', 'yith-woocommerce-subscription' ); ?></label>
		<input type="number" class="ywsbs-short" name="_ywsbs_trial_per" id="_ywsbs_trial_per" min="0"
			value="<?php echo esc_attr( $_ywsbs_trial_per ); ?>"/>
		<select id="_ywsbs_trial_time_option" name="_ywsbs_trial_time_option"
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

	<fieldset class="form-field yith-plugin-ui ywsbs_enable_fee onoff">
		<legend
			for="_ywsbs_enable_fee"><?php esc_html_e( 'Request a signup fee', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_enable_fee',
			'name'  => '_ywsbs_enable_fee',
			'value' => $_ywsbs_enable_fee,
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable to request a signup fee when the subscription is purchased.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<div data-deps-on="_ywsbs_enable_fee" data-deps-val="yes" class="form-field">
		<?php

		$args = array(
			'id'                => '_ywsbs_fee',
			'value'             => esc_attr( wc_format_localized_price( $_ywsbs_fee ) ),
			'description'       => esc_html__( 'The sign-up fee will be charged when the subscription is purchased.', 'yith-woocommerce-subscription' ),
			// translators: placeholder currency label.
			'label'             => sprintf( esc_html_x( 'Sign-up fee (%s)', 'currency value', 'yith-woocommerce-subscription' ), get_woocommerce_currency_symbol() ),
			'type'              => 'text',
			'data_type'         => 'decimal',
			'class'             => 'ywsbs-short ywsbs_fee wc_input_price',
			'custom_attributes' => 'data-deps-on="_ywsbs_override_cancellation_settings" data-deps-val="yes"',
		);
		woocommerce_wp_text_input( $args );

		?>
	</div>


	<fieldset class="form-field yith-plugin-ui ywsbs_enable_limit onoff">
		<legend
			for="_ywsbs_enable_limit"><?php esc_html_e( 'Apply subscription limits', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_enable_limit',
			'name'  => '_ywsbs_enable_limit',
			'value' => $_ywsbs_enable_limit,
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable to apply limits to the customer purchasing this subscription.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<div data-deps-on="_ywsbs_enable_limit" data-deps-val="yes" class="form-field">
	<?php
	$args = array(
		'label'       => esc_html__( 'Limit subscription', 'yith-woocommerce-subscription' ),
		'description' => esc_html__( 'Set optional limits for this product subscription.', 'yith-woocommerce-subscription' ),
		'value'       => $_ywsbs_limit,
		'id'          => '_ywsbs_limit',
		'name'        => '_ywsbs_limit',
		'options'     => array(
			'one-active' => esc_html__( 'Limit user to allow only one active subscription', 'yith-woocommerce-subscription' ),
			'one'        => esc_html__( 'Limit user to allow only one subscription of any status, either active or not', 'yith-woocommerce-subscription' ),
		),
	);
	woocommerce_wp_radio( $args );
	?>
	</div>

	<fieldset class="form-field yith-plugin-ui ywsbs_override_pause_settings onoff">
		<legend
			for="_ywsbs_override_pause_settings"><?php esc_html_e( 'Override global pausing settings', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_override_pause_settings',
			'name'  => '_ywsbs_override_pause_settings',
			'value' => $_ywsbs_override_pause_settings,
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable to set custom pausing rules for this product. This option overrides the option in the general settings.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<?php
	$args = array(
		'label'       => esc_html__( 'Allow users to pause this subscription', 'yith-woocommerce-subscription' ),
		'description' => esc_html__( 'Choose if a user can pause a subscription, and if so, to do so with or without limits.', 'yith-woocommerce-subscription' ),
		'value'       => $_ywsbs_enable_pause,
		'default'     => 'no',
		'id'          => '_ywsbs_enable_pause',
		'name'        => '_ywsbs_enable_pause',
		'options'     => array(
			'no'      => esc_html__( 'No, never', 'yith-woocommerce-subscription' ),
			'yes'     => esc_html__( 'Yes, user can pause without limits', 'yith-woocommerce-subscription' ),
			'limited' => esc_html__( 'Yes, user can pause with certain limits', 'yith-woocommerce-subscription' ),
		),
	);
	woocommerce_wp_radio( $args );
	?>
	<p class="form-field ywsbs_pause_options" data-deps-on="_ywsbs_enable_pause" data-deps-val="limited"
		data-type="radio">
		<label
			for="_ywsbs_max_pause"><?php esc_html_e( 'Subscription pausing limits', 'yith-woocommerce-subscription' ); ?></label>
		<span class="ywsbs-inline-fields">
			<span><?php esc_html_e( 'The user can pause this subscription for a max of', 'yith-woocommerce-subscription' ); ?></span>
		<input type="number" class="ywsbs-short" name="_ywsbs_max_pause" id="_ywsbs_max_pause" min="0"
			value="<?php echo esc_attr( $_ywsbs_max_pause ); ?>"/> <?php esc_html_e( 'times', 'yith-woocommerce-subscription' ); ?>;
		</span>
		<span class="ywsbs-inline-fields padding-10">
			<span><?php esc_html_e( 'Each pause can have a duration for a max of', 'yith-woocommerce-subscription' ); ?></span>
		<input type="number" class="ywsbs-short" name="_ywsbs_max_pause_duration" id="_ywsbs_max_pause_duration" min="0"
			value="<?php echo esc_attr( $_ywsbs_max_pause_duration ); ?>"/>
			<span><?php esc_html_e( 'days.', 'yith-woocommerce-subscription' ); ?></span>
			<span  class="inline-fields-desc"><?php esc_html_e( 'Then automatically the subscription will be reactivated.', 'yith-woocommerce-subscription' ); ?></span>
		</span>
	</p>

	<fieldset class="form-field yith-plugin-ui onoff">
		<legend
			for="_ywsbs_override_cancellation_settings"><?php esc_html_e( 'Override global cancellation settings', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_override_cancellation_settings',
			'name'  => '_ywsbs_override_cancellation_settings',
			'value' => $_ywsbs_override_cancellation_settings,
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable to set specific cancellation options for this product. It will override the cancellation option in the general settings.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<fieldset class="form-field yith-plugin-ui ywsbs_can_be_cancelled"
		data-deps-on="_ywsbs_override_cancellation_settings" data-deps-val="yes">
		<legend
			for="_ywsbs_can_be_cancelled"><?php esc_html_e( 'Allow users to cancel this subscription', 'yith-woocommerce-subscription' ); ?></legend>
		<?php
		$args = array(
			'type'  => 'onoff',
			'id'    => '_ywsbs_can_be_cancelled',
			'name'  => '_ywsbs_can_be_cancelled',
			'value' => $_ywsbs_can_be_cancelled,
		);
		yith_plugin_fw_get_field( $args, true );
		?>
		<span
			class="description"><?php esc_html_e( 'Enable if you want to allow users to cancel this subscription. This will override the option in the general settings.', 'yith-woocommerce-subscription' ); ?></span>
	</fieldset>

	<?php if ( YWSBS_Subscription_Delivery_Schedules()->has_delivery_scheduled( $product ) ): ?>
		<?php @include( 'single-product-delivery-schedules.php' ); ?>
	<?php endif; ?>
</div>
