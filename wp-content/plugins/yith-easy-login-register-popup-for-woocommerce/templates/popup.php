<?php
/**
 * Login Register Popup Main Template
 * @package YITH Easy Login Register Popup for WooCommerce
 */

defined( 'YITH_WELRP' ) || exit;
?>
<div id="yith-welrp">
	<div class="yith-welrp-overlay"></div>
	<div class="yith-welrp-popup-wrapper <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
		<div class="yith-welrp-popup-wrapper-region">
			<div class="yith-welrp-popup" data-animation-in="<?php echo esc_attr( $animation_in ); ?>" data-animation-out="<?php echo esc_attr( $animation_out ); ?>">
				<div class="yith-welrp-popup-inner">
					<div class="yith-welrp-popup-close <?php echo ! empty( $close_icon ) ? 'custom' : ''; ?>">
						<?php if ( $close_icon ): ?>
							<img src="<?php echo esc_url( $close_icon ); ?>" alt=""/>
						<?php endif; ?>
					</div>
					<div class="yith-welrp-popup-content-wrapper"></div>
				</div>
			</div>
		</div>
	</div>
</div>
