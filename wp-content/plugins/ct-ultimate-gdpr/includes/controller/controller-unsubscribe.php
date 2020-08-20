<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CT_Ultimate_GDPR_Controller_Unsubscribe
 *
 */
class CT_Ultimate_GDPR_Controller_Unsubscribe extends CT_Ultimate_GDPR_Controller_Abstract {
	
	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-unsubscribe';
	
	/**
	 * Init after construct
	 */
	public function init() {
		add_action( 'wp_ajax_ct_ultimate_gdpr_unsubscribe', array( $this, 'front_action' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_unsubscribe', array( $this, 'send_ajax_response' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_unsubscribe', array( $this, 'front_action' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_unsubscribe', array( $this, 'send_ajax_response' ) );
	}
	
	/**
	 * @return string
	 */
	public function get_id() {
		return self::ID;
	}
	
	/**
	 * @return string
	 */
	public function get_requests_option_key() {
		return $this->get_id() . '-requests';
	}
	
	/**
	 * @param $to
	 * @param $subject
	 * @param $message
	 * @param array $attachments
	 *
	 * @return self
	 */
	private function send_mail( $to, $subject, $message, $attachments = array() ) {
		wp_mail( $to, $subject, $message, 'Content-Type: text/html; charset=UTF-8', $attachments );
		
		return $this;
	}
	
	/**
	 *
	 */
	public function front_action() {
		
		if ( $this->is_user_request_unsubscribe() ) {
			$this->process_user_request_unsubscribe();
		}
		
		if ( $this->is_request_confirmation() ) {
			$this->process_confirm_request();
		}
		
	}
	
	/**
	 * @return bool
	 */
	private function is_request_confirmation() {
		
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
				$this->get_option( 'unsubscribe_notify_mail' )
			);
			
			$subject = apply_filters(
				'ct_ultimate_gdpr_controller_unsubscribe_request_admin_mail_subject',
				'[Ultimate GDPR] New Unsubscribe request'
			);
			
			$message = apply_filters(
				'ct_ultimate_gdpr_controller_unsubscribe_request_admin_mail_message',
				sprintf(
					esc_html__( "There is a new Unsubscribe request from email %s. See details at %s", 'ct-ultimate-gdpr' ),
					$email,
					admin_url( 'admin.php?page=ct-ultimate-gdpr-unsubscribe' )
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
			
			if ( $this->get_option( 'unsubscribe_automated_unsubscribe' ) ) {
				
				$services      = isset( $requests[ $email ]['services'] ) ? $requests[ $email ]['services'] : array();
				$services_data = array( $email => array_keys( $services ) );
				$this->users_unsubscribe( $services_data );
				
				if ( $this->get_option( 'unsubscribe_automated_user_email' ) ) {
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
	 * @return bool
	 */
	private function process_user_request_unsubscribe() {
		
		$email    = sanitize_email( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-email', $this->get_request_array(), array() ) );
		$page_url = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-request-url', $this->get_request_array(), get_home_url() );
		
		if ( ! $email ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Wrong email!", 'ct-ultimate-gdpr' ) );
			
			return false;
		}
		
		if ( ! ct_ultimate_gdpr_recaptcha_verify() ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Recaptcha error!", 'ct-ultimate-gdpr' ) );
			
			return false;
		}
		
		$services_names          = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-service-unsubscribe', $this->get_request_array(), array() );
		$all_services            = CT_Ultimate_GDPR_Model_Services::instance()->get_services();
		$services_to_unsubscribe = array();
		
		/** @var CT_Ultimate_GDPR_Service_Abstract $service */
		foreach ( $all_services as $service ) {
			
			if ( in_array( $service->get_id(), $services_names ) ) {
				$services_to_unsubscribe[ $service->get_id() ] = array(
					'status' => 0,
					'name'   => $service->get_name(),
				);
			}
			
		}
		
		if ( empty( $services_to_unsubscribe ) ) {
			return false;
		}
		
		$user = new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) );
		$this->set_user( $user );
		$request_data = $this->save_user_request( $services_to_unsubscribe );
		
		$current_url = $page_url ? set_url_scheme( $page_url ) : set_url_scheme( "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$target_url  = $this->get_option( 'unsubscribe_target_page' ) ? $this->get_option( 'unsubscribe_target_page' ) : $current_url;
		
		ct_ultimate_gdpr_send_confirm_mail(
			$email,
			sprintf( esc_html__( '[Ultimate GDPR] Please confirm request to be unsubscribed from %s newsletters', 'ct-ultimate-gdpr' ), get_bloginfo( 'name' ) ),
			$this->get_id(),
			$request_data['date'],
			$target_url
		);
		
		CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Your request was sent!", 'ct-ultimate-gdpr' ) );
		
		return true;
		
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
	 *
	 */
	private function is_user_request_unsubscribe() {
		
		$forget  = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-service-unsubscribe', $this->get_request_array() );
		$consent = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-consent-unsubscribe', $this->get_request_array() );
		$email   = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-email', $this->get_request_array() );
		$submit  = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-unsubscribe-submit', $this->get_request_array() );
		
		if ( ! $forget || ! $consent || ! $submit || ! $email ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	private
	function is_admin_request_for_users_send_email() {
		
		if (
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-unsubscribe-send', $this->get_request_array() ) &&
			ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() )
		) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * @return bool
	 */
	private
	function is_admin_request_for_users_remove() {
		
		if (
			(
				ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-unsubscribe-remove', $this->get_request_array() ) &&
				ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() )
			) ||
			(
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-unsubscribe-remove-all-sent', $this->get_request_array() )
			)
		) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * @param array $emails
	 */
	private function users_send_email( $emails = array() ) {
		
		$emails   = $emails ? $emails : ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() );
		$requests = get_option( $this->get_requests_option_key(), array() );
		
		foreach ( $emails as $email ) {
			
			$email = sanitize_email( $email );
			$this->set_user( new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) ) );
			
			$this->send_mail(
				$email,
				$this->get_option( 'unsubscribe_mail_title' ),
				$this->get_option( 'unsubscribe_mail_content' )
			);
			
			if ( isset( $requests[ $email ] ) && is_array( $requests[ $email ] ) ) {
				
				$requests[ $email ]['status'] = time();
				
			}
			
		}
		
		update_option( $this->get_requests_option_key(), $requests );
		
		$this->add_view_option(
			'notices',
			array(
				sprintf(
					esc_html__( "Data was sent to email: %s", 'ct-ultimate-gdpr' ),
					implode( ', ', $emails )
				)
			)
		);
		
	}
	
	/**
	 *
	 */
	private
	function users_remove_from_list() {
		
		$remove_all_sent = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-unsubscribe-remove-all-sent', $this->get_request_array() );
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
					esc_html__( "The following emails were removed from the list: %s", 'ct-ultimate-gdpr' ),
					implode( ', ', $emails )
				)
			)
		);
		
	}
	
	/**
	 */
	protected function admin_page_action() {
		
		if ( $this->is_admin_request_for_users_unsubscribe() ) {
			$this->users_unsubscribe();
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
	 * @param array $services_data
	 */
	private function users_unsubscribe( $services_data = array() ) {
		
		$services_data = $services_data ?
			$services_data :
			ct_ultimate_gdpr_get_value( 'services', $this->get_request_array(), array() );
		
		$requests = get_option( $this->get_requests_option_key(), array() );
		$requests = apply_filters( 'ct_ultimate_gdpr_controller_unsubscribe_users_unsubscribe_requests_before', $requests, $services_data );
		
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
						
						if ( ! $service->is_subscribeable() ) {
							continue;
						}
						
						$service->set_user( new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) ) )
						        ->unsubscribe();
						
					} catch ( Exception $e ) {
						
						$this->add_view_option(
							'notices',
							array(
								sprintf(
									esc_html__( "There was an error unsubscribing %s from %s service: %s", 'ct-ultimate-gdpr' ),
									$email,
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
		
		$requests = apply_filters( 'ct_ultimate_gdpr_controller_unsubscribe_users_unsubscribe_requests_after', $requests, $services_data );
		update_option( $this->get_requests_option_key(), $requests );
		
		$this->add_view_option(
			'notices',
			array(
				esc_html__( "[Ultimate GDPR] Unsubscription finished!", 'ct-ultimate-gdpr' ),
			)
		);
		
	}
	
	/**
	 * @return bool
	 */
	private function is_admin_request_for_users_unsubscribe() {
		
		if (
			ct_ultimate_gdpr_get_value( 'services', $this->get_request_array() ) &&
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-unsubscribe-send', $this->get_request_array() )
		) {
			return true;
		}
		
		return false;
		
		
	}
	
	/**
	 * @return mixed
	 */
	public function add_menu_page() {
		
		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Unsubscribe', 'ct-ultimate-gdpr' ),
			esc_html__( 'Unsubscribe', 'ct-ultimate-gdpr' ),
			'manage_options',
			$this->get_id(),
			array( $this, 'render_menu_page' )
		);
		
	}
	
	/**
	 * @return string
	 */
	public function get_view_template() {
		
		return 'admin/admin-unsubscribe';
	}
	
	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}
	
	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		
		/*  section */
		
		add_settings_section(
			'ct-ultimate-gdpr-unsubscribe', // ID
			esc_html__( 'Unsubscribe settings', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-unsubscribe' // Page
		);
		
		/*  section fields */
		
		{
			add_settings_field(
				'unsubscribe_hide_unsubscribe_tab', // ID
				esc_html__( "Hide Unsubscribe Tab", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_hide_unsubscribe_tab' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
			add_settings_field(
				'unsubscribe_automated_unsubscribe', // ID
				esc_html__( "Automatically unsubscribe users who confirmed their mail", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_automated_unsubscribe' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
			add_settings_field(
				'unsubscribe_automated_user_email', // ID
				esc_html__( "Automatically send email about unsubscription to users who confirmed their email", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_automated_user_email' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
			add_settings_field(
				'unsubscribe_notify_email', // ID
				esc_html__( "Email to send admin notifications to", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_notify_mail' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
			add_settings_field(
				'unsubscribe_mail_title', // ID
				esc_html__( 'User mail title', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_mail_title' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
			add_settings_field(
				'unsubscribe_mail_content', // ID
				esc_html__( 'User mail content', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_mail_content' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
			add_settings_field(
				'unsubscribe_target_page', // ID
				esc_html__( "Set custom URL to the page containing Ultimate GDPR shortcode as the email confirmation target page (or leave empty for autodetect)", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_unsubscribe_target_page' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);
			
		}
		
	}
	
	/**
	 *
	 */
	public function render_field_unsubscribe_hide_unsubscribe_tab() {
		
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
	public function render_field_unsubscribe_automated_unsubscribe() {
		
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
	public function render_field_unsubscribe_automated_user_email() {
		
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
	public function render_field_unsubscribe_target_page() {
		
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', CT_Ultimate_GDPR_Controller_Unsubscribe::ID )
		);
		
	}
	
	/**
	 *
	 */
	public function render_field_unsubscribe_mail_content() {
		
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
		
		$field_name = $admin->get_field_name( __FUNCTION__ );
		
		wp_editor(
			$admin->get_option_value( $field_name, '', CT_Ultimate_GDPR_Controller_Unsubscribe::ID ),
			$this->get_id() . '_' . $field_name,
			array(
				'textarea_rows' => 20,
				'textarea_name' => $admin->get_field_name_prefixed( $field_name ),
			)
		);
		
	}
	
	/**
	 *
	 */
	public function render_field_unsubscribe_mail_title() {
		
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
		
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', CT_Ultimate_GDPR_Controller_Unsubscribe::ID )
		);
		
	}
	
	/**
	 *
	 */
	public function render_field_unsubscribe_notify_mail() {
		
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', CT_Ultimate_GDPR_Controller_Unsubscribe::ID )
		);
		
	}
	
	/**
	 * @return array|mixed
	 */
	public function get_default_options() {
		
		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
			'unsubscribe_notify_email' => get_bloginfo( 'admin_email' ),
			'unsubscribe_mail_title'   => sprintf( esc_html__( "[Ultimate GDPR] Unsubscribe notice from %s", 'ct-ultimate-gdpr' ), get_bloginfo( 'name' ) ),
			'unsubscribe_mail_content' => sprintf( esc_html__( "Your have been unsubscribed", 'ct-ultimate-gdpr' ) ),
		) );
		
	}
	
}
