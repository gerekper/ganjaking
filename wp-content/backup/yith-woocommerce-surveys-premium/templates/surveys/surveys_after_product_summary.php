<?php
if( !defined( 'ABSPATH' ) )
    exit;

global $product;

 $title = apply_filters( 'yith_surveys_section_title', __( 'Surveys', 'yith-woocommerce-surveys' ) );
wp_enqueue_style( 'surveys_style' );
wp_enqueue_script( 'surveys_script_frontend' );
?>

<div class="survey_after_product_summary survey_hide">
    <h2><?php echo $title;?></h2>
    <div class="surveys_list">
        <?php
        $i =0;
        foreach( $all_surveys as $survey ){

                $is_user_in_list = YITH_WC_Surveys_Utility::is_user_survey_in_list( $survey );
                $hide_after_answer = get_option( 'ywcsur_hide_after_answer' ) == 'yes';
            if( !$is_user_in_list ) {
                $param = array('survey_id' => $survey, 'select_class' => $select_class );
                wc_get_template('surveys/single-survey-form.php', $param, '', YITH_WC_SURVEYS_TEMPLATE_PATH);
                $i++;
            }
            else if( !$hide_after_answer ){
                $param = array('survey_id' => $survey );
                wc_get_template('surveys/single-survey-answered.php', $param, '', YITH_WC_SURVEYS_TEMPLATE_PATH);
            }
        }
      ?>
       <?php if( $i>0 ) :?>
        <div class="yith_surveys_button">
            <a href="#" class="<?php echo $button_class;?> button"><?php _e( 'Send Answer','yith-woocommerce-surveys' );?></a>
        </div>
        <?php endif;?>
    </div>
</div>