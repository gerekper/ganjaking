<?php

namespace ACP\Sorting\Table;

use AC\ColumnRepository;
use AC\Table;
use ACP\Sorting\ApplyFilter;
use ACP\Sorting\Settings;
use ACP\Sorting\Type\SortType;

class ResetButton {

	/**
	 * @var ColumnRepository
	 */
	private $column_repository;

	/**
	 * @var Settings\ListScreen\PreferredSort
	 */
	private $setting_sort_default;

	/**
	 * @var Settings\ListScreen\PreferredSegmentSort
	 */
	private $setting_segment_default;

	/**
	 * @var ApplyFilter\DefaultSort
	 */
	private $default_sort_filter;

	public function __construct( ColumnRepository $column_repository, Settings\ListScreen\PreferredSort $setting_sort_default, Settings\ListScreen\PreferredSegmentSort $setting_segment_default, ApplyFilter\DefaultSort $default_sort_filter ) {
		$this->column_repository = $column_repository;
		$this->setting_sort_default = $setting_sort_default;
		$this->setting_segment_default = $setting_segment_default;
		$this->default_sort_filter = $default_sort_filter;
	}

	/**
	 * @param SortType $request_sort_type
	 *
	 * @return bool
	 */
	private function is_default( SortType $request_sort_type ) {
		$sort_type = $this->setting_sort_default->get();

		if ( ! $sort_type ) {
			$sort_type = $this->setting_segment_default->get();
		}

		$sort_type = $this->default_sort_filter->apply_filters( $sort_type );

		if ( ! $sort_type ) {
			return false;
		}

		return $sort_type->equals( $request_sort_type );
	}

	/**
	 * @param SortType $sort_type
	 *
	 * @return Table\Button|null
	 */
	public function get( SortType $sort_type ) {
		if ( $this->is_default( $sort_type ) ) {
			return null;
		}

		$column = $this->column_repository->find( $sort_type->get_order_by() );

		if ( ! $column ) {
			return null;
		}

		$label = strip_tags( $column->get_custom_label() );

		if ( empty( $label ) ) {
			$label = $column->get_label();
		}

		$button = new Table\Button( 'edit-columns' );
		$button->set_label( trim( __( 'Sorted by ', 'codepress-admin-columns' ) ) . ' ' . $label )
		       ->set_url( '#' )
		       ->set_text( __( 'Reset Sorting', 'codepress-admin-columns' ) )
		       ->set_attribute( 'class', 'ac-table-button reset-sorting' );

		return $button;
	}

}