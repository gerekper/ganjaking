<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Plugin;
trait Pagination
{
    //+exclude_start
    /**
     * Maybe Allow Posts Pagination
     *
     * @param bool $preempt
     * @param \WP_Query $wp_query
     * @return bool
     */
    public static function maybe_allow_posts_pagination($preempt, \WP_Query $wp_query)
    {
        // Combine all initial checks
        // 1. Return early if another filter has already handled the logic
        // 2. Check if we are on a paginated query page
        // 3. Ensure there is a specific post in the query
        // 4. Ensure the query is for a singular post/page
        if ($preempt || empty($wp_query->query_vars['page']) || empty($wp_query->post) || !is_singular()) {
            return $preempt;
        }
        $current_post_id = $wp_query->post->ID;
        $widgets_with_pagination = Plugin::instance()->features->get_feature_info_by_array(Plugin::instance()->features->filter_by_tag('pagination'), 'name');
        // Check for Elementor: If current post/page is built with Elementor and has pagination widgets
        $doc = \Elementor\Plugin::$instance->documents->get($current_post_id);
        if ($doc && $doc->is_built_with_elementor() && self::is_pagination_active($current_post_id, $widgets_with_pagination)) {
            return \true;
        }
        // Check for Dynamic.ooo Template System: If the template system is active and the current post type has pagination
        if (\DynamicContentForElementor\Plugin::instance()->template_system->is_active()) {
            $options = get_option(DCE_TEMPLATE_SYSTEM_OPTION);
            $post_type = get_post_type($current_post_id);
            if (\is_array($options) && $options['dyncontel_field_single' . $post_type] && self::is_pagination_active($options['dyncontel_field_single' . $post_type], $widgets_with_pagination)) {
                return \true;
            }
        }
        // Check for Elementor Pro: If Elementor Pro template is active and has pagination in the template
        if (\DynamicContentForElementor\Helper::is_elementorpro_active()) {
            $locations = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->get_locations();
            if (isset($locations['single'])) {
                $location_docs = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('single');
                foreach ($location_docs as $location_doc_id => $settings) {
                    if ($current_post_id !== $location_doc_id && self::is_pagination_active($location_doc_id, $widgets_with_pagination)) {
                        return \true;
                    }
                }
            }
        }
        return $preempt;
    }
    /**
     * Is Pagination Active
     *
     * @param integer $post_id
     * @param array<string> $widgets_with_pagination
     * @return bool
     */
    protected static function is_pagination_active(int $post_id, array $widgets_with_pagination)
    {
        if (!$post_id) {
            return \false;
        }
        $pagination = \false;
        $document_elements = \Elementor\Plugin::$instance->documents->get($post_id)->get_elements_data();
        // Check if widgets with pagination are present and if pagination or infinite scroll is active
        \Elementor\Plugin::$instance->db->iterate_data($document_elements, function ($element) use(&$pagination, $widgets_with_pagination) {
            if (isset($element['widgetType']) && \in_array($element['widgetType'], $widgets_with_pagination, \true)) {
                if (isset($element['settings']['pagination_enable']) && $element['settings']['pagination_enable']) {
                    $pagination = \true;
                }
                if (isset($element['settings']['infiniteScroll_enable']) && $element['settings']['infiniteScroll_enable']) {
                    $pagination = \true;
                }
            }
        });
        return $pagination;
    }
    //+exclude_end
}
