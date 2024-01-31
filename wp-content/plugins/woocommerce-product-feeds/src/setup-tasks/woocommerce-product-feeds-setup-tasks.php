<?php

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use WoocommerceProductFeedsConfigureSettingsTask as ConfigureSettingsTask;
use WoocommerceProductFeedsFeedSetupTask as FeedSetupTask;

class WoocommerceProductFeedsSetupTasks {
	/**
	 * @var string
	 */
	private $base_dir;

	public function initialise() {
		$this->base_dir = dirname( __DIR__, 2 );
		add_action( 'init', [ $this, 'register_tasks' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * @return void
	 */
	public function register_tasks() {
		TaskLists::add_task(
			'extended',
			new ConfigureSettingsTask(
				TaskLists::get_list( 'extended' ),
			)
		);
		TaskLists::add_task(
			'extended',
			new FeedSetupTask(
				TaskLists::get_list( 'extended' ),
			)
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		if (
			! class_exists( 'Automattic\WooCommerce\Internal\Admin\Loader' ) ||
			! \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page()
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
}
