<?php

namespace ElementPack\Modules\Calendly\Widgets;

use Elementor\Plugin;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Calendly extends Module_Base {

    public function get_name() {
        return 'bdt-calendly';
    }

    public function get_title() {
        return BDTEP . esc_html__('Calendly', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-calendly';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_keywords() {
        return ['calendly', 'calender', 'booking', 'booked', 'appointment'];
	  }
	
    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['calendly', 'ep-scripts'];
        } else {
			return ['calendly'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/nl4zC46SrhY';
    }

    protected function register_controls() {
		$this->start_controls_section(
			'section_calendly',
			[
				'label' => __( 'Calendly', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'calendly_username',
			[
				'label'       => __( 'Username', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Type calendly username here', 'bdthemes-element-pack' ),
				'dynamic'     => ['active' => true],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'calendly_time',
			[
				'label'   => __( 'Select Time', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'15min' => __( '15 Minutes', 'bdthemes-element-pack' ),
					'30min' => __( '30 Minutes', 'bdthemes-element-pack' ),
					'60min' => __( '60 Minutes', 'bdthemes-element-pack' ),
					'' => __( 'All', 'bdthemes-element-pack' ),
				],
				'default' => '15min'
			]
		);

		$this->add_control(
			'event_type_details',
			[
				'label'        => __( 'Hide Event Type Details', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'      => __( 'Height', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '680',
				],
				'selectors'  => [
					'{{WRAPPER}} .calendly-inline-widget' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .calendly-wrapper'       => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
        
		$this->start_controls_section(
			'section_style_calendly',
			[
				'label' => __( 'Calendly', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'calendly_pro_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					__( 'Style option only works with %s. Basic plan user can\'t change the color style. For more details please %s.', 'bdthemes-element-pack' ),
					'<a href="https://calendly.com/pages/pricing" target="_blank">Calendly Pro plan</a>',
					'<a href="https://calendly.com/pages/pricing" target="_blank">check here</a>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::COLOR,
				'alpha' => false,
			]
		);

		$this->add_control(
			'button_link_color',
			[
				'label' => __( 'Button & Link Color', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::COLOR,
        'alpha' => false,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::COLOR,
        'alpha' => false,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
        $settings = $this->get_settings_for_display();
        
		$calendly_time = $settings['calendly_time']!=''?"/{$settings['calendly_time']}":'';
		
		$calendly_event = '';
        if ( 'yes' === $settings['event_type_details'] ) {
			$calendly_event = 'hide_event_type_details=1';
		}

        $parameters = [
        	'text_color'       => ($settings['text_color']) ? str_replace( '#', '', $settings['text_color'] ) : null,
        	'primary_color'    => ($settings['button_link_color']) ? str_replace( '#', '', $settings['button_link_color'] ) : null,
        	'background_color' => ($settings['background_color']) ? str_replace( '#', '', $settings['background_color'] ) : null,
        ];

        $requestUrl = 'https://calendly.com/';
        $requestUrl .= esc_attr( $settings['calendly_username'] );
        $requestUrl .= esc_attr( $calendly_time );
        $requestUrl .= '/?';
        $requestUrl .= esc_attr( $calendly_event );

        $final_url = $requestUrl . http_build_query($parameters);
        
		?>
		<?php if ( $settings['calendly_username'] ): ?>

            <div class="calendly-inline-widget" data-url="<?php echo $final_url; ?>" style="min-width:320px;"></div>

            <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js"></script>
			<?php if ( Plugin::$instance->editor->is_edit_mode() ) : ?>
                <div class="calendly-wrapper" style="width:100%; position:absolute; top:0; left:0; z-index:100;"></div>
			<?php endif; ?>

		<?php endif; ?>
		<?php
	}
}
 