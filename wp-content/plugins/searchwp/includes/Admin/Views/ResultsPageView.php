<?php
/**
 * SearchWP ResultsPageView.
 *
 * @since 4.3.6
 */

namespace SearchWP\Admin\Views;

use SearchWP\Settings;
use SearchWP\Results\Settings as ResultsSettings;
use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class ResultsPageView is responsible for providing the UI for Results Page.
 *
 * @since 4.3.6
 */
class ResultsPageView {

    private static $slug = 'results-page';

    /**
     * ResultsPageView constructor.
     *
     * @since 4.3.6
     */
    function __construct() {

        if ( Utils::is_swp_admin_page( 'templates' ) ) {
            new NavTab( [
                'page'       => 'templates',
                'tab'        => self::$slug,
                'label'      => __( 'Results Page', 'searchwp' ),
                'is_default' => true,
            ] );
        }

        if ( Utils::is_swp_admin_page( 'templates', 'default' ) ) {
            add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
        }

        add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'save_results_page_settings',  [ __CLASS__, 'save_results_page_settings_ajax' ] );
    }

    /**
     * Outputs the assets needed for the Settings UI.
     *
     * @since 4.3.6
     */
    public static function assets() {

        if ( ! current_user_can( Settings::get_capability() ) ) {
            return;
        }

        $handle = SEARCHWP_PREFIX . self::$slug;

        wp_enqueue_script( 'iris' );

        wp_enqueue_script(
            SEARCHWP_PREFIX . 'choicesjs',
            SEARCHWP_PLUGIN_URL . 'assets/vendor/choicesjs/js/choices-10.2.0.min.js',
            [],
            '10.2.0',
            true
        );

        wp_enqueue_style(
            SEARCHWP_PREFIX . 'choicesjs',
            SEARCHWP_PLUGIN_URL . 'assets/vendor/choicesjs/css/choices-10.2.0.min.css',
            [],
            '10.2.0'
        );

        wp_enqueue_style(
            $handle,
            SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/results-page.css',
            [
                Utils::$slug . 'choicesjs',
                Utils::$slug . 'collapse-layout',
                Utils::$slug . 'color-picker',
                Utils::$slug . 'input',
                Utils::$slug . 'modal',
                Utils::$slug . 'toggle-switch',
                Utils::$slug . 'radio-img',
                Utils::$slug . 'style',
            ],
            SEARCHWP_VERSION
        );

        wp_enqueue_script(
            $handle,
            SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/results-page.js',
            [
                'underscore',
                Utils::$slug . 'choices',
                Utils::$slug . 'collapse',
                Utils::$slug . 'color-picker',
                Utils::$slug . 'copy-input-text',
                Utils::$slug . 'modal',
            ],
            SEARCHWP_VERSION,
            true
        );

        Utils::localize_script( $handle );
    }

    /**
     * Callback for rendering the settings view.
     *
     * @since 4.3.6
     */
    public static function render() {

        if ( ! current_user_can( Settings::get_capability() ) ) {
            return;
        }

        echo '<div class="swp-content-container">';

        self::render_settings_content();

        echo '</div>';
    }

    /**
     * Callback for rendering the settings content.
     *
     * @since 4.3.6
     */
    private static function render_settings_content() {

        $settings = ResultsSettings::get();

        ?>
        <div class="swp-page-header">
            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30 swp-justify-between swp-flex--align-c sm:swp-flex--align-start">

                <div class="swp-flex--row swp-flex--gap15 swp-flex--align-c">
                    <h1 class="swp-h1 swp-page-header--h1">
                        <?php esc_html_e( 'Settings', 'searchwp' ); ?>
                    </h1>
                </div>

                <div class="swp-flex--row swp-flex--gap15 swp-flex--grow0">
                    <button type="button" id="swp-results-page-save" class="swp-button swp-button--green">
                        <?php esc_html_e( 'Save', 'searchwp' ); ?>
                    </button>
                </div>

            </div>
        </div>

        <div class="swp-collapse swp-opened">
            <div class="swp-collapse--header">

                <h2 class="swp-h2">
                    <?php esc_html_e( 'Chose a theme', 'searchwp' ); ?>
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
                        <div class="swp-col swp-col--title-width--sm">
                            <h3 class="swp-h3">
                                <?php esc_html_e( 'Layout Theme', 'searchwp' ); ?>
                            </h3>
                        </div>

                        <div class="swp-col">
                            <div class="swp-flex--row swp-flex--gap20 swp-sf--layout-themes">

                                <div class="swp-flex--grow1 swp-input--radio-img">
                                    <input type="radio" id="swp-alpha-theme" name="swp-layout-theme" value="alpha"<?php checked( $settings['swp-layout-theme'], 'alpha' ); ?> />

                                    <label for="swp-alpha-theme">
                                        <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/alpha.svg' ); ?>" alt="" />
                                        <?php esc_html_e( 'Minimal', 'searchwp' ); ?>
                                    </label>
                                </div>

                                <div class="swp-flex--grow1 swp-input--radio-img">
                                    <input type="radio" id="swp-beta-theme" name="swp-layout-theme" value="beta"<?php checked( $settings['swp-layout-theme'], 'beta' ); ?> />

                                    <label for="swp-beta-theme">
                                        <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/beta.svg' ); ?>" alt="" />
                                        <?php esc_html_e( 'Compact', 'searchwp' ); ?>
                                    </label>
                                </div>

                                <div class="swp-flex--grow1 swp-input--radio-img">
                                    <input type="radio" id="swp-gamma-theme" name="swp-layout-theme" value="gamma"<?php checked( $settings['swp-layout-theme'], 'gamma' ); ?> />

                                    <label for="swp-gamma-theme">
                                        <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/gamma.svg' ); ?>" alt="" />
                                        <?php esc_html_e( 'Columns', 'searchwp' ); ?>
                                    </label>
                                </div>

                                <div class="swp-flex--grow1 swp-input--radio-img">
                                    <input type="radio" id="swp-epsilon-theme" name="swp-layout-theme" value="epsilon"<?php checked( $settings['swp-layout-theme'], 'epsilon' ); ?> />

                                    <label for="swp-epsilon-theme">
                                        <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/epsilon.svg' ); ?>" alt="" />
                                        <?php esc_html_e( 'Medium', 'searchwp' ); ?>
                                    </label>
                                </div>

                                <div class="swp-flex--grow1 swp-input--radio-img">
                                    <input type="radio" id="swp-zeta-theme" name="swp-layout-theme" value="zeta"<?php checked( $settings['swp-layout-theme'], 'zeta' ); ?> />

                                    <label for="swp-zeta-theme">
                                        <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/zeta.svg' ); ?>" alt="" />
                                        <?php esc_html_e( 'Rich', 'searchwp' ); ?>
                                    </label>
                                </div>

                                <div class="swp-flex--grow1 swp-input--radio-img">
                                    <input type="radio" id="swp-combined-theme"  name="swp-layout-theme" value="combined"<?php checked( $settings['swp-layout-theme'], 'combined' ); ?> />

                                    <label for="swp-combined-theme">
                                        <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/combined.svg' ); ?>" alt="" />
                                        <?php esc_html_e( 'Combined', 'searchwp' ); ?>
                                    </label>
                                </div>

                            </div>

                            <h4 class="swp-h4 swp-margin-t30">
                                <?php esc_html_e( 'Theme Preview', 'searchwp' ); ?>
                            </h4>

                            <div class="swp-rp-theme-preview">

                                <input class="swp-input swp-input--search swp-w-full" value="mockup" disabled>

                                <h1>
                                    <?php esc_html_e( 'Search Results for “Mockup”', 'searchwp' ); ?>
                                </h1>

                                <div class="<?php echo esc_attr( self::get_preview_container_classes( $settings ) ); ?>">

                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress001.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>


                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress002.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress003.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress001.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress002.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress003.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="swp-result-item">
                                        <div class="swp-result-item--img-container">
                                            <div class="swp-result-item--img">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/dress001.jpg' ); ?>" alt="">
                                            </div>
                                        </div>

                                        <div class="swp-result-item--info-container">
                                            <h2 class="swp-result-item--h2">
                                                <a class="swp-a" role="link" aria-disabled="true">
                                                    <?php esc_html_e( 'Create Mockups - Balsamiq Wireframes', 'searchwp' ); ?>
                                                </a>
                                            </h2>

                                            <p class="swp-result-item--desc"<?php echo empty( $settings['swp-description-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'It’s like sketching on a whiteboard. Go On, Unleash Your Creativity! Life’s too short for bad software.', 'searchwp' ); ?>
                                            </p>

                                            <?php if ( self::is_ecommerce_plugin_active() ) : ?>
                                                <p class="swp-result-item--price">
                                                    $138.00 - $156.00
                                                </p>
                                            <?php endif; ?>

                                            <button class="swp-button swp-result-item--button" type="button" disabled<?php echo empty( $settings['swp-button-enabled'] ) ? ' style="display: none;"' : ''; ?>>
                                                <?php esc_html_e( 'Read More', 'searchwp' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <div class="swp-results-pagination">
                                    <ul class="<?php echo esc_attr( self::get_pagination_container_class( $settings ) ); ?>">
                                        <li class="swp-results-pagination--inactive-item">
                                            <a class="swp-a swp-rp-pagination--prev" role="link" aria-disabled="true">
                                                <svg class="swp-rp-pagination--svg" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.85 10.718a.625.625 0 0 1-.183-.442v-.317a.642.642 0 0 1 .183-.441l4.283-4.275a.417.417 0 0 1 .592 0l.592.591a.408.408 0 0 1 0 .584l-3.709 3.7 3.709 3.7a.417.417 0 0 1 0 .591l-.592.584a.416.416 0 0 1-.592 0L6.85 10.718Z" fill="#BCBCBC"/></svg>
                                                <span class="swp-rp-pagination--link">
                                                    <?php esc_html_e( 'Prev', 'searchwp' ); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="swp-results-pagination--active-item"><a class="swp-a" role="link" aria-disabled="true">1</a></li>
                                        <li><a class="swp-a" role="link" aria-disabled="true">2</a></li>
                                        <li><a class="swp-a" role="link" aria-disabled="true">3</a></li>
                                        <li><a class="swp-a" role="link" aria-disabled="true">&hellip;</a></li>
                                        <li><a class="swp-a" role="link" aria-disabled="true">32</a></li>
                                        <li>
                                            <a class="swp-a swp-rp-pagination--next" role="link" aria-disabled="true">
                                            <svg class="swp-rp-pagination--svg" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.15 10.718a.625.625 0 0 0 .183-.442v-.317a.642.642 0 0 0-.183-.441L8.867 5.243a.417.417 0 0 0-.592 0l-.592.591a.408.408 0 0 0 0 .584l3.709 3.7-3.709 3.7a.417.417 0 0 0 0 .591l.592.584a.417.417 0 0 0 .592 0l4.283-4.275Z" fill="#BCBCBC"/></svg>
                                            <span class="swp-rp-pagination--link">
                                                    <?php esc_html_e( 'Next', 'searchwp' ); ?>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="swp-collapse swp-opened">

            <div class="swp-collapse--header">

                <h2 class="swp-h2">
                    <?php esc_html_e( 'Custom Styling', 'searchwp' ); ?>
                </h2>

                <button class="swp-expand--button">
                    <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
                    </svg>
                </button>

            </div>

            <div class="swp-collapse--content">

                <div class="swp-row">

                    <div class="swp-flex--col swp-flex--gap40">

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Layout Style', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <div class="swp-flex--col swp-flex--gap30">

                                    <div class="swp-flex--row swp-flex--gap12">

                                        <div class="swp-input--radio-img">

                                            <input type="radio" name="swp-layout-style" id="swp-layout-style-grid" value="grid"<?php checked( $settings['swp-layout-style'], 'grid' ); ?> />

                                            <label for="swp-layout-style-grid">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/grid-layout.svg' ); ?>" alt="" />

                                                <?php esc_html_e( 'Grid', 'searchwp' ); ?>

                                            </label>

                                        </div>

                                        <div class="swp-input--radio-img">

                                            <input type="radio" name="swp-layout-style" id="swp-layout-style-list" value="list"<?php checked( $settings['swp-layout-style'], 'list' ); ?> />

                                            <label for="swp-layout-style-list">
                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/list-layout.svg' ); ?>" alt="" />

                                                <?php esc_html_e( 'List', 'searchwp' ); ?>

                                            </label>

                                        </div>

                                    </div>

                                    <div id="swp-results-per-row-block" class="swp-flex--col swp-flex--gap25"<?php echo ( empty( $settings['swp-layout-style'] ) || $settings['swp-layout-style'] === 'list' ) ? ' style="display: none;"' : ''; ?>>

                                        <p class="swp-desc">
                                            <?php esc_html_e( 'Choose the number of results on each row', 'searchwp' ); ?>
                                        </p>

                                        <div class="swp-inputbox-horizontal">

                                            <div class="swp-w-1/6">
                                                <select class="swp-choicesjs-single" name="swp-results-per-row">
                                                    <option value="2"<?php selected( $settings['swp-results-per-row'], '2' ); ?>>
                                                        <?php esc_html_e( '2', 'searchwp' ); ?>
                                                    </option>

                                                    <option value="3"<?php selected( empty( $settings['swp-results-per-row'] ) || $settings['swp-results-per-row'] === '3' ); ?>>
                                                        <?php esc_html_e( '3', 'searchwp' ); ?>
                                                    </option>

                                                    <option value="4"<?php selected( $settings['swp-results-per-row'], '4' ); ?>>
                                                        <?php esc_html_e( '4', 'searchwp' ); ?>
                                                    </option>

                                                    <option value="5"<?php selected( $settings['swp-results-per-row'], '5' ); ?>>
                                                        <?php esc_html_e( '5', 'searchwp' ); ?>
                                                    </option>

                                                    <option value="6"<?php selected( $settings['swp-results-per-row'], '6' ); ?>>
                                                        <?php esc_html_e( '6', 'searchwp' ); ?>
                                                    </option>

                                                    <option value="7"<?php selected( $settings['swp-results-per-row'], '7' ); ?>>
                                                        <?php esc_html_e( '7', 'searchwp' ); ?>
                                                    </option>
                                                </select>
                                            </div>

                                            <label for="" class="swp-label">
                                                <?php esc_html_e( 'Results', 'searchwp' ); ?>
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="swp-row">

                    <div class="swp-flex--col swp-flex--gap30">

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Basic styling', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <p class="swp-desc">
                                    <?php esc_html_e( 'Adjust the appearance of the elements', 'searchwp' ); ?>
                                </p>

                            </div>

                        </div>

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Description', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <label class="swp-toggle">

                                    <input class="swp-toggle-checkbox" type="checkbox" name="swp-description-enabled"<?php checked( $settings['swp-description-enabled'] ); ?>>

                                    <div class="swp-toggle-switch"></div>

                                </label>

                            </div>

                        </div>

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Image', 'searchwp' ); ?>
                                </h3>

                            </div>

                            <div class="swp-col">
                                <div class="swp-w-1/4">
                                    <select class="swp-choicesjs-single" name="swp-image-size">
                                        <option value="">
                                            <?php esc_html_e( 'None', 'searchwp' ); ?>
                                        </option>

                                        <option value="small"<?php selected( $settings['swp-image-size'], 'small' ); ?>>
                                            <?php esc_html_e( 'Small', 'searchwp' ); ?>
                                        </option>

                                        <option value="medium"<?php selected( $settings['swp-image-size'], 'medium' ); ?>>
                                            <?php esc_html_e( 'Medium', 'searchwp' ); ?>
                                        </option>

                                        <option value="large"<?php selected( $settings['swp-image-size'], 'large' ); ?>>
                                            <?php esc_html_e( 'Large', 'searchwp' ); ?>
                                        </option>
                                    </select>
                                </div>

                            </div>

                        </div>

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Title Style', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <div class="swp-flex--row swp-flex--gap17">

                                    <div class="swp-inputbox-vertical">

                                        <label for="" class="swp-label">
                                            <?php esc_html_e( 'Color', 'searchwp' ); ?>
                                        </label>

                                        <span class="swp-input--colorpicker">
                                            <input type="text" class="swp-input" name="swp-title-color" value="<?php echo esc_attr( $settings['swp-title-color'] ); ?>" placeholder="default" maxlength="7">
                                            <svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
                                        </span>

                                    </div>

                                    <div class="swp-inputbox-vertical">

                                        <label for="" class="swp-label">
                                            <?php esc_html_e( 'Font size', 'searchwp' ); ?>
                                        </label>

                                        <span class="swp-input--font-input">
                                            <input type="number" min="0" class="swp-input" name="swp-title-font-size"<?php echo ! empty( $settings['swp-title-font-size'] ) ? ' value="' . absint( $settings['swp-title-font-size'] ) . '"' : ''; ?> placeholder="-">
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <?php if ( self::is_ecommerce_plugin_active() ) : ?>

                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                                <div class="swp-col swp-col--title-width--sm">

                                    <h3 class="swp-h3">
                                        <?php esc_html_e( 'Price Style', 'searchwp' ); ?>
                                    </h3>

                                </div>


                                <div class="swp-col">

                                    <div class="swp-flex--row swp-flex--gap17">

                                        <div class="swp-inputbox-vertical">

                                            <label for="" class="swp-label">
                                                <?php esc_html_e( 'Color', 'searchwp' ); ?>
                                            </label>

                                            <span class="swp-input--colorpicker">
                                                <input type="text" class="swp-input" name="swp-price-color" value="<?php echo esc_attr( $settings['swp-price-color'] ); ?>" placeholder="default" maxlength="7">
                                                <svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
                                            </span>

                                        </div>

                                        <div class="swp-inputbox-vertical">

                                            <label for="" class="swp-label">
                                                <?php esc_html_e( 'Font size', 'searchwp' ); ?>
                                            </label>

                                            <span class="swp-input--font-input">
                                                <input type="number" min="0" class="swp-input" name="swp-price-font-size"<?php echo ! empty( $settings['swp-price-font-size'] ) ? ' value="' . absint( $settings['swp-price-font-size'] ) . '"' : ''; ?> placeholder="-">
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="swp-row">

                    <div class="swp-flex--col swp-flex--gap30">

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Button', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <label class="swp-toggle">

                                    <input class="swp-toggle-checkbox" type="checkbox" name="swp-button-enabled"<?php checked( $settings['swp-button-enabled'] ); ?>>

                                    <div class="swp-toggle-switch"></div>

                                </label>

                            </div>

                        </div>

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">
                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Button Label', 'searchwp' ); ?>
                                </h3>
                            </div>

                            <div class="swp-col">
                                <input class="swp-input swp-w-1/4" type="text" name="swp-button-label" value="<?php echo ! empty( $settings['swp-button-label'] ) ? esc_attr( $settings['swp-button-label'] ) : ''; ?>" placeholder="<?php esc_html_e( 'Read More', 'searchwp' ); ?>">
                            </div>

                        </div>

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Button Style', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <div class="swp-flex--row swp-flex--gap17">

                                    <div class="swp-inputbox-vertical">

                                        <label for="" class="swp-label">
                                            <?php esc_html_e( 'Background Color', 'searchwp' ); ?>
                                        </label>

                                        <span class="swp-input--colorpicker">
                                            <input type="text" class="swp-input" name="swp-button-bg-color" value="<?php echo esc_attr( $settings['swp-button-bg-color'] ); ?>" placeholder="default" maxlength="7">
                                            <svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
                                        </span>

                                    </div>

                                    <div class="swp-inputbox-vertical">

                                        <label for="" class="swp-label">
                                            <?php esc_html_e( 'Font Color', 'searchwp' ); ?>
                                        </label>

                                        <span class="swp-input--colorpicker">
                                            <input type="text" class="swp-input" name="swp-button-font-color" value="<?php echo esc_attr( $settings['swp-button-font-color'] ); ?>" placeholder="default" maxlength="7">
                                            <svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
                                        </span>

                                    </div>

                                    <div class="swp-inputbox-vertical">

                                        <label for="" class="swp-label">
                                            <?php esc_html_e( 'Font', 'searchwp' ); ?>
                                        </label>

                                        <span class="swp-input--font-input">
                                            <input type="number" min="0" class="swp-input" name="swp-button-font-size"<?php echo ! empty( $settings['swp-button-font-size'] ) ? ' value="' . absint( $settings['swp-button-font-size'] ) . '"' : ''; ?> placeholder="-">
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="swp-row">

                    <div class="swp-flex--col swp-flex--gap30">

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Results per Page', 'searchwp' ); ?>
                                </h3>

                            </div>


                            <div class="swp-col">

                                <div class="swp-flex--col swp-flex--gap25">

                                    <p class="swp-desc">
                                        <?php esc_html_e( 'Choose how many maximum results you want to show per page', 'searchwp' ); ?>
                                    </p>

                                    <div class="swp-inputbox-horizontal">

                                        <input type="number" min="0" class="swp-input" name="swp-results-per-page"<?php echo ! empty( $settings['swp-results-per-page'] ) ? ' value="' . absint( $settings['swp-results-per-page'] ) . '"' : ''; ?> placeholder="2">

                                        <label for="" class="swp-label">
                                            <?php esc_html_e( 'Results', 'searchwp' ); ?>
                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30 swp-margin-b30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
                                    <?php esc_html_e( 'Pagination style', 'searchwp' ); ?>
                                </h3>

                            </div>

                            <div class="swp-col">

                                <div class="swp-flex--row swp-flex--gap12">

                                    <div class="swp-input--radio-img">

                                        <input type="radio" name="swp-pagination-style" id="swp-pagination-nobox" value=""<?php checked( empty( $settings['swp-pagination-style'] ) ); ?> />

                                        <label for="swp-pagination-nobox">
                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/pagination-nobox.svg' ); ?>" alt="" />

                                            <?php esc_html_e( 'No Box', 'searchwp' ); ?>

                                        </label>

                                    </div>

                                    <div class="swp-input--radio-img">

                                        <input type="radio" name="swp-pagination-style" id="swp-pagination-boxed" value="boxed"<?php checked( $settings['swp-pagination-style'], 'boxed' ); ?> />

                                        <label for="swp-pagination-boxed">
                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/pagination-boxed.svg' ); ?>" alt="" />

                                            <?php esc_html_e( 'Boxed', 'searchwp' ); ?>

                                        </label>

                                    </div>

                                    <div class="swp-input--radio-img">

                                        <input type="radio" name="swp-pagination-style" id="swp-pagination-circular" value="circular"<?php checked( $settings['swp-pagination-style'], 'circular' ); ?> />

                                        <label for="swp-pagination-circular">
                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/results-page/pagination-circular.svg' ); ?>" alt="" />

                                            <?php esc_html_e( 'Circular', 'searchwp' ); ?>

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <?php
    }

    /**
     * Get search results page preview container classes.
     *
     * @since 4.3.6
     *
     * @param array $settings Search Results Page settings.
     *
     * @return string
     */
    private static function get_preview_container_classes( $settings ) {

        $classes = [];

        if ( $settings['swp-layout-style'] === 'list' ) {
            $classes[] = 'swp-flex';
        }

        if ( $settings['swp-layout-style'] === 'grid' ) {
            $classes[] = 'swp-grid';
        }

        $per_row = ! empty( $settings['swp-results-per-row'] ) ? absint( $settings['swp-results-per-row'] ) : null;
        if ( ! empty( $per_row ) ) {
            $classes[] = 'swp-grid--cols-' . $per_row;
        } else {
            $classes[] = 'swp-grid--cols-3';
        }

        $image_size = $settings['swp-image-size'];
        if ( empty( $image_size ) || $image_size === 'none' ) {
            $classes[] = 'swp-result-item--img--off';
        }
        if ( $image_size === 'small' ) {
            $classes[] = 'swp-rp--img-sm';
        }
        if ( $image_size === 'medium' ) {
            $classes[] = 'swp-rp--img-m';
        }
        if ( $image_size === 'large' ) {
            $classes[] = 'swp-rp--img-l';
        }

        return implode( ' ', $classes );
    }

    /**
     * Get search results page preview pagination container classes.
     *
     * @since 4.3.6
     *
     * @param array $settings Search Results Page settings.
     *
     * @return string
     */
    private static function get_pagination_container_class( $settings ) {

        if ( $settings['swp-pagination-style'] === 'circular' ) {
            return 'swp-results-pagination--circular';
        }

        if ( $settings['swp-pagination-style'] === 'boxed' ) {
            return 'swp-results-pagination--boxed';
        }

        return 'swp-results-pagination--noboxed';
    }

    /**
     * Save form settings AJAX callback.
     *
     * @since 4.3.6
     */
    public static function save_results_page_settings_ajax() {

        Utils::check_ajax_permissions();

        if ( ! isset( $_POST['settings'] ) ) {
            wp_send_json_error();
        }

        $data = json_decode( wp_unslash( $_POST['settings'] ), true );

        ResultsSettings::update_multiple( $data );

        wp_send_json_success();
    }

    /**
     * Check if any of the supported ecommerce plugins are active.
     *
     * @since 4.3.6
     *
     * @return bool
     */
    private static function is_ecommerce_plugin_active() {

        return class_exists( 'woocommerce' ) || class_exists( 'Easy_Digital_Downloads' );
    }
}
