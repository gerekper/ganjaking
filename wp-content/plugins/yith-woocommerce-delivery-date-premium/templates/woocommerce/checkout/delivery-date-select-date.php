<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class_req            = 'yes' === $is_mandatory ? 'validate-required' : '';
$abbr_span            = 'yes' === $is_mandatory ? '<abbr class="required" title="required">*</abbr>' : '';
$carriers             = YITH_Delivery_Date_Processing_Method()->get_carriers( $processing_method );
$carrier_form_field   = apply_filters( 'ywcdd_change_carrier_label', __( 'Carrier', 'yith-woocommerce-delivery-date' ) );
$carrier_form_default = apply_filters( 'ywcdd_change_carrier_default_option_label', __( 'Select Carrier', 'yith-woocommerce-delivery-date' ) );
if ( is_array( $carriers ) ):
    $hide_div = count( $carriers ) > 1 ? '' : 'ywcdd_hide';
    ?>
    <div class="ywcdd_carrier_content <?php echo $hide_div;?>">
        <p class="form-row form-row-wide <?php esc_attr_e( $class_req ); ?>">
            <label for="ywcdd_carrier"><?php echo $carrier_form_field; ?><?php echo $abbr_span; ?></label>
            <select id="ywcdd_carrier" name="ywcdd_carrier">
                <?php if( count( $carriers )> 1 && 'no' == $is_mandatory ):?>
                <option value=""><?php _e( 'Select a carrier', 'yith-woocommerce-delivery-date' );?></option>
                <?php endif;?>
				<?php foreach ( $carriers as $carrier ):
					$carrier_label = get_the_title( $carrier );
					?>
                    <option value="<?php esc_attr_e( $carrier ); ?>"><?php echo $carrier_label; ?></option>
				<?php endforeach; ?>
            </select>
        </p>
    </div>
<?php endif; ?>
    <div class="ywcdd_datepicker_content">
        <p class="form-row form-row-wide <?php esc_attr_e( $class_req ); ?>">
            <label for="ywcdd_datepicker"><?php echo apply_filters( 'ywcdd_change_datepicker_label', __( 'Delivery Date', 'yith-woocommerce-delivery-date' ) ); ?><?php echo $abbr_span; ?></label>
            <input type="text" id="ywcdd_datepicker"
                   name="ywcdd_datepicker" class="input-text"
                   placeholder="<?php _e( 'Select a delivery date', 'yith-woocommerce-delivery-date' ); ?>"/>
            <input type="hidden" id="ywcdd_process_method" name="ywcdd_process_method"
                   value="<?php esc_attr_e( $processing_method ); ?>">
            <input type="hidden" name="ywcdd_is_mandatory" value="<?php esc_attr_e( $is_mandatory ); ?>">
            <input type="hidden" name="ywcdd_delivery_date" class="ywcdd_delivery_date"/>
        </p>
    </div>
<?php
$template_args = array(

	'is_mandatory'  => $is_mandatory
);
$template_path = YITH_DELIVERY_DATE_TEMPLATE_PATH . '/woocommerce/checkout/';
wc_get_template( 'delivery-date-select-timeslot.php', $template_args, $template_path, $template_path );
