<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tp-sf-feed <?php echo esc_attr($ErrorClass); ?>">
    
    <?php 
        include THEPLUS_PATH. "includes/social-feed/social-feed-ob-style.php";
    
        if($MediaFilter == 'default' || $MediaFilter == 'ompost'){
            include THEPLUS_PATH. "includes/social-feed/fancybox-feed.php";
        }

        if(!empty($Massage)){
            echo $Massage_html;
        }
        
        if(!empty($Description)){ 
            include THEPLUS_PATH. "includes/social-feed/feed-Description.php";  
        }       
            echo $Header_html;

            include THEPLUS_PATH. "includes/social-feed/feed-footer.php"; 
    ?>
</div>
<?php 
    echo $Copyid_html;
