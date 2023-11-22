<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Plugin;
trait Pagination
{
    //+exclude_start
    /**
     * Allow Posts Pagination
     *
     * @param bool $preempt
     * @param \WP_Query $wp_query
     * @return bool
     */
    public static function allow_posts_pagination($preempt, \WP_Query $wp_query)
    {
        if ($preempt || empty($wp_query->query_vars['page']) || empty($wp_query->post) || !is_singular()) {
            return $preempt;
        }
        $allow_pagination = \false;
        $current_post_id = $wp_query->post->ID;
        $widgets_with_pagination = Plugin::instance()->features->get_feature_info_by_array(Plugin::instance()->features->filter_by_tag('pagination'), 'name');
        // Check if current post/page is built with Elementor and check widgets with pagination
        $doc = \Elementor\Plugin::$instance->documents->get($current_post_id);
        if ($doc && $doc->is_built_with_elementor()) {
            $allow_pagination = self::check_posts_pagination($current_post_id, $widgets_with_pagination);
        }
        // Check if Single Dynamic.ooo Template System is active and check for widgets with pagination in template
        if (\DynamicContentForElementor\Plugin::instance()->template_system->is_active() && !$allow_pagination) {
            $options = get_option(DCE_TEMPLATE_SYSTEM_OPTION);
            $post_type = get_post_type($current_post_id);
            if (\is_array($options) && $options['dyncontel_field_single' . $post_type]) {
                $allow_pagination = self::check_posts_pagination($options['dyncontel_field_single' . $post_type], $widgets_with_pagination);
            }
        }
        // Check if single Elementor Pro template is active and check for DCE posts pagination in template
        if (\DynamicContentForElementor\Helper::is_elementorpro_active() && !$allow_pagination) {
            $locations = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->get_locations();
            if (isset($locations['single'])) {
                $location_docs = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('single');
                foreach ($location_docs as $location_doc_id => $settings) {
                    if ($wp_query->post->ID !== $location_doc_id) {
                        $allow_pagination = self::check_posts_pagination($location_doc_id, $widgets_with_pagination);
                        break;
                    }
                }
            }
        }
        if ($allow_pagination) {
            return $allow_pagination;
        }
        return $preempt;
    }
    /**
     * Check Posts Pagination
     *
     * @param integer $post_id
     * @param array<string> $widgets_with_pagination
     * @return bool
     */
    protected static function check_posts_pagination(int $post_id, array $widgets_with_pagination)
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
