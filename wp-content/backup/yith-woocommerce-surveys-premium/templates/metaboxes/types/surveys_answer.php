<?php
if( !defined( 'ABSPATH' ) )
    exit;

$defaults = array(
    'loop' => '',
    'answer' => '',
    'post_id'   => -1,
);

$defaults = wp_parse_args( $params, $defaults );

extract( $defaults );
?>

<li class="surveys_answer" rel="<?php echo $loop;?>">
    <span class="survey_handle"></span>
    <span class="survey_answer_label"><label for="yith_survey_answer_<?php echo $loop;?>"><?php _e( 'Answer', 'yith-woocommerce-surveys' ) ;?></label></span>
    <input type="text" id="yith_survey_answer_<?php echo $loop;?>" class="yith_survey_answer" name="yith_survey_answers[]" placeholder="<?php _e( 'Answer', 'yith-woocommerce-surveys' );?>" value="<?php esc_attr_e( $answer );?>">
    <input type="hidden" class="yith_survey_answer_post_id" name="yith_survey_answer_post_ids[]" value="<?php esc_attr_e( $post_id );?>">
    <input type="submit" class="button-secondary remove_answer" value="<?php _e( 'Remove', 'yith-woocommerce-surveys' );?>">
</li>