<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="<?php echo "social-RB-".esc_attr($Bstyle); ?> tp-review <?php echo esc_attr($BErrClass); ?>" >
    <div class="tp-batch-top">
        <div class="tp-batch-user"><?php echo esc_html($BType); ?></div>
        <div class="tp-batch-images">
            <?php foreach ($BUImage as $value) { 
                echo '<img class="tp-batch-Img" src="'.esc_attr($value).'" />';
            } ?>
        </div>
    </div>
    <div class="tp-batch-rating">
        <div class="tp-batch-start">
            <?php 
                echo esc_html($BRating) . " ";
                for ($i=0; $i<$BRating; $i++) {
                    echo '<i star-rating="'.esc_attr($i).'" class="'.esc_attr($BIcon).' b-star"></i>';
                } 
            ?>
        </div>
        <div class="tp-batch-total">
            <?php  echo esc_html($Btxt1)." ".esc_html($BTotal)." ".esc_html($Btxt2); ?> 
        </div>
    </div>
    <?php if(!empty($BMassage)){?> 
        <div class="tp-batch-Errormsg"><?php echo wp_kses_post($BMassage); ?></div>
    <?php } ?>

</div>
<?php if($BErrClass == "" && !empty($BRecommend)) { ?>
    <div class="tp-batch-recommend" >
        <div class="tp-batch-recommend-text">
            <?php echo esc_html($Blinktxt)." ".esc_html($BUname); ?> 
        </div>
        <div class="tp-batch-button-text">
            <?php 
                    echo '<a href="'.esc_url($BLink).'" class="batch-btn-yes" target="_blank" rel="noopener noreferrer">'.esc_html($BBtnName).'</a>';

                if(!empty($BSButton)) {
                    echo '<a href="#" class="batch-btn-no" target="_blank" rel="noopener noreferrer">'.esc_html($Btn2NO).'</a>';
                } ?>
        </div>
    </div>
<?php } ?>