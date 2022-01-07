<?php

use Automattic\WooCommerce\Admin\Features\Onboarding;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use Automattic\WooCommerce\Admin\Loader;

class WoocommerceProductFeedsWoocommerceAdminIntegration {

	/**
	 * @var string
	 */
	private $base_dir;

	public function initialise() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'woocommerce_get_registered_extended_tasks', array( $this, 'register_extended_task' ), 10, 1 );
		$this->base_dir = dirname( dirname( dirname( __FILE__ ) ) );
	}

	/**
	 * Register the task list item and the JS.
	 */
	public function admin_enqueue_scripts() {

		if (
			! function_exists( 'wc_admin_is_registered_page' ) ||
			! class_exists( 'Automattic\WooCommerce\Admin\Loader' ) ||
			! Loader::is_admin_page() ||
			! $this->should_show_tasks()
		) {
			return;
		}

		/**
		 * Setup tasks
		 */
		$asset_file = require $this->base_dir . '/js/dist/setup-tasks.asset.php';
		wp_register_script(
			'woocommerce-gpf-setup-tasks',
			plugins_url( basename( $this->base_dir ) . '/js/dist/setup-tasks.js' ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
		$l10n_data = array(
			'configure_settings_is_complete' => get_option( 'woocommerce_gpf_configure_settings_is_complete', false ),
			'feed_setup_is_complete'         => get_option( 'woocommerce_gpf_feed_setup_is_complete', false ),
			'settings_link'                  => admin_url( 'admin.php?page=wc-settings&tab=gpf' ),
		);
		wp_localize_script(
			'woocommerce-gpf-setup-tasks',
			'woocommerce_gpf_setup_tasks_data',
			$l10n_data
		);
		wp_enqueue_script( 'woocommerce-gpf-setup-tasks' );

		/**
		 * Store management links
		 */
		$asset_file = require $this->base_dir . '/js/dist/store-management-links.asset.php';
		wp_register_script(
			'woocommerce-gpf-store-management-links',
			plugins_url( basename( $this->base_dir ) . '/js/dist/store-management-links.js' ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
		wp_localize_script(
			'woocommerce-gpf-store-management-links',
			'woocommerce_gpf_store_management_links_data',
			[
				'settings_link' => admin_url( 'admin.php?page=wc-settings&tab=gpf' ),
			]
		);
		wp_enqueue_script( 'woocommerce-gpf-store-management-links' );
	}

	public function register_extended_task( $registered_tasks_list_items ) {
		$setup_tasks = array(
			'woocommerce_gpf_configure_settings',
			'woocommerce_gpf_configure_feed',
		);
		foreach ( $setup_tasks as $setup_task ) {
			if ( ! in_array( $setup_task, $registered_tasks_list_items, true ) ) {
				array_push( $registered_tasks_list_items, $setup_task );
			}
		}

		return $registered_tasks_list_items;
	}

	private function should_show_tasks() {
		if ( version_compare( WC_VERSION, '5.9', '<' ) ) {
			return Onboarding::should_show_tasks();
		}
		$extended_list = TaskLists::get_list( 'extended' );

		return $extended_list && ! $extended_list->is_hidden();
	}
}
