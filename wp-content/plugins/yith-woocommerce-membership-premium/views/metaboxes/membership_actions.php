<div id="membership-actions" class="panel">
    <select name="yith_wcmbs_membership_actions">
        <option value=""><?php esc_html_e( 'Actions', 'yith-woocommerce-membership' ) ?></option>
        <?php if ( $membership->can_be_paused() ) : ?>
            <option value="paused"><?php esc_html_e( 'Pause Membership', 'yith-woocommerce-membership' ) ?></option>
        <?php endif; ?>
        <?php if ( $membership->can_be_resumed() ) : ?>
            <option value="resumed"><?php esc_html_e( 'Resume Membership', 'yith-woocommerce-membership' ) ?></option>
        <?php endif; ?>
        <?php if ( $membership->can_be_cancelled() ) : ?>
            <option value="cancelled"><?php esc_html_e( 'Cancel Membership', 'yith-woocommerce-membership' ) ?></option>
        <?php endif; ?>
    </select>
</div>

<div class="membership-actions-footer">
    <button type="submit" class="button button-primary" title="<?php esc_html_e( 'Save Membership', 'yith-woocommerce-membership' ) ?>" name="yith_wcmbs_membership_button" value="actions"><?php esc_html_e( 'Save Membership', 'yith-woocommerce-membership' ) ?></button>
</div>

