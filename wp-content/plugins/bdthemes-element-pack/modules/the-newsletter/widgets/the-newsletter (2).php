<?php
namespace ElementPack\Modules\TheNewsletter\Widgets;

use Elementor\Plugin;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class The_Newsletter extends Module_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-the-newsletter';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'The Newsletter', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-the-newsletter bdt-new';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'the', 'newsletter', 'letter', 'email', 'subscribe' ];
	}

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-the-newsletter' ];
        }
    }

    public function get_custom_help_url() {
		return 'https://youtu.be/nFbzp1Pttf4';
	}

    protected function register_controls() {


        $this->start_controls_section(
            'section_content_the_newsletter',
            [
                'label' => esc_html__( 'The Newsletter', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'the_news_letter_type',
            [
                'label'   => esc_html__( 'Select Type', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'minimal',
                'options' => [
                    'minimal'  => esc_html__( 'Minimal', 'bdthemes-element-pack' ),
                    'standard' => esc_html__( 'Standard', 'bdthemes-element-pack' ),
                ],
            ]
        );


        $this->add_control(
            'firstname_show',
            [
                'label'       => __( 'Show Firstname', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'no',
                'separator'   => 'before',
                'condition'   => [
                    'the_news_letter_type' => 'standard'
                ]
            ]
        );

        $this->add_control(
            'firstname_show_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( 'Please active/deactivate this filed from your Newsletter Dashboard > List Building > Subscription Form Fields, Buttons, Labels. Otherwise this field will not visible.', 'bdthemes-element-pack' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'     => [
                    'firstname_show' => 'yes',
                    'the_news_letter_type' => 'standard'
                ]
            ]
        );

        $this->add_control(
            'lastname_show',
            [
                'label'       => __( 'Show Lastname', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'no',
                'condition'   => [
                    'the_news_letter_type' => 'standard',
                ]
            ]
        );

        $this->add_control(
            'lastname_show_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( 'Please active/deactivate this filed from your Newsletter Dashboard > List Building > Subscription Form Fields, Buttons, Labels. Otherwise this field will not visible.', 'bdthemes-element-pack' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'     => [
                    'lastname_show' => 'yes',
                    'the_news_letter_type' => 'standard'
                ],
            ]
        );

        $this->add_control(
            'select_hr',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'input_labels_spacing',
            [
                'label'     => __('Label Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp-subscription div.tnp-field label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'   => [
                    'the_news_letter_type' => 'standard'
                ],
                
            ]
        );

        $this->add_control(
            'input_fields_spacing',
            [
                'label'     => __('Input Field Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp-subscription div.tnp-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-the-newsletter .tnp.tnp-subscription-minimal .tnp-email' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();



        $this->start_controls_section(
         'section_style_label',
         [
            'label' => esc_html__( 'Input Labels', 'bdthemes-element-pack' ),
            'tab'   => Controls_Manager::TAB_STYLE,
            'condition'   => [
                'the_news_letter_type' => 'standard'
            ]
        ]
    );

        $this->add_control(
            'label_fields_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                   '{{WRAPPER}} .bdt-the-newsletter .tnp label' => 'color: {{VALUE}} ',
               ],
           ]
       );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp label',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
         'section_style_input_fields',
         [
            'label' => esc_html__( 'Input Fields', 'bdthemes-element-pack' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]
    );

        $this->start_controls_tabs('input_fields_tab');

        $this->start_controls_tab(
            'input_fields_tab_normal',
            [
                'label' => __('Placeholder  ', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'input_fields_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                   '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])' => 'color: {{VALUE}} ',
                   '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])::placeholder' => 'color: {{VALUE}} ',
               ],
           ]
       );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'input_fields_background',
                'selector'  => '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'input_fields_shadow',
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])'
            ]
        );

        $this->add_responsive_control(
            'input_fields_padding',
            [
                'label'     => __('Padding', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;', 
                ],
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'input_fields_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])',
            ]
        );

        $this->add_responsive_control(
            'input_fields_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'input_fields_typography',
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"])',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'input_fields_tab_active', [
                'label' => __('Active / Focus ', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'input_fields_color_active',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                   '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"]):focus' => 'color: {{VALUE}} ',
               ],
           ]
       );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'input_fields_background_active',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"]):focus',
            ]
        );

        $this->add_group_control(
         Group_Control_Box_Shadow::get_type(),
         [
            'name'     => 'input_fields_shadow_active',
            'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"]):focus'
        ]
    );

        $this->add_control(
            'input_fields_border_color_active',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input:not([type="submit"]):focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'input_fields_border_border!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();	

        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__( 'Submit Button', 'bdthemes-element-pack' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'submit_button_full_width',
            [
                'label'       => __( 'Full Width', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::SWITCHER,
                'condition'   => [
                    'the_news_letter_type' => 'standard'
                ]
            ]
        );
        
        $this->add_control(
            'submit_button_width',
            [
                'label'     => __('Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 25,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]' => 'width: calc({{SIZE}}% / 3.39);',
                ],
                'condition'   => [
                    'submit_button_full_width' => ''
                ]
            ]
        );

        $this->start_controls_tabs('submit_button_tab');

        $this->start_controls_tab(
            'submit_button_tab_normal',
            [
                'label' => __('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'submit_button_color',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                   '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]' => 'color: {{VALUE}} ',
               ],
           ]
       );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'submit_button_background',
                'types' => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'submit_button_shadow',
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]'
            ]
        );

        $this->add_responsive_control(
            'submit_button_padding',
            [
                'label'     => __('Padding', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submit_button_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]',
            ]
        );

        $this->add_responsive_control(
            'submit_button_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submit_button_typography',
                'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'submit_button_tab_active', [
                'label' => __('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'submit_button_color_hover',
            [
                'label'     => __('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                   '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]:hover' => 'color: {{VALUE}} ',
               ],
           ]
       );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'submit_button_background_hover',
                'types' => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]:hover',
            ]
        );

        $this->add_group_control(
         Group_Control_Box_Shadow::get_type(),
         [
            'name'     => 'submit_button_shadow_hover',
            'selector' => '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]:hover'
        ]
    );

        $this->add_control(
            'submit_button_border_color_hover',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-the-newsletter .tnp input[type=submit]:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'submit_button_border_border!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();	

    }

    private function render_editor_content() {
        $settings = $this->get_settings_for_display();

        ?>

        <?php if($settings['the_news_letter_type'] == 'minimal'): ?>
         <div class="tnp tnp-subscription-minimal">
             <form method="post">
                 <input type="hidden" name="nr" value="minimal" /><input type="hidden" name="nlang" value="" />
                 <input class="tnp-email" type="email" required="" name="ne" value="" placeholder="Email" />
                 <input class="tnp-submit" type="submit" value="Subscribe" />
             </form>
         </div>
         <?php else: ?>
            <div class="tnp tnp-subscription">
                <form method="post">
                    <input type="hidden" name="nlang" value="" />

                    <?php if( $settings['firstname_show'] == 'yes' ){ ?>
                        <div class="tnp-field tnp-field-firstname">
                            <label for="tnp-name"><?php echo esc_html('First name or full name', 'bdthemes-element-pack') ?></label>
                            <input class="tnp-name" type="text" name="nn" value="">
                        </div>
                    <?php } ?> 

                    <?php if( $settings['lastname_show'] == 'yes' ){ ?>
                        <div class="tnp-field tnp-field-surname">
                            <label for="tnp-surname"><?php echo esc_html('Last name', 'bdthemes-element-pack') ?></label>
                            <input class="tnp-surname" type="text" name="ns" value="">
                        </div>
                    <?php } ?>

                    <div class="tnp-field tnp-field-email">
                        <label for="tnp-email"><?php echo esc_html('Email', 'bdthemes-element-pack') ?></label> 
                        <input class="tnp-email" type="email" name="ne" value="" required="" />
                    </div>
                    <div class="tnp-field tnp-field-button">
                        <input class="tnp-submit" type="submit" value="Subscribe" />
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php
    }

    private function get_shortcode() {
      $settings = $this->get_settings_for_display();
      $attributes = [
            'type' => $settings['the_news_letter_type'], //minimal, standard//,
        ];

        $this->add_render_attribute( 'shortcode', $attributes );

        $shortcode   = [];
        $shortcode[] = sprintf( '[newsletter_form %s]', $this->get_render_attribute_string( 'shortcode' ) );
        return implode("", $shortcode);

    }



    public function render() {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute( 'wrapper', 'class', 'bdt-the-newsletter' ); 
        
        
        if(isset($settings['firstname_show']) && $settings['firstname_show'] != 'yes'){
            $this->add_render_attribute( 'wrapper', 'class', 'firstname-hide' ); 
        }

        if(isset($settings['lastname_show']) && $settings['lastname_show'] != 'yes'){
            $this->add_render_attribute( 'wrapper', 'class', 'lastname-hide' ); 
        }

        if(isset($settings['submit_button_full_width']) && $settings['submit_button_full_width'] == 'yes'){
            $this->add_render_attribute( 'wrapper', 'class', 'submit-full-width' ); 
        }     

        if(isset($settings['the_news_letter_type']) && $settings['the_news_letter_type'] == 'standard'){
            $this->add_render_attribute( 'wrapper', 'class', 'style-standard' ); 
        }

        

        ?>

        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

          <?php if (!Plugin::$instance->editor->is_edit_mode()) { ?>
             <?php echo do_shortcode( $this->get_shortcode() ); ?>
         <?php } else { ?>
             <?php $this->render_editor_content(); ?>
         <?php } ?>

     </div>

     <?php
 }

 public function render_plain_content() {
   echo $this->get_shortcode();
}


}
