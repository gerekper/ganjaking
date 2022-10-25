<?php 
	$settings_fileds = array();
foreach ($settings as $value) {
	// pr($value);
	if ('sectionend' != $value['type']) {
		$settings_fileds[$value['id']] = $value;
	}
}
	// pr($settings_fileds['wc_settings_anti_fraud_cancel_score']);

if ( '' === $current_section ) : ?>

	<section>

		<h2>General Settings</h2>
		<div id="<?php echo esc_attr($this->id . '_general_settings-description'); ?>">
			<?php echo wp_kses_post($settings_fileds[$this->id . '_general_settings']['desc']); ?>
		</div>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_thresholds_settings']); ?>

		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_low_risk_threshold">
							<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_low_risk_threshold']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_low_risk_threshold'] ); 
							echo  wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_low_risk_threshold" id="wc_settings_anti_fraud_low_risk_threshold" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_low_risk_threshold']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_low_risk_threshold')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_low_risk_threshold']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_low_risk_threshold']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_low_risk_threshold']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider" rowspan="2">
						<?php $this->opmc_score_slider( 0, false, true); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_higher_risk_threshold">
						<?php 
						echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_higher_risk_threshold']['name']); 
						$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_higher_risk_threshold'] ); 
						echo wp_kses_post($description['tooltip_html']);
						?>
						</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_higher_risk_threshold" id="wc_settings_anti_fraud_higher_risk_threshold" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_higher_risk_threshold']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_higher_risk_threshold')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_higher_risk_threshold']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_higher_risk_threshold']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_higher_risk_threshold']['custom_attributes']['max']); ?>">
					</td>
				</tr>
			</tbody>
		</table>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_pre_purchase_settings']); ?>

		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_fraud_check_before_payment">
						<?php echo wp_kses_post($settings_fileds['wc_af_fraud_check_before_payment']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_fraud_check_before_payment']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo esc_attr($settings_fileds['wc_af_fraud_check_before_payment']['title']); ?></span></legend>
						<label for="wc_af_fraud_check_before_payment" class="opmc-toggle-control">
							<input name="wc_af_fraud_check_before_payment" id="wc_af_fraud_check_before_payment" type="checkbox" value="1" <?php checked( get_option( 'wc_af_fraud_check_before_payment' ), 'yes' ); ?> >
							<span class="opmc-control"></span>
						</label> 
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
					   <label for="wc_af_pre_payment_message">
							<?php 
							echo wp_kses_post($settings_fileds['wc_af_pre_payment_message']['title']); 
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_af_pre_payment_message'] ); 
							echo wp_kses_post($description['tooltip_html']);
							?>
					   </label>
					</th>
					<td class="forminp forminp-textarea">
						<textarea name="wc_af_pre_payment_message" id="wc_af_pre_payment_message" style="width:100%; height: 100px;"><?php echo esc_html(get_option( 'wc_af_pre_payment_message' )); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_order_status_settings']); ?>

		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
						<label for="wc_af_fraud_update_state">
							<?php echo wp_kses_post($settings_fileds['wc_af_fraud_update_state']['title']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo esc_html($settings_fileds['wc_af_fraud_update_state']['desc_tip']); ?>"></span>
						</label>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_fraud_update_state']['title']); ?></span></legend>
							<label for="wc_af_fraud_update_state" class="opmc-toggle-control">
								<input name="wc_af_fraud_update_state" id="wc_af_fraud_update_state" type="checkbox" value="1" <?php checked( get_option( 'wc_af_fraud_update_state' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_cancel_score">
							<?php 
								echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_cancel_score']['name']); 
								$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_cancel_score'] ); 
								echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-select">
						<select name="wc_settings_anti_fraud_cancel_score" id="wc_settings_anti_fraud_cancel_score" style="display: block; width: 5em;" class="">
							<?php foreach ($settings_fileds['wc_settings_anti_fraud_cancel_score']['options'] as $option_key_inner => $option_value_inner) : ?>
								<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( (string) $option_key_inner, esc_attr( get_option('wc_settings_anti_fraud_cancel_score') ) ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_cancel_score'), true ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_hold_score">
							<?php 
								echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_hold_score']['title']); 
								$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_hold_score'] ); 
								echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-select">
						<select name="wc_settings_anti_fraud_hold_score" id="wc_settings_anti_fraud_hold_score" style="display: block; width: 5em;" class="">
							<?php foreach ($settings_fileds['wc_settings_anti_fraud_hold_score']['options'] as $option_key_inner => $option_value_inner) : ?>
								<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( (string) $option_key_inner, esc_attr( get_option('wc_settings_anti_fraud_hold_score') ) ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_hold_score'), true ); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_whitelist_payment_settings']); ?>

		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
						<label for="wc_af_enable_whitelist_payment_method">
							<?php echo wp_kses_post($settings_fileds['wc_af_enable_whitelist_payment_method']['title']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_enable_whitelist_payment_method']['desc_tip']); ?>"></span>
						</label>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_enable_whitelist_payment_method']['title']); ?></span></legend>
							<label for="wc_af_enable_whitelist_payment_method" class="opmc-toggle-control">
								<input name="wc_af_enable_whitelist_payment_method" id="wc_af_enable_whitelist_payment_method" type="checkbox" value="1" <?php checked( get_option( 'wc_af_enable_whitelist_payment_method' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_whitelist_payment_method">
							<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_whitelist_payment_method']['title']); 
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_whitelist_payment_method'] ); 
							echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-multiselect">
						<select name="wc_settings_anti_fraud_whitelist_payment_method[]" id="wc_settings_anti_fraud_whitelist_payment_method" style="" class="" multiple="multiple">
							<?php foreach ($settings_fileds['wc_settings_anti_fraud_whitelist_payment_method']['options'] as $option_key_inner => $option_value_inner) : ?>
								<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( in_array( (string) $option_key_inner, $settings_fileds['wc_settings_anti_fraud_whitelist_payment_method']['default'] ) ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_user_role_settings']); ?>

		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
						<label for="wc_af_enable_whitelist_user_roles">
							<?php echo wp_kses_post($settings_fileds['wc_af_enable_whitelist_user_roles']['title']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_enable_whitelist_user_roles']['desc_tip']); ?>"></span>
						</label>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_enable_whitelist_user_roles']['title']); ?></span></legend>
							<label for="wc_af_enable_whitelist_user_roles" class="opmc-toggle-control">
								<input name="wc_af_enable_whitelist_user_roles" id="wc_af_enable_whitelist_user_roles" type="checkbox" value="1" <?php checked( get_option( 'wc_af_enable_whitelist_user_roles' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_af_whitelist_user_roles">
							<?php 
							echo wp_kses_post($settings_fileds['wc_af_whitelist_user_roles']['title']); 
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_af_whitelist_user_roles'] ); 
							echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-multiselect">
						<select name="wc_af_whitelist_user_roles[]" id="wc_af_whitelist_user_roles" style="" class="" multiple="multiple">
							<?php foreach ($settings_fileds['wc_af_whitelist_user_roles']['options'] as $option_key_inner => $option_value_inner) : ?>
								<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected(in_array( (string) $option_key_inner, $settings_fileds['wc_af_whitelist_user_roles']['default'] ) ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_email_whitelist_settings']); ?>
		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_whitelist">
							<?php echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_whitelist']['name']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_whitelist']['desc_tip']); ?>"></span>
						</label>
					</th>
					<td  class="forminp forminp-textarea" colspan="3">
						<?php
							$email_whitelist = str_replace("\n", ',' , get_option( 'wc_settings_anti_fraud_whitelist' ));
						?>
						<textarea name="wc_settings_anti_fraud_whitelist" id="wc_settings_anti_fraud_whitelist" style="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_whitelist']['css']); ?>" class="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_whitelist']['class']); ?>"><?php echo esc_html($email_whitelist); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>


	</section>

<?php elseif ('rules' == $current_section) : ?>

	<section>
		<h2>General Rules</h2>
		<div id="<?php echo esc_attr($this->id . '_rule_settings-description'); ?>">
			<?php echo wp_kses_post($settings_fileds[$this->id . '_rule_settings']['desc']); ?>
		</div>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_first_time_purchase_settings']); ?>
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_first_order_custom">
						<?php echo wp_kses_post($settings_fileds['wc_af_first_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_first_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_first_order']['title']); ?></span></legend>
						<label for="wc_af_first_order" class="opmc-toggle-control">
						<input name="wc_af_first_order" id="wc_af_first_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_first_order' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_first_order_weight" id="wc_settings_anti_fraud_first_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_first_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_first_order_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_first_order_custom">
						<?php echo wp_kses_post($settings_fileds['wc_af_first_order_custom']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_first_order_custom']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_first_order_custom']['title']); ?></span></legend>
						<label for="wc_af_first_order_custom" class="opmc-toggle-control">
						<input name="wc_af_first_order_custom" id="wc_af_first_order_custom" type="checkbox" value="1" <?php checked( get_option( 'wc_af_first_order_custom' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_first_order_custom_weight" id="wc_settings_anti_fraud_first_order_custom_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_custom_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_first_order_custom_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_custom_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_custom_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_first_order_custom_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_first_order_custom_weight') ); ?>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>
			</tbody>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_address_based_rules_settings']); ?>

			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_ip_geolocation_order">
						<?php echo wp_kses_post($settings_fileds['wc_af_ip_geolocation_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_ip_geolocation_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_ip_geolocation_order']['title']); ?></span></legend>
						<label for="wc_af_ip_geolocation_order" class="opmc-toggle-control">
						<input name="wc_af_ip_geolocation_order" id="wc_af_ip_geolocation_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_ip_geolocation_order' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_ip_geolocation_order_weight" id="wc_settings_anti_fraud_ip_geolocation_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_geolocation_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_ip_geolocation_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_geolocation_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_geolocation_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_geolocation_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_ip_geolocation_order_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_bca_order">
						<?php echo wp_kses_post($settings_fileds['wc_af_bca_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_bca_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_bca_order']['title']); ?></span></legend>
						<label for="wc_af_bca_order" class="opmc-toggle-control">
							<input name="wc_af_bca_order" id="wc_af_bca_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_bca_order' ), 'yes' ); ?> >
							<span class="opmc-control"></span>
						</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_bca_order_weight" id="wc_settings_anti_fraud_bca_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_bca_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_bca_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_bca_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_bca_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_bca_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_bca_order_weight') ); ?>
					</td>
				</tr>
				<!-- /* Geo Localion */ -->
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_geolocation_order">
						<?php echo wp_kses_post($settings_fileds['wc_af_geolocation_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_geolocation_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_geolocation_order']['title']); ?></span></legend>
						<label for="wc_af_geolocation_order" class="opmc-toggle-control">
						<input name="wc_af_geolocation_order" id="wc_af_geolocation_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_geolocation_order' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_geolocation_order_weight" id="wc_settings_anti_fraud_geolocation_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_geolocation_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_geolocation_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_geolocation_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_geolocation_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_geolocation_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_geolocation_order_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_billing_phone_number_order">
						<?php echo wp_kses_post($settings_fileds['wc_af_billing_phone_number_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_billing_phone_number_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_billing_phone_number_order']['title']); ?></span></legend>
						<label for="wc_af_billing_phone_number_order" class="opmc-toggle-control">
						<input name="wc_af_billing_phone_number_order" id="wc_af_billing_phone_number_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_billing_phone_number_order' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_billing_phone_number_order_weight" id="wc_settings_anti_fraud_billing_phone_number_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_billing_phone_number_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_billing_phone_number_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_billing_phone_number_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_billing_phone_number_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_billing_phone_number_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_billing_phone_number_order_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_proxy_order">
						<?php echo wp_kses_post($settings_fileds['wc_af_proxy_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_proxy_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_proxy_order']['title']); ?></span></legend>
						<label for="wc_af_proxy_order" class="opmc-toggle-control">
						<input name="wc_af_proxy_order" id="wc_af_proxy_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_proxy_order' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_proxy_order_weight" id="wc_settings_anti_fraud_proxy_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_proxy_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_proxy_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_proxy_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_proxy_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_proxy_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_proxy_order_weight') ); ?>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>
			</tbody>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_multi_order_attempts_rules_settings']); ?>

			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_ip_multiple_check">
						<?php echo wp_kses_post($settings_fileds['wc_af_ip_multiple_check']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_ip_multiple_check']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_ip_multiple_check']['title']); ?></span></legend>
							<label for="wc_af_ip_multiple_check" class="opmc-toggle-control">
							<input name="wc_af_ip_multiple_check" id="wc_af_ip_multiple_check" type="checkbox" value="1" <?php checked( get_option( 'wc_af_ip_multiple_check' ), 'yes' ); ?> >
							<span class="opmc-control"></span>
						</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_ip_multiple_weight" id="wc_settings_anti_fraud_ip_multiple_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_ip_multiple_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_ip_multiple_weight') ); ?>
					</td>
				</tr>

				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_settings_anti_fraud_ip_multiple_time_span">
						<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_ip_multiple_time_span']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_ip_multiple_time_span'] ); 
							echo wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_ip_multiple_time_span" id="wc_settings_anti_fraud_ip_multiple_time_span" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_time_span']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_ip_multiple_time_span')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_time_span']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_ip_multiple_time_span']['custom_attributes']['step']); ?>">
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>

			</tbody>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_origin_countries_rules_settings']); ?>
		
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_international_order">
						<?php echo wp_kses_post($settings_fileds['wc_af_international_order']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_international_order']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_international_order']['title']); ?></span></legend>
						<label for="wc_af_international_order" class="opmc-toggle-control">
						<input name="wc_af_international_order" id="wc_af_international_order" type="checkbox" value="1" <?php checked( get_option( 'wc_af_international_order' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_international_order_weight" id="wc_settings_anti_fraud_international_order_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_international_order_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_international_order_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_international_order_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_international_order_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_international_order_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_international_order_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_unsafe_countries">
						<?php echo wp_kses_post($settings_fileds['wc_af_unsafe_countries']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_unsafe_countries']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_unsafe_countries']['title']); ?></span></legend>
						<label for="wc_af_unsafe_countries" class="opmc-toggle-control">
						<input name="wc_af_unsafe_countries" id="wc_af_unsafe_countries" type="checkbox" value="1" <?php checked( get_option( 'wc_af_unsafe_countries' ), 'yes' ); ?> >
						<span class="opmc-control"></span>
					</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_unsafe_countries_weight" id="wc_settings_anti_fraud_unsafe_countries_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_unsafe_countries_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_unsafe_countries_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_unsafe_countries_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_unsafe_countries_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_unsafe_countries_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_unsafe_countries_weight') ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_define_unsafe_countries_list">
							<?php 
									echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_define_unsafe_countries_list']['name']); 
									$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_define_unsafe_countries_list'] ); 
									echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-multiselect" colspan="2">
						<select name="wc_settings_anti_fraud_define_unsafe_countries_list[]" id="wc_settings_anti_fraud_define_unsafe_countries_list" class="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_define_unsafe_countries_list']['class']); ?>" multiple>
							<?php foreach ($settings_fileds['wc_settings_anti_fraud_define_unsafe_countries_list']['options'] as $option_key_inner => $option_value_inner) : ?>
								<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( in_array( (string) $option_key_inner, $settings_fileds['wc_settings_anti_fraud_define_unsafe_countries_list']['default'] ) ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>
			</tbody>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_high_risk_domain_rules_settings']); ?>

			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
						<label for="wc_af_suspecius_email">
							<?php echo wp_kses_post($settings_fileds['wc_af_suspecius_email']['title']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_suspecius_email']['desc_tip']); ?>"></span>
						</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_suspecius_email']['title']); ?></span></legend>
							<label for="wc_af_suspecius_email" class="opmc-toggle-control">
								<input name="wc_af_suspecius_email" id="wc_af_suspecius_email" type="checkbox" value="1" <?php checked( get_option( 'wc_af_suspecius_email' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_suspecious_email_weight" id="wc_settings_anti_fraud_suspecious_email_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_suspecious_email_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_suspecious_email_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_suspecious_email_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_suspecious_email_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_suspecious_email_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_suspecious_email_weight') ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_suspecious_email_domains">
							<?php 
									echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_suspecious_email_domains']['name']);
									$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_suspecious_email_domains'] ); 
									echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td  class="forminp forminp-textarea" colspan="3">
						<textarea name="wc_settings_anti_fraud_suspecious_email_domains" id="wc_settings_anti_fraud_suspecious_email_domains" style="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_suspecious_email_domains']['css']); ?>" class="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_suspecious_email_domains']['class']); ?>"><?php echo esc_html(get_option( 'wc_settings_anti_fraud_suspecious_email_domains' )); ?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="check_email_domain_api_key">
							<?php 
									echo wp_kses_post($settings_fileds['check_email_domain_api_key']['title']);
									$description = WC_Admin_Settings::get_field_description( $settings_fileds['check_email_domain_api_key'] ); 
									echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td  class="forminp forminp-text" colspan="3">
						<input name="check_email_domain_api_key" id="check_email_domain_api_key" type="<?php echo esc_attr($settings_fileds['check_email_domain_api_key']['type']); ?>" value="<?php echo esc_attr(get_option('check_email_domain_api_key')); ?>">
						<?php echo wp_kses_post($description['description']); ?>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>
				
			</tbody>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_order_amount_attempts_rules_settings']); ?>

			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_order_avg_amount_check">
						<?php echo wp_kses_post($settings_fileds['wc_af_order_avg_amount_check']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_order_avg_amount_check']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_order_avg_amount_check']['title']); ?></span></legend>
							<label for="wc_af_order_avg_amount_check" class="opmc-toggle-control">
								<input name="wc_af_order_avg_amount_check" id="wc_af_order_avg_amount_check" type="checkbox" value="1" <?php checked( get_option( 'wc_af_order_avg_amount_check' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_order_avg_amount_weight" id="wc_settings_anti_fraud_order_avg_amount_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_avg_amount_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_order_avg_amount_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_avg_amount_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_avg_amount_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_avg_amount_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_order_avg_amount_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_settings_anti_fraud_avg_amount_multiplier">
						<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_avg_amount_multiplier']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_avg_amount_multiplier'] ); 
							echo wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_avg_amount_multiplier" id="wc_settings_anti_fraud_avg_amount_multiplier" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_avg_amount_multiplier']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_avg_amount_multiplier')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_avg_amount_multiplier']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_avg_amount_multiplier']['custom_attributes']['step']); ?>">
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>

				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_order_amount_check">
						<?php echo wp_kses_post($settings_fileds['wc_af_order_amount_check']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_order_amount_check']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_order_amount_check']['title']); ?></span></legend>
							<label for="wc_af_order_amount_check" class="opmc-toggle-control">
								<input name="wc_af_order_amount_check" id="wc_af_order_amount_check" type="checkbox" value="1" <?php checked( get_option( 'wc_af_order_amount_check' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_order_amount_weight" id="wc_settings_anti_fraud_order_amount_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_amount_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_order_amount_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_amount_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_amount_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_amount_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_order_amount_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_settings_anti_fraud_amount_limit">
						<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_amount_limit']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_amount_limit'] ); 
							echo wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-text">
						<input name="wc_settings_anti_fraud_amount_limit" id="wc_settings_anti_fraud_amount_limit" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_amount_limit']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_amount_limit')); ?>">
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>

				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_attempt_count_check">
						<?php echo wp_kses_post($settings_fileds['wc_af_attempt_count_check']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($settings_fileds['wc_af_attempt_count_check']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_attempt_count_check']['title']); ?></span></legend>
							<label for="wc_af_attempt_count_check" class="opmc-toggle-control">
								<input name="wc_af_attempt_count_check" id="wc_af_attempt_count_check" type="checkbox" value="1" <?php checked( get_option( 'wc_af_attempt_count_check' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
						</fieldset>
					</td>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_order_attempt_weight" id="wc_settings_anti_fraud_order_attempt_weight" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_attempt_weight']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_order_attempt_weight')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_attempt_weight']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_attempt_weight']['custom_attributes']['step']); ?>" max="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_order_attempt_weight']['custom_attributes']['max']); ?>">
					</td>
					<td class="forminp forminp-slider">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_order_attempt_weight') ); ?>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_settings_anti_fraud_attempt_time_span">
						<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_attempt_time_span']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_attempt_time_span'] ); 
							echo wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_attempt_time_span" id="wc_settings_anti_fraud_attempt_time_span" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_attempt_time_span']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_attempt_time_span')); ?>">
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_settings_anti_fraud_max_order_attempt_time_span">
						<?php 
							echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_max_order_attempt_time_span']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_max_order_attempt_time_span'] ); 
							echo wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_settings_anti_fraud_max_order_attempt_time_span" id="wc_settings_anti_fraud_max_order_attempt_time_span" type="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_max_order_attempt_time_span']['type']); ?>" style="display: block; width: 5em;" value="<?php echo esc_attr(get_option('wc_settings_anti_fraud_max_order_attempt_time_span')); ?>" min="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_max_order_attempt_time_span']['custom_attributes']['min']); ?>" step="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_max_order_attempt_time_span']['custom_attributes']['step']); ?>">
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>

				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_limit_order_count">
						<?php echo wp_kses_post($settings_fileds['wc_af_limit_order_count']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_limit_order_count']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post($settings_fileds['wc_af_limit_order_count']['title']); ?></span></legend>
							<label for="wc_af_limit_order_count" class="opmc-toggle-control">
								<input name="wc_af_limit_order_count" id="wc_af_limit_order_count" type="checkbox" value="1" <?php checked( get_option( 'wc_af_limit_order_count' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
						</fieldset>
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_limit_time_start">
						<?php 
							echo wp_kses_post($settings_fileds['wc_af_limit_time_start']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_af_limit_time_start'] ); 
							echo  wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-time">
						<input name="wc_af_limit_time_start" id="wc_af_limit_time_start" type="<?php echo esc_attr($settings_fileds['wc_af_limit_time_start']['type']); ?>" style="<?php echo esc_attr($settings_fileds['wc_af_limit_time_start']['css']); ?>" value="<?php echo esc_attr(get_option('wc_af_limit_time_start')); ?>">
					</td>
				</tr>

				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_limit_time_end">
						<?php 
							echo wp_kses_post($settings_fileds['wc_af_limit_time_end']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_af_limit_time_end'] ); 
							echo  wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_af_limit_time_end" id="wc_af_limit_time_end" type="<?php echo esc_attr($settings_fileds['wc_af_limit_time_end']['type']); ?>" style="<?php echo esc_attr($settings_fileds['wc_af_limit_time_start']['css']); ?>" value="<?php echo esc_attr(get_option('wc_af_limit_time_end')); ?>">
					</td>
				</tr>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_allowed_order_limit">
						<?php 
							echo wp_kses_post($settings_fileds['wc_af_allowed_order_limit']['name']);
							$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_af_allowed_order_limit'] ); 
							echo  wp_kses_post($description['tooltip_html']);
						?>
					</label>
					</th>
					<td class="forminp forminp-number">
						<input name="wc_af_allowed_order_limit" id="wc_af_allowed_order_limit" type="<?php echo esc_attr($settings_fileds['wc_af_allowed_order_limit']['type']); ?>" style="<?php echo esc_attr($settings_fileds['wc_af_limit_time_start']['css']); ?>" value="<?php echo esc_attr(get_option('wc_af_allowed_order_limit')); ?>">
					</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<hr/>
					</td>
				</tr>

			</tbody>

		</table>
	</section>

<?php elseif ('email_alert' == $current_section) : ?>
	<section>

		<h2>Email Alerts</h2>
		<div id="<?php echo esc_attr($this->id . '_email_alert_settings-description'); ?>">
			<?php echo wp_kses_post($settings_fileds[$this->id . '_email_alert_settings']['desc']); ?>
		</div>


		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_email_notification">
						<?php echo wp_kses_post($settings_fileds['wc_af_email_notification']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_af_email_notification']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo esc_attr($settings_fileds['wc_af_email_notification']['title']); ?></span></legend>
						<label for="wc_af_email_notification" class="opmc-toggle-control">
							<input name="wc_af_email_notification" id="wc_af_email_notification" type="checkbox" value="1" <?php checked( get_option( 'wc_af_email_notification' ), 'yes' ); ?> >
							<span class="opmc-control"></span>
						</label> 
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_custom_email">
							<?php 
									echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_custom_email']['name']);
									$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_custom_email'] ); 
									echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td  class="forminp forminp-textarea" colspan="3">
						<textarea name="wc_settings_anti_fraud_custom_email" id="wc_settings_anti_fraud_custom_email" style="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_custom_email']['css']); ?>" class="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraud_custom_email']['class']); ?>"><?php echo esc_html(get_option( 'wc_settings_anti_fraud_custom_email' )); ?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraud_email_score">
							<?php 
									echo wp_kses_post($settings_fileds['wc_settings_anti_fraud_email_score']['name']); 
									$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraud_email_score'] ); 
									echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td class="forminp forminp-select">
						<select name="wc_settings_anti_fraud_email_score" id="wc_settings_anti_fraud_email_score" style="display: block; width: 5em;" class="">
							<?php foreach ($settings_fileds['wc_settings_anti_fraud_email_score']['options'] as $option_key_inner => $option_value_inner) : ?>
								<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( (string) $option_key_inner, esc_attr( get_option('wc_settings_anti_fraud_email_score') ) ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
							<?php endforeach ?>
						</select>
					</td>
					<td class="forminp forminp-slider" colspan="2">
						<?php $this->opmc_score_slider( get_option('wc_settings_anti_fraud_email_score') ); ?>
					</td>
				</tr>

			</tbody>
		</table>
	</section>
<?php elseif ('black_list' == $current_section) : ?>
	<section>
		<h2>Blacklist</h2>
		<div id="<?php echo esc_attr($this->id . '_blacklist_settings-description'); ?>">
			<?php echo wp_kses_post($settings_fileds[$this->id . '_blacklist_settings']['desc']); ?>
		</div>
		<hr/>
		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_sub_blacklist_settings']); ?>
		<table class="form-table opmc_wc_af_table">
				<tbody>
					<tr valign="top" class="">
						<th scope="row" class="titledesc">
						<label for="wc_af_email_blacklist">
							<?php echo wp_kses_post($settings_fileds['wc_settings_anti_fraudenable_automatic_email_blacklist']['title']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudenable_automatic_email_blacklist']['desc_tip']); ?>"></span>
						</label>
						</th>
						<td  class="forminp forminp-checkbox">
							<fieldset>
							<legend class="screen-reader-text"><span><?php echo esc_attr($settings_fileds['wc_settings_anti_fraudenable_automatic_email_blacklist']['title']); ?></span></legend>
							<label for="wc_settings_anti_fraudenable_automatic_email_blacklist" class="opmc-toggle-control">
								<input name="wc_settings_anti_fraudenable_automatic_email_blacklist" id="wc_settings_anti_fraudenable_automatic_email_blacklist" type="checkbox" value="1" <?php checked( get_option( 'wc_settings_anti_fraudenable_automatic_email_blacklist' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
							</fieldset>
						</td>
					</tr>

					<tr valign="top" class="">
						<th scope="row" class="titledesc">
						<label for="wc_af_automatic_blacklist">
							<?php echo wp_kses_post($settings_fileds['wc_settings_anti_fraudenable_automatic_blacklist']['title']); ?>
							<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudenable_automatic_blacklist']['desc_tip']); ?>"></span>
						</label>
						</th>
						<td  class="forminp forminp-checkbox">
							<fieldset>
							<legend class="screen-reader-text"><span><?php echo esc_attr($settings_fileds['wc_settings_anti_fraudenable_automatic_blacklist']['title']); ?></span></legend>
							<label for="wc_settings_anti_fraudenable_automatic_blacklist" class="opmc-toggle-control">
								<input name="wc_settings_anti_fraudenable_automatic_blacklist" id="wc_settings_anti_fraudenable_automatic_blacklist" type="checkbox" value="1" <?php checked( get_option( 'wc_settings_anti_fraudenable_automatic_blacklist' ), 'yes' ); ?> >
								<span class="opmc-control"></span>
							</label> 
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="wc_settings_anti_fraudblacklist_emails">
								<?php 
										echo wp_kses_post($settings_fileds['wc_settings_anti_fraudblacklist_emails']['name']);
										$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraudblacklist_emails'] ); 
										echo wp_kses_post($description['tooltip_html']);
								?>
							</label>
						</th>
						<td  class="forminp forminp-textarea" colspan="3">
							<textarea name="wc_settings_anti_fraudblacklist_emails" id="wc_settings_anti_fraudblacklist_emails" style="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudblacklist_emails']['css']); ?>" class="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudblacklist_emails']['class']); ?>"><?php echo esc_html(get_option( 'wc_settings_anti_fraudblacklist_emails' )); ?></textarea>
						</td>
					</tr>
					

				</tbody>
		</table>

		<?php $this->opmc_add_admin_field_section($settings_fileds[$this->id . '_sub_ip_blacklist_settings']); ?>
		<table class="form-table opmc_wc_af_table">
			<tbody>
				<tr valign="top" class="">
					<th scope="row" class="titledesc">
					<label for="wc_af_ip_blacklist">
						<?php echo wp_kses_post($settings_fileds['wc_settings_anti_fraudenable_automatic_ip_blacklist']['title']); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudenable_automatic_ip_blacklist']['desc_tip']); ?>"></span>
					</label>
					</th>
					<td  class="forminp forminp-checkbox">
						<fieldset>
						<legend class="screen-reader-text"><span><?php echo esc_attr($settings_fileds['wc_settings_anti_fraudenable_automatic_ip_blacklist']['title']); ?></span></legend>
						<label for="wc_settings_anti_fraudenable_automatic_ip_blacklist" class="opmc-toggle-control">
							<input name="wc_settings_anti_fraudenable_automatic_ip_blacklist" id="wc_settings_anti_fraudenable_automatic_ip_blacklist" type="checkbox" value="1" <?php checked( get_option( 'wc_settings_anti_fraudenable_automatic_ip_blacklist' ), 'yes' ); ?> >
							<span class="opmc-control"></span>
						</label> 
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wc_settings_anti_fraudblacklist_ipaddress">
							<?php 
									echo wp_kses_post($settings_fileds['wc_settings_anti_fraudblacklist_ipaddress']['name']);
									$description = WC_Admin_Settings::get_field_description( $settings_fileds['wc_settings_anti_fraudblacklist_ipaddress'] ); 
									echo wp_kses_post($description['tooltip_html']);
							?>
						</label>
					</th>
					<td  class="forminp forminp-textarea" colspan="3">
						<textarea name="wc_settings_anti_fraudblacklist_ipaddress" id="wc_settings_anti_fraud_blacklist_ipaddress" style="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudblacklist_ipaddress']['css']); ?>" class="<?php echo esc_attr($settings_fileds['wc_settings_anti_fraudblacklist_ipaddress']['class']); ?>"><?php echo esc_html(get_option( 'wc_settings_anti_fraudblacklist_ipaddress' )); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
<?php else : ?>

	<?php WC_Admin_Settings::output_fields( $settings ); ?>

<?php endif; ?>
