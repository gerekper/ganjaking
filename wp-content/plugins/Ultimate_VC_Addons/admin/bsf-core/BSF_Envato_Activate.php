<?php

/**
 * BSF_Envato_Activate setup
 *
 * @since 1.0
 */
class BSF_Envato_Activate {

	/**
	 * Instance
	 *
	 * @var BSF_Envato_Activate
	 */
	private static $instance;

	/**
	 * Reference to the License manager class.
	 *
	 * @var BSF_License_Manager
	 */
	private $license_manager;

	/**
	 * Stores temporary response messsages from the API validations.
	 *
	 * @var array()
	 */
	private $message_box;

	/**
	 *  Initiator.
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new BSF_Envato_Activate();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->license_manager = new BSF_License_Manager();

		$action = isset( $_GET['license_action'] ) ? esc_attr( $_GET['license_action'] ) : '';

		if ( $action == 'activate_license' ) {
			$this->process_envato_activation();
		}

		add_filter( 'update_footer', array( $this, 'alternate_method_link' ), 20 );
		add_action( 'bsf_inlne_license_envato_after_form', array( $this, 'inline_alternate_method_link' ), 20, 2 );
	}

	public function envato_register( $args ) {

		// Check if alternate method is to be used
		$method = isset( $_GET['activation_method'] ) ? esc_attr( $_GET['activation_method'] ) : 'oauth';

		$html         = '';
		$product_id   = isset( $args['product_id'] ) ? $args['product_id'] : '';
		$is_active    = $this->license_manager->bsf_is_active_license( $product_id );
		$product_name = $this->license_manager->bsf_get_product_info( $product_id, 'name' );
		$purchase_url = $this->license_manager->bsf_get_product_info( $product_id, 'purchase_url' );

		$bundled = BSF_Update_Manager::bsf_is_product_bundled( $product_id );

		if ( ! empty( $bundled ) ) {
			$parent_id         = $bundled[0];
			$is_active         = $this->license_manager->bsf_is_active_license( $parent_id );
			$parent_name       = brainstrom_product_name( $parent_id );
			$registration_page = bsf_registration_page_url( '', $parent_id );

			$html .= '<div class="bundled-product-license-registration">';
			$html .= '<span>';

			if ( $is_active ) {

				$html .= '<h3>License Active!</h3>';

				$html .= '<p>' . sprintf(
					'Your license is activated, you will receive updates for <i>%s</i> when they are available.',
					$product_name
				) . '</p>';
			} else {

				$html .= '<h3>Updates Unavailable!</h3>';
				$html .= '<p>' . sprintf(
					'This plugin is came bundled with the <i>%1$s</i>. For receiving updates, you need to activate license of <i>%2$s</i> <a href="%3$s">here</a>.',
					$parent_name,
					$parent_name,
					$registration_page
				) . '</p>';
			}

			$html .= '</span>';
			$html .= '</div>';

			return $html;
		}

		if ( $method == 'license-key' ) {
			$html .= bsf_license_activation_form( $args );

			return $html;
		}

		// Licence activation button.
		$form_action                  = ( isset( $args['form_action'] ) && ! is_null( $args['form_action'] ) ) ? $args['form_action'] : '';
		$form_class                   = ( isset( $args['form_class'] ) && ! is_null( $args['form_class'] ) ) ? $args['form_class'] : "bsf-license-form-{$product_id}";
		$submit_button_class          = ( isset( $args['submit_button_class'] ) && ! is_null( $args['submit_button_class'] ) ) ? $args['submit_button_class'] : '';
		$license_form_heading_class   = ( isset( $args['bsf_license_form_heading_class'] ) && ! is_null( $args['bsf_license_form_heading_class'] ) ) ? $args['bsf_license_form_heading_class'] : '';
		$license_active_class         = ( isset( $args['bsf_license_active_class'] ) && ! is_null( $args['bsf_license_active_class'] ) ) ? $args['bsf_license_active_class'] : '';
		$license_not_activate_message = ( isset( $args['bsf_license_not_activate_message'] ) && ! is_null( $args['bsf_license_not_activate_message'] ) ) ? $args['bsf_license_not_activate_message'] : '';

		$size                    = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$button_text_activate    = ( isset( $args['button_text_activate'] ) && ! is_null( $args['button_text_activate'] ) ) ? 'Sign Up & Activate' : 'Sign Up & Activate';
		$button_text_deactivate  = ( isset( $args['button_text_deactivate'] ) && ! is_null( $args['button_text_deactivate'] ) ) ? $args['button_text_deactivate'] : 'Deactivate License';
		$placeholder             = ( isset( $args['placeholder'] ) && ! is_null( $args['placeholder'] ) ) ? $args['placeholder'] : 'Enter your license key..';
		$popup_license_form      = ( isset( $args['popup_license_form'] ) ) ? $args['popup_license_form'] : false;
		$bsf_license_allow_email = ( isset( $args['bsf_license_allow_email'] ) && ! is_null( $args['bsf_license_allow_email'] ) ) ? $args['bsf_license_allow_email'] : true;

		if ( $bsf_license_allow_email == true ) {
			$form_class .= ' license-form-allow-email ';

			if ( ! $is_active ) {
				$submit_button_class .= ' button-primary button-hero bsf-envato-form-activation ';
			}
		}

		if ( $is_active != true ) {
			$form_action = bsf_get_api_site() . 'envato-validation-callback/?wp-envato-validate';
		} else {
			$form_action = bsf_registration_page_url( '', $product_id );
		}

		$html .= '<div class="envato-license-registration">';

		$html .= '<form method="post" class="' . $form_class . '" action="' . $form_action . '">';

		if ( $this->getMessage( 'message' ) !== '' ) {
			$html .= '<span class="bsf-license-message license-' . $this->getMessage( 'status' ) . '">';
			$html .= $this->getMessage( 'message' );
			$html .= '</span>';
		}

		if ( $is_active ) {

			$envato_active_oauth_title    = apply_filters( "envato_active_oauth_title_{$product_id}", 'Updates & Support Registration - <span class="active">Active!</span>' );
			$envato_active_oauth_subtitle = '<span class="active">' . sprintf(
				'Your license is active.',
				$product_name
			) . '</span>';

			$envato_active_oauth_subtitle = apply_filters( "envato_active_oauth_subtitle_{$product_id}", $envato_active_oauth_subtitle );

			if ( $popup_license_form ) {
				$html .= '<div class="bsf-wrap-title">';
				$html .= '<h3 class="envato-oauth-heading">' . $product_name . '</h2>';
				$html .= '<p class="envato-oauth-subheading">' . $envato_active_oauth_subtitle . '</p>';
				$html .= '</div>';

			} else {
				$html .= '<div class="bsf-wrap-title">';
				$html .= '<h3 class="envato-oauth-heading">' . $envato_active_oauth_title . '</h2>';
				$html .= '<p class="envato-oauth-subheading">' . $envato_active_oauth_subtitle . '</p>';
				$html .= '</div>';
			}

			$html .= '<input type="hidden" readonly class="' . $license_active_class . ' ' . $size . '-text" id="bsf_license_manager[license_key]" name="bsf_license_manager[license_key]" value="License Validated"/>';
			$html .= '<input type="hidden" class="' . $size . '-text" id="bsf_license_manager[product_id]" name="bsf_license_manager[product_id]" value="' . esc_attr( stripslashes( $product_id ) ) . '"/>';

			$html .= '<input type="submit" class="button ' . $submit_button_class . '" name="bsf_deactivate_license" value="' . esc_attr__( $button_text_deactivate, 'bsf' ) . '"/>';

		} else {

			$envato_not_active_oauth_title    = apply_filters( "envato_not_active_oauth_title_{$product_id}", __( 'Updates & Support Registration - <span class="not-active">Not Active!</span>', 'bsf' ) );
			$envato_not_active_oauth_subtitle = apply_filters( "envato_not_active_oauth_subtitle_{$product_id}", __( 'Click on the button below to activate your license and subscribe to our newsletter.', 'bsf' ) );

			if ( $popup_license_form ) {
				$html .= '<div class="bsf-wrap-title">';
				$html .= '<h3 class="envato-oauth-heading">' . $product_name . '</h2>';
				$html .= '<p class="envato-oauth-subheading">' . $envato_not_active_oauth_subtitle . '</p>';
				$html .= '</div>';
			} else {
				$html .= '<div class="bsf-wrap-title">';
				$html .= '<h3 class="envato-oauth-heading">' . $envato_not_active_oauth_title . '</h2>';
				$html .= '<p class="envato-oauth-subheading">' . $envato_not_active_oauth_subtitle . '</p>';
				$html .= '</div>';
			}

			$html .= '<input type="hidden" readonly class="' . $license_active_class . ' ' . $size . '-text" id="bsf_license_manager[license_key]" name="url" value="' . get_site_url() . '"/>';
			$html .= '<input type="hidden" readonly class="' . $license_active_class . ' ' . $size . '-text" id="bsf_license_manager[license_key]" name="redirect" value="' . $this->get_redirect_url( $product_id ) . '"/>';
			$html .= '<input type="hidden" readonly class="' . $license_active_class . ' ' . $size . '-text" id="bsf_license_manager[license_key]" name="product_id" value="' . $product_id . '"/>';

			$html .= '<input id="bsf-license-privacy-consent" name="bsf_license_manager[privacy_consent]" type="hidden" value="true" />';
			$html .= '<input id="bsf-license-terms-conditions-consent" name="bsf_license_manager[terms_conditions_consent]" type="hidden" value="true" />';

			$html .= '<div class="submit-button-wrap">';
			$html .= '<input type="button" class="button ' . $submit_button_class . '" name="bsf_activate_license" value="' . esc_attr__( $button_text_activate, 'bsf' ) . '"/>';
			$html .= "<p class='purchase-license'><a target='_blank' href='$purchase_url'>Purchase License »</a></p>";
			$html .= '</div>';
		}

		$html .= '</form>';

		$html = apply_filters( 'bsf_inlne_license_envato_after_form', $html, $product_id );

		$html .= '</div> <!-- envato-license-registration -->';

		if ( isset( $_GET['debug'] ) ) {
			$html .= get_bsf_systeminfo();
		}

		return $html;
	}

	public function envato_activation_url( $form_data ) {
		$product_id = isset( $form_data['product_id'] ) ? esc_attr( $form_data['product_id'] ) : '';

		$form_data['token'] = sha1( $this->create_token( $product_id ) );
		$url                = bsf_get_api_site() . 'envato-validation-callback/?wp-envato-validate';

		$envato_activation_url = add_query_arg(
			$form_data,
			$url
		);

		return $envato_activation_url;
	}

	protected function get_redirect_url( $product_id = '' ) {

		if ( is_ssl() ) {
			$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			$current_url = "http://". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		$current_url = esc_url( remove_query_arg( array( 'license_action', 'token', 'product_id', 'purchase_key', 'success', 'status', 'message' ), $current_url ) );

		if ( '' != $product_id ) {
			$current_url = add_query_arg(
				array(
					'bsf-inline-license-form' => $product_id,
				), $current_url
			);
		}

		return $current_url;
	}

	protected function create_token( $product_id ) {
		$token = $product_id . '|' . current_time( 'timestamp' ) . '|' . bsf_generate_rand_token();
		update_site_option( "bsf_envato_token_$product_id", $token );

		return $token;
	}

	protected function validateToken( $token, $product_id ) {

		$stored_token = get_site_option( "bsf_envato_token_$product_id", '' );

		if ( $token == sha1( $stored_token ) ) {
			$token_atts = explode( '|', $stored_token );

			$stored_id = $token_atts[0];

			if ( $stored_id != $product_id ) {
				// Token is invalid
				return false;
			}

			$timestamp  = (int) $token_atts[1];
			$validUltil = $timestamp + 900;

			if ( current_time( 'timestamp' ) > $validUltil ) {
				// Timestamp has expired.
				return false;
			}

			// If above conditions did not meet, the token is valid.
			return true;
		}

		return false;
	}

	protected function process_envato_activation() {
		$token      = isset( $_GET['token'] ) ? esc_attr( $_GET['token'] ) : '';
		$product_id = isset( $_GET['product_id'] ) ? esc_attr( $_GET['product_id'] ) : '';

		if ( $this->validateToken( $token, $product_id ) ) {
			$args                 = array();
			$args['purchase_key'] = isset( $_GET['purchase_key'] ) ? esc_attr( $_GET['purchase_key'] ) : '';
			$args['status']       = isset( $_GET['status'] ) ? esc_attr( $_GET['status'] ) : '';
			$this->license_manager->bsf_update_product_info( $product_id, $args );

			$this->setMessage(
				array(
					'status'  => 'success',
					'message' => 'License successfully activated!',
				)
			);

		} else {

			$this->setMessage(
				array(
					'status'  => 'error',
					'message' => 'The token is invalid or is expired, please try again.',
				)
			);

		}
	}

	protected function setMessage( $message = array() ) {
		$this->message_box = $message;
	}

	protected function getMessage( $key ) {
		$message = $this->message_box;

		return isset( $message[ $key ] ) ? $message[ $key ] : '';
	}

	public function inline_alternate_method_link( $html, $bsf_product_id ) {
		$privacy_policy_link   = $this->license_manager->bsf_get_product_info( $bsf_product_id, 'privacy_policy' );
		$terms_conditions_link = $this->license_manager->bsf_get_product_info( $bsf_product_id, 'terms_conditions' );

		if ( isset( $privacy_policy_link ) ) {
			$html .= sprintf(
				'<a class="license-form-external-links" target="_blank" href="%s">Privacy Policy</a> | ',
				$privacy_policy_link
			);
		}

		if ( isset( $terms_conditions_link ) ) {
			$html .= sprintf(
				'<a class="license-form-external-links" target="_blank" href="%s">Terms & Conditions</a>',
				$terms_conditions_link
			);
		}


		return $html;
	}

	public function alternate_method_link( $content ) {

			$content = sprintf(
				'<a href="%s">Activate license using purchase key</a>',
				add_query_arg(
					array(
						'activation_method' => 'license-key',
					)
				)
			);

			return $content;
	}
}

function bsf_envato_register( $args ) {
	$BSF_Envato_Activate = BSF_Envato_Activate::instance();

	return $BSF_Envato_Activate->envato_register( $args );
}
