<?php

namespace ElementPack\Includes\Controls\SelectInput;

use ElementPack\Element_Pack_Loader;
use Exception;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Dynamic_Select_Input_Module {

    const ACTION = '';

    private static $instance = null;

    /**
     * Returns the instance.
     *
     * @return object
     * @since  1.0.0
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Init method
     */

    /**
     * Constructor.
     */
    public function init() {
        add_action('wp_ajax_elementpack_dynamic_select_input_data', array($this, 'getSelectInputData'));
    }

    /**
     * get Ajax Data
     */
    public function getSelectInputData() {
        $nonce = isset($_POST['security']) ? sanitize_text_field($_POST['security']) : '';

        try {
            if (!wp_verify_nonce($nonce, 'ep_dynamic_select')) {
                throw new Exception('Invalid request');
            }

            if (!current_user_can('edit_posts')) {
                throw new Exception('Unauthorized request');
            }

            $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
            $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';

            if ($query == 'terms') {
                $data = $this->getTerms();
            } else if ($query == 'authors') {
                $data = $this->getAuthors();
            } else if ($query == 'authors_role') {
                $data = $this->getAuthorRoles();
            } else if ($query == 'only_post') {
                $data = $this->getOnlyPosts($post_type);
            } else if ($query == 'elementor_template') {
                $data = $this->getElementorTemplates();
            } else if ($query == 'anywhere_template') {
                $data = $this->getAnywhereTemplates();
            } elseif ($query == 'elementor_dynamic_loop_template') {
                $data = $this->getDynamicTemplates();
            } elseif ($query == 'bbpress_single_forum') {
                $data = $this->getDynamicBbPressForumIDs();
            } elseif ($query == 'bbpress_single_topic') {
                $data = $this->getDynamicBbPressTopicIDs();
            } elseif ($query == 'bbpress_single_reply') {
                $data = $this->getDynamicBbPressReplyIDs();
            } elseif ($query == 'bbpress_single_topic_terms') {
                $data = $this->getbbPressTerms();
            } else {
                $data = $this->getPosts();
            }

            wp_send_json_success($data);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }

        die();
    }

    /**
     * Get Post Type
     * @return string
     */
    protected function getPostType() {
        return isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';
    }

    /**
     * @return string[]|\WP_Post_Type[]
     */
    protected function getAllPublicPostTypes() {
        return array_values(element_pack_array_except(get_post_types(['public' => true]), ['attachment', 'elementor_library']));
    }

    /**
     * @return string
     */
    protected function getSearchQuery() {
        return isset($_POST['search_text']) ? sanitize_text_field($_POST['search_text']) : '';
    }

    /**
     * @return array|mixed
     */
    protected function getselecedIds() {
        return isset($_POST['ids']) ? sanitize_text_field($_POST['ids']) : [];
    }

    /**
     * @param string $taxonomy
     *
     * @return mixed|string
     */
    public function getTaxonomyName($taxonomy = '') {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $taxonomies = array_column($taxonomies, 'label', 'name');

        return isset($taxonomies[$taxonomy]) ? $taxonomies[$taxonomy] : '';
    }

    /**
     * @return string[]|\WP_Taxonomy[]
     */
    protected function getAllPublicTaxonomies() {
        return array_values(get_taxonomies(['public' => true]));
    }

    /**
     * Get Post Query Data
     *
     * @return array
     */
    public function getPosts() {
        $include    = $this->getselecedIds();
        $searchText = $this->getSearchQuery();

        $args = [];

        if ($this->getPostType() && $this->getPostType() !== '_related_post_type') {
            $args['post_type'] = $this->getPostType();
        } else {
            $args['post_type'] = $this->getAllPublicPostTypes();
        }

        if (!empty($include)) {
            $args['post__in']       = $include;
            $args['posts_per_page'] = count($include);
        } else {
            $args['posts_per_page'] = 20;
        }

        if ($searchText) {
            $args['s'] = $searchText;
        }

        $query   = new WP_Query($args);
        $results = [];
        foreach ($query->posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            if (!empty($data['include_type'])) {
                $text = $post_type_obj->labels->name . ': ' . $post->post_title;
            } else {
                $text = ($post_type_obj->hierarchical) ? $this->get_post_name_with_parents($post) : $post->post_title;
            }

            $results[] = [
                'id'   => $post->ID,
                'text' => esc_html($text),
            ];
        }

        return $results;
    }

    public function getDynamicTemplates() {
        $searchText        = $this->getSearchQuery();
        $args              = [];
        $args['post_type'] = 'elementor_library';
        if ($searchText) {
            $args['s'] = $searchText;
        }
        $query   = new WP_Query($args);
        $results = [];
        foreach ($query->posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            $text          = ($post_type_obj->hierarchical) ? $this->get_post_name_with_parents($post) : $post->post_title;
            $results[]     = [
                'id'   => $post->ID,
                'text' => esc_html($text),
            ];
        }
        return $results;
    }
    public function getDynamicBbPressForumIDs() {
        $searchText        = $this->getSearchQuery();
        $args              = [];
        $args['post_type'] = 'forum';
        if ($searchText) {
            $args['s'] = $searchText;
        }
        $query   = new WP_Query($args);
        $results = [];
        foreach ($query->posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            $text          = ($post_type_obj->hierarchical) ? $this->get_post_name_with_parents($post) : $post->post_title;
            $results[]     = [
                'id'   => $post->ID,
                'text' => esc_html($text),
            ];
        }
        return $results;
    }


    public function getDynamicBbPressTopicIDs() {
        $searchText        = $this->getSearchQuery();
        $args              = [];
        $args['post_type'] = 'topic';
        if ($searchText) {
            $args['s'] = $searchText;
        }
        $query   = new WP_Query($args);
        $results = [];
        foreach ($query->posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            $text          = ($post_type_obj->hierarchical) ? $this->get_post_name_with_parents($post) : $post->post_title;
            $results[]     = [
                'id'   => $post->ID,
                'text' => esc_html($text),
            ];
        }
        return $results;
    }

    public function getDynamicBbPressReplyIDs() {
        $searchText        = $this->getSearchQuery();
        $args              = [];
        $args['post_type'] = 'reply';
        if ($searchText) {
            $args['s'] = $searchText;
        }
        $query   = new WP_Query($args);
        $results = [];
        foreach ($query->posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            $text          = ($post_type_obj->hierarchical) ? $this->get_post_name_with_parents($post) : $post->post_title;
            $results[]     = [
                'id'   => $post->ID,
                'text' => esc_html($text),
            ];
        }
        return $results;
    }

    public function getOnlyPosts($post_type = 'post') {
        $include    = $this->getselecedIds();
        $searchText = $this->getSearchQuery();

        $args = [];

        $args['post_type'] = $post_type;

        if (!empty($include)) {
            $args['post__in']       = $include;
            $args['posts_per_page'] = count($include);
        } else {
            $args['posts_per_page'] = 20;
        }

        if ($searchText) {
            $args['s'] = $searchText;
        }

        $query   = new WP_Query($args);
        $results = [];
        foreach ($query->posts as $post) {
            $post_type_obj = get_post_type_object($post->post_type);
            if (!empty($data['include_type'])) {
                $text = $post_type_obj->labels->name . ': ' . $post->post_title;
            } else {
                $text = ($post_type_obj->hierarchical) ? $this->get_post_name_with_parents($post) : $post->post_title;
            }

            $results[] = [
                'id'   => $post->ID,
                'text' => esc_html($text),
            ];
        }

        return $results;
    }

    private function get_post_name_with_parents($post, $max = 3) {
        if (0 === $post->post_parent) {
            return $post->post_title;
        }
        $separator = is_rtl() ? ' < ' : ' > ';
        $test_post = $post;
        $names     = [];
        while ($test_post->post_parent > 0) {
            $test_post = get_post($test_post->post_parent);
            if (!$test_post) {
                break;
            }
            $names[] = $test_post->post_title;
        }

        $names = array_reverse($names);
        if (count($names) < ($max)) {
            return implode($separator, $names) . $separator . $post->post_title;
        }

        $name_string = '';
        for ($i = 0; $i < ($max - 1); $i++) {
            $name_string .= $names[$i] . $separator;
        }

        return $name_string . '...' . $separator . $post->post_title;
    }

    /**
     * Get Terms query data
     *
     * @return array
     */
    public function getTerms() {
        $search_text = $this->getSearchQuery();
        $taxonomies  = $this->getAllPublicTaxonomies();
        $include     = $this->getselecedIds();

        if ($this->getPostType() == '_related_post_type') {
            $post_type = $this->getAllPublicPostTypes();
        } elseif ($this->getPostType()) {
            $post_type = $this->getPostType();
        }

        $post_taxonomies = get_object_taxonomies($post_type);
        $taxonomies      = array_intersect($post_taxonomies, $taxonomies);
        $data            = [];

        if (empty($taxonomies)) {
            return $data;
        }

        $args = [
            'taxonomy'   => $taxonomies,
            'hide_empty' => false,
        ];

        if (!empty($include)) {
            $args['include'] = $include;
        }

        if ($search_text) {
            $args['number'] = 20;
            $args['search'] = $search_text;
        }

        $terms = get_terms($args);

        if (is_wp_error($terms) || empty($terms)) {
            return $data;
        }

        foreach ($terms as $term) {
            $label         = $term->name;
            $taxonomy_name = $this->getTaxonomyName($term->taxonomy);

            if ($taxonomy_name) {
                $label = "{$taxonomy_name}: {$label}";
            }

            $data[] = [
                'id'   => $term->term_taxonomy_id,
                'text' => $label,
            ];
        }

        return $data;
    }
    public function getbbPressTerms() {
        $search_text = $this->getSearchQuery();
        $taxonomies  = $this->getAllPublicTaxonomies();
        // $include     = $this->getselecedIds();

        // if ($this->getPostType() == '_related_post_type') {
        //     $post_type = $this->getAllPublicPostTypes();
        // } elseif ($this->getPostType()) {
        //     $post_type = $this->getPostType();
        // }

        $post_taxonomies = get_object_taxonomies('topic');
        $taxonomies      = array_intersect($post_taxonomies, $taxonomies);
        $data            = [];

        if (empty($taxonomies)) {
            return $data;
        }

        $args = [
            'taxonomy'   => $taxonomies,
            'hide_empty' => false,
        ];

        // if (!empty($include)) {
        //     $args['include'] = $include;
        // }

        if ($search_text) {
            $args['number'] = 10;
            $args['search'] = $search_text;
        }

        $terms = get_terms($args);

        if (is_wp_error($terms) || empty($terms)) {
            return $data;
        }

        foreach ($terms as $term) {
            $label         = $term->name;
            $taxonomy_name = $this->getTaxonomyName($term->taxonomy);

            if ($taxonomy_name) {
                $label = "{$taxonomy_name}: {$label}";
            }

            $data[] = [
                'id'   => $term->term_taxonomy_id,
                'text' => $label,
            ];
        }

        return $data;
    }

    /**
     * Get Authors query Data
     *
     * @return array
     */
    public function getAuthors() {
        $include     = $this->getselecedIds();
        $search_text = $this->getSearchQuery();

        $args = [
            'fields'  => ['ID', 'display_name'],
            'orderby' => 'display_name',
        ];

        if (!empty($include)) {
            $args['include'] = $include;
        }

        if ($search_text) {
            $args['number'] = 20;
            $args['search'] = "*$search_text*";
        }

        $users = get_users($args);

        $data = [];

        if (empty($users)) {
            return $data;
        }

        foreach ($users as $user) {
            $data[] = [
                'id'   => $user->ID,
                'text' => $user->display_name,
            ];
        }

        return $data;
    }

    /**
     * Get Authors Roles query Data
     *
     * @return array
     */
    public function getAuthorRoles() {
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $roles     = [];
        foreach ($all_roles as $key => $role) {
            $roles[] = [
                'id'   => $key,
                'text' => $role['name'],
            ];
        }

        return $roles;
    }

    /**
     * @return array of elementor template
     */
    public function getElementorTemplates() {
        $searchText = $this->getSearchQuery();
        if ($searchText) {
            $args['s'] = $searchText;
        }
        $templates = Element_Pack_Loader::elementor()->templates_manager->get_source('local')->get_items($args);
        $results   = [];

        if (empty($templates)) {
            $results = ['0' => __('Template Not Found!', 'bdthemes-element-pack')];
        } else {
            foreach ($templates as $template) {
                $results[] = [
                    'id'   => $template['template_id'],
                    'text' => $template['title'],
                ];
            }
        }
        return $results;
    }

    /**
     * @return array of anywhere template
     */
    /**
     * @return array of anywhere templates
     */
    public function getAnywhereTemplates() {
        $search_text = $this->getSearchQuery();
        $results     = [];
        if (post_type_exists('ae_global_templates')) {
            $anywhere = get_posts(array(
                'fields'         => 'ids', // Only get post IDs
                'posts_per_page' => -1,
                'post_type'      => 'ae_global_templates',
                's'              => $search_text,
            ));
            foreach ($anywhere as $key => $value) {
                $results[] = [
                    'id'   => $value,
                    'text' => get_the_title($value),
                ];
            }
        } else {
            $results = ['0' => esc_html__('AE Plugin Not Installed', 'bdthemes-element-pack')];
        }

        return $results;
    }
}

function Dynamic_Select_Input_Module() {
    return Dynamic_Select_Input_Module::get_instance();
}

Dynamic_Select_Input_Module()->init();
