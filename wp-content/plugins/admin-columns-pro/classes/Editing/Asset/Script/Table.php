<?php

namespace ACP\Editing\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\ListScreen;
use ACP\Editing\Preference;
use WP_List_Table;

final class Table extends Script {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var array
	 */
	private $editable_data;

	/**
	 * @var Preference\EditState
	 */
	private $edit_state;

	public function __construct(
		$handle,
		Location $location,
		ListScreen $list_screen,
		array $editable_data,
		Preference\EditState $edit_state
	) {
		parent::__construct( $handle, $location, [ 'jquery', 'ac-table' ] );

		$this->list_screen = $list_screen;
		$this->editable_data = $editable_data;
		$this->edit_state = $edit_state;
	}

	public function register() {
		parent::register();

		global $wp_list_table;

		$total_items = $wp_list_table instanceof WP_List_Table
			? $wp_list_table->get_pagination_arg( 'total_items' )
			: false;

		// Allow JS to access the column data for this list screen on the edit page
		wp_localize_script( $this->get_handle(), 'ACP_Editing_Columns', $this->editable_data );
		wp_localize_script( $this->get_handle(), 'ACP_Editing', [
			'inline_edit' => [
				'persistent' => $this->is_persistent_editing(),
				'active'     => $this->edit_state->is_active( $this->list_screen->get_key() ),
				'version'    => apply_filters( 'acp/editing/inline/deprecated_style', false ) ? 'v1' : 'v2',
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
				'subtract'      => __( 'Subtract', 'codepress-admin-columns' ),
				'done'          => __( 'Done', 'codepress-admin-columns' ),
				'replace_with'  => __( 'Replace with', 'codepress-admin-columns' ),
				'add'           => __( 'Add', 'codepress-admin-columns' ),
				'remove'        => __( 'Remove', 'codepress-admin-columns' ),
				'bulk_edit'     => [
					'bulk_edit' => __( 'Bulk Edit', 'codepress-admin-columns' ),
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

	private function is_persistent_editing() {
		return (bool) apply_filters( 'acp/editing/persistent', false, $this->list_screen );
	}

	private function get_updated_rows_per_iteration() {
		return apply_filters( 'acp/editing/bulk/updated_rows_per_iteration', 250, $this->list_screen );
	}

}