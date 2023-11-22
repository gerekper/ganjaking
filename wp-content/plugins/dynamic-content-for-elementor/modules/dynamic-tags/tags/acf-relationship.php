<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AcfRelationship extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-acf-relationship';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('ACF Relationship', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('acf_relationship_field', ['label' => __('ACF Relationship Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'post_object,relationship']);
        parent::register_controls();
    }
    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $args = parent::get_args();
        $settings = $this->get_settings_for_display();
        $relations = \get_field($settings['acf_relationship_field'], \false);
        if (!$relations) {
            $relations = get_sub_field($settings['acf_relationship_field'], \false);
        }
        if (empty($relations)) {
            return;
        }
        if (Helper::is_wpml_active()) {
            // WPML Translation
            $relations = Helper::wpml_translate_object_id($relations);
        }
        // Descending order when Order By is set to "Preserve Post ID order given"
        if ('post__in' === $args['orderby'] && 'DESC' === $args['order']) {
            $relations = \array_reverse($relations);
        }
        return $args + ['post__in' => $relations];
    }
}
