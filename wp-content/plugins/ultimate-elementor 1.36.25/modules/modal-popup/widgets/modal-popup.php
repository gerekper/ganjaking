<?php
/**
 * UAEL Modal Popup.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\ModalPopup\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Background;
use Elementor\Control_Media;
use Elementor\Modules\DynamicTags\Module as TagsModule;

// UltimateElementor Classes.
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Modal_Popup.
 */
class Modal_Popup extends Common_Widget {

	/**
	 * Retrieve Modal Popup Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Modal_Popup' );
	}

	/**
	 * Retrieve Modal Popup Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Modal_Popup' );
	}

	/**
	 * Retrieve Modal Popup Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Modal_Popup' );
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
		return parent::get_widget_keywords( 'Modal_Popup' );
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-cookie-lib', 'uael-modal-popup', 'uael-element-resize' );
	}

	/**
	 * Register Modal Popup controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_general_content_controls();
		$this->register_modal_popup_content_controls();
		$this->register_close_content_controls();
		$this->register_display_content_controls();

		$this->register_title_style_controls();
		$this->register_content_style_controls();
		$this->register_button_style_controls();
		$this->register_cta_style_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Modal Popup General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_modal_popup_content_controls() {

		$this->start_controls_section(
			'section_modal',
			array(
				'label' => __( 'Modal Popup', 'uael' ),
			)
		);

			$this->add_responsive_control(
				'modal_width',
				array(
					'label'          => __( 'Modal Popup Width', 'uael' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'px', 'em', '%' ),
					'default'        => array(
						'size' => '500',
						'unit' => 'px',
					),
					'tablet_default' => array(
						'size' => '500',
						'unit' => 'px',
					),
					'mobile_default' => array(
						'size' => '300',
						'unit' => 'px',
					),
					'range'          => array(
						'px' => array(
							'min' => 0,
							'max' => 1500,
						),
						'em' => array(
							'min' => 0,
							'max' => 100,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'      => array(
						'.uamodal-{{ID}} .uael-content' => 'width: {{SIZE}}{{UNIT}}',
					),
				)
			);

			$this->add_control(
				'modal_effect',
				array(
					'label'       => __( 'Modal Appear Effect', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'uael-effect-1',
					'label_block' => true,
					'options'     => array(
						'uael-effect-1'  => __( 'Fade in &amp; Scale', 'uael' ),
						'uael-effect-2'  => __( 'Slide in (right)', 'uael' ),
						'uael-effect-3'  => __( 'Slide in (bottom)', 'uael' ),
						'uael-effect-4'  => __( 'Newspaper', 'uael' ),
						'uael-effect-5'  => __( 'Fall', 'uael' ),
						'uael-effect-6'  => __( 'Side Fall', 'uael' ),
						'uael-effect-8'  => __( '3D Flip (horizontal)', 'uael' ),
						'uael-effect-9'  => __( '3D Flip (vertical)', 'uael' ),
						'uael-effect-10' => __( '3D Sign', 'uael' ),
						'uael-effect-11' => __( 'Super Scaled', 'uael' ),
						'uael-effect-13' => __( '3D Slit', 'uael' ),
						'uael-effect-14' => __( '3D Rotate Bottom', 'uael' ),
						'uael-effect-15' => __( '3D Rotate In Left', 'uael' ),
						'uael-effect-17' => __( 'Let me in', 'uael' ),
						'uael-effect-18' => __( 'Make way!', 'uael' ),
						'uael-effect-19' => __( 'Slip from top', 'uael' ),
					),
				)
			);

			$this->add_control(
				'overlay_color',
				array(
					'label'     => __( 'Overlay Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => 'rgba(0,0,0,0.75)',
					'selectors' => array(
						'.uamodal-{{ID}} .uael-overlay' => 'background: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_general_content_controls() {

		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Content', 'uael' ),
			)
		);

			$this->add_control(
				'preview_modal',
				array(
					'label'        => __( 'Preview Modal Popup', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
				)
			);

			$this->add_control(
				'title',
				array(
					'label'   => __( 'Title', 'uael' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array(
						'active' => true,
					),
					'default' => __( 'This is Modal Title', 'uael' ),
				)
			);

			$this->add_control(
				'content_type',
				array(
					'label'   => __( 'Content Type', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'photo',
					'options' => $this->get_content_type(),
				)
			);

			$this->add_control(
				'ct_content',
				array(
					'label'      => __( 'Description', 'uael' ),
					'type'       => Controls_Manager::WYSIWYG,
					'default'    => __( 'Enter content here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.​ Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'uael' ),
					'rows'       => 10,
					'show_label' => false,
					'dynamic'    => array(
						'active' => true,
					),
					'condition'  => array(
						'content_type' => 'content',
					),
				)
			);

			$this->add_control(
				'ct_photo',
				array(
					'label'     => __( 'Photo', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'content_type' => 'photo',
					),
				)
			);

			$this->add_control(
				'ct_video',
				array(
					'label'       => __( 'Embed Code / URL', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'condition'   => array(
						'content_type' => 'video',
					),
				)
			);

			$this->add_control(
				'ct_saved_rows',
				array(
					'label'     => __( 'Select Section', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'section' ),
					'default'   => '-1',
					'condition' => array(
						'content_type' => 'saved_rows',
					),
				)
			);

			$this->add_control(
				'ct_saved_container',
				array(
					'label'     => __( 'Select Container', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'container' ),
					'default'   => '-1',
					'condition' => array(
						'content_type' => 'saved_container',
					),
				)
			);

			$this->add_control(
				'ct_saved_modules',
				array(
					'label'     => __( 'Select Widget', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'widget' ),
					'default'   => '-1',
					'condition' => array(
						'content_type' => 'saved_modules',
					),
				)
			);

			$this->add_control(
				'ct_page_templates',
				array(
					'label'     => __( 'Select Page', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => UAEL_Helper::get_saved_data( 'page' ),
					'default'   => '-1',
					'condition' => array(
						'content_type' => 'saved_page_templates',
					),
				)
			);

			$this->add_control(
				'video_url',
				array(
					'label'       => __( 'Video URL', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'condition'   => array(
						'content_type' => array( 'youtube', 'vimeo' ),
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
						'content_type' => 'youtube',
					),
					'separator'       => 'none',
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
						'content_type' => 'vimeo',
					),
					'separator'       => 'none',
				)
			);

			$this->add_control(
				'iframe_url',
				array(
					'label'       => __( 'Iframe URL', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'condition'   => array(
						'content_type' => 'iframe',
					),
				)
			);

			$this->add_control(
				'async_iframe',
				array(
					'label'        => __( 'Async Iframe Load', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'description'  => __( 'Enabling this option will reduce the page size and page loading time. The related CSS and JS scripts will load on request. A loader will appear during loading of the Iframe.', 'uael' ),
					'condition'    => array(
						'content_type' => 'iframe',
					),
				)
			);

			$this->add_responsive_control(
				'iframe_height',
				array(
					'label'      => __( 'Height of Iframe', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em' ),
					'default'    => array(
						'size' => '500',
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 2000,
						),
						'em' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'.uamodal-{{ID}} .uael-modal-iframe .uael-modal-content-data' => 'height: {{SIZE}}{{UNIT}}',
					),
					'condition'  => array(
						'content_type' => 'iframe',
					),
				)
			);

			$this->add_control(
				'video_ratio',
				array(
					'label'              => __( 'Aspect Ratio', 'uael' ),
					'type'               => Controls_Manager::SELECT,
					'options'            => array(
						'16_9' => '16:9',
						'4_3'  => '4:3',
						'3_2'  => '3:2',
					),
					'default'            => '16_9',
					'prefix_class'       => 'uael-aspect-ratio-',
					'frontend_available' => true,
					'condition'          => array(
						'content_type' => array( 'youtube', 'vimeo' ),
					),
				)
			);

			$this->add_control(
				'video_autoplay',
				array(
					'label'        => __( 'Autoplay', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'condition'    => array(
						'content_type' => array( 'youtube', 'vimeo' ),
					),
				)
			);

			$this->add_control(
				'youtube_related_videos',
				array(
					'label'     => __( 'Related Videos From', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'no',
					'options'   => array(
						'no'  => __( 'Current Video Channel', 'uael' ),
						'yes' => __( 'Any Random Video', 'uael' ),
					),
					'condition' => array(
						'content_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'youtube_player_controls',
				array(
					'label'        => __( 'Disable Player Controls', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'condition'    => array(
						'content_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'video_controls_adv',
				array(
					'label'        => __( 'Advanced Settings', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'condition'    => array(
						'content_type' => array( 'youtube', 'vimeo' ),
					),
				)
			);

			$this->add_control(
				'start',
				array(
					'label'       => __( 'Start Time', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Specify a start time (in seconds)', 'uael' ),
					'condition'   => array(
						'content_type'       => array( 'youtube', 'vimeo' ),
						'video_controls_adv' => 'yes',
					),
				)
			);

			$this->add_control(
				'end',
				array(
					'label'       => __( 'End Time', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Specify an end time (in seconds)', 'uael' ),
					'condition'   => array(
						'content_type'       => 'youtube',
						'video_controls_adv' => 'yes',
					),
				)
			);

			$this->add_control(
				'yt_mute',
				array(
					'label'     => __( 'Mute', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'content_type'       => 'youtube',
						'video_controls_adv' => 'yes',
					),
				)
			);

			$this->add_control(
				'yt_modestbranding',
				array(
					'label'        => __( 'Modest Branding', 'uael' ),
					'description'  => __( 'This option lets you use a YouTube player that does not show a YouTube logo.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'condition'    => array(
						'content_type'             => 'youtube',
						'video_controls_adv'       => 'yes',
						'youtube_player_controls!' => 'yes',
					),
				)
			);

			$this->add_control(
				'vimeo_loop',
				array(
					'label'     => __( 'Loop', 'uael' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'content_type'       => 'vimeo',
						'video_controls_adv' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_close_content_controls() {

		$this->start_controls_section(
			'close_options',
			array(
				'label' => __( 'Close Button', 'uael' ),
			)
		);

			$this->add_control(
				'close_source',
				array(
					'label'   => __( 'Close As', 'uael' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'img'  => array(
							'title' => __( 'Image', 'uael' ),
							'icon'  => 'fa fa-image',
						),
						'icon' => array(
							'title' => __( 'Icon', 'uael' ),
							'icon'  => 'fa fa-info-circle',
						),
					),
					'default' => 'icon',
				)
			);

			/**
			 * Condition: 'close_source' => 'img'
			 */
			$this->add_control(
				'close_photo',
				array(
					'label'     => __( 'Close Image', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'condition' => array(
						'close_source' => 'img',
					),
				)
			);

			/**
			 * Condition: 'close_source' => 'icon'
			 */

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_close_icon',
				array(
					'label'            => __( 'Close Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'close_icon',
					'default'          => array(
						'value'   => 'fas fa-times',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'close_source' => 'icon',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$this->add_control(
				'close_icon',
				array(
					'label'     => __( 'Close Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'default'   => 'fa fa-close',
					'condition' => array(
						'close_source' => 'icon',
					),
				)
			);
		}

			$this->add_responsive_control(
				'close_icon_size',
				array(
					'label'      => __( 'Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'default'    => array(
						'size' => 20,
					),
					'range'      => array(
						'px' => array(
							'max' => 500,
						),
					),
					'selectors'  => array(
						'.uamodal-{{ID}} .uael-modal-close'   => 'font-size: {{SIZE}}px;line-height: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
						'.uamodal-{{ID}} .uael-modal-close i, .uamodal-{{ID}} .uael-modal-close svg' => 'font-size: {{SIZE}}px;line-height: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'close_icon' ),
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'name'     => 'close_source',
								'operator' => '==',
								'value'    => 'icon',
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				'close_img_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 20,
					),
					'range'     => array(
						'px' => array(
							'max' => 500,
						),
					),
					'selectors' => array(
						'.uamodal-{{ID}} .uael-modal-close' => 'font-size: {{SIZE}}px;line-height: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
					),
					'condition' => array(
						'close_source' => 'img',
					),
				)
			);

			$this->add_control(
				'close_icon_color',
				array(
					'label'      => __( 'Color', 'uael' ),
					'type'       => Controls_Manager::COLOR,
					'default'    => '#ffffff',
					'selectors'  => array(
						'.uamodal-{{ID}} .uael-modal-close i' => 'color: {{VALUE}};',
						'.uamodal-{{ID}} .uael-modal-close svg' => 'fill: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'close_icon' ),
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'name'     => 'close_source',
								'operator' => '==',
								'value'    => 'icon',
							),
						),
					),
				)
			);

			$this->add_control(
				'icon_position',
				array(
					'label'       => __( 'Image / Icon Position', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'top-right',
					'label_block' => true,
					'options'     => array(
						'top-left'             => __( 'Window - Top Left', 'uael' ),
						'top-right'            => __( 'Window - Top Right', 'uael' ),
						'popup-top-left'       => __( 'Popup - Top Left', 'uael' ),
						'popup-top-right'      => __( 'Popup - Top Right', 'uael' ),
						'popup-edge-top-left'  => __( 'Popup Edge - Top Left', 'uael' ),
						'popup-edge-top-right' => __( 'Popup Edge - Top Right', 'uael' ),
					),
				)
			);

			$this->add_control(
				'esc_keypress',
				array(
					'label'        => __( 'Close on ESC Keypress', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
				)
			);

			$this->add_control(
				'overlay_click',
				array(
					'label'        => __( 'Close on Overlay Click', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_display_content_controls() {

		$this->start_controls_section(
			'modal',
			array(
				'label' => __( 'Display Settings', 'uael' ),
			)
		);

			$this->add_control(
				'modal_on',
				array(
					'label'   => __( 'Display Modal On', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'button',
					'options' => array(
						'icon'      => __( 'Icon', 'uael' ),
						'photo'     => __( 'Image', 'uael' ),
						'text'      => __( 'Text', 'uael' ),
						'button'    => __( 'Button', 'uael' ),
						'custom'    => __( 'Custom Class', 'uael' ),
						'custom_id' => __( 'Custom ID', 'uael' ),
						'automatic' => __( 'Automatic', 'uael' ),
						'via_url'   => __( 'Via URL', 'uael' ),
					),
				)
			);

			$this->add_control(
				'via_url_message',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => sprintf( '<p style="font-size: 11px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'Append the "?uael-modal-action=modal-popup-id" at the end of your URL.', 'uael' ) ),
					'condition' => array(
						'modal_on' => 'via_url',
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default'          => array(
						'value'   => 'fa fa-home',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'modal_on' => 'icon',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$this->add_control(
				'icon',
				array(
					'label'     => __( 'Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'default'   => 'fa fa-home',
					'condition' => array(
						'modal_on' => 'icon',
					),
				)
			);
		}

			$this->add_control(
				'icon_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 60,
					),
					'range'     => array(
						'px' => array(
							'max' => 500,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action i, {{WRAPPER}} .uael-modal-action svg' => 'font-size: {{SIZE}}px;width: {{SIZE}}px;height: {{SIZE}}px;line-height: {{SIZE}}px;',
					),
					'condition' => array(
						'modal_on' => 'icon',
					),
				)
			);

			$this->add_control(
				'icon_color',
				array(
					'label'     => __( 'Icon Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-modal-action svg' => 'fill: {{VALUE}};',
					),
					'condition' => array(
						'modal_on' => 'icon',
					),
				)
			);

			$this->add_control(
				'icon_hover_color',
				array(
					'label'     => __( 'Icon Hover Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action i:hover' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-modal-action svg:hover' => 'fill: {{VALUE}};',
					),
					'condition' => array(
						'modal_on' => 'icon',
					),
				)
			);

			$this->add_control(
				'photo',
				array(
					'label'     => __( 'Image', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'modal_on' => 'photo',
					),
				)
			);

			$this->add_control(
				'img_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 60,
					),
					'range'     => array(
						'px' => array(
							'max' => 500,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action img' => 'width: {{SIZE}}px;',
					),
					'condition' => array(
						'modal_on' => 'photo',
					),
				)
			);

			$this->add_control(
				'modal_text',
				array(
					'label'     => __( 'Text', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Click Here', 'uael' ),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'modal_on' => 'text',
					),
				)
			);

			$this->add_control(
				'modal_custom',
				array(
					'label'       => __( 'Class', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'description' => __( 'Add your custom class without the dot. e.g: my-class', 'uael' ),
					'condition'   => array(
						'modal_on' => 'custom',
					),
				)
			);

			$this->add_control(
				'modal_custom_id',
				array(
					'label'       => __( 'Custom ID', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'description' => __( 'Add your custom id without the Pound key. e.g: my-id', 'uael' ),
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'modal_on' => 'custom_id',
					),
				)
			);

			$this->add_control(
				'exit_intent',
				array(
					'label'        => __( 'Exit Intent', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'condition'    => array(
						'modal_on' => 'automatic',
					),
					'selectors'    => array(
						'.uamodal-{{ID}}' => '',
					),
				)
			);

			$this->add_control(
				'after_second',
				array(
					'label'        => __( 'After Few Seconds', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'condition'    => array(
						'modal_on' => 'automatic',
					),
					'selectors'    => array(
						'.uamodal-{{ID}}' => '',
					),
				)
			);

			$this->add_control(
				'after_second_value',
				array(
					'label'     => __( 'Load After Seconds', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 1,
					),
					'condition' => array(
						'after_second' => 'yes',
						'modal_on'     => 'automatic',
					),
					'selectors' => array(
						'.uamodal-{{ID}}' => '',
					),
				)
			);

			$this->add_control(
				'enable_cookies',
				array(
					'label'        => __( 'Enable Cookies', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'return_value' => 'yes',
					'label_off'    => __( 'No', 'uael' ),
					'label_on'     => __( 'Yes', 'uael' ),
					'conditions'   => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'modal_on',
								'operator' => '==',
								'value'    => 'automatic',
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'exit_intent',
										'operator' => '==',
										'value'    => 'yes',
									),
									array(
										'name'     => 'after_second',
										'operator' => '==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'selectors'    => array(
						'.uamodal-{{ID}}' => '',
					),
				)
			);

			$this->add_control(
				'set_cookie_on',
				array(
					'label'       => __( 'Set Cookies On', 'uael' ),
					'description' => __( 'Choose an action on which you want to set cookies to hide the popup for number of days.', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'default',
					'label_block' => false,
					'condition'   => array(
						'enable_cookies' => 'yes',
						'modal_on'       => 'automatic',
					),
					'options'     => array(
						'default' => __( 'Page Refresh', 'uael' ),
						'closed'  => __( 'Close Action', 'uael' ),
					),
				)
			);

			$this->add_control(
				'close_cookie_days',
				array(
					'label'     => __( 'Hide for Number of Days', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 1,
					),
					'condition' => array(
						'enable_cookies' => 'yes',
						'modal_on'       => 'automatic',
					),
					'selectors' => array(
						'.uamodal-{{ID}}' => '',
					),
				)
			);

			$this->add_control(
				'btn_text',
				array(
					'label'       => __( 'Button Text', 'uael' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Click Me', 'uael' ),
					'placeholder' => __( 'Click Me', 'uael' ),
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'modal_on' => 'button',
					),
				)
			);

			$this->add_responsive_control(
				'btn_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'    => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'   => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
						'justify' => array(
							'title' => __( 'Justified', 'uael' ),
							'icon'  => 'fa fa-align-justify',
						),
					),
					'default'   => 'left',
					'condition' => array(
						'modal_on' => 'button',
					),
					'toggle'    => false,
				)
			);

			$this->add_control(
				'btn_size',
				array(
					'label'     => __( 'Size', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'sm',
					'options'   => array(
						'xs' => __( 'Extra Small', 'uael' ),
						'sm' => __( 'Small', 'uael' ),
						'md' => __( 'Medium', 'uael' ),
						'lg' => __( 'Large', 'uael' ),
						'xl' => __( 'Extra Large', 'uael' ),
					),
					'condition' => array(
						'modal_on' => 'button',
					),
				)
			);

			$this->add_responsive_control(
				'btn_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-modal-action-wrap a.elementor-button, {{WRAPPER}} .uael-modal-action-wrap .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'modal_on' => 'button',
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_btn_icon',
				array(
					'label'            => __( 'Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'btn_icon',
					'label_block'      => true,
					'condition'        => array(
						'modal_on' => 'button',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$this->add_control(
				'btn_icon',
				array(
					'label'       => __( 'Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'condition'   => array(
						'modal_on' => 'button',
					),
				)
			);
		}

			$this->add_control(
				'btn_icon_align',
				array(
					'label'      => __( 'Icon Position', 'uael' ),
					'type'       => Controls_Manager::SELECT,
					'default'    => 'left',
					'options'    => array(
						'left'  => __( 'Before', 'uael' ),
						'right' => __( 'After', 'uael' ),
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'btn_icon' ),
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'name'     => 'modal_on',
								'operator' => '==',
								'value'    => 'button',
							),
						),
					),
				)
			);

			$this->add_control(
				'btn_icon_indent',
				array(
					'label'      => __( 'Icon Spacing', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'max' => 50,
						),
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => UAEL_Helper::get_new_icon_name( 'btn_icon' ),
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'name'     => 'modal_on',
								'operator' => '==',
								'value'    => 'button',
							),
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-modal-action-wrap .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .uael-modal-action-wrap .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'all_align',
				array(
					'label'     => __( 'Alignment', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'   => 'left',
					'condition' => array(
						'modal_on' => array( 'icon', 'photo', 'text' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action-wrap' => 'text-align: {{VALUE}};',
					),
					'toggle'    => false,
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_title_style_controls() {

		$this->start_controls_section(
			'section_title_typography',
			array(
				'label'     => __( 'Title', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'title!' => '',
				),
			)
		);

			$this->add_responsive_control(
				'title_alignment',
				array(
					'label'     => __( 'Alignment', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'   => 'left',
					'selectors' => array(
						'.uamodal-{{ID}} .uael-modal-title-wrap' => 'text-align: {{VALUE}};',
					),
					'toggle'    => false,
				)
			);

			$this->add_responsive_control(
				'title_spacing',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'.uamodal-{{ID}} .uael-modal-title-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'default'    => array(
						'top'    => '15',
						'bottom' => '15',
						'left'   => '25',
						'right'  => '25',
						'unit'   => 'px',
					),
				)
			);

			$this->add_control(
				'title_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'selectors' => array(
						'.uamodal-{{ID}} .uael-modal-title-wrap .uael-modal-title' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-modal-title-wrap .uael-modal-title' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'title_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_SECONDARY,
					),
					'selectors' => array(
						'.uamodal-{{ID}} .uael-modal-title-wrap' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .uael-modal-title-wrap' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'title_tag',
				array(
					'label'   => __( 'HTML Tag', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'h1'   => __( 'H1', 'uael' ),
						'h2'   => __( 'H2', 'uael' ),
						'h3'   => __( 'H3', 'uael' ),
						'h4'   => __( 'H4', 'uael' ),
						'h5'   => __( 'H5', 'uael' ),
						'h6'   => __( 'H6', 'uael' ),
						'div'  => __( 'div', 'uael' ),
						'span' => __( 'span', 'uael' ),
						'p'    => __( 'p', 'uael' ),
					),
					'default' => 'h3',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '.uamodal-{{ID}} .uael-modal-title-wrap .uael-modal-title, {{WRAPPER}} .uael-modal-title-wrap .uael-modal-title',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_style_controls() {

		$this->start_controls_section(
			'section_content_typography',
			array(
				'label' => __( 'Content', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'content_text_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'.uamodal-{{ID}} .uael-content' => 'color: {{VALUE}};',
						'{{WRAPPER}} .uael-content'     => 'color: {{VALUE}};',
					),
					'condition' => array(
						'content_type' => 'content',
					),
				)
			);

			$this->add_control(
				'content_bg_color',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => array(
						'.uamodal-{{ID}} .uael-content' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'modal_spacing',
				array(
					'label'      => __( 'Content Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'.uamodal-{{ID}} .uael-content .uael-modal-content-data' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'default'    => array(
						'top'    => '25',
						'bottom' => '25',
						'left'   => '25',
						'right'  => '25',
						'unit'   => 'px',
					),
				)
			);

			$this->add_control(
				'vplay_icon_header',
				array(
					'label'      => __( 'Play Icon', 'uael' ),
					'type'       => Controls_Manager::HEADING,
					'separator'  => 'before',
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'vimeo',
							),
						),
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_vimeo_play_icon',
				array(
					'label'            => __( 'Select Icon', 'uael' ),
					'description'      => __( 'Note: The Upload SVG option is not supported for the Vimeo play icon.', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'vimeo_play_icon',
					'default'          => array(
						'value'   => 'fa fa-play-circle',
						'library' => 'fa-solid',
					),
					'render_type'      => 'template',
					'conditions'       => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'vimeo',
							),
						),
					),
				)
			);
		} else {
			$this->add_control(
				'vimeo_play_icon',
				array(
					'label'      => __( 'Select Icon', 'uael' ),
					'type'       => Controls_Manager::ICON,
					'default'    => 'fa fa-play-circle',
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'vimeo',
							),
						),
					),
				)
			);
		}

			$this->add_control(
				'vplay_size',
				array(
					'label'      => __( 'Icon Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'default'    => array(
						'size' => 72,
					),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'.uamodal-{{ID}} .play'        => 'width: {{SIZE}}px; height: {{SIZE}}px;',
						'.uamodal-{{ID}} .play:before' => 'font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'vimeo',
							),
						),
					),
				)
			);

			$this->add_control(
				'vplay_color',
				array(
					'label'      => __( 'Icon Color', 'uael' ),
					'type'       => Controls_Manager::COLOR,
					'default'    => 'rgba( 0,0,0,0.8 )',
					'selectors'  => array(
						'.uamodal-{{ID}} .play:before' => 'color: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'vimeo',
							),
						),
					),
				)
			);

			$this->add_control(
				'yplay_icon_header',
				array(
					'label'      => __( 'Play Icon', 'uael' ),
					'type'       => Controls_Manager::HEADING,
					'separator'  => 'before',
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {

			$this->add_control(
				'new_youtube_play_icon',
				array(
					'label'            => __( 'Select Icon', 'uael' ),
					'description'      => __( 'Note: The Upload SVG option is not supported for the YouTube play icon.', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'youtube_play_icon',
					'default'          => array(
						'value'   => 'fa fa-play-circle',
						'library' => 'fa-solid',
					),
					'render_type'      => 'template',
					'conditions'       => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				)
			);
		} else {
			$this->add_control(
				'youtube_play_icon',
				array(
					'label'      => __( 'Select Icon', 'uael' ),
					'type'       => Controls_Manager::ICON,
					'default'    => 'fa fa-play-circle',
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				)
			);
		}

			$this->add_control(
				'yplay_size',
				array(
					'label'      => __( 'Icon Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'default'    => array(
						'size' => 72,
					),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 200,
						),
					),
					'selectors'  => array(
						'.uamodal-{{ID}} .play'        => 'width: {{SIZE}}px; height: {{SIZE}}px;',
						'.uamodal-{{ID}} .play:before' => 'font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				)
			);

			$this->add_control(
				'yplay_color',
				array(
					'label'      => __( 'Icon Color', 'uael' ),
					'type'       => Controls_Manager::COLOR,
					'default'    => 'rgba( 0,0,0,0.8 )',
					'selectors'  => array(
						'.uamodal-{{ID}} .play:before' => 'color: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'video_autoplay',
								'operator' => '!=',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				)
			);

			$this->add_control(
				'loader_color',
				array(
					'label'      => __( 'Iframe Loader Color', 'uael' ),
					'type'       => Controls_Manager::COLOR,
					'default'    => 'rgba( 0,0,0,0.8 )',
					'selectors'  => array(
						'.uamodal-{{ID}} .uael-loader::before' => 'border: 3px solid {{VALUE}}; border-left-color: transparent;border-right-color: transparent;',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'async_iframe',
								'operator' => '==',
								'value'    => 'yes',
							),
							array(
								'name'     => 'content_type',
								'operator' => '==',
								'value'    => 'iframe',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'content_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector'  => '.uamodal-{{ID}} .uael-content .uael-text-editor',
					'separator' => 'before',
					'condition' => array(
						'content_type' => 'content',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_button_style_controls() {

		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Button', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'modal_on' => 'button',
				),
			)
		);

			$this->add_control(
				'btn_html_message',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => sprintf( '<p style="font-size: 11px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'To see these changes please turn off the preview setting from Content Tab.', 'uael' ) ),
					'condition' => array(
						'preview_modal' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'btn_typography',
					'label'     => __( 'Typography', 'uael' ),
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector'  => '{{WRAPPER}} .uael-modal-action-wrap a.elementor-button, {{WRAPPER}} .uael-modal-action-wrap .elementor-button',
					'condition' => array(
						'modal_on' => 'button',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_button_style' );

				$this->start_controls_tab(
					'tab_button_normal',
					array(
						'label'     => __( 'Normal', 'uael' ),
						'condition' => array(
							'modal_on' => 'button',
						),
					)
				);

					$this->add_control(
						'button_text_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .uael-modal-action-wrap a.elementor-button, {{WRAPPER}} .uael-modal-action-wrap .elementor-button' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'modal_on' => 'button',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'           => 'btn_background_color',
							'label'          => __( 'Background Color', 'uael' ),
							'types'          => array( 'classic', 'gradient' ),
							'selector'       => '{{WRAPPER}} .uael-modal-action-wrap .elementor-button',
							'separator'      => 'before',
							'condition'      => array(
								'modal_on' => 'button',
							),
							'fields_options' => array(
								'color' => array(
									'global' => array(
										'default' => Global_Colors::COLOR_ACCENT,
									),
								),
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_button_hover',
					array(
						'label'     => __( 'Hover', 'uael' ),
						'condition' => array(
							'modal_on' => 'button',
						),
					)
				);

					$this->add_control(
						'btn_hover_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-modal-action-wrap a.elementor-button:hover, {{WRAPPER}} .uael-modal-action-wrap .elementor-button:hover' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'modal_on' => 'button',
							),
						)
					);

					$this->add_control(
						'button_background_hover_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_ACCENT,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-modal-action-wrap a.elementor-button:hover, {{WRAPPER}} .uael-modal-action-wrap .elementor-button:hover' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'modal_on' => 'button',
							),
						)
					);

					$this->add_control(
						'button_hover_border_color',
						array(
							'label'     => __( 'Border Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'condition' => array(
								'border_border!' => '',
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-modal-action-wrap a.elementor-button:hover, {{WRAPPER}} .uael-modal-action-wrap .elementor-button:hover' => 'border-color: {{VALUE}};',
							),
							'condition' => array(
								'modal_on' => 'button',
							),
						)
					);

					$this->add_control(
						'btn_hover_animation',
						array(
							'label'     => __( 'Hover Animation', 'uael' ),
							'type'      => Controls_Manager::HOVER_ANIMATION,
							'condition' => array(
								'modal_on' => 'button',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'        => 'btn_border',
					'label'       => __( 'Border', 'uael' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .uael-modal-action-wrap .elementor-button',
					'separator'   => 'before',
					'condition'   => array(
						'modal_on' => 'button',
					),
				)
			);

			$this->add_control(
				'btn_border_radius',
				array(
					'label'      => __( 'Border Radius', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .uael-modal-action-wrap a.elementor-button, {{WRAPPER}} .uael-modal-action-wrap .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'modal_on' => 'button',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'button_box_shadow',
					'selector'  => '{{WRAPPER}} .uael-modal-action-wrap .elementor-button',
					'condition' => array(
						'modal_on' => 'button',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Modal Popup Title Style Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_cta_style_controls() {

		$this->start_controls_section(
			'section_cta_style',
			array(
				'label'     => __( 'Display Text', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'modal_on' => 'text',
				),
			)
		);

			$this->add_control(
				'text_html_message',
				array(
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => sprintf( '<p style="font-size: 11px;font-style: italic;line-height: 1.4;color: #a4afb7;">%s</p>', __( 'To see these changes please turn off the preview setting from Content Tab.', 'uael' ) ),
					'condition' => array(
						'preview_modal' => 'yes',
					),
				)
			);

			$this->add_control(
				'text_color',
				array(
					'label'     => __( 'Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'modal_on' => 'text',
					),
				)
			);

			$this->add_control(
				'text_hover_color',
				array(
					'label'     => __( 'Text Hover Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-modal-action:hover' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'modal_on' => 'text',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'cta_text_typography',
					'label'     => __( 'Typography', 'uael' ),
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'selector'  => '{{WRAPPER}} .uael-modal-action-wrap .uael-modal-action',
					'condition' => array(
						'modal_on' => 'text',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 0.0.1
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
				'help_doc_5',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=kXuXfaetch4&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc&index=14" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Trigger Modal Popup on the click of menu » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-trigger-a-modal-popup-on-the-click-of-a-menu-element/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Close Modal Popup on click of button or link » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/various-options-to-close-a-modal-popup-in-uael/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Trigger Modal Popup from another widget » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-open-a-modal-popup-from-another-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_4',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Modal Popup JS Triggers » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/modal-popup-js-triggers/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}
	/**
	 * Render content type list.
	 *
	 * @since 0.0.1
	 * @return array Array of content type
	 * @access public
	 */
	public function get_content_type() {

		$content_type = array(
			'content'              => __( 'Content', 'uael' ),
			'photo'                => __( 'Photo', 'uael' ),
			'video'                => __( 'Video Embed Code', 'uael' ),
			'saved_rows'           => __( 'Saved Section', 'uael' ),
			'saved_container'      => __( 'Saved Container', 'uael' ),
			'saved_page_templates' => __( 'Saved Page', 'uael' ),
			'youtube'              => __( 'YouTube', 'uael' ),
			'vimeo'                => __( 'Vimeo', 'uael' ),
			'iframe'               => __( 'Iframe', 'uael' ),
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$content_type['saved_modules'] = __( 'Saved Widget', 'uael' );
		}

		return $content_type;
	}

	/**
	 * Render button widget classes names.
	 *
	 * @since 0.0.1
	 * @param array $settings The settings array.
	 * @param int   $node_id The node id.
	 * @return string Concatenated string of classes
	 * @access public
	 */
	public function get_modal_content( $settings, $node_id ) {

		$content_type     = $settings['content_type'];
		$dynamic_settings = $this->get_settings_for_display();
		$output_html;

		switch ( $content_type ) {
			case 'content':
				global $wp_embed;
				$output_html = '<div class="uael-text-editor elementor-inline-editing" data-elementor-setting-key="ct_content" data-elementor-inline-editing-toolbar="advanced">' . wpautop( $wp_embed->autoembed( $dynamic_settings['ct_content'] ) ) . '</div>';
				break;
			case 'photo':
				if ( isset( $dynamic_settings['ct_photo']['url'] ) ) {

					$output_html = '<img src="' . $dynamic_settings['ct_photo']['url'] . '" alt="' . Control_Media::get_image_alt( $dynamic_settings['ct_photo'] ) . '" />';
				} else {
					$output_html = '<img src="" alt="" />';
				}
				break;

			case 'video':
				global $wp_embed; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration
				$output_html = $wp_embed->autoembed( $dynamic_settings['ct_video'] );
				break;
			case 'iframe':
				if ( 'yes' === $dynamic_settings['async_iframe'] ) {

					$output_html = '<div class="uael-modal-content-type-iframe" data-src="' . $dynamic_settings['iframe_url'] . '" frameborder="0" allowfullscreen></div>';
				} else {
					$output_html = '<iframe src="' . $dynamic_settings['iframe_url'] . '" class="uael-content-iframe" frameborder="0" width="100%" height="100%" allowfullscreen></iframe>';
				}
				break;
			case 'saved_rows':
				$output_html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( apply_filters( 'wpml_object_id', $settings['ct_saved_rows'], 'page' ) );
				break;
			case 'saved_container':
				$output_html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( apply_filters( 'wpml_object_id', $settings['ct_saved_container'], 'page' ) );
				break;
			case 'saved_modules':
				$output_html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['ct_saved_modules'] );
				break;
			case 'saved_page_templates':
				$output_html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['ct_page_templates'] );
				break;
			case 'youtube': // Continue in both cases.
			case 'vimeo':
				$output_html = $this->get_video_embed( $dynamic_settings, $node_id );
				break;
			default:
				break;
		}

		return $output_html;
	}

	/**
	 * Render Video.
	 *
	 * @since 0.0.1
	 * @param array $settings The settings array.
	 * @param int   $node_id The node id.
	 * @return string Concatenated string of html
	 * @access public
	 */
	public function get_video_embed( $settings, $node_id ) {

		if ( '' === $settings['video_url'] ) {
			return '';
		}

		$url    = apply_filters( 'uael_modal_video_url', $settings['video_url'], $settings );
		$vid_id = '';
		$html   = '<div class="uael-video-wrap">';
		$thumb  = '';

		$embed_param = $this->get_embed_params();
		$video_data  = $this->get_url( $embed_param, $node_id );

		$params = array();

		$play_icon = '';
		if ( 'youtube' === $settings['content_type'] ) {
			if ( UAEL_Helper::is_elementor_updated() ) {

				$youtube_migrated = isset( $settings['__fa4_migrated']['new_youtube_play_icon'] );
				$youtube_is_new   = ! isset( $settings['youtube_play_icon'] );

				if ( $youtube_is_new || $youtube_migrated ) {
					$play_icon = isset( $settings['new_youtube_play_icon']['value'] ) ? $settings['new_youtube_play_icon']['value'] : '';
				} else {
					$play_icon = $settings['youtube_play_icon'];
				}
			} else {
				$play_icon = $settings['youtube_play_icon'];
			}
		} else {

			if ( UAEL_Helper::is_elementor_updated() ) {

				$vimeo_migrated = isset( $settings['__fa4_migrated']['new_vimeo_play_icon'] );
				$vimeo_is_new   = ! isset( $settings['vimeo_play_icon'] );

				if ( ( $vimeo_is_new || $vimeo_migrated ) && isset( $settings['new_vimeo_play_icon'] ) ) {
					$play_icon = $settings['new_vimeo_play_icon']['value'];
				} elseif ( isset( $settings['vimeo_play_icon'] ) ) {
					$play_icon = $settings['vimeo_play_icon'];
				}
			} else {
				$play_icon = $settings['vimeo_play_icon'];
			}
		}

		if ( 'youtube' === $settings['content_type'] ) {

			if ( preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches ) ) {
				$vid_id = $matches[1];
			}

			$thumb = 'https://i.ytimg.com/vi/' . $vid_id . '/hqdefault.jpg';

			$html .= '<div class="uael-modal-iframe uael-video-player" data-src="youtube" data-id="' . $vid_id . '" data-thumb="' . $thumb . '" data-sourcelink="https://www.youtube.com/embed/' . $vid_id . $video_data . '" data-play-icon="' . $play_icon . '"></div>';

		} elseif ( 'vimeo' === $settings['content_type'] ) {

			if ( preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs ) ) {
				$vid_id = $regs[3];
			}

			$vid_id_image = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url, '/' ) );

			if ( '' !== $vid_id_image && 0 !== $vid_id_image ) {

				$response = wp_remote_get( "https://vimeo.com/api/v2/video/$vid_id_image.php" );

				if ( is_wp_error( $response ) ) {
					return;
				}
				$body  = wp_remote_retrieve_body( $response );
				$vimeo = json_decode( $body, true );

				if ( is_array( $vimeo ) && isset( $vimeo[0]['thumbnail_large'] ) ) {
					$thumb = $vimeo[0]['thumbnail_large'];
				}
			}

			if ( '' !== $vid_id && 0 !== $vid_id ) {
				$html .= '<div class="uael-modal-iframe uael-video-player" data-src="vimeo" data-id="' . $vid_id . '" data-thumb="' . $thumb . '" data-sourcelink="https://player.vimeo.com/video/' . $vid_id . $video_data . '" data-play-icon="' . $play_icon . '" ></div>';
			}
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * Render Button.
	 *
	 * @since 0.0.1
	 * @param int   $node_id The node id.
	 * @param array $settings The settings array.
	 * @access public
	 */
	public function render_button( $node_id, $settings ) {

		$this->add_render_attribute( 'wrapper', 'class', 'uael-button-wrapper elementor-button-wrapper' );
		$this->add_render_attribute( 'button', 'href', 'javascript:void(0);' );
		$this->add_render_attribute( 'button', 'class', 'uael-trigger elementor-button-link elementor-button elementor-clickable' );

		if ( ! empty( $settings['btn_size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['btn_size'] );
		}

		if ( ! empty( $settings['btn_align'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-align-' . $settings['btn_align'] );
		}
		if ( ! empty( $settings['btn_align_tablet'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-tablet-align-' . $settings['btn_align_tablet'] );
		}
		if ( ! empty( $settings['btn_align_mobile'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-mobile-align-' . $settings['btn_align_mobile'] );
		}

		if ( $settings['btn_hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['btn_hover_animation'] );
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?> data-modal="<?php echo esc_attr( $node_id ); ?>">
				<?php $this->render_button_text(); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render button text.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_button_text() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
		$this->add_render_attribute(
			'icon-align',
			'class',
			array(
				'elementor-align-icon-' . $settings['btn_icon_align'],
				'elementor-button-icon',
			)
		);

		$this->add_render_attribute(
			'btn-text',
			array(
				'class'                                 => 'elementor-button-text elementor-inline-editing',
				'data-elementor-setting-key'            => 'btn_text',
				'data-elementor-inline-editing-toolbar' => 'none',
			)
		);

		?>
		<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-wrapper' ) ); ?>>

			<?php
			if ( UAEL_Helper::is_elementor_updated() ) {

				$button_migrated = isset( $settings['__fa4_migrated']['new_btn_icon'] );
				$button_is_new   = ! isset( $settings['btn_icon'] );
				?>
				<?php if ( ! empty( $settings['btn_icon'] ) || ! empty( $settings['new_btn_icon'] ) ) : ?>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align' ) ); ?>>
						<?php
						if ( $button_is_new || $button_migrated ) {
							\Elementor\Icons_Manager::render_icon( $settings['new_btn_icon'], array( 'aria-hidden' => 'true' ) );
						} elseif ( ! empty( $settings['btn_icon'] ) ) {
							?>
							<i class="<?php echo esc_attr( $settings['btn_icon'] ); ?>" aria-hidden="true"></i>
						<?php } ?>
					</span>
				<?php endif; ?>
				<?php
			} elseif ( ! empty( $settings['btn_icon'] ) ) {
				?>
				<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-align' ) ); ?>>
					<i class="<?php echo esc_attr( $settings['btn_icon'] ); ?>" aria-hidden="true"></i>
				</span>
			<?php } ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'btn-text' ) ); ?> ><?php echo wp_kses_post( $this->get_settings_for_display( 'btn_text' ) ); ?></span>
		</span>
		<?php
	}

	/**
	 * Render close image/icon.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_close_icon() {

		$settings = $this->get_settings_for_display();
		?>

		<span class="uael-modal-close uael-close-icon elementor-clickable uael-close-custom-<?php echo esc_attr( $settings['icon_position'] ); ?>" >
		<?php
		if ( 'icon' === $settings['close_source'] ) {
			if ( UAEL_Helper::is_elementor_updated() ) {
				$close_migrated = isset( $settings['__fa4_migrated']['new_close_icon'] );
				$close_is_new   = ! isset( $settings['close_icon'] );

				if ( $close_is_new || $close_migrated ) {
					\Elementor\Icons_Manager::render_icon( $settings['new_close_icon'], array( 'aria-hidden' => 'true' ) );
				} elseif ( ! empty( $settings['close_icon'] ) ) {
					?>
						<i class="<?php echo esc_attr( $settings['close_icon'] ); ?>" aria-hidden="true"></i>
					<?php
				}
			} elseif ( ! empty( $settings['close_icon'] ) ) {
				?>
				<i class="<?php echo esc_attr( $settings['close_icon'] ); ?>" aria-hidden="true"></i>
				<?php
			}
		} else {
			?>
			<img class="uael-close-image" src="<?php echo ( isset( $settings['close_photo']['url'] ) ) ? esc_url( $settings['close_photo']['url'] ) : ''; ?>" alt="<?php echo ( isset( $settings['close_photo']['url'] ) ) ? wp_kses_post( Control_Media::get_image_alt( $settings['close_photo'] ) ) : ''; ?>"/>
			<?php
		}
		?>
		</span>
		<?php
	}

	/**
	 * Render action HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_action_html() {

		$settings = $this->get_settings_for_display();

		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( 'button' === $settings['modal_on'] ) {

			$this->render_button( $this->get_id(), $settings );

		} elseif (
			(
				'custom' === $settings['modal_on'] ||
				'custom_id' === $settings['modal_on'] ||
				'automatic' === $settings['modal_on'] ||
				'via_url' === $settings['modal_on']
			) &&
			$is_editor
		) {

			?>
			<div class="uael-builder-msg" style="text-align: center;">
				<h5><?php esc_html_e( 'Modal Popup - ID ', 'uael' ); ?><?php echo esc_html( $this->get_id() ); ?></h5>
				<p><?php esc_html_e( 'Click here to edit the "Modal Popup" settings. This text will not be visible on frontend.', 'uael' ); ?></p>
			</div>
			<?php

		} else {

			$inner_html = '';

			$this->add_render_attribute(
				'action-wrap',
				'class',
				array(
					'uael-modal-action',
					'elementor-clickable',
					'uael-trigger',
				)
			);

			if ( 'custom' === $settings['modal_on'] ||
				'custom_id' === $settings['modal_on'] ||
				'automatic' === $settings['modal_on'] ||
				'via_url' === $settings['modal_on']
			) {
				$this->add_render_attribute( 'action-wrap', 'class', 'uael-modal-popup-hide' );
			}

			$this->add_render_attribute( 'action-wrap', 'data-modal', $this->get_id() );

			switch ( $settings['modal_on'] ) {
				case 'text':
					$this->add_render_attribute(
						'action-wrap',
						array(
							'data-elementor-setting-key' => 'modal_text',
							'data-elementor-inline-editing-toolbar' => 'basic',
						)
					);

					$this->add_render_attribute( 'action-wrap', 'class', 'elementor-inline-editing' );

					$inner_html = $settings['modal_text'];

					break;

				case 'icon':
					$this->add_render_attribute( 'action-wrap', 'class', 'uael-modal-icon-wrap uael-modal-icon' );

					if ( UAEL_Helper::is_elementor_updated() ) {

						$icon_migrated = isset( $settings['__fa4_migrated']['new_icon'] );
						$icon_is_new   = ! isset( $settings['icon'] );
						if ( $icon_is_new || $icon_migrated ) {
							ob_start();
							\Elementor\Icons_Manager::render_icon( $settings['new_icon'], array( 'aria-hidden' => 'true' ) );
							$inner_html = ob_get_clean();
						} elseif ( ! empty( $settings['icon'] ) ) {
							$inner_html = '<i class="' . $settings['icon'] . '" aria-hidden="true"></i>';
						}
					} elseif ( ! empty( $settings['icon'] ) ) {
						$inner_html = '<i class="' . $settings['icon'] . '" aria-hidden="true"></i>';
					}

					break;

				case 'photo':
					$this->add_render_attribute( 'action-wrap', 'class', 'uael-modal-photo-wrap' );

					$url = ( isset( $settings['photo']['url'] ) && ! empty( $settings['photo']['url'] ) ) ? $settings['photo']['url'] : '';

					$inner_html = '<img class="uael-modal-photo" src="' . $url . '" alt="' . Control_Media::get_image_alt( $settings['photo'] ) . '">';

					break;
			}
			?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'action-wrap' ) ); ?>>
				<?php echo $inner_html;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php
		}
	}

	/**
	 * Get Data Attributes.
	 *
	 * @since 0.0.1
	 * @param array $settings The settings array.
	 * @return string Data Attributes
	 * @access public
	 */
	public function get_parent_wrapper_attributes( $settings ) {

		$this->add_render_attribute(
			'parent-wrapper',
			array(
				'id'                    => $this->get_id() . '-overlay',
				'data-trigger-on'       => $settings['modal_on'],
				'data-close-on-esc'     => $settings['esc_keypress'],
				'data-close-on-overlay' => $settings['overlay_click'],
				'data-exit-intent'      => $settings['exit_intent'],
				'data-after-sec'        => $settings['after_second'],
				'data-after-sec-val'    => $settings['after_second_value']['size'],
				'data-cookies'          => $settings['enable_cookies'],
				'data-cookies-days'     => $settings['close_cookie_days']['size'],
				'data-cookies-type'     => $settings['set_cookie_on'],
				'data-custom'           => $settings['modal_custom'],
				'data-custom-id'        => $this->get_settings_for_display( 'modal_custom_id' ),
				'data-content'          => $settings['content_type'],
				'data-autoplay'         => $settings['video_autoplay'],
				'data-device'           => ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== ( stripos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'iPhone' ) ) ? 'true' : 'false' ), // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__
				'data-async'            => ( 'yes' === $settings['async_iframe'] ) ? true : false,
			)
		);

		$this->add_render_attribute(
			'parent-wrapper',
			'class',
			array(
				'uael-modal-parent-wrapper',
				'uael-module-content',
				'uamodal-' . $this->get_id(),
				'uael-aspect-ratio-' . $settings['video_ratio'],
				$settings['_css_classes'] . '-popup',
			)
		);

		return $this->get_render_attribute_string( 'parent-wrapper' );
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

		$settings = $this->get_settings();

		$params = array();

		if ( 'youtube' === $settings['content_type'] ) {
			$youtube_options = array( 'rel', 'controls', 'mute', 'modestbranding' );

			$params['version']     = 3;
			$params['enablejsapi'] = 1;

			$params['autoplay'] = ( 'yes' === $settings['video_autoplay'] ) ? 1 : 0;

			foreach ( $youtube_options as $option ) {

				if ( 'rel' === $option ) {
					$params[ $option ] = ( 'yes' === $settings['youtube_related_videos'] ) ? 1 : 0;
					continue;
				}

				if ( 'controls' === $option ) {
					if ( 'yes' === $settings['youtube_player_controls'] ) {
						$params[ $option ] = 0;
					}
					continue;
				}

				if ( 'yes' === $settings['video_controls_adv'] ) {
					$value             = ( 'yes' === $settings[ 'yt_' . $option ] ) ? 1 : 0;
					$params[ $option ] = $value;
					$params['start']   = $settings['start'];
					$params['end']     = $settings['end'];
				}
			}
		}

		if ( 'vimeo' === $settings['content_type'] ) {

			if ( 'yes' === $settings['video_controls_adv'] && 'yes' === $settings['vimeo_loop'] ) {
				$params['loop'] = 1;
			}

			$params['title']    = 0;
			$params['byline']   = 0;
			$params['portrait'] = 0;
			$params['badge']    = 0;

			if ( 'yes' === $settings['video_autoplay'] ) {
				$params['autoplay'] = 1;
				$params['muted']    = 1;
			} else {
				$params['autoplay'] = 0;
			}

			/**
			 * Support Vimeo unlisted and private videos
			 */
			$h_param   = array();
			$video_url = $settings['video_url'];
			preg_match( '/(?|(?:[\?|\&]h={1})([\w]+)|\d\/([\w]+))/', $video_url, $h_param );

			if ( ! empty( $h_param ) ) {
				$params['h'] = $h_param[1];
			}
		}
		return $params;
	}

	/**
	 * Returns Video URL.
	 *
	 * @param array  $params Video Param array.
	 * @param string $node_id Video ID.
	 * @since 1.3.2
	 * @access public
	 */
	public function get_url( $params, $node_id ) {

		$settings = $this->get_settings_for_display();
		$url      = '';

		$url = add_query_arg( $params, $url );

		$url .= ( empty( $params ) ) ? '?' : '&';

		if ( 'vimeo' === $settings['content_type'] ) {

			if ( 'yes' === $settings['video_controls_adv'] ) {
				if ( '' !== $settings['start'] ) {

					$time = gmdate( 'H\hi\ms\s', $settings['start'] );
					$url .= '#t=' . $time;
				}
			}
		}

		return $url;
	}

	/**
	 * Render Modal Popup output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings  = $this->get_settings();
		$node_id   = $this->get_id();
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		$this->add_inline_editing_attributes( 'ct_content', 'advanced' );
		$this->add_inline_editing_attributes( 'title', 'basic' );
		$this->add_inline_editing_attributes( 'modal_text', 'basic' );
		$this->add_inline_editing_attributes( 'btn_text', 'none' );
		$title_tag = UAEL_Helper::validate_html_tag( $settings['title_tag'] );

		ob_start();
		include UAEL_MODULES_DIR . 'modal-popup/widgets/template.php';
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render Modal Popup output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {}
}
