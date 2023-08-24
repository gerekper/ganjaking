<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\Ai_Editor_Conditional;

/**
 * Ai_Generator_Integration class.
 */
class Ai_Generator_Integration implements Integration_Interface {

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Represents the add-on manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Represents the options manager.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Represents the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Ai_Editor_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager  The admin asset manager.
	 * @param WPSEO_Addon_Manager       $addon_manager  The addon manager.
	 * @param Options_Helper            $options_helper The options helper.
	 * @param User_Helper               $user_helper    The user helper.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		WPSEO_Addon_Manager $addon_manager,
		Options_Helper $options_helper,
		User_Helper $user_helper
	) {
		$this->asset_manager  = $asset_manager;
		$this->addon_manager  = $addon_manager;
		$this->options_helper = $options_helper;
		$this->user_helper    = $user_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! $this->options_helper->get( 'enable_ai_generator', false ) ) {
			return;
		}

		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		// Enqueue after Elementor_Premium integration, which re-registers the assets.
		\add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_assets' ], 11 );
	}

	/**
	 * Enqueues the required assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		\wp_enqueue_script( 'wp-seo-premium-ai-generator' );
		\wp_localize_script(
			'wp-seo-premium-ai-generator',
			'wpseoPremiumAiGenerator',
			[
				'adminUrl'             => \admin_url( 'admin.php' ),
				'hasConsent'           => $this->user_helper->get_meta( $this->user_helper->get_current_user_id(), '_yoast_wpseo_ai_consent', true ),
				'hasValidSubscription' => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ),
				'pluginUrl'            => \plugins_url( '', \WPSEO_PREMIUM_FILE ),
				'postType'             => \get_post_type(),
			]
		);
		$this->asset_manager->enqueue_style( 'premium-ai-generator' );
	}
}
