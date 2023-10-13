<?php
/**
 * Single Module
 *
 * @var array $module_data The module data.
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit();

$key           = $module_data['key'] ?? '';
$name          = $module_data['name'] ?? '';
$description   = $module_data['description'] ?? '';
$is_available  = $module_data['is_available'] ?? false;
$is_active     = $module_data['is_active'] ?? false;
$needs_reload  = $module_data['needs_reload'] ?? false;
$always_active = $module_data['always_active'] ?? false;
$hidden        = $module_data['hidden'] ?? false;

if ( $hidden ) {
	return;
}

$module_toggle_id = 'module__toggle-active__' . $key;

?>
<div class="module" data-module="<?php echo esc_attr( $key ); ?>" data-needs-reload="<?php echo esc_attr( $needs_reload ); ?>">
	<header>
		<h3><?php echo esc_html( $name ); ?></h3>
		<?php if ( ! $is_available ) : ?>
			<i class="yith-icon yith-icon-lock"></i>
		<?php endif; ?>
	</header>
	<div class="module__description"><?php echo wp_kses_post( $description ); ?></div>
	<?php if ( $is_available && ! $always_active ) : ?>
		<div class="module__activation">
			<label class="module__activation__label" for="<?php echo esc_attr( $module_toggle_id ); ?>">
				<?php esc_html_e( 'Enable module', 'yith-booking-for-woocommerce' ); ?>
			</label>
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => $module_toggle_id,
					'type'  => 'onoff',
					'class' => 'module__active-toggle',
					'value' => $is_active,
				),
				true
			);
			?>
		</div>
	<?php endif; ?>
</div>
