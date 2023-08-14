<?php

/**
 * SearchWP AboutUsView.
 *
 * @since 4.3.0
 */

namespace SearchWP\Admin\Views;

use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class AboutUsView is responsible for providing the UI for About Us.
 *
 * @since 4.3.0
 */
class AboutUsView {

	private static $slug = 'about-us';

	/**
	 * Plugins data storage.
	 *
	 * @since 4.3.0
	 *
	 * @var array
	 */
	private static $storage;

	/**
	 * AboutUsView constructor.
	 *
	 * @since 4.3.0
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'about-us' ) ) {
			new NavTab([
				'page'       => 'about-us',
				'tab'        => self::$slug,
				'label'      => __( 'About Us', 'searchwp' ),
				'is_default' => true,
			]);
		}

		if ( Utils::is_swp_admin_page( 'about-us', 'default' ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'plugin_install', [ __CLASS__, 'ajax_install_plugin' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'plugin_activate', [ __CLASS__, 'ajax_activate_plugin' ] );
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
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/welcome-about.css',
			[
				Utils::$slug . 'buttons',
				Utils::$slug . 'card',
				Utils::$slug . 'style',
            ],
			SEARCHWP_VERSION
		);

		wp_enqueue_script(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/about-us.js',
			[ 'jquery' ],
			SEARCHWP_VERSION,
			true
		);

		Utils::localize_script(
			$handle,
			[
				'error_strings' => [
					'plugin_error' => esc_html__( 'Could not install the plugin. Please download and install it manually.', 'searchwp' ),
				],
			]
		);
	}

	/**
	 * Fetch extensions data from the remote source.
	 * Temporarily mocks the remote with a hardcoded array.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
    private static function fetch() {

	    $images_url = SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/about/';

	    return [

		    'optinmonster/optin-monster-wp-api.php'        => [
                'file'  => 'optinmonster/optin-monster-wp-api.php',
			    'image' => $images_url . 'plugin-om.png',
			    'title' => esc_html__( 'OptinMonster', 'searchwp' ),
			    'desc'  => esc_html__( 'Instantly get more subscribers, leads, and sales with the #1 conversion optimization toolkit. Create high converting popups, announcement bars, spin a wheel, and more with smart targeting and personalization.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
		    ],

		    'wpforms-lite/wpforms.php'                     => [
			    'file'  => 'wpforms-lite/wpforms.php',
			    'image' => $images_url . 'plugin-wpforms.png',
			    'title' => esc_html__( 'WPForms', 'searchwp' ),
			    'desc'  => esc_html__( 'The best drag & drop WordPress form builder. Easily create beautiful contact forms, surveys, payment forms, and more with our 100+ form templates. Trusted by over 5 million websites as the best forms plugin.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/wpforms-lite.zip',
			    'pro'   => [
				    'file'  => 'wpforms/wpforms.php',
				    'image' => $images_url . 'plugin-wpforms.png',
				    'title' => esc_html__( 'WPForms Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'The best drag & drop WordPress form builder. Easily create beautiful contact forms, surveys, payment forms, and more with our 100+ form templates. Trusted by over 5 million websites as the best forms plugin.', 'searchwp' ),
				    'url'   => 'https://wpforms.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'google-analytics-for-wordpress/googleanalytics.php' => [
			    'file'  => 'google-analytics-for-wordpress/googleanalytics.php',
			    'image' => $images_url . 'plugin-mi.png',
			    'title' => esc_html__( 'MonsterInsights', 'searchwp' ),
			    'desc'  => esc_html__( 'The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
			    'pro'   => [
				    'file'  => 'google-analytics-premium/googleanalytics-premium.php',
				    'image' => $images_url . 'plugin-mi.png',
				    'title' => esc_html__( 'MonsterInsights Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'The leading WordPress analytics plugin that shows you how people find and use your website, so you can make data driven decisions to grow your business. Properly set up Google Analytics without writing code.', 'searchwp' ),
				    'url'   => 'https://www.monsterinsights.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'wp-mail-smtp/wp_mail_smtp.php'                => [
			    'file'  => 'wp-mail-smtp/wp_mail_smtp.php',
			    'image' => $images_url . 'plugin-smtp.png',
			    'title' => esc_html__( 'WP Mail SMTP', 'searchwp' ),
			    'desc'  => esc_html__( "Improve your WordPress email deliverability and make sure that your website emails reach user's inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.", 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
			    'pro'   => [
				    'file'  => 'wp-mail-smtp-pro/wp_mail_smtp.php',
				    'image' => $images_url . 'plugin-smtp.png',
				    'title' => esc_html__( 'WP Mail SMTP Pro', 'searchwp' ),
				    'desc'  => esc_html__( "Improve your WordPress email deliverability and make sure that your website emails reach user's inbox with the #1 SMTP plugin for WordPress. Over 3 million websites use it to fix WordPress email issues.", 'searchwp' ),
				    'url'   => 'https://wpmailsmtp.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'all-in-one-seo-pack/all_in_one_seo_pack.php'  => [
			    'file'  => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
			    'image' => $images_url . 'plugin-aioseo.png',
			    'title' => esc_html__( 'AIOSEO', 'searchwp' ),
			    'desc'  => esc_html__( "The original WordPress SEO plugin and toolkit that improves your website's search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.", 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
			    'pro'   => [
				    'file'  => 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php',
				    'image' => $images_url . 'plugin-aioseo.png',
				    'title' => esc_html__( 'AIOSEO Pro', 'searchwp' ),
				    'desc'  => esc_html__( "The original WordPress SEO plugin and toolkit that improves your website's search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.", 'searchwp' ),
				    'url'   => 'https://aioseo.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'coming-soon/coming-soon.php'                  => [
			    'file'  => 'coming-soon/coming-soon.php',
			    'image' => $images_url . 'plugin-seedprod.png',
			    'title' => esc_html__( 'SeedProd', 'searchwp' ),
			    'desc'  => esc_html__( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/coming-soon.zip',
			    'pro'   => [
				    'file'  => 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php',
				    'image' => $images_url . 'plugin-seedprod.png',
				    'title' => esc_html__( 'SeedProd Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'searchwp' ),
				    'url'   => 'https://www.seedprod.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'rafflepress/rafflepress.php'                  => [
			    'file'  => 'rafflepress/rafflepress.php',
			    'image' => $images_url . 'plugin-rp.png',
			    'title' => esc_html__( 'RafflePress', 'searchwp' ),
			    'desc'  => esc_html__( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
			    'pro'   => [
				    'file'  => 'rafflepress-pro/rafflepress-pro.php',
				    'image' => $images_url . 'plugin-rp.png',
				    'title' => esc_html__( 'RafflePress Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'searchwp' ),
				    'url'   => 'https://rafflepress.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'pushengage/main.php'                          => [
			    'file'  => 'pushengage/main.php',
			    'image' => $images_url . 'plugin-pushengage.png',
			    'title' => esc_html__( 'PushEngage', 'searchwp' ),
			    'desc'  => esc_html__( 'Connect with your visitors after they leave your website with the leading web push notification software. Over 10,000+ businesses worldwide use PushEngage to send 9 billion notifications each month.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/pushengage.zip',
		    ],

		    'instagram-feed/instagram-feed.php'            => [
			    'file'  => 'instagram-feed/instagram-feed.php',
			    'image' => $images_url . 'plugin-sb-instagram.png',
			    'title' => esc_html__( 'Smash Balloon Instagram Feeds', 'searchwp' ),
			    'desc'  => esc_html__( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
			    'pro'   => [
				    'file'  => 'instagram-feed-pro/instagram-feed.php',
				    'image' => $images_url . 'plugin-sb-instagram.png',
				    'title' => esc_html__( 'Smash Balloon Instagram Feeds Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'searchwp' ),
				    'url'   => 'https://smashballoon.com/instagram-feed/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'custom-facebook-feed/custom-facebook-feed.php' => [
			    'file'  => 'custom-facebook-feed/custom-facebook-feed.php',
			    'image' => $images_url . 'plugin-sb-fb.png',
			    'title' => esc_html__( 'Smash Balloon Facebook Feeds', 'searchwp' ),
			    'desc'  => esc_html__( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to embed albums, group content, reviews, live videos, comments, and reactions.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
			    'pro'   => [
				    'file'  => 'custom-facebook-feed-pro/custom-facebook-feed.php',
				    'image' => $images_url . 'plugin-sb-fb.png',
				    'title' => esc_html__( 'Smash Balloon Facebook Feeds Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to embed albums, group content, reviews, live videos, comments, and reactions.', 'searchwp' ),
				    'url'   => 'https://smashballoon.com/custom-facebook-feed/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'feeds-for-youtube/youtube-feed.php'           => [
			    'file'  => 'feeds-for-youtube/youtube-feed.php',
			    'image' => $images_url . 'plugin-sb-youtube.png',
			    'title' => esc_html__( 'Smash Balloon YouTube Feeds', 'searchwp' ),
			    'desc'  => esc_html__( 'Easily display YouTube videos on your WordPress site without writing any code. Comes with multiple layouts, ability to embed live streams, video filtering, ability to combine multiple channel videos, and more.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
			    'pro'   => [
				    'file'  => 'youtube-feed-pro/youtube-feed.php',
				    'image' => $images_url . 'plugin-sb-youtube.png',
				    'title' => esc_html__( 'Smash Balloon YouTube Feeds Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'Easily display YouTube videos on your WordPress site without writing any code. Comes with multiple layouts, ability to embed live streams, video filtering, ability to combine multiple channel videos, and more.', 'searchwp' ),
				    'url'   => 'https://smashballoon.com/youtube-feed/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'custom-twitter-feeds/custom-twitter-feed.php' => [
			    'file'  => 'custom-twitter-feeds/custom-twitter-feed.php',
			    'image' => $images_url . 'plugin-sb-twitter.png',
			    'title' => esc_html__( 'Smash Balloon Twitter Feeds', 'searchwp' ),
			    'desc'  => esc_html__( 'Easily display Twitter content in WordPress without writing any code. Comes with multiple layouts, ability to combine multiple Twitter feeds, Twitter card support, tweet moderation, and more.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
			    'pro'   => [
				    'file'  => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
				    'image' => $images_url . 'plugin-sb-twitter.png',
				    'title' => esc_html__( 'Smash Balloon Twitter Feeds Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'Easily display Twitter content in WordPress without writing any code. Comes with multiple layouts, ability to combine multiple Twitter feeds, Twitter card support, tweet moderation, and more.', 'searchwp' ),
				    'url'   => 'https://smashballoon.com/custom-twitter-feeds/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'trustpulse-api/trustpulse.php'                => [
			    'file'  => 'trustpulse-api/trustpulse.php',
			    'image' => $images_url . 'plugin-trustpulse.png',
			    'title' => esc_html__( 'TrustPulse', 'searchwp' ),
			    'desc'  => esc_html__( 'Boost your sales and conversions by up to 15% with real-time social proof notifications. TrustPulse helps you show live user activity and purchases to help convince other users to purchase.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/trustpulse-api.zip',
		    ],

		    'affiliate-wp/affiliate-wp.php'                => [
			    'file'  => 'affiliate-wp/affiliate-wp.php',
			    'image' => $images_url . 'plugin-affwp.png',
			    'title' => esc_html__( 'AffiliateWP', 'searchwp' ),
			    'desc'  => esc_html__( 'The #1 affiliate management plugin for WordPress. Easily create an affiliate program for your eCommerce store or membership site within minutes and start growing your sales with the power of referral marketing.', 'searchwp' ),
			    'url'   => 'https://affiliatewp.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
		    ],

		    'stripe/stripe-checkout.php'                   => [
			    'file'  => 'stripe/stripe-checkout.php',
			    'image' => $images_url . 'plugin-wp-simple-pay.png',
			    'title' => esc_html__( 'WP Simple Pay', 'searchwp' ),
			    'desc'  => esc_html__( 'The #1 Stripe payments plugin for WordPress. Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/stripe.zip',
			    'pro'   => [
				    'file'  => 'wp-simple-pay-pro-3/simple-pay.php',
				    'image' => $images_url . 'plugin-wp-simple-pay.png',
				    'title' => esc_html__( 'WP Simple Pay Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'The #1 Stripe payments plugin for WordPress. Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'searchwp' ),
				    'url'   => 'https://wpsimplepay.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],

		    'easy-digital-downloads/easy-digital-downloads.php' => [
			    'file'  => 'easy-digital-downloads/easy-digital-downloads.php',
			    'image' => $images_url . 'plugin-edd.png',
			    'title' => esc_html__( 'Easy Digital Downloads', 'searchwp' ),
			    'desc'  => esc_html__( 'The best WordPress eCommerce plugin for selling digital downloads. Start selling eBooks, software, music, digital art, and more within minutes. Accept payments, manage subscriptions, advanced access control, and more.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/easy-digital-downloads.zip',
		    ],

		    'sugar-calendar-lite/sugar-calendar-lite.php'  => [
			    'file'  => 'sugar-calendar-lite/sugar-calendar-lite.php',
			    'image' => $images_url . 'plugin-sugarcalendar.png',
			    'title' => esc_html__( 'Sugar Calendar', 'searchwp' ),
			    'desc'  => esc_html__( 'A simple & powerful event calendar plugin for WordPress that comes with all the event management features including payments, scheduling, timezones, ticketing, recurring events, and more.', 'searchwp' ),
			    'wporg' => 'https://downloads.wordpress.org/plugin/sugar-calendar-lite.zip',
			    'pro'   => [
				    'file'  => 'sugar-calendar/sugar-calendar.php',
				    'image' => $images_url . 'plugin-sugarcalendar.png',
				    'title' => esc_html__( 'Sugar Calendar Pro', 'searchwp' ),
				    'desc'  => esc_html__( 'A simple & powerful event calendar plugin for WordPress that comes with all the event management features including payments, scheduling, timezones, ticketing, recurring events, and more.', 'searchwp' ),
				    'url'   => 'https://sugarcalendar.com/?utm_source=searchwp&utm_medium=link&utm_campaign=About%20SearchWP',
			    ],
		    ],
	    ];
    }

	/**
	 * Set activation/installation statuses for every plugin.
	 *
	 * @since 4.3.0
	 *
	 * @param array $plugins Plugins data list.
	 *
	 * @return array
	 */
	private static function set_statuses( array $plugins ) {

		$installed = array_keys( get_plugins() );
		$active    = get_option( 'active_plugins', [] );

		foreach ( $plugins as $key => $plugin ) {

			$plugins[ $key ] = self::set_statuses_single( $plugin, $installed, $active );

            if ( isset( $plugin['pro'] ) ) {
	            $plugins[ $key ]['pro'] = self::set_statuses_single( $plugin['pro'], $installed, $active );
            }
		}

		return $plugins;
	}

	/**
	 * Set activation/installation statuses for a single plugin.
	 *
	 * @since 4.3.0
	 *
	 * @param array      $plugin    Plugin data.
	 * @param array|null $installed Installed plugins.
	 * @param array|null $active    Active plugins.
	 *
	 * @return array
	 */
	private static function set_statuses_single( array $plugin, $installed = null, $active = null ) {

        if ( $installed === null ) {
	        $installed = array_keys( get_plugins() );
        }

		if ( $active === null ) {
			$active = get_option( 'active_plugins', [] );
		}

        $plugin['installed'] = in_array( $plugin['file'], $installed, true );

        if ( $plugin['installed'] ) {
            $plugin['status'] = in_array( $plugin['file'], $active, true ) ? 'active' : 'inactive';
        } else {
            $plugin['status'] = 'missing';
        }

		return $plugin;
	}

	/**
	 * Get data for all plugins or one specific plugin.
	 *
	 * @since 4.3.0
	 *
	 * @param string|null $file Plugin file to get data for a single plugin.
	 */
	public static function get( string $file = null ) {

		if ( empty( self::$storage ) ) {
			self::$storage = self::set_statuses( self::fetch() );
		}

		if ( $file === null ) {
			return self::$storage;
		}

        if ( isset( self::$storage[ $file ] ) ) {
	        return self::$storage[ $file ];
        }

		$pro_versions = [];
		foreach ( self::$storage as $plugin ) {
			if ( ! empty( $plugin['pro']['file'] ) ) {
				$pro_versions[ $plugin['pro']['file'] ] = $plugin['pro'];
			}
		}

        if ( isset( $pro_versions[ $file ] ) ) {
            return $pro_versions[ $file ];
        }

		return null;
	}

	/**
	 * Determine if the plugin installations are allowed.
	 *
	 * @since 4.3.0
	 *
	 * @return bool
	 */
	public static function current_user_can_install() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		// Determine whether file modifications are allowed for SearchWP.
		if ( ! wp_is_file_mod_allowed( 'searchwp_can_install' ) ) {
			return false;
		}

        return true;
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.3.0
	 */
	public static function render() {

        echo '<div class="swp-content-container">';

        self::print_content_section();
        self::print_plugins();

        echo '</div>';
	}

	/**
	 * Print content section.
	 *
	 * @since 4.3.0
	 */
	private static function print_content_section() {

		?>
        <div class="swp-content-section swp-margin-b30">

            <div class="swp-content-block swp-bg--white">

                <div class="swp-flex--row sm:swp-flex--col swp-flex--gap25">
                    <div class="swp-flex--item swp-w-3/5 sm:swp-w-full">

                        <h2 class="swp-h2 swp-leading--160 swp-margin-b25">
                            <?php esc_html_e( 'Hello and welcome to SearchWP, the best WordPress search plugin. At SearchWP, we make all your content on your site discoverable for visitor searches.', 'searchwp' ); ?>
                        </h2>

                        <p class="swp-p-content swp-margin-b25">
                            <?php esc_html_e( 'The reality is that the default WordPress search form doesn\'t surface all your content. Custom fields are completely ignored and valuable metadata isn\'t included in search results.', 'searchwp' ); ?>
                        </p>

                        <p class="swp-p-content swp-margin-b25">
                            <?php esc_html_e( 'Our goal is to eliminate visitor confusion and frustration by providing the most accurate search results every time a search query is made.', 'searchwp' ); ?>
                        </p>

                        <p class="swp-p-content swp-margin-b25">
                            <?php esc_html_e( 'SearchWP is brought to you by the same team that\'s behind the largest WordPress resource site, WPBeginner, the most popular lead-generation software, OptinMonster, the best WordPress analytics plugin, MonsterInsights, and more!', 'searchwp' ); ?>
                        </p>

                        <p class="swp-p-content swp-margin-b0">
                            <?php esc_html_e( 'Yup, we know a thing or two about building awesome products that customers love!', 'searchwp' ); ?>
                        </p>

                    </div>

                    <div class="swp-flex--item swp-w-2/5 sm:swp-w-full">
                        <img class="swp-img" src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/about/team.jpeg' ); ?>" alt="Team photo">
                    </div>
                </div>

            </div>

        </div>
        <?php
	}

	/**
	 * Print plugins list.
	 *
	 * @since 4.3.0
	 */
	private static function print_plugins() {

		$plugins = self::get();

		echo '<div id="swp-plugins-list" class="swp-grid swp-grid--gap12">';
            foreach ( $plugins as $plugin ) {
                if ( isset( $plugin['pro'] ) ) {
                    $plugin = empty( $plugin['pro']['installed'] ) && in_array( $plugin['status'], [ 'missing', 'inactive' ], true ) ? $plugin : $plugin['pro'];
                }
	            self::print_plugin( $plugin );
            }
		echo '</div>';
	}

	/**
	 * Print a single plugin.
	 *
	 * @since 4.3.0
	 *
	 * @param array $plugin Plugin data.
	 */
	private static function print_plugin( array $plugin ) {
		?>
        <div class="swp-card swp-no-bord-btm">
            <div class="swp-card--content">
                <?php self::print_plugin_content( $plugin ); ?>
            </div>
            <div class="swp-card--footer">
				<?php self::print_plugin_footer( $plugin ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Print a single plugin content block.
	 *
	 * @since 4.3.0
	 *
	 * @param array $plugin Plugin data.
	 */
	private static function print_plugin_content( array $plugin ) {
		?>
        <div class="swp-flex--row swp-flex--gap20">
            <div class="swp-col">
                <div class="swp-card-img">
                    <img class="swp-img" src="<?php echo esc_url( $plugin['image'] ); ?>" alt="<?php esc_html_e( 'Plugin image', 'searchwp' ); ?>">
                </div>
            </div>

            <div class="swp-col">
                <h2 class="swp-plugin-name swp-card--h">
	                <?php echo esc_html( $plugin['title'] ); ?>
                </h2>
                <p class="swp-card--p">
                    <?php echo esc_html( $plugin['desc'] ); ?>
                </p>
            </div>
        </div>
		<?php
	}

	/**
	 * Print a single plugin footer block.
	 *
	 * @since 4.3.0
	 *
	 * @param array $plugin Plugin data.
	 */
	private static function print_plugin_footer( array $plugin ) {

		if ( ! isset( $plugin['file'], $plugin['status'] ) ) {
			return;
		}

		$plugin_status = $plugin['status'];
		$statuses      = self::get_action_contents_statuses();

		if ( ! array_key_exists( $plugin_status, $statuses ) ) {
			return;
		}

		// Do not display 'missing' status markup for all statuses except 'missing'
		// since none of the actions transition to 'missing' status.
		if ( $plugin_status !== 'missing' ) {
			unset( $statuses['missing'] );
		}

        ?>
        <div class="swp-plugin-statuses">
            <?php foreach ( $statuses as $status => $status_data ) : ?>
                <div class="swp-plugin-status swp-plugin-status-<?php echo esc_attr( $status ); ?> swp-flex--row swp-justify-between swp-flex--align-c" <?php echo $status !== $plugin_status ? 'style="display: none;"' : ''; ?>>
                    <p class="swp-card--p-bold">
                        <?php esc_html_e( 'Status', 'searchwp' ); ?>:
                        <span class="<?php echo esc_attr( $status_data['title_class'] ); ?>">
                            <?php echo esc_html( $status_data['title'] ); ?>
                        </span>
                    </p>
                    <?php if ( $status === 'missing' && isset( $plugin['url'] ) ) : ?>
                        <a href="<?php echo esc_url( $plugin['url'] ); ?>" class="swp-button <?php echo esc_attr( $status_data['button_class'] ); ?>" target="_blank" rel="noopener noreferrer">
	                        <?php echo esc_html( $status_data['action_title'] ); ?>
                        </a>
                    <?php else : ?>
                        <button class="swp-button <?php echo esc_attr( $status_data['button_class'] ); ?>" data-plugin="<?php echo esc_attr( $plugin['file'] ); ?>" <?php disabled( $status === 'active' ); ?>>
		                    <?php echo esc_html( $status_data['action_title'] ); ?>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
	}

	/**
	 * Get a single plugin actions block strings and classes.
	 *
	 * @since 4.3.0
	 */
	private static function get_action_contents_statuses() {

		return [
			'active'   => [
				'title'        => __( 'Active', 'searchwp' ),
				'action_title' => __( 'Activated', 'searchwp' ),
				'title_class'  => 'swp-text-green',
				'button_class' => '',
			],
			'inactive' => [
				'title'        => __( 'Inactive', 'searchwp' ),
				'action_title' => __( 'Activate', 'searchwp' ),
				'title_class'  => 'swp-text-red',
				'button_class' => '',
			],
			'missing'  => [
				'title'        => __( 'Not Installed', 'searchwp' ),
				'action_title' => __( 'Install Plugin', 'searchwp' ),
				'title_class'  => 'swp-text-gray',
				'button_class' => 'swp-button--green',
			],
		];
	}

	/**
	 * Install plugin.
	 *
	 * @since 4.3.0
	 */
	public static function ajax_install_plugin() {

		// Run a security check.
		Utils::check_ajax_permissions();

		$generic_error = esc_html__( 'There was an error while performing your request.', 'searchwp' );

		// Check if new installations are allowed.
		if ( ! self::current_user_can_install() ) {
			wp_send_json_error( $generic_error );
		}

		$error = esc_html__( 'Could not install the plugin. Please download it from searchwp.com and install it manually.', 'searchwp' );

		if ( ! isset( $_POST['plugin_file'] ) ) {
			wp_send_json_error( $error );
		}

		$plugin = self::get( sanitize_text_field( wp_unslash( $_POST['plugin_file'] ) ) );

		if ( empty( $plugin['wporg'] ) ) {
			wp_send_json_error( $error );
		}

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $plugin['wporg'], [ 'overwrite_package' => true ] );

		self::ajax_process_install_plugin_errors( $result, $skin );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin_basename = $upgrader->plugin_info();

		if ( empty( $plugin_basename ) ) {
			wp_send_json_error( $error );
		}

		// Return early if user has no permissions to activate the plugins.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_success(
				[
					'msg'        => esc_html__( 'Plugin installed.', 'searchwp' ),
					'showStatus' => 'inactive',
				]
			);
		}

		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_basename );

		if ( is_wp_error( $activated ) ) {
			wp_send_json_error( $result );
		}

		wp_send_json_success(
			[
				'msg'        => esc_html__( 'Plugin installed & activated.', 'searchwp' ),
				'showStatus' => 'active',
			]
		);
	}

	/**
	 * Process plugin install errors if any.
	 *
	 * @since 4.3.0
	 *
	 * @param bool|\WP_Error         $result Uprgrader install method result.
	 * @param \WP_Ajax_Upgrader_Skin $skin   AJAX upgrader skin.
	 */
	private static function ajax_process_install_plugin_errors( $result, \WP_Ajax_Upgrader_Skin $skin ) {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$status['debug'] = $skin->get_upgrade_messages();
		}

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();

			wp_send_json_error( $status );
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();

			wp_send_json_error( $status );
		} elseif ( $skin->get_errors()->has_errors() ) {
			$status['errorMessage'] = $skin->get_error_messages();

			wp_send_json_error( $status );
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'searchwp' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}
			wp_send_json_error( $status );
		}
	}

	/**
	 * Activate plugin.
	 *
	 * @since 4.3.0
	 */
	public static function ajax_activate_plugin() {

		// Run a security check.
		Utils::check_ajax_permissions();

		// Check for permissions.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'searchwp' ) );
		}

		$error = esc_html__( 'Could not activate the plugin. Please activate it on the Plugins page.', 'searchwp' );

		if ( ! isset( $_POST['plugin_file'] ) ) {
			wp_send_json_error( $error );
		}

		$plugin = self::get( sanitize_text_field( wp_unslash( $_POST['plugin_file'] ) ) );

		if ( empty( $plugin['file'] ) ) {
			wp_send_json_error( $error );
		}

		$activate = activate_plugins( $plugin['file'] );

		if ( is_wp_error( $activate ) ) {
			wp_send_json_error( $error );
		}

		wp_send_json_success(
			[
				'msg'        => esc_html__( 'Plugin activated.', 'searchwp' ),
				'showStatus' => 'active',
			]
		);
	}
}
