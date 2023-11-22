<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class PodsRelationship extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-relationship'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_type(), 'pods_relation_label');
        $save_guard->register_unsafe_control($this->get_type(), 'pods_relation_text');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Content', 'dynamic-content-for-elementor')]);
        $this->add_control('pods_relation_field', ['label' => __('PODS Relationship field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'pods', 'object_type' => 'relationship']);
        $this->add_control('pods_relation_render', ['label' => __('Render mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['title' => ['title' => __('Title', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'text' => ['title' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'title', 'separator' => 'before']);
        $this->add_control('pods_relation_template', ['label' => __('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['pods_relation_render' => 'template']]);
        $this->add_control('pods_relation_text', ['label' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '<h4>[post:title|esc_html]</h4>[post:thumb]<p>[post:excerpt]</p><a class="btn btn-primary" href="[post:permalink]">READ MORE</a>', 'dynamic' => ['active' => \true], 'condition' => ['pods_relation_render' => 'text']]);
        $this->add_control('pods_relation_format', ['label' => __('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Natural', 'dynamic-content-for-elementor'), 'ul' => __('Unordered List', 'dynamic-content-for-elementor'), 'ol' => __('Ordered List', 'dynamic-content-for-elementor'), 'grid' => __('Grid', 'dynamic-content-for-elementor'), 'tab' => __('Tabs', 'dynamic-content-for-elementor'), 'accordion' => __('Accordion', 'dynamic-content-for-elementor'), 'select' => __('Select', 'dynamic-content-for-elementor')]]);
        $this->add_control('pods_relation_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h2']);
        $this->add_control('pods_relation_link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['pods_relation_render' => 'title']]);
        $this->add_control('pods_relation_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[post:title|esc_html]', 'placeholder' => '[post:title|esc_html]', 'condition' => ['pods_relation_format' => ['tab', 'accordion', 'select']]]);
        $this->add_control('pods_relation_close', ['label' => __('Close by default', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['pods_relation_format' => ['accordion', 'select']]]);
        $this->add_control('pods_relation_close_label', ['label' => __('Empty value text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Choose an option', 'dynamic-content-for-elementor'), 'condition' => ['pods_relation_close!' => '', 'pods_relation_format' => 'select']]);
        $this->add_responsive_control('pods_relation_col', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'condition' => ['pods_relation_format' => 'grid']]);
        $this->add_control('pods_relation_tab', ['label' => __('Tab orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['horizontal' => ['title' => __('Horizontal', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-chevron-up'], 'vertical' => ['title' => __('Vertical', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-chevron-left']], 'toggle' => \false, 'default' => 'horizontal', 'condition' => ['pods_relation_format' => 'tab']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('pods_relation_title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pods_relation_title_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('pods_relation_title_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'text-align: {{VALUE}};']]);
        $this->add_control('pods_relation_title_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pods_relation_title_typography', 'selector' => '{{WRAPPER}} .elementor-heading-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'pods_relation_title_text_shadow', 'selector' => '{{WRAPPER}} .elementor-heading-title']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_atitle', ['label' => __('Title Active', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pods_relation_format' => 'tab']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'pods_relation_bgcolor_aitem', 'label' => __('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-tab-item.dce-tab-item-active']);
        $this->add_control('pods_relation_color_aitem', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-tab-item.dce-tab-item-active .elementor-heading-title' => 'color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_item', ['label' => __('Item', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pods_relation_format' => ['accordion', 'tab']]]);
        $this->add_control('pods_relation_padding_item', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pods_relation_border_item', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-item']);
        $this->add_control('pods_relation_border_radius_item', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'pods_relation_bgcolor_item', 'label' => __('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-item']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_pane', ['label' => __('Pane', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['pods_relation_format' => ['accordion', 'tab', 'grid', 'select', 'ul', 'ol']]]);
        $this->add_control('pods_relation_padding_pane', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pods_relation_margin_pane', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pods_relation_border_pane', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-pane']);
        $this->add_control('pods_relation_border_radius_pane', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pods_relation_color_pane', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'pods_relation_bgcolor_pane', 'label' => __('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-pane']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['pods_relation_field'])) {
            return;
        }
        global $post;
        $old_post = $post;
        if (pods(get_post_type(), get_the_ID())) {
            $related_posts = pods_field_raw($settings['pods_relation_field']);
        }
        if (empty($related_posts)) {
            return;
        }
        if (\is_numeric($related_posts)) {
            $related_posts = array($related_posts);
        } elseif (isset($related_posts['ID'])) {
            $related_posts = array($related_posts['ID']);
        } elseif (\is_array($related_posts)) {
            $related_posts = wp_list_pluck($related_posts, 'ID');
        }
        if (\count($related_posts) > 1 && $settings['pods_relation_format']) {
            $labels = [];
            if (\in_array($settings['pods_relation_format'], ['tab', 'accordion', 'select'])) {
                foreach ($related_posts as $arel) {
                    $post = get_post($arel);
                    $labels[$post->ID] = \DynamicContentForElementor\Helper::get_dynamic_value($settings['pods_relation_label']);
                }
            }
            switch ($settings['pods_relation_format']) {
                case 'ul':
                    echo '<ul class="dce-pods-relational-list">';
                    break;
                case 'ol':
                    echo '<ol class="dce-pods-relational-list">';
                    break;
                case 'grid':
                    echo '<div class="dce-view-row grid-page grid-col-md-' . $settings['pods_relation_col'] . ' grid-col-sm-' . $settings['pods_relation_col_tablet'] . ' grid-col-xs-' . $settings['pods_relation_col_mobile'] . '">';
                    break;
                case 'tab':
                    echo '<div class="dce-view-tab dce-tab dce-tab-' . $settings['pods_relation_tab'] . '"><ul>';
                    $i = 0;
                    foreach ($labels as $pkey => $alabel) {
                        ?>
						<li>
							<a class="dce-view-item dce-tab-item<?php 
                        echo !$i ? ' dce-tab-item-active' : '';
                        ?>" href="#dce-pods-relational-post-<?php 
                        echo $this->get_id() . '-' . $pkey;
                        ?>" onclick="jQuery('.elementor-element-<?php 
                        echo $this->get_id();
                        ?> .dce-pods-relational-post').hide();jQuery('.elementor-element-<?php 
                        echo $this->get_id();
                        ?> .dce-tab-item-active').removeClass('dce-tab-item-active');jQuery(jQuery(this).attr('href')).show();jQuery(this).addClass('dce-tab-item-active'); return false;">
								<<?php 
                        echo \DynamicContentForElementor\Helper::validate_html_tag($settings['pods_relation_tag']);
                        ?> class="elementor-heading-title">
								<?php 
                        echo $alabel;
                        ?>
								</<?php 
                        echo \DynamicContentForElementor\Helper::validate_html_tag($settings['pods_relation_tag']);
                        ?>>
							</a>
						</li>
						<?php 
                        $i++;
                    }
                    echo '</ul><div class="dce-tab-content">';
                    break;
                case 'select':
                    ?>
					<select class="elementor-heading-title dce-view-select" onchange="jQuery('.elementor-element-<?php 
                    echo $this->get_id();
                    ?> .dce-pods-relational-post').slideUp();jQuery(jQuery(this).val()).slideDown();">
						<?php 
                    if ($settings['pods_relation_close'] && $settings['pods_relation_close_label']) {
                        echo '<option value="#dce-view-no-show">' . $settings['pods_relation_close_label'] . '</option>';
                    }
                    foreach ($labels as $pkey => $alabel) {
                        echo '<option value="#dce-pods-relational-post-' . $this->get_id() . '-' . $pkey . '">' . $alabel . '</option>';
                    }
                    ?>
					</select>
					<div class="dce-select-content">
						<?php 
                    break;
            }
        }
        foreach ($related_posts as $rkey => $arel) {
            $post = get_post($arel);
            if (\count($related_posts) > 1) {
                switch ($settings['pods_relation_format']) {
                    case 'ul':
                    case 'ol':
                        echo '<li class="dce-view-pane dce-pods-relational-post dce-pods-relational-post-' . $post->ID . '">';
                        break;
                    default:
                        if ($settings['pods_relation_format'] == 'accordion' && $settings['pods_relation_render'] != 'title') {
                            ?>
								<div class="dce-accordion-item">
									<a class="dce-view-item" href="#dce-pods-relational-post-<?php 
                            echo $this->get_id() . '-' . $post->ID;
                            ?>" onclick="if (!jQuery(jQuery(this).attr('href')).is(':visible')) {
																					jQuery('.elementor-element-<?php 
                            echo $this->get_id();
                            ?> .dce-pods-relational-post').slideUp();
																					jQuery(jQuery(this).attr('href')).slideDown();
																				} else {
																					jQuery(jQuery(this).attr('href')).slideUp();
																				} return false;">
										<<?php 
                            echo \DynamicContentForElementor\Helper::validate_html_tag($settings['pods_relation_tag']);
                            ?> class="elementor-heading-title">
									<?php 
                            echo $labels[$post->ID];
                            ?>
										</<?php 
                            echo \DynamicContentForElementor\Helper::validate_html_tag($settings['pods_relation_tag']);
                            ?>>
									</a>
								</div>
							<?php 
                        }
                        $is_hidden = \false;
                        if (\in_array($settings['pods_relation_format'], array('accordion', 'select'))) {
                            // && $settings['pods_relation_render'] != 'title') {
                            if ($settings['pods_relation_close'] && !$rkey || $rkey) {
                                $is_hidden = \true;
                            }
                        }
                        if (\in_array($settings['pods_relation_format'], array('tab')) && $rkey) {
                            $is_hidden = \true;
                        }
                        $pstyle = $is_hidden ? ' style="display: none;"' : '';
                        echo '<div id="dce-pods-relational-post-' . $this->get_id() . '-' . $post->ID . '" class="dce-view-pane dce-' . $settings['pods_relation_format'] . '-pane dce-pods-relational-post dce-pods-relational-post-' . $post->ID . ($settings['pods_relation_format'] == 'grid' ? ' item-page' : '') . '"' . $pstyle . '>';
                        break;
                }
            }
            if ($settings['pods_relation_render'] == 'template' && $settings['pods_relation_template']) {
                echo do_shortcode('[dce-elementor-template id="' . $settings['pods_relation_template'] . '"]');
            } elseif ($settings['pods_relation_render'] == 'text') {
                echo \DynamicContentForElementor\Helper::get_dynamic_value($settings['pods_relation_text']);
            } else {
                if ($settings['pods_relation_link']) {
                    echo '<a class="dce-pods-relational-post-link" href="' . get_permalink($post->ID) . '">';
                }
                echo '<' . \DynamicContentForElementor\Helper::validate_html_tag($settings['pods_relation_tag']) . ' class="elementor-heading-title">' . wp_kses_post(get_the_title($post->ID)) . '</' . \DynamicContentForElementor\Helper::validate_html_tag($settings['pods_relation_tag']) . '>';
                if ($settings['pods_relation_link']) {
                    echo '</a>';
                }
            }
            if (\count($related_posts) > 1) {
                switch ($settings['pods_relation_format']) {
                    case 'ul':
                    case 'ol':
                        echo '</li>';
                        break;
                    default:
                        echo '</div>';
                        break;
                }
            }
        }
        if (\count($related_posts) > 1 && $settings['pods_relation_format']) {
            switch ($settings['pods_relation_format']) {
                case 'ul':
                    echo '</ul>';
                    break;
                case 'ol':
                    echo '</ol>';
                    break;
                case 'tab':
                    echo '</div>';
                case 'grid':
                case 'select':
                    echo '</div>';
                    break;
            }
        }
        wp_reset_postdata();
        $post = $old_post;
        setup_postdata($old_post);
    }
}
