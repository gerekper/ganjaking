<?php
/**
 * Subscription actions
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current Subscription.
 * @var string             $style How to show the actions
 * @var array              $pause Pause info.
 * @var array              $cancel Cancel info
 * @var array              $resume Resume info
 * @var string             $close_modal_button Label of button inside the modal.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<!-- SUBSCRIPTION MODAL -->
<?php if ( $pause ) : ?>
	<div id="pause-subscription" class="ywsbs-modal">
		<div class="ywsbs-modal-overlay"></div>
		<div class="ywsbs-modal-container">
			<div class="ywsbs-modal-wrapper">
				<div class="ywsbs-modal-content">
					<div class="ywsbs-modal-header">
						<span class="close">&times;</span>
					</div>
					<div class="ywsbs-modal-body">
						<div class="ywsbs-modal-icon"><img src="<?php echo esc_url( YITH_YWSBS_ASSETS_URL . '/images/pause-subscription.svg' ); ?>" /></div>
						<div class="ywsbs-content-text"><?php echo wp_kses_post( wpautop( $pause['modal_text'] ) ); ?></div>

						<?php if ( ! empty( $pause['modal_button_label'] ) ) : ?>
							<div class="ywsbs-action-button-wrap">
								<button class="button btn ywsbs-action-button" data-action="pause" data-id="<?php echo esc_attr( $subscription->get_id() ); ?>" data-nonce="<?php echo esc_attr( $pause['nonce'] ); ?>"><?php echo esc_html( $pause['modal_button_label'] ); ?></button>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $pause['close_modal_button'] ) ) : ?>
							<a href="#" class="close"><?php echo esc_html( $pause['close_modal_button'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if ( $resume ) : ?>
	<div id="resume-subscription" class="ywsbs-modal">
		<div class="ywsbs-modal-overlay"></div>
		<div class="ywsbs-modal-container">
			<div class="ywsbs-modal-wrapper">
				<div class="ywsbs-modal-content">
					<div class="ywsbs-modal-header">
						<span class="close">&times;</span>
					</div>
					<div class="ywsbs-modal-body">
						<div class="ywsbs-modal-icon"><img src="<?php echo esc_url( YITH_YWSBS_ASSETS_URL . '/images/resume-subscription.svg' ); ?>" /></div>

						<div class="ywsbs-content-text"><?php echo wp_kses_post( wpautop( $resume['modal_text'] ) ); ?></div>

						<?php if ( ! empty( $resume['modal_button_label'] ) ) : ?>
							<div class="ywsbs-action-button-wrap">
								<button class="button btn ywsbs-action-button" data-action="resume" data-id="<?php echo esc_attr( $subscription->get_id() ); ?>" data-nonce="<?php echo esc_attr( $resume['nonce'] ); ?>"><?php echo esc_html( $resume['modal_button_label'] ); ?></button>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $resume['close_modal_button'] ) ) : ?>
							<div class="close-modal-wrap"><a href="#"
									class="close"><?php echo esc_html( $resume['close_modal_button'] ); ?></a></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>


<?php if ( $cancel ) : ?>
	<div id="cancel-subscription" class="ywsbs-modal">
		<div class="ywsbs-modal-overlay"></div>
		<div class="ywsbs-modal-container">
			<div class="ywsbs-modal-wrapper">
				<div class="ywsbs-modal-content">
					<div class="ywsbs-modal-header">
						<span class="close">&times;</span>
					</div>
					<div class="ywsbs-modal-body">
						<div class="ywsbs-modal-icon"><img src="<?php echo esc_url( YITH_YWSBS_ASSETS_URL . '/images/delete-subscription.svg' ); ?>" /></div>

						<div class="ywsbs-content-text"><?php echo wp_kses_post( wpautop( $cancel['modal_text'] ) ); ?></div>

						<?php if ( ! empty( $cancel['modal_button_label'] ) ) : ?>
							<div class="ywsbs-action-button-wrap">
								<button class="button btn ywsbs-action-button" data-action="cancel" data-id="<?php echo esc_attr( $subscription->get_id() ); ?>" data-nonce="<?php echo esc_attr( $cancel['nonce'] ); ?>"><?php echo esc_html( $cancel['modal_button_label'] ); ?></button>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $cancel['close_modal_button'] ) ) : ?>
							<div class="close-modal-wrap"><a href="#"
									class="close"><?php echo esc_html( $cancel['close_modal_button'] ); ?></a></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
