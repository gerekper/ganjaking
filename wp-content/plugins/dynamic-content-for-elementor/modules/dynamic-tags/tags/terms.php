<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Terms extends Tag
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-terms';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Terms', 'dynamic-content-for-elementor');
    }
    /**
     * Get Group
     *
     * @return string
     */
    public function get_group()
    {
        return 'dce';
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['base', 'text'];
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['new_line' => __('New Line', 'dynamic-content-for-elementor'), 'line_break' => __('Line Break', 'dynamic-content-for-elementor'), 'comma' => __('Comma', 'dynamic-content-for-elementor')], 'default' => 'new_line', 'multiple' => \true]);
        $this->add_control('taxonomy', ['label' => __('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_public_taxonomies(), 'default' => 'category']);
        $this->add_control('orderby', ['label' => __('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_term_orderby_options(), 'default' => 'name']);
        $this->add_control('order', ['label' => __('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => __('Ascending', 'dynamic-content-for-elementor'), 'DESC' => __('Descending', 'dynamic-content-for-elementor')], 'default' => 'ASC']);
        $this->add_control('hide_empty', ['label' => __('Hide Empty', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        foreach (Helper::get_taxonomies() as $tax_key => $a_tax) {
            if ($tax_key) {
                $this->add_control('exclude_terms_' . $tax_key, ['label' => __('Exclude Terms', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('None', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tax_key, 'render_type' => 'template', 'multiple' => \true, 'condition' => ['taxonomy' => $tax_key]]);
            }
        }
        $this->add_control('number', ['label' => __('Results', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10']);
        $this->add_control('return_format', ['label' => __('Return Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['name' => __('Name', 'dynamic-content-for-elementor'), 'name_id' => __('Name | ID', 'dynamic-content-for-elementor'), 'id' => __('ID', 'dynamic-content-for-elementor')], 'default' => 'name']);
    }
    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $args = $this->get_args();
        if (empty($args)) {
            return;
        }
        $term_query = new \WP_Term_Query($args);
        if (empty($term_query->terms)) {
            return;
        }
        $count = \count($term_query->terms);
        $i = 1;
        foreach ($term_query->terms as $term) {
            echo $this->get_term_by_format($term->term_id);
            if ($i < $count) {
                echo $this->separator($settings['separator']);
            }
            $i++;
        }
    }
    /**
     * Get Term By Format
     *
     * @return string|int|false
     */
    protected function get_term_by_format($id)
    {
        if (!term_exists($id)) {
            return;
        }
        $return_format = $this->get_settings('return_format');
        switch ($return_format) {
            case 'name_id':
                return esc_html(get_term_field('name', $id)) . '|' . $id;
            case 'id':
                return $id;
            default:
                return esc_html(get_term_field('name', $id));
        }
    }
    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $settings = $this->get_settings_for_display();
        return ['taxonomy' => $settings['taxonomy'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'hide_empty' => 'yes' === $settings['hide_empty'], 'number' => $settings['number'], 'exclude' => $settings['exclude_terms_' . $settings['taxonomy'] ?? []]];
    }
    /**
     * Separator
     *
     * @param string $choice
     * @return string
     */
    protected function separator(string $choice)
    {
        switch ($choice) {
            case 'line_break':
                return '<br />';
            case 'new_line':
                return "\n";
            case 'comma':
                return ', ';
        }
        return '';
    }
}
