<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Team_Member_Two_Widget extends Widget_Base
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
        return 'appside-team-member-two-widget';
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
        return esc_html__('Team Member Two', 'aapside-master');
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
        return 'eicon-person';
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
            'settings_section',
            [
                'label' => esc_html__('General Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control('team_member_items', [
            'label' => esc_html__('Team Member Item', 'aapside-master'),
            'type' => Controls_Manager::REPEATER,
            'default' => [
                [
                    'name' => esc_html__('Maria Hexa', 'aapside-master'),
                    'image' => array(
                        'url' => Utils::get_placeholder_image_src()
                    ),
                    'designation' => esc_html__('Creative Designer', 'aapside-master')
                ],
                [
                    'name' => esc_html__('Maria Hexa', 'aapside-master'),
                    'image' => array(
                        'url' => Utils::get_placeholder_image_src()
                    ),
                    'designation' => esc_html__('Creative Designer', 'aapside-master')
                ],
            ],
            'fields' => [
                [
                    'name' => 'image',
                    'label' => esc_html__('Image', 'aapside-master'),
                    'type' => Controls_Manager::MEDIA,
                    'description' => esc_html__('enter title.', 'aapside-master'),
                    'default' => array(
                        'url' => Utils::get_placeholder_image_src()
                    )
                ],
                [
                    'name' => 'name',
                    'label' => esc_html__('Name', 'aapside-master'),
                    'type' => Controls_Manager::TEXT,
                    'description' => esc_html__('enter name', 'aapside-master'),
                    'default' => esc_html__('Lara Croft', 'aapside-master')
                ],
                [
                    'name' => 'designation',
                    'label' => esc_html__('Designation', 'aapside-master'),
                    'type' => Controls_Manager::TEXT,
                    'description' => esc_html__('enter designation', 'aapside-master'),
                    'default' => esc_html__('CEO, Appside', 'aapside-master')
                ],
                [
                    'name' => 'icon_1',
                    'label' => esc_html__('Icon one', 'aapside-master'),
                    'type' => Controls_Manager::ICON,
                    'description' => esc_html__('select icon', 'aapside-master'),
                    'default' => 'fa fa-facebook'
                ],
                [
                    'name' => 'icon_1_url',
                    'label' => esc_html__('Icon 1 Url', 'aapside-master'),
                    'type' => Controls_Manager::URL,
                    'description' => esc_html__('enter url', 'aapside-master'),
                    'default' => array(
                        'url' => '#'
                    )
                ],
                [
                    'name' => 'icon_2',
                    'label' => esc_html__('Icon Two', 'aapside-master'),
                    'type' => Controls_Manager::ICON,
                    'description' => esc_html__('select icon', 'aapside-master'),
                    'default' => 'fa fa-twitter'
                ],
                [
                    'name' => 'icon_2_url',
                    'label' => esc_html__('Icon 2 Url', 'aapside-master'),
                    'type' => Controls_Manager::URL,
                    'description' => esc_html__('enter url', 'aapside-master'),
                    'default' => array(
                        'url' => '#'
                    )
                ],
                [
                    'name' => 'icon_3',
                    'label' => esc_html__('Icon Three', 'aapside-master'),
                    'type' => Controls_Manager::ICON,
                    'description' => esc_html__('select icon', 'aapside-master'),
                    'default' => 'fa fa-instagram'
                ],
                [
                    'name' => 'icon_3_url',
                    'label' => esc_html__('Icon 3 Url', 'aapside-master'),
                    'type' => Controls_Manager::URL,
                    'description' => esc_html__('enter url', 'aapside-master'),
                    'default' => array(
                        'url' => '#'
                    )
                ],

            ],
            'title_field' => '{{name}}'
        ]);
        $this->end_controls_section();

        $this->start_controls_section(
            'styling_settings_section',
            [
                'label' => esc_html__('Styling Settings', 'aapside-master'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control('title_color', [
            'label' => esc_html__('Title Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .team-single-item .content .title" => "color: {{VALUE}}"
            ]
        ]);
        $this->add_control('designation_color', [
            'label' => esc_html__('Designation Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .team-single-item .content .post" => "color: {{VALUE}}"
            ]
        ]);
        $this->add_control('divider', [
            'type' => Controls_Manager::DIVIDER
        ]);
        $this->add_group_control(Group_Control_Background::get_type(), [
            'label' => esc_html__('Overlay Background', 'aapside-master'),
            'name' => 'overlay_background',
            'selector' => "{{WRAPPER}} .team-single-item .thumb .hover"
        ]);
        $this->add_control('thumb_border_color', [
            'label' => esc_html__('Border Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .team-single-item .thumb" => "border-color: {{VALUE}}"
            ]
        ]);
        $this->add_control('thumb_icon_color', [
            'label' => esc_html__('Social Icon Color', 'aapside-master'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                "{{WRAPPER}} .team-single-item .thumb .hover .social-icon li a" => "color: {{VALUE}}"
            ]
        ]);
        $this->end_controls_section();
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
            'selector' => "{{WRAPPER}} .team-single-item .content .title"
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'designation_typography',
            'label' => esc_html__('Designation Typography', 'aapside-master'),
            'selector' => "{{WRAPPER}} .team-single-item .content .post"
        ]);
        $this->end_controls_section();

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
        $settings = $this->get_settings_for_display();
        $all_team_member_items = $settings['team_member_items'];
        ?>
        <div class="team-single-item-wrap">
            <div class="row">
                <?php
                foreach ($all_team_member_items as $item):
                    $image_id = $item['image']['id'];
                    $image_url = wp_get_attachment_image_src($image_id, 'full', false);
                    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    ?>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="team-single-item">
                            <div class="thumb">
                                <img src="<?php echo esc_url($image_url[0]); ?>"
                                     alt="<?php echo esc_attr($image_alt); ?>">
                            </div>
                            <div class="content">
                                <div class="social-link style-02">
                                    <ul>
                                        <?php
                                        if (!empty($item['icon_1']) && !empty($item['icon_1_url'])) {
                                            printf(' <li><a href="%2$s"><i class="%1$s"></i></a></li>', esc_attr($item['icon_1']), esc_url($item['icon_1_url']['url']));
                                        }
                                        if (!empty($item['icon_2']) && !empty($item['icon_2_url'])) {
                                            printf(' <li><a href="%2$s"><i class="%1$s"></i></a></li>', esc_attr($item['icon_2']), esc_url($item['icon_2_url']['url']));
                                        }
                                        if (!empty($item['icon_3']) && !empty($item['icon_3_url'])) {
                                            printf(' <li><a href="%2$s"><i class="%1$s"></i></a></li>', esc_attr($item['icon_3']), esc_url($item['icon_3_url']['url']));
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <h4 class="title"><?php echo esc_html($item['name']); ?></h4>
                                <span class="post"><?php echo esc_html($item['designation']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type(new Appside_Team_Member_Two_Widget());