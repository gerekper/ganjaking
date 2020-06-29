<?php

namespace ACP\Search;

use AC;
use AC\Asset\Enqueueable;
use ACP;
use ACP\Search\Preferences;

class TableScreenOptions {

	const INPUT_NAME = 'acp_enable_smart_filtering_button';

	/** @var Enqueueable[] $assets */
	private $assets;

	/** @var Preferences\SmartFiltering */
	private $preferences;

	/** @var ACP\Settings\ListScreen\HideOnScreen\Filters */
	private $hide_filters;

	/**
	 * @var Settings\HideOnScreen\SmartFilters
	 */
	private $hide_smart_filters;

	public function __construct(
		array $assets,
		Preferences\SmartFiltering $preferences,
		ACP\Settings\ListScreen\HideOnScreen\Filters $hide_filters,
		Settings\HideOnScreen\SmartFilters $hide_smart_filters
	) {
		$this->assets = $assets;
		$this->preferences = $preferences;
		$this->hide_filters = $hide_filters;
		$this->hide_smart_filters = $hide_smart_filters;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
		add_action( 'ac/table', [ $this, 'register_screen_option' ] );
		add_action( 'wp_ajax_' . self::INPUT_NAME, [ $this, 'update_smart_filtering_preference' ] );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return int
	 */
	private function is_active( AC\ListScreen $list_screen ) {
		return $this->preferences->is_active( $list_screen );
	}

	public function update_smart_filtering_preference() {
		check_ajax_referer( 'ac-ajax' );

		$is_active = ( 'true' === filter_input( INPUT_POST, 'value' ) ) ? 1 : 0;

		$this->preferences->set( filter_input( INPUT_POST, 'list_screen' ), $is_active );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( $table ) {
		$list_screen = $table->get_list_screen();

		if ( $this->hide_filters->is_hidden( $list_screen ) || $this->hide_smart_filters->is_hidden( $list_screen ) ) {
			return;
		}

		$check_box = new AC\Form\Element\Checkbox( self::INPUT_NAME );

		$check_box->set_options( [ 1 => __( 'Smart Filtering', 'codepress-admin-columns' ) ] )
		          ->set_value( $this->is_active( $list_screen ) ? 1 : 0 );

		$table->register_screen_option( $check_box );
	}

	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}
	}

}