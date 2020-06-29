<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;

/**
 * Sorts a list table by the meta value (raw db value) that is associated with the supplied meta key.
 * @since 5.2
 */
class MetaFactory {

	/**
	 * @param string        $meta_type e.g. post, user, comment or taxonomy
	 * @param string        $meta_key  e.g. 'my_custom_field_key'
	 * @param DataType|null $data_type e.g. numeric, date or string
	 *
	 * @return AbstractModel
	 */
	public function create( $meta_type, $meta_key, DataType $data_type = null ) {
		switch ( $meta_type ) {
			case MetaType::POST :
				return new Post\Meta( $meta_key, $data_type );
			case MetaType::USER :
				return new User\Meta( $meta_key, $data_type );
			case MetaType::COMMENT :
				return new Comment\Meta( $meta_key, $data_type );
			case MetaType::TERM :
				return new Taxonomy\Meta( $meta_key, $data_type );
			default :
				return new Disabled();
		}
	}

}