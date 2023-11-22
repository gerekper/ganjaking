<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class PageSettings
{
    public $page_settings = [];
    public function __construct()
    {
        $this->page_settings = \DynamicContentForElementor\Plugin::instance()->features->filter(['type' => 'page-setting']);
    }
    /**
     * On extensions Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function on_page_settings_registered()
    {
        $this->register_page_settings();
    }
    /**
     * On Controls Registered
     *
     * @since 1.0.4
     *
     * @access public
     */
    public function register_page_settings()
    {
        foreach ($this->page_settings as $page_setting_info) {
            if ($page_setting_info['status'] === 'active') {
                $class = '\\DynamicContentForElementor\\' . $page_setting_info['class'];
                $page_setting_object = new $class();
                \DynamicContentForElementor\Assets::add_depends($page_setting_object);
            }
        }
    }
    public static function get_excluded_page_settings()
    {
        return \json_decode(get_option('dce_excluded_page_settings', '[]'), \true);
    }
}
