<?php

/**
 * Social Share widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Social_Share extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title () {
		return __( 'Social Share', 'happy-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon () {
		return 'hm hm-share';
	}

	public function get_keywords () {
		return [ 'social', 'share', 'facebook', 'twitter', 'instagram', 'linkedin' ];
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls () {

		$this->start_controls_section(
			'_section_content',
			[
				'label' => __( 'Buttons', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'share_network',
			[
				'label'   => __( 'Network', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'facebook'    => __( 'Facebook', 'happy-elementor-addons' ),
					'twitter'     => __( 'Twitter', 'happy-elementor-addons' ),
					'linkedin'    => __( 'Linkedin', 'happy-elementor-addons' ),
					'email'       => __( 'Email', 'happy-elementor-addons' ),
					'whatsapp'    => __( 'Whatsapp', 'happy-elementor-addons' ),
					'telegram'    => __( 'Telegram', 'happy-elementor-addons' ),
					'viber'       => __( 'Viber', 'happy-elementor-addons' ),
					'pinterest'   => __( 'Pinterest', 'happy-elementor-addons' ),
					'tumblr'      => __( 'Tumblr', 'happy-elementor-addons' ),
					'reddit'      => __( 'Reddit', 'happy-elementor-addons' ),
					'vk'          => __( 'VK', 'happy-elementor-addons' ),
					'xing'        => __( 'Xing', 'happy-elementor-addons' ),
					'get-pocket'  => __( 'Get Pocket', 'happy-elementor-addons' ),
					'digg'        => __( 'Digg', 'happy-elementor-addons' ),
					'stumbleupon' => __( 'StumbleUpon', 'happy-elementor-addons' ),
					'weibo'       => __( 'Weibo', 'happy-elementor-addons' ),
					'renren'      => __( 'Renren', 'happy-elementor-addons' ),
					'skype'       => __( 'Skype', 'happy-elementor-addons' ),
				],
				'default' => 'facebook',
			]
		);

		$repeater->add_control(
			'custom_link',
			[
				'label'       => __( 'Custom Link', 'happy-elementor-addons' ),
				'placeholder' => __( 'https://your-share-link.com', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'hashtags',
			[
				'label'       => __( 'Hashtags', 'happy-elementor-addons' ),
				'description' => __( 'Write hashtags without # sign and with comma separated value', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'dynamic'     => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'facebook',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'linkedin',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'whatsapp',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'reddit',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'skype',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'pinterest',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'email',
						],
					]
				]
			]
		);

		$repeater->add_control(
			'share_title',
			[
				'label'     => __( 'Custom Title', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'dynamic'   => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'facebook',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'linkedin',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'reddit',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'skype',
						],
						[
							'name' => 'share_network',
							'operator' => '!==',
							'value' => 'pinterest',
						],
					]
				]
			]
		);

		$repeater->add_control(
			'email_to',
			[
				'label'     => __( 'To', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					'share_network' => 'email',
				]
			]
		);

		$repeater->add_control(
			'email_subject',
			[
				'label'     => __( 'Subject', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					'share_network' => 'email',
				]
			]
		);

		$repeater->add_control(
			'twitter_handle',
			[
				'label'     => __( 'Twitter Handle', 'happy-elementor-addons' ),
				'description' => __( 'Write without @ sign.', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'share_network' => 'twitter',
				]
			]
		);

		$repeater->add_control(
			'image',
			[
				'type' => Controls_Manager::MEDIA,
				'label' => __( 'Custom Image', 'happy-elementor-addons' ),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'share_network' => 'pinterest'
				]
			]
		);

		$repeater->add_control(
			'share_text',
			[
				'label'       => __( 'Button Text', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Share on Facebook', 'happy-elementor-addons' ),
				'dynamic'     => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'customize',
			[
				'label'          => __( 'Want To Customize?', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => __( 'Yes', 'happy-elementor-addons' ),
				'label_off'      => __( 'No', 'happy-elementor-addons' ),
				'return_value'   => 'yes',
				'separator'      => 'before'
			]
		);

		$repeater->start_controls_tabs(
			'_tab_share_colors',
			[
				'condition' => [ 'customize' => 'yes' ]
			]
		);

		$repeater->start_controls_tab(
			'_tab_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'single_color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::COLOR,
				'condition'      => [
					'customize' => 'yes'
				],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-share-network' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-share-icon-and-text > {{CURRENT_ITEM}} .ha-share-label' => 'border-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'single_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::COLOR,
				'condition'      => [
					'customize' => 'yes'
				],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-share-network' => 'background-color: {{VALUE}};',
				]
			]
		);

		$repeater->add_control(
			'single_border_color',
			[
				'label'          => __( 'Border Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'condition'      => [
					'customize' => 'yes'
				],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-share-network' => 'border-color: {{VALUE}};',
				]
			]
		);

		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'_tab_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'signle_hover_color',
			[
				'label'          => __( 'Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'condition'      => [
					'customize' => 'yes'
				],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-share-network:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$repeater->add_control(
			'single_hover_bg_color',
			[
				'label'          => __( 'Background Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'condition'      => [
					'customize' => 'yes'
				],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-share-network:hover' => 'background-color: {{VALUE}};',
				]
			]
		);

		$repeater->add_control(
			'single_hover_border_color',
			[
				'label'          => __( 'Border Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'condition'      => [
					'customize' => 'yes'
				],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ha-share-network:hover' => 'border-color: {{VALUE}};',
				]
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'icon_list',
			[
				'label'       => __( 'Share Icons', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{ share_network }}',
				'default'     => [
					[
						'share_icon'    => [
							'value'   => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
						'share_network' => 'facebook',
					],
					[
						'share_icon'    => [
							'value'   => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
						'share_network' => 'twitter',
					],
					[
						'share_icon'    => [
							'value'   => 'fab fa-linkedin',
							'library' => 'fa-brands',
						],
						'share_network' => 'linkedin',
					],
				]
			]
		);

		$this->add_control(
			'network_view',
			[
				'label'     => __( 'View', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'icon_and_text',
				'options'   => [
					'icon_and_text' => __( 'Icon and Text', 'happy-elementor-addons' ),
					'icon_only'     => __( 'Icon', 'happy-elementor-addons' ),
					'text_only'     => __( 'Text', 'happy-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'separator_show',
			[
				'label'          => __( 'Separator', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => __( 'Yes', 'happy-elementor-addons' ),
				'label_off'      => __( 'No', 'happy-elementor-addons' ),
				'return_value'   => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'ha-separator-',
				'condition' => [
					'network_view' => 'icon_and_text'
				]
			]
		);

		$this->add_responsive_control(
			'display',
			[
				'label'       => __( 'Display', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'inline-block'   => [
						'title' => __( 'Inline', 'happy-elementor-addons' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'block' => [
						'title' => __( 'Block', 'happy-elementor-addons' ),
						'icon'  => 'eicon-ellipsis-v',
					]
				],
				'desktop_default' => 'inline-block',
				'tablet_default' => 'inline-block',
				'mobile_default' => 'block',
				'toggle' => false,
				// 'prefix_class' => 'ha-display-',
				'selectors'   => [
					'{{WRAPPER}} .ha-share-button' => 'display: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'       => __( 'Alignment', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'center',
				'selectors'   => [
					'{{WRAPPER}} .ha-share-buttons' => 'text-align: {{VALUE}};'
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls () {

		$this->start_controls_section(
			'_section_button_style',
			[
				'label' => __( 'Button', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'          => __( 'Padding', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => [ 'px', '%' ],
				'selectors'      => [
					'{{WRAPPER}} .ha-share-network' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'     => __( 'Spacing', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ha-share-button:not(:last-child)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-share-network' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .ha-share-network',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .ha-share-network',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => __( 'Icon Size', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-share-network' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'          => __( 'Icon Right Spacing', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::SLIDER,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-network i' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'separator_spacing',
			[
				'label'          => __( 'Separator Right Spacing', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::SLIDER,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-label' => 'padding-left: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-share-network .ha-share-label'
			]
		);

		$this->start_controls_tabs( '_tab_icons_colors' );

		$this->start_controls_tab(
			'_tab_normal_color',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::COLOR,

				'selectors'      => [
					'{{WRAPPER}} .ha-share-network ' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-network' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __( 'Separator Color', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-label' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_common_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'common_hover_color',
			[
				'label'          => __( 'Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-network:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'common_hover_bg_color',
			[
				'label'          => __( 'Background Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-network:hover' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'common_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-share-network:hover' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'separator_hover_color',
			[
				'label' => __( 'Separator Color', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-share-network:hover .ha-share-label' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render () {
		$settings = $this->get_settings_for_display();
		$social_icons = $settings['icon_list'];
		$network_view = $settings['network_view'];

		// print_r($settings);
		?>
		<ul class="ha-share-buttons">
			<?php
			foreach ( $social_icons as $icon ) :
				$social_media_name  = $icon['share_network'];
				$custom_share_title = esc_html( $icon['share_title'] );
				$share_text         = esc_html( $icon['share_text'] );
				$default_share_text = ucfirst( $social_media_name );
				$image = isset($icon['image']['url'])? $icon['image']['url']: '';
				$twitter_handle = $icon['twitter_handle'];
				$email_to = $icon['email_to'];
				$email_subject = $icon['email_subject'];

				$share_on_text = $share_text ? $share_text : $default_share_text;

				$hashtags = $icon['hashtags'];
				$url = get_the_permalink();

				$custom_share_url = $icon['custom_link']['url'];
				$share_url        = $custom_share_url ? $custom_share_url : $url;

				$this->set_render_attribute( 'list_classes', 'class', [
					'ha-share-button',
					'elementor-repeater-item-' . $icon['_id']
				] );

				$this->set_render_attribute( 'link_classes', 'class', [
					'sharer',
					'ha-share-network',
					'elementor-social-icon-' . esc_attr( $social_media_name ),
				] );

				$this->set_render_attribute( 'link_classes', 'data-sharer', esc_attr( $social_media_name ) );
				$this->set_render_attribute( 'link_classes', 'data-url', $share_url );
				$this->set_render_attribute( 'link_classes', 'data-hashtags', $hashtags ? esc_html( $hashtags ) : '' );
				$this->set_render_attribute( 'link_classes', 'data-title', $custom_share_title );
				$this->set_render_attribute( 'link_classes', 'data-image', esc_url( $image ) );
				$this->set_render_attribute( 'link_classes', 'data-to', esc_attr( $email_to ) );
				$this->set_render_attribute( 'link_classes', 'data-subject', esc_attr( $email_subject ) );
				?>
				<li <?php $this->print_render_attribute_string( 'list_classes' ); ?>>
					<a <?php $this->print_render_attribute_string( 'link_classes' ); ?>>
						<?php
						$social_media_name = $social_media_name == 'email' ? 'envelope' : $social_media_name;
						$ico_library = $social_media_name == 'envelope' ? 'fa' : 'fab';
						
						if ( 'icon_and_text' == $network_view ) {
							?>
							<i class="<?=$ico_library?> fa-<?php echo esc_attr( $social_media_name ); ?>" aria-hidden="true"></i>
							<?php
							if ( ! empty( $share_on_text ) && '' != $share_on_text ) {
								printf( "<span class='ha-share-label'>%s</span>", $share_on_text );
							}
						}
						if ( 'icon_only' == $network_view ) {
							?>
							<i class="<?=$ico_library?> fa-<?php echo esc_attr( $social_media_name ); ?>" aria-hidden="true"></i>
							<?php
						}
						if ( 'text_only' == $network_view ) {
							if ( ! empty( $share_on_text ) && '' != $share_on_text ) {
								printf( "<span class='ha-share-label'>%s</span>", $share_on_text );
							}
						}
						?>
					</a>
				</li>
				<?php
			endforeach;
			?>

		</ul>
		<?php

	}

	}
