<?php
if( !defined( 'ABSPATH' ) )
    exit;

global $post;

$pending_order_survey = get_post_meta( $post->ID, '_yith_pending_survey_question', true );

?>
<div id="ywcpos_pending_order_survey-container">
    <label for="<?php esc_attr_e( $post->ID );?>_add_question"><?php _e( 'Add new survey questions','yith-woocommerce-pending-order-survey' );
        ?></label>
    <p>
        <input type="submit" class="button-secondary add_survey_question" value="<?php _e( 'Add survey question',
            'yith-woocommerce-pending-order-survey' );?>">
    </p>

    <div class="ywcpos_wrapper">
        <div class="pending_order_surveys_list">
            <?php
            if( ! empty( $pending_order_survey ) ):
                $loop   = 0;
                foreach( $pending_order_survey as $single_survey ):
                    $params = array( 'single_survey'=> $single_survey, 'loop' => $loop );
                    $params['params'] = $params;
                    wc_get_template( 'metaboxes/types/single_pending_survey_question.php', $params, '', YITH_WCPO_SURVEY_TEMPLATE_PATH );
                $loop++;
                endforeach;
            endif;
            ?>
        </div>
    </div>
</div>