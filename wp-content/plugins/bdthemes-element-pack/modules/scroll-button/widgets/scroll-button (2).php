<?php
namespace ElementPack\Modules\ScrollButton\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Scroll Button Widget
 */
class Scroll_Button extends Module_Base {

	public function get_name() {
		return 'bdt-scroll-button';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Scroll Button', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-scroll-button';
	}

	public function get_categories() {
	 	return [ 'element-pack' ];
 	}

 	public function get_keywords() {
		return [ 'scroll', 'button', 'link' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-scroll-button' ];
        }
    }
	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return [ 'ep-scroll-button' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/y8LJCO3tQqk';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_scroll_button',
			[
				'label' => esc_html__( 'Button', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'duration',
			[
				'label'      => esc_html__( 'Duration', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'offset',
			[
				'label' => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -200,
						'max'  => 200,
						'step' => 10,
					],
				],
			]
		);

		$this->add_control(
			'scroll_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'Scroll Up', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Scroll Up', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'section_id',
			[
				'label'       => esc_html__( 'Section ID', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'my-header',
				'description' => esc_html__( "By clicking this scroll button, to which section in your page you want to go? Just write that's section ID here such 'my-header'. N.B: No need to add '#'.", 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'scroll_button_position',
			[
				'label'   => __( 'Scroll Button Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => element_pack_position(),
			]
		);

		$this->add_responsive_control(
			'scroll_button_offset',
			[
				'label'     => __( 'Button Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button-wrapper' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'condition' => [
					'scroll_button_position!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_button_align',
			[
				'label'        => esc_html__( 'Button Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'elementor%s-align-',
				'default'      => 'center',
				'options'      => [
					'left' => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'condition' => [
					'scroll_button_position' => '',
				],
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label'       => esc_html__( 'Button Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'scroll_button_icon',
				'default' => [
					'value' => 'fas fa-angle-up',
					'library' => 'fa-solid',
				],
				'skin' => 'inline',
				'label_block' => false
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Before', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'After', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'button_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_indent',
			[
				'label'   => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'button_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button .bdt-scroll-button-align-icon-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-scroll-button .bdt-scroll-button-align-icon-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hide_on_before_scrolling',
			[
				'label'   => esc_html__( 'Hide On Before Scrolling', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'scroll_button_position[value]!' => '',
				],
				'render_type' => 'template',

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_scroll_button',
			[
				'label' => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_scroll_button_style' );

		$this->start_controls_tab(
			'tab_scroll_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'scroll_button_text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-scroll-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_button_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'scroll_button_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-scroll-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scroll-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scroll-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'scroll_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-scroll-button',
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-scroll-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_scroll_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'scroll_button_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-scroll-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_button_background_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'scroll_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_button_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'show_fancy_animation' => '',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'show_fancy_animation',
			[
				'label' => __( 'Show Fancy Animation', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'fancy_animation',
			[
				'label'       => esc_html__( 'Fancy Animation', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'shadow-pulse',
				'options'     => [
					'shadow-pulse' => esc_html__( 'Shadow Pulse', 'bdthemes-element-pack' ),
					'multi-shadow' => esc_html__( 'Multi Shadow', 'bdthemes-element-pack' ),
					'line-bounce' => esc_html__( 'Line Bounce', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'show_fancy_animation' => 'yes',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'fancy_border_color',
			[
				'label'     => esc_html__( 'Animated Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button-wrapper .bdt-scroll-button:before, {{WRAPPER}} .bdt-scroll-button-wrapper .bdt-scroll-button:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'show_fancy_animation' => 'yes',
					'fancy_animation' => 'line-bounce',
				],
			]
		);

		$this->add_control(
			'button_shadow_color',
			[
				'label'     => esc_html__( 'Animated Shadow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scroll-button-wrapper .bdt-scroll-button' => '--box-shadow-color: {{VALUE}};',
				],
				'condition' => [
					'show_fancy_animation' => 'yes',
					'fancy_animation!' => 'line-bounce',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render_text($settings) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'content-wrapper', 'class', 'bdt-scroll-button-content-wrapper' );
		$this->add_render_attribute( 'text', 'class', 'bdt-scroll-button-text' );

		if ( ! isset( $settings['scroll_button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['scroll_button_icon'] = 'fas fa-arrow-down';
		}

		$migrated  = isset( $settings['__fa4_migrated']['button_icon'] );
		$is_new    = empty( $settings['scroll_button_icon'] ) && Icons_Manager::is_migration_allowed();

		?>
		<span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
			<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
			<span class="bdt-scroll-button-align-icon-<?php echo esc_attr($settings['icon_align']); ?>">

				<?php if ( $is_new || $migrated ) :
					Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
				else : ?>
					<i class="<?php echo esc_attr( $settings['scroll_button_icon'] ); ?>" aria-hidden="true"></i>
				<?php endif; ?>

			</span>
			<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo esc_html($settings['scroll_button_text']); ?></span>
		</span>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'bdt-scroll-button', 'class', ['bdt-scroll-button', 'bdt-button', 'bdt-button-primary'] );

		if ( $settings['scroll_button_hover_animation'] ) {
			$this->add_render_attribute( 'bdt-scroll-button', 'class', 'elementor-animation-'.esc_attr($settings['scroll_button_hover_animation']) );
		}

		$this->add_render_attribute(
			[
				'bdt-scroll-button' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'duration'              => ( '' != $settings['duration']['size'] ) ? $settings['duration']['size'] : '',
							'offset'                => ( '' != $settings['offset']['size'] ) ? $settings['offset']['size'] : '',
							'HideOnBeforeScrolling' => ( '' != $settings['hide_on_before_scrolling'] ) ? true : false,
				        ]))
					]
				]
			]
		);

		if ('' != $settings['hide_on_before_scrolling']) {
			$this->add_render_attribute( 'bdt-scroll-button', 'style', 'opacity: 0;' );
		}

		$this->add_render_attribute( 'bdt-scroll-button', 'data-selector', '#' . esc_attr($settings['section_id']) );

		if ( '' !== $settings['scroll_button_position'] ) {
			$this->add_render_attribute( 'bdt-scroll-wrapper', 'class', ['bdt-position-fixed', 'bdt-position-' . $settings['scroll_button_position']] );
		}

		if ( 'shadow-pulse' == $settings['fancy_animation'] ) {
			$this->add_render_attribute( 'bdt-scroll-wrapper', 'class', 'bdt-scroll-button-wrapper bdt-shadow-pulse' );
		} elseif ( 'line-bounce' == $settings['fancy_animation'] ) {
			$this->add_render_attribute( 'bdt-scroll-wrapper', 'class', 'bdt-scroll-button-wrapper bdt-line-bounce' );
		} elseif ( 'multi-shadow' == $settings['fancy_animation'] ) {
			$this->add_render_attribute( 'bdt-scroll-wrapper', 'class', 'bdt-scroll-button-wrapper bdt-multi-shadow' );
		} else {
			$this->add_render_attribute( 'bdt-scroll-wrapper', 'class', 'bdt-scroll-button-wrapper' );
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'bdt-scroll-wrapper' ); ?>>
			<button <?php echo $this->get_render_attribute_string( 'bdt-scroll-button' ); ?>>
				<?php $this->render_text($settings); ?>
			</button>
		</div>

		<?php
	}

	protected function content_template() {
		?>

		<#

		var data = {
			duration: (settings.duration.size) ? new Number(settings.duration.size) : "",
			offset: (settings.offset.size) ? new Number(settings.offset.size) : "",
			HideOnBeforeScrolling: ( 'yes' == settings.hide_on_before_scrolling ) ? new Boolean(true) : Boolean(false),
		};

		var dataSetting = JSON.stringify(data);

		if ( 'yes' == settings.hide_on_before_scrolling ) {
			view.addRenderAttribute( 'bdt-scroll-button', 'style', 'opacity: 0;' );
		}


		var iconHTML = elementor.helpers.renderIcon( view, settings.button_icon, { 'aria-hidden': true }, 'i' , 'object' );

		var migrated = elementor.helpers.isIconMigrated( settings, 'button_icon' );


		if ( '' !== settings.scroll_button_position ) {
			view.addRenderAttribute( 'bdt-scroll-wrapper', 'class', 'bdt-position-fixed bdt-position-' + settings.scroll_button_position );
		}

		view.addRenderAttribute( 'bdt-scroll-wrapper', 'class', 'bdt-scroll-button-wrapper' );

		if ( 'yes' == settings.show_fancy_animation ) {
			if ( 'shadow-pulse' == settings.fancy_animation ) {
				view.addRenderAttribute( 'bdt-scroll-wrapper', 'class', 'bdt-shadow-pulse' );
			} else if ( 'line-bounce' == settings.fancy_animation ) {
				view.addRenderAttribute( 'bdt-scroll-wrapper', 'class', 'bdt-line-bounce' );
			} else if ( 'multi-shadow' == settings.fancy_animation ) {
				view.addRenderAttribute( 'bdt-scroll-wrapper', 'class', 'bdt-multi-shadow' );
			}
		}


		#>
		<div <# print( view.getRenderAttributeString( 'bdt-scroll-wrapper' ) ); #> >
			<button <# print( view.getRenderAttributeString( 'bdt-scroll-button' ) ); #> class="bdt-scroll-button bdt-button bdt-button-primary elementor-animation-{{ settings.scroll_button_hover_animation }}" data-selector="#{{ settings.section_id }}" data-settings="{{dataSetting}}">
				<span class="bdt-scrollr-button-content-wrapper">
					<# if ( settings.button_icon.value ) { #>
					<span class="bdt-scroll-button-icon bdt-scroll-button-align-icon-{{ settings.icon_align }}">

						<# if ( iconHTML && iconHTML.rendered && ( ! settings.scroll_button_icon || migrated ) ) { #>
							{{{ iconHTML.value }}}
						<# } else { #>
							<i class="{{ settings.scroll_button_icon }}" aria-hidden="true"></i>
						<# } #>

					</span>
					<# } #>
					<span class="bdt-scroll-button-text">{{{ settings.scroll_button_text }}}</span>
				</span>
			</button>
		</div>
		<?php
	}
}
