<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;
use InvalidArgumentException;

/**
 * Sorts any list table on a meta key. The meta value will go through a formatter before being sorted.
 * The meta value may contain mixed values, as long as the formatter can process them.
 * @since 5.2
 */
class MetaFormatFactory
{

    public function create(
        string $meta_type,
        string $meta_key,
        FormatValue $formatter,
        DataType $data_type = null,
        array $args = []
    ) {
        switch ($meta_type) {
            case MetaType::POST :
                return new Post\MetaFormat($formatter, $meta_key, $data_type);
            case MetaType::USER :
                return new User\MetaFormat($formatter, $meta_key, $data_type);
            case MetaType::COMMENT :
                return new Comment\MetaFormat($formatter, $meta_key, $data_type);
            case MetaType::TERM :
                $taxonomy = $args['taxonomy'] ?? null;

                if ( ! $taxonomy) {
                    throw new InvalidArgumentException('Missing taxonomy');
                }

                return new Taxonomy\MetaFormat($taxonomy, $formatter, $meta_key, $data_type);
            default :
                return null;
        }
    }

}