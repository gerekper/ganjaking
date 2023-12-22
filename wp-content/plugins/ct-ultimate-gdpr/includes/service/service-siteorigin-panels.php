<?php

/**
 * Class CT_Ultimate_GDPR_Service_Siteorigin_Panels
 */
class CT_Ultimate_GDPR_Service_Siteorigin_Panels extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @var string
	 */
	private $siteorigin_consent = '';

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_siteorigin-panels/siteorigin-panels.php', '__return_true' );
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Page Builder by SiteOrigin' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists('SiteOrigin_Panels');
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		if ( $this->is_active() ) {

			add_settings_section(
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}", // ID
				esc_html( $this->get_name() ), // Title
				null, // callback
				$this->front_controller->find_controller('services')->get_id() // Page
			);
		
			add_settings_field(
				"services_{$this->get_id()}_service_name", // ID
				sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
				array( $this, "render_name_field" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);
	
			add_settings_field(
				"services_{$this->get_id()}_description", // ID
				sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
				array( $this, "render_description_field" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);

			add_settings_field(
				"services_{$this->get_id()}_consent_field_privacy_policy", // ID
				sprintf( esc_html__( "[%s] Inject consent to privacy policy", 'ct-ultimate-gdpr' ), $this->get_name() ),
				array( $this, "render_field_services_{$this->get_id()}_consent_field_privacy_policy" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);
		}
    }

	/**
	 *
	 */
	public function render_field_services_siteorigin_panels_consent_field_privacy_policy() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);
	}

	/**
	 * @return mixed
	 */
	public function front_action() {

		$ct_ultimate_gdpr_services = get_option( 'ct-ultimate-gdpr-services' );
		$insert_to_privacy_policy = ( isset( $ct_ultimate_gdpr_services['services_siteorigin_panels_consent_field_privacy_policy'] ) ) 
			? $ct_ultimate_gdpr_services['services_siteorigin_panels_consent_field_privacy_policy'] : NULL;
		$this->siteorigin_consent = ( isset( $ct_ultimate_gdpr_services['services_siteorigin_panels_description'] ) )
			? $ct_ultimate_gdpr_services['services_siteorigin_panels_description'] : '';

		if( !empty( $insert_to_privacy_policy ) && !empty( $this->get_privacy_policy_page_id() ) ) {

			add_filter( 'the_content', function($content) {

				if( is_singular() && get_post()->ID == $this->get_privacy_policy_page_id() ) {
					$content .= '<div>';
					$content .= '<h2>'.$this->get_name().'</h2>';	
					$content .= '<div>'.$this->siteorigin_consent.'</div>';
					$content .= '</div>';
				} 
				return $content;
			}, 9);
		} 
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Page Builder by SiteOrigin does not collect users data, however it may use services from Google Fonts, Google Maps, and embed external videos from Youtube or Vimeo. By using this Site, you signify your acceptance of this policy.', 'ct-ultimate-gdpr' );
	}

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	public function collect() {
		return $this;
	}

}