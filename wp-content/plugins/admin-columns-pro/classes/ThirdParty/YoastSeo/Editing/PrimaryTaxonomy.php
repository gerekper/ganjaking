<?php

namespace ACP\ThirdParty\YoastSeo\Editing;

use AC;
use ACP;
use ACP\Editing;
use ACP\Helper\Select;

/**
 * @property ACP\ThirdParty\YoastSeo\Column\PrimaryTaxonomy $column
 */
class PrimaryTaxonomy extends Editing\Model\Meta
	implements Editing\PaginatedOptions {

	/**
	 * @param int $id
	 *
	 * @return array|false
	 */
	public function get_edit_value( $id ) {
		$term = $this->column->get_raw_value( $id );

		if ( ! $term ) {
			$terms = wp_get_post_terms( $id, $this->column->get_taxonomy() );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return null;
			}

			return false;
		}

		$term = get_term( $term, $this->column->get_taxonomy() );

		return [
			$term->term_id => $term->name,
		];
	}

	public function get_view_settings() {
		return [
			self::VIEW_TYPE          => 'select2_dropdown',
			'multiple'               => false,
			'ajax_populate'          => true,
			self::VIEW_BULK_EDITABLE => false,
		];
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'     => $search,
			'page'       => $page,
			'taxonomy'   => $this->column->get_taxonomy(),
			'object_ids' => [ $id ],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->remove_setting( Editing\Settings\BulkEditing::NAME );
	}

}