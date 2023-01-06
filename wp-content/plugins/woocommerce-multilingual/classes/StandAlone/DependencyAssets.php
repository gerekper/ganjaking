<?php

namespace WCML\StandAlone;

use WCML\Utilities\AdminPages;

class DependencyAssets {

	/** @var string $dependencyBaseUrl */
	private $dependencyBaseUrl;

	/**
	 * @param string $dependencyBaseUrl
	 */
	public function __construct( $dependencyBaseUrl ) {
		$this->dependencyBaseUrl = $dependencyBaseUrl;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function enqueue() {
		/* phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter */
		wp_register_script( 'wpml-scripts', $this->dependencyBaseUrl . '/res/js/scripts.js', [], WCML_VERSION );
		wp_register_style( 'wpml-styles', $this->dependencyBaseUrl . '/res/css/styles.css', [], WCML_VERSION );
		wp_register_style( 'otgs-dialogs', $this->dependencyBaseUrl . '/res/css/otgs-dialogs.css', [ 'wp-jquery-ui-dialog' ], WCML_VERSION );
		wp_register_style( 'wpml-dialog', $this->dependencyBaseUrl . '/res/css/dialog.css', [ 'otgs-dialogs' ], WCML_VERSION );

		if ( AdminPages::isMultiCurrency() || AdminPages::isTab( 'multilingual' ) ) {
			wp_enqueue_script( 'wpml-scripts' );

			wp_enqueue_style( 'wpml-styles' );
			wp_enqueue_style( 'wpml-dialog' );

			wp_enqueue_style( \OTGS_Assets_Handles::SWITCHER );
			wp_enqueue_script( \OTGS_Assets_Handles::SWITCHER );
			wp_enqueue_style( \OTGS_Assets_Handles::POPOVER_TOOLTIP );
			wp_enqueue_script( \OTGS_Assets_Handles::POPOVER_TOOLTIP );

			wp_enqueue_style( \OTGS_ASSETS_ICONS_STYLES );
			wp_enqueue_style( 'otgs-icons' );
		}
	}
}
