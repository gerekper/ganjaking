<?php

namespace ElementPack\Modules\UserRegister\Skins;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Dropdown extends Elementor_Skin_Base
{

	protected function _register_controls_actions()
	{
		parent::_register_controls_actions();

		add_action('elementor/element/bdt-user-register/section_style/before_section_start', [$this, 'register_controls']);
		add_action('elementor/element/bdt-user-register/section_forms_additional_options/before_section_start', [$this, 'register_dropdown_button_controls']);
	}

	public function get_id()
	{
		return 'bdt-dropdown';
	}

	public function get_title()
	{
		return __('Dropdown', 'bdthemes-element-pack');
	}

	public function register_dropdown_button_controls(Module_Base $widget)
	{

		$this->parent = $widget;

		$this->start_controls_section(
			'section_dropdown_button',
			[
				'label' => esc_html__('Dropdown Button', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dropdown_button_text',
			[
				'label'   => esc_html__('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__('Register', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'dropdown_button_size',
			[
				'label'   => esc_html__('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => element_pack_button_sizes(),
			]
		);

		$this->add_responsive_control(
			'dropdown_button_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
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
			'user_register_dropdown_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'dropdown_button_icon',
			]
		);

		$this->add_control(
			'dropdown_button_icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__('Before', 'bdthemes-element-pack'),
					'right' => esc_html__('After', 'bdthemes-element-pack'),
				],
				'condition' => [
					$this->get_control_id('user_register_dropdown_icon[value]!') => '',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_button_icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
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
					$this->get_control_id('user_register_dropdown_icon[value]!') => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-button-dropdown .bdt-flex-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-button-dropdown .bdt-flex-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function register_controls(Module_Base $widget)
	{
		$this->parent = $widget;

		$this->start_controls_section(
			'section_dropdown_style',
			[
				'label' => esc_html__('Dropdown Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#dropdown{{ID}}.bdt-user-register .bdt-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dropdown_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#dropdown{{ID}}.bdt-user-register .bdt-dropdown',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'#dropdown{{ID}}.bdt-user-register .bdt-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_text_padding',
			[
				'label'      => esc_html__('Text Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#dropdown{{ID}}.bdt-user-register .bdt-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dropdown_offset',
			[
				'label' => esc_html__('Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);

		$this->add_control(
			'dropdown_position',
			[
				'label'   => esc_html__('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-right',
				'options' => element_pack_drop_position(),
			]
		);

		$this->add_control(
			'dropdown_mode',
			[
				'label'   => esc_html__('Mode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
					'click' => esc_html__('Clicked', 'bdthemes-element-pack'),
				],
			]
		);

		$this->end_controls_section();
	}

	public function render()
	{
		$id       = 'dropdown' . $this->parent->get_id();

		$settings    = $this->parent->get_settings();
		$current_url = remove_query_arg('fake_arg');
		$button_size = $this->get_instance_value('dropdown_button_size');
		$button_animation = $this->get_instance_value('dropdown_button_animation');

		if ($settings['redirect_after_register'] && !empty($settings['redirect_url']['url'])) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}

		$dropdown_offset = $this->get_instance_value('dropdown_offset');

		if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) {

			$this->parent->add_render_attribute(
				[
					'dropdown-settings' => [
						'class'         => 'bdt-dropdown',
						'data-bdt-dropdown' => [
							wp_json_encode(array_filter([
								"mode"   => "click",
								"pos"    => $this->get_instance_value("dropdown_position"),
								"offset" => $dropdown_offset["size"]
							]))
						]
					]
				]
			);
		} else {

			$this->parent->add_render_attribute(
				[
					'dropdown-settings' => [
						'class'        => 'bdt-dropdown',
						'data-bdt-dropdown' => [
							wp_json_encode(array_filter([
								"mode"   => $this->get_instance_value("dropdown_mode"),
								"pos"    => $this->get_instance_value("dropdown_position"),
								"offset" => $dropdown_offset["size"]
							]))
						]
					]
				]
			);
		}

		$this->parent->add_render_attribute(
			[
				'dropdown-button' => [
					'class' => [
						'elementor-button',
						'bdt-button-dropdown',
						'elementor-size-' . esc_attr($button_size),
						$button_animation ? 'elementor-animation-' . esc_attr($button_animation) : ''
					],
					'href' => wp_logout_url($current_url)
				]
			]
		);

		if (is_user_logged_in() && !Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			if ($settings['show_logged_in_message']) {
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
					<a <?php echo $this->parent->get_render_attribute_string('dropdown-button'); ?>>
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
				'dropdown-button-settings' => [
					'class' => [
						'elementor-button',
						'bdt-button-dropdown',
						'elementor-size-' . esc_attr($button_size),
						$button_animation ? 'elementor-animation-' . esc_attr($button_animation) : ''
					],
					'href' => 'javascript:void(0)'
				]
			]
		);

		$this->parent->add_render_attribute(
			[
				'user_register' => [
					'id' => $id,
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
		<div <?php $this->parent->print_render_attribute_string('user_register'); ?>>
			<a <?php echo $this->parent->get_render_attribute_string('dropdown-button-settings'); ?>>
				<?php $this->render_text(); ?>
			</a>

			<div <?php echo $this->parent->get_render_attribute_string('dropdown-settings'); ?>>
				<div class="elementor-form-fields-wrapper bdt-text-left">
					<?php $this->parent->user_register_form(); ?>
				</div>
			</div>
		</div>
	<?php

	}

	protected function render_text()
	{
		$settings = $this->parent->get_settings_for_display();
		$button_align = $this->get_instance_value('dropdown_button_icon_align');

		$this->parent->add_render_attribute(
			[
				'button-icon' => [
					'class' => [
						'bdt-dropdown-button-icon',
						'bdt-flex-align-' . esc_attr($button_align)
					],
				]
			]
		);

		$dropdown_icon = $this->get_instance_value('user_register_dropdown_icon');

		if (is_user_logged_in() && !Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			$button_text = esc_html__('Logout', 'bdthemes-element-pack');
		} else {
			$button_text = $this->get_instance_value('dropdown_button_text');
		}

		if (!isset($settings['dropdown_button_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['dropdown_button_icon'] = 'fas fa-user';
		}

		$migrated  = isset($settings['__fa4_migrated']['user_register_dropdown_icon']);
		$is_new    = empty($settings['dropdown_button_icon']) && Icons_Manager::is_migration_allowed();


	?>

		<span class="elementor-button-content-wrapper">
			<?php if (!empty($dropdown_icon['value'])) : ?>
				<span <?php echo $this->parent->get_render_attribute_string('button-icon'); ?>>

					<?php if ($is_new || $migrated) :
						Icons_Manager::render_icon((array) $dropdown_icon, ['aria-hidden' => 'true', 'class' => 'fa-fw']);
					else : ?>
						<i class="<?php echo esc_attr($settings['dropdown_button_icon']); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</span>
			<?php else : ?>
				<?php $this->parent->add_render_attribute('button-icon', 'class', ['bdt-hidden@l']); ?>
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
