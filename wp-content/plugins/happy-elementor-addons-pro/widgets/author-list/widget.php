<?php
/**
 * Autor List widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || die();

class Author_List extends Base {

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Author List', 'happy-addons-pro' );
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
        return 'hm hm-user-male';
    }

    public function get_keywords() {
        return [ 'author', 'list', 'post' ];
    }

	/**
     * Register widget content controls
     */
    protected function register_content_controls() {

		$this->start_controls_section(
            '_author_list',
            [
                'label' => __( 'Author List', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
			'post_type',
			[
				'label' => __( 'Post Type', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_post_type_list(),
				'default' => key( $this->get_post_type_list() ),
			]
        );

        $this->add_control(
			'author_name',
			[
				'label' => __( 'Name', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
                    'display_name' => __( 'Display Name', 'happy-addons-pro' ),
                    'first_name' => __( 'First Name', 'happy-addons-pro' ),
                    'last_name' => __( 'Last Name', 'happy-addons-pro' ),
                    'nickname' => __( 'Nick Name', 'happy-addons-pro' ),
                    'user_nicename' => __( 'User Nice Name', 'happy-addons-pro' )
                ],
				'default' => 'display_name',
			]
        );

        $this->add_control(
			'author_avatar',
			[
				'label' => __( 'Avatar', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
        );

        $this->add_control(
			'author_avatar_size',
			[
				'label' => __( 'Avatar Size', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'author_avatar' => 'yes'
                ],
				'options' => [
                    '25' => __( '25 x 25', 'happy-addons-pro' ),
                    '35' => __( '35 x 35', 'happy-addons-pro' ),
                    '45' => __( '45 x 45', 'happy-addons-pro' ),
                    '60' => __( '60 x 60', 'happy-addons-pro' ),
                    '80' => __( '80 x 80', 'happy-addons-pro' ),
                    '150' => __( '150 x 150', 'happy-addons-pro' )
                ],
				'default' => '45',
			]
        );

        $this->add_control(
			'author_icon',
			[
				'label' => __( 'Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'default' => [
					'value' => 'hm hm-avatar-man',
					'library' => 'happy-icons'
				],
				'condition' => [
					'author_avatar!' => 'yes'
				]
			]
        );

        $this->add_control(
			'author_post_count',
			[
				'label' => __( 'Post Count', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
        );

        $this->add_control(
            'author_post_count_text',
            [
                'label' => __( 'Post Count Text', 'happy-addons-pro' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => __( 'Post Count ', 'happy-addons-pro' ),
                'placeholder' => __( 'Post Count Text', 'happy-addons-pro' ),
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'author_post_count' => 'yes'
                ]
            ]
        );

        $this->add_control(
			'author_email',
			[
				'label' => __( 'Email', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

        $this->add_control(
			'author_description',
			[
				'label' => __( 'Author Bio', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

        $this->add_control(
			'author_archive_link_name',
			[
				'label' => __( 'Archive Link in Name', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

        $this->add_control(
			'author_website_link_image',
			[
				'label' => __( 'Website Link in Image/Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

        $this->add_responsive_control(
            'list_position',
            [
                'label' => __( 'List Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'separator' => 'before',
                'options' => [
                    'inline' => [
                        'title' => __( 'Inline', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-h',
                    ],
                    'block' => [
                        'title' => __( 'Block', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-v',
                    ],
                ],
                'prefix_class' => 'ha-list-',
                'default' => 'block',
                'toggle' => false,
                'selectors_dictionary' => [
                    'inline' => 'flex-direction: row',
                    'block' => 'flex-direction: column',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-autor-list-wrapper' => '{{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'avatar_position',
            [
                'label' => __( 'Avatar Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'inline' => [
                        'title' => __( 'Inline', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-h',
                    ],
                    'block' => [
                        'title' => __( 'Block', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-v',
                    ],
                ],
                'toggle' => false,
                'prefix_class' => 'ha-avatar-',
                'default' => 'inline',
                'selectors_dictionary' => [
                    'inline' => 'flex-direction: row',
                    'block' => 'flex-direction: column',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-head' => '{{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_count_position',
            [
                'label' => __( 'Post Count Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'inline' => [
                        'title' => __( 'Inline', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-h',
                    ],
                    'block' => [
                        'title' => __( 'Block', 'happy-addons-pro' ),
                        'icon' => 'eicon-ellipsis-v',
                    ],
                ],
                'toggle' => false,
                'prefix_class' => 'ha-post-count-',
                'default' => 'block',
                'condition' => [
                    'author_post_count' => 'yes'
                ],
                'selectors_dictionary' => [
                    'inline' => 'flex-direction: row',
                    'block' => 'flex-direction: column',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-name' => '{{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'list_alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
                'toggle' => false,
                'prefix_class' => 'ha-alignment-',
				'selectors' => [
					'{{WRAPPER}} .ha-text'  => 'text-align: {{VALUE}};'
				],
			]
		);

        $this->end_controls_section();
    }

	/**
     * Register widget style controls
     */
    protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__author_style_controls();
	}

    protected function __common_style_controls() {

        $this->start_controls_section(
            '_section_common_style',
            [
                'label' => __( 'Common', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'list_spacing',
			[
				'label' => __( 'Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
                    '{{WRAPPER}}.ha-list-inline .ha-author-list:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ha-list-block .ha-author-list:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
        );

        $this->add_responsive_control(
            'list_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'list_boder_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-author-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'list_boder',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-author-list'
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'list_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-author-list',
			]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .ha-author-list',
            ]
        );

        $this->end_controls_section();
	}

    protected function __author_style_controls() {

        $this->start_controls_section(
            'author_style',
            [
                'label' => __( 'Author', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            '_heading_avater',
            [
                'type' => Controls_Manager::HEADING,
                'label' => __( 'Avatar', 'happy-addons-pro' ),
            ]
        );

        $this->add_responsive_control(
            'avatar_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-avater' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'avatar_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'condition' => [
                    'author_avatar' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-avater img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'avatar_icon_size',
            [
                'label' => __( 'Icon Size', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'condition' => [
                    'author_avatar!' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-avater i' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'avatar_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'condition' => [
                    'author_avatar' => 'yes'
                ],
                'selector' => '{{WRAPPER}} .ha-author-list-avater img, {{WRAPPER}} .ha-author-list-avater i'
            ]
        );

        $this->add_control(
            'avatar_icon_color',
            [
                'label' => __( 'Icon Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'author_avatar!' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-avater i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'avatar_icon_hover_color',
            [
                'label' => __( 'Icon Hover Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'author_avatar!' => 'yes',
                    'author_website_link_image' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-avater i:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            '_heading_name',
            [
                'type' => Controls_Manager::HEADING,
                'label' => __( 'Name', 'happy-addons-pro' ),
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'exclude' => [
                    'line_height'
                ],
                'selector' => '{{WRAPPER}} .ha-author-list-name-text, {{WRAPPER}} .ha-author-list-name-text a',
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-name-text' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ha-author-list-name-text a' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'name_hover_color',
            [
                'label' => __( 'Hover Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'author_archive_link_name' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-name-text a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            '_heading_post_count',
            [
                'type' => Controls_Manager::HEADING,
                'label' => __( 'Post Count', 'happy-addons-pro' ),
                'separator' => 'before'
            ]
        );

        $this->add_control(
			'note',
			[
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'author_post_count!' => 'yes',
				],
				'raw' => __( '<strong>Post Count</strong> is Switched off from "Author List" content section', 'happy-addons-pro' ),
			]
		);

        $this->add_responsive_control(
            'post_count_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'condition' => [
                    'author_post_count' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-post-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'post_count_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'exclude' => [
                    'line_height'
                ],
                'condition' => [
                    'author_post_count' => 'yes'
                ],
                'selector' => '{{WRAPPER}} .ha-author-list-post-count',
            ]
        );

        $this->add_control(
            'post_count_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'author_post_count' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-post-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            '_heading_email',
            [
                'type' => Controls_Manager::HEADING,
                'label' => __( 'Email', 'happy-addons-pro' ),
                'separator' => 'before'
            ]
        );

        $this->add_control(
			'note_email',
			[
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'author_email!' => 'yes',
				],
				'raw' => __( '<strong>Email</strong> is Switched off from "Author List" content section', 'happy-addons-pro' ),
			]
		);

        $this->add_responsive_control(
            'email_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'condition' => [
                    'author_email' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-email' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'email_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'exclude' => [
                    'line_height'
                ],
                'condition' => [
                    'author_email' => 'yes'
                ],
                'selector' => '{{WRAPPER}} .ha-author-list-email',
            ]
        );

        $this->add_control(
            'email_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'author_email' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-email' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            '_heading_description',
            [
                'type' => Controls_Manager::HEADING,
                'label' => __( 'Author Bio', 'happy-addons-pro' ),
                'separator' => 'before'
            ]
        );

        $this->add_control(
			'note_description',
			[
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'condition' => [
					'author_description!' => 'yes',
				],
				'raw' => __( '<strong>Author Bio</strong> is Switched off from "Author List" content section', 'happy-addons-pro' ),
			]
		);

        $this->add_responsive_control(
            'description_spacing',
            [
                'label' => __( 'Spacing', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'condition' => [
                    'author_description' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => __( 'Typography', 'happy-addons-pro' ),
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'exclude' => [
                    'line_height'
                ],
                'condition' => [
                    'author_description' => 'yes'
                ],
                'selector' => '{{WRAPPER}} .ha-author-list-description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'author_description' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-author-list-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $author_ids = [];
        $users = get_users();

        foreach ( $users as $user ) {
            $user_post_count = count_user_posts( $user->ID, $settings['post_type'], true );
            if ( $user_post_count > 0 ) {
                $author_ids[] = ['autor_id' => $user->ID, 'post_count' => $user_post_count ];
            }
        }

        if ( empty( $author_ids ) ) {
            printf( '<div class="ha-author-list-error"><strong>%s</strong> %s</div>', $settings['post_type'], __( ' post type don\'t have any post.', 'happy-addons-pro' ) );
            return;
        }

        // print_r( $author_ids );
        ?>

        <div class="ha-autor-list-wrapper">
            <?php foreach ( $author_ids as $author_id ) : ?>
                <div class="ha-author-list">
                    <div class="ha-author-list-head">
                        <?php if ( $settings['author_avatar'] == 'yes' ) : ?>
                            <div class="ha-author-list-avater">
                                <?php
                                if ( $settings['author_website_link_image'] == 'yes' ) {
                                    printf( '<a href="%s">%s</a>',
                                        esc_url( get_the_author_meta( 'user_url', $author_id['autor_id'] ) ),
                                        get_avatar( $author_id['autor_id'], $settings['author_avatar_size'] )
                                    );
                                } else {
                                    echo get_avatar( $author_id['autor_id'], $settings['author_avatar_size'] );
                                }
                                ?>
                            </div>
                        <?php elseif ( $settings['author_icon']['value'] ) : ?>
                            <div class="ha-author-list-avater">
                                <?php
                                if ( $settings['author_website_link_image'] == 'yes' ) { ?>
                                    <a href="<?php echo esc_url( get_the_author_meta( 'user_url', $author_id['autor_id'] ) ) ?>">
                                        <?php Icons_Manager::render_icon( $settings['author_icon'], [ 'aria-hidden' => 'true' ] ) ?>
                                    </a>
                                <?php
                                } else {
                                    Icons_Manager::render_icon( $settings['author_icon'], [ 'aria-hidden' => 'true' ] );
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="ha-author-list-meta">
                            <div class="ha-author-list-name">
                               <div class="ha-author-list-name-text">
                                    <?php
                                    if ( $settings['author_archive_link_name'] == 'yes' ) {
                                        printf( '<a href="%s">%s</a>',
                                            esc_url( get_author_posts_url( $author_id['autor_id'] ) ),
                                            esc_html( get_the_author_meta( $settings['author_name'], $author_id['autor_id'] ) )
                                        );
                                    } else {
                                        echo esc_html( get_the_author_meta( $settings['author_name'], $author_id['autor_id'] ) );
                                    }
                                    ?>
                                </div>

                                <?php if ( $settings['author_post_count'] == 'yes' ) : ?>
                                    <div class="ha-author-list-post-count">
                                        <?php
                                        echo ! empty( $settings['author_post_count_text'] ) ? esc_html( $settings['author_post_count_text'] ) : '';
                                        echo __( $author_id['post_count'], 'happy-addons-pro' );
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ( $settings['author_email'] == 'yes' ) : ?>
                                <div class="ha-author-list-email"><?php echo esc_html( get_the_author_meta( 'user_email', $author_id['autor_id'] ) ); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( $settings['author_description'] == 'yes' ) : ?>
                        <div class="ha-author-list-description"><?php echo esc_html( get_the_author_meta( 'description', $author_id['autor_id'] ) ); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php
    }

    protected function get_post_type_list() {
        $args = [
            'public'   => true,
            'show_in_nav_menus' => true
        ];
        $post_types = get_post_types( $args, 'objects' );
        $post_types = wp_list_pluck( $post_types, 'label', 'name' );
        return $post_types;
    }

}
