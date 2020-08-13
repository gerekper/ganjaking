<?php

/**
 * Interface CT_Ultimate_GDPR_Collector_Interface
 */
class CT_Ultimate_GDPR_Service_WP_Comments extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return $this
	 */
	public function collect() {

		/**
		 * WPML support
		 *
		 * @var SitePress $sitepress
		 */
		global $sitepress;
		$sitepress && remove_filter( 'comments_clauses', array( $sitepress, 'comments_clauses' ), 10, 2 );

		// get comments
		$comments = get_comments( array(
			'author_email' => $this->user->get_email(),
		) );

		/**
		 * WPML support
		 */
		$sitepress && add_filter( 'comments_clauses', array( $sitepress, 'comments_clauses' ), 10, 2 );

		// set to collected array
		return $this->set_collected( $comments );

	}

	/**
	 * @return mixed|string
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", "WP Comments" );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true && $this->is_active();
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

		$errors = array();
		$this->collect();

		/** @var WP_Comment $post */
		foreach ( $this->collected as $post ) {

			$result = wp_update_comment( array(
				'comment_ID'           => $post->comment_ID,
				'comment_author'       => esc_html__( 'Anonymous', 'ct-ultimate-gdpr' ),
				'comment_author_email' => '',
				'comment_author_url'   => '',
				'comment_author_ip'    => '',
			) );

			if ( ! $result ) {
				$errors[] = $post->comment_ID;
			}
		}

		if ( ! empty( $errors ) ) {
			throw new Exception( sprintf( esc_html__( "Could not update comment data for comments: %s", 'ct-ultimate-gdpr' ), implode( ', ', $errors ) ) );
		}
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-wpcomments_accordion-17', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);
/*
		add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpcomments_accordion-17' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpcomments_accordion-17' // Section
        );
		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[WP Comments] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpcomments_accordion-17' // Section
		);

		add_settings_field(
			'services_wp_comments_consent_field', // ID
			esc_html__( '[WP Comments] Inject consent checkbox to comments fields', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_wp_comments_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpcomments_accordion-17' // Section
		);

	}

	/**
	 *
	 */
	public function render_field_services_wp_comments_consent_field() {

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
	 * @return void
	 */
	public function init() {
		add_action( 'pre_comment_on_post', array( $this, 'pre_comment_on_post_action' ) );
	}

	/**
	 * @return mixed
	 */
	public function front_action() {
		add_filter( 'comment_form_submit_field', array( $this, 'comment_form_submit_field_filter' ), 100 );
	}

	/**
	 * @param $markup
	 *
	 * @return string
	 */
	public function comment_form_submit_field_filter( $markup ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_wp_comments_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		if( $inject ) {
			$markup = ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-wp-comments-consent-field', false ) ) . $markup;
		}

		return $markup;
	}

	/**
	 * @param $comment_id
	 */
	public function pre_comment_on_post_action( $comment_id ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_wp_comments_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if( $inject && ! ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-consent-field', $_POST ) ) {
			die ( __( 'Please give consent to collect your data', 'ct-ultimate-gdpr' ) );
		} elseif ( $inject ) {
			$this->log_user_consent();
		}

	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'WordPress Comments are data entered by users in comments', 'ct-ultimate-gdpr' );
	}

}