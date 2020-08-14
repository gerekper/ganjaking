<?php
if( !defined( 'ABSPATH' ) )
    exit;

global $post;

$order_id = $post->ID;

$all_surveys = get_post_meta( $order_id, '_yith_order_survey_voting', true  );


if( $all_surveys ):?>

    <div class="yith_survey_answer_container">
        <ul class="survey_answer_contents">
            <?php
                foreach( $all_surveys as $survey ):?>
                    <li class="survey_name"><?php echo $survey['survey_title'];?></li>
                    <li class="answer_name"><?php echo $survey['answer_title'];?></li>

                <?php endforeach;?>
        </ul>
    </div>

<?php endif;