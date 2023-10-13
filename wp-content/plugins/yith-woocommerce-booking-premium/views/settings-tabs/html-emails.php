<?php
/**
 * Emails tab content.
 *
 * @var YITH_WCBK_Email[] $emails
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit();

$columns = array(
	'name'      => _x( 'Email', 'Email list header', 'yith-booking-for-woocommerce' ),
	'recipient' => _x( 'Recipient(s)', 'Email list header', 'yith-booking-for-woocommerce' ),
	'status'    => _x( 'Active', 'Email list header', 'yith-booking-for-woocommerce' ),
	'actions'   => '',
);

?>
<div class="yith-wcbk-emails">
	<div class="yith-wcbk-emails__headings">
		<?php foreach ( $columns as $key => $column ) : ?>
			<div class="yith-wcbk-emails__heading yith-wcbk-emails__heading-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $column ); ?></div>
		<?php endforeach; ?>
	</div>
	<div class="yith-wcbk-emails__list">
		<?php foreach ( $emails as $email_key => $email ) : ?>
			<div class="yith-wcbk-emails__email" data-email="<?php echo esc_attr( $email_key ); ?>">
				<div class="yith-wcbk-emails__email__head">
					<?php foreach ( $columns as $key => $column ) : ?>
						<div class="yith-wcbk-emails__email__column yith-wcbk-emails__email__column-<?php echo esc_attr( $key ); ?>">
							<?php
							switch ( $key ) {
								case 'name':
									echo '<strong>' . esc_html( $email->get_title() ) . '</strong>';
									echo '<div class="description">' . esc_html( $email->get_description() ) . '</div>';
									break;
								case 'recipient':
									echo esc_html( $email->get_recipient_to_show_in_settings_list() );
									break;
								case 'status':
									if ( $email->is_manual() ) {
										echo esc_html__( 'Manually sent', 'yith-booking-for-woocommerce' );
									} else {
										yith_plugin_fw_get_field(
											array(
												'type'  => 'onoff',
												'value' => $email->is_enabled(),
												'class' => 'yith-wcbk-emails__email__toggle-active',
											),
											true
										);
									}
									break;
								case 'actions':
									yith_plugin_fw_get_component(
										array(
											'class'  => 'yith-wcbk-emails__email__toggle-editing',
											'type'   => 'action-button',
											'action' => 'edit',
											'icon'   => 'edit',
											'title'  => __( 'Edit', 'yith-booking-for-woocommerce' ),
											'url'    => '#',
										),
										true
									);
									break;
								default:
									break;
							}
							?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="yith-wcbk-emails__email__options">
					<form class="yith-wcbk-emails__email__options__form">
						<table class="form-table">
							<?php $email->generate_settings_html(); ?>
						</table>
						<div class="yith-wcbk-emails__email__actions">
							<span
									class="yith-wcbk-emails__email__save yith-plugin-fw__button yith-plugin-fw__button--primary yith-plugin-fw__button--xl"
									data-save-message="<?php esc_attr_e( 'Save', 'yith-booking-for-woocommerce' ); ?>"
									data-saved-message="<?php esc_attr_e( 'Saved', 'yith-booking-for-woocommerce' ); ?>"
							>
								<svg class="yith-wcbk-emails__email__save__saved-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" role="img">
									<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
								</svg>
								<span class="yith-wcbk-emails__email__save__text">
									<?php esc_html_e( 'Save', 'yith-booking-for-woocommerce' ); ?>
								</span>
							</span>
						</div>
					</form>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
