<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

/**
 * Sorts any list table on a meta key. The meta value will go through a formatter before being sorted.
 * The meta value may contain mixed values, as long as the formatter can process them.
 * @since 5.2
 */
class MetaFormatFactory {

	/**
	 * @param string      $meta_type e.g. post, user, comment or taxonomy
	 * @param string      $meta_key  e.g. 'my_custom_field'
	 * @param FormatValue $formatter The formatter applies formatting to the raw meta value
	 * @param DataType    $data_type e.g. string or numeric
	 *
	 * @return AbstractModel
	 */
	public function create( $meta_type, $meta_key, FormatValue $formatter, DataType $data_type = null ) {
		switch ( $meta_type ) {
			case MetaType::POST :
				return new Post\MetaFormat( $formatter, $meta_key, $data_type );
			case MetaType::USER :
				return new User\MetaFormat( $formatter, $meta_key, $data_type );
			case MetaType::COMMENT :
				return new Comment\MetaFormat( $formatter, $meta_key, $data_type );
			case MetaType::TERM :
				return new Taxonomy\MetaFormat( $formatter, $meta_key, $data_type );
			default :
				return new Disabled();
		}
	}

}