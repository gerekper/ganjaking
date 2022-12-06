<?php

namespace ACA\GravityForms\TableScreen;

use AC;
use ACA\GravityForms\Column;
use ACA\GravityForms\Editing;
use ACA\GravityForms\HideOnScreen\EntryFilters;
use ACA\GravityForms\HideOnScreen\WordPressNotifications;
use ACA\GravityForms\ListScreen;
use GF_Field;
use GFAPI;
use GFFormsModel;

class Entry implements AC\Registerable {

	public function register() {
		add_filter( 'gform_entry_list_columns', [ $this, 'remove_selector_column' ], 11, 2 );
		add_filter( 'acp/editing/result', [ $this, 'get_editing_ajax_value' ], 10, 3 );
		add_action( 'ac/table/list_screen', [ $this, 'create_default_list_screen' ], 9 );
		add_action( 'ac/table/list_screen', [ $this, 'store_active_gf_columns' ], 10 );
		add_action( 'ac/table/list_screen', [ $this, 'register_table_rows' ] );
		add_action( 'ac/admin_head', [ $this, 'hide_entry_filters' ] );
		add_action( 'ac/admin_head', [ $this, 'hide_wordpress_notifications' ] );
	}

	public function hide_entry_filters( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen\Entry || ! ( new EntryFilters )->is_hidden( $list_screen ) ) {
			return;
		}
		?>
		<style>
			#entry_search_container {
				display: none !important;
			}
		</style>
		<?php
	}

	/**
	 * @return bool
	 */
	private function has_stored_default_columns( AC\ListScreen $list_screen ) {
		return ! empty( ( new AC\DefaultColumnsRepository() )->get( $list_screen->get_key() ) );
	}

	public function hide_wordpress_notifications( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen\Entry || ! ( new WordPressNotifications() )->is_hidden( $list_screen ) ) {
			return;
		}
		?>
		<style>
			#gf-wordpress-notices {
				display: none !important;
			}
		</style>
		<?php
	}

	public function register_table_rows( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen\Entry ) {
			return;
		}

		$table_rows = new Editing\TableRows\Entry( new AC\Request(), $list_screen );

		if ( $table_rows->is_request() ) {
			$table_rows->register();
		}
	}

	public function get_editing_ajax_value( $result, $id, $column ) {
		if ( $column instanceof Column\Entry ) {
			$result['cell_html'] = $column->get_entry_value( $id );
		}

		return $result;
	}

	public function create_default_list_screen( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen\Entry || ! $this->has_stored_default_columns( $list_screen ) ) {
			return;
		}

		if ( ! apply_filters( 'acp/gravityforms/create_default_set', true ) ) {
			return;
		}

		if ( $list_screen->has_id() && AC()->get_storage()->exists( $list_screen->get_id() ) ) {
			return;
		}

		$default_columns = GFFormsModel::get_grid_columns( $list_screen->get_form_id() );
		$settings = [
			'is_starred' => [
				'label' => '<span class="dashicons dashicons-star-filled"></span>',
				'type'  => 'is_starred',
			],
		];

		foreach ( $default_columns as $field_id => $info ) {
			$key = 'field_id-' . $field_id;
			$settings[ $key ] = [
				'label' => $info['label'],
				'type'  => $key,
			];
		}

		$list_screen->set_settings( $settings );
		$list_screen->set_title( 'Default' );
		$list_screen->set_layout_id( AC\Type\ListScreenId::generate()->get_id() );

		AC()->get_storage()->save( $list_screen );
	}

	public function store_active_gf_columns( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen\Entry || ! $this->has_stored_default_columns( $list_screen ) ) {
			return;
		}

		$grid_columns = array_keys( GFFormsModel::get_grid_columns( $list_screen->get_form_id() ) );

		foreach ( $grid_columns as $key => $column ) {
			$field = GFAPI::get_field( $list_screen->get_form_id(), $column );
			if ( $field && in_array( $field['type'], $this->get_unsupported_field_types(), false ) ) {
				unset( $grid_columns[ $key ] );
			}
		}

		$current_columns = array_merge( $grid_columns, $this->get_field_ids( GFAPI::get_form( $list_screen->get_form_id() ) ), $this->get_default_table_column_names() );
		$current_columns = array_unique( $current_columns );

		if ( md5( serialize( GFFormsModel::get_grid_column_meta( $list_screen->get_form_id() ) ) ) !== md5( serialize( $current_columns ) ) ) {
			GFFormsModel::update_grid_column_meta( $list_screen->get_form_id(), $current_columns );
		}
	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function remove_selector_column( $columns ) {
		unset( $columns['column_selector'] );

		return $columns;
	}

	/**
	 * @return array
	 */
	private function get_default_table_column_names() {
		return [
			'id',
			'date_created',
			'ip',
			'source_url',
			'payment_status',
			'transaction_id',
			'payment_amount',
			'payment_date',
			'created_by',
		];
	}

	private function get_unsupported_field_types() {
		return [ 'section', 'html', 'page' ];
	}

	/**
	 * @param array $form
	 *
	 * @return array
	 */
	private function get_field_ids( $form ) {
		$field_ids = [];

		/**
		 * @var GF_Field $field
		 */
		foreach ( $form['fields'] as $field ) {
			if ( in_array( $field['type'], $this->get_unsupported_field_types(), false ) ) {
				continue;
			}
			$inputs = $field->get_entry_inputs();
			$field_ids[] = $field->id;

			if ( is_array( $inputs ) ) {
				foreach ( $inputs as $input ) {
					$field_ids[] = $input['id'];
				}
			}
		}

		return $field_ids;
	}

}