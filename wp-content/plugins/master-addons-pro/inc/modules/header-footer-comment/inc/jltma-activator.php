<?php 
namespace MasterHeaderFooter;
use MasterHeaderFooter\Master_Header_Footer;


defined( 'ABSPATH' ) || exit;

class JLTMA_HF_Activator {
    public static $instance = null;

    protected $templates;
    public $header_template;
    public $footer_template;
    public $comment_template;

    protected $current_theme;
    protected $current_template;

    protected $post_type = 'master_template';

    public function __construct() {
        
        $this->jltma_include_theme_support_files();
        
        add_action( 'wp', array( $this, 'jltma_hooks' ) );

    }

    public function jltma_include_theme_support_files(){
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/theme-support.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/hello-elementor.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/storefront.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/astra.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/bbtheme.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/generatepress.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/genesis.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/my-listing.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/oceanwp.php';
        include JLTMA_PLUGIN_PATH . '/inc/theme-hooks/twenty-nineteen.php';
    }

    
    public function jltma_hooks(){
        $this->current_template = basename(get_page_template_slug());
        
        if($this->current_template == 'elementor_canvas'){
            return;
        }

        $this->current_theme = get_template();

        switch($this->current_theme){

            case 'hello-elementor':  case 'hello-elementor-child':
                new Theme_Hooks\Hello_Elementor(self::template_ids());
                break;

            case 'astra':
                new Theme_Hooks\Astra(self::template_ids());
                break;

            case 'storefront':  case 'storefront-child':
                new Theme_Hooks\Storefront(self::template_ids());
                break;

            case 'generatepress':  case 'generatepress-child':
                new Theme_Hooks\Generatepress(self::template_ids());
                break;

            case 'oceanwp': case 'oceanwp-child':
                new Theme_Hooks\Oceanwp(self::template_ids());
                break;

            case 'bb-theme':  case 'bb-theme-child':
                new Theme_Hooks\Bbtheme(self::template_ids());
                break;

            case 'genesis':  case 'genesis-child':
                new Theme_Hooks\Genesis(self::template_ids());
                break;

            case 'twentynineteen':
                new Theme_Hooks\TwentyNineteen(self::template_ids());
                break;

            case 'my-listing': case 'my-listing-child':
                new Theme_Hooks\MyListing(self::template_ids());
                break;

            default:
                new Theme_Hooks\Theme_Support(self::template_ids());
                return;

        }

        
    }

    public static function template_ids(){
        $cached = wp_cache_get( 'master_template_ids' );

		if ( false !== $cached ) {
			return $cached;
        }
        
        $instance = self::instance();
        $instance->the_filter();

        $ids = [
            $instance->header_template,
            $instance->footer_template,
            $instance->comment_template,
        ];

        if($instance->header_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->header_template);
        }

        if($instance->footer_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->footer_template);
        }

        if($instance->comment_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->comment_template);
        }

        wp_cache_set( 'master_template_ids', $ids );

        return $ids;
    }

    protected function the_filter(){
        $arg = [
            'posts_per_page'   => -1,
            'orderby'          => 'id',
            'order'            => 'DESC',
            'post_status'      => 'publish',
            'post_type'        => $this->post_type,
            'meta_query' => [
                [
                    'key'     => 'master_template_activation',
                    'value'   => 'yes',
                    'compare' => '=',
                ],
            ],
        ];
        $this->templates = get_posts($arg);

        // entire site
        if(!is_admin()){
            $filters = [[
                'key'     => 'jltma_hf_conditions',
                'value'   => 'entire_site',
            ]];
            $this->get_header_footer($filters);
        }

        // all archive
        if(is_archive()){
            $filters = [[
                'key'     => 'jltma_hf_conditions',
                'value'   => 'archive',
            ]];
            $this->get_header_footer($filters);
        }

        // all singular
        if(is_page() || is_single() || is_404()){
            $filters = [
                [
                    'key'     => 'jltma_hf_conditions',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'jltma_hfc_singular',
                    'value'   => 'all',
                ]
            ];
            $this->get_header_footer($filters);
        }
        
        // all pages, all posts, 404 page
        if(is_page()){
            $filters = [
                [
                    'key'     => 'jltma_hf_conditions',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'jltma_hfc_singular',
                    'value'   => 'all_pages',
                ]
            ];
            $this->get_header_footer($filters);
        }elseif(is_single()){
            $filters = [
                [
                    'key'     => 'jltma_hf_conditions',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'jltma_hfc_singular',
                    'value'   => 'all_posts',
                ]
            ];
            $this->get_header_footer($filters);
        }elseif(is_404()){
            $filters = [
                [
                    'key'     => 'jltma_hf_conditions',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'jltma_hfc_singular',
                    'value'   => '404page',
                ]
            ];
            $this->get_header_footer($filters);
        }

        // singular selective
        if(is_page() || is_single()){
            $filters = [
                [
                    'key'     => 'jltma_hf_conditions',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'jltma_hfc_singular',
                    'value'   => 'selective',
                ],
                [
                    'key'     => 'jltma_hfc_singular_id',
                    'value'   => get_the_ID(),
                ]
            ];
            $this->get_header_footer($filters);
        }

        // homepage
        if(is_home() || is_front_page()){
            $filters = [
                [
                    'key'     => 'jltma_hf_conditions',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'jltma_hfc_singular',
                    'value'   => 'front_page',
                ]
            ];
            $this->get_header_footer($filters);
        }


    }

    protected function get_header_footer($filters){
        $template_id = array();

        if($this->templates != null){
            foreach($this->templates as $template){
                $template = $this->get_full_data($template);
                
                $match_found = true;

                // WPML Language Check
                if ( defined( 'ICL_LANGUAGE_CODE' ) ):
                    $current_lang = apply_filters( 'wpml_post_language_details', NULL, $template['ID'] );

                    if ( !empty($current_lang) && !$current_lang['different_language'] && ($current_lang['language_code'] == ICL_LANGUAGE_CODE) ):
                        $template_id[ $template['type'] ] = $template['ID'];
                    endif;
                endif;
                
                foreach($filters as $filter){
                    if($filter['key'] == 'jltma_hfc_singular_id'){

                        $ids = explode(',', $template[$filter['key']]);
                        if(!in_array($filter['value'], $ids)){
                            $match_found = false;
                        }
                    }elseif($template[$filter['key']] != $filter['value']){
                        $match_found = false;
                    }
                    if( $filter['key'] == 'jltma_hf_conditions' && $template[$filter['key']] == 'singular' && count($filters) < 2){
                        $match_found = false;
                    }
                }

                if($match_found == true){
                    if($template['type'] == 'header'){
                        $this->header_template = isset( $template_id['header'] ) ? $template_id['header'] : $template['ID'];
                    }
                    if($template['type'] == 'footer'){
                        $this->footer_template = isset( $template_id['footer'] ) ? $template_id['footer'] : $template['ID'];
                    }
                    if($template['type'] == 'comment'){
                        $this->comment_template = isset( $template_id['comment'] ) ? $template_id['comment'] : $template['ID'];
                    }
                }
            }
        }
    }

    protected function get_full_data($post){
        if($post != null){
            return array_merge((array)$post, [
                'type' => get_post_meta($post->ID, 'master_template_type', true),
                'jltma_hf_conditions'   => get_post_meta($post->ID, 'master_template_jltma_hf_conditions', true),
                'jltma_hfc_singular'    => get_post_meta($post->ID, 'master_template_jltma_hfc_singular', true),
                'jltma_hfc_singular_id' => get_post_meta($post->ID, 'master_template_jltma_hfc_singular_id', true),
            ]);
        }
    }

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

JLTMA_HF_Activator::instance();