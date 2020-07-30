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
		$editable_data = [];

		foreach ( $list_screen->get_columns() as $column ) {
			if ( ! $column instanceof Editable ) {
				continue;
			}

			$model = $column->editing();

			if ( $model instanceof Model\Disabled ) {
				continue;
			}

			$is_inline_editable = $this->is_list_screen_inline_editable( $list_screen ) && $this->is_column_inline_editable( $column );
			$is_bulk_editable = $this->is_list_screen_bulk_editable( $list_screen ) && $this->is_column_bulk_editable( $column );

			if ( ! $is_inline_editable && ! $is_bulk_editable ) {
				continue;
			}

			$data = $this->get_editable_settings( $column );

			if ( false === $data ) {
				continue;
			}

			if ( isset( $data['options'] ) ) {
				$data['options'] = $this->format_js( $data['options'] );
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
	 * @param Column $column
	 *
	 * @return bool
	 */
	private function is_column_bulk_editable( Column $column ) {
		$settings = $this->get_editable_settings( $column );

		if ( isset( $settings[ Model::VIEW_BULK_EDITABLE ] ) && false === $settings[ Model::VIEW_BULK_EDITABLE ] ) {
			return false;
		}

		$setting = $column->get_setting( 'bulk_edit' );

		$is_bulk_editable = $setting instanceof Settings\BulkEditing && $setting->is_active();

		return (bool) apply_filters( 'acp/editing/bulk-edit-active', $is_bulk_editable, $column );
	}

	/**
	 * @param Column $column
	 *
	 * @return bool
	 */
	private function is_column_inline_editable( Column $column ) {
		$setting = $column->get_setting( 'edit' );

		return $setting instanceof Settings && $setting->is_active();
	}

	/**
	 * @param Column $column
	 *
	 * @return array|false
	 */
	private function get_editable_settings( Column $column ) {
		$data = false;

		if ( $column instanceof Editable ) {
			$data = $column->editing()->get_view_settings();
		}

		$data = apply_filters( 'acp/editing/view_settings', $data, $column );
		$data = apply_filters( 'acp/editing/view_settings/' . $column->get_type(), $data, $column );

		return $data;
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