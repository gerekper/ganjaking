<?php
/**
 * One Page Checkout Settings class.
 *
 * @since 1.6.0
 */
final class WCOPC_Settings {

	/** @var string */
	private $id = 'wcopc';

	/**
	 * Array of settings, containing their defaults and the ID.
	 *
	 * @var array
	 */
	private $settings = array(
		'autoscroll' => array(
			'default' => 'yes',
			'id'      => 'wcopc_autoscroll',
		),
	);

	/**
	 * Add our plugin methods to the appropriate hooks.
	 */
	public function init() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 50 );
		add_action( "woocommerce_settings_{$this->id}", array( $this, 'output' ) );
		add_action( "woocommerce_settings_save_{$this->id}", array( $this, 'save' ) );
	}

	/**
	 * Add our settings page tab to the other WC tabs.
	 *
	 * @param array $tabs The existing tabs.
	 *
	 * @return array The tabs with ours added.
	 */
	public function add_settings_page( $tabs ) {
		$tabs[ $this->id ] = __( 'One Page Checkout', 'wcopc' );

		return $tabs;
	}

	/**
	 * Output our settings.
	 *
	 * @see woocommerce_admin_fields()
	 */
	public function output() {
		woocommerce_admin_fields( $this->get_settings() );
		wp_nonce_field( 'wcopc_settings', '_opc_nonce', false );
	}

	/**
	 * Save our settings.
	 *
	 * @see woocommerce_update_options()
	 */
	public function save() {
		$nonce_verified = isset( $_POST['_opc_nonce'] ) && wp_verify_nonce( $_POST['_opc_nonce'], 'wcopc_settings' );
		if ( ! $nonce_verified ) {
			return;
		}

		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Get an individual setting value.
	 *
	 * @param string $setting The setting name. This should correlate to a key in self::$settings.
	 *
	 * @return mixed The setting's value.
	 * @throws LogicException When an unknown setting is requested.
	 */
	public function get_setting( $setting ) {
		if ( ! array_key_exists( $setting, $this->settings ) ) {
			throw new LogicException( sprintf( 'The setting %s is not a valid setting', $setting ) );
		}

		// Cache the setting values so they only need to be retrieved once.
		if ( ! array_key_exists( 'value', $this->settings[ $setting ] ) ) {
			$this->settings[ $setting ]['value'] = get_option(
				$this->settings[ $setting ]['id'],
				$this->settings[ $setting ]['default']
			);
		}

		return $this->settings[ $setting ]['value'];
	}

	/**
	 * Add our settings to the existing set of settings.
	 *
	 * @param array $settings The existing settings.
	 *
	 * @return array The new settings.
	 */
	private function get_settings() {
		$settings = array(
			array(
				'title' => __( 'Checkout Behavior', 'wcopc' ),
				'type'  => 'title',
				'id'    => 'wcopc_checkout_behavior',
			),
			array(
				'title'    => __( 'Enable auto-scroll', 'wcopc' ),
				'desc'     => __( 'Automatically scroll to notifications on checkout page', 'wcopc' ),
				'id'       => $this->settings['autoscroll']['id'],
				'type'     => 'checkbox',
				'desc_tip' => __( 'When items in the cart are added, removed, or otherwise updated, One Page Checkout automatically scrolls to the notification section if necessary. This setting controls that behavior.', 'wcopc' ),
				'default'  => $this->settings['autoscroll']['default'],
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wcopc_checkout_behavior',
			),
		);

		return apply_filters( "woocommerce_get_settings_{$this->id}", $settings );
	}
}
