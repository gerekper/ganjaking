<?php
namespace ElementPack\Modules\SocialShare\Widgets;

use Elementor\Controls_Manager;
use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use ElementPack\Modules\SocialShare\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Social_Share extends Module_Base {

	protected $_has_template_content = false;

	private static $medias_class = [
		'email'      => 'ep-icon-envelope',
		'vkontakte'  => 'ep-icon-vk',
	];

	private static function get_social_media_class( $media_name ) {
		if ( isset( self::$medias_class[ $media_name ] ) ) {
			return self::$medias_class[ $media_name ];
		}
		return 'ep-icon-' . $media_name;
	}


	public function get_name() {
		return 'bdt-social-share';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Social Share', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-social-share';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'social', 'link', 'share' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-social-share' ];
        }
    }
	
	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['goodshare', 'ep-scripts'];
        } else {
			return [ 'goodshare' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/3OPYfeVfcb8';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_buttons_content',
			[
				'label' => esc_html__( 'Share Buttons', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$medias = Module::get_social_media();

		$medias_names = array_keys( $medias );

		$repeater->add_control(
			'button',
			[
				'label' => esc_html__( 'Social Media', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'options' => array_reduce( $medias_names, function( $options, $media_name ) use ( $medias ) {
					$options[ $media_name ] = $medias[ $media_name ]['title'];

					return $options;
				}, [] ),
				'default' => 'facebook',
			]
		);

		$repeater->add_control(
			'text',
			[
				'label' => esc_html__( 'Custom Label', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'copied_text',
			[
				'label'              => esc_html__( 'Copied Text', 'bdthemes-element-pack' ),
				'default'            => 'Copied',
				'type'               => Controls_Manager::TEXT,
				'condition'          => [
					'button' => 'link'
				]
			]
		);

		$this->add_control(
			'share_buttons',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[ 'button' => 'facebook' ],
					[ 'button' => 'linkedin' ],
					[ 'button' => 'twitter' ],
					[ 'button' => 'pinterest' ],
				],
				'title_field' => '{{{ button }}}',
			]
		);

		$this->add_control(
			'view',
			[
				'label'       => esc_html__( 'View', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'options'     => [
					'icon-text' => 'Icon & Text',
					'icon'      => 'Icon',
					'text'      => 'Text',
				],
				'default'      => 'icon-text',
				'separator'    => 'before',
				'prefix_class' => 'bdt-ss-btns-view-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'show_label',
			[
				'label'     => esc_html__( 'Label', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'view' => 'icon-text',
				],
			]
		);

		$this->add_control(
			'show_counter',
			[
				'label'     => esc_html__( 'Count', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'view!' => 'icon',
				],
			]
		);


		$this->add_control(
			'show_counter_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Note: Social share count only works with those platform: vkontakte, facebook, odnoklassniki, moimir, linkedin, tumblr, pinterest, buffer.', 'bdthemes-element-pack' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
				'condition' => [
					'show_counter' => 'yes',
					'view!' => 'icon',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'columns',
		// 	[
		// 		'label'   => esc_html__( 'Columns', 'bdthemes-element-pack' ),
		// 		'type'    => Controls_Manager::SELECT,
		// 		'default' => '0',
		// 		'options' => [
		// 			'0' => 'Auto',
		// 			'1' => '1',
		// 			'2' => '2',
		// 			'3' => '3',
		// 			'4' => '4',
		// 			'5' => '5',
		// 			'6' => '6',
		// 		],
		// 		'prefix_class' => 'bdt-ep-grid%s-',
		// 	]
		// );

		$this->add_responsive_control(
			'columns',
			[
				'label'           => __( 'Columns', 'bdthemes-element-pack' ) . BDTEP_UC,
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => '0',
				'tablet_default'  => '0',
				'mobile_default'  => '0',
				'options'         => [
					'0' => 'Auto',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				// 'selectors' => [
				// 	'{{WRAPPER}} .bdt-social-share' => 'grid-template-columns: repeat({{SIZE}}, 1fr); display: grid;',
				// ],
				'selectors_dictionary' => [
                    '0' => 'display: flex; flex-wrap: wrap;',
					'1' => 'grid-template-columns: repeat(1, 1fr); display: grid;',
					'2' => 'grid-template-columns: repeat(2, 1fr); display: grid;',
					'3' => 'grid-template-columns: repeat(3, 1fr); display: grid;',
					'4' => 'grid-template-columns: repeat(4, 1fr); display: grid;',
					'5' => 'grid-template-columns: repeat(5, 1fr); display: grid;',
					'6' => 'grid-template-columns: repeat(6, 1fr); display: grid;',
                ],
				'selectors' => [
                    '{{WRAPPER}} .bdt-social-share' => '{{VALUE}};',
                ],
                'render_type'     => 'template',
                'style_transfer'  => true,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-social-share' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-social-share' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
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
						'title' => esc_html__( 'Justify', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'bdt-ss-btns-align-',
				'condition'    => [
					'columns' => '0',
				],
			]
		);

		$this->add_control(
			'share_url_type',
			[
				'label'   => esc_html__( 'Target URL', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'current_page' => esc_html__( 'Current Page', 'bdthemes-element-pack' ),
					'custom'       => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				],
				'default'   => 'current_page',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'share_url',
			[
				'label'         => esc_html__( 'URL', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'placeholder'   => 'http://your-link.com',
				'condition'     => [
					'share_url_type' => 'custom',
				],
				'show_label'         => false,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_style',
			[
				'label' => esc_html__( 'Share Buttons', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'flat'     => esc_html__( 'Flat', 'bdthemes-element-pack' ),
					'framed'   => esc_html__( 'Framed', 'bdthemes-element-pack' ),
					'gradient' => esc_html__( 'Gradient', 'bdthemes-element-pack' ),
					'minimal'  => esc_html__( 'Minimal', 'bdthemes-element-pack' ),
					'boxed'    => esc_html__( 'Boxed Icon', 'bdthemes-element-pack' ),
				],
				'default'      => 'flat',
				'prefix_class' => 'bdt-ss-btns-style-',
			]
		);

		$this->add_control(
			'shape',
			[
				'label'   => esc_html__( 'Shape', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'square'  => esc_html__( 'Square', 'bdthemes-element-pack' ),
					'rounded' => esc_html__( 'Rounded', 'bdthemes-element-pack' ),
					'circle'  => esc_html__( 'Circle', 'bdthemes-element-pack' ),
				],
				'default'      => 'square',
				'prefix_class' => 'bdt-ss-btns-shape-',
			]
		);

		$this->add_control(
			'divider_one',
			[
				'type'    => Controls_Manager::DIVIDER,
			]
		);

		// $this->add_responsive_control(
		// 	'column_gap',
		// 	[
		// 		'label'   => esc_html__( 'Columns Gap', 'bdthemes-element-pack' ),
		// 		'type'    => Controls_Manager::SLIDER,
		// 		'default' => [
		// 			'size' => 10,
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-ss-btn' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
		// 			'{{WRAPPER}} .bdt-ep-grid'             => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'row_gap',
		// 	[
		// 		'label'   => esc_html__( 'Rows Gap', 'bdthemes-element-pack' ),
		// 		'type'    => Controls_Manager::SLIDER,
		// 		'default' => [
		// 			'size' => 10,
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-ss-btn' => 'margin-bottom: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		$this->add_responsive_control(
			'button_size',
			[
				'label' => esc_html__( 'Button Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0.5,
						'max'  => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ss-btn' => 'font-size: calc({{SIZE}}{{UNIT}} * 10);',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min'  => 0.5,
						'max'  => 4,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ss-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'view!' => 'text',
				],
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label' => esc_html__( 'Button Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min'  => 1,
						'max'  => 7,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ss-btn' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_size',
			[
				'label'      => esc_html__( 'Border Size', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
					'em' => [
						'max'  => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ss-btn' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => [ 'framed', 'boxed' ],
				],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label'      => esc_html__( 'Text Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} a.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'view' => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-social-share-title, {{WRAPPER}} .bdt-ss-counter',
				'exclude'  => [ 'line_height' ],
			]
		);

		$this->add_control(
			'color_source',
			[
				'label'       => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'options'     => [
					'original' => 'Original Color',
					'custom'   => 'Custom Color',
				],
				'default'      => 'original',
				'prefix_class' => 'bdt-ss-btns-color-',
				'separator'    => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'     => esc_html__( 'Normal', 'bdthemes-element-pack' ),
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label'     => esc_html__( 'Primary Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-ss-btns-style-flat .bdt-ss-btn,
					 {{WRAPPER}}.bdt-ss-btns-style-gradient .bdt-ss-btn,
					 {{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn .bdt-ss-icon,
					 {{WRAPPER}}.bdt-ss-btns-style-minimal .bdt-ss-btn .bdt-ss-icon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.bdt-ss-btns-style-framed .bdt-ss-btn,
					 {{WRAPPER}}.bdt-ss-btns-style-minimal .bdt-ss-btn,
					 {{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn' => 'color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label'     => esc_html__( 'Secondary Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-ss-btns-style-flat .bdt-ss-icon, 
					 {{WRAPPER}}.bdt-ss-btns-style-flat .bdt-social-share-text, 
					 {{WRAPPER}}.bdt-ss-btns-style-gradient .bdt-ss-icon,
					 {{WRAPPER}}.bdt-ss-btns-style-gradient .bdt-social-share-text,
					 {{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-icon,
					 {{WRAPPER}}.bdt-ss-btns-style-minimal .bdt-ss-icon' => 'color: {{VALUE}}',
					 '{{WRAPPER}}.bdt-ss-btns-style-framed .bdt-ss-btn' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn, {{WRAPPER}}.bdt-ss-btns-style-framed .bdt-ss-btn' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
					'style' => ['framed', 'boxed']
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'     => esc_html__( 'Hover', 'bdthemes-element-pack' ),
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'primary_color_hover',
			[
				'label'     => esc_html__( 'Primary Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-ss-btns-style-flat .bdt-ss-btn:hover,
					 {{WRAPPER}}.bdt-ss-btns-style-gradient .bdt-ss-btn:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.bdt-ss-btns-style-framed .bdt-ss-btn:hover,
					 {{WRAPPER}}.bdt-ss-btns-style-minimal .bdt-ss-btn:hover,
					 {{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn:hover' => 'color: {{VALUE}}; border-color: {{VALUE}}',
					'{{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn:hover .bdt-ss-icon, 
					 {{WRAPPER}}.bdt-ss-btns-style-minimal .bdt-ss-btn:hover .bdt-ss-icon' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'secondary_color_hover',
			[
				'label'     => esc_html__( 'Secondary Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-ss-btns-style-flat .bdt-ss-btn:hover .bdt-ss-icon, 
					 {{WRAPPER}}.bdt-ss-btns-style-flat .bdt-ss-btn:hover .bdt-social-share-text, 
					 {{WRAPPER}}.bdt-ss-btns-style-gradient .bdt-ss-btn:hover .bdt-ss-icon,
					 {{WRAPPER}}.bdt-ss-btns-style-gradient .bdt-ss-btn:hover .bdt-social-share-text,
					 {{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn:hover .bdt-ss-icon,
					 {{WRAPPER}}.bdt-ss-btns-style-minimal .bdt-ss-btn:hover .bdt-ss-icon' => 'color: {{VALUE}}',
					 '{{WRAPPER}}.bdt-ss-btns-style-framed .bdt-ss-btn:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-ss-btns-style-boxed .bdt-ss-btn:hover, {{WRAPPER}}.bdt-ss-btns-style-framed .bdt-ss-btn:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'color_source' => 'custom',
					'style' => ['framed', 'boxed']
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function has_counter( $media_name ) {
		$settings = $this->get_active_settings();

		return 'icon' !== $settings['view'] && 'yes' === $settings['show_counter'] && ! empty( Module::get_social_media( $media_name )['has_counter'] );
	}
	
	public function render() {

		$settings  = $this->get_active_settings();

		if ( empty( $settings['share_buttons'] ) ) {
			return;
		}

		$show_text = 'text' === $settings['view'] ||  $settings['show_label'];
		?>
		<div class="bdt-social-share bdt-ep-grid">
			<?php
			foreach ( $settings['share_buttons'] as $button ) {
				$social_name = $button['button'];
				$has_counter = $this->has_counter( $social_name );

				if ( 'custom' === $settings['share_url_type'] ) {
					$this->add_render_attribute( 'social-attrs', 'data-url', esc_url( $settings['share_url']['url'] ), true );
				}

				$this->add_render_attribute(
					[
						'social-attrs' => [
							'class' => [
								'bdt-ss-btn',
								'bdt-ss-' . $social_name
							],
							'data-social' => $social_name,
						]
					], '', '', true
				);

				if('link' == $social_name){
					global $wp;
					$current_url = home_url(add_query_arg(array(), $wp->request));

					$this->add_render_attribute(
						[
							'social-attrs' => [
								'data-url' => $current_url
							]
						],
						'',
						'',
						true
					);
				}
				if(isset($button['copied_text']) & !empty($button['copied_text'])){
					$this->add_render_attribute(
						[
							'social-attrs' => [
								'data-orginal' => $button['text'] ? esc_html($button['text']) : Module::get_social_media($social_name)['title'],
								'data-copied' => $button['copied_text']
							]
						],
						'',
						'',
						true
					);
				}

				?>
				<div class="bdt-social-share-item bdt-ep-grid-item">
					<div <?php echo $this->get_render_attribute_string( 'social-attrs' ); ?>>
						<?php if ( 'icon' === $settings['view'] || 'icon-text' === $settings['view'] ) : ?>
							<span class="bdt-ss-icon">
								<i class="<?php echo self::get_social_media_class( $social_name ); ?>"></i>
							</span>
						<?php endif; ?>
						<?php if ( $show_text || $has_counter ) : ?>
							<div class="bdt-social-share-text bdt-inline">
								<?php if ( 'yes' === $settings['show_label'] || 'text' === $settings['view'] ) : ?>
									<span class="bdt-social-share-title">
										<?php echo $button['text'] ? esc_html($button['text']) : Module::get_social_media( $social_name )['title']; ?>
									</span>
								<?php endif; ?>
								<?php if ( $has_counter ) : ?>
									<span class="bdt-social-share-counter" data-counter="<?php echo esc_attr($social_name); ?>"></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>

		
		<?php

		
	}

	
}
