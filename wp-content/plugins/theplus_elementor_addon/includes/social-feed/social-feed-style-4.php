<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tp-sf-feed">
    <div class="tp-sf-contant-img" style="background-image: url('<?php echo esc_url($ImageURL); ?>');">
        <?php
            echo '<div class="tp-sf-contant">';
            include THEPLUS_PATH. "includes/social-feed/social-feed-ob-style.php";

            if(!empty($Massage)){
                echo $Massage_html;
            }
            if(!empty($Description)){ 
                include THEPLUS_PATH. "includes/social-feed/feed-Description.php"; 
            }
                echo $Header_html;
                include THEPLUS_PATH. "includes/social-feed/feed-footer.php"; 
            echo '</div>';

            include THEPLUS_PATH. "includes/social-feed/fancybox-feed.php"; 
        ?>
    </div>
</div>
<?php 
    echo $Copyid_html;