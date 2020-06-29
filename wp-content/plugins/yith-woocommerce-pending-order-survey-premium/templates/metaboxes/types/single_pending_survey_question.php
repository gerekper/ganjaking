<?php
if( !defined( 'ABSPATH' ) )
    exit;

$defaults = array(
  'loop' => 0,
  'single_survey'   => array()
);

$defaults = wp_parse_args( $params, $defaults );

extract( $defaults );

$question = isset( $single_survey['question'] ) ? $single_survey['question'] : '';
$required = isset( $single_survey['required'] ) ? $single_survey['required'] : 'no';
$position = isset( $single_survey['position'] ) ? $single_survey['position'] : $loop ;
?>
<div class="survey_question closed"  data-pos="<?php esc_attr_e( $loop );?>">
    <h3>
    	<span class="survey_qst"><?php echo $question;?></span>
        <button type="button" class="remove_question button"><?php _e('Remove', 'yith-woocommerce-pending-order-survey') ?></button>
        <div class="handlediv" title="Click to toggle"></div>

    </h3>
    <table class="widefat ywcpos_survey_content">
        <tbody>
        <tr valign="top">
            <th scope="row">
                <label for="ywcpos_question_txt_<?php esc_attr_e( $loop );?>"><?php _e( 'Survey question', 'yith-woocommerce-pending-order-survey'
                    );?></label>
            </th>
            <td class="forminp forminp-text">
                <input type="text" class="widefat qst_txt" name="yit_metaboxes[_yith_pending_survey_question][<?php echo $loop;?>][question]"
                       id="ywcpos_question_txt_<?php esc_attr_e( $loop );?>" value="<?php echo $question ;?>" placeholder="<?php _e( 'Enter your text here','yith-woocommerce-pending-order-survey' );?>">
                <input type="hidden" name="yit_metaboxes[_yith_pending_survey_question][<?php echo $loop;?>][question_type]" value="textarea">
                <input type="hidden" name="yit_metaboxes[_yith_pending_survey_question][<?php echo $loop;?>][answers]" value="">
                <input type="hidden" class="survey_order" name="yit_metaboxes[_yith_pending_survey_question][<?php echo $loop;?>][position]" value="<?php echo $position;?>">
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="ywcpos_question_required_<?php esc_attr_e( $loop );?>"><?php _e( 'Required','yith-woocommerce-pending-order-survey' )?></label>
            </th>
            <td class="forminp forminp-checkbox">
                <input type="checkbox" style="min-width:16px;width: 16px;" name="yit_metaboxes[_yith_pending_survey_question][<?php echo $loop;?>][required]" id="ywcpos_question_required_<?php esc_attr_e( $loop );?>" value="yes" <?php checked( 'yes', $required );?> />
            </td>
        </tr>

        </tbody>
    </table>
</div>
