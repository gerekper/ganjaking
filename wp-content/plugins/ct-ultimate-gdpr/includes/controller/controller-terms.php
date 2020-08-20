<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Terms
 */
class CT_Ultimate_GDPR_Controller_Terms extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-terms';

	/**
	 */
	private function set_redirect_after_page() {

		if (
			- 1 != $this->get_option( 'terms_after_page' ) ||
			$this->is_consent_valid() ||
			get_the_ID() == $this->get_option( 'terms_target_pages' )
		) {
			return;
		}

		if ( ! is_page() && ! is_home() && ! is_front_page() ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		$current_url = set_url_scheme( "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		update_option( 'ct-ultimate-gdpr-terms-redirect-after', $current_url );

	}

	/**
	 * @return string
	 */
	public static function get_redirect_after_page() {
		return get_option( 'ct-ultimate-gdpr-terms-redirect-after', '' );
	}

	/**
	 * Init after construct
	 */
	public function init() {

		add_action( 'wp_ajax_ct_ultimate_gdpr_terms_consent_give', array( $this, 'give_consent' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_terms_consent_give', array( $this, 'give_consent' ) );
		add_action( 'wp_ajax_ct_ultimate_gdpr_terms_consent_decline', array( $this, 'decline_consent' ) );
		add_action( 'wp_ajax_nopriv_ct_ultimate_gdpr_terms_consent_decline', array( $this, 'decline_consent' ) );

		// also for ajax forms
		$this->add_placeholders();

	}

	/**
	 * Do actions on frontend
	 */
	public function front_action() {

		$this->set_front_view();

		$this->set_redirect_after_page();
		if ( $this->should_redirect_user() ) {
			$this->schedule_redirect();
		}

	}

	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}

	/**
	 * Get unique controller id (page name, option id)
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Do actions on current admin page
	 */
	protected function admin_page_action() {

		if ( $this->is_request_consents_log() ) {
			$this->download_consents_log();
		}

	}

	/**
	 * @return bool|mixed
	 */
	private function is_request_consents_log() {
		return ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-log', $this->get_request_array() );
	}

	/**
	 * Download logs of all user consents
	 */
	private function download_consents_log() {

		$rendered = $this->logger->render_logs( $this->logger->get_logs( $this->get_id() ) );

/*		global $wpdb;

		// get all user metas
		$sql = $wpdb->prepare(
			"
				SELECT user_id, meta_value 
				FROM {$wpdb->usermeta}
				WHERE meta_key = %s
			",
			$this->get_id()
		);

		$results = $wpdb->get_results( $sql, ARRAY_A );

		// default to array
		if ( ! $results ) {
			$results = array();
		}

		// create a response
		$response = '';
		foreach ( $results as $result ) {

			$id      = $result['user_id'];
			$data    = maybe_unserialize( ( $result['meta_value'] ) );
			$expire  = $data['terms_expire_time'];
			$version = $data['terms_version'];

			// either get consent given time (v1.4) or calculate it
			$created = isset( $data['terms_consent_time'] ) ? $data['terms_consent_time'] : ( $expire - (int) $this->get_option( 'terms_expire', YEAR_IN_SECONDS ) );

			// format dates
			$expire  = ct_ultimate_gdpr_date( $expire );
			$created = ct_ultimate_gdpr_date( $created );

			$response .= sprintf(
				__( "user id: %d \r\nconsent version: %s \r\nconsent given: %s \r\nconsent expires: %s \r\n\r\n", 'ct-ultimate-gdpr' ),
				$id, $version, $created, $expire
			);

		}*/

		// download
		header( "Content-Type: application/octet-stream" );
		header( "Content-Disposition: attachment; filename='{$this->get_id()}-logs.txt'" );
		echo $rendered;
		exit;

	}

	/**
	 * Add menu page (if not added in admin controller)
	 */
	public function add_menu_page() {

		add_submenu_page(
			CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_name(),
			esc_html__( 'Terms And Conditions', 'ct-ultimate-gdpr' ),
			esc_html__( 'Terms And Conditions', 'ct-ultimate-gdpr' ),
			'manage_options',
			$this->get_id(),
			array( $this, 'render_menu_page' )
		);
	}

	/**
	 * Get view template string
	 * @return string
	 */
	public function get_view_template() {
		return '/admin/admin-terms';
	}

	/**
	 * @return bool
	 */
	private function should_redirect_user() {

		$terms_target_page = $this->get_option( 'terms_target_page', '', 'page' );

		if ( ! $terms_target_page && ! $this->get_option( 'terms_target_custom' ) ) {
			return false;
		}

		$url = $this->get_custom_target_page() ? $this->get_custom_target_page() : get_permalink( $terms_target_page );
		if( $url == get_permalink() ){
			return false;
		}

		if ( get_post() && get_post()->ID == $terms_target_page && ! $this->get_option( 'terms_target_custom' ) ) {
			return false;
		}

		$policy_page = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'policy_target_page', '', CT_Ultimate_GDPR_Controller_Policy::ID, 'page' );
		if ( $policy_page && get_post() && $policy_page == get_post()->ID ) {
			return false;
		}

		if ( is_user_logged_in() ) {

			if ( current_user_can( 'administrator' ) && ! $this->get_option( 'terms_require_administrator' ) ) {
				return false;
			}

			if ( ! $this->get_option( 'terms_require_users' ) ) {
				return false;
			}

		} else {

			if ( ! $this->get_option( 'terms_require_guests' ) ) {
				return false;
			}

		}

		if ( $this->is_consent_valid() ) {
			return false;
		}

		if ( $this->is_user_bot() ) {
			return false;
		}

		return true;

	}

	/**
	 * @return bool
	 */
	private function is_user_bot() {

		$bots_option = $this->get_option( 'terms_user_agents_whitelisted', '' );
		$bots        = array_filter( array_map( 'trim', explode( ',', $bots_option ) ) );

		foreach ( $bots as $bot ) {
			if ( strstr( strtolower( $_SERVER['HTTP_USER_AGENT'] ), $bot ) ) {
				return true;
			}

		}

		return false;

	}

	/**
	 *
	 */
	private function schedule_redirect() {

		$priority = CT_Ultimate_GDPR_Model_Redirect::PRIORITY_STANDARD;
		$url      = $this->get_custom_target_page() ? $this->get_custom_target_page() : get_permalink( $this->get_option( 'terms_target_page', '', 'page' ) );

		new CT_Ultimate_GDPR_Model_Redirect(
			$url,
			$priority
		);
	}

	/**
	 * @return bool|string
	 */
	private function get_custom_target_page() {
		$read_more_url        = false;
		$terms_target_custom = $this->get_option( 'terms_target_custom' );
		if ( $terms_target_custom ) {
			$read_more_url = strpos( $terms_target_custom, 'http' ) === false ? 'http://' . $terms_target_custom : $terms_target_custom;
		}

		return set_url_scheme($read_more_url);
	}

	/**
	 * @param string $variable_name
	 * @param string $variable_default_value
	 *
	 * @return array|mixed|object|string
	 */
	private function get_cookie( $variable_name = '', $variable_default_value = '' ) {

		$value  = ct_ultimate_gdpr_get_encoded_cookie( $this->get_id() );
		$cookie = $value ? json_decode( stripslashes( $value ), true ) : array();

		if ( $variable_name ) {
			return is_array( $cookie ) && isset( $cookie[ $variable_name ] ) ? $cookie[ $variable_name ] : $variable_default_value;
		}

		return $cookie;

	}

	/**
	 * @return bool
	 */
	private function is_consent_valid() {

		$user_meta = get_user_meta( $this->user->get_current_user_id(), $this->get_id(), true );

		$time_user_valid    = false;
		$version_user_valid = false;
		if ( $this->user->get_current_user_id() ) {

			$time_user_valid = (
				is_array( $user_meta ) &&
				! empty( $user_meta['terms_expire_time'] ) &&
				$user_meta['terms_expire_time'] > time()
			);

			$version_user_valid = (
				is_array( $user_meta ) &&
				! empty( $user_meta['terms_version'] ) &&
				$user_meta['terms_version'] === $this->get_option( 'terms_version' )
			);

		}

		$cookie_date       = $this->get_cookie( 'terms_expire_time', 0 );
		$time_cookie_valid = (
			$cookie_date &&
			$cookie_date > time()
		);

		$cookie_version       = $this->get_cookie( 'terms_version' );
		$version_cookie_valid = ( $cookie_version === $this->get_option( 'terms_version' ) );

		return ( $time_user_valid || $time_cookie_valid ) && ( $version_user_valid || $version_cookie_valid );

	}

	/**
	 *
	 */
	private function set_front_view() {

		if ( $this->is_consent_valid() ) {
			CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'terms_accepted', true );
		}
		CT_Ultimate_GDPR_Model_Front_View::instance()->set( 'terms_btn_styling', $this->get_option( 'terms_btn_styling' ) );
	}

	/**
	 * @param int $custom_expire_time
	 */
	public function give_consent( $custom_expire_time = 0 ) {

		$time = time();

		$expire_time = $custom_expire_time ?
			$custom_expire_time :
			$time + (int) $this->get_option( 'terms_expire', YEAR_IN_SECONDS );

		$data = array(
			'terms_expire_time'  => $expire_time,
			'terms_consent_time' => $time,
			'terms_version'      => $this->get_option( 'terms_version' ),
		);

		ct_ultimate_gdpr_set_encoded_cookie( $this->get_id(), ct_ultimate_gdpr_json_encode( $data ), $expire_time, '/' );
		//for wp-rocket caching
		ct_ultimate_gdpr_set_encoded_cookie( $this->get_id() . '-level', 1, $expire_time, '/' );

		if ( is_user_logged_in() ) {
			update_user_meta( $this->user->get_current_user_id(), $this->get_id(), $data );
		}

		$this->logger->consent( array(
			'type'       => $this->get_id(),
			'time'       => $time,
			'user_id'    => $this->user->get_current_user_id(),
			'user_ip'    => ct_ultimate_gdpr_get_permitted_user_ip(),
			'user_agent' => ct_ultimate_gdpr_get_permitted_user_agent(),
			'data'       => $data,
		) );

	}

	/**
	 * User decline consent to Terms
	 */
	public function decline_consent() {

		setcookie( $this->get_id(), '', 1, '/' );
		//for wp-rocket caching
		setcookie( $this->get_id() . '-level', '', 1 );

		if ( is_user_logged_in() ) {
			delete_user_meta( $this->user->get_current_user_id(), $this->get_id() );
		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		/* Section */

		add_settings_section(
			$this->get_id(), // ID
			esc_html__( 'Terms and Conditions', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			$this->get_id() // Page
		);

		/* Section fields */

		add_settings_field(
			'terms_header', // ID
			esc_html__( 'Instructions', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_header' ), // Callback
			$this->get_id(), // Page
			$this->get_id(), // Section
			array(
				'class' => 'ct-ultimate-gdpr-message ct-ultimate-gdpr-msg-clone',
			)
		);

		add_settings_field(
			'terms_require_administrator', // ID
			esc_html__( 'Require administrators to accept Terms and Conditions (redirect)', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_require_administrator' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_require_users', // ID
			esc_html__( 'Require logged in users to accept Terms and Conditions (redirect)', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_require_users' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_require_guests', // ID
			esc_html__( 'Require guest users to accept Terms and Conditions (redirect)', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_require_guests' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_target_page', // ID
			esc_html__( 'The page with existing Terms and Conditions', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_target_page' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_after_page', // ID
			esc_html__( 'The page to redirect to after Terms accepted', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_after_page' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_target_custom',
			esc_html__( 'Terms and Condition Custom URL', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_terms_target_custom' ),
			$this->get_id(),
			$this->get_id()
		);

		add_settings_field(
			'terms_btn_styling', // ID
			esc_html__( 'Shortcode Button Styling', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_btn_styling' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_expire', // ID
			esc_html__( 'Set consent expire time [s]', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_expire' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_version', // ID
			esc_html__( 'Terms version, eg. 1.0 (if you change it, user has to give consent again)', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_version' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_placeholder', // ID
			esc_html__( "Convert the following text to Terms and Conditions link in all services templates, eg. 'Terms and Conditions'", 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_placeholder' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

		add_settings_field(
			'terms_user_agents_whitelisted', // ID
			esc_html__( "Do not block user agents (eg. bots) containing the following texts (comma separated)", 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_terms_user_agents_whitelisted' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);

	}

	/**
	 *
	 */
	public function render_field_terms_target_custom() {
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name, '', $this->get_id() );

		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			esc_html( $value )
		);
	}

	/**
	 *
	 */
	public function render_field_terms_header() {
		printf(
			esc_html__( '1. Place %s shortcode on your existing Terms and Conditions page to add an accept button.%s2. Select Terms and Conditions page below, so users can be redirected there to give their consent.', 'ct-ultimate-gdpr' ),
			'<b>[ultimate_gdpr_terms_accept]</b>',
			'<br>'
		);
	}

	/**
	 *
	 */
	public function render_field_terms_content() {

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
	public function render_field_terms_version() {

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
	 *
	 */
	public function render_field_terms_expire() {

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
	 *
	 */
	public function render_field_terms_target_page() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name, '', $this->get_id() ); // do not translate this id
		$post_types = ct_ultimate_gpdr_get_default_post_types();
		$posts      = ct_ultimate_gdpr_wpml_get_original_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => $post_types,
		) );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		// empty option
		echo "<option></option>";

		/** @var WP_Post $post */
		foreach ( $posts as $post ) :

			$post_title = $post->post_title ? $post->post_title : $post->post_name;
			$post_id    = $post->ID;
			$selected   = $post_id == $value ? "selected" : '';
			echo "<option value='$post->ID' $selected>$post_title</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_terms_btn_styling() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name  = $admin->get_field_name( __FUNCTION__ );
		$field_value = $admin->get_option_value( $field_name );
		$positions   = array(
			'term_theme_default' => esc_html__( 'Theme Default', 'ct-ultimate-gdpr' ),
			'term_cookie_btn'    => esc_html__( 'Cookie box buttons', 'ct-ultimate-gdpr' ),
		);

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name )
		);

		foreach ( $positions as $value => $label ) :

			$selected = ( $field_value == $value ) ? "selected" : '';
			echo "<option value='$value' $selected>$label</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_terms_placeholder() {

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
	 *
	 */
	public function render_field_terms_user_agents_whitelisted() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_option( $field_name, ct_ultimate_gdpr_get_value( $field_name, $this->get_default_options() ) )
		);

	}

	/**
	 *
	 */
	public function render_field_terms_after_page() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		$value      = $admin->get_option_value( $field_name, '', $this->get_id() ); // do not translate this id
		$post_types = ct_ultimate_gpdr_get_default_post_types();
		$posts      = ct_ultimate_gdpr_wpml_get_original_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => $post_types,
		) );

		printf(
			'<select class="ct-ultimate-gdpr-field" id="%s" name="%s">',
			$field_name,
			$admin->get_field_name_prefixed( $field_name )
		);

		// add default options
		printf( "<option value='-1' %s >%s</option>",  $value == -1 ? 'selected' : '' , esc_html__( 'Last visited page', 'ct-ultimate-gdpr' ) );
		printf( "<option value='0' %s >%s</option>",  $value == 0 ? 'selected' : '', esc_html__( "Don't redirect", 'ct-ultimate-gdpr' ) );

		/** @var WP_Post $post */
		foreach ( $posts as $post ) :

			$post_title = $post->post_title ? $post->post_title : $post->post_name;
			$post_id    = $post->ID;
			$selected   = $post_id == $value ? "selected" : '';
			echo "<option value='$post->ID' $selected>$post_title</option>";

		endforeach;

		echo '</select>';

	}

	/**
	 *
	 */
	public function render_field_terms_require_administrator() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', $this->get_id() ) ? 'checked' : ''
		);

	}


	/**
	 *
	 */
	public function render_field_terms_require_users() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', $this->get_id() ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_terms_require_guests() {

		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value( $field_name, '', $this->get_id() ) ? 'checked' : ''
		);

	}

	/**
	 * @return array|mixed
	 */
	public function get_default_options() {

		return apply_filters( "ct_ultimate_gdpr_controller_{$this->get_id()}_default_options", array(
			'forgotten_notify_mail'         => get_bloginfo( 'admin_email' ),
			'terms_expire'                  => YEAR_IN_SECONDS,
			'terms_version'                 => '1.0',
			'terms_btn_styling'             => 'term_theme_default',
			'terms_placeholder'             => esc_html__( 'Terms and Conditions', 'ct-ultimate-gdpr' ),
			'terms_user_agents_whitelisted' => 'bot, crawl, slurp, spider, mediapartners',
		) );

	}

	/**
	 * Set a placeholder for templates
	 */
	private function add_placeholders() {
		CT_Ultimate_GDPR_Model_Placeholders::instance()->add(
			$this->get_option( 'terms_placeholder' ),
			sprintf(
				'<a href="%s">%s</a>',
				get_permalink( $this->get_option( 'terms_target_page', '', 'page' ) ),
				$this->get_option( 'terms_placeholder' )
			)
		);
	}

}