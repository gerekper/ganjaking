<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AcfRelationship extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-acf-relationship';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Acf_Relationship_Skin_Grid($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_Carousel($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_DualCarousel($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_Accordion($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_List($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_Table($this));
        $this->add_skin(new Skins\Acf_Relationship_Skin_Timeline($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'relationship']);
    }
    /**
     * Register Widget Specific Controls
     *
     * @return void
     */
    protected function register_widget_specific_controls()
    {
        $this->start_controls_section('section_acf_relationship_field', ['label' => __('ACF Relationship Field', 'dynamic-content-for-elementor')]);
        $this->add_acf_relationship_controls();
        $this->end_controls_section();
    }
}
