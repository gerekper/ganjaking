<?php
/**
 * User preferences metabox template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Terms & Condtions Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCTC' ) ) {
	exit;
} // Exit if accessed directly

?>

<?php if( empty( $terms_type ) ): ?>

    <p class="description">
        <?php _e( 'No data registered for this order', 'yith-woocommerce-terms-conditions-premium' ) ?>
    </p>

<?php else: ?>

    <div class="user-preferences">
        <h4><?php _e( 'User agreement', 'yith-woocommerce-terms-conditions-premium' ) ?></h4>
        <p class="description">
            <?php _e( 'In this section you\'ll find data about customer consent to Mailchimp subscription', 'yith-woocommerce-terms-conditions-premium' ) ?>
        </p>
        <p class="option">
            <span class="option-label"><?php _e( 'Type of agreement:', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
            <span class="option-value"><?php echo $hide_checkboxes == 'no' ? __( 'explicit', 'yith-woocommerce-terms-conditions-premium' ) : __( 'implicit', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
        </p>

        <?php if( in_array( $terms_type, array( 'terms', 'both' ) ) ) : ?>
            <p class="option">
                <span class="option-label"><?php _e( 'Terms & Conditions accepted:', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
                <span class="option-value"><?php echo $hide_checkboxes == 'yes' || $terms_accepted == 'yes' ? __( 'yes', 'yith-woocommerce-terms-conditions-premium' ) : __( 'no', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
            </p>
        <?php endif; ?>

        <?php if( in_array( $terms_type, array( 'privacy', 'both' ) ) ) : ?>
            <p class="option">
                <span class="option-label"><?php _e( 'Privacy Policy accepted:', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
                <span class="option-value"><?php echo $hide_checkboxes == 'yes' || $privacy_accepted == 'yes' ? __( 'yes', 'yith-woocommerce-terms-conditions-premium' ) : __( 'no', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
            </p>
        <?php endif; ?>
    </div>

    <div class="tos-versions">
        <h4><?php _e( 'Terms / Privacy versions', 'yith-woocommerce-terms-conditions-premium' ) ?></h4>
        <p class="description">
            <?php _e( 'In this section you\'ll find last update for Terms & Conditions and Privacy Policy, at the time the customer accepted them', 'yith-woocommerce-terms-conditions-premium' ) ?>
        </p>

        <?php if( in_array( $terms_type, array( 'terms', 'both' ) ) ) : ?>
            <p class="option">
                <span class="option-label"><?php _e( 'T&C last update:', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
                <span class="option-value"><?php echo date_i18n( wc_date_format(), strtotime( $last_terms_update ) ) ?></span>
            </p>
        <?php endif; ?>

        <?php if( in_array( $terms_type, array( 'privacy', 'both' ) ) ) : ?>
            <p class="option">
                <span class="option-label"><?php _e( 'Privacy last update:', 'yith-woocommerce-terms-conditions-premium' ) ?></span>
                <span class="option-value"><?php echo date_i18n( wc_date_format(), strtotime( $last_privacy_update ) ) ?></span>
            </p>
        <?php endif; ?>
    </div>

<?php endif;?>