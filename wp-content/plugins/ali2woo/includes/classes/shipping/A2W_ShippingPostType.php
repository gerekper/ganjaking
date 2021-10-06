<?php
/**
 * Description of A2W_ShippingPostType
 *
 * @author MA_GROUP
 */
  
if (!class_exists('A2W_ShippingPostType')):

	class A2W_ShippingPostType {
        static public function init() {
          register_post_type( 'a2w_shipping',
            array(
              'labels' => array(
                'name' => __( 'Shipping List', 'ali2woo'),
                'singular_name' => __( 'Shipping List', 'ali2woo')
              ),
              'public' => false,
              'publicly_queriable' => true,
              'show_ui' => true,
              'exclude_from_search' => true,
              'has_archive' => false,
            //  'menu_position'=> 30000,
              'show_in_menu'  => false,
              'supports'           => array( 'title',),
              'rewrite'            => false,
                'capabilities' => array(
                    'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
                  ),
                'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
          
            )
          );
        }  
        
      
          //get shipping data by initial shipping name or by service name
          // return flase if item is disabled and empty if no local name
        static public function get_item($shipping_name = false, $service_name = false){
            
            $args = array();
            
            if ( $shipping_name )
                $args = array(
                    'meta_key' => 'a2w_text_initial_name',
                    'meta_value' => $shipping_name,
                    'post_type' => 'a2w_shipping',
                    'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
                    'posts_per_page' => -1
                );
            
            if ($service_name)
                $args = array(
                    'meta_key' => 'a2w_service_name',
                    'meta_value' => $service_name,
                    'post_type' => 'a2w_shipping',
                    'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
                    'posts_per_page' => 1
                );
                
            $posts = get_posts($args);  
            
            if (count($posts)<1) return '';
            
            $post = $posts[0];
            
            if ($post->post_status !== "publish") return false;
            
            $use_price_rule = A2W_ShippingPriceFormula::allow_post_price_rule($post->ID);
            
            if ( $shipping_name ) 
                return array('id'=>$post->ID, 'title'=>$post->post_title, 'init_name'=>$shipping_name, 'use_price_rule'=> $use_price_rule );
            
            if ( $service_name ) 
                return array('id'=>$post->ID, 'title'=>$post->post_title, 'service_name'=>$service_name, 'use_price_rule'=> $use_price_rule );
            
            return '';
        }
    
        //add new shipping data
        static public function add_item($shipping_name, $service_name){
            
            $shipping_name_f = A2W_PhraseFilter::apply_filter_to_text($shipping_name);
            $id = wp_insert_post(array('post_title'=>$shipping_name_f, 'post_type'=>'a2w_shipping', 'post_status' => 'publish'));
            add_post_meta($id, 'a2w_text_initial_name', $shipping_name);
            add_post_meta($id, 'a2w_service_name', $service_name);
            delete_post_meta($id, 'a2w_use_price_rule');
            add_post_meta($id, 'a2w_use_price_rule', 1);
            
            return $id;    
        } 
  
     	
	}

endif;


add_action('init', array('A2W_ShippingPostType', 'init'));
