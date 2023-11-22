<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class ImageToken extends \Elementor\Core\DynamicTags\Data_Tag
{
    public function get_name()
    {
        return 'dce-dynamic-tag-image-token';
    }
    public function get_title()
    {
        return __('Image Token', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
    }
    public function get_docs()
    {
        return '';
    }
    protected function register_controls()
    {
        if (\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $this->register_controls_settings();
        } else {
            $this->register_controls_non_admin_notice();
        }
    }
    protected function register_controls_non_admin_notice()
    {
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this dynamic tag.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
    }
    protected function register_controls_settings()
    {
        $this->add_control('code', ['label' => __('Token Code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'placeholder' => '[acf:imagefield:id]', 'description' => esc_html__('The token should expand to either a WordPress Media ID or a URL. We recommend Media IDs as the result will use responsive image sizes.', 'dynamic-content-for-elementor')]);
    }
    public function get_value(array $options = [])
    {
        $code = $this->get_settings('code');
        $res = \DynamicContentForElementor\Helper::get_dynamic_value($code);
        $res = \trim($res);
        if (\is_numeric($res)) {
            $url = wp_get_attachment_image_url((int) $res, 'full');
            return ['id' => $res, 'url' => $url ?: ''];
        }
        return ['url' => $res];
    }
}
