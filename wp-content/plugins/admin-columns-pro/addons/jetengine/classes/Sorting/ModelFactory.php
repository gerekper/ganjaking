<?php

namespace ACA\JetEngine\Sorting;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;
use ACA\JetEngine\Sorting;
use ACP;
use ACP\Sorting\Type\DataType;

final class ModelFactory
{

    public function create(Field $field, string $meta_type, array $args = [])
    {
        switch (true) {
            case $field instanceof Type\Media:
                return (new ACP\Sorting\Model\MetaFormatFactory())->create(
                    $meta_type,
                    $field->get_name(),
                    new Sorting\FormatValue\Media(),
                    null,
                    $args
                );

            case $field instanceof Type\Select:
                $choices = $field->get_options();
                natcasesort($choices);

                return $field->is_multiple()
                    ? (new ACP\Sorting\Model\MetaFormatFactory())->create(
                        $meta_type,
                        $field->get_name(),
                        new FormatValue\Select($choices),
                        null,
                        $args
                    )
                    : (new ACP\Sorting\Model\MetaMappingFactory())->create(
                        $meta_type,
                        $field->get_name(),
                        array_keys($choices)
                    );

            case $field instanceof Type\Date:
                $dataType = $field->is_timestamp() ? DataType::NUMERIC : DataType::DATE;

                return (new ACP\Sorting\Model\MetaFactory())->create(
                    $meta_type,
                    $field->get_name(),
                    new DataType($dataType)
                );

            case $field instanceof Type\DateTime:
                $dataType = $field->is_timestamp() ? DataType::NUMERIC : DataType::DATETIME;

                return (new ACP\Sorting\Model\MetaFactory())->create(
                    $meta_type,
                    $field->get_name(),
                    new DataType($dataType)
                );

            case $field instanceof Type\Number:
                return (new ACP\Sorting\Model\MetaFactory())->create(
                    $meta_type,
                    $field->get_name(),
                    new DataType(DataType::NUMERIC)
                );

            default:
                return (new ACP\Sorting\Model\MetaFactory())->create($meta_type, $field->get_name());
        }
    }

}