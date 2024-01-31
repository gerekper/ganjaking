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

		wp_enqueue_style(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/general-settings.css',
			[
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'input',
				Utils::$slug . 'toggle-switch',
				Utils::$slug . 'style',
			],
			SEARCHWP_VERSION
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
									<?php esc_html_e( 'License Key', 'searchwp' ); ?>
                                </h3>
                            </div>

                            <div class="swp-col">

                                <?php $license = Settings::get( 'license' ); ?>

                                <?php // TODO: Process the case when the active license gets expired over time. ?>

                                <div class="swp-flex--row sm:swp-flex--wrap swp-flex--gap12">
                                    <input id="swp-license" class="swp-input swp-w-2/5" type="<?php echo License::is_active() ? 'password' : 'text'; ?>"<?php echo License::is_active() ? ' value="' . esc_attr( License::get_key() ) . '" disabled' : ''; ?>>
									<?php if ( License::is_active() ) : ?>
										<button id="swp-license-deactivate" class="swp-button"><?php esc_html_e( 'Remove Key', 'searchwp' ); ?></button>
									<?php else : ?>
										<button id="swp-license-activate" class="swp-button swp-button--green"><?php esc_html_e( 'Verify Key', 'searchwp' ); ?></button>
									<?php endif; ?>
                                </div>

                                <p id="swp-license-error-msg" class="swp-desc--btm swp-text-red" style="display:none"></p>

								<p class="swp-desc--btm">
									<?php if ( License::is_active() ) : ?>
										Your license key level is <b><span id="swp-license-type"><?php echo License::is_active() ? esc_html( strtoupper( License::get_type() ) ) : ''; ?></span></b>. <span id="swp-license-remaining"><?php echo License::is_active() ? esc_html( $license['remaining'] ) : ''; ?></span>.
									<?php else : ?>
										Your license key can be found in your <a href="https://searchwp.com/account/?utm_campaign=plugin&utm_source=WordPress&utm_medium=settings-license&utm_content=Account%20Dashboard" target="_blank" rel="noopener noreferrer">SearchWP Account Dashboard</a>. Don’t have a license? <a href="https://searchwp.com/buy/?utm_campaign=plugin&utm_source=WordPress&utm_medium=settings-license&utm_content=License%20Key%20Sign%20Up" target="_blank" rel="noopener noreferrer">Sign up today!</a>
									<?php endif; ?>
								</p>

								<?php self::render_license_upsell(); ?>

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

	/**
	 * Renders the license upsell notice.
	 *
	 * @since 4.3.10
	 */
	private static function render_license_upsell() {

		$license_type = License::get_type();

		$title       = '';
		$upsell_text = '';
		$upsell_url  = '';
		$bonus_text  = '';

		switch ( $license_type ) {
			case '':
				$title       = '<h5>' . __( 'Get SearchWP Pro Today and Unlock all the Powerful Features', 'searchwp' ) . '</h5>';
				$upsell_text = __( 'Buy SearchWP Pro Today »', 'searchwp' );
				$upsell_url  = 'https://searchwp.com/buy/?utm_source=WordPress&utm_medium=License+Field+Upsell+Link&utm_campaign=SearchWP&utm_content=Buy+SearchWP+Pro+Today';
				break;

			case 'standard':
				$title       = '<h5>' . __( 'Get SearchWP Pro Today and Unlock all the Powerful Features', 'searchwp' ) . '</h5>';
				$upsell_text = __( 'Get SearchWP Pro Today »', 'searchwp' );
				$upsell_url  = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=License+Field+Upsell+Link&utm_campaign=SearchWP&utm_content=Get+SearchWP+Pro+Today';
				$bonus_text  = __( '<strong>Bonus:</strong> SearchWP Standard users get up to <span class="green">$200 off their upgrade price</span>, automatically applied at checkout!', 'searchwp' );
				break;

			case 'pro':
				$title       = '<h5>' . __( 'Upgrade to SearchWP Agency today and use SearchWP on an unlimited number of websites!', 'searchwp' ) . '</h5>';
				$upsell_text = __( 'Get SearchWP Agency Now »', 'searchwp' );
				$upsell_url  = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=License+Field+Upsell+Link&utm_campaign=SearchWP&utm_content=Get+SearchWP+Agency+Now';
				$bonus_text  = __( '<strong>Bonus:</strong> SearchWP Pro users get up to <span class="green">$300 off their upgrade price</span>, automatically applied at checkout!', 'searchwp' );
				break;

			case 'agency':
				$title = __( 'Thank you for using SearchWP! ❤️', 'searchwp' );
				break;

			default:
				break;
		}

		?>
		<div class="searchwp-settings-license-upsell <?php echo esc_attr( $license_type ); ?>">

			<?php echo wp_kses_post( $title ); ?>

			<?php if ( empty( $license_type ) || $license_type === 'standard' ) : ?>
				<div class="list">
					<ul>
						<li><?php esc_html_e( 'WooCommerce & Easy Digital Downloads support', 'searchwp' ); ?></li>
						<li><?php esc_html_e( 'Advanced search Metrics and insights on visitor activity', 'searchwp' ); ?></li>
						<li><?php esc_html_e( 'Click tracking to know what search results users are picking', 'searchwp' ); ?></li>
						<li><?php esc_html_e( 'Multi-language search support with WPML and Polylang', 'searchwp' ); ?></li>
					</ul>
					<ul>
						<li><?php esc_html_e( 'Increased activation limit (use SearchWP on more sites!)', 'searchwp' ); ?></li>
						<li><?php esc_html_e( 'Granular customization of search results order', 'searchwp' ); ?></li>
						<li><?php esc_html_e( 'Related block to display similar search results', 'searchwp' ); ?></li>
						<li><?php esc_html_e( 'Conditional redirects based on the search request', 'searchwp' ); ?></li>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $upsell_text ) ) : ?>
				<p><a href="<?php echo esc_url( $upsell_url ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_html( $upsell_text ); ?>"><?php echo esc_html( $upsell_text ); ?></a></p>
			<?php endif; ?>

			<?php if ( ! empty( $bonus_text ) ) : ?>
				<p>
					<?php
						echo wp_kses(
							$bonus_text,
							[
								'strong' => [],
								'span'   => [
									'class' => [],
								],
							]
						);
					?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}
