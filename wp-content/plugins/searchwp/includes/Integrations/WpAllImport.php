<?php

/**
 * SearchWP WpAllImport.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class WpAllImport is responsible for reacting to WP All Import routines.
 *
 * @since 4.1.16
 */
class WpAllImport {
	/**
	 * Local cache of Source names.
	 *
	 * @since 4.1.16
	 * @var string[]
	 */
	private $sources = [];

	/**
	 * Initializer.
	 *
	 * @since 4.1.16
	 * @return void
	 */
	public function init() {
		$this->ignore_delta_update_triggers();
		$this->add_hooks();
		$this->cycle_entries_during_import();
	}

	/**
	 * Add hooks to WPAI
	 *
	 * @since 4.1.17
	 * @return void
	 */
	private function add_hooks() {
		$class = '\\SearchWP\\Integrations\\WpAllImport';

		// There are two import hooks, we're going to default to xml_import.
		if ( apply_filters( 'searchwp\integration\wp-all-import\use-xml-hooks', false ) ) {
			add_action( 'pmxi_before_post_import', "{$class}::before_import", 20 );
			add_action( 'pmxi_after_post_import',  "{$class}::after_import",  20 );
		} else {
			add_action( 'pmxi_before_xml_import',  "{$class}::before_import", 20 );
			add_action( 'pmxi_after_xml_import',   "{$class}::after_import",  20 );
		}
	}

	/**
	 * Tell SearchWP to cycle entries after WPAI has edited them.
	 *
	 * @since 4.1.17
	 * @return void
	 */
	private function cycle_entries_during_import() {
		add_action( 'pmxi_saved_post', function( $post_id ) {
			$source_name = \SearchWP\Utils::get_post_type_source_name( get_post_type( $post_id ) );

			if ( ! in_array( $source_name, $this->sources, true ) ) {
				return;
			}

			$source  = \SearchWP::$index->get_source_by_name( $source_name );
			$entry   = new \SearchWP\Entry( $source, $post_id );
			$entries = new \SearchWP\Entries( $source, [ $post_id, ] );

			// Cycle this entry in the Index.
			\SearchWP::$index->drop( $source, $post_id, true );
			\SearchWP::$index->introduce( $entries );
			\SearchWP::$index->add( $entry );
		}, 10 );
	}

	/**
	 * Ignore delta triggers during import process.
	 *
	 * @since 4.1.17
	 * @return void
	 */
	private function ignore_delta_update_triggers() {
		if (
			( function_exists( 'wp_doing_cron' ) && wp_doing_cron() && isset( $_REQUEST['import_id'] ) )
			|| ( isset( $_REQUEST['page'] ) && 'pmxi-admin-import' === $_REQUEST['page'] )
		) {
			add_filter( 'searchwp\index\source\add_hooks', '__return_false', 999 );
		}

		add_action( 'searchwp\loaded', function() {
			$this->sources = \SearchWP\Utils::get_global_engine_source_names();
		} );
	}

	/**
	 * Callback before import process has started.
	 *
	 * @since 4.1.16
	 * @param integer $import_id The ID of the import.
	 * @return void
	 */
	public static function before_import( $import_id ) {
		do_action( 'searchwp\debug\log', 'Pausing indexer before WP All Import process ' . $import_id, 'integration' );
		\SearchWP::$indexer->pause();
	}

	/**
	 * Callback after import process has finished.
	 *
	 * @since 4.1.16
	 * @param integer $import_id The ID of the import.
	 * @return void
	 */
	public static function after_import( $import_id ) {
		do_action( 'searchwp\debug\log', 'Unpausing indexer after WP All Import process ' . $import_id, 'integration' );
		\SearchWP::$indexer->unpause();
	}
}
