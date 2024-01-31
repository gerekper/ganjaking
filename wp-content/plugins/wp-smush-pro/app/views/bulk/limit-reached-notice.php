<?php
/**
 * Limit reached notice metabox on bulk smush page.
 *
 * @var bool $with_resume_button   With resume button or not.
 */
?>
<div id="smush-limit-reached-notice" class="sui-notice sui-notice-warning sui-hidden smush-limit-reached-notice">
	<div class="sui-notice-content">
		<div class="sui-notice-message">
			<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
			<p>
				<?php
				$is_directory_smush = 'smush-directory' === $this->get_slug();
				$upgrade_url        = $this->get_utm_link(
					array(
						'utm_campaign' => $is_directory_smush ? 'smush_directory_smush_paused_50_limit' : 'smush_bulk_smush_paused_50_limit',
					)
				);
				$bg_optimization    = WP_Smush::get_instance()->core()->mod->bg_optimization;
				$discount           = WP_Smush::get_instance()->admin()->get_plugin_discount();
				/* translators: %s: Discount */
				$discount_text = '<strong>' . sprintf( esc_html__( 'Get %s off when you upgrade today.', 'wp-smushit' ), $discount ) . '</strong>';
				printf(
					/* translators: %s1$d - bulk smush limit, %2$s - upgrade link, %3$s - <strong>, %4$s - </strong>, %5$s - Bulk Smush limit */
					esc_html__( 'The free version of Smush only allows you to compress %1$d images at a time. %2$s to compress %3$sunlimited images at once%4$s or click Resume to compress another %1$d images. %5$s', 'wp-smushit' ),
					Smush\Core\Core::MAX_FREE_BULK,
					'<a class="smush-upsell-link" href="' . esc_url( $upgrade_url ) . '" target="_blank"><strong>' . esc_html__( 'Upgrade to Smush Pro', 'wp-smushit' ) . '</strong></a>',
					'<strong>',
					'</strong>',
					$discount_text
				)
				?>
			</p>
		</div>
	</div>
</div>