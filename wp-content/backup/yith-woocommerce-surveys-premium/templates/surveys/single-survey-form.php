<?php
if( !defined( 'ABSPATH' ) )
    exit;

$args = array(
    'post_parent' => $survey_id,
    'meta_key' => '_yith_survey_position',
    'orderby'   => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => '_yith_answer_visible_in_survey',
            'value'   => 'yes'
        )
    ),
);
$survey_answers  = YITH_Surveys_Type()->get_survey_children( $args );
$survey_title = get_the_title( $survey_id );
$survey_title = $survey_title ? $survey_title : '';

$hide_after_anserw = get_option( 'ywcsur_hide_after_answer' ) == 'yes';
$is_user_in_list = YITH_WC_Surveys_Utility::is_user_survey_in_list( $survey_id );

$survey_already_answered = $is_user_in_list ? 'yes' : 'no';
$show_class= $is_user_in_list ? 'hide_survey' :'';
?>

<div class="yith_surveys_container <?php echo $show_class;?>" data-survey_answered="<?php echo $survey_already_answered;?>">
    <div class="yith_single_surveys_container">
        <div class="yith_surveys_title"><h4><?php echo $survey_title;?></h4></div>
        <?php if( !$is_user_in_list ):?>
        <select name="yith_surveys_answers" class="<?php echo $select_class;?>" id="yith_surveys_answers-<?php echo $survey_id;?>">
            <option value="" selected><?php _e( 'Select an option', 'yith-woocommerce-surveys' );?></option>
            <?php foreach ( $survey_answers as $answer ) :
                $title = get_the_title( $answer );
                $title = $title ? $title : '';
                ;?>
                <option value="<?php esc_attr_e( $answer );?>"><?php echo $title;?></option>
            <?php endforeach;?>
        </select>
        <?php else:?>
        <div class="yith_survey_answer"><?php echo YITH_WC_Surveys_Utility::get_user_answer_by_survey_id( $survey_id );?></div>
        <?php endif;?>
    </div>
</div>
