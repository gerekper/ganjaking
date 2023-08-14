<?php

/**
 * SearchWP MiscSettingsView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\Settings;
use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class MiscSettingsView is responsible for providing the UI for Misc Settings.
 *
 * @since 4.3.0
 */
class MiscSettingsView {

	private static $slug = 'misc-settings';

	/**
	 * MiscSettingsView constructor.
	 *
	 * @since 4.3.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'settings' ) ) {
			new NavTab( [
				'page'  => 'settings',
				'tab'   => self::$slug,
				'label' => __( 'Misc', 'searchwp' ),
			] );
		}

		if ( Utils::is_swp_admin_page( 'settings', self::$slug ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'update_setting',  [ __CLASS__, 'update_setting' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'wake_indexer',    [ __CLASS__, 'wake_indexer' ] );
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.3.0
	 */
	public static function assets() {

		$handle = SEARCHWP_PREFIX . self::$slug;

		array_map(
			'wp_enqueue_style',
			[
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'input',
				Utils::$slug . 'modal',
				Utils::$slug . 'toggle-switch',
				Utils::$slug . 'style',
			]
		);

		wp_enqueue_script(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/misc-settings.js',
			[
				Utils::$slug . 'collapse',
				Utils::$slug . 'modal',
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
	 */
	public static function render() {

		$settings = [
			'debug'                          => [
				'label' => __( 'Debugging', 'searchwp' ),
				'desc'  => __( 'Log information during indexing and searching for review.', 'searchwp' ),
			],
			'tokenize_pattern_matches'       => [
				'label' => __( 'Pattern Match Tokens', 'searchwp' ),
				'desc'  => __( 'When enabled, additional tokens will be generated from regex pattern matches.', 'searchwp' ),
			],
			'remove_min_word_length'         => [
				'label' => __( 'Remove Minimum Word Length', 'searchwp' ),
				'desc'  => __( 'Index everything regardless of token length.', 'searchwp' ),
			],
			'indexer_paused'                 => [
				'label' => __( 'Pause Indexing', 'searchwp' ),
				'desc'  => __( 'Continue to queue (but do not apply) delta index updates. Queued updates will be processed immediately when the indexer is unpaused.', 'searchwp' ),
			],
			'reduced_indexer_aggressiveness' => [
				'label' => __( 'Reduce Indexer Load', 'searchwp' ),
				'desc'  => __( 'Process less data per index pass (less resource intensive, but slower).', 'searchwp' ),
			],
			'document_content_reset'         => [
				'label' => __( 'Re-parse Document Content', 'searchwp' ),
				'desc'  => __( 'Remove extracted Document Content, PDF Metadata, and image EXIF data and re-parse when rebuilding Index. Leaving this parsed content in place speeds up index rebuilds.', 'searchwp' ),
			],
			'hide_announcements'             => [
				'label' => __( 'Hide Announcements', 'searchwp' ),
				'desc'  => __( 'Hide plugin announcements and update details.', 'searchwp' ),
			],
			'nuke_on_delete'                 => [
				'label' => __( 'Uninstall SearchWP', 'searchwp' ),
				'desc'  => __( 'Remove all traces of SearchWP when it is deactivated and deleted from the Plugins page.', 'searchwp' ),
			],
		];

		?>
        <div class="swp-content-container">

            <div class="swp-collapse swp-opened"> <!-- Miscellaneous Settings collapse -->

                <div class="swp-collapse--header">

                    <h2 class="swp-h2">
                        Miscellaneous Settings
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

            </div>  <!-- End Miscellaneous Settings collapse -->

            <div class="swp-collapse swp-opened"> <!-- Troubleshooting collapse -->

                <div class="swp-collapse--header">

                    <h2 class="swp-h2">
                        Troubleshooting
                    </h2>

                    <button class="swp-expand--button">
                        <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                        </svg>
                    </button>

                </div>

                <div class="swp-collapse--content">

                    <div class="swp-row">

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width">
                                <h3 class="swp-h3">
                                    Wake Up Indexer
                                </h3>
                            </div>

                            <div class="swp-col">
                                <label class="swp-toggle">
                                    <div class="swp-flex--row swp-flex--gap17 swp-flex--align-c">
                                        <button type="button" id="swp-wake-up-indexer-btn" class="swp-button" data-swp-modal="#swp-wake-up-indexer-modal">Wake Up Indexer</button>
                                    </div>
                                    <span class="swp-label">
                                        If the indexer appears to be stuck, first review the PHP error log to see if anything needs to be fixed before waking it up. The indexer can become stuck when customizations are not working as expected.
                                    </span>
                                </label>
                            </div>

                        </div>

                    </div>

                </div>

            </div>  <!-- End Troubleshooting collapse -->

            <div id="swp-wake-up-indexer-modal" class="swp-modal swp-modal--centered swp-modal-xs" style="display: none;">

                <div class="swp-modal--header swp-bg--gray">

                    <div class="swp-flex--row swp-justify-between swp-flex--align-c">

                        <h1 class="swp-h1 swp-font-size16">
                            Wake Up Indexer
                        </h1>

                        <button class="swp-modal--close">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#646970"/>
                            </svg>
                        </button>

                    </div>

                </div>

                <div class="swp-modal--content">

                    <p>Are you sure? The existing background process will be destroyed and then restarted.</p>

                </div> <!-- .swp-modal--content -->

                <div class="swp-modal--footer">
                    <div class="swp-flex--row sm:swp-flex--align-start swp-flex--gap15">

                        <button type="button" id="swp-wake-up-indexer-continue-btn" class="swp-button swp-button--green">
                            Continue
                        </button>

                        <button type="button" class="swp-button swp-modal--cancel">
                            Cancel
                        </button>

                    </div>
                </div>

            </div>

        </div>

        <div class="swp-modal--bg"></div>
		<?php
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

	/**
	 * AJAX callback to wake the indexer.
	 *
	 * @since 4.3.0
	 */
	public static function wake_indexer() {

		Utils::check_ajax_permissions();

		$indexer = \SearchWP::$indexer;
		$indexer->_wake_up();

		wp_send_json_success();
	}
}
