<?php
/**
 * UAEL ImageGallery.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\ImageGallery\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Control_Media;

// UltimateElementor Classes.
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class ImageGallery.
 */
class Image_Gallery extends Common_Widget {

	/**
	 * Retrieve ImageGallery Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Image_Gallery' );
	}

	/**
	 * Retrieve ImageGallery Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Image_Gallery' );
	}

	/**
	 * Retrieve ImageGallery Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Image_Gallery' );
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
		return parent::get_widget_keywords( 'Image_Gallery' );
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
		return array(
			'uael-isotope',
			'imagesloaded',
			'uael-slick',
			'uael-element-resize',
			'uael-frontend-script',
			'uael-fancybox',
			'uael-justified',
		);
	}

	/**
	 * Image filter options.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param boolean $inherit if inherit option required.
	 * @return array Filters.
	 */
	protected function filter_options( $inherit = false ) {

		$inherit_ops = array();

		if ( $inherit ) {
			$inherit_ops = array(
				'' => __( 'Inherit', 'uael' ),
			);
		}

		$filter = array(
			'normal'    => __( 'Normal', 'uael' ),
			'a-1977'    => __( '1977', 'uael' ),
			'aden'      => __( 'Aden', 'uael' ),
			'earlybird' => __( 'Earlybird', 'uael' ),
			'hudson'    => __( 'Hudson', 'uael' ),
			'inkwell'   => __( 'Inkwell', 'uael' ),
			'perpetua'  => __( 'Perpetua', 'uael' ),
			'poprocket' => __( 'Poprocket', 'uael' ),
			'sutro'     => __( 'Sutro', 'uael' ),
			'toaster'   => __( 'Toaster', 'uael' ),
			'willow'    => __( 'Willow', 'uael' ),
		);

		return array_merge( $inherit_ops, $filter );
	}

	/**
	 * Register ImageGallery controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_content_image_controls();
		$this->register_content_grid_controls();
		$this->register_content_slider_controls();
		$this->register_content_general_controls();
		$this->register_content_lightbox_controls();

		/* Style */
		$this->register_style_layout_controls();
		$this->register_style_thumbnail_controls();
		$this->register_style_caption_controls();
		$this->register_style_navigation_controls();
		$this->register_style_cat_filters_controls();

		$this->register_helpful_information();
	}

	/**
	 * Register ImageGallery General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_image_controls() {

		$this->start_controls_section(
			'section_content_images',
			array(
				'label' => __( 'Gallery', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'gallery_style',
				array(
					'label'     => __( 'Layout', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'grid',
					'options'   => array(
						'grid'      => __( 'Grid', 'uael' ),
						'masonry'   => __( 'Masonry', 'uael' ),
						'justified' => __( 'Justified', 'uael' ),
						'carousel'  => __( 'Carousel', 'uael' ),
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'wp_gallery',
				array(
					'label'   => '',
					'type'    => Controls_Manager::GALLERY,
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			$gallery = new Repeater();

			$gallery->add_control(
				'image',
				array(
					'label'   => __( 'Choose Image', 'uael' ),
					'type'    => Controls_Manager::MEDIA,
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register ImageGallery General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_grid_controls() {
		$this->start_controls_section(
			'section_content_grid',
			array(
				'label'     => __( 'Grid / Masonry / Justified', 'uael' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'gallery_style' => array( 'grid', 'masonry', 'justified' ),
				),
			)
		);
			$this->add_responsive_control(
				'gallery_columns',
				array(
					'label'              => __( 'Columns', 'uael' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => '4',
					'tablet_default'     => '3',
					'mobile_default'     => '2',
					'options'            => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class'       => 'uael-img-grid%s__column-',
					'condition'          => array(
						'gallery_style!' => array( 'justified', 'carousel' ),
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'justified_row_height',
				array(
					'label'     => __( 'Row Height', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 120,
					),
					'range'     => array(
						'px' => array(
							'min' => 50,
							'max' => 500,
						),
					),
					'condition' => array(
						'gallery_style' => 'justified',
					),
				)
			);

			$this->add_control(
				'last_row',
				array(
					'label'     => __( 'Last Row Layout', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'nojustify',
					'options'   => array(
						'nojustify' => __( 'No Justify', 'uael' ),
						'justify'   => __( 'Justify', 'uael' ),
						'hide'      => __( 'Hide', 'uael' ),
					),
					'condition' => array(
						'gallery_style' => 'justified',
					),
				)
			);

			$this->add_control(
				'masonry_filters_enable',
				array(
					'label'        => __( 'Filterable Image Gallery', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'condition'    => array(
						'gallery_style' => array( 'grid', 'masonry', 'justified' ),
					),
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'masonry_filters_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s admin link */
					'raw'             => sprintf( __( 'Learn : %1$s How to design filterable image gallery? %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-design-filterable-image-gallery/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);
		}

			$this->add_control(
				'filters_all_text',
				array(
					'label'     => __( '"All" Tab Label', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'All', 'uael' ),
					'condition' => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'default_filter_switch',
				array(
					'label'        => __( 'Default Tab on Page Load', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'label_off'    => __( 'First', 'uael' ),
					'label_on'     => __( 'Custom', 'uael' ),
					'condition'    => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);
			$this->add_control(
				'default_filter',
				array(
					'label'     => __( 'Enter Category Name', 'uael' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => '',
					'condition' => array(
						'default_filter_switch'  => 'yes',
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'default_filter_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s admin link */
					'raw'             => sprintf( __( 'Note: Enter the category name that you wish to set as a default on page load. Read %1$s this article %2$s for more information.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/display-specific-category-tab-as-a-default/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'default_filter_switch'  => 'yes',
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'tabs_dropdown',
			array(
				'label'        => __( 'Responsive Support', 'uael' ),
				'description'  => __( 'Enable this option to display Filterable Tabs in a Dropdown on Mobile.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
					'masonry_filters_enable' => 'yes',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Register Slider Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_slider_controls() {
		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Carousel', 'uael' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'              => __( 'Images to Show', 'uael' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 4,
				'tablet_default'     => 3,
				'mobile_default'     => 2,
				'condition'          => array(
					'gallery_style' => 'carousel',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'              => __( 'Images to Scroll', 'uael' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'condition'          => array(
					'gallery_style' => 'carousel',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Speed (ms)', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array(
					'autoplay'      => 'yes',
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => __( 'Pause on Hover', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'autoplay'      => 'yes',
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => __( 'Infinite Loop', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'transition_speed',
			array(
				'label'     => __( 'Transition Speed (ms)', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'     => __( 'Navigation', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => array(
					'both'   => __( 'Arrows and Dots', 'uael' ),
					'arrows' => __( 'Arrows', 'uael' ),
					'dots'   => __( 'Dots', 'uael' ),
					'none'   => __( 'None', 'uael' ),
				),
				'condition' => array(
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register ImageGallery General Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_content_general_controls() {

		$this->start_controls_section(
			'section_content_general',
			array(
				'label' => __( 'Additional Options', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'image',
					'label'   => __( 'Image Size', 'uael' ),
					'default' => 'medium',
				)
			);
			$this->add_control(
				'click_action',
				array(
					'label'   => __( 'Click Action', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'file',
					'options' => array(
						'lightbox'   => __( 'Lightbox', 'uael' ),
						'file'       => __( 'Media File', 'uael' ),
						'attachment' => __( 'Attachment Page', 'uael' ),
						'custom'     => __( 'Custom Link', 'uael' ),
						''           => __( 'None', 'uael' ),
					),
				)
			);
		if ( parent::is_internal_links() ) {
			$this->add_control(
				'click_action_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s admin link */
					'raw'             => sprintf( __( 'Learn : %1$s How to assign custom link for images? %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-set-a-custom-link-for-the-image/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'click_action' => 'custom',
					),
				)
			);
		}
			$this->add_control(
				'link_target',
				array(
					'label'     => __( 'Link Target', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '_blank',
					'options'   => array(
						'_self'  => __( 'Same Window', 'uael' ),
						'_blank' => __( 'New Window', 'uael' ),
					),
					'condition' => array(
						'click_action' => array( 'file', 'attachment', 'custom' ),
					),
				)
			);
			$this->add_control(
				'gallery_rand',
				array(
					'label'   => __( 'Ordering', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						''     => __( 'Default', 'uael' ),
						'rand' => __( 'Random', 'uael' ),
					),
					'default' => '',
				)
			);
			$this->add_control(
				'gallery_caption',
				array(
					'label'   => __( 'Show Caption', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						''         => __( 'Never', 'uael' ),
						'on-image' => __( 'On Image', 'uael' ),
						'on-hover' => __( 'On Hover', 'uael' ),
					),
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'caption_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s admin link */
					'raw'             => sprintf( __( 'Learn : %1$s How to assign captions for images? %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-a-caption-for-the-image/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'gallery_caption!' => '',
					),
				)
			);
		}

		$this->add_control(
			'clickable_caption',
			array(
				'label'        => __( 'Clickable Caption', 'uael' ),
				'description'  => __( 'Enable this option to make the captions clickable.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'render_type'  => 'template',
				'condition'    => array(
					'click_action!'    => '',
					'gallery_caption!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Image Gallery Lightbox Controls.
	 *
	 * @since 1.17.0
	 * @access protected
	 */
	protected function register_content_lightbox_controls() {

		$this->start_controls_section(
			'section_lightbox_layout',
			array(
				'label'     => __( 'Lightbox', 'uael' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'click_action' => 'lightbox',
				),
			)
		);

			$this->add_control(
				'lightbox_actions',
				array(
					'label'       => __( 'Lightbox Actions', 'uael' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => array(
						'zoom'       => __( 'Zoom', 'uael' ),
						'share'      => __( 'Social Share', 'uael' ),
						'slideShow'  => __( 'Slideshow', 'uael' ),
						'fullScreen' => __( 'Full Screen', 'uael' ),
						'download'   => __( 'Download', 'uael' ),
						'thumbs'     => __( 'Gallery', 'uael' ),
					),
					'label_block' => true,
					'render_type' => 'template',
					'multiple'    => true,
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'lightbox_link_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click %1$s here %2$s to learn more about this.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/image-gallery-widget/#advanced-lightbox-actions/" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);
		}

			$this->add_control(
				'show_caption_lightbox',
				array(
					'label'        => __( 'Show Caption Below Image', 'uael' ),
					'description'  => __( 'Enable this option to display the caption under the image in Lightbox.', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'render_type'  => 'template',
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'lightbox_caption_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s admin link */
					'raw'             => sprintf( __( 'Learn : %1$s How to assign captions for images? %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-add-a-caption-for-the-image/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'show_caption_lightbox' => 'yes',
					),
				)
			);
		}

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'lightbox_typography',
					'label'     => __( 'Typography', 'uael' ),
					'selector'  => '.uael-fancybox-gallery-{{ID}} .fancybox-caption',
					'condition' => array(
						'show_caption_lightbox' => 'yes',
					),
				)
			);

			$this->add_control(
				'lightbox_text_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'.uael-fancybox-gallery-{{ID}} .fancybox-caption,
						.uael-fancybox-gallery-{{ID}} .fancybox-caption a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'show_caption_lightbox' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'lightbox_margin_bottom',
				array(
					'label'              => __( 'Caption Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'          => array(
						'.uael-fancybox-gallery-{{ID}} .fancybox-caption' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'          => array(
						'show_caption_lightbox' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'lightbox_loop',
				array(
					'label'        => __( 'Loop', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'YES', 'uael' ),
					'label_off'    => __( 'NO', 'uael' ),
					'description'  => __( 'Enable infinite gallery navigation.', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
					'condition'    => array(
						'click_action' => 'lightbox',
					),
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Style Tab
	 */
	/**
	 * Register Layout Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => __( 'Layout', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'              => __( 'Columns Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 20,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition'          => array(
					'gallery_style!' => 'justified',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-img-gallery-wrap .uael-grid-item' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .uael-img-gallery-wrap' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'              => __( 'Rows Gap', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 20,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-img-gallery-wrap .uael-grid-item-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'gallery_style' => array( 'grid', 'masonry' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'justified_margin',
			array(
				'label'              => __( 'Image Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 3,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'condition'          => array(
					'gallery_style' => 'justified',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-img-justified-wrap .uael-grid-item-content' => 'margin: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'images_valign',
			array(
				'label'              => __( 'Image Vertical</br>Alignment', 'uael' ),
				'type'               => Controls_Manager::CHOOSE,
				'default'            => 'flex-start',
				'options'            => array(
					'flex-start' => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => __( 'Middle', 'uael' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'uael' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'condition'          => array(
					'gallery_style' => 'grid',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-img-gallery-wrap .uael-grid-item' => 'align-items: {{VALUE}}; display: inline-grid;',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Thumbnail Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_thumbnail_controls() {
		$this->start_controls_section(
			'section_design_thumbnail',
			array(
				'label' => __( 'Thumbnail', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->start_controls_tabs( 'thumb_style' );

				$this->start_controls_tab(
					'thumb_style_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);

					$this->add_control(
						'image_scale',
						array(
							'label'     => __( 'Scale', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'range'     => array(
								'px' => array(
									'min'  => 1,
									'max'  => 2,
									'step' => 0.01,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-grid-img-thumbnail img' => 'transform: scale({{SIZE}});',
							),
						)
					);

					$this->add_control(
						'image_opacity',
						array(
							'label'     => __( 'Opacity (%)', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array(
								'size' => 1,
							),
							'range'     => array(
								'px' => array(
									'max'  => 1,
									'min'  => 0,
									'step' => 0.01,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-grid-img-thumbnail img' => 'opacity: {{SIZE}}',
							),
						)
					);

					$this->add_control(
						'image_filter',
						array(
							'label'        => __( 'Image Effect', 'uael' ),
							'type'         => Controls_Manager::SELECT,
							'default'      => 'normal',
							'options'      => $this->filter_options(),
							'prefix_class' => 'uael-ins-',
						)
					);

					$this->add_control(
						'overlay_background_color',
						array(
							'label'     => __( 'Overlay Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-grid-img-overlay' => 'background-color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'image_style_hover',
					array(
						'label' => __( 'Hover', 'uael' ),
					)
				);

					$this->add_control(
						'image_scale_hover',
						array(
							'label'     => __( 'Scale', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'range'     => array(
								'px' => array(
									'min'  => 1,
									'max'  => 2,
									'step' => 0.01,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-grid-gallery-img:hover .uael-grid-img-thumbnail img' => 'transform: scale({{SIZE}});',
							),
						)
					);

					$this->add_control(
						'image_opacity_hover',
						array(
							'label'     => __( 'Opacity (%)', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array(
								'size' => 1,
							),
							'range'     => array(
								'px' => array(
									'max'  => 1,
									'min'  => 0,
									'step' => 0.01,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-grid-gallery-img:hover .uael-grid-img-thumbnail img' => 'opacity: {{SIZE}}',
							),
						)
					);

					$this->add_control(
						'image_filter_hover',
						array(
							'label'        => __( 'Image Effect', 'uael' ),
							'type'         => Controls_Manager::SELECT,
							'default'      => '',
							'options'      => $this->filter_options( true ),
							'prefix_class' => 'uael-ins-hover-',
						)
					);

					$this->add_control(
						'overlay_background_color_hover',
						array(
							'label'     => __( 'Overlay Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .uael-grid-gallery-img:hover .uael-grid-img-overlay' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'overlay_image_type',
						array(
							'label'   => __( 'Overlay Icon', 'uael' ),
							'type'    => Controls_Manager::CHOOSE,
							'options' => array(
								'photo' => array(
									'title' => __( 'Image', 'uael' ),
									'icon'  => 'fa fa-picture-o',
								),
								'icon'  => array(
									'title' => __( 'Font Icon', 'uael' ),
									'icon'  => 'fa fa-info-circle',
								),
							),
							'default' => '',
							'toggle'  => true,
						)
					);

		if ( UAEL_Helper::is_elementor_updated() ) {
			$this->add_control(
				'new_overlay_icon_hover',
				array(
					'label'            => __( 'Select Overlay Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'overlay_icon_hover',
					'default'          => array(
						'value'   => 'fa fa-search',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'overlay_image_type' => 'icon',
					),
				)
			);
		} else {
			$this->add_control(
				'overlay_icon_hover',
				array(
					'label'     => __( 'Select Overlay Icon', 'uael' ),
					'type'      => Controls_Manager::ICON,
					'default'   => 'fa fa-search',
					'condition' => array(
						'overlay_image_type' => 'icon',
					),
				)
			);
		}

					$this->add_control(
						'overlay_icon_color_hover',
						array(
							'label'      => __( 'Overlay Icon Color', 'uael' ),
							'type'       => Controls_Manager::COLOR,
							'conditions' => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'overlay_icon_hover' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'overlay_image_type',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
							'default'    => '#ffffff',
							'selectors'  => array(
								'{{WRAPPER}} .uael-grid-gallery-img .uael-grid-img-overlay i' => 'color: {{VALUE}};',
								'{{WRAPPER}} .uael-grid-gallery-img .uael-grid-img-overlay svg' => 'fill: {{VALUE}};',
							),
						)
					);

					$this->add_responsive_control(
						'overlay_icon_size_hover',
						array(
							'label'              => __( 'Overlay Icon Size', 'uael' ),
							'type'               => Controls_Manager::SLIDER,
							'size_units'         => array( 'px', 'em', 'rem' ),
							'range'              => array(
								'px' => array(
									'min' => 1,
									'max' => 200,
								),
							),
							'default'            => array(
								'size' => 40,
								'unit' => 'px',
							),
							'conditions'         => array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => UAEL_Helper::get_new_icon_name( 'overlay_icon_hover' ),
										'operator' => '!=',
										'value'    => '',
									),
									array(
										'name'     => 'overlay_image_type',
										'operator' => '==',
										'value'    => 'icon',
									),
								),
							),
							'selectors'          => array(
								'{{WRAPPER}} .uael-grid-gallery-img .uael-grid-img-overlay i,
								{{WRAPPER}} .uael-grid-gallery-img .uael-grid-img-overlay svg' => 'font-size: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
							),
							'frontend_available' => true,
						)
					);
					$this->add_control(
						'overlay_image_hover',
						array(
							'label'     => __( 'Overlay Image', 'uael' ),
							'type'      => Controls_Manager::MEDIA,
							'default'   => array(
								'url' => Utils::get_placeholder_image_src(),
							),
							'condition' => array(
								'overlay_image_type' => 'photo',
							),
						)
					);
					$this->add_responsive_control(
						'overlay_image_size_hover',
						array(
							'label'              => __( 'Overlay Image Width', 'uael' ),
							'type'               => Controls_Manager::SLIDER,
							'size_units'         => array( 'px', 'em', 'rem' ),
							'range'              => array(
								'px' => array(
									'min' => 1,
									'max' => 2000,
								),
							),
							'default'            => array(
								'size' => 50,
								'unit' => 'px',
							),
							'condition'          => array(
								'overlay_image_type' => 'photo',
							),
							'selectors'          => array(
								'{{WRAPPER}} .uael-grid-gallery-img .uael-grid-img-overlay img' => 'width: {{SIZE}}{{UNIT}};',
							),
							'frontend_available' => true,
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Layout Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_caption_controls() {
		$this->start_controls_section(
			'section_design_caption',
			array(
				'label'     => __( 'Caption', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'gallery_caption!' => '',
				),
			)
		);

			$this->add_control(
				'caption_alignment',
				array(
					'label'       => __( 'Text Alignment', 'uael' ),
					'type'        => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options'     => array(
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
					'default'     => 'center',
					'selectors'   => array(
						'{{WRAPPER}} .uael-img-gallery-wrap .uael-grid-img-caption' => 'text-align: {{VALUE}};',
					),
					'condition'   => array(
						'gallery_caption!' => '',
					),
				)
			);
			$this->add_control(
				'caption_valign',
				array(
					'label'        => __( 'Vertical Alignment', 'uael' ),
					'type'         => Controls_Manager::CHOOSE,
					'default'      => 'bottom',
					'options'      => array(
						'top'    => array(
							'title' => __( 'Top', 'uael' ),
							'icon'  => 'eicon-v-align-top',
						),
						'middle' => array(
							'title' => __( 'Middle', 'uael' ),
							'icon'  => 'eicon-v-align-middle',
						),
						'bottom' => array(
							'title' => __( 'Bottom', 'uael' ),
							'icon'  => 'eicon-v-align-bottom',
						),
					),
					'condition'    => array(
						'gallery_caption!' => '',
					),
					'prefix_class' => 'uael-img-caption-valign-',
				)
			);
			$this->add_control(
				'caption_tag',
				array(
					'label'     => __( 'HTML Tag', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
					),
					'default'   => 'h4',
					'condition' => array(
						'gallery_caption!' => '',
					),
				)
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'caption_typography',
					'label'     => __( 'Typography', 'uael' ),
					'selector'  => '{{WRAPPER}} .uael-grid-img-caption .uael-grid-caption-text',
					'condition' => array(
						'gallery_caption!' => '',
					),
				)
			);

			$this->add_control(
				'caption_text_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-grid-img-caption .uael-grid-caption-text' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'gallery_caption!' => '',
					),
				)
			);

			$this->add_control(
				'caption_background_color',
				array(
					'label'     => __( 'Background', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .uael-grid-img-caption' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'gallery_caption!' => '',
					),
				)
			);

			$this->add_responsive_control(
				'caption_padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .uael-grid-img-caption'   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array(
						'gallery_caption!' => '',
					),
					'frontend_available' => true,
				)
			);
		$this->end_controls_section();
	}

	/**
	 * Register Navigation Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_navigation_controls() {
		$this->start_controls_section(
			'section_style_navigation',
			array(
				'label'     => __( 'Navigation', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation'    => array( 'arrows', 'dots', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'heading_style_arrows',
			array(
				'label'     => __( 'Arrows', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'navigation'    => array( 'arrows', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows_position',
			array(
				'label'        => __( 'Arrows Position', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'outside',
				'options'      => array(
					'inside'  => __( 'Inside', 'uael' ),
					'outside' => __( 'Outside', 'uael' ),
				),
				'prefix_class' => 'uael-img-carousel-arrow-',
				'condition'    => array(
					'navigation'    => array( 'arrows', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows_size',
			array(
				'label'     => __( 'Arrows Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 20,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'navigation'    => array( 'arrows', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows_color',
			array(
				'label'     => __( 'Arrows Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-slider .slick-prev:before, {{WRAPPER}} .slick-slider .slick-next:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'navigation'    => array( 'arrows', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'heading_style_dots',
			array(
				'label'     => __( 'Dots', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'navigation'    => array( 'dots', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'dots_size',
			array(
				'label'     => __( 'Dots Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 5,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'navigation'    => array( 'dots', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => __( 'Dots Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'navigation'    => array( 'dots', 'both' ),
					'gallery_style' => 'carousel',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Category Filters Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_style_cat_filters_controls() {

		$this->start_controls_section(
			'section_style_cat_filters',
			array(
				'label'     => __( 'Filterable Tabs', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
					'masonry_filters_enable' => 'yes',
				),
			)
		);
			$this->add_responsive_control(
				'cat_filter_align',
				array(
					'label'              => __( 'Tab Alignment', 'uael' ),
					'type'               => Controls_Manager::CHOOSE,
					'options'            => array(
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
					'default'            => 'center',
					'toggle'             => false,
					'render_type'        => 'template',
					'prefix_class'       => 'uael%s-gallery-filter-align-',
					'selectors'          => array(
						'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters' => 'text-align: {{VALUE}};',
					),
					'condition'          => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'all_typography',
					'global'    => array(
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					),
					'condition' => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
					'selector'  => '{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter,{{WRAPPER}} .uael-img-gallery-tabs-dropdown .uael-filters-dropdown-button',
				)
			);
			$this->add_responsive_control(
				'cat_filter_padding',
				array(
					'label'              => __( 'Padding', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', 'em', '%' ),
					'selectors'          => array(
						'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'cat_filter_bet_spacing',
				array(
					'label'              => __( 'Spacing Between Tabs', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
						'(mobile){{WRAPPER}} .uael-gallery-parent .uael-img-gallery-tabs-dropdown .uael-masonry-filters .uael-masonry-filter' => 'margin-left: 0px; margin-right: 0px;',
					),
					'condition'          => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
					'frontend_available' => true,
				)
			);
			$this->add_responsive_control(
				'cat_filter_spacing',
				array(
					'label'              => __( 'Tabs Bottom Spacing', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'max' => 100,
						),
					),
					'selectors'          => array(
						'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'          => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
					'separator'          => 'after',
					'frontend_available' => true,
				)
			);

			$this->start_controls_tabs( 'cat_filters_tabs_style' );

			$this->start_controls_tab(
				'cat_filters_normal',
				array(
					'label'     => __( 'Normal', 'uael' ),
					'condition' => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);

				$this->add_control(
					'cat_filter_color',
					array(
						'label'     => __( 'Text Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-img-gallery-tabs-dropdown .uael-filters-dropdown-button, {{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
							'masonry_filters_enable' => 'yes',
						),
					)
				);

				$this->add_control(
					'cat_filter_bg_color',
					array(
						'label'     => __( 'Background Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter, {{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-filters-dropdown-button' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
							'masonry_filters_enable' => 'yes',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'      => 'cat_filter_border',
						'label'     => __( 'Border', 'uael' ),
						'selector'  => '{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter, {{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-filters-dropdown-button',
						'condition' => array(
							'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
							'masonry_filters_enable' => 'yes',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'cat_filters_hover',
				array(
					'label'     => __( 'Hover', 'uael' ),
					'condition' => array(
						'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
						'masonry_filters_enable' => 'yes',
					),
				)
			);

				$this->add_control(
					'cat_filter_hover_color',
					array(
						'label'     => __( 'Text Active / Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#ffffff',
						'selectors' => array(
							'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter:hover, {{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-current' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
							'masonry_filters_enable' => 'yes',
						),
					)
				);

				$this->add_control(
					'cat_filter_bg_hover_color',
					array(
						'label'     => __( 'Background Active / Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter:hover, {{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-current' => 'background-color: {{VALUE}};',
						),
						'condition' => array(
							'gallery_style'          => array( 'grid', 'masonry', 'justified' ),
							'masonry_filters_enable' => 'yes',
						),
					)
				);

				$this->add_control(
					'cat_filter_border_hover_color',
					array(
						'label'     => __( 'Border Hover Color', 'uael' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array(
							'default' => Global_Colors::COLOR_ACCENT,
						),
						'selectors' => array(
							'{{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-masonry-filter:hover, {{WRAPPER}} .uael-gallery-parent .uael-masonry-filters .uael-current' => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							'gallery_style'             => array( 'grid', 'masonry', 'justified' ),
							'masonry_filters_enable'    => 'yes',
							'cat_filter_border_border!' => '',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

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
				'help_doc_0',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/image-gallery-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=7Q-3fAKKhbg&index=11&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Design filterable Image Gallery » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-design-filterable-image-gallery/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Open lightbox on the click of an image » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/image-gallery-widget/#open-lightbox" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_4',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Open a webpage on the click of an image » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-open-a-webpage-with-the-click-of-an-image/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_5',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Apply scale, opacity, overlay, effects to images » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-customize-images/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_6',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Display specific category tab as a default on page load » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/display-specific-category-tab-as-a-default/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_7',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Set icon on image hover » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-to-set-icon-on-image-hover/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_8',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Display Caption below image in the lightbox » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/image-gallery-widget/#caption-below-image-of-lightbox" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}
	/**
	 * Render Image thumbnail.
	 *
	 * @param array $image Image object.
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_image_thumbnail( $image ) {

		$settings                = $this->get_settings();
		$settings['image_index'] = $image;
		$click_action            = $settings['click_action'];
		$img_wrap_tag            = 'figure';
		if ( '' !== $click_action ) {
				$img_wrap_tag = 'a';
		}
		$output  = '<div class="uael-grid-img-thumbnail uael-ins-target">';
		$output .= Group_Control_Image_Size::get_attachment_image_html( $settings, 'image', 'image_index' );

		if ( 'yes' === $settings['clickable_caption'] ) {
			$output .= $this->render_image_caption( $image );
		}

		$output .= '</div>';
		return $output;
	}

	/**
	 * Render Image Overlay.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_image_overlay() {

		$settings = $this->get_settings_for_display();

		$output = '<div class="uael-grid-img-overlay">';

		if ( 'icon' === $settings['overlay_image_type'] ) {
			if ( UAEL_Helper::is_elementor_updated() ) {
				if ( ! isset( $settings['overlay_icon_hover'] ) && ! \Elementor\Icons_Manager::is_migration_allowed() ) {
					// add old default.
					$settings['overlay_icon_hover'] = 'fa fa-search';
				}

				$has_icon = ! empty( $settings['overlay_icon_hover'] );

				if ( ! $has_icon && ! empty( $settings['new_overlay_icon_hover']['value'] ) ) {
					$has_icon = true;
				}

				$migrated = isset( $settings['__fa4_migrated']['new_overlay_icon_hover'] );
				$is_new   = ! isset( $settings['overlay_icon_hover'] ) && \Elementor\Icons_Manager::is_migration_allowed();

				if ( $has_icon ) {
					$output .= '<span class="uael-overlay-icon">';

					if ( $is_new || $migrated ) {
						ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['new_overlay_icon_hover'], array( 'aria-hidden' => 'true' ) );
						$output .= ob_get_clean();
					} elseif ( ! empty( $settings['overlay_icon_hover'] ) ) {
						$output .= '<i class="' . $settings['overlay_icon_hover'] . '" aria-hidden="true"></i>';
					}
					$output .= '</span>';
				}
			} else {
				$output .= '<span class="uael-overlay-icon">';
				$output .= '<i class="' . $settings['overlay_icon_hover'] . '" aria-hidden="true"></i>';
				$output .= '</span>';
			}
		} elseif ( 'photo' === $settings['overlay_image_type'] ) {
			if ( ! empty( $settings['overlay_image_hover']['url'] ) ) {
				$output .= '<img class="uael-overlay-img" src="' . $settings['overlay_image_hover']['url'] . '" alt="' . Control_Media::get_image_alt( $settings['overlay_image_hover'] ) . '">';
			}
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Render Image caption.
	 *
	 * @param array $image Image object.
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_image_caption( $image ) {

		$settings = $this->get_settings();

		$caption_tag = UAEL_Helper::validate_html_tag( $settings['caption_tag'] );

		if ( '' === $settings['gallery_caption'] || ! $image['caption'] ) {
			return;
		}

		$output              = '<figcaption class="uael-grid-img-content">';
			$output         .= '<div class="uael-grid-img-caption">';
				$output     .= '<' . $caption_tag . ' class="uael-grid-caption-text">';
					$output .= $image['caption'];
				$output     .= '</' . $caption_tag . '>';
			$output         .= '</div>';
		$output             .= '</figcaption>';

		return $output;
	}

	/**
	 * Render Gallery Inner Data.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_gallery_inner_data() {
		$settings = $this->get_settings_for_display();

		$images = $this->get_wp_gallery_image_data( $settings['wp_gallery'] );

		$this->render_gallery_image( $images );
	}

	/**
	 * Get WordPress Gallery Data.
	 *
	 * @since 0.0.1
	 * @param array $images raw array of images.
	 * @access protected
	 */
	protected function get_wp_gallery_image_data( $images ) {

		$gallery = $images;

		foreach ( $images as $i => $data ) {
			$gallery[ $i ]['custom_link'] = get_post_meta( $data['id'], 'uael-custom-link', true );
		}

		return $gallery;
	}

	/**
	 * Render Gallery Images.
	 *
	 * @since 0.0.1
	 * @param array $images array of images.
	 * @access protected
	 */
	protected function render_gallery_image( $images ) {

		$settings              = $this->get_settings();
		$gallery               = $images;
		$img_size              = $settings['image_size'];
		$new_gallery           = array();
		$output                = '';
		$cat_filter            = array();
		$tab_responsive        = ( 'yes' === $settings['tabs_dropdown'] ) ? ' uael-img-gallery-tabs-dropdown' : '';
		$lightbox_gallery_loop = ( 'yes' === $settings['lightbox_loop'] ) ? true : false;

		if ( ! is_array( $gallery ) ) {
			return;
		}

		if ( 'rand' === $settings['gallery_rand'] ) {
			$keys = array_keys( $gallery );
			shuffle( $keys );

			foreach ( $keys as $key ) {
				$new_gallery[ $key ] = $gallery[ $key ];
			}
		} else {
			$new_gallery = $gallery;
		}

		$click_action = $settings['click_action'];
		$img_wrap_tag = 'figure';
		$img_url      = '';

		foreach ( $new_gallery as $index => $item ) {

			if ( array_key_exists( 'url', $new_gallery ) ) {
				$img_url = $item['url'];
			}

			$image = UAEL_Helper::get_image_data( $item['id'], $img_url, $img_size );

			$image_cat = array();

			$image_link = wp_get_attachment_image_src( $item['id'], 'full' );

			if ( empty( $image ) || false === $image_link ) {
				continue;
			}

			if ( ( 'grid' === $settings['gallery_style'] || 'masonry' === $settings['gallery_style'] || 'justified' === $settings['gallery_style'] ) && 'yes' === $settings['masonry_filters_enable'] ) {
				$cat = get_post_meta( $item['id'], 'uael-categories', true );

				if ( '' !== $cat ) {
					$cat_arr = explode( ',', $cat );

					foreach ( $cat_arr as $value ) {
						$cat      = trim( $value );
						$cat_slug = strtolower( str_replace( ' ', '-', $cat ) );

						$image_cat[]             = $cat_slug;
						$cat_filter[ $cat_slug ] = $cat;
					}
				}
			}

			$this->add_render_attribute(
				'grid-media-' . $index,
				'class',
				array(
					'uael-grid-img',
					'uael-grid-gallery-img',
					'uael-ins-hover',
					'elementor-clickable',
				)
			);

			if ( '' !== $click_action ) {

				$item_link = '';

				if ( 'lightbox' === $click_action ) {
					if ( $item['id'] ) {
						$item_link = $image_link;
						$item_link = $item_link[0];
					} else {
						$item_link = $item['url'];
					}

					$this->add_render_attribute(
						'grid-media-' . $index,
						array(
							'data-fancybox' => 'uael-gallery',
						)
					);
					$lightbox         = 'caption';
					$lightbox_content = apply_filters( 'uael-lightbox-content', $lightbox );
					if ( 'yes' === $settings['show_caption_lightbox'] ) {
						$this->add_render_attribute(
							'grid-media-' . $index,
							array(
								'data-caption' => $image[ $lightbox_content ],
							)
						);
					}
				} elseif ( 'file' === $click_action ) {
					if ( $item['id'] ) {
						$item_link = $image_link;
						$item_link = $item_link[0];
					} else {
						$item_link = $item['url'];
					}
				} elseif ( 'attachment' === $click_action ) {
					$item_link = get_permalink( $item['id'] );
				} elseif ( 'custom' === $click_action ) {
					if ( ! empty( $item['custom_link'] ) ) {
						$item_link = $item['custom_link'];
					}
				}

				if ( 'file' === $click_action || 'attachment' === $click_action || ( 'custom' === $click_action && '' !== $item_link ) ) {
					$link_target = $settings['link_target'];

					$this->add_render_attribute( 'grid-media-' . $index, 'target', $link_target );

					if ( '_blank' === $link_target ) {
						$this->add_render_attribute( 'grid-media-' . $index, 'rel', 'dofollow' );
					}
				}
				$img_wrap_tag = ( ! empty( $item_link ) ) ? 'a' : 'span';

				if ( ! empty( $item_link ) ) {
					$this->add_render_attribute(
						'grid-media-' . $index,
						array(
							'href'                         => $item_link,
							'data-elementor-open-lightbox' => 'no',
						)
					);
				} else {
					$this->add_render_attribute(
						'grid-media-' . $index,
						array(
							'data-elementor-open-lightbox' => 'no',
						)
					);
				}
			}

			if ( 'justified' === $settings['gallery_style'] ) {
				$output         .= '<div class="uael-grid-item ' . implode( ' ', $image_cat ) . '">';
					$output     .= '<div class="uael-grid-item-content">';
						$output .= '<' . $img_wrap_tag . ' ' . $this->get_render_attribute_string( 'grid-media-' . $index ) . '>';
						$output .= $this->render_image_thumbnail( $image );

							$output .= $this->render_image_overlay();

							$output .= '</' . $img_wrap_tag . '>';

				if ( 'yes' !== $settings['clickable_caption'] ) {
					$output .= $this->render_image_caption( $image );
				}
					$output .= '</div>';
				$output     .= '</div>';
			} else {
				$output         .= '<div class="uael-grid-item ' . implode( ' ', $image_cat ) . ' uael-img-gallery-item-' . ( $index + 1 ) . '">';
					$output     .= '<div class="uael-grid-item-content">';
						$output .= '<' . $img_wrap_tag . ' ' . $this->get_render_attribute_string( 'grid-media-' . $index ) . '>';

							$output .= $this->render_image_thumbnail( $image );

							$output .= $this->render_image_overlay();

							$output .= '</' . $img_wrap_tag . '>';

				if ( 'yes' !== $settings['clickable_caption'] ) {
					$output .= $this->render_image_caption( $image );
				}

					$output .= '</div>';
				$output     .= '</div>';
			}
		}

		if ( ( 'grid' === $settings['gallery_style'] || 'masonry' === $settings['gallery_style'] || 'justified' === $settings['gallery_style'] ) && 'yes' === $settings['masonry_filters_enable'] ) {
			ksort( $cat_filter );
			$cat_filter = apply_filters( 'uael_image_gallery_tabs', $cat_filter );

			$default_cat = '';

			if ( 'yes' === $settings['default_filter_switch'] && '' !== $settings['default_filter'] ) {
				$default_cat = '.' . trim( $settings['default_filter'] );
				$default_cat = strtolower( str_replace( ' ', '-', $default_cat ) );
			}

			$filters_output = '<div class="uael-masonry-filters-wrapper' . $tab_responsive . '">';

				$filters_output     .= '<div class="uael-masonry-filters" data-default="' . $default_cat . '">';
					$filters_output .= '<div class="uael-masonry-filter uael-current" data-filter="*">' . $settings['filters_all_text'] . '</div>';

			foreach ( $cat_filter as $key => $value ) {
				$filters_output .= '<div class="uael-masonry-filter" data-filter=".' . $key . '">' . $value . '</div>';
			}

				$filters_output .= '</div>';

			if ( 'yes' === $settings['tabs_dropdown'] ) {
				$filters_output .= '<div class="uael-filters-dropdown uael-masonry-filters" data-default="' . $default_cat . '">';

					$filters_output .= '<div class="uael-filters-dropdown-button">' . $settings['filters_all_text'] . '</div>';

					$filters_output .= '<ul class="uael-filters-dropdown-list">';

						$filters_output .= '<li class="uael-filters-dropdown-item uael-masonry-filter uael-current" data-filter="*">' . $settings['filters_all_text'] . '</li>';

				foreach ( $cat_filter as $key => $value ) {
					$filters_output .= '<li class="uael-filters-dropdown-item uael-masonry-filter" data-filter=".' . $key . '">' . $value . '</li>';
				}

					$filters_output .= '</ul>';
					$filters_output .= '</div>';
			}

			$filters_output .= '</div>';

			echo wp_kses_post( $filters_output );
		}

		if ( 'lightbox' === $click_action ) {
			$actions_arr = array();

			if ( ! empty( $settings['lightbox_actions'] ) ) {
				if ( is_array( $settings['lightbox_actions'] ) ) {
					foreach ( $settings['lightbox_actions'] as $action ) {
						$actions_arr[] = $action;
					}
				} else {
					$actions_arr[] = $settings['lightbox_actions'];
				}
			}

			$actions_arr[] = 'close';

			$this->add_render_attribute(
				'grid-wrap',
				array(
					'class'                      => 'uael-image-lightbox-wrap',
					'data-lightbox_actions'      => wp_json_encode( $actions_arr ),
					'data-lightbox-gallery-loop' => $lightbox_gallery_loop,
				)
			);
		}

		echo '<div ' . wp_kses_post( $this->get_render_attribute_string( 'grid-wrap' ) ) . '>';
			echo wp_kses_post( $output );
		echo '</div>';
	}

	/**
	 * Render Masonry Script.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render_editor_script() {

		?><script type="text/javascript">
			jQuery( document ).ready( function( $ ) {

				$( '.uael-img-grid-masonry-wrap' ).each( function() {

					var $node_id    = '<?php echo esc_attr( $this->get_id() ); ?>';
					var scope       = $( '[data-id="' + $node_id + '"]' );
					var selector    = $(this);

					if ( selector.closest( scope ).length < 1 ) {
						return;
					}

					var $justified_selector = scope.find('.uael-img-justified-wrap');
					var row_height  = $justified_selector.data( 'rowheight' );
					var lastrow     = $justified_selector.data( 'lastrow' );
					var layoutMode = 'fitRows';
					var filter_cat;

					if ( selector.hasClass('uael-masonry') ) {
						layoutMode = 'masonry';
					}

					var filters = scope.find('.uael-masonry-filters');
					var def_cat = '*';

					if ( filters.length > 0 ) {

						var def_filter = filters.attr('data-default');

						if ( '' !== def_filter ) {

							def_cat     = def_filter;
							def_cat_sel = filters.find('[data-filter="'+def_filter+'"]');

							if ( def_cat_sel.length > 0 ) {
								def_cat_sel.siblings().removeClass('uael-current');
								def_cat_sel.addClass('uael-current');
							}
						}
					}
					if ( $justified_selector.length > 0 ) {
						$justified_selector.imagesLoaded( function() {
						})
						.done(function( instance ) {
							$justified_selector.justifiedGallery({
								filter: def_cat,
								rowHeight : row_height,
								lastRow : lastrow,
								selector : 'div',
							});
						});
					} else {
						var masonryArgs = {
							// set itemSelector so .grid-sizer is not used in layout
							filter          : def_cat,
							itemSelector    : '.uael-grid-item',
							percentPosition : true,
							layoutMode      : layoutMode,
							hiddenStyle     : {
								opacity     : 0,
							},
						};

						var $isotopeObj = {};

						selector.imagesLoaded( function() {

							$isotopeObj = selector.isotope( masonryArgs );

							selector.find('.uael-grid-item').resize( function() {
								$isotopeObj.isotope( 'layout' );
							});
						});
					}

					if ( selector.hasClass('uael-cat-filters') ) {
						// bind filter button click
						scope.on( 'click', '.uael-masonry-filter', function() {

							var $this       = $(this);
							var filterValue = $this.attr('data-filter');

							$this.siblings().removeClass('uael-current');
							$this.addClass('uael-current');
							if( '*' === filterValue ) {
								filter_cat = scope.find('.uael-img-gallery-wrap').data('filter-default');
							} else {
								filter_cat = filterValue.substr(1);
							}

							if( scope.find( '.uael-masonry-filters' ).data( 'default' ) ){
								var def_filter = scope.find( '.uael-masonry-filters' ).data( 'default' );
							}
							else{
								var def_filter = '.' + scope.find('.uael-img-gallery-wrap').data( 'filter-default' );
							}

							var str_img_text = scope.find('.uael-current').text();
							var str_img_text = str_img_text.substring( def_filter.length - 1, str_img_text.length );
							scope.find( '.uael-filters-dropdown-button' ).text( str_img_text );

							if ( $justified_selector.length > 0 ) {
								$justified_selector.justifiedGallery({
									filter: filterValue,
									rowHeight : row_height,
									lastRow : lastrow,
									selector : 'div',
								});
							} else {
								$isotopeObj.isotope({ filter: filterValue });
							}
						});
					}

					if( scope.find( '.uael-masonry-filters' ).data( 'default' ) ){
						var def_filter = scope.find( '.uael-masonry-filters' ).data( 'default' );
					}
					else{
						var def_filter = '.' + scope.find('.uael-img-gallery-wrap').data( 'filter-default' );
					}

					var str_img_text = scope.find('.uael-current').text();
					var str_img_text = str_img_text.substring( def_filter.length - 1, str_img_text.length );
					scope.find( '.uael-filters-dropdown-button' ).text( str_img_text );
				});
			});
		</script>
		<?php
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function get_carousel_attr() {

		$settings = $this->get_settings();

		if ( 'carousel' !== $settings['gallery_style'] ) {
			return;
		}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) );
		$show_arrows = ( in_array( $settings['navigation'], array( 'arrows', 'both' ), true ) );

		$slick_options = array(
			'slidesToShow'   => ( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 4,
			'slidesToScroll' => ( $settings['slides_to_scroll'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => $show_arrows,
			'dots'           => $show_dots,
			'rtl'            => $is_rtl,
		);

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {
			$slick_options['responsive'] = array();

			if ( $settings['slides_to_show_tablet'] ) {
				$tablet_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_scroll = ( $settings['slides_to_scroll_tablet'] ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( $settings['slides_to_show_mobile'] ) {
				$mobile_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_scroll = ( $settings['slides_to_scroll_mobile'] ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$slick_options = apply_filters( 'uael_image_gallery_carousel_options', $slick_options );

		$this->add_render_attribute(
			'grid-wrap',
			array(
				'data-image_carousel' => wp_json_encode( $slick_options ),
			)
		);
	}

	/**
	 * Render ImageGallery output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings    = $this->get_settings();
		$node_id     = $this->get_id();
		$is_editor   = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$row_height  = '';
		$unjustified = '';
		$row_margin  = '';

		$wrap_class = array(
			'uael-img-gallery-wrap',
			'uael-img-' . $settings['gallery_style'] . '-wrap',
		);

		if ( 'grid' === $settings['gallery_style'] || 'masonry' === $settings['gallery_style'] || 'justified' === $settings['gallery_style'] ) {
			$wrap_class[] = 'uael-img-grid-masonry-wrap';

			if ( 'masonry' === $settings['gallery_style'] ) {
				$wrap_class[] = 'uael-masonry';
			}

			if ( 'yes' === $settings['masonry_filters_enable'] ) {
				$wrap_class[] = 'uael-cat-filters';
			}
		}

		if ( 'carousel' === $settings['gallery_style'] ) {
			$wrap_class[] = 'uael-nav-' . $settings['navigation'];
			$this->get_carousel_attr();
		}

		$this->add_render_attribute( 'grid-wrap', 'class', $wrap_class );
		$this->add_render_attribute( 'grid-wrap', 'data-filter-default', $settings['filters_all_text'] );

		if ( 'justified' === $settings['gallery_style'] ) {
			$row_height = ( '' !== $settings['justified_row_height']['size'] ) ? $settings['justified_row_height']['size'] : 120;

			$this->add_render_attribute(
				'grid-wrap',
				array(
					'data-rowheight' => $row_height,
					'data-lastrow'   => $settings['last_row'],
				)
			);
		}

		if ( 'justified' !== $settings['gallery_style'] ) {
			$unjustified = 'uael-gallery-unjustified';
		}

		echo '<div class="uael-gallery-parent uael-caption-' . esc_attr( $settings['gallery_caption'] ) . ' ' . esc_attr( $unjustified ) . '">';

			$this->render_gallery_inner_data();

		echo '</div>';

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			if ( ( 'grid' === $settings['gallery_style'] && 'yes' === $settings['masonry_filters_enable'] ) || 'masonry' === $settings['gallery_style'] || 'justified' === $settings['gallery_style'] ) {
				/* Scripts will load for editor changes */
				$this->render_editor_script();
			}
		}
	}
}

