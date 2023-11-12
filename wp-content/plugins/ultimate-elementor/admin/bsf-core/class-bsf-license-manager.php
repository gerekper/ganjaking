<?php
/**
 * BSF License manager class file.
 *
 * @package bsf-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BSF_License_Manager' ) ) {

	/**
	 * Class BSF_License_Manager
	 */
	class BSF_License_Manager {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance = null;

		/**
		 * Inline form products.
		 *
		 * @access private
		 * @var $inline_form_products Inline form products.
		 */
		private static $inline_form_products = array();

		/**
		 * Instance.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Initiator
		 */
		public function __construct() {
			add_action( 'admin_head', array( $this, 'admin_css' ), 100 );

			add_action( 'admin_init', array( $this, 'bsf_activate_license' ) );
			add_action( 'admin_init', array( $this, 'bsf_deactivate_license' ) );
			add_action( 'bsf_product_update_registered', array( $this, 'refresh_products_on_license_activae' ) );
			add_action( 'admin_footer', array( $this, 'render_popup_form_markup' ) );

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		}
		/**
		 *  Refresh products on License activation.
		 */
		public function refresh_products_on_license_activae() {
			update_site_option( 'bsf_force_check_extensions', true );
		}
		/**
		 *  Includes.
		 */
		public function includes() {

			require_once BSF_UPDATER_PATH . '/class-bsf-envato-activate.php';
		}
		/**
		 *  Admin CSS.
		 */
		public function admin_css() {
			?>

			<style type="text/css">
				.bsf-pre {
					white-space: normal;
				}

				/* license consent */
				.bsf-license-consent-container {
					display: flex;
				}

				.bsf-license-consent-container label {
					padding-top: 0;
				}

				.wp-admin p.bsf-license-consent-container input {
					margin-top: 2px;
					margin-right: 10px;
				}
			</style>

			<?php
		}
		/**
		 *  BSF Deactivate license.
		 */
		public function bsf_deactivate_license() {

			if ( ! isset( $_POST['bsf_deactivate_license'] ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf_graupi_nonce'] ) || ( isset( $_POST['bsf_graupi_nonce'] ) && ! wp_verify_nonce( $_POST['bsf_graupi_nonce'], 'bsf_license_activation_deactivation_nonce' ) ) ) {

				return;
			}

			if ( ! isset( $_POST['bsf_license_manager']['license_key'] ) || empty( $_POST['bsf_license_manager']['license_key'] ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf_license_manager']['product_id'] ) || empty( $_POST['bsf_license_manager']['product_id'] ) ) {
				return;
			}

			$product_id  = esc_attr( $_POST['bsf_license_manager']['product_id'] );
			$license_key = $this->bsf_get_product_info( $product_id, 'purchase_key' );

			// Check if the key is from EDD.
			$is_edd = $this->is_edd( $license_key );

			$path = bsf_get_api_url() . '?referer=deactivate-' . $product_id;

			// Using Brainstorm API v2.
			$data = array(
				'action'       => 'bsf_deactivate_license',
				'purchase_key' => $license_key,
				'product_id'   => $product_id,
				'site_url'     => get_site_url(),
				'is_edd'       => $is_edd,
				'referer'      => 'customer',
			);

			$data     = apply_filters( 'bsf_deactivate_license_args', $data );
			$response = wp_remote_post(
				$path,
				array(
					'body'    => $data,
					'timeout' => '15',
				)
			);

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$result = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $result['success'] ) && ( true === $result['success'] || 'true' === $result['success'] ) ) {
					// update license saus to the product.
					$_POST['bsf_license_deactivation']['success'] = $result['success'];
					$_POST['bsf_license_deactivation']['message'] = $result['message'];
					unset( $result['success'] );
					unset( $result['message'] );

					$this->bsf_update_product_info( $product_id, $result );

					do_action( 'bsf_deactivate_license_' . $product_id . '_after_success', $result, $response, $_POST );

				} else {
					$_POST['bsf_license_deactivation']['success'] = $result['success'];
					$_POST['bsf_license_deactivation']['message'] = $result['message'];
				}
			} else {
				// If there is an error, the status will not be changed. hence it's true.
				$_POST['bsf_license_activation']['success'] = true;
				$_POST['bsf_license_activation']['message'] = 'There was an error when connecting to our license API - <pre class="bsf-pre">' . $response->get_error_message() . '</pre>';
			}

			// Delete license key status transient.
			delete_transient( $product_id . '_license_status' );
		}

		/**
		 *  BSF Activate license.
		 */
		public function bsf_activate_license() {

			if ( ! isset( $_POST['bsf_activate_license'] ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf_graupi_nonce'] ) || ( isset( $_POST['bsf_graupi_nonce'] ) && ! wp_verify_nonce( $_POST['bsf_graupi_nonce'], 'bsf_license_activation_deactivation_nonce' ) ) ) {

				return;
			}

			if ( ! isset( $_POST['bsf_license_manager']['license_key'] ) || empty( $_POST['bsf_license_manager']['license_key'] ) ) {
				return;
			}

			if ( ! isset( $_POST['bsf_license_manager']['product_id'] ) || empty( $_POST['bsf_license_manager']['product_id'] ) ) {
				return;
			}

			$post_data = $_POST['bsf_license_manager'];

			$_POST['bsf_license_activation'] = $this->bsf_process_license_activation( $post_data );

		}

		/**
		 *  BSF Activate license processing.
		 *
		 * @param Array $post_data Post data.
		 */
		public function bsf_process_license_activation( $post_data ) {

			$license_key              = esc_attr( $post_data['license_key'] );
			$product_id               = esc_attr( $post_data['product_id'] );
			$user_name                = isset( $post_data['user_name'] ) ? esc_attr( $post_data['user_name'] ) : '';
			$user_email               = isset( $post_data['user_email'] ) ? esc_attr( $post_data['user_email'] ) : '';
			$privacy_consent          = ( isset( $post_data['privacy_consent'] ) && 'true' === $post_data['privacy_consent'] ) ? true : false;
			$terms_conditions_consent = ( isset( $post_data['terms_conditions_consent'] ) && 'true' === $post_data['terms_conditions_consent'] ) ? true : false;

			// Check if the key is from EDD.
			$is_edd = $this->is_edd( $license_key );

			// Server side check if the license key is valid.
			$path = bsf_get_api_url() . '?referer=activate-' . $product_id;

			// Using Brainstorm API v2.
			$data = array(
				'action'                   => 'bsf_activate_license',
				'purchase_key'             => $license_key,
				'product_id'               => $product_id,
				'user_name'                => $user_name,
				'user_email'               => $user_email,
				'privacy_consent'          => $privacy_consent,
				'terms_conditions_consent' => $terms_conditions_consent,
				'site_url'                 => get_site_url(),
				'is_edd'                   => $is_edd,
				'referer'                  => 'customer',
			);

			$data     = apply_filters( 'bsf_activate_license_args', $data );
			$response = wp_remote_post(
				$path,
				array(
					'body'    => $data,
					'timeout' => '15',
				)
			);

			$res = array();

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$result = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $result['success'] ) && ( true === $result['success'] || 'true' === $result['success'] ) ) {
					// update license saus to the product.
					$res['success'] = $result['success'];
					$res['message'] = $result['message'];
					unset( $result['success'] );

					// Update product key.
					$result['purchase_key'] = $license_key;

					$this->bsf_update_product_info( $product_id, $result );

					do_action( 'bsf_activate_license_' . $product_id . '_after_success', $result, $response, $post_data );

				} else {
					$res['success'] = $result['success'];
					$res['message'] = $result['message'];
				}
			} else {
				$res['success'] = false;
				$res['message'] = 'There was an error when connecting to our license API - <pre class="bsf-pre">' . $response->get_error_message() . '</pre>';
			}

			// Delete license key status transient.
			delete_transient( $product_id . '_license_status' );

			return $res;
		}

		/**
		 *  Is EDD.
		 *
		 * @param string $license_key License key.
		 */
		public function is_edd( $license_key ) {

			// Purchase key length for EDD is 32 characters.
			if ( strlen( $license_key ) === 32 ) {

				return true;
			}

			return false;
		}

		/**
		 *  BSF Update product Info.
		 *
		 * @param int   $product_id Product ID.
		 * @param array $args Arguments.
		 */
		public function bsf_update_product_info( $product_id, $args ) {
			$brainstrom_products = get_option( 'brainstrom_products', array() );

			foreach ( $brainstrom_products as $type => $products ) {

				foreach ( $products as $id => $product ) {

					if ( $id == $product_id ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
						foreach ( $args as $key => $value ) {
							$brainstrom_products[ $type ][ $id ][ $key ] = $value;
							do_action( "bsf_product_update_{$value}", $product_id, $value );
						}
					}
				}
			}

			update_option( 'brainstrom_products', $brainstrom_products );
		}

		/**
		 *  BSF is active license.
		 *
		 * @param int $product_id Product ID.
		 */
		public static function bsf_is_active_license( $product_id ) {

			$brainstrom_products = get_option( 'brainstrom_products', array() );
			$brainstorm_plugins  = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
			$brainstorm_themes   = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();

			$all_products = $brainstorm_plugins + $brainstorm_themes;

			// If a product is marked as free, it is considered as active.
			$is_free = self::is_product_free( $product_id );
			if ( 'true' === $is_free || true === $is_free ) {
				return true;
			}

			$is_bundled = BSF_Update_Manager::bsf_is_product_bundled( $product_id );

			// The product is not bundled.
			if ( isset( $all_products[ $product_id ] ) ) {

				if ( isset( $all_products[ $product_id ]['status'] ) && 'registered' === $all_products[ $product_id ]['status'] ) {

					// If the purchase key is empty, Return false.
					if ( ! isset( $all_products[ $product_id ]['purchase_key'] ) ) {
						return false;
					}

					// Check if license is active on API.
					if ( false === self::instance()->get_remote_license_status( $all_products[ $product_id ]['purchase_key'], $product_id ) ) {
						return false;
					}

					return true;
				}
			}

			// The product is bundled.
			if ( ! empty( $is_bundled ) ) {

				// If the bundled product does not require to activate the license then treat the license is active.
				$product = get_brainstorm_product( $product_id );

				if ( isset( $product['licence_require'] ) && 'false' === $product['licence_require'] ) {
					return true;
				}

				foreach ( $is_bundled as $key => $value ) {

					$product_id = $value;

					if ( isset( $all_products[ $product_id ] ) ) {
						if ( isset( $all_products[ $product_id ]['status'] ) && 'registered' === $all_products[ $product_id ]['status'] ) {
							// If the purchase key is empty, Return false.
							if ( ! isset( $all_products[ $product_id ]['purchase_key'] ) ) {
								return false;
							}

							// Check if license is active on API.
							if ( false === self::instance()->get_remote_license_status( $all_products[ $product_id ]['purchase_key'], $product_id ) ) {
								return false;
							}

							return true;
						}
					}
				}
			}

			// By default Return false.
			return false;
		}
		/**
		 *  Get remote license status.
		 *
		 * @param string $purchase_key Purchase Key.
		 * @param int    $product_id Product ID.
		 */
		public function get_remote_license_status( $purchase_key, $product_id ) {

			$transient_key = $product_id . '_license_status';

			// Check if license status is cached.
			if ( false !== get_transient( $transient_key ) ) {
				return (bool) get_transient( $transient_key );
			}

			// Set default license to license status stored in the database.
			$license_status = $this->bsf_get_product_info( $product_id, 'status' );
			if ( 'registered' === $license_status ) {
				$license_status = '1';
			} else {
				$license_status = '0';
			}

			$path = bsf_get_api_url() . '?referer=license-status-' . $product_id;

			// Using Brainstorm API v2.
			$data = array(
				'action'       => 'bsf_license_status',
				'purchase_key' => $purchase_key,
				'site_url'     => get_site_url(),
			);

			$data     = apply_filters( 'bsf_license_status_args', $data );
			$response = wp_remote_post(
				$path,
				array(
					'body'    => $data,
					'timeout' => '10',
				)
			);

			// Try to make a second request to unsecure URL.
			if ( is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) !== 200 ) {
				$path     = bsf_get_api_url( true ) . '?referer=license-status-' . $product_id;
				$response = wp_remote_post(
					$path,
					array(
						'body'    => $data,
						'timeout' => '8',
					)
				);
			}

			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

				// Check if status received from API is true.
				if ( isset( $response_body['status'] ) && true === $response_body['status'] ) {
					$license_status = '1';
				} else {
					$license_status = '0';
				}
			}

			// Save license status in transient which will expire in 6 hours.
			set_transient( $transient_key, $license_status, 6 * HOUR_IN_SECONDS );

			return (bool) $license_status;
		}
		/**
		 *  Is product free.
		 *
		 * @param int $product_id Product ID.
		 */
		public static function is_product_free( $product_id ) {
			$license_manager = self::instance();
			$is_free         = $license_manager->bsf_get_product_info( $product_id, 'is_product_free' );

			return $is_free;
		}
		/**
		 *  Get product info.
		 *
		 * @param int    $product_id Product ID.
		 * @param string $key Key.
		 */
		public function bsf_get_product_info( $product_id, $key ) {

			$brainstrom_products = get_option( 'brainstrom_products', array() );
			$brainstorm_plugins  = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
			$brainstorm_themes   = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();

			$all_products = $brainstorm_plugins + $brainstorm_themes;

			if ( isset( $all_products[ $product_id ][ $key ] ) && ! empty( $all_products[ $product_id ][ $key ] ) ) {
				return $all_products[ $product_id ][ $key ];
			}
		}

		/**
		 * For Popup License form check `popup_license_form` is `true`.
		 *
		 * @param array $args Arguments.
		 */
		public function license_activation_form( $args ) {
			$html = '';

			$product_id = ( isset( $args['product_id'] ) && ! is_null( $args['product_id'] ) ) ? $args['product_id'] : '';

			// bail out if product id is missing.
			if ( empty( $product_id ) ) {
				esc_html_e( 'Product id is missing.', 'bsf' );

				return;
			}

			$popup_license_form           = ( isset( $args['popup_license_form'] ) ) ? $args['popup_license_form'] : false;
			$form_action                  = ( isset( $args['form_action'] ) && ! is_null( $args['form_action'] ) ) ? $args['form_action'] : '';
			$form_class                   = ( isset( $args['form_class'] ) && ! is_null( $args['form_class'] ) ) ? $args['form_class'] : "bsf-license-form-{$product_id}";
			$submit_button_class          = ( isset( $args['submit_button_class'] ) && ! is_null( $args['submit_button_class'] ) ) ? $args['submit_button_class'] : '';
			$license_form_heading_class   = ( isset( $args['bsf_license_form_heading_class'] ) && ! is_null( $args['bsf_license_form_heading_class'] ) ) ? $args['bsf_license_form_heading_class'] : '';
			$license_active_class         = ( isset( $args['bsf_license_active_class'] ) && ! is_null( $args['bsf_license_active_class'] ) ) ? $args['bsf_license_active_class'] : '';
			$license_not_activate_message = ( isset( $args['bsf_license_not_activate_message'] ) && ! is_null( $args['bsf_license_not_activate_message'] ) ) ? $args['bsf_license_not_activate_message'] : '';

			$size                    = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
			$button_text_activate    = ( isset( $args['button_text_activate'] ) && ! is_null( $args['button_text_activate'] ) ) ? $args['button_text_activate'] : __( 'Activate License', 'bsf' );
			$button_text_deactivate  = ( isset( $args['button_text_deactivate'] ) && ! is_null( $args['button_text_deactivate'] ) ) ? $args['button_text_deactivate'] : __( 'Deactivate License', 'bsf' );
			$placeholder             = ( isset( $args['placeholder'] ) && ! is_null( $args['placeholder'] ) ) ? $args['placeholder'] : 'Enter your license key..';
			$placeholder_name        = ( isset( $args['placeholder_name'] ) && ! is_null( $args['placeholder_name'] ) ) ? $args['placeholder_name'] : 'Your Name..';
			$placeholder_email       = ( isset( $args['placeholder_email'] ) && ! is_null( $args['placeholder_email'] ) ) ? $args['placeholder_email'] : 'Your Email..';
			$bsf_license_allow_email = ( isset( $args['bsf_license_allow_email'] ) && ! is_null( $args['bsf_license_allow_email'] ) ) ? $args['bsf_license_allow_email'] : true;
			$license_form_title      = ( isset( $args['license_form_title'] ) && ! is_null( $args['license_form_title'] ) ) ? $args['license_form_title'] : 'Updates & Support Registration - ';

			$is_active   = self::bsf_is_active_license( $product_id );
			$license_key = $this->bsf_get_product_info( $product_id, 'purchase_key' );

			if ( true === $bsf_license_allow_email || 'true' === $bsf_license_allow_email ) {
				$form_class .= ' license-form-allow-email ';

				if ( ! $is_active ) {
					$button_text_activate = 'Sign Up & Activate';
					$submit_button_class .= ' button-primary button-hero ';
				}
			}

			// Forcefully disable the subscribe options for uabb.
			// This should be disabled from uabb and removed from graupi.
			if ( 'uabb' === $product_id ) {
				$bsf_license_allow_email = false;
			}

			$purchase_url = $this->bsf_get_product_info( $product_id, 'purchase_url' );
			$product_name = apply_filters( "agency_updater_productname_{$product_id}", $this->bsf_get_product_info( $product_id, 'name' ) );
			if ( empty( $product_name ) ) {
				$product_name = apply_filters( "agency_updater_productname_{$product_id}", $this->bsf_get_product_info( $product_id, 'product_name' ) );
			}

			// License activation messages.
			$current_status  = '';
			$current_message = '';

			if ( isset( $_POST['bsf_license_activation']['success'] ) && isset( $_POST['bsf_license_manager']['product_id'] ) && $product_id === $_POST['bsf_license_manager']['product_id'] ) { // phpcs:ignore:WordPress.Security.NonceVerification.Missing
				$current_status = esc_attr( $_POST['bsf_license_activation']['success'] );// phpcs:ignore:WordPress.Security.NonceVerification.Missing
				if ( true === $current_status || 'true' === $current_status || '1' === $current_status ) {
					$current_status = 'bsf-current-license-success bsf-current-license-success-' . $product_id;
					$is_active      = true;
				} else {
					$current_status = 'bsf-current-license-error bsf-current-license-error-' . $product_id;
					$is_active      = false;
				}
			}

			if ( isset( $_POST['bsf_license_activation']['message'] ) ) { // phpcs:ignore:WordPress.Security.NonceVerification.Missing
				$current_message = wp_kses_post( $_POST['bsf_license_activation']['message'] );// phpcs:ignore:WordPress.Security.NonceVerification.Missing
			}

			$license_status       = 'Active!';
			$license_status_class = 'bsf-license-active-' . $product_id;

			$html .= '<div class="bsf-license-key-registration">';

			// License not active message.
			$form_heading_status = '';
			if ( false === $is_active || 'false' === $is_active ) {
				$license_status       = 'Not Active!';
				$license_status_class = 'bsf-license-not-active-' . $product_id;
				$not_activate         = '';
				$html                .= apply_filters( "bsf_license_not_activate_message_{$product_id}", $not_activate, $license_status_class, $license_not_activate_message );

				if ( true === $bsf_license_allow_email || 'true' === $bsf_license_allow_email ) {
					$popup_license_subtitle = apply_filters( "bsf_license_key_form_inactive_subtitle_{$product_id}", sprintf( '<p>%s</p>', __( 'Click on the button below to activate your license and subscribe to our newsletter.', 'bsf' ) ) );
				} else {
					$popup_license_subtitle = apply_filters( "bsf_license_key_form_inactive_subtitle_{$product_id}", sprintf( '<p>%s</p>', __( 'Enter your purchase key and activate automatic updates.', 'bsf' ) ) );
				}
			} else {
				$form_class            .= " form-submited-{$product_id}";
				$popup_license_subtitle = apply_filters( "bsf_license_key_form_active_subtitle_{$product_id}", '' );
			}

			do_action( "bsf_before_license_activation_form_{$product_id}" );

			$html .= '<form method="post" class="' . $form_class . '" action="' . $form_action . '">';

			$html .= wp_nonce_field( 'bsf_license_activation_deactivation_nonce', 'bsf_graupi_nonce', true, false );

			if ( $popup_license_form ) {
				$form_heading  = '<h3 class="' . $license_status_class . ' ' . $license_form_heading_class . '">' . $product_name . '</h3>';
				$form_heading .= $popup_license_subtitle;
			} else {
				$form_heading = '<h3 class="' . $license_status_class . ' ' . $license_form_heading_class . '">' . $license_form_title . '<span>' . $license_status . '</span></h3>';
			}

			$html .= apply_filters( "bsf_license_form_heading_{$product_id}", $form_heading, $license_status_class, $license_status );

			if ( ! empty( $current_status ) && ! empty( $current_message ) ) {
				$current_message = '<span class="' . $current_status . '">' . $current_message . '</span>';
				$html           .= apply_filters( "bsf_license_current_message_{$product_id}", $current_message );
			}

			if ( true === $is_active || 'true' === $is_active ) {

				$licnse_active_message = __( 'Your license is active.', 'bsf' );
				$licnse_active_message = apply_filters( 'bsf_license_active_message', $licnse_active_message );

				$html .= '<span class="license-form-field">';
				$html .= '<input type="text" readonly class="' . $license_active_class . ' ' . $size . '-text" id="bsf_license_manager[license_key]" name="bsf_license_manager[license_key]" value="' . esc_attr( $licnse_active_message ) . '"/>';
				$html .= '</span>';
				$html .= '<input type="hidden" class="' . $size . '-text" id="bsf_license_manager[product_id]" name="bsf_license_manager[product_id]" value="' . esc_attr( stripslashes( $product_id ) ) . '"/>';

				do_action( "bsf_before_license_activation_submit_button_{$product_id}" );

				$html .= '<input type="submit" class="button ' . $submit_button_class . '" name="bsf_deactivate_license" value="' . esc_attr( $button_text_deactivate ) . '"/>';
			} else {

				if ( true === $bsf_license_allow_email || 'true' === $bsf_license_allow_email ) {

					$html .= '<span class="license-form-field">';
					$html .= '<h4>Your Name</h4>';
					$html .= '<input type="text" class="' . $size . '-text" id="bsf_license_manager[user_name]" name="bsf_license_manager[user_name]" value=""/>';
					$html .= '</span>';

					$html .= '<span class="license-form-field">';
					$html .= '<h4>Your Email Address</h4>';
					$html .= '<input type="email" class="' . $size . '-text" id="bsf_license_manager[user_email]" name="bsf_license_manager[user_email]" value=""/>';
					$html .= '</span>';

					$html .= '<span class="license-form-field">';
					$html .= '<h4>Your License Key</h4>';
					$html .= '<input type="text" class="' . $size . '-text" id="bsf_license_manager[license_key]" name="bsf_license_manager[license_key]" value="" autocomplete="off" required/>';
					$html .= '</span>';

					$html .= '<span class="license-form-field">';
					$html .= '</span>';

				} else {
					$html .= '<span class="license-form-field">';
					$html .= '<input type="text" placeholder="' . esc_attr( $placeholder ) . '" class="' . $size . '-text" id="bsf_license_manager[license_key]" name="bsf_license_manager[license_key]" value="" autocomplete="off"/>';
					$html .= '</span>';
				}

				$html .= '<input type="hidden" class="' . $size . '-text" id="bsf_license_manager[product_id]" name="bsf_license_manager[product_id]" value="' . esc_attr( stripslashes( $product_id ) ) . '"/>';

				do_action( "bsf_before_license_activation_submit_button_{$product_id}" );

				$html .= '<input id="bsf-license-privacy-consent" name="bsf_license_manager[privacy_consent]" type="hidden" value="true" />';
				$html .= '<input id="bsf-license-terms-conditions-consent" name="bsf_license_manager[terms_conditions_consent]" type="hidden" value="true" />';

				$html .= '<div class="submit-button-wrap">';
				$html .= '<input type="submit" class="button ' . $submit_button_class . '" name="bsf_activate_license" value="' . esc_attr( $button_text_activate ) . '"/>';

				if ( true === $bsf_license_allow_email || 'true' === $bsf_license_allow_email ) {
					$get_license_message = "<p class='purchase-license'><a target='_blank' href='$purchase_url'>Purchase License »</a></p>";
				} else {
					$get_license_message = "<p>If you don't have a license, you can <a target='_blank' href='$purchase_url'>get it here »</a></p>";
				}

				$html .= apply_filters( "bsf_get_license_message_{$product_id}", $get_license_message, $purchase_url );
				$html .= '</div>';
			}

			$html .= '</form>';

			do_action( "bsf_after_license_activation_form_{$product_id}" );

			$html = apply_filters( 'bsf_inlne_license_envato_after_form', $html, $product_id );

			$html .= '</div> <!-- envato-license-registration -->';

			if ( isset( $_GET['debug'] ) ) { // phpcs:ignore:WordPress.Security.NonceVerification.Recommended
				$html .= get_bsf_systeminfo();
			}

			// Output the license activation/deactivation form.
			return apply_filters( "bsf_core_license_activation_form_{$product_id}", $html, $args );
		}


		/**
		 * Load Scripts
		 *
		 * @since 1.0.0
		 *
		 * @param  string $hook Current Hook.
		 * @return void
		 */
		public function load_scripts( $hook = '' ) {

			if ( 'plugins.php' === $hook ) {
				wp_register_script( 'bsf-core-jquery-history', bsf_core_url( '/assets/js/jquery-history.js' ), array( 'jquery' ), BSF_UPDATER_VERSION, true );
				wp_enqueue_style( 'bsf-core-license-form', bsf_core_url( '/assets/css/license-form-popup.css' ), array(), BSF_UPDATER_VERSION, 'all' );
				wp_enqueue_script( 'bsf-core-license-form', bsf_core_url( '/assets/js/license-form-popup.js' ), array( 'jquery', 'bsf-core-jquery-history' ), BSF_UPDATER_VERSION, true );
			}

		}
		/**
		 *  Get BSF inline license form.
		 *
		 * @param array  $links Links.
		 * @param array  $args Arguments.
		 * @param string $license_from_type license form type.
		 */
		public function get_bsf_inline_license_form( $links, $args, $license_from_type ) {

			$product_id = $args['product_id'];

			if ( ! isset( $product_id ) ) {
				return $links;
			}

			if ( is_multisite() && ! is_network_admin() && false === apply_filters( "bsf_core_popup_license_form_per_network_site_{$product_id}", false ) ) {
				return $links;
			}

			$status         = 'inactive';
			$license_string = __( 'Activate License', 'bsf-core' );
			if ( self::bsf_is_active_license( $product_id ) ) {
				$status         = 'active';
				$license_string = __( 'License', 'bsf-core' );
			}

			$product_id = $args['product_id'];

			// Render the license form only once on a page.
			if ( array_key_exists( $product_id, self::$inline_form_products ) ) {
				return $links;
			}

			$form_args = array(
				'product_id'                       => $product_id,
				'button_text_activate'             => esc_html__( 'Activate License', 'bsf-core' ),
				'button_text_deactivate'           => esc_html__( 'Deactivate License', 'bsf-core' ),
				'license_form_title'               => '',
				'license_deactivate_status'        => esc_html__( 'Your license is not active!', 'bsf-core' ),
				'license_activate_status'          => esc_html__( 'Your license is activated!', 'bsf-core' ),
				'submit_button_class'              => 'bsf-product-license button-default',
				'form_class'                       => 'form-wrap bsf-license-register-' . esc_attr( $product_id ),
				'bsf_license_form_heading_class'   => 'bsf-license-heading',
				'bsf_license_active_class'         => 'success-message',
				'bsf_license_not_activate_message' => 'license-error',
				'size'                             => 'regular',
				'bsf_license_allow_email'          => false,
				'popup_license_form'               => ( isset( $args['popup_license_form'] ) ) ? $args['popup_license_form'] : false,
				'license_from_type'                => $license_from_type,
			);

			$form_args = wp_parse_args( $args, $form_args );

			self::$inline_form_products[ $product_id ] = $form_args;

			$action_links = array(
				'license' => '<a plugin-slug="' . esc_attr( $product_id ) . '" class="bsf-core-plugin-link bsf-core-license-form-btn ' . esc_attr( $status ) . '" aria-label="' . esc_attr( $license_string ) . '">' . esc_html( $license_string ) . '</a>',
			);

			return array_merge( $links, $action_links );
		}

		/**
		 * Render the markup for popup form.
		 */
		public function render_popup_form_markup() {

			$current_screen = get_current_screen();

			// Bail if not on plugins.php screen.
			if ( ! is_object( $current_screen ) && null === $current_screen ) {
				return;
			}

			if ( 'plugins' !== $current_screen->id && 'plugins-network' !== $current_screen->id ) {
				return;
			}

			foreach ( self::$inline_form_products as $product_id => $product ) {
				?>

				<div plugin-slug="<?php echo esc_attr( $product_id ); ?>" class="bsf-core-license-form" style="display: none;">
					<div class="bsf-core-license-form-overlay"></div>
					<div class="bsf-core-license-form-inner">
						<button type="button" class="bsf-core-license-form-close-btn">
							<span class="screen-reader-text"><?php esc_html_e( 'Close', 'bsf-core' ); ?></span>
							<span class="dashicons dashicons-no-alt"></span>
						</button>

						<?php
							$licence_form_method = isset( $_GET['license-form-method'] ) ? sanitize_text_field( $_GET['license-form-method'] ) : ''; // phpcs:ignore:WordPress.Security.NonceVerification.Recommended

							$allowed_html = array(
								'a'      => array(
									'href'   => array(),
									'title'  => array(),
									'target' => array(),
								),
								'br'     => array(),
								'em'     => array(),
								'strong' => array(),
								'div'    => array(
									'class' => array(),
									'id'    => array(),
								),
								'span'   => array(
									'class' => array(),
									'id'    => array(),
								),
								'input'  => array(
									'class'        => array(),
									'id'           => array(),
									'type'         => array(),
									'name'         => array(),
									'value'        => array(),
									'autocomplete' => array(),
								),
								'p'      => array(
									'class' => array(),
								),
								'h1'     => array(),
								'h2'     => array(),
								'h3'     => array(),
								'h4'     => array(),
								'h5'     => array(),
								'h6'     => array(),
								'i'      => array(),
								'form'   => array(
									'method' => array(),
									'action' => array(),
									'class'  => array(),
									'id'     => array(),
								),
							);

							if ( 'edd' === $product['license_from_type'] || 'license-key' === $licence_form_method ) {
								echo wp_kses( bsf_license_activation_form( $product ), $allowed_html );
							} elseif ( 'envato' === $product['license_from_type'] || 'oauth' === $licence_form_method ) {
								echo wp_kses( bsf_envato_register( $product ), $allowed_html );
							}

							do_action( "bsf_inlne_license_form_footer_{$product[ 'license_from_type' ]}", $product_id );

							do_action( 'bsf_inlne_license_form_footer', $product_id );

							// Avoid rendering the markup twice as admin_footer can be called multiple times.
							unset( self::$inline_form_products[ $product_id ] );
							?>
					</div>
				</div>

				<?php
			}

		}


	} // Class BSF_License_Manager

	new BSF_License_Manager();
}

/**
 * BSF License activation form.
 *
 * @param array $args Arguments.
 */
function bsf_license_activation_form( $args ) {
	$license_manager = BSF_License_Manager::instance();

	return $license_manager->license_activation_form( $args );
}

/**
 *  Get BSF inline license form.
 *
 * @param array  $links Links.
 * @param array  $bsf_product_id BSF Product ID.
 * @param string $license_from_type license form type.
 */
function get_bsf_inline_license_form( $links, $bsf_product_id, $license_from_type ) {
	$license_manager = BSF_License_Manager::instance();

	return $license_manager->get_bsf_inline_license_form( $links, $bsf_product_id, $license_from_type );
}
