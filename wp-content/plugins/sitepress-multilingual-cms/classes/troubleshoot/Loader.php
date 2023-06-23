<?php

namespace WPML\TM\Troubleshooting;

use WPML\LIB\WP\Nonce;
use WPML\Core\WP\App\Resources;
use WPML\TM\ATE\AutoTranslate\Endpoint\CancelJobs;
use WPML\TM\ATE\Hooks\JobActionsFactory;
use WPML\LanguageSwitcher\LsTemplateDomainUpdater;

class Loader implements \IWPML_Backend_Action {

	public function add_hooks() {

		add_action( 'after_setup_complete_troubleshooting_functions', [ $this, 'render' ], 7 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueScripts' ] );
	}

	public function render() {
		echo '<div id="wpml-troubleshooting-container" style="margin: 5px 0; width: 350px;"></div>';
	}

	public function enqueueScripts( $hook ) {
		if ( WPML_PLUGIN_FOLDER . '/menu/troubleshooting.php' === $hook ) {
			$enqueue = Resources::enqueueApp( 'troubleshooting' );
			$enqueue(
				[
					'name' => 'troubleshooting',
					'data' => [
						'refreshLicense' => [
							'nonce' => Nonce::create( 'update_site_key_wpml' ),
						],
						'endpoints'      => [
							'cancelJobs'              => CancelJobs::class,
							'lsTemplatesUpdateDomain' => LsTemplateDomainUpdater::class,
						],
					],
				]
			);
		}
	}
}