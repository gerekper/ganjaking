<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Portfolio_Grid_Three_Widget extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve Elementor widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name()
    {
        return 'appside-portfolio-grid-three-widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve Elementor widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title()
    {
        return esc_html__('Portfolio Grid: 03', 'aapside-master');
    }

    /**
     * Get widget icon.
     *
     * Retrieve Elementor widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon()
    {
        return 'eicon-posts-justified';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Elementor widget belongs to.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_categories()
    {
        return ['appside_widgets'];
    }

    /**
     * Register Elementor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {


        $this->start_controls_section(
            'slider_settings_section',
            [
                'label' => esc_html__('Query Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'image',
            [
                'label' => esc_html__('Image', 'attorg-master'),
                'type' => Controls_Manager::MEDIA,
                'description' => esc_html__('enter image.', 'attorg-master'),
            ]
        );
        $this->add_control(
            'name',
            [
                'label' => esc_html__('Title', 'aapside-master'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('enter title', 'aapside-master'),
                'default' => esc_html__('Zenefits.com', 'aapside-master')
            ]
        );
        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'aapside-master'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('enter description', 'aapside-master'),
                'default' => esc_html__('Each time a digital asset is purchased or sold, Sequoir donates a percentage of the fees back', 'aapside-master')
            ]
        );
        $this->add_control(
            'icon_box_link',
            [
                'label' => esc_html__('Link', 'attorg-master'),
                'type' => Controls_Manager::URL,
                'description' => esc_html__('enter url.', 'attorg-master'),
            ]
        );
        $this->end_controls_section();

        /*  title styling tabs start */
        $this->start_controls_section(
            'title_settings_section',
            [
                'label' => esc_html__('Title Styling', 'aapside-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'style_tabs'
        );

        $this->start_controls_tab(
            'style_normal_tab',
            [
                'label' => __('Normal', 'aapside-master'),
            ]
        );

        $this->add_control('title_color', [
            'label' => esc_html__('Title Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .hard-single-item .content .title' => "color:{{VALUE}}"
            ]
        ]);
        $this->add_control('description_color', [
            'label' => esc_html__('Description Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .hard-single-item .content p' => "color:{{VALUE}}"
            ]
        ]);
        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_hover_tab',
            [
                'label' => __('Hover', 'aapside-master'),
            ]
        );
        $this->add_control('title_hover_color', [
            'label' => esc_html__('Title Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .hard-single-item .content .title:hover' => "color:{{VALUE}}"
            ]
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /*  title styling tabs end */


        /*  Typography tabs start */
        $this->start_controls_section(
            'typography_settings_section',
            [
                'label' => esc_html__('Typography Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'label' => esc_html__('Title Typography', 'aapside-master'),
            'selector' => "{{WRAPPER}} .hard-single-item .content .title"
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'post_meta_typography',
            'label' => esc_html__('Description Typography', 'aapside-master'),
            'selector' => "{{WRAPPER}} .hard-single-item .content p"
        ]);
        $this->end_controls_section();

        /*  Typography tabs end */
    }

    /**
     * Render Elementor widget output on the frontend.
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        //query variable
        $settings = $this->get_settings_for_display();

        $image_id = $settings['image']['id'];
        $image_url = !empty($image_id) ? wp_get_attachment_image_src($image_id, 'full')[0] : '';
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

        $this->add_render_attribute('link_wrapper', 'class', '');
        if (!empty($settings['icon_box_link']['url'])) {
            $this->add_link_attributes('link_wrapper', $settings['icon_box_link']);
        }
        ?>

        <div class="hard-single-item margin-bottom-30">
            <div class="thumb">
                <a <?php echo $this->get_render_attribute_string('link_wrapper'); ?>>
                    <img src="<?php echo esc_url($image_url); ?>"
                         alt="<?php echo esc_attr($image_alt); ?>">
                </a>
            </div>
            <div class="content">
                <a <?php echo $this->get_render_attribute_string('link_wrapper'); ?>>
                    <h4 class="title"><?php echo esc_html($settings['name']); ?></h4></a>
                <p class="catagory"><?php echo esc_html($settings['description']); ?></p>
            </div>
        </div>

        <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type(new Appside_Portfolio_Grid_Three_Widget());