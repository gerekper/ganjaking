<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !class_exists( 'UPDBProfileCustomizer' ) ){
	
	class UPDBProfileCustomizer{

		function __construct(){
		}

		function get_unused_widgets( $user_id ){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			$active_widgets = array();
			$number_col = $updb_default_options->updb_get_option( 'number_of_column' );
			for( $i=0; $i<=$number_col; $i++)
			{
				$widgets = get_user_meta( $user_id, "updb_widget_col_$i", true );
				if( !empty( $widgets ) ){
					$active_widgets = array_merge( $active_widgets, $widgets );
				}
			}
			$updb_available_widgets = $updb_default_options->updb_get_option( 'updb_available_widgets' );
			//$updb_unused_widgets = get_user_meta( $user_id, 'updb_unused_widgets', true );
			$updb_unused_widgets = $updb_default_options->updb_get_option( 'updb_unused_widgets' );
			if(is_array($updb_unused_widgets))
			foreach( $updb_unused_widgets as $updb_widget ){
				if( in_array( $updb_widget, $active_widgets) ){
					continue;
				}
				include UPDB_PATH.'templates/customizer/template-widget.php';
			}
			
		}
		
		function get_unused_widgets_admin(){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			$active_widgets = array();
			$number_col = $updb_default_options->updb_get_option( 'number_of_column' );
			for( $i=1; $i<=$number_col; $i++)
			{
				$widgets = $updb_default_options->updb_get_option( 'updb_admin_widget_layout_'.$i );
				if( !empty( $widgets ) ){
					$active_widgets = array_merge( $active_widgets, $widgets );
				}
			}
			$updb_available_widgets = $updb_default_options->updb_get_option( 'updb_available_widgets' );
			$updb_unused_widgets = $updb_default_options->updb_get_option( 'updb_unused_widgets' );

			if(is_array($updb_unused_widgets))
				foreach( $updb_unused_widgets as $updb_widget ){
					if( in_array( $updb_widget, $active_widgets) ){
						continue;
					}
					include UPDB_PATH.'templates/customizer/template-widget.php';
				}
		}
		
		function get_column_widgets_admin( $col ){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			$updb_available_widgets = $updb_default_options->updb_get_option( 'updb_available_widgets' );
			
			$updb_col_widgets = $updb_default_options->updb_get_option( 'updb_admin_widget_layout_'.$col );
				
			if( !empty( $updb_col_widgets )  ){
				foreach( $updb_col_widgets as $updb_widget ){
					if( array_key_exists($updb_widget, $updb_available_widgets) ){
						include UPDB_PATH.'templates/customizer/template-widget.php';
					}
				}
			}
		}

		function get_column_widgets( $user_id , $col ){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			$updb_available_widgets = $updb_default_options->updb_get_option( 'updb_available_widgets' );
			$updb_col_widgets = get_user_meta( $user_id, "updb_widget_col_$col", true );
			if( !empty( $updb_col_widgets )  ){
				foreach( $updb_col_widgets as $updb_widget ){
					if( array_key_exists($updb_widget, $updb_available_widgets) ){
						include UPDB_PATH.'templates/customizer/template-widget.php';
					}
				}
			}
		}
		
		function save_column_widgets( $user_id = null ){
			global $wpdb;
			if( !isset( $user_id ) ){
				$user_id = get_current_user_id();	
			}
			$widget_col_1 = "";
			$widget_col_2 = "";
			$widget_col_3 = "";
			$unused_widget = "";
			
			$widget_col_1 = $_POST['col1'];
			if( $widget_col_1 != "" &&  $widget_col_1 !="[object Object]" ){
				$widget_col_1 = explode( ',', $widget_col_1 );
			}
			else{
				$widget_col_1 = "";
			}
			$widget_col_2 = $_POST['col2'];
			if( $widget_col_2 != "" &&  $widget_col_2 !="[object Object]" ){
				$widget_col_2 = explode( ',', $widget_col_2 );
			}
			else{
				$widget_col_2 = "";
			}

			$widget_col_3 = $_POST['col3'];
			if( $widget_col_3 != "" &&  $widget_col_3 !=="[object Object]" ){
				$widget_col_3 = explode( ',', $widget_col_3 );
			}
			else{
				$widget_col_3 = "";
			}

			$unused_widget = $_POST['unused_widget'];
			if( $unused_widget != "" &&  $unused_widget !=="[object Object]" ){
				$unused_widget = explode( ',', $unused_widget );
				update_user_meta ( $user_id, 'updb_unused_widgets', $unused_widget );
			}
			update_user_meta ( $user_id, 'updb_widget_col_1', $widget_col_1 );
			update_user_meta ( $user_id, 'updb_widget_col_2', $widget_col_2 );
			update_user_meta ( $user_id, 'updb_widget_col_3', $widget_col_3 );
		}
		
		function show_column_widgets( $user_id, $col, $args = null, $view_fields = null , $i = 0 ){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			if( $updb_default_options->updb_get_option('userpro_db_custom_layout') == '1'){
				$col_widgets = $updb_default_options->updb_get_option( 'updb_admin_widget_layout_'.$col );
			}
			else{
				$col_widgets = get_user_meta( $user_id, "updb_widget_col_$col", true);
			}
			
			$updb_available_widgets = $updb_default_options->updb_get_option( 'updb_available_widgets' );
			$updb_custom_widgets = get_option('updb_custom_widgets');
			if( !empty( $col_widgets ) ){
				foreach( $col_widgets as $col_widget ){
					if( isset($updb_available_widgets[$col_widget]['widget_content']) ){
							echo "<li>";
								include UPDB_PATH . 'templates/customizer/custom-widgets.php';
							echo "</li>";
					}else if( isset($updb_available_widgets[$col_widget]['template_path']) ){
						$template_path = $updb_available_widgets[$col_widget]['template_path'];
						echo "<li>";
						include $template_path.'widget-'.$col_widget.".php";
						echo "</li>";
					}
				}
			}
		}
	}
}
