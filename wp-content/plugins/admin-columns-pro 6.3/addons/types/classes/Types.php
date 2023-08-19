<?php

namespace ACA\Types;

use AC;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;

final class Types implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! $this->has_minimum_required_types()) {
            return;
        }

        if ( ! $this->load_types_api()) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Columns(),
            new Service\Scripts($this->location),
            new IntegrationStatus('ac-addon-types'),
        ]);
    }

    private function has_minimum_required_types(): bool
    {
        $min_required_types_version = '3.4';

        return ! ( ! class_exists('Types_Main', false) ||
                   ! defined('TYPES_VERSION') ||
                   version_compare(TYPES_VERSION, $min_required_types_version, '<='));
    }

    /**
     * Load Types API functions
     */
    private function load_types_api(): bool
    {
        if ( ! defined('WPCF_EMBEDDED_TOOLSET_ABSPATH')) {
            return false;
        }

        $calls = [
            WPCF_EMBEDDED_TOOLSET_ABSPATH . '/types/embedded/frontend.php'        => [
                'types_render_termmeta',
                'types_render_field',
                'types_render_usermeta',
            ],
            WPCF_EMBEDDED_TOOLSET_ABSPATH . '/types/embedded/includes/fields.php' => [
                'wpcf_admin_fields_get_fields_by_group',
                'wpcf_admin_fields_get_field',
                'wpcf_admin_get_groups_by_post_type',
            ],
        ];

        foreach ($calls as $file => $functions) {
            if ( ! is_readable($file)) {
                return false;
            }

            require_once $file;

            foreach ($functions as $function) {
                if ( ! function_exists($function)) {
                    return false;
                }
            }
        }

        return true;
    }

}