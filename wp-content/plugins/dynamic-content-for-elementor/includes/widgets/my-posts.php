<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class MyPosts extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-my-posts';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\My_Posts_Skin_Grid($this));
        $this->add_skin(new Skins\My_Posts_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\My_Posts_Skin_Carousel($this));
        $this->add_skin(new Skins\My_Posts_Skin_DualCarousel($this));
        $this->add_skin(new Skins\My_Posts_Skin_Accordion($this));
        $this->add_skin(new Skins\My_Posts_Skin_List($this));
        $this->add_skin(new Skins\My_Posts_Skin_Table($this));
        $this->add_skin(new Skins\My_Posts_Skin_Timeline($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'get_cpt']);
        $this->update_control('query_filter', ['label' => __('By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['date' => __('Date', 'dynamic-content-for-elementor'), 'term' => __('Terms & Taxonomy', 'dynamic-content-for-elementor'), 'author' => __('Author', 'dynamic-content-for-elementor'), 'metakey' => __('Metakey', 'dynamic-content-for-elementor')], 'multiple' => \true, 'label_block' => \true, 'default' => ['author']]);
        $this->update_control('heading_query_filter_author', ['type' => Controls_Manager::HIDDEN, 'condition' => ['query_filter' => 'author']]);
        $this->update_control('author_from', ['type' => Controls_Manager::HIDDEN, 'default' => 'current_user']);
    }
}
