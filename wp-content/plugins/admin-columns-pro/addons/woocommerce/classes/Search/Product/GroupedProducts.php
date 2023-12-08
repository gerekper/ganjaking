<?php

namespace ACA\WC\Search\Product;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Helper\Select\Product\LabelFormatter\ProductTitle;
use ACA\WC\Helper\Select\Product\PaginatedFactory;
use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use WC_Product;

class GroupedProducts extends Comparison\Meta
    implements Comparison\SearchableValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, '_children');
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        switch ($operator) {
            case Operators::EQ :
                return [
                    'key'     => $this->get_meta_key(),
                    'compare' => 'LIKE',
                    'value'   => serialize((int)$value->get_value()),
                ];
            default:
                $comparison = SerializedComparisonFactory::create($this->meta_key, $operator, $value);

                return $comparison();
        }
    }

    private function get_grouped_products(): array
    {
        foreach (wc_get_products(['type' => 'grouped']) as $grouped_product) {
            if ( ! $grouped_product instanceof WC_Product) {
                continue;
            }
            $options = [];

            foreach ($grouped_product->get_children() as $child) {
                $options[] = $child;
            }
        }

        return $options;
    }

    public function format_label($value): string
    {
        $product = wc_get_product($value);

        return $product
            ? $this->get_label_formatter()->format_label($product)
            : '';
    }

    protected function get_label_formatter(): ProductTitle
    {
        return new ProductTitle();
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'        => $search,
            'paged'    => $page,
            'post__in' => $this->get_grouped_products(),
        ], $this->get_label_formatter());
    }

}