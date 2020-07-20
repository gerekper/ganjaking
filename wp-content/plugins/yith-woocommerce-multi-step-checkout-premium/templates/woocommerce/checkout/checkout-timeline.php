<?php
$i = 0;
$is_text_style =  'text' == $style;
$use_step_number = 'yes' === get_option( 'yith_wcms_show_step_number', 'yes' );

$enable_checkout_login_reminder = 'yes' == get_option( 'woocommerce_enable_checkout_login_reminder', 'yes' ) ? true : false;
$image_class = apply_filters( 'yith_wcms_timeline_icon_class', '' );
$show_login_step = ! $is_user_logged_in && $enable_checkout_login_reminder;
?>
<ul id="checkout_timeline" class="woocommerce_checkout_timeline <?php echo $display ?> <?php echo $style ?> <?php echo $shipping_step_enabled ? '' : 'shipping_removed'; ?>">
    <?php if( $show_login_step ) : ?>
        <li id="timeline-login" data-step="login" class="timeline login <?php echo ! $is_user_logged_in ? 'active' : '';?>" >
            <div class="timeline-wrapper">
				<?php $login_use_icon = yith_wcms_step_use_icon( 'login' ); ?>
                 <span class="timeline-step <?php echo false !== $login_use_icon ? 'with-icon' : '' ?>">
					<?php if( ! $is_text_style && false !== $login_use_icon ) : ?>
						<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'login' ); ?>
					<?php endif; ?>
					<?php $i = $i + 1 ?>
					<?php if( $use_step_number && false === $login_use_icon ) : ?>
						<?php echo $i; ?>
					<?php endif; ?>
				</span>
                <a href="#" class="timeline-label">
					<?php echo $use_step_number && false !== $login_use_icon ? $i . '.' : ''; ?>
					<?php echo $labels['login'] ?>
				</a>
            </div>
        </li>
    <?php endif; ?>
    <li id="timeline-billing" data-step="billing" class="timeline billing <?php echo ! $show_login_step ? 'active' : '';?>" >
        <div class="timeline-wrapper">
			<?php $billing_use_icon = yith_wcms_step_use_icon( 'billing' ); ?>
            <span class="timeline-step <?php echo false !== $billing_use_icon ? 'with-icon' : '' ?>">
                <?php if( ! $is_text_style && false !== $billing_use_icon ) : ?>
					<?php echo yith_wcms_checkout_timeline_get_icon( $style, 'billing' ); ?>
				<?php endif; ?>
				<?php $i = $i + 1 ?>
				<?php if( $use_step_number && false === $billing_use_icon ) : ?>
					<?php echo $i; ?>
				<?php endif; ?>
            </span>
            <a href="#" class="timeline-label">
				<?php echo $use_step_number && false !== $billing_use_icon ? $i . '.' : ''; ?>
				<?php echo $labels['billing'] ?>
			</a>
        </div>
    </li>
    <?php if( $shipping_step_enabled ) : ?>
    <li id="timeline-shipping" data-step="shipping" class="timeline shipping" >
        <div class="timeline-wrapper">
			<?php $shipping_use_icon = yith_wcms_step_use_icon( 'shipping' ); ?>
            <span class="timeline-step <?php echo $shipping_use_icon ? 'with-icon' : '' ?>">
               <?php if( ! $is_text_style && false !== $shipping_use_icon ) : ?>
				   <?php echo yith_wcms_checkout_timeline_get_icon( $style, 'shipping' ); ?>
			   <?php endif; ?>
			   <?php $i = $i + 1 ?>
			   <?php if( $use_step_number && false === $shipping_use_icon ) : ?>
				   <?php echo $i; ?>
			   <?php endif; ?>
            </span>
            <a href="#" class="timeline-label">
				<?php echo $use_step_number && false !== $shipping_use_icon ? $i . '.' : ''; ?>
				<?php echo $labels['shipping'] ?>
			</a>
        </div>
    </li>
    <?php endif; ?>
    <li id="timeline-order" data-step="order" class="timeline order" >
        <div class="timeline-wrapper">
			<?php $order_use_icon = yith_wcms_step_use_icon( 'order' ); ?>
            <span class="timeline-step <?php echo $order_use_icon ? 'with-icon' : '' ?>">
              <?php if( ! $is_text_style && false !== $order_use_icon ) : ?>
				  <?php echo yith_wcms_checkout_timeline_get_icon( $style, 'order' ); ?>
			  <?php endif; ?>
			  <?php $i = $i + 1 ?>
			  <?php if( $use_step_number && false === $order_use_icon ) : ?>
				  <?php echo $i; ?>
			  <?php endif; ?>
            </span>
            <a href="#" class="timeline-label">
				<?php echo $use_step_number && false !== $order_use_icon ? $i . '.' : ''; ?>
				<?php echo $labels['order'] ?>
			</a>
        </div>
    </li>
	<?php if( $payment_step_enabled ) : ?>
    <li id="timeline-payment" data-step="payment" class="timeline payment" >
        <div class="timeline-wrapper">
			<?php $payment_use_icon = yith_wcms_step_use_icon( 'payment' ); ?>
             <span class="timeline-step <?php echo $payment_use_icon ? 'with-icon' : '' ?>">
               <?php if( ! $is_text_style && false !== $payment_use_icon ) : ?>
				   <?php echo yith_wcms_checkout_timeline_get_icon( $style, 'payment' ); ?>
			   <?php endif; ?>
			   <?php $i = $i + 1 ?>
			   <?php if( $use_step_number && false === $order_use_icon ) : ?>
				   <?php echo $i; ?>
			   <?php endif; ?>
            </span>
            <a href="#" class="timeline-label">
				<?php echo $use_step_number && false !== $order_use_icon ? $i . '.' : ''; ?>
				<?php echo $labels['payment'] ?>
			</a>
        </div>
    </li>
	<?php endif; ?>
</ul>
