<?php
if( !defined( 'ABSPATH' ) )
    exit;

global $post;

$post_id = yith_wpml_get_translated_id( $post->ID, 'yith_wc_surveys' );
$title = $post->post_title;
$title = $title ? $title  : '';
$survey_type = get_post_meta( $post_id, '_yith_survey_visible_in', true );
$survey_handle = get_post_meta( $post_id, '_yith_survey_wc_handle', true );
$is_wc_active = function_exists( 'WC' );
$survey_type = $survey_type ? $survey_type : 'other_page';
$survey_handle = $survey_handle ? $survey_handle : 'after_order_notes';
$is_req = 'no';
$check_out_handle = array(
    'checkout_before_customer_details'  =>  __( 'Before Customer Details','yith-woocommerce-surveys'),
    'checkout_after_customer_details'   =>  __( 'After Customer Details','yith-woocommerce-surveys'),
    'before_checkout_billing_form'      =>  __( 'Before Checkout Billing Form', 'yith-woocommerce-surveys' ),
    'after_checkout_billing_form'       =>  __( 'After Checkout Billing Form', 'yith-woocommerce-surveys' ),
    'before_checkout_shipping_form'     =>  __( 'Before Checkout Shipping Form','yith-woocommerce-surveys'),
    'after_checkout_shipping_form'      =>  __( 'After Checkout Shipping Form', 'yith-woocommerce-surveys' ),
    'before_order_notes'                =>  __( 'Before Order Notes', 'yith-woocommerce-surveys' ),
    'after_order_notes'                 =>  __( 'After Order Notes', 'yith-woocommerce-surveys' ),
);

?>
<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="<?php esc_attr_e( $post_id );?>_title"><?php _e( 'Survey Title', 'yith-woocommerce-surveys' );?></label>
        </th>
        <td class="forminp">
            <input type="text" id="<?php esc_attr_e( $post_id );?>_title" class="yith_survey_title widefat" placeholder="<?php _e( 'Insert a Survey Title', 'yith-woocommerce-surveys' );?>" value="<?php esc_attr_e( $title );?>"  name="yith_survey_title">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="<?php esc_attr_e( $post_id );?>_visible_in"><?php _e( 'Display Survey in', 'yith-woocommerce-surveys' );?></label>
        </th>
        <td>
            <select name="yith_survey_visible_in" id="<?php esc_attr_e( $post_id );?>_visible_in" class="survey_visible_in">
                <option value="checkout" <?php selected( 'checkout', $survey_type ) ;?>><?php _e( 'WooCommerce Checkout','yith-woocommerce-surveys' );?></option>
                <option value="product" <?php selected( 'product', $survey_type ) ;?>><?php _e( 'WooCommerce Product','yith-woocommerce-surveys' );?></option>
                <option value="other_page" <?php selected( 'other_page', $survey_type ) ;?>><?php _e( 'Other Pages','yith-woocommerce-surveys' );?></option>
            </select>
            <span class="description"><?php _e( 'If "Checkout" is selected, surveys will show in checkout page, while if "Other Pages" is selected,
            surveys can be added using shortcode or a widget','yith-woocommerce-surveys' );?></span>
        </td>
    </tr>
    <tr valign="top" class="show_if_survey_is_checkout">
        <th colspan="row" class="titledesc">
            <label for="<?php esc_attr_e( $post_id );?>_handle"><?php _e( 'Position in Checkout', 'yith-woocommerce-surveys' );?></label>
        </th>
        <td>
           <select name="yith_survey_wc_handle" class="survey_handle" id="<?php esc_attr_e( $post_id );?>_handle">
               <?php foreach( $check_out_handle as $key => $value ):?>
               <option value="<?php esc_attr_e( $key );?>" <?php selected( $key, $survey_handle );?> ><?php echo $value;?></option>
               <?php endforeach;?>
           </select>
        </td>
    </tr>
    <tr valign="top" class="show_if_survey_is_checkout">
        <th colspan="row" class="titledesc">
            <label for="<?php esc_attr_e( $post_id );?>_req"><?php _e( 'Required', 'yith-woocommerce-surveys' );?></label>
        </th>
        <td>
            <input type="checkbox" <?php checked( 'yes', $is_req );?> id="<?php esc_attr_e( $post_id );?>_req" class="survey_is_req">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="<?php esc_attr_e( $post_id );?>_answers"><?php _e( 'Survey Answers', 'yith-woocommerce-surveys' );?></label>
        </th>
        <td class="forminp <?php esc_attr_e( $post_id );?>_answers_container">
            <?php
            $args = array(
                'post_parent' => $post_id,
                'orderby'   => 'meta_value',
                'order' => 'ASC',
                'meta_key' => '_yith_survey_position',
                'meta_query' => array(
                    array(
                        'key' => '_yith_answer_visible_in_survey',
                        'value'   => 'yes'
                    )
                ),
                'relation' => 'AND'
            );
            $children_ids = YITH_Surveys_Type()->get_survey_children( $args );
            $loop = 0;
            ?>

            <ul class="suverys_answers">
                <?php
                if( $children_ids ) :
                    foreach( $children_ids as $child_id ):
                        $answer =  get_the_title( $child_id );
                        $position = get_post_meta( $child_id, '_yith_survey_position', true );
                        $params = array(
                            'loop' => $loop,
                            'answer'    => $answer,
                            'post_id'   => $child_id
                        );
                        $params['params'] = $params;
                        wc_get_template( 'admin/surveys_answer.php', $params,'', YITH_WC_SURVEYS_TEMPLATE_PATH );
                        $loop++;
                    endforeach;
                endif;
                ?>
            </ul>

            <div class="surveys_error" style="display: none">
                <span class="surveys_icon dashicons dashicons-no"></span>
                <span class="survey_error_text"><?php _e( 'You have reached the maximum number of elements for this survey, remove o edit existing
                ones to add new ones.',
                        'yith-woocommerce-surveys' );?></span>
            </div>
            <input type="submit" class="button-secondary add_answer" value="<?php _e( 'Add Survey Answer','yith-woocommerce-surveys' );?>">
        </td>
    </tr>
    <input type="hidden" name="save_survey" value="yes">
    </tbody>
</table>