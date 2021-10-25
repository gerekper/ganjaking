<?php 
namespace MasterHeaderFooter;

defined( 'ABSPATH' ) || exit;

class Master_Addons_CPT{

    const CPT = 'master_template';

    public function __construct() {
        add_action( 'init', [ $this, 'jltma_register_post_type' ] );
        add_action( 'admin_menu', [$this, 'jltma_cpt_menu'], 50);
        add_filter( 'single_template', [ $this, 'load_canvas_templates' ] ); 
    }


    function load_canvas_templates( $single_template ) {

        global $post;

        if ( 'master_template' == $post->post_type ) {

            $elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

            if ( file_exists( $elementor_2_0_canvas ) ) {
                return $elementor_2_0_canvas;
            } else {
                return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
            }
        }

        return $single_template;
    }




    public function jltma_register_post_type() {
        
        $labels = array(
            'name'               => esc_html__( 'MA Templates', JLTMA_TD ),
            'singular_name'      => esc_html__( 'MA Template', JLTMA_TD ),
            'menu_name'          => esc_html__( 'MA Templates', JLTMA_TD ),
            'name_admin_bar'     => esc_html__( 'MA Templates', JLTMA_TD ),
            'add_new'            => esc_html__( 'Add New', JLTMA_TD ),
            'add_new_item'       => esc_html__( 'Add New Template', JLTMA_TD ),
            'new_item'           => esc_html__( 'New Template', JLTMA_TD ),
            'edit_item'          => esc_html__( 'Edit Template', JLTMA_TD ),
            'view_item'          => esc_html__( 'View Template', JLTMA_TD ),
            'all_items'          => esc_html__( 'All Templates', JLTMA_TD ),
            'search_items'       => esc_html__( 'Search Templates', JLTMA_TD ),
            'parent_item_colon'  => esc_html__( 'Parent Templates:', JLTMA_TD ),
            'not_found'          => esc_html__( 'No Templates found.', JLTMA_TD ),
            'not_found_in_trash' => esc_html__( 'No Templates found in Trash.', JLTMA_TD ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'rewrite'             => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'capability_type'     => 'page',
            'hierarchical'        => true,
            'supports'            => array( 'title', 'thumbnail', 'elementor', 'comments' )
        );

        register_post_type( 'master_template', $args );
    }

    public function jltma_cpt_menu(){
        add_submenu_page(
            'master-addons-settings',
            esc_html__('MA Templates', JLTMA_TD),
            esc_html__('MA Templates', JLTMA_TD),
            'manage_options',
            'edit.php?post_type=master_template'
        );
    }
    
}

new Master_Addons_CPT();