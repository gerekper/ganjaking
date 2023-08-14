<?php

/**
 * SearchWP ImportExportView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\Settings;
use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class ImportExportView is responsible for providing the UI for Import/Export.
 *
 * @since 4.3.0
 */
class ImportExportView {

	private static $slug = 'import-export';

	/**
	 * ImportExportView constructor.
	 *
	 * @since 4.3.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'tools' ) ) {
			new NavTab([
				'page'       => 'tools',
				'tab'        => self::$slug,
				'label'      => __( 'Import/Export', 'searchwp' ),
				'is_default' => true,
			]);
		}

		if ( Utils::is_swp_admin_page( 'tools', 'default' ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'import_settings', [ __CLASS__, 'import_settings' ] );
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
				Utils::$slug . 'style',
			]
		);

		wp_enqueue_script(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/import-export.js',
			[
                Utils::$slug . 'collapse',
                Utils::$slug . 'modal',
            ],
			SEARCHWP_VERSION,
			true
		);

		$settings = [
			'debug',
			'do_suggestions',
			'document_content_reset',
			'hide_announcements',
			'highlighting',
			'indexer_paused',
			'nuke_on_delete',
			'parse_shortcodes',
			'partial_matches',
			'quoted_search_support',
			'reduced_indexer_aggressiveness',
			'remove_min_word_length',
			'tokenize_pattern_matches',
		];

		$stopwords = new \SearchWP\Logic\Stopwords();
		$synonyms  = new \SearchWP\Logic\Synonyms();

		Utils::localize_script( $handle, [
			'engines'   => Settings::_get_engines_settings(),
			'settings'  => call_user_func_array( 'array_merge', array_map( function( $key ) {
				return [ $key => Settings::get_single( $key, 'boolean' ) ];
			}, $settings ) ),
			'stopwords' => $stopwords->get(),
			'synonyms'  => $synonyms->get(),
		] );
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.3.0
	 */
	public static function render() {

		?>
        <div class="swp-content-container">

            <div id="import-export">

                <div class="swp-collapse swp-opened">

                    <div class="swp-collapse--header">

                        <h2 class="swp-h2">
                            Import
                        </h2>

                        <button class="swp-expand--button">
                            <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                            </svg>
                        </button>

                    </div>

                    <div class="swp-collapse--content">

                        <div class="swp-row">

                            <div class="swp-flex--row">

                                <p class="swp-p swp-margin-b30">
                                    To import: Paste settings and click "Import Settings".
                                </p>

                            </div>

                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                                <div class="swp-col swp-col--title-width">
                                    <h3 class="swp-h3">
                                        Import
                                    </h3>
                                </div>

                                <div class="swp-col sm:swp-w-full">

                                    <textarea id="swp-tools-import" class="swp-textarea swp-w-full swp-margin-b15" name="swp-tools-import" rows="10"></textarea>

                                    <div class="swp-flex--row swp-flex--gap17 swp-flex--align-c">
                                        <button type="button" id="swp-tools-import-btn" class="swp-button" data-swp-modal="#swp-tools-import-modal">
                                            Import Settings
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>


                <div class="swp-collapse swp-opened">

                    <div class="swp-collapse--header">

                        <h2 class="swp-h2">
                            Export
                        </h2>

                        <button class="swp-expand--button">
                            <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                            </svg>
                        </button>

                    </div>

                    <div class="swp-collapse--content">

                        <div class="swp-row">

                            <div class="swp-flex--row">

                                <p class="swp-p swp-margin-b30">
                                    To export: Choose Export Items and click "Copy to Clipboard".
                                </p>

                            </div>

                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                                <div class="swp-col swp-col--title-width">
                                    <h3 class="swp-h3">
                                        Export Items
                                    </h3>
                                </div>

                                <div class="swp-col sm:swp-w-full">

                                    <div id="swp-tools-export-items" class="swp-flex--row sm:swp-flex--col swp-flex--gap20 swp-margin-b30">

                                        <label class="swp-label">
                                            <input class="swp-checkbox" data-export-item="engines" type="checkbox" checked>
                                            Engines
                                        </label>

                                        <label class="swp-label">
                                            <input class="swp-checkbox" data-export-item="settings" type="checkbox" checked>
                                            Settings
                                        </label>

                                        <label class="swp-label">
                                            <input class="swp-checkbox" data-export-item="stopwords" type="checkbox" checked>
                                            Stopwords
                                        </label>

                                        <label class="swp-label">
                                            <input class="swp-checkbox" data-export-item="synonyms" type="checkbox" checked>
                                            Synonyms
                                        </label>

                                    </div>

                                    <textarea id="swp-tools-export" class="swp-textarea swp-w-full swp-margin-b15" name="swp-tools-export" rows="10" readonly></textarea>

                                    <div class="swp-flex--row swp-flex--gap17 swp-flex--align-c">
                                        <button id="swp-tools-export-copy" class="swp-button">
                                            Copy to Clipboard
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div id="swp-tools-import-modal" class="swp-modal swp-modal--centered swp-modal-xs" style="display: none;">

                <div class="swp-modal--header swp-bg--gray">

                    <div class="swp-flex--row swp-justify-between swp-flex--align-c">

                        <h1 class="swp-h1 swp-font-size16">
                            Import Settings
                        </h1>

                        <button class="swp-modal--close">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#646970"/>
                            </svg>
                        </button>

                    </div>

                </div>

                <div class="swp-modal--content">

                    <p>Existing settings of the same type will be overwritten. Continue?</p>

                </div> <!-- .swp-modal--content -->

                <div class="swp-modal--footer">
                    <div class="swp-flex--row sm:swp-flex--align-start swp-flex--gap15">

                        <button type="button" id="swp-tools-import-continue-btn" class="swp-button swp-button--green">
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
	 * AJAX callback to import engines.
	 *
	 * @since 4.3.0
	 */
	public static function import_settings() {

		Utils::check_ajax_permissions();

		$settings = isset( $_REQUEST['settings'] ) ? json_decode( stripslashes( $_REQUEST['settings'] ), true ) : false;

		if ( ! is_null( $settings['engines'] ) ) {
			// Run these Engines through the saving process which validates and persists.
			$engines_view = new \SearchWP\Admin\Views\EnginesView();
			$engines_view->update_engines( $settings['engines'] );
		}

		if ( ! is_null( $settings['settings'] ) && is_array( $settings['settings'] ) ) {
			foreach ( $settings['settings'] as $setting => $value ) {
				Settings::update( $setting, $value );
			}
		}

		if ( ! is_null( $settings['stopwords'] ) && is_array( $settings['stopwords'] ) ) {
			$stopwords = new \SearchWP\Logic\Stopwords();
			$stopwords->save( $settings['stopwords'] );
		}

		if ( ! is_null( $settings['synonyms'] ) && is_array( $settings['synonyms'] ) ) {
			$synonyms = new \SearchWP\Logic\Synonyms();
			$synonyms->save( $settings['synonyms'] );
		}

		wp_send_json_success();
	}
}
