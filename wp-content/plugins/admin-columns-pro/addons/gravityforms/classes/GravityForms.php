<?php

namespace ACA\GravityForms;

use AC;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\Registerable;
use ACA\GravityForms\Column\EntryConfigurator;
use ACA\GravityForms\Column\EntryFactory;
use ACA\GravityForms\ListScreen;
use ACA\GravityForms\Search\Query;
use ACA\GravityForms\TableScreen;
use ACP\Search\QueryFactory;
use ACP\Search\TableScreenFactory;
use ACP\Service\IntegrationStatus;
use GFAPI;
use GFCommon;

final class GravityForms implements Registerable {

	const GROUP = 'gravity_forms';

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	/**
	 * Register hooks
	 */
	public function register() {
		if ( ! class_exists( 'GFCommon', false ) ) {
			return;
		}

		$minimum_gf_version = '2.5';

		if ( class_exists( 'GFCommon', false ) && version_compare( GFCommon::$version, $minimum_gf_version, '<' ) ) {
			return;
		}

		add_action( 'ac/list_screens', [ $this, 'register_list_screen' ] );

		// Group labels
		add_action( 'ac/column_groups', [ $this, 'register_column_group' ] );
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );

		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'ac/table_scripts', [ $this, 'table_scripts' ] );

		add_filter( "gform_noconflict_styles", [ $this, 'allowed_acp_styles' ] );
		add_filter( "gform_noconflict_scripts", [ $this, 'allowed_acp_scripts' ] );

		$services = [
			new TableScreen\Entry(),
			new Admin(),
			new IntegrationStatus( 'ac-addon-gravityforms' ),
		];

		array_map( function ( Registerable $service ) {
			$service->register();
		}, $services );

		// Enable Search
		QueryFactory::register( MetaTypes::GRAVITY_FORMS_ENTRY, Query::class );
		TableScreenFactory::register( ListScreen\Entry::class, Search\TableScreen\Entry::class );
	}

	public function register_list_screen() {
		$list_screen_types = AC\ListScreenTypes::instance();

		if ( ! $list_screen_types ) {
			return;
		}

		$forms = array_merge( GFAPI::get_forms(), GFAPI::get_forms( [ 'active' => false ] ) );

		foreach ( $forms as $form ) {
			$fieldFactory = new FieldFactory();
			$columnFactory = new EntryFactory( new FieldFactory() );

			$configurator = new EntryConfigurator( (int) $form['id'], $columnFactory, $fieldFactory );
			$configurator->register();

			$list_screen_types->register_list_screen( new ListScreen\Entry( $form['id'], $configurator ) );
		}
	}

	public function register_list_screen_group( AC\Groups $groups ) {
		$groups->register_group( self::GROUP, __( 'Gravity Forms', 'codepress-admin-columns' ), 8 );
	}

	public function register_column_group( $groups ) {
		$groups->register_group( self::GROUP, __( 'Gravity Forms', 'codepress-admin-columns' ), 11 );
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