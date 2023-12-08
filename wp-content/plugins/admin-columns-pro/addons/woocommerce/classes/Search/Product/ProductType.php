<?php

declare(strict_types=1);

namespace ACA\WC\Search\Product;

use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Term;

class ProductType extends Comparison implements Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias_pml = $bindings->get_unique_alias('pml_prtype');
        $alias_tr = $bindings->get_unique_alias('tr_prtype');

        $join_pml = "LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup AS $alias_pml 
                        ON $wpdb->posts.ID = $alias_pml.product_id";
        $join_tr = "LEFT JOIN $wpdb->term_relationships AS $alias_tr 
                        ON $wpdb->posts.ID = $alias_tr.object_id";

        switch ($value->get_value()) {
            case 'virtual' :
                $bindings->join($join_pml)
                         ->where("$alias_pml.virtual=1");
                break;
            case 'downloadable' :
                $bindings->join($join_pml)
                         ->where("$alias_pml.downloadable=1");
                break;
            default :
                $term = get_term_by('name', $value->get_value(), 'product_type');

                if ($term instanceof WP_Term) {
                    $bindings->join($join_tr)
                             ->where(
                                 $wpdb->prepare("$alias_tr.term_taxonomy_id = %d", $term->term_id)
                             );
                }
                break;
        }

        return $bindings;
    }

    public function get_values(): Options
    {
        $options = [];

        foreach (wc_get_product_types() as $name => $label) {
            $options[$name] = $label;

            if ('simple' === $name) {
                $indent = is_rtl() ? '&larr;' : '&rarr;';

                $options['downloadable'] = sprintf('%s %s', $indent, __('Downloadable', 'woocommerce'));
                $options['virtual'] = sprintf('%s %s', $indent, __('Virtual', 'woocommerce'));
            }
        }

        return Options::create_from_array($options);
    }

}