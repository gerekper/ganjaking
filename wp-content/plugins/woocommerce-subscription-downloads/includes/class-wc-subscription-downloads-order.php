<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Subscription Downloads Order.
 *
 * @package  WC_Subscription_Downloads_Order
 * @category Order
 * @author   WooThemes
 */
class WC_Subscription_Downloads_Order {

	/**
	 * Order actions.
	 */
	public function __construct() {
		add_action( 'woocommerce_subscription_status_changed', array( $this, 'download_permissions' ), 10, 4 );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_list_downloads' ), 10, 3 );
	}

	/**
	 * Save the download permissions in the subscription depending on the status.
	 *
	 * @param  int $order_id Order ID.
	 *
	 * @return void
	 */
	public function download_permissions( $subscription_id, $old_status, $new_status, $subscription ) {
		if ( ! in_array( $new_status, array( 'active', 'expired', 'cancelled' ) ) ) {
			return;
		}

		$product_item_ids = array_map( function( $item ) {
			return $item['product_id'];
		}, $subscription->get_items() );

		foreach ( $subscription->get_items() as $item ) {

			// Gets the downloadable products.
			$downloadable_products = WC_Subscription_Downloads::get_downloadable_products( $item['product_id'], $item['variation_id'] );

			if ( $downloadable_products ) {

				foreach ( $downloadable_products as $product_id ) {
					$_product = wc_get_product( $product_id );

					if ( ! $_product ) {
						continue;
					}

					$product_status = version_compare( WC_VERSION, '3.0', '<' ) ? $_product->post->post_status : $_product->get_status();

					if ( 'expired' === $new_status || 'cancelled' === $new_status ) {
						WCS_Download_Handler::revoke_downloadable_file_permission( $product_id, $subscription_id, $subscription->get_user_id() );
					} 
					// Adds the downloadable files to the subscription.
					else if ( $_product && $_product->exists() && $_product->is_downloadable() && 'publish' === $product_status ) {
						WCS_Download_Handler::revoke_downloadable_file_permission( $product_id, $subscription_id, $subscription->get_user_id() );
						$downloads = version_compare( WC_VERSION, '3.0', '<' ) ? $_product->get_files() : $_product->get_downloads();

						foreach ( array_keys( $downloads ) as $download_id ) {
							wc_downloadable_file_permission( $download_id, $product_id, $subscription );

							if ( ! in_array( $_product->get_id(), $product_item_ids ) ) {
								// Skip wrong recalculation of totals by adding a 0 amount Subscriptions.
								$totals = array(
									'subtotal'     => wc_format_decimal( 0 ),
									'total'        => wc_format_decimal( 0 ),
									'subtotal_tax' => wc_format_decimal( 0 ),
									'tax'          => wc_format_decimal( 0 ),
								);

								$subscription->add_product( $_product, 1, array( 'totals' => $totals ) );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * List the downloads in order emails.
	 *
	 * @param  WC_Order $order         Order data
	 * @param  bool     $sent_to_admin Sent or not to admin.
	 * @param  bool     $plain_text    Plain or HTML email.
	 * @return string                  List of downloads.
	 */
	public function email_list_downloads( $order, $sent_to_admin = false, $plain_text = false ) {
		$order_status = version_compare( WC_VERSION, '3.0', '<' ) ? $order->status : $order->get_status();

		if ( $sent_to_admin && ! in_array( $order_status, array( 'processing', 'completed' ) ) ) {
			return;
		}

		$downloads = WC_Subscription_Downloads::get_order_downloads( $order );

		if ( $downloads && $plain_text ) {
			$html = apply_filters( 'woocommerce_subscription_downloads_my_downloads_title', __( 'Available downloads', 'woocommerce-subscription-downloads' ) );
			$html .= PHP_EOL . PHP_EOL;

			foreach ( $downloads as $download ) {
				$html .= $download['name'] . ': ' . $download['download_url'] . PHP_EOL;
			}

			$html .= PHP_EOL;
			$html .= '****************************************************';
			$html .= PHP_EOL . PHP_EOL;

			echo $html;

		} elseif ( $downloads && ! $plain_text ) {
			$html = '<h2>' . apply_filters( 'woocommerce_subscription_downloads_my_downloads_title', __( 'Available downloads', 'woocommerce-subscription-downloads' ) ) . '</h2>';

			$html .= '<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">';
				$html .= '<tr>';
					$html .= '<td valign="top">';
						$html .= '<ul class="digital-downloads">';
			foreach ( $downloads as $download ) {
				$html .= sprintf( '<li><a href="%1$s" title="%2$s" target="_blank">%2$s</a></li>', $download['download_url'], $download['name'] );
			}
						$html .= '</ul>';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</table>';

			echo $html;
		}
	}
}

new WC_Subscription_Downloads_Order;
