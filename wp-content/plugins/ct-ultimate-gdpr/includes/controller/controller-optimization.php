<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Optimization
 */
class CT_Ultimate_GDPR_Controller_Optimization extends CT_Ultimate_GDPR_Controller_Abstract {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-optimization';
    
    /**
	 *
	 */
	public $ctOptimization = [];


	/**
	 * Get unique controller id (page name, option id)
	 */
	public function get_id() {
		return self::ID;
	}    

	/**
	 * Init after construct
	 */
	public function init() {

		$this->ctOptimization = get_option('ct-ultimate-gdpr-optimization');
 
        // Optimize on!
        if( !empty( $this->ctOptimization['optimization_settings']) ) {
            
            if(!is_admin()){ // do not activate defer asset if on admin page
                add_filter('script_loader_tag', array( $this, 'defer_script' ), 10, 2);
                add_filter('style_loader_tag', array( $this, 'defer_style' ), 10, 2);

                // We can add here other options to optimize: like defer attributes
                // add_filter('script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 2);
            }

            // enqueue defer script
            $this->wp_enqueue_scripts_action();
        }
		// else {
		// 	if( !empty($this->ctOptimization) ) update_option('ct-ultimate-gdpr-optimization', null);
		// }
		if(is_admin()){ 
			wp_enqueue_style( 'ct-ultimate-gdpr-admin-optimization-css', ct_ultimate_gdpr_url( '/assets/css/admin-optimization.css' ), array(), ct_ultimate_gdpr_get_plugin_version() );
        	wp_enqueue_script( 'ct-ultimate-gdpr-admin-optimization', ct_ultimate_gdpr_url( '/assets/js/admin-optimization.js' ), array('jquery'), ct_ultimate_gdpr_get_plugin_version() );
		}
	}

    /**
	 * Add menu page (if not added in admin controller)
	 */
	public function add_menu_page() {
		add_submenu_page(
			'ct-ultimate-gdpr',
			esc_html__( 'Optimization', 'ct-ultimate-gdpr' ),
			esc_html__( 'Optimization', 'ct-ultimate-gdpr' ),
			'manage_options',
			$this->get_id(),
			array( $this, 'render_menu_page' )
		);
	}

    /**
	 * Add enqueue scripts
	 */
	public function wp_enqueue_scripts_action() {
        // load this in frontend
		if(!is_admin()){ 
		    wp_enqueue_script( 'ct-ultimate-gdpr-defer-assets', ct_ultimate_gdpr_url( '/assets/js/load-deferred-assets.js' ), ct_ultimate_gdpr_get_plugin_version() );
        }
	}

     /**
	 * Defer the scripts
	 */
	public function defer_script($scriptTag, $handle){

		$exclude = [];

		if(isset($this->ctOptimization['optimization_settings_age_popup'])) $exclude[] = 'ct-ultimate-gdpr-age-popup';
		if(isset($this->ctOptimization['optimization_settings_cookie_popup'])) $exclude[] = 'ct-ultimate-gdpr-cookie-popup';
		if(isset($this->ctOptimization['optimization_settings_cookie_list'])) $exclude[] = 'ct-ultimate-gdpr-cookie-list';
		if(isset($this->ctOptimization['optimization_settings_jquerybase64'])) $exclude[] = 'ct-ultimate-gdpr-base64';
		if(isset($this->ctOptimization['optimization_settings_shortcode_myaccount'])) $exclude[] = 'ct-ultimate-gdpr-tabs';
		if(isset($this->ctOptimization['optimization_settings_facebook_pixel'])) $exclude[] = 'ct-ultimate-gdpr-service-facebook-pixel';
		if(isset($this->ctOptimization['optimization_settings_shortcode_block_cookie'])) $exclude[] = 'ct-ultimate-gdpr-shortcode-block-cookie';

		if(count($exclude)>0){
			if(strpos($scriptTag, "id='ct-ultimate-gdpr")) {
				if(in_array($handle, $exclude)) {
					$scriptTag = str_replace('<script', '<meta class="ct-ultimate-gdpr-deferred-js" has-loaded="0"', $scriptTag);
					$scriptTag = str_replace('></script>', '/>', $scriptTag);
				}	
			}
		}
		return $scriptTag;
	}

     /**
	 * Defer the styles
	 */
	public function defer_style($styleTag, $handle){

		$exclude = [];

		if(isset($this->ctOptimization['optimization_settings_gdpr_main_css'])) $exclude[] = 'ct-ultimate-gdpr';
		if(isset($this->ctOptimization['optimization_settings_gdpr_jquery_ui_css'])) $exclude[] = 'ct-ultimate-gdpr-jquery-ui';
		if(isset($this->ctOptimization['optimization_settings_gdpr_age_popup_css'])) $exclude[] = 'ct-ultimate-gdpr-age-popup';
		if(isset($this->ctOptimization['optimization_settings_gdpr_cookie_popup_css'])) $exclude[] = 'ct-ultimate-gdpr-cookie-popup';
		if(isset($this->ctOptimization['optimization_settings_gdpr_fontawesome_css'])) $exclude[] = 'ct-ultimate-gdpr-font-awesome';

		if(count($exclude)>0) {
			if(strpos($styleTag, "id='ct-ultimate-gdpr")) {
				if(in_array($handle, $exclude)) {
					$styleTag = str_replace('<link', '<meta class="ct-ultimate-gdpr-deferred-css" has-loaded="0"', $styleTag);
					$styleTag = str_replace('link>', '/>', $styleTag);
				}
			}
		}
		return $styleTag;
	}

    /**
	 * Add defer attribute
	 */
	public function add_defer_attribute($tag, $handle){

		// defer here js that needs to be defer for example: ct-ultimate-gdpr-tabs
		$scripts_to_defer = array(
			// 'ct-ultimate-gdpr-tabs'
		);

		$exclude = [];

		foreach($scripts_to_defer as $defer_script) {
			if ($defer_script === $handle) 
				return str_replace(' src', ' defer="defer" src', $tag);
		}
		return $tag;
	}

    /**
	 * Get view template string
	 * @return string
	 */
	public function get_view_template() {
		return 'admin/admin-optimization';
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
        $admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();

        add_settings_section(
			$this->get_id().'_tab-1_section-1', // ID
			esc_html__( 'Optimization Setting', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			$this->get_id() // Page
		);

		add_settings_section(
			$this->get_id().'_tab-1_section-2', // ID
			esc_html__( 'Defer Javascript Setting', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			$this->get_id() // Page
		);

		add_settings_section(
			$this->get_id().'_tab-1_section-3', // ID
			esc_html__( 'Defer CSS Setting', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			$this->get_id() // Page
		);

        {
            add_settings_field(
				'optimization_settings',
				esc_html__( "Enable optimization", 'ct-ultimate-gdpr' ),
				array( $this, 'render_field_optimization_settings' ),
				$this->get_id(),
				$this->get_id().'_tab-1_section-1',
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Enabling optimization mode will postpone JS and CSS and will only loads when user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        ''
                    ),
                )
			);

            add_settings_field(
				'optimization_settings_age_popup', // ID
				esc_html__( 'Defer Age Popup JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_age_popup' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Age Popup JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

            add_settings_field(
				'optimization_settings_cookie_popup', // ID
				esc_html__( 'Defer Cookie Popup JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_cookie_popup' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Cookie Popup JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_cookie_list', // ID
				esc_html__( 'Defer Cookie List JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_cookie_list' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Cookie List JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_jquerybase64', // ID
				esc_html__( 'Defer JqueryBase64 JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_jquerybase64' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'JqueryBase64 JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_shortcode_myaccount', // ID
				esc_html__( 'Defer Shortcode My-account JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_shortcode_myaccount' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Shortcode My-account JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_facebook_pixel', // ID
				esc_html__( 'Defer Facebook Pixel JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_facebook_pixel' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Facebook Pixel JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_shortcode_block_cookie', // ID
				esc_html__( 'Defer Shortcode Block Cookie JS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_shortcode_block_cookie' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-2', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'Postpone %s until user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Shortcode Block Cookie JS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			/**--------------------------- CSS Fields ----------------------------- */
			add_settings_field(
				'optimization_settings_gdpr_main_css', // ID
				esc_html__( 'Defer Main CSS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_gdpr_main_css' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-3', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'You might want to leave this as Disabled. If enabled, styles will only load when user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Defer Main CSS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_gdpr_jquery_ui_css', // ID
				esc_html__( 'Defer JqueryUI CSS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_gdpr_jquery_ui_css' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-3', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'You might want to leave this as Disabled. If enabled, styles will only load when user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Defer JqueryUI CSS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_gdpr_age_popup_css', // ID
				esc_html__( 'Defer Age Popup CSS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_gdpr_age_popup_css' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-3', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'You might want to leave this as Disabled. If enabled, styles will only load when user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Defer Age Popup CSS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_gdpr_cookie_popup_css', // ID
				esc_html__( 'Defer Cookie Popup CSS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_gdpr_cookie_popup_css' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-3', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'You might want to leave this as Disabled. If enabled, styles will only load when user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Defer Cookie Popup CSS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

			add_settings_field(
				'optimization_settings_gdpr_fontawesome_css', // ID
				esc_html__( 'Defer Font Awesome CSS', 'ct-ultimate-gdpr' ), // Title
				array( $this, 'render_field_optimization_settings_gdpr_fontawesome_css' ), // Callback
				$this->get_id(), // Page
				$this->get_id().'_tab-1_section-3', // Section
				array(
                    'hint' => sprintf(
                        wp_kses_post( __( 'You might want to leave this as Disabled. If enabled, styles will only load when user interacts with the website.', 'ct-ultimate-gdpr' ) ),
                        esc_html__( 'Defer Font Awesome CSS', 'ct-ultimate-gdpr' )
                    ),
					'class' => $this->checkField()
                )
			);

        }
    }

	/**
	 * Disable fields
    */
	public function checkField() {
		if( empty($this->ctOptimization['optimization_settings'])) {
			return 'opt_disable';
		}
	}

    /**
	 *
    */
	public function render_field_optimization_settings() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

     /**
	 *
    */
	public function render_field_optimization_settings_age_popup() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

    /**
	 * Defer Cookie Popup JS
    */
	public function render_field_optimization_settings_cookie_popup() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Cookie List JS
    */
	public function render_field_optimization_settings_cookie_list() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Jquery Base 64 JS
    */
	public function render_field_optimization_settings_jquerybase64() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Shortcode My-account JS
    */
	public function render_field_optimization_settings_shortcode_myaccount() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Facebook Pixel JS
    */
	public function render_field_optimization_settings_facebook_pixel() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Shortcode Block JS
    */
	public function render_field_optimization_settings_shortcode_block_cookie() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-js' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/** ------------------- START CSS DEFER ----------------------------- */
	/**
	 * Defer GDPR Main CSS
    */
	public function render_field_optimization_settings_gdpr_main_css() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-css' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Jquery UI CSS
    */
	public function render_field_optimization_settings_gdpr_jquery_ui_css() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-css' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Age Popup CSS
    */
	public function render_field_optimization_settings_gdpr_age_popup_css() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-css' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer Cookie Popup CSS
    */
	public function render_field_optimization_settings_gdpr_cookie_popup_css() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-css' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

	/**
	 * Defer FontAwesome CSS
    */
	public function render_field_optimization_settings_gdpr_fontawesome_css() {
        
		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field defer-sub-option defer-css' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);       
	}

    /**
	 * Do actions on frontend
	 */
	public function front_action() {
	}

	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}

	/**
	 * Do actions on current admin page
	 */
	protected function admin_page_action() {
	}


	/**
	 * @param $active_plugin
	 *
	 * @return mixed|
	 */
	private function check_plugin_collects_data( $active_plugin ) {
	}

	/**
	 * @param $active_plugin
	 *
	 * @return mixed|
	 */
	private function check_plugin_compatible( $active_plugin ) {
	}
}