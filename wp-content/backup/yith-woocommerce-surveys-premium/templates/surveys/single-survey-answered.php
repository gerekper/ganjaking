<?php
if( !defined( 'ABSPATH' ) )
    exit;

$hide_after_anserw = get_option( 'ywcsur_hide_after_answer' ) == 'yes';

if( $hide_after_anserw )
    return;

$survey_question = get_the_title( $survey_id );
$survey_answer  = YITH_WC_Surveys_Utility::get_user_answer_by_survey_id( $survey_id );


?>
<div class="survey_answered">
    <h4 class="question"><?php echo $survey_question;?></h4>
    <span class="answer"><?php echo $survey_answer;?></span>
</div>
