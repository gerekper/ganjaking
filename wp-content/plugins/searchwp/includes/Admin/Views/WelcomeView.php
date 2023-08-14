<?php

/**
 * SearchWP WelcomeView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\Admin\OptionsView;
use SearchWP\Settings;
use SearchWP\Utils;

/**
 * Class WelcomeView is responsible for displaying a Welcome screen on plugin's first installation.
 *
 * @since 4.3.0
 */
class WelcomeView {

	/**
	 * Hidden welcome page slug.
	 *
	 * @since 4.3.0
	 */
	private static $slug = 'searchwp-welcome';

	/**
	 * Init.
	 *
	 * @since 4.3.0
	 */
	public static function init() {

		add_action( 'plugins_loaded', [ __CLASS__, 'hooks' ] );
	}

	/**
	 * Register all WP hooks.
	 *
	 * @since 4.3.0
	 */
	public static function hooks() {

		// If user is in admin ajax or doing cron, return.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// If user cannot manage_options, return.
		if ( ! current_user_can( Settings::get_capability() ) ) {
			return;
		}

		add_action( 'admin_menu', [ __CLASS__, 'register' ] );
		add_action( 'admin_head', [ __CLASS__, 'hide_menu' ] );
		add_action( 'admin_init', [ __CLASS__, 'redirect' ], 9999 );

		if ( Utils::is_swp_admin_page( 'welcome' ) ) {
			add_action( 'init', function() {
				remove_action( 'in_admin_header', [ OptionsView::class, 'admin_header' ], 100 );
			}, 99999 );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.3.0
	 */
	public static function assets() {

		wp_enqueue_style(
			self::$slug,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/welcome-about.css',
			[ Utils::$slug . 'style' ],
			SEARCHWP_VERSION
		);
	}

	/**
	 * Register the dashboard page to be used for the Welcome screen.
	 *
	 * @since 4.3.0
	 */
	public static function register() {

		add_dashboard_page(
			esc_html__( 'Welcome to SearchWP', 'searchwp' ),
			esc_html__( 'Welcome to SearchWP', 'searchwp' ),
			apply_filters( 'searchwp_welcome_cap', Settings::get_capability() ),
			self::$slug,
			[ __CLASS__, 'render' ]
		);
	}

	/**
	 * Remove the dashboard page from the admin menu.
	 * The page remains available but hidden.
	 *
	 * @since 4.3.0
	 */
	public static function hide_menu() {

		remove_submenu_page( 'index.php', self::$slug );
	}

	/**
	 * Welcome screen redirect.
	 *
	 * This function checks if a new install or update has just occurred. If so,
	 * then we redirect the user to the appropriate page.
	 *
	 * @since 4.3.0
	 */
	public static function redirect() {

		// Check if we should consider redirection.
		if ( ! get_transient( 'searchwp_activation_redirect' ) ) {
			return;
		}

		// If we are redirecting, clear the transient, so it only happens once.
		delete_transient( 'searchwp_activation_redirect' );

		// Check option to disable welcome redirect.
		if ( get_option( 'searchwp_activation_redirect', false ) ) {
			return;
		}

		// Only do this for single site installs.
		if ( isset( $_GET['activate-multi'] ) || is_network_admin() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// Check if this is an update or first install.
		$upgrade = get_option( 'searchwp_version_upgraded_from' );

		if ( ! $upgrade ) {
			// Initial install.
			wp_safe_redirect( admin_url( 'index.php?page=' . self::$slug ) );
			exit;
		}
	}

	/**
	 * Welcome screen. Shows after first install.
	 *
	 * @since 4.3.0
	 */
	public static function render() {

		?>
		<div class="swp-content-container--s">

			<div class="swp-content-section swp-margin-b40">

				<div class="swp-content-block swp-bg--white swp-text-center swp-padding-t85 swp-padding-b30 swp-no-bord-btm">

					<div class="swp-circle-img swp-logo-s--position">
						<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/swp-logo-s.svg' ); ?>" alt="">
					</div>

					<div class="swp-title--l">
						<?php esc_html_e( 'Welcome to SearchWP', 'searchwp' ); ?>
					</div>

					<p class="swp-p-content">
						<?php esc_html_e( 'Thank you for choosing SearchWP - the best WordPress search plugin!', 'searchwp' ); ?>
					</p>

				</div> <!-- End White Section -->

				<div class="swp-content-block swp-padding0 swp-no-bord-x">
					<a class="swp-img-link" href="https://searchwp.com/documentation/setup/engines/?utm_source=WordPress&utm_medium=Welcome+Page+Guide+Image&utm_campaign=SearchWP" target="_blank">
						<img
								src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/getting-started/engine.png' ); ?>"
								alt="<?php esc_attr_e( 'SearchWP Engine Overview', 'searchwp' ); ?>"
								style="width:70%;margin:0 auto;display:block"
						>
					</a>
				</div> <!-- End LinkImage Section -->

				<div class="swp-content-block swp-bg--white swp-text-center swp-padding-t30 swp-no-bord-top">

					<p class="swp-p-content swp-margin-b30">
						<?php esc_html_e( 'SearchWP supercharges search by making all your content discoverable! Start by customizing your first search engine or read the getting started guide.', 'searchwp' ); ?>
					</p>

					<div class="swp-flex--row sm:swp-flex--col swp-justify-center swp-flex--gap20">
						<a class="swp-button swp-button--green swp-button--xl swp-flex--grow1" href="<?php echo esc_url( add_query_arg( [
							'page'    => 'searchwp-algorithm',
							'welcome' => '1',
						], admin_url( 'admin.php' ) ) ); ?>">
							<?php esc_html_e( 'Customize Your Search Engine', 'searchwp' ); ?>
						</a>

						<a class="swp-button swp-button--xl swp-flex--grow1" href="https://searchwp.com/documentation/setup/engines/?utm_source=WordPress&utm_medium=Welcome+Page+Guide+Button&utm_campaign=SearchWP" target="_blank">
							<?php esc_html_e( 'Read the Getting Started Guide', 'searchwp' ); ?>
						</a>
					</div>

				</div> <!-- End White Section -->

			</div> <!-- .swp-content-section -->


			<div class="swp-content-section">

				<div class="swp-content-block swp-bg--white">

					<div class="swp-title--l swp-text-center">
						<?php esc_html_e( 'SearchWP Features & Addons', 'searchwp' ); ?>
					</div>

					<p class="swp-p-content swp-text-center swp-margin-b60">
						<?php esc_html_e( 'SearchWP is both easy to use and extremely powerful.
						We have tons of helpful features to surface all your valuable content to visitor search queries.', 'searchwp' ); ?>
					</p>

					<div class="swp-features swp-grid swp-grid--cols-2 sm:swp-grid--col-1 swp-margin-b40">

						<div class="swp-flex--row swp-flex--gap20"> <!-- Feature 1 -->

							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame01.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'PDF & Office Doc Indexing', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Index the content of PDF, Office,
									and text documents in your WordPress Media library.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 2 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame02.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Automatic Integration', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'SearchWP\'s default engine uses your existing
									WordPress native search forms and results template.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20"> <!-- Feature 3 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame03.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Multiple Search Engines', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Configure individual search engines,
									each with their own settings to meet your needs.
									Easily integrate into your theme with step-by-step instructions.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 4 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame04.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Keyword Stemming', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Show better results by using keyword stems instead of exact term matches.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 5 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame05.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Search Everything', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Search your product details, Custom Fields content,
									Shortcode output, custom database table content,
									cross-site multisite search, and more!', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 6 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame06.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Advanced Custom Fields Support', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Stop ignoring content stored in Advanced Custom
									Fields when searching your site.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 7 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame07.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'WooCommerce Integration', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Stop losing money when your customers
									can\'t search by your product details!', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 8 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame08.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Exclude or Attribute Results', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Easily exclude content from search results,
									or attribute findings to more appropriate results.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 9 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame09.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Search Statistics and Insights', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Log searches to find out what
									your visitors are searching for and (not?) finding.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 10 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame10.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Easy Algorithm Customization', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Easily customize how results are ranked using
									SearchWP\'s intuitive interface and weighting system.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 11 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame11.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'bbPress Integration', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Improve the usefulness of your forums
									by implementing a powerful, relevant search.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 12 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame12.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'WP Job Manager Integration', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Allow visitors to search for Listing metadata,
									which is otherwise ignored by WordPress.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 13 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame13.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Easy Digital Downloads Integration', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Make sure your customers are finding your products easily and effectively.', 'searchwp' ); ?>
								</p>
							</div>
						</div>

						<div class="swp-flex--row swp-flex--gap20">  <!-- Feature 14 -->
							<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/frame14.svg' ); ?>">

							<div>
								<p class="swp-title--s">
									<?php esc_html_e( 'Suggest a Feature', 'searchwp' ); ?>
								</p>

								<p>
									<?php esc_html_e( 'Did something get left out? Please feel free to inquire about additional features!', 'searchwp' ); ?>
								</p>
							</div>
						</div>

					</div> <!-- .swp-grid -->

					<div class="swp-text-center swp-margin-b20">
						<a class="swp-button swp-button--l swp-margin-auto" href="https://searchwp.com/features/?utm_source=WordPress&utm_medium=Welcome+See+Features+Button&utm_campaign=SearchWP" target="_blank">
							<?php esc_html_e( 'See All Features', 'searchwp' ); ?>
						</a>
					</div>

				</div> <!-- End White Section -->

				<div class="swp-content-block swp-bg--black">

					<div class="swp-title--m sm:swp-text-center sm:swp-margin-b30">
						<?php esc_html_e( 'Upgrade to SearchWP PRO Today!', 'searchwp' ); ?>
					</div>

					<div class="swp-flex--row sm:swp-flex--col swp-flex--align-c sm:swp-flex--gap30">
						<ul class="swp-upgrade-list swp-flex--grow1">
							<li><?php esc_html_e( 'Related Content', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'MultiSite Search', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'eCommerce Data', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'Custom Content', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'Conditional Redirects', 'searchwp' ); ?></li>
						</ul>

						<ul class="swp-upgrade-list swp-flex--grow1">
							<li><?php esc_html_e( 'WPML Support', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'ACF Support', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'Metrics & Insights', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'Result Ordering', 'searchwp' ); ?></li>
							<li><?php esc_html_e( 'Plugin Integrations', 'searchwp' ); ?></li>
						</ul>

						<div class="swp-upgrade swp-flex--grow1">
							<div class="swp-title--m">
								PRO
							</div>

							<div class="swp-pro-price">
								<span class="swp-price--sign">$</span>
								<span class="swp-price--amount">199</span>
								<div class="swp-price--term">
									Per Year
								</div>
							</div>

							<a class="swp-button swp-button--green swp-button--l swp-margin-auto" href="https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Welcome+Screen+Upsell+Pro+Button&utm_campaign=SearchWP&utm_content=Upgrade+Now" target="_blank">
								Upgrade Now
							</a>
						</div>

					</div>

				</div> <!-- End Black Section -->

				<div class="swp-content-block swp-bg--white">

					<div class="swp-title--l swp-text-center swp-margin-t20 swp-margin-b20">
						Testimonials
					</div>

					<div class="swp-testimonials-list">

						<div class="swp-testimonials-item swp-margin-b30">
							<div class="swp-flex--row swp-flex--gap25 swp-flex--align-c">

								<div class="swp-testimonial-avatar">
									<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/welcome/testimonials/swp-Scott-B.jpg' ); ?>" alt="User avatar">
								</div>

								<div class="swp-testimonial-content">

									<p class="swp-margin-b15">
										“2 things I’ve done based on data from SearchWP: create content people searched for that was missing, and customize results for important terms. Being able to see search data and customize results has been awesome!”
									</p>

									<div class="swp-title--s swp-margin-b0">
                                        Scott B.
									</div>

								</div>

							</div>
						</div>

					</div>

				</div> <!-- End White Section -->

				<div class="swp-content-block swp-bg--gray swp-padding23">

					<div class="swp-flex--row sm:swp-flex--col swp-justify-center swp-flex--align-c swp-flex--gap20">
						<a class="swp-button swp-button--green swp-button--xl swp-flex--grow1" href="<?php echo esc_url( add_query_arg( [
							'page'    => 'searchwp-algorithm',
							'welcome' => '1',
						], admin_url( 'admin.php' ) ) ); ?>">
							<?php esc_html_e( 'Customize Your Search Engine', 'searchwp' ); ?>
						</a>

						<a class="swp-button swp-button--xl swp-flex--grow1" href="https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Welcome+Screen+Upsell+Button&utm_campaign=SearchWP&utm_content=Upgrade+to+SearchWP+Pro" target="_blank">
							<?php esc_html_e( 'Upgrade to SearchWP PRO', 'searchwp' ); ?>
						</a>
					</div>

				</div>

			</div> <!-- .swp-content-section -->

		</div>
		<?php
	}
}
