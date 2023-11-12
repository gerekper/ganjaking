<?php

class PAFE_Widget_Creator extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-widget-creator';
	}

	public function get_title() {
		return __( 'Widget Creator', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-progress-tracker';
	}

	public function get_categories() {
		return [ 'pafe-widget-creator' ];
	}

	public function get_keywords() {
		return [ 'widget', 'creator' ];
	}

	public function get_style_depends() {
		return [ 
			'pafe-widget-creator-style',
			'pafe-font-awesome-style',
		];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'pafe_widget_creator_settings_section',
			[
				'label' => __( 'Settings', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_title',
			[
				'label' => __( 'Title* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'pafe_widget_creator_name',
			[
				'label' => __( 'Name* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'The name have to be unique, with latin character and no space, no number. E.g your-widget', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_icon',
			[
				'label' => __( 'Icon', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::ICON,
			]
		);

		$this->add_control(
			'pafe_widget_creator_categories',
			[
				'label' => __( 'Categories', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Categories are separated by commas', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_keywords',
			[
				'label' => __( 'Keywords', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Keywords are separated by commas', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_widget_creator_controls_section',
			[
				'label' => __( 'Controls', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'type',
			[
				'label' => __( 'Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'section' => __( 'Section', 'pafe' ),
					'text' => __( 'Text', 'pafe' ),
					'textarea' => __( 'Textarea', 'pafe' ),
					'wysiwyg' => __( 'WYSIWYG', 'pafe'),
					'number' => __( 'Number', 'pafe' ),
					'url' => __( 'URL', 'pafe' ),
					'select' => __( 'Select', 'pafe' ),
					'select2' => __( 'Select2', 'pafe' ),
					'choose' => __( 'Choose', 'pafe' ),
					'slider' => __( 'Slider', 'pafe' ),
					'color' => __( 'Color', 'pafe' ),
					'switcher' => __( 'Switcher', 'pafe' ),
					'media' => __( 'Media', 'pafe' ),
					'icon' => __( 'Icon', 'pafe' ),
					'icons' => __( 'Icons', 'pafe' ),
					'slider' => __( 'Slider', 'pafe' ),
					'hidden' => __( 'Hidden', 'pafe' ),
					'date_time' => __( 'Date Time', 'pafe' ),
					'code' => __( 'Code', 'pafe' ),
					'background' => __( 'Background', 'pafe' ),
					'border' => __( 'Border', 'pafe' ),
					'box-shadow' => __( 'Box Shadow', 'pafe' ),
					'css-filter' => __( 'CSS Filter', 'pafe' ),
					'image-size' => __( 'Image Size', 'pafe' ),
					'text-shadow' => __( 'Text Shadow', 'pafe' ),
					'typography' => __( 'Typography', 'pafe' ),
					'dimensions' => __( 'Dimensions', 'pafe' ),
					'font' => __( 'Font', 'pafe' ),
					'gallery' => __( 'Gallery', 'pafe' ),
					'raw_html' => __( 'Raw HTML', 'pafe' ),
					'image_dimensions' => __( 'Image Dimensions', 'pafe' ),
					'repeater_start' => __( 'Repeater Start', 'pafe' ),
					'repeater_end' => __( 'Repeater End', 'pafe' ),
					'tabs_start' => __( 'Tabs Start', 'pafe' ),
					'tabs_end' => __( 'Tabs End', 'pafe' ),
					'tab_start' => __( 'Tab Start', 'pafe' ),
					'tab_end' => __( 'Tab End', 'pafe' ),
				]
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'repeater_start',
								'tabs_end',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'label',
			[
				'label' => __( 'Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Label',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'repeater_start',
								'tabs_start',
								'tabs_end',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'label_block',
			[
				'label' => __( 'Label Block', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'section',
								'repeater_start',
								'tabs_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __( 'Description', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'section',
								'repeater_start',
								'tabs_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'min',
			[
				'label' => __( 'Min', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'max',
			[
				'label' => __( 'Max', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'step',
			[
				'label' => __( 'Step', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'number',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'options',
			[
				'label' => __( 'Options', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => __( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: Option 1|200', 'pafe' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'select',
								'select2',
								'choose',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'multiple',
			[
				'label' => __( 'Multiple', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'select2',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'size_units',
			[
				'label' => __( 'Size Units', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Separate CSS units with a comma like: px,em,rem,%,deg,vh', 'pafe' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'dimensions',
								'slider',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label' => __( 'Placeholder', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'text',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'label_on',
			[
				'label' => __( 'Label On', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'switcher',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'label_off',
			[
				'label' => __( 'Label Off', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'No',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'switcher',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'return_value',
			[
				'label' => __( 'Return Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'yes',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'switcher',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'media_types',
			[
				'label' => __( 'Media Types', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => [
					'image' => __( 'Image', 'pafe' ),
					'video' => __( 'Video', 'pafe' ),
					'svg' => __( 'SVG', 'pafe' ),
				],
				'default' => 'image',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'media',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'range',
			[
				'label' => __( 'Range', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => '<a href="https://github.com/elementor/elementor-developers-docs/blob/master/src/controls/classes/control-slider.md" target="_blank">Document</a>',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'slider',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'title_field',
			[
				'label' => __( 'Repeater Item Title Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => '{{ control_name }}',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'repeater_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'default',
			[
				'label' => __( 'Default', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'section',
								'raw_html',
								'repeater_start',
								'tabs_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'raw',
			[
				'label' => __( 'Raw (HTML)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'raw_html',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'language',
			[
				'label' => __( 'Language', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'html',
				'description' => __( 'Any <a href="https://ace.c9.io/build/kitchen-sink.html" target="_blank">language supported by Ace editor</a>', 'pafe' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'code',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'rows',
			[
				'label' => __( 'Rows', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => 'in',
							'value' => [
								'textarea',
								'code',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'separator',
			[
				'label' => __( 'Separator', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'none', 'pafe' ),
					'default' => __( 'default', 'pafe' ),
					'before' => __( 'before', 'pafe' ),
					'after' => __( 'after', 'pafe' ),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'section',
								'repeater_start',
								'tabs_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'responsive',
			[
				'label' => __( 'Responsive', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'section',
								'repeater_start',
								'tabs_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'devices',
			[
				'label' => __( 'Devices', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => [
					'desktop' => __( 'Desktop', 'pafe' ),
					'tablet' => __( 'Tablet', 'pafe' ),
					'mobile' => __( 'Mobile', 'pafe' ),
				],
				'default' => [ 'desktop', 'tablet', 'mobile' ],
				'multiple' => true,
				'condition' => [
					'type!' => [
						'section',
						'repeater_start',
						'tabs_start',
						'tabs_end',
						'tab_start',
						'tab_end',
					],
					'responsive' => 'yes'
				],
			]
		);

		// TODO: desktop_default, tablet_default, mobile_default

		$repeater->add_control(
			'tab',
			[
				'label' => __( 'Tab', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'content' => __( 'Content', 'pafe' ),
					'style' => __( 'Style', 'pafe' ),
					'advanced' => __( 'Advanced', 'pafe' ),
					'layout' => __( 'Layout', 'pafe' ),
					'responsive' => __( 'Responsive', 'pafe' ),
				],
				'condition' => [
					'type' => 'section',
				],
			]
		);

		$repeater->add_control(
			'conditions_simple_enable',
			[
				'label' => __( 'Simple Conditions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'repeater_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'conditions_simple_relation',
			[
				'label' => __( 'Conditions Relation', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'and' => 'and',
					'or' => 'or',
				],
				'default' => 'and',
				'condition' => [
					'conditions_simple_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'conditions_simple',
			[
				'label' => __( 'Conditions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => __( 'control-name|operator|control-value<br>One condition per line. E.g:<br>control-name|==|a<br>control-name|==|b', 'pafe' ),
				'condition' => [
					'conditions_simple_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'conditions_advanced_enable',
			[
				'label' => __( 'Advanced Conditions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'type',
							'operator' => '!in',
							'value' => [
								'repeater_start',
								'tabs_end',
								'tab_start',
								'tab_end',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'conditions_advanced',
			[
				'label' => __( 'Conditions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => '<a href="https://developers.elementor.com/docs/controls/conditional-display/#advanced-conditions" target="_blank">Document</a>',
				'condition' => [
					'conditions_advanced_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'selectors_enable',
			[
				'label' => __( 'Selectors (Dynamic CSS)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'type!' => [
						'section',
						'repeater_start',
						'tabs_start',
						'tabs_end',
						'tab_start',
						'tab_end',
					],
				],
			]
		);

		$repeater->add_control(
			'selectors',
			[
				'label' => __( 'Selectors', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'description' => 'One selector per line<br>- Color Control:<br>{{WRAPPER}} .selector | color: {{VALUE}}<br>- Slider Control:<br>{{WRAPPER}} .selector | width:{{SIZE}}{{UNIT}}<br>- Dimensions Control:<br>{{WRAPPER}} .selector | border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};<br>- Repeater Item:<br>{{WRAPPER}} {{CURRENT_ITEM}} | color: {{VALUE}}<br>- Background, Border, Box Shadow, CSS Filter, Image Size, Text Shadow, Typography:<br>{{WRAPPER}} .selector',
				'condition' => [
					'type!' => [
						'section',
						'repeater_start',
						'tabs_start',
						'tabs_end',
						'tab_start',
						'tab_end',
					],
					'selectors_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'classes',
			[
				'label' => __( 'Classes', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'type!' => [
						'section',
						'repeater_start',
						'tabs_start',
						'tabs_end',
						'tab_start',
						'tab_end',
					],
				]
			]
		);

		$this->add_control(
			'pafe_widget_creator_controls',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'label'   => __( 'Note: Controls have to start by Section > Controls', 'pafe' ),
				'description' => __( 'Note: Controls start by Section > Other Controls', 'pafe' ),
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ label }}} - {{{ type }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_widget_creator_render_section',
			[
				'label' => __( 'Render', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_render',
			[
				'label' => __( 'Render', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'language' => 'php',
				'rows' => 10,
				'description' => __( 'You can use PHP or Twig. <a href="https://twig.symfony.com/doc/3.x/templates.html" target="_blank">Twig Document</a><br>Control Value:<br>- PHP: $control_name<br>- Twig: {{control_name}}', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_widget_creator_assets_section',
			[
				'label' => __( 'Assets (JS, CSS Includes)', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_assets',
			[
				'label' => __( 'Upload Assets to ..wp-content/uploads/piotnet-addons-for-elementor/widget-creator', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Files_Control::Select_Files,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_widget_creator_javascript_section',
			[
				'label' => __( 'Javascript', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_javascript',
			[
				'label' => __( 'Javascript', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'language' => 'javascript',
				'rows' => 10,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_widget_creator_css_section',
			[
				'label' => __( 'CSS', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_widget_creator_css',
			[
				'label' => __( 'CSS', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'language' => 'css',
				'rows' => 10,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		?>	
			<div class="pwc-preview">
				<div class="pwc-preview__widgets">
					<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/widget-creator-widgets.png'; ?>" alt="">
					<div class="pwc-preview__widget">
						<?php if (!empty($settings['pafe_widget_creator_icon'])) : ?>
							<div class="pwc-preview__widget-icon">
								<i class="<?php echo $settings['pafe_widget_creator_icon']; ?>"></i>
							</div>
						<?php endif; ?>
						<?php if (!empty($settings['pafe_widget_creator_title'])) : ?>
							<div class="pwc-preview__widget-title"><?php echo $settings['pafe_widget_creator_title']; ?></div>
						<?php endif; ?>
					</div>
				</div>
				<?php if (!empty($settings['pafe_widget_creator_controls'])) : ?>
				<?php
					$tabs = [];

					foreach ($settings['pafe_widget_creator_controls'] as $key => $control) {
						if ($control['type'] == 'section') {
							if (!empty($control['tab'])) {
								$tabs[] = $control['tab'];
							}
						}
					}

					$tabs = array_unique($tabs);

					foreach ($tabs as $tab) :
				?>
					<div class="pwc-preview__tab pwc-preview__tab--<?php echo $tab; ?>">
						<div class="pwc-preview__tab-header">
							Edit <?php if (!empty($settings['pafe_widget_creator_title'])) { echo $settings['pafe_widget_creator_title']; } ?>
						</div>
						<div class="pwc-preview__tab-tabs">
							<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/widget-creator-tab-' . $tab . '.png'; ?>" alt="">
						</div>
						<div class="pwc-preview__tab-controls">
						<?php
							$tab_current = false;
							foreach ($settings['pafe_widget_creator_controls'] as $key => $control) :
								$render = false;

								if ($control['type'] == 'section') {
									if ($control['tab'] == $tab) {
										$tab_current = true;
										$render = true;
									} else {
										$tab_current = false;
									}
								} else {
									// is control
									if ($tab_current) {
										$render = true;
									}
								}

								if ($render) :
									if ($control['type'] == 'section') :
						?>
									<div class="pwc-preview__tab-section">
										<?php if (!empty($control['label'])) { echo $control['label']; } ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						<?php endforeach; ?>
						</div>
						<div class="pwc-preview__tab-tabs">
							<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/widget-creator-footer.png'; ?>" alt="">
						</div>
					</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
			
        <?php

	}

	// public function add_wpml_support() {
	// 	add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	// }

	// public function wpml_widgets_to_translate_filter( $widgets ) {
	// 	$widgets[ $this->get_name() ] = [
	// 		'conditions' => [ 'widgetType' => $this->get_name() ],
	// 		'fields'     => [
	// 			[
	// 				'field'       => 'pafe_form_builder_lost_password_text',
	// 				'type'        => __( 'Lost Password Text', 'pafe' ),
	// 				'editor_type' => 'LINE'
	// 			],
	// 		],
	// 	];

	// 	return $widgets;
	// }
}
