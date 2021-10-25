<?php

namespace Elementor;

if(!defined('ABSPATH')) {
    exit;
}

use GT3\Elementor\Templates\Tabs as Templates_Tabs;

require_once __DIR__.'/elementor-widgets/images.php';

class GT3_Core_Elementor_Plugin {
    public static $JS_URL = 'js';
    public static $CSS_URL = 'css';
    public static $IMAGE_URL = 'img';
    public static $PATH = '/';
    private $suffix = '';

    private $widgets = array(
//        'Testimonials',
//        'Flipbox',
//        'Tabs',
//        'Accordion',
//        'EmptySpace',
//        'Divider',
//        'CustomMeta',
//        'Sharing',
//        'Counter',
//        'Button',
//        'InfoList',
//        'PieChart',
//        'Portfolio',
//        'Project',
//        'Team',
//        'Blog',
//        'GalleryPackery',
//        'DesignDraw',
//        'GoogleMap',
//        'NewAccordion',
//        'PriceBox',
//        'ImageBox',
//        'TeamTabs',
    );

    private $controls = array(
        // Controls
        'gt3-elementor-core-gallery' => 'Gallery',
	    'gt3-elementor-core-query' => 'Query',
    );

    private $group_controls = array(
        // Controls

    );

    const version = GT3_CORE_ELEMENTOR_VERSION;

    private static $instance = null;

    public static function instance(){
        if(!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){
        $this->suffix    = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $this->suffix    = '';
        self::$JS_URL    = plugins_url('/assets/js/', __FILE__);
        self::$CSS_URL   = plugins_url('/assets/css/', __FILE__);
        self::$IMAGE_URL = plugins_url('/assets/img/', __FILE__);
        self::$PATH      = plugin_dir_path(__FILE__);

        require_once __DIR__.'/templates/class-tabs.php';

        $this->actions();

	    add_filter('single_template', array( Templates_Tabs::class, 'load_canvas_template' ));
    }

    private function actions(){
        add_action('elementor/init', array( $this, 'elementor_init' ), 50);
        add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), -1);
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts' ));
        add_action('elementor/controls/controls_registered', array( $this, 'controls_registered' ));
	    add_action('elementor/elements/categories_registered', array( $this, 'categories_registered' ));

        add_action('elementor/editor/after_enqueue_scripts', array( $this, 'editor_enqueue_scripts' ));
        add_action('elementor/editor/after_enqueue_styles', array( $this, 'editor_enqueue_styles' ));
        add_action('elementor/frontend/after_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ));
        add_action('wp_head', array( $this, 'frontend_enqueue_styles' ), -1);

        add_action('wp_footer', array($this,'wp_footer'));

	    add_action('elementor/widget/before_render_content', function($widget){
		    /** @var \Elementor\Widget_Base $widget */


		    $widget_name = $widget->get_name();
		    do_action("elementor/widget/before_render_content/${widget_name}", $widget);

		    $customControls = array(
			    'typography_font_size'          => 'elementor-element-custom_font_size',
			    'typography_font_size_tablet'   => 'elementor-element-custom_font_size_tablet',
			    'typography_font_size_mobile'   => 'elementor-element-custom_font_size_mobile',
			    'typography_line_height'        => 'elementor-element-custom_line_height',
			    'typography_line_height_tablet' => 'elementor-element-custom_line_height_tablet',
			    'typography_line_height_mobile' => 'elementor-element-custom_line_height_mobile',
			    'typography_font_family'        => 'elementor-element-custom_font_family',
			    'typography_font_weight'        => 'elementor-element-custom_font_weight',
			    'text_color'                    => 'elementor-element-custom_color',
		    );
		    switch($widget->get_name()) {
			    case 'text-editor':
				    foreach($customControls as $key => $class) {
					    $value = $widget->get_settings($key);
					    switch($key) {
						    case 'typography_font_size':
						    case 'typography_font_size_tablet':
						    case 'typography_font_size_mobile':
						    case 'typography_line_height':
						    case 'typography_line_height_tablet':
						    case 'typography_line_height_mobile':
							    if(is_array($value) && key_exists('size', $value) && $value['size'] !== '') {
								    $widget->add_render_attribute('_wrapper', 'class', $customControls[$key]);
							    }
							    break;
						    case 'text_color':
						    case 'typography_font_family':
						    case 'typography_font_weight':
							    if($value !== '') {
								    $widget->add_render_attribute('_wrapper', 'class', $customControls[$key]);
							    }
							    break;
					    }
				    }
				    break;
		    }
	    });

	    add_action('elementor/documents/register', [ $this, 'register_default_types' ], 0);
    }

    public function register_default_types() {
	    if (in_array('AdvancedTabs', $this->widgets)) {
	        \Elementor\Plugin::instance()->documents->register_document_type(Templates_Tabs::$name, Templates_Tabs::class);
	    }
    }


	/** @var \Elementor\Elements_Manager $elements_manager */
	public function categories_registered($elements_manager){
		$categories = $elements_manager->get_categories();
		if (!key_exists('gt3-core-elements',$categories)) {
			$elements_manager->add_category(
				'gt3-core-elements',
				array(
					'title' => esc_html__('GT3 Core Widgets', 'gt3_themes_core'),
					'icon'  => 'fa fa-plug'
				)
			);
		}
	}


	public function elementor_init(){
		$elements_manager = Plugin::instance()->elements_manager;
		$categories = $elements_manager->get_categories();
		if (!key_exists('gt3-core-elements',$categories)) {
			$elements_manager->add_category(
				'gt3-core-elements',
				array(
					'title' => esc_html__('GT3 Core Widgets', 'gt3_themes_core'),
					'icon'  => 'fa fa-plug'
				)
			);
		}
		require_once __DIR__.'/core/basic_widget.php';

		$this->include_files();
	}

    /**
     * @param \Elementor\Controls_Manager $controls_manager
     */
    public function controls_registered($controls_manager){
        if(is_array($this->controls) && !empty($this->controls)) {
            foreach($this->controls as $module) {
                /** @var \Elementor\\GT3_Elementor_Core_Control_{$module} $module */
                $module = sprintf('Elementor\\GT3_Core_Elementor_Control_%s', $module);

                if (class_exists($module)) {
                    if ($controls_manager->get_control($module::type()) === false) {
                        $controls_manager->register_control($module::type(), new $module);
                    }
                }
            }
        }

        if(is_array($this->group_controls) && !empty($this->group_controls)) {
            foreach($this->group_controls as $module) {
                /** @var \Elementor\\GT3_Elementor_Core_Control_{$module} $module */
                $module = sprintf('Elementor\\GT3_Core_Elementor_Control_%s', $module);

                if (class_exists($module)) {
                    if ($controls_manager->get_control($module::type()) === false) {
                        $controls_manager->add_group_control($module::type(), new $module);
                    }
                }
            }
        }
    }

    /**
     * @param string $widget
     * @param bool   $include
     *
     * @return string
     */
    private function get_widget_init_template($widget, $include = true){
        $name_lower = strtolower($widget);

        $template = locate_template(array( 'widgets/'.$name_lower.'/init.php', 'elementor/widgets/'.$name_lower.'/init.php' ));
        if(empty($template) && file_exists(self::$PATH.'widgets/'.$name_lower.'/init.php')) {
            $template = self::$PATH.'widgets/'.$name_lower.'/init.php';
        }
        if(!empty($template) && $include) {
            require_once $template;
        } else {
            return $template;
        }
    }

    /**
     * @param string $control
     * @param bool   $include
     *
     * @return string
     */
    private function get_control_init_template($control, $include = true){
        $name_lower = strtolower($control);

        $template = locate_template(array( 'controls/'.$name_lower.'.php', 'elementor/controls/'.$name_lower.'.php' ));
        if(empty($template) && file_exists(self::$PATH.'controls/'.$name_lower.'.php')) {
            $template = self::$PATH.'controls/'.$name_lower.'.php';
        }
        if(!empty($template) && $include) {
            require_once $template;
        } else {
            return $template;
        }
    }

    private function include_files(){
    	require_once __DIR__.'/core/ajax.php';

        $this->controls = apply_filters('gt3/elementor/controls/register', $this->controls);

        if(is_array($this->controls) && !empty($this->controls)) {
            foreach($this->controls as $slug => $module) {
                $this->get_control_init_template($module);
            }
        }
        if(is_array($this->group_controls) && !empty($this->group_controls)) {
            foreach($this->group_controls as $slug => $module) {
                $this->get_control_init_template($module);
            }
        }

        $this->widgets = apply_filters('gt3/elementor/widgets/register', $this->widgets);

        if(is_array($this->widgets) && !empty($this->widgets)) {
            foreach($this->widgets as $module) {
                $this->get_widget_init_template($module);
                $module = sprintf('ElementorModal\\Widgets\\GT3_Core_Elementor_Widget_%s', $module);
                if(class_exists($module)) {
                    new $module();
                }
            }
        }
    }

	public function wp_footer(){
		if(isset($GLOBALS['gt3_core_elementor__footer'])) {
			?>
			<div
				id="popup_gt3_elementor_gallery"
				class="gt3pg_gallery_wrap gt3pg_wrap_controls gt3_gallery_type_lightbox gt3pg_version_lite">
				<div class="gt3pg_slide_header">
					<div class="free-space"></div>
					<div class="gt3pg_close_wrap">
						<div class="gt3pg_close"></div>
					</div>
				</div>

				<div class="gt3pg_slides"></div>
				<div class="gt3pg_slide_footer">
					<div class="gt3pg_title_wrap">
						<div class="gt3pg_title gt3pg_clip"></div>
						<div class="gt3pg_description gt3pg_clip"></div>
					</div>
					<div class="free-space"></div>
					<div class="gt3pg_caption_wrap">
						<div class="gt3pg_caption_current"></div>
						<div class="gt3pg_caption_delimiter"></div>
						<div class="gt3pg_caption_all"></div>
					</div>
				</div>

				<div class="gt3pg_controls">
					<div class="gt3pg_prev_wrap">
						<div class="gt3pg_prev"></div>
					</div>
					<div class="gt3pg_next_wrap">
						<div class="gt3pg_next"></div>
					</div>
				</div>
			</div>
			<?php
			echo $GLOBALS['gt3_core_elementor__footer'];
		};
	}

    public function frontend_enqueue_styles(){
        wp_enqueue_style('gt3-elementor-core-frontend', self::$CSS_URL.'frontend.css');
    }

    public function frontend_enqueue_scripts(){
        wp_enqueue_script('gt3-elementor-core-frontend-core', plugins_url('/assets/js/core-frontend.js', __FILE__), array(), $this::version, true);
    }

    public function editor_enqueue_scripts(){
        wp_enqueue_script('gt3-sortable', plugins_url('/assets/js/Sortable.js', __FILE__), array(), '1.7.0', true);
	    wp_enqueue_media();
	    wp_enqueue_script('media-grid');
	    wp_enqueue_script('media');
	    wp_enqueue_script('gt3-elementor-core-editor-core', plugins_url('/assets/js/core-editor.js', __FILE__), array( 'wp-i18n','imagesloaded' ), $this::version, true);


    }

    public function editor_enqueue_styles(){
        wp_enqueue_style('gt3-elementor-core-editor-core', plugins_url('/assets/css/core-editor.css', __FILE__), array(), $this::version);
    }

    public function enqueue_scripts() {
        /* CSS */
	    wp_register_style('slick',
            plugins_url('/assets/css/slick.css', __FILE__)
        );

	    wp_register_style('elementor-blueimp-gallery', plugins_url('/assets/css/gallery.css', __FILE__));

	    wp_register_style('swipebox_style',
		    plugins_url('/assets/js/swipebox/css/swipebox.min.css', __FILE__)
	    );

        /* JS */
        $translation_array = array(
            'ajaxurl' => esc_url(admin_url('admin-ajax.php'))
        );
        wp_localize_script( 'jquery', 'gt3_themes_core', $translation_array );

        wp_register_script('slick',
            plugins_url('/assets/js/slick.js', __FILE__),
            array('jquery'),
            $this::version,
            true
        );

        wp_register_script('countUp',
            plugins_url('/assets/js/countUp.js', __FILE__),
            array( 'jquery' ),
            $this::version,
            true
        );

        wp_register_script('circle-progress',
            plugins_url('/assets/js/circle-progress.min.js', __FILE__),
            array( 'jquery' ),
            $this::version,
            true
        );

	    $rtl_sufix = '';
	    if (is_rtl()) {
		    $rtl_sufix = '_rtl';
	    }
        wp_register_script('gt3_isotope_js',
            plugins_url('/assets/js/jquery.isotope'.$rtl_sufix.'.min.js', __FILE__),
            array( 'jquery' ),
            $this::version,
            true
        );

        wp_register_script('countdown',
            plugins_url('/assets/js/jquery.countdown.min.js', __FILE__),
            array( 'jquery' ),
            $this::version,
            true
        );

        wp_register_script('elementor-blueimp-gallery',
            plugins_url('/assets/js/gallery.min.js', __FILE__),
            array( 'jquery' ),
            $this::version,
            true
        );

        wp_register_script('swipebox_js',
            plugins_url('/assets/js/swipebox/js/jquery.swipebox.min.js', __FILE__),
            array( 'jquery' ),
            '1.4.4',
            true
        );

        wp_register_script('google-maps-api', add_query_arg('key', gt3_option('google_map_api_key'), 'https://maps.google.com/maps/api/js'), array(), '', true);

    }
}

GT3_Core_Elementor_Plugin::instance();


