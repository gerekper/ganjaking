<?php

/**
 * Class CT_Ultimate_GDPR_Service_Facebook_Pixel
 */
class CT_Ultimate_GDPR_Service_Wp_Job_Manager extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_wp-job-manager/wp-job-manager.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_wp-job-manager/wp-job-manager.php', '__return_true' );
	}

	/**
	 * @return $this
	 */

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'WP Job Manager' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'WP_Job_Manager' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
		$this->collect();
		foreach( $this->collected as $post_id ) {
			$result = wp_delete_post( $post_id, true );
			if ( ! ( $result ) ) {
				throw new Exception( sprintf( esc_html__( "Could not delete posts for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-wpjobmanager_accordion-18', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);


		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpjobmanager_accordion-18' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpjobmanager_accordion-18' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[WP Job Manager] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpjobmanager_accordion-18' // Section
		);

	}

	/**
	 * @param array $cookies
	 * @param bool $force
	 *
	 * @return array
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {

		$cookies_to_block = array();
		if ( $force || $this->get_admin_controller()->get_option_value( 'services_facebook_pixel_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {
			$cookies_to_block = array(
				"wp-job-manager-submitting-job-id",
				"wp-job-manager-submitting-job-key",
			);
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_necessary() ] ) ) {
			$cookies[ $this->get_group()->get_level_necessary() ] = array_merge( $cookies[ $this->get_group()->get_level_necessary() ], $cookies_to_block );
		}

		return $cookies;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'WP Job Manager gathers data entered by users in forms', 'ct-ultimate-gdpr' );
	}

	/**
	 * @return array
	 */
	public function get_group_levels() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_group_levels", array( $this->get_group()->get_level_necessary() ) );
	}

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	public function collect() {

		$user_id = $this->user->get_id();

		$post_ids = get_posts( array(
			'post_type'   => 'job_listing',
			'post_status' => 'any',
			'numberposts' => -1,
			'fields'      => 'ids',
			'author__in'  => $user_id,
		) );

		return $this->set_collected( $post_ids );
	}
}