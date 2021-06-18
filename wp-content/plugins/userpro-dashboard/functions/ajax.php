<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !class_exists( 'UPDBAjax' ) ){
	class UPDBAjax{
		function __construct(){
	
			add_action( 'wp_ajax_updb_save_widgets', array( $this, 'updb_save_widgets' ) );
			add_action( 'wp_ajax_nopriv_updb_save_widgets', array( $this, 'updb_save_widgets' ) );
			
			add_action( 'wp_ajax_upd_add_new_widget', array( $this, 'upd_add_new_widget' ) );
			add_action( 'wp_ajax_nopriv_upd_add_new_widget', array( $this, 'upd_add_new_widget' ) );
		
			add_action( 'wp_ajax_upd_save_new_widget', array( $this, 'upd_save_new_widget' ) );
			add_action( 'wp_ajax_nopriv_upd_save_new_widget', array( $this, 'upd_save_new_widget' ) );
			
			add_action( 'wp_ajax_upd_edit_custom_widget', array( $this, 'upd_edit_custom_widget' ) );
			add_action( 'wp_ajax_nopriv_upd_edit_custom_widget', array( $this, 'upd_edit_custom_widget' ) );
			
			add_action( 'wp_ajax_updb_delete_widget', array( $this, 'updb_delete_widget' ) );
			add_action( 'wp_ajax_nopriv_updb_delete_widget', array( $this, 'updb_delete_widget' ) );
                        
                        add_action( 'wp_ajax_updb_edit_post', array( $this, 'updb_edit_post' ) );
			add_action( 'wp_ajax_nopriv_updb_edit_post', array( $this, 'updb_edit_post' ) );
                        
                        add_action( 'wp_ajax_updb_nextpage', array( $this, 'updb_nextpage' ) );
			add_action( 'wp_ajax_nopriv_updb_nextpage', array( $this, 'updb_nextpage' ) );
                        
                        add_action( 'wp_ajax_updb_pagination', array( $this, 'updb_pagination' ) );
			add_action( 'wp_ajax_nopriv_updb_pagination', array( $this, 'updb_pagination' ) );
		
		}
		
		function updb_save_widgets(){
			if( !isset( $updb_customizer_api ) ){
				$updb_customizer_api = new UPDBProfileCustomizer();
			}
			$updb_customizer_api->save_column_widgets();
			die();
		}
		
		function upd_add_new_widget(){
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			if($updb_default_options->updb_get_option('custom_widget_section') == 1){
				ob_start();
				include_once UPDB_PATH . 'admin/templates/add-custom-widgets.php';
				
				$template = ob_get_contents();
				ob_end_clean();
				echo json_encode(array('html' => $template));
				die;
			}
		}
		
		function upd_save_new_widget(){
			$updb_custom_widgets_options = get_option('updb_custom_widgets');
			if(empty($updb_custom_widgets_options)){
				$updb_custom_widgets_options = array();
			}
			if(isset($_POST['widget_id_save'])){
				$widget_id=$_POST['widget_id_save'];
				$updb_custom_widgets_options[$widget_id] = array('title' => $_POST['widget_title'], 'content' => $_POST['widget_content']);
				
			}else{
				$widget_count=count($updb_custom_widgets_options);
				$widget_id=$_POST['widget_id'].'_'.$widget_count;
				$new_custom_widget[$widget_id] = array('title' => $_POST['widget_title'], 'content' => $_POST['widget_content']);
				$updb_custom_widgets_options = array_merge($updb_custom_widgets_options , $new_custom_widget);
			}
			update_option('updb_custom_widgets', $updb_custom_widgets_options );
		}
		
		function updb_delete_widget(){
			$widget_id = $_POST['widget_id'];
			$updb_custom_widgets_options = get_option('updb_custom_widgets');
			if(isset($updb_custom_widgets_options)){
				unset($updb_custom_widgets_options[$widget_id]);
			}
			update_option('updb_custom_widgets', $updb_custom_widgets_options );
		}
                function updb_edit_post(){
                        if(!empty( $_POST['post_id'])){
                            $_GET['post_id'] = $_POST['post_id'];
                        }
			ob_start();
                        echo do_shortcode("[userpro template='publish']");
                        $output['response'] = ob_get_contents();
                        ob_end_clean();
		
                        $output=json_encode($output);
                        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
		}
                
                function updb_pagination($ajax=false, $page_no=1){
                    if(!empty($_POST['ajax'])){
                       $ajax = $_POST['ajax'];
                    }
                    if(!empty($_POST['page_no'])){
                        $page_no = $_POST['page_no'];
                    }
                    global $current_user;
                    $output = '';
                    wp_get_current_user();
                    $updb_default_options = new UPDBDefaultOptions();
                    $updb_post_count = $updb_default_options->updb_get_option( 'userpro_db_post_count' );
                    $author_query = array('posts_per_page' => $updb_post_count,'author' => $current_user->ID,'post_status' => array( 'pending', 'draft','publish'), 'paged' => $page_no);

                    $author_posts =  get_posts($author_query);
                    if(!empty($author_posts)){
                        ob_start();
                        foreach($author_posts as $single_post){
                            include UPDB_PATH.'templates/edit-my-post.php';
                        }
                        
                        include UPDB_PATH.'templates/post-pagination.php';
                        $output = ob_get_contents();
                        $output .='<div class="updb-new-post-add">
                                    <input type="button" class="updb-add-new-post" value="Add New" >
                                </div>';
                        ob_end_clean();
                    }
                    
                    
                    if($ajax == false){
                        return $output ;
                    }
                    else{
                        $output=json_encode($output);
                        if(is_array($output)){ print_r($output); }else{ echo $output; } die;
                    }
                }
	}
	new UPDBAjax();
}