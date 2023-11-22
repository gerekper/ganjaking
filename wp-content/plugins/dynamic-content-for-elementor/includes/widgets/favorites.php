<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class ShowFavorites extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-dynamic-show-favorites';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Show_Favorites_Skin_Grid($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_Carousel($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_DualCarousel($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_Accordion($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_List($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_Table($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_Timeline($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_3D($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_Gridtofullscreen3d($this));
        $this->add_skin(new Skins\Show_Favorites_Skin_CrossroadsSlideshow($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'favorites']);
    }
}
