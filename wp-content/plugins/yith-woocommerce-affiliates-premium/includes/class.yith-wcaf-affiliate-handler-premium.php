<?php
/**
 * Affiliate Handler Premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Affiliate_Handler_Premium' ) ) {
	/**
	 * WooCommerce Affiliate Handler Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Affiliate_Handler_Premium extends YITH_WCAF_Affiliate_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Affiliate_Handler_Premium
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Affiliate_Handler_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'admin_init', array( $this, 'export_csv' ) );
			add_action( 'admin_action_yith_wcaf_pay_commissions', array(
				$this,
				'handle_pay_commissions_panel_actions'
			) );
			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ) );
		}

		/**
		 * Filter general settings, to add notification settings
		 *
		 * @param $settings mixed Original settings array
		 *
		 * @return mixed Filtered settings array
		 * @since 1.0.0
		 */
		public function filter_general_settings( $settings ) {
			$auto_enable_setting         = array(
				'referral-registration-auto-enable' => array(
					'title'   => __( 'Auto enable affiliates', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Auto enable affiliates on registration', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_auto_enable',
					'default' => 'yes'
				),
			);
			$registration_settings       = array(
				'referral-registration-show-website-field'             => array(
					'title'   => __( 'Show website field', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Show "Website" field on registration form', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_show_website_field',
					'default' => 'no'
				),
				'referral-registration-show-promotional-methods-field' => array(
					'title'   => __( 'Show promotional methods field', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Show "How will you promote?" field on registration form', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_show_promotional_methods_field',
					'default' => 'no'
				),
				'referral-registration-show-terms-field'               => array(
					'title'   => __( 'Show Terms & Conditions field', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Show "Terms & Condition" checkbox on registration form', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_show_terms_field',
					'default' => 'no'
				),
				'referral-registration-terms-label'                    => array(
					'title'   => __( 'Terms & Conditions label', 'yith-woocommerce-affiliates' ),
					'type'    => 'text',
					'desc'    => __( 'Label for Terms & Condition checkbox; use <code>%TERMS%</code> placeholder to include a link to Terms & Condition page', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_terms_label',
					'default' => __( 'Please, read an accept our %TERMS%' )
				),
				'referral-registration-terms-anchor-url'               => array(
					'title'   => __( 'Terms & Conditions anchor url', 'yith-woocommerce-affiliates' ),
					'type'    => 'text',
					'desc'    => __( 'Url to Terms & Conditions page; will be used to generate anchor inside Terms & Conditions label', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_terms_anchor_url',
					'default' => ''
				),
				'referral-registration-terms-anchor-text'              => array(
					'title'   => __( 'Terms & Conditions anchor text', 'yith-woocommerce-affiliates' ),
					'type'    => 'text',
					'desc'    => __( 'Text used to generate anchor inside Terms & Conditions label', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_referral_registration_terms_anchor_text',
					'default' => ''
				),
			);
			$notify_registration_setting = array(
				'referral-registration-notify-admin'          => array(
					'title'   => __( 'Notify admin', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => sprintf( '%s <a href="%s">%s</a>', __( 'Notify admin of a new affiliate registration; customize email on', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
						'page'    => 'wc-settings',
						'tab'     => 'email',
						'section' => 'yith_wcaf_admin_new_affiliate_email'
					), admin_url( 'admin.php' ) ) ), __( 'WooCommerce Settings Page', 'yith-woocommerce-affiliates' ) ),
					'id'      => 'yith_wcaf_referral_registration_notify_admin',
					'default' => 'yes'
				),
				'referral-registration-notify-affiliates'     => array(
					'title'   => __( 'Notify affiliate when account changes status', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => sprintf( '%s <a href="%s">%s</a>', __( 'Notify affiliates of any change in the status of his/her affiliation request; customize email on', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
						'page'    => 'wc-settings',
						'tab'     => 'email',
						'section' => 'yith_wcaf_customer_status_change_email'
					), admin_url( 'admin.php' ) ) ), __( 'WooCommerce Settings Page', 'yith-woocommerce-affiliates' ) ),
					'id'      => 'yith_wcaf_referral_registration_notify_affiliates',
					'default' => 'yes'
				),
				'referral-registration-notify-affiliates-ban' => array(
					'title'   => __( 'Notify affiliate when account gets banned', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => sprintf( '%s <a href="%s">%s</a>', __( 'Notify affiliates whenever his/her account is banned; customize email on', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
						'page'    => 'wc-settings',
						'tab'     => 'email',
						'section' => 'yith_wcaf_customer_ban_email'
					), admin_url( 'admin.php' ) ) ), __( 'WooCommerce Settings Page', 'yith-woocommerce-affiliates' ) ),
					'id'      => 'yith_wcaf_referral_registration_notify_affiliates_ban',
					'default' => 'yes'
				),
			);
			$ban_settings                = array(
				'referral-ban-options'               => array(
					'title' => __( 'Banned/Rejected affiliates', 'yith-woocommerce-affiliates' ),
					'type'  => 'title',
					'id'    => 'yith_wcaf_referral_ban_options'
				),
				'referral-ban-reject-global-message' => array(
					'title'    => __( 'Global reject message', 'yith-woocommerce-affiliates' ),
					'type'     => 'textarea',
					'desc'     => __( 'Enter a message to show to all rejexted users; you can override this message, using appropriate option in user\'s profile', 'yith-woocommerce-affiliates' ),
					'id'       => 'yith_wcaf_ban_reject_global_message',
					'css'      => 'min-width: 300px;min-height: 100px;',
					'default'  => '',
					'desc_tip' => true
				),
				'referral-ban-global-message'        => array(
					'title'    => __( 'Global ban message', 'yith-woocommerce-affiliates' ),
					'type'     => 'textarea',
					'desc'     => __( 'Enter a message to show to all banned users; you can override this message, using appropriate option in user\'s profile', 'yith-woocommerce-affiliates' ),
					'id'       => 'yith_wcaf_ban_global_message',
					'css'      => 'min-width: 300px;min-height: 100px;',
					'default'  => '',
					'desc_tip' => true
				),
				'referral-ban-hidden-sections'       => array(
					'title'   => __( 'Hidden sections', 'yith-woocommerce-affiliates' ),
					'type'    => 'multiselect',
					'desc'    => __( 'Select any Affiliate\'s Dashboard section that you want to hide to all your banned affiliates', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_ban_hidden_sections',
					'options' => array(
						'summary'       => __( 'Dashboard', 'yith-woocommerce-affiliates' ),
						'commissions'   => __( 'Commissions', 'yith-woocommerce-affiliates' ),
						'clicks'        => __( 'Clicks', 'yith-woocommerce-affiliates' ),
						'payments'      => __( 'Payments', 'yith-woocommerce-affiliates' ),
						'withdraw'      => __( 'Withdraw', 'yith-woocommerce-affiliates' ),
						'generate-link' => __( 'Generate Link', 'yith-woocommerce-affiliates' ),
						'settings'      => __( 'Settings', 'yith-woocommerce-affiliates' )
					),
					'css'     => 'min-width: 300px;',
					'class'   => 'wc-enhanced-select',
					'default' => '',
				),
				'referral-ban-options-end'           => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcaf_referral_ban_options'
				),
			);

			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'referral-registration-form', $auto_enable_setting );
			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'referral-registration-show-surname-field', $registration_settings );
			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'referral-registration-show-fields-on-become-an-affiliate', $notify_registration_setting );
			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'referral-registration-options-end', $ban_settings );

			return $settings;
		}

		/* === HELPER METHODS === */

		/**
		 * Checks whether current affiliate has been excluded from affiliation program
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is a valid affiliate or not
		 * @since 1.2.5
		 */
		public function is_user_excluded_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$excluded_users = get_option( 'yith_wcaf_exclusions_excluded_users', array() );

			if ( ! is_array( $excluded_users ) ) {

				$excluded_users = array();
			}

			return apply_filters( 'yith_wcaf_is_user_excluded_affiliate', in_array( $user_id, $excluded_users ), $user_id );
		}

		/**
		 * Check if user can see a specific section of the Affiliate Dashboard
		 *
		 * @param $user_id int|bool User id; false to use current user id
		 * @param $section string Section id
		 * @param $nopriv  bool Whether section should be visible by unauthenticated users or not
		 *
		 * @return bool Whether user can see section or not
		 *
		 * @since 1.2.5
		 */
		public function can_user_see_section( $user_id = false, $section = 'summary', $nopriv = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id && ! $nopriv ) {
				return false;
			}

			$return = true;

			if ( $this->is_user_banned_affiliate( $user_id ) ) {
				$hidden_sections = get_option( 'yith_wcaf_ban_hidden_sections' );
				$return          = ! in_array( $section, $hidden_sections );
			}

			return apply_filters( 'yith_wcaf_can_user_see_section', $return, $user_id, $section );
		}

		/* === FORM HANDLER METHODS === */

		/**
		 * Flag a registered user as an affiliates
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function become_an_affiliate() {
			if ( ( isset( $_REQUEST['become_an_affiliate'] ) && $_REQUEST['become_an_affiliate'] == 1 ) || apply_filters( 'yith_wcaf_become_an_affiliate_check', false ) ) {
				if ( is_user_logged_in() ) {
					$user_id     = get_current_user_id();
					$auto_enable = 'yes' == get_option( 'yith_wcaf_referral_registration_auto_enable' );
					$customer_id = get_current_user_id();
					$affiliates  = $this->get_affiliates( array( 'user_id' => $customer_id ) );
					$affiliate   = isset( $affiliates[0] ) ? $affiliates[0] : false;

					$show_additional_fields         = get_option( 'yith_wcaf_referral_show_fields_on_become_an_affiliate', 'no' );
					$show_name_field                = get_option( 'yith_wcaf_referral_registration_show_name_field', 'no' );
					$show_surname_field             = get_option( 'yith_wcaf_referral_registration_show_surname_field', 'no' );
					$show_website_field             = get_option( 'yith_wcaf_referral_registration_show_website_field' );
					$show_promotional_methods_field = get_option( 'yith_wcaf_referral_registration_show_promotional_methods_field' );
					$show_terms_field               = get_option( 'yith_wcaf_referral_registration_show_terms_field' );

					if ( ! $affiliate ) {
						$validation_error = new WP_Error();
						$validation_error = apply_filters( 'yith_wcaf_process_become_an_affiliate_errors', $validation_error, $customer_id );

						if ( $validation_error->get_error_code() ) {
							wc_add_notice( $validation_error->get_error_message(), 'error' );
						} else {
							$id = $this->add( array(
								'user_id' => $customer_id,
								'enabled' => $auto_enable,
								'token'   => $this->get_default_user_token( $customer_id )
							) );

							if ( 'yes' == $show_additional_fields ) {
								if ( 'yes' == $show_name_field ) {
									update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
								}
								if ( 'yes' == $show_surname_field ) {
									update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST['last_name'] ) );
								}
								if ( 'yes' == $show_website_field ) {
									update_user_meta( $user_id, '_yith_wcaf_website', esc_url( $_POST['website'] ) );
								}
								if ( 'yes' == $show_promotional_methods_field ) {
									update_user_meta( $user_id, '_yith_wcaf_promotional_method', in_array( $_POST['how_promote'], array_keys( yith_wcaf_get_promote_methods() ) ) ? $_POST['how_promote'] : '' );
									update_user_meta( $user_id, '_yith_wcaf_custom_method', sanitize_text_field( $_POST['custom_promote'] ) );
								}
								if ( 'yes' == $show_terms_field ) {
									update_user_meta( $user_id, '_yith_wcaf_terms', isset( $_POST['terms'] ) ? 'yes' : 'no' );
								}
							}

							if ( $id ) {
								// set up payment email address
								if ( 'yes' == $show_additional_fields ) {
									$payment_email = apply_filters( 'yith_wcaf_sanitized_payment_email', sanitize_email( $_POST['payment_email'] ), $_REQUEST['payment_email'] );
									YITH_WCAF_Affiliate_Handler()->update( $id, array( 'payment_email' => $payment_email ) );
								}

								wc_add_notice( __( apply_filters( 'yith_wcaf_process_become_an_affiliate_request_correctly', 'Your request has been processed correctly' ), 'yith-woocommerce-affiliates' ) );

								// trigger new affiliate action
								do_action( 'yith_wcaf_new_affiliate', $id );
							} else {
								wc_add_notice( __( 'An error occurred while trying to create the affiliate; try later.', 'yith-woocommerce-affiliates' ), 'error' );
							}
						}
					} else {
						wc_add_notice( __( 'You have already affiliated with us!', 'yith-woocommerce-affiliates' ), 'error' );
					}
				}

				wp_redirect( esc_url( apply_filters( 'yith_wcaf_become_an_affiliate_redirection', remove_query_arg( 'become_an_affiliate' ) ) ) );
				die();
			}
		}

		/**
		 * Print lower part of Affiliate registration form
		 *
		 * @return void
		 * @since 1.2.5
		 */
		public function print_bottom_fields() {
			parent::print_bottom_fields();

			$show_website_field             = get_option( 'yith_wcaf_referral_registration_show_website_field' );
			$show_promotional_methods_field = get_option( 'yith_wcaf_referral_registration_show_promotional_methods_field' );
			$show_terms_field               = get_option( 'yith_wcaf_referral_registration_show_terms_field' );
			$terms_label                    = get_option( 'yith_wcaf_referral_registration_terms_label' );
			$terms_anchor_url               = get_option( 'yith_wcaf_referral_registration_terms_anchor_url' );
			$terms_anchor_text              = get_option( 'yith_wcaf_referral_registration_terms_anchor_text' );

			if ( 'yes' == $show_website_field ):
				$label = apply_filters( 'yith_wcaf_website_label', __( 'Website', 'yith-woocommerce-affiliates' ) );
				$required                   = apply_filters( 'yith_wcaf_website_required', true );
				?>
				<p class="form-row form-row-wide">
					<label for="website"><?php echo $label ?><?php echo $required ? ' <span class="required">*</span>' : '' ?></label>
					<input type="<?php echo apply_filters( 'yith_wcaf_website_type', 'url' ); ?>" class="input-text" name="website" id="website" value="<?php if ( ! empty( $_POST['website'] ) ) {
						echo esc_attr( $_POST['website'] );
					} ?>"/>
				</p>
			<?php
			endif;

			if ( 'yes' == $show_promotional_methods_field ):
				$label = apply_filters( 'yith_wcaf_promotional_methods_label', __( 'How will you promote our site?', 'yith-woocommerce-affiliates' ) );
				$required                   = apply_filters( 'yith_wcaf_promotional_methods_required', true );
				?>
				<p class="form-row form-row-wide">
					<label for="how_promote"><?php echo $label; ?><?php echo $required ? ' <span class="required">*</span>' : '' ?></label>
					<select name="how_promote" id="how_promote">
						<?php
						$how_promote_options = yith_wcaf_get_promote_methods();

						if ( ! empty( $how_promote_options ) ):
							foreach ( $how_promote_options as $id => $value ):
								?>
								<option value="<?php echo esc_attr( $id ) ?>" <?php selected( isset( $_POST['how_promote'] ) && $_POST['how_promote'] == $id ) ?>><?php echo esc_html( $value ) ?></option>
							<?php
							endforeach;
						endif;
						?>
					</select>
				</p>

				<?php
				$label    = apply_filters( 'yith_wcaf_custom_promote_label', __( 'Specify how will you promote our site', 'yith-woocommerce-affiliates' ) );
				$required = apply_filters( 'yith_wcaf_custom_promote_required', true );
				?>

				<p class="form-row form-row-wide">
					<label for="custom_promote"><?php echo $label; ?><?php echo $required ? ' <span class="required">*</span>' : '' ?></label>
					<textarea name="custom_promote" id="custom_promote"><?php if ( ! empty( $_POST['custom_promote'] ) ) {
							echo esc_html( $_POST['custom_promote'] );
						} ?></textarea>
				</p>
			<?php
			endif;

			if ( 'yes' == $show_terms_field ):

				$terms_link = sprintf( '<a target="_blank" href="%s">%s</a>', $terms_anchor_url, $terms_anchor_text );
				$label                      = apply_filters( 'yith_wcaf_terms_label', str_replace( '%TERMS%', $terms_link, $terms_label ) );
				$required                   = apply_filters( 'yith_wcaf_terms_required', true );

				?>
				<p class="form-row form-row-wide">
					<label for="terms">
						<input type="checkbox" name="terms" id="terms" value="yes" <?php checked( isset( $_POST['terms'] ) ) ?> />
						<?php echo wp_kses_post( $label ) ?> <?php echo $required ? '<span class="required">*</span>' : '' ?>
					</label>
				</p>
			<?php
			endif;
		}

		/**
		 * Check affiliate additional data
		 *
		 * @param $validation_error \WP_Error Registration errors object
		 *
		 * @return \WP_Error
		 * @since 1.0.0
		 */
		public function check_affiliate( $validation_error ) {
			$enabled_form                       = get_option( 'yith_wcaf_referral_registration_form_options' );
			$show_website_field                 = get_option( 'yith_wcaf_referral_registration_show_website_field', 'no' );
			$show_promotional_methods_field     = get_option( 'yith_wcaf_referral_registration_show_promotional_methods_field', 'no' );
			$show_terms_field                   = get_option( 'yith_wcaf_referral_registration_show_terms_field', 'no' );
			$show_fields_on_become_an_affiliate = get_option( 'yith_wcaf_referral_show_fields_on_become_an_affiliate', 'no' );
			$val_error                          = array();
			$validation_error                   = parent::check_affiliate( $validation_error );

			if (
				( ! empty( $_POST['register_affiliate'] ) && wp_verify_nonce( $_POST['register_affiliate'], 'yith-wcaf-register-affiliate' ) ) ||
				( ! empty( $_POST['register'] ) && isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register' ) && $enabled_form == 'any' ) ||
				( isset( $_GET['become_an_affiliate'] ) && 'yes' == $show_fields_on_become_an_affiliate )
			) {
				if (
					$show_website_field == 'yes' && (
						( apply_filters( 'yith_wcaf_website_required', true ) && empty( $_POST['website'] ) ) ||
						( ! empty( $_POST['website'] ) && ! wc_is_valid_url( $_POST['website'] ) )
					)
				) {
					$val_error['invalid_website'] = __( 'Please, enter a valid website', 'yith-woocommerce-affiliates' );
				}

				if (
					$show_promotional_methods_field == 'yes' && (
						( apply_filters( 'yith_wcaf_how_promote_required', true ) && empty( $_POST['how_promote'] ) ) ||
						( ! empty( $_POST['how_promote'] ) && ! in_array( $_POST['how_promote'], array_keys( yith_wcaf_get_promote_methods() ) ) )
					)
				) {
					$val_error['invalid_promote_method'] = __( 'Please, specify how will you promote our site', 'yith-woocommerce-affiliates' );

				}

				if (
					isset( $_POST['how_promote'] ) && 'others' == $_POST['how_promote'] &&
					(
						( apply_filters( 'yith_wcaf_custom_promote_required', true ) && empty( $_POST['custom_promote'] ) ) ||
						( ! empty( $_POST['custom_promote'] ) && ! sanitize_textarea_field( $_POST['how_promote'] ) )
					)
				) {
					$val_error['invalid_custom_promote_method'] = __( 'Please, specify how will you promote our site', 'yith-woocommerce-affiliates' );

				}

				if ( $show_terms_field == 'yes' && ( apply_filters( 'yith_wcaf_terms_required', true ) && ! isset( $_POST['terms'] ) ) ) {
					$val_error['invalid_terms'] = __( 'Please, read and accept our Terms & Conditions', 'yith-woocommerce-affiliates' );

				}

				if ( ! empty( $val_error ) ) {
					$val_error = apply_filters( 'yith_wcaf_check_affiliate_val_error_premium', $val_error );
					foreach ( $val_error as $error_key => $error_message ) {
						$validation_error->add( $error_key, $error_message );
					}
				}
			}

			return apply_filters('yith_wcaf_check_affiliate_validation_error', $validation_error) ;
		}

		/**
		 * Register a user as an affiliate (register form action handling)
		 *
		 * @param $customer_id int Customer ID
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_affiliate( $customer_id ) {
			// retrieve options
			$enabled_form = get_option( 'yith_wcaf_referral_registration_form_options' );

			parent::register_affiliate( $customer_id );

			// retrieve post data
			$website            = ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : false;
			$promotional_method = ! empty( $_POST['how_promote'] ) ? $_POST['how_promote'] : false;
			$custom_method      = ! empty( $_POST['custom_promote'] ) ? sanitize_text_field( $_POST['custom_promote'] ) : false;
			$terms              = isset( $_POST['terms'] ) ? 'yes' : 'no';
			$auto_enable        = 'yes' == get_option( 'yith_wcaf_referral_registration_auto_enable' );

			if (
				( ! empty( $_POST['register_affiliate'] ) && isset( $_POST['register_affiliate'] ) && wp_verify_nonce( $_POST['register_affiliate'], 'yith-wcaf-register-affiliate' ) ) ||
				( ! empty( $_POST['register'] ) && isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register' ) && $enabled_form == 'any' )
			) {
				if ( $auto_enable && $affiliate = $this->get_affiliate_by_user_id( $customer_id ) ) {
					$this->update( $affiliate['ID'], array( 'enabled' => true ) );
				}

				update_user_meta( $customer_id, '_yith_wcaf_website', $website );
				update_user_meta( $customer_id, '_yith_wcaf_promotional_method', $promotional_method );
				update_user_meta( $customer_id, '_yith_wcaf_custom_method', $custom_method );
				update_user_meta( $customer_id, '_yith_wcaf_terms', $terms );
			}
		}

		/* === PANEL HANDLING METHODS === */

		/**
		 * Print affiliate panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_affiliate_panel() {
			// define variables to use in template
			$affiliate_id = isset( $_REQUEST['affiliate_id'] ) ? $_REQUEST['affiliate_id'] : false;

			if ( ! empty( $affiliate_id ) && $affiliate = $this->get_affiliate_by_id( intval( $affiliate_id ) ) ) {

				// save data, if user is submitting form
				if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					$this->save_affiliate_extra_fields( $affiliate['user_id'] );
				}

				// retrieve user
				$user_info = get_userdata( $affiliate['user_id'] );
				$user      = get_user_by( 'id', $affiliate['user_id'] );

				if ( $user ) {
					$username = ! empty( $user->first_name ) ? sprintf( '%s %s', $user->first_name, $user->last_name ) : $user->user_login;
				} else {
					$username = '';
				}

				// retrieve available action
				$available_affiliate_actions = array();

				$enabled = $affiliate['enabled'];
				$banned  = $affiliate['banned'];

				$redirect_to = urlencode( $_SERVER['REQUEST_URI'] );

				if ( ! $banned ) {
					// disable button
					if ( $enabled == 0 || $enabled == 1 ) {
						$available_affiliate_actions['disable'] = __( 'Change status to Rejected', 'yith-woocommerce-affiliates' );
					}

					// enable button
					if ( $enabled == 0 || $enabled == - 1 ) {
						$available_affiliate_actions['enable'] = __( 'Change status to Active', 'yith-woocommerce-affiliates' );
					}

					// ban button
					$available_affiliate_actions['ban'] = __( 'Ban affiliate', 'yith-woocommerce-affiliates' );
				} else {
					// unban button
					$available_affiliate_actions['unban'] = __( 'Unban affiliate', 'yith-woocommerce-affiliates' );
				}

				// last affiliate commissions
				$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
					'user_id'        => $affiliate['user_id'],
					'status__not_in' => 'trash',
					'order_by'       => 'created_at',
					'order'          => 'DESC',
					'limit'          => 5
				) );

				// last affiliate payments
				$payments = YITH_WCAF_Payment_Handler()->get_payments( array(
					'user_id'  => $affiliate['user_id'],
					'order_by' => 'created_at',
					'order'    => 'DESC',
					'limit'    => 5
				) );

				// affiliate associated users.
				$associated_users = get_users( array(
					'meta_key'   => '_yith_wcaf_persistent_token',
					'meta_value' => $affiliate['token'],
				) );

				// link generator
				$original_url  = isset( $_REQUEST['original_url'] ) ? esc_url( $_REQUEST['original_url'] ) : false;
				$generated_url = YITH_WCAF()->get_referral_url( $affiliate['token'], $original_url );

				// require rate panel template
				include( YITH_WCAF_DIR . 'templates/admin/affiliate-panel-detail.php' );
			} else {
				// prepare user affiliates table items
				$affiliates_table = new YITH_WCAF_Affiliates_Table_Premium();
				$affiliates_table->prepare_items();

				include( YITH_WCAF_DIR . 'templates/admin/affiliate-panel.php' );
			}
		}

		/**
		 * Pay all unpaid commissions for an affiliate, from Affiliate panel
		 *
		 * @return void
		 * @since 1.0.10
		 */
		public function handle_pay_commissions_panel_actions() {
			$affiliate_id = isset( $_REQUEST['affiliate_id'] ) ? $_REQUEST['affiliate_id'] : 0;
			$gateway      = isset( $_REQUEST['gateway'] ) && in_array( $_REQUEST['gateway'], array_keys( YITH_WCAF_Payment_Handler_Premium()->get_available_gateways() ) ) ? $_REQUEST['gateway'] : '';

			$res = YITH_WCAF_Payment_Handler_Premium()->pay_all_affiliate_commissions( $affiliate_id, ! empty( $gateways ), $gateway );

			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : esc_url_raw( add_query_arg( array(
				'page'             => 'yith_wcaf_panel',
				'tab'              => 'affiliates',
				'commissions_paid' => $res['status']
			), admin_url( 'admin.php' ) ) );

			wp_redirect( $redirect_to );
			die();
		}

		/**
		 * Process export, and generate csv file to download with commissions
		 *
		 * @return void
		 * @since 1.6.4
		 */
		public function export_csv() {
			$query_arg = array();

			if (
				! isset( $_REQUEST['page'] ) ||
				$_REQUEST['page'] != 'yith_wcaf_panel' ||
				! isset( $_REQUEST['tab'] ) ||
				$_REQUEST['tab'] != 'affiliates' ||
				! isset( $_REQUEST['export_action'] )
			) {
				return;
			}

			if ( ! empty( $_GET['status'] ) && ! in_array( $_GET['status'], array( 'all', 'banned' ) ) ) {
				$query_arg['enabled'] = $_GET['status'];
				$query_arg['banned']  = 'unbanned';
			} elseif ( ! empty( $_GET['status'] ) && $_GET['status'] == 'banned' ) {
				$query_arg['banned'] = $_GET['status'];
			}

			if ( ! empty( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) {
				$query_arg['s'] = $_REQUEST['s'];
			}


			$affiliates = $this->get_affiliates(
				array_merge(
					array(
						'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'ID',
						'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
					),
					$query_arg
				)
			);

			$headings = apply_filters( 'yith_wcaf_affiliates_csv_heading', array(
				'ID',
				'token',
				'user_id',
				'rate',
				'earnings',
				'refunds',
				'paid',
				'click',
				'conversion',
				'enabled',
				'banned',
				'payment_email',
				'total',
				'balance',
				'conversion_rate',
				'user_login',
				'user_email',
				'user_display_name',
				'user_nicename'
			), $affiliates );

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			$sitename .= ( ! empty( $sitename ) ) ? '-' : '';
			$filename = $sitename . 'affiliates-' . date( 'Y-m-d' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

			$df = fopen( 'php://output', 'w' );

			fputcsv( $df, $headings );

			foreach ( $affiliates as $row ) {
				fputcsv( $df, apply_filters( 'yith_wcaf_affiliates_csv_row', $row, $headings ) );
			}

			fclose( $df );

			die();
		}

		/* === EDIT PROFILE METHODS === */

		/**
		 * Render affiliate fields
		 *
		 * @param $user \WP_User User object
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function render_affiliate_extra_fields( $user ) {
			parent::render_affiliate_extra_fields( $user );

			$persistent_token   = '';
			$website            = '';
			$promotional_method = '';
			$custom_method      = '';
			$selected           = '';
			$affiliate          = false;

			if ( ! current_user_can( apply_filters( 'yith_wcaf_panel_capability', 'manage_woocommerce' ) ) ) {
				return;
			}

			if ( isset( $user->ID ) ) {
				$persistent_token   = get_user_meta( $user->ID, '_yith_wcaf_persistent_token', true );
				$website            = get_user_meta( $user->ID, '_yith_wcaf_website', true );
				$promotional_method = get_user_meta( $user->ID, '_yith_wcaf_promotional_method', true );
				$custom_method      = get_user_meta( $user->ID, '_yith_wcaf_custom_method', true );
				$terms              = get_user_meta( $user->ID, '_yith_wcaf_terms', true );
			}

			if ( ! empty( $persistent_token ) ) {
				$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $persistent_token );

				if ( $affiliate ) {
					$user = get_userdata( $affiliate['user_id'] );
					if ( ! is_wp_error( $user ) ) {
						$username = '';

						if ( $user->first_name || $user->last_name ) {
							$username .= esc_html( ucfirst( $user->first_name ) . ' ' . ucfirst( $user->last_name ) );
						} else {
							$username .= esc_html( ucfirst( $user->display_name ) );
						}

						$selected = $username . ' (#' . $user->ID . ' &ndash; ' . sanitize_email( $user->user_email ) . ')';
					}
				}
			}

			?>
			<hr/>
			<h3><?php _e( 'Affiliate additional information', 'yith-woocommerce-affiliates' ) ?></h3>
			<table class="form-table">
				<tr>
					<th><label for="website"><?php _e( 'Website', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<input type="url" id="website" name="website" value="<?php echo $website ?>" class="regular-text"/>
						<span class="description"><?php _e( 'Affiliate website, to double-check affiliate activity', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="promotional_method"><?php _e( 'Promotion method', 'yith-woocommerce-affiliates' ) ?></label>
					</th>
					<td>
						<select name="promotional_method" id="promotional_method" class="regular-text">
							<?php
							$how_promote_options = yith_wcaf_get_promote_methods();

							if ( ! empty( $how_promote_options ) ):
								foreach ( $how_promote_options as $id => $value ):
									?>
									<option value="<?php echo esc_attr( $id ) ?>" <?php selected( $promotional_method == $id ) ?>><?php echo esc_html( $value ) ?></option>
								<?php
								endforeach;
							endif;
							?>
						</select>
						<span class="description"><?php _e( 'Method affiliate will use to promote this site', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="custom_method"><?php _e( 'Custom promotion method', 'yith-woocommerce-affiliates' ) ?></label>
					</th>
					<td>
						<textarea name="custom_method" id="custom_method" cols="50" rows="3"><?php echo $custom_method ?></textarea>
						<p class="description"><?php _e( 'When selects "other promotion methods", affiliate can specify which kind of promotion will he perform', 'yith-woocommerce-affiliates' ) ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="terms"><?php _e( 'Terms', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<input type="checkbox" name="terms" id="terms" value="yes" <?php checked( isset( $terms ) && 'yes' == $terms ) ?> />
						<span class="description"><?php _e( 'Whether affiliate accepted Terms and Conditions', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
			</table>
			<hr/>
			<h3><?php _e( 'Associated Affiliate', 'yith-woocommerce-affiliates' ) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label for="persistent_token"><?php _e( 'Associated Affiliate', 'yith-woocommerce-affiliates' ) ?></label>
					</th>
					<td>
						<?php
						yit_add_select2_fields( array(
							'name'             => 'persistent_token',
							'id'               => 'persistent_token',
							'class'            => 'wc-product-search',
							'data-action'      => 'json_search_affiliates',
							'data-placeholder' => __( 'Select an affiliate', 'yith-woocommerce-affiliates' ),
							'data-selected'    => $affiliate ? array( $affiliate['ID'] => $selected ) : array(),
							'data-allow_clear' => true,
							'value'            => $affiliate ? esc_attr( $affiliate['ID'] ) : '',
							'style'            => "min-width: 25em; vertical-align: middle; display: inline-block!important; margin-right: 2px;"
						) );
						?>
						<span class="description"><?php _e( 'Affiliate that will receive permanent commission from this customer\'s purchases', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
			</table>
			<?php
		}

		/**
		 * Save affiliate fields
		 *
		 * @param $user_id int User id
		 *
		 * @return bool Whether method actually saved option or not
		 * @since  1.0.0
		 */
		public function save_affiliate_extra_fields( $user_id ) {
			if ( ! current_user_can( apply_filters( 'yith_wcaf_panel_capability', 'manage_woocommerce' ) ) ) {
				return;
			}

			parent::save_affiliate_extra_fields( $user_id );

			$persistent_affiliate_user_id = isset( $_POST['persistent_token'] ) ? trim( $_POST['persistent_token'] ) : false;
			$website                      = isset( $_POST['website'] ) ? $_POST['website'] : '';
			$promotional_method           = isset( $_POST['promotional_method'] ) ? $_POST['promotional_method'] : '';
			$custom_method                = isset( $_POST['custom_method'] ) ? $_POST['custom_method'] : '';
			$terms                        = isset( $_POST['terms'] ) ? 'yes' : 'no';

			// updates custom fields
			update_user_meta( $user_id, '_yith_wcaf_website', $website );
			update_user_meta( $user_id, '_yith_wcaf_promotional_method', $promotional_method );
			update_user_meta( $user_id, '_yith_wcaf_custom_method', $custom_method );
			update_user_meta( $user_id, '_yith_wcaf_terms', $terms );

			// updates persistent token
			if ( $persistent_affiliate_user_id ) {
				$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $persistent_affiliate_user_id );

				if ( $affiliate ) {
					/**
					 * Filter yith_wcaf_updated_persisten_token
					 *
					 * @param $user_id  int Current user id
					 * @param $referral string Current referral token
					 * @param $order_id int Current order id (if any; null otherwise)
					 *
					 * @since 1.1.1
					 */
					do_action( 'yith_wcaf_updated_persisten_token', $user_id, $affiliate['token'], null );

					update_user_meta( $user_id, '_yith_wcaf_persistent_token', $affiliate['token'] );
				}
			} else {
				/**
				 * Filter yith_wcaf_updated_persisten_token
				 *
				 * @param $user_id int Current user id
				 *
				 * @since 1.1.1
				 */
				do_action( 'yith_wcaf_deleted_persisten_token', $user_id );

				delete_user_meta( $user_id, '_yith_wcaf_persistent_token' );
			}
		}

		/**
		 * Saves invoice information inside user account, for future use
		 *
		 * @param $user_id int User id
		 * @param $posted  array Array of invoice fields
		 *
		 * @return void
		 */
		public function save_affiliate_invoice_profile( $user_id, $posted ) {
			$available_fields = YITH_WCAF_Payment_Handler_Premium()->get_available_invoice_fields();

			if ( ! empty( $available_fields ) ) {
				foreach ( array_keys( $available_fields ) as $field_id ) {
					$field_value = isset( $posted[ $field_id ] ) ? $posted[ $field_id ] : '';
					update_user_meta( $user_id, "_yith_wcaf_invoice_{$field_id}", $field_value );
				}
			}
		}

		/**
		 * Retrieve invoice profile for the user
		 *
		 * @param $user_id int User id
		 *
		 * @return mixed Array of stored information about affiliate invoice profile
		 */
		public function get_affiliate_invoice_profile( $user_id ) {
			$available_fields = YITH_WCAF_Payment_Handler_Premium()->get_available_invoice_fields();
			$invoice_profile  = array();

			if ( ! empty( $available_fields ) ) {
				foreach ( array_keys( $available_fields ) as $field_id ) {
					$field_value                  = get_user_meta( $user_id, "_yith_wcaf_invoice_{$field_id}", true );
					$invoice_profile[ $field_id ] = $field_value ? $field_value : null;
				}
			}

			return $invoice_profile;
		}

		/**
		 * Retrieve formatted invoice profile for the user
		 *
		 * @param $user_id int User id
		 *
		 * @return mixed Array of stored information about affiliate invoice profile
		 */
		public function get_formatted_affiliate_invoice_profile( $user_id ) {
			$available_fields = YITH_WCAF_Payment_Handler_Premium()->get_available_invoice_fields();
			$invoice_profile  = $this->get_affiliate_invoice_profile( $user_id );

			$formatted_address = apply_filters( 'yith_wcaf_formatted_affiliate_invoice_profile_format', "{{number}}
		    {{first_name}} {{last_name}}
		    {{company}}
		    {{billing_address_1}}, {{billing_city}} {{billing_postcode}}
		    {{billing_state}} {{billing_country}}
		    {{cif}}
		    {{vat}}
		    " );

			if ( ! empty( $available_fields ) ) {
				foreach ( $available_fields as $field => $label ) {
					$value = isset( $invoice_profile[ $field ] ) ? $invoice_profile[ $field ] : '';

					$formatted_address = str_replace( "{{{$field}}}", $value, $formatted_address );
				}
			}

			// remove empty placeholders
			$formatted_address = preg_replace( '/\{\{[^}]+\}\}/', '', $formatted_address );

			return nl2br( $formatted_address );
		}

		/* === AFFILIATE DASHBOARD METHODS === */

		/**
		 * Print ban message is affiliate is banned and message not empty
		 *
		 * @return void
		 * @since 1.2.5
		 */
		public function print_ban_message() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$user_id = get_current_user_id();

			if ( $this->is_user_affiliate( $user_id ) && $this->is_user_banned_affiliate( $user_id ) ) {
				$ban_message = get_user_meta( $user_id, '_yith_wcaf_ban_message', true );
				$ban_message = $ban_message ? $ban_message : get_option( 'yith_wcaf_ban_global_message', '' );

				if ( $ban_message ) {
					wc_print_notice( nl2br( $ban_message ), 'error' );
				}
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Affiliate_Handler_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Affiliate_Handler class
 *
 * @return \YITH_WCAF_Affiliate_Handler_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Affiliate_Handler_Premium() {
	return YITH_WCAF_Affiliate_Handler_Premium::get_instance();
}
