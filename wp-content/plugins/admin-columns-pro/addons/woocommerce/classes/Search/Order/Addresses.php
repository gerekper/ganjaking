<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order;

use ACA\WC\Type\AddressType;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Addresses extends Comparison
{

    private $field;

    private $address_type;

    public function __construct(string $field, AddressType $address_type)
    {
        parent::__construct(
            new Operators([
                Operators::CONTAINS,
                Operators::EQ,
                Operators::NEQ,
                Operators::NOT_CONTAINS,
                Operators::ENDS_WITH,
                Operators::BEGINS_WITH,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );

        $this->field = $field;
        $this->address_type = $address_type;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('addresses');

        $bindings->join(
            sprintf(
                "
                JOIN {$wpdb->prefix}wc_order_addresses AS $alias 
                ON {$wpdb->prefix}wc_orders.id = $alias.order_id AND $alias.address_type = '%s'
                ",
                esc_sql((string)$this->address_type)
            )
        );

        $field = sprintf("%s.%s", $alias, esc_sql($this->field));

        switch ($operator) {
            case Operators::NOT_IS_EMPTY :
                $where = sprintf("%s is NOT NULL", $field);
                break;
            case Operators::IS_EMPTY :
                $where = sprintf("%s is NULL", $field);
                break;
            default :
                $where = ComparisonFactory::create(
                    $field,
                    $operator,
                    $value
                )->prepare();
        }

        $bindings->where($where);

        return $bindings;
    }

}