<?php

declare(strict_types=1);

namespace ACP\Table;

use AC;
use AC\Table\ListKeyCollection;
use AC\Type\ListKey;
use ACP\ListScreen\Taxonomy;
use WP_Taxonomy;

class ListKeysFactory implements AC\Table\ListKeysFactoryInterface
{

    /**
     * @var AC\Table\ListKeysFactory
     */
    private $factory;

    public function __construct(AC\Table\ListKeysFactory $factory)
    {
        $this->factory = $factory;
    }

    public function create(): ListKeyCollection
    {
        $keys = $this->factory->create();

        foreach ($this->get_taxonomies() as $taxonomy) {
            $keys->add(new ListKey(Taxonomy::KEY_PREFIX . $taxonomy->name));
        }

        // Network
        $keys->add(new ListKey('wp-ms_users'));
        $keys->add(new ListKey('wp-ms_sites'));

        do_action('acp/list_keys', $keys);

        return $keys;
    }

    private function get_taxonomies(): array
    {
        $taxonomies = [];

        foreach ($this->get_taxonomy_names() as $taxonomy_name) {
            $taxonomy = get_taxonomy($taxonomy_name);

            if ( ! $taxonomy instanceof WP_Taxonomy) {
                continue;
            }

            $taxonomies[] = $taxonomy;
        }

        return $taxonomies;
    }

    /**
     * @return string[]
     */
    private function get_taxonomy_names(): array
    {
        $taxonomies = get_taxonomies(['show_ui' => true]);

        unset($taxonomies['post_format']);

        if ( ! get_option('link_manager_enabled')) {
            unset($taxonomies['link_category']);
        }

        return (array)apply_filters('acp/taxonomies', $taxonomies);
    }

}