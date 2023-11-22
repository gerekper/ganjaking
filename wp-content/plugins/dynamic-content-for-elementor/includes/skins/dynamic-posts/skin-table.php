<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_Table extends \DynamicContentForElementor\Includes\Skins\Skin_Base
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
    public $depended_styles = ['dce-dynamicPosts-table'];
    /**
     * Get ID
     *
     * @return string
     */
    public function get_id()
    {
        return 'table';
    }
    /**
     * Get Ttitle
     *
     * @return string
     */
    public function get_title()
    {
        return __('Table', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_table_controls'], 20);
    }
    /**
     * Register Additional Controls
     *
     * @param \DynamicContentForElementor\Widgets\DynamicPostsBase $widget
     * @return void
     */
    public function register_additional_table_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_table', ['label' => __('Table', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $widget->add_control('heading', ['label' => __('Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ label }}}', 'fields' => $repeater_fields->get_controls(), 'prevent_empty' => \false]);
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
        $this->start_controls_section('section_style_table', ['label' => __('Table', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('col_space', ['label' => __('Col space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} td, {{WRAPPER}} th' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('row_space', ['label' => __('Row space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1.0]], 'selectors' => ['{{WRAPPER}} td, {{WRAPPER}} th' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} table, {{WRAPPER}} th, {{WRAPPER}} td']);
        $this->add_control('table_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;']]);
        $this->add_control('heading_th', ['label' => __('Heading', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('th_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} th' => 'text-align: {{VALUE}};']]);
        $this->add_responsive_control('th_vertical_align', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => 'middle', 'selectors' => ['{{WRAPPER}} th' => 'vertical-align: {{VALUE}};']]);
        $this->add_control('th_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} th' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'th_typography', 'selector' => '{{WRAPPER}} th']);
        $this->add_control('th_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} th' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'th_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} th']);
        $this->add_control('heading_td', ['label' => __('Rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('tr_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} tr' => 'text-align: {{VALUE}};']]);
        $this->add_responsive_control('tr_vertical_align', ['label' => __('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'middle' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'default' => 'middle', 'selectors' => ['{{WRAPPER}} tr' => 'vertical-align: {{VALUE}};']]);
        $this->add_control('tr_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} tr td' => 'background-color: {{VALUE}} !important;']]);
        $this->add_control('tr_background_color_even', ['label' => __('Background Color for even rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} tr:nth-child(even) td' => 'background-color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'td_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} td']);
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
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts']]);
        $this->add_direction();
        $this->render_pagination_top();
        ?>

		<table <?php 
        echo $this->get_parent()->get_render_attribute_string('container');
        ?>>

		<?php 
        $this->render_table_heading();
        $this->render_posts_before();
        $this->render_posts_wrapper_before();
    }
    /**
     * Render Table Heading
     *
     * @return void
     */
    protected function render_table_heading()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        if (!empty($settings['heading'])) {
            echo '<thead>';
            echo '<tr>';
            foreach ($settings['heading'] as $key => $value) {
                echo '<th>' . ($value['label'] ?? '') . '</th>';
            }
            echo '</tr>';
            echo '</thead>';
        }
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
        ?>
	
		</table>

		<?php 
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

		<tr <?php 
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
		</tr>
		<?php 
    }
    /**
     * Render Repeater Item - Start
     *
     * @param string $id
     * @param string $item_id
     * @param array<mixed> $item_settings
     * @return void
     */
    protected function render_repeater_item_start(string $id, string $item_id, array $item_settings)
    {
        $this->get_parent()->set_render_attribute('dynposts_' . $id, ['class' => ['dce-item', 'dce-' . $item_id, 'elementor-repeater-item-' . $id]]);
        $this->render_responsive_settings($id, $item_settings);
        echo '<td ' . $this->get_parent()->get_render_attribute_string('dynposts_' . $id) . '>';
    }
    /**
     * Render Repeater Item - End
     *
     * @return void
     */
    protected function render_repeater_item_end()
    {
        echo '</td>';
    }
}
