<?php
/**
 *	EventON Post Data Store 
 * @+ 3.0.2
 * @u 3.0.2
 */

class EVO_Data_Store{

	public $post_type = 'post';
	public $meta_array_key = '_edata';
	public $meta_array_data = array();
	public $meta_data = array();

	public $ID;
	public $meta = array();

// meta data
	public function get_permalink(){
		return get_permalink( $this->ID);
	}
	public function get_meta($key, $force = false){
		if(!isset($this->meta_data[ $key]) || $force){
			if( !is_array($this->meta_data)) $this->meta_data = array();
			$this->meta_data[ $key] = get_metadata('post',$this->ID, $key, true);	
		} 
				

		if(!isset( $this->meta_data[ $key] )) return false;

		return (is_array($this->meta_data[ $key]) && isset($this->meta_data[ $key][0]) )? 
			maybe_unserialize( $this->meta_data[ $key][0] ): 
			maybe_unserialize( $this->meta_data[ $key] );
	}

	public function load_all_meta(){
		$this->meta_data = get_metadata('post',$this->ID);

		// also populate meta array data if present in all meta
		if( isset($this->meta_data[ $this->meta_array_key ]))
			$this->meta_array_data = maybe_unserialize( $this->meta_data[ $this->meta_array_key ] );
	}

	public function load_certain_meta( $meta_key_array ){

		if(!is_array($meta_key_array)) return false;

		$meta_key_array = array_map(function($v) {
		    return "'" . esc_sql($v) . "'";
		}, $meta_key_array);
		$keys = implode(',', $meta_key_array);

		global $wpdb;
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id='%d' AND meta_key IN ({$keys})"
			, $this->ID)
		, ARRAY_A);	

		if($results && !is_wp_error($results)){
			foreach($results as $d){
				$this->meta_data[ $d['meta_key']] = $d['meta_value'];
			}
		}	
		
	}

	public function load_all_meta_query(){
		global $wpdb;
		$results = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id='{$this->ID}'", ARRAY_A);	

		if($results && !is_wp_error($results)){
			foreach($results as $d){
				$this->meta_data[ $d['meta_key']] = $d['meta_value'];
			}
		}	
	}
	public function get_all_meta(){
		if(count($this->meta_data) == 0) $this->load_all_meta();
		return $this->meta_data;
	}

	public function check_yn($field){
		$val = $this->get_meta($field);

		if(!$val) return false;
		if($val =='yes') return true;
		return false;
	}

	public function load_post(){

		/*
		$results = get_post( $this->ID);

		$this->post_author = $this->author = $results->post_author;
		$this->title = $this->post_title = $results->post_title;
		$this->post_type = $results->post_type;
		$this->post_name = $results->post_name;
		$this->post_date = $results->post_date;
		$this->content = $this->post_content = $results->post_content;
		$this->post_excerpt = $this->excerpt = $results->post_excerpt;
		$this->post_status = $results->post_status;
		$this->post_parent = $results->post_parent;

		$time = strtotime($this->post_date);
		$this->time = date('M j,Y \A\T g:iA', $time);

		return;
		*/

		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID='{$this->ID}'");
		
		if($results && count($results)>0){
			$this->populate_post( $results[0]);
			return true;
		}
		return false;
	}
	public function populate_post($post_object){

		$results = $post_object;
		$this->post_author = $this->author = $results->post_author;
		$this->title = $this->post_title = $results->post_title;
		$this->post_type = $results->post_type;
		$this->post_name = $results->post_name;
		$this->post_date = $results->post_date;
		$this->content = $this->post_content = EVO()->frontend->filter_evo_content($results->post_content);
		$this->post_excerpt = $this->excerpt = $results->post_excerpt;
		$this->post_status = $results->post_status;
		$this->post_parent = $results->post_parent;

		$time = strtotime($this->post_date);
		$this->time = date('M j,Y \A\T g:iA', $time);
	}

	public function set_meta($key, $value, $update_meta = true){
		if( !is_array($this->meta_data)) $this->meta_data = array();
		$this->meta_data[$key] = $value;

		if($update_meta) $this->save_meta( $key, $value);
	}
	public function set_multiple_meta($array){
		if(!is_array($array)) return false;

		foreach($array as $key=>$value){
			$this->set_meta( $key, $value);
		}
	}

	public function save_all_meta(){
		//print_r($this->meta_data);
		if(count($this->meta_data) == 0) return false;
		foreach($this->meta_data as $key=>$value){
			$this->save_meta( $key, $value);
		}
	}

	public function save_multiple_meta($array){
		if(!is_array($array)) return false;

		foreach($array as $key=>$value){
			$this->save_meta( $key, $value);
		}
	}

	public function save_meta($key, $value){
		//echo $this->ID.'/'.$this->post_type.'/'.$key;
		update_metadata('post',$this->ID, $key, $value);
		// may be improved with custom wpdb
	}
	public function delete_meta($field){
		delete_metadata('post',$this->ID, $field);
	}

// Legacy
	public function get_prop($field){
		return $this->get_meta( $field );
	}
	public function set_prop($field, $value, $update_meta ){
		$this->set_meta( $field, $value, $update_meta);
	}
	public function save(){
		$this->save_all_meta();
	}
	public function del_prop($f){
		$this->delete_meta($f);
	}

// post
	public function create_new($post_data){
		
		if(empty($post_data) || !is_array($post_data)) return false;

		// Post type
		if(isset($post_data['post_type'])) $this->post_type = $post_data['post_type'];

		// TYPE 
			$valid_type = (function_exists('post_type_exists') &&  post_type_exists($this->post_type) );
			if(!$valid_type)	return false;

		// CONTENT
			$__post_content = !empty($_POST['post_content'])? $_POST['post_content']: 
				(!empty($post_data['post_content'])?$post_data['post_content']:false);
			$__post_content = ($__post_content)?
		        wpautop(convert_chars(stripslashes($__post_content))): '';

	    // author id
		    $post_author = 1;
		    if( !empty($post_data['post_author']) ) $post_author = $post_data['post_author'];
		    if( !empty($post_data['author_id']) ) $post_author = $post_data['author_id'];
		    
		    if($post_author == 1){
		    	$current_user = wp_get_current_user();
		    	if( ($current_user instanceof WP_User) )	$post_author = $current_user->ID;
		    }

	       
        $new_post = array(
            'post_title'   => wp_strip_all_tags($post_data['post_title']),
            'post_content' => $__post_content,
            'post_status'  => (isset($post_data['post_status'])? $post_data['post_status']:'publish'),
            'post_type'    => $this->post_type,
            'post_name'    => sanitize_title($post_data['post_title']),
            'post_author'  => $post_author,
            'post_parent'  => (isset($post_data['post_parent'])? $post_data['post_parent']:''),
        );
	    $id =  wp_insert_post($new_post);


	    if($id && !is_wp_error($id)){
	    	$this->ID = $id;
	    	$this->post_content = $__post_content;
	    	$this->post_author = $post_author;

	    	// set new post meta - pas inside $post_data['meta'];
	    	if(isset($post_data['meta']) && is_array($post_data['meta'])){	    		
	    		$this->set_multiple_meta( $post_data['meta'] );	    		
	    	}

	    	// array meta values
	    	if(isset($post_data['array_meta']) && is_array($post_data['array_meta'])){	    		
	    		$this->set_multiple_meta( $post_data['array_meta'] );	    		
	    	}

	    	// save meta to database
    		if(isset($post_data['save_meta']) && $post_data['save_meta']){
    			$this->save_all_meta();
    		}

	    	return $id;
	    }
	    return false;
		
	}

// Meta data as array
	public function load_meta_array($meta_array_key='', $force = false){
		if(!empty($meta_array_key)) $this->meta_array_key = $meta_array_key;
		$this->meta_array_data = $this->get_meta($this->meta_array_key, $force);

		return $this->meta_array_data? $this->meta_array_data: array();
	}
	public function get_array_meta($field){

		if( !is_array( $this->meta_array_data )) return false;
		if(count($this->meta_array_data)==0) return false;
		if(!isset($this->meta_array_data[$field])) return false;

		return $this->meta_array_data[$field];
	}

	public function check_yn_array_meta($field){
		$value = $this->get_array_meta( $field);
		if(!$value) return $value;
		if($value == 'yes') return true;
		return false;
	}
	public function set_array_meta( $field, $val , $meta_array_key='', $force = false){
		
		if(!empty($meta_array_key)) $this->meta_array_key = $meta_array_key;

		if(!is_array($this->meta_array_data)) $this->meta_array_data = array();

		$this->meta_array_data[$field] = sanitize_text_field($val);
		$this->set_meta( $this->meta_array_key, $this->meta_array_data);

		if($force) $this->save_meta( $this->meta_array_key , $this->meta_array_data );				
	}
	public function save_array_meta($meta_array_key=''){
		if(!empty($meta_array_key)) $this->meta_array_key = $meta_array_key;
		$this->save_meta( $this->meta_array_key, $this->meta_array_data);
	}

	// DELETE
	public function delete_array_meta($field, $meta_array_key='', $force = false){
		if(!empty($meta_array_key)) $this->meta_array_key = $meta_array_key;
		
		$this->meta_array_data = $this->get_meta($this->meta_array_key, $force);

		if(!isset($this->meta_array_data[$field])) return true;

		unset($this->meta_array_data[$field]);

		$this->set_meta( $meta_array_key, $this->meta_array_data);

		if($force) $this->save_meta( $meta_array_key , $this->meta_array_data );	
		
	}
	public function delete_post(){
		if(empty($this->ID)) return false;
		wp_trash_post( $this->ID);
	}
}