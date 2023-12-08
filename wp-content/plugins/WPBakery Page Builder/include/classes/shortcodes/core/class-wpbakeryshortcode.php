<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode
 */
abstract class WPBakeryShortCode {
	/**
	 * @var
	 */
	public static $config;
	/**
	 * @var string
	 */
	protected $controls_css_settings = 'cc';

	/**
	 * Backend section controls.
	 *
	 * @note for a frontend editor section controls please see
	 * include/templates/editors/partials/frontend_controls.tpl.php
	 *
	 * @var array
	 */
	protected $controls_list = array(
		'edit',
		'clone',
		'copy',
		'delete',
	);

	/**
	 * @var string
	 */
	protected $shortcode_content = '';

	/**
	 * @var string - shortcode tag
	 */
	protected $shortcode;
	/**
	 * @var
	 */
	protected $html_template;

	/**
	 * @var
	 */
	protected $atts;

	/**
	 * @var
	 */
	protected $settings;

	/**
	 * @var array
	 */
	protected static $js_scripts = array();
	/**
	 * @var array
	 */
	protected static $css_scripts = array();
	/**
	 * default scripts like scripts
	 * @var bool
	 * @since 4.4.3
	 */
	protected static $default_scripts_enqueued = false;
	/**
	 * @var string
	 */
	protected $shortcode_string = '';
	/**
	 * @var string
	 */
	protected $controls_template_file = 'editors/partials/backend_controls.tpl.php';

	public $nonDraggableClass = 'vc-non-draggable';

	/**
	 * @param $settings
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
		$this->shortcode = $this->settings['base'];
	}

	/**
	 * @param $settings
	 * @deprecated not used
	 */
	public function init( $settings ) {
		self::$config = (array) $settings;
	}

	/**
	 * @param $action
	 * @param $method
	 * @param int $priority
	 * @return true|void
	 * @deprecated 6.0 use native WordPress actions
	 */
	public function addAction( $action, $method, $priority = 10 ) {
		return add_action( $action, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $action
	 * @param $method
	 * @param int $priority
	 *
	 * @return bool
	 * @deprecated 6.0 use native WordPress actions
	 *
	 */
	public function removeAction( $action, $method, $priority = 10 ) {
		return remove_action( $action, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $filter
	 * @param $method
	 * @param int $priority
	 *
	 * @return bool|void
	 * @deprecated 6.0 use native WordPress actions
	 *
	 */
	public function addFilter( $filter, $method, $priority = 10 ) {
		return add_filter( $filter, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $filter
	 * @param $method
	 * @param int $priority
	 * @return bool
	 * @deprecated 6.0 use native WordPress
	 *
	 */
	public function removeFilter( $filter, $method, $priority = 10 ) {
		return remove_filter( $filter, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $tag
	 * @param $func
	 * @deprecated 6.0 not used
	 *
	 */
	public function addShortCode( $tag, $func ) {
		// this function is deprecated since 6.0
	}

	/**
	 * @param $content
	 * @deprecated 6.0 not used
	 *
	 */
	public function doShortCode( $content ) {
		// this function is deprecated since 6.0
	}

	/**
	 * @param $tag
	 * @deprecated 6.0 not used
	 *
	 */
	public function removeShortCode( $tag ) {
		// this function is deprecated since 6.0
	}

	/**
	 * @param $param
	 *
	 * @return null
	 * @deprecated 6.0 not used, use vc_post_param
	 *
	 */
	public function post( $param ) {
		// this function is deprecated since 6.0

		return vc_post_param( $param );
	}

	/**
	 * @param $param
	 *
	 * @return null
	 * @deprecated 6.0 not used, use vc_get_param
	 *
	 */
	public function get( $param ) {
		// this function is deprecated since 6.0

		return vc_get_param( $param );
	}

	/**
	 * @param $asset
	 *
	 * @return string
	 * @deprecated 4.5 use vc_asset_url
	 *
	 */
	public function assetURL( $asset ) {
		// this function is deprecated since 4.5

		return vc_asset_url( $asset );
	}

	/**
	 * @param $asset
	 *
	 * @return string
	 * @deprecated 6.0 not used
	 */
	public function assetPath( $asset ) {
		// this function is deprecated since 6.0

		return self::$config['APP_ROOT'] . self::$config['ASSETS_DIR'] . $asset;
	}

	/**
	 * @param $name
	 *
	 * @return null
	 * @deprecated 6.0 not used
	 */
	public static function config( $name ) {
		return isset( self::$config[ $name ] ) ? self::$config[ $name ] : null;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function addInlineAnchors( $content ) {
		return ( $this->isInline() || $this->isEditor() && true === $this->settings( 'is_container' ) ? '<span class="vc_container-anchor"></span>' : '' ) . $content;
	}

	/**
	 *
	 */
	public function enqueueAssets() {
		if ( ! empty( $this->settings['admin_enqueue_js'] ) ) {
			$this->registerJs( $this->settings['admin_enqueue_js'] );
		}
		if ( ! empty( $this->settings['admin_enqueue_css'] ) ) {
			$this->registerCss( $this->settings['admin_enqueue_css'] );
		}
	}

	/**
	 * Prints out the styles needed to render the element icon for the back end interface.
	 * Only performed if the 'icon' setting is a valid URL.
	 *
	 * @return void
	 * @since  4.2
	 * @modified 4.4
	 * @author Benjamin Intal
	 */
	public function printIconStyles() {
		if ( ! filter_var( $this->settings( 'icon' ), FILTER_VALIDATE_URL ) ) {
			return;
		}
		$first_tag = 'style';
		echo '
            <' . esc_attr( $first_tag ) . '>
                .vc_el-container #' . esc_attr( $this->settings['base'] ) . ' .vc_element-icon,
                .wpb_' . esc_attr( $this->settings['base'] ) . ' > .wpb_element_wrapper > .wpb_element_title > .vc_element-icon,
                .vc_el-container > #' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon,
                .vc_el-container > #' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon[data-is-container="true"],
                .compose_mode .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon,
                .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon,
                .compose_mode .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon[data-is-container="true"],
                .vc_helper.vc_helper-' . esc_attr( $this->settings['base'] ) . ' > .vc_element-icon[data-is-container="true"],
                .wpb_' . esc_attr( $this->settings['base'] ) . ' > .wpb_element_wrapper > .wpb_element_title > .vc_element-icon,
                .wpb_' . esc_attr( $this->settings['base'] ) . ' > .wpb_element_wrapper > .wpb_element_title > .vc_element-icon[data-is-container="true"] {
                    background-position: 0 0;
                    background-image: url(' . esc_url( $this->settings['icon'] ) . ');
                    -webkit-background-size: contain;
                    -moz-background-size: contain;
                    -ms-background-size: contain;
                    -o-background-size: contain;
                    background-size: contain;
                }
            </' . esc_attr( $first_tag ) . '>';
	}

	/**
	 * @param $param
	 */
	protected function registerJs( $param ) {
		if ( is_array( $param ) && ! empty( $param ) ) {
			foreach ( $param as $value ) {
				$this->registerJs( $value );
			}
		} elseif ( is_string( $param ) && ! empty( $param ) ) {
			$name = 'admin_enqueue_js_' . md5( $param );
			self::$js_scripts[] = $name;
			wp_register_script( $name, $param, array( 'jquery-core' ), WPB_VC_VERSION, true );
		}
	}

	/**
	 * @param $param
	 */
	protected function registerCss( $param ) {
		if ( is_array( $param ) && ! empty( $param ) ) {
			foreach ( $param as $value ) {
				$this->registerCss( $value );
			}
		} elseif ( is_string( $param ) && ! empty( $param ) ) {
			$name = 'admin_enqueue_css_' . md5( $param );
			self::$css_scripts[] = $name;
			wp_register_style( $name, $param, array( 'js_composer' ), WPB_VC_VERSION );
		}
	}

	/**
	 *
	 */
	public static function enqueueCss() {
		if ( ! empty( self::$css_scripts ) ) {
			foreach ( self::$css_scripts as $stylesheet ) {
				wp_enqueue_style( $stylesheet );
			}
		}
	}

	/**
	 *
	 */
	public static function enqueueJs() {
		if ( ! empty( self::$js_scripts ) ) {
			foreach ( self::$js_scripts as $script ) {
				wp_enqueue_script( $script );
			}
		}
	}

	/**
	 * @param $shortcode
	 */
	public function shortcode( $shortcode ) {

	}

	/**
	 * @param $template
	 *
	 * @return string
	 */
	protected function setTemplate( $template ) {
		return $this->html_template = apply_filters( 'vc_shortcode_set_template_' . $this->shortcode, $template );
	}

	/**
	 * @return bool
	 */
	protected function getTemplate() {
		if ( isset( $this->html_template ) ) {
			return $this->html_template;
		}

		return false;
	}

	/**
	 * @return mixed
	 */
	protected function getFileName() {
		return $this->shortcode;
	}

	/**
	 * Find html template for shortcode output.
	 */
	protected function findShortcodeTemplate() {
		// Check template path in shortcode's mapping settings
		if ( ! empty( $this->settings['html_template'] ) && is_file( $this->settings( 'html_template' ) ) ) {
			return $this->setTemplate( $this->settings['html_template'] );
		}

		// Check template in theme directory
		$user_template = vc_shortcodes_theme_templates_dir( $this->getFileName() . '.php' );
		if ( is_file( $user_template ) ) {
			return $this->setTemplate( $user_template );
		}

		// Check default place
		$default_dir = vc_manager()->getDefaultShortcodesTemplatesDir() . '/';
		if ( is_file( $default_dir . $this->getFileName() . '.php' ) ) {
			return $this->setTemplate( $default_dir . $this->getFileName() . '.php' );
		}
		$template = apply_filters( 'vc_shortcode_set_template_' . $this->shortcode, '' );

		if ( ! empty( $template ) ? $template : '' ) {
			return $this->setTemplate( $template );
		}

		return '';
	}

	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function content( $atts, $content = null ) {
		return $this->loadTemplate( $atts, $content );
	}

	/**
	 * @param $atts
	 * @param null $content
	 *
	 * vc_filter: vc_shortcode_content_filter - hook to edit template content
	 * vc_filter: vc_shortcode_content_filter_after - hook after template is loaded to override output
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function loadTemplate( $atts, $content = null ) {
		$output = '';
		if ( ! is_null( $content ) ) {
			/** @var string $content */
			$content = apply_filters( 'vc_shortcode_content_filter', $content, $this->shortcode, $atts );
		}
		$this->findShortcodeTemplate();
		if ( $this->html_template && file_exists( $this->html_template ) ) {
			if ( strpos( $this->html_template, WPB_PLUGIN_DIR ) === false ) {
				// Modified or new
				Vc_Modifications::$modified = true;
			}
			ob_start();
			/** @var string $content - used inside template */
			$output = require $this->html_template;
			// Allow return in template files
			if ( 1 === $output ) {
				$output = ob_get_contents();
			}
			ob_end_clean();
		}

		return apply_filters( 'vc_shortcode_content_filter_after', $output, $this->shortcode, $atts, $content );
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function contentAdmin( $atts, $content = null ) {
		$output = $custom_markup = $width = $el_position = '';
		if ( null !== $content ) {
			$content = wpautop( stripslashes( $content ) );
		}
		$shortcode_attributes = array( 'width' => '1/1' );
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts ) + $shortcode_attributes;
		$this->atts = $atts;
		$elem = $this->getElementHolder( $width );
		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
			$markup = $this->settings['custom_markup'];
			$elem = str_ireplace( '%wpb_element_content%', $this->customMarkup( $markup, $content ), $elem );
			$output .= $elem;
		} else {
			$inner = $this->outputTitle( $this->settings['name'] );
			$inner .= $this->paramsHtmlHolders( $atts );
			$elem = str_ireplace( '%wpb_element_content%', $inner, $elem );
			$output .= $elem;
		}

		return $output;
	}

	/**
	 * @return bool
	 */
	public function isAdmin() {
		return apply_filters( 'vc_shortcodes_is_admin', is_admin() );
	}

	/**
	 * @return bool
	 */
	public function isInline() {
		return vc_is_inline();
	}

	/**
	 * @return bool
	 */
	public function isEditor() {
		return vc_is_editor();
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @param string $base
	 *
	 * vc_filter: vc_shortcode_output - hook to override output of shortcode
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function output( $atts, $content = null, $base = '' ) {
		$this->atts = $prepared_atts = $this->prepareAtts( $atts );
		$this->shortcode_content = $content;
		$output = '';
		$content = empty( $content ) && ! empty( $atts['content'] ) ? $atts['content'] : $content;
		if ( ( $this->isInline() || vc_is_page_editable() ) && method_exists( $this, 'contentInline' ) ) {
			$output .= $this->contentInline( $this->atts, $content );
		} else {
			$this->enqueueDefaultScripts();
			$custom_output = VC_SHORTCODE_CUSTOMIZE_PREFIX . $this->shortcode;
			$custom_output_before = VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX . $this->shortcode; // before shortcode function hook
			$custom_output_after = VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX . $this->shortcode; // after shortcode function hook

			// Before shortcode
			if ( function_exists( $custom_output_before ) ) {
				$output .= $custom_output_before( $this->atts, $content );
			} else {
				$output .= $this->beforeShortcode( $this->atts, $content );
			}
			// Shortcode content
			if ( function_exists( $custom_output ) ) {
				$output .= $custom_output( $this->atts, $content );
			} else {
				$output .= $this->content( $this->atts, $content );
			}
			// After shortcode
			if ( function_exists( $custom_output_after ) ) {
				$output .= $custom_output_after( $this->atts, $content );
			} else {
				$output .= $this->afterShortcode( $this->atts, $content );
			}
		}
		// Filter for overriding outputs
		$output = apply_filters( 'vc_shortcode_output', $output, $this, $prepared_atts, $this->shortcode );

		return $output;
	}

	public function enqueueDefaultScripts() {
		if ( false === self::$default_scripts_enqueued ) {
			wp_enqueue_script( 'wpb_composer_front_js' );
			wp_enqueue_style( 'js_composer_front' );
			self::$default_scripts_enqueued = true;
		}
	}

	/**
	 * Return shortcode attributes, see \WPBakeryShortCode::output
	 * @return array
	 * @since 4.4
	 */
	public function getAtts() {
		return $this->atts;
	}

	/**
	 * Creates html before shortcode html.
	 *
	 * @param $atts - shortcode attributes list
	 * @param $content - shortcode content
	 *
	 * @return string - html which will be displayed before shortcode html.
	 */
	public function beforeShortcode( $atts, $content ) {
		return '';
	}

	/**
	 * Creates html before shortcode html.
	 *
	 * @param $atts - shortcode attributes list
	 * @param $content - shortcode content
	 *
	 * @return string - html which will be displayed after shortcode html.
	 */
	public function afterShortcode( $atts, $content ) {
		return '';
	}

	/**
	 * @param $el_class
	 *
	 * @return string
	 */
	public function getExtraClass( $el_class ) {
		$output = '';
		if ( '' !== $el_class ) {
			$output = ' ' . str_replace( '.', '', $el_class );
		}

		return $output;
	}

	/**
	 * @param $css_animation
	 *
	 * @return string
	 */
	public function getCSSAnimation( $css_animation ) {
		$output = '';
		if ( '' !== $css_animation && 'none' !== $css_animation ) {
			wp_enqueue_script( 'vc_waypoints' );
			wp_enqueue_style( 'vc_animate-css' );
			$output = ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
		}

		return $output;
	}

	/**
	 * Create HTML comment for blocks only if wpb_debug=true
	 *
	 * @param $string
	 *
	 * @return string
	 * @deprecated 4.7 For debug type html comments use more generic debugComment function.
	 *
	 */
	public function endBlockComment( $string ) {
		return '';
	}

	/**
	 * if wpb_debug=true return HTML comment
	 *
	 * @param string $comment
	 *
	 * @return string
	 * @since 4.7
	 * @deprecated 5.5 no need for extra info in output, use xdebug
	 */
	public function debugComment( $comment ) {
		return '';
	}

	/**
	 * @param $name
	 *
	 * @return null
	 */
	public function settings( $name ) {
		return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function setSettings( $name, $value ) {
		$this->settings[ $name ] = $value;
	}

	/**
	 * @return mixed
	 * @since 5.5
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param $width
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getElementHolder( $width ) {
		$output = '';
		$column_controls = $this->getColumnControlsModular();
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );
		$css_class = 'wpb_' . $this->settings['base'] . ' wpb_content_element ' . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );
		$output .= '<div data-element_type="' . $this->settings['base'] . '" class="' . $css_class . '">';
		$output .= str_replace( '%column_size%', wpb_translateColumnWidthToFractional( $width ), $column_controls );
		$output .= $this->getCallbacks( $this->shortcode );
		$output .= '<div class="wpb_element_wrapper ' . $this->settings( 'wrapper_class' ) . '">';
		$output .= '%wpb_element_content%';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	// Return block controls

	/**
	 * @param $controls
	 * @param string $extended_css
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControls( $controls, $extended_css = '' ) {
		$controls_start = '<div class="vc_controls controls_element' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';

		$controls_end = '</div>';

		$controls_add = '';
		$controls_edit = ' <a class="vc_control column_edit" href="javascript:;" title="' . sprintf( esc_attr__( 'Edit %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><span class="vc_icon"></span></a>';
		$controls_delete = ' <a class="vc_control column_clone" href="javascript:;" title="' . sprintf( esc_attr__( 'Clone %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><span class="vc_icon"></span></a> <a class="column_delete" href="javascript:;" title="' . sprintf( esc_attr__( 'Delete %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><span class="vc_icon"></span></a>';

		$column_controls_full = $controls_start . $controls_add . $controls_edit . $controls_delete . $controls_end;
		$column_controls_size_delete = $controls_start . $controls_delete . $controls_end;
		$column_controls_popup_delete = $controls_start . $controls_delete . $controls_end;
		$column_controls_edit_popup_delete = $controls_start . $controls_edit . $controls_delete . $controls_end;
		$column_controls_edit = $controls_start . $controls_edit . $controls_end;

		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );

		if ( 'popup_delete' === $controls ) {
			return $allAccess ? $column_controls_popup_delete : '';
		} elseif ( 'edit_popup_delete' === $controls ) {
			return $allAccess ? $column_controls_edit_popup_delete : ( $editAccess ? $column_controls_edit : '' );
		} elseif ( 'size_delete' === $controls ) {
			return $allAccess ? $column_controls_size_delete : '';
		} elseif ( 'add' === $controls ) {
			return $allAccess ? ( $controls_start . $controls_add . $controls_end ) : '';
		} else {
			return $allAccess ? $column_controls_full : ( $editAccess ? $column_controls_edit : '' );
		}
	}

	/**
	 * Return list of controls
	 * @return array
	 * @throws \Exception
	 */
	public function getControlsList() {
		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
		if ( $allAccess ) {
			return apply_filters( 'vc_wpbakery_shortcode_get_controls_list', $this->controls_list, $this->shortcode );
		} else {
			$controls = apply_filters( 'vc_wpbakery_shortcode_get_controls_list', $this->controls_list, $this->shortcode );
			if ( $editAccess ) {
				foreach ( $controls as $key => $value ) {
					if ( 'edit' !== $value && 'add' !== $value ) {
						unset( $controls[ $key ] );
					}
				}

				return $controls;
			} else {
				return in_array( 'add', $controls, true ) ? array( 'add' ) : array();
			}
		}
	}

	/**
	 * Build new modern controls for shortcode.
	 *
	 * @param string $extended_css
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControlsModular( $extended_css = '' ) {
		ob_start();
		vc_include_template( apply_filters( 'vc_wpbakery_shortcode_get_column_controls_modular_template', $this->controls_template_file ), array(
			'shortcode' => $this->shortcode,
			'position' => $this->controls_css_settings,
			'extended_css' => $extended_css,
			'name' => $this->settings( 'name' ),
			'controls' => $this->getControlsList(),
			'name_css_class' => $this->getBackendEditorControlsElementCssClass(),
			'add_allowed' => $this->getAddAllowed(),
		) );

		return ob_get_clean();
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getBackendEditorControlsElementCssClass() {
		$moveAccess = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();

		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) && $moveAccess ? ' vc_element-move' : ' ' . $this->nonDraggableClass );

		return 'vc_control-btn vc_element-name' . $sortable;
	}

	/**
	 * This will fire callbacks if they are defined in map.php
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public function getCallbacks( $id ) {
		$output = '';

		if ( isset( $this->settings['js_callback'] ) ) {
			foreach ( $this->settings['js_callback'] as $text_val => $val ) {
				// TODO: name explain
				$output .= '<input type="hidden" class="wpb_vc_callback wpb_vc_' . esc_attr( $text_val ) . '_callback " name="' . esc_attr( $text_val ) . '" value="' . $val . '" />';
			}
		}

		return $output;
	}

	/**
	 * @param $param
	 * @param $value
	 *
	 * vc_filter: vc_wpbakeryshortcode_single_param_html_holder_value - hook to override param value (param type and etc is available in args)
	 *
	 * @return string
	 */
	public function singleParamHtmlHolder( $param, $value ) {
		$value = apply_filters( 'vc_wpbakeryshortcode_single_param_html_holder_value', $value, $param, $this->settings, $this->atts );
		$output = '';
		// Compatibility fixes
		$old_names = array(
			'yellow_message',
			'blue_message',
			'green_message',
			'button_green',
			'button_grey',
			'button_yellow',
			'button_blue',
			'button_red',
			'button_orange',
		);
		$new_names = array(
			'alert-block',
			'alert-info',
			'alert-success',
			'btn-success',
			'btn',
			'btn-info',
			'btn-primary',
			'btn-danger',
			'btn-warning',
		);
		$value = str_ireplace( $old_names, $new_names, $value );
		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';
		if ( ! empty( $param['holder'] ) ) {
			if ( 'input' === $param['holder'] ) {
				$output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
			} elseif ( in_array( $param['holder'], array(
				'img',
				'iframe',
			), true ) ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . esc_url( $value ) . '">';
			} elseif ( 'hidden' !== $param['holder'] ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
		}
		if ( ! empty( $param['admin_label'] ) && true === $param['admin_label'] ) {
			$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . $param['heading'] . '</label>: ' . $value . '</span>';
		}

		return $output;
	}

	/**
	 * @param $params
	 *
	 * @return string
	 */
	protected function getIcon( $params ) {
		$data = '';
		if ( isset( $params['is_container'] ) && true === $params['is_container'] ) {
			$data = ' data-is-container="true"';
		}
		$title = '';
		if ( isset( $params['title'] ) ) {
			$title = 'title="' . esc_attr( $params['title'] ) . '" ';
		}

		return '<i ' . $title . ' class="vc_general vc_element-icon' . ( ! empty( $params['icon'] ) ? ' ' . sanitize_text_field( $params['icon'] ) : '' ) . '"' . $data . '></i> ';
	}

	/**
	 * @param $title
	 *
	 * @return string
	 */
	protected function outputTitle( $title ) {
		$icon = $this->settings( 'icon' );
		if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
			$icon = '';
		}
		$params = array(
			'icon' => $icon,
			'is_container' => $this->settings( 'is_container' ),
		);

		return '<h4 class="wpb_element_title"> ' . $this->getIcon( $params ) . esc_attr( $title ) . '</h4>';
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function template( $content = '' ) {
		return $this->contentAdmin( $this->atts, $content );
	}

	/**
	 * This functions prepares attributes to use in template
	 * Converts back escaped characters
	 *
	 * @param $atts
	 *
	 * @return array
	 */
	protected function prepareAtts( $atts ) {
		$returnAttributes = array();
		if ( is_array( $atts ) ) {
			foreach ( $atts as $key => $val ) {
				$returnAttributes[ $key ] = str_replace( array(
					'`{`',
					'`}`',
					'``',
				), array(
					'[',
					']',
					'"',
				), $val );
			}
		}

		return apply_filters( 'vc_shortcode_prepare_atts', $returnAttributes, $this->shortcode, $this->settings );
	}

	/**
	 * @return string
	 */
	public function getShortcode() {
		return $this->shortcode;
	}

	/**
	 * Possible placeholders:
	 *      {{ content }}
	 *      {{ title }}
	 *      {{ container-class }}
	 *
	 * Possible keys:
	 *  {{
	 *  <%
	 *  %
	 * @param $markup
	 * @param string $content
	 *
	 * @return string
	 * @throws \Exception
	 * @since 4.5
	 */
	protected function customMarkup( $markup, $content = '' ) {
		$pattern = '/\{\{([\s\S][^\n]+?)\}\}|<%([\s\S][^\n]+?)%>|%([\s\S][^\n]+?)%/';
		preg_match_all( $pattern, $markup, $matches, PREG_SET_ORDER );
		if ( is_array( $matches ) && ! empty( $matches ) ) {
			foreach ( $matches as $match ) {
				switch ( strtolower( trim( $match[1] ) ) ) {
					case 'content':
						if ( '' !== $content ) {
							$markup = str_replace( $match[0], $content, $markup );
						} elseif ( isset( $this->settings['default_content_in_template'] ) && '' !== $this->settings['default_content_in_template'] ) {
							$markup = str_replace( $match[0], $this->settings['default_content_in_template'], $markup );
						} else {
							$markup = str_replace( $match[0], '', $markup );
						}
						break;
					case 'title':
						$markup = str_replace( $match[0], $this->outputTitle( $this->settings['name'] ), $markup );
						break;
					case 'container-class':
						if ( method_exists( $this, 'containerContentClass' ) ) {
							$markup = str_replace( $match[0], $this->containerContentClass(), $markup );
						} else {
							$markup = str_replace( $match[0], '', $markup );
						}
						break;
					case 'editor_controls':
						$markup = str_replace( $match[0], $this->getColumnControls( $this->settings( 'controls' ) ), $markup );
						break;
					case 'editor_controls_bottom_add':
						$markup = str_replace( $match[0], $this->getColumnControls( 'add', 'bottom-controls' ), $markup );
						break;
				}
			}
		}

		return do_shortcode( $markup );
	}

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	protected function paramsHtmlHolders( $atts ) {
		$inner = '';
		if ( isset( $this->settings['params'] ) && is_array( $this->settings['params'] ) ) {
			foreach ( $this->settings['params'] as $param ) {
				$param_value = isset( $atts[ $param['param_name'] ] ) ? $atts[ $param['param_name'] ] : '';
				$inner .= $this->singleParamHtmlHolder( $param, $param_value );
			}
		}

		return $inner;
	}

	/**
	 * Check is allowed to add another element inside current element.
	 *
	 * @return bool
	 * @since 4.8
	 *
	 */
	public function getAddAllowed() {
		return true;
	}
}

