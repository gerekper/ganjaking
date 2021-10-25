<?php
/**
 * Integrations meta box
 *
 * @package WP_Smush
 *
 * @var array  $basic_features    Basic features array.
 * @var bool   $is_pro            Is PRO user or not.
 * @var array  $integration_group Integration group.
 * @var array  $settings          Settings array.
 * @var string $upsell_url        Upsell URL.
 *
 * @var Abstract_Page $this
 */

use Smush\App\Abstract_Page;

if ( ! defined( 'WPINC' ) ) {
	die;
}

foreach ( $integration_group as $name ) {
	$disable = apply_filters( 'wp_smush_integration_status_' . $name, false ); // Disable setting.
	$upsell  = ! in_array( $name, $basic_features, true ) && ! $is_pro; // Gray out row, disable setting.
	$value   = $upsell || empty( $settings[ $name ] ) || $disable ? false : $settings[ $name ];
	do_action( 'wp_smush_render_setting_row', $name, $value, $disable, $upsell );
}
?>

<?php if ( ! $is_pro ) : ?>
	<div class="sui-box-settings-row sui-upsell-row">
		<img class="sui-image sui-upsell-image sui-upsell-image-smush integrations-upsell-image" alt="" style="width: 80px"
			src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-graphic-integrations-upsell.png' ); ?>"
			srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-graphic-integrations-upsell@2x.png' ); ?> 2x">
		<div class="sui-notice sui-notice-purple smush-upsell-notice">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<p>
						<?php
						printf(
							/* translators: %1$s - a href tag, %2$s - a href closing tag */
							esc_html__( 'Smush Pro supports hosting images on Amazon S3 and optimizing NextGen Gallery images directly through NextGen Gallery settings. %1$sTry it free%2$s with a WPMU DEV membership today!', 'wp-smushit' ),
							'<a href="' . esc_url( $upsell_url ) . '" target="_blank" title="' . esc_html__( 'Try Smush Pro for FREE', 'wp-smushit' ) . '">',
							'</a>'
						);
						?>
					</p>
					<p>
						<a href="<?php echo esc_url( $upsell_url ); ?>" target="_blank" class="sui-button sui-button-purple">
							<?php esc_html_e( 'Try Smush Pro for Free', 'wp-smushit' ); ?>
						</a>
					</p>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>