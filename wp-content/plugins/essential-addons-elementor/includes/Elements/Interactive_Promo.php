<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}
// If this file is called directly, abort.

class Interactive_Promo extends Widget_Base
{

    public function get_name()
    {
        return 'eael-interactive-promo';
    }

    public function get_title()
    {
        return esc_html__('Interactive Promo', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eaicon-interactive-promo';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    public function get_keywords()
    {
        return [
            'BANNER',
            'Promotional ad',
            'promo',
            'interactive banner',
            'ea',
            'essential addons',
        ];
    }

    public function get_custom_help_url()
    {
        return 'https://essential-addons.com/elementor/docs/interactive-promo/';
    }

    protected function register_controls()
    {

        // Content Controls
        $this->start_controls_section(
            'eael_section_promo_content',
            [
                'label' => esc_html__('Promo Content', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'promo_image',
            [
                'label' => __('Promo Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
	            'dynamic' => [
		            'active' => true,
	            ],
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $this->add_control(
            'promo_image_alt',
            [
                'label' => __('Image ALT Tag', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '',
                'placeholder' => __('Enter alter tag for the image', 'essential-addons-elementor'),
                'title' => __('Input image alter tag here', 'essential-addons-elementor'),
                'dynamic' => ['action' => true],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'promo_heading',
            [
                'label' => __('Promo Heading', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => 'I am Interactive',
                'placeholder' => __('Enter heading for the promo', 'essential-addons-elementor'),
                'title' => __('Enter heading for the promo', 'essential-addons-elementor'),
                'dynamic' => ['active' => true],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'promo_content',
            [
                'label' => __('Promo Content', 'essential-addons-elementor'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __('Click to inspect, then edit as needed.', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'promo_link_url',
            [
                'label' => __('Link URL', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'label_block' => true,
                'default' => '#',
                'placeholder' => __('Enter link URL for the promo', 'essential-addons-elementor'),
                'title' => __('Enter URL for the promo', 'essential-addons-elementor'),
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'promo_link_target',
            [
                'label' => esc_html__('Open in new window?', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('_blank', 'essential-addons-elementor'),
                'label_off' => __('_self', 'essential-addons-elementor'),
                'default' => '_self',
            ]
        );

        $this->end_controls_section();

        // Style Controls
        $this->start_controls_section(
            'eael_section_promo_settings',
            [
                'label' => esc_html__('Promo Effects &amp; Settings', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'promo_effect',
            [
                'label' => esc_html__('Set Promo Effect', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'effect-lily',
                'options' => [
                    'effect-lily' => esc_html__('Lily', 'essential-addons-elementor'),
                    'effect-sadie' => esc_html__('Sadie', 'essential-addons-elementor'),
                    'effect-layla' => esc_html__('Layla', 'essential-addons-elementor'),
                    'effect-oscar' => esc_html__('Oscar', 'essential-addons-elementor'),
                    'effect-marley' => esc_html__('Marley', 'essential-addons-elementor'),
                    'effect-ruby' => esc_html__('Ruby', 'essential-addons-elementor'),
                    'effect-roxy' => esc_html__('Roxy', 'essential-addons-elementor'),
                    'effect-bubba' => esc_html__('Bubba', 'essential-addons-elementor'),
                    'effect-romeo' => esc_html__('Romeo', 'essential-addons-elementor'),
                    'effect-sarah' => esc_html__('Sarah', 'essential-addons-elementor'),
                    'effect-chico' => esc_html__('Chico', 'essential-addons-elementor'),
                    'effect-milo' => esc_html__('Milo', 'essential-addons-elementor'),
                    'effect-apollo' => esc_html__('Apolo', 'essential-addons-elementor'),
                    'effect-jazz' => esc_html__('Jazz', 'essential-addons-elementor'),
                    'effect-ming' => esc_html__('Ming', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'promo_container_width',
            [
                'label' => esc_html__('Set max width for the container?', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('yes', 'essential-addons-elementor'),
                'label_off' => __('no', 'essential-addons-elementor'),
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'promo_container_width_value',
            [
                'label' => __('Container Max Width (% or px)', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 480,
                    'unit' => 'px',
                ],
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-interactive-promo' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'promo_container_width' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'promo_border',
                'selector' => '{{WRAPPER}} .eael-interactive-promo figure',
            ]
        );

        $this->add_control(
            'promo_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-interactive-promo figure' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_promo_styles',
            [
                'label' => esc_html__('Colors &amp; Typography', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'promo_heading_color',
            [
                'label' => esc_html__('Promo Heading Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-interactive-promo figure figcaption h2' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_promo_title_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-interactive-promo figure figcaption h2',
            ]
        );

        $this->add_control(
            'promo_content_color',
            [
                'label' => esc_html__('Promo Content Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-interactive-promo figure p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_promo_content_typography',
                'global' => [
	                'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector' => '{{WRAPPER}} .eael-interactive-promo figure p',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'promo_overlay_color',
                'label' => __('Background', 'essential-addons-elementor'),
                'types' => ['classic', 'gradient'],
                'default' => '#3085a3',
                'selector' => '{{WRAPPER}} .eael-interactive-promo figure',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $promo_image = $this->get_settings_for_display('promo_image');
?>
        <div id="eael-promo-<?php echo esc_attr($this->get_id()); ?>" class="eael-interactive-promo">
            <figure class="<?php echo esc_attr($settings['promo_effect']); ?>">
                <?php echo '<img alt="' . $settings['promo_image_alt'] . '" src="' . $promo_image['url'] . '">'; ?>
                <figcaption>
                    <div>
                        <?php if (!empty($settings['promo_heading'])) : ?>
                            <h2><?php echo esc_attr($settings['promo_heading']); ?></h2>
                        <?php endif; ?>

                        <p><?php echo $settings['promo_content']; ?></p>
                    </div>
                    <?php if (isset($settings['promo_link_url']) && !empty($settings['promo_link_url'])) : ?>
                        <a href="<?php echo esc_attr($settings['promo_link_url']); ?>" target="<?php echo esc_attr($settings['promo_link_target']); ?>"></a>
                    <?php endif; ?>
                </figcaption>
            </figure>
        </div>
<?php

    }

    protected function content_template()
    {
    }
}
