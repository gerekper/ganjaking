<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class MetaboxRelationship extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-metabox-relationship';
    }
    /**
     * Register Skins
     *
     * @return void
     */
    protected function register_skins()
    {
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Grid($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Carousel($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_DualCarousel($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Accordion($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_List($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Table($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Timeline($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_3D($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_Gridtofullscreen3d($this));
        $this->add_skin(new Skins\Metabox_Relationship_Skin_CrossroadsSlideshow($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'metabox_relationship']);
    }
    /**
     * Register Widget Specific Controls
     *
     * @return void
     */
    protected function register_widget_specific_controls()
    {
        $this->start_controls_section('section_metabox_relationship_field', ['label' => __('Meta Box Relationship Field', 'dynamic-content-for-elementor')]);
        $this->add_control('metabox_relationship_id', ['label' => __('Meta Box Relationship', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the Relationship...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metabox_relationship']);
        $this->add_control('metabox_relationship_relation', ['label' => __('Relation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['from' => __('From', 'dynamic-content-for-elementor'), 'to' => __('To', 'dynamic-content-for-elementor')], 'default' => 'from']);
        $this->end_controls_section();
    }
}
