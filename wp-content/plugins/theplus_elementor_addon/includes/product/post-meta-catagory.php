<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
$terms = get_the_terms( $post->ID, 'product_cat' );
foreach ($terms as $term) {
    $product_cat_name = $term->name;
	echo '<span class="post-catagory">'.$product_cat_name.'</span>';
    break;
}

?>