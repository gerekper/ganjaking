<?php

namespace ACA\WC\Search\ShopOrder\Customer;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Meta extends Comparison\Meta
{

    /** @var string */
    protected $related_meta_key;

    public function __construct($related_meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        $this->related_meta_key = $related_meta_key;

        parent::__construct($operators, '_customer_user');
    }

    public function get_meta_query(string $operator, Value $value): array
    {
        return [
            'key'     => $this->get_meta_key(),
            'value'   => $this->get_user_ids($value->get_value()),
            'compare' => 'IN',
        ];
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function get_user_ids($value)
    {
        return get_users([
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'   => $this->related_meta_key,
                    'value' => $value,
                ],
            ],
        ]);
    }

}