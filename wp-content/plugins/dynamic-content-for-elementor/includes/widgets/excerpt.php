<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class Excerpt extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-excerpt'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Excerpt', 'dynamic-content-for-elementor')]);
        $this->add_control('html_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'div']);
        $this->add_control('excerpt_advanced', ['label' => __('Advanced manipulation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'post' => __('Post URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_advanced_excerpt', ['label' => __('Advanced Manipulation', 'dynamic-content-for-elementor'), 'condition' => ['excerpt_advanced!' => '']]);
        $this->add_control('excerpt_no_custom', ['label' => __('Generate Excerpts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Generate excerpts even if a post hasn\'t a custom excerpt attached.', 'dynamic-content-for-elementor')]);
        $this->add_control('excerpt_length', ['label' => __('Excerpt Length', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'default' => 40]);
        $this->add_control('excerpt_length_type', ['label' => __('Length Unit', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'words', 'options' => ['words' => __('Words', 'dynamic-content-for-elementor'), 'charachters' => __('Characters', 'dynamic-content-for-elementor')]]);
        $this->add_control('excerpt_ellipsis', ['label' => __('Excerpt Ellipsis', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => __('Will substitute the part of the post that is omitted in the excerpt.', 'dynamic-content-for-elementor'), 'default' => '&hellip;']);
        $this->add_control('excerpt_finish', ['label' => __('Finish', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'exact', 'options' => ['exact' => __('Exact', 'dynamic-content-for-elementor'), 'exact_w_spaces' => __('Exact (count spaces as well)', 'dynamic-content-for-elementor'), 'word' => __('Word', 'dynamic-content-for-elementor'), 'sentence' => __('Sentence', 'dynamic-content-for-elementor')]]);
        $this->add_control('excerpt_no_shortcode', ['label' => __('Remove Shortcode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('excerpt_strip_tags', ['label' => __('Strip Tags', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('excerpt_allowed_tags', ['label' => __('Remove all tags except the following', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'a,b,strong,i', 'description' => __('Write a list of HTML tag to maintain separated by comma.', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['excerpt_strip_tags!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Excerpt', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-excerpt, {{WRAPPER}} .dce-excerpt a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-excerpt']);
        $this->add_control('rollhover_heading', ['label' => __('Rollover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_color', ['label' => __('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-excerpt a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source']);
        $post = get_post($id_page);
        $excerpt = $post->post_excerpt;
        if (!empty($settings['excerpt_advanced'])) {
            $excerpt = $this->get_the_excerpt($post);
        }
        if (empty($excerpt)) {
            return;
        }
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'post':
                $link = esc_url(get_the_permalink());
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $html = \sprintf('<%1$s class="dce-excerpt %2$s">', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']), $animation_class);
        if ($link) {
            $html .= \sprintf('<a href="%s">%s</a>', $link, $excerpt);
        } else {
            $html .= $excerpt;
        }
        $html .= \sprintf('</%s>', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']));
        echo $html;
    }
    public function get_the_excerpt($post)
    {
        $settings = $this->get_settings_for_display();
        $excerpt = $post->post_excerpt;
        if (!\trim(\strip_tags($excerpt))) {
            if (!empty($settings['excerpt_no_custom'])) {
                $pieces = \explode('<!--more-->', $post->post_content, 2);
                $excerpt = \reset($pieces);
            }
        }
        // remove shortcodes
        if (!empty($settings['excerpt_no_shortcode'])) {
            $excerpt = strip_shortcodes($excerpt);
            $excerpt = Helper::vc_strip_shortcodes($excerpt);
        }
        $excerpt = apply_filters('the_excerpt', $excerpt);
        // From the default wp_trim_excerpt():
        // Some kind of precaution against malformed CDATA in RSS feeds I suppose
        $excerpt = \str_replace(']]>', ']]&gt;', $excerpt);
        // Strip HTML if $allowed_tags_option is set to 'remove_all_tags_except'
        if (!empty($settings['excerpt_strip_tags'])) {
            $allowed_tags = Helper::str_to_array(',', $settings['excerpt_allowed_tags'], 'strtolower');
            if (!empty($allowed_tags)) {
                $tag_string = '<' . \implode('><', $allowed_tags) . '>';
            } else {
                $tag_string = '';
            }
            $excerpt = \strip_tags($excerpt, $tag_string);
        }
        // Check if the excerpt is longer than excerpt_length setting
        $excerpt_is_longer = \true;
        switch ($settings['excerpt_length_type']) {
            case 'words':
                if (\str_word_count($excerpt) < $settings['excerpt_length']) {
                    $excerpt_is_longer = \false;
                }
                break;
            case 'charachters':
                if (\strlen($excerpt) < $settings['excerpt_length']) {
                    $excerpt_is_longer = \false;
                }
                break;
        }
        // Create the excerpt reduced
        if ($excerpt_is_longer) {
            $excerpt = Helper::text_reduce($excerpt, $settings['excerpt_length'], $settings['excerpt_length_type'], $settings['excerpt_finish']);
            $excerpt = $excerpt . $settings['excerpt_ellipsis'];
        }
        return $excerpt;
    }
}
