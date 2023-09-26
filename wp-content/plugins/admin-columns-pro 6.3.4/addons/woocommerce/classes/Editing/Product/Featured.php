<?php

namespace ACA\WC\Editing\Product;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP\Editing\Service;
use ACP\Editing\View;

class Featured implements Service
{

    public function get_value($id)
    {
        $product = wc_get_product($id);

        return ($product && $product->is_featured()) ? 1 : 0;
    }

    public function update(int $id, $data): void
    {
        $product = wc_get_product($id);

        $product->set_featured($data);
        $product->save();
    }

    public function get_view(string $context): ?View
    {
        return new View\Toggle(
            new ToggleOptions(
                new Option(1, __('Yes')),
                new Option(0, __('No'))
            )
        );
    }

}