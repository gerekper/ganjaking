<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID(); ?>
<div class="post-category-list style-2">	
	<?php $categories = get_the_category($postid); 
	foreach ( $categories as $category ) {
		echo '<span><a href="'.get_category_link($category->cat_ID).'" class="bss-cat-link">'.$category->name.'</a></span>';
	}
	?>
</div>

