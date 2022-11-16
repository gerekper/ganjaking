<?php
use Elementor\Controls_Manager;
use ElementorPro\Modules\GlobalWidget\Documents\Widget;
use ElementorPro\Plugin;
use ElementorPro\Modules\ThemeBuilder as ThemeBuilder;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Group_Control_Border as Group_Control_Border;

/**
 * Working with elementor plugin
 *
 *
 * @since      1.4.2
 * @package    BetterDocs_Pro
 * @subpackage BetterDocs_Pro/elementor
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class BetterDocs_Pro_Elementor
{
    public static $pro_active;

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.4.2
     */
    public static function init()
    {
        add_action('elementor/widgets/register', [__CLASS__, 'register_widgets']);
        add_action('betterdocs/elementor/widgets/advanced-search/switcher', [__CLASS__,'betterdocs_advance_search'], 10, 1);
        add_action('betterdocs/elementor/widgets/advanced-search/controllers', [__CLASS__,'betterdocs_advance_search_controls'], 10, 1);
        if (is_plugin_active('elementor-pro/elementor-pro.php')) {
            add_action('elementor/init', [__CLASS__, 'load_widget_file']);
        }
    }

    /**
     *
     * Mange all widget for single docs
     *
     * @return string[]
     * @since  1.4.2
     */
    public static function get_widget_list()
    {
        $widget_arr['betterdocs-elementor-multiple-kb']   = 'BetterDocs_Elementor_Multiple_Kb';
        $widget_arr['betterdocs-elementor-popular-view']  = 'Betterdocs_Elementor_Popular_View';
        $widget_arr['betterdocs-elementor-tab-view-list'] = 'BetterDocs_Elementor_Tab_View';
        
        return $widget_arr;
    }

    public static function load_widget_file()
    {
        //load widget file
        foreach (self::get_widget_list() as $key => $value) {
            require_once BETTERDOCS_PRO_ROOT_DIR_PATH . "includes/elementor/widgets/$key.php";
        }
    }

    public static function register_widgets($widgets_manager)
    {
        foreach (self::get_widget_list() as $value) {
            if (class_exists($value)) {
                $widgets_manager->register(new $value);
            }
        }
    }

    public static function betterdocs_advance_search($wp) {
        if($wp->get_name() === 'betterdocs-search-form' ) {
            $wp->add_control(
                'betterdocs_category_search_toogle',
                [
                    'label' => esc_html__( 'Enable Category Search', 'betterdocs-pro' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'On', 'betterdocs-pro' ),
                    'label_off' => esc_html__( 'Off', 'betterdocs-pro' ),
                    'return_value' => 'true',
                    'default' => false,
                ]
            );

            $wp->add_control(
                'betterdocs_search_button_toogle',
                [
                    'label' => esc_html__( 'Enable Search Button', 'betterdocs-pro' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'On', 'betterdocs-pro' ),
                    'label_off' => esc_html__( 'Off', 'betterdocs-pro' ),
                    'return_value' => 'true',
                    'default' => false,
                ]
            );

            $wp->add_control(
                'betterdocs_popular_search_toogle',
                [
                    'label' => esc_html__( 'Enable Popular Search', 'betterdocs-pro' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'On', 'betterdocs-pro' ),
                    'label_off' => esc_html__( 'Off', 'betterdocs-pro' ),
                    'return_value' => 'true',
                    'default' => false,
                ]
            );
        }
    }

    public static function betterdocs_advance_search_controls($wp) {
        if( $wp->get_name() === 'betterdocs-search-form') {
            $wp->start_controls_section(
                'advance_search_controls',
                [
                    'label' => __('Advanced Search', 'betterdocs-pro'),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );

            $wp->add_control(
                'advance_category_search_bd',
                [
                    'label' => esc_html__('Category Search', 'betterdocs-pro'),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'advance_search_category_search_typography',
                    'selector' => '{{WRAPPER}}  .betterdocs-searchform .betterdocs-search-category'
                ]
            );

            $wp->add_control(
                'advance_search_category_search_font_color',
                [
                    'label'     => esc_html__('Font Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .betterdocs-search-category' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $wp->add_control(
                'advance_search_search_button_heading',
                [
                    'label' => esc_html__('Search Button', 'betterdocs-pro'),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'advance_search_search_button_typography',
                    'selector' => '{{WRAPPER}} .betterdocs-searchform .search-submit'
                ]
            );

            $wp->add_control(
                'advance_search_search_button_font_color',
                [
                    'label'     => esc_html__('Font Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_control(
                'advance_search_search_button_background_color',
                [
                    'label'     => esc_html__('Background Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'background-color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_control(
                'advance_search_search_button_background_color_hover',
                [
                    'label'     => esc_html__('Background Hover Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit:hover' => 'background-color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_responsive_control(
                'advance_search_search_button_border_radius',
                [
                    'label'      => esc_html__('Border Radius', 'betterdocs-pro'),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $wp->add_responsive_control(
                'advance_search_search_button_padding',
                [
                    'label'      => esc_html__('Padding', 'betterdocs-pro'),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-searchform .search-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $wp->add_control(
                'advance_search_popular_search',
                [
                    'label' => esc_html__('Popular Search', 'betterdocs-pro'),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_title_placeholder',
                [
                    'label'   => __('Title Placeholder', 'betterdocs-pro'),
                    'type'    => Controls_Manager::TEXT,
                    'default' => esc_html__('Popular Search', 'betterdocs-pro')
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_title_color',
                [
                    'label'     => esc_html__('Title Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-search-title' => 'color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label'     => esc_html__('Title Typography', 'betterdocs-pro'),
                    'name'     => 'advance_search_popular_search_title_typography',
                    'selector' => '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-search-title'
                ]
            );

            $wp->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label'     => esc_html__('Keyword Typography', 'betterdocs-pro'),
                    'name'     => 'advance_search_popular_search_keyword_typography',
                    'selector' => '{{WRAPPER}}  .betterdocs-popular-search-keyword .popular-keyword'
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_keyword_background_color',
                [
                    'label'     => esc_html__('Keyword Background Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'background-color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_keyword_background_color_hover',
                [
                    'label'     => esc_html__('Keyword Background Hover Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword:hover' => 'background-color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_control(
                'advance_search_popular_search_keyword_text_color',
                [
                    'label'     => esc_html__('Keyword Text Color', 'betterdocs-pro'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'color: {{VALUE}};'
                    ],
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_margin',
                [
                    'label'      => esc_html__('Title Margin', 'betterdocs-pro'),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $wp->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'       => 'advance_search_popular_search_keyword_border_type',
                    'label'      => esc_html__('Border', 'betterdocs-pro'),
                    'fields_options' => [
                        'border' => [
                            'default' => 'solid',
                        ],
                        'width'  => [
                            'default' => [
                                'top'      => '1',
                                'right'    => '1',
                                'bottom'   => '1',
                                'left'     => '1',
                                'isLinked' => false,
                            ],
                        ],
                        'color'  => [
                            'default' => '#DDDEFF',
                        ],
                    ],
                    'selector'  => '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword'
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_keyword_border_radius',
                [
                    'label'      => esc_html__('Keyword Border Radius', 'betterdocs-pro'),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_keyword_padding',
                [
                    'label'      => esc_html__('Keyword Padding', 'betterdocs-pro'),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $wp->add_responsive_control(
                'advance_search_popular_search_keyword_margin',
                [
                    'label'      => esc_html__('Keyword Margin', 'betterdocs-pro'),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors'  => [
                        '{{WRAPPER}} .betterdocs-popular-search-keyword .popular-keyword' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $wp->end_controls_section();
        }
    }
}

BetterDocs_Pro_Elementor::init();
