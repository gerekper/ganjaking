<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicPosts extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-dynamicposts-v2';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Skin_Grid($this));
        $this->add_skin(new Skins\Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Skin_Carousel($this));
        $this->add_skin(new Skins\Skin_DualCarousel($this));
        $this->add_skin(new Skins\Skin_Accordion($this));
        $this->add_skin(new Skins\Skin_List($this));
        $this->add_skin(new Skins\Skin_Table($this));
        $this->add_skin(new Skins\Skin_Timeline($this));
        $this->add_skin(new Skins\Skin_3D($this));
        $this->add_skin(new Skins\Skin_Gridtofullscreen3d($this));
        $this->add_skin(new Skins\Skin_CrossroadsSlideshow($this));
    }
}
