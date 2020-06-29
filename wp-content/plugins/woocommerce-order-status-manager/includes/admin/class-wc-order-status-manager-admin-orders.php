<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Order Status Manager Orders Admin
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Admin_Orders {


	/** @var array all custom emails */
	private $custom_emails = array();

	/**
	 * Setup admin class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// add order status "next" actions
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'custom_order_actions' ), 10, 2 );

		// handle custom order statuses icons
		add_action( 'admin_head', array( $this, 'custom_order_status_icons' ) );

		// add custom bulk actions and replace core labels with custom labels
		add_action( 'admin_footer-edit.php', array( $this, 'bulk_admin_footer' ), 1 );

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.2.0' ) ) {

			add_filter( 'woocommerce_order_actions', array( $this, 'custom_email_order_actions' ) );

			foreach( $this->get_custom_emails() as $email ) {
				add_action( "woocommerce_order_action_send_osm_email_{$email->post_name}", array( $this, 'send_custom_order_email' ) );
			}
		}
	}


	/**
	 * Add custom order actions in order list view
	 *
	 * @since 1.0.0
	 * @param array $actions
	 * @param \WC_Order $order
	 * @return array
	 */
	public function custom_order_actions( $actions, WC_Order $order ) {

		$custom_actions = $custom_actions = wc_order_status_manager()->get_order_statuses_instance()->get_custom_order_actions( $order );

		if ( ! empty( $custom_actions ) ) {
			$actions = array_merge( $custom_actions, wc_order_status_manager()->get_order_statuses_instance()->trim_order_actions( $actions ) );
		}

		return $actions;
	}


	/**
	 * Gets all custom emails added via this plugin.
	 *
	 * @since 1.8.0
	 *
	 * @return \WP_Post[]
	 */
	protected function get_custom_emails() {

		if ( empty( $this->custom_emails ) ) {
			$this->custom_emails = wc_order_status_manager()->get_emails_instance()->get_emails();
		}

		return $this->custom_emails;
	}


	/**
	 * Re-add custom email order actions in WooCommerce 3.2+.
	 *
	 * These were removed from WC core in 3.2, but merchants <3 them, so we'll give them back.
	 *
	 * @since 1.8.0
	 *
	 * @param string[] $actions single order actions
	 * @return string[] updated actions
	 */
	public function custom_email_order_actions( $actions ) {

		$custom_emails = $this->get_custom_emails();

		// don't modify anything if we have no custom emails
		if ( ! empty( $custom_emails ) ) {

			$new_actions = array();

			foreach ( $custom_emails as $email ) {

				/* translators: Placeholders: %s - custom email name */
				$new_actions["send_osm_email_{$email->post_name}"] = sprintf( __( 'Send %s custom email', 'woocommerce-order-status-manager' ), $email->post_title );
			}

			$actions = Framework\SV_WC_Helper::array_insert_after( $actions, 'send_order_details', $new_actions );
		}

		return $actions;
	}


	/**
	 * Send custom emails when the order action is fired.
	 *
	 * @since 1.8.0
	 *
	 * @param \WC_Order $order the order object
	 */
	public function send_custom_order_email( $order ) {

		if ( ! empty( $_POST['wc_order_action'] ) ) {

			$action = wc_clean( $_POST['wc_order_action'] );

			if ( Framework\SV_WC_Helper::str_starts_with( $action, 'send_osm_email_' ) ) {

				// we'll always find an email WP_Post given we're only hooked here for our custom emails
				$email_slug = str_replace( 'send_osm_email_', '', $action );
				$email_post = get_page_by_path( $email_slug, OBJECT, 'wc_order_email' );

				do_action( 'woocommerce_before_resend_order_emails', $order, $email_slug );

				// ensure gateways + shipping are loaded in case their content is loaded in emails
				WC()->payment_gateways();
				WC()->shipping();

				$emails = WC()->mailer()->get_emails();
				$email  = isset( $emails["wc_order_status_email_{$email_post->ID}"] ) ? $emails["wc_order_status_email_{$email_post->ID}"] : null;

				if ( $email instanceof \WC_Order_Status_Manager_Order_Status_Email ) {

					$email->trigger( $order->get_id(), $order );

					/* translators: Placeholders: %s - custom email name */
					$success = sprintf( __( '%s email notification manually sent.', 'woocommerce-order-status-manager' ), $email_post->post_title );

					$order->add_order_note( $success, false, true );
					wc_order_status_manager()->get_message_handler()->add_message( $success );

				} else {

					/* translators: Placeholders: %s - custom email name */
					$message = sprintf( __( "%s email notification couldn't be sent.", 'woocommerce-order-status-manager' ), $email_post->post_title );

					wc_order_status_manager()->get_message_handler()->add_error( $message );
				}

				do_action( 'woocommerce_after_resend_order_email', $order, $email_slug );
			}
		}
	}


	/**
	 * Print styles for custom order status icons
	 *
	 * @since 1.0.0
	 */
	public function custom_order_status_icons() {

		$custom_status_colors = array();
		$custom_status_badges = array();
		$custom_status_icons  = array();
		$custom_action_icons  = array();

		foreach ( wc_get_order_statuses() as $slug => $name ) {

			$status = new WC_Order_Status_Manager_Order_Status( $slug );

			// sanity check: bail if no status was found
			// this can happen if some statuses are registered late
			if ( ! $status || ! $status->get_id() ) {
				continue;
			}

			$color       = $status->get_color();
			$icon        = $status->get_icon();
			$action_icon = $status->get_action_icon();
			$slug        = (string) esc_attr( $status->get_slug() );

			if ( $color ) {
				$custom_status_colors[ $slug ] = $color;
			}

			// Font icon
			if ( $icon && $icon_details = wc_order_status_manager()->get_icons_instance()->get_icon_details( $icon ) ) {
				$custom_status_icons[ $slug ] = $icon_details;
			}

			// Image icon
			elseif ( is_numeric( $icon ) && $icon_src = wp_get_attachment_image_src( $icon, 'wc_order_status_icon' ) ) {
				$custom_status_icons[ $slug ] = $icon_src[0];
			}

			// Badge
			elseif ( ! $icon ) {
				$custom_status_badges[] = $slug;
			}

			// Font action icon
			if ( $action_icon && $action_icon_details = wc_order_status_manager()->get_icons_instance()->get_icon_details( $action_icon ) ) {
				$custom_action_icons[ $slug ] = $action_icon_details;
			}

			// Image action icon
			elseif ( is_numeric( $action_icon ) && $action_icon_src = wp_get_attachment_image_src( $action_icon, 'wc_order_status_icon' ) ) {
				$custom_action_icons[ $slug ] = $action_icon_src[0];
			}

			// special handling for WooCommerce core "completed" status which is sometimes changed to "complete" in CSS
			if ( 'completed' === $slug && isset( $custom_action_icons['completed'] ) ) {
				$custom_action_icons['complete'] = $custom_action_icons['completed'];
			}
		}

		?>
		<!-- Custom Order Status Icon styles -->
		<style type="text/css">
			/*<![CDATA[*/

			<?php // general styles for status badges ?>
			<?php if ( ! empty( $custom_status_badges ) ) : ?>
				<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) : ?>
					.widefat .column-order_status mark.<?php echo esc_html( implode( ', .widefat .column-order_status mark.', $custom_status_badges ) ); ?> {
						display: inline-block;
						font-size: 0.8em;
						line-height: 1.1;
						text-indent: 0;
						background-color: #666;
						width: auto;
						height: auto;
						padding: 0.4em;
						color: #fff;
						border-radius: 2px;
						word-wrap: break-word;
						max-width: 100%;
					}

					.widefat .column-order_status mark.<?php echo esc_html( implode( ':after, .widefat .column-order_status mark.', $custom_status_badges ) ); ?>:after {
						display: none;
					}
				<?php endif; ?>
			<?php endif; ?>

			<?php // general styles for status icons ?>
			<?php if ( ! empty( $custom_status_icons ) ) : ?>

				<?php $custom_status_font_icons = array_filter( $custom_status_icons, 'is_array' ); ?>

				<?php if ( ! empty( $custom_status_font_icons ) ) : ?>

					<?php $selector = Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ? '.widefat .column-order_status mark.' : '.widefat .column-order_status .order-status.status-'; ?>

					<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' ) ) : ?>
						<?php echo esc_html( $selector . implode( ', ' . $selector, array_keys( $custom_status_font_icons ) ) ); ?> {
							position: relative;
							padding: 0;
							text-indent: -9999px;
							background: transparent;
							border: 0;
							font-size: 2em;
							line-height: 1;
							vertical-align: text-top;
						}
					<?php endif; ?>

					<?php echo esc_html( $selector . implode( ':after, ' . $selector, array_keys( $custom_status_font_icons ) ) ); ?>:after {
						speak: none;
						font-weight: normal;
						font-variant: normal;
						text-transform: none;
						line-height: 1;
						-webkit-font-smoothing: antialiased;
						margin: 0;
						text-indent: 0;
						position: absolute;
						top: 0;
						left: 0;
						text-align: center;
						<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) : ?>
							width: 100%;
							height: 100%;
						<?php endif; ?>
					}

				<?php endif; ?>
			<?php endif; ?>

			<?php // general styles for action icons ?>
			.widefat .column-order_actions a.button {
				padding: 0 0.5em;
				height: 2em;
				line-height: 1.9em;
			}

			<?php if ( ! empty( $custom_action_icons ) ) : ?>

				<?php $custom_action_font_icons = array_filter( $custom_action_icons, 'is_array' ); ?>

				<?php if ( ! empty( $custom_action_font_icons ) ) : ?>

					<?php

					if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) {
						$target_classes       = '.order_actions .' . implode( ', .order_actions .', array_keys( $custom_action_icons ) );
						$target_classes_after = '.order_actions .' . implode( ':after, .order_actions .', array_keys( $custom_action_icons ) );
					}  else {
						$target_classes       = '.wc-action-button.wc-action-button-' . implode( ', .wc-action-button.wc-action-button-', array_keys( $custom_action_icons ) );
						$target_classes_after = '.wc-action-button.wc-action-button-' . implode( ':after, .wc-action-button.wc-action-button-', array_keys( $custom_action_icons ) );
					}

					echo esc_html( $target_classes ); ?> {
						display: block;
						text-indent: -9999px;
						position: relative;
						padding: 0!important;
						height: 2em!important;
						width: 2em;
					}
					<?php
					echo esc_html( $target_classes_after ); ?>:after {
						speak: none;
						font-weight: 400;
						font-variant: normal;
						text-transform: none;
						-webkit-font-smoothing: antialiased;
						margin: 0;
						text-indent: 0;
						position: absolute;
						top: 0;
						left: 0;
						width: 100%;
						height: 100%;
						text-align: center;
						<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) : ?>
							line-height: 1.85;
						<?php endif; ?>
					}

				<?php endif; ?>
			<?php endif; ?>

			<?php // specific status icon styles ?>
			<?php if ( ! empty( $custom_status_icons ) ) : ?>
				<?php foreach ( $custom_status_icons as $status => $value ) : ?>

					<?php $selector = Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ? '.widefat .column-order_status mark.' : '.widefat .column-order_status .order-status.status-'; ?>

					<?php if ( is_array( $value ) ) : ?>
						<?php echo esc_html( $selector. $status ); ?>:after {
							font-family: "<?php echo esc_html( $value['font'] ); ?>";
							content:     "<?php echo esc_html( $value['glyph'] ); ?>";
						}
					<?php else : ?>
						<?php echo esc_html( $selector . $status ); ?> {
							background-size: 100% 100%;
							background-image: url( <?php echo esc_url( $value ); ?> );
							<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' ) ) : ?>
								background-color: transparent;
								border: 0;
								padding: 0;
								text-indent: -9999px;
								width: 2em;
								height: 2em;
							<?php endif; ?>
						}
					<?php endif; ?>

				<?php endforeach; ?>
			<?php endif; ?>

			<?php // specific status color styles ?>
			<?php if ( ! empty( $custom_status_colors ) ) : ?>
				<?php foreach ( $custom_status_colors as $status => $color ) : ?>

					<?php if ( in_array( $status, $custom_status_badges, true ) ) : ?>

						<?php $selector = Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ? '.widefat .column-order_status mark.' : '.widefat .order-status.status-'; ?>

						<?php echo esc_html( $selector . $status ); ?> {
							background-color: <?php echo esc_html( $color ); ?>;
							color: <?php echo esc_html( wc_order_status_manager()->get_icons_instance()->get_contrast_text_color( $color ) ); ?>;
						}
					<?php endif; ?>

					<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' )  ) : ?>
						.wc-order-preview .order-status.status-<?php echo esc_html( $status ); ?> {
							background-color: <?php echo $color; ?>;
							color: <?php echo esc_html( wc_order_status_manager()->get_icons_instance()->get_contrast_text_color( $color ) ); ?>;
						}
					<?php endif; ?>

					<?php if ( isset( $custom_status_icons[ $status ] ) ) : ?>
						<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) : ?>
							.widefat .column-order_status mark.<?php echo esc_html( $status ); ?>:after {
								color: <?php echo esc_html( $color ); ?>;
							}
						<?php else : ?>
							.order-status.status-<?php echo esc_html( $status ); ?> {
								background-color: none;
								border: 0;
								color: <?php echo esc_html( $color ); ?>
							}
						<?php endif; ?>
					<?php endif; ?>

				<?php endforeach; ?>
			<?php endif; ?>

			<?php // specific action icon styles ?>
			<?php if ( ! empty( $custom_action_icons ) ) : ?>
				<?php foreach ( $custom_action_icons as $status => $value ) : ?>

					<?php if ( is_array( $value ) ) : ?>
						<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) : ?>
							.order_actions .<?php echo esc_html( $status ); ?>:after {
								font-family: "<?php echo esc_html( $value['font'] ); ?>";
								content:     "<?php echo esc_html( $value['glyph'] ); ?>";
							}
						<?php else : ?>
							.wc-action-button.wc-action-button-<?php echo esc_html( $status ); ?>:after,
							.widefat .column-wc_actions a.<?php echo esc_html( $status ); ?>:after {
								font-family: "<?php echo esc_html( $value['font'] ); ?>";
								content:     "<?php echo esc_html( $value['glyph'] ); ?>";
								line-height: unset;
							}
						<?php endif; ?>
					<?php else : ?>
						<?php if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.3' ) ) : ?>
							.order_actions .<?php echo esc_html( $status ); ?>,
							.order_actions .<?php echo esc_html( $status ); ?>:focus,
							.order_actions .<?php echo esc_html( $status ); ?>:hover {
								background-size: 69% 69%;
								background-position: center center;
								background-repeat: no-repeat;
								background-image: url( <?php echo esc_url( $value ); ?> );
							}
						<?php else : ?>
							.wc-action-button.wc-action-button-<?php echo esc_html( $status ); ?>,
							.wc-action-button.wc-action-button-<?php echo esc_html( $status ); ?>:focus,
							.wc-action-button.wc-action-button-<?php echo esc_html( $status ); ?>:hover {
								background-size: 69% 69%;
								background-position: center center;
								background-repeat: no-repeat;
								background-image: url( <?php echo esc_url( $value ); ?> );
							}
						<?php endif; ?>
					<?php endif; ?>

				<?php endforeach; ?>
			<?php endif; ?>

			/*]]>*/
		</style>
		<?php
	}


	/**
	 * Add extra bulk action options to mark orders with custom statuses
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
	 *
	 * @since 1.0.0
	 */
	public function bulk_admin_footer() {
		global $post_type;

		if ( 'shop_order' === $post_type ) :

			// get statuses
			$custom_order_statuses = wc_order_status_manager()->get_order_statuses_instance()->get_order_status_posts( array(
				'suppress_filters' => false,
			) );

			// sanity check
			if ( ! $custom_order_statuses ) {
				return;
			}

			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {

					var $dropdownTop, $dropdownBottom, $filterPostsList,
					    $breadcrumb, $count, label, $optionTop, $optionBottom;

					$dropdownTop     = $( 'select[name="action"]' );
					$dropdownBottom  = $( 'select[name="action2"]' );
					$filterPostsList = $( 'div.wrap > ul.subsubsub' );

					<?php foreach ( $custom_order_statuses as $custom_order_status ) :

						$status  = new WC_Order_Status_Manager_Order_Status( $custom_order_status );
						$slug    = $status->get_slug();
						$name    = $status->get_name();
						?>

						<?php // bulk actions - TODO: refactor this when WP 4.7 is the minimum required WP version, see https://make.wordpress.org/core/2016/10/04/custom-bulk-actions/ {IT 2018-01-15} ?>
						$optionTop    = $dropdownTop.find( 'option[value="mark_<?php echo sanitize_html_class( $slug ); ?>"]' );
						$optionBottom = $dropdownBottom.find( 'option[value="mark_<?php echo sanitize_html_class( $slug ); ?>"]' );

						<?php // remove all status bulk actions - they will be re-added below as needed ?>
						$optionTop.remove();
						$optionBottom.remove();

						<?php if ( $status->is_bulk_action() ) :

							/* translators: Placeholder: %s - order status name */ ?>
							label = '<?php printf( __( 'Mark %s', 'woocommerce-order-status-manager' ), esc_html( strtolower( $name ) ) ); ?>';

							<?php // append statuses actions marked to be included in bulk actions ?>
							$( '<option>' ).val( 'mark_<?php echo sanitize_html_class( $slug ); ?>' ).text( label ).appendTo( $dropdownTop );
							$( '<option>' ).val( 'mark_<?php echo sanitize_html_class( $slug ); ?>' ).text( label ).appendTo( $dropdownBottom );

						<?php endif; ?>

					<?php endforeach; ?>

				} );
			</script>
			<?php

		endif;
	}


}
