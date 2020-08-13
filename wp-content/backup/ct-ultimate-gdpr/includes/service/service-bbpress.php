<?php

/**
 * Class CT_Ultimate_GDPR_Service_bbPress
 */
class CT_Ultimate_GDPR_Service_bbPress extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_action( 'bbp_filter_anonymous_post_data', array( $this, 'bbpress_validation_filter' ) );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_bbpress/bbpress.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_bbpress/bbpress.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		global $wpdb;
		$collected = array();
		$user_id = $this->user->get_id();
		if ( $user_id != 0 ) {
			$args = array(
				'author' => $user_id,
				'post_status' => 'any',
				'post_type' => array ( 'forum', 'topic', 'reply', 'topic-tag' ),
				'numberposts' => -1,
			);
			$ids = get_posts($args);
		} else {
			$email = $this->user->get_email();
			$args = array(
				'meta_query' => array(
					array(
						'key' => '_bbp_anonymous_email',
						'value' => $email,
					)
				),
				'post_type' => array ( 'forum', 'topic', 'reply', 'topic-tag' ),
				'post_status' => 'any',
				'numberposts' => -1,
			);
			$ids = get_posts( $args );
		}
		if( !is_wp_error( $ids ) ) {
			$collected = $ids;
		}

		$this->set_collected( $collected );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'bbPress' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'bbPress' );
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

		$this->collect();
		$reassign_user = $this->get_target_user_id();
		if ( $reassign_user ) {
			/** @var object wp_post $entry */
			foreach ( $this->collected as $entry ) {
				$wpdb->update(
					"{$wpdb->base_prefix}posts",
					array( 'post_author' => $reassign_user ),
					array ( 'ID' => $entry->ID )
				);
			}
		} else {
			/** @var object wp_post $entry */
			foreach($this->collected as $entry) {
				wp_delete_post( $entry->ID );
			}
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

	public function render_field_forgotten_bbpress_target_user() {

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

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}" // ID
		);*/

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
			esc_html__( $this->get_name() ),
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
		$args = array(
			'meta_query' => array(
				array(
					'key' => '_bbp_anonymous_email',
					'value' => array(''),
					'compare' => 'NOT IN'
				)
			),
			'post_type' => array ( 'forum', 'topic', 'reply', 'topic-tag' ),
			'post_status' => 'any',
			'numberposts' => -1,
		);
		$posts = get_posts($args);
		$emails = array();
		foreach($posts as $post) {
			$emails[] = get_post_meta($post->ID, '_bbp_anonymous_email', true);
		}
		return $emails;
	}

	/**
	 * @return mixed
	 */
	public function front_action() {
		$this->add_consent_checkbox_hooks();
	}

	/**
	 * @return mixed
	 */
	public function add_consent_checkbox() {
		ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-bbpress-consent-field', false ), true );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'bbPress gathers users activity in forums', 'ct-ultimate-gdpr' );
	}

	/**
	 * Add consent checkboxes to templates
	 */
	private function add_consent_checkbox_hooks() {
		$inject = $this->get_admin_controller()->get_option_value( "services_{$this->get_id()}_consent_field", false, $this->front_controller->find_controller('services')->get_id() );
		if ( ! $inject ) {
			return;
		}
		add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $this, 'add_consent_checkbox' ), 100 );
		add_action( 'bbp_theme_before_forum_form_submit_wrapper', array( $this, 'add_consent_checkbox' ), 100 );
	}

	/**
	 * @param $validation_result
	 *
	 * @return mixed
	 */
	public function bbpress_validation_filter( $args ) {

		$filter_actions = array( 'bbp-new-forum', 'bbp-new-reply', 'bbp-new-topic)');

		if ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], $filter_actions ) ) {

			$inject = $this->get_admin_controller()->get_option_value('services_bbpress_consent_field', false, $this->front_controller->find_controller('services')->get_id());
			$consent_given = !empty($_POST[$this->get_consent_field_id()]);

			if(!$consent_given && $inject) {
				bbp_add_error( 'no_consent_given',  __( '<strong>ERROR</strong>: Please consent to storage of your data',   'ct-ultimate-gdpr' ) );
				return false;
			}

		}

		$this->log_user_consent();
		return $args;

	}

	public function render_field_services_bbpress_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	private function get_consent_field_id() {
		return 'ct-ultimate-gdpr-consent-field-bbpress';
	}
}