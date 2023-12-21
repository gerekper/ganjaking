<?php
/**
 * Sticky Video
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Sticky_Video extends Base {

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Sticky Video', 'happy-addons-pro' );
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
        return 'hm hm-sticky-video';
    }

    public function get_keywords() {
        return [ 'video', 'sticky', 'video-sticky' ];
    }

	/**
     * Register widget content controls
     */
    protected function register_content_controls() {
		$this->__video_content_controls();
		$this->__overlay_content_controls();
		$this->__settings_content_controls();
	}

    protected function __video_content_controls() {

		$this->start_controls_section(
			'_section_video',
			[
				'label' => __( 'Video', 'happy-addons-pro' ),
            ]
		);

        $this->add_control(
            'video_type',
            [
                'label'   => __( 'Video Type', 'happy-addons-pro' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => __( 'YouTube', 'happy-addons-pro' ),
                    'vimeo'   => __( 'Vimeo', 'happy-addons-pro' ),
                    'self_hosted'  => __( 'Self Hosted', 'happy-addons-pro' ),
                ],
            ]
        );

        $this->add_control(
            'youtube_link',
            [
                'label'       => __( 'Link', 'happy-addons-pro' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active'     => true
                ],
                'default'     => 'https://www.youtube.com/watch?v=QwJrFMtfO2U',
                'label_block' => true,
                'condition'   => [
                    'video_type' => 'youtube',
                ],
            ]
        );

        $this->add_control(
            'youtube_link_doc',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf( __( '<p style="word-break: break-word;"><b>Note:</b> Make sure you add the actual URL of the video and not the share URL.</br></br><b>Valid:</b>&nbsp;https://www.youtube.com/watch?v=HJRzUQMhJMQ</br><b>Invalid:</b>&nbsp;https://youtu.be/HJRzUQMhJMQ</p>', 'happy-addons-pro' ) ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'       => [
                    'video_type' => 'youtube',
                ],
                'separator'       => 'none',
            ]
        );

        $this->add_control(
            'vimeo_link',
            [
                'label'       => __( 'Link', 'happy-addons-pro' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active'     => true
                ],
                'default'     => 'https://vimeo.com/274860274',
                'label_block' => true,
                'condition'   => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->add_control(
            'vimeo_link_doc',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf( __( '<b>Note:</b> Make sure you add the actual URL of the video and not the categorized URL.</br></br><b>Valid:</b>&nbsp;https://vimeo.com/274860274</br><b>Invalid:</b>&nbsp;https://vimeo.com/channels/staffpicks/274860274', 'happy-addons-pro' ) ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'       => [
                    'video_type' => 'vimeo',
                ],
                'separator'       => 'none',
            ]
        );

        $this->add_control(
            'allow_remote_url',
            [
                'label' => __('Remote URL', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_block' => false,
                'condition' => [
                    'video_type' => 'self_hosted',
                ],
            ]
        );

        $this->add_control(
            'remote_url',
            [
                'label' => __('Link', 'happy-addons-pro'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('Enter your URL', 'happy-addons-pro'),
                'label_block' => true,
                'show_label' => false,
                'condition' => [
                    'video_type' => 'self_hosted',
                    'allow_remote_url' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'remote_url_doc',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf( __( '<b>Note:</b> Make sure that, the remote URL contain video extension at the end. e.g. .mp4, .mkv, .webm etc.', 'happy-addons-pro' ) ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => [
                    'video_type' => 'self_hosted',
                    'allow_remote_url' => 'yes',
                ],
                'separator'       => 'none',
            ]
        );

        $this->add_control(
            'hosted_url',
            [
                'label' => __('Choose File', 'happy-addons-pro'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true
                ],
                'media_type' => 'video',
                'condition' => [
                    'video_type' => 'self_hosted',
                    'allow_remote_url' => '',
                ],
            ]
        );

        $this->add_control(
            'heading_video_opt',
            [
                'label'     => __( 'Video Options', 'happy-addons-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'     => __( 'Autoplay', 'happy-addons-pro' ),
                'type'      => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'mute',
            [
                'label'     => __( 'Mute', 'happy-addons-pro' ),
                'type'      => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label'     => __( 'Loop', 'happy-addons-pro' ),
                'type'      => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'start',
            [
                'label'       => __( 'Start Time', 'happy-addons-pro' ),
                'type'        => Controls_Manager::NUMBER,
                'description' => __( 'Specify a start time (in seconds)', 'happy-addons-pro' ),
                'condition'   => [
                    'video_type' => [ 'youtube', 'self_hosted' ],
                ],
            ]
        );

        $this->add_control(
            'end',
            [
                'label'       => __( 'End Time', 'happy-addons-pro' ),
                'type'        => Controls_Manager::NUMBER,
                'description' => __( 'Specify an end time (in seconds)', 'happy-addons-pro' ),
                'condition'   => [
                    'video_type' => [ 'youtube', 'self_hosted' ],
                ],
            ]
        );

        $this->add_control(
            'aspect_ratio',
            [
                'label'        => __( 'Aspect Ratio', 'happy-addons-pro' ),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    '' => 'Null',
                    '16:9' => '16:9',
                    '4:3'  => '4:3',
                    '3:2'  => '3:2',
                    '9:16' => '9:16',
                ],
                'default'      => '',
            ]
        );

        $this->add_control(
            'control_bar',
            [
                'label' => __('Control Bar', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_block' => false,
				'default' => 'yes',
                'selectors_dictionary' => [
                    'yes' => 'display: flex!important;',
                    '' => 'display: none!important;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap .plyr__controls' => '{{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __overlay_content_controls() {

        $this->start_controls_section(
            'section_overlay',
            [
                'label' => __('Overlay', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'overlay_options',
            [
                'label' => __('Overlay', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_block' => false,
                'label_on' => __('Show', 'happy-addons-pro'),
                'label_off' => __('Hide', 'happy-addons-pro'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'overlay_image',
            [
                'label' => __('Choose Image', 'happy-addons-pro'),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'condition' => [
                    'overlay_options' => 'yes',
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        /* $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'default' => 'full',
                'name' => 'overlay_image_size',
                'exclude' => ['custom'],
                'condition' => [
                    'overlay_options' => 'yes',
                ],
            ]
        ); */

        $this->add_control(
            'overlay_play_icon',
            [
                'label' => __('Play Icon', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_block' => false,
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'overlay_options' => 'yes',
                    'overlay_image[url]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon',
            [
                'label' => esc_html__('Choose Icon', 'happy-addons-pro'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'hm hm-play-button',
                    'library' => 'happy-icons',
                ],
                'condition' => [
                    'overlay_options' => 'yes',
                    'overlay_image[url]!' => '',
                    'overlay_play_icon' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __settings_content_controls() {

        //Sticky Options
        $this->start_controls_section(
            'section_sticky',
            [
                'label' => __('Sticky Settings', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'sticky_on_off',
            [
                'label' => __('Sticky', 'happy-addons-pro'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => '',
                'render_type'      => 'template',
            ]
        );

        $this->add_control(
            'sticky_position',
            [
                'label' => __('Position', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'top-left' => __('Top Left', 'happy-addons-pro'),
                    'top-right' => __('Top Right', 'happy-addons-pro'),
                    'bottom-left' => __('Bottom Left', 'happy-addons-pro'),
                    'bottom-right' => __('Bottom Right', 'happy-addons-pro'),
                ],
                'default' => 'bottom-left',
                'selectors_dictionary' => [
                    'top-left' => 'top:20px; left:20px',
                    'top-right' => 'top:20px; right:20px',
                    'bottom-left' => 'bottom:20px; left:20px',
                    'bottom-right' => 'bottom:20px; right:20px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-box.sticky' => '{{VALUE}}',
                ],
                'style_transfer' => true,
                'condition' => [
                    'sticky_on_off' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

	/**
     * Register widget style controls
     */
    protected function register_style_controls() {
		$this->__sticky_box_style_controls();
		$this->__player_style_controls();
		$this->__overlay_style_controls();
	}

    protected function __sticky_box_style_controls() {

        $this->start_controls_section(
            'section_sticky_box_style',
            [
                'label' => __('Sticky Box', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'sticky_on_off' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sticky_box_width',
            [
                'label' => __('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 295,
                'max' => 1000,
                'step' => 1,
                'default' => 295,
                'condition' => [
                    'sticky_on_off' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-box.sticky' => 'width: {{VALUE}}px; height: auto;',
                ],
            ]
        );

        /* $this->add_control(
            'sticky_box_height',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::NUMBER,
                'min' => 55,
                'max' => 280,
                'step' => 1,
                'default' => 169,
                'condition' => [
                    'sticky_on_off' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-box.sticky' => 'height: {{VALUE}}px;',
                ],
            ]
        ); */

        $this->add_control(
            'sticky_box_close_button_color',
            [
                'label' => __('Close Button Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'sticky_on_off' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-box.sticky .ha-sticky-video-close' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __player_style_controls() {

        $this->start_controls_section(
            'section_sticky_video_player_style',
            [
                'label' => __('Player', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'sticky_video_player_width',
            [
                'label' => __('Width', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_video_player_height',
            [
                'label' => __('Height', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sticky_video_player_border_type',
            [
                'label' => __('Border Type', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'happy-addons-pro'),
                    'solid' => __('Solid', 'happy-addons-pro'),
                    'double' => __('Double', 'happy-addons-pro'),
                    'dotted' => __('Dotted', 'happy-addons-pro'),
                    'dashed' => __('Dashed', 'happy-addons-pro'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_video_player_border_width',
            [
                'label' => __('Border Width', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sticky_video_player_border_color',
            [
                'label' => __('Border Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_video_player_border_radius',
            [
                'label' => __('Border Radius', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_sticky_video_play_btn',
            [
                'label'     => __( 'Play Button', 'happy-addons-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'sticky_video_play_button_size',
            [
                'label' => __('Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .plyr__control--overlaid' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_sticky_video_player_interface',
            [
                'label'     => __( 'Interface Color', 'happy-addons-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'sticky_video_player_interface_color_one',
            [
                'label' => __('Color One', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' => '--plyr-video-control-color: {{VALUE}};
                                       --plyr-video-control-color-hover: {{VALUE}};
                                       --plyr-range-thumb-background: {{VALUE}}',
                    '{{WRAPPER}} button:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'sticky_video_player_interface_color_two',
            [
                'label' => __('Color Two', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' => '--plyr-color-main: {{VALUE}}',
                    '{{WRAPPER}} button:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'heading_sticky_video_bar',
            [
                'label'     => __( 'Bar', 'happy-addons-pro' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'control_bar' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_video_player_bar_padding',
            [
                'label' => __('Bar Padding', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'control_bar' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .plyr--video .plyr__controls' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_video_bar_margin',
            [
                'label' => __('Bar Margin', 'happy-addons-pro'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'condition' => [
                    'control_bar' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .plyr--video .plyr__controls' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
	}

    protected function __overlay_style_controls() {

        $this->start_controls_section(
            'section_sticky_video_player_overlay_style',
            [
                'label' => __('Overlay', 'happy-addons-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'overlay_options' => 'yes',
                    'overlay_play_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sticky_video_overlay_play_btn_color',
            [
                'label' => __('Play Button Color', 'happy-addons-pro'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-overlay-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ha-sticky-video-overlay-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_video_overlay_play_btn_size',
            [
                'label' => __('Play Button Size', 'happy-addons-pro'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-sticky-video-overlay-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ha-sticky-video-overlay-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $autoplay = 'yes' === $settings['autoplay'] ? true: false;
        $sticky = 'yes' === $settings['sticky_on_off'] ? true: false;
        $overlay = 'yes' === $settings['overlay_options'] ? true: false;
        $play_icon = 'yes' === $settings['overlay_play_icon'] ? true: false;
        $player_settings = [
            'autoplay' => $autoplay,
            'sticky' => $sticky,
            'overlay' => $overlay,
            'play_icon' => $play_icon,
        ];

        $player_settings = json_encode($player_settings);
        $sticky_class = 'yes' === $settings['sticky_on_off'] ? 'ha-sticky-video-sticky-on': 'ha-sticky-video-sticky-off';
        $this->add_render_attribute(
            'wrapper',
            [
                'class' => [ 'ha-sticky-video-wrap', $sticky_class ],
                'data-ha-player' => $player_settings,
            ]
        );

        $this->add_render_attribute (
            'video_box',
            [
                'class' => 'ha-sticky-video-box',
            ]
        );

        if ( 'yes' === $settings['overlay_options'] ) {
            $this->add_render_attribute (
                'overlay',
                [
                    'class' => 'ha-sticky-video-overlay',
                ]
            );
            $image = $settings['overlay_image']['url'];

            if( $image ) {
                $this->add_render_attribute (
                    'overlay',
                    [
                        'style' => "background-image:url('" . esc_url($image) . "');",
                    ]
                );
            }
        }

        ?>
        <div <?php $this->print_render_attribute_string('wrapper');?>>
            <?php if ( 'yes' === $settings['overlay_options'] && $settings['overlay_image']['url'] ): ?>
                <div <?php $this->print_render_attribute_string('overlay');?>>
                    <?php if ( 'yes' === $settings['overlay_play_icon'] && $settings['play_icon']['value'] ): ?>
                        <span class="ha-sticky-video-overlay-icon">
                            <?php Icons_Manager::render_icon( $settings['play_icon'], ['aria-hidden' => true ] )?>
                        </span>
                    <?php endif;?>
                </div>
            <?php endif;?>
            <div <?php $this->print_render_attribute_string('video_box');?>>
                <?php if ( 'yes' === $settings['sticky_on_off'] ) : ?>
                    <span class="ha-sticky-video-close"><i class="fas fa-times"></i></span>
                <?php endif;?>
                <?php echo $this->video_player($settings);?>
            </div>
        </div>
        <?php
    }

    protected function video_player($settings) {
        $id = $this->get_id_from_url($settings);
        $autoplay = 'yes' === $settings['autoplay'] ? true: false;
        $mute = 'yes' === $settings['mute'] ? true: false;
        $loop = 'yes' === $settings['loop'] ? true: false;
        $ratio = '' !== $settings['aspect_ratio'] ? $settings['aspect_ratio']: null;

        $player_settings = [
            'autoplay' => $autoplay,
            'muted' => $mute,
            'ratio' => $ratio,
            'loop' => ['active'=>$loop],
        ];

        if( 'youtube' === $settings['video_type'] && 'yes' !== $settings['loop'] && $settings['start'] && $settings['end'] ) {
            $player_settings['youtube'] = [
                'start' => $settings['start'],
                'end' => $settings['end'],
            ];
        }

        if( 'self_hosted' === $settings['video_type'] && $settings['start'] && $settings['end'] ){
            $id = $id . '#t=' . $settings['start'] .','. $settings['end'];
        }

        $player_settings = json_encode($player_settings);

        if ('youtube' === $settings['video_type'] || 'vimeo' === $settings['video_type']) {
            $this->add_render_attribute(
                'player',
                [
                    'id' => 'ha-sticky-video-player-'. $this->get_id(),
                    'data-plyr-provider' => $settings['video_type'],
                    'data-plyr-embed-id' => esc_attr($id),
                    'data-plyr-config' => $player_settings,
                ]
            );
            $markup = '<div '.$this->get_render_attribute_string('player').'></div>';
        }
        elseif ('self_hosted' === $settings['video_type']) {
            $this->add_render_attribute(
                'player',
                [
                    'id' => 'ha-sticky-video-player-'. $this->get_id(),
                    'playsinline' => '',
                    'controls' => '',
                    'data-plyr-config' => $player_settings,
                ]
            );

            $this->add_render_attribute(
                'source',
                [
                    'id' => 'ha-sticky-video-player-'. $this->get_id(),
                    'src' => esc_url($id),
                    'type' => 'video/mp4',
                ]
            );
            $markup = '<video '.$this->get_render_attribute_string('player').'>';
            $markup .= '<source '.$this->get_render_attribute_string('source').'/>';
            $markup .= '</video>';
        }
        return $markup;
    }


    protected function get_id_from_url($settings) {

        if ( '' !== $settings['youtube_link'] && 'youtube' === $settings['video_type'] ) {
            $url = $settings['youtube_link'];
            $link = explode('=', parse_url($url, PHP_URL_QUERY));
            $id = end($link);
        }
        elseif ( '' !== $settings['vimeo_link'] && 'vimeo' === $settings['video_type'] ) {
            $url = $settings['vimeo_link'];
            $link = explode('/', $url);
            $id = end($link);
        }
        elseif ('self_hosted' === $settings['video_type']) {
            $allow = $settings['allow_remote_url'];
            if ('yes' == $allow) {
                $id = $settings['remote_url']['url'];
            } else {
                $id = $settings['hosted_url']['url'];
            }
		}

        return $id;
    }
}
