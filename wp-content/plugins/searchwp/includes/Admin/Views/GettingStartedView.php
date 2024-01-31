<?php

/**
 * SearchWP GettingStartedView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\License;
use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class GettingStartedView is responsible for providing the UI for Getting Started.
 *
 * @since 4.3.0
 */
class GettingStartedView {

	private static $slug = 'getting-started';

	/**
	 * GettingStartedView constructor.
	 *
	 * @since 4.3.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'about-us' ) ) {
			new NavTab([
				'page'  => 'about-us',
				'tab'   => self::$slug,
				'label' => __( 'Getting Started', 'searchwp' ),
			]);
		}

		if ( Utils::is_swp_admin_page( 'about-us', self::$slug ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.3.0
	 * @return void
	 */
	public static function assets() {

		wp_enqueue_style(
			SEARCHWP_PREFIX . self::$slug,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/welcome-about.css',
			[
				Utils::$slug . 'card',
				Utils::$slug . 'style',
			],
			SEARCHWP_VERSION
		);
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.3.0
	 * @return void
	 */
	public static function render() {

		?>
        <div class="swp-content-container">

            <div class="swp-content-section swp-margin-b40">

                <div class="swp-content-block swp-bg--white">

                    <div class="swp-flex--row sm:swp-flex--col swp-flex--gap25">
                        <div class="swp-flex--item swp-w-3/5 sm:swp-w-full">

                            <h2 class="swp-h2 swp-leading--160 swp-margin-b25">
                                <?php esc_html_e( 'Configuring Your First Search Engine', 'searchwp' ); ?>
                            </h2>

                            <p class="swp-p-content swp-margin-b25">
                                <?php esc_html_e( 'To get started with SearchWP, click on one of our helpful documentation tutorials below to learn how you can configure your search engines to surface all the valuable content on your site.', 'searchwp' ); ?>
                            </p>

                            <p class="swp-margin-b15">
                                <a class="swp-a" href="https://searchwp.com/documentation/setup/engines/?utm_source=WordPress&utm_medium=Getting+Started+Documentation+Link&utm_campaign=SearchWP" target="_blank">
                                    <?php esc_html_e( 'Configuring Your First Search Engine', 'searchwp' ); ?>&nbsp;&rarr;
                                </a>
                            </p>

                            <p class="swp-margin-b15">
                                <a class="swp-a" href="https://searchwp.com/documentation/setup/global-rules/?utm_source=WordPress&utm_medium=Getting+Started+Documentation+Link&utm_campaign=SearchWP" target="_blank">
                                    <?php esc_html_e( 'Configuring Synonyms and Stopwords', 'searchwp' ); ?>&nbsp;&rarr;
                                </a>
                            </p>

                            <p class="swp-margin-b15">
                                <a class="swp-a" href="https://searchwp.com/documentation/setup/settings/?utm_source=WordPress&utm_medium=Getting+Started+Documentation+Link&utm_campaign=SearchWP" target="_blank">
                                    <?php esc_html_e( 'Configuring Fuzzy Matches, Did You Mean? and More', 'searchwp' ); ?>&nbsp;&rarr;
                                </a>
                            </p>

                            <p>
                                    <a class="swp-a" href="https://searchwp.com/documentation/setup/statistics/?utm_source=WordPress&utm_medium=Getting+Started+Documentation+Link&utm_campaign=SearchWP" target="_blank">
                                        <?php esc_html_e( 'Analyzing Search Statistics', 'searchwp' ); ?>&nbsp;&rarr;
                                    </a>
                                </p>
                        </div>

                        <div class="swp-flex--item swp-w-2/5 sm:swp-w-full">
                            <img
                                    src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/getting-started/engine.png' ); ?>"
                                    alt="<?php esc_attr_e( 'SearchWP Engine Overview', 'searchwp' ); ?>"
                                    style="width:100%"
                            >
                        </div>
                    </div>

                </div> <!-- .swp-bg--white -->

            </div> <!-- .swp-content-section -->

            <?php
            $license_type = License::get_type();

            // If the license is Pro, show the Agency upsell.
            if ( $license_type === 'pro' ) {
                ?>
                <div class="swp-content-section swp-margin-b40">
                    <div class="swp-content-block swp-bg--white">
                        <div class="searchwp-getting-started-cta <?php echo esc_attr( $license_type ); ?>">
                            <h2 class="swp-h2 swp-leading--160 swp-margin-b25">
                                <?php esc_html_e( 'Upgrade to SearchWP Agency today and use SearchWP on an unlimited number of websites!', 'searchwp' ); ?>
                            </h2>
                            <h2 class="swp-h2 swp-margin-b10">
                                <a href="<?php echo esc_url( 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Getting+Started+Upsell+Link&utm_campaign=SearchWP&utm_content=Get+SearchWP+Agency+Now' ); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   title="<?php esc_html_e( 'Get SearchWP Agency Now »', 'searchwp' ); ?>"
                                   class="swp-a swp-font-semibold"
                                >
                                    <?php esc_html_e( 'Get SearchWP Agency Now »', 'searchwp' ); ?>
                                </a>
                            </h2>
                            <p class="swp-p-content">
                                <?php
									echo wp_kses(
										__( '<strong>Bonus:</strong> SearchWP Pro users get up to <span class="swp-text-green">$300 off their upgrade price</span>, automatically applied at checkout!', 'searchwp' ),
										[
											'strong' => [],
											'span'   => [
												'class' => [],
											],
										]
									);
								?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }

            // If the license is higher than Standard, skip the Pro upsell.
            if ( ! empty( $license_type ) && $license_type !== 'standard' ) {
                return;
            }
            ?>

            <div class="swp-content-section swp-margin-b40">

                <div class="swp-content-block swp-bg--gray">

                    <h2 class="swp-h2 swp-leading--160 swp-margin-b25">
                        <?php esc_html_e( 'Get SearchWP Pro And Unlock All The Powerful Features', 'searchwp' ); ?>
                    </h2>

                    <p class="swp-p-content swp-margin-b25">
                        <?php esc_html_e( 'Upgrade to SearchWP Pro to unlock all the awesome features and experience why SearchWP is consistently rated the best WordPress search plugin.', 'searchwp' ); ?>
                    </p>

                    <p class="swp-p-content">
                        <?php esc_html_e( 'We know that you will truly love SearchWP Pro. It’s used on over 30,000 smart WordPress websites and is consistently rated 5-stars', 'searchwp' ); ?> ( <span class="swp-rating-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span> ) <?php esc_html_e( 'by our customers.', 'searchwp' ); ?>
                    </p>

                </div> <!-- .swp-bg--gray -->

                <div class="swp-content-block swp-bg--white">

                    <ul class="swp-features-list">
                        <li><?php esc_html_e( 'Search related content', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Cross-site multisite search', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Search ecommerce product data', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Search custom database tables and other custom content', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Conditional search redirects', 'searchwp' ); ?></li>

                        <li><?php esc_html_e( 'WPML integration support', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Advanced Custom Fields (ACF) support', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Advanced search metrics and insights', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Custom search result ordering', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Additional popular plugin integrations', 'searchwp' ); ?></li>
                    </ul>

                    <h2 class="swp-h2 swp-margin-b10">
                        <a class="swp-a swp-font-semibold" href="https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Getting+Started+Upsell+Button&utm_campaign=SearchWP&utm_content=Get+SearchWP+Pro+Today+and+Unlock+all+the+Powerful+Features" target="_blank">
                            <?php esc_html_e( 'Get SearchWP Pro Today and Unlock all the Powerful Features', 'searchwp' ); ?> &raquo;
                        </a>
                    </h2>

                    <p class="swp-p-content">
                        <?php esc_html_e( 'Bonus: SearchWP users get up to', 'searchwp' ); ?> <span class="swp-text-green">$300 off their upgrade price</span>, <?php esc_html_e( 'automatically applied at checkout.', 'searchwp' ); ?>
                    </p>

                </div> <!-- .swp-bg--white -->

            </div> <!-- .swp-content-section -->

        </div>
		<?php
	}
}
