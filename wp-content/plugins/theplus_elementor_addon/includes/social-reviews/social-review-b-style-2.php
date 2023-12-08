<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="<?php echo "social-RB-".esc_attr($Bstyle); ?> tp-review <?php echo esc_attr($BErrClass); ?>" >   
    <div class="tp-batch-top">
        <?php if(!empty($BIconHidden2)){ ?>
            <img class="tp-SR-logo" src="<?php echo esc_url($BLogo); ?>" />
        <?php } ?>
        <div class="tp-batch-contant">
            <div class="tp-batch-user"><?php echo esc_html($BUname); ?></div>
            <div class="tp-batch-start">
                <?php 
                    echo esc_html($BRating) . " ";
                    for ($i=0; $i<$BRating; $i++) {
                        echo '<i star-rating="'.esc_attr($i).'" class="'.esc_attr($BIcon).' b-star"></i>';
                    }
                ?>
            </div> 
            <div class="tp-batch-total">
                <?php echo esc_html($Btxt1)." ".esc_html($BTotal)." ".esc_html($Btxt2); ?> 
            </div>
        </div>
    </div>
    <?php if(!empty($BMassage)){
            echo esc_html($BType)." ".esc_html__(" - ","theplus")." ".wp_kses_post($BMassage);
    } ?>    
</div>