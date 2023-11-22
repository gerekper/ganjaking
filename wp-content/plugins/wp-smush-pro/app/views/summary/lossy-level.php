<?php

use Smush\Core\Settings;

$lossy_level_setting = Settings::get_instance()->get_lossy_level_setting();
$is_ultra_active     = Settings::LEVEL_ULTRA_LOSSY === $lossy_level_setting;
$is_super_active     = Settings::LEVEL_SUPER_LOSSY === $lossy_level_setting;
$is_pro              = WP_Smush::is_pro();
$class_names         = array();
if ( ! $is_pro ) {
	$class_names[]     = 'smush-upsell-link wp-smush-upsell-ultra-compression';
	$is_dashboard_page = 'smush' === $this->get_slug();
	$location          = $is_dashboard_page ? 'dashboard_summary' : 'bulksmush_summary';
	$utm_link          = $this->get_utm_link(
		array(
			'utm_campaign' => "smush_ultra_{$location}",
		)
	);
} elseif ( $is_ultra_active ) {
	$class_names[] = 'sui-hidden';
}
?>
<li class="smush-summary-row-compression-type">
	<span class="sui-list-label"><?php esc_html_e( 'Smush Mode', 'wp-smushit' ); ?></span>
	<span class="sui-list-detail">
		<span class="wp-smush-current-compression-level sui-tag sui-tag-green"><?php echo esc_html( Settings::get_instance()->get_current_lossy_level_label() ); ?></span>
		<a target="<?php echo $is_pro ? '_self' : '_blank'; ?>" href="<?php echo isset( $utm_link ) ? esc_url( $utm_link ) : esc_url( $this->get_url( 'smush-bulk' ) ) . '#lossy-settings-row'; ?>" class="<?php echo esc_attr( join( ' ', $class_names ) ); ?>" title="<?php esc_attr_e( 'Choose the level of compression that suits your needs.', 'wp-smushit' ); ?>">
			<?php if ( $is_pro ) : ?>
				<?php esc_html_e( 'Improve page speed with Ultra', 'wp-smushit' ); ?>
			<?php else : ?>
				<?php esc_html_e( '5x your compression with Ultra', 'wp-smushit' ); ?>
				<span class="sui-icon-open-new-window" aria-hidden="true"></span>
			<?php endif; ?>
		</a>
	</span>
</li>