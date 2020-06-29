<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

trait PageTargetingRuleTrait
{
    /**
     * Determine if the optin passes page level targeting rules.
     *
     * @param int $id
     *
     * @return bool
     */
    public function page_level_targeting_rule_checker($id)
    {
        /**
         * get_queried_object_id() could be the ID of a term, category, author etc depending on view context.
         * @see \WP_Query::get_queried_object() from ln 4106
         */
        $post_id = is_singular() || is_front_page() ? get_queried_object_id() : 0;

        if ( ! $post_id) {
            if ('page' == get_option('show_on_front')) {
                $post_id = get_option('page_for_posts');
            }
        }

        /** @var array $post_categories categories of current post */
        $post_categories = wp_get_post_categories($post_id);

        /** @var array $post_tags tags of current post */
        $post_tags = wp_get_post_tags($post_id, ['fields' => 'ids']);

        /** @var string $post_post_type post type of current post */
        $post_post_type = get_post_type($post_id);

        $load_optin_index                = OCR::get_customizer_value($id, 'load_optin_index');
        $posts_never_load                = OCR::get_customizer_value($id, 'posts_never_load');
        $pages_never_load                = OCR::get_customizer_value($id, 'pages_never_load');
        $cpt_never_load                  = OCR::get_customizer_value($id, 'cpt_never_load');
        $post_categories_load            = OCR::get_customizer_value($id, 'post_categories_load');
        $post_categories_hide            = OCR::get_customizer_value($id, 'post_categories_hide');
        $post_tags_load                  = OCR::get_customizer_value($id, 'post_tags_load');
        $exclusive_post_types_posts_load = OCR::get_customizer_value($id, 'exclusive_post_types_posts_load');
        $post_types_load                 = OCR::get_customizer_value($id, 'exclusive_post_types_load');

        if (OCR::get_customizer_value($id, 'click_launch_status') || OCR::get_merged_customizer_value($id, 'load_optin_globally')) {
            // if current post should never contain optin, return false.
            if ( ! empty($posts_never_load) && in_array($post_id, $posts_never_load)) {
                return false;
            }

            // if current page should never contain optin, return false.
            if ( ! empty($pages_never_load) && is_page($post_id) && in_array($post_id, $pages_never_load)) {
                return false;
            }

            // if current CPT post should never contain optin, return false.
            if ( ! empty($cpt_never_load) && in_array($post_id, $cpt_never_load)) {
                return false;
            }

            // if current post category contain a category that optin should not load for, return false.
            if ( ! empty($post_categories_hide) && is_singular('post')) {

                $intersect = array_intersect($post_categories, $post_categories_hide);
                if ( ! empty($intersect)) {
                    return false;
                }
            }

            return true;
        }

        if (apply_filters('mailoptin_page_targeting_optin_rule', false, $id, $post_id)) {
            return true;
        }

        // if current view is neither frontpage, homepage, archive page or search page, return false.
        if ( ! empty($load_optin_index) && (is_front_page() || is_home() || is_archive() || is_search())) {
            return true;
        }

        // if current post should never contain optin, return false.
        if ( ! empty($posts_never_load) && in_array($post_id, $posts_never_load)) {
            return false;
        }

        // if current page should never contain optin, return false.
        if ( ! empty($pages_never_load) && is_page($post_id) && in_array($post_id, $pages_never_load)) {
            return false;
        }

        // if current post category contain a category that optin should not load for, return false.
        if ( ! empty($post_categories_hide) && is_singular('post')) {

            $intersect = array_intersect($post_categories, $post_categories_hide);
            if ( ! empty($intersect)) {
                return false;
            }
        }

        // if current CPT post should never contain optin, return false.
        if ( ! empty($cpt_never_load) && in_array($post_id, $cpt_never_load)) {
            return false;
        }

        // if current post category contain a category that optin should load for, return true.
        // array_intersect() return array element that exist in both comparison arrays.
        // i came across an odd case where a page had a post category and thus was showing the optin.
        // mind you, pages doesn't have categories.
        if ( ! empty($post_categories_load)) {
            if (is_singular('post')) {
                $intersect = array_intersect($post_categories, $post_categories_load);
                if ( ! empty($intersect)) {
                    return true;
                }
            }
        }

        // if current post category contain a tag that optin should load for, return true.
        // array_intersect() return array element that exist in both comparison arrays.
        if ( ! empty($post_tags_load)) {
            $intersect = array_intersect($post_tags, $post_tags_load);
            if ( ! empty($intersect)) {
                return true;
            }
        }

        // if current post isn't found in a set of all cpt posts to display optin for, return false.
        if ( ! empty($exclusive_post_types_posts_load) && in_array($post_id, $exclusive_post_types_posts_load)) {
            return true;
        }

        // there was a case where post listing page had a post type of post. this shouldn't be the case
        // as it is a frontpage (is_front_page()) and/or home page (is_home())
        if ( ! is_front_page() && ! is_home() && ! empty($post_types_load) && in_array($post_post_type, $post_types_load)) {
            return true;
        }

        return false;
    }
}