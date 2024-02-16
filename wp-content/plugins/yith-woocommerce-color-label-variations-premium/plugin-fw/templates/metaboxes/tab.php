<?php
/**
 * The Template for displaying meta-box tabs.
 *
 * @var array  $tabs  The tabs.
 * @var string $class The CSS Class.
 * @package YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $post;
$classes  = apply_filters( 'yith_plugin_fw_metabox_class', $class, $post );
$classes  = yith_plugin_fw_remove_duplicate_classes( $classes );
$ul_style = count( $tabs ) <= 1 ? 'display:none;' : '';
$i        = 0;
do_action( 'yit_before_metaboxes_tab' );

?>
	<div class="yith-plugin-fw metaboxes-tab <?php echo esc_attr( $classes ); ?>">
		<?php do_action( 'yit_before_metaboxes_labels' ); ?>
		<ul class="metaboxes-tabs clearfix" style="<?php echo esc_attr( $ul_style ); ?>">
			<?php foreach ( $tabs as $key => $_tab ) : ?>

				<?php
				if ( empty( $_tab['fields'] ) ) {
					continue;
				}
				$anchor_id = 'yith-plugin-fw-metabox-tab-' . urldecode( $key ) . '-anchor';

				// Parse deps for the tab visibility.
				if ( isset( $_tab['deps'] ) ) {
					$_tab['deps']['target-id'] = isset( $_tab['deps']['target-id'] ) ? $_tab['deps']['target-id'] : $anchor_id;
					if ( isset( $_tab['deps']['id'] ) && strpos( $_tab['deps']['id'], '_' ) !== 0 ) {
						$_tab['deps']['id'] = '_' . $_tab['deps']['id'];
					}
					if ( isset( $_tab['deps']['ids'] ) && strpos( $_tab['deps']['ids'], '_' ) !== 0 ) {
						$_tab['deps']['ids'] = '_' . $_tab['deps']['ids'];
					}

					$_tab['deps']['type'] = 'hideme';
				}

				$class = ! $i ? 'tabs' : '';
				$i ++;
				?>
				<li id="<?php echo esc_attr( $anchor_id ); ?>" class="<?php echo esc_attr( $class ); ?>" <?php echo yith_field_deps_data( $_tab ); ?>>
					<a href="#<?php echo esc_attr( urldecode( $key ) ); ?>">
						<?php
						// This is not escaped to allow, for example, svg icons and custom html in the tab title.
						// TODO: escape it and provide a specific way to add icons.
						echo $_tab['label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php do_action( 'yit_after_metaboxes_labels' ); ?>

		<?php
		if ( isset( $_tab['label'] ) ) {
			do_action( 'yit_before_metabox_option_' . urldecode( $key ) );
		}
		?>

		<?php wp_nonce_field( 'metaboxes-fields-nonce', 'yit_metaboxes_nonce' ); ?>

		<?php foreach ( $tabs as $key => $_tab ) : ?>
			<div class="tabs-panel" id="<?php echo esc_attr( urldecode( $key ) ); ?>">
				<?php
				if ( empty( $_tab['fields'] ) ) {
					continue;
				}

				$_tab['fields'] = apply_filters( 'yit_metabox_' . $key . '_tab_fields', $_tab['fields'] );
				?>

				<?php foreach ( $_tab['fields'] as $id_tab => $field ) : ?>
					<?php
					$field_name = $field['name'];
					$field_name = str_replace( 'yit_metaboxes[', '', $field_name );
					$pos        = strpos( $field_name, ']' );
					if ( $pos ) {
						$field_name = substr_replace( $field_name, '', $pos, 1 );
					}
					$value                  = yit_get_post_meta( $post->ID, $field_name );
					$field['value']         = false === $value ? ( isset( $field['std'] ) ? $field['std'] : '' ) : $value;
					$field['checkboxgroup'] = ( 'checkbox' === $field['type'] && isset( $field['checkboxgroup'] ) ) ? ' ' . $field['checkboxgroup'] : '';
					$container_classes      = 'the-metabox ' . $field['type'] . $field['checkboxgroup'] . ' clearfix ';
					$extra_row_class        = isset( $field['extra_row_class'] ) ? $field['extra_row_class'] : '';

					$container_classes .= empty( $field['label'] ) ? 'no-label' : '';
					$container_classes .= ' ' . $extra_row_class;

					?>
					<div class="<?php echo esc_attr( $container_classes ); ?>">
						<?php
						$field_template_path = yith_plugin_fw_get_field_template_path( $field );
						if ( $field_template_path ) {
							$display_row                 = 'hidden' !== $field['type'];
							$display_row                 = isset( $field['yith-display-row'] ) ? ! ! $field['yith-display-row'] : $display_row;
							$field['display-field-only'] = in_array( $field['type'], array( 'hidden', 'html', 'sep', 'simple-text', 'title', 'list-table' ), true );

							if ( $display_row ) {

								$field_row_path = apply_filters( 'yith_plugin_fw_metabox_field_row_template_path', YIT_CORE_PLUGIN_TEMPLATE_PATH . '/metaboxes/field-row.php', $field );
								file_exists( $field_row_path ) && include $field_row_path;
							} else {
								yith_plugin_fw_get_field( $field, true );
							}
						} else {
							// Backward compatibility.
							$args       = apply_filters(
								'yit_fw_metaboxes_type_args',
								array(
									'basename' => YIT_CORE_PLUGIN_PATH,
									'path'     => '/metaboxes/types/',
									'type'     => $field['type'],
									'args'     => array( 'args' => $field ),
								)
							);
							$basename   = $args['basename'];
							$field_path = $args['path'];
							$field_type = $args['type'];
							$field_args = $args['args'];

							yit_plugin_get_template( $basename, $field_path . $field_type . '.php', $field_args );
						}
						?>
					</div>
				<?php endforeach ?>
			</div>
		<?php endforeach ?>
	</div>

<?php

do_action( 'yit_after_metaboxes_tab' );
