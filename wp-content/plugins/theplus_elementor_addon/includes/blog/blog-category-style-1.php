<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID(); ?>
<div class="post-category-list style-1">	
	<?php $categories = get_the_category($postid); 
	if(!empty($dpc_all) && $dpc_all=='yes'){
		$i=1;
	}else{
		$i=0;
	}
	foreach ( $categories as $category ) {
		if(!empty($dpc_all) && $dpc_all=='yes'){			
				echo '<span><a href="'.get_category_link($category->cat_ID).'">'.$category->name.'</a></span>';
		}else{
			if($i==0){
				echo '<span><a href="'.get_category_link($category->cat_ID).'">'.$category->name.'</a></span>';
			}
			$i++;
		}		
	}
	?>
</div>

