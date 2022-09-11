<?php
/**
 * Elementor Integration of eventON
 */

class EVO_Elementor{
	private static $instance = null;

    public static function get_instance() {
      if ( ! self::$instance )
         self::$instance = new self;
      return self::$instance;
    }

    public function init(){
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) ); 

        add_action('elementor/editor/wp_head', array($this, 'editor_header'));
        add_action('elementor/editor/footer', array($this, 'editor_footer'));

        add_action( 'elementor/editor/before_enqueue_scripts', array($this, 'styles'));
        //add_action( 'elementor/editor/after_enqueue_scripts', array($this, 'scripts'));
        add_action( 'elementor/elements/categories_registered', array($this, 'category'));
    }

    // Loading eventON parts to editor
    function editor_header(){
    
        EVO()->evo_admin->wp_admin_scripts_styles();       

        $this->scripts();
    }

    function editor_footer(){     

        wp_enqueue_script('shortcode_generator');
        wp_enqueue_script('backender_colorpicker');
        wp_enqueue_script('ajde_wp_admin');
        wp_enqueue_style('ajde_wp_admin');

        EVO()->lightbox->admin_footer();

        EVO()->elements->register_styles_scripts();
        EVO()->elements->enqueue();
        EVO()->elements->register_shortcode_generator_styles_scripts();
        EVO()->elements->enqueue_shortcode_generator();
    }

    function category($elements_manager){
      $elements_manager->add_category(
           'eventon-category',
           [
            'title' => __( 'EventON', 'eventon' ),
            'icon' => 'fa fa-plug',
           ]
      );
    }
    function styles(){
        wp_enqueue_style( 'evo_wp_admin_widgets',AJDE_EVCAL_URL.'/assets/css/admin/widgets.css',array(), EVO()->version);
        wp_enqueue_style( 'evoelm_css',EVO()->assets_path. 'lib/elementor/elementor.css', [], EVO()->version );
    }
    function scripts(){        
        wp_enqueue_script('evoelm_js', 
            EVO()->assets_path. 'lib/elementor/elementor.js',
            [], EVO()->version
        );        
    }

    

    public function widgets_registered() {

        if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){

            $widget_file = AJDE_EVCAL_PATH.'/includes/integration/elementor/elementor_widget.php';

            require_once $widget_file;

        }
    }
}
EVO_Elementor::get_instance()->init();

