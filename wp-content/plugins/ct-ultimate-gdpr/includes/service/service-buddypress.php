<?php

/**
 * Class CT_Ultimate_GDPR_Service_Buddypress
 */
class CT_Ultimate_GDPR_Service_Buddypress extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_buddypress/bp-loader.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_buddypress/bp-loader.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;
		$collected = array();

		if ( $this->user->get_id() ) {

			/* bp_groups table */

			$query = $wpdb->prepare( "
				SELECT * FROM {$wpdb->base_prefix}bp_groups
				WHERE creator_id = %d
			",
				$this->user->get_id()
			);

			$groups = $wpdb->get_results( $query, ARRAY_A );

			/* bp_groups_members table */

			$query = $wpdb->prepare( "
				SELECT * FROM {$wpdb->base_prefix}bp_groups_members
				WHERE user_id = %d
			",
				$this->user->get_id()
			);

			$groups_members = $wpdb->get_results( $query, ARRAY_A );

			/* bp_xprofile_data table */

			$query = $wpdb->prepare( "
				SELECT * FROM {$wpdb->base_prefix}bp_xprofile_data
				WHERE user_id = %d
			",
				$this->user->get_id()
			);

			$xprofile_data = $wpdb->get_results( $query, ARRAY_A );

			/* bp_messages_messages table */

			$query = $wpdb->prepare( "
				SELECT m.* FROM {$wpdb->base_prefix}bp_messages_recipients as r
					INNER JOIN {$wpdb->base_prefix}bp_messages_messages as m
					ON r.thread_id = m.thread_id
				WHERE r.user_id = %d
			",
				$this->user->get_id()
			);

			$messages = $wpdb->get_results( $query, ARRAY_A );

			$collected = $groups || $groups_members || $messages || $xprofile_data ?
				compact( 'groups', 'groups_members', 'messages', 'xprofile_data' ) :
				array();

		}

		$this->set_collected( $collected );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'BuddyPress' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'buddypress' );
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

		if ( ! $this->user->get_id() ) {
			throw new Exception( sprintf( esc_html__( "Could not delete user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		$reassign_user = $this->get_target_user_id();

		if ( $reassign_user ) {
			$this->reassign_user( $this->user->get_id(), $reassign_user );

			return;
		}

		$this->delete_user_data( $this->user->get_id() );

	}

	/** Reassign all user data to another user
	 *
	 * @param $user_id
	 * @param int $reassign
	 *
	 * @return void
	 * @throws Exception
	 */
	private function reassign_user( $user_id, $reassign ) {

		global $wpdb;

		/* bp_groups table */

		$result = $wpdb->update(
			"{$wpdb->base_prefix}bp_groups",
			array( 'creator_id' => $reassign ),
			array( 'creator_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_groups_members table */

		$result = $wpdb->update(
			"{$wpdb->base_prefix}bp_groups_members",
			array( 'user_id' => $reassign ),
			array( 'user_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_xprofile_data table */

		$result = $wpdb->update(
			"{$wpdb->base_prefix}bp_xprofile_data",
			array( 'user_id' => $reassign ),
			array( 'user_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_messages_messages table */

		$result = $wpdb->update(
			"{$wpdb->base_prefix}bp_messages_recipients",
			array( 'user_id' => $reassign ),
			array( 'user_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_messages_recipients table */

		$result = $wpdb->update(
			"{$wpdb->base_prefix}bp_messages_messages",
			array( 'sender_id' => $reassign ),
			array( 'sender_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

	}

	/** Deletes all data connected to the user
	 *
	 * @param $user_id
	 *
	 * @throws Exception
	 */
	private function delete_user_data( $user_id ) {

		global $wpdb;

		/* bp_groups table */

		$result = $wpdb->delete(
			"{$wpdb->base_prefix}bp_groups",
			array( 'creator_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_groups_members table */

		$result = $wpdb->delete(
			"{$wpdb->base_prefix}bp_groups_members",
			array( 'user_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_xprofile_data table */

		$result = $wpdb->delete(
			"{$wpdb->base_prefix}bp_xprofile_data",
			array( 'user_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_messages_messages table */

		$result = $wpdb->delete(
			"{$wpdb->base_prefix}bp_messages_recipients",
			array( 'user_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}

		/* bp_messages_recipients table */

		$result = $wpdb->delete(
			"{$wpdb->base_prefix}bp_messages_messages",
			array( 'sender_id' => $user_id )
		);

		if ( false === $result ) {
			throw new Exception( sprintf( esc_html__( "Could not reassign user data for user: %s", 'ct-ultimate-gdpr' ), $this->user->get_email() ) );
		}


	}


	/** Get id of user the posts will be reassign to
	 *
	 * @return int
	 */
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

	/**
	 *
	 */
	public function render_field_forgotten_buddypress_target_user() {

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
			'ct-ultimate-gdpr-services-buddypress_accordion-3', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/* Services section */

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-buddypress_accordion-3' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-buddypress_accordion-3' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-buddypress_accordion-3' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}__consent_field", // ID
			sprintf( esc_html__( '[%s] Inject consent checkbox to all forms', 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_field_services_{$this->get_id()}_consent_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-buddypress_accordion-3'// Section
		);

		/* Forgotten section */

		add_settings_field(
			"forgotten_{$this->get_id()}_target_user", // ID
			sprintf( esc_html__( "[%s] Enter the existing user's email whom the posts will be reassigned to (or leave empty to delete them when forgetting)", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_field_forgotten_{$this->get_id()}_target_user" ), // Callback
			$this->front_controller->find_controller('forgotten')->get_id(), // Page
			'ct-ultimate-gdpr-services-buddypress_accordion-3' // Section
		);

	}

	/**
	 *
	 */
	public function render_field_services_buddypress_consent_field() {

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
	 *
	 */
	public function render_field_services_buddypress_consent_field_position_first() {

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

		$this->add_consent_checkbox_hooks();

		// script for frontend consent validation
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ) );
	}

	public function enqueue_static() {
		wp_enqueue_script( 'ct-ultimate-gdpr-service-buddypress', ct_ultimate_gdpr_url( '/assets/js/buddypress.min.js' ) );
	}

	/**
	 * @return mixed
	 */
	public function add_consent_checkbox() {
		ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-buddypress-consent-field', false ), true );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'BuddyPress gathers users activity in profile pages and groups', 'ct-ultimate-gdpr' );
	}

	/**
	 * Add consent checkboxes to templates
	 */
	private function add_consent_checkbox_hooks() {

		$inject = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );

		if ( ! $inject ) {
			return;
		}

		add_action( 'bp_after_message_reply_box', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'bp_after_messages_compose_content', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'bp_activity_post_form_options', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'bp_after_group_forum_post_new', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'groups_forum_new_topic_after', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'groups_forum_new_reply_after', array( $this, 'add_consent_checkbox' ), 100 );

	}
}