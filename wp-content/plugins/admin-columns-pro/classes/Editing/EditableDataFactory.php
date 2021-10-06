<?php

namespace ACP\Editing;

use AC;
use AC\Column;

/**
 * Get all data settings needed to load editing for the WordPress list table
 */
class EditableDataFactory {

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return array
	 */
	public function create( AC\ListScreen $list_screen ) {
		$is_table_inline_editable = $this->is_list_screen_inline_editable( $list_screen );
		$is_table_bulk_editable = $this->is_list_screen_bulk_editable( $list_screen );

		if ( ! $is_table_inline_editable && ! $is_table_bulk_editable ) {
			return [];
		}

		$editable_data = [];
		foreach ( $list_screen->get_columns() as $column ) {
			$service = ServiceFactory::create( $column );

			if ( ! $service instanceof Service ) {
				continue;
			}

			$inline_data = $is_table_inline_editable && $this->is_inline_edit_active( $column )
				? $this->create_data_by_service( $service, $column, Service::CONTEXT_SINGLE )
				: null;

			$bulk_data = $is_table_bulk_editable && $this->is_bulk_edit_active( $column )
				? $this->create_data_by_service( $service, $column, Service::CONTEXT_BULK )
				: null;

			if ( ! $inline_data && ! $bulk_data ) {
				continue;
			}

			$editable_data[ $column->get_name() ] = [
				'type'        => $column->get_type(),
				'inline_edit' => $inline_data,
				'bulk_edit'   => $bulk_data,
			];
		}

		return $editable_data;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function is_list_screen_bulk_editable( AC\ListScreen $list_screen ) {
		$is_enabled = ! ( new HideOnScreen\BulkEdit() )->is_hidden( $list_screen );

		return (bool) apply_filters( 'acp/editing/bulk/active', $is_enabled, $list_screen );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function is_list_screen_inline_editable( AC\ListScreen $list_screen ) {
		return ! ( new HideOnScreen\InlineEdit() )->is_hidden( $list_screen );
	}

	/**
	 * @param Service $service
	 * @param Column  $column
	 * @param string  $context
	 *
	 * @return array|null
	 */
	private function create_data_by_service( Service $service, Column $column, $context ) {
		$view = $service->get_view( $context );

		if ( ! $view ) {
			return null;
		}

		$data = apply_filters( 'acp/editing/view_settings', $view->get_args(), $column );
		$data = apply_filters( 'acp/editing/view_settings/' . $column->get_type(), $data, $column );

		if ( ! is_array( $data ) ) {
			return null;
		}

		if ( isset( $data['options'] ) ) {
			$data['options'] = $this->format_js( $data['options'] );
		}

		return (array) $data;
	}

	/**
	 * @param Column $column
	 *
	 * @return bool
	 */
	private function is_bulk_edit_active( Column $column ) {
		$setting = $column->get_setting( Settings\BulkEditing::NAME );

		$is_active = $setting instanceof Settings\BulkEditing && $setting->is_active();

		return (bool) apply_filters( 'acp/editing/bulk-edit-active', $is_active, $column );
	}

	/**
	 * @param Column $column
	 *
	 * @return bool
	 */
	private function is_inline_edit_active( Column $column ) {
		$setting = $column->get_setting( Settings::NAME );

		return $setting instanceof Settings && $setting->is_active();
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

}