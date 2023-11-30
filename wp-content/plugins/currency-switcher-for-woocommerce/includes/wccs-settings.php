<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * This class defines wccs settings for the plugin.
 */

if ( ! class_exists( 'WCCS_Settings' ) ) {

	class WCCS_Settings {

		private $default_currency = null;

		public function __construct() {

			$this->default_currency = get_woocommerce_currency();

			// add plugin setting page
			add_action( 'admin_menu', array( $this, 'wccs_custom_menu_page' ), 99 );

			// hook on saving "update_type" option
			add_action( 'update_option_wccs_update_type', array( $this, 'wccs_update_type_option_logic' ), 10, 2 );

			// hook on saving "update_rate" option
			add_action( 'update_option_wccs_update_rate', array( $this, 'wccs_update_rate_option_logic' ), 10, 2 );

			// hook on updating "woocommerce currency" option
			add_action( 'update_option_woocommerce_currency', array( $this, 'wccs_woocommerce_currency_option_logic' ), 10, 2 );

			/**
			 * Add custom fields to coupon setting page admin
			 */
			// Add a custom field to Admin coupon settings pages
			add_action( 'woocommerce_coupon_options', array( $this, 'wccs_add_fields_to_coupon_setting' ), 10 );
			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'wccs_add_fields_to_coupon_usage_restriction_setting' ), 10 );

			// Save the custom field value from Admin coupon settings pages
			add_action( 'woocommerce_coupon_options_save', array( $this, 'wccs_save_fields_to_coupon_setting' ), 10, 2 );

			//adding zone pricing tab in product edit page
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'wccs_zp_product_tab' ), 10, 1 );
			//adding zone pricing tab content in product edit page
			add_action( 'woocommerce_product_data_panels', array( $this, 'wccs_zp_product_tab_content' ) );
			//save zone pricing product based values
			add_action('post_updated', array( $this, 'wccs_zp_product_data_tab_content_save' ), 10, 2 );
		}

		public function wccs_zp_product_data_tab_content_save( $post_id, $post ) {

			global $post;

			if ( is_object($post) && 'product' !== $post->post_type ) {
				return;
			}
			
			if ( !isset($_POST['zp_nonce']) || ( isset($_POST['zp_nonce']) && !wp_verify_nonce( wc_clean($_POST['zp_nonce']), 'wccs') ) ) {
				wp_die('Un Authorized!');
			}

			$data = $_POST;

			if ( isset( $data['zp_rate'] ) && count( $data['zp_rate'] ) > 0 ) {
				foreach ( $data['zp_rate'] as $key => $rate ) {
					if ( '' != $rate ) {
						update_post_meta( $post_id, 'wccs_zp_override_rate_for_' . $data['zp_id'][$key], $rate );
					} else {
						delete_post_meta( $post_id, 'wccs_zp_override_rate_for_' . $data['zp_id'][$key] );
					}
				}
			}           
		}

		public function wccs_zp_product_tab_content() {

			global $post;
			?>
			<div id='wccs_zp_tab' class='panel woocommerce_options_panel hidden'>
				<div id="wccs_zp_pricing_view_for_product" >
					<input type="hidden" name="zp_nonce" value="<?php echo wp_kses_post(wp_create_nonce('wccs')); ?>" />
					<?php /* translators: %1$s for <em>, %2$s for </em> */ ?>
					<p class="notice-warning notice wccs_zp_notice"><?php printf( esc_html__( 'Entering a new value will override exchange rate in  %1$s Zone Pricing %2$s setting tab. Leave it empty for default setting.', 'wccs' ), '<em>', '</em>' ); ?></p>
					<?php 
					// Get existing values from db.
					$wccs_zp_data = get_option( 'wccs_zp_data', false );
					if ( false != $wccs_zp_data && count( $wccs_zp_data ) > 0 ) {
						echo '<table class="wccs_zp_pricing_view">';
						echo '<thead>
								<tr>
									<th>Zone Name</th>
									<th>Countries</th>
									<th>Currency</th>
									<th>Exchange Rate</th>
									<th>Decimals</th>
								</tr>
							</thead>
							<tbody id="wccs_zp_data">';
						foreach ( $wccs_zp_data as $key => $zp_data ) {
							$zp_countries = implode( ', ', $zp_data['zp_countries'] );
							$individual_rate = get_post_meta( $post->ID, 'wccs_zp_override_rate_for_' . $key, true );
							$decimal = isset($zp_data['zp_decimal']) ? $zp_data['zp_decimal'] : 0;
							echo '<tr>								
								<td><span class="zp_name">' . esc_attr( $zp_data['zp_name'] ) . '</span></td>
								<td><span class="zp_countries">' . esc_attr( $zp_countries ) . '</span></td>
								<td><span class="zp_currency">' . esc_attr( $zp_data['zp_currency'] ) . '</span></td>
								<td>
									<span class="zp_rate">
										<input type="hidden" name="zp_id[]" value="' . esc_attr( $key ) . '" />
										<input type="text" name="zp_rate[]" placeholder="' . esc_attr( $zp_data['zp_rate'] ) . '" value="' . esc_attr( $individual_rate ) . '" style="margin-right: 10px" /> = 1 ' . esc_attr( $this->default_currency ) . '
									</span>
								</td>
								<td><span class="zp_decimal">' . esc_attr( $decimal ) . '</span></td>
							</tr>';                         
						}
						echo '</tbody></table>';
					}
					?>
				</div>
			</div>
			<?php
		}
		
		public function wccs_zp_product_tab( $default_tabs ) {
			$default_tabs['wccs_zp_tab'] = array(
				'label'   =>  __( 'WCCS Zone Pricing', 'wccs' ),
				'target'  =>  'wccs_zp_tab',
				'priority' => 60,
				'class' => array(),
			);
			return $default_tabs;
		}

		public function wccs_save_fields_to_coupon_setting( $post_id, $coupon ) {


			if ( get_option( 'wccs_fixed_coupon_amount' ) && get_option( 'wccs_pay_by_user_currency' ) ) { // If fixed amount for coupon setting is enable and also shop by user currency is enable.
				// wp_die();                
				if ( ! isset( $_POST['wccs_coupon_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wccs_coupon_nonce'] ), 'wccs_coupon_nonce' ) ) {
					wp_die( 'access denied! Nonce not verify.' );
				}

				$post = $_POST;

				if ( isset( $post['wccs_cfa_min_value'] ) && count( $post['wccs_cfa_min_value'] ) > 0 ) {
					$coupon_minmax_price_per_currency = array();
					foreach ( $post['wccs_cfa_min_value'] as $key => $value ) {
						$code                                     = sanitize_text_field( $post['wccs_cfa_minmax_code'][ $key ] );
						$coupon_fixed_price_per_currency[ $code ] = array(
							'min' => sanitize_text_field( $value ),
							'max' => sanitize_text_field( $post['wccs_cfa_max_value'][ $key ] ),
						);
					}

					$coupon->update_meta_data( 'wccs_cfa_minmax_data', $coupon_fixed_price_per_currency );
				} else {
					$coupon->update_meta_data( 'wccs_cfa_minmax_data', array() );
				}

				if ( isset( $post['wccs_cfa_value'] ) && count( $post['wccs_cfa_value'] ) > 0 ) {
					$coupon_fixed_price_per_currency = array();
					foreach ( $post['wccs_cfa_value'] as $key => $value ) {
						$code                                     = sanitize_text_field( $post['wccs_cfa_code'][ $key ] );
						$coupon_fixed_price_per_currency[ $code ] = sanitize_text_field( $value );
					}

					$coupon->update_meta_data( 'wccs_cfa_data', $coupon_fixed_price_per_currency );
				} else {
					$coupon->update_meta_data( 'wccs_cfa_data', array() );
				}

				if ( isset( $post['product_ids'] ) ) {
					$coupon->update_meta_data( 'product_ids', $post['product_ids'] );
				} else {
					$coupon->update_meta_data( 'product_ids', '' );
				}

				if ( isset( $post['exclude_product_ids'] ) ) {
					$coupon->update_meta_data( 'exclude_product_ids', $post['exclude_product_ids'] );
				} else {
					$coupon->update_meta_data( 'exclude_product_ids', '' );
				}

				if ( isset( $post['product_categories'] ) ) {
					$coupon->update_meta_data( 'product_categories', $post['product_categories'] );
				} else {
					$coupon->update_meta_data( 'product_categories', '' );
				}

				if ( isset( $post['exclude_product_categories'] ) ) {
					$coupon->update_meta_data( 'exclude_product_categories', $post['exclude_product_categories'] );
				} else {
					$coupon->update_meta_data( 'exclude_product_categories', '' );
				}

				if ( isset( $post['customer_email'] ) ) {
					$coupon->update_meta_data( 'customer_email', $post['customer_email'] );
				} else {
					$coupon->update_meta_data( 'customer_email', '' );
				}

				$coupon->save();
			}
		}

		/**
		 * Add custom fields to coupon usage restriction tab setting page admin
		 */
		public function wccs_add_fields_to_coupon_usage_restriction_setting() {

			if ( get_option( 'wccs_fixed_coupon_amount' ) && get_option( 'wccs_pay_by_user_currency' ) ) { // If fixed amount for coupon setting is enable and also shop by user currency is enable.
				$currencies = get_option( 'wccs_currencies', array() );

				global $post;
				$wccs_cfa_minmax_data = get_post_meta( $post->ID, 'wccs_cfa_minmax_data', true );

				// echo '<pre>$wccs_cfa_minmax_datawccs_cfa_minmax_data';
				// print_r( $wccs_cfa_minmax_data );
				// echo '</pre>';

				?>
				<div class="wccs_fixed_coupon_settings">
					<h3>WC Currency Switcher - the coupon Minimum and Maximum Spend</h3>
					<?php
					if ( ! empty( $currencies ) ) {
						?>
						<select class="wccs_get_defined_currency">
							<option value="">--select currency--</option>
							<?php
							foreach ( $currencies as $currency_code => $currency_data ) {
								?>
								<option value="<?php echo esc_attr( $currency_code ); ?>"><?php echo esc_attr( $currency_data['label'] ); ?></option>
								<?php
							}
							?>
						</select>
						<a class="button button-primary wccs_add_single_currency" data-type="multiple" id="">Add</a>
						<a class="button button-primary wccs_add_all_currencies" data-type="multiple" id="">Add All</a>
						<?php
					}
					?>

					<div id="wccs_coupon_minmax_amount_for_currencies_wrapped">
						<?php
						if ( ! empty( $wccs_cfa_minmax_data ) && count( $wccs_cfa_minmax_data ) > 0 ) {
							foreach ( $wccs_cfa_minmax_data as $code => $value ) {
								?>
								<p class=" form-field discount_type_field">
									<input type="hidden" name="wccs_cfa_minmax_code[]" value="<?php echo esc_attr( $code ); ?>" />
									
									<span class="wccs_form_control">
										<label for="wccs_cfa_min_value">
											<strong>Minimum spend (<?php echo esc_attr( $code ); ?>): </strong>                    
										</label>								
										<input type="text" id="wccs_cfa_min_value" name="wccs_cfa_min_value[]" Placeholder="auto" value="<?php echo esc_attr( $value['min'] ); ?>" />
										<a href="#" class="ml-10 button button-secondary wccs_cfa_remove">remove</a>
									</span>

									<span class="wccs_form_control">
										<label for="wccs_cfa_min_value">
											<strong>Maximum spend (<?php echo esc_attr( $code ); ?>): </strong>                    
										</label>
										<input type="text" id="wccs_cfa_max_value" name="wccs_cfa_max_value[]" Placeholder="auto" value="<?php echo esc_attr( $value['max'] ); ?>" />
									</span>								
								</p>
								<?php
							}
						}
						?>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Add custom fields to coupon setting page admin
		 */
		public function wccs_add_fields_to_coupon_setting() {

			if ( get_option( 'wccs_fixed_coupon_amount' ) && get_option( 'wccs_pay_by_user_currency' ) ) { // If fixed amount for coupon setting is enable and also shop by user currency is enable.
				$currencies = get_option( 'wccs_currencies', array() );

				// echo '<pre>';
				// print_r( $currencies );
				// echo '</pre>';
				global $post;
				$wccs_cfa_data = get_post_meta( $post->ID, 'wccs_cfa_data', true );

				// echo '<pre>$wccs_cfa_data';
				// print_r( $wccs_cfa_data );
				// echo '</pre>';

				?>
				<div class="wccs_fixed_coupon_settings">
					<h3>WC Currency Switcher - the coupon fixed amount</h3>
					<input type="hidden" name="wccs_coupon_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wccs_coupon_nonce' ) ); ?>">
					<?php
					if ( ! empty( $currencies ) ) {
						?>
						<select class="wccs_get_defined_currency">
							<option value="">--select currency--</option>
							<?php
							foreach ( $currencies as $currency_code => $currency_data ) {
								?>
								<option value="<?php echo esc_attr( $currency_code ); ?>"><?php echo esc_attr( $currency_data['label'] ); ?></option>
								<?php
							}
							?>
						</select>
						<a class="button button-primary wccs_add_single_currency" data-type="single" id="">Add</a>
						<a class="button button-primary wccs_add_all_currencies" data-type="single" id="">Add All</a>
						<?php
					}
					?>

					<div id="wccs_coupon_amount_for_currencies_wrapped">
						<?php
						if ( ! empty( $wccs_cfa_data ) && count( $wccs_cfa_data ) > 0 ) {
							foreach ( $wccs_cfa_data as $code => $value ) {
								?>
								<p class=" form-field discount_type_field">
									<label for="wccs_cfa_value">
										<strong>Coupon amount (<?php echo esc_attr( $code ); ?>): </strong>                    
									</label>
									<input type="hidden" name="wccs_cfa_code[]" value="<?php echo esc_attr( $code ); ?>" />
									<input type="text" id="wccs_cfa_value" name="wccs_cfa_value[]" Placeholder="auto" value="<?php echo esc_attr( $value ); ?>" />
									<a href="#" class="ml-10 button button-secondary wccs_cfa_remove">remove</a>
								</p>
								<?php
							}
						}
						?>
					</div>
				</div>
				<?php
			}
		}

		public function wccs_custom_menu_page() {
			add_submenu_page( 'woocommerce', 'WC Currency Switcher Settings', 'WCCS Settings', 'manage_options', 'wccs-settings', array( $this, 'wccs_settings_page_callback' ) );

			// call register settings function
			add_action( 'admin_init', array( $this, 'wccs_register_settings' ) );
		}

		public function wccs_register_settings() {
			if ( isset( $_POST['option_page'] ) ) {
				if ( ! empty( $_POST['option_page'] && wp_verify_nonce( sanitize_text_field( $_POST['custom_nonce'] ), 'custom_nonce' ) ) ) {
					$option_page = sanitize_text_field( $_POST['option_page'] );

					switch ( $option_page ) {
						case 'wccs-settings-general':
							register_setting( 'wccs-settings-general', 'wccs_update_type' );
							register_setting( 'wccs-settings-general', 'wccs_currency_display' );
							register_setting( 'wccs-settings-general', 'wccs_oer_api_key' );
							register_setting( 'wccs-settings-general', 'wccs_ipapi_key' );
							register_setting( 'wccs-settings-general', 'wccs_update_rate' );
							register_setting( 'wccs-settings-general', 'wccs_admin_email' );
							register_setting( 'wccs-settings-general', 'wccs_email' );
							register_setting( 'wccs-settings-general', 'wccs_currencies' );
							register_setting( 'wccs-settings-general', 'wccs_show_flag' );
							register_setting( 'wccs-settings-general', 'wccs_currency_storage' );
							register_setting( 'wccs-settings-general', 'wccs_show_currency' );
							register_setting( 'wccs-settings-general', 'wccs_currency_by_location' );
							register_setting( 'wccs-settings-general', 'wccs_show_in_menu' );
							register_setting( 'wccs-settings-general', 'wccs_switcher_menu' );
							register_setting( 'wccs-settings-general', 'wccs_shortcode_style' );
							register_setting( 'wccs-settings-general', 'wccs_sticky_switcher' );
							register_setting( 'wccs-settings-general', 'wccs_sticky_position' );
							register_setting( 'wccs-settings-general', 'wccs_default_currency_flag' );
							register_setting( 'wccs-settings-general', 'wccs_pay_by_user_currency' );
							register_setting( 'wccs-settings-general', 'wccs_fixed_coupon_amount' );
							register_setting( 'wccs-settings-general', 'wccs_currency_by_billing' );
							register_setting( 'wccs-settings-general', 'wccs_currency_by_lang' );
							register_setting( 'wccs-settings-general', 'wccs_lang' );
							break;
						case 'wccs-settings-zone-pricing':
							register_setting( 'wccs-settings-zone-pricing', 'wccs_zp_toggle' );                         
							break;
					}
				}               
			}
		}

		public function wccs_settings_page_callback() {
			$currencies = get_option( 'wccs_currencies', array() );
			if ( ! empty( $currencies ) ) {
				$available = wccs_get_available_currencies( array_keys( $currencies ) );
			}

			// prinitng errors for settings
			settings_errors();
			// initializing variable to check which tab is active
			$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'wccs-settings-general';
			?>
			<div class="wrap">
				<div id="icon-tools" class="icon32"></div>
				<h1><?php esc_html_e( 'WC Currency Switcher Settings', 'wccs' ); ?></h1>

				<div class="nav-tab-wrapper">
					<a href="?page=wccs-settings&tab=wccs-settings-general" class="nav-tab <?php echo ( 'wccs-settings-general' == $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'wccs' ); ?></a>
					<a href="?page=wccs-settings&tab=wccs-settings-zone-pricing" class="nav-tab <?php echo ( 'wccs-settings-zone-pricing' == $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Zone Pricing', 'wccs' ); ?></a>					
				</div>

				<form method="post" action="options.php">
					<input type="hidden" id="custom_nonce" name="custom_nonce" value="<?php echo esc_html( wp_create_nonce( 'custom_nonce' ) ); ?>">

					<?php

					if ( 'wccs-settings-zone-pricing' == $active_tab ) {
						settings_fields( 'wccs-settings-zone-pricing' );
						//do_settings_sections( 'wccs-settings-zone-pricing' );
						?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Zone Pricing', 'wccs' ); ?></th>
								<td>
									<label for="wccs_zp_toggle">
										<input type="checkbox" id="wccs_zp_toggle" name="wccs_zp_toggle" value="1" <?php if ( 1 ==get_option( 'wccs_zp_toggle' ) ) { ?> 
										checked <?php } ?> />
										<?php esc_html_e( 'Enable Zone Pricing', 'wccs' ); ?>
									</label>
									<?php /* translators: %1$s is em, %2$s is em end  */ ?>
									<p class="description"><?php printf( esc_html__( 'Enabling this option will override general settings except location IP feature. This option will require %1$s Shop Currency %2$s and %1$s Currency by Location %2$s to be enabled.', 'wccs' ), '<em>', '</em>' ); ?></p>
								</td>
							</tr>
						</table>

						<h2><?php esc_html_e( 'Price by Country', 'wccs' ); ?></h2>
						<br/>
						<h2><?php esc_html_e( 'Pricing Zone', 'wccs' ); ?></h2>
						<div class="wccs_zp_container">
							<div class="wccs_zp_col_50">
								<div class="wccs_form_control">
									<label for=""><?php esc_html_e( 'Zone Name', 'wccs' ); ?></label>
									<input type="text" id="wccs_zp_name" name="wccs_zp_name" >
									<span class="wccs_require_field"></span>
								</div>

								<div class="wccs_form_control">
									<label for=""><?php esc_html_e( 'Countries', 'wccs' ); ?></label>
									<?php 
									global $woocommerce;
									$countries_obj = new WC_Countries();
									$countries = $countries_obj->__get('countries');
									if ( ! empty( $countries ) && count( $countries ) > 0 ) {                                       
										?>
										<select id="wccs_zp_countries" class="wc-enhanced-select wccs-multiselect multiselect" name="wccs_zp_countires[]" multiple>	
											<option value="ALL">All Countries</option>
											<?php 
											foreach ( $countries as $key => $country ) {
												echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $country ) . '</option>';
											}
											?>
										</select>

										<!-- <button id="wccs_select_all_countries" value="select All">All Countries</button> -->
										<?php 
									}
									?>
									<span class="wccs_require_field"></span>
								</div>

								<div class="wccs_form_control">
									<label for=""><?php esc_html_e( 'Currency', 'wccs' ); ?></label>
									<?php
									$currencies = get_woocommerce_currencies();
									asort( $currencies );
									if ( isset( $currencies ) && count( $currencies ) > 0 ) {
										?>
										<select id="wccs_zp_currency" name="wccs_zp_currency" >
											<option value=""><?php esc_html_e( 'Add Currency', 'wccs' ); ?></option>
											<?php foreach ( $currencies as $code => $label ) { ?>
											<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
											<?php } ?>
										</select>
										<?php
									}
									?>
									<span class="wccs_require_field"></span>
								</div>

								<div class="wccs_form_control">
									<label for=""><?php esc_html_e( 'Exchange Rate', 'wccs' ); ?></label>
									<div class="wccs-flex-line">
										<input type="text" id="wccs_zp_rate" name="wccs_zp_rate" >	
										<span class="wccs_equal_to"><?php printf( esc_html( '= 1 %s', 'wccs' ), esc_attr( $this->default_currency ) ); ?></span>
									</div>
									<span class="wccs_require_field"></span>
								</div>

								<div class="wccs_form_control">
									<label for=""><?php esc_html_e( 'Decimals', 'wccs' ); ?></label>
									<select id="wccs_zp_decimal" name="wccs_zp_decimal">
										<option value="0">0</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
									</select>
									<span class="wccs_require_field"></span>
								</div>

								<a href="#" id="wccs_add_zp"><?php esc_html_e( 'Add Zone', 'wccs' ); ?></a>
							</div>
							<div class="wccs_zp_col_50">
								<table class="wccs_zp_pricing_view">
									<thead>
										<tr>
											<th><?php esc_html_e( 'Zone Name', 'wccs' ); ?></th>
											<th><?php esc_html_e( 'Countries', 'wccs' ); ?></th>
											<th><?php esc_html_e( 'Currency', 'wccs' ); ?></th>
											<th><?php esc_html_e( 'Exchange Rate', 'wccs' ); ?></th>
											<th><?php esc_html_e( 'Decimals', 'wccs' ); ?></th>
										</tr>
									</thead>
									<tbody id="wccs_zp_data">
										<?php 
										$wccs_zp_data = get_option( 'wccs_zp_data', false );
										if ( false != $wccs_zp_data ) {
											foreach ( $wccs_zp_data as $key => $zp_data ) {
												$zp_countries = implode(', ', $zp_data['zp_countries'] );
												$decimals = isset($zp_data['zp_decimal']) ? $zp_data['zp_decimal'] : 0;
												?>
												<tr>
													<td>
														<span class="zp_name"><?php echo esc_html( $zp_data['zp_name'] ); ?></span>
														<div class="wccs_zp_action">
															<a href="#" class="wccs_zp_edit" data-id="<?php echo esc_attr( $key ); ?>">Edit</a> | <a href="#" class="wccs_zp_delete" data-id="<?php echo esc_attr( $key ); ?>">Delete</a>
														</div>
													</td>
													<td>
														<span class="zp_countries"><?php echo esc_attr( $zp_countries ) ; ?></span>
													</td>
													<td>
														<span class="zp_currency"><?php echo esc_attr( $zp_data['zp_currency'] ); ?></span>
													</td>
													<td>
														<span class="zp_rate"><?php echo esc_attr( $zp_data['zp_rate'] ); ?></span>
													</td>
													<td>
														<span class="zp_decimal"><?php echo esc_attr( $decimals ); ?></span>
													</td>
												</tr>
												<?php
											}
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<?php
					}

					if ( 'wccs-settings-general' == $active_tab ) {
						settings_fields( 'wccs-settings-general' ); 
						?>

						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Currencies', 'wccs' ); ?></th>
								<td>
									<?php
									if ( isset( $available ) && count( $available ) ) {
										asort( $available );
										?>
									<select id="wccs_add_currency" class="test1">
										<option value=""><?php esc_html_e( 'Add Currency', 'wccs' ); ?></option>
										<?php foreach ( $available as $code => $label ) { ?>
										<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
										<?php } ?>
									</select>
										<?php 
									} else {
										$currencies = get_woocommerce_currencies();
										asort( $currencies );
										?>
										
										<select id="wccs_add_currency" class="test2">
											<option value=""><?php esc_html_e( 'Add Currency', 'wccs' ); ?></option>
											
											<?php foreach ( $currencies as $code => $label ) { ?>
											<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
											<?php } ?>
										</select>
										<?php
									}
									?>
									<?php if ( 'api' == get_option( 'wccs_update_type', 'fixed' ) ) { ?>
									<button type="button" id="wccs_update_all" class="button button-update"><i class="dashicons dashicons-update"></i> <?php esc_html_e( 'Update All', 'wccs' ); ?></button>
									<?php } ?>
									<table class="widefat" id="wccs_currencies_table"<?php if ( empty( $currencies ) || 0 == count( $currencies ) ) : ?> 
									style="display: none;" 
									<?php endif; ?>>
										<thead>
											<tr>
												<th><?php esc_html_e( 'Code', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Label', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Rate', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Price Format', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Symbol Prefix', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Decimals', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Price Rounding', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Price Charming', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Flag', 'wccs' ); ?></th>
												<th style="padding: 15px 4px"><?php esc_html_e( 'Payment Gateways', 'wccs' ); ?></th>
												<th><?php esc_html_e( 'Actions', 'wccs' ); ?></th>
											</tr>
										</thead>
										<tbody id="wccs_currencies_list">
										<?php

										$html = ''; 

										if ( ! empty( $currencies ) && ! empty( $available ) && count( $currencies ) > 0 ) {

											foreach ( (array) $currencies as $code => $info ) {
												$symbol = get_woocommerce_currency_symbol( $code );
												$flags  = wccs_get_all_flags();
												$info['label'] = isset($info['label']) ? $info['label'] : ''; 
												$required = get_option( 'wccs_update_type', 'fixed' ) == 'api' ? 'readonly' : 'required';
												$info['symbol_prefix'] = isset( $info['symbol_prefix'] ) ? $info['symbol_prefix'] : '';
												$rounding = isset($info['rounding']) ? $info['rounding'] : '0';
												$charming = isset($info['charming']) ? $info['charming'] : '0';
												$currency_countries = get_currency_countries( $code );
												$avl_payment_gateways = get_all_active_payment_gateways();
												?>
												<tr>

												<td><?php echo esc_attr($code); ?></td>
																								<td><input type="text" name="wccs_currencies[<?php echo esc_attr($code); ?>][label]" value="<?php echo esc_attr($info['label']); ?>" required></td>
												<?php $info['rate'] = isset($info['rate']) ? $info['rate'] : ''; ?>
												<td><input class="wccs_w_100" type="text" name="wccs_currencies[<?php echo esc_attr($code); ?>][rate]" value="<?php echo esc_attr($info['label']); ?>" <?php echo esc_attr( $required ); ?>></td>

												<td><select name="wccs_currencies[<?php echo esc_attr($code); ?>][format]" class="wccs_w_150">
												<option value="left" <?php selected($info['format'], 'left'); ?>><?php echo esc_html__( 'Left', 'wccs' ); ?></option>
												<option value="right" <?php selected($info['format'], 'right'); ?>><?php echo esc_html__( 'Right', 'wccs' ); ?></option>
												<option value="left_space" <?php selected($info['format'], 'left_space'); ?>><?php echo esc_html__( 'Left with space', 'wccs' ); ?></option>
												<option value="right_space" <?php selected($info['format'], 'right_space'); ?>><?php echo esc_html__( 'Right with space', 'wccs' ); ?></option>
												</select></td>

												<td>
												<input class="wccs_w_50" maxlength="4" type="text" name="wccs_currencies[<?php echo esc_attr($code); ?>][symbol_prefix]" value="<?php echo esc_attr($info['symbol_prefix']); ?>">
												</td>

												<td><select name="wccs_currencies[<?php echo esc_attr($code); ?>][decimals]" class="wccs_w_50">
													<option value="0" <?php selected($info['decimals'], '0'); ?>><?php echo esc_html__( '0', 'wccs' ); ?></option>
													<option value="1" <?php selected($info['decimals'], '1'); ?>><?php echo esc_html__( '1', 'wccs' ); ?></option>
													<option value="2" <?php selected($info['decimals'], '2'); ?>><?php echo esc_html__( '2', 'wccs' ); ?></option>
													<option value="3" <?php selected($info['decimals'], '3'); ?>><?php echo esc_html__( '3', 'wccs' ); ?></option>
													<option value="4" <?php selected($info['decimals'], '4'); ?>><?php echo esc_html__( '4', 'wccs' ); ?></option>
													<option value="5" <?php selected($info['decimals'], '5'); ?>><?php echo esc_html__( '5', 'wccs' ); ?></option>
													<option value="6" <?php selected($info['decimals'], '6'); ?>><?php echo esc_html__( '6', 'wccs' ); ?></option>
													<option value="7" <?php selected($info['decimals'], '7'); ?>><?php echo esc_html__( '7', 'wccs' ); ?></option>
													<option value="8" <?php selected($info['decimals'], '8'); ?>><?php echo esc_html__( '8', 'wccs' ); ?></option>
												</select></td>

												<td><select name="wccs_currencies[<?php echo esc_attr($code); ?>][rounding]">
													<option value="0" <?php selected($rounding, '0'); ?>><?php echo esc_html__( 'none', 'wccs' ); ?></option>
													<option value="0.25" <?php selected($rounding, '0.25'); ?>><?php echo esc_html__( '0.25', 'wccs' ); ?></option>
													<option value="0.5" <?php selected($rounding, '0.5'); ?>><?php echo esc_html__( '0.5', 'wccs' ); ?></option>
													<option value="1" <?php selected($rounding, '1'); ?>><?php echo esc_html__( '1', 'wccs' ); ?></option>
													<option value="5" <?php selected($rounding, '5'); ?>><?php echo esc_html__( '5', 'wccs' ); ?></option>
													<option value="10" <?php selected($rounding, '10'); ?>><?php echo esc_html__( '10', 'wccs' ); ?></option>
														
													</select></td>
												
											<td><select name="wccs_currencies[<?php echo esc_attr($code); ?>][charming]">
												<option value="0" <?php selected($charming, '0'); ?>><?php echo esc_html__( 'none', 'wccs' ); ?></option>
													<option value="-0.01" <?php selected($charming, '-0.01'); ?>><?php echo esc_html__( '-0.01', 'wccs' ); ?></option>
													<option value="-0.05" <?php selected($charming, '-0.05'); ?>><?php echo esc_html__( '-0.05', 'wccs' ); ?></option>
													<option value="-0.10" <?php selected($charming, '-0.10'); ?>><?php echo esc_html__( '-0.10', 'wccs' ); ?></option>
													</select></td>

												<td><select class="flags" name="wccs_currencies[<?php echo esc_attr($code); ?>][flag]">
												<option value=""><?php echo esc_html__( 'Choose Flag', 'wccs' ); ?></option>

												

												<?php 
												foreach ( (array) $flags as $country => $flag ) {

													foreach ( (array) $currency_countries as $value ) {
														if ( $country == $value ) {
															if ( count( $currency_countries ) == 1 ) {
																$selected = 'selected="selected"';
															} else {
																$selected = '';
															}
															if ( isset( $info['flag'] ) && strtolower( $country ) == $info['flag'] ) {
																$selected = 'selected="selected"';
															}
															?>
															<option value="<?php echo esc_attr(strtolower( $country )); ?>" <?php echo esc_attr($selected); ?> data-prefix="<span class='wcc-flag flag-icon flag-icon-<?php echo esc_attr(strtolower( $country )) ; ?>'></span>">
																(<?php echo esc_attr( $country ); ?>)
															</option>
																<?php
														}
													}
												}
												?>
												</select></td>

												<td class="wccs-payment-gateway-td">
												<?php
												if ( isset( $avl_payment_gateways ) && ! empty( $avl_payment_gateways ) ) {
													?>
													<button data-code="<?php echo esc_attr($code); ?>" class="button button-secondary wccs-close"><?php echo esc_html__( 'Hide Gateway', 'wccs' ); ?></button>
													<div class="wccs_payment_gateways_container">
														<ul>
															<?php
															foreach ( $avl_payment_gateways as $payment ) {
														
																$checked = ( isset( $info['payment_gateways'] ) && in_array( $payment['id'], $info['payment_gateways'] ) ) ? 'checked="true"' : '';
																?>
														<li> <label for="<?php echo esc_attr($code) . '_' . esc_attr($payment['id']); ?>"> <input type="checkbox" id="<?php echo esc_attr($code) . '_' . esc_attr($payment['id']); ?>" name="wccs_currencies[<?php echo esc_attr($code); ?>][payment_gateways][]" value="<?php echo esc_attr($payment['id']); ?>" <?php echo esc_attr($checked); ?> /></label><?php echo esc_attr($payment['title']); ?> </label></li>
																<?php
															}
															?>
													</ul></div>
													<?php
												} else {
													echo esc_html__( 'No payment Gateway is enabled', 'wccs' );
												}
												?>

												</td>

												<td>
												<div class="wccs_actions">
												<input type="hidden" name="wccs_currencies[<?php echo esc_attr($code); ?>][symbol]" value="<?php echo esc_attr($symbol); ?>">
												<?php if ( get_option( 'wccs_update_type', 'fixed' ) == 'api' ) { ?>
													<a href="javascript:void(0);" title="<?php echo esc_html__( 'Update rate', 'wccs' ); ?>" class="wccs_update_rate" data-code="<?php echo esc_attr($code); ?>"><i class="dashicons dashicons-update"></i></a>
													<?php
												}
												?>
												<span title="<?php echo esc_html__( 'Sort', 'wccs' ); ?>" style="cursor:grab;"><i class="dashicons dashicons-move"></i></span>
												<a href="javascript:void(0);" title="<?php echo esc_html__( 'Remove', 'wccs' ); ?>" class="wccs_remove_currency" data-value="<?php echo esc_attr($code); ?>" data-label="<?php echo esc_attr($info['label']); ?>"><i class="dashicons dashicons-trash"></i></a>
												</div>
												</td>
												</tr>
												<?php
											}
										}
										?>
										</tbody>
									</table>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Currency Display Type', 'wccs' ); ?></th>
								<td>
									<label id="wccs_currency_display_for_symbol"><input id="wccs_currency_display_for_symbol" type="radio" name="wccs_currency_display" value="symbol" <?php if ( 'symbol' == get_option( 'wccs_currency_display', 'symbol' ) ) { ?> 
									checked 
									<?php } ?>> <?php esc_html_e( 'Symbol', 'wccs' ); ?></label>&nbsp;&nbsp;
									<label for="wccs_currency_display_for_iso-code" ><input id="wccs_currency_display_for_iso-code" type="radio" name="wccs_currency_display" value="iso-code" <?php if ( 'iso-code' == get_option( 'wccs_currency_display', 'symbol' ) ) { ?> 
									checked 
									<?php } ?>> <?php esc_html_e( 'ISO Code', 'wccs' ); ?></label>
									
									<p class="description"><?php esc_html_e( 'Choose whether to show currency symbol or currency code on shop. Default is symbol', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Exchange Rates Type', 'wccs' ); ?></th>
								<td>
									<input type="radio" name="wccs_update_type" value="fixed"<?php if ( 'fixed' == get_option( 'wccs_update_type', 'fixed' ) ) { ?> 
									checked 
									<?php } ?>> <?php esc_html_e( 'Fixed', 'wccs' ); ?>
									<input type="radio" name="wccs_update_type" value="api"<?php if ( 'api' == get_option( 'wccs_update_type', 'fixed' ) ) { ?> 
									checked 
									<?php } ?>> <?php esc_html_e( 'API', 'wccs' ); ?>
									
									<p class="description"><?php esc_html_e( 'Choose how exchange rates will be added either manually (fixed) or automatically using API (api). Default is manually.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top" class="wccs_api_section">
								<th scope="row"><?php esc_html_e( 'Open Exchange Rates API key', 'wccs' ); ?></th>
								<td>
									<input type="text" name="wccs_oer_api_key" value="<?php echo esc_attr( get_option( 'wccs_oer_api_key' ) ); ?>"/>
									<p class="description"><?php esc_html_e( 'Add Open Exchange Rates API key to be used to get exchange rate data (it is required). You can get from here:', 'wccs' ); ?> <a href="https://openexchangerates.org/signup" target="_blank"><?php esc_html_e( 'Get API Key', 'wccs' ); ?></a><br/><?php esc_html_e( 'For API limitations', 'wccs' ); ?> <a href="https://openexchangerates.org/signup" target="_blank"><?php esc_html_e( 'Click here', 'wccs' ); ?></a></p>
								</td>
							</tr>
							
							<tr valign="top" class="wccs_api_section">
								<th scope="row"><?php esc_html_e( 'Update Rate', 'wccs' ); ?></th>
								<td>
									<select name="wccs_update_rate">
										<option value="hourly"<?php if ( 'hourly' == get_option( 'wccs_update_rate', 'hourly' ) ) { ?> 
										selected <?php } ?>><?php esc_html_e( 'Hourly', 'wccs' ); ?></option>
										<option value="twicedaily"<?php if ( 'twicedaily' == get_option( 'wccs_update_rate', 'hourly' ) ) { ?> 
										selected <?php } ?>><?php esc_html_e( 'Twice Daily', 'wccs' ); ?></option>
										<option value="daily"<?php if ( 'daily' == get_option( 'wccs_update_rate', 'hourly' ) ) { ?> 
										selected <?php } ?>><?php esc_html_e( 'Daily', 'wccs' ); ?></option>
										<option value="weekly"<?php if ( 'weekly' == get_option( 'wccs_update_rate', 'hourly' ) ) { ?> 
										selected <?php } ?>><?php esc_html_e( 'Weekly', 'wccs' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Choose how often the exchange rates for currencies will be updated by api.', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top" class="wccs_api_section">
								<th scope="row"><?php esc_html_e( 'Send Email', 'wccs' ); ?></th>
								<td>
									<label for="wccs_admin_email">
										<input type="checkbox" id="wccs_admin_email" name="wccs_admin_email" value="1" <?php if ( get_option( 'wccs_admin_email', 0 ) ) { ?> 
										checked <?php } ?> />
										<?php esc_html_e( 'Send a notification email', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this if you want an email to be sent each time a currency rate changes. Default is unchecked.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top" class="wccs_api_section wccs_email_section">
								<th scope="row"><?php esc_html_e( 'Email', 'wccs' ); ?></th>
								<td>
									<input type="email" name="wccs_email" value="<?php if ( get_option( 'wccs_email', '' ) ) { ?>
									<?php echo esc_attr( get_option( 'wccs_email' ) ); } ?>"/>
									<p class="description"><?php esc_html_e( 'Add the email that will receive the updated rates. If left empty, the email will be sent to admin email. (Note: if an email address is added, only it will receive the email.)', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Rate Storage', 'wccs' ); ?></th>
								<td>
									<select name="wccs_currency_storage">
										<option value="transient"<?php if ( get_option( 'wccs_currency_storage', 'transient' ) == 'transient' ) { ?> 
										selected<?php } ?>><?php esc_html_e( 'Transient', 'wccs' ); ?></option>
										<option value="session"<?php if ( get_option( 'wccs_currency_storage', 'transient' ) == 'session' ) { ?> 
										selected<?php } ?>><?php esc_html_e( 'Session', 'wccs' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Choose the way you want to cache rates. Default is Transient', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><?php printf( esc_html( 'Default Currency Flag (%s)', 'wccs' ), esc_attr( $this->default_currency ) ); ?></th>
								<td>
									<?php
										$currency_countries = get_currency_countries( $this->default_currency );
										$flags              = wccs_get_all_flags();
									?>
									<select class="flags" name="wccs_default_currency_flag" id="wccs_default_currency_flag">
										<option value=""><?php esc_html_e( 'Choose Flag', 'wccs' ); ?></option>
										<?php

										foreach ( $flags as $country => $flag ) {
											foreach ( $currency_countries as $value ) {
												$single_option = '';
												if ( $country == $value ) {
													if ( 1 == count( $currency_countries ) || strtolower( $country ) === get_option( 'wccs_default_currency_flag', true ) ) {
														$selected = 'selected="selected"';
													} else {
														$selected = '';
													}
													?>
													<option value="<?php echo esc_attr(strtolower( $country )); ?>" <?php echo esc_attr($selected); ?>  data-prefix="<span class='wcc-flag flag-icon flag-icon-<?php echo esc_attr(strtolower( $country )); ?>'></span>">(<?php echo esc_attr($country); ?>)</option>
													<?php
												}
											}
										}
										?>
									</select>                                
									<p class="description"><?php esc_html_e( 'Set flag for your default currency', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Flag', 'wccs' ); ?></th>
								<td>
									<label for="wccs_show_flag">
										<input type="checkbox" id="wccs_show_flag" name="wccs_show_flag" value="1" <?php if ( get_option( 'wccs_show_flag', 1 ) ) { ?> 
										checked <?php } ?> />
										<?php esc_html_e( 'Show country flag', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this if you want the switcher to have country flag. Default is checked.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Shop Currency', 'wccs' ); ?></th>
								<td>
									<label for="wccs_pay_by_user_currency">
										<input type="checkbox" id="wccs_pay_by_user_currency" name="wccs_pay_by_user_currency" value="1" <?php if ( get_option( 'wccs_pay_by_user_currency' ) ) { ?> 
										checked <?php } ?> />
										<?php esc_html_e( 'Pay in user selected currency', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this option to let user pay in their selected currency. Default is unchecked.', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top" class="wccs_fixed_coupon_amount_wrapper" style="display:none">
								<th scope="row"><?php esc_html_e( 'Fixed amount for coupon', 'wccs' ); ?></th>
								<td>
									<label for="wccs_fixed_coupon_amount">
										<input type="checkbox" id="wccs_fixed_coupon_amount" name="wccs_fixed_coupon_amount" value="1" <?php if ( get_option( 'wccs_fixed_coupon_amount', 0 ) ) { ?>
										checked<?php } ?> />
										<?php esc_html_e( 'Enable to set fixed amount for coupon against specific currency', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Pay in user selected currency option should be enabled.', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top" class="wccs_fixed_coupon_amount_wrapper" style="display:none">
								<th scope="row"><?php esc_html_e( 'Currency on Billing', 'wccs' ); ?></th>
								<td>
									<label for="wccs_currency_by_billing">
										<input type="checkbox" id="wccs_currency_by_billing" name="wccs_currency_by_billing" value="1" <?php if ( get_option( 'wccs_currency_by_billing' ) ) { ?>
										checked<?php } ?> />
										<?php esc_html_e( 'Enable to change currency according to the billing country on checkout.', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Pay in user selected currency option should be enabled.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Currency Symbol', 'wccs' ); ?></th>
								<td>
									<label for="wccs_show_currency">
										<input type="checkbox" id="wccs_show_currency" name="wccs_show_currency" value="1" <?php if ( get_option( 'wccs_show_currency', 1 ) ) { ?>
										checked<?php } ?> />
										<?php esc_html_e( 'Show currency symbol', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this if you want the switcher to have currency symbol. Default is checked.', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Currency by Location', 'wccs' ); ?></th>
								<td>
									<label for="wccs_currency_by_location">
										<input type="checkbox" id="wccs_currency_by_location" name="wccs_currency_by_location" value="1" <?php if ( get_option( 'wccs_currency_by_location' ) ) { ?>
										checked<?php } ?> />
										<?php esc_html_e( 'Show currency by location', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this if you want the switcher to select User IP\'s currency. If not then default will selected.', 'wccs' ); ?></p>
								</td>
							</tr>

							<tr valign="top" class="wccs_ipapi_key" style="display:none;">
								<th scope="row"><?php esc_html_e( 'Location API Access key', 'wccs' ); ?></th>
								<td>
									<input type="text" id="wccs_ipapi_key" name="wccs_ipapi_key" value="<?php echo esc_attr( get_option('wccs_ipapi_key') ); ?>" />
									<p class="description"><?php esc_html_e( 'Add Location API key to be used to change currency on location (it is required). You can get from here:', 'wccs' ); ?> <a href="https://ipapi.com/signup" target="_blank"><?php esc_html_e( 'Get API Key', 'wccs' ); ?></a></p>
								</td>
							</tr>
							
							<?php
							if ( function_exists( 'icl_get_languages' ) ) {
								?>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( 'Currency by Language', 'wccs' ); ?></th>
									<td>
										<label for="wccs_currency_by_lang">
											<input type="checkbox" id="wccs_currency_by_lang" name="wccs_currency_by_lang" value="1" <?php if ( get_option( 'wccs_currency_by_lang' ) ) { ?>
											checked<?php } ?> />
											<?php esc_html_e( 'Enable to change currency according to the users language.', 'wccs' ); ?>
										</label>
										<p class="description"><?php esc_html_e( 'Enable to change currency by WPML language. Currency by location must be disabled.', 'wccs' ); ?></p>
									</td>
								</tr>
								
								<tr valign="top" class="wccs_wpml_lang_wrapper" style="display:none;">
									<th scope="row"><?php esc_html_e( 'Languages', 'wccs' ); ?></th>
									<td>
									<?php
									if ( count( icl_get_languages() ) > 0 ) {
										?>
										<ul>
										<?php
										// print_r( icl_get_languages() );
										$wccs_lang = get_option( 'wccs_lang' );
										// echo '<pre>';
										// print_r( $wccs_lang );
										// echo '</pre>';
										foreach ( icl_get_languages() as $lang => $details ) {
											?>
											<li>
												<label style="min-width: 150px; display:inline-block">Set currency for <?php echo esc_attr( $details['translated_name'] ); ?></label>
												<?php
												if ( ! empty( $currencies ) && ! empty( $available ) && count( $currencies ) > 0 ) { 
													?>
													<select name="wccs_lang[<?php echo esc_attr( $lang ); ?>]" class="wccs_lang_dd">
														<option value=""><?php echo esc_attr('Default Currency', 'wccs'); ?></option>
														<?php 
														foreach ( $currencies as $currency_code => $values ) {
															$code = isset( $wccs_lang[ $lang ] ) ? esc_attr( $wccs_lang[ $lang ] ) : '';
															if ( $code == $currency_code ) {
																$selected = 'selected="selected"';
															} else {
																$selected = '';
															}
															?>
															<option value="<?php echo esc_attr( $currency_code ); ?>" <?php echo esc_attr( @$selected ); ?>><?php echo esc_attr( $values['label'] ); ?></option>
															<?php
														}
														?>
													</select>
													<?php
												}
												?>
											</li>
											<?php
										}
										?>
										</ul>
										<?php
									}
									?>
									</td>
								</tr>
								<?php
							}
							?>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Add to Menu', 'wccs' ); ?></th>
								<td>
									<label for="wccs_show_in_menu">
										<input type="checkbox" id="wccs_show_in_menu" name="wccs_show_in_menu" value="1" <?php if ( get_option( 'wccs_show_in_menu', 0 ) ) { ?> 
										checked <?php } ?> />
										<?php esc_html_e( 'Add switcher as a menu item', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this if you want to add switcher to a menu as a menu and submenu item. Default is unchecked.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top" class="wccs_menu_section">
								<th scope="row"><?php esc_html_e( 'Switcher Menu', 'wccs' ); ?></th>
								<td>
									<select name="wccs_switcher_menu">
										<?php
										$menus = wp_get_nav_menus();
										foreach ( $menus as $menu ) {
											?>
										<option value="<?php echo esc_attr( $menu->slug ); ?>"<?php if ( get_option( 'wccs_switcher_menu', '' ) == $menu->slug ) { ?> 
										selected <?php } ?>><?php echo esc_html( $menu->name ); ?></option>
											<?php
										}
										?>
									</select>
									<p class="description"><?php esc_html_e( 'Choose the menu you want the switcher to be added to.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Shortcodes', 'wccs' ); ?></th>
								<td>
									<p class="description"><?php esc_html_e( 'Use [wcc_switcher] shortcode to view the currency switcher any place you want.', 'wccs' ); ?></p>
									<p class="description"><?php esc_html_e( 'Use [wcc_rates] shortcode to view the currency rates any place you want.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Switcher Style', 'wccs' ); ?></th>
								<td>
									<select name="wccs_shortcode_style">
										<option value="style_01"<?php if ( 'style_01' == get_option( 'wccs_shortcode_style', 'style_01' ) ) { ?> 
										selected<?php } ?>><?php esc_html_e( 'Style 1', 'wccs' ); ?></option>
										<option value="style_02"<?php if ( 'style_02' == get_option( 'wccs_shortcode_style', 'style_01' ) ) { ?> 
										selected<?php } ?>><?php esc_html_e( 'Style 2', 'wccs' ); ?></option>
										<option value="style_03"<?php if ( 'style_03' == get_option( 'wccs_shortcode_style', 'style_01' ) ) { ?> 
										selected<?php } ?>><?php esc_html_e( 'Style 3', 'wccs' ); ?></option>
										<option value="style_04"<?php if ( 'style_04' == get_option( 'wccs_shortcode_style', 'style_01' ) ) { ?> 
										selected<?php } ?>><?php esc_html_e( 'Style 4', 'wccs' ); ?></option>
									</select>
									<p class="description"><?php esc_html_e( 'Choose different style for [wcc_switcher] shortcode. You can also override the style for specific shortcode using attribute ex: [wcc_switcher style="style_02"]. Default is Style 1.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Sticky Switcher', 'wccs' ); ?></th>
								<td>
									<label for="wccs_sticky_switcher">
										<input type="checkbox" id="wccs_sticky_switcher" name="wccs_sticky_switcher" value="1" <?php if ( get_option( 'wccs_sticky_switcher', 0 ) ) { ?>
										checked<?php } ?> />
										<?php esc_html_e( 'Add sticky switcher', 'wccs' ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Check this if you want to add sticky switcher to all website pages. Default is unchecked.', 'wccs' ); ?></p>
								</td>
							</tr>
							
							<tr valign="top" class="wccs_sticky_section">
								<th scope="row"><?php esc_html_e( 'Sticky Switcher Position', 'wccs' ); ?></th>
								<td>
									<input type="radio" name="wccs_sticky_position" value="right"<?php if ( 'right' == get_option( 'wccs_sticky_position', 'right' ) ) { ?> 
									checked<?php } ?>> <?php esc_html_e( 'Right', 'wccs' ); ?>
									<input type="radio" name="wccs_sticky_position" value="left"<?php if ( 'left' == get_option( 'wccs_sticky_position', 'right' ) ) { ?> 
									checked<?php } ?>> <?php esc_html_e( 'Left', 'wccs' ); ?>
									
									<p class="description"><?php esc_html_e( 'Choose Sticky Switcher Position in page either right or left. Default is right.', 'wccs' ); ?></p>
								</td>
							</tr>						
						</table>
						<?php
						submit_button();
					}                   

					?>

				</form>
			</div>
			<?php
		}
										
		public function wccs_update_type_option_logic( $old_value, $new_value ) {
			if ( $old_value != $new_value ) {
				if ( 'api' == $new_value ) {
					$frequency = get_option( 'wccs_update_rate', 'hourly' );
					if ( ( ! wp_next_scheduled( 'wccs_update_rates' ) ) && $frequency ) {
						wp_schedule_event( strtotime( 'now' ), $frequency, 'wccs_update_rates', array( true ) );
					}
				} else {
					wp_clear_scheduled_hook( 'wccs_update_rates', array( true ) );
				}
			}
		}

		public function wccs_update_rate_option_logic( $old_value, $new_value ) {
			if ( 'api' == get_option( 'wccs_update_type', 'fixed' ) && $old_value != $new_value ) {
				wp_clear_scheduled_hook( 'wccs_update_rates', array( true ) );

				if ( ! wp_next_scheduled( 'wccs_update_rates' ) ) {
					wp_schedule_event( strtotime( 'now' ), $new_value, 'wccs_update_rates', array( true ) );
				}
			}
		}

		public function wccs_woocommerce_currency_option_logic( $old_value, $new_value ) {
			if ( $old_value != $new_value ) {
				$currencies = get_option( 'wccs_currencies', array() );

				if ( isset( $currencies[ $new_value ] ) ) {
					unset( $currencies[ $new_value ] );

					update_option( 'wccs_currencies', $currencies );
				}
			}
		}
	}

	$wccs_settings = new WCCS_Settings();
}
