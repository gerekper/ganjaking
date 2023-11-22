<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_Grid_Filters extends \DynamicContentForElementor\Includes\Skins\Skin_Grid
{
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_grid_controls'], 20);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_filters_controls'], 11);
    }
    public $depended_scripts = ['dce-dynamicPosts-grid-filters'];
    public $depended_styles = [];
    public function get_script_depends()
    {
        return \array_merge(['imagesloaded', 'dce-dynamicPosts-grid', 'jquery-masonry', 'dce-infinitescroll', 'isotope', 'dce-jquery-match-height'], $this->depended_scripts);
    }
    public function get_style_depends()
    {
        return \array_merge(['dce-dynamicPosts-grid'], $this->depended_styles);
    }
    public function get_id()
    {
        return 'grid-filters';
    }
    public function get_title()
    {
        return __('Grid with Filters', 'dynamic-content-for-elementor');
    }
    public function register_additional_grid_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        parent::register_additional_grid_controls($widget);
        // Remove controls from Skin Grid
        $this->remove_control('flex_grow');
        $this->remove_control('v_pos_postitems');
        $this->remove_control('h_pos_postitems');
    }
    public function register_additional_filters_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $taxonomies = Helper::get_taxonomies();
        $this->start_controls_section('section_filters', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('filters_taxonomy', ['label' => __('Data Filters (Taxonomy)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor')] + $taxonomies, 'default' => 'category', 'label_block' => \true]);
        $this->add_control('filters_taxonomy_first_level_terms', ['label' => __('Use first level Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => [$this->get_control_id('filters_taxonomy!') => '']]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('filters_taxonomy_terms_' . $tkey, ['label' => __('Data Filters (Selected Terms)', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Term Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tkey, 'description' => __('Use only Selected taxonomy terms or leave empty to use All terms of this taxonomy', 'dynamic-content-for-elementor'), 'multiple' => \true, 'condition' => [$this->get_control_id('filters_taxonomy') => $tkey, $this->get_control_id('filters_taxonomy_first_level_terms') => '']]);
            }
        }
        $this->add_control('orderby_filters', ['label' => __('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['parent' => __('Parent', 'dynamic-content-for-elementor'), 'count' => __('Count (number of associated posts)', 'dynamic-content-for-elementor'), 'term_order' => __('Order', 'dynamic-content-for-elementor'), 'name' => __('Name', 'dynamic-content-for-elementor'), 'slug' => __('Slug', 'dynamic-content-for-elementor'), 'term_group' => __('Group', 'dynamic-content-for-elementor'), 'term_id' => 'ID'], 'default' => 'name']);
        $this->add_control('order_filters', ['label' => __('Sorting', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['ASC' => ['title' => __('ASC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-up'], 'DESC' => ['title' => __('DESC', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sort-down']], 'toggle' => \false, 'default' => 'ASC']);
        $this->add_control('all_filter', ['label' => __('Add "All" filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('all_default', ['label' => __('"All" filter is default', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => [$this->get_control_id('all_filter!') => '']]);
        $this->add_control('alltext_filter', ['label' => __('All text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('All', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('all_filter!') => '']]);
        $this->add_control('separator_filter', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ' / ']);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_filters', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('filters_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left']);
        $this->add_control('filters_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_hover', ['label' => __('Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_active', ['label' => __('Color Active', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#990000', 'selectors' => ['{{WRAPPER}} .dce-filters .filters-item.filter-active a' => 'color: {{VALUE}};']]);
        $this->add_control('filters_color_separator', ['label' => __('Separator Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-filters .filters-separator' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filters']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_filters_separator', 'label' => __('Typography Separator', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-filters .filters-separator']);
        $this->add_responsive_control('filters_padding_items', ['label' => __('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 0, 'max' => 100], 'px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filters .filters-separator' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('filters_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('filters_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'default' => ['top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} .dce-filters' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('filters_move_separator', ['label' => __('Vertical Shift Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-filters .filters-separator' => 'top: {{SIZE}}{{UNIT}}; position: relative;']]);
        $this->end_controls_section();
    }
    /**
     * Render Grid Filters Bar
     *
     * @return void
     */
    protected function render_grid_filters_bar()
    {
        if (!$this->get_instance_value('filters_taxonomy')) {
            return;
        }
        $p_query = $this->get_parent()->get_query();
        $term_filter = esc_html($this->get_instance_value('filters_taxonomy'));
        $args_filters = ['taxonomy' => $term_filter, 'object_ids' => wp_list_pluck($p_query->posts, 'ID')];
        if (Helper::is_wpml_active()) {
            $args_filters['object_ids'] = Helper::wpml_translate_object_id($args_filters['object_ids']);
        }
        $include_terms = [];
        if ($this->get_instance_value('filters_taxonomy_first_level_terms')) {
            $args_filters['parent'] = 0;
        } elseif ($this->get_instance_value('filters_taxonomy_terms_' . $term_filter) && !empty($this->get_instance_value('filters_taxonomy_terms_' . $term_filter))) {
            $include_terms = $this->get_instance_value('filters_taxonomy_terms_' . $term_filter);
        }
        $args_filters['include'] = $include_terms;
        $args_filters['orderby'] = $this->get_instance_value('orderby_filters');
        $args_filters['order'] = $this->get_instance_value('order_filters');
        $term_list_filters = get_terms($args_filters);
        if (!is_wp_error($term_list_filters)) {
            $this->render_filter($term_list_filters);
        }
    }
    /**
     * Render Filter
     *
     * @param array<int,\WP_Term> $terms
     * @return void
     */
    protected function render_filter(array $terms)
    {
        if (empty($terms)) {
            return;
        }
        $this->get_parent()->set_render_attribute('filter', 'class', ['dce-filters', 'align-' . $this->get_instance_value('filters_align')]);
        $all_filter = $this->get_instance_value('all_filter') === 'yes';
        $all_default = $this->get_instance_value('all_default') === 'yes';
        $this->get_parent()->set_render_attribute('separator', 'class', 'filters-separator');
        $this->add_direction('filter');
        echo '<div ' . $this->get_parent()->get_render_attribute_string('filter') . '>';
        $separator = '<span ' . $this->get_parent()->get_render_attribute_string('separator') . '>';
        $separator .= $this->get_instance_value('separator_filter');
        $separator .= '</span>';
        if ($all_filter) {
            $this->render_all_text($all_default);
            echo $separator;
        }
        foreach ($terms as $key => $term) {
            if (\is_object($term) && \get_class($term) === 'WP_Term') {
                if ($key) {
                    echo $separator;
                }
                $term_link = get_term_link($term->term_id);
                $term_link = is_wp_error($term_link) ? '' : $term_link;
                // Filter Item
                $this->get_parent()->set_render_attribute('filter-item', 'class', 'filters-item');
                if (0 === $key && !($all_filter & $all_default)) {
                    $this->get_parent()->add_render_attribute('filter-item', 'class', 'filter-active');
                }
                // Filter Item - Link
                $this->get_parent()->set_render_attribute('filter-item-link', 'href', $term_link);
                // Disable the Transition functionality for that specific link
                $this->get_parent()->set_render_attribute('filter-item-link', 'data-e-disable-page-transition', 'false');
                // Taxonomy Class
                $taxonomy_class = sanitize_html_class($term->taxonomy);
                // 'post_tag' taxonomy uses the 'tag' prefix for backward compatibility
                if ('post_tag' === $term->taxonomy) {
                    $taxonomy_class = 'tag';
                }
                // Term Class
                $term_class = sanitize_html_class($term->slug);
                if (\is_numeric($term_class) || !\trim($term_class, '-')) {
                    $term_class = $term->term_id;
                }
                $this->get_parent()->set_render_attribute('filter-item-link', 'data-filter', '.' . $taxonomy_class . '-' . $term_class);
                echo '<span ' . $this->get_parent()->get_render_attribute_string('filter-item') . '>';
                echo '<a ' . $this->get_parent()->get_render_attribute_string('filter-item-link') . '>';
                echo $term->name;
                echo '</a>';
                echo '</span>';
            }
        }
        echo '</div>';
    }
    /**
     * Render All Text
     *
     * @return void
     */
    protected function render_all_text($default)
    {
        $all_text = wp_kses_post($this->get_instance_value('alltext_filter'));
        if ($default) {
            $this->get_parent()->set_render_attribute('filter-item', 'class', ['filters-item', 'filter-active']);
        }
        echo '<span ' . $this->get_parent()->get_render_attribute_string('filter-item') . '>';
        echo '<a href="#" data-filter="*">' . $all_text . '</a>';
        echo '</span>';
    }
    protected function render_posts_before()
    {
        $this->render_grid_filters_bar();
    }
    public function get_container_class()
    {
        return 'dce-' . $this->get_id() . '-container dce-skin-' . $this->get_id() . ' dce-skin-' . parent::get_id() . ' dce-skin-' . parent::get_id() . '-' . $this->get_instance_value('grid_type');
    }
    public function get_wrapper_class()
    {
        return 'dce-' . $this->get_id() . '-wrapper dce-wrapper-' . $this->get_id() . ' dce-wrapper-' . parent::get_id();
    }
    public function get_item_class()
    {
        return 'dce-' . $this->get_id() . '-item dce-item-' . $this->get_id() . ' dce-item-' . parent::get_id();
    }
}
