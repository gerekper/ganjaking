<?php

namespace ACA\WC\Service;

use AC\Groups;
use AC\ListScreen;
use AC\Registerable;
use ACP\ListScreen\Taxonomy;

class ListScreenGroups implements Registerable
{

    public function register(): void
    {
        add_action('ac/list_screen_groups', [$this, 'register_list_screen_groups']);
        add_action('ac/admin/menu_group', [$this, 'update_menu_list_groups'], 10, 2);
    }

    public function update_menu_list_groups(string $group, ListScreen $list_screen): string
    {
        if (in_array($list_screen->get_key(), $this->get_post_list_keys(), true)) {
            return 'woocommerce';
        }

        if (in_array($list_screen->get_key(), $this->get_attribute_list_keys(), true)) {
            return 'woocommerce_attributes';
        }

        if (in_array($list_screen->get_key(), $this->get_taxonomy_list_keys(), true)) {
            return 'woocommerce_taxonomy';
        }

        return $group;
    }

    public function register_list_screen_groups(Groups $groups): void
    {
        $groups->add('woocommerce', __('WooCommerce', 'codepress-admin-columns'), 11);
        $groups->add(
            'woocommerce_taxonomy',
            sprintf('%s - %s', __('WooCommerce', 'codepress-admin-columns'), __('Taxonomy', 'codepress-admin-columns')),
            11
        );
        $groups->add(
            'woocommerce_attributes',
            sprintf(
                '%s - %s',
                __('WooCommerce', 'codepress-admin-columns'),
                __('Attribute', 'codepress-admin-columns')
            ),
            11
        );
    }

    private function get_attribute_list_keys(): array
    {
        $taxonomies = [];

        foreach (wc_get_attribute_taxonomy_names() as $taxonomy_name) {
            $taxonomies[] = Taxonomy::KEY_PREFIX . $taxonomy_name;
        }

        return $taxonomies;
    }

    private function get_post_list_keys(): array
    {
        $keys = [
            'product',
            'product_variation',
            'shop_coupon',
        ];

        if ( ! ACA_WC_USE_HPOS) {
            $keys[] = 'shop_order';
            $keys[] = 'shop_subscription';
        }

        return $keys;
    }

    private function get_taxonomy_list_keys(): array
    {
        return [
            Taxonomy::KEY_PREFIX . 'product_cat',
            Taxonomy::KEY_PREFIX . 'product_tag',
        ];
    }

}