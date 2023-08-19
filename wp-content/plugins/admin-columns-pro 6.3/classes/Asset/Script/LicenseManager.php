<?php

namespace ACP\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\Asset\Script\Localize\Translation;
use AC\Nonce\Ajax;
use Plugin_Upgrader;

class LicenseManager extends Script {

	public function __construct( Location\Absolute $location ) {
		parent::__construct( 'acp-license-manager', $location, [ Script\GlobalTranslationFactory::HANDLE ] );
	}

	public function register(): void {
		parent::register();

		$this->add_inline_variable( 'ACP_LICENSE', [
			'_ajax_nonce' => ( new Ajax() )->create(),
		] );

		$translation = Translation::create( [
			'license_removal'             => __( 'Are you sure you want deactivate Admin Columns Pro?', 'codepress-admin-columns' ),
			'license_removal_explanation' => __( 'You need to fill in your license key again if you want to reactivate.', 'codepress-admin-columns' ),
			'plugin_update_success'       => $this->get_plugin_update_success_string(),
			'updating_plugin'             => __( 'Updating plugin', 'codepress-admin-columns' ),
			'plugin_up_to_date'           => __( 'This plugin is up to date', 'codepress-admin-columns' ),
		] );

		$this->localize( 'ACP_LICENSE_I18N', $translation );
	}

	private function get_plugin_update_success_string() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$upgrader = new Plugin_Upgrader();
		$upgrader->upgrade_strings();

		return $upgrader->strings['process_success'] ?? null;
	}

}