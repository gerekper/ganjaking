<?php

namespace ACA\BP\Search\User;

use AC;
use AC\Helper\Select\Options;
use AC\MetaType;
use ACA\BP\Helper\Select;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class MemberTypes extends Comparison
    implements Comparison\Values
{

    /**
     * @var array
     */
    private $options;

    public function get_meta_type()
    {
        return MetaType::USER;
    }

    public function __construct(array $options)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        $this->options = $options;

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;
        $bindings = new Bindings();

        $member_type = get_term_by('slug', $value->get_value(), 'bp_member_type');

        if ( ! $member_type) {
            return $bindings;
        }

        $alias = $bindings->get_unique_alias('tr');
        $sql = $wpdb->prepare(
            "INNER JOIN wp_term_relationships as {$alias} ON {$alias}.object_id = ID AND {$alias}.term_taxonomy_id=%d",
            $member_type->term_id
        );
        $bindings->join($sql);

        return $bindings;
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array($this->options);
    }

}