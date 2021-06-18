<?php

add_action('before_default_layout','upml_memberlist_functions');
function upml_memberlist_functions($args = array()){
    $settings = get_option('userpro_memberlists');
    if( isset( $settings['user_memberlist_template'] )  && $settings['user_memberlist_template'] != 0){
        include_once( userpro_memberlists_path.'functions/upml-layout-switcher.php');
        $layout_switch = new upml_layout_switcher();
        $layout_switch->upml_load_layout($settings['user_memberlist_template']);
        include userpro_memberlists_path . "templates/template".$settings['user_memberlist_template']."/index.php";
    }
    return 0;
}