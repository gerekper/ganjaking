<?php
/**
 * The Template for displaying the settings sections in YIT panel.
 *
 * @var YIT_Plugin_Panel $panel
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $wp_settings_sections, $wp_settings_fields;

$sections = $wp_settings_sections['yit'] ?? array();
?>

<?php foreach ( $sections as $section ) : ?>
	<?php
	$fields = isset( $wp_settings_fields ) && isset( $wp_settings_fields['yit'] ) && isset( $wp_settings_fields['yit'][ $section['id'] ] ) ? ( (array) $wp_settings_fields['yit'][ $section['id'] ] ) : array();
	?>
	<div class="yith-plugin-fw__panel__section">
		<?php if ( $section['title'] ) : ?>
			<div class="yith-plugin-fw__panel__section__title">
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<?php if ( ! empty( $section['description'] ) ) : ?>
					<div class="yith-plugin-fw__panel__section__description">
						<?php echo wp_kses_post( wpautop( wptexturize( $section['description'] ) ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( ! ! $fields ) : ?>
			<div class="yith-plugin-fw__panel__section__content">
				<?php foreach ( $fields as $field ) : ?>
					<?php
					$label_for   = $field['args']['label_for'] ?? '';
					$option      = $field['args']['option'] ?? array();
					$option_type = $option['type'] ?? '';
					$option_type = 'on-off' === $option_type ? 'onoff' : $option_type;
					$is_disabled = $option['is_option_disabled'] ?? false;
					$row_classes = array(
						'yith-plugin-fw__panel__option',
						$option_type ? 'yith-plugin-fw__panel__option--' . $option_type : false,
						$is_disabled ? 'yith-plugin-fw__panel__option--is-disabled' : '',
					);
					$row_classes = implode( ' ', array_filter( $row_classes ) );
					?>

					<div class="<?php echo esc_attr( $row_classes ); ?>" <?php echo yith_panel_field_deps_data( $option, $panel ); ?>>
						<div class="yith-plugin-fw__panel__option__label">
							<label for="<?php echo esc_attr( $label_for ); ?>"><?php echo wp_kses_post( $field['title'] ); ?></label>
							<?php $panel->get_template( 'panel-option-label-tags.php', array( 'field' => $option ) ); ?>
						</div>
						<div class="yith-plugin-fw__panel__option__content">
							<?php
							call_user_func( $field['callback'], $field['args'] );
							?>
						</div>
						<?php if ( ! empty( $option['desc'] ) ) : ?>
							<div class="yith-plugin-fw__panel__option__description">
								<?php echo wp_kses_post( $option['desc'] ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
