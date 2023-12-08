<?php

namespace ACA\YoastSeo\Search\Post;

use AC;
use AC\Helper\Select\Options;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class IsIndexed extends ACP\Search\Comparison\Meta
    implements ACP\Search\Comparison\Values
{

    /**
     * @var int|null
     */
    private $null_value;

    public function __construct(string $meta_key, int $null_value = null)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        $this->null_value = $null_value;

        parent::__construct($operators, $meta_key, Value::INT);
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            0 => __('Default for Post Type', 'codepress-admin-columns'),
            1 => __('No'),
            2 => __('Yes'),
        ]);
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        $base_query = parent::get_meta_query($operator, $value);

        if ((int)$value->get_value() === 0) {
            $operator = Operators::IS_EMPTY;

            return parent::get_meta_query($operator, $value);
        }

        $query = [
            'relation' => 'OR',
            $base_query,
        ];

        if ($this->null_value === (int)$value->get_value()) {
            $query[] = [
                'key'     => $this->meta_key,
                'compare' => 'NOT EXISTS',
            ];
        }

        return $query;
    }

}