<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID(); ?>
<div class="post-category-list style-2">	
	<?php
	if(!empty($post_type) && $post_type == 'product' && !empty($texonomy_category ) && $texonomy_category =='product_tag'){
		$categories = get_terms( $texonomy_category  );
	}else if(!empty($post_type) && $post_type != 'post' && !empty($texonomy_category ) && $texonomy_category  != 'category'){
		$categories = get_the_terms($postid ,$texonomy_category  ); 
	}else{
		$categories = get_the_category($postid);
	}
	
	foreach ( $categories as $category ) {	
		if(!empty($post_type) && $post_type == 'product' && !empty($texonomy_category) && $texonomy_category=='product_tag'){			
			echo '<span><a href="'.get_category_link($category->term_id).'">'.$category->name.'</a></span>';
		}else if(!empty($post_type) && $post_type != 'post' && !empty($texonomy_category) && $texonomy_category != 'category'){	
			echo '<span><a href="'.get_category_link($category->term_id).'">'.$category->name.'</a></span>';
		}else{
			echo '<span><a href="'.get_category_link($category->cat_ID).'">'.$category->name.'</a></span>';
		}		
	}
	?>
</div>