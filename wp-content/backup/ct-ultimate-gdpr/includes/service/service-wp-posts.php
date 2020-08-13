<?php

/**
 * Interface CT_Ultimate_GDPR_Service_WP_Posts
 */
class CT_Ultimate_GDPR_Service_WP_Posts extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return $this
	 */
	public function collect() {

		$posts = $this->user->get_id() ?
			get_posts( array(
				'numberposts' => - 1,
				'post_type'   => 'any',
				'author'      => $this->user->get_id()
			) ) :
			array();

		return $this->set_collected( $posts );

	}

	/**
	 * @return mixed|string
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name",  "WordPress Posts" );
	}

	/**
	 * @return mixed
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

		/* Get id of user to set posts authorship to */
		$user_id = $this->user->get_target_user_id();

		$options = $this->get_admin_controller()->get_options( $this->front_controller->find_controller('forgotten')->get_id() );

		/** @var WP_Post $post */
		foreach ( $this->collected as $post ) {

			if ( ! empty( $options['forgotten_wp_posts_delete'] ) ) {

				$result = wp_delete_post( $post->ID, true );

			} else {

				$result = wp_update_post( array(
					'ID'          => $post->ID,
					'post_author' => $user_id,
				) );

			}

			if ( ! $result ) {
				$errors[] = $post->post_title ? $post->post_title : $post->post_name;
			}
		}

		if ( ! empty( $errors ) ) {
			throw new Exception( sprintf( esc_html__( "Could not update post data for posts: %s", 'ct-ultimate-gdpr' ), implode( ', ', $errors ) ) );
		}
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-wpposts_accordion-19', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			'forgotten_wp_posts_delete', // ID
			esc_html__( '[WP Posts] Delete posts instead of reassigning to a different user?', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_forgotten_wp_posts_delete' ), // Callback
			$this->front_controller->find_controller('forgotten')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpposts_accordion-19' // Section
		);

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpposts_accordion-19' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpposts_accordion-19' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[WordPress Posts] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpposts_accordion-19' // Section
		);

		add_settings_field(
			'forgotten_wp_posts_target_user', // ID
			esc_html__( '[WP Posts] Enter the user email whom the posts will be reassigned to (default is admin)', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_forgotten_wp_posts_target_user' ), // Callback
			$this->front_controller->find_controller('forgotten')->get_id(), // Page
			$this->front_controller->find_controller('forgotten')->get_id() // Section
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_wp_posts_target_user() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name, get_bloginfo( 'admin_email' ) )
		);

	}

	/**
	 *
	 */
	public function render_field_forgotten_wp_posts_delete() {

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
		return esc_html__( 'WordPress posts author data', 'ct-ultimate-gdpr' );
	}

}