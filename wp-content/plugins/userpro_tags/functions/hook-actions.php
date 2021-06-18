<?php

add_action( 'init','create_topics_nonhierarchical_taxonomy',0);
function create_topics_nonhierarchical_taxonomy() {


// Labels part for the GUI

 $labels = array(
			'name'                       => __( 'UserPro Tags', 'userpro-tags' ),
			'singular_name'              => __( 'UserPro Tag', 'userpro-tags' ),
			'search_items'               => __( 'Search User Tags','userpro-tags' ),
			'popular_items'              => __( 'Popular User Tags','userpro-tags' ),
			'all_items'                  => __( 'All User Tags','userpro-tags' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit User Tag','userpro-tags' ),
			'update_item'                => __( 'Update User Tag','userpro-tags' ),
			'add_new_item'               => __( 'Add New User Tag','userpro-tags' ),
			'new_item_name'              => __( 'New User Tag Name','userpro-tags' ),
			'separate_items_with_commas' => __( 'Separate user tags with commas','userpro-tags' ),
			'add_or_remove_items'        => __( 'Add or remove user tags','userpro-tags' ),
			'choose_from_most_used'      => __( 'Choose from the most used user tags','userpro-tags' ),
			'not_found'                  => __( 'No user tags found.','userpro-tags' ),
			'menu_name'                  => __( 'Userpro Tags','userpro-tags' ),
		);

// Now register the non-hierarchical taxonomy like tag

  register_taxonomy('userpro_tags','post',array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'show_in_menu'      => false,	
    'update_count_callback' => 'user_tag_update_count_callback',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'topic' ),
  ));
}

function register_taxonomy_tag()
		
	{$terms = get_terms( 'userpro_tags', array( 'fields' => 'ids', 'hide_empty' => false ) );	
		$tags = get_terms( 'userpro_tags', array( 'hide_empty' => 0 ) );

	}

add_action('init','register_taxonomy_tag',9);

add_action('userpro_after_profile_img','add_users_tag',1,10);
function add_users_tag($user_id)
{
	global $userpro;
	$tagsdata = get_terms( 'userpro_tags', array( 'hide_empty' => 0 ) );
	

  $tags= userpro_profile_data('tags', $user_id);
	echo '<span class="userpro-tags-span">';
 if(is_array($tags))
  foreach ($tags as $tag)
  {
	
		$des='';
		foreach($tagsdata as $data)
		{
			if($data->name==$tag)
			$des=$data->description;
			

		}
	echo '<a target="_blank" title="'.$des.'" href="'.userpro_page_link('directory_page').'?tags='.$tag.'" class="userpro_tags">'.$tag.'</a>';
  }			
	echo '</span>';
}
function userpro_page_link($template){
		$pages = get_option('userpro_pages');
		if ($template=='view') $template = 'profile';
		if (isset($pages[$template])){
			return get_page_link( $pages[$template] );
		}
	}


add_action('wp_head','userpro_add_tags_styles', 99999);
	function userpro_add_tags_styles() {
		wp_register_style('userpro_tag', userpro_tags_url . 'css/style.css');
		wp_enqueue_style('userpro_tag');

}

add_action('userpro_modify_search_filters', 'userpro_tags_search_filter');
function userpro_tags_search_filter($args){

	if(isset($args['tags_filter']) && $args['tags_filter'] == 1 ){
		$tagsdata = get_terms( 'userpro_tags', array( 'hide_empty' => 0 ) );
		?>
		<div class="tags-search-filter">
			<select name="emd-tags" id="emd-tags" class="chosen-select">
			<option value="" disabled selected><?php echo __('Tags Filter','userpro-tags');?></option>
			<option value="all" <?php if(isset($_GET['emd-tags']) && $_GET['emd-tags'] == 'all'){ echo "selected=selected"; }?>><?php echo __('All','userpro-tags');?></option>
			<?php
				foreach($tagsdata as $k => $v){
					?>
					<option value="<?php echo $v->slug; ?>" <?php if(isset($_GET['emd-tags']) && $_GET['emd-tags'] == $v->slug){ echo "selected=selected";}?>><?php echo ucfirst($v->name); ?></option>
					<?php 						
				}
				?>
			</select>
		</div>
		<?php
	}
}