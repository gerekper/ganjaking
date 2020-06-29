<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Forgotten
 */
class CT_Ultimate_GDPR_Controller_Forgotten extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-forgotten';

	/**
	 * Init after construct
	 */
	public function init() {
		add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'legacy_update' ) );
		add_filter( 'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_recipient', array(
			$this,
			'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_recipient'
		) );
		add_filter( 'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_subject', array(
			$this,
			'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_subject'
		) );
		add_filter( 'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_message', array(
			$this,
			'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_message'
		), 10, 2 );
		add_action( 'wp_ajax_ct_ultimate_gdpr_forget', array( $this, 'front_action' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_forget', array( $this, 'send_ajax_response' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_forget', array( $this, 'front_action' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_forget', array( $this, 'send_ajax_response' ) );
	}

	/**
	 * Do actions on frontend
	 */
	public function front_action() {

		if ( $this->is_request_to_forget() ) {
			$this->process_request_to_forget();
		}

		if ( $this->is_request_confirmation() ) {
			$this->process_confirm_request();
		}
	}

	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}

	/**
	 * Get unique controller id (page name, option key)
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Do actions on current admin page
	 */
	protected function admin_page_action() {

		if ( $this->is_admin_request_for_users_forget() ) {
			$this->users_forget();
		}

		if ( $this->is_admin_request_for_users_send_email() ) {
			$this->users_send_email();
		}

		if ( $this->is_admin_request_for_users_remove() ) {
			$this->users_remove_from_list();
		}

		/* Default settings page */
		$this->add_view_option( 'data', $this->get_all_requested_users_data( $confirmed_only = true ) );

	}

	/**
	 * @param $called_email_address
	 *
	 * @return string
	 */
	public function ct_ultimate_gdpr_controller_forgotten_request_admin_mail_recipient( $called_email_address ) {
		if ( strlen( $called_email_address ) > 0 ) {
			$email_address = esc_html__( $called_email_address );
		} else {
			$email_address = esc_html__( get_option( 'admin_email' ) );
		}

		return $email_address;
	}

	/**
	 * @param $called_email_title
	 *
	 * @return string
	 */
	public function ct_ultimate_gdpr_controller_forgotten_request_admin_mail_subject( $called_email_title ) {
		if ( strlen( $called_email_title ) > 0 ) {
			$email_title = esc_html__( $called_email_title );
		} else {
			$email_title = sprintf(
				esc_html__( "[Ultimate GDPR] New Right To Be Forgotten request from %s", 'ct-ultimate-gdpr' ),
				get_bloginfo( 'name' )
			);
		}

		return $email_title;
	}

	/**
	 * @param $called_email_body
	 * @param $email
	 *
	 * @return string
	 */
	public function ct_ultimate_gdpr_controller_forgotten_request_admin_mail_message( $called_email_body, $email ) {
		if ( strlen( $called_email_body ) > 0 ) {
			$email_address = sprintf( esc_html__( $called_email_body ), $email );
		} else {
			$email_address = sprintf(
				esc_html__( "There is a new Right To Be Forgotten request from email %s. See details at %s", 'ct-ultimate-gdpr' ),
				$email,
				admin_url( "admin.php?page=" . $this->get_id() )
			);
		}

		return $email_address;
	}

	/**
	 * @param bool $confirmed_only
	 *
	 * @return array
	 */
	private function get_all_requested_users_data( $confirmed_only = false ) {

		$data     = array();
		$requests = get_option( $this->get_requests_option_key(), array() );

		foreach ( $requests as $request ) {

			if ( $confirmed_only && empty( $request['confirmed'] ) ) {
				continue;
			}

			$user                           = new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $request['user_email'] ) );
			$datum                          = array(
				'id'         => $user->get_id(),
				'user_email' => $request['user_email'],
				'date'       => ct_ultimate_gdpr_date( $request['date'] ),
				'services'   => $request['services'],
				'status'     => $request['status'] ? ct_ultimate_gdpr_date( $request['status'] ) : '',
				'confirmed'  => $request['confirmed'],
			);
			$data[ $request['user_email'] ] = $datum;

		}

		// recent first
		$data = array_reverse( $data );

		return $data;

	}

	/**
	 * Add menu page (if not added in admin controller)
	 */
	public function add_menu_page() {
		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Right To Be Forgotten', 'ct-ultimate-gdpr' ),
			esc_html__( 'Right To Be Forgotten', 'ct-ultimate-gdpr' ),
			'manage_options',
			$this->get_id(),
			array( $this, 'render_menu_page' )
		);

	}

	/**
	 * Get view template string
	 * @return string
	 */
	public function get_view_template() {
		return 'admin/admin-forgotten';
	}

	/** @return bool */
	private function is_request_to_forget() {

		$forget  = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-service-forget', $this->get_request_array() );
		$consent = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-consent-forget-me', $this->get_request_array() );
		$email   = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-email', $this->get_request_array() );
		$submit  = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forget-submit', $this->get_request_array() );

		if ( ! $forget || ! $consent || ! $submit || ! $email ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function process_request_to_forget() {

		$email = sanitize_email( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-email', $this->get_request_array(), array() ) );
		$page_url = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-request-url', $this->get_request_array(), get_home_url() );

		if ( ! $email ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__(  "Wrong email!", 'ct-ultimate-gdpr' ) );
			return false;
		}

		if ( ! ct_ultimate_gdpr_recaptcha_verify() ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Recaptcha error!", 'ct-ultimate-gdpr' ) );
			return false;
		}

		$services_names     = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-service-forget', $this->get_request_array(), array() );
		$all_services       = CT_Ultimate_GDPR_Model_Services::instance()->get_services();
		$services_to_forget = array();

		/** @var CT_Ultimate_GDPR_Service_Abstract $service */
		foreach ( $all_services as $service ) {

			if ( in_array( $service->get_id(), $services_names ) ) {
				$services_to_forget[ $service->get_id() ] = array(
					'status' => 0,
					'name'   => $service->get_name(),
				);
			}

		}

		if ( empty( $services_to_forget ) ) {
			return false;
		}

		$user = new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) );
		$this->set_user( $user );
		$request_data = $this->save_user_request( $services_to_forget );

		$current_url = $page_url ? set_url_scheme( $page_url ) : set_url_scheme( "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$target_url  = $this->get_option( 'forgotten_target_page' ) ? $this->get_option( 'forgotten_target_page' ) : $current_url;

		ct_ultimate_gdpr_send_confirm_mail(
			$email,
			sprintf( esc_html__( '[Ultimate GDPR] Please confirm request to be forgotten from %s', 'ct-ultimate-gdpr' ), get_bloginfo( 'name' ) ),
			$this->get_id(),
			$request_data['date'],
			$target_url
		);

		CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Your request was sent!", 'ct-ultimate-gdpr' ) );

		return true;

	}

	/**
	 * @return bool
	 */
	private function is_request_confirmation() {

		$submit = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forget-submit', $this->get_request_array() );

		// request to forget, not to confirm
		if ( $submit ) {
			return false;
		}

		if ( empty( $_GET['e'] ) || empty( $_GET['h'] ) || empty( $_GET['i'] ) ) {
			return false;
		}

		if ( $_GET['i'] != $this->get_id() ) {
			return false;
		}

		return true;

	}

	/**
	 *
	 */
	private function process_confirm_request() {

		// get email from query string (GET doesnt work for email)
		preg_match( '#e=\K[a-zA-Z0-9\._\-+%]+@[a-zA-Z0-9\._\-+%]+\.[a-zA-Z0-9]{2,}#', $_SERVER['QUERY_STRING'], $matches );
		$email = sanitize_email( $matches[0] );

		$requests = $this->get_all_requested_users_data();

		if ( ! empty( $requests[ $email ]['confirmed'] ) ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add(
				'notices',
				esc_html__( 'Your request was already confirmed.', 'ct-ultimate-gdpr' )
			);

			return;
		}

		if ( isset( $requests[ $email ] ) ) {

			// check hashes match. salt is request time() value
			$result = ct_ultimate_gdpr_check_confirm_mail( $email, strtotime( $requests[ $email ]['date'] ), $_GET['h'] );

		}

		if ( ! empty( $result ) ) {

			// update request option
			$requests = get_option( $this->get_requests_option_key(), array() );
			if ( isset( $requests[ $email ]['confirmed'] ) ) {
				$requests[ $email ]['confirmed'] = 1;
				update_option( $this->get_requests_option_key(), $requests );
			}


			CT_Ultimate_GDPR_Model_Front_View::instance()->add(
				'notices',
				esc_html__( 'Your request was successfully confirmed.', 'ct-ultimate-gdpr' )
			);

			$notify_mail = apply_filters(
				'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_recipient',
				$this->get_option( 'forgotten_notify_mail' )
			);

			$subject = apply_filters(
				'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_subject',
				sprintf(
					esc_html__( "[Ultimate GDPR] New Right To Be Forgotten request from %s", 'ct-ultimate-gdpr' ),
					get_bloginfo( 'name' )
				)
			);

			$message = apply_filters(
				'ct_ultimate_gdpr_controller_forgotten_request_admin_mail_message',
				sprintf(
					esc_html__( "There is a new Right To Be Forgotten request from email %s. See details at %s", 'ct-ultimate-gdpr' ),
					$email,
					admin_url( 'admin.php?page=ct-ultimate-gdpr-forgotten' )
				),
				$email
			);

			if ( $notify_mail ) {
				wp_mail(
					$notify_mail,
					$subject,
					$message,
					''
				);

			}

			if (
				$this->get_option( 'forgotten_automated_forget' ) &&
				array_intersect(
					(array) wp_get_current_user()->roles,
					$this->get_option(
						'forgotten_automated_user_forget_roles',
						ct_ultimate_gdpr_get_value('forgotten_automated_user_forget_roles', $this->get_default_options() ) )
				)
			) {

				$services      = isset( $requests[ $email ]['services'] ) ? $requests[ $email ]['services'] : array();
				$services_data = array( $email => array_keys( $services ) );
				$this->users_forget( $services_data );

				if ( $this->get_option( 'forgotten_automated_user_email' ) ) {

					$this->users_send_email( array( $email ) );

				}
			}

		} else {

			CT_Ultimate_GDPR_Model_Front_View::instance()->add(
				'notices',
				esc_html__( 'Your request was not confirmed. Please check the link or contact admin.', 'ct-ultimate-gdpr' )
			);

		}


	}

	/**
	 * @param array $services
	 *
	 * @return array
	 */
	private function save_user_request( $services ) {
		$requests                             = get_option( $this->get_requests_option_key(), array() );
		$data                                 = array(
			'id'         => $this->user->get_id(),
			'user_email' => $this->user->get_email(),
			'date'       => time(),
			'services'   => $services,
			'status'     => 0,
			'confirmed'  => 0,
		);
		$requests[ $this->user->get_email() ] = $data;
		update_option( $this->get_requests_option_key(), $requests );

		return $data;

	}

	/**
	 * @return bool
	 */
	private function is_admin_request_for_users_send_email() {

		if (
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forgotten-send', $this->get_request_array() ) &&
			ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() )
		) {
			return true;
		}

		return false;

	}

	/**
	 * @return bool
	 */
	private function is_admin_request_for_users_remove() {

		if (
			(
				ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forgotten-remove', $this->get_request_array() ) &&
				ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() )
			) ||
			(
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forgotten-remove-all-sent', $this->get_request_array() )
			)
		) {
			return true;
		}

		return false;

	}

	/**
	 * @return bool
	 */
	private function is_admin_request_for_users_forget() {

		if (
			ct_ultimate_gdpr_get_value( 'services', $this->get_request_array() ) &&
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forgotten-send', $this->get_request_array() )
		) {
			return true;
		}

		return false;


	}

	/**
	 * @param array $emails
	 */
	private function users_send_email( $emails = array() ) {

		$emails   = $emails ?
			$emails :
			ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() );
		$requests = get_option( $this->get_requests_option_key(), array() );

		$message = apply_filters(
			'ct_ultimate_gdpr_controller_forgotten_request_user_mail_message',
			$this->get_option( 'forgotten_notify_email_message' )
		);

		$subject = apply_filters(
			'ct_ultimate_gdpr_controller_forgotten_request_user_mail_subject',
			$this->get_option( 'forgotten_notify_email_subject' )
		);

		foreach ( $emails as $email ) {

			$email = sanitize_email( $email );
			wp_mail( $email, $subject, $message, 'Content-Type: text/html; charset=UTF-8' );
			$requests[ $email ]['status'] = time();

		}

		update_option( $this->get_requests_option_key(), $requests );

		$this->add_view_option(
			'notices',
			array(
				sprintf(
					esc_html__( "[Ultimate GDPR] Emails were sent to the following users: %s", 'ct-ultimate-gdpr' ),
					implode( ', ', $emails )
				)
			)
		);

	}

	/**
	 *
	 */
	private function users_remove_from_list() {

		$remove_all_sent = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-forgotten-remove-all-sent', $this->get_request_array() );
		$emails          = ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array(), array() );
		$requests        = get_option( $this->get_requests_option_key(), array() );

		foreach ( $requests as $key => $request ) {

			if (
				( ! $remove_all_sent && in_array( $key, $emails ) ) ||
				( $remove_all_sent && ! empty( $request['status'] ) )
			) {

				{
					unset( $requests[ $key ] );
				}

			}

		}

		update_option( $this->get_requests_option_key(), $requests );

		$this->add_view_option(
			'notices',
			array(
				sprintf(
					esc_html__( "[Ultimate GDPR] The following emails were removed from list: %s", 'ct-ultimate-gdpr' ),
					implode( ', ', $emails )
				)
			)
		);

	}

	/**
	 * @param array $services_data
	 */
	private function users_forget( $services_data = array() ) {

		$services_data = $services_data ?
			$services_data :
			ct_ultimate_gdpr_get_value( 'services', $this->get_request_array(), array() );
		$requests      = get_option( $this->get_requests_option_key(), array() );
		$requests      = apply_filters( 'ct_ultimate_gdpr_controller_forgotten_users_forget_requests_before', $requests, $services_data );

		foreach ( $services_data as $email => $services ) {

			if ( isset( $requests[ $email ] ) && is_array( $requests[ $email ] ) ) {

				/** @var CT_Ultimate_GDPR_Service_Abstract $service */
				foreach ( $services as $service_id ) {

					$service = CT_Ultimate_GDPR_Model_Services::instance()->get_service_by_id( $service_id );

					try {

						if ( ! $service ) {
							throw new Exception(
								sprintf(
									esc_html__( "Non existing service id: %s", 'ct-ultimate-gdpr' ),
									$service_id
								)
							);

						}

						if ( ! $service->is_forgettable() ) {
							continue;
						}

						$service->set_user( new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) ) )
						        ->forget();

					} catch ( Exception $e ) {

						$this->add_view_option(
							'notices',
							array(
								sprintf(
									esc_html__( "There was an error forgetting %s service: %s", 'ct-ultimate-gdpr' ),
									$service_id,
									$e->getMessage()
								)
							)
						);

						continue;
					}

					if ( isset( $requests[ $email ]['services'][ $service_id ]['status'] ) ) {
						$requests[ $email ]['services'][ $service_id ]['status'] = time();
					}

				}
			}

		}

		$requests = apply_filters( 'ct_ultimate_gdpr_controller_forgotten_users_forget_requests_after', $requests, $services_data );
		update_option( $this->get_requests_option_key(), $requests );

		$this->add_view_option(
			'notices',
			array(
				esc_html__( "[Ultimate GDPR] Services data were removed", 'ct-ultimate-gdpr' ),
			)
		);

	}


	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		/* Right To Be Forgotten section */

		add_settings_section(
			'ct-ultimate-gdpr-forgotten_section-1', // ID
			esc_html__( 'Forms skin', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			self::ID // Page
		);

		add_settings_section(
			'ct-ultimate-gdpr-forgotten', // ID
			esc_html__( 'Right to be forgotten settings', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-forgotten' // Page
		);

		/* Right To Be Forgotten section fields */

		{

			add_settings_field(
				'forgotten_skin_shape',
				esc_html__( 'Skin', 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_forgotten_skin_shape' ),
				self::ID,
				'ct-ultimate-gdpr-forgotten_section-1'
			);

			add_settings_field(
				'forgotten_automated_forget', // ID
				esc_html__( "Automatically forget users who confirmed their mail", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_automated_forget' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'forgotten_automated_user_email', // ID
				esc_html__( "Automatically send email about data removal to users who confirmed their email", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_automated_user_email' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'forgotten_automated_user_forget_roles', // ID
				esc_html__( "Permit users of selected roles only to be forgotten automatically", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_automated_user_forget_roles' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'forgotten_notify_mail', // ID
				esc_html__( "Admin email to send new request notifications to", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_notify_mail' ), // Callback
				'ct-ultimate-gdpr-forgotten', // Page
				'ct-ultimate-gdpr-forgotten' // Section
			);

			add_settings_field(
				'forgotten_notify_email_subject', // ID
				esc_html__( "User notification email subject", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_notify_email_subject' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'forgotten_notify_email_message', // ID
				esc_html__( "User notification email message", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_notify_email_message' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'forgotten_target_page', // ID
				esc_html__( "Set custom URL to the page containing Ultimate GDPR shortcode as the email confirmation target page (or leave empty for autodetect)", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_forgotten_target_page' ), // Callback
				'ct-ultimate-gdpr-forgotten', // Page
				'ct-ultimate-gdpr-forgotten' // Section
			);

		}

	}

	/**
	 *
	 */
	public function render_field_forgotten_automated_user_forget_roles() {

		global $wp_roles;
		$roles       = empty( $wp_roles ) ? array() : $wp_roles->roles;
		$roles_array = array();

		/** @var array $role_array */
		foreach ( $roles as $role_slug => $role_array ) {
			$roles_array[ $role_slug ] = $role_array['name'];
		}

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value(
			$field_name,
			ct_ultimate_gdpr_get_value( 'forgotten_automated_user_forget_roles', $this->get_default_options() )
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s" size="8" multiple>',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ) . "[]"
		);

		foreach ( $roles_array as $role_slug => $role_name ) :

			$selected = is_array( $values ) && in_array( $role_slug, $values ) ? "selected" : '';
			echo "<option value='$role_slug' $selected>$role_name</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_forgotten_automated_forget() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_automated_user_email() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_skin_shape() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name, 'ct-ultimate-gdpr-simple-form' );
		$options    = array(
			''                              => esc_html__( 'Default', 'ct-ultimate-gdpr' ),
			'ct-ultimate-gdpr-simple-form'  => esc_html__( 'Simple form', 'ct-ultimate-gdpr' ),
			'ct-ultimate-gdpr-rounded-form' => esc_html__( 'Rounded form', 'ct-ultimate-gdpr' ),
			'ct-ultimate-gdpr-tabbed-form'  => esc_html__( 'Tabbed form', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		/** @var WP_Post $post */
		foreach ( $options as $attr => $label ) :

			$selected = $attr == $value ? "selected" : '';
			echo "<option value='$attr' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}


	/**
	 *
	 */
	public function render_field_forgotten_target_page() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_notify_email_subject() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, '' )
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_notify_email_message() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );

		wp_editor(
			$admin->get_option_value( $field_name, '' ),
			$this->get_id() . '_' . $field_name,
			array(
				'textarea_rows' => 10,
				'textarea_name' => $admin->get_field_name_prefixed( $field_name ),
			)
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_notify_mail() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}

	/**
	 * @return array|mixed
	 */
	public function get_default_options() {

		global $wp_roles;
		$roles_default = isset( $wp_roles->roles ) ? array_keys( $wp_roles->roles ) : array();

		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
			'forgotten_notify_mail'                 => get_bloginfo( 'admin_email' ),
			'forgotten_notify_email_subject'        => esc_html__( "[Ultimate GDPR] Your data has been forgotten", 'ct-ultimate-gdpr' ),
			'forgotten_notify_email_message'        => esc_html__( "[Ultimate GDPR] Your data has been forgotten", 'ct-ultimate-gdpr' ),
			'forgotten_automated_user_forget_roles' => $roles_default,
		) );

	}

	/**
	 * @return string
	 */
	public function get_requests_option_key() {
		return $this->get_id() . '-requests';
	}


	/**
	 * Update options for legacy structure
	 */
	public function legacy_update() {

		/* Request data was moved from controller option to requests option in v1.1 */

		// get legacy requests data
		$legacy_requests = $this->get_option( 'requests', array() );

		// something to update
		if ( $legacy_requests && is_array( $legacy_requests ) ) {

			// new requests structure
			$requests = get_option( $this->get_requests_option_key(), array() );

			// merge requests
			$requests = array_merge( $legacy_requests, $requests );

			// fill new structure with data
			update_option( $this->get_requests_option_key(), $requests );

		}

		// destroy old structure data
		if ( $legacy_requests ) {

			$options = get_option( $this->get_id(), array() );
			unset( $options['requests'] );
			update_option( $this->get_id(), $options );

		}

	}

}