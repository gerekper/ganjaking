<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Tags extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Terms
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-tags';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Tags', 'dynamic-content-for-elementor');
    }
    protected function register_controls()
    {
        parent::register_controls();
        $this->update_control('taxonomy', ['type' => Controls_Manager::HIDDEN, 'default' => 'post_tag']);
    }
}
