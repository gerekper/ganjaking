<?php
/**
 * Downloads
 *
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>
<h3 class="account-sub-title mb-3 mt-2"><i class="Simple-Line-Icons-cloud-download align-middle m-r-sm"></i><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h3>
<?php if ( $has_downloads ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	<?php if ( version_compare( WC_VERSION, '3.2', '>=' ) ) : ?>

		<?php do_action( 'woocommerce_available_downloads', $downloads ); ?>

	<?php else : ?>

		<table class="woocommerce-MyAccount-downloads shop_table shop_table_responsive">
			<thead>
				<tr>
					<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
						<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<?php foreach ( $downloads as $download ) : ?>
				<tr>
					<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
						<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php
							if ( has_action( 'woocommerce_account_downloads_column_' . $column_id ) ) {
								do_action( 'woocommerce_account_downloads_column_' . $column_id, $download );
							} else {
								switch ( $column_id ) {
									case 'download-product':
										?>
											<a href="<?php echo esc_url( get_permalink( $download['product_id'] ) ); ?>">
												<?php echo esc_html( $download['product_name'] ); ?>
											</a>
											<?php
										break;
									case 'download-file':
										?>
											<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="woocommerce-MyAccount-downloads-file button alt wc-action-btn px-4">
												<?php echo esc_html( $download['file']['name'] ); ?>
											</a>
											<?php
										break;
									case 'download-remaining':
										echo is_numeric( $download['downloads_remaining'] ) ? esc_html( $download['downloads_remaining'] ) : __( '&infin;', 'woocommerce' );
										break;
									case 'download-expires':
										?>
											<?php if ( ! empty( $download['access_expires'] ) ) : ?>
												<time datetime="<?php echo date( 'Y-m-d', strtotime( $download['access_expires'] ) ); ?>" title="<?php echo esc_attr( strtotime( $download['access_expires'] ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ); ?></time>
											<?php else : ?>
												<?php esc_html_e( 'Never', 'woocommerce' ); ?>
											<?php endif; ?>
											<?php
										break;
									case 'download-actions':
										?>
											<?php
											$actions = array(
												'download' => array(
													'url'  => $download['download_url'],
													'name' => __( 'Download', 'woocommerce' ),
												),
											);
											$actions = apply_filters( 'woocommerce_account_download_actions', $actions, $download );
											if ( $actions ) {
												foreach ( $actions as $key => $action ) {
													echo '<a href="' . esc_url( $action['url'] ) . '" class="button woocommerce-Button wc-action-btn px-4 ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
												}
											}
											?>
											<?php
										break;
								}
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</table>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info">
		<p><?php esc_html_e( 'No downloads available yet.', 'woocommerce' ); ?></p>
		<a class="woocommerce-Button button wc-action-btn mt-3 px-4" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Go shopping', 'woocommerce' ); ?>
		</a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>
