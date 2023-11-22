<?php

namespace ElementPack\Modules\UserLogin\Skins;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Element_Pack_Loader;

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Skin_Modal extends Elementor_Skin_Base {

    protected function _register_controls_actions() {
        parent::_register_controls_actions();

        add_action('elementor/element/bdt-user-login/section_style/before_section_start', [$this, 'register_controls']);
        add_action('elementor/element/bdt-user-login/section_forms_additional_options/before_section_start', [$this, 'register_modal_button_controls']);
        // add_action( 'elementor/element/bdt-user-login/section_style/before_section_start', [ $this, 'register_modal_button_style_controls' ] );

    }

    public function get_id() {
        return 'bdt-modal';
    }

    public function get_title() {
        return __('Modal', 'bdthemes-element-pack');
    }

    public function register_modal_button_controls(Module_Base $widget) {
        $this->parent = $widget;

        $this->start_controls_section(
            'section_modal_button',
            [
                'label' => esc_html__('Modal Button', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'modal_button_text',
            [
                'label'   => esc_html__('Text', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Log In', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'modal_btn_icon_only_on_mobile',
            [
                'label'   => esc_html__('Show Icon Only on Mobile', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'modal_button_size',
            [
                'label'   => esc_html__('Size', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'sm',
                'options' => element_pack_button_sizes(),
            ]
        );

        $this->add_responsive_control(
            'modal_button_align',
            [
                'label'        => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => [
                    'left'    => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
                'default'      => '',
            ]
        );

        $this->add_control(
            'user_login_modal_icon',
            [
                'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
                'type'             => Controls_Manager::ICONS,
                'fa4compatibility' => 'modal_button_icon',
            ]
        );

        $this->add_control(
            'modal_button_icon_align',
            [
                'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'right',
                'options'   => [
                    'left'  => esc_html__('Before', 'bdthemes-element-pack'),
                    'right' => esc_html__('After', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    $this->get_control_id('user_login_modal_icon[value]!') => '',
                ],
            ]
        );

        $this->add_control(
            'modal_button_icon_indent',
            [
                'label'     => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 8,
                ],
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    $this->get_control_id('user_login_modal_icon[value]!') => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-button-modal .bdt-modal-button-icon.elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-button-modal .bdt-modal-button-icon.elementor-align-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function register_controls(Module_Base $widget) {
        $this->parent = $widget;

        $this->start_controls_section(
            'section_modal_style',
            [
                'label' => esc_html__('Modal Style', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'modal_text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '#modal{{ID}} .bdt-modal-dialog .bdt-modal-header *' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'modal_background_color',
            [
                'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
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

        $this->add_control(
            'modal_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '#modal{{ID}} .bdt-modal-dialog' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'modal_text_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '#modal{{ID}} .bdt-modal-dialog .bdt-modal-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'modal_close_button',
            [
                'label'   => esc_html__('Close Button', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'modal_header',
            [
                'label'   => esc_html__('Modal Header', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'modal_custom_width',
            [
                'label'     => esc_html__('Modal Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'default'   => esc_html__('Default', 'bdthemes-element-pack'),
                    'full'      => esc_html__('Full', 'bdthemes-element-pack'),
                    'container' => esc_html__('Container', 'bdthemes-element-pack'),
                    'custom'    => esc_html__('Custom', 'bdthemes-element-pack'),
                ],
                'default'   => 'default',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'modal_custom_width_custom',
            [
                'label'     => esc_html__('Custom Width(px)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '#modal{{ID}}.bdt-modal-custom .bdt-modal-dialog' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $this->get_control_id('modal_custom_width[value]') => 'custom',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render() {
        $settings    = $this->parent->get_settings_for_display();
        $id          = 'modal' . $this->parent->get_id();
        $current_url = remove_query_arg('fake_arg');

        $button_size      = $this->get_instance_value('modal_button_size');
        $button_animation = $this->get_instance_value('modal_button_animation');
        $modal_width = $this->get_instance_value('modal_custom_width');

        $this->parent->add_render_attribute(
            [
                'modal-button' => [
                    'class' => [
                        'elementor-button',
                        'bdt-button-modal',
                        'elementor-size-' . esc_attr($button_size),
                        $this->get_instance_value('modal_button_animation') ? 'elementor-animation-' . esc_attr($button_animation) : ''

                    ],
                    'href'  => '#',
                ]
            ]
        );

        if ( is_user_logged_in() && !Element_Pack_Loader::elementor()->editor->is_edit_mode() ) { ?>


            <?php $current_user = wp_get_current_user(); ?>
            <div id="<?php echo esc_attr($id); ?>" class="bdt-user-login bdt-user-login-skin-dropdown">
                <?php if ( $settings['show_logged_in_content'] ) : ?>
                    <a <?php echo $this->parent->get_render_attribute_string('modal-button'); ?>>

                        <span class="bdt-user-name bdt-visible@l">
                            <?php if ( $settings['show_logged_in_message'] ) : ?>
                                <?php if ( $settings['logged_in_custom_message'] and $settings['custom_labels'] ) : ?>
                                    <?php echo esc_html($settings['logged_in_custom_message']); ?>
                                <?php else : ?>
                                    <?php esc_html_e('Hi', 'bdthemes-element-pack'); ?>,
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ( $settings['show_user_name'] ) : ?>
                                <?php echo esc_html($current_user->display_name); ?>
                            <?php endif; ?>
                        </span>

                        <span class="bdt-user-login-button-avatar<?php echo ('' == $settings['show_avatar_in_button']) ? ' bdt-hidden@l' : ''; ?>"><?php echo get_avatar($current_user->user_email, 32); ?></span>
                    </a>

                    <?php $this->parent->user_dropdown_menu(); ?>

                <?php else : ?>
                    <?php
                    $logout_url = $current_url;
                    if ( isset($settings['redirect_after_logOut'])
                        && !empty($settings['redirect_logOut_url']['url'])
                    ) {
                        $logout_url = $settings['redirect_logOut_url']['url'];
                    }
                    ?>
                    <a class="bdt-logout-button bdt-button bdt-button-primary"
                       href="<?php echo wp_logout_url($logout_url); ?>" class="bdt-ul-logout-menu">
                        <?php echo esc_html($settings['logout_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php


            return;
        }

        $this->parent->form_fields_render_attributes();

        $this->parent->add_render_attribute(
            [
                'modal-button-settings' => [
                    'class'           => [
                        'elementor-button',
                        'bdt-button-modal',
                        'elementor-size-' . esc_attr($button_size),
                        $this->get_instance_value('modal_button_animation') ? 'elementor-animation-' . esc_attr($button_animation) : ''

                    ],
                    'href'            => 'javascript:void(0)',
                    'data-bdt-toggle' => 'target: #' . esc_attr($id),
                ]
            ]
        );

        ?>
        <div class="bdt-user-login bdt-user-login-skin-modal">

            <a <?php echo $this->parent->get_render_attribute_string('modal-button-settings'); ?>>
                <?php $this->render_text(); ?>
            </a>

            <div id="<?php echo esc_attr($id); ?>"
                 class="bdt-flex-top bdt-user-login-modal bdt-modal-<?php echo esc_attr($modal_width); ?>"
                 data-bdt-modal>
                <div class="bdt-modal-dialog bdt-margin-auto-vertical">
                    <?php if ( $this->get_instance_value('modal_close_button') ) : ?>
                        <button class="bdt-modal-close-default" type="button" data-bdt-close></button>
                    <?php endif; ?>
                    <?php if ( $this->get_instance_value('modal_header') ) : ?>
                        <div class="bdt-modal-header">
                            <h2 class="bdt-modal-title"><span
                                        class="ep-icon-user-circle-o"></span> <?php esc_html_e('User Login!', 'bdthemes-element-pack'); ?>
                            </h2>
                        </div>
                    <?php endif; ?>
                    <div class="elementor-form-fields-wrapper bdt-modal-body">
                        <?php $this->parent->user_login_form(); ?>
                        <?php $this->parent->social_login(); ?>
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
        $settings = $this->parent->get_settings_for_display();

        $icon_align = $this->get_instance_value('modal_button_icon_align');

        $this->parent->add_render_attribute('button-icon', 'class', ['bdt-modal-button-icon', 'elementor-button-icon', 'elementor-align-icon-' . esc_attr($icon_align)]);

        if ( is_user_logged_in() && !Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
            $button_text = esc_html__('Logout', 'bdthemes-element-pack');
        } else {
            $button_text = $this->get_instance_value('modal_button_text');
        }

        if ( !isset($settings['modal_button_icon']) && !Icons_Manager::is_migration_allowed() ) {
            // add old default
            $settings['modal_button_icon'] = 'fas fa-user';
        }

        $migrated = isset($settings['__fa4_migrated']['user_login_modal_icon']);
        $is_new   = empty($settings['modal_button_icon']) && Icons_Manager::is_migration_allowed();

        $user_login_modal_icon [] =  $this->get_instance_value('user_login_modal_icon');

        $icon_visible = $this->get_instance_value('modal_btn_icon_only_on_mobile');

        ?>

        <span class="elementor-button-content-wrapper">
			<?php if ( !empty($user_login_modal_icon['value']) ) : ?>

                <span <?php echo $this->parent->get_render_attribute_string('button-icon'); ?>>

					<?php if ( $is_new || $migrated ) :
                        Icons_Manager::render_icon($user_login_modal_icon, ['aria-hidden' => 'true', 'class' => 'fa-fw']);
                    else : ?>
                        <i class="<?php echo esc_attr($settings['modal_button_icon']); ?>" aria-hidden="true"></i>
                    <?php endif; ?>

				</span>

            <?php else : ?>

                <?php if ( $icon_visible ) : ?>
                    <?php $this->parent->add_render_attribute('button-icon', 'class', ['bdt-hidden@l']); ?>
                    <span <?php echo $this->parent->get_render_attribute_string('button-icon'); ?>>
                        <i class="ep-icon-lock" aria-hidden="true"></i>
                    </span>

                <?php endif; ?>

            <?php endif; ?>


            <?php $text_visible = ($this->get_instance_value('modal_btn_icon_only_on_mobile')) ? ' bdt-visible@l' : ''; ?>

			<span class="elementor-button-text<?php echo esc_attr($text_visible); ?>">
				<?php echo esc_html($button_text); ?>
			</span>
		</span>
        <?php
    }

}

