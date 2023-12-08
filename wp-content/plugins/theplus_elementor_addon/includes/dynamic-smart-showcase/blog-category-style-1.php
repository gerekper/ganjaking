<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID(); ?>
<div class="post-category-list style-1">	
	<?php $categories = get_the_category($postid); 
	$i=0;
	foreach ( $categories as $category ) {
		if($i==0){
			echo '<span><a href="'.get_category_link($category->cat_ID).'" class="bss-cat-link">'.$category->name.'</a></span>';
		}
		$i++;
	}
	?>
</div>

