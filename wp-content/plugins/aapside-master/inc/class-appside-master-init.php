<?php
/**
 * @package Appside
 * @author Ir-Tech
 */
if (!defined("ABSPATH")) {
    exit(); //exit if access directly
}

if (!class_exists('Appside_Shortcodes')) {

    class Appside_Shortcodes
    {
        /*
        * $instance
        * @since 1.0.0
        * */
        protected static $instance;

        public function __construct()
        {
        	//Load plugin assets
	        add_action('wp_enqueue_scripts',array($this,'plugin_assets'));
        	//Load plugin admin assets
	        add_action('admin_enqueue_scripts',array($this,'admin_assets'));
        	//load plugin text domain
	        add_action('init',array($this,'load_textdomain'));
	        //load plugin dependency files()
            add_action('plugins_loaded',[$this, 'load_plugin_dependency_files']);
        }

        /**
         * getInstance()
         * */
        public static function getInstance()
        {
            if (null == self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

		/**
		 * Load Plugin Text domain
		 * @since 1.0.0
		 * */
		public function load_textdomain(){
			load_plugin_textdomain('aapside-master',false,APPSIDE_MASTER_ROOT_PATH .'/languages');
		}

		/**
		 * load plugin dependency files()
		 * @since 1.0.0
		 * */
		public function load_plugin_dependency_files(){
			$includes_files = array(
				array(
					'file-name' => 'codestar-framework',
					'folder-name' => APPSIDE_MASTER_LIB .'/codestar-framework'
				),
				array(
					'file-name' => 'class-menu-page',
					'folder-name' => APPSIDE_MASTER_ADMIN
				),
				array(
					'file-name' => 'class-custom-post-type',
					'folder-name' => APPSIDE_MASTER_ADMIN
				),
				array(
					'file-name' => 'class-post-column-customize',
					'folder-name' => APPSIDE_MASTER_ADMIN
				),
				array(
					'file-name' => 'class-admin-request',
					'folder-name' => APPSIDE_MASTER_ADMIN
				),
				array(
					'file-name' => 'add-menu-item-custom-fields',
					'folder-name' => APPSIDE_MASTER_LIB .'/mega-menu'
				),
				array(
					'file-name' => 'class-appside-shortcodes',
					'folder-name' => APPSIDE_MASTER_INC
				),
				array(
					'file-name' => 'class-elementor-section-extends',
					'folder-name' => APPSIDE_MASTER_ELEMENTOR
				),
				array(
					'file-name' => 'class-elementor-widget-init',
					'folder-name' => APPSIDE_MASTER_ELEMENTOR
				),
				array(
					'file-name' => 'class-about-us-widget',
					'folder-name' => APPSIDE_MASTER_WP_WIDGETS
				),
				array(
					'file-name' => 'class-recent-post-widget',
					'folder-name' => APPSIDE_MASTER_WP_WIDGETS
				),
				array(
					'file-name' => 'class-contact-info-widget',
					'folder-name' => APPSIDE_MASTER_WP_WIDGETS
				)
			);
			//add if theme was not activate
			if (!APPSIDE_THEME_ACTIVE){
				$includes_files[] = array(
					'file-name' => 'class-appside-excerpt',
					'folder-name' => APPSIDE_MASTER_INC
				);
			}

			if (!empty(get_option('appside_license_status')) && get_option('appside_license_status') != 'not_verified'){
				$includes_files[] = array(
					'file-name' => 'class-demo-data-import',
					'folder-name' => APPSIDE_MASTER_DEMO_IMPORT
				);
			}

			if (is_array($includes_files) && !empty($includes_files)){
				foreach ($includes_files as $file){
					if (file_exists($file['folder-name'].'/'.$file['file-name'].'.php')){
						require_once $file['folder-name'].'/'.$file['file-name'].'.php';
					}
				}
			}
		}

		/**
		 * admin assets
		 * @since 1.0.0
		 * */
		public function plugin_assets(){
			self::load_plugin_css_files();
			self::load_plugin_js_files();
		}

	    /**
	     * load plugin css files()
	     * @since 1.0.0
	     * */
	    public function load_plugin_css_files(){
		    $plugin_version = APPSIDE_MASTER_ENV ? time() : APPSIDE_MASTER_VERSION;
		    $all_css_files = array(
			    array(
				    'handle' => 'flaticon',
				    'src' => APPSIDE_MASTER_CSS .'/flaticon.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    ),
			    array(
				    'handle' => 'ir-icon',
				    'src' => APPSIDE_MASTER_CSS .'/ir-icon.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    ),
                array(
				    'handle' => 'xg-icons',
				    'src' => APPSIDE_MASTER_CSS .'/xg-icons.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    ),
                array(
                    'handle' => 'oxo-icons',
                    'src' => APPSIDE_MASTER_CSS .'/oxo-icon.css',
                    'deps' => array(),
                    'ver' => $plugin_version,
                    'media' => 'all'
                ),
			    array(
				    'handle' => 'owl-carousel',
				    'src' => APPSIDE_MASTER_CSS .'/owl.carousel.min.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    ),
			    array(
				    'handle' => 'appside-master-main-style',
				    'src' => APPSIDE_MASTER_CSS .'/main-style.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    )
		    );
		    if (!APPSIDE_THEME_ACTIVE){
			    $all_css_files[] = array(
				    'handle' => 'animate',
				    'src' => APPSIDE_MASTER_CSS .'/animate.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    );
			    $all_css_files[] = array(
				    'handle' => 'bootstrap',
				    'src' => APPSIDE_MASTER_CSS .'/bootstrap.min.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    );
			    $all_css_files[] = array(
				    'handle' => 'magnific-popup',
				    'src' => APPSIDE_MASTER_CSS .'/magnific-popup.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    );
			    $all_css_files[] = array(
				    'handle' => 'font-awesome',
				    'src' => APPSIDE_MASTER_CSS .'/font-awesome.min.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    );
			    $all_css_files[] = array(
				    'handle' => 'appside-theme-main-style',
				    'src' => APPSIDE_MASTER_CSS .'/theme-main-style.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    );
			    $all_css_files[] = array(
				    'handle' => 'appside-responsive',
				    'src' => APPSIDE_MASTER_CSS .'/responsive.css',
				    'deps' => array(),
				    'ver' => $plugin_version,
				    'media' => 'all'
			    );
		    }

		    $all_css_files = apply_filters('appside_master_css',$all_css_files);

		    if (is_array($all_css_files) && !empty($all_css_files)){
			    foreach ($all_css_files as $css){
				    call_user_func_array('wp_enqueue_style',$css);
			    }
		    }

	    }

	    /**
	     * load plugin js
	     * @since 1.0.0
	     * */
	    public function load_plugin_js_files(){
		    $plugin_version = APPSIDE_MASTER_VERSION;
		    $all_js_files = array(
			    array(
				    'handle' => 'waypoints',
				    'src' => APPSIDE_MASTER_JS .'/waypoints.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    ),
			    array(
				    'handle' => 'counterup',
				    'src' => APPSIDE_MASTER_JS .'/jquery.counterup.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    ),
			    array(
				    'handle' => 'owl-carousel',
				    'src' => APPSIDE_MASTER_JS .'/owl.carousel.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    ),
			    array(
				    'handle' => 'imagesloaded',
				    'src' => APPSIDE_MASTER_JS .'/imagesloaded.pkgd.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    ),
			    array(
				    'handle' => 'isotope',
				    'src' => APPSIDE_MASTER_JS .'/isotope.pkgd.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    ),
			    array(
				    'handle' => 'appside-master-main-script',
				    'src' => APPSIDE_MASTER_JS .'/main.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    ),
		    );

		    if (!APPSIDE_THEME_ACTIVE){
			    $all_js_files[] = array(
				    'handle' => 'popper',
				    'src' => APPSIDE_MASTER_JS .'/popper.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    );
			    $all_js_files[] = array(
				    'handle' => 'bootstrap',
				    'src' => APPSIDE_MASTER_JS .'/bootstrap.min.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    );
			    $all_js_files[] = array(
				    'handle' => 'magnific-popup',
				    'src' => APPSIDE_MASTER_JS .'/jquery.magnific-popup.js',
				    'deps' => array('jquery'),
				    'ver' => $plugin_version,
				    'in_footer' => true
			    );
		    }

			$all_js_files = apply_filters('appside_master_js',$all_js_files);
			
		    if (is_array($all_js_files) && !empty($all_js_files)){
			    foreach ($all_js_files as $js){
				    call_user_func_array('wp_enqueue_script',$js);
			    }
		    }
	    }

		/**
		 * admin assets
		 * @since 1.0.0
		 * */
		public function admin_assets(){
			self::load_admin_css_files();
			self::load_admin_js_files();
		}

		/**
		 * load plugin admin css files()
		 * @since 1.0.0
		 * */
		public function load_admin_css_files(){
			$plugin_version = APPSIDE_MASTER_VERSION;
			$all_css_files = array(
				array(
					'handle' => 'appside-master-admin-style',
					'src' => APPSIDE_MASTER_ADMIN_ASSETS .'/css/admin.css',
					'deps' => array(),
					'ver' => $plugin_version,
					'media' => 'all'
				),
			);

			$all_css_files = apply_filters('appside_admin_css',$all_css_files);
			if (is_array($all_css_files) && !empty($all_css_files)){
				foreach ($all_css_files as $css){
					call_user_func_array('wp_enqueue_style',$css);
				}
			}
		}

		/**
		 * load plugin admin js
		 * @since 1.0.0
		 * */
		public function load_admin_js_files(){
			$plugin_version = APPSIDE_MASTER_VERSION;
			$all_js_files = array(
				array(
					'handle' => 'appside-master-widget',
					'src' => APPSIDE_MASTER_ADMIN_ASSETS .'/js/widget.js',
					'deps' => array('jquery'),
					'ver' => $plugin_version,
					'in_footer' => true
				),
			);

			$all_js_files = apply_filters('appside_admin_js',$all_js_files);
			if (is_array($all_js_files) && !empty($all_js_files)){
				foreach ($all_js_files as $js){
					call_user_func_array('wp_enqueue_script',$js);
				}
			}
		}

    }//end class
    if (class_exists('Appside_Shortcodes')){
	    Appside_Shortcodes::getInstance();
    }
}

