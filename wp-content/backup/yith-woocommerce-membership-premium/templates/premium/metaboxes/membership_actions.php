<div id="membership-actions" class="panel">
    <select name="yith_wcmbs_membership_actions">
        <option value=""><?php _e( 'Actions', 'yith-woocommerce-membership' ) ?></option>
        <?php if ( $membership->can_be_paused() ) : ?>
            <option value="paused"><?php _e( 'Pause Membership', 'yith-woocommerce-membership' ) ?></option>
        <?php endif; ?>
        <?php if ( $membership->can_be_resumed() ) : ?>
            <option value="resumed"><?php _e( 'Resume Membership', 'yith-woocommerce-membership' ) ?></option>
        <?php endif; ?>
        <?php if ( $membership->can_be_cancelled() ) : ?>
            <option value="cancelled"><?php _e( 'Cancel Membership', 'yith-woocommerce-membership' ) ?></option>
        <?php endif; ?>
    </select>
</div>

<div class="membership-actions-footer">
    <button type="submit" class="button button-primary" title="<?php _e( 'Save Membership', 'yith-woocommerce-membership' ) ?>" name="yith_wcmbs_membership_button" value="actions"><?php _e( 'Save Membership', 'yith-woocommerce-membership' ) ?></button>
</div>

