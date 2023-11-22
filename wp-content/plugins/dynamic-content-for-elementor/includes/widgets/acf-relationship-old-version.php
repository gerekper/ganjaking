<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfRelationshipOldVersion extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-acf-relationship-old-version'];
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
        $save_guard->register_unsafe_control($this->get_type(), 'acf_relation_label');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Content', 'dynamic-content-for-elementor')]);
        $this->add_control('deprecated', ['raw' => __('This widget is deprecated. You can continue to use it but we recommend that you use ACF Relationship widget new version instead', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $this->add_control('acf_relation_field', ['label' => __('ACF Relationship field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false], 'object_type' => 'post_object,relationship']);
        $this->add_control('acf_relation_from', ['label' => __('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => __('Current Post', 'dynamic-content-for-elementor'), 'current_user' => __('Current User', 'dynamic-content-for-elementor'), 'current_author' => __('Current Author', 'dynamic-content-for-elementor'), 'current_term' => __('Current Term', 'dynamic-content-for-elementor'), 'options_page' => __('Options Page', 'dynamic-content-for-elementor')]]);
        $this->add_control('acf_relation_taxonomy', ['label' => __('Retrieve the current term from this taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => get_taxonomies(['public' => \true]), 'default' => 'category', 'label_block' => \true, 'condition' => ['acf_relation_from' => 'current_term']]);
        $this->add_control('acf_relation_invert', ['label' => __('Invert direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('For bidirectional relationships, retrieve all posts that are associated with the current post', 'dynamic-content-for-elementor'), 'condition' => ['acf_relation_from' => 'current_post']]);
        $this->add_control('acf_relation_draft', ['label' => __('Ignore drafts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('acf_relation_num_posts', ['label' => __('Number of Posts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '0']);
        $this->add_control('acf_relation_orderby', ['label' => __('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => \array_merge(['post__in' => __('Same order of ACF Relationship', 'dynamic-content-for-elementor')], Helper::get_post_orderby_options()), 'default' => 'post__in']);
        $this->add_control('acf_relation_metakey', ['label' => __('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'condition' => ['acf_relation_orderby' => ['meta_value_date', 'meta_value_num', 'meta_value']]]);
        $this->add_control('acf_relation_order', ['label' => __('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => 'Ascending', 'DESC' => 'Descending'], 'default' => 'DESC', 'condition' => ['acf_relation_orderby!' => 'post__in']]);
        $this->add_control('acf_relation_render', ['label' => __('Render mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['title' => ['title' => __('Title', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'text' => ['title' => __('Text editor', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'title', 'separator' => 'before']);
        $this->add_control('acf_relation_template', ['label' => __('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['acf_relation_render' => 'template']]);
        $this->add_control('acf_relation_text', ['label' => __('Post HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => '<h4>[post:title|esc_html]</h4>[post:thumb]<p>[post:excerpt]</p><a class="btn btn-primary" href="[post:permalink]">' . __('Read more', 'dynamic-content-for-elementor') . '</a>', 'description' => __('Define related post structure', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['acf_relation_render' => 'text']]);
        $this->add_control('acf_relation_format', ['label' => __('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Natural', 'dynamic-content-for-elementor'), 'ul' => __('Unordered List', 'dynamic-content-for-elementor'), 'ol' => __('Ordered List', 'dynamic-content-for-elementor'), 'grid' => __('Grid', 'dynamic-content-for-elementor'), 'tab' => __('Tabs', 'dynamic-content-for-elementor'), 'accordion' => __('Accordion', 'dynamic-content-for-elementor'), 'select' => __('Select', 'dynamic-content-for-elementor')]]);
        $this->add_control('acf_relation_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h2']);
        $this->add_control('acf_relation_link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['acf_relation_render' => 'title']]);
        $this->add_control('acf_relation_separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['acf_relation_format' => '']]);
        $this->add_control('acf_relation_label', ['label' => __('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[post:title|esc_html]', 'placeholder' => '[post:title|esc_html]', 'condition' => ['acf_relation_format' => ['tab', 'accordion', 'select']]]);
        $this->add_control('acf_relation_close', ['label' => __('Closed by default', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['acf_relation_format' => ['accordion', 'select']]]);
        $this->add_control('acf_relation_close_label', ['label' => __('Empty value text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Choose an option', 'dynamic-content-for-elementor'), 'condition' => ['acf_relation_close!' => '', 'acf_relation_format' => 'select']]);
        $this->add_responsive_control('acf_relation_col', ['label' => __('Columns', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 6, 'description' => __('Set 1 to show one result per line', 'dynamic-content-for-elementor'), 'condition' => ['acf_relation_format' => 'grid']]);
        $this->add_responsive_control('acf_relation_col_align', ['label' => __('Columns Align', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .grid-page' => 'justify-content: {{VALUE}};'], 'condition' => ['acf_relation_format' => 'grid']]);
        $this->add_control('acf_relation_tab', ['label' => __('Tab orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['horizontal' => ['title' => __('Horizontal', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-chevron-up'], 'vertical' => ['title' => __('Vertical', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-chevron-left']], 'toggle' => \false, 'default' => 'horizontal', 'condition' => ['acf_relation_format' => 'tab']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('acf_relation_title_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('acf_relation_title_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('acf_relation_title_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-heading-title, {{WRAPPER}} .dce-acf-relationship-natural' => 'text-align: {{VALUE}};']]);
        $this->add_control('acf_relation_title_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => [
            // Stronger selector to avoid section style from overwriting
            '{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
        ]]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'acf_relation_title_typography', 'selector' => '{{WRAPPER}} .elementor-heading-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'acf_relation_title_text_shadow', 'selector' => '{{WRAPPER}} .elementor-heading-title']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_atitle', ['label' => __('Title Active', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_relation_format' => 'tab']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'acf_relation_bgcolor_aitem', 'label' => __('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-tab-item.dce-tab-item-active']);
        $this->add_control('acf_relation_color_aitem', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-tab-item.dce-tab-item-active .elementor-heading-title' => 'color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_item', ['label' => __('Item', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_relation_format' => ['accordion', 'tab']]]);
        $this->add_control('acf_relation_padding_item', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'acf_relation_border_item', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-item']);
        $this->add_control('acf_relation_border_radius_item', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'acf_relation_bgcolor_item', 'label' => __('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-item']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_pane', ['label' => __('Pane', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['acf_relation_format' => ['accordion', 'tab', 'grid', 'select', 'ul', 'ol']]]);
        $this->add_control('acf_relation_padding_pane', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('acf_relation_margin_pane', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'acf_relation_border_pane', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-pane']);
        $this->add_control('acf_relation_border_radius_pane', ['label' => __('Border radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('acf_relation_color_pane', ['label' => __('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'acf_relation_bgcolor_pane', 'label' => __('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-pane']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (empty($settings['acf_relation_field'])) {
            return;
        }
        global $post;
        $old_post = $post;
        if ($settings['acf_relation_invert']) {
            $rel_posts = Helper::get_acf_field_value_relationship_invert($settings['acf_relation_field']);
        } else {
            if ($settings['acf_relation_from'] == 'current_post') {
                $rel_posts = get_post_meta(get_the_ID(), $settings['acf_relation_field'], \true);
            } elseif ($settings['acf_relation_from'] == 'current_author') {
                $rel_posts = get_the_author_meta($settings['acf_relation_field']);
            } elseif ($settings['acf_relation_from'] == 'current_user') {
                $rel_posts = get_user_meta(get_current_user_id(), $settings['acf_relation_field'], \true);
            } elseif ($settings['acf_relation_from'] == 'current_term') {
                $terms = get_the_terms(get_the_ID(), $settings['acf_relation_taxonomy']);
                $rel_posts = get_term_meta($terms[0]->term_id, $settings['acf_relation_field'], \true);
            } elseif ($settings['acf_relation_from'] == 'options_page') {
                $rel_posts_objects = \get_field($settings['acf_relation_field'], 'option');
                $rel_posts_ids = [];
                foreach ($rel_posts_objects as $rel_post) {
                    \array_push($rel_posts_ids, $rel_post->ID);
                }
                $rel_posts = $rel_posts_ids;
            }
            if (empty($rel_posts)) {
                $rel_posts = Helper::get_acf_field_value($settings['acf_relation_field']);
            }
        }
        if (empty($rel_posts)) {
            return;
        }
        if (!\is_array($rel_posts)) {
            $rel_posts = [$rel_posts];
        }
        if (Helper::is_wpml_active()) {
            // WPML Translation
            $rel_posts = Helper::wpml_translate_object_id($rel_posts);
        }
        if ($settings['acf_relation_num_posts']) {
            $num_posts = $settings['acf_relation_num_posts'];
        } else {
            $num_posts = '0';
        }
        if ($settings['acf_relation_order']) {
            $order = $settings['acf_relation_order'];
        } else {
            $order = 'DESC';
        }
        $acf_relation_orderby = 'post__in';
        if ($settings['acf_relation_orderby']) {
            $acf_relation_orderby = $settings['acf_relation_orderby'];
        }
        $post_status = ['publish', 'draft'];
        if ($settings['acf_relation_draft']) {
            $post_status = 'publish';
        }
        if ('attachment' === get_post_type($rel_posts[0])) {
            $args = ['post_type' => 'attachment', 'posts_per_page' => $num_posts, 'post__in' => $rel_posts, 'post_status' => 'inherit', 'orderby' => $acf_relation_orderby, 'order' => $order, 'meta_key' => $settings['acf_relation_metakey']];
        } else {
            $args = ['post_type' => 'any', 'posts_per_page' => $num_posts, 'post__in' => $rel_posts, 'post_status' => $post_status, 'orderby' => $acf_relation_orderby, 'order' => $order, 'meta_key' => $settings['acf_relation_metakey']];
        }
        $relationship_results = get_posts($args);
        $relationship_count = \count($relationship_results);
        $i = 1;
        if ($settings['acf_relation_format']) {
            $labels = [];
            foreach ($relationship_results as $post) {
                $labels[$post->ID] = Helper::get_dynamic_value($settings['acf_relation_label']);
            }
            switch ($settings['acf_relation_format']) {
                case 'ul':
                    echo '<ul class="dce-acf-relational-list">';
                    break;
                case 'ol':
                    echo '<ol class="dce-acf-relational-list">';
                    break;
                case 'grid':
                    echo '<div class="dce-view-row grid-page grid-col-md-' . $settings['acf_relation_col'] . ' grid-col-sm-' . ($settings['acf_relation_col_tablet'] ?? '') . ' grid-col-xs-' . ($settings['acf_relation_col_mobile'] ?? '') . '">';
                    break;
                case 'tab':
                    echo '<div class="dce-view-tab dce-tab dce-tab-' . $settings['acf_relation_tab'] . '"><ul>';
                    $i = 0;
                    foreach ($labels as $pkey => $alabel) {
                        ?>
							<li>
								<a class="dce-view-item dce-tab-item<?php 
                        echo !$i ? ' dce-tab-item-active' : '';
                        ?>" href="#dce-acf-relational-post-<?php 
                        echo $this->get_id() . '-' . $pkey;
                        ?>" onclick="jQuery('.elementor-element-<?php 
                        echo $this->get_id();
                        ?> .dce-acf-relational-post').hide();jQuery('.elementor-element-<?php 
                        echo $this->get_id();
                        ?> .dce-tab-item-active').removeClass('dce-tab-item-active');jQuery(jQuery(this).attr('href')).show();jQuery(this).addClass('dce-tab-item-active'); return false;">
									<<?php 
                        echo \DynamicContentForElementor\Helper::validate_html_tag($settings['acf_relation_tag']);
                        ?> class="elementor-heading-title">
							<?php 
                        echo $alabel;
                        ?>
									</<?php 
                        echo \DynamicContentForElementor\Helper::validate_html_tag($settings['acf_relation_tag']);
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
                    ?> .dce-acf-relational-post').slideUp();jQuery(jQuery(this).val()).slideDown();">
						<?php 
                    if ($settings['acf_relation_close'] && $settings['acf_relation_close_label']) {
                        echo '<option value="#dce-view-no-show">' . $settings['acf_relation_close_label'] . '</option>';
                    }
                    foreach ($labels as $pkey => $alabel) {
                        echo '<option value="#dce-acf-relational-post-' . $this->get_id() . '-' . $pkey . '">' . $alabel . '</option>';
                    }
                    ?>
						</select>
						<div class="dce-select-content">
						<?php 
                    break;
            }
        }
        if (!$settings['acf_relation_format']) {
            echo '<div class="dce-acf-relationship-natural">';
        }
        foreach ($relationship_results as $post) {
            setup_postdata($post);
            if ($settings['acf_relation_format']) {
                switch ($settings['acf_relation_format']) {
                    case 'ul':
                    case 'ol':
                        echo '<li class="dce-view-pane dce-acf-relational-post dce-acf-relational-post-' . get_the_ID() . '">';
                        break;
                    default:
                        if ($settings['acf_relation_format'] == 'accordion' && $settings['acf_relation_render'] != 'title') {
                            ?>

							<div class="dce-accordion-item">
								<a class="dce-view-item" href="#dce-acf-relational-post-<?php 
                            echo $this->get_id() . '-' . get_the_ID();
                            ?>" onclick="if (!jQuery(jQuery(this).attr('href')).is(':visible')) {
																				jQuery('.elementor-element-<?php 
                            echo $this->get_id();
                            ?> .dce-acf-relational-post').slideUp();
																				jQuery(jQuery(this).attr('href')).slideDown();
																			} else {
																				jQuery(jQuery(this).attr('href')).slideUp();
																			} return false;">
									<<?php 
                            echo \DynamicContentForElementor\Helper::validate_html_tag($settings['acf_relation_tag']);
                            ?> class="elementor-heading-title">
									<?php 
                            if (isset($labels[$post->ID])) {
                                echo $labels[$post->ID];
                            }
                            ?>
									</<?php 
                            echo \DynamicContentForElementor\Helper::validate_html_tag($settings['acf_relation_tag']);
                            ?>>
								</a>
							</div>
							  <?php 
                        }
                        $is_hidden = \false;
                        if (\in_array($settings['acf_relation_format'], ['accordion'])) {
                            if ($settings['acf_relation_close'] && $settings['acf_relation_render'] != 'title') {
                                $is_hidden = \true;
                            }
                        }
                        if (\in_array($settings['acf_relation_format'], ['select'])) {
                            if ($settings['acf_relation_close']) {
                                $is_hidden = \true;
                            }
                        }
                        if (\in_array($settings['acf_relation_format'], ['tab'])) {
                            $is_hidden = \true;
                        }
                        $pstyle = $is_hidden ? ' style="display: none;"' : '';
                        echo '<div id="dce-acf-relational-post-' . $this->get_id() . '-' . get_the_ID() . '" class="dce-view-pane dce-' . $settings['acf_relation_format'] . '-pane dce-acf-relational-post dce-acf-relational-post-' . get_the_ID() . ($settings['acf_relation_format'] == 'grid' ? ' item-page' : '') . '"' . $pstyle . '>';
                        break;
                }
            }
            if ($settings['acf_relation_render'] == 'template' && $settings['acf_relation_template']) {
                echo do_shortcode('[dce-elementor-template id="' . $settings['acf_relation_template'] . '" post_id="' . get_the_id() . '"]');
            } elseif ($settings['acf_relation_render'] == 'text') {
                echo \DynamicContentForElementor\Helper::get_dynamic_value($settings['acf_relation_text']);
            } else {
                if ($settings['acf_relation_link']) {
                    echo '<a class="dce-acf-relational-post-link" href="' . get_permalink() . '">';
                }
                echo '<' . \DynamicContentForElementor\Helper::validate_html_tag($settings['acf_relation_tag']) . ' class="elementor-heading-title">' . wp_kses_post(get_the_title()) . '</' . \DynamicContentForElementor\Helper::validate_html_tag($settings['acf_relation_tag']) . '>';
                if ($settings['acf_relation_link']) {
                    echo '</a>';
                }
                if (!$settings['acf_relation_format'] && $i < $relationship_count) {
                    echo wp_kses_post($settings['acf_relation_separator']);
                }
            }
            if ($relationship_count && $settings['acf_relation_format']) {
                switch ($settings['acf_relation_format']) {
                    case 'ul':
                    case 'ol':
                        echo '</li>';
                        break;
                    default:
                        echo '</div>';
                        break;
                }
            }
            $i++;
        }
        if (!$settings['acf_relation_format']) {
            echo '</div>';
        }
        if ($relationship_count && $settings['acf_relation_format']) {
            switch ($settings['acf_relation_format']) {
                case 'ul':
                    echo '</ul>';
                    break;
                case 'ol':
                    echo '</ol>';
                    break;
                case 'tab':
                    echo '</div>';
                // no break
                case 'grid':
                case 'select':
                    echo '</div>';
                    break;
            }
        }
        if (!empty($relationship_results)) {
            wp_reset_postdata();
            $post = $old_post;
        }
    }
}
