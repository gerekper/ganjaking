<?php

/**
 * Class CT_Ultimate_GDPR_Service_WP_Foro
 */
class CT_Ultimate_GDPR_Service_WP_Foro extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_wpforo/wpforo.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_wpforo/wpforo.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		$collected = array();

		// Table: wp_wpforo_activity
		$query1  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_activity
				WHERE userid = %s or email = %s
			",
			$this->user->get_id(),
			$this->user->get_email()
		);
		$result1 = $wpdb->get_results( $query1, OBJECT );
		if ( count( $result1 ) > 0 ) {
			$collected[] = $result1;
		}

		// Table: wp_wpforo_likes
		$query2  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_likes
				WHERE userid = %s
			",
			$this->user->get_id()
		);
		$result2 = $wpdb->get_results( $query2, OBJECT );
		if ( count( $result2 ) > 0 ) {
			$collected[] = $result2;
		}

		// Table: wp_wpforo_posts
		$query3  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_posts
				WHERE userid = %s or email = %s
			",
			$this->user->get_id(),
			$this->user->get_email()
		);
		$result3 = $wpdb->get_results( $query3, OBJECT );
		if ( count( $result3 ) > 0 ) {
			$collected[] = $result3;
		}

		// Table: wp_wpforo_profiles
		$query4  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_profiles
				WHERE userid = %s
			",
			$this->user->get_id()
		);
		$result4 = $wpdb->get_results( $query4, OBJECT );
		if ( count( $result4 ) > 0 ) {
			$collected[] = $result4;
		}

		// Table: wp_wpforo_subscribes
		$query5  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_subscribes
				WHERE userid = %s
			",
			$this->user->get_id()
		);
		$result5 = $wpdb->get_results( $query5, OBJECT );
		if ( count( $result5 ) > 0 ) {
			$collected[] = $result5;
		}

		// Table: wp_wpforo_topics
		$query6  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_topics
				WHERE userid = %s or email = %s
			",
			$this->user->get_id(),
			$this->user->get_email()
		);
		$result6 = $wpdb->get_results( $query6, OBJECT );
		if ( count( $result6 ) > 0 ) {
			$collected[] = $result6;
		}

		// Table: wp_wpforo_views
		$query7  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_views
				WHERE userid = %s
			",
			$this->user->get_id()
		);
		$result7 = $wpdb->get_results( $query7, OBJECT );
		if ( count( $result7 ) > 0 ) {
			$collected[] = $result7;
		}

		// Table: wp_wpforo_visits
		$query8  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_visits
				WHERE userid = %s
			",
			$this->user->get_id()
		);
		$result8 = $wpdb->get_results( $query8, OBJECT );
		if ( count( $result8 ) > 0 ) {
			$collected[] = $result8;
		}

		// Table: wp_wpforo_votes
		$query9  = $wpdb->prepare( "
				SELECT * FROM 				
				{$wpdb->prefix}wpforo_votes
				WHERE userid = %s
			",
			$this->user->get_id()
		);
		$result9 = $wpdb->get_results( $query9, OBJECT );
		if ( count( $result9 ) > 0 ) {
			$collected[] = $result9;
		}

		/* items table */

		$this->set_collected( $collected );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'wpForo' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'wpForo' );
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

		global $wpdb;

		$reassign_user = $this->get_target_user_id();
		if ( $reassign_user ) {

			// Table: wp_wpforo_activity
			$query1 = $wpdb->prepare( "
				UPDATE 
				{$wpdb->prefix}wpforo_activity
				SET userid = %s, email = '', name = ''
				WHERE userid = %s or email = %s
			",
				$reassign_user,
				$this->user->get_id(),
				$this->user->get_email()
			);
			$wpdb->query( $query1 );

			// Table: wp_wpforo_likes
			$query2 = $wpdb->prepare( "
				UPDATE 
				{$wpdb->prefix}wpforo_likes
				SET userid = %s
				WHERE userid = %s
			",
				$reassign_user,
				$this->user->get_id()
			);
			$wpdb->query( $query2 );

			// Table: wp_wpforo_posts
			$query3 = $wpdb->prepare( "
				UPDATE 
				{$wpdb->prefix}wpforo_posts
				SET userid = %s, email = '', name = ''
				WHERE userid = %s or email = %s
			",
				$reassign_user,
				$this->user->get_id(),
				$this->user->get_email()
			);
			$wpdb->query( $query3 );

			// Table: wp_wpforo_profiles
			$query4 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_profiles
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query4 );

			// Table: wp_wpforo_subscribes
			$query5 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_subscribes
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query5 );

			// Table: wp_wpforo_views
			$query7 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_views
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query7 );
			// Table: wp_wpforo_visits
			$query8 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_visits
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query8 );

			// Table: wp_wpforo_votes
			$query9 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_votes
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query9 );


		}
		else {

			// Table: wp_wpforo_activity
			$query1 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_activity
				WHERE userid = %s or email = %s
			",
				$this->user->get_id(),
				$this->user->get_email()
			);
			$wpdb->query( $query1 );

			// Table: wp_wpforo_likes
			$query2 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_likes
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query2 );

			// Table: wp_wpforo_posts
			$query3 = $wpdb->prepare( "
				DELETE FROM 
				{$wpdb->prefix}wpforo_posts
				WHERE userid = %s or email = %s
			",
				$this->user->get_id(),
				$this->user->get_email()
			);
			$wpdb->query( $query3 );

			// Table: wp_wpforo_profiles
			$query4 = $wpdb->prepare( "
				DELETE FROM 				
				{$wpdb->prefix}wpforo_profiles
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query4 );

			// Table: wp_wpforo_subscribes
			$query5 = $wpdb->prepare( "
				DELETE FROM 				
				{$wpdb->prefix}wpforo_subscribes
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query5 );

			// Table: wp_wpforo_topics
			$query6 = $wpdb->prepare( "
				DELETE FROM 				
				{$wpdb->prefix}wpforo_topics
				WHERE userid = %s or email = %s
			",
				$this->user->get_id(),
				$this->user->get_email()
			);
			$wpdb->query( $query6 );

			// Table: wp_wpforo_views
			$query7 = $wpdb->prepare( "
				DELETE FROM 				
				{$wpdb->prefix}wpforo_views
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query7 );

			// Table: wp_wpforo_visits
			$query8 = $wpdb->prepare( "
				DELETE FROM 				
				{$wpdb->prefix}wpforo_visits
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query8 );

			// Table: wp_wpforo_votes
			$query9 = $wpdb->prepare( "
				DELETE FROM 				
				{$wpdb->prefix}wpforo_votes
				WHERE userid = %s
			",
				$this->user->get_id()
			);
			$wpdb->query( $query9 );
		}
	}

	public function get_target_user_id() {

		$user_id = 0;

		$user_email = $this->get_admin_controller()->get_option_value(
			"forgotten_{$this->get_id()}_target_user",
			0,
			$this->front_controller->find_controller('forgotten')->get_id()
		);

		if ( $user_email ) {
			$user = get_user_by( 'email', $user_email );
			if ( $user ) {
				$user_id = $user->ID;
			}
		}

		return $user_id;
	}

	public function render_field_forgotten_wp_foro_target_user() {

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
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}", // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/* Services section */

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}" // ID
		);

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}" // ID
		);

		add_settings_field(
			"services_{$this->get_id()}__consent_field", // ID
			sprintf( esc_html__( '[%s] Inject consent checkbox to all forms', 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}" // ID
		);

		add_settings_field(
			"services_{$this->get_id()}__consent_field", // ID
			esc_html( $this->get_name() ), // Title
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}" // ID
		);

		add_settings_field(
			"forgotten_{$this->get_id()}_target_user", // ID
			sprintf( esc_html__( "[%s] Enter the existing user's email whom the posts will be reassigned to (or leave empty to delete them when forgetting)", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_field_forgotten_{$this->get_id()}_target_user" ), // Callback
			$this->front_controller->find_controller('forgotten')->get_id(), // Page
			$this->front_controller->find_controller('forgotten')->get_id() // Section
		);

	}

	public function render_field_breach_services() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	public function breach_recipients_filter( $recipients ) {

		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}

		return array_merge( $recipients, $this->get_all_users_emails() );
	}

	public function get_all_users_emails() {
		global $wpdb;
		$emails = array();

		$query = $wpdb->get_results(
			$wpdb->prepare( "
		SELECT DISTINCT 
		email 
		FROM 
		{$wpdb->prefix}wpforo_posts 
		WHERE email != %s;
		", "" )

		);

		foreach ( $query as $row ) {
			$emails[] = $row->email;
		}

		return $emails;
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
		return esc_html__( 'wpForo gathers users activity in forums', 'ct-ultimate-gdpr' );
	}


	public function render_field_services_wp_foro_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

}