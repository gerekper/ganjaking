<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tp-sf-feed">

    <?php 
        include THEPLUS_PATH. "includes/social-feed/social-feed-ob-style.php";
            echo $Header_html;
        
        if(!empty($Massage)){
            echo $Massage_html;
        }
        
        if(!empty($Description) && empty($DescripBTM)){ 
            include THEPLUS_PATH. "includes/social-feed/feed-Description.php"; 
        }

        if($MediaFilter == 'default' || $MediaFilter == 'ompost' ){
            include THEPLUS_PATH. "includes/social-feed/fancybox-feed.php";
        }

        if(!empty($Description) && !empty($DescripBTM)){ 
            include THEPLUS_PATH. "includes/social-feed/feed-Description.php"; 
        }
            include THEPLUS_PATH. "includes/social-feed/feed-footer.php";   
    ?>

</div>
<?php 
    echo $Copyid_html;