<?php

namespace ACA\ACF\Search\Comparison\FieldGroup;

use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Location extends Comparison implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;
        $binding = new Bindings();

        $groups = acf_get_field_groups([$value->get_value() => true]);
        $ids = is_array($groups) ? wp_list_pluck($groups, 'ID') : [];

        if (empty($ids)) {
            $ids = [0];
        }

        $binding->where(sprintf($wpdb->posts . '.ID IN (%s)', implode(',', $ids)));

        return $binding;
    }

    public function get_values(): Options
    {
        return Options::create_from_array([
            'block'        => _x('Block', 'ACF location', 'codepress-admin-columns'),
            'user_form'    => _x('Users', 'ACF location', 'codepress-admin-columns'),
            'taxonomy'     => __('Taxonomy'),
            'comment'      => __('Comment'),
            'post_type'    => _x('Post Type', 'ACF location', 'codepress-admin-columns'),
            'widget'       => _x('Widget', 'ACF location', 'codepress-admin-columns'),
            'options_page' => _x('Option Page', 'ACF location', 'codepress-admin-columns'),
            'nav_menu'     => _x('Menu', 'ACF location', 'codepress-admin-columns'),
        ]);
    }

}