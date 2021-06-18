<?php
add_action( 'wp_ajax_updb_save_widgets_admin', 'updb_save_widgets_admin' );
add_action( 'wp_ajax_nopriv_updb_save_widgets_admin', 'updb_save_widgets_admin' );

function updb_save_widgets_admin(){
		
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
	
	$widget_col_1 = "";
	$widget_col_2 = "";
	$widget_col_3 = "";
	$unused_widget = "";
		
	$widget_col_1 = $_POST['col1'];
	if( $widget_col_1 != "" &&  $widget_col_1 !="[object Object]" ){
		$widget_col_1 = explode( ',', $widget_col_1 );
		$updb_default_options->updb_set_option( 'updb_admin_widget_layout_1', $widget_col_1 );
	}
	else{
		$widget_col_1 = "";
		$updb_default_options->updb_set_option( 'updb_admin_widget_layout_1', $widget_col_1 );
	}
	$widget_col_2 = $_POST['col2'];
	if( $widget_col_2 != "" &&  $widget_col_2 !="[object Object]" ){
		$widget_col_2 = explode( ',', $widget_col_2 );
		$updb_default_options->updb_set_option( 'updb_admin_widget_layout_2', $widget_col_2 );
	}
	else{
		$widget_col_2 = "";
		$updb_default_options->updb_set_option( 'updb_admin_widget_layout_2', $widget_col_2 );
	}
	
	$widget_col_3 = $_POST['col3'];
	if( $widget_col_3 != "" &&  $widget_col_3 !=="[object Object]" ){
		$widget_col_3 = explode( ',', $widget_col_3 );
		$updb_default_options->updb_set_option( 'updb_admin_widget_layout_3', $widget_col_3 );
	}
	else{
		$widget_col_3 = "";
		$updb_default_options->updb_set_option( 'updb_admin_widget_layout_3', $widget_col_3 );
	}
	
	$unused_widget = $_POST['unused_widget'];
	if( $unused_widget != "" &&  $unused_widget !=="[object Object]" ){
		$unused_widget = explode( ',', $unused_widget );
		$updb_default_options->updb_set_option( 'updb_unused_widgets_admin', $unused_widget );
	}
		
	die();
}