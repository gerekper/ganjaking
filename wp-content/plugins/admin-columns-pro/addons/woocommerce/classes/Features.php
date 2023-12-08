<?php

declare(strict_types=1);

namespace ACA\WC;

use Automattic\WooCommerce\Internal\Features\FeaturesController;

class Features
{

    private $features_controller;

    public function __construct(FeaturesController $features_controller = null)
    {
        $this->features_controller = $features_controller;
    }

    public function use_hpos(): bool
    {
        return $this->features_controller && $this->features_controller->feature_is_enabled('custom_order_tables');
    }

    public function declare_compatibility_hpos(string $plugin_basename): void
    {
        if ($this->features_controller) {
            $this->features_controller->declare_compatibility('custom_order_tables', $plugin_basename);
        }
    }

}