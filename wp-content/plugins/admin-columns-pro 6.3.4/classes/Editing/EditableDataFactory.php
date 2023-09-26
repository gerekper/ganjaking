<?php

namespace ACP\Editing;

use AC\Column;
use ACP\Editing\Factory\BulkEditFactory;
use ACP\Editing\Factory\InlineEditFactory;

/**
 * Get all data settings needed to load editing for the WordPress list table
 */
class EditableDataFactory {

	/**
	 * @var InlineEditFactory
	 */
	private $inline_edit_factory;

	/**
	 * @var BulkEditFactory
	 */
	private $bulk_edit_factory;

	public function __construct( InlineEditFactory $inline_edit_factory, BulkEditFactory $bulk_edit_factory ) {
		$this->inline_edit_factory = $inline_edit_factory;
		$this->bulk_edit_factory = $bulk_edit_factory;
	}

	public function create() {
		$data = [];

		foreach ( $this->inline_edit_factory->create() as $column ) {
			$column_data = $this->create_data_by_column( $column, Service::CONTEXT_SINGLE );

			if ( $column_data ) {
				$data[ $column->get_name() ]['type'] = $column->get_type();
				$data[ $column->get_name() ]['inline_edit'] = $column_data;
			}
		}

		foreach ( $this->bulk_edit_factory->create() as $column ) {
			$column_data = $this->create_data_by_column( $column, Service::CONTEXT_BULK );

			if ( $column_data ) {
				$data[ $column->get_name() ]['type'] = $column->get_type();
				$data[ $column->get_name() ]['bulk_edit'] = $column_data;
			}
		}

		return $data;
	}

	/**
	 * @param Column $column
	 * @param string $context
	 *
	 * @return array|null
	 */
	private function create_data_by_column( Column $column, $context ) {
		$service = ServiceFactory::create( $column );

		$filter = new ApplyFilter\View( $column, $context, $service );

		$view = $filter->apply_filters( $service->get_view( $context ) );

		if ( ! $view instanceof View ) {
			return null;
		}

		$data = $view->get_args();

		$data = apply_filters_deprecated( 'acp/editing/view_settings', [ $data, $column ], '5.7', "acp/editing/view" );
		$data = apply_filters_deprecated( 'acp/editing/view_settings/' . $column->get_type(), [ $data, $column ], '5.7', "acp/editing/view" );

		if ( ! is_array( $data ) ) {
			return null;
		}

		if ( isset( $data['options'] ) ) {
			$data['options'] = $this->format_js( $data['options'] );
		}

		return $data;
	}

	/**
	 * @param array $list
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