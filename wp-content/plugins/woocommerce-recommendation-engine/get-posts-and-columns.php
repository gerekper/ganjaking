<?php
// Thie file is used to set the number of posts and the number of columns that should be displayed when viewing recommended products. 
// What we do is hook into the woocommerce_locate_template filter in woocommerce_recommender_get_posts_and_columns and return this file.  
// The globals can then be used later when rendering recommender based recommended products. 
// It's complicated, but a necessary evil to make this work in all theme configurations. 


global $related_posts_per_page, $related_columns;
$related_posts_per_page = $posts_per_page;
$related_columns = $columns;