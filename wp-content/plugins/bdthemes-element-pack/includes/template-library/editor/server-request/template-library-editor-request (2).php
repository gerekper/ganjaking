<?php

namespace ElementPack\Includes\TemplateLibrary\Editor\ServerRequest;

use ElementPack\Includes\TemplateLibrary\ElementPack_Template_Library_Base;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

defined('ABSPATH') || exit;
class ElementPackTemplateLibraryEditorApi extends ElementPack_Template_Library_Base
{
    protected $source = null;

    public function __construct()
    {
        parent::__construct();

        add_action('wp_ajax_bdt_element_pack_template_library_get_layouts', [$this, 'get_layouts']);
        add_action('wp_ajax_bdt_element_pack_template_library_making_syncing', [$this, 'syncBtnClick']);
        add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions_data' ] );
    }

    public function register_ajax_actions_data( Ajax $ajax ) {

        $ajax->register_ajax_action( 'get_bdt_elementpack_template_data', function( $data ) {
            if ( ! current_user_can( 'edit_posts' ) ) {
                throw new \Exception( 'Access Denied' );
            }

            if ( ! empty( $data['editor_post_id'] ) ) {
                $editor_post_id = absint( $data['editor_post_id'] );

                if ( ! get_post( $editor_post_id ) ) {
                    throw new \Exception( __( 'Post not found', 'bdthemes-element-pack' ) );
                }

                \Elementor\Plugin::instance()->db->switch_to_post( $editor_post_id );
            }

            if ( empty( $data['template_id'] ) ) {
                throw new \Exception( __( 'Template id missing', 'bdthemes-element-pack' ) );
            }

            return $this->get_template_data( $data );
        } );
    }

    public function get_template_data( array $args ) {

        $source = $this->get_source();
        $result = $this->findDemo($args['template_id']);

        if(!is_array($result) || !isset($result['json_url'])){
            throw new \Exception( __( 'Template id missing', 'bdthemes-element-pack' ) );
        }

        if($result['is_pro'] == 1 && !$this->packLicenseActivated){
            throw new \Exception( __( 'required_activated_license', 'bdthemes-element-pack' ) );
        }

        $args['demo_json'] = $result['json_url'];
        $data = $source->get_data( $args );

        return $data;
    }

    public function get_source() {
        if ( is_null( $this->source ) ) {
            $this->source = new Library_Source();
        }

        return $this->source;
    }

    public function getCategoriesItems() {

        $this->checkDemoData();
        $demoDataType = $this->demoType;
        // Table Info
        $postTable      = $this->table_post;
        $postCatTable   = $this->table_cat_post;
        $catTable       = $this->table_cat;

        $demoData = $this->wpdb->get_results("SELECT COUNT(*) as ttotal, {$catTable}.* FROM {$postTable}
 LEFT JOIN {$postCatTable}  ON {$postTable}.demo_id = {$postCatTable} .demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = wp_ep_template_library_cat_post.term_id
 WHERE type={$demoDataType} GROUP BY term_id", ARRAY_A);

        $navItems = array();
        $totalDemo = 0;
        foreach ( $demoData as $data ) {
            $total = intval($data['ttotal']);
            $totalDemo = $totalDemo + $total;
            $navItems[] = array( 'term_slug' => $data['slug'], 'term_name' => $data['name'],'term_id' => $data['term_id'],'count'=> $total);
        }
        $this->demo_total = $totalDemo;
        $firstItem = array( 'term_slug' => '', 'term_name' => 'All Templates','term_id' => 0,'count'=> $totalDemo);

        return array_merge_recursive([$firstItem], $navItems);
    }

    public function get_layouts()
    {
        isset($_REQUEST['tab']) || exit();

        $tab = (empty($_REQUEST['tab']) ? 'bdt_elementpack_page' : $_REQUEST['tab']);

        if($tab == 'bdt_elementpack_block'){
            $this->demoType = 2;
        }elseif($tab == 'bdt_elementpack_header'){
            $this->demoType = 3;
        }elseif($tab == 'bdt_elementpack_footer'){
            $this->demoType = 4;
        }else{
            $this->demoType = 1;
        }

        $this->termSlug = 'demo_term_all';
        if(isset($_REQUEST['term_slug']) && !empty($_REQUEST['term_slug'])){
            $this->termSlug = $_REQUEST['term_slug'];
        }

        $this->perPage = 500000;

        echo wp_send_json([
            'data'=>[
                'categories'    => $this->getCategoriesItems(),
                'templates'     => $this->getElementorLibraryData()
            ]
        ],200);

    }

    public function syncBtnClick(){

        $this->createTemplateTables();

        echo json_encode(
            array(
                'success' => true,
                'data'    => array(),
            )
        );

        wp_die();
    }
}

new ElementPackTemplateLibraryEditorApi();