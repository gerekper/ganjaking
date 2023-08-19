<?php

namespace ACP\API\Request;

use ACP\API\Request;

/**
 * Used for displaying changelog information when clicking "view details" on the plugins page.
 */
class ProductInformation extends Request
{

    public function __construct(string $plugin_name)
    {
        parent::__construct([
            'command'     => 'product_information',
            'plugin_name' => $plugin_name,
        ]);
    }

}