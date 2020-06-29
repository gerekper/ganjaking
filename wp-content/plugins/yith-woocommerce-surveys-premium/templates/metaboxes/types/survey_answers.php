<?php
if(!defined( 'ABSPATH' ) )
    exit;

extract( $args );

global $post;
$suvery_id = yith_wpml_get_translated_id( $post->ID, 'yith_wc_surveys' );

extract( $args );
?>

<div id="<?php echo $id ?>-container" <?php if ( isset($deps) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <label for="<?php echo $id ?>"><?php echo $label ?></label>
    <div class="survey_answers_wrapper" style="width: 600px;">
        <?php
        $params = array(
            'post_parent' => $suvery_id,
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
       
        $children_ids = YITH_Surveys_Type()->get_survey_children( $params );
      
        $loop = 0;
        ?>

        <ul class="suverys_answers">
            <?php
            if( $children_ids ) :
                foreach( $children_ids as $child_id ):
                    $answer =  get_the_title( $child_id );
                    $answer = $answer ? $answer : '';
                    $position = get_post_meta( $child_id, '_yith_survey_position', true );
                    $params = array(
                        'loop' => $loop,
                        'answer'    => $answer,
                        'post_id'   => $child_id,
                    );
                    $params['params'] = $params;
                    wc_get_template( 'metaboxes/types/surveys_answer.php', $params,'', YITH_WC_SURVEYS_TEMPLATE_PATH );
                    $loop++;
                endforeach;
            endif;
            ?>
        </ul>

        <div class="surveys_error" style="display: none">
            <span class="surveys_icon dashicons dashicons-no"></span>
            <span class="survey_error_text"><?php _e( 'You have reached the maximum number of elements for this survey, remove o edit existing
                ones to add new ones.', 'yith-woocommerce-surveys' );?></span>
        </div>
        <input type="submit" class="button-secondary add_answer" value="<?php _e( 'Add Survey Answer','yith-woocommerce-surveys' );?>">
    </div>
</div>
