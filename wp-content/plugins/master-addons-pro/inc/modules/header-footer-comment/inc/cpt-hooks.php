<?php 
namespace MasterHeaderFooter;
defined( 'ABSPATH' ) || exit;

class JLTMA_CPT_Hook {
    public static $instance = null;

    public function __construct() {
    
		add_action( 'admin_init', [ $this, 'add_author_support_to_column' ], 10 );
		add_filter( 'manage_master_template_posts_columns', [ $this, 'jltma_master_template_columns' ] );
        add_action( 'manage_master_template_posts_custom_column', [ $this, 'jltma_master_template_render_column' ], 10, 2 );
        add_filter( 'parse_query', [ $this, 'query_filter' ] );
    }

    public function add_author_support_to_column() {
        add_post_type_support( 'master_template', 'author' ); 
    }

	/**
	 * Set custom column for template list.
	 */
	public function jltma_master_template_columns( $columns ) {

		$date_column = $columns['date'];
		$author_column = $columns['author'];

		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns['type']      = esc_html__( 'Type', JLTMA_TD );
		$columns['condition'] = esc_html__( 'Conditions', JLTMA_TD );
		$columns['date']      = $date_column;
		$columns['author']    = $author_column;

		return $columns;
	}


	public function jltma_master_template_render_column( $column, $post_id ) {
		switch ( $column ) {
            case 'type':
            
				$type = get_post_meta( $post_id, 'master_template_type', true );
                $active = get_post_meta( $post_id, 'master_template_activation', true );

                echo ucfirst($type) . (($active == 'yes') 
                ? ( '<span class="jltma-hf-status jltma-hf-status-active">'. esc_html__('Active', JLTMA_TD) .'</span>' ) 
                : ( '<span class="jltma-hf-status jltma-hf-status-inactive">'. esc_html__('Inactive', JLTMA_TD) .'</span>' ));

				break;
            case 'condition':

                $cond = [
                    'jltma_hf_conditions'   => get_post_meta($post_id, 'master_template_jltma_hf_conditions', true),
                    'jltma_hfc_singular'    => get_post_meta($post_id, 'master_template_jltma_hfc_singular', true),
                    'jltma_hfc_singular_id' => get_post_meta($post_id, 'master_template_jltma_hfc_singular_id', true),
                ];

                echo ucwords( str_replace('_', ' ',
                    $cond['jltma_hf_conditions']  
                    . (($cond['jltma_hf_conditions'] == 'singular')
                        ? (($cond['jltma_hfc_singular'] != '' )
                            ? (' > ' . $cond['jltma_hfc_singular'] 
                            . (($cond['jltma_hfc_singular_id'] != '')
                                ? ' > ' . $cond['jltma_hfc_singular_id']
                                : ''))
                            : '')
                        : '')
                ));

				break;
		}
    }
    

    public function  query_filter($query) {
        global $pagenow;
        $current_page = isset( $_GET['post_type'] ) ? sanitize_key($_GET['post_type']) : '';

        if ( 
            is_admin() 
            && 'master_template' == $current_page 
            && 'edit.php' == $pagenow   
            && isset( $_GET['master_template_type_filter'] ) 
            && $_GET['master_template_type_filter'] != ''
            && $_GET['master_template_type_filter'] != 'all'
        ){
            $type = sanitize_key($_GET['master_template_type_filter']);
            $query->query_vars['meta_key'] = 'master_template_type';
            $query->query_vars['meta_value'] = $type;
            $query->query_vars['meta_compare'] = '=';
        }
    }


    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

new JLTMA_CPT_Hook();