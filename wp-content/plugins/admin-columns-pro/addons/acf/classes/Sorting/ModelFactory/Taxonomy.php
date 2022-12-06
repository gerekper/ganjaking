<?php

namespace ACA\ACF\Sorting\ModelFactory;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Sorting;
use ACP;

class Taxonomy implements Sorting\SortingModelFactory {

	public function create( Field $field, $meta_key, Column $column ) {
		if ( ! $field instanceof Field\Type\Taxonomy ) {
			return new ACP\Sorting\Model\Disabled();
		}

		if ( $field->uses_native_term_relation() ) {
			return new ACP\Sorting\Model\Disabled();
		}

		return ( new ACP\Sorting\Model\MetaFormatFactory() )->create( $column->get_meta_type(), $meta_key, new Sorting\FormatValue\Taxonomy() );
	}

}