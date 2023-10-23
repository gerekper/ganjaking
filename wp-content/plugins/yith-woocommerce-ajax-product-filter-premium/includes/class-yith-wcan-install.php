<?php
/**
 * Manage install, and performs all post update operations
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Install' ) ) {
	/**
	 * Filter Presets Handling
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Install {

		/**
		 * Name of Filters Lookup table
		 *
		 * @var string
		 */
		public static $filter_sessions;

		/**
		 * Stored version
		 *
		 * @var string
		 */
		protected static $stored_version;

		/**
		 * Default preset slug
		 *
		 * @var string
		 */
		protected static $default_preset_slug = 'default-preset';

		/**
		 * Hooks methods required to install/update plugin
		 *
		 * @return void
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'check_version' ), 5 );

			add_filter( 'yith_wcan_default_accent_color', array( __CLASS__, 'set_default_accent' ) );
		}

		/**
		 * Check current version, and trigger update procedures when needed
		 *
		 * @return void
		 */
		public static function check_version() {
			self::$stored_version = get_option( 'yith_wcan_version' );

			if ( version_compare( self::$stored_version, YITH_WCAN_VERSION, '<' ) ) {
				self::update();
				do_action( 'yith_wcan_updated' );
			}
		}

		/**
		 * Update/install procedure
		 *
		 * @return void
		 */
		public static function update() {
			self::maybe_create_preset();
			self::maybe_show_upgrade_note();
			self::maybe_update_options();
			self::maybe_flush_rules();
			self::update_version();
		}

		/**
		 * Create default preset, when it doesn't exists already
		 *
		 * @return void
		 */
		public static function maybe_create_preset() {
			// if preset already exists, skip.
			if ( ! self::should_create_default_preset() ) {
				return;
			}

			$new_preset = new YITH_WCAN_Preset();

			$new_preset->set_slug( self::$default_preset_slug );
			$new_preset->set_title( _x( 'Default preset', '[ADMIN] Name of default preset that is installed with the plugin', 'yith-woocommerce-ajax-navigation' ) );
			$new_preset->set_filters( self::get_default_filters() );
			$new_preset->save();

			update_option( 'yith_wcan_default_preset_created', true );

			do_action( 'yith_wcan_default_preset_created' );
		}

		/**
		 * Flag Upgrade Note for display, when there are widgets in the sidebar
		 *
		 * @return void
		 */
		public static function maybe_show_upgrade_note() {
			if ( ! self::should_show_upgrade_note() ) {
				return;
			}

			// set upgrade note status: 0 => hide; 1 => show.
			update_option( 'yith_wcan_upgrade_note_status', 1 );
		}

		/**
		 * Update options to latest version, when required
		 *
		 * @return void
		 */
		public static function maybe_update_options() {
			// do incremental upgrade.
			version_compare( self::$stored_version, '4.0.0', '<' ) && self::do_400_upgrade();
			version_compare( self::$stored_version, '4.1.0', '<' ) && self::do_410_upgrade();

			// space for future revisions.

			do_action( 'yith_wcan_did_option_upgrade' );
		}

		/**
		 * Updated version option to latest version, to avoid executing upgrade multiple times
		 *
		 * @return void
		 */
		public static function update_version() {
			update_option( 'yith_wcan_version', YITH_WCAN_VERSION );
		}

		/**
		 * Get default preset, when it exists
		 *
		 * @return bool|YITH_WCAN_Preset
		 */
		public static function get_default_preset() {
			return YITH_WCAN_Preset_Factory::get_preset( self::$default_preset_slug );
		}

		/**
		 * Checks whether we should create default preset
		 *
		 * @return bool Whether we should create default preset
		 */
		public static function should_create_default_preset() {
			return ! self::get_default_preset() && ! get_option( 'yith_wcan_default_preset_created' );
		}

		/**
		 * Checks whether we should show upgrade to preset notice
		 *
		 * @return bool Whether we should show upgrade note
		 */
		public static function should_show_upgrade_note() {
			// check if note was already dismissed.
			if ( '0' === get_option( 'yith_wcan_upgrade_note_status' ) ) {
				return false;
			}

			// check whether there is any filter in the sidebar.
			return ! ! yith_wcan_get_sidebar_with_filters();
		}

		/**
		 * Flush rewrite rules on key plugin update
		 *
		 * @return void
		 */
		public static function maybe_flush_rules() {
			version_compare( self::$stored_version, '4.0.0', '<' ) && flush_rewrite_rules();
		}

		/**
		 * Set default accent color, when possible, matching theme's style
		 *
		 * @param string $default_accent Default color accent.
		 *
		 * @return string Filtered color code.
		 */
		public static function set_default_accent( $default_accent ) {
			if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
				return $default_accent;
			}

			return get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' );
		}

		/**
		 * Upgrade options to version 4.0.0
		 *
		 * @return void.
		 */
		protected static function do_400_upgrade() {
			$old_options = get_option( 'yit_wcan_options' );

			if ( ! $old_options ) {
				return;
			}

			$options_to_export = array(
				'yith_wcan_enable_seo',
				'yith_wcan_seo_value',
				'yith_wcan_seo_rel_nofollow',
				'yith_wcan_change_browser_url',
			);

			foreach ( $options_to_export as $option ) {
				update_option( $option, yith_wcan_get_option( $option ) );
			}

			do_action( 'yith_wcan_did_400_upgrade' );
		}

		/**
		 * Upgrade options to version 4.1.0
		 *
		 * Scratch method; nothing needs to be done on this version of the software.
		 *
		 * @return void.
		 */
		protected static function do_410_upgrade() {
			do_action( 'yith_wcan_did_410_upgrade' );
		}

		/**
		 * Generates default filters for the preset created on first installation of the plugin
		 *
		 * @return array Array of filters.
		 */
		protected static function get_default_filters() {
			$filters = array();

			// set taxonomies filters.
			$filters = array_merge( $filters, self::get_taxonomies_filters() );

			return apply_filters( 'yith_wcan_default_filters', $filters );
		}

		/**
		 * Generates default Taxonomies filters for the preset created on first installation of the plugin
		 *
		 * @return array Array of filters.
		 */
		protected static function get_taxonomies_filters() {
			$filters = array();

			// start with taxonomy filters.
			$supported_taxonomies = YITH_WCAN_Query()->get_supported_taxonomies();

			foreach ( $supported_taxonomies as $taxonomy_slug => $taxonomy_object ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy_slug,
						'hide_empty' => true,
						'number'     => apply_filters( 'yith_wcan_max_default_term_count', 20 ),
					)
				);

				if ( empty( $terms ) ) {
					continue;
				}

				$filter      = new YITH_WCAN_Filter_Tax();
				$terms_array = array();

				foreach ( $terms as $term ) {
					$terms_array[ $term->term_id ] = array(
						'label'   => $term->name,
						'tooltip' => $term->name,
					);
				}

				// translators: 1. Taxonomy name.
				$filter->set_title( sprintf( _x( 'Filter by %s', '[ADMIN] Name of default taxonomy filter created by plugin', 'yith-woocommerce-ajax-navigation' ), $taxonomy_object->label ) );
				$filter->set_taxonomy( $taxonomy_slug );
				$filter->set_terms( $terms_array );
				$filter->set_filter_design( 'checkbox' );
				$filter->set_show_toggle( 'no' );
				$filter->set_show_count( 'no' );
				$filter->set_hierarchical( 'no' );
				$filter->set_multiple( 'yes' );
				$filter->set_relation( 'and' );
				$filter->set_adoptive( 'hide' );

				$filters[] = $filter->get_data();
			}

			return $filters;
		}

	}
}
