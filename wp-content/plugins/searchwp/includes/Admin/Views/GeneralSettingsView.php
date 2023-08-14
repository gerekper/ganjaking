<?php

/**
 * SearchWP GeneralSettingsView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\License;
use SearchWP\Settings;
use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class GeneralSettingsView is responsible for providing the UI for General Settings.
 *
 * @since 4.3.0
 */
class GeneralSettingsView {

	private static $slug = 'general-settings';

	/**
	 * GeneralSettingsView constructor.
	 *
	 * @since 4.3.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'settings' ) ) {
			new NavTab( [
				'page'       => 'settings',
				'tab'        => self::$slug,
				'label'      => __( 'General', 'searchwp' ),
				'is_default' => true,
			] );
		}

		if ( Utils::is_swp_admin_page( 'settings', 'default' ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'license_activate',   [ __CLASS__, 'license_activate' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'license_deactivate', [ __CLASS__, 'license_deactivate' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'update_setting',  [ __CLASS__, 'update_setting' ] );
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.3.0
	 * @return void
	 */
	public static function assets() {

		$handle = SEARCHWP_PREFIX . self::$slug;

		array_map(
			'wp_enqueue_style',
			[
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'input',
				Utils::$slug . 'toggle-switch',
				Utils::$slug . 'style',
			]
		);

		wp_enqueue_script(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/general-settings.js',
			[
				Utils::$slug . 'collapse',
                Utils::$slug . 'settings-toggle',
            ],
			SEARCHWP_VERSION,
			true
		);

		Utils::localize_script( $handle );
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.3.0
	 * @return void
	 */
	public static function render() {

		$settings = [
			'partial_matches'       => [
				'label' => __( 'Partial Matches', 'searchwp' ),
				'desc'  => __( 'Find partial matches when search terms yield no results. Spelling corrections are applied if necessary.', 'searchwp' ),
			],
			'do_suggestions'        => [
				'label' => __( 'Closest Match', 'searchwp' ),
				'desc'  => __( 'Use the closest match for searches that yield no results and output a notice (requires Partial Matches).', 'searchwp' ),
			],
			'quoted_search_support' => [
				'label' => __( '"Quoted" Searches', 'searchwp' ),
				'desc'  => __( 'When search terms are wrapped in double quotes, results will be limited to those with exact matches.', 'searchwp' ),
			],
			'highlighting'          => [
				'label' => __( 'Highlight Terms', 'searchwp' ),
				'desc'  => __( 'Automatically highlight terms in search results when possible.', 'searchwp' ),
			],
			'parse_shortcodes'      => [
				'label' => __( 'Parse Shortcodes', 'searchwp' ),
				'desc'  => __( 'Index expanded Shortcode output (at the time of indexing).', 'searchwp' ),
			],
		];

		?>
        <div class="swp-content-container">

            <div class="swp-collapse swp-opened"> <!-- License collapse -->

                <div class="swp-collapse--header">

                    <h2 class="swp-h2">
                        License
                    </h2>

                    <button class="swp-expand--button">
                        <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                        </svg>
                    </button>

                </div>

                <div class="swp-collapse--content">

                    <div class="swp-row">

                        <p class="swp-p">
                            Your license key provides access to updates and extensions.
                        </p>

                    </div>

                    <div class="swp-row"> <!-- License Key row -->

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width">
                                <h3 class="swp-h3">
                                    License Key
                                </h3>
                            </div>

                            <div class="swp-col">

                                <?php $license = Settings::get( 'license' ); ?>

                                <?php // TODO: Process the case when the active license gets expired over time. ?>

                                <div class="swp-flex--row sm:swp-flex--wrap swp-flex--gap12">
                                    <input id="swp-license" class="swp-input swp-w-2/5" type="<?php echo License::is_active() ? 'password' : 'text'; ?>"<?php echo License::is_active() ? ' value="' . esc_attr( License::get_key() ) . '" disabled' : ''; ?>>
                                    <button id="swp-license-deactivate" class="swp-button"<?php echo License::is_active() ? '' : ' style="display:none"'; ?>>Remove Key</button>
                                    <button id="swp-license-activate" class="swp-button swp-button--green"<?php echo License::is_active() ? ' style="display:none"' : ''; ?>>Verify Key</button>
                                </div>

                                <p id="swp-license-error-msg" class="swp-desc--btm swp-text-red" style="display:none"></p>

                                <p id="swp-license-active-msg" class="swp-desc--btm"<?php echo License::is_active() ? '' : ' style="display:none"'; ?>>
                                    Your license key level is <b><span id="swp-license-type"><?php echo License::is_active() ? esc_html( strtoupper( License::get_type() ) ) : ''; ?></span></b>. <span id="swp-license-remaining"><?php echo License::is_active() ? esc_html( $license['remaining'] ) : ''; ?></span>.
                                </p>
                                <p id="swp-license-inactive-msg" class="swp-desc--btm"<?php echo License::is_active() ? ' style="display:none"' : ''; ?>>
                                    Your license key can be found in your <a href="https://searchwp.com/account/?utm_campaign=plugin&utm_source=WordPress&utm_medium=settings-license&utm_content=Account%20Dashboard" target="_blank" rel="noopener noreferrer">SearchWP Account Dashboard</a>. Don’t have a license? <a href="https://searchwp.com/buy/?utm_campaign=plugin&utm_source=WordPress&utm_medium=settings-license&utm_content=License%20Key%20Sign%20Up" target="_blank" rel="noopener noreferrer">Sign up today!</a>
                                </p>
                            </div>

                        </div>

                    </div>
                </div>

            </div> <!-- End License collapse -->

            <div class="swp-collapse swp-opened"> <!-- General Settings collapse -->

                <div class="swp-collapse--header">

                    <h2 class="swp-h2">
                        General Settings
                    </h2>

                    <button class="swp-expand--button">
                        <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                        </svg>
                    </button>

                </div>

                <div class="swp-collapse--content">

                    <?php foreach ( $settings as $key => $setting ) : ?>

                        <div class="swp-row">

                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                                <div class="swp-col swp-col--title-width">
                                    <h3 class="swp-h3">
                                        <?php echo esc_html( $setting['label'] ); ?>
                                    </h3>
                                </div>

                                <div class="swp-col">
                                    <label class="swp-toggle">
                                        <div class="swp-flex--row swp-flex--gap17 swp-flex--align-c">
                                            <input class="swp-toggle-checkbox" type="checkbox" id="swp-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php checked( Settings::get_single( $key, 'boolean' ) ); ?>>
                                            <div class="swp-toggle-switch"></div>
                                        </div>
                                        <span class="swp-label">
                                            <?php echo esc_html( $setting['desc'] ); ?>
                                        </span>
                                    </label>
                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>  <!-- End General Settings collapse -->

        </div>
		<?php
	}

	/**
	 * Callback to activate license key.
	 *
	 * @since 4.3.0
	 */
	public static function license_activate() {

		Utils::check_ajax_permissions();

		$license_key = isset( $_REQUEST['license_key'] ) ? Utils::decode_string( $_REQUEST['license_key'] ) : '';

		$response = License::activate( $license_key );

		if ( $response['success'] ) {
			wp_send_json_success( Settings::get( 'license' ) );
		} else {
			wp_send_json_error( $response['data'] );
		}
	}

	/**
	 * Callback to deactivate license key.
	 *
	 * @since 4.3.0
	 */
	public static function license_deactivate() {

		Utils::check_ajax_permissions();

		$license_key = isset( $_REQUEST['license_key'] ) ? Utils::decode_string( $_REQUEST['license_key'] ) : '';

		$response = License::deactivate( $license_key );

		if ( $response['success'] ) {
			wp_send_json_success( Settings::get( 'license' ) );
		} else {
			wp_send_json_error( $response['data'] );
		}
	}

	/**
	 * AJAX callback to update a setting.
	 *
	 * @since 4.3.0
	 */
	public static function update_setting() {

		Utils::check_ajax_permissions();

		$setting = isset( $_REQUEST['setting'] ) ? Utils::decode_string( $_REQUEST['setting'] ) : null;
		$value   = isset( $_REQUEST['value'] )   ? json_decode( stripslashes( $_REQUEST['value'] ), true ) : null;

		if ( is_null( $setting ) || is_null( $value ) ) {
			wp_send_json_error();
		}

		Settings::update( $setting, $value );

		wp_send_json_success();
	}
}
