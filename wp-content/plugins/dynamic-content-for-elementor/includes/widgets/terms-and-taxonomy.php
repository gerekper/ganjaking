<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class TermsAndTaxonomy extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-terms'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Terms', 'dynamic-content-for-elementor')]);
        $this->add_control('taxonomy', ['label' => __('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['auto' => __('Dynamic', 'dynamic-content-for-elementor')] + get_taxonomies(), 'default' => 'category']);
        $this->add_control('only_parent_terms', ['label' => __('Show', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['both' => ['title' => __('Both', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tags'], 'yes' => ['title' => __('Parents', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-sitemap'], 'children' => ['title' => __('Children', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-child']], 'toggle' => \false, 'default' => 'both']);
        $this->add_control('separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => ', ']);
        $this->add_control('use_termdescription', ['label' => __('Show term description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'return_value' => 'yes']);
        $this->add_control('heading_spaces', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('space', ['label' => __('Separator Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-separator' => 'padding: 0 {{SIZE}}{{UNIT}};'], 'condition' => ['separator!' => '']]);
        $this->add_responsive_control('terms_space', ['label' => __('Items Horizontal Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-terms ul li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('terms_space_vertical', ['label' => __('Items Vertical Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-terms ul li' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_control('text_before', ['label' => __('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'separator' => 'before', 'default' => '']);
        $this->add_control('text_after', ['label' => __('Text After', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('text_before_after_position', ['label' => __('Text in the same row', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['row' => __('Yes', 'dynamic-content-for-elementor'), 'column' => __('No', 'dynamic-content-for-elementor')], 'default' => 'block', 'selectors' => ['{{WRAPPER}} .dce-terms' => 'display: flex; flex-direction: {{VALUE}};', '{{WRAPPER}} .dce-terms span.text-before, {{WRAPPER}} .dce-terms span.text-after' => 'display: flex']]);
        $this->add_responsive_control('align', ['label' => __('Block Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => '', 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .dce-terms, {{WRAPPER}} .dce-terms .text-before, {{WRAPPER}} .dce-terms .text-after, {{WRAPPER}} .dce-terms ul, {{WRAPPER}} .dce-terms ul.dce-image-block li' => 'justify-content: {{VALUE}};']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'term' => __('Term', 'dynamic-content-for-elementor')]]);
        $this->end_controls_section();
        if (Helper::is_acf_active()) {
            $this->start_controls_section('section_image', ['label' => __('ACF', 'dynamic-content-for-elementor')]);
            $this->add_control('image_acf_enable', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
            $this->add_control('acf_field_image', ['label' => __('Image Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'term', 'condition' => ['image_acf_enable!' => '']]);
            $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'imgsize', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'render_type' => 'template', 'condition' => ['image_acf_enable' => 'yes']]);
            $this->add_control('block_enable', ['label' => __('Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dce-terms img' => 'display: {{VALUE}};'], 'render_type' => 'template', 'condition' => ['image_acf_enable' => 'yes']]);
            $this->add_responsive_control('block_grid', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'tablet_default' => '3', 'mobile_default' => '1', 'options' => ['' => 'Auto', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'], 'selectors' => ['{{WRAPPER}} .dce-image-block li' => 'flex: 0 1 calc( 100% / {{VALUE}} );'], 'condition' => ['block_enable!' => '']]);
            $this->add_responsive_control('image_acf_size', ['label' => __('Size (Max-Width)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'range' => ['px' => ['min' => 1, 'max' => 800]], 'selectors' => ['{{WRAPPER}} .dce-terms img' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['image_acf_enable' => 'yes']]);
            $this->add_responsive_control('image_acf_space', ['label' => __('Shift X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-terms img' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['image_acf_enable' => 'yes']]);
            $this->add_responsive_control('image_acf_shift', ['label' => __('Shift Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-terms img' => 'margin-bottom: {{SIZE}}{{UNIT}};'], 'condition' => ['image_acf_enable' => 'yes']]);
            $this->add_control('color_acf_enable', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
            $this->add_control('acf_field_color', ['label' => __('ACF Field Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'condition' => ['color_acf_enable!' => '']]);
            $this->add_control('acf_field_color_hover', ['label' => __('ACF Field Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'condition' => ['color_acf_enable!' => '', 'acf_field_color!' => '', 'link_to!' => 'none']]);
            $this->add_control('acf_field_color_dyn', ['label' => __('Color Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'term', 'condition' => ['color_acf_enable!' => '']]);
            $this->add_control('acf_field_color_hover_dyn', ['label' => __('Color Hover Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'term', 'condition' => ['color_acf_enable!' => '', 'acf_field_color_dyn!' => ['', null]]]);
            $this->add_control('acf_field_color_mode', ['label' => __('Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-font'], 'background' => ['title' => __('Background', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-square'], 'border' => ['title' => __('Border Bottom', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-minus']], 'toggle' => \false, 'default' => 'text', 'condition' => ['color_acf_enable!' => '']]);
            $this->add_responsive_control('acf_field_colorbg_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'rem'], 'default' => [], 'selectors' => ['{{WRAPPER}} .dce-term-item.dce-term-mode-background' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['color_acf_enable!' => '', 'acf_field_color_mode' => ['background']]]);
            $this->add_control('acf_field_colorborderradius_width', ['label' => __('Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-term-item.dce-term-mode-background' => 'border-radius: {{SIZE}}{{UNIT}};'], 'condition' => ['color_acf_enable!' => '', 'acf_field_color_mode' => ['background']]]);
            $this->add_control('acf_field_colorborder_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-term-item.dce-term-mode-border' => 'border-bottom-width: {{SIZE}}{{UNIT}};'], 'condition' => ['color_acf_enable!' => '', 'acf_field_color_mode' => ['border']]]);
            $this->end_controls_section();
        }
        $this->start_controls_section('section_style', ['label' => __('Terms', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-terms' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-terms a' => 'color: {{VALUE}};']]);
        $this->add_control('color_hover', ['label' => __('Text Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-terms a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('color_separator', ['label' => __('Separator color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-separator' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-terms']);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('description_heading', ['label' => __('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['use_termdescription!' => '']]);
        $this->add_control('decription_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-terms .dce-term-description' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-terms .dce-term-description a' => 'color: {{VALUE}};'], 'condition' => ['use_termdescription!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => __('typography_description', 'dynamic-content-for-elementor'), 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-terms .dce-term-description', 'condition' => ['use_termdescription!' => '']]);
        $this->add_responsive_control('decription_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-terms .dce-term-description' => 'margin-top: {{SIZE}}{{UNIT}};'], 'condition' => ['use_termdescription!' => '']]);
        $this->add_control('txbefore_heading', ['label' => __('Text Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['text_before!' => '']]);
        $this->add_control('tx_before_color', ['label' => __('Text Before Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-terms span.text-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-terms a span.text-before' => 'color: {{VALUE}};'], 'condition' => ['text_before!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_before', 'label' => __('Typography Before', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-terms span.text-before', 'condition' => ['text_before!' => '']]);
        $this->add_control('txafter_heading', ['label' => __('Text After', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['text_after!' => '']]);
        $this->add_control('tx_after_color', ['label' => __('Text After Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-terms span.text-after' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-terms a span.text-after' => 'color: {{VALUE}};'], 'condition' => ['text_after!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_after', 'label' => __('Typography After', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-terms span.text-after', 'condition' => ['text_after!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('Other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
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
        $taxonomy = [];
        if (empty($settings['taxonomy'])) {
            return;
        }
        if ('auto' === $settings['taxonomy']) {
            // Taxonomy set to "Dynamic"
            $taxonomy = get_post_taxonomies($id_page);
        } else {
            // Taxonomy set manually
            $taxonomy = $settings['taxonomy'];
        }
        $terms = \DynamicContentForElementor\Helper::get_the_terms_ordered($id_page, $taxonomy);
        if (empty($terms) || is_wp_error($terms)) {
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return;
            }
            Helper::notice('', __('This is a dummy content to help you choose the style and settings', 'dynamic-content-for-elementor'));
            $terms = get_terms(['taxonomy' => 'category', 'hide_empty' => \false]);
        }
        $separator = '';
        $this->set_render_attribute('wrapper', 'class', 'dce-terms');
        if (!empty($settings['hover_animation'])) {
            $this->add_render_attribute('wrapper', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>

		<?php 
        if (!empty($settings['text_before'])) {
            // Text before
            $this->set_render_attribute('text-before', 'class', 'text-before');
            ?>
			<span <?php 
            echo $this->get_render_attribute_string('text-before');
            ?>>
				<?php 
            echo sanitize_text_field($settings['text_before']) . '&nbsp;';
            ?>
			</span>
		<?php 
        }
        if (!empty($settings['block_enable'])) {
            $this->set_render_attribute('ul', 'class', 'dce-image-block');
        } else {
            $this->set_render_attribute('ul', 'class', 'dce-image-inline');
        }
        ?>

		<ul <?php 
        echo $this->get_render_attribute_string('ul');
        ?>>

		<?php 
        $i = 1;
        foreach ($terms as $term) {
            if (!empty($settings['only_parent_terms'])) {
                if ('yes' === $settings['only_parent_terms'] && $term->parent) {
                    continue;
                }
                if ('children' === $settings['only_parent_terms'] && !$term->parent) {
                    continue;
                }
            }
            $field_type = '';
            $image_src = '';
            ?>

			<li>


			<?php 
            $this->set_render_attribute('term-item', 'class', 'dce-term-item');
            $this->add_render_attribute('term-item', 'class', 'term' . $term->term_id);
            if (Helper::is_acf_active()) {
                if (!empty($settings['image_acf_enable']) && !empty($settings['acf_field_image'])) {
                    $image_field = get_term_meta($term->term_id, $settings['acf_field_image'], \true);
                    if ($image_field) {
                        if (\is_numeric($image_field)) {
                            $field_type = 'image';
                            $image_src = Group_Control_Image_Size::get_attachment_image_src($image_field, 'imgsize', $settings);
                        } elseif (\is_string($image_field)) {
                            $field_type = 'image_url';
                            $image_src = $image_field;
                        } elseif (\is_array($image_field)) {
                            $field_type = 'image_array';
                            $image_src = Group_Control_Image_Size::get_attachment_image_src($image_field['ID'], 'imgsize', $settings);
                        }
                    }
                    if ($image_src) {
                        $this->set_render_attribute('term-wrapper', 'class', 'dce-term-wrap');
                        ?>
						<span <?php 
                        echo $this->get_render_attribute_string('term-wrapper');
                        ?>>
						<img src="<?php 
                        echo esc_url($image_src);
                        ?>" />
					<?php 
                    }
                }
                if (!empty($settings['color_acf_enable'])) {
                    $field_color_mode = $settings['acf_field_color_mode'];
                    // Normal Color
                    $field_color = \false;
                    if ($settings['acf_field_color_dyn']) {
                        $field_color = get_term_meta($term->term_id, $settings['acf_field_color_dyn'], \true);
                    } elseif ($settings['acf_field_color']) {
                        $idField_color = $settings['acf_field_color'];
                        $field_color = \get_field($idField_color, 'term_' . $term->term_id);
                    }
                    if ($field_color) {
                        if ($field_color_mode == 'text') {
                            $this->set_render_attribute('term-item', 'style', 'color:' . $field_color);
                            $this->add_render_attribute('term-item', 'class', 'dce-term-mode-text');
                        } elseif ($field_color_mode == 'background') {
                            $this->set_render_attribute('term-item', 'style', 'background-color:' . $field_color);
                            $this->add_render_attribute('term-item', 'class', 'dce-term-mode-background');
                        } elseif ($field_color_mode == 'border') {
                            $this->set_render_attribute('term-item', 'style', 'border-bottom-color:' . $field_color);
                            $this->add_render_attribute('term-item', 'class', 'dce-term-mode-border');
                        }
                    }
                    // Hover Color
                    $field_color_hover = \false;
                    if ($settings['acf_field_color_hover_dyn']) {
                        $field_color_hover = get_term_meta($term->term_id, $settings['acf_field_color_hover_dyn'], \true);
                    } elseif ($settings['acf_field_color_hover']) {
                        $idField_color_hover = $settings['acf_field_color_hover'];
                        $field_color_hover = \get_field($idField_color_hover, 'term_' . $term->term_id);
                    }
                    if ($field_color_hover) {
                        if ('text' === $field_color_mode) {
                            $this->set_render_attribute('term-item', 'onmouseover', 'this.style.color=' . $field_color_hover);
                            $this->set_render_attribute('term-item', 'onmouseout', 'this.style.color=' . $field_color);
                        } elseif ('background' === $field_color_mode) {
                            $this->set_render_attribute('term-item', 'onmouseover', 'this.style.background=' . $field_color_hover);
                            $this->set_render_attribute('term-item', 'onmouseout', 'this.style.background=' . $field_color);
                        } elseif ('border' === $field_color_mode) {
                            $this->set_render_attribute('term-item', 'onmouseover', 'this.style.borderBottomColor=' . $field_color_hover);
                            $this->set_render_attribute('term-item', 'onmouseout', 'this.style.borderBottomColor=' . $field_color);
                        }
                    }
                }
            }
            switch ($settings['link_to']) {
                case 'term':
                    ?>
					<a href="<?php 
                    echo esc_url(get_term_link($term));
                    ?>" <?php 
                    echo $this->get_render_attribute_string('term-item');
                    ?>>
						<?php 
                    echo sanitize_text_field($term->name);
                    ?>
					</a>
					<?php 
                    break;
                case 'none':
                default:
                    ?>
					<span <?php 
                    echo $this->get_render_attribute_string('term-item');
                    ?>>
						<?php 
                    echo sanitize_text_field($term->name);
                    ?>
					</span>
					<?php 
                    break;
            }
            if ($settings['use_termdescription']) {
                $this->set_render_attribute('term-description', 'class', 'dce-term-description');
                ?>
				<div <?php 
                echo $this->get_render_attribute_string('term-description');
                ?>>
					<?php 
                echo term_description($term);
                ?>
				</div>
			<?php 
            }
            if ($image_src) {
                ?>
				</span>
			<?php 
            }
            if ($i < \count($terms) && !empty($settings['separator'])) {
                $this->set_render_attribute('separator', 'class', ['dce-term-item', 'dce-separator']);
                ?>
				<span <?php 
                echo $this->get_render_attribute_string('separator');
                ?>>
					<?php 
                $separator = \str_replace(' ', '&nbsp;', $settings['separator']);
                echo wp_kses_post($separator);
                ?>
				</span>
			<?php 
                $i++;
            }
            ?>

			</li>
			<?php 
        }
        ?>
		</ul>

		<?php 
        if (!empty($settings['text_after'])) {
            // Text after
            $this->set_render_attribute('text-after', 'class', 'text-after');
            ?>
			<span <?php 
            echo $this->get_render_attribute_string('text-after');
            ?>>
				<?php 
            echo '&nbsp;' . sanitize_text_field($settings['text_after']);
            ?>
			</span>
		<?php 
        }
        ?>
		</div>
		<?php 
    }
}
