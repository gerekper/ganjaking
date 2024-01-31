<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder basic class.
 * @since 4.2
 */
class Vc_Base {
	/**
	 * Shortcode's edit form.
	 *
	 * @since  4.2
	 * @access protected
	 * @var bool|Vc_Shortcode_Edit_Form
	 */
	protected $shortcode_edit_form = false;

	/**
	 * Templates management panel editor.
	 * @since  4.4
	 * @access protected
	 * @var bool|Vc_Templates_Panel_Editor
	 */
	protected $templates_panel_editor = false;
	/**
	 * Presets management panel editor.
	 * @since  5.2
	 * @access protected
	 * @var bool|Vc_Preset_Panel_Editor
	 */
	protected $preset_panel_editor = false;
	/**
	 * Post object for VC in Admin.
	 *
	 * @since  4.4
	 * @access protected
	 * @var bool|Vc_Post_Admin
	 */
	protected $post_admin = false;
	/**
	 * Post object for VC.
	 *
	 * @since  4.4.3
	 * @access protected
	 * @var bool|Vc_Post_Admin
	 */
	protected $post = false;
	/**
	 * List of shortcodes map to VC.
	 *
	 * @since  4.2
	 * @access public
	 * @var array WPBakeryShortCodeFishBones
	 */
	protected $shortcodes = array();

	/** @var  Vc_Shared_Templates */
	public $shared_templates;

	/**
	 * Load default object like shortcode parsing.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function init() {
		do_action( 'vc_before_init_base' );
		$this->postAdmin()->init();
		add_filter( 'body_class', array(
			$this,
			'bodyClass',
		) );
		add_filter( 'the_excerpt', array(
			$this,
			'excerptFilter',
		) );
		add_action( 'wp_head', array(
			$this,
			'addMetaData',
		) );
		if ( is_admin() ) {
			$this->initAdmin();
		} else {
			$this->initPage();
		}
		do_action( 'vc_after_init_base' );
	}

	/**
	 * Post object for interacting with Current post data.
	 * @return Vc_Post_Admin
	 * @since 4.4
	 */
	public function postAdmin() {
		if ( false === $this->post_admin ) {
			require_once vc_path_dir( 'CORE_DIR', 'class-vc-post-admin.php' );
			$this->post_admin = new Vc_Post_Admin();
		}

		return $this->post_admin;
	}

	/**
	 * Build VC for frontend pages.
	 *
	 * @since  4.2
	 * @access public
	 */
	public function initPage() {
		do_action( 'vc_build_page' );
		add_action( 'template_redirect', array(
			$this,
			'frontCss',
		) );
		add_action( 'template_redirect', array(
			'WPBMap',
			'addAllMappedShortcodes',
		) );
		add_action( 'wp_head', array(
			$this,
			'addFrontCss',
		), 1000 );
		add_action( 'wp_head', array(
			$this,
			'addNoScript',
		), 1000 );
		add_action( 'template_redirect', array(
			$this,
			'frontJsRegister',
		) );
		add_filter( 'the_content', array(
			$this,
			'fixPContent',
		), 11 );
		add_filter( 'print_head_scripts', array(
			$this,
			'outputPostHeaderCustomJs',
		), 90 );
		add_filter( 'wp_print_footer_scripts', array(
			$this,
			'outputPostFooterCustomJs',
		), 90 );
		add_filter( 'print_head_scripts', array(
			$this,
			'outputGlobalHeaderCustomHtml',
		), 100 );
		add_filter( 'wp_print_footer_scripts', array(
			$this,
			'outputGlobalFooterCustomHtml',
		), 100 );
	}

	/**
	 * Load admin required modules and elements
	 *
	 * @since  4.2
	 * @access public
	 */
	public function initAdmin() {
		do_action( 'vc_build_admin_page' );
		// editors actions:
		$this->editForm()->init();
		$this->templatesPanelEditor()->init();
		$this->shared_templates->init();

		// plugins list page actions links
		add_filter( 'plugin_action_links', array(
			$this,
			'pluginActionLinks',
		), 10, 2 );
	}

	/**
	 * Setter for edit form.
	 * @param Vc_Shortcode_Edit_Form $form
	 * @since 4.2
	 *
	 */
	public function setEditForm( Vc_Shortcode_Edit_Form $form ) {
		$this->shortcode_edit_form = $form;
	}

	/**
	 * Get Shortcodes Edit form object.
	 *
	 * @return Vc_Shortcode_Edit_Form
	 * @since  4.2
	 * @access public
	 * @see    Vc_Shortcode_Edit_Form::__construct
	 */
	public function editForm() {
		return $this->shortcode_edit_form;
	}

	/**
	 * Setter for Templates editor.
	 * @param Vc_Templates_Panel_Editor $editor
	 * @since 4.4
	 *
	 */
	public function setTemplatesPanelEditor( Vc_Templates_Panel_Editor $editor ) {
		$this->templates_panel_editor = $editor;
	}

	/**
	 * Setter for Preset editor.
	 * @param Vc_Preset_Panel_Editor $editor
	 * @since 5.2
	 *
	 */
	public function setPresetPanelEditor( Vc_Preset_Panel_Editor $editor ) {
		$this->preset_panel_editor = $editor;
	}

	/**
	 * Get templates manager.
	 * @return bool|Vc_Templates_Panel_Editor
	 * @since  4.4
	 * @access public
	 * @see    Vc_Templates_Panel_Editor::__construct
	 */
	public function templatesPanelEditor() {
		return $this->templates_panel_editor;
	}

	/**
	 * Get preset manager.
	 * @return bool|Vc_Preset_Panel_Editor
	 * @since  5.2
	 * @access public
	 * @see    Vc_Preset_Panel_Editor::__construct
	 */
	public function presetPanelEditor() {
		return $this->preset_panel_editor;
	}

	/**
	 * Get shortcode class instance.
	 *
	 * @param string $tag
	 *
	 * @return Vc_Shortcodes_Manager|null
	 * @see    WPBakeryShortCodeFishBones
	 * @since  4.2
	 * @access public
	 *
	 */
	public function getShortCode( $tag ) {
		return Vc_Shortcodes_Manager::getInstance()->setTag( $tag );
	}

	/**
	 * Remove shortcode from shortcodes list of VC.
	 *
	 * @param $tag - shortcode tag
	 * @since  4.2
	 * @access public
	 *
	 */
	public function removeShortCode( $tag ) {
		remove_shortcode( $tag );
	}

	/**
	 * Set or modify new settings for shortcode.
	 *
	 * This function widely used by WPBMap class methods to modify shortcodes mapping
	 *
	 * @param $tag
	 * @param $name
	 * @param $value
	 * @throws \Exception
	 * @since 4.3
	 */
	public function updateShortcodeSetting( $tag, $name, $value ) {
		Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag )->setSettings( $name, $value );
	}

	/**
	 * Build custom css styles for page from shortcodes attributes created by VC editors.
	 *
	 * Called by save method, which is hooked by edit_post action.
	 * Function creates meta data for post with the key '_wpb_shortcodes_custom_css'
	 * and value as css string, which will be added to the footer of the page.
	 *
	 * @param $id
	 * @throws \Exception
	 * @since  4.2
	 * @access public
	 */
	public function buildShortcodesCustomCss( $id ) {
		if ( 'dopreview' === vc_post_param( 'wp-preview' ) && wp_revisions_enabled( get_post( $id ) ) ) {
			$latest_revision = wp_get_post_revisions( $id );
			if ( ! empty( $latest_revision ) ) {
				$array_values = array_values( $latest_revision );
				$id = $array_values[0]->ID;
			}
		}

		$post = get_post( $id );
		/**
		 * vc_filter: vc_base_build_shortcodes_custom_css
		 * @since 4.4
		 */
		$css = apply_filters( 'vc_base_build_shortcodes_custom_css', $this->parseShortcodesCustomCss( $post->post_content ), $id );
		if ( empty( $css ) ) {
			delete_metadata( 'post', $id, '_wpb_shortcodes_custom_css' );
		} else {
			update_metadata( 'post', $id, '_wpb_shortcodes_custom_css', $css );
			update_metadata( 'post', $id, '_wpb_shortcodes_custom_css_updated', true );
		}
	}

	/**
	 * Parse shortcodes custom css string.
	 *
	 * This function is used by self::buildShortcodesCustomCss and creates css string from shortcodes attributes
	 * like 'css_editor'.
	 *
	 * @param $content
	 *
	 * @return string
	 * @throws \Exception
	 * @see    WPBakeryCssEditor
	 * @since  4.2
	 * @access public
	 */
	public function parseShortcodesCustomCss( $content ) {
		$css = '';
		if ( ! preg_match( '/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content ) ) {
			return $css;
		}
		WPBMap::addAllMappedShortcodes();
		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );
		foreach ( $shortcodes[2] as $index => $tag ) {
			$shortcode = WPBMap::getShortCode( $tag );
			$attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
			if ( isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
				foreach ( $shortcode['params'] as $param ) {
					if ( isset( $param['type'] ) && 'css_editor' === $param['type'] && isset( $attr_array[ $param['param_name'] ] ) ) {
						$css .= $attr_array[ $param['param_name'] ];
					}
				}
			}
		}

		$css_lib = [];
		foreach ( $shortcodes[5] as $shortcode_content ) {
			$shortcode_css = $this->parseShortcodesCustomCss( $shortcode_content );

			if ( in_array( $shortcode_css, $css_lib ) ) {
				continue;
			}

			$css .= $shortcode_css;

			$css_lib[] = $shortcode_css;
		}

		return $css;
	}

	/**
	 * Get current post id.
	 *
	 * @since  7.0
	 * @return false|int
	 */
	public function get_post_id() {
		$id = false;
		if ( is_front_page() || is_home() ) {
			$id = get_queried_object_id();
		} elseif ( is_singular() ) {
			$id = get_the_ID();
		}

		return $id;
	}

	/**
	 * Hooked class method by wp_head WP action to output post custom css.
	 *
	 * Method gets post meta value for page by key '_wpb_post_custom_css' and if it is not empty
	 * outputs css string wrapped into style tag.
	 *
	 * @param int $id
	 * @since  4.2
	 * @access public
	 *
	 */
	public function addPageCustomCss( $id = null ) {
		$id = $id ?: $this->get_post_id();

		if ( ! $id ) {
			return;
		}

		if ( 'true' === vc_get_param( 'preview' ) && wp_revisions_enabled( get_post( $id ) ) ) {
			$latest_revision = wp_get_post_revisions( $id );
			if ( ! empty( $latest_revision ) ) {
				$array_values = array_values( $latest_revision );
				$id = $array_values[0]->ID;
			}
		}
		$post_custom_css = get_metadata( 'post', $id, '_wpb_post_custom_css', true );
		$post_custom_css = apply_filters( 'vc_post_custom_css', $post_custom_css, $id );
		if ( ! empty( $post_custom_css ) ) {
			$post_custom_css = wp_strip_all_tags( $post_custom_css );
			echo '<style type="text/css" data-type="vc_custom-css">';
			echo $post_custom_css;
			echo '</style>';
		}
	}

	/**
	 * Hooked class method by wp_footer WP action to output shortcodes css editor settings from page meta data.
	 *
	 * Method gets post meta value for page by key '_wpb_shortcodes_custom_css' and if it is not empty
	 * outputs css string wrapped into style tag.
	 *
	 * @param int $id
	 *
	 * @since  4.2
	 * @access public
	 *
	 */
	public function addShortcodesCustomCss( $id = null ) {
		if ( ! $id && is_singular() ) {
			$id = get_the_ID();
		}
		// if is woocommerce shop page
		if ( ! $id && function_exists( 'is_shop' ) && is_shop() ) {
			$id = get_option( 'woocommerce_shop_page_id' );
		}

		if ( ! $id ) {
			return;
		}

		if ( 'true' === vc_get_param( 'preview' ) && wp_revisions_enabled( get_post( $id ) ) ) {
			$latest_revision = wp_get_post_revisions( $id );
			if ( ! empty( $latest_revision ) ) {
				$array_values = array_values( $latest_revision );
				$id = $array_values[0]->ID;
			}
		}

		$shortcodes_custom_css = $this->get_shortcodes_custom_css( $id );
		if ( ! empty( $shortcodes_custom_css ) ) {
			$shortcodes_custom_css = wp_strip_all_tags( $shortcodes_custom_css );
			echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
			echo $shortcodes_custom_css;
			echo '</style>';
		}
	}

	/**
	 * Get custom css of all shortcodes for particular post.
	 *
	 * @param int $id
	 * @return mixed
	 *
	 * @since  6.2
	 * @access public
	 */
	public function get_shortcodes_custom_css( $id ) {
		$is_updated = get_metadata( 'post', $id, '_wpb_shortcodes_custom_css_updated', true );

		if ( empty( $is_updated ) ) {
			$this->buildShortcodesCustomCss( $id );
		}

		$shortcodes_custom_css = get_metadata( 'post', $id, '_wpb_shortcodes_custom_css', true );

		return apply_filters( 'vc_shortcodes_custom_css', $shortcodes_custom_css, $id );
	}

	/**
	 * Add css styles for current page and elements design options added w\ editor.
	 */
	public function addFrontCss( $id = null ) {
		$this->addPageCustomCss( $id );
		$this->addShortcodesCustomCss( $id );
	}

	public function addNoScript() {
		$custom_tag = 'style';
		$second_tag = 'noscript';
		echo '<' . esc_attr( $second_tag ) . '>';
		echo '<' . esc_attr( $custom_tag ) . '>';
		echo ' .wpb_animate_when_almost_visible { opacity: 1; }';
		echo '</' . esc_attr( $custom_tag ) . '>';
		echo '</' . esc_attr( $second_tag ) . '>';
	}

	/**
	 * Register front css styles.
	 *
	 * Calls wp_register_style for required css libraries files.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function frontCss() {
		wp_register_style( 'flexslider', vc_asset_url( 'lib/flexslider/flexslider.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'nivo-slider-css', vc_asset_url( 'lib/bower/nivoslider/nivo-slider.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'nivo-slider-theme', vc_asset_url( 'lib/bower/nivoslider/themes/default/default.min.css' ), array( 'nivo-slider-css' ), WPB_VC_VERSION );
		wp_register_style( 'prettyphoto', vc_asset_url( 'lib/prettyphoto/css/prettyPhoto.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'isotope-css', vc_asset_url( 'css/lib/isotope.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'vc_font_awesome_5_shims', vc_asset_url( 'lib/bower/font-awesome/css/v4-shims.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'vc_font_awesome_5', vc_asset_url( 'lib/bower/font-awesome/css/all.min.css' ), array( 'vc_font_awesome_5_shims' ), WPB_VC_VERSION );
		wp_register_style( 'vc_animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'lightbox2', vc_asset_url( 'lib/lightbox2/dist/css/lightbox.min.css' ), array(), WPB_VC_VERSION );
		$front_css_file = vc_asset_url( 'css/js_composer.min.css' );
		$upload_dir = wp_upload_dir();
		$vc_upload_dir = vc_upload_dir();
		if ( '1' === vc_settings()->get( 'use_custom' ) && is_file( $upload_dir['basedir'] . '/' . $vc_upload_dir . '/js_composer_front_custom.css' ) ) {
			$front_css_file = $upload_dir['baseurl'] . '/' . $vc_upload_dir . '/js_composer_front_custom.css';
			$front_css_file = vc_str_remove_protocol( $front_css_file );
		}
		wp_register_style( 'js_composer_front', $front_css_file, array(), WPB_VC_VERSION );

		$custom_css_path = $upload_dir['basedir'] . '/' . $vc_upload_dir . '/custom.css';
		if ( is_file( $upload_dir['basedir'] . '/' . $vc_upload_dir . '/custom.css' ) && filesize( $custom_css_path ) > 0 ) {
			$custom_css_url = $upload_dir['baseurl'] . '/' . $vc_upload_dir . '/custom.css';
			$custom_css_url = vc_str_remove_protocol( $custom_css_url );
			wp_register_style( 'js_composer_custom_css', $custom_css_url, array(), WPB_VC_VERSION );
		}

		add_action( 'wp_enqueue_scripts', array(
			$this,
			'enqueueStyle',
		) );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_front_css' );
	}

	/**
	 * Enqueue base css class for VC elements and enqueue custom css if exists.
	 */
	public function enqueueStyle() {
		$post = get_post();
		if ( $post && strpos( $post->post_content, '[vc_row' ) !== false ) {
			wp_enqueue_style( 'js_composer_front' );
		}
		wp_enqueue_style( 'js_composer_custom_css' );
	}

	/**
	 * Register front javascript libs.
	 *
	 * Calls wp_register_script for required css libraries files.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function frontJsRegister() {
		wp_register_script( 'prettyphoto', vc_asset_url( 'lib/prettyphoto/js/jquery.prettyPhoto.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'lightbox2', vc_asset_url( 'lib/lightbox2/dist/js/lightbox.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_waypoints', vc_asset_url( 'lib/vc_waypoints/vc-waypoints.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );

		// @deprecated used in old tabs
		wp_register_script( 'jquery_ui_tabs_rotate', vc_asset_url( 'lib/bower/jquery-ui-tabs-rotate/jquery-ui-tabs-rotate.min.js' ), array(
			'jquery-core',
			'jquery-ui-tabs',
		), WPB_VC_VERSION, true );

		// used in vc_gallery, old grid
		wp_register_script( 'isotope', vc_asset_url( 'lib/bower/isotope/dist/isotope.pkgd.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );

		wp_register_script( 'twbs-pagination', vc_asset_url( 'lib/bower/twbs-pagination/jquery.twbsPagination.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'nivo-slider', vc_asset_url( 'lib/bower/nivoslider/jquery.nivo.slider.pack.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'flexslider', vc_asset_url( 'lib/flexslider/jquery.flexslider.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_composer_front_js', vc_asset_url( 'js/dist/js_composer_front.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );

		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_front_js' );
	}

	/**
	 * Register admin javascript libs.
	 *
	 * Calls wp_register_script for required css libraries files for Admin dashboard.
	 *
	 * @since  3.1
	 * vc_filter: vc_i18n_locale_composer_js_view, since 4.4 - override localization for js
	 * @access public
	 */
	public function registerAdminJavascript() {
		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_admin_js' );

	}

	/**
	 * Register admin css styles.
	 *
	 * Calls wp_register_style for required css libraries files for admin dashboard.
	 *
	 * @since  3.1
	 * @access public
	 */
	public function registerAdminCss() {
		/**
		 * @since 4.4
		 */
		do_action( 'vc_base_register_admin_css' );
	}

	/**
	 * Add Settings link in plugin's page
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 * @throws \Exception
	 * @since 4.2
	 */
	public function pluginActionLinks( $links, $file ) {
		if ( plugin_basename( vc_path_dir( 'APP_DIR', '/js_composer.php' ) ) === $file ) {
			$title = esc_html__( 'WPBakery Page Builder Settings', 'js_composer' );
			$html = esc_html__( 'Settings', 'js_composer' );
			if ( ! vc_user_access()->part( 'settings' )->can( 'vc-general-tab' )->get() ) {
				$title = esc_html__( 'About WPBakery Page Builder', 'js_composer' );
				$html = esc_html__( 'About', 'js_composer' );
			}
			$link = '<a title="' . esc_attr( $title ) . '" href="' . esc_url( $this->getSettingsPageLink() ) . '">' . $html . '</a>';
			array_unshift( $links, $link ); // Add to top
		}

		return $links;
	}

	/**
	 * Get settings page link
	 * @return string url to settings page
	 * @throws \Exception
	 * @since 4.2
	 */
	public function getSettingsPageLink() {
		$page = 'vc-general';
		if ( ! vc_user_access()->part( 'settings' )->can( 'vc-general-tab' )->get() ) {
			$page = 'vc-welcome';
		}

		return add_query_arg( array( 'page' => $page ), admin_url( 'admin.php' ) );
	}

	/**
	 * Hooked class method by wp_head WP action.
	 * @since  4.2
	 * @access public
	 */
	public function addMetaData() {
		echo '<meta name="generator" content="Powered by WPBakery Page Builder - drag and drop page builder for WordPress."/>' . "\n";
	}

	/**
	 * Method adds css class to body tag.
	 *
	 * Hooked class method by body_class WP filter. Method adds custom css class to body tag of the page to help
	 * identify and build design specially for VC shortcodes.
	 *
	 * @param $classes
	 *
	 * @return array
	 * @since  4.2
	 * @access public
	 *
	 */
	public function bodyClass( $classes ) {
		return js_composer_body_class( $classes );
	}

	/**
	 * Builds excerpt for post from content.
	 *
	 * Hooked class method by the_excerpt WP filter. When user creates content with VC all content is always wrapped by
	 * shortcodes. This methods calls do_shortcode for post's content and then creates a new excerpt.
	 *
	 * @param $output
	 *
	 * @return string
	 * @since  4.2
	 * @access public
	 *
	 */
	public function excerptFilter( $output ) {
		global $post;
		if ( empty( $output ) && ! empty( $post->post_content ) ) {
			$text = wp_strip_all_tags( do_shortcode( $post->post_content ) );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more = apply_filters( 'excerpt_more', ' [...]' );
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

			return $text;
		}

		return $output;
	}

	/**
	 * Remove unwanted wraping with p for content.
	 *
	 * Hooked by 'the_content' filter.
	 * @param null $content
	 *
	 * @return string|null
	 * @since 4.2
	 *
	 */
	public function fixPContent( $content = null ) {
		if ( $content ) {
			$s = array(
				'/' . preg_quote( '</div>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<div ', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<section ', '/' ) . '/i',
				'/' . preg_quote( '</section>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
			);
			$r = array(
				'</div>',
				'<div ',
				'<section ',
				'</section>',
			);
			$content = preg_replace( $s, $r, $content );

			// if content contains vc_row for a page view or
			// vc_welcome for a frontend editor
			// then wrap with '<div>'
			if ( preg_match( '/vc_row/', $content ) || preg_match( '/vc_welcome/', $content ) ) {
				$content = '<div class="wpb-content-wrapper">' . $content . '</div>';
			}

			return $content;
		}

		return null;
	}

	/**
	 * Get array of string for locale.
	 *
	 * @return array
	 * @since 4.7
	 *
	 */
	public function getEditorsLocale() {
		return array(
			'add_remove_picture' => esc_html__( 'Add/remove picture', 'js_composer' ),
			'finish_adding_text' => esc_html__( 'Finish Adding Images', 'js_composer' ),
			'add_image' => esc_html__( 'Add Image', 'js_composer' ),
			'add_images' => esc_html__( 'Add Images', 'js_composer' ),
			'settings' => esc_html__( 'Settings', 'js_composer' ),
			'main_button_title' => esc_html__( 'WPBakery Page Builder', 'js_composer' ),
			'main_button_title_backend_editor' => esc_html__( 'Backend Editor', 'js_composer' ),
			'main_button_title_frontend_editor' => esc_html__( 'Frontend Editor', 'js_composer' ),
			'main_button_title_revert' => esc_html__( 'Classic Mode', 'js_composer' ),
			'main_button_title_gutenberg' => esc_html__( 'Gutenberg Editor', 'js_composer' ),
			'please_enter_templates_name' => esc_html__( 'Enter template name you want to save.', 'js_composer' ),
			'confirm_deleting_template' => esc_html__( 'Confirm deleting "{template_name}" template, press Cancel to leave. This action cannot be undone.', 'js_composer' ),
			'press_ok_to_delete_section' => esc_html__( 'Press OK to delete section, Cancel to leave', 'js_composer' ),
			'drag_drop_me_in_column' => esc_html__( 'Drag and drop me in the column', 'js_composer' ),
			'press_ok_to_delete_tab' => esc_html__( 'Press OK to delete "{tab_name}" tab, Cancel to leave', 'js_composer' ),
			'slide' => esc_html__( 'Slide', 'js_composer' ),
			'tab' => esc_html__( 'Tab', 'js_composer' ),
			'section' => esc_html__( 'Section', 'js_composer' ),
			'please_enter_new_tab_title' => esc_html__( 'Please enter new tab title', 'js_composer' ),
			'press_ok_delete_section' => esc_html__( 'Press OK to delete "{tab_name}" section, Cancel to leave', 'js_composer' ),
			'section_default_title' => esc_html__( 'Section', 'js_composer' ),
			'please_enter_section_title' => esc_html__( 'Please enter new section title', 'js_composer' ),
			'error_please_try_again' => esc_html__( 'Error. Please try again.', 'js_composer' ),
			'if_close_data_lost' => esc_html__( 'If you close this window all shortcode settings will be lost. Close this window?', 'js_composer' ),
			'header_select_element_type' => esc_html__( 'Select element type', 'js_composer' ),
			'header_media_gallery' => esc_html__( 'Media gallery', 'js_composer' ),
			'header_element_settings' => esc_html__( 'Element settings', 'js_composer' ),
			'add_tab' => esc_html__( 'Add tab', 'js_composer' ),
			'are_you_sure_convert_to_new_version' => esc_html__( 'Are you sure you want to convert to new version?', 'js_composer' ),
			'loading' => esc_html__( 'Loading...', 'js_composer' ),
			// Media editor
			'set_image' => esc_html__( 'Set Image', 'js_composer' ),
			'are_you_sure_reset_css_classes' => esc_html__( 'Are you sure that you want to remove all your data?', 'js_composer' ),
			'loop_frame_title' => esc_html__( 'Loop settings', 'js_composer' ),
			'enter_custom_layout' => esc_html__( 'Custom row layout', 'js_composer' ),
			'wrong_cells_layout' => esc_html__( 'Wrong row layout format! Example: 1/2 + 1/2 or span6 + span6.', 'js_composer' ),
			'row_background_color' => esc_html__( 'Row background color', 'js_composer' ),
			'row_background_image' => esc_html__( 'Row background image', 'js_composer' ),
			'column_background_color' => esc_html__( 'Column background color', 'js_composer' ),
			'column_background_image' => esc_html__( 'Column background image', 'js_composer' ),
			'guides_on' => esc_html__( 'Guides ON', 'js_composer' ),
			'guides_off' => esc_html__( 'Guides OFF', 'js_composer' ),
			'template_save' => esc_html__( 'New template successfully saved.', 'js_composer' ),
			'template_added' => esc_html__( 'Template added to the page.', 'js_composer' ),
			'template_added_with_id' => esc_html__( 'Template added to the page. Template has ID attributes, make sure that they are not used more than once on the same page.', 'js_composer' ),
			'template_removed' => esc_html__( 'Template successfully removed.', 'js_composer' ),
			'template_is_empty' => esc_html__( 'Template is empty: There is no content to be saved as a template.', 'js_composer' ),
			'template_save_error' => esc_html__( 'Error while saving template.', 'js_composer' ),
			'page_settings_updated' => esc_html__( 'Page settings updated!', 'js_composer' ),
			'update_all' => esc_html__( 'Update all', 'js_composer' ),
			'confirm_to_leave' => esc_html__( 'The changes you made will be lost if you navigate away from this page.', 'js_composer' ),
			'inline_element_saved' => esc_html__( '%s saved!', 'js_composer' ),
			'inline_element_deleted' => esc_html__( '%s deleted!', 'js_composer' ),
            // phpcs:ignore
			'inline_element_cloned' => sprintf( __( '%%1$s cloned. %2$sEdit now?%s', 'js_composer' ), '<a href="#" class="vc_edit-cloned" data-model-id="%s">', '</a>' ),
			'gfonts_loading_google_font_failed' => esc_html__( 'Loading Google Font failed', 'js_composer' ),
			'gfonts_loading_google_font' => esc_html__( 'Loading Font...', 'js_composer' ),
			'gfonts_unable_to_load_google_fonts' => esc_html__( 'Unable to load Google Fonts', 'js_composer' ),
			'no_title_parenthesis' => sprintf( '(%s)', esc_html__( 'no title', 'js_composer' ) ),
			'error_while_saving_image_filtered' => esc_html__( 'Error while applying filter to the image. Check your server and memory settings.', 'js_composer' ),
			'ui_saved' => sprintf( '<i class="vc-composer-icon vc-c-icon-check"></i> %s', esc_html__( 'Saved!', 'js_composer' ) ),
			'ui_danger' => sprintf( '<i class="vc-composer-icon vc-c-icon-close"></i> %s', esc_html__( 'Failed to Save!', 'js_composer' ) ),
			'delete_preset_confirmation' => esc_html__( 'You are about to delete this preset. This action can not be undone.', 'js_composer' ),
			'ui_template_downloaded' => esc_html__( 'Downloaded', 'js_composer' ),
			'ui_template_update' => esc_html__( 'Update', 'js_composer' ),
			'ui_templates_failed_to_download' => esc_html__( 'Failed to download template', 'js_composer' ),
			'preset_removed' => esc_html__( 'Element successfully removed.', 'js_composer' ),
			'vc_successfully_updated' => esc_html__( 'Successfully updated!', 'js_composer' ),
			'gutenbergDoesntWorkProperly' => esc_html__( 'Gutenberg plugin doesn\'t work properly. Please check Gutenberg plugin.', 'js_composer' ),
			'unfiltered_html_access' => esc_html__( 'Custom HTML is disabled for your user role. Please contact your site Administrator to change your capabilities.', 'js_composer' ),
			'not_editable_post' => sprintf( '%s %s %s', esc_html__( 'This', 'js_composer' ), get_post_type() ? get_post_type() : 'post', esc_html__( 'can not be edited with WPBakery since it is missing a WordPress default content area.', 'js_composer' ) ),
			'ai_response_error' => esc_html__( 'An error occurred when requesting a response from WPBakery AI (Code: 623)', 'js_composer' ),
			'generate' => esc_html__( 'Generate', 'js_composer' ),
			'regenerate' => esc_html__( 'Regenerate', 'js_composer' ),
			'problems' => esc_html__( 'Problems', 'js_composer' ),
			'warnings' => esc_html__( 'Warnings', 'js_composer' ),
			'success' => esc_html__( 'Success', 'js_composer' ),
			'goodJob' => esc_html__( 'Good job!', 'js_composer' ),
			'wellDone' => esc_html__( 'Well done!', 'js_composer' ),
			'greatWork' => esc_html__( 'Great work!', 'js_composer' ),
			'great' => esc_html__( 'Great!', 'js_composer' ),
			'internalLinks' => esc_html__( 'Internal links', 'js_composer' ),
			'outboundLinks' => esc_html__( 'Outbound links', 'js_composer' ),
			'noOutboundLinks' => esc_html__( 'No outbound links appear in this page.', 'js_composer' ),
			'noInternalLinks' => esc_html__( 'No internal links appear in this page, make sure to add some!', 'js_composer' ),
			'images' => esc_html__( 'Images', 'js_composer' ),
			'noImages' => esc_html__( 'No images appear on this page. Add some!', 'js_composer' ),
			'focusKeywordTitle' => esc_html__( 'Focus keyphrase', 'js_composer' ),
			'noFocusKeyword' => esc_html__( 'No focus keyphrase was set for this page.', 'js_composer' ),
			'seoTitle' => esc_html__( 'SEO title width', 'js_composer' ),
			'seoTitleWidthTooLong' => esc_html__( 'The SEO title is wider than the viewable limit. Try to make it shorter.', 'js_composer' ),
			'textLength' => esc_html__( 'Text Length', 'js_composer' ),
			'textLengthLess' => esc_html__( 'The text contains %1$s words. This is %2$s the recommended minimum of 300 words.', 'js_composer' ),
			'textLengthSuccess' => esc_html__( 'The text contains %s words. Good job!', 'js_composer' ),
			'seoTitleEmpty' => esc_html__( 'Please create an SEO title.', 'js_composer' ),
			'seoDescription' => esc_html__( 'Meta description length', 'js_composer' ),
			'seoDescriptionEmpty' => esc_html__( 'No meta description has been specified. Search engines will display copy from the page instead. Make sure to write one!', 'js_composer' ),
			'seoDescriptionTooShort' => esc_html__( 'The meta description is too short (under 120 characters). Up to 156 characters are available. Use the space!', 'js_composer' ),
			'seoDescriptionTooLong' => esc_html__( 'The meta description is over 156 characters. To ensure the entire description will be visible, you should reduce the length!', 'js_composer' ),
			'keyphraseInTitleText' => esc_html__( 'Keyphrase in SEO title', 'js_composer' ),
			'keyphraseInTitleWarn' => esc_html__( 'The exact match of the focus keyphrase appears in the SEO title, but not at the beginning.', 'js_composer' ),
			'keyphraseInTitleEmpty' => esc_html__( 'Not all the words from your keyphrase "%1$s" appear in the SEO title.', 'js_composer' ),
			'keyphraseInDescriptionText' => esc_html__( 'Keyphrase in meta description', 'js_composer' ),
			'keyphraseInDescriptionEmpty' => esc_html__( 'The meta description has been specified, but it does not contain the keyphrase', 'js_composer' ),
			'keyphraseInDescriptionSuccess' => esc_html__( 'Keyphrase appears in the meta description. Well done!', 'js_composer' ),
			'keyphraseInSlug' => esc_html__( 'Keyphrase in slug', 'js_composer' ),
			'keyphraseInSlugProblem' => esc_html__( '(Part of) your keyphrase does not appear in the slug.', 'js_composer' ),
			'imageKeyphrase' => esc_html__( 'Image Keyphrase', 'js_composer' ),
			'imageKeyphraseTooMuch' => esc_html__( 'Out of %1$s images on this page, %2$s have alt attributes with words from your keyphrase or synonyms. That\'s a bit much. Only include the keyphrase or its synonyms when it really fits the image.', 'js_composer' ),
			'imageKeyphraseNotEnough' => esc_html__( 'Out of %1$s images on this page, only %2$s has an alt attribute that reflects the topic of your text. Add your keyphrase or synonyms to the alt tags of more relevant images!', 'js_composer' ),
			'imageKeyphraseMissing' => esc_html__( 'Images on this page do not have alt attributes with at least half of the words from your keyphrase.', 'js_composer' ),
			'keyphraseInIntroductionText' => esc_html__( 'Keyphrase in introduction', 'js_composer' ),
			'keyphraseInIntroductionEmpty' => esc_html__( 'Your keyphrase do not appear in the first paragraph.', 'js_composer' ),
			'keyphraseDensity' => esc_html__( 'Keyphrase density', 'js_composer' ),
			'keyphraseDensitySuccess' => esc_html__( 'The keyphrase was found %1$s times. This is great!', 'js_composer' ),
			'keyphraseDensityNotEnough' => esc_html__( 'The keyphrase was found %1$s time. That\'s less than the recommended minimum of %2$s times for a text of this length.', 'js_composer' ),
			'keyphraseDensityTooMuch' => esc_html__( 'The keyphrase was found %1$s times. That\'s way more than the recommended maximum of %2$s times for a text of this length.', 'js_composer' ),
			'consecutiveSentences' => esc_html__( 'Consecutive sentences', 'js_composer' ),
			'consecutiveSentencesSuccess' => esc_html__( 'There is enough variety in your sentences. That\'s great!', 'js_composer' ),
			'consecutiveSentencesFail' => esc_html__( 'The text contains %1$s consecutive sentences starting with the same word.', 'js_composer' ),
			'keyphraseInIntroductionEmpty' => esc_html__( 'Your keyphrase do not appear in the first paragraph.', 'js_composer' ),
			'passiveVoice' => esc_html__( 'Passive voice', 'js_composer' ),
			'passiveVoiceError' => esc_html__( '%s of the sentences contain passive voice, which is more than the recommended maximum of 10%', 'js_composer' ),
			'passiveVoiceSuccess' => esc_html__( 'You\'re using enough active voice. That\'s great!', 'js_composer' ),
			'paragraphLength' => esc_html__( 'Paragraph length', 'js_composer' ),
			'paragraphLengthError' => esc_html__( '%s of the paragraphs contains more than the recommended maximum of 150 words.', 'js_composer' ),
			'paragraphLengthSuccess' => esc_html__( 'None of the paragraphs are too long. Great job!', 'js_composer' ),
			'sentenceLength' => esc_html__( 'Sentence length', 'js_composer' ),
			'sentenceLengthError' => esc_html__( '%s%% of the sentences contain more than 20 words, which is more than the recommended maximum of 25%.', 'js_composer' ),
			'subheadingDistribution' => esc_html__( 'Subheading distribution', 'js_composer' ),
			'subheadingDistributionFail' => esc_html__( 'You are not using any subheadings, although your text is rather long. Try and add some subheadings.', 'js_composer' ),
			'subheadingDistributionWarn' => esc_html__( '%s section of your text is longer than 300 words and is not separated by any subheadings. Add subheadings to improve readability.', 'js_composer' ),
			'previouslyUsedKeyphrase' => esc_html__( 'Previously used keyphrase', 'js_composer' ),
			'previouslyUsedKeyphraseSuccess' => esc_html__( 'You\'ve not used this keyphrase before, very good.', 'js_composer' ),
			'previouslyUsedKeyphraseWarn' => esc_html__( 'You\'ve used this keyphrase before', 'js_composer' ),
		);
	}

	/**
	 * Add custom html to the header tag of the page.
	 *
	 * @since 7.0
	 */
	public function outputGlobalHeaderCustomHtml() {
		$global_header_html = get_option( Vc_Settings::$field_prefix . 'custom_js_header' );

		echo '<script>';
		echo wp_unslash( $global_header_html );
		echo '</script>';
	}

	/**
	 * Add custom html to the footer tag of the page.
	 *
	 * @since 7.0
	 */
	public function outputGlobalFooterCustomHtml() {
		$global_footer_html = get_option( Vc_Settings::$field_prefix . 'custom_js_footer' );

		echo '<script>';
		echo wp_unslash( $global_footer_html );
		echo '</script>';
	}

	/**
	 * Add post custom html to the header tag of the page.
	 *
	 * @since 7.0
	 */
	public function outputPostHeaderCustomJs() {
		$id = $this->get_post_id();

		if ( ! $id ) {
			return;
		}

		$post_header_html = get_post_meta( $id, '_wpb_post_custom_js_header', true );

		if ( empty( $post_header_html ) ) {
			return;
		}

		$this->outputCustomJs( $post_header_html, 'header' );
	}

	/**
	 * Add post custom html to the footer tag of the page.
	 *
	 * @since 7.0
	 */
	public function outputPostFooterCustomJs() {
		$id = $this->get_post_id();

		if ( ! $id ) {
			return;
		}

		$post_footer_html = get_post_meta( $id, '_wpb_post_custom_js_footer', true );

		if ( empty( $post_footer_html ) ) {
			return;
		}

		$this->outputCustomJs( $post_footer_html, 'footer' );
	}

	/**
	 * Output custom on a page.
	 *
	 * @since 7.0
	 * @param string $js
	 * @param string $area
	 */
	public function outputCustomJs( $js, $area ) {
		echo '<script data-type="vc_custom-js-"' . $area . '>';
		// we need to wait for iframe load on frontend editor side.
		if ( vc_is_page_editable() ) {
			echo 'setTimeout(() => {';
			echo wp_unslash( $js );
			echo '}, 2000);';
		} else {
			echo wp_unslash( $js );
		}
		echo '</script>';
	}
}
