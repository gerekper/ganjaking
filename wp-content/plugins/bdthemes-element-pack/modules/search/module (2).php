<?php

namespace ElementPack\Modules\Search;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }


    public function get_name() {
        return 'search';
    }

    public function get_widgets() {

        $widgets = [
            'Search',
        ];

        return $widgets;
    }

    /**
     * @param array $term_ids
     * @return array
     */
    private function mapGroupControlQuery($term_ids = []) {
        $terms = get_terms(
            [
                'term_taxonomy_id' => $term_ids,
                'hide_empty'       => false,
            ]
        );

        $tax_terms_map = [];

        foreach ($terms as $term) {
            $taxonomy                   = $term->taxonomy;
            $tax_terms_map[$taxonomy][] = $term->term_id;
        }

        return $tax_terms_map;
    }

    public function element_pack_ajax_search() {
        global $post;

        $result       = array('results' => array());
        $search_input = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
        $settings     = isset($_POST['settings']) ? $_POST['settings'] : array();

        if (strlen($search_input) >= 3) {

            $query_args = [
                'post_type'      => isset($settings['post_type']) ? $settings['post_type'] : 'post',
                's'              => sanitize_text_field($search_input),
                'posts_per_page' => ($settings['per_page']) ? sanitize_text_field($settings['per_page']) : 5,
                'post_status'    => 'publish',
            ];

            /**
             * Set Authors
             */
            $include_users = [];
            $exclude_users = [];
            if (!empty($settings['include_author_ids'])) {
                if (in_array('authors', $settings['include_by'])) {
                    $include_users = wp_parse_id_list($settings['include_author_ids']);
                }
            }
            if (!empty($settings['exclude_author_ids'])) {
                if (in_array('authors', $settings['exclude_by'])) {
                    $exclude_users = wp_parse_id_list($settings['exclude_author_ids']);
                    $include_users = array_diff($include_users, $exclude_users);
                }
            }
            if (!empty($include_users)) {
                $query_args['author__in'] = $include_users;
            }

            if (!empty($exclude_users)) {
                $query_args['author__not_in'] = $exclude_users;;
            }

            /**
             * Set Taxonomy
             */

            $include_terms = [];
            $exclude_terms = [];
            $terms_query   = [];

            if (!empty($settings['include_term_ids'])) {
                if (in_array('terms', $settings['include_by'])) {
                    $include_terms = wp_parse_id_list($settings['include_term_ids']);
                }
            }
            if (!empty($settings['exclude_term_ids'])) {
                if (in_array('terms', $settings['exclude_by'])) {
                    $exclude_terms = wp_parse_id_list($settings['exclude_term_ids']);
                    $include_terms = array_diff($include_terms, $exclude_terms);
                }
            }

            if (!empty($include_terms)) {
                $tax_terms_map = $this->mapGroupControlQuery($include_terms);
                foreach ($tax_terms_map as $tax => $terms) {
                    $terms_query[] = [
                        'taxonomy' => $tax,
                        'field'    => 'term_id',
                        'terms'    => $terms,
                        'operator' => 'IN',
                    ];
                }
            }

            if (!empty($exclude_terms)) {
                $tax_terms_map = $this->mapGroupControlQuery($exclude_terms);
                foreach ($tax_terms_map as $tax => $terms) {
                    $terms_query[] = [
                        'taxonomy' => $tax,
                        'field'    => 'term_id',
                        'terms'    => $terms,
                        'operator' => 'NOT IN',
                    ];
                }
            }

            if (!empty($terms_query)) {
                $query_args['tax_query']             = $terms_query;
                $query_args['tax_query']['relation'] = 'AND';
            }

            $query_posts = get_posts($query_args);
            if (!empty($query_posts)) {
                foreach ($query_posts as $post) {
                    $content = !empty($post->post_excerpt) ? strip_tags(strip_shortcodes($post->post_excerpt)) : strip_tags(strip_shortcodes($post->post_content));
                    if (strlen($content) > 180) {
                        $content = substr($content, 0, 179) . '...';
                    }
                    $result['results'][] = array(
                        'title' => get_the_title(),
                        'text'  => $content,
                        'url'   => get_permalink($post->ID),
                    );
                }
            }
        }

        die(json_encode($result));
    }


    protected function add_actions() {

        // TODO AJAX SEARCH
        add_action('wp_ajax_element_pack_search', [$this, 'element_pack_ajax_search']);
        add_action('wp_ajax_nopriv_element_pack_search', [$this, 'element_pack_ajax_search']);
    }
}
