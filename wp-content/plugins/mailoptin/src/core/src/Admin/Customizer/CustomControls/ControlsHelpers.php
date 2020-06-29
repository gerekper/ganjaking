<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

class ControlsHelpers
{
    /**
     * @return array
     */
    public static function get_system_font_stack()
    {
        return array(
            "Arial, Helvetica, sans-serif",
            "Times New Roman, Times, serif",
            "Tahoma, Geneva, sans-serif",
            "Consolas, Lucida Console, monospace",
            "Futura, Century Gothic, sans-serif",
            "Franklin Gothic Medium, sans-serif",
            "Courier New, Courier, monospace",
            "Copperplate Light, serif",
            "Cambria, Georgia, Times, serif",
            "Georgia, Times New Roman, serif",
            "Corbel, Lucida Grande, sans-serif",
            "Century Gothic, Apple Gothic, sans-serif",
            "Trebuchet MS, Arial, sans-serif",
            "Verdana, Geneva, sans-serif",
            "Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif",
            "Gill Sans, Calibri, sans-serif",
            "Segoe UI, Candara, Bitstream Vera Sans, Verdana",
            "Impact, Haettenschweiler, sans-serif",
            "Lucida Sans, Lucida Grande, sans-serif",
            "Garamond, Hoefler Text, serif",
            "Palatino Linotype, Book Antiqua, serif"
        );
    }

    /**
     * Get post of all post types.
     *
     * @param array $exclude
     * @param int $limit
     * @param string $search
     *
     * @return array
     */
    public static function get_all_post_types_posts($exclude = array(), $limit = 5000, $search = '')
    {
        $cache_key = md5($limit . '_' . implode("|", $exclude) . '_' . $search);

        $result = get_transient($cache_key);

        if (empty($result) || false === $result) {

            $post_types          = get_post_types(array('public' => true));
            $filtered_post_types = array_diff($post_types, $exclude);

            $result = array();

            $total_options_count = absint(apply_filters('mailoptin_all_post_types_posts_limit', $limit));

            $total_per_post_types = absint($total_options_count / count($filtered_post_types));

            foreach ($filtered_post_types as $post_type) {
                $result[$post_type] = self::get_post_type_posts($post_type, $total_per_post_types, 'publish', $search);
            }

            set_transient($cache_key, $result, apply_filters('mo_get_all_post_types_posts_cache_expiration', MINUTE_IN_SECONDS));
        }

        return $result;
    }

    /**
     * Array of a given post type posts with the post ID as key and title as value.
     *
     * @param string $post_type
     * @param int $limit
     * @param string $post_status
     * @param string $search
     *
     * @return array
     */
    public static function get_post_type_posts($post_type, $limit = 3000, $post_status = 'publish', $search = '')
    {
        global $wpdb;

        $limit = absint(apply_filters('mailoptin_post_type_posts_limit', $limit));

        $cache_key = md5("{$post_type}_{$limit}_{$post_status}_{$search}");

        $data = get_transient($cache_key);

        if (empty($data) || false === $data) {

            $table = $wpdb->posts;

            $sql = "SELECT ID, post_title FROM $table WHERE post_type = '%s' AND post_status = '%s'";

            if ( ! empty($search)) {
                $sql .= " AND post_title LIKE '%s'";
            }

            $sql .= " LIMIT $limit";

            $prepare_args = [$post_type, $post_status];
            if ( ! empty($search)) {
                $prepare_args[] = "%$search%";
            }

            $results = $wpdb->get_results($wpdb->prepare($sql, $prepare_args), 'ARRAY_A');

            $data = array();

            foreach ($results as $result) {
                $data[$result['ID']] = $result['post_title'];
            }

            set_transient($cache_key, $data, apply_filters('mo_get_post_type_posts_cache_expiration', MINUTE_IN_SECONDS));
        }

        return $data;
    }

    /**
     * Array of post categories.
     *
     * @return mixed
     */
    public static function get_categories()
    {
        $data = get_transient('mo_get_categories');

        if (empty($data) || false === $data) {

            $data = get_categories([
                'fields'     => 'id=>name',
                'hide_empty' => false
            ]);

            set_transient('mo_get_categories', $data, apply_filters('mo_get_categories_cache_expiration', MINUTE_IN_SECONDS));
        }

        return $data;
    }

    /**
     * Array of terms of a taxonomy.
     *
     * @return mixed
     */
    public static function get_terms($taxonomy)
    {
        return get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'fields'     => 'id=>name'
        ]);
    }

    public static function get_roles()
    {
        $all_roles = wp_roles()->roles;

        $editable_roles = apply_filters('editable_roles', $all_roles);

        $result = [];
        foreach ($editable_roles as $key => $value) {
            $result[$key] = $value['name'];
        }

        return $result;
    }

    /**
     * Array of post tags.
     *
     * @return mixed
     */
    public static function get_tags()
    {
        $data = get_transient('mo_get_tags');

        if (empty($data) || false === $data) {

            $data = get_tags([
                'orderby' => 'count',
                'order'   => 'DESC',
                'fields'  => 'id=>name'

            ]);

            set_transient('mo_get_tags', $data, apply_filters('mo_get_tags_cache_expiration', MINUTE_IN_SECONDS));
        }

        return $data;
    }


    /**
     * Array of authors.
     *
     * @return mixed
     */
    public static function get_authors()
    {
        $data = get_transient('mo_get_authors');

        if (empty($data) || false === $data) {

            $data = get_users(['who' => 'authors', 'fields' => ['ID', 'display_name']]);

            $data = array_reduce($data, function ($carry, $item) {
                $carry[$item->ID] = $item->display_name;

                return $carry;
            }, []);

            set_transient('mo_get_authors', $data, apply_filters('mo_get_authors_cache_expiration', MINUTE_IN_SECONDS));
        }

        return $data;
    }

    /**
     * Return array of publicly queryable custom post types.
     *
     * @return mixed
     */
    public static function get_post_types()
    {
        $data = get_transient('mo_get_post_types');

        if (empty($data) || false === $data) {

            $post_types = get_post_types(apply_filters('mailoptin_controls_helper_get_post_types', ['public' => true]), 'objects');

            $data = array_reduce($post_types, function ($carry, \WP_Post_Type $item) {
                $carry[$item->name] = $item->label;

                return $carry;
            });

            set_transient('mo_get_post_types', $data, apply_filters('mo_get_post_types_cache_expiration', MINUTE_IN_SECONDS));
        }

        return $data;
    }

    public static function custom_post_types()
    {
        $post_types = self::get_post_types();

        unset($post_types['post']);
        unset($post_types['page']);
        unset($post_types['attachment']);
        unset($post_types['wpbf_hooks']);
        unset($post_types['fl-builder-template']);
        unset($post_types['elementor_library']);
        unset($post_types['mailpoet_page']);

        return $post_types;
    }
}