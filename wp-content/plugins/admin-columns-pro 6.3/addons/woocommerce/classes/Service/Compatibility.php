<?php

declare(strict_types=1);

namespace ACA\WC\Service;

use AC\Entity\Plugin;
use AC\Registerable;
use ACA\WC\Features;

class Compatibility implements Registerable
{

    private $plugin;

    private $features;

    public function __construct(Plugin $plugin, Features $features)
    {
        $this->plugin = $plugin;
        $this->features = $features;
    }

    public function register(): void
    {
        add_action('before_woocommerce_init', [$this, 'declare_compat']);
    }

    public function declare_compat(): void
    {
        $this->features->declare_compatibility_hpos($this->plugin->get_basename());
    }

}