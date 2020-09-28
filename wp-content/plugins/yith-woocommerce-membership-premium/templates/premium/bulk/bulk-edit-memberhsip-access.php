<fieldset class="inline-edit-col-center">
    <div class="inline-edit-col">
        <span class="title inline-edit-plans-label"><?php _e( 'Set Access', 'yith-woocommerce-membership' ) ?></span>
        <ul class="plans-checklist cat-checklist product_cat-checklist">
            <?php

            $plans = YITH_WCMBS_Manager()->plans;
            if ( !empty( $plans ) ) {
                foreach ( $plans as $plan ) {
                    echo "<li id='plan-{$plan->ID}'><label class='selectit'><input value='{$plan->ID}'
                                                                                   name='_yith_wcmbs_restrict_access_plan[]'
                                                                                   id='in-plan-{$plan->ID}'
                                                                                   type='checkbox'>{$plan->post_title}</label>";
                }
            }
            ?>
        </ul>
    </div>
</fieldset>