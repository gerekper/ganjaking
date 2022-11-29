<?php
/**
 * BSF extension installer class file.
 *
 * @package bsf-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BSF_Extension_Installer Extension installer.
 */
class BSF_Extension_Installer {

	/**
	 *  Constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_ajax_bsf-extention-activate', array( $this, 'activate_plugin' ) );
	}

	/**
	 * Load scripts needed for extension installer.
	 *
	 * @param  hook $hook current page hook.
	 * @return void
	 */
	public function load_scripts( $hook ) {
		$bsf_ext_inst = apply_filters( 'bsf_extension_installer_screens', array( 'bsf-extensions' ), $hook );

		foreach ( $bsf_ext_inst as $key => $value ) {
			if ( false !== strpos( $hook, $value ) ) {
				wp_register_script( 'bsf-extension-installer', bsf_core_url( '/assets/js/extension-installer.js' ), array( 'jquery', 'wp-util', 'updates' ), BSF_UPDATER_VERSION, true );
				wp_enqueue_script( 'bsf-extension-installer' );
			}
		}
	}

	/**
	 * Activates plugin.
	 *
	 * @return void
	 */
	public function activate_plugin() {

		if ( ! wp_verify_nonce( $_POST['security'], 'bsf_activate_extension_nonce' ) ) {

			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'You are not authorized to perform this action.', 'bsf' ),
				)
			);
		}

		if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! $_POST['init'] ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'No plugin specified', 'bsf' ),
				)
			);
		}

		$plugin_init = ( isset( $_POST['init'] ) ) ? esc_attr( $_POST['init'] ) : '';
		$activate    = activate_plugin( $plugin_init, '', false, true );

		if ( is_wp_error( $activate ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => $activate->get_error_message(),
				)
			);
		}

		wp_send_json_success(
			array(
				'success' => true,
				'message' => __( 'Plugin Activated', 'bsf' ),
			)
		);
	}
}

new BSF_Extension_Installer();
