<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class_req = 'yes' === $is_mandatory ? 'validate-required' : '';
$abbr_span = 'yes' === $is_mandatory ? '<abbr class="required" title="required">*</abbr>' : '';

?>

<div class="ywcdd_timeslot_content ywcdd_hide">
    <p class="form-row form-row-wide form-row-slot">
        <label for="ywcdd_timeslot"><?php echo apply_filters( 'ywcdd_change_timeslot_label', __( 'Time Slot', 'yith-woocommerce-delivery-date' ) ); ?><?php echo $abbr_span; ?></label>

        <select id="ywcdd_timeslot" name="ywcdd_timeslot">
            <?php if( 'no' == $is_mandatory ):?>
            <option value=""><?php _e( 'Select time slot', 'yith-woocommerce-delivery-date' ); ?></option>
            <?php endif;?>
        </select>
        <input type="hidden" name="ywcdd_timeslot_av" class="ywcdd_timeslot_av"/>
    </p>
</div>
