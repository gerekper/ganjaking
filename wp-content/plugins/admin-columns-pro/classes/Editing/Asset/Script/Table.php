<?php

namespace ACP\Editing\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\Column;
use AC\ListScreen;
use ACP\Editing\Editable;
use ACP\Editing\HideOnScreen;
use ACP\Editing\Preference;
use WP_List_Table;

final class Table extends Script {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var Column[]
	 */
	private $editable_columns;

	/**
	 * @var Preference\EditState
	 */
	private $edit_state;

	public function __construct(
		$handle,
		Location $location,
		ListScreen $list_screen,
		array $editable_columns,
		Preference\EditState $edit_state
	) {
		parent::__construct( $handle, $location, [ 'jquery' ] );

		$this->list_screen = $list_screen;
		$this->editable_columns = $editable_columns;
		$this->edit_state = $edit_state;
	}

	public function register() {
		/** @var WP_List_Table $wp_list_table */
		global $wp_list_table;

		parent::register();

		$total_items = $wp_list_table instanceof WP_List_Table
			? $wp_list_table->get_pagination_arg( 'total_items' )
			: false;

		// Allow JS to access the column data for this list screen on the edit page
		wp_localize_script( $this->get_handle(), 'ACP_Editing_Columns', $this->get_editable_data() );
		wp_localize_script( $this->get_handle(), 'ACP_Editing', [
			'inline_edit' => [
				'persistent' => $this->is_persistent_editing(),
				'active'     => $this->edit_state->is_active( $this->list_screen->get_key() ),
			],
			'bulk_edit'   => [
				'updated_rows_per_iteration' => $this->get_updated_rows_per_iteration(),
				'total_items'                => $total_items,
				'show_confirmation'          => apply_filters( 'acp/editing/bulk/show_confirmation', true ),
			],
			'i18n'        => [
				'select_author' => __( 'Select author', 'codepress-admin-columns' ),
				'edit'          => __( 'Edit' ),
				'redo'          => __( 'Redo', 'codepress-admin-columns' ),
				'undo'          => __( 'Undo', 'codepress-admin-columns' ),
				'date'          => __( 'Date' ),
				'delete'        => __( 'Delete', 'codepress-admin-columns' ),
				'download'      => __( 'Download', 'codepress-admin-columns' ),
				'errors'        => [
					'field_required' => __( 'This field is required.', 'codepress-admin-columns' ),
					'invalid_float'  => __( 'Please enter a valid float value.', 'codepress-admin-columns' ),
					'invalid_floats' => __( 'Please enter valid float values.', 'codepress-admin-columns' ),
					'unknown'        => __( 'Something went wrong.', 'codepress-admin-columns' ),
				],
				'inline_edit'   => __( 'Inline Edit', 'codepress-admin-columns' ),
				'media'         => __( 'Media', 'codepress-admin-columns' ),
				'image'         => __( 'Image', 'codepress-admin-columns' ),
				'audio'         => __( 'Audio', 'codepress-admin-columns' ),
				'time'          => __( 'Time', 'codepress-admin-columns' ),
				'update'        => __( 'Update', 'codepress-admin-columns' ),
				'cancel'        => __( 'Cancel', 'codepress-admin-columns' ),
				'done'          => __( 'Done', 'codepress-admin-columns' ),
				'replace_with'  => __( 'Replace with', 'codepress-admin-columns' ),
				'add'           => __( 'Add', 'codepress-admin-columns' ),
				'remove'        => __( 'Remove', 'codepress-admin-columns' ),
				'bulk_edit'     => [
					'selecting' => [
						'select_all'    => __( 'Select all {0} entries', 'codepress-admin-columns' ),
						'selected'      => __( '<strong>{0} entries</strong> selected for Bulk Edit.', 'codepress-admin-columns' ),
						'done_deselect' => __( 'Done & Deselect All', 'codepress-admin-columns' ),
					],
					'form'      => [
						'heads_up'      => __( 'This will update {0} entries.', 'codepress-admin-columns' ),
						'clear_values'  => __( 'You are about to clear {0} entries.', 'codepress-admin-columns' ),
						'update_values' => __( 'You are about to update {0} entries.', 'codepress-admin-columns' ),
						'are_you_sure'  => __( 'Are you sure?', 'codepress-admin-columns' ),
						'yes_update'    => __( 'Yes, Update', 'codepress-admin-columns' ),
					],
					'feedback'  => [
						'finished'  => __( 'Processed {0} entries', 'codepress-admin-columns' ),
						'updating'  => __( 'Updating entries.', 'codepress-admin-columns' ),
						'processed' => __( 'Processed {0} of {1} entries.', 'codepress-admin-columns' ),
						'failure'   => __( 'Updating failed. Please try again.', 'codepress-admin-columns' ),
						'error'     => __( 'We have found <strong>{0} errors</strong> while processing.', 'codepress-admin-columns' ),
					],
				],
			],
		] );
	}

	/**
	 * @return array
	 */
	private function get_editable_data() {
		$editable_data = [];

		foreach ( $this->editable_columns as $column ) {
			if ( ! $column instanceof Editable ) {
				continue;
			}

			$data = $column->editing()->get_view_settings();

			$data = apply_filters( 'acp/editing/view_settings', $data, $column );
			$data = apply_filters( 'acp/editing/view_settings/' . $column->get_type(), $data, $column );

			if ( false === $data ) {
				continue;
			}

			if ( isset( $data['options'] ) ) {
				$data['options'] = $this->format_js( $data['options'] );
			}

			$is_bulk_editable = $column->editing()->is_bulk_edit_active();

			if ( ( new HideOnScreen\BulkEdit() )->is_hidden( $this->list_screen ) ) {
				$is_bulk_editable = false;
			}

			if ( $is_bulk_editable ) {
				$is_bulk_editable = (bool) apply_filters( 'acp/editing/bulk/active', $is_bulk_editable, $this->list_screen );
			}

			$is_inline_editable = $column->editing()->is_active();

			if ( ( new HideOnScreen\InlineEdit() )->is_hidden( $this->list_screen ) ) {
				$is_inline_editable = false;
			}

			$editable_data[ $column->get_name() ] = [
				'type'        => $column->get_type(),
				'editable'    => $data,
				'inline_edit' => $is_inline_editable,
				'bulk_edit'   => $is_bulk_editable,
			];
		}

		return $editable_data;
	}

	/**
	 * @param $list
	 *
	 * @return array
	 */
	private function format_js( $list ) {
		$options = [];

		if ( $list ) {
			foreach ( $list as $index => $option ) {
				if ( is_array( $option ) && isset( $option['options'] ) ) {
					$option['options'] = $this->format_js( $option['options'] );
					$options[] = $option;
				} else if ( is_scalar( $option ) ) {
					$options[] = [
						'value' => $index,
						'label' => html_entity_decode( $option ),
					];
				}
			}
		}

		return $options;
	}

	private function is_persistent_editing() {
		return (bool) apply_filters( 'acp/editing/persistent', false, $this->list_screen );
	}

	private function get_updated_rows_per_iteration() {
		return apply_filters( 'acp/editing/bulk/updated_rows_per_iteration', 250, $this->list_screen );
	}

}