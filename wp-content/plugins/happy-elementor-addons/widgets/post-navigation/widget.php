<?php

/**
 * Post Navigation widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

defined('ABSPATH') || die();

class Post_Navigation extends Base {

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Post Navigation', 'happy-elementor-addons');
    }

    public function get_custom_help_url() {
        return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/post-navigation/';
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-breadcrumbs';
    }

    public function get_keywords() {
        return ['navigation', 'post', 'page', 'next', 'prev', 'previous'];
    }

    public function get_public_post_types($args = []) {
        $post_type_args = [
            // Default is the value $public.
            'show_in_nav_menus' => true,
        ];

        // Keep for backwards compatibility
        if (!empty($args['post_type'])) {
            $post_type_args['name'] = $args['post_type'];
            unset($args['post_type']);
        }

        $post_type_args = wp_parse_args($post_type_args, $args);

        $_post_types = get_post_types($post_type_args, 'objects');

        $post_types = [];

        foreach ($_post_types as $post_type => $object) {
            $post_types[$post_type] = $object->label;
        }

        return $post_types;
    }

    public function get_taxonomies($args = [], $output = 'names', $operator = 'and') {
        global $wp_taxonomies;

        $field = ('names' === $output) ? 'name' : false;

        // Handle 'object_type' separately.
        if (isset($args['object_type'])) {
            $object_type = (array) $args['object_type'];
            unset($args['object_type']);
        }

        $taxonomies = wp_filter_object_list($wp_taxonomies, $args, $operator);

        if (isset($object_type)) {
            foreach ($taxonomies as $tax => $tax_data) {
                if (!array_intersect($object_type, $tax_data->object_type)) {
                    unset($taxonomies[$tax]);
                }
            }
        }

        if ($field) {
            $taxonomies = wp_list_pluck($taxonomies, $field);
        }

        return $taxonomies;
    }

    /**
     * Register widget content controls
     */
    protected function register_content_controls() {
        $this->__post_navigation_controls();
    }

    protected function __post_navigation_controls() {
        $this->start_controls_section(
            '_section_post_navigation',
            [
                'label' => __('Post Navigation', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_label',
            [
                'label' => esc_html__('Label', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'prev_label',
            [
                'label' => esc_html__('Previous Label', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('Previous', 'happy-elementor-addons'),
                'condition' => [
                    'show_label' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'next_label',
            [
                'label' => esc_html__('Next Label', 'happy-elementor-addons'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Next', 'happy-elementor-addons'),
                'condition' => [
                    'show_label' => 'yes',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'show_arrow',
            [
                'label' => esc_html__('Arrows', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'arrow',
            [
                'label' => esc_html__('Arrows Type', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'fa fa-angle-left' => esc_html__('Angle', 'happy-elementor-addons'),
                    'fa fa-angle-double-left' => esc_html__('Double Angle', 'happy-elementor-addons'),
                    'fa fa-chevron-left' => esc_html__('Chevron', 'happy-elementor-addons'),
                    'fa fa-chevron-circle-left' => esc_html__('Chevron Circle', 'happy-elementor-addons'),
                    'fa fa-caret-left' => esc_html__('Caret', 'happy-elementor-addons'),
                    'fa fa-arrow-left' => esc_html__('Arrow', 'happy-elementor-addons'),
                    'fa fa-long-arrow-left' => esc_html__('Long Arrow', 'happy-elementor-addons'),
                    'fa fa-arrow-circle-left' => esc_html__('Arrow Circle', 'happy-elementor-addons'),
                    'fa fa-arrow-circle-o-left' => esc_html__('Arrow Circle Negative', 'happy-elementor-addons'),
                ],
                'default' => 'fa fa-angle-left',
                'condition' => [
                    'show_arrow' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Post Title', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_borders',
            [
                'label' => esc_html__('Borders', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'happy-elementor-addons'),
                'label_off' => esc_html__('Hide', 'happy-elementor-addons'),
                'default' => 'yes',
                'prefix_class' => 'ha-post-navigator-borders-',
            ]
        );

        // Filter out post type without taxonomies
        $post_type_options = [];
        $post_type_taxonomies = [];
        foreach ($this->get_public_post_types() as $post_type => $post_type_label) {
            $taxonomies = $this->get_taxonomies(['object_type' => $post_type], false);
            if (empty($taxonomies)) {
                continue;
            }

            $post_type_options[$post_type] = $post_type_label;
            $post_type_taxonomies[$post_type] = [];
            foreach ($taxonomies as $taxonomy) {
                $post_type_taxonomies[$post_type][$taxonomy->name] = $taxonomy->label;
            }
        }

        $this->add_control(
            'in_same_term',
            [
                'label' => esc_html__('In same Term', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT2,
                'options' => $post_type_options,
                'default' => '',
                'multiple' => true,
                'label_block' => true,
                'description' => esc_html__('Indicates whether next post must be within the same taxonomy term as the current post, this lets you set a taxonomy per each post type', 'happy-elementor-addons'),
            ]
        );

        foreach ($post_type_options as $post_type => $post_type_label) {
            $this->add_control(
                $post_type . '_taxonomy',
                [
                    'label' => $post_type_label . ' ' . esc_html__('Taxonomy', 'happy-elementor-addons'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $post_type_taxonomies[$post_type],
                    'default' => '',
                    'condition' => [
                        'in_same_term' => $post_type,
                    ],
                ]
            );
        }

        $this->end_controls_section();
    }
    /**
     * Register styles related controls
     */
    protected function register_style_controls() {
        $this->__post_navigation_style_controls();
    }


    protected function __post_navigation_style_controls() {

        $this->start_controls_section(
            'label_style',
            [
                'label' => esc_html__('Label', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_label' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_label_style');

        $this->start_controls_tab(
            'label_color_normal',
            [
                'label' => esc_html__('Normal', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} span.post-navigation__prev--label' => 'color: {{VALUE}};',
                    '{{WRAPPER}} span.post-navigation__next--label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'label_color_hover',
            [
                'label' => esc_html__('Hover', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'label_hover_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} span.post-navigation__prev--label:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} span.post-navigation__next--label:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} span.post-navigation__prev--label, {{WRAPPER}} span.post-navigation__next--label',
                'exclude' => ['line_height'],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'title_style',
            [
                'label' => esc_html__('Title', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_post_navigation_style');

        $this->start_controls_tab(
            'tab_color_normal',
            [
                'label' => esc_html__('Normal', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_color_hover',
            [
                'label' => esc_html__('Hover', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} span.post-navigation__prev--title:hover, {{WRAPPER}} span.post-navigation__next--title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title',
                'exclude' => ['line_height'],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'arrow_style',
            [
                'label' => esc_html__('Arrow', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_arrow' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_post_navigation_arrow_style');

        $this->start_controls_tab(
            'arrow_color_normal',
            [
                'label' => esc_html__('Normal', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-navigation__arrow-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'arrow_color_hover',
            [
                'label' => esc_html__('Hover', 'happy-elementor-addons'),
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-navigation__arrow-wrapper:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => esc_html__('Size', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-navigation__arrow-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_padding',
            [
                'label' => esc_html__('Gap', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} .post-navigation__arrow-prev' => 'padding-right: {{SIZE}}{{UNIT}};',
                    'body:not(.rtl) {{WRAPPER}} .post-navigation__arrow-next' => 'padding-left: {{SIZE}}{{UNIT}};',
                    'body.rtl {{WRAPPER}} .post-navigation__arrow-prev' => 'padding-left: {{SIZE}}{{UNIT}};',
                    'body.rtl {{WRAPPER}} .post-navigation__arrow-next' => 'padding-right: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'borders_section_style',
            [
                'label' => esc_html__('Borders', 'happy-elementor-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_borders!' => '',
                ],
            ]
        );

        $this->add_control(
            'sep_color',
            [
                'label' => esc_html__('Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                //'default' => '#D4D4D4',
                'selectors' => [
                    '{{WRAPPER}} .ha-post-navigator__separator' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .ha-post-navigator' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'borders_width',
            [
                'label' => esc_html__('Size', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-post-navigator__separator' => 'width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .ha-post-navigator' => 'border-top-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-post-navigator__next.ha-post-navigator__link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
                    '{{WRAPPER}} .ha-post-navigator__prev.ha-post-navigator__link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
                ],
            ]
        );

        $this->add_responsive_control(
            'borders_spacing',
            [
                'label' => esc_html__('Spacing', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ha-post-navigator' => 'padding: {{SIZE}}{{UNIT}} 0;',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $prev_label = '';
        $next_label = '';
        $prev_arrow = '';
        $next_arrow = '';

        if ('yes' === $settings['show_label']) {
            $prev_label = '<span class="post-navigation__prev--label">' . $settings['prev_label'] . '</span>';
            $next_label = '<span class="post-navigation__next--label">' . $settings['next_label'] . '</span>';
        }

        if ('yes' === $settings['show_arrow']) {
            if (is_rtl()) {
                $prev_icon_class = str_replace('left', 'right', $settings['arrow']);
                $next_icon_class = $settings['arrow'];
            } else {
                $prev_icon_class = $settings['arrow'];
                $next_icon_class = str_replace('left', 'right', $settings['arrow']);
            }

            $prev_arrow = '<span class="post-navigation__arrow-wrapper post-navigation__arrow-prev"><i class="' . $prev_icon_class . '" aria-hidden="true"></i><span class="elementor-screen-only">' . esc_html__('Prev', 'happy-elementor-addons') . '</span></span>';
            $next_arrow = '<span class="post-navigation__arrow-wrapper post-navigation__arrow-next"><i class="' . $next_icon_class . '" aria-hidden="true"></i><span class="elementor-screen-only">' . esc_html__('Next', 'happy-elementor-addons') . '</span></span>';
        }

        $prev_title = '';
        $next_title = '';

        if ('yes' === $settings['show_title']) {
            $prev_title = '<span class="post-navigation__prev--title">%title</span>';
            $next_title = '<span class="post-navigation__next--title">%title</span>';
        }

        $in_same_term = false;
        $taxonomy = 'category';
        $post_type = get_post_type(get_queried_object_id());

        if (!empty($settings['in_same_term']) && is_array($settings['in_same_term']) && in_array($post_type, $settings['in_same_term'])) {
            if (isset($settings[$post_type . '_taxonomy'])) {
                $in_same_term = true;
                $taxonomy = $settings[$post_type . '_taxonomy'];
            }
        }
?>
        <div class="ha-post-navigator">
            <div class="ha-post-navigator__prev ha-post-navigator__link">
                <?php previous_post_link('%link', $prev_arrow . '<span class="ha-post-navigator__link__prev">' . $prev_label . $prev_title . '</span>', $in_same_term, '', $taxonomy); ?>
            </div>
            <?php if ('yes' === $settings['show_borders']) : ?>
                <div class="ha-post-navigator__separator-wrapper">
                    <div class="ha-post-navigator__separator"></div>
                </div>
            <?php endif; ?>
            <div class="ha-post-navigator__next ha-post-navigator__link">
                <?php next_post_link('%link', '<span class="ha-post-navigator__link__next">' . $next_label . $next_title . '</span>' . $next_arrow, $in_same_term, '', $taxonomy); ?>
            </div>
        </div>
<?php
    }
}
