<?php
namespace ElementPack\Modules\Countdown\Skins;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Icons_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Event_Countdown extends Elementor_Skin_Base {
	public function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/bdt-countdown/section_number_style/before_section_start',     [ $this, 'register_title_style_controls'        ] );
		add_action( 'elementor/element/bdt-countdown/section_label_style/after_section_end',         [ $this, 'register_event_button_style_controls' ] );
		add_action( 'elementor/element/bdt-countdown/section_content_count/after_section_end',       [ $this, 'register_event_button_controls'       ] );
		add_action( 'elementor/element/bdt-countdown/section_content_layout/before_section_end',     [ $this, 'register_event_controls'              ] );
		add_action( 'elementor/element/bdt-countdown/section_content_additional/before_section_end', [ $this, 'register_event_additional_controls'   ] );

	}

	public function get_id() {
		return 'bdt-event-countdown';
	}

	public function get_title() {
		return __( 'Event Countdown', 'bdthemes-element-pack' );
	}

	public function register_event_additional_controls( Module_Base $widget ) {
		$this->parent = $widget;

		$this->add_control(
			'show_event_title',
			[
				'label'   => esc_html__( 'Show Event Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_event_button',
			[
				'label'   => esc_html__( 'Show Event Button', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	public static function get_event_list() {

		if(is_plugin_active('the-events-calendar/the-events-calendar.php')) {
			$event_item = get_posts(array(
				'fields'         => 'ids', // Only get post IDs
				'posts_per_page' => -1,
				'post_type'      => \Tribe__Events__Main::POSTTYPE,
			));

			$event_items = ['0' => esc_html__( 'Select Event', 'bdthemes-element-pack' ) ];

			foreach ($event_item as $key => $value) {
				$event_items[$value] = get_the_title($value);
			}

			wp_reset_postdata();
		} else {
			$event_items = ['0' => esc_html__( 'Event Calendar Not Installed', 'bdthemes-element-pack' ) ];
		}
		return $event_items;
	}

	public function register_event_controls( Module_Base $widget ) {
		$this->parent = $widget;

		$this->add_control(
			'event_id',
			[
				'label'       => esc_html__( 'Event List', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Select your event from this list', 'bdthemes-element-pack' ),
				'options'     => self::get_event_list(),
				'default'     => '0',
			]
		);
	}

	public function register_event_button_controls() {
		$this->start_controls_section(
			'section_event_button',
			[
				'label' => esc_html__( 'Event Button', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'event_button_text',
			[
				'label'   => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'VIEW DETAILS', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'event_button_size',
			[
				'label'   => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lg',
				'options' => element_pack_button_sizes(),
			]
		);

		$this->add_control(
			'event_button_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			]
		);

		$this->add_control(
			'event_button_icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Before', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'After', 'bdthemes-element-pack' ),
				],
				'condition' => [
					$this->get_control_id( 'event_button_icon[value]!' ) => '',
				],
			]
		);

		$this->add_control(
			'event_button_icon_indent',
			[
				'label' => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					$this->get_control_id( 'event_button_icon[value]!' ) => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-wrapper .bdt-event-button-icon.elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-countdown-wrapper .bdt-event-button-icon.elementor-align-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function register_event_button_style_controls() {
		$this->start_controls_section(
			'section_style_event_button',
			[
				'label'     => esc_html__( 'Event Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show_event_button' ) => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_event_button_style' );

		$this->start_controls_tab(
			'tab_event_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'event_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'event_button_typography',
				//'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-event-button',
			]
		);

		$this->add_control(
			'event_button_background_color',
			[
				'label'  => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name'        => 'event_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-event-button',
			]
		);

		$this->add_control(
			'event_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_control(
			'event_button_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'event_button_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button .bdt-event-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-event-button .bdt-event-button-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'event_button_icon[value]!' ) => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_event_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'event_button_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'event_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'event_button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'event_button_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'event_button_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->add_control(
			'event_button_hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-event-button:hover .bdt-event-button-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-event-button:hover .bdt-event-button-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'event_button_icon[value]!' ) => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function register_title_style_controls(Module_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Event Title', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show_event_title' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'event_title_background_color',
			[
				'label'  => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-event-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'event_title_color',
			[
				'label'  => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-event-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name'        => 'event_title_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-countdown-event-title',
			]
		);

		$this->add_responsive_control(
			'event_title_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-event-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'event_title_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-event-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'event_title_space',
			[
				'label' => esc_html__( 'Space', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'  => -200,
						'max'  => 200,
						'step' => 5,
					],
				],
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-countdown-event-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'event_title_typography',
				'selector' => '{{WRAPPER}} .bdt-countdown-event-title',
				//'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->end_controls_section();
	}

	protected function render_text() {

		$this->parent->add_render_attribute(
			[
				'event-button-icon' => [
					'class' => [
						'bdt-event-button-icon',
						'elementor-button-icon',
						'elementor-align-icon-' . $this->get_instance_value('event_button_icon_align')
					],
				],
			]
		);

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset( $settings['__fa4_migrated']['event_button_icon'] );
		$is_new    = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		?> 
		<span class="elementor-button-content-wrapper">
			<?php 
			$event_button_icon = $this->get_instance_value('event_button_icon');
			if ( ! empty( $event_button_icon ) ) : ?>
				<span <?php echo $this->parent->get_render_attribute_string( 'event-button-icon' ); ?>>
				
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $event_button_icon , [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
					else : ?>
						<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</span>
			<?php endif; ?>

			<span class="elementor-button-text">
				<?php echo esc_html($this->get_instance_value('event_button_text')); ?>
			</span>
		</span>
		<?php
	}

	public function render() {
		$settings  = $this->parent->get_settings();
		$event_id  = $this->get_instance_value('event_id');
		$event_url = get_permalink($event_id);

		if ($event_id) {
			$this->parent->add_render_attribute(
				[
					'event-button' => [
						'class' => [
							'elementor-button',
							'bdt-event-button',
							'elementor-size-' . $this->get_instance_value('event_button_size'),
							$this->get_instance_value('event_button_animation') ? 'elementor-animation-' . $this->get_instance_value('event_button_animation') : ''
						],
						'href' => [
							esc_url( $event_url )
						],
					],
				]
			);

			// $event_date    = tribe_get_start_date ( $event_id, false,  'Y-m-d H:i' );
			$event_date    = tribe_get_end_date ( $event_id, false,  'Y-m-d H:i' );
			$event_title   = get_the_title($event_id);
			
			$due_date      = $event_date;
			$string        = $this->parent->get_strftime( $settings );

			$with_gmt_time = date( 'Y-m-d H:i', strtotime( $due_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
			$datetime      = new \DateTime($with_gmt_time);
			$final_time    = $datetime->format('c');

			$this->parent->add_render_attribute(
				[
					'countdown' => [
						'class' => [
							'bdt-grid',
							'bdt-flex-middle bdt-flex-' . esc_attr($settings['alignment']),
							$this->get_instance_value('column_gap') ? 'bdt-grid-'.$this->get_instance_value('column_gap') : '',
						],
						'data-bdt-countdown' => [
							'date: ' . $final_time
						],
						'data-bdt-grid' => ''
					],
				]
			);
 
			?>
			<div class="bdt-countdown-wrapper bdt-countdown-skin-event bdt-text-<?php echo esc_attr($settings['alignment']); ?>">
				<?php if( '' != $event_id  and 'yes' == $this->get_instance_value('show_event_title') ) : ?>
					<div class="bdt-countdown-event-title bdt-display-inline-block">
						<?php echo esc_attr($event_title); ?>
					</div>
				<?php endif; ?>

				<div <?php echo $this->parent->get_render_attribute_string( 'countdown' ); ?>>

					<?php echo wp_kses_post($string); ?>

					<?php if( '' != $event_id  and 'yes' == $this->get_instance_value('show_event_button') ) : ?>
						<div class="bdt-countdown bdt-countdown-event-button">
							<a <?php echo $this->parent->get_render_attribute_string( 'event-button' ); ?>>
								<?php $this->render_text(); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php 
		} else echo '<div class="bdt-alert-warning" data-bdt-alert><p>You couldn\'t select any event, please select a event from event list.</p></div>';
	}
}

