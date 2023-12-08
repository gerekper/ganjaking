<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="post-title"> <?php 
    if($tlContentFrom == 'tlrepeater'){ 
        echo esc_html($testiLabel);
    }else{
        echo esc_html(get_the_title());
    } ?> 
</div>