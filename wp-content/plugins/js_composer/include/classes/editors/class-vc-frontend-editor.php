<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder front end editor
 *
 * @package WPBakeryPageBuilder
 *
 */

/**
 * Vc front end editor.
 *
 * Introduce principles ‘What You See Is What You Get’ into your page building process with our amazing frontend editor.
 * See how your content will look on the frontend instantly with no additional clicks or switches.
 *
 * @since   4.0
 */
class Vc_Frontend_Editor {
	/**
	 * @var
	 */
	protected $dir;
	/**
	 * @var int
	 */
	protected $tag_index = 1;
	/**
	 * @var array
	 */
	public $post_shortcodes = array();
	/**
	 * @var string
	 */
	protected $template_content = '';
	/**
	 * @var bool
	 */
	protected static $enabled_inline = true;

	/**
	 * @var
	 */
	public $current_user;
	/**
	 * @var
	 */
	public $post;
	/**
	 * @var
	 */
	public $post_id;
	/**
	 * @var
	 */
	public $post_url;
	/**
	 * @var
	 */
	public $url;
	/**
	 * @var
	 */
	public $post_type;
	/**
	 * @var array
	 */
	protected $settings = array(
		'assets_dir' => 'assets',
		'templates_dir' => 'templates',
		'template_extension' => 'tpl.php',
		'plugin_path' => 'js_composer/inline',
	);
	/**
	 * @var string
	 */
	protected static $content_editor_id = 'content';
	/**
	 * @var array
	 */
	protected static $content_editor_settings = array(
		'dfw' => true,
		'tabfocus_elements' => 'insert-media-button',
		'editor_height' => 360,
	);
	/**
	 * @var string
	 */
	protected static $brand_url = 'https://wpbakery.com/?utm_campaign=VCplugin&utm_source=vc_user&utm_medium=frontend_editor';
	public $post_custom_css;
	/**
	 * @var string
	 */
	protected $vc_post_content = '';

	/**
	 *
	 */
	public function init() {
		$this->addHooks();
		/**
		 * If current mode of VC is frontend editor load it.
		 */
		if ( vc_is_frontend_editor() ) {
			$this->hookLoadEdit();
		} elseif ( vc_is_page_editable() ) {
			/**
			 * if page loaded inside frontend editor iframe it has page_editable mode.
			 * It required to some some js/css elements and add few helpers for editor to be used.
			 */
			$this->buildEditablePage();
		} else {
			// Is it is simple page just enable buttons and controls
			$this->buildPage();
		}

	}

	/**
	 *
	 */
	public function addHooks() {
		add_action( 'template_redirect', array(
			$this,
			'loadShortcodes',
		) );
		add_filter( 'page_row_actions', array(
			$this,
			'renderRowAction',
		) );
		add_filter( 'post_row_actions', array(
			$this,
			'renderRowAction',
		) );
		add_shortcode( 'vc_container_anchor', 'vc_container_anchor' );

	}

	/**
	 *
	 */
	public function hookLoadEdit() {
		add_action( 'current_screen', array(
			$this,
			'adminInit',
		) );
		do_action( 'vc_frontend_editor_hook_load_edit' );
	}

	/**
	 *
	 */
	public function adminInit() {
		if ( Vc_Frontend_Editor::frontendEditorEnabled() ) {
			$this->setPost();
			if ( vc_check_post_type() ) {
				$this->renderEditor();
			}
		}
	}

	/**
	 *
	 */
	public function buildEditablePage() {
		if ( 'vc_load_shortcode' === vc_request_param( 'action' ) ) {
			return;
		}
		visual_composer()->shared_templates->init();
		add_filter( 'the_title', array(
			$this,
			'setEmptyTitlePlaceholder',
		) );

		add_action( 'the_post', array(
			$this,
			'parseEditableContent',
		), 9999 ); // after all the_post actions ended

		do_action( 'vc_inline_editor_page_view' );
		add_filter( 'wp_enqueue_scripts', array(
			$this,
			'loadIFrameJsCss',
		) );

		add_action( 'wp_footer', array(
			$this,
			'printPostShortcodes',
		) );
	}

	/**
	 *
	 */
	public function buildPage() {
		add_action( 'admin_bar_menu', array(
			$this,
			'adminBarEditLink',
		), 1000 );
		add_filter( 'edit_post_link', array(
			$this,
			'renderEditButton',
		) );
	}

	/**
	 * @return bool
	 */
	public static function inlineEnabled() {
		return true === self::$enabled_inline;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public static function frontendEditorEnabled() {
		return self::inlineEnabled() && vc_user_access()->part( 'frontend_editor' )->can()->get();
	}

	/**
	 * @param bool $disable
	 */
	public static function disableInline( $disable = true ) {
		self::$enabled_inline = ! $disable;
	}

	/**
	 * Main purpose of this function is to
	 *  1) Parse post content to get ALL shortcodes in to array
	 *  2) Wrap all shortcodes into editable-wrapper
	 *  3) Return "iframe" editable content in extra-script wrapper
	 *
	 * @param Wp_Post $post
	 * @throws \Exception
	 */
	public function parseEditableContent( $post ) {
		if ( ! vc_is_page_editable() || vc_action() || vc_post_param( 'action' ) ) {
			return;
		}

		$post_id = (int) vc_get_param( 'vc_post_id' );
		if ( $post_id > 0 && $post->ID === $post_id && ! defined( 'VC_LOADING_EDITABLE_CONTENT' ) ) {
			$post_content = '';
			define( 'VC_LOADING_EDITABLE_CONTENT', true );
			remove_filter( 'the_content', 'wpautop' );
			do_action( 'vc_load_shortcode' );
			$post_content .= $this->getPageShortcodesByContent( $post->post_content );
			ob_start();
			vc_include_template( 'editors/partials/vc_welcome_block.tpl.php' );
			$post_content .= ob_get_clean();

			ob_start();
			vc_include_template( 'editors/partials/post_shortcodes.tpl.php', array( 'editor' => $this ) );
			$post_shortcodes = ob_get_clean();
			$custom_tag = 'script';
			$this->vc_post_content = '<' . $custom_tag . ' type="template/html" id="vc_template-post-content" style="display:none">' . rawurlencode( apply_filters( 'the_content', $post_content ) ) . '</' . $custom_tag . '>' . $post_shortcodes;
			// We already used the_content filter, we need to remove it to avoid double-using
			remove_all_filters( 'the_content' );
			// Used for just returning $post->post_content
			add_filter( 'the_content', array(
				$this,
				'editableContent',
			) );
		}
	}

	/**
	 * @since 4.4
	 * Used to print rendered post content, wrapped with frontend editors "div" and etc.
	 */
	public function printPostShortcodes() {
		// @codingStandardsIgnoreLine
		print $this->vc_post_content;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function editableContent( $content ) {
		// same addContentAnchor
		do_shortcode( $content ); // this will not be outputted, but this is needed to enqueue needed js/styles.

		return '<span id="vc_inline-anchor" style="display:none !important;"></span>';
	}

	/**
	 * @param string $url
	 * @param string $id
	 *
	 * vc_filter: vc_get_inline_url - filter to edit frontend editor url (can be used for example in vendors like
	 *     qtranslate do)
	 *
	 * @return mixed
	 */
	public static function getInlineUrl( $url = '', $id = '' ) {
		$the_ID = ( strlen( $id ) > 0 ? $id : get_the_ID() );

		return apply_filters( 'vc_get_inline_url', admin_url() . 'post.php?vc_action=vc_inline&post_id=' . $the_ID . '&post_type=' . get_post_type( $the_ID ) . ( strlen( $url ) > 0 ? '&url=' . rawurlencode( $url ) : '' ) );
	}

	/**
	 * @return string
	 */
	public function wrapperStart() {
		return '';
	}

	/**
	 * @return string
	 */
	public function wrapperEnd() {
		return '';
	}

	/**
	 * @param $url
	 */
	public static function setBrandUrl( $url ) {
		self::$brand_url = $url;
	}

	/**
	 * @return string
	 */
	public static function getBrandUrl() {
		return self::$brand_url;
	}

	/**
	 * @return string
	 */
	public static function shortcodesRegexp() {
		$tagnames = array_keys( WPBMap::getShortCodes() );
		$tagregexp = implode( '|', array_map( 'preg_quote', $tagnames ) );
		// WARNING from shortcodes.php! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return '\\[' // Opening bracket
			. '(\\[?)' // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)" // 2: Shortcode name
			. '(?![\\w\-])' // Not followed by word character or hyphen
			. '(' // 3: Unroll the loop: Inside the opening shortcode tag
			. '[^\\]\\/]*' // Not a closing bracket or forward slash
			. '(?:' . '\\/(?!\\])' // A forward slash not followed by a closing bracket
			. '[^\\]\\/]*' // Not a closing bracket or forward slash
			. ')*?' . ')' . '(?:' . '(\\/)' // 4: Self closing tag ...
			. '\\]' // ... and closing bracket
			. '|' . '\\]' // Closing bracket
			. '(?:' . '(' // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			. '[^\\[]*+' // Not an opening bracket
			. '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			. '[^\\[]*+' // Not an opening bracket
			. ')*+' . ')' . '\\[\\/\\2\\]' // Closing shortcode tag
			. ')?' . ')' . '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

	}

	/**
	 *
	 */
	public function setPost() {
		global $post;
		$this->post = get_post(); // fixes #1342 if no get/post params set
		$this->post_id = vc_get_param( 'post_id' );
		if ( vc_post_param( 'post_id' ) ) {
			$this->post_id = vc_post_param( 'post_id' );
		}
		if ( $this->post_id ) {
			$this->post = get_post( $this->post_id );
		}
		do_action_ref_array( 'the_post', array( $this->post ) );
		$post = $this->post;
		$this->post_id = $this->post->ID;
	}

	/**
	 * @return mixed
	 */
	public function post() {
		! isset( $this->post ) && $this->setPost();

		return $this->post;
	}

	/**
	 * Used for wp filter 'wp_insert_post_empty_content' to allow empty post insertion.
	 *
	 * @param $allow_empty
	 *
	 * @return bool
	 */
	public function allowInsertEmptyPost( $allow_empty ) {
		return false;
	}

	/**
	 * vc_filter: vc_frontend_editor_iframe_url - hook to edit iframe url, can be used in vendors like qtranslate do.
	 */
	public function renderEditor() {
		global $current_user;
		wp_get_current_user();
		$this->current_user = $current_user;
		$this->post_url = set_url_scheme( get_permalink( $this->post_id ) );

		$array = array(
			'edit_post',
			$this->post_id,
		);
		if ( ! self::inlineEnabled() || ! vc_user_access()->wpAny( $array )->get() ) {
			header( 'Location: ' . $this->post_url );
		}
		$this->registerJs();
		$this->registerCss();
		visual_composer()->registerAdminCss(); // bc
		visual_composer()->registerAdminJavascript(); // bc
		if ( $this->post && 'auto-draft' === $this->post->post_status ) {
			$post_data = array(
				'ID' => $this->post_id,
				'post_status' => 'draft',
				'post_title' => '',
			);
			add_filter( 'wp_insert_post_empty_content', array(
				$this,
				'allowInsertEmptyPost',
			) );
			wp_update_post( $post_data, true );
			$this->post->post_status = 'draft';
			$this->post->post_title = '';

		}
		add_filter( 'admin_body_class', array(
			$this,
			'filterAdminBodyClass',
		) );

		$this->post_type = get_post_type_object( $this->post->post_type );
		$this->url = $this->post_url . ( preg_match( '/\?/', $this->post_url ) ? '&' : '?' ) . 'vc_editable=true&vc_post_id=' . $this->post->ID . '&_vcnonce=' . vc_generate_nonce( 'vc-admin-nonce' );
		$this->url = apply_filters( 'vc_frontend_editor_iframe_url', $this->url );
		$this->enqueueAdmin();
		$this->enqueueMappedShortcode();
		wp_enqueue_media( array( 'post' => $this->post_id ) );
		remove_all_actions( 'admin_notices', 3 );
		remove_all_actions( 'network_admin_notices', 3 );

		$post_custom_css = wp_strip_all_tags( get_post_meta( $this->post_id, '_wpb_post_custom_css', true ) );
		$this->post_custom_css = $post_custom_css;

		if ( ! defined( 'IFRAME_REQUEST' ) ) {
			define( 'IFRAME_REQUEST', true );
		}
		/**
		 * @deprecated vc_admin_inline_editor action hook
		 */
		do_action( 'vc_admin_inline_editor' );
		/**
		 * new one
		 */
		do_action( 'vc_frontend_editor_render' );

		add_filter( 'admin_title', array(
			$this,
			'setEditorTitle',
		) );
		$this->render( 'editor' );
		die();
	}

	/**
	 * @return string
	 */
	public function setEditorTitle() {
		return sprintf( esc_html__( 'Edit %s with WPBakery Page Builder', 'js_composer' ), $this->post_type->labels->singular_name );
	}

	/**
	 * @param $title
	 *
	 * @return string
	 */
	public function setEmptyTitlePlaceholder( $title ) {
		return ! is_string( $title ) || strlen( $title ) === 0 ? esc_attr__( '(no title)', 'js_composer' ) : $title;
	}

	/**
	 * @param $template
	 */
	public function render( $template ) {
		vc_include_template( 'editors/frontend_' . $template . '.tpl.php', array( 'editor' => $this ) );
	}

	/**
	 * @param $link
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function renderEditButton( $link ) {
		if ( $this->showButton( get_the_ID() ) ) {
			return $link . ' <a href="' . esc_url( self::getInlineUrl() ) . '" id="vc_load-inline-editor" class="vc_inline-link">' . esc_html__( 'Edit with WPBakery Page Builder', 'js_composer' ) . '</a>';
		}

		return $link;
	}

	/**
	 * @param $actions
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function renderRowAction( $actions ) {
		$post = get_post();
		if ( $this->showButton( $post->ID ) ) {
			$actions['edit_vc'] = '<a
		href="' . esc_url( $this->getInlineUrl( '', $post->ID ) ) . '">' . esc_html__( 'Edit with WPBakery Page Builder', 'js_composer' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * @param null $post_id
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function showButton( $post_id = null ) {
		$type = get_post_type();

		$post_status = array(
			'private',
			'trash',
		);
		$post_types = array(
			'templatera',
			'vc_grid_item',
		);
		$cap_edit_post = array(
			'edit_post',
			$post_id,
		);
		$result = self::inlineEnabled() && ! in_array( get_post_status(), $post_status, true ) && ! in_array( $type, $post_types, true ) && vc_user_access()->wpAny( $cap_edit_post )
				->get() && vc_check_post_type( $type );

		return apply_filters( 'vc_show_button_fe', $result, $post_id, $type );
	}

	/**
	 * @param WP_Admin_Bar $wp_admin_bar
	 * @throws \Exception
	 */
	public function adminBarEditLink( $wp_admin_bar ) {
		if ( ! is_object( $wp_admin_bar ) ) {
			global $wp_admin_bar;
		}
		if ( is_singular() ) {
			if ( $this->showButton( get_the_ID() ) ) {
				$wp_admin_bar->add_menu( array(
					'id' => 'vc_inline-admin-bar-link',
					'title' => esc_html__( 'Edit with WPBakery Page Builder', 'js_composer' ),
					'href' => self::getInlineUrl(),
					'meta' => array( 'class' => 'vc_inline-link' ),
				) );
			}
		}
	}

	/**
	 * @param $content
	 */
	public function setTemplateContent( $content ) {
		$this->template_content = $content;
	}

	/**
	 * vc_filter: vc_inline_template_content - filter to override template content
	 * @return mixed
	 */
	public function getTemplateContent() {
		return apply_filters( 'vc_inline_template_content', $this->template_content );
	}

	/**
	 *
	 */
	public function renderTemplates() {
		$this->render( 'templates' );
		die;
	}

	/**
	 *
	 */
	public function loadTinyMceSettings() {
		if ( ! class_exists( '_WP_Editors' ) ) {
			require ABSPATH . WPINC . '/class-wp-editor.php';
		}
		$set = _WP_Editors::parse_settings( self::$content_editor_id, self::$content_editor_settings );
		_WP_Editors::editor_settings( self::$content_editor_id, $set );
	}

	/**
	 *
	 */
	public function loadIFrameJsCss() {
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'wpb_composer_front_js' );
		wp_enqueue_style( 'js_composer_front' );
		wp_enqueue_style( 'vc_inline_css', vc_asset_url( 'css/js_composer_frontend_editor_iframe.min.css' ), array(), WPB_VC_VERSION );
		wp_enqueue_script( 'vc_waypoints' );
		wp_enqueue_script( 'wpb_scrollTo_js', vc_asset_url( 'lib/bower/scrollTo/jquery.scrollTo.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_enqueue_style( 'js_composer_custom_css' );

		wp_enqueue_script( 'wpb_php_js', vc_asset_url( 'lib/php.default/php.default.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_enqueue_script( 'vc_inline_iframe_js', vc_asset_url( 'js/dist/page_editable.min.js' ), array(
			'jquery-core',
			'underscore',
		), WPB_VC_VERSION, true );
		do_action( 'vc_load_iframe_jscss' );
	}

	/**
	 *
	 * @throws \Exception
	 */
	public function loadShortcodes() {
		if ( vc_is_page_editable() && vc_enabled_frontend() ) {
			$action = vc_post_param( 'action' );
			if ( 'vc_load_shortcode' === $action ) {
				$output = '';
				ob_start();
				$this->setPost();
				$shortcodes = (array) vc_post_param( 'shortcodes' );
				do_action( 'vc_load_shortcode', $shortcodes );
				$output .= ob_get_clean();
				$output .= $this->renderShortcodes( $shortcodes );
				$output .= '<div data-type="files">';
				ob_start();
				_print_styles();
				print_head_scripts();
				wp_footer();
				$output .= ob_get_clean();
				$output .= '</div>';
				// @codingStandardsIgnoreLine
				print apply_filters( 'vc_frontend_editor_load_shortcode_ajax_output', $output );
			} elseif ( 'vc_frontend_load_template' === $action ) {
				$this->setPost();
				visual_composer()->templatesPanelEditor()->renderFrontendTemplate();
			} elseif ( '' !== $action ) {
				do_action( 'vc_front_load_page_' . esc_attr( vc_post_param( 'action' ) ) );
			}
		}
	}

	/**
	 * @param $s
	 *
	 * @return string
	 */
	public function fullUrl( $s ) {
		$ssl = ( ! empty( $s['HTTPS'] ) && 'on' === $s['HTTPS'] ) ? true : false;
		$sp = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port = $s['SERVER_PORT'];
		$port = ( ( ! $ssl && '80' === $port ) || ( $ssl && '443' === $port ) ) ? '' : ':' . $port;
		if ( isset( $s['HTTP_X_FORWARDED_HOST'] ) ) {
			$host = $s['HTTP_X_FORWARDED_HOST'];
		} else {
			$host = ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : $s['SERVER_NAME'] );
		}

		return $protocol . '://' . $host . $port . $s['REQUEST_URI'];
	}

	/**
	 * @return string
	 */
	public static function cleanStyle() {
		return '';
	}

	/**
	 *
	 */
	public function enqueueRequired() {
		do_action( 'wp_enqueue_scripts' );
		visual_composer()->frontCss();
		visual_composer()->frontJsRegister();
	}

	/**
	 * @param array $shortcodes
	 *
	 * vc_filter: vc_front_render_shortcodes - hook to override shortcode rendered output
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function renderShortcodes( array $shortcodes ) {
		$this->enqueueRequired();
		$output = '';
		foreach ( $shortcodes as $shortcode ) {
			if ( isset( $shortcode['id'] ) && isset( $shortcode['string'] ) ) {
				if ( isset( $shortcode['tag'] ) ) {
					$shortcode_obj = visual_composer()->getShortCode( $shortcode['tag'] );
					if ( is_object( $shortcode_obj ) ) {
						$output .= '<div data-type="element" data-model-id="' . $shortcode['id'] . '">';
						$is_container = $shortcode_obj->settings( 'is_container' ) || ( null !== $shortcode_obj->settings( 'as_parent' ) && false !== $shortcode_obj->settings( 'as_parent' ) );
						if ( $is_container ) {
							$shortcode['string'] = preg_replace( '/\]/', '][vc_container_anchor]', $shortcode['string'], 1 );
						}
						$output .= '<div class="vc_element" data-shortcode-controls="' . esc_attr( wp_json_encode( $shortcode_obj->shortcodeClass()
								->getControlsList() ) ) . '" data-container="' . esc_attr( $is_container ) . '" data-model-id="' . $shortcode['id'] . '">' . $this->wrapperStart() . do_shortcode( stripslashes( $shortcode['string'] ) ) . $this->wrapperEnd() . '</div>';
						$output .= '</div>';
					}
				}
			}
		}

		return apply_filters( 'vc_front_render_shortcodes', $output );
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public function filterAdminBodyClass( $string ) {
		// @todo check vc_inline-shortcode-edit-form class looks like incorrect place
		$string .= ( strlen( $string ) > 0 ? ' ' : '' ) . 'vc_editor vc_inline-shortcode-edit-form';
		if ( '1' === vc_settings()->get( 'not_responsive_css' ) ) {
			$string .= ' vc_responsive_disabled';
		}

		return $string;
	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	public function adminFile( $path ) {
		return ABSPATH . 'wp-admin/' . $path;
	}

	public function registerJs() {
		wp_register_script( 'vc_bootstrap_js', vc_asset_url( 'lib/bower/bootstrap3/dist/js/bootstrap.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc_accordion/vc-accordion.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'wpb_php_js', vc_asset_url( 'lib/php.default/php.default.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		// used as polyfill for JSON.stringify and etc
		wp_register_script( 'wpb_json-js', vc_asset_url( 'lib/bower/json-js/json2.min.js' ), array(), WPB_VC_VERSION, true );
		// used in post settings editor
		wp_register_script( 'ace-editor', vc_asset_url( 'lib/bower/ace-builds/src-min-noconflict/ace.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array(), WPB_VC_VERSION, true ); // Google Web Font CDN
		wp_register_script( 'wpb_scrollTo_js', vc_asset_url( 'lib/bower/scrollTo/jquery.scrollTo.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc_accordion/vc-accordion.min.js' ), array( 'jquery-core' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc-frontend-editor-min-js', vc_asset_url( 'js/dist/frontend-editor.min.js' ), array(), WPB_VC_VERSION, true );
		wp_localize_script( 'vc-frontend-editor-min-js', 'i18nLocale', visual_composer()->getEditorsLocale() );
	}

	/**
	 *
	 */
	public function enqueueJs() {
		$wp_dependencies = array(
			'jquery-core',
			'underscore',
			'backbone',
			'media-views',
			'media-editor',
			'wp-pointer',
			'mce-view',
			'wp-color-picker',
			'jquery-ui-sortable',
			'jquery-ui-droppable',
			'jquery-ui-draggable',
			'jquery-ui-resizable',
			'jquery-ui-accordion',
			'jquery-ui-autocomplete',
			// used in @deprecated tabs
			'jquery-ui-tabs',
			'wp-color-picker',
			'farbtastic',
		);
		$dependencies = array(
			'vc_bootstrap_js',
			'vc_accordion_script',
			'wpb_php_js',
			'wpb_json-js',
			'ace-editor',
			'webfont',
			'vc_accordion_script',
			'vc-frontend-editor-min-js',
		);

		// This workaround will allow to disable any of dependency on-the-fly
		foreach ( $wp_dependencies as $dependency ) {
			wp_enqueue_script( $dependency );
		}
		foreach ( $dependencies as $dependency ) {
			wp_enqueue_script( $dependency );
		}
	}

	public function registerCss() {
		wp_register_style( 'ui-custom-theme', vc_asset_url( 'css/ui-custom-theme/jquery-ui-less.custom.min.css' ), false, WPB_VC_VERSION );
		wp_register_style( 'vc_animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), false, WPB_VC_VERSION, 'screen' );
		wp_register_style( 'vc_font_awesome_5_shims', vc_asset_url( 'lib/bower/font-awesome/css/v4-shims.min.css' ), array(), WPB_VC_VERSION );
		wp_register_style( 'vc_font_awesome_5', vc_asset_url( 'lib/bower/font-awesome/css/all.min.css' ), array( 'vc_font_awesome_5_shims' ), WPB_VC_VERSION );
		wp_register_style( 'vc_inline_css', vc_asset_url( 'css/js_composer_frontend_editor.min.css' ), array(), WPB_VC_VERSION );

	}

	public function enqueueCss() {
		$wp_dependencies = array(
			'wp-color-picker',
			'farbtastic',
		);
		$dependencies = array(
			'ui-custom-theme',
			'vc_animate-css',
			'vc_font_awesome_5',
			// 'wpb_jscomposer_autosuggest',
			'vc_inline_css',
		);

		// This workaround will allow to disable any of dependency on-the-fly
		foreach ( $wp_dependencies as $dependency ) {
			wp_enqueue_style( $dependency );
		}
		foreach ( $dependencies as $dependency ) {
			wp_enqueue_style( $dependency );
		}
	}

	/**
	 *
	 */
	public function enqueueAdmin() {
		$this->enqueueJs();
		$this->enqueueCss();
		do_action( 'vc_frontend_editor_enqueue_js_css' );
	}

	/**
	 * Enqueue js/css files from mapped shortcodes.
	 *
	 * To add js/css files to this enqueue please add front_enqueue_js/front_enqueue_css setting in vc_map array.
	 * @since 4.3
	 *
	 */
	public function enqueueMappedShortcode() {
		$userShortCodes = WPBMap::getUserShortCodes();
		if ( is_array( $userShortCodes ) ) {
			foreach ( $userShortCodes as $shortcode ) {
				$param = isset( $shortcode['front_enqueue_js'] ) ? $shortcode['front_enqueue_js'] : null;
				if ( is_array( $param ) && ! empty( $param ) ) {
					foreach ( $param as $value ) {
						$this->enqueueMappedShortcodeJs( $value );
					}
				} elseif ( is_string( $param ) && ! empty( $param ) ) {
					$this->enqueueMappedShortcodeJs( $param );
				}

				$param = isset( $shortcode['front_enqueue_css'] ) ? $shortcode['front_enqueue_css'] : null;
				if ( is_array( $param ) && ! empty( $param ) ) {
					foreach ( $param as $value ) {
						$this->enqueueMappedShortcodeCss( $value );
					}
				} elseif ( is_string( $param ) && ! empty( $param ) ) {
					$this->enqueueMappedShortcodeCss( $param );
				}
			}
		}
	}

	/**
	 * @param $value
	 */
	/**
	 * @param $value
	 */
	/**
	 * @param $value
	 */
	/**
	 * @param $value
	 */
	public function enqueueMappedShortcodeJs( $value ) {
		wp_enqueue_script( 'front_enqueue_js_' . md5( $value ), $value, array( 'vc-frontend-editor-min-js' ), WPB_VC_VERSION, true );
	}

	/**
	 * @param $value
	 */
	/**
	 * @param $value
	 */
	/**
	 * @param $value
	 */
	public function enqueueMappedShortcodeCss( $value ) {
		wp_enqueue_style( 'front_enqueue_css_' . md5( $value ), $value, array( 'vc_inline_css' ), WPB_VC_VERSION );
	}

	/**
	 * @param $content
	 *
	 * @return string|void
	 * @throws \Exception
	 * @since 4.4
	 */
	public function getPageShortcodesByContent( $content ) {
		if ( ! empty( $this->post_shortcodes ) ) {
			return;
		}
		$content = shortcode_unautop( trim( $content ) ); // @todo this seems not working fine.
		$not_shortcodes = preg_split( '/' . self::shortcodesRegexp() . '/', $content );

		foreach ( $not_shortcodes as $string ) {
			$temp = str_replace( array(
				'<p>',
				'</p>',
			), '', $string ); // just to avoid autop @todo maybe do it better like vc_wpnop in js.
			if ( strlen( trim( $temp ) ) > 0 ) {
				$content = preg_replace( '/(' . preg_quote( $string, '/' ) . '(?!\[\/))/', '[vc_row][vc_column width="1/1"][vc_column_text]$1[/vc_column_text][/vc_column][/vc_row]', $content );
			}
		}

		return $this->parseShortcodesString( $content );
	}

	/**
	 * @param $content
	 * @param bool $is_container
	 * @param bool $parent_id
	 *
	 * @return string
	 * @throws \Exception
	 * @since 4.2
	 */
	public function parseShortcodesString( $content, $is_container = false, $parent_id = false ) {
		$string = '';
		preg_match_all( '/' . self::shortcodesRegexp() . '/', trim( $content ), $found );
		WPBMap::addAllMappedShortcodes();
		add_shortcode( 'vc_container_anchor', 'vc_container_anchor' );

		if ( count( $found[2] ) === 0 ) {
			return $is_container && strlen( $content ) > 0 ? $this->parseShortcodesString( '[vc_column_text]' . $content . '[/vc_column_text]', false, $parent_id ) : $content;
		}
		foreach ( $found[2] as $index => $s ) {
			$id = md5( time() . '-' . $this->tag_index ++ );
			$content = $found[5][ $index ];
			$attrs = shortcode_parse_atts( $found[3][ $index ] );
			if ( empty( $attrs ) ) {
				$attrs = array();
			} elseif ( ! is_array( $attrs ) ) {
				$attrs = (array) $attrs;
			}
			$shortcode = array(
				'tag' => $s,
				'attrs_query' => $found[3][ $index ],
				'attrs' => $attrs,
				'id' => $id,
				'parent_id' => $parent_id,
			);
			if ( false !== WPBMap::getParam( $s, 'content' ) ) {
				$shortcode['attrs']['content'] = $content;
			}
			$this->post_shortcodes[] = rawurlencode( wp_json_encode( $shortcode ) );
			$string .= $this->toString( $shortcode, $content );
		}

		return $string;
	}

	/**
	 * @param $shortcode
	 * @param $content
	 *
	 * @return string
	 * @throws \Exception
	 * @since 4.2
	 */
	public function toString( $shortcode, $content ) {
		$shortcode_obj = visual_composer()->getShortCode( $shortcode['tag'] );
		$is_container = $shortcode_obj->settings( 'is_container' ) || ( null !== $shortcode_obj->settings( 'as_parent' ) && false !== $shortcode_obj->settings( 'as_parent' ) );
		$shortcode = apply_filters( 'vc_frontend_editor_to_string', $shortcode, $shortcode_obj );

		$output = sprintf( '<div class="vc_element" data-tag="%s" data-shortcode-controls="%s" data-model-id="%s">%s[%s %s]%s[/%s]%s</div>', esc_attr( $shortcode['tag'] ), esc_attr( wp_json_encode( $shortcode_obj->shortcodeClass()
			->getControlsList() ) ), esc_attr( $shortcode['id'] ), $this->wrapperStart(), $shortcode['tag'], $shortcode['attrs_query'], $is_container ? '[vc_container_anchor]' . $this->parseShortcodesString( $content, $is_container, $shortcode['id'] ) : do_shortcode( $content ), $shortcode['tag'], $this->wrapperEnd() );

		return $output;
	}
}

if ( ! function_exists( 'vc_container_anchor' ) ) {
	/**
	 * @return string
	 * @since 4.2
	 */
	function vc_container_anchor() {
		return '<span class="vc_container-anchor" style="display: none;"></span>';
	}
}
