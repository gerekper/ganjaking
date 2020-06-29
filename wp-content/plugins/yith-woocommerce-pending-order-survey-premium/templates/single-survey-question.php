<?php
if( !defined( 'ABSPATH' ) )
    exit;

$is_required = ( isset( $question['required'] ) && $question['required'] == 'yes' )  ;
$div_validate_req = $is_required ? 'validate-require' : '';
?>


<div class="pending_question_container <?php echo $div_validate_req;?>">
    <?php if( $is_required ):?>
    <span class="dashicons dashicons-star-empty is_req" title="<?php _e('Required','yith-woocommerce-pending-order-survey' );?>"></span>
    <?php endif;?>
    <h3 class="pending_question_name"><?php echo $question['question'];?></h3>
    <?php
        $args = array(
            'answer'    => $question['answers']
        );
        wc_get_template("single-{$question['question_type']}-answer.php", array( 'answer' => $args ), '', YITH_WCPO_SURVEY_TEMPLATE_PATH.'answers/' );
    ?>
</div>
