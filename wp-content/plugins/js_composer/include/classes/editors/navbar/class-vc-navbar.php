<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Renders navigation bar for Editors.
 */
class Vc_Navbar {
	/**
	 * @var array
	 */
	protected $controls = array(
		'add_element',
		'templates',
		'save_backend',
		'preview',
		'frontend',
		'custom_css',
		'fullscreen',
		'windowed',
	);
	/**
	 * @var string
	 */
	protected $brand_url = 'https://wpbakery.com/?utm_campaign=VCplugin&utm_source=vc_user&utm_medium=backend_editor';
	/**
	 * @var string
	 */
	protected $css_class = 'vc_navbar';
	/**
	 * @var string
	 */
	protected $controls_filter_name = 'vc_nav_controls';
	/**
	 * @var bool|WP_Post
	 */
	protected $post = false;

	/**
	 * @param WP_Post $post
	 */
	public function __construct( WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Generate array of controls by iterating property $controls list.
	 * vc_filter: vc_nav_controls - hook to override list of controls
	 * @return array - list of arrays witch contains key name and html output for button.
	 */
	public function getControls() {
		$control_list = array();
		foreach ( $this->controls as $control ) {
			$method = vc_camel_case( 'get_control_' . $control );
			if ( method_exists( $this, $method ) ) {
				$control_list[] = array(
					$control,
					$this->$method(),
				);
			}
		}

		return apply_filters( $this->controls_filter_name, $control_list );
	}

	/**
	 * Get current post.
	 * @return null|WP_Post
	 */
	public function post() {
		if ( $this->post ) {
			return $this->post;
		} else {
			$this->post = get_post();
		}

		return $this->post;
	}

	/**
	 * Render template.
	 */
	public function render() {
		vc_include_template( 'editors/navbar/navbar.tpl.php', array(
			'css_class' => $this->css_class,
			'controls' => $this->getControls(),
			'nav_bar' => $this,
			'post' => $this->post(),
		) );
	}

	/**
	 * vc_filter: vc_nav_front_logo - hook to override WPBakery Page Builder logo
	 * @return string
	 */
	public function getLogo() {
		$output = '<a id="vc_logo" class="vc_navbar-brand" title="' . esc_attr__( 'WPBakery Page Builder', 'js_composer' ) . '" href="' . esc_url( $this->brand_url ) . '" target="_blank">' . esc_attr__( 'WPBakery Page Builder', 'js_composer' ) . '</a>';

		return apply_filters( 'vc_nav_front_logo', $output );
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getControlCustomCss() {
		if ( ! vc_user_access()->part( 'post_settings' )->can()->get() ) {
			return '';
		}

		return '<li class="vc_pull-right"><a id="vc_post-settings-button" href="javascript:;" class="vc_icon-btn vc_post-settings" title="' . esc_attr__( 'Page settings', 'js_composer' ) . '">' . '<span id="vc_post-css-badge" class="vc_badge vc_badge-custom-css" style="display: none;">' . esc_attr__( 'CSS', 'js_composer' ) . '</span><i class="vc-composer-icon vc-c-icon-cog"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlFullscreen() {
		return '<li class="vc_show-mobile vc_pull-right">' . '<a id="vc_fullscreen-button" class="vc_icon-btn vc_fullscreen-button" title="' . esc_attr__( 'Full screen', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-fullscreen"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlWindowed() {
		return '<li class="vc_show-mobile vc_pull-right">' . '<a id="vc_windowed-button" class="vc_icon-btn vc_windowed-button" title="' . esc_attr__( 'Exit full screen', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-fullscreen_exit"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getControlAddElement() {
		if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )
				->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) {
			return '<li class="vc_show-mobile">' . '	<a href="javascript:;" class="vc_icon-btn vc_element-button" data-model-id="vc_element" id="vc_add-new-element" title="' . '' . esc_attr__( 'Add new element', 'js_composer' ) . '">' . '    <i class="vc-composer-icon vc-c-icon-add_element"></i>' . '	</a>' . '</li>';
		}

		return '';
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getControlTemplates() {
		if ( ! vc_user_access()->part( 'templates' )->can()->get() ) {
			return '';
		}

		return '<li><a href="javascript:;" class="vc_icon-btn vc_templates-button"  id="vc_templates-editor-button" title="' . esc_attr__( 'Templates', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-add_template"></i></a></li>';
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getControlFrontend() {
		if ( ! vc_enabled_frontend() ) {
			return '';
		}

		return '<li class="vc_pull-right" style="display: none;">' . '<a href="' . esc_url( vc_frontend_editor()->getInlineUrl() ) . '" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn" id="wpb-edit-inline">' . esc_html__( 'Frontend', 'js_composer' ) . '</a>' . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlPreview() {
		return '';
	}

	/**
	 * @return string
	 */
	public function getControlSaveBackend() {
		$post = $this->post();
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );

		if ( in_array( get_post_status( $post ), array(
			'publish',
			'future',
			'private',
		) ) ) {
			$save_text = esc_html__( 'Update', 'js_composer' );
		} else if ( $can_publish ) {
			$save_text = esc_html__( 'Publish', 'js_composer' );
		} else {
			$save_text = esc_html__( 'Submit for Review', 'js_composer' );
		}

		return '<li class="vc_pull-right vc_save-backend">' . '<a href="javascript:;" class="vc_btn vc_btn-grey vc_btn-sm vc_navbar-btn vc_control-preview">' . esc_attr__( 'Preview', 'js_composer' ) . '</a>' . '<a class="vc_btn vc_btn-sm vc_navbar-btn vc_btn-primary vc_control-save" id="wpb-save-post">' . $save_text . '</a>' . '</li>';
	}
}
