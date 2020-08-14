<?php
if( !defined('ABSPATH'))
    exit;

global $post;

$all_survey_answers = get_post_meta( $post->ID, '_ywcpos_all_answers', true );

?>

<div id="ywcpos_pending_order_survey_report-container" style="margin-left: -180px;" data-max_char="200" data-show_more_txt="<?php _e( 'Show more',
    'yith-woocommerce-pending-order-survey' );?>" data-show_less_txt="<?php _e( 'Show less','yith-woocommerce-pending-order-survey' );?>">
    <?php
        if( !empty( $all_survey_answers ) && is_array( $all_survey_answers ) ):
            foreach( $all_survey_answers as $question=>$answers ):?>
               <div class="single_report closed">
                    <h3><?php echo stripcslashes( $question );?><div class="handlediv" title="Click to toggle"></div></h3>
                    <div class="answers_container">
                        <?php
                            foreach( $answers as $answer ):?>
                                <div class="single_answer ywcpos_more"><?php echo stripcslashes( $answer );?></div>
                        <?php endforeach;?>
                    </div>
               </div>
            <?php  endforeach;
        endif;
    ?>
</div>