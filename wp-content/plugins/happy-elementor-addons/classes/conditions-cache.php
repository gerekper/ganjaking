<?php

namespace Happy_Addons\Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Conditions_Cache {

    const OPTION_NAME = 'happy_theme_elements_conditions';

    protected $conditions = [];

    public function __construct() {
        $this->refresh();
    }

    /**
     * @param Theme_Document $document
     * @param array          $conditions
     *
     * @return $this
     */
    public function add($location, $post_id, array $conditions) {

        if ($location) {
            if (is_array($this->conditions) && !isset($this->conditions[$location])) {
                $this->conditions[$location] = [];
            }
            $this->conditions[$location][$post_id] = $conditions;
        }

        return $this;
    }

    /**
     * @param int $post_id
     *
     * @return $this
     */
    public function remove($post_id) {
        $post_id = absint($post_id);

        foreach ($this->conditions as $location => $templates) {
            foreach ($templates as $id => $template) {
                if ($post_id === $id) {
                    unset($this->conditions[$location][$id]);
                }
            }
        }

        return $this;
    }

    /**
     * @param Theme_Document $document
     * @param array          $conditions
     *
     * @return $this
     */
    public function update($location, $post_id, $conditions) {
        return $this->remove($post_id)->add($location, $post_id, $conditions);
    }

    public function save() {
        return update_option(self::OPTION_NAME, $this->conditions);
    }

    public function refresh() {
        $this->conditions = get_option(self::OPTION_NAME, []);

        return $this;
    }

    public function clear() {
        $this->conditions = [];

        return $this;
    }

    public function get_by_location($location) {
        if (isset($this->conditions[$location])) {
            return $this->conditions[$location];
        }

        return [];
    }

    public function regenerate() {
        $this->clear();

        $document_types = array_keys(Theme_Builder::TEMPLATE_TYPE);

        $post_types = [
            Theme_Builder::CPT,
        ];

        $query = new \WP_Query([
            'posts_per_page' => -1,
            'post_type' => $post_types,
            'fields' => 'ids',
            'meta_key' => '_ha_display_cond',
        ]);

        foreach ($query->posts as $post_id) {
            $conditions = get_post_meta($post_id,'_ha_display_cond',true);
            $location = get_post_meta($post_id,'_ha_library_type',true);

            $this->add($location, $post_id, $conditions);
        }

        $this->save();

        return $this;
    }
}
