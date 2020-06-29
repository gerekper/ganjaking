<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Class CT_Ultimate_GDPR_Data_Access
*
*/
class CT_Ultimate_GDPR_Controller_Data_Access extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	*
	*/
	const ID = 'ct-ultimate-gdpr-dataaccess';

	/**
	* @var
	*/
	private $services;

	/**
	*
	*/
	public function init() {

		$this->services = array();
		add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'legacy_update' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_data_access', array( $this, 'front_action' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_data_access', array( $this, 'send_ajax_response' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_data_access', array( $this, 'front_action' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_data_access', array( $this, 'send_ajax_response' ) );

	}

	/**
	* @return string
	*/
	public function get_id() {
		return self::ID;
	}

	/**
	* @return mixed
	*/
	private function collect_user_data() {

		$services = CT_Ultimate_GDPR_Model_Services::instance()->get_services( $this->options );

		/** @var CT_Ultimate_GDPR_Service_Abstract $service */
		foreach ( $services as $service ) {

			$this->services[ $service->get_id() ] = $service->set_user( $this->user )
					->collect();

		}

		return $this;

	}

	/**
	* @return array
	*/
	public function render_user_data_email_attachments() {

		$attachments            = array();
		$file_combined          = $this->create_attachment_file( 'all-services', 'txt' );
		$file_human_readable    = $this->create_attachment_file( 'human-readable', 'txt' );

		/** @var CT_Ultimate_GDPR_Service_Abstract $service */
		foreach ( $this->services as $service ) {

			$rendered       = $service->render_collected( false  );
			$rendered_human = $service->render_collected( true );

			if ( ! $rendered ) {
				continue;
			}

			$file = $this->create_attachment_file( $service->get_id(), 'json' );

			// pushing data to temp file created via WP
			file_put_contents( $file, $rendered );

			$underline          = str_repeat( '-', strlen( $service->get_name() ) );
			$combined_rendered  = $service->get_name() . PHP_EOL . $underline . PHP_EOL . PHP_EOL . $rendered . PHP_EOL . PHP_EOL;
			$combined_human     = $service->get_name() . PHP_EOL . $underline . PHP_EOL . PHP_EOL . $rendered_human . PHP_EOL . PHP_EOL;

			// pushing data to temp file created via WP
			file_put_contents( $file_combined, $combined_rendered, FILE_APPEND );
			file_put_contents( $file_human_readable, $combined_human, FILE_APPEND );

			$attachments[] = $file;
		}

		$attachments[] = $file_combined;
		$attachments[] = $file_human_readable;

		return $attachments;

	}

	/**
	*
	* Use Wordpress built in wp_tempnam to create attachment
	*
	* @param $name
	* @param $extension
	*
	* @return mixed
	*/
	private function create_attachment_file( $name, $extension ) {

		$file       = wp_tempnam( $name );
		$file_new   = str_replace( ".tmp", ".$extension", $file );
		rename( $file, $file_new ) ;

		return $file_new;

	}

	/**
	* @param $to
	* @param $subject
	* @param $message
	* @param array $attachments
	*
	* @return CT_Ultimate_GDPR_Controller_Data_Access
	*/
	private function send_mail( $to, $subject, $message, $attachments = array() ) {

		$site_name  = get_bloginfo( 'name' );
		$site_email = get_bloginfo( 'admin_email' );

		$headers = array(
			"From: $site_name <$site_email>",
			"Content-Type: text/html; charset=UTF-8"
		);

		wp_mail( $to, $subject, $message, $headers, $attachments );

		foreach ( $attachments as $attachment ) {
			unlink( $attachment );
		}

		return $this;
	}

	/**
	*
	*/
	public function front_action() {

		if ( $this->is_user_request_for_user_data() ) {
			$this->process_request_for_user_data();
		}

	}

	/**
	* @return bool
	*/
	private function process_request_for_user_data() {

		$email = sanitize_email( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-email', $this->get_request_array() ) );

		if ( ! $email ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Wrong email!", 'ct-ultimate-gdpr' ) );

			return false;
		}

		if ( ! ct_ultimate_gdpr_recaptcha_verify() ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Recaptcha error!", 'ct-ultimate-gdpr' ) );

			return false;
		}

		$user = new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) );
		$this->set_user( $user );
		$this->save_user_request();

		$notify_mail = $this->get_option( 'dataaccess_notify_mail' );
		if ( $notify_mail ) {
			wp_mail(
				$notify_mail,
				esc_html__( "[Ultimate GDPR] New Data Access request", 'ct-ultimate-gdpr' ),
				sprintf(
					esc_html__( "There is a new data access request from email %s. See details at %s", 'ct-ultimate-gdpr' ),
					$email,
					admin_url( 'admin.php?page=ct-ultimate-gdpr-dataaccess' )
				),
				'Content-Type: text/html; charset=UTF-8'
			);
		}

		CT_Ultimate_GDPR_Model_Front_View::instance()->add( 'notices', esc_html__( "Your request was sent!", 'ct-ultimate-gdpr' ) );

		if ( $this->get_option( 'dataaccess_automated_dataaccess' ) ) {
			$this->users_send_email( array( $email ) );
		}

		return true;

	}

	/**
	*
	*/
	private function is_user_request_for_user_data() {

		$email      = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-email', $this->get_request_array() );
		$consent    = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-consent-data-access', $this->get_request_array() );
		$submit     = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-submit', $this->get_request_array() );

		if ( ! $email || ! $consent || ! $submit ) {
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
			ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-send', $this->get_request_array() ) &&
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
				ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-remove', $this->get_request_array() ) &&
				ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() )
			) ||
			(
				ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-remove-all-sent', $this->get_request_array() )
			)
		) {
			return true;
		}

		return false;

	}

	/**
	* @return bool
	*/
	private
	function is_admin_request_for_user_data() {

		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-details', $this->get_request_array() ) ) {
			return true;
		}

		return false;
	}

	/**
	* @param array $emails
	*/
	private function users_send_email( $emails = array() ) {

		$emails     = $emails ? $emails : ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array() );
		$requests   = get_option( $this->get_requests_option_key(), array() );

		foreach ( $emails as $email ) {

			$email = sanitize_email( $email ) ;
			$this->set_user( new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $email ) ) );
			$this->collect_user_data();
			$this->send_mail(
				$email,
				$this->get_option( 'dataaccess_mail_title' ),
				$this->get_option( 'dataaccess_mail_content' ),
				$this->render_user_data_email_attachments()
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

		$remove_all_sent    = ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-remove-all-sent', $this->get_request_array() );
		$emails             = ct_ultimate_gdpr_get_value( 'emails', $this->get_request_array(), array() );
		$requests           = get_option( $this->get_requests_option_key(), array() );

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
	*
	*/
	private
	function user_data_preview() {

		$email = ct_ultimate_gdpr_get_value( 'email', $this->get_request_array() );
		$this->set_user( new CT_Ultimate_GDPR_Model_User( array(
			'user_email' => $email,
		) ) );

		$this->collect_user_data();

		$service_data = array();

		/** @var CT_Ultimate_GDPR_Service_Abstract $service */
		foreach ( $this->services as $service ) {
			$service_data[ $service->get_name() ] = $service->render_collected( false );
		}

		$view_options = array(
			'email' => $email,
			'data'  => $service_data,
		);
		$this->set_view_options( $view_options );

	}

	/**
	*/
	protected
	function admin_page_action() {

		if ( $this->is_admin_request_for_user_data() ) {
			$this->user_data_preview();

			// custom view
			return;

		}

		if ( $this->is_admin_request_for_users_send_email() ) {
			$this->users_send_email();
		}

		if ( $this->is_admin_request_for_users_remove() ) {
			$this->users_remove_from_list();
		}

		/* Default settings page */
		$this->add_view_option( 'data', $this->get_all_requested_users_data() );

	}

	/**
	* @return array
	*/
	private
	function get_all_requested_users_data() {

		$data           = array();
		$requests       = get_option( $this->get_requests_option_key(), array() );
		$date_format    = apply_filters( 'ct_ultimate_gdpr_date_format', 'd M Y H:m:s' );

		if ( ! $requests ) {
			return $data;
		}

		foreach ( $requests as $request ) {

			$user   = new CT_Ultimate_GDPR_Model_User( array( 'user_email' => $request['user_email'] ) );
			$datum  = array(
				'id'            => $user->get_id(),
				'user_email'    => $request['user_email'],
				'date'          => date( $date_format, $request['date'] ),
				'status'        => $request['status'] ? date( $date_format, $request['date'] ) : esc_html__( "Not sent", 'ct-ultimate-gdpr' ),
			);
			$data[] = $datum;

		}

		// recent go first
		$data = array_reverse( $data );

		return $data;

	}

	/**
	*
	*/
	private
	function save_user_request() {

		$requests                               = get_option( $this->get_requests_option_key(), array() );
		$requests[ $this->user->get_email() ]   = array(
			'id'         => $this->user->get_id(),
			'user_email' => $this->user->get_email(),
			'date'       => time(),
			'status'     => 0,
		);
		update_option( $this->get_requests_option_key(), $requests );

	}

	/**
	* @return mixed
	*/
	public
	function add_menu_page() {

		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Data Access', 'ct-ultimate-gdpr' ),
			esc_html__( 'Data Access', 'ct-ultimate-gdpr' ),
			'manage_options',
			'ct-ultimate-gdpr-dataaccess',
			array( $this, 'render_menu_page' )
		);

	}

	/**
	* @return string
	*/
	public
	function get_view_template() {

		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-data-access-details', $this->get_request_array() ) ) {
		    return 'admin/admin-data-access-user-details';
		}

		return 'admin/admin-data-access';
	}

	/**
	* Do actions in admin (general)
	*/
	public
	function admin_action() {
	}

	/**
	* @return mixed
	*/
	public function add_option_fields() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		/* Data access section */

		add_settings_section(
			'ct-ultimate-gdpr-dataaccess', // ID
			esc_html__( 'Data access settings', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			'ct-ultimate-gdpr-dataaccess' // Page
		);

		/* Data accesss section fields */

		{

			add_settings_field(
				'dataaccess_automated_dataaccess', // ID
				esc_html__( "Automatically send data to all users on their request", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_dataaccess_automated_dataaccess' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'dataaccess_remove_empty_fields', // ID
				esc_html__( "Remove empty fields in Data Access Request List", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_dataaccess_remove_empty_fields' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'dataaccess_notify_email', // ID
				esc_html__( "Email to send new request notifications to", 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_dataaccess_notify_mail' ), // Callback
				'ct-ultimate-gdpr-dataaccess', // Page
				'ct-ultimate-gdpr-dataaccess' // Section
			);

			add_settings_field(
				'dataaccess_mail_title', // ID
				esc_html__( 'Mail title', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_dataaccess_mail_title' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

			add_settings_field(
				'dataaccess_mail_content', // ID
				esc_html__( 'Mail content', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_dataaccess_mail_content' ), // Callback
				$this->get_id(), // Page
				$this->get_id() // Section
			);

		}

	}

	public function render_field_dataaccess_automated_dataaccess() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	public function render_field_dataaccess_remove_empty_fields() {

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
	public
	function render_field_dataaccess_mail_content() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );

		wp_editor(
			$admin->get_option_value( $field_name, '', $this->get_id() ),
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
	public
	function render_field_dataaccess_mail_title() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', $this->get_id() )
		);

	}

	/**
	*
	*/
	public
	function render_field_dataaccess_notify_mail() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
		"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', $this->get_id() )
		);

	}

	/**
	* @return array|mixed
	*/
	public
	function get_default_options() {

		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
			'dataaccess_notify_email'   => get_bloginfo( 'admin_email' ),
			'dataaccess_mail_title'     => sprintf( esc_html__( "[Ultimate GDPR] Data request results from %s", 'ct-ultimate-gdpr' ), get_bloginfo( 'name' ) ),
			'dataaccess_mail_content'   => sprintf( esc_html__( "Please find data attached", 'ct-ultimate-gdpr' ) ),
		) );

	}

	/**
	* @return string
	*/
	public
	function get_requests_option_key() {
		return $this->get_id() . '-requests';
	}


	/**
	* Update options for legacy structure
	*/
	public
	function legacy_update() {

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
