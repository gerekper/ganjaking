<?php

use Smush\Core\Settings;

$lossy_level_setting = Settings::get_instance()->get_lossy_level_setting();
$is_ultra_active     = Settings::LEVEL_ULTRA_LOSSY === $lossy_level_setting;
$is_super_active     = Settings::LEVEL_SUPER_LOSSY === $lossy_level_setting;
$is_pro              = WP_Smush::is_pro();
$is_dashboard_page   = 'smush' === $this->get_slug();
$location            = $is_dashboard_page ? 'dashboard_summary' : 'summary_box';
$modal_id            = "wp-smush-ultra-compression-modal__{$location}";
$class_names         = array();
if ( ! $is_pro ) {
	$class_names[] = 'smush-upsell-link';
} elseif ( $is_ultra_active ) {
	$class_names[] = 'sui-hidden';
}
?>
<li class="smush-summary-row-compression-type">
	<span class="sui-list-label"><?php esc_html_e( 'Smush Mode', 'wp-smushit' ); ?></span>
	<span class="sui-list-detail">
		<span class="wp-smush-current-compression-level sui-tag sui-tag-green"><?php echo esc_html( Settings::get_instance()->get_current_lossy_level_label() ); ?></span>
		<a href="<?php echo esc_url( $this->get_url( 'smush-bulk' ) ); ?>#lossy-settings-row" class="<?php echo esc_attr( join( ' ', $class_names ) ); ?>" <?php echo $is_pro ? '' : ' data-modal-open="' . esc_attr( $modal_id ) . '"'; ?> title="<?php esc_attr_e( 'Choose the level of compression that suits your needs.', 'wp-smushit' ); ?>">
			<?php esc_html_e( 'Improve page speed with Ultra', 'wp-smushit' ); ?>
		</a>
	</span>
</li>