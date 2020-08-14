
<div class="yith-pos-info">
	<h4><?php echo  __('Order made in store:', 'yith-point-of-sale-for-woocommerce') ?></h4>
	<p><?php echo $store_name ?></p>
</div>

<div class="yith-pos-info">
    <h4><?php echo  __('Register:', 'yith-point-of-sale-for-woocommerce') ?></h4>
    <p><?php echo $register_name ?></p>
</div>

<div class="yith-pos-info">
	<h4><?php echo  __('Cashier:', 'yith-point-of-sale-for-woocommerce') ?></h4>
	<p><?php echo $cashier ?></p>
</div>

<?php if ( $payment_methods ) :
	$gateways= WC()->payment_gateways()->payment_gateways();

    ?>
    <div class="yith-pos-info">
        <h4><?php echo __( 'Payment methods:', 'yith-point-of-sale-for-woocommerce' ) ?></h4>

		<?php foreach ( $payment_methods as $payment_method ) :
            if( isset( $gateways[$payment_method->paymentMethod]) ) :
                $gateway_name = $gateways[$payment_method->paymentMethod]->title;
            ?>
            <div class="payment-method"><span class="title"><?php echo $gateway_name ?></span><span
                        class="amount"><?php echo wc_price( $payment_method->amount, $currency ) ?></span></div>
		<?php endif;
		endforeach;
		?>
    </div>
<?php endif ?>