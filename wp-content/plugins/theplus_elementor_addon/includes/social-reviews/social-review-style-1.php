<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="grid-item <?php echo esc_attr($desktop_class)." ".esc_attr($tablet_class)." ".esc_attr($mobile_class)." ".esc_attr($category_filter)." ".esc_attr($RKey)." ".esc_attr($ReviewClass); ?>">
    <?php include THEPLUS_PATH. "includes/social-reviews/social-review-ob-style.php"; ?>

    <div class="tp-review <?php echo esc_attr($ErrClass); ?>" >
        <?php 
            echo '<div class="tp-SR-header">';
                echo $Profile_HTML;
                echo $UserName_HTML;
                echo $Star_HTML;
            echo '</div>';
            echo $Description_HTML; 
        ?>

        <div class="tp-SR-bottom">
            <?php echo $Logo_HTML; ?>
            <div class="tp-SR-logotext" >
                <span class="tp-newline" ><?php echo esc_html__("Posted On ","theplus"); ?></span>
                <span class="tp-newline"><?php echo esc_html($PlatformName); ?></span>
            </div>
            <?php echo $Time_HTML; ?>
        </div>
    </div>
    <?php echo $Copyid_html; ?>
</div>
