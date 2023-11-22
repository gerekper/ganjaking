<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class MetaboxRelationship extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-metabox-relationship';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Meta Box Relationship', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('metabox_relationship_id', ['label' => __('Meta Box Relationship', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the Relationship...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metabox_relationship']);
        $this->add_control('metabox_relationship_relation', ['label' => __('Relation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['from' => __('From', 'dynamic-content-for-elementor'), 'to' => __('To', 'dynamic-content-for-elementor')], 'default' => 'from']);
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
        if (empty($settings['metabox_relationship_id'])) {
            return;
        }
        $metabox_relationship['id'] = $settings['metabox_relationship_id'];
        // Check if the Meta Box Relationship ID exists
        if (!\array_key_exists($metabox_relationship['id'], \MB_Relationships_API::get_all_relationships())) {
            return;
        }
        if ('from' === $settings['metabox_relationship_relation']) {
            $metabox_relationship['from'] = get_the_ID();
        } else {
            $metabox_relationship['to'] = get_the_ID();
        }
        return $args + ['relationship' => $metabox_relationship];
    }
}
