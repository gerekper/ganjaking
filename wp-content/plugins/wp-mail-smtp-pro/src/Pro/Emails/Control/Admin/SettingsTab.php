<?php

namespace WPMailSMTP\Pro\Emails\Control\Admin;

use WPMailSMTP\Admin\Pages\ControlTab;
use WPMailSMTP\Helpers\UI;
use WPMailSMTP\Options;
use WPMailSMTP\WP;

/**
 * Class SettingsTab.
 *
 * @since 1.5.0
 */
class SettingsTab extends ControlTab {

	/**
	 * Output HTML of the email controls settings.
	 *
	 * @since 1.5.0
	 */
	public function display() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$options  = Options::init();
		$controls = wp_mail_smtp()->pro->get_control()->get_controls();
		?>

		<form method="POST" action="">
			<?php $this->wp_nonce_field(); ?>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading wp-mail-smtp-section-heading--has-divider">
				<div class="wp-mail-smtp-setting-field">
					<h2><?php esc_html_e( 'Email Controls', 'wp-mail-smtp-pro' ); ?></h2>
					<p class="desc">
						<?php esc_html_e( 'WordPress, by default, will send out emails for many events on your site. Using the toggles below, you can decide exactly which emails you\'d like enabled.', 'wp-mail-smtp-pro' ); ?>
					</p>
				</div>
			</div>

			<?php
			foreach ( $controls as $section_id => $section ) :
				if ( empty( $section['emails'] ) ) {
					continue;
				}

				if ( $this->is_it_for_multisite( sanitize_key( $section_id ) ) && ! WP::use_global_plugin_settings() ) {
					continue;
				}
				?>
				<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content wp-mail-smtp-clear section-heading no-desc" id="wp-mail-smtp-setting-row-email-heading-<?php echo esc_attr( $section_id ); ?>">
					<div class="wp-mail-smtp-setting-field">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
					</div>
				</div>
				<div class="wp-mail-smtp-setting-group">
					<?php
					foreach ( $section['emails'] as $email_id => $email ) :
						$email_id = sanitize_key( $email_id );

						if ( empty( $email_id ) || empty( $email['label'] ) ) {
							continue;
						}

						if ( $this->is_it_for_multisite( sanitize_key( $email_id ) ) && ! WP::use_global_plugin_settings() ) {
							continue;
						}
						?>
						<div id="wp-mail-smtp-setting-row-control_<?php echo esc_attr( $email_id ); ?>" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-checkbox-toggle wp-mail-smtp-clear">
							<div class="wp-mail-smtp-setting-label">
								<label for="wp-mail-smtp-setting-<?php echo esc_attr( $email_id ); ?>">
									<?php echo esc_html( $email['label'] ); ?>
								</label>
							</div>
							<div class="wp-mail-smtp-setting-field">
								<?php
								UI::toggle(
									[
										'name'    => 'wp-mail-smtp[control][' . $email_id . ']',
										'id'      => 'wp-mail-smtp-setting-' . $email_id,
										'checked' => ! $options->get( 'control', $email_id ),
									]
								);
								?>
								<?php if ( ! empty( $email['desc'] ) ) : ?>
									<p class="desc">
										<?php echo $email['desc']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<?php $this->display_save_btn(); ?>

		</form>
		<?php
	}

	/**
	 * Process tab form submission ($_POST).
	 *
	 * @since 1.5.0
	 *
	 * @param array $data Post data specific for the plugin.
	 */
	public function process_post( $data ) {

		$this->check_admin_referer();

		$options      = Options::init();
		$is_multisite = is_multisite();

		$controls = wp_mail_smtp()->pro->get_control()->get_controls( true );

		foreach ( $controls as $control ) {
			// In MS we have all options listed - so just preserve everything and convert to boolean.
			if ( $is_multisite ) {
				// Those that are not in $data - user unchecked - which means disabled.
				$data['control'][ $control ] = empty( $data['control'][ $control ] );
			} else {
				// Process MS specific separately with on by default (enabled).
				if ( strpos( $control, 'network' ) !== false ) {
					$data['control'][ $control ] = false;
				} else {
					// Those that are not in $data - user unchecked - which means disabled.
					$data['control'][ $control ] = empty( $data['control'][ $control ] );
				}
			}
		}

		// All the sanitization is done there.
		$options->set( $data, false, false );

		WP::add_admin_notice(
			esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
			WP::ADMIN_NOTICE_SUCCESS
		);
	}
}
