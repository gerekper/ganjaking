<?php
/**
 * UAEL Video.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Video\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Video.
 */
class Video extends Common_Widget {

	/**
	 * Retrieve Video Widget name.
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Video' );
	}

	/**
	 * Retrieve Video Widget title.
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Video' );
	}

	/**
	 * Retrieve Video Widget icon.
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Video' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Video' );
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'elementor-waypoints', 'uael-frontend-script', 'uael-video-subscribe', 'jquery-ui-draggable' );
	}

	/**
	 * Register Video controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_video_content();
		$this->register_overlay_content();
		$this->register_video_icon_style();
		$this->register_video_lightbox();
		$this->register_video_sticky();
		$this->register_video_subscribe_bar();
		$this->register_schema_controls();
		$this->register_helpful_information();
	}

	/**
	 * Video Tab.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function register_video_content() {

		$this->start_controls_section(
			'section_video',
			array(
				'label' => __( 'Video', 'uael' ),
			)
		);

			$this->add_control(
				'video_type',
				array(
					'label'   => __( 'Video Type', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'youtube',
					'options' => array(
						'youtube' => __( 'YouTube', 'uael' ),
						'vimeo'   => __( 'Vimeo', 'uael' ),
						'wistia'  => __( 'Wistia', 'uael' ),
						'hosted'  => __( 'Self Hosted', 'uael' ),
					),
				)
			);

			$this->add_control(
				'insert_link',
				array(
					'label'     => __( 'External URL', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
					),
				)
			);

			$this->add_control(
				'hosted_link',
				array(
					'label'      => __( 'Choose File', 'uael' ),
					'type'       => Controls_Manager::MEDIA,
					'dynamic'    => array(
						'active'     => true,
						'categories' => array(
							TagsModule::MEDIA_CATEGORY,
						),
					),
					'media_type' => 'video',
					'condition'  => array(
						'video_type'  => 'hosted',
						'insert_link' => '',
					),
				)
			);

			$this->add_control(
				'external_link',
				array(
					'label'        => __( 'URL', 'uael' ),
					'type'         => Controls_Manager::URL,
					'autocomplete' => false,
					'options'      => false,
					'label_block'  => true,
					'show_label'   => false,
					'dynamic'      => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'media_type'   => 'video',
					'placeholder'  => __( 'Enter your URL', 'uael' ),
					'condition'    => array(
						'video_type'  => 'hosted',
						'insert_link' => 'yes',
					),
				)
			);

			$default_youtube = apply_filters( 'uael_video_default_youtube_link', 'https://www.youtube.com/watch?v=HJRzUQMhJMQ' );

			$default_vimeo = apply_filters( 'uael_video_default_vimeo_link', 'https://vimeo.com/274860274' );

			$default_wistia = apply_filters( 'uael_video_default_wistia_link', '<p><a href="https://pratikc.wistia.com/medias/gyvkfithw2?wvideo=gyvkfithw2"><img src="https://embed-ssl.wistia.com/deliveries/53eec5fa72737e60aa36731b57b607a7c0636f52.webp?image_play_button_size=2x&amp;image_crop_resized=960x540&amp;image_play_button=1&amp;image_play_button_color=54bbffe0" width="400" height="225" style="width: 400px; height: 225px;"></a></p><p><a href="https://pratikc.wistia.com/medias/gyvkfithw2?wvideo=gyvkfithw2">Video Placeholder - Brainstorm Force - pratikc</a></p>' );

			$this->add_control(
				'youtube_link',
				array(
					'label'       => __( 'Link', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'default'     => $default_youtube,
					'label_block' => true,
					'condition'   => array(
						'video_type' => 'youtube',
					),
				)
			);
			$this->add_control(
				'youtube_link_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '<b>Note:</b> Make sure you add the actual URL of the video and not the share URL.</br></br><b>Valid:</b>&nbsp;https://www.youtube.com/watch?v=HJRzUQMhJMQ</br><b>Invalid:</b>&nbsp;https://youtu.be/HJRzUQMhJMQ', 'uael' ) ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'video_type' => 'youtube',
					),
					'separator'       => 'none',
				)
			);

			$this->add_control(
				'vimeo_link',
				array(
					'label'       => __( 'Link', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'default'     => $default_vimeo,
					'label_block' => true,
					'condition'   => array(
						'video_type' => 'vimeo',
					),
				)
			);
			$this->add_control(
				'vimeo_link_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '<b>Note:</b> Make sure you add the actual URL of the video and not the categorized URL.</br></br><b>Valid:</b>&nbsp;https://vimeo.com/274860274</br><b>Invalid:</b>&nbsp;https://vimeo.com/channels/staffpicks/274860274', 'uael' ) ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'video_type' => 'vimeo',
					),
					'separator'       => 'none',
				)
			);

			$this->add_control(
				'wistia_link',
				array(
					'label'       => __( 'Link & Thumbnail Text', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'default'     => $default_wistia,
					'label_block' => true,
					'condition'   => array(
						'video_type' => 'wistia',
					),
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'wistia_link_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Go to your Wistia video, right click, "Copy Link & Thumbnail" and paste here. %1$s Learn more %2$s.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/video-widget/#how-to-get-a-valid-link-for-wistia-video-" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'video_type' => 'wistia',
					),
				)
			);
		} else {
			$this->add_control(
				'wistia_link_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => __( 'Go to your Wistia video, right click, "Copy Link & Thumbnail" and paste here.', 'uael' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'video_type' => 'wistia',
					),
				)
			);
		}

			$this->add_control(
				'start',
				array(
					'label'       => __( 'Start Time', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'dynamic'     => array(
						'active' => true,
					),
					'description' => __( 'Specify a start time (in seconds)', 'uael' ),
					'condition'   => array(
						'video_type' => array( 'youtube', 'vimeo', 'hosted' ),
					),
				)
			);

			$this->add_control(
				'end',
				array(
					'label'       => __( 'End Time', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'dynamic'     => array(
						'active' => true,
					),
					'description' => __( 'Specify an end time (in seconds)', 'uael' ),
					'condition'   => array(
						'video_type' => array( 'youtube', 'hosted' ),
					),
				)
			);

			$this->add_control(
				'aspect_ratio',
				array(
					'label'        => __( 'Aspect Ratio', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'16_9' => '16:9',
						'4_3'  => '4:3',
						'3_2'  => '3:2',
						'9_16' => '9:16',
						'1_1'  => '1:1',
						'21_9' => '21:9',
					),
					'default'      => '16_9',
					'prefix_class' => 'uael-aspect-ratio-',
				)
			);

			$this->add_control(
				'heading_youtube',
				array(
					'label'     => __( 'Video Options', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			// Lightbox.
			$this->add_control(
				'lightbox',
				array(
					'label' => __( 'Lightbox', 'uael' ),
					'type'  => Controls_Manager::SWITCHER,
				)
			);

			// YouTube.
			$this->add_control(
				'yt_autoplay',
				array(
					'label'     => __( 'Autoplay', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'yt_rel',
				array(
					'label'     => __( 'Related Videos From', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'no',
					'options'   => array(
						'no'  => __( 'Current Video Channel', 'uael' ),
						'yes' => __( 'Any Random Video', 'uael' ),
					),
					'condition' => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'yt_controls',
				array(
					'label'     => __( 'Player Control', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'yt_mute',
				array(
					'label'     => __( 'Mute', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'yt_modestbranding',
				array(
					'label'       => __( 'Modest Branding', 'uael' ),
					'description' => __( 'This option lets you use a YouTube player that does not show a YouTube logo.', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'condition'   => array(
						'video_type'  => 'youtube',
						'yt_controls' => 'yes',
					),
				)
			);

			$this->add_control(
				'yt_privacy',
				array(
					'label'       => __( 'Privacy Mode', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'uael' ),
					'condition'   => array(
						'video_type' => 'youtube',
					),
				)
			);

			// Vimeo.
			$this->add_control(
				'vimeo_autoplay',
				array(
					'label'     => __( 'Autoplay', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_loop',
				array(
					'label'     => __( 'Loop', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_muted',
				array(
					'label'     => __( 'Mute', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_title',
				array(
					'label'     => __( 'Intro Title', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_portrait',
				array(
					'label'     => __( 'Intro Portrait', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_byline',
				array(
					'label'     => __( 'Intro Byline', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_color',
				array(
					'label'     => __( 'Controls Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-vimeo-title a'  => 'color: {{VALUE}}',
						'{{WRAPPER}} .uael-vimeo-byline a' => 'color: {{VALUE}}',
						'{{WRAPPER}} .uael-vimeo-title a:hover' => 'color: {{VALUE}}',
						'{{WRAPPER}} .uael-vimeo-byline a:hover' => 'color: {{VALUE}}',
						'{{WRAPPER}} .uael-vimeo-title a:focus' => 'color: {{VALUE}}',
						'{{WRAPPER}} .uael-vimeo-byline a:focus' => 'color: {{VALUE}}',
					),
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			// Wistia.
			$this->add_control(
				'wistia_autoplay',
				array(
					'label'     => __( 'Autoplay', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
				)
			);

			$this->add_control(
				'wistia_loop',
				array(
					'label'     => __( 'Loop', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
				)
			);

			$this->add_control(
				'wistia_muted',
				array(
					'label'     => __( 'Mute', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
				)
			);

			$this->add_control(
				'wistia_playbar',
				array(
					'label'     => __( 'Show Playbar', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
					'default'   => 'yes',
				)
			);

			// Hosted.
			$this->add_control(
				'autoplay',
				array(
					'label'     => __( 'Autoplay', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
					),
				)
			);

			$this->add_control(
				'loop',
				array(
					'label'     => __( 'Loop', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
					),
				)
			);

			$this->add_control(
				'controls',
				array(
					'label'     => __( 'Player Control', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'uael' ),
					'label_on'  => __( 'Show', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'hosted',
					),
				)
			);

			$this->add_control(
				'muted',
				array(
					'label'     => __( 'Mute', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
					),
				)
			);

			$this->add_control(
				'mute_notice',
				array(
					'raw'             => __( 'Note: Mute functionality will not work inside the lightbox.', 'uael' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
					'condition'       => array(
						'lightbox' => 'yes',
					),
				)
			);

			$this->add_control(
				'video_double_click',
				array(
					'label'        => __( 'Enable Double Click on Mobile', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'default'      => 'no',
					'return_value' => 'yes',
				)
			);

		if ( parent::is_internal_links() ) {

			$this->add_control(
				'video_double_click_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Enable this option if you are not able to see custom thumbnail or overlay color on Mobile. Please read %1$s this article %2$s for more information.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/double-click-on-mobile-for-video/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Overlay Tab.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function register_overlay_content() {

		$this->start_controls_section(
			'section_image_overlay',
			array(
				'label' => __( 'Thumbnail & Overlay', 'uael' ),
			)
		);

			$this->add_control(
				'yt_thumbnail_size',
				array(
					'label'     => __( 'Thumbnail Size', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'maxresdefault' => __( 'Maximum Resolution', 'uael' ),
						'hqdefault'     => __( 'High Quality', 'uael' ),
						'mqdefault'     => __( 'Medium Quality', 'uael' ),
						'sddefault'     => __( 'Standard Quality', 'uael' ),
					),
					'default'   => 'maxresdefault',
					'condition' => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'show_image_overlay',
				array(
					'label'        => __( 'Custom Thumbnail', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'default'      => 'no',
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'image_overlay',
				array(
					'label'     => __( 'Select Image', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_image_overlay' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'image_overlay', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_overlay_size` and `image_overlay_custom_dimension` phpcs:ignore Squiz.PHP.CommentedOutCode.Found.
					'default'   => 'full',
					'separator' => 'none',
					'condition' => array(
						'show_image_overlay' => 'yes',
					),
				)
			);

			$this->add_control(
				'image_overlay_color',
				array(
					'label'     => __( 'Overlay Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-video__outer-wrap:before' => 'background: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'video_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-video__outer-wrap:before,
						{{WRAPPER}} .uael-video__outer-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-youtube-subscribe-yes .uael-subscribe-bar' => 'border-radius: 0{{UNIT}} 0{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-youtube-subscribe-yes .uael-video__outer-wrap:before,
						{{WRAPPER}}.uael-youtube-subscribe-yes .uael-video__outer-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0{{UNIT}} 0{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Style Tab.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function register_video_icon_style() {

		$this->start_controls_section(
			'section_play_icon',
			array(
				'label' => __( 'Play Button', 'uael' ),
			)
		);

			$this->add_control(
				'play_source',
				array(
					'label'   => __( 'Image/Icon', 'uael' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'default' => array(
							'title' => __( 'Default', 'uael' ),
							'icon'  => 'fa fa-youtube-play',
						),
						'img'     => array(
							'title' => __( 'Image', 'uael' ),
							'icon'  => 'fa fa-picture-o',
						),
						'icon'    => array(
							'title' => __( 'Icon', 'uael' ),
							'icon'  => 'fa fa-info-circle',
						),
					),
					'default' => 'icon',
				)
			);

			$this->add_control(
				'play_img',
				array(
					'label'     => __( 'Select Image', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'condition' => array(
						'play_source' => 'img',
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_play_icon',
				array(
					'label'            => __( 'Select Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'play_icon',
					'default'          => array(
						'value'   => 'fa fa-play-circle',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'play_source' => 'icon',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$this->add_control(
				'play_icon',
				array(
					'label'     => __( 'Select Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'default'   => 'fa fa-play-circle',
					'condition' => array(
						'play_source' => 'icon',
					),
				)
			);
		}

			$this->add_responsive_control(
				'play_icon_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 10,
							'max' => 700,
						),
					),
					'default'   => array(
						'size' => 72,
						'unit' => 'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-video__play-icon i, {{WRAPPER}} .uael-video__play-icon svg' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-video__play-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-video__play-icon > img' => 'width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-video__play-icon.uael-video__vimeo-play' => 'width: auto; height: auto;',
						'{{WRAPPER}} .uael-vimeo-icon-bg' => 'width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-video-wistia-play' => 'height: {{SIZE}}{{UNIT}}; width: calc( {{SIZE}}{{UNIT}} * 1.45 );',
					),
				)
			);

			$this->add_control(
				'hover_animation_img',
				array(
					'label'     => __( 'Hover Animation', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => array(
						''                => __( 'None', 'uael' ),
						'grow'            => __( 'Grow', 'uael' ),
						'float'           => __( 'Float', 'uael' ),
						'sink'            => __( 'Sink', 'uael' ),
						'wobble-vertical' => __( 'Wobble Vertical', 'uael' ),
					),
					'condition' => array(
						'play_source' => 'img',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_style' );

				$this->start_controls_tab(
					'tab_normal',
					array(
						'label'      => __( 'Normal', 'uael' ),
						'conditions' => array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
									'operator' => '!=',
									'value'    => '',
								),
								array(
									'name'     => 'play_source',
									'operator' => '==',
									'value'    => 'icon',
								),
							),
						),
					)
				);

					$this->add_control(
						'play_icon_color',
						array(
							'label'      => __( 'Color', 'uael' ),
							'type'       => Controls_Manager::COLOR,
							'selectors'  => array(
								'{{WRAPPER}} .uael-video__play-icon i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .uael-video__play-icon svg' => 'fill: {{VALUE}}',
							),
							'conditions' => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'play_source',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
						)
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						array(
							'name'       => 'play_icon_text_shadow',
							'selector'   => '{{WRAPPER}} .uael-video__play-icon i, {{WRAPPER}} .uael-video__play-icon svg',
							'conditions' => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'play_source',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_hover',
					array(
						'label'      => __( 'Hover', 'uael' ),
						'conditions' => array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
									'operator' => '!=',
									'value'    => '',
								),
								array(
									'name'     => 'play_source',
									'operator' => '==',
									'value'    => 'icon',
								),
							),
						),
					)
				);

					$this->add_control(
						'play_icon_hover_color',
						array(
							'label'      => __( 'Color', 'uael' ),
							'type'       => Controls_Manager::COLOR,
							'selectors'  => array(
								'{{WRAPPER}} .uael-video__outer-wrap:hover .uael-video__play-icon i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .uael-video__outer-wrap:hover .uael-video__play-icon svg' => 'fill: {{VALUE}}',
							),
							'conditions' => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'play_source',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
						)
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						array(
							'name'       => 'play_icon_hover_text_shadow',
							'selector'   => '{{WRAPPER}} .uael-video__outer-wrap:hover .uael-video__play-icon i, {{WRAPPER}} .uael-video__outer-wrap:hover .uael-video__play-icon svg',
							'conditions' => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'play_source',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
						)
					);

					$this->add_control(
						'hover_animation',
						array(
							'label'      => __( 'Hover Animation', 'uael' ),
							'type'       => Controls_Manager::SELECT,
							'default'    => '',
							'options'    => array(
								''                => __( 'None', 'uael' ),
								'grow'            => __( 'Grow', 'uael' ),
								'float'           => __( 'Float', 'uael' ),
								'sink'            => __( 'Sink', 'uael' ),
								'wobble-vertical' => __( 'Wobble Vertical', 'uael' ),
							),
							'conditions' => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'play_icon' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'play_source',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'default_play_icon_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-youtube-icon-bg, {{WRAPPER}} .uael-vimeo-icon-bg' => 'fill: {{VALUE}}',
						'{{WRAPPER}} .uael-video-wistia-play' => 'background-color: {{VALUE}}',
					),
					'condition' => array(
						'play_source' => 'default',
					),
				)
			);

			$this->add_control(
				'default_play_icon_hover_color',
				array(
					'label'     => __( 'Hover Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-video__outer-wrap:hover .uael-video__play-icon .uael-youtube-icon-bg, {{WRAPPER}} .uael-video__outer-wrap:hover .uael-video__play-icon .uael-vimeo-icon-bg' => 'fill: {{VALUE}}',
						'{{WRAPPER}} .uael-video__outer-wrap:hover .uael-video-wistia-play' => 'background-color: {{VALUE}}',
					),
					'condition' => array(
						'play_source' => 'default',
					),
				)
			);

			$this->add_control(
				'default_hover_animation',
				array(
					'label'     => __( 'Hover Animation', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => array(
						''                => __( 'None', 'uael' ),
						'grow'            => __( 'Grow', 'uael' ),
						'float'           => __( 'Float', 'uael' ),
						'sink'            => __( 'Sink', 'uael' ),
						'wobble-vertical' => __( 'Wobble Vertical', 'uael' ),
					),
					'condition' => array(
						'play_source' => 'default',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Lightbox style.
	 *
	 * @since 1.21.0
	 * @access protected
	 */
	protected function register_video_lightbox() {

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label'     => __( 'Lightbox', 'uael' ),
				'condition' => array(
					'lightbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_icon_color',
			array(
				'label'     => __( 'Close Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'lightbox_icon_color_hover',
			array(
				'label'     => __( 'Close Icon Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'lightbox_video_width',
			array(
				'label'     => __( 'Content Width', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => '%',
				),
				'range'     => array(
					'%' => array(
						'min' => 30,
					),
				),
				'selectors' => array(
					'(desktop+)#elementor-lightbox-{{ID}} .elementor-video-container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'lightbox_content_position',
			array(
				'label'                => __( 'Content Position', 'uael' ),
				'type'                 => Controls_Manager::SELECT,
				'frontend_available'   => true,
				'options'              => array(
					''    => __( 'Center', 'uael' ),
					'top' => __( 'Top', 'uael' ),
				),
				'selectors'            => array(
					'#elementor-lightbox-{{ID}} .elementor-video-container' => '{{VALUE}}; transform: translateX(-50%);',
				),
				'selectors_dictionary' => array(
					'top' => 'top: 60px',
				),
			)
		);

		$this->add_responsive_control(
			'lightbox_content_animation',
			array(
				'label'              => __( 'Entrance Animation', 'uael' ),
				'description'        => __( 'Note: Entrance animation will work only at the frontend.', 'uael' ),
				'type'               => Controls_Manager::ANIMATION,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Sticky feature for Video
	 *
	 * @since 1.9.2
	 * @access protected
	 */
	protected function register_video_sticky() {
		$this->start_controls_section(
			'section_sticky',
			array(
				'label' => __( 'Sticky Video', 'uael' ),
			)
		);

			$this->add_control(
				'enable_sticky',
				array(
					'label'     => __( 'Enable Sticky Video', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'No', 'uael' ),
					'label_on'  => __( 'Yes', 'uael' ),
					'default'   => 'no',
				)
			);

			$this->add_responsive_control(
				'sticky_video_width',
				array(
					'label'          => __( 'Video Width (px)', 'uael' ),
					'type'           => Controls_Manager::SLIDER,
					'range'          => array(
						'px' => array(
							'min' => 100,
							'max' => 1000,
						),
					),
					'default'        => array(
						'size' => 320,
						'unit' => 'px',
					),
					'mobile_default' => array(
						'size' => 250,
						'unit' => 'px',
					),
					'condition'      => array(
						'enable_sticky' => 'yes',
					),
					'selectors'      => array(
						'{{WRAPPER}}.uael-aspect-ratio-16_9 .uael-video__outer-wrap.uael-sticky-apply .uael-video-inner-wrap,
						{{WRAPPER}}.uael-aspect-ratio-16_9 .uael-sticky-apply .uael-video__thumb' => 'width: {{SIZE}}px; height: calc( {{SIZE}}px * 0.5625 );',
						'{{WRAPPER}}.uael-aspect-ratio-4_3 .uael-video__outer-wrap.uael-sticky-apply .uael-video-inner-wrap,
						{{WRAPPER}}.uael-aspect-ratio-4_3 .uael-sticky-apply .uael-video__thumb' => 'width: {{SIZE}}px; height: calc( {{SIZE}}px * 0.75 );',
						'{{WRAPPER}}.uael-aspect-ratio-3_2 .uael-video__outer-wrap.uael-sticky-apply .uael-video-inner-wrap,
						{{WRAPPER}}.uael-aspect-ratio-3_2 .uael-sticky-apply .uael-video__thumb' => 'width: {{SIZE}}px; height: calc( {{SIZE}}px * 0.6666666666666667 );',
						'{{WRAPPER}}.uael-aspect-ratio-9_16 .uael-video__outer-wrap.uael-sticky-apply .uael-video-inner-wrap,
						{{WRAPPER}}.uael-aspect-ratio-9_16 .uael-sticky-apply .uael-video__thumb' => 'width: {{SIZE}}px; height: calc( {{SIZE}}px * 0.1778 );',
					),
				)
			);

			$this->add_control(
				'align_sticky',
				array(
					'label'        => __( 'Sticky Alignment', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'top_left',
					'options'      => array(
						'top_left'     => __( 'Top Left', 'uael' ),
						'top_right'    => __( 'Top Right', 'uael' ),
						'bottom_left'  => __( 'Bottom Left', 'uael' ),
						'bottom_right' => __( 'Bottom Right', 'uael' ),
						'center_left'  => __( 'Center Left', 'uael' ),
						'center_right' => __( 'Center Right', 'uael' ),
					),
					'condition'    => array(
						'enable_sticky' => 'yes',
					),
					'prefix_class' => 'uael-video-sticky-',
					'render_type'  => 'template',
				)
			);

			$this->add_responsive_control(
				'sticky_video_margin',
				array(
					'label'              => __( 'Spacing from Edges', 'uael' ),
					'description'        => __( 'Note: This is spacing around the sticky video with respect to the Alignment chosen.', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px' ),
					'selectors'          => array(
						'{{WRAPPER}}.uael-video-sticky-top_right .uael-sticky-apply .uael-video-inner-wrap' => 'top: {{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}};',
						'{{WRAPPER}}.uael-video-sticky-top_left .uael-sticky-apply .uael-video-inner-wrap' => 'top: {{TOP}}{{UNIT}}; left: {{LEFT}}{{UNIT}};',
						'.admin-bar {{WRAPPER}}.uael-video-sticky-top_left .uael-sticky-apply .uael-video-inner-wrap,
						.admin-bar {{WRAPPER}}.uael-video-sticky-top_right .uael-sticky-apply .uael-video-inner-wrap' => 'top: calc( {{TOP}}px + 32px );',
						'{{WRAPPER}}.uael-video-sticky-bottom_right .uael-sticky-apply .uael-video-inner-wrap' => 'bottom: {{BOTTOM}}{{UNIT}}; right: {{RIGHT}}{{UNIT}};',
						'{{WRAPPER}}.uael-video-sticky-bottom_left .uael-sticky-apply .uael-video-inner-wrap' => 'bottom: {{BOTTOM}}{{UNIT}}; left: {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-video-sticky-center_left .uael-sticky-apply .uael-video-inner-wrap' => 'left: {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-video-sticky-center_right .uael-sticky-apply .uael-video-inner-wrap' => 'right: {{RIGHT}}{{UNIT}};',
					),
					'condition'          => array(
						'enable_sticky' => 'yes',
					),
					'render_type'        => 'template',
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'sticky_video_padding',
				array(
					'label'      => __( 'Background Size', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-sticky-apply iframe, {{WRAPPER}} .uael-sticky-apply .uael-video__thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'enable_sticky' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_video_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .uael-video__outer-wrap.uael-sticky-apply .uael-video-inner-wrap' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'enable_sticky' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_hide_on',
				array(
					'label'              => __( 'Hide Sticky Video on', 'uael' ),
					'type'               => Controls_Manager::SELECT2,
					'multiple'           => true,
					'label_block'        => true,
					'options'            => array(
						'desktop' => __( 'Desktop', 'uael' ),
						'tablet'  => __( 'Tablet', 'uael' ),
						'mobile'  => __( 'Mobile', 'uael' ),
					),
					'condition'          => array(
						'enable_sticky' => 'yes',
					),
					'render_type'        => 'template',
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'heading_sticky_close_button',
				array(
					'label'     => __( 'Close Button', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'enable_sticky' => 'yes',
					),
				)
			);

			$this->add_control(
				'enable_sticky_close_button',
				array(
					'label'     => __( 'Enable', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'No', 'uael' ),
					'label_on'  => __( 'Yes', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'enable_sticky' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_close_icon_color',
				array(
					'label'     => __( 'Icon Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-sticky-apply .uael-video-sticky-close' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'enable_sticky'              => 'yes',
						'enable_sticky_close_button' => 'yes',
					),
				)
			);
			$this->add_control(
				'sticky_close_icon_bgcolor',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-sticky-apply .uael-video-sticky-close' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'enable_sticky'              => 'yes',
						'enable_sticky_close_button' => 'yes',
					),
				)
			);

			$this->add_control(
				'heading_sticky_info_bar',
				array(
					'label'     => __( 'Info Bar', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'enable_sticky' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_info_bar_switch',
				array(
					'label'       => __( 'Enable', 'uael' ),
					'description' => __( 'Enable this option to display the informative text under Sticky video.', 'uael' ),
					'type'        => Controls_Manager::SWITCHER,
					'label_off'   => __( 'No', 'uael' ),
					'label_on'    => __( 'Yes', 'uael' ),
					'default'     => 'no',
					'condition'   => array(
						'enable_sticky' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_info_bar_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXTAREA,
					'default'   => '<b>Now Playing:</b> Sticky Video',
					'rows'      => 2,
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'enable_sticky'          => 'yes',
						'sticky_info_bar_switch' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_info_bar_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-sticky-apply .uael-video-sticky-infobar' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'enable_sticky'          => 'yes',
						'sticky_info_bar_switch' => 'yes',
					),
				)
			);

			$this->add_control(
				'sticky_info_bar_bgcolor',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .uael-sticky-apply .uael-video-sticky-infobar' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'enable_sticky'          => 'yes',
						'sticky_info_bar_switch' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'sticky_info_bar_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'  => '{{WRAPPER}} .uael-sticky-apply .uael-video-sticky-infobar',
					'condition' => array(
						'enable_sticky'          => 'yes',
						'sticky_info_bar_switch' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'sticky_info_bar_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-sticky-apply .uael-video-sticky-infobar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'enable_sticky'          => 'yes',
						'sticky_info_bar_switch' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Subscribe bar below Video.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function register_video_subscribe_bar() {

		$this->start_controls_section(
			'section_subscribe_bar',
			array(
				'label'     => __( 'YouTube Subscribe Bar', 'uael' ),
				'condition' => array(
					'video_type' => 'youtube',
				),
			)
		);

			$this->add_control(
				'subscribe_bar',
				array(
					'label'        => __( 'Enable Subscribe Bar', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'default'      => 'no',
					'condition'    => array(
						'video_type' => 'youtube',
					),
					'prefix_class' => 'uael-youtube-subscribe-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'subscribe_bar_select',
				array(
					'label'     => __( 'Select', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'channel_name' => __( 'Use Channel Name', 'uael' ),
						'channel_id'   => __( 'Use Channel ID', 'uael' ),
					),
					'default'   => 'channel_id',
					'condition' => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_control(
				'subscribe_bar_channel_name',
				array(
					'label'       => __( 'YouTube Channel Name', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'TheBrainstormForce',
					'label_block' => true,
					'condition'   => array(
						'video_type'           => 'youtube',
						'subscribe_bar'        => 'yes',
						'subscribe_bar_select' => 'channel_name',
					),
				)
			);

			$this->add_control(
				'subscribe_bar_channel_id',
				array(
					'label'       => __( 'YouTube Channel ID', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'UCtFCcrvupjyaq2lax_7OQQg',
					'label_block' => true,
					'condition'   => array(
						'video_type'           => 'youtube',
						'subscribe_bar'        => 'yes',
						'subscribe_bar_select' => 'channel_id',
					),
				)
			);

		if ( parent::is_internal_links() ) {

			$this->add_control(
				'subscribe_channel_id_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click %1$s here %2$s to find your YouTube Channel Name.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/youtube-channel-name-and-channel-id/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'video_type'           => 'youtube',
						'subscribe_bar'        => 'yes',
						'subscribe_bar_select' => 'channel_name',
					),
				)
			);

			$this->add_control(
				'subscribe_channel_name_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click %1$s here %2$s to find your YouTube Channel ID.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/youtube-channel-name-and-channel-id/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'video_type'           => 'youtube',
						'subscribe_bar'        => 'yes',
						'subscribe_bar_select' => 'channel_id',
					),
				)
			);
		}

			$this->add_control(
				'subscribe_bar_channel_text',
				array(
					'label'       => __( 'Subscribe to Channel Text', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'Subscribe to our YouTube Channel',
					'label_block' => true,
					'condition'   => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_control(
				'subscribe_count',
				array(
					'label'     => __( 'Show Subscribers Count', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'No', 'uael' ),
					'label_on'  => __( 'Yes', 'uael' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_control(
				'subscribe_bar_color',
				array(
					'label'     => __( 'Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'{{WRAPPER}} .uael-subscribe-bar-prefix' => 'color: {{VALUE}}',
					),
					'condition' => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_control(
				'subscribe_bar_bgcolor',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#1b1b1b',
					'selectors' => array(
						'{{WRAPPER}} .uael-subscribe-bar' => 'background-color: {{VALUE}}',
					),
					'condition' => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'subscribe_bar_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'  => '{{WRAPPER}} .uael-subscribe-bar-prefix',
					'condition' => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'subscribe_bar_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-subscribe-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

			$this->add_control(
				'subscribe_bar_responsive',
				array(
					'label'        => __( 'Stack on', 'uael' ),
					'description'  => __( 'Choose a breakpoint where the subscribe bar content will stack.', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'none',
					'options'      => array(
						'none'    => __( 'None', 'uael' ),
						'desktop' => __( 'Desktop', 'uael' ),
						'tablet'  => __( 'Tablet', 'uael' ),
						'mobile'  => __( 'Mobile', 'uael' ),
					),
					'condition'    => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
					'prefix_class' => 'uael-subscribe-responsive-',
					'separator'    => 'before',
				)
			);

			$this->add_responsive_control(
				'subscribe_bar_spacing',
				array(
					'label'      => __( 'Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-subscribe-bar-prefix ' => 'margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.uael-subscribe-responsive-desktop .uael-subscribe-bar-prefix ' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: 0px;',
						'(tablet){{WRAPPER}}.uael-subscribe-responsive-tablet .uael-subscribe-bar-prefix ' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: 0px;',
						'(mobile){{WRAPPER}}.uael-subscribe-responsive-mobile .uael-subscribe-bar-prefix ' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: 0px;',
					),
					'condition'  => array(
						'video_type'    => 'youtube',
						'subscribe_bar' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Schema related controls.
	 *
	 * @since 1.33.1
	 * @access protected
	 */
	protected function register_schema_controls() {
		$this->start_controls_section(
			'section_schema',
			array(
				'label' => __( 'Video Schema', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'schema_support',
			array(
				'label'     => __( 'Schema Support', 'uael' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'uael' ),
				'label_off' => __( 'No', 'uael' ),
				'default'   => 'no',
			)
		);

		$this->add_control(
			'schema_title',
			array(
				'label'       => __( 'Video Title', 'uael' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Title of the video.', 'uael' ),
				'condition'   => array(
					'schema_support' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_description',
			array(
				'label'     => __( 'Video Description', 'uael' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 10,
				'default'   => __( 'Description of the video.', 'uael' ),
				'condition' => array(
					'schema_support' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_thumbnail',
			array(
				'label'     => __( 'Video Thumbnail', 'uael' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'schema_support'      => 'yes',
					'show_image_overlay!' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_upload_date',
			array(
				'label'       => __( 'Video Upload Date & Time', 'uael' ),
				'type'        => Controls_Manager::DATE_TIME,
				'placeholder' => __( 'yyyy-mm-dd', 'uael' ),
				'default'     => gmdate( 'Y-m-d H:i' ),
				'condition'   => array(
					'schema_support' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function register_helpful_information() {

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/video-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=2RlvBU_EFV4&index=18&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Unable to edit Video widget » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/unable-to-edit-video-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_4',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Display YouTube Subscribe Bar for Video. » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/youtube-subscribe-bar-for-video/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_5',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Find YouTube Channel Name and Channel ID. » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/youtube-channel-name-and-channel-id/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_6',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Enable Sticky Video » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/sticky-video/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_7',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Get Wistia Link and Thumbnail text » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/video-widget/#how-to-get-a-valid-link-for-wistia-video-" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Returns Video Thumbnail Image.
	 *
	 * @param string $id Video ID.
	 * @since 1.3.2
	 * @access protected
	 */
	protected function get_video_thumb( $id ) {

		if ( '' === $id ) {
			return '';
		}

		$settings = $this->get_settings_for_display();
		$thumb    = '';

		if ( 'yes' === $settings['show_image_overlay'] ) {

			$thumb = Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings );

		} else {

			if ( 'youtube' === $settings['video_type'] ) {

				$thumb = 'https://i.ytimg.com/vi/' . $id . '/' . apply_filters( 'uael_video_youtube_image_quality', $settings['yt_thumbnail_size'] ) . '.jpg';

			} elseif ( 'vimeo' === $settings['video_type'] ) {

				$response = wp_remote_get( "https://vimeo.com/api/v2/video/$id.php" );

				if ( is_wp_error( $response ) || 404 === $response['response']['code'] ) {
					return;
				}
				$vimeo = maybe_unserialize( $response['body'] );

				// privacy enabled videos don't return thumbnail data.
				$thumb = ( isset( $vimeo[0]['thumbnail_large'] ) && ! empty( $vimeo[0]['thumbnail_large'] ) ) ? str_replace( '_640', '_840', $vimeo[0]['thumbnail_large'] ) : '';

			} elseif ( 'wistia' === $settings['video_type'] ) {
				$url   = $settings['wistia_link'];
				$thumb = 'https://embed-ssl.wistia.com/deliveries/' . $this->getStringBetween( $url, 'deliveries/', '?' );
			}
		}
		return $thumb;
	}

	/**
	 * Returns Video ID.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function get_video_id() {

		$settings = $this->get_settings_for_display();
		$id       = '';
		$url      = $settings[ $settings['video_type'] . '_link' ];

		if ( 'youtube' === $settings['video_type'] ) {

			if ( preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches ) ) {
				$id = $matches[1];
			}
		} elseif ( 'vimeo' === $settings['video_type'] ) {

			if ( preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs ) ) {
				$id = $regs[3];
			}
		} elseif ( 'wistia' === $settings['video_type'] ) {

			$id = $this->getStringBetween( $url, 'wvideo=', '"' );

		}

		return $id;
	}

	/**
	 * Returns Video URL.
	 *
	 * @param string $url Video URL.
	 * @param string $from From compare string.
	 * @param string $to To compare string.
	 * @since 1.17.0
	 * @access protected
	 */
	protected function getStringBetween( $url, $from, $to ) {
		$sub = substr( $url, strpos( $url, $from ) + strlen( $from ), strlen( $url ) );
		$id  = substr( $sub, 0, strpos( $sub, $to ) );

		return $id;
	}

	/**
	 * Returns Video URL.
	 *
	 * @param array  $params Video Param array.
	 * @param string $id Video ID.
	 * @since 1.3.2
	 * @access protected
	 */
	protected function get_url( $params, $id ) {

		$settings = $this->get_settings_for_display();
		$url      = '';

		if ( 'vimeo' === $settings['video_type'] ) {

			$url = 'https://player.vimeo.com/video/';

		} elseif ( 'youtube' === $settings['video_type'] ) {

			$cookie = '';

			if ( 'yes' === $settings['yt_privacy'] ) {
				$cookie = '-nocookie';
			}
			$url = 'https://www.youtube' . $cookie . '.com/embed/';

		} elseif ( 'wistia' === $settings['video_type'] ) {

			$url = 'https://fast.wistia.net/embed/iframe/';

		}

		$url = add_query_arg( $params, $url . $id );

		$url .= ( empty( $params ) ) ? '?' : '&';

		$url .= 'autoplay=1';

		if ( 'vimeo' === $settings['video_type'] && '' !== $settings['start'] ) {

			$time = gmdate( 'H\hi\ms\s', $settings['start'] );

			$url .= '#t=' . $time;

		} elseif ( 'vimeo' === $settings['video_type'] ) {

			$url .= '#t=';
		}

		$url = apply_filters( 'uael_video_url_filter', $url, $id );

		return $url;
	}

	/**
	 * Returns Vimeo Headers.
	 *
	 * @param string $id Video ID.
	 * @since 1.3.2
	 * @access protected
	 */
	protected function get_header_wrap( $id ) {

		$settings = $this->get_settings_for_display();

		if ( 'vimeo' !== $settings['video_type'] ) {
			return;
		}

		$response = wp_remote_get( "https://vimeo.com/api/v2/video/$id.php" );

		if ( is_wp_error( $response ) ) {
			return;
		}

		if ( 404 === $response['response']['code'] ) {
			return;
		}
		$vimeo = maybe_unserialize( $response['body'] );

		if (
			'yes' === $settings['vimeo_portrait'] ||
			'yes' === $settings['vimeo_title'] ||
			'yes' === $settings['vimeo_byline']
		) { ?>
		<div class="uael-vimeo-wrap">
			<?php if ( 'yes' === $settings['vimeo_portrait'] ) { ?>
			<div class="uael-vimeo-portrait">
				<a href="<?php esc_url( $vimeo[0]['user_url'] ); ?>"><img src="<?php echo esc_url( $vimeo[0]['user_portrait_huge'] ); ?>" alt=""></a>
			</div>
			<?php } ?>
			<?php
			if (
				'yes' === $settings['vimeo_title'] ||
				'yes' === $settings['vimeo_byline']
			) {
				?>
			<div class="uael-vimeo-headers">
				<?php if ( 'yes' === $settings['vimeo_title'] ) { ?>
				<div class="uael-vimeo-title">
					<a href="<?php $settings['vimeo_link']; ?>"><?php echo esc_html( $vimeo[0]['title'] ); ?></a>
				</div>
				<?php } ?>
				<?php if ( 'yes' === $settings['vimeo_byline'] ) { ?>
				<div class="uael-vimeo-byline">
					<?php esc_attr_e( 'from ', 'uael' ); ?> <a href="<?php $settings['vimeo_link']; ?>"><?php echo esc_html( $vimeo[0]['user_name'] ); ?></a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
		<?php
	}

	/**
	 * Render Video.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function get_video_embed() {

		$settings       = $this->get_settings_for_display();
		$sticky_hide    = array();
		$is_editor      = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$id             = $this->get_video_id();
		$embed_param    = $this->get_embed_params();
		$sticky         = ( 'yes' === $settings['enable_sticky'] ) ? 'yes' : 'no';
		$stick_desktop  = '';
		$stick_tablet   = '';
		$stick_mobile   = '';
		$sticky_infobar = ( 'yes' === $settings['sticky_info_bar_switch'] ) ? 'uael-sticky-infobar-wrap' : '';
		$viewport       = 0;
		$viewport       = apply_filters( 'uael_sticky_video_viewport', $viewport );

		if ( 'hosted' !== $settings['video_type'] ) {
			$src = $this->get_url( $embed_param, $id );
		} else {
			$src = $this->get_hosted_video_url();
		}

		if ( is_array( $settings['sticky_hide_on'] ) ) {
			foreach ( $settings['sticky_hide_on'] as $element ) {
				if ( 'desktop' === $element ) {
					$stick_desktop = 'desktop';
				} elseif ( 'tablet' === $element ) {
					$stick_tablet = 'tablet';
				} elseif ( 'mobile' === $element ) {
					$stick_mobile = 'mobile';
				}
			}
		} else {
			if ( 'desktop' === $settings['sticky_hide_on'] ) {
				$stick_desktop = 'desktop';
			} elseif ( 'tablet' === $settings['sticky_hide_on'] ) {
				$stick_tablet = 'tablet';
			} elseif ( 'mobile' === $settings['sticky_hide_on'] ) {
				$stick_mobile = 'mobile';
			}
		}

		if ( 'yes' === $settings['video_double_click'] ) {
			$device = 'false';
		} else {
			$device = ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== ( stripos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'iPhone' ) ) ? 'true' : 'false' ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__
		}

		switch ( $settings['video_type'] ) {

			case 'youtube':
				$autoplay = ( 'yes' === $settings['yt_autoplay'] ) ? '1' : '0';
				break;

			case 'vimeo':
				$autoplay = ( 'yes' === $settings['vimeo_autoplay'] ) ? '1' : '0';
				break;

			case 'wistia':
				$autoplay = ( 'yes' === $settings['wistia_autoplay'] ) ? '1' : '0';
				break;

			case 'hosted':
				$autoplay = ( 'yes' === $settings['autoplay'] ) ? '1' : '0';
				break;

			default:
				break;
		}

		if ( ! empty( $settings['sticky_video_margin'] ) ) {
			$sticky_bottom = ( '' !== $settings['sticky_video_margin']['bottom'] ) ? $settings['sticky_video_margin']['bottom'] : '';
			$this->add_render_attribute( 'video-outer', 'data-stickybottom', $sticky_bottom );
		}

		$this->add_render_attribute( 'video-outer', 'class', 'uael-video__outer-wrap' );
		$this->add_render_attribute( 'video-outer', 'class', $sticky_infobar );
		$this->add_render_attribute( 'video-outer', 'class', 'uael-video-type-' . $settings['video_type'] );
		$this->add_render_attribute( 'video-outer', 'data-device', $device );
		$this->add_render_attribute( 'video-outer', 'data-vsticky', $sticky );

		$this->add_render_attribute( 'video-outer', 'data-hidedesktop', $stick_desktop );
		$this->add_render_attribute( 'video-outer', 'data-hidetablet', $stick_tablet );
		$this->add_render_attribute( 'video-outer', 'data-hidemobile', $stick_mobile );

		$this->add_render_attribute( 'video-outer', 'data-vsticky-viewport', $viewport );

		$this->add_render_attribute( 'video-wrapper', 'class', 'uael-video__play' );
		$this->add_render_attribute( 'video-wrapper', 'data-src', $src );

		$this->add_render_attribute( 'video-thumb', 'class', 'uael-video__thumb' );

		if ( 'hosted' !== $settings['video_type'] ) {
			$this->add_render_attribute( 'video-thumb', 'src', $this->get_video_thumb( $id ) );
		} else {
			if ( 'yes' === $settings['show_image_overlay'] ) {

				$thumb = Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings );

			} else {
				$thumb = $this->get_hosted_video_url();

			}
			$this->add_render_attribute( 'video-thumb', 'src', $thumb );
		}

		$this->add_render_attribute( 'video-thumb', 'alt', Control_Media::get_image_alt( $settings['image_overlay'] ) );
		$this->add_render_attribute( 'video-play', 'class', 'uael-video__play-icon' );

		if ( 'default' === $settings['play_source'] ) {
			switch ( $settings['video_type'] ) {
				case 'youtube':
					$html = '<svg height="100%" version="1.1" viewBox="0 0 68 48" width="100%"><path class="uael-youtube-icon-bg" d="m .66,37.62 c 0,0 .66,4.70 2.70,6.77 2.58,2.71 5.98,2.63 7.49,2.91 5.43,.52 23.10,.68 23.12,.68 .00,-1.3e-5 14.29,-0.02 23.81,-0.71 1.32,-0.15 4.22,-0.17 6.81,-2.89 2.03,-2.07 2.70,-6.77 2.70,-6.77 0,0 .67,-5.52 .67,-11.04 l 0,-5.17 c 0,-5.52 -0.67,-11.04 -0.67,-11.04 0,0 -0.66,-4.70 -2.70,-6.77 C 62.03,.86 59.13,.84 57.80,.69 48.28,0 34.00,0 34.00,0 33.97,0 19.69,0 10.18,.69 8.85,.84 5.95,.86 3.36,3.58 1.32,5.65 .66,10.35 .66,10.35 c 0,0 -0.55,4.50 -0.66,9.45 l 0,8.36 c .10,4.94 .66,9.45 .66,9.45 z" fill="#1f1f1e"></path><path d="m 26.96,13.67 18.37,9.62 -18.37,9.55 -0.00,-19.17 z" fill="#fff"></path><path d="M 45.02,23.46 45.32,23.28 26.96,13.67 43.32,24.34 45.02,23.46 z" fill="#ccc"></path></svg>';
					break;

				case 'vimeo':
					$this->add_render_attribute( 'video-play', 'class', 'uael-video__vimeo-play' );

					$html = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="uael-vimeo-icon-bg" x="0px" y="0px" width="100%" height="100%" viewBox="0 14.375 95 66.25" enable-background="new 0 14.375 95 66.25" xml:space="preserve" fill="rgba(23,34,35,.75)"><path d="M12.5,14.375c-6.903,0-12.5,5.597-12.5,12.5v41.25c0,6.902,5.597,12.5,12.5,12.5h70c6.903,0,12.5-5.598,12.5-12.5v-41.25 c0-6.903-5.597-12.5-12.5-12.5H12.5z"/><polygon fill="#FFFFFF" points="39.992,64.299 39.992,30.701 62.075,47.5 "/></svg>';
					break;

				case 'wistia':
					$this->add_render_attribute( 'video-play', 'class', 'uael-video__vimeo-play' );

					$html = '<button class="uael-video-wistia-play w-big-play-button w-css-reset-button-important w-vulcan-v2-button"><svg x="0px" y="0px" viewBox="0 0 125 80" enable-background="new 0 0 125 80" focusable="false" alt="" style="fill: rgb(255, 255, 255); height: 100%; left: 0px; stroke-width: 0px; top: 0px; width: 100%;"><rect fill-rule="evenodd" clip-rule="evenodd" fill="none" width="125" height="80"></rect><polygon fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" points="53,22 53,58 79,40"></polygon></svg></button>';
					break;

				case 'hosted':
					$this->add_render_attribute( 'video-play', 'class', 'uael-video__hosted-play' );

					$html = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="uael-vimeo-icon-bg" x="0px" y="0px" width="100%" height="100%" viewBox="0 14.375 95 66.25" enable-background="new 0 14.375 95 66.25" xml:space="preserve" fill="rgba(23,34,35,.75)"><path d="M12.5,14.375c-6.903,0-12.5,5.597-12.5,12.5v41.25c0,6.902,5.597,12.5,12.5,12.5h70c6.903,0,12.5-5.598,12.5-12.5v-41.25 c0-6.903-5.597-12.5-12.5-12.5H12.5z"/><polygon fill="#FFFFFF" points="39.992,64.299 39.992,30.701 62.075,47.5 "/></svg>';
					break;

				default:
					break;
			}
		} elseif ( 'icon' === $settings['play_source'] ) {
			$html = '';

			if ( UAEL_Helper::is_elementor_updated() ) {

				if ( ( isset( $settings['play_icon'] ) || isset( $settings['new_play_icon'] ) ) ) {
					$play_icon_migrated = isset( $settings['__fa4_migrated']['new_play_icon'] );
					$play_icon_is_new   = ! isset( $settings['play_icon'] );

					if ( $play_icon_is_new || $play_icon_migrated ) {
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['new_play_icon'], array( 'aria-hidden' => 'true' ) );
						$html = ob_get_clean();
					} elseif ( ! empty( $settings['play_icon'] ) ) {
						$html = '<i class="' . $settings['play_icon'] . '" aria-hidden="true"></i>';
					}
				}
			} elseif ( ! empty( $settings['play_icon'] ) ) {
				$html = '<i class="' . $settings['play_icon'] . '" aria-hidden="true"></i>';
			}
		} else {
			$html = '<img src="' . $settings['play_img']['url'] . '" alt="' . Control_Media::get_image_alt( $settings['play_img'] ) . '" />';
		}

		if ( 'img' === $settings['play_source'] ) {
			$this->add_render_attribute( 'video-play', 'class', 'uael-animation-' . $settings['hover_animation_img'] );
		} elseif ( 'default' === $settings['play_source'] ) {
			$this->add_render_attribute( 'video-play', 'class', 'uael-animation-' . $settings['default_hover_animation'] );
		} else {
			$this->add_render_attribute( 'video-play', 'class', 'uael-animation-' . $settings['hover_animation'] );
		}

		if ( 'hosted' === $settings['video_type'] ) {

			$video_url = $this->get_hosted_video_url();

			ob_start();

			$this->render_hosted_video();

			$video_html = ob_get_clean();

			$video_html = wp_json_encode( $video_html );

			$video_html = htmlspecialchars( $video_html, ENT_QUOTES );

			$this->add_render_attribute(
				'video-outer',
				array(
					'data-hosted-html' => $video_html,
				)
			);
		}

		if ( 'yes' === $settings['lightbox'] ) {

			if ( 'hosted' === $settings['video_type'] ) {
				$lightbox_src = $video_url;
			} else {
				$lightbox_src = $src;
			}

			$lightbox_options = array(
				'type'         => 'video',
				'videoType'    => $settings['video_type'],
				'url'          => $lightbox_src,
				'modalOptions' => array(
					'id'                       => 'elementor-lightbox-' . $this->get_id(),
					'entranceAnimation'        => $settings['lightbox_content_animation'],
					'entranceAnimation_tablet' => $settings['lightbox_content_animation_tablet'],
					'entranceAnimation_mobile' => $settings['lightbox_content_animation_mobile'],
					'videoAspectRatio'         => '169',
				),
			);

			if ( 'hosted' === $settings['video_type'] ) {
				$lightbox_options['videoParams'] = $this->get_hosted_parameter();
			}

			$this->add_render_attribute( 'video-outer', 'class', 'uael-video-play-lightbox' );
			$this->add_render_attribute(
				'video-outer',
				array(
					'data-elementor-open-lightbox' => 'yes',
					'data-elementor-lightbox'      => wp_json_encode( $lightbox_options ),
				)
			);

		} else {
			$this->add_render_attribute( 'video-outer', 'data-autoplay', $autoplay );
		}

		if ( 'hosted' === $settings['video_type'] && 'yes' !== $settings['show_image_overlay'] ) {
			$custom_tag = 'video';
		} else {
			$custom_tag = 'img';
		}
		if ( 'hosted' === $settings['video_type'] ) {
			$video_url = $this->get_hosted_video_url();
		} else {
			$video_url = $this->get_url( $embed_param, $id );
		}

		?>
		<?php if ( 'hosted' === $settings['video_type'] && empty( $video_url ) && $is_editor ) { ?>
			<span class='uael-hosted-error-message'>
				<?php
				echo '<div class="elementor-alert elementor-alert-warning">';
					echo esc_attr__( 'Please choose a file.', 'uael' );
				echo '</div>';
				?>
			</span>
		<?php } elseif ( ! empty( $video_url ) ) { ?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'video-outer' ) ); ?>>
			<?php $this->get_header_wrap( $id ); ?>
			<div class="uael-video-inner-wrap">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'video-wrapper' ) ); ?>>
					<<?php echo esc_attr( $custom_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'video-thumb' ) ); ?>></<?php echo esc_attr( $custom_tag ); ?>>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'video-play' ) ); ?>>
						<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
				<?php if ( 'yes' === $settings['enable_sticky'] && 'yes' === $settings['enable_sticky_close_button'] ) { ?>
					<div class="uael-video-sticky-close">
						<i class="fas fa-times uael-sticky-close-icon"></i>
					</div>
				<?php } ?>
				<?php if ( 'yes' === $settings['sticky_info_bar_switch'] && '' !== $settings['sticky_info_bar_text'] ) { ?>
					<div class="uael-video-sticky-infobar"><?php echo wp_kses_post( $settings['sticky_info_bar_text'] ); ?></div>
				<?php } ?>
			</div>
		</div>
			<?php
		}
		if ( 'youtube' === $settings['video_type'] && 'yes' === $settings['subscribe_bar'] ) {
			$channel_name = ( '' !== $settings['subscribe_bar_channel_name'] ) ? $settings['subscribe_bar_channel_name'] : '';

			$channel_id = ( '' !== $settings['subscribe_bar_channel_id'] ) ? $settings['subscribe_bar_channel_id'] : '';

			$channel_id = apply_filters( 'uael_video_default_channel_id', $channel_id, $settings );

			$youtube_text = ( '' !== $settings['subscribe_bar_channel_text'] ) ? $settings['subscribe_bar_channel_text'] : '';

			$subscriber_count = ( 'yes' === $settings['subscribe_count'] ) ? 'default' : 'hidden';

			?>
			<div class="uael-subscribe-bar">
				<div class="uael-subscribe-bar-prefix"><?php echo wp_kses_post( $youtube_text ); ?></div>
				<div class="uael-subscribe-content">
				<?php if ( false !== $is_editor ) { ?>
						<script src="https://apis.google.com/js/platform.js"></script> <?php //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
					<?php } ?>
				<?php if ( 'channel_name' === $settings['subscribe_bar_select'] ) { ?>
						<div class="g-ytsubscribe" data-channel="<?php echo esc_attr( $channel_name ); ?>" data-count="<?php echo esc_attr( $subscriber_count ); ?>"></div>
					<?php } elseif ( 'channel_id' === $settings['subscribe_bar_select'] ) { ?>
						<div class="g-ytsubscribe" data-channelid="<?php echo esc_attr( $channel_id ); ?>" data-count="<?php echo esc_attr( $subscriber_count ); ?>"></div>
					<?php } ?>
				</div>
			</div>
				<?php
		}
	}

	/**
	 * Render Video output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.3.2
	 * @access protected
	 */
	protected function render() {

		$settings               = $this->get_settings_for_display();
		$enable_schema          = $settings['schema_support'];
		$content_schema_warning = false;
		$is_editor              = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$is_custom_thumbnail    = 'yes' === $settings['show_image_overlay'] ? true : false;
		$custom_thumbnail_url   = isset( $settings['image_overlay']['url'] ) ? $settings['image_overlay']['url'] : '';

		if ( 'yes' === $enable_schema && ( ( '' === $settings['schema_title'] || '' === $settings['schema_description'] || ( ! $is_custom_thumbnail && '' === $settings['schema_thumbnail']['url'] ) || '' === $settings['schema_upload_date'] ) || ( $is_custom_thumbnail && '' === $custom_thumbnail_url ) ) ) {
			$content_schema_warning = true;
		}

		if ( 'yes' === $enable_schema && true === $content_schema_warning && $is_editor ) {
			?>
			<div class="uael-builder-msg elementor-alert elementor-alert-warning">
				<?php if ( $is_custom_thumbnail && '' === $custom_thumbnail_url ) { ?>
					<span class="elementor-alert-description"><?php esc_html_e( 'Please set a custom thumbnail to display video schema properly.', 'uael' ); ?></span>
				<?php } else { ?>
					<span class="elementor-alert-description"><?php esc_html_e( 'Some fields are empty under the video schema section. Please fill in all required fields.', 'uael' ); ?></span>
				<?php } ?>
			</div>
			<?php
		}

		if ( '' === $settings['youtube_link'] && 'youtube' === $settings['video_type'] ) {
			return '';
		}

		if ( '' === $settings['vimeo_link'] && 'vimeo' === $settings['video_type'] ) {
			return '';
		}

		if ( '' === $settings['wistia_link'] && 'wistia' === $settings['video_type'] ) {
			return '';
		}

		if ( '' === $settings['hosted_link'] && 'hosted' === $settings['video_type'] ) {
			return '';
		}

		$this->get_video_embed();
	}

	/**
	 * Get hosted video URL.
	 *
	 * @since 1.29.1
	 * @access protected
	 */
	private function get_hosted_video_url() {

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['insert_link'] ) ) {
			$video_url = $settings['external_link']['url'];
		} else {
			$video_url = isset( $settings['hosted_link']['url'] ) ? $settings['hosted_link']['url'] : '';
		}

		if ( empty( $video_url ) ) {
			return '';
		}
		if ( $settings['start'] || $settings['end'] ) {
			$video_url .= '#t=';
		}

		if ( $settings['start'] ) {
			$video_url .= $settings['start'];
		}

		if ( $settings['end'] ) {
			$video_url .= ',' . $settings['end'];
		}
		return $video_url;
	}


	/**
	 * Get hosted video parameters.
	 *
	 * @since 1.29.1
	 * @access protected
	 */
	private function get_hosted_parameter() {
		$settings = $this->get_settings_for_display();

		$video_params = array();

		foreach ( array( 'autoplay', 'loop', 'controls' ) as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$video_params[ $option_name ] = '';
			}
		}

		if ( $settings['muted'] ) {
			$video_params['muted'] = 'muted';
		}

		return $video_params;
	}


	/**
	 * Render hosted video.
	 *
	 * @since 1.29.1
	 * @access protected
	 */
	private function render_hosted_video() {
		$video_url = $this->get_hosted_video_url();
		if ( empty( $video_url ) ) {
			return;
		}

		$video_params = $this->get_hosted_parameter();

		?>
		<video class="uael-hosted-video" src="<?php echo esc_url( $video_url ); ?>" <?php echo esc_attr( Utils::render_html_attributes( $video_params ) ); ?>></video>
		<?php
	}

	/**
	 * Render video widget as plain content.
	 *
	 * Override the default behavior, by printing the video URL insted of rendering it.
	 *
	 * @since 1.3.2
	 * @access public
	 */
	public function render_plain_content() {
		$settings = $this->get_settings_for_display();
		$url      = 'youtube' === $settings['video_type'] ? $settings['youtube_link'] : $settings['vimeo_link'];

		echo esc_url( $url );
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget embed parameters.
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params() {

		$settings = $this->get_settings_for_display();

		$params = array();

		if ( 'youtube' === $settings['video_type'] ) {
			$youtube_options = array( 'autoplay', 'rel', 'controls', 'mute', 'modestbranding' );

			foreach ( $youtube_options as $option ) {

				if ( 'autoplay' === $option ) {
					if ( 'yes' === $settings['yt_autoplay'] ) {
						$params[ $option ] = '1';
					}
					continue;
				}

				$value             = ( 'yes' === $settings[ 'yt_' . $option ] ) ? '1' : '0';
				$params[ $option ] = $value;
				$params['start']   = $settings['start'];
				$params['end']     = $settings['end'];
			}
		}

		if ( 'vimeo' === $settings['video_type'] ) {
			$vimeo_options = array( 'autoplay', 'loop', 'title', 'portrait', 'byline', 'muted' );

			foreach ( $vimeo_options as $option ) {

				if ( 'autoplay' === $option ) {
					if ( 'yes' === $settings['vimeo_autoplay'] ) {
						$params[ $option ] = '1';
					}
					continue;
				}

				$value             = ( 'yes' === $settings[ 'vimeo_' . $option ] ) ? '1' : '0';
				$params[ $option ] = $value;
			}

			$params['color']     = str_replace( '#', '', $settings['vimeo_color'] );
			$params['autopause'] = '0';

			/**
			 * Support Vimeo unlisted and private videos
			 *
			 * Vimeo requires an additional parameter when displaying private/unlisted videos. It has two ways of
			 * passing that parameter:
			 * * as an endpoint - vimeo.com/{video_id}/{privacy_token}
			 * OR
			 * * as a GET parameter named `h` - vimeo.com/{video_id}?h={privacy_token}
			 *
			 * The following regex match looks for either of these methods in the Vimeo URL, and if it finds a privacy
			 * token, it adds it to the embed params array as the `h` parameter (which is how Vimeo can receive it when
			 * using Oembed).
			 */
			$h_param   = array();
			$video_url = $settings['vimeo_link'];
			preg_match( '/(?|(?:[\?|\&]h={1})([\w]+)|\d\/([\w]+))/', $video_url, $h_param );

			if ( ! empty( $h_param ) ) {
				$params['h'] = $h_param[1];
			}
		}

		if ( 'wistia' === $settings['video_type'] ) {

			$wistia_options = array( 'autoplay', 'muted', 'playbar', 'loop' );

			foreach ( $wistia_options as $option ) {

				if ( 'autoplay' === $option ) {
					if ( 'yes' === $settings['wistia_autoplay'] ) {
						$params[ $option ] = '1';
					}
					continue;
				}

				if ( 'loop' === $option ) {
					if ( 'yes' === $settings['wistia_loop'] ) {
						$params['endVideoBehavior'] = 'loop';
					}
					continue;
				}

				$value             = ( 'yes' === $settings[ 'wistia_' . $option ] ) ? 'true' : 'false';
				$params[ $option ] = $value;
			}

			$params['videoFoam'] = 'true';
		}

		return $params;
	}
}

