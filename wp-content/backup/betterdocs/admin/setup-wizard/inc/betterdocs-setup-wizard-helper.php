<?php
if(!function_exists('betterdocs_setup_get_customizer_setting_url')){
    function betterdocs_setup_get_customizer_setting_url(){
        $query['autofocus[panel]'] = 'betterdocs_customize_options';
        $query['return'] = admin_url( 'edit.php?post_type=docs' );
        $allOption = get_option( 'betterdocs_settings' );
        $docs_slug = $allOption['docs_slug'];
        if($docs_slug){
            $query['url'] = site_url( '/'.$docs_slug );
        }
        $customizer_link = add_query_arg( $query, admin_url( 'customize.php' ) );
        return esc_url($customizer_link);
    }
}

if(!function_exists('betterdocs_get_admin_settings_url')){
    function betterdocs_get_admin_settings_url(){
        $settingUrl = 'edit.php?post_type=docs&page=betterdocs-settings';
        if(class_exists('Betterdocs_Pro')){
            $settingUrl = 'admin.php?page=betterdocs-settings';
        }
        return esc_url( admin_url($settingUrl) );     
    }
}

if(!function_exists('betterdocs_setup_docs_page_url')){
    function betterdocs_setup_docs_page_url(){
        $allOption = get_option( 'betterdocs_settings' );
        $docs_slug = $allOption['docs_slug'];
        if($docs_slug){
            return esc_url( site_url( '/'.$docs_slug ) );
        }
        return esc_url( site_url() );
    }
}