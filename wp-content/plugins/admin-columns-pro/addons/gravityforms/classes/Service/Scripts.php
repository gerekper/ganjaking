<?php

declare(strict_types=1);

namespace ACA\GravityForms\Service;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\ListScreen;
use AC\Registerable;
use ACA\GravityForms\ListScreen\Entry;

class Scripts implements Registerable
{

    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('ac/admin_scripts', [$this, 'admin_scripts']);
        add_action('ac/table_scripts', [$this, 'table_scripts']);
        add_filter("gform_noconflict_styles", [$this, 'allowed_acp_styles']);
        add_filter("gform_noconflict_scripts", [$this, 'allowed_acp_scripts']);
    }

    public function admin_scripts(): void
    {
        wp_enqueue_style('gform_font_awesome');
    }

    public function table_scripts(ListScreen $list_screen): void
    {
        if ( ! $list_screen instanceof Entry) {
            return;
        }

        $style = new Style('aca-gf-table', $this->location->with_suffix('assets/css/table.css'));
        $style->enqueue();

        $script = new Script('aca-gf-table', $this->location->with_suffix('assets/js/table.js'));
        $script->enqueue();

        wp_enqueue_script('wp-tinymce');
    }

    public function allowed_acp_styles($objects)
    {
        global $wp_styles;

        foreach ($wp_styles->queue as $handle) {
            if ( ! $this->is_acp_asset($handle)) {
                continue;
            }

            $objects[] = $handle;
        }

        return $objects;
    }

    public function allowed_acp_scripts($objects)
    {
        global $wp_scripts;

        foreach ($wp_scripts->queue as $handle) {
            if ( ! $this->is_acp_asset($handle)) {
                continue;
            }

            $objects[] = $handle;
        }

        return $objects;
    }

    private function is_acp_asset(string $key): bool
    {
        $acp_prefixes = ['ac-', 'acp-', 'aca-', 'editor', 'mce-view', 'quicktags', 'common', 'tinymce'];

        foreach ($acp_prefixes as $prefix) {
            if (strpos($key, $prefix) !== false) {
                return true;
            }
        }

        return false;
    }

}