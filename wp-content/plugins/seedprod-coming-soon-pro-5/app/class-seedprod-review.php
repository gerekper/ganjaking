<?php
/**
 * Ask for some love.
 *
 * @package    SeedProd
 * @author     SeedProd
 * @since      1.1.3
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, SeedProd LLC
 */
if ( ! class_exists( 'SeedProd_Review' ) ) {
	/**
	* PLugin Review Request
	*/
	class SeedProd_Review {

		/**
		 * Primary class constructor.
		 *
		 * @since 7.0.7
		 */
		public function __construct() {
			// Admin notice requesting review.
			add_action( 'admin_notices', array( $this, 'review_request' ) );
			add_action( 'wp_ajax_seedprod_review_dismiss', array( $this, 'review_dismiss' ) );
		}
		/**
		 * Add admin notices as needed for reviews.
		 *
		 * @since 7.0.7
		 */
		public function review_request() {
			// Only consider showing the review request to admin users.
			if ( ! is_super_admin() ) {
				return;
			}

			// If the user has opted out of product annoucement notifications, don't
			// display the review request.
			if ( get_option( 'seedprod_hide_review' ) ) {
				return;
			}
			// Verify that we can do a check for reviews.
			$review = get_option( 'seedprod_review' );
			$time   = time();
			$load   = false;

			if ( ! $review ) {
				$review = array(
					'time'      => $time,
					'dismissed' => false,
				);
				update_option( 'seedprod_review', $review );
			} else {
				// Check if it has been dismissed or not.
				if ( ( isset( $review['dismissed'] ) && ! $review['dismissed'] ) && ( isset( $review['time'] ) && ( ( $review['time'] + DAY_IN_SECONDS ) <= $time ) ) ) {
					$load = true;
				}
			}

			// If we cannot load, return early.
			if ( ! $load ) {
				return;
			}

			$this->review();
		}

		/**
		 * Maybe show review request.
		 *
		 * @since 7.0.7
		 */
		public function review() {
			// Fetch when plugin was initially installed.
			$activated = get_option( 'seedprod_over_time', array() );
			if ( ! empty( $activated['installed_date'] ) ) {
				//Only continue if plugin has been installed for at least 7 days.
				if ( ( $activated['installed_date'] + ( DAY_IN_SECONDS * 7 ) ) > time() ) {
					return;
				}
				// only if version great than or = to 6.0.8.5
				if ( ! empty( $activated['installed_version'] ) && version_compare( $activated['installed_version'], '6.0.8.5' ) < 0 ) {
					return;
				}
			} else {
				$data = array(
					'installed_version' => SEEDPROD_PRO_VERSION,
					'installed_date'    => time(),
				);

				update_option( 'seedprod_over_time', $data );
				return;
			}

			$feedback_url = 'https://www.seedprod.com/plugin-feedback/?utm_source=liteplugin&utm_medium=review-notice&utm_campaign=feedback&utm_content=' . SEEDPROD_PRO_VERSION;
			// We have a candidate! Output a review message. ?>
		<div class="notice notice-info is-dismissible seedprod-review-notice">
			<div class="seedprod-review-step seedprod-review-step-1">
				<p><?php esc_html_e( 'Are you enjoying SeedProd?', 'seedprod-pro' ); ?></p>
				<p>
					<a href="#" class="seedprod-review-switch-step" data-step="3"><?php esc_html_e( 'Yes', 'seedprod-pro' ); ?></a><br />
					<a href="#" class="seedprod-review-switch-step" data-step="2"><?php esc_html_e( 'Not Really', 'seedprod-pro' ); ?></a>
				</p>
			</div>
			<div class="seedprod-review-step seedprod-review-step-2" style="display: none">
				<p><?php esc_html_e( 'We\'re sorry to hear you aren\'t enjoying SeedProd. We would love a chance to improve. Could you take a minute and let us know what we can do better?', 'seedprod-pro' ); ?></p>
				<p>
					<a href="<?php echo esc_url( $feedback_url ); ?>" class="seedprod-dismiss-review-notice seedprod-review-out"><?php esc_html_e( 'Give Feedback', 'seedprod-pro' ); ?></a><br>
					<a href="#" class="seedprod-dismiss-review-notice" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'No thanks', 'seedprod-pro' ); ?></a>
				</p>
			</div>
			<div class="seedprod-review-step seedprod-review-step-3" style="display: none">
				<p><?php esc_html_e( 'That’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'seedprod-pro' ); ?></p>
				<p><strong><?php echo wp_kses( __( '~ John Turner<br>Co-Founder of SeedProd', 'seedprod-pro' ), array( 'br' => array() ) ); ?></strong></p>
				<p>
					<a href="https://wordpress.org/support/plugin/coming-soon/reviews/?filter=5#new-post" class="seedprod-dismiss-review-notice seedprod-review-out" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Ok, you deserve it', 'seedprod-pro' ); ?></a><br>
					<a href="#" class="seedprod-dismiss-review-notice" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Nope, maybe later', 'seedprod-pro' ); ?></a><br>
					<a href="#" class="seedprod-dismiss-review-notice" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'I already did', 'seedprod-pro' ); ?></a>
				</p>
			</div>
		</div>
		<script type="text/javascript">
			jQuery( document ).ready( function ( $ ) {
				$( document ).on( 'click', '.seedprod-dismiss-review-notice, .seedprod-review-notice button', function ( event ) {
					if ( ! $( this ).hasClass( 'seedprod-review-out' ) ) {
						event.preventDefault();
					}
					$.post( ajaxurl, {
						action: 'seedprod_review_dismiss'
					} );
					$( '.seedprod-review-notice' ).remove();
				} );

				$( document ).on( 'click', '.seedprod-review-switch-step', function ( e ) {
					e.preventDefault();
					var target = $( this ).attr( 'data-step' );
					if ( target ) {
						var notice = $( this ).closest( '.seedprod-review-notice' );
						var review_step = notice.find( '.seedprod-review-step-' + target );
						if ( review_step.length > 0 ) {
							notice.find( '.seedprod-review-step:visible').fadeOut( function (  ) {
								review_step.fadeIn();
							});
						}
					}
				})
			} );
		</script>
			<?php
		}
		/**
		 * Dismiss the review admin notice
		 *
		 * @since 7.0.7
		 */
		public function review_dismiss() {
			$review              = get_option( 'seedprod_review', array() );
			$review['time']      = time();
			$review['dismissed'] = true;
			update_option( 'seedprod_review', $review );
			die;
		}
	}
	new SeedProd_Review();
}

