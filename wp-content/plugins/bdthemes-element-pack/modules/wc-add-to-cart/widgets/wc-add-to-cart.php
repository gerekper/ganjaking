<?php

namespace ElementPack\Modules\WcAddToCart\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;


use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use ElementPack\Element_Pack_Loader;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// class Add_To_Cart extends Widget_Button {
class WC_Add_To_Cart extends Module_Base {

	public function get_name() {
		return 'bdt-wc-add-to-cart';
	}

	public function get_title() {
		return BDTEP . esc_html__('WC - Add To Cart', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-wc-add-to-cart';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['add', 'to', 'cart', 'woocommerce', 'wc', 'add to cart'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-wc-add-to-cart', 'datatables'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/471vvaA9WQY';
	}

	public function on_export($element) {
		unset($element['settings']['product_id']);

		return $element;
	}

	public function unescape_html($safe_text, $text) {
		return $text;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_product',
			[
				'label' => esc_html__('Product', 'bdthemes-element-pack'),
			]
		);

		$post_list = get_posts(['numberposts' => 50, 'post_type' => 'product',]);

		$post_list_options = ['0' => esc_html__('Select Post', 'bdthemes-element-pack')];

		foreach ($post_list as $list) :
			$post_list_options[$list->ID] = $list->post_title;
		endforeach;

		$this->add_control(
			'product_id',
			[
				'label' => esc_html__('Product', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $post_list_options,
				'default'     => ['0'],
			]
		);

		$this->add_control(
			'show_quantity',
			[
				'label'     => esc_html__('Show Quantity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__('Hide', 'bdthemes-element-pack'),
				'label_on'  => esc_html__('Show', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quantity',
			[
				'label'     => esc_html__('Quantity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'condition' => [
					'show_quantity' => '',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_button',
			[
				'label' => __('Button', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_type',
			[
				'label' => __('Type', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __('Default', 'bdthemes-element-pack'),
					'info' => __('Info', 'bdthemes-element-pack'),
					'success' => __('Success', 'bdthemes-element-pack'),
					'warning' => __('Warning', 'bdthemes-element-pack'),
					'danger' => __('Danger', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'elementor-button-',
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __('Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('Add to Cart', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Add to Cart', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __('Alignment', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __('Extra Small', 'bdthemes-element-pack'),
					'sm' => __('Small', 'bdthemes-element-pack'),
					'md' => __('Medium', 'bdthemes-element-pack'),
					'lg' => __('Large', 'bdthemes-element-pack'),
					'xl' => __('Extra Large', 'bdthemes-element-pack'),
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => __('Icon Position', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __('Before', 'bdthemes-element-pack'),
					'right' => __('After', 'bdthemes-element-pack'),
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => __('Icon Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __('View', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => __('Button ID', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack'),
				'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
				'separator' => 'before',

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __('Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				// ],
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '#1E87F0',
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => __('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __('Hover Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .elementor-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'qty_style',
			[
				'label'     => __('Quantity Field', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_quantity' => 'yes'
				]
			]
		);

		$this->add_control(
			'qty_fields_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .quantity'  => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'qty_fields_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quantity input[type=number]' => 'color: {{VALUE}} ',
					'{{WRAPPER}} .quantity input[type=number]::placeholder' => 'color: {{VALUE}} ',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'qty_fields_background',
				'selector'  => '{{WRAPPER}} .quantity input[type=number]',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qty_fields_typography',
				'selector' => '{{WRAPPER}} .quantity input[type=number]',
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'qty_fields_shadow',
				'selector' => '{{WRAPPER}} .quantity input[type=number]'
			]
		);



		$this->add_responsive_control(
			'qty_fields_padding',
			[
				'label'     => __('Padding', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .quantity input[type=number]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'qty_fields_border',
				'label'    => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .quantity input[type=number]',
			]
		);

		$this->add_responsive_control(
			'qty_fields_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .quantity input[type=number]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (!empty($settings['product_id'])) {
			$product_id = $settings['product_id'];
		} elseif (wp_doing_ajax()) {
			$product_id = esc_attr($_POST['post_id']);
		} else {
			$product_id = get_queried_object_id();
		}

		global $product;
		$product = wc_get_product($product_id);

		if ('yes' === $settings['show_quantity']) {
			$this->render_form_button($product);
		} else {
			$this->render_ajax_button($product);
		}
	}

	/**
	 * @param \WC_Product $product
	 */
	private function render_ajax_button($product) {
		$settings = $this->get_settings_for_display();

		if ($product) {
			if (version_compare(WC()->version, '3.0.0', '>=')) {
				$product_type = $product->get_type();
			} else {
				$product_type = $product->product_type;
			}

			$class = implode(' ', array_filter([
				'product_type_' . $product_type,
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : '',
			]));

			$this->add_render_attribute(
				'button',
				[
					'rel' => 'nofollow',
					'href' => $product->add_to_cart_url(),
					'data-quantity' => (isset($settings['quantity']) ? $settings['quantity'] : 1),
					'data-product_id' => $product->get_id(),
					'class' => $class,
				]
			);
		} elseif (current_user_can('manage_options')) {
			$settings['text'] = __('Please set a valid product', 'bdthemes-element-pack');
			$this->set_settings($settings);
		}


		$this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');

		if (!empty($settings['link']['url'])) {
			$this->add_link_attributes('button', $settings['link']);
			$this->add_render_attribute('button', 'class', 'elementor-button-link');
		}

		$this->add_render_attribute('button', 'class', 'elementor-button');
		$this->add_render_attribute('button', 'role', 'button');

		if (!empty($settings['button_css_id'])) {
			$this->add_render_attribute('button', 'id', $settings['button_css_id']);
		}

		if (!empty($settings['size'])) {
			$this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
		}

		if ($settings['hover_animation']) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
		}

?>
		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
			<a <?php echo $this->get_render_attribute_string('button'); ?>>
				<?php $this->render_text(); ?>
			</a>
		</div>

	<?php
	}
	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$migrated = isset($settings['__fa4_migrated']['selected_icon']);
		$is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

		if (!$is_new && empty($settings['icon_align'])) {
			// @todo: remove when deprecated
			// added as bc in 2.6
			//old default
			$settings['icon_align'] = $this->get_settings('icon_align');
		}

		$this->add_render_attribute([
			'content-wrapper' => [
				'class' => 'elementor-button-content-wrapper',
			],
			'icon-align' => [
				'class' => [
					'elementor-button-icon',
					'elementor-align-icon-' . $settings['icon_align'],
				],
			],
			'text' => [
				'class' => 'elementor-button-text',
			],
		]);

		$this->add_inline_editing_attributes('text', 'none');
	?>
		<span <?php echo $this->get_render_attribute_string('content-wrapper'); ?>>
			<?php if (!empty($settings['icon']) || !empty($settings['selected_icon']['value'])) : ?>
				<span <?php echo $this->get_render_attribute_string('icon-align'); ?>>
					<?php if ($is_new || $migrated) :
						Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
					else : ?>
						<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo esc_html($settings['text']); ?></span>
		</span>
<?php
	}

	private function render_form_button($product) {
		if (!$product && current_user_can('manage_options')) {
			echo __('Please set a valid product', 'bdthemes-element-pack');

			return;
		}

		$text_callback = function () {
			ob_start();
			$this->render_text();

			return ob_get_clean();
		};

		add_filter('woocommerce_get_stock_html', '__return_empty_string');
		add_filter('woocommerce_product_single_add_to_cart_text', $text_callback);
		add_filter('esc_html', [$this, 'unescape_html'], 10, 2);

		ob_start();
		woocommerce_template_single_add_to_cart();
		$form = ob_get_clean();
		$form = str_replace('single_add_to_cart_button', 'single_add_to_cart_button elementor-button', $form);
		echo $form;

		remove_filter('woocommerce_product_single_add_to_cart_text', $text_callback);
		remove_filter('woocommerce_get_stock_html', '__return_empty_string');
		remove_filter('esc_html', [$this, 'unescape_html']);
	}

	// Force remote render
	protected function content_template() {
	}
}
