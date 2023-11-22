<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_List extends \DynamicContentForElementor\Includes\Skins\Skin_Base
{
    /**
     * Depended Scripts
     *
     * @var array<string>
     */
    public $depended_scripts = [];
    /**
     * Depended Styles
     *
     * @var array<string>
     */
    public $depended_styles = [];
    /**
     * Get ID
     *
     * @return string
     */
    public function get_id()
    {
        return 'list';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('List', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_list_controls'], 20);
    }
    /**
     * Register Additional Controls
     *
     * @param \DynamicContentForElementor\Widgets\DynamicPostsBase $widget
     * @return void
     */
    public function register_additional_list_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_list', ['label' => __('List', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('type', ['label' => __('List Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['unordered' => ['title' => __('Unordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'ordered' => ['title' => __('Ordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ol']], 'default' => 'unordered']);
        $this->add_control('numbering', ['label' => __('Numbering Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['numbers' => __('Numbers', 'dynamic-content-for-elementor'), 'lowercase_letters' => __('Lowercase letters', 'dynamic-content-for-elementor'), 'uppercase_letters' => __('Uppercase letters', 'dynamic-content-for-elementor'), 'lowercase_roman_numerals' => __('Lowercase Roman numerals', 'dynamic-content-for-elementor'), 'uppercase_roman_numerals' => __('Uppercase Roman numerals', 'dynamic-content-for-elementor')], 'default' => 'numbers', 'condition' => [$this->get_control_id('type') => 'ordered']]);
        $this->end_controls_section();
    }
    /**
     * Register Style Controls
     *
     * @return void
     */
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_list', ['label' => __('List', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('row_gap', ['label' => __('Rows Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} li.dce-post' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    /**
     * Render Loop Start
     *
     * @return void
     */
    protected function render_loop_start()
    {
        if (!$this->parent) {
            throw new \Exception('Parent not found');
        }
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $this->add_direction();
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts']]);
        $this->render_pagination_top();
        if ('unordered' === $settings['list_type']) {
            ?>
			<ul <?php 
            echo $this->get_parent()->get_render_attribute_string('container');
            ?>>
			<?php 
        } else {
            switch ($settings['list_numbering']) {
                case 'lowercase_letters':
                    $this->get_parent()->add_render_attribute('container', 'type', 'a');
                    break;
                case 'uppercase_letters':
                    $this->get_parent()->add_render_attribute('container', 'type', 'A');
                    break;
                case 'lowercase_roman_numerals':
                    $this->get_parent()->add_render_attribute('container', 'type', 'i');
                    break;
                case 'uppercase_roman_numerals':
                    $this->get_parent()->add_render_attribute('container', 'type', 'I');
                    break;
            }
            ?>
			<ol <?php 
            echo $this->get_parent()->get_render_attribute_string('container');
            ?>>
		<?php 
        }
        $this->render_posts_before();
        $this->render_posts_wrapper_before();
    }
    /**
     * Render Loop End
     *
     * @return void
     */
    protected function render_loop_end()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $this->render_posts_wrapper_after();
        $this->render_posts_after();
        if ('unordered' === $settings['list_type']) {
            ?>
			</ul>
			<?php 
        } else {
            ?>
			</ol>
		<?php 
        }
        $this->render_pagination_bottom();
        $this->render_infinite_scroll();
    }
    /**
     * Render Post - Start
     *
     * @return void
     */
    protected function render_post_start()
    {
        $this->get_parent()->set_render_attribute('post', ['class' => get_post_class()]);
        $this->get_parent()->add_render_attribute('post', 'class', 'dce-post');
        $this->get_parent()->add_render_attribute('post', 'class', 'dce-post-item');
        $this->get_parent()->add_render_attribute('post', 'class', $this->get_item_class());
        $this->get_parent()->set_render_attribute('post', 'data-dce-post-id', $this->current_id);
        $this->get_parent()->set_render_attribute('post', 'data-dce-post-index', $this->counter);
        ?>

		<li <?php 
        echo $this->get_parent()->get_render_attribute_string('post');
        ?>>
		<?php 
    }
    /**
     * Render Post - End
     *
     * @return void
     */
    protected function render_post_end()
    {
        ?>
		</li>
		<?php 
    }
}
