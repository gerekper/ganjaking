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

$type = get_post_meta( $survey_id, '_yith_survey_visible_in', true );

$is_required = get_post_meta( $survey_id, '_yith_survey_required', true );
$is_required = $is_required ? 'yes' : 'no';

$class_req = 'yes' == $is_required ? 'validate-required' : '';
$abbr_span = 'yes' == $is_required ? '<abbr class="required" title="required">*</abbr>' : '';
$survey_title = get_the_title( $survey_id );
$survey_title = $survey_title ? $survey_title : '';
if( $survey_answers ):
    wp_enqueue_style( 'surveys_style' );
    wp_enqueue_script( 'surveys_script_frontend' );
?>

<p class="form-row form-row-wide <?php echo $class_req;?>" id="survey_<?php esc_attr_e( $survey_id );?>">
    <label for="title_survey_<?php esc_attr_e( $survey_id );?>"><?php echo $survey_title;?> <?php echo $abbr_span;?></label>
    <select class="ywc_sur_answers select" name="ywc_sur_answers[]">
        <option value="" ><?php _e( 'Select an option','yith-woocommerce-surveys' );?></option>
        <?php foreach ( $survey_answers as $answer ):
            $title = get_the_title( $answer );
            $title = $title ? $title : '';
            ;?>
                <option value="<?php esc_attr_e( $answer );?>"><?php echo $title;?></option>
        <?php endforeach;?>
    </select>
    <input type="hidden" name="ywc_sur_ids[]" value="<?php echo $survey_id;?>">
</p>
<?php endif;?>