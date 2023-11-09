<?php

namespace WPDeveloper\BetterDocsPro\REST;

use WPDeveloper\BetterDocs\Core\BaseAPI;

class DocsOrder extends BaseAPI {

    /**
     * @return mixed
     */
    public function register() {
        $this->get( '/docs_order', [$this, 'docs_order'], ['orderby' => [], "order" => [], "per_page" => [], "doc_category" => []] );
    }

     /**
     * BetterDocs Order API Callback
     */
    public function docs_order( $attr )
    {
        $query_args = array(
            'post_status' => 'publish',
            'post_type' => 'docs',
            'posts_per_page' => isset($attr['per_page']) ? $attr['per_page'] : 10,
            'orderby' => isset($attr['orderby']) && $attr['orderby'] === 'betterdocs_order' ? 'post__in' : 'menu_order',
        );

        $term_id = isset($attr['doc_category']) ? $attr['doc_category'] : 0;
        $docs_order = get_term_meta($term_id, '_docs_order', true);

        $term_object = get_term($term_id);


        $query_args['tax_query'][] =
            array(
                'taxonomy' => 'doc_category',
                'field'     => 'slug',
                'terms'    => $term_object->slug,
                'operator' => 'AND',
                'include_children' => false
            );

        $query_args['orderby'] =  !empty($docs_order) ? $query_args['orderby'] : 'menu_order';

        $new_ids = [];
        global $wpdb;

        if (!empty($docs_order)) {

            $docs_order = explode(',', $docs_order);

            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = $term_id");

            if (!is_null($results) && !empty($results) && is_array($results)) {

                $object_ids = array_filter($results, function ($value) use ($docs_order) {
                    return !in_array($value->object_id, $docs_order);
                });

                if (!empty($object_ids)) {

                    array_walk($object_ids, function ($value) use (&$new_ids) {
                        $new_ids[] = $value->object_id;
                    });
                }
            }
        } else {
            $docs_order = [];
        }



        $query_args['post__in'] = array_merge($new_ids, $docs_order);

        $data = [];
        $loop = new \WP_Query($query_args);

        if ($loop->have_posts()) {
            while ($loop->have_posts()) {
                $loop->the_post();
                $docs                       = array();
                $docs['id']                 = get_the_ID();
                $docs['title']['rendered']  =  wp_kses(get_the_title(get_the_ID()), betterdocs()->template_helper::ALLOWED_HTML_TAGS);
                $docs['permalink']          = get_permalink();
                $data[] = $docs;
            }
            wp_reset_postdata();
        }
        return $data;
    }

}
