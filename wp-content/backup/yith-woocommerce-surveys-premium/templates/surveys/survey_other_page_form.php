<?php
if( !defined( 'ABSPATH' ) )
    exit;

wp_enqueue_style( 'surveys_style' );
wp_enqueue_script( 'surveys_script_frontend' );

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
$is_user_in_list = YITH_WC_Surveys_Utility::is_user_survey_in_list( $survey_id );
?>
<div class="yith_surveys_other_container">
    <?php if( !$is_user_in_list ):?>
    <div class="yith_single_surveys_container">
        <div class="yith_surveys_title"><h4><?php echo $survey_title;?></h4></div>
            <select name="yith_surveys_answers" class="yith_surveys_answers" id="yith_surveys_answers-<?php echo $survey_id;?>">
                <option value=""><?php _e( 'Select an option', 'yith-woocommerce-surveys' );?></option>
                <?php foreach ( $survey_answers as $answer ) :
                    $title = get_the_title( $answer );
                    $title = $title ? $title : '';
                    ;?>
                    <option value="<?php esc_attr_e( $answer );?>"><?php echo $title;?></option>
                <?php endforeach;?>
            </select>
       <div class="yith_surveys_button">
            <a href="#" class="yith_send_single_answer button alt"><?php _e('Send Answer','yith-woocommerce-surveys' );?></a>
       </div>
    </div>
    <?php else:
        $param = array('survey_id' => $survey_id );
        wc_get_template('surveys/single-survey-answered.php', $param, '', YITH_WC_SURVEYS_TEMPLATE_PATH);
        ?>

    <?php endif;?>
</div>
