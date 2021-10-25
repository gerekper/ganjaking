<?php 
namespace MasterHeaderFooter;

class JLTMA_Header_Footer_CPT_API extends JLTMA_Header_Footer_Rest_API {

    public function __construct(){
        $this->config("ma-template", "/(?P<id>\w+)/");
        $this->init();
    }

    public function get_update(){
        $id = $this->request['id'];
        $open_editor = $this->request['open_editor'];

        $title = ($this->request['title'] == '') ? ('Master Addons Template #' . time()) : $this->request['title'];
        $activation = $this->request['activation'];
        $type = $this->request['type'];

        $jltma_hf_conditions        = ($type == 'section') ? '' : $this->request['jltma_hf_conditions'];
        $jltma_hfc_singular         = ($type == 'section') ? '' : $this->request['jltma_hfc_singular'];
        $jltma_hfc_singular_id      = ($type == 'section') ? '' : $this->request['jltma_hfc_singular_id'];

        $post_data = array(
            'post_title'    => $title,
            'post_status'   => 'publish',
            'post_type'     => 'master_template',
        );

        $post = get_post($id);
        
        if($post == null){
            $id = wp_insert_post($post_data);
        }else{
            $post_data['ID'] = $id;
            wp_update_post( $post_data );
        }
        
        update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
        update_post_meta( $id, 'master_template_activation', $activation );
        update_post_meta( $id, 'master_template_type', $type );
        update_post_meta( $id, 'master_template_jltma_hf_conditions', $jltma_hf_conditions );
        update_post_meta( $id, 'master_template_jltma_hfc_singular', $jltma_hfc_singular );
        update_post_meta( $id, 'master_template_jltma_hfc_singular_id', implode( ", ", $jltma_hfc_singular_id ) );

        if($open_editor == 'true'){
            $url = get_admin_url() . '/post.php?post='.$builder_post_id.'&action=elementor';
            wp_redirect( $url );
            exit;
        }else{
            $cond = ucwords( str_replace('_', ' ',
                $jltma_hf_conditions  
                . (($jltma_hf_conditions == 'singular')
                    ? (($jltma_hfc_singular != '' )
                        ? (' > ' . $jltma_hfc_singular 
                        . (($jltma_hfc_singular_id != '')
                            ? ' > ' . implode( ", ", $jltma_hfc_singular_id )
                            : ''))
                        : '')
                    : '')
            ));

            return [
                'saved' => true,
                'data' => [
                    'id' => $id,
                    'title' => $title,
                    'type' => $type,
                    'activation' => $activation,
                    'cond_text' => $cond,
                    'type_html' => (ucfirst($type) . (($activation == 'yes') 
                        ? ( '<span class="jltma-hf-status jltma-hf-status-active">'. esc_html__('Active', JLTMA_TD) .'</span>' ) 
                        : ( '<span class="jltma-hf-status jltma-hf-status-inactive">'. esc_html__('Inactive', JLTMA_TD) .'</span>' ))),
                ]
            ];
        }
    }

    public function get_get(){
        $id = $this->request['id'];
        $post = get_post($id);
        if($post != null){
            return [
                'title'                 => $post->post_title,
                'status'                => $post->post_status,
                'activation'            => get_post_meta($post->ID, 'master_template_activation', true),
                'type'                  => get_post_meta($post->ID, 'master_template_type', true),
                'jltma_hf_conditions'   => get_post_meta($post->ID, 'master_template_jltma_hf_conditions', true),
                'jltma_hfc_singular'    => get_post_meta($post->ID, 'master_template_jltma_hfc_singular', true),
                'jltma_hfc_singular_id' => get_post_meta($post->ID, 'master_template_jltma_hfc_singular_id', true),
            ];
        }
        return true;
    }    
}

new JLTMA_Header_Footer_CPT_API();