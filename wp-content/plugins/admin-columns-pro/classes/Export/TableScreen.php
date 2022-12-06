<?php

namespace ACP\Export;

use AC;
use AC\Asset\Location;
use AC\Preferences;
use AC\Registerable;
use AC\Type\ListScreenId;
use ACP;
use ACP\Export\Asset\Script;

class TableScreen implements Registerable {

	/**
	 * @var Location
	 */
	protected $location;

	public function __construct( Location $location ) {
		$this->location = $location;
	}

	public function register() {
		add_action( 'ac/table/list_screen', [ $this, 'load_list_screen' ] );
		add_filter( 'ac/table/body_class', [ $this, 'add_hide_export_button_class' ], 10, 2 );
		add_action( 'wp_ajax_acp_export_show_export_button', [ $this, 'update_table_option_show_export_button' ] );
	}

	/**
	 * Load a list screen and potentially attach the proper exporting information to it
	 *
	 * @param AC\ListScreen $list_screen List screen for current table screen
	 */
	public function load_list_screen( AC\ListScreen $list_screen ): void {
		if ( ! $this->is_exportable( $list_screen ) ) {
			return;
		}

		$list_screen->export()->attach();

		add_action( 'ac/table', [ $this, 'register_screen_option' ] );
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
	}

	private function is_exportable( AC\ListScreen $list_screen ): bool {
		return $list_screen instanceof ListScreen && $list_screen->has_id() && $list_screen->export()->is_active();
	}

	private function get_user_preference_column_names( ListScreenId $id ): array {
		$user_preference = new UserPreference\ExportedColumns();

		return $user_preference->exists( $id )
			? $user_preference->get( $id )
			: [];
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return AC\Column[]
	 */
	private function get_exportable_column_labels( AC\ListScreen $list_screen ): array {
		if ( ! $list_screen instanceof ListScreen ) {
			return [];
		}

		$columns = [];

		$hidden_columns = get_hidden_columns( $list_screen->get_screen_id() );
		$user_preference_columns = $this->get_user_preference_column_names( $list_screen->get_id() );

		foreach ( $list_screen->export()->get_exportable_columns() as $column ) {
			$column_name = $column->get_name();

			if ( $user_preference_columns ) {
				$default_state = in_array( $column_name, $user_preference_columns, true ) ? 'on' : 'off';
			} else {
				$default_state = in_array( $column_name, $hidden_columns, true ) ? 'off' : 'on';
			}

			$columns[] = [
				'name'          => $column_name,
				'label'         => $this->get_sanitized_label( $column ),
				'default_state' => $default_state,
			];
		}

		return $columns;
	}

	private function get_sanitized_label( AC\Column $column ): string {
		return $this->sanitize_column_label( $column->get_custom_label() ) ?: sprintf( '%s (%s)', $column->get_name(), $column->get_label() );
	}

	/**
	 * Allows plain text and dashicons
	 */
	private function sanitize_column_label( string $label ): string {
		if ( false === strpos( $label, 'dashicons' ) ) {
			$label = strip_tags( $label );
		}

		return trim( $label );
	}

	public function scripts( AC\ListScreen $list_screen ) {
		$style = new AC\Asset\Style(
			'acp-export-listscreen',
			$this->location->with_suffix( 'assets/export/css/listscreen.css' )
		);
		$style->enqueue();

		$script = new Script\Table(
			'acp-export-listscreen',
			$this->location->with_suffix( 'assets/export/js/listscreen.js' ),
			$list_screen->export(),
			$this->get_exportable_column_labels( $list_screen )
		);
		$script->enqueue();
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( AC\Table\Screen $table ) {
		$list_screen = $table->get_list_screen();

		$check_box = new AC\Form\Element\Checkbox( 'acp_export_show_export_button' );
		$check_box->set_options( [ 1 => __( 'Export Button', 'codepress-admin-columns' ) ] )
		          ->set_value( $this->get_export_button_setting( $list_screen ) ? 1 : 0 );

		$table->register_screen_option( $check_box );

		$button = new AC\Table\Button( 'export' );
		$button->set_label( __( 'Export to CSV', 'codepress-admin-columns' ) )
		       ->set_text( __( 'Export', 'codepress-admin-columns' ) )
		       ->set_url( '#' );

		$table->register_button( $button );
	}

	public function preferences() {
		return new Preferences\Site( 'show_export_button' );
	}

	private function get_export_button_setting( AC\ListScreen $list_screen ): bool {
		$setting = $this->preferences()->get( $list_screen->get_key() );

		// No setting found, enable export
		if ( $setting === null ) {
			$setting = 1;
		}

		return 1 === $setting;
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