<?php
/**
 * Circle Image Group widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || die();

class Image_Stack_Group extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Image Stack Group', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/image-stack-group/';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-lens';
	}

	public function get_keywords() {
		return [ 'image', 'stack', 'icon', 'group' ];
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'_section_icon',
			[
				'label' => __( 'Items', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'media_type',
			[
				'label' => __( 'Media Type', 'happy-elementor-addons' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'img' => [
						'title' => __( 'Image', 'happy-elementor-addons' ),
						'icon' => 'eicon-image',
					],
					'icon' => [
						'title' => __( 'Icon', 'happy-elementor-addons' ),
						'icon' => 'eicon-star',
					],
				],
				'default' => 'img',
				'toggle' => false,
			]
		);

		if ( ha_is_elementor_version( '<', '2.6.0' ) ) {
			$repeater->add_control(
				'icon',
				[
					'label' => 'Icon',
					'type' => Controls_Manager::ICON,
					'label_block' => true,
					'options' => ha_get_happy_icons(),
					'default' => 'fa fa-smile-o',
					'condition' => [ 'media_type' => 'icon' ],
				]
			);
		} else {
			$repeater->add_control(
				'selected_icon',
				[
					'label' => 'Icon',
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'label_block' => false,
					'skin' => 'inline',
					'exclude_inline_options' => ['svg'],
					'default' => [
						'value' => 'fas fa-smile-wink',
						'library' => 'fa-solid',
					],
					'condition' => [ 'media_type' => 'icon' ]
				]
			);
		}

		$repeater->add_control(
			'image',
			[
				'type' => Controls_Manager::MEDIA,
				'label' => __( 'Image', 'happy-elementor-addons' ),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [ 'media_type' => 'img' ],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'tooltip',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __( 'Tooltip', 'happy-elementor-addons' ),
				'placeholder' => __( 'Type title here', 'happy-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'tooltip_position',
			[
				'label' => __( 'Tooltip Position', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-left'
					],
					'up'  => [
						'title' => __( 'Up', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-top'
					],
					'down'  => [
						'title' => __( 'Down', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-bottom'
					],
					'right'  => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-right'
					],
				],
				'toggle' => true,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'happy-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => 'https://example.com',
				'dynamic' => [
					'active' => true,
				]
			]
		);


		$repeater->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [ 'media_type' => 'icon' ],
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item{{CURRENT_ITEM}} i' => 'color: {{VALUE}}'
				]
			]
		);

		$repeater->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'icon_bg_color',
				'label' => __( 'Background', 'happy-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-cig-item{{CURRENT_ITEM}} i'
			]
		);

		$repeater->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);


		$repeater->add_control(
			'border_color_item',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} {{CURRENT_ITEM}} .fw-svg-wrap' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$placeholder = [
			'image' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		];

		$this->add_control(
			'images',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '<# print(tooltip || "Image Group Item"); #>',
				'default' => array_fill( 0, 4, $placeholder )
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'toggle' => true,
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'_section_style_icon',
			[
				'label' => __( 'Image / Icon', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'img_size',
			[
				'label' => __( 'Item Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item i,{{WRAPPER}} .ha-cig-item img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-cig-item i,{{WRAPPER}} .ha-cig-item .fw-svg-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-cig-item svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hr1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'icon_border_size',
			[
				'label' => __( 'Border Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item i,{{WRAPPER}} .ha-cig-item img,{{WRAPPER}} .ha-cig-item .fw-svg-wrap' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item i' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-cig-item img' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-cig-item .fw-svg-wrap' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
            'icon_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-elementor-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-cig-item,{{WRAPPER}}  .ha-cig-item i, {{WRAPPER}} .ha-cig-item img, {{WRAPPER}} .ha-cig-item .fw-svg-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);

		$this->add_control(
			'hr2',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cig-item i' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'icon_bg_color',
				'label' => __( 'Background', 'happy-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-cig-item i'
			]
		);

		$this->add_group_control( Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'label' => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-cig-item-outline',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'_section_style_tooltip',
			[
				'label' => __( 'Tooltips', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tooltip_position',
			[
				'label' => __( 'Position', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-left'
					],
					'up'  => [
						'title' => __( 'Up', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-top'
					],
					'down'  => [
						'title' => __( 'Down', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-bottom'
					],
					'right'  => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-right'
					],
				],
				'default' => 'up',
				'toggle' => false,
			]
		);

		$this->add_responsive_control(
			'tooltip_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} [tooltip]::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
            'tooltip_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-elementor-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} [tooltip]::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);

		$this->add_control(
			'hr3',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(),
			[
				'name' => 'tooltip_content_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} [tooltip]::after',
			]
		);

		$this->add_control(
			'tooltip_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [tooltip]::after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tooltip_background',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [tooltip]::after' => 'background: {{VALUE}};',
					'{{WRAPPER}} [tooltip]::before' => '--caret-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control( Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tooltip_box_shadow',
				'label' => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} [tooltip]::after',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Used to generate the final HTML displayed on the frontend.
	 *
	 * Note that if skin is selected, it will be rendered by the skin itself,
	 * not the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['images'] ) ) {
			return;
		}

		$fs_inline_fw = ha_elementor()->experiments->is_feature_active( 'e_font_icon_svg' );
		?>

		<div class="ha-cig">
			<?php foreach ( $settings['images'] as $item ) :
				$media_type = $item['media_type'];


				$item_id = 'elementor-repeater-item-'.$item['_id'];

				if($media_type == "icon"){
					$bgType = $item['icon_bg_color_background'];
					$bg = $item['icon_bg_color_color'];
					$bgGlobal = isset($item['__globals__'])?$item['__globals__']['icon_bg_color_color']:'';

					$library = $item['selected_icon']['library'];
					$library = explode('-', $library);
					$library = $library[0];

					if($bgGlobal){
						$bgGlobal = explode("=",$bgGlobal);
						$bgGlobal = $bgGlobal[1];
						$bgGlobal = 'var(--e-global-color-'.$bgGlobal.')';
					}

					$backGround = $bg?$bg:$bgGlobal;

					if($bgType == 'classic'){
						$attr['style'] = "background:".$backGround." !important";
					}else{
						$attr['style'] = "";
					}

					ob_start();
					ha_render_icon( $item, 'icon', 'selected_icon', $attr);
					$content = ob_get_clean();

					if($fs_inline_fw && $library == "fa"){
						$content = '<span class="fw-svg-wrap">'.$content.'</span>';
					}
				}else{

					if(isset($item['image']) && $item['image']['url'] != ''){
						$img_url = $item['image']['url'];
					}else{
						$img_url = Utils::get_placeholder_image_src();
					}

					$content = '<img src="'.$img_url.'" alt="">';
				}

				$tooltip_data = '';

				$tooltip_txt = $item['tooltip'];
				if(!empty($item['tooltip_position'])){
					$tooltip_position = $item['tooltip_position'];
				}else{
					$tooltip_position = $settings['tooltip_position'];
				}

				if($tooltip_txt){
					$tooltip_data = 'tooltip="'.$tooltip_txt.'" flow="'.$tooltip_position.'"';
				}

				$id = 'ha-cig-item-' . $item['_id'];

				$link = $item['link'];

				if(!empty($link['url'])){
					$this->add_link_attributes( $id, $item['link'] );
					$wrap_start = '<a '.$this->get_render_attribute_string( $id ).' class="ha-cig-item ha-cig-item-outline '.$item_id.'" '.$tooltip_data.'>';
					$wrap_end   = '</a>';
				}else{
					$wrap_start = '<span class="ha-cig-item ha-cig-item-outline '.$item_id.'" '.$tooltip_data.'>';
					$wrap_end   = '</span>';
				}

				echo $wrap_start, $content, $wrap_end;

			endforeach; ?>

		</div>

		<?php
	}
}
