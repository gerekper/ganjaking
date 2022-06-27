<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Option;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Form;

/**
 * Crawl_Settings_Integration class
 */
class Crawl_Settings_Integration implements Integration_Interface {

	/**
	 * Holds the settings + labels for the head clean up piece.
	 *
	 * @var array
	 */
	private $basic_settings;

	/**
	 * Holds the settings + labels for the HTTP header clean up piece.
	 *
	 * @var array
	 */
	private $header_settings;

	/**
	 * Holds the settings + labels for the feeds clean up.
	 *
	 * @var array
	 */
	private $feed_settings;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * In this case: when on an admin page.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Registers an action to add a new tab to the General page.
	 */
	public function register_hooks() {
		$this->register_setting_labels();

		\add_action( 'wpseo_settings_tab_crawl_cleanup', [ $this, 'add_crawl_settings_tab_content' ] );
		\add_action( 'wpseo_settings_tab_crawl_cleanup_network', [ $this, 'add_crawl_settings_tab_content_network' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue the workouts app.
	 */
	public function enqueue_assets() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date is not processed or saved.
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'wpseo_dashboard' ) {
			return;
		}

		\wp_enqueue_script( 'wp-seo-premium-crawl-settings' );
	}

	/**
	 * Connects the settings to their labels.
	 *
	 * @return void
	 */
	private function register_setting_labels() {
		$this->feed_settings = [
			'remove_feed_global'            => \__( 'Global feed', 'wordpress-seo-premium' ),
			'remove_feed_global_comments'   => \__( 'Global comment feeds', 'wordpress-seo-premium' ),
			'remove_feed_post_comments'     => \__( 'Post comments feeds', 'wordpress-seo-premium' ),
			'remove_feed_authors'           => \__( 'Post authors feeds', 'wordpress-seo-premium' ),
			'remove_feed_post_types'        => \__( 'Post type feeds', 'wordpress-seo-premium' ),
			'remove_feed_categories'        => \__( 'Category feeds', 'wordpress-seo-premium' ),
			'remove_feed_tags'              => \__( 'Tag feeds', 'wordpress-seo-premium' ),
			'remove_feed_custom_taxonomies' => \__( 'Custom taxonomy feeds', 'wordpress-seo-premium' ),
			'remove_feed_search'            => \__( 'Search results feeds', 'wordpress-seo-premium' ),
			'remove_atom_rdf_feeds'         => \__( 'Atom/RDF feeds', 'wordpress-seo-premium' ),
		];

		$this->basic_settings = [
			'remove_shortlinks'     => \__( 'Shortlinks', 'wordpress-seo-premium' ),
			'remove_rest_api_links' => \__( 'REST API links', 'wordpress-seo-premium' ),
			'remove_rsd_wlw_links'  => \__( 'RSD / WLW links', 'wordpress-seo-premium' ),
			'remove_oembed_links'   => \__( 'oEmbed links', 'wordpress-seo-premium' ),
			'remove_generator'      => \__( 'Generator tag', 'wordpress-seo-premium' ),
			'remove_emoji_scripts'  => \__( 'Emoji scripts', 'wordpress-seo-premium' ),
		];

		$this->header_settings = [
			'remove_pingback_header'   => \__( 'Pingback HTTP header', 'wordpress-seo-premium' ),
			'remove_powered_by_header' => \__( 'Powered by HTTP header', 'wordpress-seo-premium' ),
		];
	}

	/**
	 * Adds content to the Crawl Cleanup tab.
	 *
	 * @param Yoast_Form $yform The yoast form object.
	 */
	public function add_crawl_settings_tab_content( $yform ) {
		$this->add_crawl_settings( $yform, false );
	}

	/**
	 * Adds content to the Crawl Cleanup network tab.
	 *
	 * @param Yoast_Form $yform The yoast form object.
	 */
	public function add_crawl_settings_tab_content_network( $yform ) {
		$this->add_crawl_settings( $yform, true );
	}

	/**
	 * Print the settings sections.
	 *
	 * @param Yoast_Form $yform        The Yoast form class.
	 * @param boolean    $allow_prefix Whether to prefix options with the allow prefix or not.
	 *
	 * @return void
	 */
	private function add_crawl_settings( $yform, $allow_prefix ) {
		echo '<h3 class="yoast-crawl-settings">'
			. \esc_html__( 'Basic crawl settings', 'wordpress-seo-premium' )
			. '</h3>';

		if ( ! $allow_prefix ) {
			echo '<p class="yoast-crawl-settings-explanation">'
				. \esc_html__( 'Remove links added by WordPress to the header and &lt;head&gt;.', 'wordpress-seo-premium' )
				. '</p>';
		}
		$this->print_toggles( $this->basic_settings, $yform, $allow_prefix );
		$this->print_toggles( $this->header_settings, $yform, $allow_prefix );

		echo '<h3 class="yoast-crawl-settings">'
			. \esc_html__( 'Feed crawl settings', 'wordpress-seo-premium' )
			. '</h3>';

		if ( ! $allow_prefix ) {
			echo '<p class="yoast-crawl-settings-explanation">'
				. \esc_html__( "Remove feed links added by WordPress that aren't needed for this site.", 'wordpress-seo-premium' )
				. '</p>';
		}

		$this->print_toggles( $this->feed_settings, $yform, $allow_prefix );
	}

	/**
	 * Prints a list of toggles for an array of settings with labels.
	 *
	 * @param array      $settings     The settings being displayed.
	 * @param Yoast_Form $yform        The Yoast form class.
	 * @param boolean    $allow_prefix Whether we should prefix with the allow key.
	 *
	 * @return void
	 */
	private function print_toggles( array $settings, Yoast_Form $yform, $allow_prefix = false ) {
		$toggles        = [
			'off' => \__( 'Keep', 'wordpress-seo-premium' ),
			'on'  => \__( 'Remove', 'wordpress-seo-premium' ),
		];
		$setting_prefix = '';

		if ( $allow_prefix ) {
			$setting_prefix = WPSEO_Option::ALLOW_KEY_PREFIX;
			$toggles        = [
				// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Reason: text is originally from Yoast SEO.
				'on'  => \__( 'Allow Control', 'wordpress-seo' ),
				// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Reason: text is originally from Yoast SEO.
				'off' => \__( 'Disable', 'wordpress-seo' ),
			];
		}
		foreach ( $settings as $setting => $label ) {
			$yform->toggle_switch(
				$setting_prefix . $setting,
				$toggles,
				$label
			);
			if ( $setting === 'remove_feed_global_comments' && ! $allow_prefix ) {
				echo '<p class="yoast-global-comments-feed-help">';
				echo \esc_html__( 'By removing Global comments feed, Post comments feeds will be removed too.', 'wordpress-seo-premium' );
				echo '</p>';
			}
		}
	}
}
