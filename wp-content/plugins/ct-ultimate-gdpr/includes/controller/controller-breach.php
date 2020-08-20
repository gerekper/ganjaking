<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Breach
 */
class CT_Ultimate_GDPR_Controller_Breach extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-breach';

	/**
	 * Get unique controller id (page name, option id)
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Init after construct
	 */
	public function init() {
	}

	/**
	 * Do actions on frontend
	 */
	public function front_action() {
	}

	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}

	/**
	 * Do actions on current admin page
	 */
	protected function admin_page_action() {

		if ( $this->is_send_request() ) {
			$this->send();
		}

		if ( $this->is_send_screen_request() ) {
			$this->add_view_option( 'recipients', $this->get_recipients() );
		}

	}

	/**
	 * @return array
	 */
	private function get_recipients() {

		$services   = $this->get_option( 'breach_services', array() );
		$recipients = array();

		foreach ( $services as $service_id ) {
			$service_recipients = apply_filters( "ct_ultimate_gdpr_breach_recipients_$service_id", array() );
			$recipients         = array_merge( $recipients, $service_recipients );
		}

		return array_unique( $recipients );
	}

	/**
	 * Send emails
	 */
	private function send() {

		$recipients = $this->get_recipients();
		$headers = array(
			'Content-Type: text/html; charset=UTF-8'
		);

		foreach ( $recipients as $recipient ) {

			wp_mail(
				$recipient,
				$this->get_option( 'breach_mail_title' ),
				$this->get_option( 'breach_mail_content' ),
				$headers
			);

		}

	}

	/**
	 * Add menu page (if not added in admin controller)
	 */
	public function add_menu_page() {
		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Data Breach', 'ct-ultimate-gdpr' ),
			esc_html__( 'Data Breach', 'ct-ultimate-gdpr' ),
			'manage_options',
			self::ID,
			array( $this, 'render_menu_page' )
		);
	}

	/**
	 * Get view template string
	 * @return string
	 */
	public function get_view_template() {
		if ( $this->is_send_screen_request() || $this->is_send_request() ) {
			return 'admin/admin-breach-send';
		}

		return 'admin/admin-breach';
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		/* Section */

		add_settings_section(
			'ct-ultimate-gdpr-breach_section-2', // ID
			esc_html__( 'Data Breach', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			$this->get_id() // Page
		);

		/* Section fields */

		add_settings_field(
			'breach_mail_title', // ID
			esc_html__( 'Mail title', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_breach_mail_title' ), // Callback
			$this->get_id(), // Page
			'ct-ultimate-gdpr-breach_section-2'
		);

		add_settings_field(
			'breach_mail_content', // ID
			esc_html__( 'Mail content', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_breach_mail_content' ), // Callback
			$this->get_id(), // Page
			'ct-ultimate-gdpr-breach_section-2'
		);

		add_settings_field(
			'breach_services_header',
			esc_html__( 'Collect user emails from services', 'ct-ultimate-gdpr' ),
			'__return_empty_string',
			CT_Ultimate_GDPR_Controller_Breach::ID,
			'ct-ultimate-gdpr-breach_section-2'
		);




	}

	/**
	 *
	 */
	public function render_field_breach_mail_content() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

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
	public function render_field_breach_mail_title() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', $this->get_id() )
		);

	}

	/**
	 * @return bool|mixed
	 */
	private function is_send_request() {

		return ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-breach-send-submit', $_POST );
	}

	/**
	 * @return bool|mixed
	 */
	private function is_send_screen_request() {

		return ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-breach-send-screen-submit', $_POST );
	}

	/**
	 * @return array
	 */
	public function get_default_options() {

		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
			'breach_mail_title' => sprintf(
				esc_html__( '[Ultimate GDPR] Data breach information from %s', 'ct-ultimate-gdpr' ),
				get_bloginfo( 'name' )
			),
			'breach_mail_content' => esc_html__( 'There was a data breach on our page...', 'ct-ultimate-gdpr' )
		) );

	}

}
