<?php

/**
 * SearchWP GlobalRulesView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Admin\NavTab;
use SearchWP\Logic\Synonyms;
use SearchWP\Logic\Stopwords;

/**
 * Class GlobalRulesView is responsible for providing the UI for Global Rules.
 *
 * @since 4.3.0
 */
class GlobalRulesView {

	private static $slug = 'global-rules';

	/**
	 * GlobalRulesView constructor.
	 *
	 * @since 4.3.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'algorithm' ) ) {
			new NavTab([
				'page'  => 'algorithm',
				'tab'   => self::$slug,
				'label' => __( 'Global Rules', 'searchwp' ),
			]);
		}

		if ( Utils::is_swp_admin_page( 'algorithm', self::$slug ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'stopwords_suggestions', [ __CLASS__ , 'get_stopwords_suggestions_ajax' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'stopwords_update',      [ __CLASS__ , 'update_stopwords' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'synonyms_update',       [ __CLASS__ , 'update_synonyms' ] );
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.3.0
	 */
	public static function assets() {

		$handle = SEARCHWP_PREFIX . self::$slug;

		wp_enqueue_style(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/global-rules.css',
			[
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'draggable',
				Utils::$slug . 'input',
				Utils::$slug . 'tooltip',
				Utils::$slug . 'toggle-switch',
				Utils::$slug . 'pills',
				Utils::$slug . 'modal',
				Utils::$slug . 'style',
            ],
			SEARCHWP_VERSION
		);

		wp_enqueue_script(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/global-rules.js',
			[
				Utils::$slug . 'collapse',
				Utils::$slug . 'pills',
				Utils::$slug . 'modal',
            ],
			SEARCHWP_VERSION,
			true
		);

		$stopwords = new Stopwords();
		$synonyms  = new Synonyms();

		Utils::localize_script( $handle, [
			'stopwords' => [
				'list'     => $stopwords->get(),
				'defaults' => $stopwords->get_default(),
				'suggest'  => (bool) apply_filters( 'searchwp\stopwords\suggestions', true ),
			],
			'synonyms' => $synonyms->get(),
		] );
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.3.0
	 */
	public static function render() {
		// This node structure is as such to inherit WP-admin CSS.
		?>
        <div class="swp-content-container">

            <div id="global-rules">

                <div class="swp-collapse swp-opened">

                    <div class="swp-collapse--header">

                        <h2 class="swp-h2">
                            Synonyms
                        </h2>

                        <div class="swp-flex--item">

                            <div class="swp-flex--row swp-flex--gap20 swp-flex--align-c">

                                <div class="swp-actions-menu">

                                    <button class="swp-action-menu--button swp-button--flex-content">

                                        Actions

                                        <svg width="10" height="6" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                                        </svg>

                                    </button>

                                    <ul class="swp-swp-action-menu--list swp-display-none">
                                        <li id="swp-synonyms-sort-asc" class="swp-action-menu--item">Sort ASC</li>
                                        <li id="swp-synonyms-sort-desc" class="swp-action-menu--item">Sort DESC</li>
                                        <li id="swp-synonyms-remove-all" class="swp-action-menu--item">Remove All</li>
                                    </ul>

                                </div>

                                <button class="swp-expand--button">
                                    <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="swp-collapse--content">

                        <div class="swp-row">

                            <p class="swp-p">
                                Synonyms facilitate replacement of search terms. Use * wildcard for partial matching.
                                <a class="swp-a" href="https://searchwp.com/?p=424396#synonyms" target="_blank">More info &rarr;</a>
                            </p>

                        </div>

                        <div class="swp-row">

                            <div class="swp-flex--row swp-flex--gap20 sm:swp-display-none">

                                <div class="swp-flex--item swp-w-1/3">

                                    <div class="swp-flex--row swp-flex--align-c swp-flex--gap5">

                                        <p class="swp-label swp-b">Search Term(s)</p>

                                        <div class="swp-tooltip--container">

                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                            </svg>

                                            <div class="swp-tooltip--text">
                                                <?php esc_html_e( 'What visitors search for (separate different search terms by commas)', 'searchwp' ); ?>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="swp-flex--item swp-space-fix"></div>

                                <div class="swp-flex--item swp-w-1/3">

                                    <div class="swp-flex--row swp-flex--align-c swp-flex--gap5">

                                        <p class="swp-label swp-b">Synonym(s)</p>

                                        <div class="swp-tooltip--container">

                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                            </svg>

                                            <div class="swp-tooltip--text">
	                                            <?php esc_html_e( 'Term(s) that are synonymous with the Search Term(s)', 'searchwp' ); ?>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="swp-flex--item swp-w-1/6">

                                    <div class="swp-flex--row swp-flex--align-c swp-flex--gap5">

                                        <p class="swp-label swp-b">Replace</p>

                                        <div class="swp-tooltip--container">

                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                            </svg>

                                            <div class="swp-tooltip--text">
	                                            <?php esc_html_e( 'When enabled, original Search Term(s) will be removed', 'searchwp' ); ?>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="swp-flex--item swp-w-1/6">

                                    <p class="swp-label swp-b swp-justify-center">Action</p>

                                </div>

                            </div>



                            <div id="swp-synonyms" class="swp-dragsort--container swp-margin-t15 swp-margin-b30">

                                <?php $synonyms = ( new Synonyms() )->get(); ?>

                                <?php foreach ( $synonyms as $synonym ) : ?>

                                    <div class="swp-synonym swp-row--draggable swp-flex--row swp-flex--align-c sm:swp-flex--align-start swp-flex--gap20 sm:swp-margin-b30">

                                        <div class="swp-dragsort-handle">
                                            <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.66667 13.6673C1.20833 13.6673 0.816111 13.5043 0.49 13.1782C0.163333 12.8515 0 12.459 0 12.0007C0 11.5423 0.163333 11.1498 0.49 10.8232C0.816111 10.497 1.20833 10.334 1.66667 10.334C2.125 10.334 2.5175 10.497 2.84417 10.8232C3.17028 11.1498 3.33333 11.5423 3.33333 12.0007C3.33333 12.459 3.17028 12.8515 2.84417 13.1782C2.5175 13.5043 2.125 13.6673 1.66667 13.6673ZM6.66667 13.6673C6.20833 13.6673 5.81611 13.5043 5.49 13.1782C5.16333 12.8515 5 12.459 5 12.0007C5 11.5423 5.16333 11.1498 5.49 10.8232C5.81611 10.497 6.20833 10.334 6.66667 10.334C7.125 10.334 7.5175 10.497 7.84417 10.8232C8.17028 11.1498 8.33333 11.5423 8.33333 12.0007C8.33333 12.459 8.17028 12.8515 7.84417 13.1782C7.5175 13.5043 7.125 13.6673 6.66667 13.6673ZM1.66667 8.66732C1.20833 8.66732 0.816111 8.50398 0.49 8.17732C0.163333 7.85121 0 7.45898 0 7.00065C0 6.54232 0.163333 6.14982 0.49 5.82315C0.816111 5.49704 1.20833 5.33398 1.66667 5.33398C2.125 5.33398 2.5175 5.49704 2.84417 5.82315C3.17028 6.14982 3.33333 6.54232 3.33333 7.00065C3.33333 7.45898 3.17028 7.85121 2.84417 8.17732C2.5175 8.50398 2.125 8.66732 1.66667 8.66732ZM6.66667 8.66732C6.20833 8.66732 5.81611 8.50398 5.49 8.17732C5.16333 7.85121 5 7.45898 5 7.00065C5 6.54232 5.16333 6.14982 5.49 5.82315C5.81611 5.49704 6.20833 5.33398 6.66667 5.33398C7.125 5.33398 7.5175 5.49704 7.84417 5.82315C8.17028 6.14982 8.33333 6.54232 8.33333 7.00065C8.33333 7.45898 8.17028 7.85121 7.84417 8.17732C7.5175 8.50398 7.125 8.66732 6.66667 8.66732ZM1.66667 3.66732C1.20833 3.66732 0.816111 3.50398 0.49 3.17732C0.163333 2.85121 0 2.45898 0 2.00065C0 1.54232 0.163333 1.1501 0.49 0.823984C0.816111 0.497318 1.20833 0.333984 1.66667 0.333984C2.125 0.333984 2.5175 0.497318 2.84417 0.823984C3.17028 1.1501 3.33333 1.54232 3.33333 2.00065C3.33333 2.45898 3.17028 2.85121 2.84417 3.17732C2.5175 3.50398 2.125 3.66732 1.66667 3.66732ZM6.66667 3.66732C6.20833 3.66732 5.81611 3.50398 5.49 3.17732C5.16333 2.85121 5 2.45898 5 2.00065C5 1.54232 5.16333 1.1501 5.49 0.823984C5.81611 0.497318 6.20833 0.333984 6.66667 0.333984C7.125 0.333984 7.5175 0.497318 7.84417 0.823984C8.17028 1.1501 8.33333 1.54232 8.33333 2.00065C8.33333 2.45898 8.17028 2.85121 7.84417 3.17732C7.5175 3.50398 7.125 3.66732 6.66667 3.66732Z" fill="#0E2121" fill-opacity="0.4"/>
                                            </svg>
                                        </div>

                                        <div class="swp-flex--row sm:swp-flex--col swp-flex--align-c sm:swp-flex--align-start sm:swp-items-stretch swp-flex--gap20">

                                            <div class="swp-flex--item swp-w-1/3 sm:swp-w-full">

                                                <div class="swp-display-none sm:swp-display-block swp-margin-b10">

                                                    <div class="swp-flex--row swp-flex--gap5">

                                                        <label class="swp-label swp-b" for="">
                                                            Search Term(s)
                                                        </label>

                                                        <div class="swp-tooltip--container">

                                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                                            </svg>

                                                            <div class="swp-tooltip--text">
                                                                <?php esc_html_e( 'What visitors search for (separate different search terms by commas)', 'searchwp' ); ?>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <input class="swp-synonym-sources-input swp-input swp-w-full" type="text" value="<?php echo esc_attr( $synonym['sources'] ); ?>">

                                            </div>

                                            <div class="swp-flex--item swp-w-1/3 sm:swp-w-full">

                                                <div class="swp-display-none sm:swp-display-block swp-margin-b10">

                                                    <div class="swp-flex--row swp-flex--gap5">

                                                        <label class="swp-label swp-b" for="">
                                                            Synonym(s)
                                                        </label>

                                                        <div class="swp-tooltip--container">

                                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                                            </svg>

                                                            <div class="swp-tooltip--text">
                                                                <?php esc_html_e( 'Term(s) that are synonymous with the Search Term(s)', 'searchwp' ); ?>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <input class="swp-synonym-synonyms-input swp-input swp-w-full" type="text" value="<?php echo esc_attr( $synonym['synonyms'] ); ?>">

                                            </div>

                                            <div class="swp-flex--item swp-w-1/6 sm:swp-w-full swp-text-center sm:swp-text-left">

                                                <label class="swp-label sm:swp-display-flex--row swp-flex--gap5">

                                                    <input class="swp-synonym-replace-checkbox swp-checkbox" type="checkbox"<?php checked( $synonym['replace'] ); ?>>

                                                    <span class="sm:swp-display-none">Replace</span>

                                                    <span class="swp-display-none sm:swp-display-block">

                                                    <span class="swp-flex--row swp-flex--gap5">

                                                        Replace

                                                        <span class="swp-tooltip--container">

                                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                                            </svg>

                                                            <span class="swp-tooltip--text">
                                                                <?php esc_html_e( 'When enabled, original Search Term(s) will be removed', 'searchwp' ); ?>
                                                            </span>

                                                        </span>

                                                    </span>

                                                </span>

                                                </label>

                                            </div>

                                            <div class="swp-flex--item swp-w-1/6 sm:swp-w-full swp-text-center sm:swp-text-left">

                                                <button class="swp-synonym-delete swp-button swp-button--trash">
                                                    <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1.77277 15.6668C1.77277 16.7144 2.57857 17.5716 3.56343 17.5716H10.7261C11.7109 17.5716 12.5167 16.7144 12.5167 15.6668V4.23823H1.77277V15.6668ZM3.56343 6.143H10.7261V15.6668H3.56343V6.143ZM10.2784 1.38109L9.38307 0.428711H4.90642L4.01109 1.38109H0.877441V3.28585H13.4121V1.38109H10.2784Z" fill="#0E2121" fill-opacity="0.7"/>
                                                    </svg>
                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                                <template id="swp-synonym-template">
                                    <div class="swp-synonym swp-row--draggable swp-flex--row swp-flex--align-c sm:swp-flex--align-start swp-flex--gap20 sm:swp-margin-b30">

                                        <div class="swp-dragsort-handle">
                                            <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.66667 13.6673C1.20833 13.6673 0.816111 13.5043 0.49 13.1782C0.163333 12.8515 0 12.459 0 12.0007C0 11.5423 0.163333 11.1498 0.49 10.8232C0.816111 10.497 1.20833 10.334 1.66667 10.334C2.125 10.334 2.5175 10.497 2.84417 10.8232C3.17028 11.1498 3.33333 11.5423 3.33333 12.0007C3.33333 12.459 3.17028 12.8515 2.84417 13.1782C2.5175 13.5043 2.125 13.6673 1.66667 13.6673ZM6.66667 13.6673C6.20833 13.6673 5.81611 13.5043 5.49 13.1782C5.16333 12.8515 5 12.459 5 12.0007C5 11.5423 5.16333 11.1498 5.49 10.8232C5.81611 10.497 6.20833 10.334 6.66667 10.334C7.125 10.334 7.5175 10.497 7.84417 10.8232C8.17028 11.1498 8.33333 11.5423 8.33333 12.0007C8.33333 12.459 8.17028 12.8515 7.84417 13.1782C7.5175 13.5043 7.125 13.6673 6.66667 13.6673ZM1.66667 8.66732C1.20833 8.66732 0.816111 8.50398 0.49 8.17732C0.163333 7.85121 0 7.45898 0 7.00065C0 6.54232 0.163333 6.14982 0.49 5.82315C0.816111 5.49704 1.20833 5.33398 1.66667 5.33398C2.125 5.33398 2.5175 5.49704 2.84417 5.82315C3.17028 6.14982 3.33333 6.54232 3.33333 7.00065C3.33333 7.45898 3.17028 7.85121 2.84417 8.17732C2.5175 8.50398 2.125 8.66732 1.66667 8.66732ZM6.66667 8.66732C6.20833 8.66732 5.81611 8.50398 5.49 8.17732C5.16333 7.85121 5 7.45898 5 7.00065C5 6.54232 5.16333 6.14982 5.49 5.82315C5.81611 5.49704 6.20833 5.33398 6.66667 5.33398C7.125 5.33398 7.5175 5.49704 7.84417 5.82315C8.17028 6.14982 8.33333 6.54232 8.33333 7.00065C8.33333 7.45898 8.17028 7.85121 7.84417 8.17732C7.5175 8.50398 7.125 8.66732 6.66667 8.66732ZM1.66667 3.66732C1.20833 3.66732 0.816111 3.50398 0.49 3.17732C0.163333 2.85121 0 2.45898 0 2.00065C0 1.54232 0.163333 1.1501 0.49 0.823984C0.816111 0.497318 1.20833 0.333984 1.66667 0.333984C2.125 0.333984 2.5175 0.497318 2.84417 0.823984C3.17028 1.1501 3.33333 1.54232 3.33333 2.00065C3.33333 2.45898 3.17028 2.85121 2.84417 3.17732C2.5175 3.50398 2.125 3.66732 1.66667 3.66732ZM6.66667 3.66732C6.20833 3.66732 5.81611 3.50398 5.49 3.17732C5.16333 2.85121 5 2.45898 5 2.00065C5 1.54232 5.16333 1.1501 5.49 0.823984C5.81611 0.497318 6.20833 0.333984 6.66667 0.333984C7.125 0.333984 7.5175 0.497318 7.84417 0.823984C8.17028 1.1501 8.33333 1.54232 8.33333 2.00065C8.33333 2.45898 8.17028 2.85121 7.84417 3.17732C7.5175 3.50398 7.125 3.66732 6.66667 3.66732Z" fill="#0E2121" fill-opacity="0.4"/>
                                            </svg>
                                        </div>

                                        <div class="swp-flex--row sm:swp-flex--col swp-flex--align-c sm:swp-flex--align-start sm:swp-items-stretch swp-flex--gap20">

                                            <div class="swp-flex--item swp-w-1/3 sm:swp-w-full">

                                                <div class="swp-display-none sm:swp-display-block swp-margin-b10">

                                                    <div class="swp-flex--row swp-flex--gap5">

                                                        <label class="swp-label swp-b" for="">
                                                            Search Term(s)
                                                        </label>

                                                        <div class="swp-tooltip--container">

                                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                                            </svg>

                                                            <div class="swp-tooltip--text">
                                                                <?php esc_html_e( 'What visitors search for (separate different search terms by commas)', 'searchwp' ); ?>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <input class="swp-synonym-sources-input swp-input swp-w-full" type="text" value="">

                                            </div>

                                            <div class="swp-flex--item swp-w-1/3 sm:swp-w-full">

                                                <div class="swp-display-none sm:swp-display-block swp-margin-b10">

                                                    <div class="swp-flex--row swp-flex--gap5">

                                                        <label class="swp-label swp-b" for="">
                                                            Synonym(s)
                                                        </label>

                                                        <div class="swp-tooltip--container">

                                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                                            </svg>

                                                            <div class="swp-tooltip--text">
                                                                <?php esc_html_e( 'Term(s) that are synonymous with the Search Term(s)', 'searchwp' ); ?>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <input class="swp-synonym-synonyms-input swp-input swp-w-full" type="text" value="">

                                            </div>

                                            <div class="swp-flex--item swp-w-1/6 sm:swp-w-full swp-text-center sm:swp-text-left">

                                                <label class="swp-label sm:swp-display-flex--row swp-flex--gap5">

                                                    <input class="swp-synonym-replace-checkbox swp-checkbox" type="checkbox">

                                                    <span class="sm:swp-display-none">Replace</span>

                                                    <span class="swp-display-none sm:swp-display-block">

                                                    <span class="swp-flex--row swp-flex--gap5">

                                                        Replace

                                                        <span class="swp-tooltip--container">

                                                            <svg  class="swp-help-icon swp-tooltip" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 8.50008C0 3.90008 3.73333 0.166748 8.33333 0.166748C12.9333 0.166748 16.6667 3.90008 16.6667 8.50008C16.6667 13.1001 12.9333 16.8334 8.33333 16.8334C3.73333 16.8334 0 13.1001 0 8.50008ZM9.16667 11.8334V13.5001H7.5V11.8334H9.16667ZM8.33334 15.1667C4.65834 15.1667 1.66667 12.1751 1.66667 8.50008C1.66667 4.82508 4.65834 1.83341 8.33334 1.83341C12.0083 1.83341 15 4.82508 15 8.50008C15 12.1751 12.0083 15.1667 8.33334 15.1667ZM5 6.83342C5 4.99175 6.49167 3.50008 8.33333 3.50008C10.175 3.50008 11.6667 4.99175 11.6667 6.83342C11.6667 7.90251 11.0083 8.47785 10.3673 9.03803C9.75918 9.56947 9.16667 10.0873 9.16667 11.0001H7.5C7.5 9.48235 8.2851 8.88057 8.97537 8.35148C9.51686 7.93642 10 7.56609 10 6.83342C10 5.91675 9.25 5.16675 8.33333 5.16675C7.41667 5.16675 6.66667 5.91675 6.66667 6.83342H5Z" fill="#0E2121" fill-opacity="0.3"/>
                                                            </svg>

                                                            <span class="swp-tooltip--text">
                                                                <?php esc_html_e( 'When enabled, original Search Term(s) will be removed', 'searchwp' ); ?>
                                                            </span>

                                                        </span>

                                                    </span>

                                                </span>

                                                </label>

                                            </div>

                                            <div class="swp-flex--item swp-w-1/6 sm:swp-w-full swp-text-center sm:swp-text-left">

                                                <button class="swp-synonym-delete swp-button swp-button--trash">
                                                    <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1.77277 15.6668C1.77277 16.7144 2.57857 17.5716 3.56343 17.5716H10.7261C11.7109 17.5716 12.5167 16.7144 12.5167 15.6668V4.23823H1.77277V15.6668ZM3.56343 6.143H10.7261V15.6668H3.56343V6.143ZM10.2784 1.38109L9.38307 0.428711H4.90642L4.01109 1.38109H0.877441V3.28585H13.4121V1.38109H10.2784Z" fill="#0E2121" fill-opacity="0.7"/>
                                                    </svg>
                                                </button>

                                            </div>

                                        </div>

                                    </div>
                                </template>

                            </div>

                            <div class="swp-flex--row sm:swp-flex--wrap swp-flex--gap12">
                                <button id="swp-synonyms-save" class="swp-button swp-button--green">Save Synonyms</button>
                                <button id="swp-synonyms-add-new-btn" class="swp-button swp-button--flex-content">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.41667 2.91667H5.25V5.25H2.91667V6.41667H5.25V8.75H6.41667V6.41667H8.75V5.25H6.41667V2.91667ZM5.83333 0C2.61333 0 0 2.61333 0 5.83333C0 9.05333 2.61333 11.6667 5.83333 11.6667C9.05333 11.6667 11.6667 9.05333 11.6667 5.83333C11.6667 2.61333 9.05333 0 5.83333 0ZM5.83333 10.5C3.26083 10.5 1.16667 8.40583 1.16667 5.83333C1.16667 3.26083 3.26083 1.16667 5.83333 1.16667C8.40583 1.16667 10.5 3.26083 10.5 5.83333C10.5 8.40583 8.40583 10.5 5.83333 10.5Z" fill="#3E4D4D"/>
                                    </svg>
                                    Add New
                                </button>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="swp-collapse swp-opened">

                    <div class="swp-collapse--header">

                        <h2 class="swp-h2">
                            Stopwords
                        </h2>

                        <div class="swp-flex--item">

                            <div class="swp-flex--row swp-flex--gap20 swp-flex--align-c">

                                <div class="swp-actions-menu">

                                    <button class="swp-action-menu--button swp-button--flex-content">

                                        Actions

                                        <svg width="10" height="6" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                                        </svg>

                                    </button>

                                    <ul class="swp-swp-action-menu--list swp-display-none">
                                        <li id="swp-stopwords-view-suggestions" class="swp-action-menu--item" data-swp-modal="#swp-stopwords-suggestions-modal">View Suggestions</li>
                                        <li id="swp-stopwords-sort-asc" class="swp-action-menu--item">Sort Alphabetically</li>
                                        <li id="swp-stopwords-restore-defaults" class="swp-action-menu--item">Restore Defaults</li>
                                        <li id="swp-stopwords-clear" class="swp-action-menu--item">Clear Stopwords</li>
                                    </ul>

                                </div>

                                <button class="swp-expand--button">
                                    <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="swp-collapse--content">

                        <div class="swp-row">

                            <p class="swp-p">
                                Stopwords are ignored so as to improve relevancy and performance.
                                <a class="swp-a" href="https://searchwp.com/?p=424396#stopwords" target="_blank">More info &rarr;</a>
                            </p>

                        </div>

                        <div class="swp-row">

	                        <?php $stopwords = ( new Stopwords() )->get(); ?>

                            <div id="swp-stopwords" class="swp-pills">
                                <div class="swp-pills-container swp-flex--row swp-flex--wrap swp-flex--gap9 swp-margin-b30">
                                    <?php foreach ( $stopwords as $stopword ) : ?>
                                        <span class="swp-pill">
                                            <span class="swp-pill-text"><?php echo esc_html( $stopword ); ?></span>
                                            <button class="swp-pill-delete">
                                                <svg width="7" height="7" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.7" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#0e2121"/>
                                                </svg>
                                            </button>
                                        </span>
                                    <?php endforeach; ?>
                                    <input class="swp-input swp-pills-input">
                                </div>

                                <template class="swp-pill-template">
                                    <span class="swp-pill">
                                        <span class="swp-pill-text"></span>
                                        <button class="swp-pill-delete">
                                            <svg width="7" height="7" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.7" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#0e2121"/>
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                            </div>

                            <div class="swp-flex--row sm:swp-flex--wrap swp-flex--gap12">
                                <button id="swp-stopwords-save" class="swp-button swp-button--green">Save Stopwords</button>
                                <button id="swp-stopwords-add-new-btn" class="swp-button swp-button--flex-content">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.41667 2.91667H5.25V5.25H2.91667V6.41667H5.25V8.75H6.41667V6.41667H8.75V5.25H6.41667V2.91667ZM5.83333 0C2.61333 0 0 2.61333 0 5.83333C0 9.05333 2.61333 11.6667 5.83333 11.6667C9.05333 11.6667 11.6667 9.05333 11.6667 5.83333C11.6667 2.61333 9.05333 0 5.83333 0ZM5.83333 10.5C3.26083 10.5 1.16667 8.40583 1.16667 5.83333C1.16667 3.26083 3.26083 1.16667 5.83333 1.16667C8.40583 1.16667 10.5 3.26083 10.5 5.83333C10.5 8.40583 8.40583 10.5 5.83333 10.5Z" fill="#3E4D4D"/>
                                    </svg>
                                    Add New
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div id="swp-stopwords-suggestions-modal" class="swp-modal swp-modal--centered swp-modal-s" style="display: none;">

                <div class="swp-modal--header swp-bg--gray">

                    <div class="swp-flex--row swp-justify-between swp-flex--align-c">

                        <h1 class="swp-h1 swp-font-size16">
                            Suggested Stopwords
                        </h1>

                        <button class="swp-modal--close">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#646970"/>
                            </svg>
                        </button>

                    </div>

                </div>

                <div class="swp-modal--content">

                    <div class="swp-flex--row swp-flex--gap20 swp-flex--align-c">

                        <p class="swp-label swp-b swp-flex--same-size">Term</p>

                        <p class="swp-label swp-b swp-flex--same-size">Prevalence</p>

                        <p class="swp-label swp-b swp-flex--same-size">Action</p>

                    </div>

	                <?php $suggested_stopwords = self::get_stopwords_suggestions(); ?>

                    <div id="swp-suggested-stopwords">
                        <?php foreach ( $suggested_stopwords as $suggested_stopword ) : ?>
                            <div class="swp-suggested-stopword swp-flex--row swp-flex--gap20 swp-flex--align-c">

                                <p class="swp-suggested-stopword-name swp-label swp-flex--same-size"><?php echo esc_html( $suggested_stopword['token'] ) ?></p>

                                <p class="swp-label swp-flex--same-size"><?php echo esc_html( $suggested_stopword['prevalence'] ) ?></p>

                                <div class="swp-flex--same-size">

                                    <button class="swp-suggested-stopword-add swp-button swp-button--slim">
                                        Add Stopword
                                    </button>

                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>

                </div> <!-- .swp-modal--content -->

            </div>

        </div>

        <div class="swp-modal--bg"></div>

		<?php
	}

	/**
	 * AJAX callback to update saved synonyms.
	 *
	 * @since 4.3.0
	 */
	public static function update_synonyms() {

		Utils::check_ajax_permissions();

		$update = isset( $_REQUEST['synonyms'] ) ? json_decode( stripslashes( $_REQUEST['synonyms'] ), true ) : false;

		$synonyms = new Synonyms();
		$update = $synonyms->save( $update );

		wp_send_json_success( $update );
	}

	/**
	 * AJAX callback to update saved stopwords.
	 *
	 * @since 4.3.0
	 */
	public static function update_stopwords() {

		Utils::check_ajax_permissions();

		$update = isset( $_REQUEST['stopwords'] ) ? json_decode( stripslashes( $_REQUEST['stopwords'] ), true ) : false;

		$stopwords = new Stopwords();
		$update = $stopwords->save( $update );

		wp_send_json_success( $update );
	}

	/**
	 * Get suggested stopwords.
	 *
	 * @since 4.3.0
	 */
	private static function get_stopwords_suggestions() {

		$stopwords = new Stopwords();

		return $stopwords->get_suggestions( [
			'limit'     => absint( apply_filters( 'searchwp\stopwords\suggestions\limit', 20 ) ),
			'threshold' => (float) apply_filters( 'searchwp\stopwords\suggestions\threshold', 0.3 ),
		] );
	}

	/**
	 * AJAX callback to get suggested stopwords.
	 *
	 * @since 4.3.0
	 */
	public static function get_stopwords_suggestions_ajax() {

		Utils::check_ajax_permissions();

		wp_send_json_success( self::get_stopwords_suggestions() );
	}
}
