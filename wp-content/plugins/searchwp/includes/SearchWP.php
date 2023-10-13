<?php
/**
 * The main SearchWP class.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

use SearchWP\Utils;
use SearchWP\Index\Controller as Index;

/**
 * Class SearchWP initializes core components.
 *
 * @since 4.0
 */
class SearchWP {

	/**
	 * Singleton Index reference.
	 *
	 * @since 4.0
	 * @var Index
	 */
	public static $index;

	/**
	 * Singleton Indexer reference.
	 *
	 * @since 4.0
	 * @var \SearchWP\Indexer
	 */
	public static $indexer;

	/**
	 * Regsitered Sources reference.
	 *
	 * @since 4.0
	 * @var \SearchWP\Source[]
	 */
	private static $sources = [];

	/**
	 * Singleton Native reference.
	 *
	 * @since 4.0
	 * @var \SearchWP\Native
	 */
	public static $native;

	/**
	 * Whether suggestion output has been ouput.
	 *
	 * @since 4.0.32
	 * @var bool
	 */
	private $did_suggestion = false;

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @return void
	 */
	function __construct() {
		new \SearchWP\License();
		new \SearchWP\Debug();
		new \SearchWP\CLI();

		add_action( 'admin_init', function() {
			\SearchWP\Upgrader::run();
		}, 99999 );

		\SearchWP\Admin\Views\WelcomeView::init();

		add_action( 'init', [ $this, 'init' ], 99999 );

		if ( ! has_action( SEARCHWP_PREFIX . 'network_install', [ __CLASS__, 'network_install' ] ) ) {
			add_action( SEARCHWP_PREFIX . 'network_install', [ __CLASS__, 'network_install' ] );
		}

		// Single event after Engine save.
		if ( ! has_action( SEARCHWP_PREFIX . 'index_dispatch', [ $this, '_dispatch' ] ) ) {
			add_action( SEARCHWP_PREFIX . 'index_dispatch', [ $this, '_dispatch' ] );
		}

		add_action( 'switch_blog', function( $new_blog_id, $prev_blog_id ) {
			if (
				$new_blog_id != $prev_blog_id
				&& apply_filters( 'searchwp\auto_update_providers', false, [ 'site' => $new_blog_id, ] )
			) {
				$this->set_providers();
			}
		}, 99, 2 );
	}

	/**
	 * Callback to single CRON event after Engine save.
	 *
	 * @since 4.0
	 */
	public function _dispatch() {
		self::$indexer->trigger();
	}

	/**
	 * Callback to perform network wide installation procedure.
	 *
	 * @since 4.0
	 */
	public static function network_install() {
		\SearchWP\Upgrader::run( true );
	}

	/**
	 * Getter for registered Sources.
	 *
	 * @since 4.0
	 * @return \SearchWP\Source[]
	 */
	public static function get_sources() {
		return self::$sources;
	}

	/**
	 * Setter and initializer for Sources.
	 *
	 * @since 4.0
	 * @return void
	 */
	private static function set_sources() {
		$sources = apply_filters( 'searchwp\sources', self::get_core_sources() );

		// If the data is unexpected, revert.
		if ( ! is_array( $sources ) || empty( $sources ) ) {
			do_action( 'searchwp\debug\log', 'Found unexpected Sources, reverting', 'main' );
			do_action( 'searchwp\debug\log', print_r( $sources, true ), 'main' );
			self::$sources = self::get_core_sources();

			return;
		}

		// Validate that we have only Source objects.
		foreach ( $sources as $source ) {
			if ( $source instanceof \SearchWP\Source ) {
				$source->init();
				self::$sources[ $source->get_name() ] = $source;
			} else {
				do_action( 'searchwp\debug\log', 'Skipping invalid Source', 'main' );
				do_action( 'searchwp\debug\log', print_r( $source, true ), 'main' );
			}
		}
	}

	/**
	 * Defines the Sources we're going to support by default.
	 *
	 * @since 4.0
	 * @return array The Sources themselves.
	 */
	private static function get_core_sources() {
		$registered_post_types = Utils::get_post_types();

		// Add each post type as a Source.
		$post_types = [];
		foreach ( $registered_post_types as $key => $post_type ) {
			// Attachments require a custom Source so remove it for now.
			if ( 'attachment' === $key ) {
				continue;
			}

			$post_types[ $key ] = new \SearchWP\Sources\Post( $post_type );
		}

		$sources = array_values( $post_types );

		// Add each Taxonomy as a Source
		$registered_taxonomies = Utils::get_taxonomies();

		$taxonomies = [];
		foreach ( $registered_taxonomies as $key => $taxonomy_name ) {
			$taxonomies[$key] = new \SearchWP\Sources\Taxonomy( $taxonomy_name );
		}

		$sources = array_merge( $sources, array_values( $taxonomies ) );


		$sources[] = new \SearchWP\Sources\Attachment();
		$sources[] = new \SearchWP\Sources\Comment();
		$sources[] = new \SearchWP\Sources\User();


		return $sources;
	}

	/**
	 * Environment setup.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function set_providers() {
		self::set_sources();

		// Instantiate Index so we can instantiate Sources.
		self::$index = new Index();
		self::$index->_add_hooks();

		// Instantiate the Indexer.
		self::$indexer = new \SearchWP\Indexer();

		do_action( 'searchwp\init' );
	}

	/**
	 * Callback to init action, trigger the Indexer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function init() {

		$this->load_plugin_textdomain();

		self::setup_integrations();

		$this->set_providers();

		// Implement global behaviors.
		new \SearchWP\Statistics();
		new \SearchWP\Logic\Stopwords();
		new \SearchWP\Logic\Synonyms();
		new \SearchWP\Logic\PartialMatches();

		searchwp()
			->register( \SearchWP\Debug\Watcher::class )
			->init();

		searchwp()
			->register( \SearchWP\Debug\Console\Console::class )
			->init();

		searchwp()
			->register( \SearchWP\Forms\Frontend::class )
			->init();

		searchwp()
			->register( \SearchWP\Results\Frontend::class )
			->init();

		// Hook in to core behavior.
		$this->add_hooks();

		// Handle native searches.
		self::$native = new \SearchWP\Native();

		// Add REST search handler.
		if ( apply_filters( 'searchwp\rest', true ) ) {
			add_filter( 'wp_rest_search_handlers', function( $handlers ) {
				return [ new \SearchWP\Rest() ];
			}, 99 );
		}

		// Schedule Maintenance.
		if ( ! wp_next_scheduled( SEARCHWP_PREFIX . 'maintenance' ) ) {
			wp_schedule_event( time(), 'daily', SEARCHWP_PREFIX . 'maintenance' );
		}

		do_action( 'searchwp\loaded' );
	}

	/**
	 * Set up global integrations.
	 *
	 * @since 4.1.5
	 * @return void
	 */
	private static function setup_integrations() {
		// Divi Search Results Template with Archive Posts Module.
		if ( apply_filters( 'searchwp\integration\divi', 'divi' === strtolower( get_template() ) ) ) {
			new \SearchWP\Integrations\Divi();
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			if ( file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			} else {
				return;
			}
		}

		// Beaver Builder Themer Plugin doesn't need an integration in most cases, so it's opt-in.
		if ( apply_filters(
			'searchwp\integration\beaver-builder',
			is_plugin_active( 'bb-plugin/fl-builder.php' ) )
		) {
			new \SearchWP\Integrations\BeaverBuilder();
		}

		if ( apply_filters(
			'searchwp\integration\wp-all-import',
			is_plugin_active( 'wp-all-import/plugin.php' )
			|| is_plugin_active( 'wp-all-import-pro/wp-all-import-pro.php' )
		)
		) {
			$wpai = new \SearchWP\Integrations\WpAllImport();
			$wpai->init();
		}
	}

	/**
	 * Adds hooks to core behavior.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function add_hooks() {
		// Output suggested search after a query is run.
		add_action( 'searchwp\query\ran', [ $this, 'query_ran' ], 5 );

		// If we're in the Admin, implement our Options screen.
		if ( is_admin() ) {
			new \SearchWP\Admin\AdminBar();

			if ( empty( \SearchWP\Settings::get( 'hide_announcements' ) ) ) {
				\SearchWP\Admin\Notifications\Notifications::init();
			}

			// Legacy version of Metrics extension compatibility.
			// This line has to be deleted once Metrics v1.4.2 is released.
			\SearchWP\Admin\LegacyMetricsCompat::hooks();

			new \SearchWP\Admin\OptionsView();

			new \SearchWP\Admin\DashboardWidgets\StatisticsDashboardWidget();

			if ( apply_filters( 'searchwp\missing_integration_notices', true ) ) {
				add_action( 'admin_init', [ $this, 'check_for_missing_integrations' ] );
			}

			if ( file_exists( SEARCHWP_PLUGIN_DIR . '/searchwp.php' ) ) {
				new \SearchWP\Admin\AdminNotices\DirtyInstallAdminNotice();
			}
		}

		// Trigger the Indexer (and Controller) when it has been unpaused.
		add_action( 'searchwp\settings\update\indexer_paused', function( $value ) {
			if ( $value ) {
				self::$index->trigger();
				self::$indexer->trigger();
			}
		}, 5 );
	}

	/**
	 * Callback for searchwp\query\ran hook.
	 *
	 * @since 4.0
	 * @param \SearchWP\Query $query The query being run.
	 * @return void
	 */
	public function query_ran( \SearchWP\Query $query ) {
		// Suggested search handling.
		if (
			$query->get_suggested_search()
			&& apply_filters( 'searchwp\query\output_suggested_search',
					\SearchWP\Settings::get( 'do_suggestions', 'boolean' ),
					$query )
			&& ! $this->did_suggestion
		) {
			$this->did_suggestion = true;
			add_action( 'loop_start', function( $wp_query ) use( $query ) {
				if ( $wp_query->is_main_query() ) {
					$this->output_suggested_search( $query->get_suggested_search() );
				}
			} );
		}
	}

	/**
	 * Callback for loop_start action to output suggested search revision.
	 *
	 * @since 4.0
	 * @param string $suggestion The revised search.
	 * @return void
	 */
	public function output_suggested_search( string $suggestion ) {
		do_action( 'searchwp\debug\log', "Render suggested search: {$suggestion}", 'core' );

		echo '<p class="searchwp-revised-search-notice">';
		echo wp_kses(
			sprintf(
				apply_filters( 'searchwp\query\revised_search_notice',
					// Translators: Placeholder is the revised search string.
					__( 'Showing results for <em class="searchwp-suggested-revision-query">%s</em>', 'searchwp' )
				),
				esc_html( $suggestion )
			),
			[ 'em' => [ 'class' => [], ], ]
		);
		echo '</p>';
	}

	/**
	 * Load textdomain.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'searchwp', false, dirname( plugin_basename( SEARCHWP_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Compares active Plugins with known SearchWP Extensions and outputs an Admin Notice when one is missing.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function check_for_missing_integrations() {
		// These are the available integration Extensions.
		$integration_extensions = [
			'bbpress' => [
				'plugin' => [
					'file' => 'bbpress/bbpress.php',
					'name' => 'bbPress',
					'url' => 'https://wordpress.org/plugins/bbpress/',
				],
				'integration' => [
					'file' => 'searchwp-bbpress/searchwp-bbpress.php',
					'name' => 'bbPress Integration',
					'url' => 'https://searchwp.com/extensions/bbpress-integration/',
				],
			],
			'wpml' => [
				'plugin' => [
					'file' => 'sitepress-multilingual-cms/sitepress.php',
					'name' => 'WPML',
					'url' => 'http://wpml.org/',
				],
				'integration' => [
					'file' => 'searchwp-wpml/searchwp-wpml.php',
					'name' => 'WPML Integration',
					'url' => 'https://searchwp.com/extensions/wpml-integration/',
				],
			],
			'polylang' => [
				'plugin' => [
					'file' => 'polylang/polylang.php',
					'name' => 'Polylang',
					'url' => 'https://wordpress.org/plugins/polylang/',
				],
				'integration' => [
					'file' => 'searchwp-polylang/searchwp-polylang.php',
					'name' => 'Polylang Integration',
					'url' => 'https://searchwp.com/extensions/polylang-integration/',
				],
			],
			'woocommerce' => [
				'plugin' => [
					'file' => 'woocommerce/woocommerce.php',
					'name' => 'WooCommerce',
					'url' => 'https://wordpress.org/plugins/woocommerce/',
				],
				'integration' => [
					'file' => 'searchwp-woocommerce/searchwp-woocommerce.php',
					'name' => 'WooCommerce Integration',
					'url' => 'https://searchwp.com/extensions/woocommerce-integration/',
				],
			],
			'wpjobmanager' => [
				'plugin' => [
					'file' => 'wp-job-manager/wp-job-manager.php',
					'name' => 'WP Job Manager',
					'url' => 'https://wordpress.org/plugins/wp-job-manager/',
				],
				'integration' => [
					'file' => 'searchwp-wp-job-manager-integration/searchwp-wp-job-manager-integration.php',
					'name' => 'WP Job Manager Integration',
					'url' => 'https://searchwp.com/extensions/wp-job-manager-integration/',
				],
			],
			'privatecontent' => [
				'plugin' => [
					'file' => 'private-content/private_content.php',
					'name' => 'PrivateContent',
					'url' => 'http://codecanyon.net/item/privatecontent-multilevel-content-plugin/1467885',
				],
				'integration' => [
					'file' => 'searchwp-privatecontent/searchwp-privatecontent.php',
					'name' => 'PrivateContent Integration',
					'url' => 'https://searchwp.com/extensions/privatecontent-integration/',
				],
			],
		];

		foreach ( $integration_extensions as $slug => $integration_extension ) {
			if (
				is_plugin_active( $integration_extension['plugin']['file'] )
				&& ! is_plugin_active( $integration_extension['integration']['file'] )
			) {
				new \SearchWP\Admin\AdminNotices\MissingIntegrationAdminNotice( $slug, $integration_extension );
			}
		}
	}
}
