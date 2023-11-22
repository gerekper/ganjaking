<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class Iframe extends \DynamicContentForElementor\Widgets\RemoteContent
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('incorporate', ['type' => Controls_Manager::HIDDEN, 'default' => '']);
        $this->update_control('iframe_doc', ['type' => Controls_Manager::HIDDEN, 'default' => '']);
    }
}
