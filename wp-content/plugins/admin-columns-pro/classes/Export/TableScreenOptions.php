<?php

namespace ACP\Export;

use AC;
use AC\Asset\Location;
use AC\Preferences;
use AC\Registrable;
use ACP;
use ACP\Export\HideOnScreen;

class TableScreenOptions implements Registrable {

	/**
	 * @var Location
	 */
	protected $location;

	public function __construct( Location $location ) {
		$this->location = $location;
	}

	public function register() {
		add_action( 'ac/table', [ $this, 'register_screen_option' ] );
		add_filter( 'ac/table/body_class', [ $this, 'add_hide_export_button_class' ], 10, 2 );
		add_action( 'wp_ajax_acp_export_show_export_button', [ $this, 'update_table_option_show_export_button' ] );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( AC\Table\Screen $table ) {
		$list_screen = $table->get_list_screen();

		if ( ! ( $list_screen instanceof ACP\Export\ListScreen ) ) {
			return;
		}

		if ( ! apply_filters( 'acp/export/is_active', true, $list_screen ) ) {
			return;
		}

		if ( ( new HideOnScreen\Export() )->is_hidden( $list_screen ) ) {
			return;
		}

		$exclude_columns = get_hidden_columns( $list_screen->get_screen_id() );

		$columns = ( new ExportableColumnFactory( $list_screen ) )->create( $exclude_columns );

		if ( empty( $columns ) ) {
			return;
		}

		$check_box = new AC\Form\Element\Checkbox( 'acp_export_show_export_button' );
		$check_box->set_options( [ 1 => __( 'Export Button', 'codepress-admin-columns' ) ] )
		          ->set_value( $this->get_export_button_setting( $list_screen ) === 1 ? 1 : 0 );

		$table->register_screen_option( $check_box );

		$button = new AC\Table\Button( 'export' );
		$button->set_label( __( 'Export to CSV', 'codepress-admin-columns' ) )
		       ->set_text( __( 'Export', 'codepress-admin-columns' ) )
		       ->set_url( '#' );

		$table->register_button( $button );
	}

	/**
	 * @return Preferences\Site
	 */
	public function preferences() {
		return new Preferences\Site( 'show_export_button' );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function get_export_button_setting( $list_screen ) {
		$setting = $this->preferences()->get( $list_screen->get_key() );

		// No setting found, enable export
		if ( $setting === null ) {
			$setting = 1;
		}

		return $setting;
	}

	/**
	 * @param string $list_screen_key
	 * @param int    $value
	 */
	private function set_export_button_setting( $list_screen_key, $value ) {
		$this->preferences()->set( $list_screen_key, (int) $value );
	}

	public function update_table_option_show_export_button() {
		check_ajax_referer( 'ac-ajax' );

		$this->set_export_button_setting( filter_input( INPUT_POST, 'list_screen' ), ( 'true' === filter_input( INPUT_POST, 'value' ) ) ? 1 : 0 );

		exit;
	}

	/**
	 * @param string          $classes
	 * @param AC\Table\Screen $table
	 *
	 * @return string
	 */
	public function add_hide_export_button_class( $classes, $table ) {
		if ( ! $this->get_export_button_setting( $table->get_list_screen() ) ) {
			$classes .= ' ac-hide-export-button';
		}

		return $classes;
	}

}