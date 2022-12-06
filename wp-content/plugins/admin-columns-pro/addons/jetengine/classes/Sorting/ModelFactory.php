<?php

namespace ACA\JetEngine\Sorting;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;
use ACA\JetEngine\Sorting;
use ACP;

final class ModelFactory {

	/**
	 * @param Field  $field
	 * @param string $meta_type
	 *
	 * @return ACP\Sorting\AbstractModel
	 */
	public function create( Field $field, $meta_type ) {
		switch ( true ) {
			case $field instanceof Type\Media:
				return ( new ACP\Sorting\Model\MetaFormatFactory() )->create( $meta_type, $field->get_name(), new Sorting\FormatValue\Media );

			case $field instanceof Type\Select:
				$choices = $field->get_options();
				natcasesort( $choices );

				return $field->is_multiple()
					? ( new ACP\Sorting\Model\MetaFormatFactory )->create( $meta_type, $field->get_name(), new FormatValue\Select( $choices ) )
					: ( new ACP\Sorting\Model\MetaMappingFactory )->create( $meta_type, $field->get_name(), array_keys( $choices ) );

			case $field instanceof Type\Date:
				$dataType = $field->is_timestamp() ? ACP\Sorting\Type\DataType::NUMERIC : ACP\Sorting\Type\DataType::DATE;

				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $field->get_name(), new ACP\Sorting\Type\DataType( $dataType ) );

			case $field instanceof Type\DateTime:
				$dataType = $field->is_timestamp() ? ACP\Sorting\Type\DataType::NUMERIC : ACP\Sorting\Type\DataType::DATETIME;

				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $field->get_name(), new ACP\Sorting\Type\DataType( $dataType ) );

			case $field instanceof Type\Number:
				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $field->get_name(), new ACP\Sorting\Type\DataType( ACP\Sorting\Type\DataType::NUMERIC ) );

			default:
				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $field->get_name() );
		}
	}

}