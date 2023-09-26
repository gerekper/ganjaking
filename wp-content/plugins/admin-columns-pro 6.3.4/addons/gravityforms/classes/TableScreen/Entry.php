<?php

namespace ACA\GravityForms\TableScreen;

use AC;
use AC\DefaultColumnsRepository;
use AC\ListScreenFactory;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;
use ACA\GravityForms\Column;
use ACA\GravityForms\Editing;
use ACA\GravityForms\HideOnScreen\EntryFilters;
use ACA\GravityForms\HideOnScreen\WordPressNotifications;
use ACA\GravityForms\ListScreen;
use GF_Field;
use GFAPI;
use GFFormsModel;

class Entry implements AC\Registerable {

	private $list_screen_factory;

	private $storage;

	private $default_columns_repository;

	public function __construct(
		ListScreenFactory $list_screen_factory,
		Storage $storage,
		DefaultColumnsRepository $default_columns_repository
	) {
		$this->list_screen_factory = $list_screen_factory;
		$this->storage = $storage;
		$this->default_columns_repository = $default_columns_repository;
	}

	public function register(): void
    {
		add_filter( 'gform_entry_list_columns', [ $this, 'remove_selector_column' ], 11, 2 );
		add_filter( 'acp/editing/result', [ $this, 'get_editing_ajax_value' ], 10, 3 );
		add_action( 'ac/table/list_screen', [ $this, 'create_default_list_screen' ], 9 );
		add_action( 'ac/table/list_screen', [ $this, 'store_active_gf_columns' ] );
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

	private function has_stored_default_columns( string $list_key ): bool {
		return ! empty( $this->default_columns_repository->get( $list_key ) );
	}

	public function hide_wordpress_notifications( AC\ListScreen $list_screen ): void {
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

	public function create_default_list_screen( AC\ListScreen $list_screen ): void {
		if ( ! $list_screen instanceof ListScreen\Entry ) {
			return;
		}

		if ( ! apply_filters( 'acp/gravityforms/create_default_set', true ) ) {
			return;
		}

		$list_key = $list_screen->get_key();

		if ( ! $this->has_stored_default_columns( $list_key ) ) {
			return;
		}

		if ( $list_screen->has_id() && $this->storage->exists( $list_screen->get_id() ) ) {
			return;
		}

		if ( ! $this->list_screen_factory->can_create( $list_key ) ) {
			return;
		}

		$columns = [
			'is_starred' => [
				'label' => '<span class="dashicons dashicons-star-filled"></span>',
				'type'  => 'is_starred',
			],
		];

		foreach ( GFFormsModel::get_grid_columns( $list_screen->get_form_id() ) as $field_id => $data ) {
			$key = 'field_id-' . $field_id;
			$columns[ $key ] = [
				'label' => $data['label'],
				'type'  => $key,
			];
		}

		$settings = [
			'list_id' => ListScreenId::generate()->get_id(),
			'title'   => __( 'Default', 'codepress-admin-columns' ),
			'columns' => $columns,
		];

		$this->storage->save( $this->list_screen_factory->create( $list_key, $settings ) );
	}

	public function store_active_gf_columns( AC\ListScreen $list_screen ): void {
		if ( ! $list_screen instanceof ListScreen\Entry ) {
			return;
		}

		$form_id = $list_screen->get_form_id();

		if ( ! $form_id ) {
			return;
		}

		$list_key = $list_screen->get_key();

		if ( ! $this->has_stored_default_columns( $list_key ) ) {
			return;
		}

		$grid_columns = array_keys( GFFormsModel::get_grid_columns( $form_id ) );

		foreach ( $grid_columns as $key => $column ) {
			$field = GFAPI::get_field( $form_id, $column );

			if ( $field && in_array( $field['type'], $this->get_unsupported_field_types(), false ) ) {
				unset( $grid_columns[ $key ] );
			}
		}

		$forms = GFAPI::get_form( $form_id );
		$form_fields = $forms['fields'] ?? [];

		if ( ! $form_fields ) {
			return;
		}

		$current_columns = array_merge( $grid_columns, $this->get_field_ids( $form_fields ), $this->get_default_table_column_names() );
		$current_columns = array_unique( $current_columns );

		if ( md5( serialize( GFFormsModel::get_grid_column_meta( $form_id ) ) ) !== md5( serialize( $current_columns ) ) ) {
			GFFormsModel::update_grid_column_meta( $form_id, $current_columns );
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

	private function get_field_ids( array $form_fields ): array {
		$field_ids = [];

		foreach ( $form_fields as $field ) {
			if ( ! $field instanceof GF_Field ) {
				continue;
			}

			if ( in_array( $field->type, $this->get_unsupported_field_types(), false ) ) {
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