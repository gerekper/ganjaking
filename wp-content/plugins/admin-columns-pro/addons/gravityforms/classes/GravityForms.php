<?php

namespace ACA\GravityForms;

use AC;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\DefaultColumnsRepository;
use AC\Registerable;
use ACA\GravityForms\ListScreen;
use ACA\GravityForms\Search\Query;
use ACA\GravityForms\TableScreen;
use ACP\Search\QueryFactory;
use ACP\Search\TableScreenFactory;
use ACP\Service\IntegrationStatus;
use GFCommon;

final class GravityForms implements Registerable {

	public const GROUP = 'gravity_forms';

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! class_exists( 'GFCommon', false ) ) {
			return;
		}

		$minimum_gf_version = '2.5';

		if ( class_exists( 'GFCommon', false ) && version_compare( GFCommon::$version, $minimum_gf_version, '<' ) ) {
			return;
		}

		AC\ListScreenFactory::add( new ListScreenFactory\EntryFactory() );

		add_action( 'ac/column_groups', [ $this, 'register_column_group' ] );
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'ac/table_scripts', [ $this, 'table_scripts' ] );
		add_filter( "gform_noconflict_styles", [ $this, 'allowed_acp_styles' ] );
		add_filter( "gform_noconflict_scripts", [ $this, 'allowed_acp_scripts' ] );

		$services = [
			new Service\ListScreens(),
			new TableScreen\Entry( new AC\ListScreenFactory(), AC()->get_storage(), new DefaultColumnsRepository() ),
			new Admin(),
			new IntegrationStatus( 'ac-addon-gravityforms' ),
		];

		array_map( static function ( Registerable $service ) {
			$service->register();
		}, $services );

		// Enable Search
		QueryFactory::register( MetaTypes::GRAVITY_FORMS_ENTRY, Query::class );
		TableScreenFactory::register( ListScreen\Entry::class, Search\TableScreen\Entry::class );
	}

	public function register_column_group( AC\Groups $groups ): void {
		$groups->add( 'gravity_forms', __( 'Gravity Forms', 'codepress-admin-columns' ), 14 );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	private function is_acp_asset( $key ) {
		$acp_prefixes = [ 'ac-', 'acp-', 'aca-', 'editor', 'mce-view', 'quicktags', 'common', 'tinymce' ];

		foreach ( $acp_prefixes as $prefix ) {
			if ( strpos( $key, $prefix ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $objects
	 *
	 * @return array
	 */
	public function allowed_acp_styles( $objects ) {
		global $wp_styles;

		foreach ( $wp_styles->queue as $handle ) {
			if ( ! $this->is_acp_asset( $handle ) ) {
				continue;
			}

			$objects[] = $handle;
		}

		return $objects;
	}

	/**
	 * @param array $objects
	 *
	 * @return array
	 */
	public function allowed_acp_scripts( $objects ) {
		global $wp_scripts;

		foreach ( $wp_scripts->queue as $handle ) {
			if ( ! $this->is_acp_asset( $handle ) ) {
				continue;
			}

			$objects[] = $handle;
		}

		return $objects;
	}

	public function admin_scripts() {
		wp_enqueue_style( 'gform_font_awesome' );
	}

	public function table_scripts() {
		$style = new Style( 'aca-gf-table', $this->location->with_suffix( 'assets/css/table.css' ) );
		$style->enqueue();

		$script = new Script( 'aca-gf-table', $this->location->with_suffix( 'assets/js/table.js' ) );
		$script->enqueue();

		wp_enqueue_script( 'wp-tinymce' );
	}

}