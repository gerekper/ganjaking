<?php use Premmerce\WooCommercePinterest\Admin\Product\PinAll\PinAllManager;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Used data
 *
 * @var array $data
 */
?>

<style>
	.woocommerce-importer-progress__progressbar {
		width: 100%;
		height: 42px;
		margin: 0 auto 24px;
		display: block;
		border: none;
		background: #f5f5f5;
		border: 2px solid #eee;
		border-radius: 4px;
		padding: 0;
		box-shadow: 0 1px 0 0 rgba(255, 255, 255, 0.2);
	}

	.woocommerce-importer-progress__progressbar::-webkit-progress-bar {
		background: transparent none;
		border: 0;
		border-radius: 4px;
		padding: 0;
		box-shadow: none;
	}

	.woocommerce-importer-progress__progressbar[value]::-webkit-progress-value {
		border-radius: 3px;
		box-shadow: inset 0 1px 1px 0 rgba(255, 255, 255, 0.4);
		background: #a46497;
		background: linear-gradient(to bottom, #a46497, #66405f), #a46497;
		transition: width 1s ease;
	}
</style>
<tr valign="top">
	<th scope="row" class="titledesc">
		<button
				class="button"
				id="start_pin_all_process_button"
				data-url="
				<?php 
				echo esc_attr( add_query_arg( array(
					'nonce'  => wp_create_nonce( PinAllManager::START_PIN_PROCESS_ACTION ),
					'action' => PinAllManager::START_PIN_PROCESS_ACTION,
				), admin_url( 'admin-post.php' ) ) ) 
				?>
				">
			<?php echo esc_attr( $data['title'] ); ?>
		</button>
	</th>

	<td class="forminp">

	</td>
</tr>
<tr valign="top" style="display: none">
	<th scope="row" class="titledesc" colspan="2">

		<h3><?php esc_attr_e( 'Pinning...', 'woocommerce-pinterest' ); ?></h3>

		<div class="woocommerce-importer-progress"
			 data-action="<?php echo esc_attr( PinAllManager::AJAX_GET_UPDATE_ACTION ); ?>"
			 data-nonce="<?php echo esc_attr( wp_create_nonce( PinAllManager::AJAX_GET_UPDATE_ACTION ) ); ?>">
			<progress class="woocommerce-importer-progress__progressbar" max="100" value="0"></progress>

			<h4>
			<?php
			/* translators: '%s replaced with category name' */
			printf( esc_attr__( 'Category: %s', 'woocommerce-pinterest' ),
					'<b><span class="woocommerce-importer-progress__category"></span></b>'
				); 
			?>
			</h4>
			<p>
				<?php
				/* translators: '%1$s replaced with count of products wrapper %2$s replaced with total wrapper %3$s replaced with percentage part wrapper' */
				printf( esc_attr__( 'Pushed %1$s product of %2$s (%3$s%%)', 'woocommerce-pinterest' ),
					'<span class="woocommerce-importer-progress__pushed"></span>',
					'<span class="woocommerce-importer-progress__total"></span>',
					'<span class="woocommerce-importer-progress__percents"></span>'
				); 
				?>
			</p>
		</div>
	</th>
</tr>



