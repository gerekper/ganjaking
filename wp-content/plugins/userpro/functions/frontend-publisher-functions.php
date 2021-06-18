<?php

	/**
	Front end publisher
	functions
	**/
	
	/******************************************
	Get all categories list
	******************************************/
	function userpro_publish_categories($args){
		$taxonomies=get_taxonomies( array('public' => true) , 'names');
		if (isset($args['allowed_taxonomies'])){
			$allowed = explode(',',$args['allowed_taxonomies']);
		} else {
			$allowed = array('category','post_tag');
		}
		if(isset($args['category_select'])){
			$category_select = explode(',',$args['category_select']);
		}
		$taxonomies = array_intersect( $taxonomies, $allowed );
		foreach ($taxonomies as $taxonomy ) {
			$the_tax = get_taxonomy( $taxonomy );
			$terms = get_terms( $taxonomy , array('hide_empty' => 0));
			if ($terms) {
				$array["optgroup_b_{$taxonomy}"] = $the_tax->labels->name;
				foreach($terms as $term) {
						if( (isset($args['category_select']) && in_array($term->term_id, $category_select)) ||  !isset($args['category_select'])){
							$array["" . $term->slug . "#" . $taxonomy . ""] = $term->name;
						}
				}
				$array["optgroup_e_{$taxonomy}"] = $the_tax->labels->name;
			}
		}
		return $array;
	}
	
	/******************************************
	Get post types available for publishing
	******************************************/
	function userpro_publish_types($args){
		$array = array();
		if (isset($args['post_type'])){
			$allowed = explode(',',$args['post_type']);
		} else {
			$allowed = array('post');
		}
		
		$types = get_post_types( array('public' => true) , 'objects');
		foreach($types as $type){
			if (in_array($type->name, $allowed ) ) {
				$array[$type->name] = $type->labels->singular_name;
			}
		}
		return $array;
	}
	
	/* Post editor */
	function userpro_post_editor($i, $class, $args) {
		if (!isset($args['require_content'])) {$args['require_content'] = 1;} // default for content: required
		?>
			<div class="userpro-field userpro-field-editor" data-key="<?php echo $class; ?>">
				<div class="userpro-input">
				<?php
				
				if(isset($_GET['post_id']))
				{
					$post = get_post($_GET['post_id']);
					$content=apply_filters('the_content', $post->post_content);
				}else 
					$content='';	
					if( userpro_get_option('enable_post_editor') == 'y' ){
						wp_editor($content,"$class-$i", array('media_buttons'=>false));
					}
					else{		
					echo "<textarea data-custom-error='".__('Provide some content','userpro')."'data-required='".$args['require_content']."'  name='$class-$i' id='$class-$i' placeholder='".__('Enter content here...','userpro')."'>".$content."</textarea>";
					}
				?>
				</div>
			</div><div class="userpro-clear"></div>
		<?php
	}
	
	/* Publisher fields */
	function userpro_edit_field_misc( $i, $key, $args, $label=null, $help=null, $placeholder=null) {
		global $userpro;
		$res = null;
		$postid=isset($_GET['post_id'])?$_GET['post_id']:'';
		$post_categories = wp_get_post_categories($postid );
		$cats = array();
		
		foreach($post_categories as $c){
			$cat = get_category( $c );
			$cats[] = array( 'name' => $cat->slug);
			
		}		
		
		if(isset($_GET['post_id']))	
		$title=get_the_title($_GET['post_id'] );
		else 
		$title='';	
		
		if (!isset($args['require_featured'])) {$args['require_featured'] = 1;} // default for featured: required
		if (!isset($args['require_title'])) {$args['require_title'] = 1;} // default for title: required
		if (!isset($args['require_category'])) {$args['require_category'] = 1;} // default for category: required
		
		$res .= "<div class='userpro-field' data-key='$key'>";
		
		if ($label) {
			$res .= "<div class='userpro-label'><label for='$key-$i'>".$label."</label></div>";
		}
		
		$res .= "<div class='userpro-input' data-placeholder='".userpro_url . "img/placeholder.jpg'>";
		
			switch($key) {
			
				/* post meta */
				default:
					$metavalue='';
					if(isset($postid))
					{	
						$postmeta=get_post_meta( $postid, $key,true);
						if(! empty( $postmeta ))
						$metavalue=$postmeta;
					}	
					else 
					$metavalue='';
				
					if (!isset($args['require_'.$key])) {$args['require_'.$key] = 0;} // default for custom fields (not required)
					$res .= "<input data-required='".$args['require_'.$key]."' type='text' name='$key-$i' id='$key-$i' value='$metavalue' placeholder='".$placeholder."' />";
					break;

				/* set title */
				case 'post_title':
					$res .= "<input data-custom-error='".__('You must enter a post title','userpro')."' data-required='".$args['require_title']."' type='text' name='$key-$i' id='$key-$i' value='".$title."' placeholder='".$placeholder."' />";
					break;
					
				/* set categories */
				case 'post_categories':
					$options = userpro_publish_categories($args);
					
					$selectclass="multiple='multiple'";					
					if(userpro_get_option('categorie_selection')=="1")
					$selectclass="";	
					$res .= "<select name='".$key.'-'.$i.'[]'."' $selectclass class='chosen-select' data-custom-error='".__('Please choose a category at least','userpro')."' data-required='".$args['require_category']."' data-placeholder='".$placeholder."'>";
					foreach($options as $k=>$v) {
						if (strstr($k, 'optgroup_b')) {
							$res .= "<optgroup label='$v'>";
						} elseif (strstr($k, 'optgroup_e')) {
							$res .= "</optgroup>";
						} else {
							 echo userpro_is_selected($k,$cats);
							
							$res .= "<option value='$k' ".userpro_selected($k,$cats).">$v</option>";
						}
					}
					$res .= "</select>";
					break;
				
				/* set post type */
				case 'post_type':
					$options = userpro_publish_types($args);
					$res .= "<select name='".$key.'-'.$i."' id='".$key.'-'.$i."' class='chosen-select' data-placeholder='".$placeholder."'>";

					$i = 0;
					foreach($options as $k=>$v) {
						$i++;
						if ($i == 1){
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						$res .= "<option value='$k' ".$selected.">$v</option>";
					}
					
					$res .= "</select>";
					break;
				
				/* set featured image */
				case 'post_featured_image':
					$requirevalue = $args['require_featured'];
					if(isset($postid) && has_post_thumbnail( $postid )) {
					$url = wp_get_attachment_url( get_post_thumbnail_id($postid, 'thumbnail') ); 
				     $res .=	"<div class='userpro_post_feature_img'><img src= $url /></div>";
				     $requirevalue = 0;
					}
					
					$value = '<img src="'.userpro_url . 'img/placeholder.jpg" width="" height="" class="modified no_feature" />';
					$res .= "<div class='userpro-pic userpro-pic-nomargin userpro-pic-".$key."' data-remove_text='".__('Remove','userpro')."'>".$value."</div>";
					$res .= "<div class='userpro-pic-upload' data-filetype='picture' data-allowed_extensions='png,gif,jpg,jpeg'>".__('Set Featured Image','userpro')."</div>";
					$res .= "<input data-custom-error='".__('You must upload a featured image','userpro')."' data-required='".$requirevalue."' type='hidden' name='$key-$i' id='$key-$i' value='' />";
					
					break;
					
			}
		
			if ( $help ) {
				$res .= "<div class='userpro-help'>".$help."</div>";
			}
		
		$res .= "<div class='userpro-clear'></div>";
		$res .= "</div>";
		$res .= "</div><div class='userpro-clear'></div>";
		
		return $res;
	}
