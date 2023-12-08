<?php

declare(strict_types=1);

namespace ACA\WC\Helper\Select\Product;

use AC\Helper\Select;
use WC_Product;

class Options extends Select\Options
{

    /**
     * @var WC_Product[]
     */
    private $products;

    /**
     * @var array
     */
    private $labels = [];

    private $formatter;

    public function __construct(array $posts, LabelFormatter $formatter)
    {
        $this->formatter = $formatter;
        array_map([$this, 'set_product'], $posts);
        $this->rename_duplicates();

        parent::__construct($this->get_options());
    }

    private function set_product(WC_Product $product): void
    {
        $this->products[$product->get_id()] = $product;
        $this->labels[$product->get_id()] = $this->formatter->format_label($product);
    }

    public function get_product(int $id): WC_Product
    {
        return $this->products[$id];
    }

    private function get_options(): array
    {
        return self::create_from_array($this->labels)->get_copy();
    }

    protected function rename_duplicates(): void
    {
        $duplicates = array_diff_assoc($this->labels, array_unique($this->labels));

        foreach ($this->labels as $id => $label) {
            if (in_array($label, $duplicates, true)) {
                $this->labels[$id] = $this->formatter->format_label_unique($this->get_product($id));
            }
        }
    }

}