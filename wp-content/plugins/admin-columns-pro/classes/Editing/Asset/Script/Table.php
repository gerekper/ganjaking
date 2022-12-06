<?php

namespace ACP\Editing\Asset\Script;

use AC;
use AC\Asset\Location;
use AC\Asset\Script;
use ACP;
use ACP\Editing\ApplyFilter;
use ACP\Editing\EditableDataFactory;
use ACP\Editing\Preference;
use WP_List_Table;
use WP_User;

final class Table extends Script {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	/**
	 * @var array
	 */
	private $active_states;

	public function __construct(
		$handle,
		Location $location,
		AC\ListScreen $list_screen,
		EditableDataFactory $editable_data_factory,
		Preference\EditState $edit_state,
		array $active_states
	) {
		parent::__construct( $handle, $location, [ 'jquery', 'ac-table' ] );

		$this->list_screen = $list_screen;
		$this->editable_data_factory = $editable_data_factory;
		$this->edit_state = $edit_state;
		$this->active_states = $active_states;
	}

	public function register() {
		parent::register();

		// Allow JS to access the column data for this list screen on the edit page
		wp_localize_script( $this->get_handle(), 'ACP_Editing_Columns', $this->editable_data_factory->create() );

		wp_localize_script( $this->get_handle(), 'ACP_Editing', [
			'inline_edit' => [
				'active'       => $this->active_states['inline_edit'],
				'toggle_state' => $this->edit_state->is_active( $this->list_screen->get_key() ),
				'persistent'   => $this->is_persistent_editing(),
				'version'      => apply_filters( 'acp/editing/inline/deprecated_style', false ) ? 'v1' : 'v2',
			],
			'bulk_edit'   => [
				'active'                     => $this->active_states['bulk_edit'],
				'updated_rows_per_iteration' => $this->get_updated_rows_per_iteration(),
				'total_items'                => $this->get_total_items() ?: 0,
				'total_items_formatted'      => number_format_i18n( $this->get_total_items() ?: 0 ),
				'show_confirmation'          => $this->show_bulk_edit_confirmation(),
			],
			'bulk_delete' => [
				'active'                    => $this->active_states['bulk_delete'],
				'delete_rows_per_iteration' => $this->get_deleted_rows_per_iteration(),
				'component'                 => $this->get_bulk_delete_component(),
				'reassign_user_id'          => $this->get_reassign_user()->ID,
				'reassign_user_name'        => $this->get_reassign_user_name(),
			],
			'i18n'        => [
				'select_author'  => __( 'Select author', 'codepress-admin-columns' ),
				'edit'           => __( 'Edit' ),
				'redo'           => __( 'Redo', 'codepress-admin-columns' ),
				'undo'           => __( 'Undo', 'codepress-admin-columns' ),
				'date'           => __( 'Date' ),
				'delete'         => __( 'Delete', 'codepress-admin-columns' ),
				'restore'        => __( 'Restore', 'codepress-admin-columns' ),
				'download'       => __( 'Download', 'codepress-admin-columns' ),
				'errors'         => [
					'field_required' => __( 'This field is required.', 'codepress-admin-columns' ),
					'invalid_float'  => __( 'Please enter a valid float value.', 'codepress-admin-columns' ),
					'invalid_floats' => __( 'Please enter valid float values.', 'codepress-admin-columns' ),
					'unknown'        => __( 'Something went wrong.', 'codepress-admin-columns' ),
				],
				'inline_edit'    => __( 'Inline Edit', 'codepress-admin-columns' ),
				'media'          => __( 'Media', 'codepress-admin-columns' ),
				'image'          => __( 'Image', 'codepress-admin-columns' ),
				'audio'          => __( 'Audio', 'codepress-admin-columns' ),
				'time'           => __( 'Time', 'codepress-admin-columns' ),
				'update'         => __( 'Update', 'codepress-admin-columns' ),
				'cancel'         => __( 'Cancel', 'codepress-admin-columns' ),
				'done'           => __( 'Done', 'codepress-admin-columns' ),
				'replace_with'   => __( 'Replace with', 'codepress-admin-columns' ),
				'add'            => __( 'Add', 'codepress-admin-columns' ),
				'remove'         => __( 'Remove', 'codepress-admin-columns' ),
				'operators'      => [
					'subtract' => __( 'Subtract', 'codepress-admin-columns' ),
					'add'      => __( 'Add', 'codepress-admin-columns' ),
					'remove'   => __( 'Remove', 'codepress-admin-columns' ),
					'multiply' => __( 'Multiply', 'codepress-admin-columns' ),
					'divide'   => __( 'Divide', 'codepress-admin-columns' ),
				],
				'bulk_selection' => [
					'affected_items'     => _x( 'This will affect {0}', 'bulk-delete', 'codepress-admin-columns' ),
					'all_selected_items' => __( 'all selected items', 'codepress-admin-columns' ),
					'all_items'          => __( 'all items', 'codepress-admin-columns' ),
					'all_items_ucfirst'  => ucfirst( __( 'all items', 'codepress-admin-columns' ) ),
					'items'              => __( '{0} items', 'codepress-admin-columns' ),
					'item'               => __( '1 item', 'codepress-admin-columns' ),
					'select_all'         => __( 'Select all {0} items', 'codepress-admin-columns' ),
					'selected'           => sprintf( __( '{0} selected for %s.', 'codepress-admin-columns' ), ac_helper()->string->enumeration_list( $this->get_bulk_selection_labels() ) ),
				],
				'bulk_delete'    => [
					'assignment_to'                     => _x( 'Assign all content to:', 'bulk-delete', 'codepress-admin-columns' ),
					'bulk_delete'                       => __( 'Bulk Delete', 'codepress-admin-columns' ),
					'bulk_restore'                      => __( 'Bulk Restore', 'codepress-admin-columns' ),
					'confirmation'                      => __( 'Do you want to bulk delete all selected items?', 'codepress-admin-columns' ),
					'confirmation_restore'              => __( 'Do you want to bulk restore all selected items?', 'codepress-admin-columns' ),
					'current_user'                      => _x( 'current user', 'bulk-delete', 'codepress-admin-columns' ),
					'delete_all_content'                => _x( 'Delete all content', 'bulk-delete', 'codepress-admin-columns' ),
					'delete_items_permanently'          => _x( 'Delete the items permanently', 'bulk-delete', 'codepress-admin-columns' ),
					'delete_selected_items_permanently' => _x( 'The selected items will be deleted permanently.', 'bulk-delete', 'codepress-admin-columns' ),
					'restore'                           => _x( 'Restore', 'bulk-delete', 'codepress-admin-columns' ),
					'restore_items'                     => _x( 'Restore the items', 'bulk-delete', 'codepress-admin-columns' ),
					'finished'                          => __( 'Processed {0} items', 'codepress-admin-columns' ),
					'move_to_trash'                     => _x( 'Move the items to trash', 'bulk-delete', 'codepress-admin-columns' ),
					'user_assignment_description'       => _x( 'What should be done with content owned by these users?', 'bulk-delete', 'codepress-admin-columns' ),
					'yes_delete'                        => __( 'Yes, Delete', 'codepress-admin-columns' ),
					'yes_restore'                       => __( 'Yes, Restore', 'codepress-admin-columns' ),
				],
				'bulk_edit'      => [
					'bulk_edit'     => __( 'Bulk Edit', 'codepress-admin-columns' ),
					'done_deselect' => __( 'Done & Deselect All', 'codepress-admin-columns' ),
					'form'          => [
						'heads_up'      => __( 'This will update {0} items.', 'codepress-admin-columns' ),
						'clear_values'  => __( 'You are about to clear {0} items.', 'codepress-admin-columns' ),
						'update_values' => __( 'You are about to update {0} items.', 'codepress-admin-columns' ),
						'are_you_sure'  => __( 'Are you sure?', 'codepress-admin-columns' ),
						'yes_update'    => __( 'Yes, Update', 'codepress-admin-columns' ),
					],
					'feedback'      => [
						'show_items'         => __( 'Show items', 'codepress-admin-columns' ),
						'hide_items'         => __( 'Hide items', 'codepress-admin-columns' ),
						'finished'           => __( 'Processed {0} items', 'codepress-admin-columns' ),
						'updating'           => __( 'Updating items.', 'codepress-admin-columns' ),
						'processed'          => __( 'Processed {0} of {1} items.', 'codepress-admin-columns' ),
						'failure'            => __( 'Updating failed. Please try again.', 'codepress-admin-columns' ),
						'error'              => _x( 'We have found {0} while processing.', 'bulk edit errors', 'codepress-admin-columns' ),
						'not_editable_found' => __( 'These items are not editable and could not be modified:', 'codepress-admin-columns' ),
					],
				],
			],
		] );
	}

	private function get_bulk_selection_labels(): array {
		$selection = [];

		$labels = [
			'bulk_edit'   => [
				'label' => __( 'bulk edit', 'codepress-admin-columns' ),
				'tip'   => __( 'Bulk edit items by clicking the Bulk Edit button below the column labels.', 'codepress-admin-columns' ),
			],
			'bulk_delete' => [
				'label' => __( 'bulk delete', 'codepress-admin-columns' ),
				'tip'   => __( 'Bulk delete items by clicking the trash icon in the top left corner.', 'codepress-admin-columns' ),
			],
			'export'      => [
				'label' => __( 'export', 'codepress-admin-columns' ),
				'tip'   => __( 'Export items by clicking the Export button.', 'codepress-admin-columns' ),
			],
		];

		foreach ( $labels as $key => $item ) {
			if ( $this->active_states[ $key ] ) {
				$selection[] = sprintf( '<span data-ac-tip="%s">%s</span>', esc_attr( $item['tip'] ), esc_html( $item['label'] ) );
			}
		}

		return $selection;
	}

	private function get_reassign_user_name(): string {
		$user = $this->get_reassign_user();
		$name = ac_helper()->user->get_display_name( $user );

		if ( get_current_user_id() === $user->ID ) {
			$name = sprintf( '%s (%s)', $name, __( 'current user', 'codepress-admin-columns' ) );
		}

		return $name;
	}

	private function get_reassign_user(): WP_User {
		$user_id = ( new ApplyFilter\ReassignUser() )->apply_filters( get_current_user_id() );

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return wp_get_current_user();
		}

		return $user;
	}

	/**
	 * @return false|int
	 */
	private function get_total_items() {
		global $wp_list_table;

		return $wp_list_table instanceof WP_List_Table
			? $wp_list_table->get_pagination_arg( 'total_items' )
			: false;
	}

	private function show_bulk_edit_confirmation(): bool {
		return (bool) apply_filters( 'acp/editing/bulk/show_confirmation', true );
	}

	private function get_bulk_delete_component(): string {
		switch ( true ) {
			case $this->list_screen instanceof ACP\ListScreen\Post:
				if ( 'trash' === get_query_var( 'post_status', null ) ) {
					return 'trash';
				}

				return 'post';
			case $this->list_screen instanceof ACP\ListScreen\User:
				return 'user';
			case $this->list_screen instanceof ACP\ListScreen\Comment:
				$comment_status = isset( $_REQUEST['comment_status'] ) ? wp_unslash( $_REQUEST['comment_status'] ) : '';
				if ( 'trash' === $comment_status ) {
					return 'trash';
				}

				return 'comment';
			default:
				return '';
		}
	}

	private function is_persistent_editing() {
		return (bool) apply_filters( 'acp/editing/persistent', false, $this->list_screen );
	}

	private function get_updated_rows_per_iteration() {
		return (int) apply_filters( 'acp/editing/bulk/updated_rows_per_iteration', 250, $this->list_screen );
	}

	private function get_deleted_rows_per_iteration() {
		return (int) apply_filters( 'acp/delete/bulk/deleted_rows_per_iteration', 250, $this->list_screen );
	}

}