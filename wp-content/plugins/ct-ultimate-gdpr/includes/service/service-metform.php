<?php

/**
 * Class CT_Ultimate_GDPR_Service_Metform
 */
class CT_Ultimate_GDPR_Service_Metform extends CT_Ultimate_GDPR_Service_Abstract {

	public $inject;
	public $privacy_content;

	/**
	 * @return void
	 */
	public function init() {

		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_metform/metform.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_metform/metform.php', '__return_true' );

		$this->inject = $this->get_admin_controller()->get_option_value( 'services_metform_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$this->privacy_content = $this->get_admin_controller()->get_option_value( 'services_metform_privacy_policy', false, $this->front_controller->find_controller('services')->get_id() );
	}

	/**
	 * @return $this
	 */
	public function collect() {
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Metform' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return (in_array( 'metform/metform.php', apply_filters('active_plugins', get_option('active_plugins')) ));
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
		return false;
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		if ( $this->is_active() ) {

			add_settings_section(
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}", // ID
				esc_html( $this->get_name() ).  ' <small><strong>('.__('Elementor addon', 'ct-ultimate-gpdr').')</strong></small>', // Title
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
				"services_{$this->get_id()}_privacy_policy", // ID
				sprintf( esc_html__( "[%s] Privacy policy content", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
				array( $this, "render_privacy_field" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);

			add_settings_field(
				"services_{$this->get_id()}_privacy_field", // ID
				sprintf(
					esc_html__( "[%s] Inject privacy policy", 'ct-ultimate-gdpr' ),
					$this->get_name()
				),
				array( $this, "render_field_services_{$this->get_id()}_privacy_field" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);

			add_settings_field(
				"services_{$this->get_id()}_consent_field", // ID
				sprintf(
					esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
					$this->get_name()
				),
				array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);

			add_settings_field(
				"services_{$this->get_id()}_consent_field_position_first", // ID
				sprintf(
					esc_html__( '[%s] Inject consent checkbox as the first field instead of the last', 'ct-ultimate-gdpr' ),
					$this->get_name()
				),
				array( $this, "render_field_services_{$this->get_id()}_consent_field_position_first" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}",
				array( 
					"hint" => __('If it does not work, try disabling the inject consent to all form field above, then enable again together with this field.', 'ct-ultimate-gdpr')
				),
			);

			add_settings_field(
				"services_{$this->get_id()}_hide_from_forgetme_form", // ID
				sprintf( 
					esc_html__( "[%s] Hide from Forget Me Form", 'ct-ultimate-gdpr' ), 
					$this->get_name() 
				),
				array( $this, "render_hide_from_forgetme_form" ), // Callback
				$this->front_controller->find_controller('services')->get_id(), // Page
				"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
			);
		
		}
	}

	/**
	 * render privacy checkbox field
	 */
	public function render_field_services_metform_privacy_field() {
		$admin = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf( "<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);
	}

	/**
	 * render consent field
	 */
	public function render_field_services_metform_consent_field() {
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
	 * render consent position first
	 */
	public function render_field_services_metform_consent_field_position_first() {
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

		if( $this->inject ) {
			add_filter( 'elementor/frontend/builder_content_data', array( $this, 'add_consent_field'), 100, 2);
		} else {
			add_filter( 'elementor/frontend/builder_content_data', array( $this, 'remove_consent_field'), 100, 2);
		}

		$inject_privacy = $this->get_admin_controller()->get_option_value( 'services_metform_privacy_field', false, $this->front_controller->find_controller('services')->get_id() );
		
		if( $inject_privacy ) {
			add_filter( 'the_content', function($content) {
				if( is_singular() && ( get_post()->ID == $this->get_privacy_policy_page_id() || get_post()->ID == get_option('wp_page_for_privacy_policy')  ) ) {
					$content .= '<div>';
					$content .= '<h2>'.$this->get_name().'</h2>';	
					$content .= '<div>'.$this->privacy_content.'</div>';
					$content .= '</div>';
				} 
				return $content;
			}, 9);
		}
	}

	/**
	 * @param $data
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function add_consent_field( $data, $post_id ) {

		$widgetType = array();
		$position_first = $this->get_admin_controller()->get_option_value( 'services_metform_consent_field_position_first', false, $this->front_controller->find_controller('services')->get_id() );
		$elements = $data[0]['elements'][0]['elements'];
		
		if( $elements ) {
			foreach ($elements as $element) {
				$widgetType[] = $element['widgetType'];
			}
		}
		// if gdpr consent is not in the form, then insert.
		if( !in_array('mf-gdpr-consent', $widgetType ) ) {
	
			if( get_post_type($post_id) === 'metform-form') {
				
				$consent_data = array(
					'id' => 'ct-consent',
					'elType' => 'widget',
					'settings' => array( 
						'mf_input_label' => 'GDPR Consent',
						'mf_input_name' => 'mf-gdpr-consent',
						'mf_gdpr_consent_option_text' => __('I consent to the storage of my data according to the Privacy Policy', 'ct-ultimate-gdpr'),
						'mf_input_validation_warning_message' => __('This field is required.', 'ct-ultimate-gdpr'),
					),
					'elements' => array(),
					'widgetType' => 'mf-gdpr-consent',
				);

				if( $position_first ) {
					array_unshift($data[0]['elements'][0]['elements'], $consent_data);
				} else {
					$submit_btn = array_pop($data[0]['elements'][0]['elements']);			
					array_push($data[0]['elements'][0]['elements'], $consent_data);
					array_push($data[0]['elements'][0]['elements'], $submit_btn); 
				} 
				// Update all metform forms to add GDPR consent field
				update_post_meta( $post_id, '_elementor_data', json_encode($data));
			}
		} 
		return $data;
	}
	
	/**
	 * @param $data
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function remove_consent_field( $data, $post_id ) {

		$widgetID = array();
		$new_data = array();
		$elements = $data[0]['elements'][0]['elements'];

		if( $elements ) {
			foreach( $elements as $element ) {
				$widgetID[] = $element['id'];
			} 
		}

		// if gdpr consent is in the form
		if ( in_array('ct-consent', $widgetID) ) {

			if( get_post_type($post_id) === 'metform-form' ) {

				foreach ($data[0]['elements'][0]['elements'] as $element) {
					if( $element['id'] == 'ct-consent' )
						continue;
					$new_data[] = $element;
				} 
				$data[0]['elements'][0]['elements'] = $new_data;	
				update_post_meta( $post_id, '_elementor_data', json_encode($data));
			}
		}

		return $data;
	}
 
	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'MetForm is an advanced and flexible form builder for Elementor.', 'ct-ultimate-gdpr' );
	}

	/**
	 * Render field for privacy policy
	*/
	public function render_privacy_field() {

		$admin      = $this->get_admin_controller();
		$field_name = "services_{$this->get_id()}_privacy_policy";

		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='10' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, $this->get_privacy_policy() )
		);
	}

	/**
	 * Get privacy policy
	 *
	 * @return string
	 */
	public function get_privacy_policy() {

		$privacy_policy   = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_privacy_policy", '', $this->front_controller->find_controller('services')->get_id() );
		$return           = $privacy_policy ? $privacy_policy : $this->get_default_privacy_policy();
		$return           = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_privacy_policy", $return );
		
		return $return;
	}

	/**
	 * @return string
	 */
	protected function get_default_privacy_policy() {
		return esc_html__( 'MetForm might collect data such as Form data and Browser data; IP addresses, User agent.', 'ct-ultimate-gdpr' );
	}

}
