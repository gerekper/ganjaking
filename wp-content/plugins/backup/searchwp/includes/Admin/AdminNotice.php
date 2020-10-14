<?php

/**
 * SearchWP AdminNotice.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin;

use SearchWP\Settings;

/**
 * Class AdminNotice is responsible for displaying an Admin Notice.
 *
 * @since 4.0
 */
abstract class AdminNotice {

	/**
	 * The message to display in this Admin Notice.
	 *
	 * @since 4.0
	 * @var string
	 */
	protected $message;

	/**
	 * Unique slug for this Admin Notice.
	 *
	 * @since 4.0
	 * @var string
	 */
	protected $slug;

	/**
	 * Whether this Admin Notice is dismissible.
	 *
	 * @since 4.0
	 * @var boolean
	 */
	protected $dismissible;

	/**
	 * The type of this Admin Notice. Supported types are: 'error', 'warning', 'success', or 'info'.
	 *
	 * @since 4.0
	 * @var string
	 */
	protected $type = 'warning';

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @return void
	 */
	function __construct() {
		add_action( 'admin_notices', [ $this, 'render' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'notice_dismiss', [ $this, 'dismiss' ] );
	}

	/**
	 * Returns whether this Admin Notice has been dismissed.
	 *
	 * @since 4.0
	 * @return bool
	 */
	private function is_dismissed() {
		$dismissed = Settings::get( 'dismissed_notices', 'array' );

		return in_array( $this->slug, $dismissed );
	}

	/**
	 * Outputs this Admin Notice.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function render() {
		if ( $this->is_dismissed() ) {
			return;
		}

		$class = 'notice notice-' . $this->type;

		if ( $this->dismissible ) {
			$class .= ' is-dismissible';
			$this->get_dismiss_callback();
		}

		printf(
			'<div class="%1$s" id="%2$s"><p>%3$s</p></div>',
			esc_attr( $class ),
			esc_attr( 'searchwp-admin-notice-' . $this->slug ),
			wp_kses_post( $this->message )
		);
	}

	/**
	 * Binds the dismissal icon added by WordPress so we can actually track the dismissal.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function get_dismiss_callback() {
		$nonce = wp_create_nonce( SEARCHWP_PREFIX . 'admin_notice' . $this->slug );
		?>
		<script>
			jQuery(document).ready(function($){
				$('body').on('click', '#searchwp-admin-notice-<?php echo esc_js( $this->slug ); ?> button.notice-dismiss', function(e) {
					$.post(ajaxurl, {
						_ajax_nonce: '<?php echo esc_js( $nonce ); ?>',
						action: '<?php echo esc_js( SEARCHWP_PREFIX . 'notice_dismiss' ); ?>',
						notice: '<?php echo esc_js( $this->slug ); ?>'
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * AJAX callback that marks this Notice as dismissed.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function dismiss() {
		$dismissed = Settings::get( 'dismissed_notices', 'array' );

		$dismissed[] = $this->slug;

		Settings::update( 'dismissed_notices', array_unique( $dismissed ) );

		wp_send_json_success();
	}
}
