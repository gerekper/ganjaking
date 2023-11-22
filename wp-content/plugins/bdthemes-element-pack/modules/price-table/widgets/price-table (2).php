<?php

namespace ElementPack\Modules\PriceTable\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;

use ElementPack\Modules\PriceTable\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Price_Table extends Module_Base {

	public function get_name() {
		return 'bdt-price-table';
	}

	public function get_title() {
		return BDTEP . __('Price Table', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-price-table';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['price', 'table', 'rate', 'cost', 'value', 'pricing'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-price-table', 'tippy'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['popper', 'tippyjs', 'ep-scripts'];
		} else {
			return ['popper', 'tippyjs', 'ep-price-table'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/D8_inzgdvyg';
	}

	protected function register_skins() {
		$this->add_skin(new Skins\Skin_Partait($this));
		$this->add_skin(new Skins\Skin_Erect($this));
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __('Layout', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => __('Default', 'bdthemes-element-pack'),
					'2' => __('Two (Features and Price interchange)', 'bdthemes-element-pack'),
					'3' => __('Three (Features in at Last)', 'bdthemes-element-pack'),
					'4' => __('Four (Header in at Middle)', 'bdthemes-element-pack'),
					'5' => __('Five (No Features List)', 'bdthemes-element-pack'),
					'6' => __('Six (Image Under Header)', 'bdthemes-element-pack'),
					'7' => __('Seven (Image Under Features)', 'bdthemes-element-pack'),
					'8' => __('Eight (Header Under Pricing)', 'bdthemes-element-pack'),
					'9' => __('Nine (Header & Pricing Inline)', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'wrapper_overflow_hidden',
			[
				'label'        => __('Overflow Hidden', 'bdthemes-element-pack') . BDTEP_NC,
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'separator'    => 'before',
				'prefix_class' => 'bdt-pt-overflow-hidden--',
				'render_type'  => 'template'
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_image',
			[
				'label' => __('Image', 'bdthemes-element-pack'),
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __('Choose Image', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'label'     => esc_html__( 'Image Size', 'bdthemes-element-pack' ) . BDTEP_NC,
				'exclude'   => [ 'custom' ],
				'default'   => 'thumbnail',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_header',
			[
				'label' => __('Header', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'heading',
			[
				'label'   => __('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Service Name', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label'   => __('HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_title_tags(),
				'default' => 'h3',
			]
		);

		$this->add_control(
			'sub_heading',
			[
				'label'     => __('Subtitle', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Service sub title', 'bdthemes-element-pack'),
				'condition' => [
					'_skin!' => 'bdt-partait',
				],
			]
		);

		$this->add_control(
			'sticky_heading',
			[
				'label'     => __('Sticky Heading', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_pricing',
			[
				'label' => __('Pricing', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'currency_symbol',
			[
				'label'   => __('Currency Symbol', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''             => __('None', 'bdthemes-element-pack'),
					'dollar'       => '&#36; ' 	. _x('Dollar', 'Currency Symbol', 'bdthemes-element-pack'),
					'euro'         => '&#128; ' . _x('Euro', 'Currency Symbol', 'bdthemes-element-pack'),
					'baht'         => '&#3647; ' . _x('Baht', 'Currency Symbol', 'bdthemes-element-pack'),
					'franc'        => '&#8355; ' . _x('Franc', 'Currency Symbol', 'bdthemes-element-pack'),
					'guilder'      => '&fnof; ' . _x('Guilder', 'Currency Symbol', 'bdthemes-element-pack'),
					'krona'        => 'kr ' 	. _x('Krona', 'Currency Symbol', 'bdthemes-element-pack'),
					'lira'         => '&#8356; ' . _x('Lira', 'Currency Symbol', 'bdthemes-element-pack'),
					'peseta'       => '&#8359 ' . _x('Peseta', 'Currency Symbol', 'bdthemes-element-pack'),
					'peso'         => '&#8369; ' . _x('Peso', 'Currency Symbol', 'bdthemes-element-pack'),
					'pound'        => '&#163; ' . _x('Pound Sterling', 'Currency Symbol', 'bdthemes-element-pack'),
					'real'         => 'R$ ' 	. _x('Real', 'Currency Symbol', 'bdthemes-element-pack'),
					'ruble'        => '&#8381; ' . _x('Ruble', 'Currency Symbol', 'bdthemes-element-pack'),
					'rupee'        => '&#8360; ' . _x('Rupee', 'Currency Symbol', 'bdthemes-element-pack'),
					'indian_rupee' => '&#8377; ' . _x('Rupee (Indian)', 'Currency Symbol', 'bdthemes-element-pack'),
					'shekel'       => '&#8362; ' . _x('Shekel', 'Currency Symbol', 'bdthemes-element-pack'),
					'yen'          => '&#165; ' . _x('Yen/Yuan', 'Currency Symbol', 'bdthemes-element-pack'),
					'bdt'          => '&#2547; ' . _x('Taka', 'Currency Symbol', 'bdthemes-element-pack'),
					'won'          => '&#8361; ' . _x('Won', 'Currency Symbol', 'bdthemes-element-pack'),
					'custom'       => __('Custom', 'bdthemes-element-pack'),
				],
				'default' => 'dollar',
			]
		);

		$this->add_control(
			'currency_symbol_custom',
			[
				'label'     => __('Custom Symbol', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'currency_symbol' => 'custom',
				],
			]
		);

		$this->add_control(
			'price',
			[
				'label'   => __('Price', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => '49.99',
			]
		);


		$this->add_control(
			'currency_format',
			[
				'label' => __('Currency Format', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => '1,234.56 (Default)',
					',' => '1.234,56',
				],
			]
		);

		$this->add_control(
			'price_add_custom_attributes',
			[
				'label'     => __('Add Custom Attributes', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'price_custom_attributes',
			[
				'label' => __('Custom Attributes', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('key|value', 'bdthemes-element-pack'),
				'description' => sprintf(__('Set custom attributes for the price table button tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack'), '<code>|</code>'),
				'classes' => 'elementor-control-direction-ltr',
				'condition' => ['price_add_custom_attributes' => 'yes']
			]
		);

		$this->add_control(
			'sale',
			[
				'label'     => __('Sale', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'original_price',
			[
				'label'     => __('Original Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '79',
				'condition' => [
					'sale' => 'yes',
				],
			]
		);

		$this->add_control(
			'sale_add_custom_attributes',
			[
				'label'     => __('Add Custom Attributes', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'sale' => 'yes',
				],
			]
		);

		$this->add_control(
			'sale_custom_attributes',
			[
				'label' => __('Custom Attributes', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('key|value', 'bdthemes-element-pack'),
				'description' => sprintf(__('Set custom attributes for the price table button tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack'), '<code>|</code>'),
				'classes' => 'elementor-control-direction-ltr',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'sale_add_custom_attributes',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'sale',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);

		$this->add_control(
			'period',
			[
				'label'   => __('Period', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Monthly', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'sticky_pricing',
			[
				'label'     => __('Sticky Pricing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HIDDEN,
				'default' 	=> 'no',
				'separator' => 'before'
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_features',
			[
				'label'     => __('Features', 'bdthemes-element-pack'),
				'condition' => [
					'layout!' => '5',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('features_list_tabs');

		$repeater->start_controls_tab(
			'features_list_tab_normal_text',
			[
				'label' => __('Normal Text', 'bdthemes-element-pack')
			]
		);

		$repeater->add_control(
			'item_text',
			[
				'label'   => __('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('List Item', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'price_table_item_icon',
			[
				'label'   => __('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'item_icon',
				'default' => [
					'value' => 'fas fa-check',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'item_icon_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-price-table {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
				],
				'render_type' => 'template',
			]
		);

		$repeater->add_control(
			'item_icon_hover_color',
			[
				'label'     => __('Icon Hover Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-price-table:hover {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
				],
				'render_type' => 'template',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'features_list_tab_tooltip_text',
			[
				'label' => __('Tooltip Text', 'bdthemes-element-pack')
			]
		);

		$repeater->add_control(
			'tooltip_text',
			[
				'label' => __('Text', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'tooltip_placement',
			[
				'label'   => __('Placement', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'top'    => __('Top', 'bdthemes-element-pack'),
					'bottom' => __('Bottom', 'bdthemes-element-pack'),
					'left'   => __('Left', 'bdthemes-element-pack'),
					'right'  => __('Right', 'bdthemes-element-pack'),
				],
				'condition'   => [
					'tooltip_text!' => '',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'features_list',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'item_text' 			 => __('List Item #1', 'bdthemes-element-pack'),
						'price_table_item_icon'  => ['value' => 'fas fa-check', 'library' => 'fa-solid'],
					],
					[
						'item_text' => __('List Item #2', 'bdthemes-element-pack'),
						'price_table_item_icon'  => ['value' => 'fas fa-check', 'library' => 'fa-solid'],
					],
					[
						'item_text' => __('List Item #3', 'bdthemes-element-pack'),
						'price_table_item_icon'  => ['value' => 'fas fa-check', 'library' => 'fa-solid'],
					],
				],
				'title_field' => '{{{ item_text }}}',
			]
		);

		$this->add_control(
			'features_hide_on',
			[
				'label'       => __('Features Hide On', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => [
					'desktop' => __('Desktop', 'bdthemes-element-pack'),
					'tablet'  => __('Tablet', 'bdthemes-element-pack'),
					'mobile'  => __('Mobile', 'bdthemes-element-pack'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'features_alignment',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_footer',
			[
				'label' => __('Footer', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => __('Button Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Select Plan', 'bdthemes-element-pack'),
			]
		);

		if (class_exists('Easy_Digital_Downloads')) {
			$edd_posts = get_posts(['numberposts' => 10, 'post_type'   => 'download']);
			$options = ['0' => __('Select EDD', 'bdthemes-element-pack')];
			foreach ($edd_posts as $edd_post) {
				$options[$edd_post->ID] = $edd_post->post_title;
			}
		} else {
			$options = ['0' => __('Not found', 'bdthemes-element-pack')];
		}

		$this->add_control(
			'edd_as_button',
			[
				'label' => __('Easy Digital Download Integration', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);


		$this->add_control(
			'edd_id',
			[
				'label'       => __('Easy Digital Download Item', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => $options,
				'label_block' => true,
				'condition'   => [
					'edd_as_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'edd_as_button' => '',
				],
			]
		);

		$this->add_control(
			'add_custom_attributes',
			[
				'label'     => __('Add Custom Attributes', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_attributes',
			[
				'label' => __('Custom Attributes', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('key|value', 'bdthemes-element-pack'),
				'description' => sprintf(__('Set custom attributes for the price table button tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack'), '<code>|</code>'),
				'classes' => 'elementor-control-direction-ltr',
				'condition' => ['add_custom_attributes' => 'yes']
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => __('Button ID', 'bdthemes-element-pack') . BDTEP_NC,
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

		$this->add_control(
			'footer_additional_info',
			[
				'label'     => __('Additional Info', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __('This is footer text', 'bdthemes-element-pack'),
				'rows'      => 2,
				'condition' => [
					'_skin' => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_ribbon',
			[
				'label' => __('Ribbon', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_ribbon',
			[
				'label'     => __('Show', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ribbon_title',
			[
				'label'     => __('Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Popular', 'bdthemes-element-pack'),
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ribbon_align',
			[
				'label'   => __('Align', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justify', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => 'left',
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ribbon_horizontal_position',
			[
				'label' => __('Horizontal Position', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
				],
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'show_ribbon' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-ribbon-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'ribbon_vertical_position',
			[
				'label' => __('Vertical Position', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
				],
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'show_ribbon' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-ribbon-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'ribbon_rotate',
			[
				'label'   => __('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
						'step' => 5,
					],
				],
				'condition' => [
					'show_ribbon' => 'yes',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-ribbon-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => __('Image', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'image[url]!' => '',
					'_skin' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'image_bg_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-image',
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_image_style');

		$this->start_controls_tab(
			'tabs_image_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label'   => __('Size (%)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'opacity',
			[
				'label'   => __('Opacity', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'image_background_color',
				'selector' => '{{WRAPPER}} .bdt-price-table img',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'image_border',
				'label'     => __('Image Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-price-table img',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_inner_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_shadow',
				'exclude' => [
					'shadow_position',
				],
				'selector' => '{{WRAPPER}} .bdt-price-table img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_image_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label'   => __('Opacity', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'image_background_hover_color',
				'selector' => '{{WRAPPER}} .bdt-price-table:hover img',
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'image_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover img' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label'     => __('Hover Animation', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'separator' => 'before'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __('Header', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'header_bg_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-header',
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'header_box_shadow',
				'label'      => __('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-price-table-header',
			]
		);

		$this->add_responsive_control(
			'heading_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-header' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_header_style');

		$this->start_controls_tab(
			'tabs_header_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'heading_heading_style',
			[
				'label'     => __('Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'heading_shadow',
				'label' => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-price-table-heading',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .bdt-price-table-heading',
			]
		);

		$this->add_control(
			'heading_sub_heading_style',
			[
				'label'     => __('Sub Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sub_heading_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-subheading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'sub_heading_shadow',
				'label' => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-price-table-subheading',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_heading_typography',
				'selector' => '{{WRAPPER}} .bdt-price-table-subheading',
			]
		);

		$this->add_responsive_control(
			'sub_title_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-subheading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_header_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'heading_hover_color',
			[
				'label'     => __('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sub_heading_hover_color',
			[
				'label'     => __('Sub Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-subheading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pricing',
			[
				'label' => __('Pricing', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
            'pricing_align',
            [
                'label'     => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors_dictionary' => [
                    'left' => 'justify-content: flex-start; text-align: left;',
                    'right' => 'justify-content: flex-end; text-align: right;',
                    'center' => '    justify-content: center; text-align: center;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-table-price' => '{{VALUE}};',
                ],
            ]
        );

		$this->start_controls_tabs('tabs_pricing_style');

		$this->start_controls_tab(
			'tabs_pricing_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'pricing_element_bg_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-price',
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-currency, {{WRAPPER}} .bdt-price-table-integer-part, {{WRAPPER}} .bdt-price-table-fractional-part' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'pricing_border',
				'selector'    => '{{WRAPPER}} .bdt-price-table-price',
			]
		);

		$this->add_responsive_control(
			'readmore_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pricing_element_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pricing_element_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'price_shadow',
				'label' => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-price-table-currency, {{WRAPPER}} .bdt-price-table-integer-part, {{WRAPPER}} .bdt-price-table-fractional-part',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .bdt-price-table-price',
			]
		);

		$this->add_control(
			'heading_currency_style',
			[
				'label'     => __('Currency Symbol', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'currency_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-currency' => 'font-size: calc({{SIZE}}em/100)',
				],
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'currency_horizontal_position',
			[
				'label'   => __('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'currency_vertical_position',
			[
				'label'   => __('Vertical Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-currency' => 'align-self: {{VALUE}}',
				],
				'condition' => [
					'currency_symbol!' => '',
				],
			]
		);

		$this->add_control(
			'fractional_part_style',
			[
				'label'     => __('Fractional Part', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'currency_format' => '',
				]
			]
		);

		$this->add_control(
			'fractional-part_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-fractional-part' => 'font-size: calc({{SIZE}}em/100)',
				],
				'condition' => [
					'currency_format' => '',
				]
			]
		);

		$this->add_control(
			'fractional_part_vertical_position',
			[
				'label'   => __('Vertical Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-after-price' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'currency_format' => '',
				]
			]
		);

		$this->add_control(
			'heading_original_price_style',
			[
				'label'     => __('Original Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'sale'            => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_control(
			'original_price_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-original-price' => 'color: {{VALUE}}',
				],
				'condition' => [
					'sale'            => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'original_price_typography',
				'selector'  => '{{WRAPPER}} .bdt-price-table-original-price',
				'condition' => [
					'sale'            => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_control(
			'original_price_vertical_position',
			[
				'label'   => __('Vertical Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'top: 0;',
					'middle' => 'top: 40%;',
					'bottom' => 'bottom: 0;',
				],
				'default'   => 'middle',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-original-price' => '{{VALUE}}',
				],
				'condition' => [
					'sale'            => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->add_control(
			'original_price_offset_toggle',
			[
				'label' => __('Offset', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('None', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
				'condition' => [
					'sale'            => 'yes',
					'original_price!' => '',
				],
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'original_price_horizontal_offset',
			[
				'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'original_price_offset_toggle' => 'yes',
					'sale'            => 'yes',
					'original_price!' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-pt-original-price-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'original_price_vertical_offset',
			[
				'label' => __('Vertical Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -300,
						'step' => 2,
						'max' => 300,
					],
				],
				'condition' => [
					'original_price_offset_toggle' => 'yes',
					'sale'            => 'yes',
					'original_price!' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-pt-original-price-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'original_price_rotate',
			[
				'label' => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'original_price_offset_toggle' => 'yes',
					'sale'            => 'yes',
					'original_price!' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-pt-original-price-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'heading_period_style',
			[
				'label'     => __('Period', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'period!' => '',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'period_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-period' => 'color: {{VALUE}}',
				],
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'period_typography',
				'selector'  => '{{WRAPPER}} .bdt-price-table-period',
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->add_control(
			'period_position',
			[
				'label'   => __('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'below'  => 'Below',
					'beside' => 'Beside',
				],
				'default'   => 'below',
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_pricing_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'pricing_element_hover_bg_color',
				'selector' => '{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-price',
			]
		);

		$this->add_control(
			'pricing_table_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'pricing_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-price' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_hover_color',
			[
				'label'     => __('Price Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-currency, {{WRAPPER}} .bdt-price-table:hover .bdt-price-table-integer-part, {{WRAPPER}} .bdt-price-table:hover .bdt-price-table-fractional-part' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'period_hover_color',
			[
				'label'     => __('Period Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-period' => 'color: {{VALUE}}',
				],
				'condition' => [
					'period!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_features',
			[
				'label'     => __('Features', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout!' => '5',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'features_list_bg_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-features-list',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'features_list_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-table-features-list',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'features_list_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-features-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_list_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-features-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'features_list_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-features-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_style_features');

		$this->start_controls_tab(
			'tab_features_normal_text',
			[
				'label' => __('Normal Text', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'features_list_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list, {{WRAPPER}} .edd_price_options li span, {{WRAPPER}} .bdt-price-table-features-list a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-price-table-features-list svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'features_list_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-features-list, {{WRAPPER}} .bdt-price-table:hover .edd_price_options li span, {{WRAPPER}} .bdt-price-table:hover .bdt-price-table-features-list a, {{WRAPPER}} .bdt-price-table .bdt-price-table-features-list a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-features-list svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'features_list_typography',
				'selector' => '{{WRAPPER}} .bdt-price-table-features-list li, {{WRAPPER}} .edd_price_options li span',
			]
		);

		$this->add_responsive_control(
			'features_list_alignment',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'item_width',
			[
				'label' => __('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 25,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-feature-inner' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_responsive_control(
			'features_list_inner_padding',
			[
				'label'      => __('List Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-features-list .bdt-price-table-feature-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'list_striped',
			[
				'label'     => __('Striped', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'prefix_class' => 'bdt-price-table-striped--',
			]
		);

		$this->add_control(
			'list_striped_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'list_striped' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:nth-of-type(odd)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'list_striped_bg_color',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'list_striped' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:nth-of-type(odd)' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'list_divider',
			[
				'label'     => __('Divider', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
				'prefix_class' => 'bdt-price-table-divider--',
				'condition' => [
					'list_striped' => ''
				]
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'   => __('Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'solid'  => __('Solid', 'bdthemes-element-pack'),
					'double' => __('Double', 'bdthemes-element-pack'),
					'dotted' => __('Dotted', 'bdthemes-element-pack'),
					'dashed' => __('Dashed', 'bdthemes-element-pack'),
				],
				'default'   => 'solid',
				'condition' => [
					'list_divider' => 'yes',
					'list_striped' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:before' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ddd',
				'condition' => [
					'list_divider' => 'yes',
					'list_striped' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:before' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'list_divider' => 'yes',
					'list_striped' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-features-list li:before' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'   => __('Weight', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'list_divider' => 'yes',
					'list_striped' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:before' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label'     => __('Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'list_divider' => 'yes',
					'list_striped' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:before' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_control(
			'divider_gap',
			[
				'label'   => __('Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'list_divider' => 'yes',
					'list_striped' => ''
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-features-list li:before' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_features_tooltip_text',
			[
				'label' => __('Tooltip Text', 'bdthemes-element-pack')
			]
		);

		$this->add_responsive_control(
			'features_tooltip_width',
			[
				'label'      => esc_html__('Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'features_tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'features_tooltip_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'features_tooltip_text_align',
			[
				'label'   => esc_html__('Text Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
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
				],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'features_tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_control(
			'features_tooltip_arrow_color',
			[
				'label'     => esc_html__('Arrow Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'features_tooltip_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'features_tooltip_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'features_tooltip_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'features_tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_footer',
			[
				'label' => __('Footer', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'footer_bg_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-footer',
			]
		);

		$this->add_responsive_control(
			'footer_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'footer_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-footer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_footer_button',
			[
				'label'     => __('Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'   => __('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => [
					'md' => __('Default', 'bdthemes-element-pack'),
					'sm' => __('Small', 'bdthemes-element-pack'),
					'xs' => __('Extra Small', 'bdthemes-element-pack'),
					'lg' => __('Large', 'bdthemes-element-pack'),
					'xl' => __('Extra Large', 'bdthemes-element-pack'),
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'     => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-button',
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-table-button',
				'condition'   => [
					'button_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_vertical_offset',
			[
				'label' => __('Vertical offset', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-btn-wrap' => 'transform: translateY({{SIZE}}px)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-table-button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'selector'  => '{{WRAPPER}} .bdt-price-table-button',
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'     => __('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-button:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_hover_color',
				'selector' => '{{WRAPPER}} .bdt-price-table-button:hover',
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label'     => __('Animation', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'heading_additional_info',
			[
				'label'     => __('Additional Info', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'footer_additional_info!' => '',
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'additional_info_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-additional_info' => 'color: {{VALUE}}',
				],
				'condition' => [
					'footer_additional_info!' => '',
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'additional_info_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table:hover .bdt-price-table-additional_info' => 'color: {{VALUE}}',
				],
				'condition' => [
					'footer_additional_info!' => '',
					'_skin' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'additional_info_typography',
				'selector'  => '{{WRAPPER}} .bdt-price-table-additional_info',
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_3,
				'condition' => [
					'footer_additional_info!' => '',
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'additional_info_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default'    => [
					'top'    => 15,
					'right'  => 30,
					'bottom' => 0,
					'left'   => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-additional_info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'footer_additional_info!' => '',
					'_skin' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_ribbon',
			[
				'label'     => __('Ribbon', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ribbon_bg_color',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#14ABF4',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-ribbon-inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ribbon_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-table-ribbon-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ribbon_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-ribbon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ribbon_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-table-ribbon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'shadow',
				'selector' => '{{WRAPPER}} .bdt-price-table-ribbon-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ribbon_typography',
				'selector' => '{{WRAPPER}} .bdt-price-table-ribbon-inner',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price_header',
			[
				'label' => __('Header & Pricing Wrap', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => '9',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'header_price_bg_color',
				'selector' => '{{WRAPPER}} .bdt-ep-price-header-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'header_price_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-price-header-wrap',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'header_price_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-price-header-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_price_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-price-header-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_price_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-price-header-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'header_priceshadow',
				'selector' => '{{WRAPPER}} .bdt-ep-price-header-wrap',
			]
		);

		$this->end_controls_section();
	}

	private function get_currency_symbol($symbol_name) {
		$symbols = [
			'dollar'       => '&#36;',
			'baht'         => '&#3647;',
			'euro'         => '&#128;',
			'franc'        => '&#8355;',
			'guilder'      => '&fnof;',
			'indian_rupee' => '&#8377;',
			'krona'        => 'kr',
			'lira'         => '&#8356;',
			'peseta'       => '&#8359',
			'peso'         => '&#8369;',
			'pound'        => '&#163;',
			'real'         => 'R$',
			'ruble'        => '&#8381;',
			'rupee'        => '&#8360;',
			'bdt'          => '&#2547;',
			'shekel'       => '&#8362;',
			'won'          => '&#8361;',
			'yen'          => '&#165;',
		];
		return isset($symbols[$symbol_name]) ? $symbols[$symbol_name] : '';
	}

	public function render_image() {

		$settings = $this->get_settings_for_display();

		if (empty($settings['image']['url'])) {
			return;
		}

		$this->add_render_attribute('wrapper', 'class', 'bdt-price-table-image');

		?>
		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

			<?php 
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'thumbnail', $settings);
			if (!$thumb_url) {
				printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($settings['heading']));
			} else {
				print(wp_get_attachment_image(
					$settings['image']['id'],
					$settings['thumbnail_size'],
					false,
					[
						'alt' => esc_html($settings['heading'])
					]
				));
			}
			?>

		</div>
		<?php
	}

	public function render_header() {
		$settings = $this->get_settings_for_display();
		$id = 'bdt-price-table-' . $this->get_id();

		$this->add_render_attribute('header', [
			'class' => 'bdt-price-table-header'
		]);

		if ('yes' == $settings['sticky_heading']) {
			$this->add_render_attribute('header', [
				'data-bdt-sticky' => 'end: #' . $id
			]);
		}

		if ($settings['heading'] || $settings['sub_heading']) : ?>
			<div <?php $this->print_render_attribute_string('header'); ?>>
				<?php if (!empty($settings['heading'])) : ?>
					<<?php echo Utils::get_valid_html_tag($settings['heading_tag']); ?> class="bdt-price-table-heading">
						<?php echo esc_html($settings['heading']); ?>
					</<?php echo Utils::get_valid_html_tag($settings['heading_tag']); ?>>
				<?php endif; ?>

				<?php if (!empty($settings['sub_heading']) and 'bdt-partait' != $settings['_skin']) : ?>
					<span class="bdt-price-table-subheading">
						<?php echo esc_html($settings['sub_heading']); ?>
					</span>
				<?php endif; ?>
			</div>
		<?php endif;
	}

	public function render_price() {
		$settings = $this->get_settings_for_display();

		$symbol   = '';
		$image    = '';

		if (!empty($settings['currency_symbol'])) {
			if ('custom' !== $settings['currency_symbol']) {
				$symbol = $this->get_currency_symbol($settings['currency_symbol']);
			} else {
				$symbol = $settings['currency_symbol_custom'];
			}
		}


		$currency_format = empty($settings['currency_format']) ? '.' : $settings['currency_format'];
		$price = explode($currency_format, $settings['price']);
		$intpart = $price[0];
		$fraction = '';
		if (2 === count($price)) {
			$fraction = $price[1];
		}


		// $price    = explode( '.', $settings['price'] );
		// $intpart  = $price[0];
		// $fraction = '';

		// if ( 2 === sizeof( $price ) ) {
		// 	$fraction = $price[1];
		// }

		$period_position = $settings['period_position'];
		$period_class    = ($period_position == 'below') ? ' bdt-price-table-period-position-below' : ' bdt-price-table-period-position-beside';
		$period_element  = '<span class="bdt-price-table-period elementor-typo-excluded' . $period_class . '">' . $settings['period'] . '</span>';

		$currency_position = $settings['currency_horizontal_position'];

		if (isset($settings['sale_add_custom_attributes']) and ($settings['sale_add_custom_attributes'] == 'yes') and !empty($settings['sale_custom_attributes'])) {
			$attributes = explode("\n", $settings['sale_custom_attributes']);

			$reserved_attr = ['href', 'target'];

			foreach ($attributes as $attribute) {
				if (!empty($attribute)) {
					$attr = explode('|', $attribute, 2);
					if (!isset($attr[1])) {
						$attr[1] = '';
					}

					if (!in_array(strtolower($attr[0]), $reserved_attr)) {
						$this->add_render_attribute('sale-price-attr', trim($attr[0]), trim($attr[1]));
					}
				}
			}
		}
		if (isset($settings['price_add_custom_attributes']) and ($settings['price_add_custom_attributes'] == 'yes') and !empty($settings['price_custom_attributes'])) {
			$attributes = explode("\n", $settings['price_custom_attributes']);

			$reserved_attr = ['href', 'target'];

			foreach ($attributes as $attribute) {
				if (!empty($attribute)) {
					$attr = explode('|', $attribute, 2);
					if (!isset($attr[1])) {
						$attr[1] = '';
					}

					if (!in_array(strtolower($attr[0]), $reserved_attr)) {
						$this->add_render_attribute('price-attr', trim($attr[0]), trim($attr[1]));
					}
				}
			}
		}

		$id = 'bdt-price-table-' . $this->get_id();

		$this->add_render_attribute('pricing', [
			'class' => 'bdt-price-table-price'
		]);

		if ('yes' == $settings['sticky_pricing']) {
			$this->add_render_attribute('pricing', [
				'data-bdt-sticky' => 'end: #' . $id
			]);
		}

		?>

		<div <?php $this->print_render_attribute_string('pricing'); ?>>
			<?php if ($settings['sale'] && !empty($settings['original_price'])) : ?>
				<span class="bdt-price-table-original-price elementor-typo-excluded bdt-display-block" <?php echo $this->get_render_attribute_string('sale-price-attr'); ?>>
					<?php echo esc_html($symbol . $settings['original_price']); ?>
				</span>
			<?php endif; ?>

			<?php if (!empty($symbol) && is_numeric($intpart) && 'left' === $currency_position) : ?>
				<span class="bdt-price-table-currency">
					<?php echo esc_attr($symbol); ?>
				</span>
			<?php endif; ?>

			<?php if (!empty($intpart) || 0 <= $intpart) : ?>
				<span class="bdt-price-table-integer-part" <?php echo $this->get_render_attribute_string('price-attr'); ?>>
					<?php echo esc_attr($intpart); ?>
				</span>
			<?php endif; ?>

			<?php if (0 < $fraction || (!empty($settings['period']) && 'beside' === $period_position)) : ?>
				<div class="bdt-price-table-after-price">
					<span class="bdt-price-table-fractional-part">
						<?php echo esc_attr($fraction); ?>
					</span>
					<?php if (!empty($settings['period']) && 'beside' === $period_position) : ?>
						<?php echo wp_kses_post($period_element); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($symbol) && is_numeric($intpart) && 'right' === $currency_position) : ?>
				<span class="bdt-price-table-currency">
					<?php echo esc_attr($symbol); ?>
				</span>
			<?php endif; ?>

			<?php if (!empty($settings['period']) && 'below' === $period_position) : ?>
				<?php echo wp_kses_post($period_element); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_features_list() {

		$settings = $this->get_settings_for_display();
		$features_hide_on_setup = '';

		if (!empty($settings['features_hide_on'])) {
			foreach ($settings['features_hide_on'] as $element) {

				if ($element == 'desktop') {
					$features_hide_on_setup .= ' bdt-desktop';
				}
				if ($element == 'tablet') {
					$features_hide_on_setup .= ' bdt-tablet';
				}
				if ($element == 'mobile') {
					$features_hide_on_setup .= ' bdt-mobile';
				}
			}
		}

		if (!empty($settings['features_list'])) : ?>
			<ul class="bdt-price-table-features-list <?php echo $features_hide_on_setup; ?>">
				<?php foreach ($settings['features_list'] as $item) :

					$this->add_render_attribute('features', 'class', 'bdt-price-table-feature-text bdt-display-inline-block', true);

					if ($item['tooltip_text']) {
						// Tooltip settings
						$this->add_render_attribute('features', 'class', 'bdt-tippy-tooltip');
						$this->add_render_attribute('features', 'data-tippy', '', true);
						$this->add_render_attribute('features', 'data-tippy-arrow', 'true', true);
						$this->add_render_attribute('features', 'data-tippy-placement', $item['tooltip_placement'], true);
						$this->add_render_attribute('features', 'data-tippy-content', $item['tooltip_text'], true);
					}

					if (!isset($item['item_icon']) && !Icons_Manager::is_migration_allowed()) {
						// add old default
						$item['item_icon'] = 'fas fa-arrow-right';
					}

					$migrated  = isset($item['__fa4_migrated']['price_table_item_icon']);
					$is_new    = empty($item['item_icon']) && Icons_Manager::is_migration_allowed();

				?>
					<li class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">
						<div class="bdt-price-table-feature-inner">
							<?php if (!empty($item['price_table_item_icon']['value'])) : ?>

								<?php if ($is_new || $migrated) :
									Icons_Manager::render_icon($item['price_table_item_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
								else : ?>
									<i class="<?php echo esc_attr($item['item_icon']); ?>" aria-hidden="true"></i>
								<?php endif; ?>

							<?php endif; ?>
							<?php if (!empty($item['item_text'])) : ?>
								<div <?php echo $this->get_render_attribute_string('features'); ?>>
									<?php echo wp_kses($item['item_text'], element_pack_allow_tags('text')); ?>
								</div>
							<?php else :
								echo '&nbsp;';
							endif;
							?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif;
	}

	public function render_features_list_column() {

		$settings = $this->get_settings_for_display();
		$features_hide_on_setup = '';

		if (!empty($settings['features_hide_on'])) {
			foreach ($settings['features_hide_on'] as $element) {

				if ($element == 'desktop') {
					$features_hide_on_setup .= ' bdt-desktop';
				}
				if ($element == 'tablet') {
					$features_hide_on_setup .= ' bdt-tablet';
				}
				if ($element == 'mobile') {
					$features_hide_on_setup .= ' bdt-mobile';
				}
			}
		}

		if (!empty($settings['features_list'])) : ?>
			<div>
				<ul class="bdt-price-table-features-list bdt-grid-collapse bdt-child-width-expand@m bdt-flex bdt-flex-middle  <?php echo $features_hide_on_setup; ?>" data-bdt-grid>
					<?php foreach ($settings['features_list'] as $item) :

						$this->add_render_attribute('features', 'class', 'bdt-price-table-feature-text bdt-display-inline-block', true);

						if ($item['tooltip_text']) {
							// Tooltip settings
							$this->add_render_attribute('features', 'class', 'bdt-tippy-tooltip');
							$this->add_render_attribute('features', 'data-tippy', '', true);
							$this->add_render_attribute('features', 'data-tippy-arrow', 'true', true);
							$this->add_render_attribute('features', 'data-tippy-placement', $item['tooltip_placement'], true);
							$this->add_render_attribute('features', 'data-tippy-content', $item['tooltip_text'], true);
						}

						if (!isset($item['item_icon']) && !Icons_Manager::is_migration_allowed()) {
							// add old default
							$item['item_icon'] = 'fas fa-arrow-right';
						}

						$migrated  = isset($item['__fa4_migrated']['price_table_item_icon']);
						$is_new    = empty($item['item_icon']) && Icons_Manager::is_migration_allowed();

					?>
						<li class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">
							<div class="bdt-price-table-feature-inner">
								<?php if (!empty($item['price_table_item_icon']['value'])) : ?>

									<?php if ($is_new || $migrated) :
										Icons_Manager::render_icon($item['price_table_item_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
									else : ?>
										<i class="<?php echo esc_attr($item['item_icon']); ?>" aria-hidden="true"></i>
									<?php endif; ?>

								<?php endif; ?>
								<?php if (!empty($item['item_text'])) : ?>
									<div <?php echo $this->get_render_attribute_string('features'); ?>>
										<?php echo wp_kses($item['item_text'], element_pack_allow_tags('text')); ?>
									</div>
								<?php else :
									echo '&nbsp;';
								endif;
								?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif;
	}

	public function render_footer() {
		$settings = $this->get_settings_for_display();

		if (!empty($settings['button_text']) || !empty($settings['footer_additional_info'])) : ?>
			<div class="bdt-price-table-footer">


				<?php $this->render_button(); ?>

				<?php if (!empty($settings['footer_additional_info']) and '' == $settings['_skin']) : ?>
					<div class="bdt-price-table-additional_info">
						<?php echo wp_kses_post($settings['footer_additional_info']); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php endif;
	}


	public function render_button() {
		$settings         = $this->get_settings_for_display();
		$button_size      = ($settings['button_size']) ? 'elementor-size-' . $settings['button_size'] : '';
		$button_animation = (!empty($settings['button_hover_animation'])) ? ' elementor-animation-' . $settings['button_hover_animation'] : '';

		$this->add_render_attribute(
			'button',
			'class',
			[
				'bdt-price-table-button',
				'elementor-button',
				$button_size,
			]
		);

		if (!empty($settings['button_css_id'])) {
			$this->add_render_attribute('button', 'id', $settings['button_css_id']);
		}

		if (!empty($settings['link']['url'])) {
			$this->add_render_attribute('button', 'href', $settings['link']['url']);

			if (!empty($settings['link']['is_external'])) {
				$this->add_render_attribute('button', 'target', '_blank');
			}
		}

		if (!empty($settings['button_hover_animation'])) {
			$this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
		}

		if ($settings['add_custom_attributes'] and !empty($settings['custom_attributes'])) {
			$attributes = explode("\n", $settings['custom_attributes']);

			$reserved_attr = ['href', 'target'];

			foreach ($attributes as $attribute) {
				if (!empty($attribute)) {
					$attr = explode('|', $attribute, 2);
					if (!isset($attr[1])) {
						$attr[1] = '';
					}

					if (!in_array(strtolower($attr[0]), $reserved_attr)) {
						$this->add_render_attribute('button', trim($attr[0]), trim($attr[1]));
					}
				}
			}
		}

		if ($settings['edd_as_button'] == 'yes') {
			echo edd_get_purchase_link([
				'download_id' => $settings['edd_id'],
				'price' => false,
				'text' => esc_html($settings['button_text']),
				'class' => 'bdt-price-table-button elementor-button ' . $button_size . $button_animation,
			]);
		} else {
			if (!empty($settings['button_text'])) : ?>
				<div class="bdt-price-table-btn-wrap">
					<a <?php echo $this->get_render_attribute_string('button'); ?>>
						<?php echo esc_html($settings['button_text']); ?>
					</a>
				</div>
			<?php endif;
		}
	}

	public function render_ribbon() {
		$settings = $this->get_settings_for_display();

		if ($settings['show_ribbon'] && !empty($settings['ribbon_title'])) :
			$this->add_render_attribute('ribbon-wrapper', 'class', 'bdt-price-table-ribbon');

			if (!empty($settings['ribbon_align'])) :
				$this->add_render_attribute('ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ribbon_align']);
			endif; ?>

			<div <?php echo $this->get_render_attribute_string('ribbon-wrapper'); ?>>
				<div class="bdt-price-table-ribbon-inner">
					<?php echo esc_html($settings['ribbon_title']); ?>
				</div>
			</div>
		<?php endif;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id = 'bdt-price-table-' . $this->get_id();
		?>
		<div class="bdt-price-table skin-default" id="<?php echo esc_attr($id); ?>">
			<?php
			if ('1' == $settings['layout']) :
				$this->render_image();
				$this->render_header();
				$this->render_price();
				$this->render_features_list();
				$this->render_footer();
			endif;

			if ('2' == $settings['layout']) :
				$this->render_image();
				$this->render_header();
				$this->render_features_list();
				$this->render_price();
				$this->render_footer();
			endif;

			if ('3' == $settings['layout']) :
				$this->render_image();
				$this->render_header();
				$this->render_price();
				$this->render_footer();
				$this->render_features_list();
			endif;

			if ('4' == $settings['layout']) :
				$this->render_image();
				$this->render_features_list();
				$this->render_header();
				$this->render_price();
				$this->render_footer();
			endif;

			if ('5' == $settings['layout']) :
				$this->render_image();
				$this->render_header();
				$this->render_price();
				$this->render_footer();
			endif;

			if ('6' == $settings['layout']) :
				$this->render_header();
				$this->render_image();
				$this->render_price();
				$this->render_features_list();
				$this->render_footer();
			endif;

			if ('7' == $settings['layout']) :
				$this->render_header();
				$this->render_price();
				$this->render_features_list();
				$this->render_image();
				$this->render_footer();
			endif;

			if ('8' == $settings['layout']) :
				$this->render_image();
				$this->render_price();
				$this->render_header();
				$this->render_features_list();
				$this->render_footer();
			endif;

			if ('9' == $settings['layout']) :
				$this->render_image();
				?>
				<div class="bdt-ep-price-header-wrap bdt-flex bdt-flex-middle bdt-flex-between">
				<?php
				$this->render_header();
				$this->render_price();
				?>
				</div>
				<?php
				$this->render_features_list();
				$this->render_footer();
			endif;
			
			$this->render_ribbon();
			?>
		</div>
		<?php 
	}
}
