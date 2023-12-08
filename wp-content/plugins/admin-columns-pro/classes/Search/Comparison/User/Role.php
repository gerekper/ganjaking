<?php

namespace ACP\Search\Comparison\User;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Role extends Meta
    implements Values
{

    public function __construct(string $meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
        ]);

        parent::__construct($operators, $meta_key);
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        $comparison = SerializedComparisonFactory::create(
            $this->get_meta_key(),
            $operator,
            $value
        );

        return $comparison();
    }

    public function get_values(): Options
    {
        $options = [];

        foreach (wp_roles()->roles as $key => $role) {
            $options[$key] = translate_user_role($role['name']);
        }

        asort($options);

        return AC\Helper\Select\Options::create_from_array($options);
    }

}