<?php

namespace DynamicContentForElementor;

trait Wp
{
    /**
     * Get ALT for attachment
     *
     * @param int $attachment_id
     * @return string
     */
    public static function get_attachment_alt($attachment_id)
    {
        // Get ALT
        $thumb_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', \true);
        // Return ALT
        return esc_attr(\trim(wp_strip_all_tags($thumb_alt)));
    }
    /**
     * Get current site domain
     * @license GPLv3
     * @copyright Elementor
     * @return string
     */
    public static function get_site_domain()
    {
        return \str_ireplace('www.', '', wp_parse_url(home_url(), \PHP_URL_HOST));
    }
    public static function get_client_ip()
    {
        $server_ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        foreach ($server_ip_keys as $key) {
            if (isset($_SERVER[$key]) && \filter_var($_SERVER[$key], \FILTER_VALIDATE_IP)) {
                return sanitize_text_field($_SERVER[$key]);
            }
        }
        // Fallback local ip.
        return '127.0.0.1';
    }
    public static function get_post_fields($meta = \false, $group = \false, $info = \true)
    {
        $postFieldsKey = array();
        $postTmp = get_post();
        if ($postTmp) {
            $postProp = array();
            $postPropAll = \get_object_vars($postTmp);
            if (!empty($meta) && \is_string($meta)) {
                foreach ($postPropAll as $key => $value) {
                    $pos_key = \stripos($value, $meta);
                    $pos_name = \stripos($key, $meta);
                    if ($pos_key === \false && $pos_name === \false) {
                        continue;
                    }
                    $postProp[$key] = $value;
                }
            } else {
                $postProp = $postPropAll;
            }
            if ($meta) {
                $metas = self::get_post_metas($group, \is_string($meta) ? $meta : null, $info);
                $postFieldsKey = $metas;
            }
            $postFields = \array_keys($postProp);
            if (!empty($postFields)) {
                foreach ($postFields as $value) {
                    $name = \str_replace('post_', '', $value);
                    $name = \str_replace('_', ' ', $name);
                    $name = \ucwords($name);
                    if ($info) {
                        $name .= ' (' . $value . ')';
                    }
                    if ($group) {
                        $postFieldsKey['POST'][$value] = $name;
                    } else {
                        $postFieldsKey[$value] = $name;
                    }
                }
                if ($group) {
                    $postFieldsKey = \array_merge(['POST' => $postFieldsKey['POST']], $postFieldsKey);
                    // in first position
                }
            }
        }
        return $postFieldsKey;
    }
    public static function get_post_data($args)
    {
        $defaults = array('posts_per_page' => 5, 'offset' => 0, 'category' => '', 'category_name' => '', 'orderby' => 'date', 'order' => 'DESC', 'include' => '', 'exclude' => '', 'meta_key' => '', 'meta_value' => '', 'post_type' => 'post', 'post_mime_type' => '', 'post_parent' => '', 'author' => '', 'author_name' => '', 'post_status' => 'publish', 'suppress_filters' => \true);
        $atts = wp_parse_args($args, $defaults);
        $posts = get_posts($atts);
        return $posts;
    }
    /**
     * Get Public Post Types
     *
     * @param boolean $exclude
     * @return array<string>
     */
    public static function get_public_post_types($exclude = \true)
    {
        $args = array('public' => \true);
        $skip_post_types = ['attachment', 'elementor_library', 'oceanwp_library'];
        $post_types = get_post_types($args);
        if ($exclude) {
            $post_types = \array_diff($post_types, $skip_post_types);
        }
        foreach ($post_types as $akey => $acpt) {
            $cpt = get_post_type_object($acpt);
            if ($cpt !== null && \is_object($cpt) && \property_exists($cpt, 'label')) {
                $post_types[$akey] = (string) $cpt->label;
            } else {
                unset($post_types[$akey]);
            }
        }
        return $post_types;
    }
    public static function get_pages()
    {
        $args = array('sort_order' => 'desc', 'sort_column' => 'menu_order', 'hierarchical' => 1, 'exclude' => '', 'include' => '', 'meta_key' => '', 'meta_value' => '', 'authors' => '', 'child_of' => 0, 'parent' => -1, 'exclude_tree' => '', 'number' => '', 'offset' => 0, 'post_type' => 'page', 'post_status' => 'publish');
        $pages = get_pages($args);
        $listPage = [];
        foreach ($pages as $page) {
            $listPage[$page->ID] = $page->post_title;
        }
        return $listPage;
    }
    public static function get_post_terms($post_id = 0, $taxonomy = null, $args = array(), $targs = array('public' => \true))
    {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        if ($taxonomy) {
            return wp_get_post_terms($post_id, $taxonomy, $args);
        }
        $post_terms = array();
        $post_taxonomies = get_taxonomies($targs);
        if (!empty($post_taxonomies)) {
            foreach ($post_taxonomies as $key => $atax) {
                $tmp_terms = wp_get_post_terms($post_id, $atax, $args);
                $post_terms = \array_merge($post_terms, $tmp_terms);
            }
        }
        return $post_terms;
    }
    public static function get_taxonomies($dynamic = \false, $cpt = '', $search = '')
    {
        $args = array();
        $output = 'objects';
        // or objects
        $operator = 'and';
        // 'and' or 'or'
        $taxonomies = get_taxonomies($args, $output, $operator);
        $listTax = [];
        if ($dynamic) {
            $listTax['dynamic'] = 'Dynamic';
        }
        if (!$cpt || $cpt == 'post') {
            $listTax['category'] = 'Categories posts (category)';
            $listTax['post_tag'] = 'Tags posts (post_tag)';
        }
        if ($taxonomies) {
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->name == 'elementor_library_category' || $taxonomy->name == 'elementor_font_type' || $taxonomy->name == 'nav_menu' || $taxonomy->name == 'link_category') {
                    continue;
                }
                if (!$cpt || \in_array($cpt, $taxonomy->object_type)) {
                    $listTax[$taxonomy->name] = $taxonomy->label . ' (' . $taxonomy->name . ')';
                }
            }
        }
        if (!empty($search)) {
            $tmp = array();
            foreach ($listTax as $tkey => $atax) {
                $pos_key = \stripos($tkey, $search);
                $pos_name = \stripos($atax, $search);
                if ($pos_key !== \false || $pos_name !== \false) {
                    $tmp[$tkey] = $atax;
                }
            }
            $listTax = $tmp;
        }
        return $listTax;
    }
    public static function get_woocommerce_taxonomies()
    {
        return ['product_cat', 'product_visibility', 'product_type', 'product_variation'];
    }
    public static function get_taxonomy_terms($taxonomy = null, $flat = \false, $search = '', $info = \true, $orderby = 'name', $order = 'ASC')
    {
        $listTerms = [];
        $flatTerms = [];
        $listTerms[''] = 'None';
        $args = array('taxonomy' => $taxonomy, 'hide_empty' => \false, 'orderby' => $orderby, 'order' => $order);
        if ($search) {
            $args['name__like'] = $search;
        }
        if ($taxonomy) {
            $terms = get_terms($args);
            if (!empty($terms)) {
                foreach ($terms as $aterm) {
                    if ($info) {
                        $listTerms[$aterm->term_id] = $aterm->name . ' (' . $aterm->slug . ')';
                    } else {
                        $listTerms[$aterm->term_id] = $aterm->name;
                    }
                }
                $flatTerms = $listTerms;
            }
        } else {
            $taxonomies = self::get_taxonomies();
            foreach ($taxonomies as $tkey => $atax) {
                if ($tkey) {
                    $args['taxonomy'] = $tkey;
                    $terms = get_terms($args);
                    if (!empty($terms)) {
                        $tmp = [];
                        $tmp['label'] = $atax;
                        foreach ($terms as $aterm) {
                            $term_name = $aterm->name;
                            if ($info) {
                                $term_name .= $term_name . ' (' . $aterm->slug . ')';
                            }
                            $tmp['options'][$aterm->term_id] = $term_name;
                            $flatTerms[$aterm->term_id] = $atax . ' > ' . $term_name;
                        }
                        $listTerms[] = $tmp;
                    }
                }
            }
        }
        if ($flat) {
            return $flatTerms;
        }
        return $listTerms;
    }
    public static function get_the_terms_ordered($post_id, $taxonomy)
    {
        $terms = get_the_terms($post_id, $taxonomy);
        if (\is_array($terms)) {
            \usort($terms, function ($a, $b) {
                return $a->term_order - $b->term_order;
            });
        }
        return $terms;
    }
    public static function get_parentterms($tax)
    {
        $parentTerms = get_terms($tax);
        $listTerm = [];
        $listTerm[0] = 'None';
        foreach ($parentTerms as $term_item) {
            $termChildren = get_term_children($term_item->term_id, $tax);
            if (\count($termChildren) > 0) {
                $listTerm[$term_item->term_id] = $term_item->name;
            }
        }
        return $listTerm;
    }
    public static function get_post_settings($settings)
    {
        $post_type = \DynamicContentForElementor\Helper::validate_post_type($settings['post_type']);
        $post_args['post_type'] = $post_type;
        if ($settings['post_type'] == 'post') {
            $post_args['category'] = $settings['category'];
        }
        $post_args['posts_per_page'] = $settings['num_posts'];
        $post_args['offset'] = $settings['post_offset'];
        $post_args['orderby'] = $settings['orderby'];
        $post_args['order'] = $settings['order'];
        return $post_args;
    }
    public static function get_excerpt_by_id($post_id, $excerpt_length = 160)
    {
        $the_post = get_post($post_id);
        // Get post
        $the_excerpt = null;
        if ($the_post) {
            $the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content;
        }
        $the_excerpt = \strip_tags(strip_shortcodes($the_excerpt));
        // Strip tags and images
        $words = \explode(' ', $the_excerpt, $excerpt_length + 1);
        if (\count($words) > $excerpt_length) {
            \array_pop($words);
            $the_excerpt = \implode(' ', $words);
            $the_excerpt .= '...';
            // Don't put a space before
        }
        return $the_excerpt;
    }
    // ************************************** ALL POST SINGLE IN ALL REGISTER TYPE ***************************/
    public static function get_all_posts($myself = null, $group = \false, $orderBy = 'title')
    {
        $args = array('public' => \true);
        $output = 'names';
        // names or objects, note names is the default
        $operator = 'and';
        // 'and' or 'or'
        $posttype_all = get_post_types($args, $output, $operator);
        $type_excluded = array('elementor_library', 'oceanwp_library', 'ae_global_templates');
        $typesRegistered = \array_diff($posttype_all, $type_excluded);
        // Return elementor templates array
        $templates[0] = 'None';
        $exclude_io = array();
        if (isset($myself) && $myself) {
            $exclude_io = array($myself);
        }
        $get_templates = get_posts(array('post_type' => $typesRegistered, 'numberposts' => -1, 'post__not_in' => $exclude_io, 'post_status' => 'publish', 'orderby' => $orderBy, 'order' => 'DESC'));
        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {
                if ($group) {
                    $templates[$template->post_type]['options'][$template->ID] = $template->post_title;
                    $templates[$template->post_type]['label'] = $template->post_type;
                } else {
                    $templates[$template->ID] = $template->post_title;
                }
            }
        }
        return $templates;
    }
    public static function get_posts_by_type($typeId, $myself = null, $group = \false)
    {
        $exclude_io = array();
        if (isset($myself) && $myself) {
            $exclude_io = array($myself);
        }
        $templates = array();
        $get_templates = get_posts(array('post_type' => $typeId, 'numberposts' => -1, 'post__not_in' => $exclude_io, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'DESC', 'suppress_filters' => \false));
        if (!empty($get_templates)) {
            foreach ($get_templates as $template) {
                $templates[$template->ID] = $template->post_title;
            }
        }
        return $templates;
    }
    //+exclude_start
    /**
     * Get Post object by post_meta query
     *
     * @use         $post = get_post_by_meta( array( meta_key = 'page_name', 'meta_value = 'contact' ) )
     * @since       1.0.4
     * @return      Object      WP post object
     */
    public static function get_post_by_meta($args = array())
    {
        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = (object) wp_parse_args($args);
        // grab page - polylang will take take or language selection ##
        $args = array(
            'meta_query' => array(array('key' => $args->meta_key, 'value' => $args->meta_value)),
            'post_type' => $args->post_type,
            //'page',
            'posts_per_page' => '1',
        );
        // run query ##
        $posts = get_posts($args);
        // check results ##
        if (is_wp_error($posts)) {
            if (WP_DEBUG) {
                $error_string = $result->get_error_message();
                echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            }
        }
        if (!$posts) {
            if (WP_DEBUG) {
                $error_string = __('No results found', 'dynamic-content-for-elementor');
                echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            }
            return \false;
        }
        // kick back results ##
        return \reset($posts);
    }
    //+exclude_end
    /**
     * Get Roles
     *
     * @param boolean $everyone
     * @param boolean $remove_admin
     * @return array<string,string>
     */
    public static function get_roles($everyone = \false, $remove_admin = \false)
    {
        $all_roles = wp_roles()->roles;
        $ret = array();
        if ($everyone) {
            $ret['everyone'] = __('Everyone', 'dynamic-content-for-elementor');
        }
        foreach ($all_roles as $key => $value) {
            $ret[$key] = $value['name'];
        }
        if ($remove_admin) {
            unset($ret['administrator']);
        }
        return $ret;
    }
    public static function get_current_user_role()
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $role = (array) $user->roles;
            return $role[0];
        } else {
            return \false;
        }
    }
    public static function get_term_posts($term_id, $cpt = 'any')
    {
        $posts = array();
        $term = self::get_term_by('id', $term_id);
        if ($term) {
            $term_medias = get_posts(array('post_type' => $cpt, 'numberposts' => -1, 'tax_query' => array(array('taxonomy' => $term->taxonomy, 'field' => 'id', 'terms' => $term_id, 'include_children' => \false))));
            return $term_medias;
        }
        return $posts;
    }
    public static function get_term_fields($meta = \false, $group = \false, $info = \true)
    {
        $termTmp = self::get_term_by('id', 1, 'category');
        if ($termTmp) {
            $termPropAll = \get_object_vars($termTmp);
            if (!empty($meta) && \is_string($meta)) {
                $termProp = array();
                foreach ($termPropAll as $key => $value) {
                    $pos_key = \stripos($value, $meta);
                    $pos_name = \stripos($key, $meta);
                    if ($pos_key === \false && $pos_name === \false) {
                        continue;
                    }
                    $termProp[$key] = $value;
                }
            } else {
                $termProp = $termPropAll;
            }
            if ($meta) {
                $metas = self::get_term_metas($group, \is_string($meta) ? $meta : null);
                $termFieldsKey = $metas;
            }
            $termFields = \array_keys($termProp);
            if (!empty($termFields)) {
                foreach ($termFields as $value) {
                    $name = \str_replace('term_', '', $value);
                    $name = \str_replace('_', ' ', $name);
                    $name = \ucwords($name);
                    if ($group) {
                        $termFieldsKey['TERM'][$value] = $name;
                    } else {
                        $termFieldsKey[$value] = $name;
                    }
                }
            }
            if ($group) {
                $termFieldsKey = \array_merge(['TERM' => $termFieldsKey['TERM']], $termFieldsKey);
                // in first position
            }
        }
        return $termFieldsKey;
    }
    public static function get_term($term_id, $atax = 'category')
    {
        $term = \false;
        if (\is_numeric($term_id)) {
            $term_id = \intval($term_id);
            $term = get_term($term_id);
        } else {
            $term = get_term_by('slug', $term_id, $atax);
            if (!$term) {
                $term = get_term_by('name', $term_id, $atax);
            }
        }
        return $term;
    }
    public static function get_term_by($field = 'id', $value = 1, $taxonomy = '')
    {
        if ($field == 'id' || $field == 'term_id') {
            $term = get_term($value);
        } else {
            $term = get_term_by($field, $value, $taxonomy);
        }
        return $term;
    }
    public static function get_taxonomy_by_term_id($term_id)
    {
        $term = get_term($term_id);
        if ($term) {
            return $term->taxonomy;
        }
        return \false;
    }
    public static function get_user_fields($meta = \false, $group = \false, $info = \true)
    {
        $userFieldsKey = array();
        $userTmp = wp_get_current_user();
        $blacklist_user_fields = array('user_login', 'user_pass', 'user_email', 'user_registered', 'user_activation_key', 'user_status');
        if (!$userTmp) {
            return array();
        }
        $userProp = \get_object_vars($userTmp);
        if (!empty($userProp['data'])) {
            $userPropAll = (array) $userProp['data'];
            $userProp = array();
            if (!empty($meta) && \is_string($meta)) {
                foreach ($userPropAll as $key => $value) {
                    if (!\is_string($value)) {
                        continue;
                    }
                    $pos_key = \stripos($value, $meta);
                    $pos_name = \stripos($key, $meta);
                    if ($pos_key === \false && $pos_name === \false) {
                        continue;
                    }
                    $userProp[$key] = $value;
                }
            } else {
                $userProp = $userPropAll;
            }
        }
        if ($meta) {
            $metas = self::get_user_metas($group, \is_string($meta) ? $meta : null, $info);
            $userFieldsKey = $metas;
        }
        $userFields = \array_keys($userProp);
        if (!empty($userFields)) {
            foreach ($userFields as $value) {
                if (\in_array($value, $blacklist_user_fields)) {
                    continue;
                }
                $name = \str_replace('user_', '', $value);
                $name = \str_replace('_', ' ', $name);
                $name = \ucwords($name) . ' (' . $value . ')';
                if ($group) {
                    $userFieldsKey['USER'][$value] = $name;
                } else {
                    $userFieldsKey[$value] = $name;
                }
            }
        }
        $pos_key = \is_string($meta) ? \stripos('avatar', $meta) : \false;
        if (empty($meta) || !\is_string($meta) || $pos_key !== \false) {
            if ($group) {
                $userFieldsKey['USER']['avatar'] = 'Avatar';
            } else {
                $userFieldsKey['avatar'] = 'Avatar';
            }
        }
        if ($group) {
            $userFieldsKey = \array_merge(['USER' => $userFieldsKey['USER']], $userFieldsKey);
            // in first position
        }
        return $userFieldsKey;
    }
    public static function get_adjacent_post_by_id($in_same_term = \false, $excluded_terms = '', $previous = \true, $taxonomy = 'category', $post_id = null)
    {
        global $wpdb;
        if (!($post = get_post($post_id))) {
            return null;
        }
        $current_post_date = $post->post_date;
        $adjacent = $previous ? 'previous' : 'next';
        $op = $previous ? '<' : '>';
        $join = '';
        $order = $previous ? 'DESC' : 'ASC';
        $where = $wpdb->prepare("WHERE p.post_date {$op} %s AND p.post_type = %s AND p.post_status = 'publish'", $current_post_date, $post->post_type);
        $sort = "ORDER BY p.post_date {$order} LIMIT 1";
        $query = "SELECT p.ID FROM {$wpdb->posts} AS p {$join} {$where} {$sort}";
        $result = $wpdb->get_var($query);
        if (null === $result) {
            $result = '';
        }
        if ($result) {
            $result = get_post($result);
        }
        return $result;
    }
    public static function wooc_data($idprod = null)
    {
        global $product;
        if (\DynamicContentForElementor\Helper::is_woocommerce_active()) {
            if (isset($idprod)) {
                $product = \wc_get_product($idprod);
            } else {
                $product = \wc_get_product();
            }
        }
        if (empty($product)) {
            return;
        }
        return $product;
    }
    public static function get_rev_ID($revid, $revtype = \false)
    {
        // Check if WPML is installed
        if (!\DynamicContentForElementor\Helper::is_plugin_active('sitepress-multilingual-cms')) {
            return $revid;
        }
        if (!$revtype) {
            $revtype = get_post_type($revid);
        }
        $rev_id = apply_filters('wpml_object_id', $revid, $revtype, \true);
        if (!$rev_id) {
            return $revid;
        }
        return $rev_id;
    }
    public static function get_post_value($post_id = null, $field = 'ID', $sub_field = '', $single = null)
    {
        $postValue = null;
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        $post_id = apply_filters('wpml_object_id', $post_id, get_post_type($post_id), \true);
        if ($field == 'permalink' || $field == 'get_permalink') {
            $postValue = get_permalink($post_id);
        }
        if ($field == 'post_excerpt' || $field == 'excerpt') {
            $post = get_post($post_id);
            $postValue = $post->post_excerpt;
        }
        if ($field == 'the_author' || $field == 'post_author' || $field == 'author') {
            $postValue = get_the_author();
        }
        if (\in_array($field, array('thumbnail', 'post_thumbnail', 'thumb'))) {
            $postValue = get_the_post_thumbnail();
        }
        if ($postValue === null) {
            if (\property_exists('WP_Post', $field)) {
                $postTmp = get_post($post_id);
                $postValue = $postTmp->{$field};
            }
        }
        if ($postValue === null) {
            if (\property_exists('WP_Post', 'post_' . $field)) {
                $postTmp = get_post($post_id);
                if ($postTmp) {
                    $postValue = $postTmp->{'post_' . $field};
                }
            }
        }
        if ($postValue === null || !$single) {
            if (metadata_exists('post', $post_id, $field)) {
                $postValue = get_post_meta($post_id, $field, $single);
            }
        }
        if ($postValue === null) {
            // for meta created with Toolset plugin
            if (metadata_exists('post', $post_id, 'wpcf-' . $field)) {
                $postValue = get_post_meta($post_id, 'wpcf-' . $field, $single);
            }
        }
        if ($postValue === null) {
            // for meta WooCoomerce plugin
            if (metadata_exists('post', $post_id, '_' . $field)) {
                $postValue = get_post_meta($post_id, '_' . $field, $single);
            }
        }
        if ($postValue === null) {
            $postValue = array();
            $post_terms = get_the_terms($post_id, $field);
            if (!empty($post_terms) && !is_wp_error($post_terms)) {
                foreach ($post_terms as $key => $aterm) {
                    $postValue[$aterm->term_id] = $aterm;
                }
            } else {
                // WooCommerce taxonomies (Attributes) begin with pa_
                $post_terms = get_the_terms($post_id, 'pa_' . $field);
                if (!empty($post_terms) && !is_wp_error($post_terms)) {
                    foreach ($post_terms as $key => $aterm) {
                        $postValue[$aterm->term_id] = $aterm;
                    }
                }
            }
        }
        if (\is_array($postValue)) {
            if (empty($postValue)) {
                return '';
            }
            if ($single === \true || \count($postValue) == 1) {
                return \reset($postValue);
            }
        }
        return $postValue;
    }
    public static function get_user_value($user_id = null, $field = 'display_name', $single = null)
    {
        $metaValue = null;
        if ($user_id) {
            $userTmp = get_user_by('ID', $user_id);
            if ($userTmp) {
                $user_properties_whitelist = ['ID', 'user_nicename', 'nicename', 'name', 'registered', 'user_registered', 'user_email', 'email', 'display_name', 'description', 'url', 'user_url', 'roles'];
                if (\in_array($field, $user_properties_whitelist)) {
                    // campo nativo
                    if (\property_exists($userTmp->data, $field)) {
                        $metaValue = $userTmp->data->{$field};
                    }
                    if ($metaValue === null) {
                        if (\property_exists($userTmp->data, 'user_' . $field)) {
                            $metaValue = $userTmp->data->{'user_' . $field};
                        }
                    }
                    // altri campi nativi
                    if ($metaValue === null) {
                        $userInfo = get_userdata($user_id);
                        if (\property_exists($userInfo, $field)) {
                            $metaValue = $userInfo->{$field};
                        }
                        if ($metaValue === null) {
                            if (\property_exists($userInfo, 'user_' . $field)) {
                                $metaValue = $userInfo->{'user_' . $field};
                            }
                        }
                    }
                }
                // campo meta
                if ($metaValue === null || !$single) {
                    if (metadata_exists('user', $user_id, $field)) {
                        $metaValue = get_user_meta($user_id, $field, \false);
                    }
                    if ($metaValue === null) {
                        // meta from module user_registration
                        if (metadata_exists('user', $user_id, 'user_registration_' . $field)) {
                            $metaValue = get_user_meta($user_id, 'user_registration_' . $field, \false);
                        }
                    }
                }
            }
        }
        if (\is_array($metaValue)) {
            if (empty($metaValue)) {
                return '';
            }
            if ($single === \true || \count($metaValue) == 1) {
                return \reset($metaValue);
            }
        }
        return wp_kses_post($metaValue);
    }
    public static function get_term_value($term = null, $field = 'name', $single = null)
    {
        $termValue = null;
        if (!\is_object($term)) {
            $term = self::get_term_by('id', $term);
        }
        if ($field == 'permalink' || $field == 'get_permalink' || $field == 'get_term_link' || $field == 'term_link') {
            $termValue = get_term_link($term);
        }
        if ($termValue === null) {
            if (\property_exists('WP_Term', $field)) {
                $termValue = $term->{$field};
            }
        }
        if ($termValue === null) {
            if (\property_exists('WP_Term', 'term_' . $field)) {
                $termValue = $term->{'term_' . $field};
            }
        }
        if ($termValue === null) {
            if (metadata_exists('term', $term->term_id, $field)) {
                $termValue = get_term_meta($term->term_id, $field, \false);
            }
        }
        if ($termValue === null) {
            // for meta created with Toolset plugin
            if (metadata_exists('term', $term->term_id, 'wpcf-' . $field)) {
                $termValue = get_term_meta($term->term_id, 'wpcf-' . $field, \false);
            }
        }
        if (\is_array($termValue)) {
            if (empty($termValue)) {
                return '';
            }
            if ($single === \true || \count($termValue) == 1) {
                return \reset($termValue);
            }
        }
        return $termValue;
    }
    public static function get_post_link($post_id = null)
    {
        return get_permalink($post_id);
    }
    public static function get_user_link($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_the_author_meta('ID');
        }
        return get_author_posts_url($user_id);
    }
    public static function get_term_link($term_id = null)
    {
        return get_term_link($term_id);
    }
    public static function get_options($like = '')
    {
        global $wpdb;
        $options = array();
        $query = 'SELECT option_name FROM ' . $wpdb->prefix . 'options';
        if ($like) {
            $query .= ' WHERE option_name LIKE %s';
        }
        $prepared_query = $wpdb->prepare($query, $like ? '%' . $wpdb->esc_like($like) . '%' : '');
        $results = $wpdb->get_results($prepared_query);
        if (!empty($results)) {
            foreach ($results as $key => $aopt) {
                $options[$aopt->option_name] = $aopt->option_name;
            }
            \ksort($options);
        }
        return $options;
    }
    //+exclude_start
    public static function get_dynamic_value($value, $fields = array(), $var = 'form')
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return $value;
        }
        if (\is_array($value)) {
            if (!empty($value)) {
                foreach ($value as $key => $setting) {
                    if (\is_string($setting)) {
                        $value[$key] = self::get_dynamic_value($setting, $fields);
                    }
                    // repeater
                    if (\is_array($setting)) {
                        foreach ($setting as $akey => $avalue) {
                            if (\is_array($avalue)) {
                                foreach ($avalue as $rkey => $rvalue) {
                                    $value[$key][$akey][$rkey] = self::get_dynamic_value($rvalue, $fields);
                                }
                            }
                        }
                    }
                }
            }
        }
        if (\is_string($value)) {
            $value = \DynamicContentForElementor\Tokens::do_tokens($value);
            if (!\DynamicContentForElementor\Tokens::$data) {
                $value = do_shortcode($value);
                if (!empty($fields)) {
                    $value = self::replace_setting_shortcodes($value, $fields);
                    $value = \DynamicContentForElementor\Tokens::replace_var_tokens($value, $var, $fields);
                }
            }
        }
        return $value;
    }
    //+exclude_end
    public static function get_post_css($post_id = null, $theme = \false)
    {
        $upload = wp_upload_dir();
        $elementor_styles = array('elementor-custom-frontend' => ELEMENTOR_ASSETS_PATH . 'css/custom-frontend.css', 'elementor-frontend-css' => ELEMENTOR_ASSETS_PATH . 'css/frontend.min.css', 'elementor-common-css' => ELEMENTOR_ASSETS_PATH . 'css/common.min.css');
        if ($theme) {
            $elementor_styles['theme-style'] = get_stylesheet_directory() . '/style.css';
            if (is_child_theme()) {
                $elementor_styles['theme-templatepath'] = get_template_directory() . '/style.css';
                $elementor_styles['theme-templatepath'] = get_template_directory() . '/assets/css/style.css';
            }
        }
        if (self::is_elementorpro_active()) {
            $elementor_styles['elementor-pro-css'] = ELEMENTOR_PRO_ASSETS_PATH . 'css/frontend.min.css';
        }
        if ($post_id) {
            $elementor_styles['elementor-post-' . $post_id . '-css'] = $upload['basedir'] . '/elementor/css/post-' . $post_id . '.css';
        }
        $css = '';
        foreach ($elementor_styles as $key => $astyle) {
            $css .= self::get_style_embed($astyle);
        }
        return $css;
    }
    public static function get_style_embed($style)
    {
        $css = '';
        if (\file_exists($style)) {
            $css = wp_remote_retrieve_body(wp_remote_get($style));
        }
        return $css;
    }
    public static function auto_login($uid)
    {
        if (\is_int($uid)) {
            $user = get_user_by('ID', $uid);
        } else {
            $user = get_user_by('login', $uid);
        }
        if (!$user instanceof \WP_User) {
            return;
        }
        // login as this user
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $user->user_login, $user);
    }
    /**
     * Get Queried Object Type
     *
     * @return string
     */
    public static function get_queried_object_type()
    {
        $queried_object = get_queried_object();
        if (\is_object($queried_object)) {
            switch (\get_class($queried_object)) {
                case 'WP_Term':
                    return 'term';
                case 'WP_User':
                    return 'user';
            }
        }
        return 'post';
    }
    public static function get_permalink($obj = null, $fallback = \false)
    {
        if (empty($obj) && !empty($fallback)) {
            return $fallback;
        }
        if (\is_numeric($obj) || empty($obj)) {
            return get_permalink($obj);
        }
        if (\is_string($obj)) {
            return $obj;
        }
        if (\is_array($obj)) {
            if (isset($obj['term_id'])) {
                return get_term_link($obj['term_id']);
            }
            if (isset($obj['user_login']) && isset($obj['ID'])) {
                return self::get_user_link($obj['ID']);
            }
            if (isset($obj['ID'])) {
                return get_permalink($obj['ID']);
            }
        }
        if (\is_object($obj)) {
            $val_class = \get_class($obj);
            if ($val_class == 'WP_Post') {
                return self::get_post_link($val->ID);
            }
            if ($val_class == 'WP_Term') {
                return get_term_link($val->term_id);
            }
            if ($val_class == 'WP_User') {
                return self::get_user_link($val->ID);
            }
        }
        return $fallback;
    }
    public static function get_id($obj = null, $fallback = \false)
    {
        if (empty($obj) && $fallback) {
            return get_the_ID();
        }
        if (\is_numeric($obj)) {
            return \intval($obj);
        }
        if (\filter_var($obj, \FILTER_VALIDATE_URL)) {
            return url_to_postid($obj);
        }
        if (\is_string($obj)) {
            return \intval($obj);
        }
        if (\is_array($obj)) {
            if (isset($obj['term_id'])) {
                return $obj['term_id'];
            }
            if (isset($obj['ID'])) {
                return $obj['ID'];
            }
        }
        if (\is_object($obj)) {
            $val_class = \get_class($obj);
            if ($val_class == 'WP_Post') {
                return $val->ID;
            }
            if ($val_class == 'WP_Term') {
                return $val->term_id;
            }
            if ($val_class == 'WP_User') {
                return $val->ID;
            }
        }
        return \false;
    }
    public static function get_post_id_from_url($url = '')
    {
        if (!$url) {
            global $wp;
            $url = home_url(add_query_arg(array(), $wp->request));
        }
        return url_to_postid($url);
    }
    public static function get_wp_query_args()
    {
        // https://www.billerickson.net/code/wp_query-arguments/
        $args = array(
            //////Author Parameters - Show posts associated with certain author.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Author_Parameters
            'author',
            //(int) - use author id [use minus (-) to exclude authors by ID ex. 'author' => '-1,-2,-3,']
            'author_name',
            //(string) - use 'user_nicename' (NOT name)
            'author__in',
            //(array) - use author id (available with Version 3.7).
            'author__not_in',
            //(array)' - use author id (available with Version 3.7).
            //////Category Parameters - Show posts associated with certain categories.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters
            'cat',
            //(int) - use category id.
            'category_name',
            //(string) - Display posts that have these categories, using category slug.
            'category_name',
            //(string) - Display posts that have "all" of these categories, using category slug.
            'category__and',
            //(array) - use category id.
            'category__in',
            //(array) - use category id.
            'category__not_in',
            //(array) - use category id.
            //////Tag Parameters - Show posts associated with certain tags.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Tag_Parameters
            'tag',
            //(string) - use tag slug.
            'tag_id',
            //(int) - use tag id.
            'tag__and',
            //(array) - use tag ids.
            'tag__in',
            //(array) - use tag ids.
            'tag__not_in',
            //(array) - use tag ids.
            'tag_slug__and',
            //(array) - use tag slugs.
            'tag_slug__in',
            //(array) - use tag slugs.
            //////Taxonomy Parameters - Show posts associated with certain taxonomy.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters
            //Important Note: tax_query takes an array of tax query arguments arrays (it takes an array of arrays)
            //This construct allows you to query multiple taxonomies by using the relation parameter in the first (outer) array to describe the boolean relationship between the taxonomy queries.
            'tax_query',
            //(array) - use taxonomy parameters (available with Version 3.1).
            //////Post & Page Parameters - Display content based on post and page parameters.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Post_.26_Page_Parameters
            'p',
            //(int) - use post id.
            'name',
            //(string) - use post slug.
            'page_id',
            //(int) - use page id.
            'pagename',
            //(string) - use page slug.
            'pagename',
            //(string) - Display child page using the slug of the parent and the child page, separated ba slash
            'post_parent',
            //(int) - use page id. Return just the child Pages. (Only works with heirachical post types.)
            'post_parent__in',
            //(array) - use post ids. Specify posts whose parent is in an array. NOTE: Introduced in 3.6
            'post_parent__not_in',
            //(array) - use post ids. Specify posts whose parent is not in an array.
            'post__in',
            //(array) - use post ids. Specify posts to retrieve. ATTENTION If you use sticky posts, they will be included (prepended!) in the posts you retrieve whether you want it or not. To suppress this behaviour use ignore_sticky_posts
            'post__not_in',
            //(array) - use post ids. Specify post NOT to retrieve.
            //NOTE: you cannot combine 'post__in' and 'post__not_in' in the same query
            //////Password Parameters - Show content based on post and page parameters. Remember that default post_type is only set to display posts but not pages.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Password_Parameters
            'has_password',
            //(bool) - available with Version 3.9
            //null for all posts with and without passwords
            'post_password',
            //(string) - show posts with a particular password (available with Version 3.9)
            //////Type & Status Parameters - Show posts associated with certain type or status.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters
            //NOTE: The 'any' keyword available to both post_type and post_status queries cannot be used within an array.
            'post_type' => 'any',
            // - retrieves any type except revisions and types with 'exclude_from_search' set to true.
            //////Type & Status Parameters - Show posts associated with certain type or status.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Status_Parameters
            'post_status',
            //(string / array) - use post status. Retrieves posts by Post Status, default value i'publish'.
            //NOTE: The 'any' keyword available to both post_type and post_status queries cannot be used within an array.
            //////Pagination Parameters
            //http://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters
            'posts_per_page',
            //(int) - number of post to show per page (available with Version 2.1). Use 'posts_per_page'=1 to show all posts (the 'offset' parameter is ignored with a -1 value). Note if the query is in a feed, wordpress overwrites this parameter with the stored 'posts_per_rss' option. Treimpose the limit, try using the 'post_limits' filter, or filter 'pre_option_posts_per_rss' and return -1
            'posts_per_archive_page',
            //(int) - number of posts to show per page - on archive pages only. Over-rides showposts anposts_per_page on pages where is_archive() or is_search() would be true
            'nopaging',
            //(bool) - show all posts or use pagination. Default value is 'false', use paging.
            'paged',
            //(int) - number of page. Show the posts that would normally show up just on page X when usinthe "Older Entries" link.
            // This whole paging thing gets tricky. Some links to help you out:
            // http://codex.wordpress.org/Function_Reference/next_posts_link#Usage_when_querying_the_loop_with_WP_Query
            // http://codex.wordpress.org/Pagination#Troubleshooting_Broken_Pagination
            'offset',
            // (int) - number of post to displace or pass over.
            // Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination. for a workaround see: http://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
            // The 'offset' parameter is ignored when 'posts_per_page'=>-1 (show all posts) is used.
            'page',
            // (int) - number of page for a static front page. Show the posts that would normally show up just on page X of a Static Front Page.
            //NOTE: The query variable 'page' holds the pagenumber for a single paginated Post or Page that includes the <!--nextpage--> Quicktag in the post content.
            'ignore_sticky_posts',
            // (boolean) - ignore sticky posts or not (available with Version 3.1, replaced caller_get_posts parameter). Default value is 0 - don't ignore sticky posts. Note: ignore/exclude sticky posts being included at the beginning of posts returned, but the sticky post will still be returned in the natural order of that list of posts returned.
            //////Order & Orderby Parameters - Sort retrieved posts.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
            'order',
            //(string) - Designates the ascending or descending order of the 'orderby' parameter. Default to 'DESC'.
            //Possible Values:
            'orderby',
            //(string) - Sort retrieved posts by parameter. Defaults to 'date'. One or more options can be passed. EX: 'orderby' => 'menu_order title'
            //Possible Values:
            //'none' - No order (available with Version 2.8).
            //'ID' - Order by post id. Note the captialization.
            //'author' - Order by author.
            //'title' - Order by title.
            //'name' - Order by post name (post slug).
            //'date' - Order by date.
            //'modified' - Order by last modified date.
            //'parent' - Order by post/page parent id.
            //'rand' - Random order.
            //'comment_count' - Order by number of comments (available with Version 2.9).
            //'menu_order' - Order by Page Order. Used most often for Pages (Order field in the EdiPage Attributes box) and for Attachments (the integer fields in the Insert / Upload MediGallery dialog), but could be used for any post type with distinct 'menu_order' values (theall default to 0).
            //'meta_value' - Note that a 'meta_key=keyname' must also be present in the query. Note alsthat the sorting will be alphabetical which is fine for strings (i.e. words), but can bunexpected for numbers (e.g. 1, 3, 34, 4, 56, 6, etc, rather than 1, 3, 4, 6, 34, 56 as yomight naturally expect).
            //'meta_value_num' - Order by numeric meta value (available with Version 2.8). Also notthat a 'meta_key=keyname' must also be present in the query. This value allows for numericasorting as noted above in 'meta_value'.
            //'title menu_order' - Order by both menu_order AND title at the same time. For more info see: http://wordpress.stackexchange.com/questions/2969/order-by-menu-order-and-title
            //'post__in' - Preserve post ID order given in the post__in array (available with Version 3.5).
            //////Date Parameters - Show posts associated with a certain time and date period.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Date_Parameters
            'year',
            //(int) - 4 digit year (e.g. 2011).
            'monthnum',
            //(int) - Month number (from 1 to 12).
            'w',
            //(int) - Week of the year (from 0 to 53). Uses the MySQL WEEK command. The mode is dependenon the "start_of_week" option.
            'day',
            //(int) - Day of the month (from 1 to 31).
            'hour',
            //(int) - Hour (from 0 to 23).
            'minute',
            //(int) - Minute (from 0 to 60).
            'second',
            //(int) - Second (0 to 60).
            'm',
            //(int) - YearMonth (For e.g.: 201307).
            'date_query',
            //(array) - Date parameters (available with Version 3.7).
            //these are super powerful. check out the codex for more comprehensive code examples http://codex.wordpress.org/Class_Reference/WP_Query#Date_Parameters
            //////Custom Field Parameters - Show posts associated with a certain custom field.
            //http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
            'meta_key',
            //(string) - Custom field key.
            'meta_value',
            //(string) - Custom field value.
            'meta_value_num',
            //(number) - Custom field value.
            'meta_compare',
            //(string) - Operator to test the 'meta_value'. Possible values are '!=', '>', '>=', '<', or ='. Default value is '='.
            'meta_query',
            //(array) - Custom field parameters (available with Version 3.1).
            //////Permission Parameters - Display published posts, as well as private posts, if the user has the appropriate capability:
            //http://codex.wordpress.org/Class_Reference/WP_Query#Permission_Parameters
            'perm',
            //(string) Possible values are 'readable', 'editable'
            //////Caching Parameters
            //http://codex.wordpress.org/Class_Reference/WP_Query#Caching_Parameters
            //NOTE Caching is a good thing. Setting these to false is generally not advised.
            'cache_results',
            //(bool) Default is true - Post information cache.
            'update_post_term_cache',
            //(bool) Default is true - Post meta information cache.
            'update_post_meta_cache',
            //(bool) Default is true - Post term information cache.
            'no_found_rows',
            //(bool) Default is false. WordPress uses SQL_CALC_FOUND_ROWS in most queries in order to implement pagination. Even when you donтАЩt need pagination at all. By Setting this parameter to true you are telling wordPress not to count the total rows and reducing load on the DB. Pagination will NOT WORK when this parameter is set to true. For more information see: http://flavio.tordini.org/speed-up-wordpress-get_posts-and-query_posts-functions
            //////Search Parameter
            //http://codex.wordpress.org/Class_Reference/WP_Query#Search_Parameter
            's',
            //(string) - Passes along the query string variable from a search. For example usage see: http://www.wprecipes.com/how-to-display-the-number-of-results-in-wordpress-search
            'exact',
            //(bool) - flag to make it only match whole titles/posts - Default value is false. For more information see: https://gist.github.com/2023628#gistcomment-285118
            'sentence',
            //(bool) - flag to make it do a phrase search - Default value is false. For more information see: https://gist.github.com/2023628#gistcomment-285118
            //////Post Field Parameters
            //For more info see: http://codex.wordpress.org/Class_Reference/WP_Query#Return_Fields_Parameter
            //also https://gist.github.com/luetkemj/2023628/#comment-1003542
            'fields',
        );
        return $args;
    }
    /**
     * Recursive sanitation for an array
     *
     * @param $array
     *
     * @return mixed
     */
    public static function recursive_sanitize_text_field($array)
    {
        if (!\is_array($array)) {
            return sanitize_text_field($array);
        }
        foreach ($array as $key => &$value) {
            if (\is_array($value)) {
                $value = self::recursive_sanitize_text_field($value);
            } else {
                $value = sanitize_text_field($value);
            }
        }
        return $array;
    }
    /**
     * Convert ACF Post Objects to IDS
     *
     * @param object|array<mixed>|void $input
     *
     * @return array<mixed>|void
     */
    public static function convert_acf_post_objects_to_ids($input)
    {
        if (\is_array($input)) {
            if (!empty($input) && \is_object($input[0])) {
                return \array_map(function ($post) {
                    return $post->ID;
                }, $input);
            }
            return $input;
        } elseif (\is_object($input)) {
            return $input->ID ?? '';
        }
        return $input;
    }
}
