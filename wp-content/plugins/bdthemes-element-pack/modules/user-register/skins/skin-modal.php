<?php
namespace ElementPack\Modules\UserRegister\Skins;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Element_Pack_Loader;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Modal extends Elementor_Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/bdt-user-register/section_style/before_section_start', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/bdt-user-register/section_forms_additional_options/before_section_start', [ $this, 'register_modal_button_controls' ] );
	}

	public function get_id() {
		return 'bdt-modal';
	}

	public function get_title() {
		return __( 'Modal', 'bdthemes-element-pack' );
	}

	public function register_modal_button_controls(Module_Base $widget) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_modal_button',
			[
				'label' => esc_html__( 'Modal Button', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'modal_button_text',
			[
				'label'   => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Register', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'modal_button_size',
			[
				'label'   => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => element_pack_button_sizes(),
			]
		);

		$this->add_responsive_control(
			'modal_button_align',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
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
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			]
		);

		$this->add_control(
			'user_register_modal_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'modal_button_icon',
			]
		);

		$this->add_control(
			'modal_button_icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Before', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'After', 'bdthemes-element-pack' ),
				],
				'condition' => [
					$this->get_control_id( 'user_register_modal_icon[value]!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'modal_button_icon_indent',
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
					$this->get_control_id( 'user_register_modal_icon[value]!' ) => '',
				],
				'selectors' => [
                    '{{WRAPPER}} .bdt-button-modal .bdt-flex-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-button-modal .bdt-flex-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],

			]
		);

		$this->end_controls_section();
	}

	public function register_controls(Module_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_modal_style',
			[
				'label' => esc_html__( 'Modal Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'modal_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#modal{{ID}} .bdt-modal-dialog' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name'        => 'modal_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#modal{{ID}} .bdt-modal-dialog',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'modal_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'#modal{{ID}} .bdt-modal-dialog' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modal_text_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'#modal{{ID}} .bdt-modal-dialog .bdt-modal-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'modal_close_button',
			[
				'label'   => esc_html__( 'Close Button', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'modal_close_button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack') .BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#modal{{ID}} .bdt-modal-dialog .bdt-modal-close-default' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id('modal_close_button') => 'yes'
				]
			]
		);

		$this->add_control(
			'modal_close_button_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack') .BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#modal{{ID}} .bdt-modal-dialog .bdt-modal-close-default:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id('modal_close_button') => 'yes'
				]
			]
		);

		$this->add_control(
			'modal_header',
			[
				'label'   => esc_html__( 'Modal Header', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'modal_header_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack') .BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#modal{{ID}} .bdt-modal-dialog .bdt-modal-header .bdt-modal-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id('modal_header') => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_header_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack') .BDTEP_NC,
				'selector' => '#modal{{ID}} .bdt-modal-dialog .bdt-modal-header .bdt-modal-title',
				'condition' => [
					$this->get_control_id('modal_header') => 'yes'
				]
			]
		);

		$this->add_control(
			'modal_custom_width',
			[
				'label'   => esc_html__( 'Modal Width', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' 	=> esc_html__( 'Default', 'bdthemes-element-pack' ),
					'full' 		=> esc_html__( 'Full', 'bdthemes-element-pack' ),
					'container' => esc_html__( 'Container', 'bdthemes-element-pack' ),
					'custom'    => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				],
				'default' 	=> 'default',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'modal_custom_width_custom',
			[
				'label' => esc_html__( 'Custom Width(px)', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors'  => [
					'#modal{{ID}}.bdt-modal-custom .bdt-modal-dialog' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'modal_custom_width[value]' ) => 'custom',
				],
			]
		);

		$this->add_control(
			'modal_recaptcha',
			[
				'label'   => esc_html__( 'Recaptcha Text', 'bdthemes-element-pack' ) .BDTEP_NC,
				'type'    => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'modal_recaptcha_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#modal{{ID}} .bdt-modal-dialog .bdt-recaptcha-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_recaptcha_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '#modal{{ID}} .bdt-modal-dialog .bdt-recaptcha-text',
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings    = $this->parent->get_settings();
		$id          = 'modal' . $this->parent->get_id();
		$current_url = remove_query_arg( 'fake_arg' );
		$button_size = $this->get_instance_value('modal_button_size');
		$button_animation = $this->get_instance_value('modal_button_animation');

		$this->parent->add_render_attribute(
			[
				'modal-button' => [
					'class' => [
						'elementor-button',
						'bdt-button-modal',
						'elementor-size-' . esc_attr($button_size),
						$button_animation ? 'elementor-animation-' . esc_attr($button_animation) : ''
					],
					'href' => wp_logout_url( $current_url )
				]
			]
		);

		if ( is_user_logged_in() && ! Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
			if ( $settings['show_logged_in_message'] ) {

					$this->parent->add_render_attribute(
						[
							'user_register' => [
								'class' => 'bdt-user-register bdt-user-register-skin-dropdown',
							]
						]
					);
					if (isset($settings['password_strength']) && 'yes' == $settings['password_strength']) {
						$this->parent->add_render_attribute(
							[
								'user_register' => [
									'data-settings' => [
										wp_json_encode(
											array_filter([
												"id"                  => 'bdt-user-register' . $this->parent->get_id(),
												"passStrength"    => true,
												"forceStrongPass" => 'yes' == $settings['force_strong_password']  ? true : false,
											])
										),
									],
								],
							]
						);
					}

				?>
				<div id="<?php echo esc_attr($id); ?>" <?php $this->parent->print_render_attribute_string('user_register'); ?>>
					<a <?php echo $this->parent->get_render_attribute_string( 'modal-button' ); ?>>
						<?php $this->render_text(); ?>
					</a>
				</div>
				<?php
			}

			return;
		}

		$this->parent->form_fields_render_attributes();

		$this->parent->add_render_attribute(
			[
				'modal-button-settings' => [
					'class' => [
						'elementor-button',
						'bdt-button-modal',
						'elementor-size-' . esc_attr($button_size),
						$button_animation ? 'elementor-animation-' . esc_attr($button_animation) : ''
					],
					'href'       => 'javascript:void(0)',
					'data-bdt-toggle' => 'target: #' . esc_attr($id)
				]
			]
		);

			$this->parent->add_render_attribute(
				[
					'user_register' => [
						'class' => 'bdt-user-register bdt-user-register-skin-modal',
					]
				]
			);
			if (isset($settings['password_strength']) && 'yes' == $settings['password_strength']) {
				$this->parent->add_render_attribute(
					[
						'user_register' => [
							'data-settings' => [
								wp_json_encode(
									array_filter([
										"id"                  => 'bdt-user-register' . $this->parent->get_id(),
										"passStrength"    => true,
										"forceStrongPass" => 'yes' == $settings['force_strong_password']  ? true : false,
									])
								),
							],
						],
					]
				);
			}

		?>
		<div <?php $this->parent->print_render_attribute_string('user_register'); ?>>

			<a <?php echo $this->parent->get_render_attribute_string( 'modal-button-settings' ); ?>>
				<?php $this->render_text(); ?>
			</a>
			<div id="<?php echo esc_attr($id); ?>" class="bdt-flex-top bdt-user-register-modal bdt-modal-<?php echo esc_attr($this->get_instance_value('modal_custom_width')); ?>" data-bdt-modal>
				<div class="bdt-modal-dialog bdt-margin-auto-vertical">
					<?php if ($this->get_instance_value('modal_close_button')) : ?>
						<button class="bdt-modal-close-default" type="button" data-bdt-close></button>
					<?php endif; ?>
					<?php if ($this->get_instance_value('modal_header')) : ?>
					<div class="bdt-modal-header">
			            <h2 class="bdt-modal-title"><span class="ep-icon-user-circle-o"></span> <?php esc_html_e('User Registration', 'bdthemes-element-pack'); ?></h2>
			        </div>
					<?php endif; ?>
					<div class="elementor-form-fields-wrapper bdt-modal-body">
						<?php $this->parent->user_register_form(); ?>
					</div>

                    <div class="bdt-recaptcha-text bdt-text-center">
                        This site is protected by reCAPTCHA and the Google <br class="bdt-visible@s">
                        <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                        <a href="https://policies.google.com/terms">Terms of Service</a> apply.
                    </div>
				</div>
    
                
			</div>
		</div>
		<?php

	}

	protected function render_text() {		

		$this->parent->add_render_attribute('button-icon', 'class', ['bdt-modal-button-icon', 'bdt-flex-align-' . esc_attr($this->get_instance_value('modal_button_icon_align'))]);

		if ( is_user_logged_in() && ! Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
			$button_text = esc_html__( 'Logout', 'bdthemes-element-pack' );
		} else {
			$button_text = $this->get_instance_value('modal_button_text');
		}

		$modal_button_icon = $this->get_instance_value('user_register_modal_icon');

		if ( ! isset( $settings['modal_button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['modal_button_icon'] = 'fas fa-user';
		}

		$migrated  = isset( $settings['__fa4_migrated']['user_register_modal_icon'] );
		$is_new    = empty( $settings['modal_button_icon'] ) && Icons_Manager::is_migration_allowed();
		
		?>
		<span class="elementor-button-content-wrapper">
			<?php if ( ! empty( $modal_button_icon['value'] ) ) : ?>
				<span <?php echo $this->parent->get_render_attribute_string('button-icon'); ?>>

					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( (array) $modal_button_icon, [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
					else : ?>
						<i class="<?php echo esc_attr( $settings['modal_button_icon'] ); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</span>
			<?php else : ?>
				<?php $this->parent->add_render_attribute('button-icon', 'class', [ 'bdt-hidden@l' ]); ?>
				<span <?php echo $this->parent->get_render_attribute_string('button-icon'); ?>>
					<i class="ep-icon-lock" aria-hidden="true"></i>
				</span>

			<?php endif; ?>

			<span class="elementor-button-text bdt-visible@l">
				<?php echo esc_html($button_text); ?>
			</span>
		</span>
		<?php
	}

}

