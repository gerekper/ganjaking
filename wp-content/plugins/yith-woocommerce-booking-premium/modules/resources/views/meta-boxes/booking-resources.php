<?php
/**
 * Resources in Booking data meta-box
 *
 * @var YITH_WCBK_Booking $booking The booking.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

$product      = $booking->get_product();
$label        = ! ! $product ? $product->get_resources_label() : '';
$resource_ids = $booking->get_resource_ids( 'edit' );
/**
 * The resources.
 *
 * @var YITH_WCBK_Resource[] $resources
 */
$resources = array_filter( array_map( 'yith_wcbk_get_resource', $resource_ids ) );
?>

<?php if ( ! ! $resources ) : ?>
	<h4>
		<?php esc_html_e( 'Resources', 'yith-booking-for-woocommerce' ); ?>

		<?php if ( ! ! $label ) : ?>
			- <span class="yith-wcbk-booking-resources-label"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>

	</h4>
	<table class="yith-plugin-fw__classic-table yith-wcbk-booking-resources-table widefat">
		<?php foreach ( $resources as $resource ) : ?>
			<tr>
				<td>
					<?php
					echo sprintf(
						'%s <small>#%s</small>',
						esc_html( $resource->get_name() ),
						esc_html( $resource->get_id() )
					);
					?>
				</td>
				<td class="actions">
					<?php
					$actions = array(
						'view'          => array(
							'type'   => 'action-button',
							'action' => 'view',
							'title'  => __( 'View resource', 'yith-booking-for-woocommerce' ),
							'icon'   => 'eye',
							'url'    => get_edit_post_link( $resource->get_id() ),
						),
						'view-calendar' => array(
							'type'   => 'action-button',
							'action' => 'view-calendar',
							'title'  => __( 'View resource calendar', 'yith-booking-for-woocommerce' ),
							'icon'   => 'calendar',
							'url'    => $resource->get_admin_calendar_url(),
						),
					);

					yith_plugin_fw_get_action_buttons( $actions, true );
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
