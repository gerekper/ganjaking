<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooProducts extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-woo-products';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Products', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        parent::register_controls();
        $this->update_control('post_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'product']);
        $this->update_control('fallback', ['default' => __('No products found', 'dynamic-content-for-elementor')]);
    }
}
