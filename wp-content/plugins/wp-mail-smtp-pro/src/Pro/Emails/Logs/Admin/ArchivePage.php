<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Admin\Area;

/**
 * Class ArchivePage displays a Email Log page content.
 *
 * @since 1.5.0
 */
class ArchivePage extends PageAbstract {

	/**
	 * Link label of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Email Log', 'wp-mail-smtp-pro' );
	}

	/**
	 * Tab content.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added search functionality.
	 */
	public function display() {

		$page_url = wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' );
		?>

		<div class="wp-mail-smtp-page-title">
			<span class="page-title">
				<?php echo esc_html( $this->get_label() ); ?>
			</span>

			<?php if ( ! empty( $_REQUEST['search']['term'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<a href="<?php echo esc_url( $page_url ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange action">
					<?php esc_html_e( 'Reset Search', 'wp-mail-smtp-pro' ); ?>
				</a>
				<span class="search-term">
					<?php
					printf(
						/* translators: %s - search term. */
						'Search results for %s',
						'<code>' . esc_html( wp_unslash( $_REQUEST['search']['term'] ) ) . '</code>' // phpcs:ignore WordPress.Security
					);
					?>
				</span>
			<?php endif; ?>
		</div>

		<h1 class="screen-reader-text">
			<?php echo esc_html( $this->get_label() ); ?>
		</h1>

		<div class="wp-mail-smtp-page-content">
			<?php do_action( 'wp_mail_smtp_admin_pages_before_content' ); ?>

			<form action="<?php echo esc_url( $page_url ); ?>" method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( Area::SLUG . '-logs' ); ?>" />

				<?php
				if ( wp_mail_smtp()->pro->get_logs()->is_enabled() ) {
					if ( ! wp_mail_smtp()->pro->get_logs()->is_valid_db() ) {
						$this->display_logging_not_installed();
					} else {
						$table = new Table();
						$table->prepare_items();

						$table->search_box(
							esc_html__( 'Search Emails', 'wp-mail-smtp-pro' ),
							Area::SLUG . '-logs-archive-search-input'
						);

						$table->display();
					}
				} else {
					$this->display_logging_disabled();
				}
				?>
			</form>
		</div>

		<?php
	}
}
