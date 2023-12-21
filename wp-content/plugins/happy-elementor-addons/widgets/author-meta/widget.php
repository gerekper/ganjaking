<?php
/**
 * Author Meta widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Author_Meta extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Author Meta', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/post-title/';
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
		return 'hm hm-tb-author-meta';
	}

	public function get_keywords() {
		return [ 'author', 'author_meta', 'author info' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__author_content_controls();

	}

	protected function __author_content_controls() {
		$this->start_controls_section(
			'_section_author_meta',
			[
				'label' => __( 'Author Meta', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'        => __( 'Show Author Name', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);


		$this->add_control(
			'author_meta_tag',
			[
				'label' => __( 'Author Name Tag', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'h1'  => [
						'title' => __( 'H1', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h1'
					],
					'h2'  => [
						'title' => __( 'H2', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h2'
					],
					'h3'  => [
						'title' => __( 'H3', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h3'
					],
					'h4'  => [
						'title' => __( 'H4', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h4'
					],
					'h5'  => [
						'title' => __( 'H5', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h5'
					],
					'h6'  => [
						'title' => __( 'H6', 'happy-elementor-addons' ),
						'icon' => 'eicon-editor-h6'
					]
				],
				'default' => 'h4',
				'toggle' => false,
				'condition'=>[
					'show_author' => 'yes',
				]
			]
		);

		$this->add_control(
			'show_avatar',
			[
				'label'        => __( 'Show Avatar', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'show_bio',
			[
				'label'        => __( 'Show Short Bio', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			]
		);
		
		$this->add_control(
			'show_archive_btn',
			[
				'label'        => __( 'Show Archive Button', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'author_link_to',
			[
				'label' => __( 'Link', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::SELECT,
				'options' => [
					''              => __( 'None', 'happy-elementor-addons' ),
					'website'       => __( 'Website', 'happy-elementor-addons' ),
					'admin_archive' => __( 'Admin Posts', 'happy-elementor-addons' ),
				],
				'description'       => __( 'Link for the Author Name and Image', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'avatar_size',
			[
				'label' => __( 'Avatar Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 500,
				'step' => 1,
				'default' => 96,
			]
		);

		$this->add_control(
			'avatar_image_position',
			[
				'label'   => __( 'Avatar Image Position', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
					'top' => [
						'title' => __( 'Top', 'happy-elementor-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-elementor-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'left',
			]
		);

        $this->end_controls_section();
	}

	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {
		$this->__author_style_controls();
		$this->__avatar_style_controls();
		$this->__author_short_bio_controls();
		$this->__author_button_style_controls();
	}


	protected function __author_style_controls() {

        $this->start_controls_section(
            '_section_style_text',
            [
                'label' => __( 'Author Name', 'happy-elementor-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'author_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-author-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-author-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'author_text_shadow',
				'selector' => '{{WRAPPER}} .ha-author-title',
			]
		);


        $this->end_controls_section();
	}

	protected function __author_short_bio_controls() {

        $this->start_controls_section(
            '_section_style_short_bio',
            [
                'label' => __( 'Short Bio', 'happy-elementor-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'bio_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-desc p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'bio_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-desc p',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);


        $this->end_controls_section();
	}

	protected function __avatar_style_controls() {

        $this->start_controls_section(
            '_section_avatar_style',
            [
                'label' => __( 'Avatar', 'happy-elementor-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'avatar_vertical_lign',
			[
				'label'   => __( 'Vertical Align', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Top', 'happy-elementor-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'happy-elementor-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-avatar' => 'align-self:{{UNIT}};',
				],
			]
		);
        
		$this->add_responsive_control(
			'avatar_width',
			[
				'label' => __( 'Wdth', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 96,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-avatar img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		// $this->add_responsive_control(
		// 	'avatar_margin',
		// 	[
		// 		'label' => __( 'Avatar Margin', 'happy-elementor-addons' ),
		// 		'type' => Controls_Manager::DIMENSIONS,
		// 		'size_units' => [ 'px', '%' ],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ha-avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

        $this->add_responsive_control(
			'avatar_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-avatar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'avatar_border',
				'label' => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-avatar img',
			]
		);

		$this->add_control(
			'avatar_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '50',
					'right' => '50',
					'bottom' => '50',
					'left' => '50',
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function __author_button_style_controls() {

        $this->start_controls_section(
            '_section_style_button',
            [
                'label' => __( 'Button', 'happy-elementor-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'author_info_button_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '7',
					'right' => '15',
					'bottom' => '7',
					'left' => '15',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-author-posts' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_info_button_hover_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-author-posts',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'author_info_button_border',
				'selector' => '{{WRAPPER}} .ha-desc .ha-author-posts',
			]
		);

		$this->add_control(
			'author_info_button_border_radius',
			[
				'label' => __('Border Radius', 'happy-elementor-addons'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-desc .ha-author-posts' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
            'author_info_button_active_tabs'
        );

		$this->start_controls_tab(
            'author_info_button_normal_tab',
            [
                'label'    => __('Normal', 'happy-elementor-addons')
            ]
        );

        $this->add_control(
			'author_info_button_text_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#555555',
				'selectors' => [
					'{{WRAPPER}} .ha-author-posts-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'author_info_button_background',
                'label' => __('Background', 'happy-elementor-addons'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .ha-author-posts-btn',
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
            'author_info_button_hover_tab',
            [
                'label'    => __('Hover', 'happy-elementor-addons')
            ]
        );

		$this->add_control(
			'author_info_button_hover_text_color',
			[
				'label' => esc_html__( 'Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#555555',
				'selectors' => [
					'{{WRAPPER}} .ha-author-posts-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'author_info_button_hover_background',
                'label' => __('Background', 'happy-elementor-addons'),
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .ha-author-posts-btn:hover',
            ]
        );

		$this->add_control(
			'author_info_button_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#555555',
				'selectors' => [
					'{{WRAPPER}} .ha-author-posts-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		// $user_id = get_the_author_meta( 'ID' );
		// $avatar = get_avatar($user_id, $settings['avatar_size']);
		// $display_name = get_the_author_meta( 'display_name' );
		// $bio = get_the_author_meta( 'description' );
		$user_id = get_post_field( 'post_author', get_the_ID() );
		$avatar = get_avatar($user_id, $settings['avatar_size']);
		$display_name = get_the_author_meta( 'display_name', $user_id );
		$bio = get_the_author_meta( 'description', $user_id );

		$post_url = get_author_posts_url( $user_id );
		$user_url =  get_the_author_meta( 'user_url', $user_id );

		$this->add_render_attribute('author', 'class', 'ha-author');
		$this->add_render_attribute('avatar', 'class', 'ha-avatar');
		if( $settings['avatar_image_position'] && 'yes' === $settings['show_avatar']){
			$this->add_render_attribute('author', 'class', 'avatar-position-' . $settings['avatar_image_position']);
		}

		if( $settings['show_author'] ){
			$this->add_render_attribute('author-title', 'class', 'ha-author-title');
		}

		?>

		<div <?php $this->print_render_attribute_string('author'); ?>>
			<?php if('yes' === $settings['show_avatar']) : ?>
				<div <?php $this->print_render_attribute_string('avatar'); ?>>
					<?php echo $avatar; ?>
				</div>
			<?php endif; ?>

			<div class="ha-desc">
				<?php
				if('yes' === $settings['show_author']){
					printf('<%1$s %2$s>%3$s</%1$s>', esc_attr($settings['author_meta_tag']), $this->get_render_attribute_string('author-title'), esc_html($display_name));
				}
				if('yes' === $settings['show_bio']){
					printf('<p>%1$s</p>', esc_html($bio));
				}

				if( 'yes' == $settings['show_archive_btn'] ) { ?>
					<a class="ha-author-posts ha-author-posts-btn" href="<?php echo esc_url( $post_url ); ?>">All Posts</a>
				<?php }
				?>
			</div>
		</div>


		<?php
	}
}
