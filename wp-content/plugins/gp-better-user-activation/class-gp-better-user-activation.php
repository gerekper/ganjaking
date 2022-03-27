<?php

class GP_Better_User_Activation extends GP_Perk {

	public $version                   = GP_BETTER_USER_ACTIVATION_VERSION;
	public $min_gravity_perks_version = '1.2.18';
	public $min_gravity_forms_version = '2.1.2.15';
	public $min_wp_version            = '4.1';

	private $_activation_page_id = null;

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if ( self::$instance == null ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function init() {

		define( 'GPBUA_TEMPLATE_DIR', gpbua()->get_base_path() . '/templates' );

		// include files
		require_once( gpbua()->get_base_path() . '/includes/GPBUA_Shortcode_GPBUA.php' );
		require_once( gpbua()->get_base_path() . '/includes/GPBUA_Merge_Tags.php' );
		require_once( gpbua()->get_base_path() . '/includes/GPBUA_Activate.php' );
		require_once( gpbua()->get_base_path() . '/includes/GPBUA_Template.php' );
		require_once( gpbua()->get_base_path() . '/includes/GPBUA_Meta_Box.php' );

		$this->enqueue_field_settings();

		add_action( 'admin_init', array( $this, 'register_scripts' ) );

		// activation page filters
		add_filter( 'the_title', array( $this, 'hide_title' ), 2, 2 );
		add_filter( 'the_content', array( $this, 'show_content' ), 2 );

		// remove default content editor
		add_action( 'admin_head', array( $this, 'remove_default_content_editor' ) );

		// activation page meta boxes
		new GPBUA_Meta_Box();

		// activate user hooks
		add_filter( 'gform_user_registration_signup_meta', array( $this, 'add_form_id_to_signup_meta' ), 10, 4 );
		add_action( 'wp', array( $this, 'maybe_redirect_activation' ), 5 );
		add_action( 'wp', array( $this, 'maybe_activate_user' ), 5 );

		// support redirect on success
		add_action( 'gpbua_activation_success', array( $this, 'maybe_redirect_on_success' ), 10 );

		// Force Classic editor for our activation page. Priority 101 to fire *after* the Classic Editor plugin.
		add_filter( 'use_block_editor_for_post', array( $this, 'disable_block_editor_for_activation_page' ), 101, 2 );

		// add shortcodes
		new GPBUA_Shortcode_GPBUA();

		// add merge tags
		new GPBUA_Merge_Tags();

	}

	public function requirements() {
		return array(
			array(
				'class'   => 'GFUser',
				'message' => 'GP Better User Activation requires the Gravity Forms User Registration add-on.',
			),
		);
	}

	public function disable_block_editor_for_activation_page( $use_block_editor, $post ) {
		return gpbua_is_activation_page( $post->ID ) ? false : $use_block_editor;
	}

	public function remove_default_content_editor() {

		if ( ! gpbua_is_activation_page() ) {
			return;
		}

		remove_post_type_support( 'page', 'editor' );

	}

	public function maybe_redirect_on_success( $activation ) {

		$redirect_url = '';

		$redirect_on_success_id = $this->get_activation_success_redirect_id();
		if ( $redirect_on_success_id ) {
			$redirect_url = get_permalink( $redirect_on_success_id );
		}

		/**
		 * Filter the URL to which users will be redirected on a successful activation.
		 *
		 * @since 1.1.5
		 *
		 * @param $redirect_url string The URL to which the user will be redirected on a successful activation.
		 * @param $activation   object An instance of the GPBUA_Activate object used to activate the user.
		 */
		$redirect_url = apply_filters( 'gpbua_activation_redirect_url', $redirect_url, $activation );
		if ( $redirect_url ) {
			wp_redirect( $redirect_url );
		}

	}

	public function hide_title( $title, $post_id = false ) {

		// Some 3rd party plugins apply the the_title filter without passing a $post_id.
		if ( ! $post_id ) {
			return $title;
		}

		/**
		 * Filter whether the page title should be visible on the activation page.
		 *
		 * @since 1.0-beta-1
		 *
		 * @param bool $hide_title Whether or not the page title should be visible. Defaults to true.
		 */
		$hide_title = apply_filters( 'gpbua_hide_title', true );

		if ( ! gpbua_is_activation_page( $post_id ) || ! $hide_title ) {
			return $title;
		}

		return '';
	}

	public function show_content( $content ) {

		if ( ! isset( $GLOBALS['post']->ID ) || $GLOBALS['post']->ID != gpbua_get_activation_page_id() ) {
			return $content;
		}

		// get the activate instance
		$activate = GPBUA_Activate::get_instance();

		// init template class
		$template = new GPBUA_Template( $activate->get_view(), $activate->get_result() );

		// check if content has the gpbua shortcode
		if ( ! has_shortcode( $content, 'gpbua' ) ) {

			// shortcode not used so we append the view content
			$content .= $template->render_view();

		}

		return $content;
	}

	public function maybe_redirect_activation() {

		if ( ! $this->is_gf_activation_page() ) {
			return;
		}

		// replace with check has_custom_activation_page to support no page set
		$activation_page_id = $this->get_activation_page_id();
		if ( ! $activation_page_id ) {
			return;
		}

		parse_str( $_SERVER['QUERY_STRING'], $query_args );

		// Before GFUR 4.6, the activation key was passed via the "key" parameter. Post GFUR 4.6, it is passed via the
		// "gfur_activation" parameter.
		$key = rgar( $query_args, 'key', rgar( $query_args, 'gfur_activation' ) );

		// For GFUR < 4.6
		unset( $query_args['page'] );
		unset( $query_args['gfur_activation'] );

		$query_args['key'] = $key;

		// redirect user to custom activation page
		$activation_url = add_query_arg( $query_args, get_permalink( $activation_page_id ) );
		wp_redirect( $activation_url );
		exit;

	}

	public function is_gf_activation_page() {
		$is_activation_page = isset( $_GET['page'] ) && $_GET['page'] === 'gf_activation';
		// Parameter changed in GFUR 4.6 due to issues introduced by WP 5.5.
		$is_activation_page = $is_activation_page || isset( $_GET['gfur_activation'] );
		return $is_activation_page;
	}

	public function maybe_activate_user() {

		// global $post;
		global $wp_query;
		$post_id = $wp_query->get_queried_object_id();

		if ( $post_id != $this->get_activation_page_id() ) {
			return;
		}

		// remove standard gfur wp hook
		remove_action( 'wp', array( gf_user_registration(), 'maybe_activate_user' ) );

		$activate = GPBUA_Activate::get_instance();
		$activate->init();
		$activate->activate();

	}

	public function register_scripts() {

		if ( ! $this->is_activation_page( rgget( 'post' ) ) ) {
			return;
		}

		wp_register_script( 'gpbua_script', plugins_url( 'js/gpbua.js', __FILE__ ), array( 'jquery', 'gform_form_admin' ), $this->version, true );
		wp_enqueue_script( 'gpbua_script' );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );

		wp_register_style( 'gpbua_jquery_ui_styles', plugins_url( 'css/gpbua-jquery-ui.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( 'gpbua_jquery_ui_styles' );

		wp_register_style( 'gpbua_styles', plugins_url( 'css/gpbua.css', __FILE__ ), array( 'gpbua_jquery_ui_styles' ), $this->version );
		wp_enqueue_style( 'gpbua_styles' );

	}

	private function get_pages() {

		// empty option
		$pages = array( 0 => __( 'None', 'gp-better-user-activation' ) );

		$pageList = get_pages( array(
			'post_status' => array( 'publish', 'draft', 'private' ),
		) );

		if ( empty( $pageList ) ) {
			return $pages;
		}

		$current_parent = 0;
		$indent         = '&mdash;&nbsp;';
		foreach ( $pageList as $pageObj ) {

			$parent = $pageObj->post_parent;

			if ( $parent ) {

				if ( $current_parent != $parent ) {
					// new level
					$indent         = '&mdash;' . $indent;
					$current_parent = $parent;
				}

				$pages[ $pageObj->ID ] = $indent . $pageObj->post_title;

			} else {

				// no parent
				$current_parent        = $pageObj->ID;
				$indent                = '&mdash;&nbsp;'; // reset indent
				$pages[ $pageObj->ID ] = $pageObj->post_title;

			}
		}

		return $pages;

	}

	public function get_activation_success_redirect_id() {
		$settings = self::get_perk_settings( $this->slug );
		return $this->get_setting( $this->get_option_id( 'activation_success_redirect' ), $settings );
	}

	public function get_activation_success_redirect_page() {
		return get_post( $this->get_activation_success_redirect_id() );
	}

	public function get_activation_page_id() {

		if ( $this->_activation_page_id === null ) {
			$this->_activation_page_id = $this->get_setting( $this->get_option_id( 'activation_page' ) );
		}

		/**
		 * Filter the ID that will be used to identify and fetch the activation page.
		 *
		 * @since 1.2.6
		 *
		 * @param int|null $activation_page_id The ID of the activation page.
		 */
		$this->_activation_page_id = apply_filters( 'gpbua_activation_page_id', $this->_activation_page_id );

		return $this->_activation_page_id;
	}

	public function get_activation_page() {
		return get_post( $this->get_activation_page_id() );
	}

	public function settings() {

		// get page list
		$pages = $this->get_pages();

		echo self::generate_select( $this, array(
			'id'          => $this->get_option_id( 'activation_page' ),
			'label'       => __( 'User Activation Page', 'gp-better-user-activation' ),
			'values'      => $pages,
			'description' => __( 'Select a page to which the user will be directed to activate their account.', 'gp-better-user-activation' ),
		) );

		echo self::generate_select( $this, array(
			'id'          => $this->get_option_id( 'activation_success_redirect' ),
			'label'       => __( 'Redirect Page', 'gp-better-user-activation' ),
			'values'      => $pages,
			'description' => __( 'Select a page to which the user will be redirected after a successful activation.', 'gp-better-user-activation' ),
		) );

	}

	public function get_option_id( $id ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$lang = $sitepress->get_current_language();
			if ( $lang && $lang != 'all' ) {
				$id = sprintf( '%s_%s', $id, $lang );
			}
		}
		return $id;
	}

	public function register_settings( $perk ) {
		return array( $this->get_option_id( 'activation_page' ), $this->get_option_id( 'activation_success_redirect' ) );
	}

	public function is_activation_page( $post_id = false ) {
		global $post;

		if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && /*! $post_id &&*/ is_admin() && is_callable( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && $screen->id != 'page' ) {
				return false;
			}
		}

		if ( ! $post_id && $post ) {
			$post_id = $post->ID;
		}

		if ( $post_id && $this->get_activation_page_id() == $post_id ) {
			return true;
		}

		return false;
	}

	public function get_default_content( $view ) {
		ob_start();
		if ( ! $view ) {
			$view = 'success';
		}
		require_once( GPBUA_TEMPLATE_DIR . '/' . $this->template_name_by_key( $view ) );
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function template_name_by_key( $key ) {
		return 'activate-' . str_replace( '_', '-', $key ) . '.php';
	}

	/**
	 * Add the form id to signup meta that way if the entry goes missing is deleted (GP Disable Entry Creation),
	 * errors aren't encountered when trying to figure out which form is associated with the signup.
	 *
	 * @param array $meta All the metadata in an array (user login, email, password, etc)
	 * @param array $form The current Form object
	 * @param array $entry The Entry object (To pull the meta from the entry array)
	 * @param array $feed The Feed object
	 *
	 * @return array
	 */
	public function add_form_id_to_signup_meta( $meta, $form, $entry, $feed ) {
		$meta['form_id'] = $form['id'];

		return $meta;
	}

}

function gp_better_user_activation() {
	return GP_Better_User_Activation::get_instance( null );
}

function gpbua() {
	return gp_better_user_activation();
}

function gpbua_is_activation_page( $post_id = false ) {
	return gpbua()->is_activation_page( $post_id );
}

function gpbua_get_activation_page_id() {
	return gpbua()->get_activation_page_id();
}
