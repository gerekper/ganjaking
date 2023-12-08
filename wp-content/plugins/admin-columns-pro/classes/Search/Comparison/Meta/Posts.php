<?php

namespace ACP\Search\Comparison\Meta;

use AC\Meta\Query;
use ACA\ACF\Search\UnserializedValuesTrait;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Posts extends Post
{

    use UnserializedValuesTrait;

    /**
     * @var string
     */
    private $serialize_type;

    public function __construct(
        string $meta_key,
        array $post_types = [],
        array $terms = [],
        Query $query = null,
        $serialize_type = Value::STRING
    ) {
        parent::__construct(
            $meta_key,
            $post_types,
            $terms,
            new Labels([
                Operators::EQ  => __('contains', 'codepress-admin-columns'),
                Operators::NEQ => __('does not contain', 'codepress-admin-columns'),
            ]),
            $query
        );

        $this->serialize_type = $serialize_type;
    }

    protected function get_post__in(): array
    {
        return $this->get_unserialized_values(parent::get_post__in());
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        if (Value::INT === $this->serialize_type) {
            $value = new Value(
                (int)$value->get_value(),
                $value->get_type()
            );
        }

        $comparison = SerializedComparisonFactory::create(
            $this->get_meta_key(),
            $operator,
            $value
        );

        return $comparison();
    }

}