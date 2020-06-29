<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) : ?>

	<?php if( in_array( mfn_opts_get( 'shop-product-style' ), array( 'tabs', 'wide tabs', 'modern' ) ) ): ?>

		<div class="jq-tabs tabs_wrapper">

			<ul class="tabs wc-tabs" role="tablist">
				<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<a href="#tab-<?php echo esc_attr( $key ); ?>">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php
				$output_tabs = '';

				foreach ( $product_tabs as $key => $product_tab ){
					echo '<div id="tab-'. $key .'">';
						if ( isset( $product_tab['callback'] ) ) {
							call_user_func( $product_tab['callback'], $key, $product_tab );
						}
					echo '</div>';
				}
			?>

		</div>

	<?php else: ?>

		<div class="accordion">
			<div class="mfn-acc accordion_wrapper open1st">
				<?php foreach ( $product_tabs as $key => $product_tab ) : ?>

					<div class="question">

						<div class="title">
							<i class="icon-plus acc-icon-plus"></i><i class="icon-minus acc-icon-minus"></i>
							<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
						</div>

						<div class="answer">
							<?php
								if ( isset( $product_tab['callback'] ) ) {
									call_user_func( $product_tab['callback'], $key, $product_tab );
								}
							?>
						</div>

					</div>

				<?php endforeach; ?>
			</div>
		</div>

	<?php endif; ?>

<?php endif; ?>
