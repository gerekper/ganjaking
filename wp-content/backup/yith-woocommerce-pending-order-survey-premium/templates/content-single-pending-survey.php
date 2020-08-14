<?php

if (!defined('ABSPATH'))
    exit;

global $post;

    do_action( 'pending_order_survey_before_single_survey' );
 ?>
<div class="survey-main-content">
    <div id="pending-survey-<?php the_ID() ;?>" <?php post_class();?> >
        <h1 class="pending_survey_title"><?php the_title();?></h1>
        <form class="pending_survey_form">
            <input type="hidden" class="survey_id" value="<?php the_ID();?>">
            <div class="pending_question_list">
                <?php
                   $questions = get_post_meta( $post->ID, '_yith_pending_survey_question', true );

                    foreach( $questions as $question )
                        wc_get_template( 'single-survey-question.php',  array('question' => $question ), '',YITH_WCPO_SURVEY_TEMPLATE_PATH );
                ?>
            </div>
            <button type="submit" class="ywcpos_sendsurvey"><?php _e('Send Survey','yith-woocommerce-pending-order-survey' );?></button>
        </form>
    </div><!-- #pending-survey-<?php the_ID();?>-->
</div>
    <?php
        do_action( 'pending_order_survey_after_single_survey' );
    ?>


