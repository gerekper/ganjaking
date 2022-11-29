<?php
/**
 * WP CLI Commands to manage Brainstorm Force products.
 *
 * @package bsf-core
 */

/**
 * Class BSF_WP_CLI_Command
 */
class BSF_WP_CLI_Command extends WP_CLI_Command {

	/**
	 * BSF_License Manager instance.
	 *
	 * @var $license_manager
	 */
	private $license_manager = '';

	/**
	 * Initiator.
	 */
	public function __construct() {
		$this->license_manager = new BSF_License_Manager();
	}

	/**
	 * WP CLI Command to activate and deactivate licenses for brainstormforce products.
	 *
	 * ## OPTIONS
	 *
	 * <action>
	 *      activate or deactivate
	 *
	 * <priduct-id>
	 *      Product id is unique for each brainstorm product, it can be found in the file <product-root-directory>/admin/.bsf.yml
	 *
	 * <license-key>
	 *      Your purchase key.
	 *
	 * ## EXAMPLES
	 *
	 *  1. wp brainstormforce license activate uabb <purchase-key>
	 *      - This will activate the license for plugin Ultimate Addons for beaver builder with purchase key <purchase-key>
	 *  2. wp brainstormforce license deactivate uabb <purchase-key>
	 *      - This will deactivate the license for plugin Ultimate Addons for beaver builder with purchase key <purchase-key>
	 *
	 * @param array $args Arguments.
	 * @param array $assoc_args Associative Arguments.
	 */
	public function license( $args, $assoc_args ) {

		if ( isset( $args[0] ) && 'activate' === $args[0] || 'deactivate' === $args[0] ) {
			$action = $args[0];
		} else {
			WP_CLI::error( 'Please enter the correct action.' );
		}

		if ( isset( $args[1] ) ) {
			$poduct_id = $args[1];
		} else {
			WP_CLI::error( 'Please enter a product id.' );
		}

		if ( isset( $args[2] ) ) {
			$purchase_key = $args[2];
		} else {
			WP_CLI::error( 'Please enter the purchase key.' );
		}

		// Setup brainstorm_products data.
		init_bsf_core();

		$_POST = array(
			'bsf_license_manager' => array(
				'license_key' => $purchase_key,
				'product_id'  => $poduct_id,
			),
		);

		$bsf_action = '';

		if ( 'activate' === $action ) {
			$bsf_action                    = 'bsf_license_activation';
			$_POST['bsf_activate_license'] = true;
			$_POST['bsf_graupi_nonce']     = wp_create_nonce( 'bsf_license_activation_deactivation_nonce' );
			$this->license_manager->bsf_activate_license();
		} else {
			$bsf_action                      = 'bsf_license_deactivation';
			$_POST['bsf_deactivate_license'] = true;
			$_POST['bsf_graupi_nonce']       = wp_create_nonce( 'bsf_license_activation_deactivation_nonce' );
			$this->license_manager->bsf_deactivate_license();
		}

		if ( '' !== $bsf_action ) {
			if ( isset( $_POST[ $bsf_action ]['success'] ) && ( true === $_POST[ $bsf_action ]['success'] || 'true' === $_POST[ $bsf_action ]['success'] ) && isset( $_POST['bsf_graupi_nonce'] ) && wp_verify_nonce( $_POST['bsf_graupi_nonce'], 'bsf_license_activation_deactivation_nonce' ) ) {

				$success_message = esc_attr( $_POST[ $bsf_action ]['message'] );

				WP_CLI::success( $success_message );
			} else {
				$error_message = esc_attr( $_POST[ $bsf_action ]['message'] );

				WP_CLI::error( $error_message );
			}
		}

	}
}

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::add_command( 'brainstormforce', 'BSF_WP_CLI_Command' );
}
