<?php

/**
 * Class CT_Ultimate_GDPR_Service_Yith_Woocommerce_Wishlist
 */
class CT_Ultimate_GDPR_Service_Yith_Woocommerce_Wishlist extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_yith-woocommerce-wishlist/init.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_yith-woocommerce-wishlist/init.php', '__return_true' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare("
				SELECT y.*, u.ID, u.user_email,yu.*
				FROM {$wpdb->prefix}yith_wcwl_lists as y
				INNER JOIN {$wpdb->prefix}yith_wcwl as yu
				ON y.user_id = yu.user_id
				INNER JOIN {$wpdb->prefix}users as u
				ON y.user_id = u.ID				
				WHERE u.user_email = %s
				",
				$this->user->get_email()
			),
			ARRAY_A
		);

		if($results) {
			foreach ( $results as $result ):
				$results[] = $result['user_email'];
			endforeach;

			$this->set_collected( $results );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'YITH Woocommerce Wishlist' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'yith_wishlist_install' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
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
			"breach_services_yith_woocommerce_wishlist",
			esc_html( $this->get_name() ), // Title
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2' // Section
		);
	}

	/**
	 *
	 */
	public function render_field_breach_services() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array(), $this->front_controller->find_controller('breach')->get_id() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
		global $wpdb;

		$result_email = $wpdb->get_results(
			$wpdb->prepare("
				SELECT *
				FROM {$wpdb->prefix}users
				WHERE user_email = %s
				",
				$this->user->get_email()
			),
			ARRAY_A
		);

		if($result_email):
			$result_uid = $result_email[0]['ID'];

			$result = $wpdb->query(
				$wpdb->prepare("
					DELETE e, ed 
					FROM {$wpdb->prefix}yith_wcwl_lists as e
					INNER JOIN {$wpdb->prefix}yith_wcwl as ed
					ON ed.user_id = e.user_id
					WHERE ed.user_id = %s
					",
					$result_uid
				),
				ARRAY_A
			);
		endif;
	}

	/**
	 * @param array $recipients
	 *
	 * @return array
	 */
	public function breach_recipients_filter( $recipients ) {
		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}

		global $wpdb;

		$results = $wpdb->get_results("
			SELECT y.*, u.*
			FROM {$wpdb->prefix}yith_wcwl_lists as y
			INNER JOIN {$wpdb->prefix}users as u
			ON y.user_id = u.ID
			",
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return $recipients;
		}

		foreach( $results as $result ):
			if ( is_email( $result['user_email'] ) ) {
				$recipients[] = $result['user_email'];
			}
		endforeach;

		return $recipients;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {}

	public function enqueue_static( ) {	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'WooCommerce Wishlist gathers data entered by users in shop orders', 'ct-ultimate-gdpr' );
	}
}