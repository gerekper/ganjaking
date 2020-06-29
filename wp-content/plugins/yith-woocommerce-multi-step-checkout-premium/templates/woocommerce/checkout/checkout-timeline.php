<?php
$i = 0;
$with_icon = 'icon' == get_option( 'yith_wcms_timeline_step_count_type' ) && 'text' != $style ? true : false;
$enable_checkout_login_reminder = 'yes' == get_option( 'woocommerce_enable_checkout_login_reminder', 'yes' ) ? true : false;
$image_class = apply_filters( 'yith_wcms_timeline_icon_class', '' );
$show_login_step = ! $is_user_logged_in && $enable_checkout_login_reminder;
?>
<ul id="checkout_timeline" class="woocommerce_checkout_timeline <?php echo $display ?> <?php echo $style ?> <?php echo $remove_shipping_step ? 'shipping_removed' : ''; ?>">
    <?php if( $show_login_step ) : ?>
        <li id="timeline-login" data-step="login" class="timeline login <?php echo ! $is_user_logged_in ? 'active' : '';?>" >
            <div class="timeline-wrapper">
                 <span class="timeline-step <?php echo $with_icon ? 'with-icon' : '' ?>">
                <?php if( $with_icon ) : ?>
                    <img src="<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'login' ); ?>" alt="<?php echo $labels['login'] ?>" class="<?php echo $image_class; ?>" width="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" height="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" />
                <?php else : ?>
                    <?php echo $i= $i + 1 ?>
                <?php endif; ?>
            </span>
                <span class="timeline-label"><?php echo $labels['login'] ?></span>
            </div>
        </li>
    <?php endif; ?>
    <li id="timeline-billing" data-step="billing" class="timeline billing <?php echo ! $show_login_step ? 'active' : '';?>" >
        <div class="timeline-wrapper">
            <span class="timeline-step <?php echo $with_icon ? 'with-icon' : '' ?>">
                <?php if( $with_icon ) : ?>
                    <img src="<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'billing' ); ?>" alt="<?php echo $labels['billing'] ?>" class="<?php echo $image_class; ?>" width="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" height="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" />
                <?php else : ?>
                    <?php echo $i= $i + 1 ?>
                <?php endif; ?>
            </span>
            <span class="timeline-label"><?php echo $labels['billing'] ?></span>
        </div>
    </li>
    <?php if( ! $remove_shipping_step ) : ?>
    <li id="timeline-shipping" data-step="shipping" class="timeline shipping" >
        <div class="timeline-wrapper">
            <span class="timeline-step <?php echo $with_icon ? 'with-icon' : '' ?>">
               <?php if( $with_icon ) : ?>
                    <img src="<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'shipping' ); ?>" alt="<?php echo $labels['shipping'] ?>" class="<?php echo $image_class; ?>" width="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" height="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" />
                <?php else : ?>
                    <?php echo $i= $i + 1 ?>
                <?php endif; ?>
            </span>
            <span class="timeline-label"><?php echo $labels['shipping'] ?></span>
        </div>
    </li>
    <?php endif; ?>
    <li id="timeline-order" data-step="order" class="timeline order" >
        <div class="timeline-wrapper">
            <span class="timeline-step <?php echo $with_icon ? 'with-icon' : '' ?>">
               <?php if( $with_icon ) : ?>
                    <img src="<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'order' ); ?>" alt="<?php echo $labels['order'] ?>" class="<?php echo $image_class; ?>" width="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" height="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" />
                <?php else : ?>
                    <?php echo $i= $i + 1 ?>
                <?php endif; ?>
            </span>
            <span class="timeline-label"><?php echo $labels['order'] ?></span>
        </div>
    </li>
    <li id="timeline-payment" data-step="payment" class="timeline payment" >
        <div class="timeline-wrapper">
             <span class="timeline-step <?php echo $with_icon ? 'with-icon' : '' ?>">
               <?php if( $with_icon ) : ?>
                    <img src="<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'payment' ); ?>" alt="<?php echo $labels['payment'] ?>" class="<?php echo $image_class; ?>" width="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" height="<?php echo YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style]['width']; ?>" />
                <?php else : ?>
                    <?php echo $i= $i + 1 ?>
                <?php endif; ?>
            </span>
            <span class="timeline-label"><?php echo $labels['payment'] ?></span>
        </div>
    </li>
</ul>