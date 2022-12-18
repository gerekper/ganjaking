<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @var Vc_Column_Offset $param
 * @var Vc_Column_Offset $sizes ::$size_types
 */
global $porto_settings;

if ( isset( $porto_settings['container-width'] ) && (int) $porto_settings['container-width'] >= 1360 ) {
	$xxl = (int) $porto_settings['container-width'];
} else {
	$xxl = 1360;
}

$layouts    = array(
	'xs' => 'portrait-smartphones',
	'sm' => 'portrait-tablets',
	'md' => 'landscape-tablets',
	'lg' => 'default',
	'xl' => 'large-desktop',
);
$sizes      = array(
	'xl' => 'Extra Large (>= ' . ( $xxl + ( isset( $porto_settings['grid-gutter-width'] ) ? (int) $porto_settings['grid-gutter-width'] : 30 ) * 2 ) . 'px)',
	'lg' => 'Large (>= ' . ( $porto_settings['container-width'] + $porto_settings['grid-gutter-width'] ) . 'px)',
	'md' => 'Medium (>= 992px)',
	'sm' => 'Small (>= 768px)',
	'xs' => 'Extra small',
);
$custom_tag = 'script';
?>
<div class="vc_column-offset" data-column-offset="true">
	<?php if ( '1' === vc_settings()->get( 'not_responsive_css' ) ) : ?>
		<div class="wpb_alert wpb_content_element vc_alert_rounded wpb_alert-warning">
			<div class="messagebox_text">
				<?php /* translators: WPBakery admin settings page url */ ?>
				<p><?php printf( esc_html__( 'Responsive design settings are currently disabled. You can enable them in WPBakery Page Builder %1$ssettings page%2$s by unchecking "Disable responsive content elements".', 'js_composer' ), '<a href="' . esc_url( admin_url( 'admin.php?page=vc-general' ) ) . '">', '</a>' ); ?></p>
			</div>
		</div>
	<?php endif ?>
	<input name="<?php echo esc_attr( $settings['param_name'] ); ?>"
			class="wpb_vc_param_value <?php echo esc_attr( $settings['param_name'] ); ?>
	<?php echo esc_attr( $settings['type'] ); ?> '_field" type="hidden" value="<?php echo esc_attr( $value ); ?>"/>
	<table class="vc_table vc_column-offset-table">
		<tr>
			<th>
				<?php esc_html_e( 'Device', 'js_composer' ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Offset', 'js_composer' ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Width', 'js_composer' ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Hide on device?', 'js_composer' ); ?>
			</th>
		</tr>
		<?php foreach ( $sizes as $key => $size ) : ?>
			<tr class="vc_size-<?php echo esc_attr( $key ); ?>">
				<td class="vc_screen-size vc_screen-size-<?php echo esc_attr( $key ); ?>">
					<span title="<?php echo esc_attr( $size ); ?>">
						<i class="vc-composer-icon vc-c-icon-layout_<?php echo isset( $layouts[ $key ] ) ? esc_attr( $layouts[ $key ] ) : esc_attr( $key ); ?>"></i>
					</span>
				</td>
				<td>
					<?php
					// @codingStandardsIgnoreLine
					print $param->offsetControl( $key );
					?>
				</td>
				<td>
					<?php
					// @codingStandardsIgnoreLine
					print $param->sizeControl( $key );
					?>
				</td>
				<td>
					<label>
						<input type="checkbox" name="vc_hidden-<?php echo esc_attr( $key ); ?>"
								value="yes"<?php echo in_array( 'vc_hidden-' . $key, $data, true ) ? ' checked="true"' : ''; ?>
								class="vc_column_offset_field">
					</label>
				</td>
			</tr>
		<?php endforeach ?>
	</table>
</div>
<<?php echo esc_attr( $custom_tag ); ?>>
	window.VcI8nColumnOffsetParam =
	<?php
	echo wp_json_encode(
		array(
			'inherit'         => esc_html__( 'Inherit: ', 'js_composer' ),
			'inherit_default' => esc_html__( 'Inherit from default', 'js_composer' ),
		)
	)
	?>
	;
</<?php echo esc_attr( $custom_tag ); ?>>
