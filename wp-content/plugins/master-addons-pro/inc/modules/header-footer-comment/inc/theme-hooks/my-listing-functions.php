<?php
namespace MasterHeaderFooter\Theme_Hooks;
use MasterHeaderFooter\Master_Header_Footer;

if(!function_exists( 'jltma_get_hfc_render_comment' )){
    function jltma_get_hfc_render_comment( $comment_template ){
        global $master_template_ids;
        if($master_template_ids[2] == null){
            return;
        }

        ob_start();
        return JLTMA_PLUGIN_PATH . '/inc/view/theme-support-comment.php';
        ob_get_clean();
    }
}

if(!function_exists( 'jltma_get_hfc_comment_id' )){
    function jltma_get_hfc_comment_id(){
        global $master_template_ids;
        return $master_template_ids[2];
    }
}



if(!function_exists( 'jltma_get_hfc_render_header' )){
    function jltma_get_hfc_render_header(){
        global $master_template_ids;
        if($master_template_ids[0] == null){
            return;
        }

        do_action('masteraddons/template/before_header');
        echo '<div class="jltma-template-content-markup jltma-template-content-header">';
            echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($master_template_ids[0]);
        echo '</div>';
        do_action('masteraddons/template/after_header');
    }
}

if(!function_exists( 'jltma_get_hfc_header_id' )){
    function jltma_get_hfc_header_id(){
        global $master_template_ids;
        return $master_template_ids[0];
    }
}

if(!function_exists( 'jltma_hfc_render_footer' )){
    function jltma_hfc_render_footer(){
        global $master_template_ids;
        if($master_template_ids[1] == null){
            return;
        }

        do_action('masteraddons/template/before_header');
        echo '<div class="jltma-template-content-markup jltma-template-content-header">';
            echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($master_template_ids[1]);
        echo '</div>';
        do_action('masteraddons/template/after_header');
    }
}

if(!function_exists( 'jltma_hfc_get_footer_id' )){
    function jltma_hfc_get_footer_id(){
        global $master_template_ids;
        return $master_template_ids[1];
    }
}
