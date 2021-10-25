<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Icons_Manager;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;


use MasterAddons\Inc\Controls\MA_Control_Visual_Select;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 3/15/2020
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MA Header Search
 */
class Search extends Widget_Base
{

    public function get_name()
    {
        return 'ma-search';
    }
    public function get_title()
    {
        return __('Search', MELA_TD);
    }

    public function get_categories()
    {
        return ['master-addons'];
    }

    public function get_icon()
    {
        return 'ma-el-icon eicon-search';
    }

    public function get_keywords()
    {
        return ['search', 'search bar', 'header search', 'header', 'footer'];
    }

    public function get_help_url()
    {
        return 'https://master-addons.com/demos/search-element/';
    }


    private function jltma_get_post_types()
    {
        $args = array('public' => true);

        $post_types = get_post_types($args, 'objects');
        $posts = array();

        foreach ($post_types as $post_type) {
            $labels = get_post_type_labels($post_type);
            $posts[$post_type->name] = $labels->name;
        }

        return $posts;
    }

    protected function _register_controls()
    {


        $this->start_controls_section(
            'jltma_search_general',
            array(
                'label'      => __('Content', MELA_TD),
            )
        );

        $this->add_control(
            'jltma_search_type',
            array(
                'label'       => __('Type', MELA_TD),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'icon',
                'options'     => array(
                    'form'    => __('Form', MELA_TD),
                    'icon'    => __('Icon Popup', MELA_TD)
                )
            )
        );

        $this->add_control(
            'jltma_search_submit_type',
            array(
                'label'       => __('Submit Button Type', MELA_TD),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'none',
                'options'     => array(
                    'none'   => __('None', MELA_TD),
                    'icon'   => __('Icon', MELA_TD),
                    'button' => __('Button', MELA_TD),
                ),
                'condition' => array(
                    'jltma_search_type' => 'form'
                )
            )
        );


        $this->add_control(
            'jltma_search_submit_button',
            array(
                'label'       => __('Button Text', MELA_TD),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Search',
                'condition' => array(
                    'jltma_search_submit_type' => 'button'
                )
            )
        );


        $this->add_control(
            'jltma_search_icon',
            array(
                'label'       => __('Icon', MELA_TD),
                'description' => __('Please choose an icon from the list.', MELA_TD),
                'type'    => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fa fa-search',
                    'library' => 'fa-solid',
                ],
                'conditions'   => array(
                    'relation' => 'or',
                    'terms'    => array(
                        array(
                            'name'     => 'jltma_search_type',
                            'operator' => '===',
                            'value'    => 'icon'
                        ),
                        array(
                            'name'     => 'jltma_search_submit_type',
                            'operator' => '===',
                            'value'    => 'icon'
                        )
                    )
                )
            )
        );

        // $this->add_control(
        //     'jltma_search_has_category',
        //     array(
        //         'label'       => __('Searcy by Category', MELA_TD),
        //         'type'         => Controls_Manager::SWITCHER,
        //         'label_on'     => __( 'On', MELA_TD ),
        //         'label_off'    => __( 'Off', MELA_TD ),
        //         'return_value' => true,
        //         'default'      => false,
        //         'condition' => array(
        //             'jltma_search_type' => 'form'
        //         )
        //     )
        // );



        // $this->add_control(
        //     'jltma_search_post_types',
        //     array(
        //         'label'       => __('Post Types', MELA_TD),
        //         'type'        => Controls_Manager::SELECT2,
        //         'multiple' => true,
        //         'options'     => $this->jltma_get_post_types()
        //     )
        // );


        $this->end_controls_section();




        $this->start_controls_section(
            'jltma_search_icon_section',
            array(
                'label'     => __('Icon', MELA_TD),
                'tab'       => Controls_Manager::TAB_STYLE,
                'conditions'   => array(
                    'relation' => 'or',
                    'terms'    => array(
                        array(
                            'name'     => 'jltma_search_type',
                            'operator' => '===',
                            'value'    => 'icon'
                        ),
                        array(
                            'name'     => 'jltma_search_submit_type',
                            'operator' => '===',
                            'value'    => 'icon'
                        )
                    )
                )
            )
        );


        $this->add_responsive_control(
            'jltma_search_icon_size',
            array(
                'label'      => __('Size', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em'),
                'range'      => array(
                    'px' => array(
                        'max' => 100
                    ),
                    'em' => array(
                        'max' => 10
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-search-icon:before, {{WRAPPER}} .jltma-submit-icon-container:before' => 'font-size: {{SIZE}}{{UNIT}};',
                )
            )
        );

        $this->add_control(
            'jltma_search_icon_color',
            array(
                'label'       => __('Icon color', MELA_TD),
                'type'        => Controls_Manager::COLOR,
                'default'     => '#303030',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-search-icon:before, {{WRAPPER}} .jltma-submit-icon-container:before' => 'color: {{VALUE}}',
                )
            )
        );

        $this->add_responsive_control(
            'jltma_search_icon_margin',
            array(
                'label'      => __('Icon Margin', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-icon, {{WRAPPER}} .jltma-submit-icon-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                )
            )
        );

        $this->end_controls_section();





        /*  Icon Section
            /*-------------------------------------*/
        $this->start_controls_section(
            'jltma_search_form_section',
            array(
                'label'     => __('Form', MELA_TD),
                'tab'       => Controls_Manager::TAB_STYLE,
                'conditions'   => array(
                    'relation' => 'or',
                    'terms'    => array(
                        array(
                            'name'     => 'jltma_search_type',
                            'operator' => '===',
                            'value'    => 'form'
                        )
                    )
                )
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'jltma_search_form_typgraphy',
                'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
                'selector'  => '{{WRAPPER}} .jltma-search-form .jltma-search-field'
            )
        );


        $this->add_responsive_control(
            'jltma_search_form_width',
            array(
                'label'      => __('Width', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', '%'),
                'range'      => array(
                    '%' => array(
                        'min'  => 1,
                        'max'  => 120,
                        'step' => 1
                    ),
                    'em' => array(
                        'min'  => 1,
                        'max'  => 120,
                        'step' => 1
                    ),
                    'px' => array(
                        'min'  => 1,
                        'max'  => 1900,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-search-form' => 'max-width:{{SIZE}}{{UNIT}};'
                ),
            )
        );

        $this->add_responsive_control(
            'jltma_search_form_margin',
            array(
                'label'      => __('Margin', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                )
            )
        );

        $this->add_responsive_control(
            'jltma_search_form_padding',
            array(
                'label'      => __('Padding', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form .jltma-search-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                )
            )
        );

        $this->add_control(
            'jltma_search_form_color',
            array(
                'label'       => __('Form Background Color', MELA_TD),
                'type'        => Controls_Manager::COLOR,
                'default'     => '#FFF',
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form .jltma-search-field' => 'background-color: {{VALUE}}',
                )
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'      => 'form_border',
                'selector'  => '{{WRAPPER}} .jltma-search-form .jltma-search-field',
                'separator' => 'none'
            )
        );

        $this->add_control(
            'jltma_search_form_border_radius',
            array(
                'label'      => __('Border Radius', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form .jltma-search-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;'
                ),
                'allowed_dimensions' => 'all',
                'separator'  => 'after'
            )
        );

        $this->end_controls_section();



        /*  Icon Section
            /*-------------------------------------*/
        $this->start_controls_section(
            'jltma_search_button_section',
            array(
                'label'     => __('Button', MELA_TD),
                'tab'       => Controls_Manager::TAB_STYLE,
                'conditions'   => array(
                    'relation' => 'and',
                    'terms'    => array(
                        array(
                            'name'     => 'jltma_search_type',
                            'operator' => '===',
                            'value'    => 'form'
                        ),
                        array(
                            'name'     => 'jltma_search_submit_type',
                            'operator' => '===',
                            'value'    => 'button'
                        )
                    )
                )
            )
        );

        $this->add_control(
            'jltma_search_button_color',
            array(
                'label'       => __('Background Color', MELA_TD),
                'type'        => Controls_Manager::COLOR,
                'default'     => '#303030',
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form .jltma-search-submit' => 'background-color: {{VALUE}}',
                )
            )
        );

        $this->add_responsive_control(
            'jltma_search_button_padding',
            array(
                'label'      => __('Padding', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form .jltma-search-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                )
            )
        );

        $this->add_responsive_control(
            'jltma_search_button_margin',
            array(
                'label'      => __('Margin', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-search-form .jltma-search-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                )
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'jltma_search_button_typgraphy',
                'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
                'selector'  => '{{WRAPPER}}  .jltma-search-form .jltma-search-submit'
            )
        );

        $this->end_controls_section();






        /**
         * Content Tab: Docs Links
         */
        $this->start_controls_section(
            'jltma_section_help_docs',
            [
                'label' => esc_html__('Help Docs', MELA_TD),
            ]
        );


        $this->add_control(
            'help_doc_1',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/search-element/" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_2',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/search-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_3',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=Uk6nnoN5AJ4" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );
        $this->end_controls_section();



        //Upgrade to Pro
       
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $jltma_search_type = $settings['jltma_search_type'];
        // $settings['jltma_search_has_category']
        $jltma_search_submit_type = $settings['jltma_search_submit_type'];
        $jltma_search_submit_button = $settings['jltma_search_submit_button'];
        // $jltma_search_icon = $settings['jltma_search_icon'];

        $icon_migrated = isset($settings['__fa4_migrated']['jltma_search_icon']);
        $icon_is_new = empty($settings['jltma_search_icon_new']);


        $this->add_render_attribute('ma_el_search_wrap', [
            'class'    => [
                'ma-el-search-wrapper',
                'ma-el-search-wrapper-' . $jltma_search_type,

            ],
            'id' => 'ma-el-search-wrapper-' . $this->get_id(),
            'data-search-type' => $jltma_search_type
        ]);
?>

        <div <?php echo $this->get_render_attribute_string('ma_el_search_wrap'); ?>>

            <?php if ($jltma_search_type == "icon") { ?>

                <main class="main-wrap">
                    <div class="search-wrap">
                        <button id="btn-search" class="btn--search">
                            <?php if ($icon_migrated || $icon_is_new) {
                                Icons_Manager::render_icon($settings['jltma_search_icon'], ['aria-hidden' => 'true']);
                            } else { ?>
                                <i class="<?php echo $settings['jltma_search_icon_new']; ?>"></i>
                            <?php } ?>
                        </button>
                    </div>
                </main><!-- /main-wrap -->

                <div class="jltma-search">
                    <button id="btn-search-close" class="btn--search-close" aria-label="Close search form">
                        <svg class="icon--search" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 40 40">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: url(#linear-gradient);
                                    }

                                    .cls-2 {
                                        fill: url(#linear-gradient-2);
                                    }

                                    .cls-3 {
                                        fill: #fff;
                                    }
                                </style>
                                <linearGradient id="linear-gradient" y1="40" x2="40" gradientUnits="userSpaceOnUse">
                                    <stop offset="0" stop-color="#c6027a" />
                                    <stop offset="1" stop-color="#143df4" />
                                </linearGradient>
                                <linearGradient id="linear-gradient-2" y1="40" x2="40" gradientUnits="userSpaceOnUse">
                                    <stop offset="1" stop-color="#c6027a" />
                                    <stop offset="1" stop-color="#143df4" />
                                </linearGradient>
                            </defs>
                            <title>close_03</title>
                            <rect class="cls-1" width="40" height="40" />
                            <rect class="cls-2" width="40" height="40" />
                            <path class="cls-3" d="M22.12,20l6.72-6.72a1.5,1.5,0,1,0-2.12-2.12L20,17.88l-6.72-6.72a1.5,1.5,0,1,0-2.12,2.12L17.88,20l-6.72,6.72a1.5,1.5,0,1,0,2.12,2.12L20,22.12l6.72,6.72a1.5,1.5,0,1,0,2.12-2.12Z" />
                        </svg>
                    </button>

                    <form class="search__form" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <input class="search__input" name="s" id="s" placeholder="Search ..." autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />


                        <?php
                        // if ( $args['has_category'] ) {
                        //     $all_taxs     = get_taxonomies( array( '_builtin' => FALSE ) );
                        //     $accepted_tax = array('product_cat', 'news-category', 'portfolio-cat');
                        //     $taxonomies   = array( 'category' );

                        //     foreach ( $all_taxs as $tax => $value ) {
                        //         if( in_array( $tax, $accepted_tax ) ) {
                        //             array_push( $taxonomies, $tax );
                        //         }
                        //     }

                        //     $dropdown_args = array (
                        //         'show_option_all' =>  __('All Categories', MELA_TD),
                        //         'taxonomy' => $taxonomies
                        //     );
                        // }

                        // if ( $args['has_category'] ) {
                        //     wp_dropdown_categories( $dropdown_args );
                        // }
                        ?>


                        <?php
                        // $jltma_search_post_types = $settings['jltma_search_post_types'];
                        // $query_types = get_query_var('post_type');
                        // foreach ($jltma_search_post_types as $post_type) {
                        //     if (in_array($post_type, $query_types)) {
                        //         $checked_types =  'checked="checked"';
                        //     }
                        //     echo '<input type="checkbox" name="post_type[]" value="'. $post_type .'"' . $checked_types .' /><label>'. ucwords($post_type) .'</label>';
                        // }
                        ?>

                        <span class="search__info">
                            <?php echo __('Hit enter to search or ESC to close', MELA_TD); ?>
                        </span>
                    </form>
                </div><!-- /search -->

            <?php } else if ($jltma_search_type == "form") { ?>

                <form method="get" class="jltma-search-form" action="<?php echo esc_url(home_url('/')); ?>">



                    <div class="input-group mb-3">

                        <input type="text" class="form-control jltma-search-field" name="s" title="Search for:" placeholder="<?php echo esc_html__('Search', MELA_TD); ?>" required="">

                        <div class="input-group-append">
                            <button type="submit" class="jltma-search-submit <?php if ($jltma_search_submit_type == "button") echo "jltma-text"; ?>">
                                <?php if ($icon_migrated || $icon_is_new) {
                                    Icons_Manager::render_icon($settings['jltma_search_icon'], ['aria-hidden' => 'true']);
                                } else { ?>
                                    <i class="<?php echo $settings['jltma_search_icon_new']; ?>"></i>
                                <?php } ?>

                                <?php
                                if ($jltma_search_submit_button) {
                                    echo '<span>' . $jltma_search_submit_button . '</span>';
                                } ?>
                            </button>
                        </div>
                    </div>

                </form>

            <?php } ?>

        </div> <!-- .search-wrapper -->

<?php

    }
}
