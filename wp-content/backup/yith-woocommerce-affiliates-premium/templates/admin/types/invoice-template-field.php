<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>">
		<?php
		$local_file = $this->get_theme_template_file( $template );
		$template_file = yith_wcaf_locate_template( $template );
		$template_dir = WC()->template_path() . 'yith-wcaf';
		?>
        <div class="template" id="<?php echo esc_html( $value['id'] ) ?>">

			<?php if ( file_exists( $local_file ) ) : ?>
                <p>
                    <a href="#" class="button toggle_editor"></a>

					<?php if ( is_writable( $local_file ) ) : // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable ?>
                        <a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array(
							'move_template',
							'saved'
						), add_query_arg( 'delete_template', $template ) ), 'yith_wcaf_template_nonce', '_yith_wcaf_nonce' ) ); ?>" class="delete_template button">
							<?php esc_html_e( 'Delete template file', 'yith-woocommerce-affiliates' ); ?>
                        </a>
					<?php endif; ?>

					<?php
					/* translators: %s: Path to template file */
					printf( esc_html__( 'This template has been overridden by your theme and can be found in: %s.', 'yith-woocommerce-affiliates' ), '<code>' . esc_html( trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ) . '</code>' );
					?>
                </p>

                <div class="editor" style="display:none">
                    <?php if( is_writable( $local_file ) ): ?>
                        <input type="hidden" name="save_template[]" value="<?php echo $template; ?>"/>
                    <?php endif;?>
                    <textarea class="code" cols="25" rows="20"
						<?php
						if ( ! is_writable( $local_file ) ) : // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable
							?>
                            readonly="readonly" disabled="disabled"
						<?php else : ?>
                            name="<?php echo esc_attr( $template ) . '_code'; ?>"<?php endif; ?>><?php echo esc_html( file_get_contents( $local_file ) ); ?></textarea>
                </div>
			<?php elseif ( file_exists( $template_file ) ) : ?>
                <p>
                    <a href="#" class="button toggle_editor"></a>

					<?php
					$target_dir = get_stylesheet_directory() . '/' . $template_dir;

					if ( is_writable( $target_dir ) ) : // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable
						?>
                        <a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array(
							'delete_template',
							'saved'
						), add_query_arg( 'move_template', $template ) ), 'yith_wcaf_template_nonce', '_yith_wcaf_nonce' ) ); ?>" class="button">
							<?php esc_html_e( 'Copy file to theme', 'yith-woocommerce-affiliates' ); ?>
                        </a>
					<?php endif; ?>

					<?php
					/* translators: 1: Path to template file 2: Path to theme folder */
					printf( esc_html__( 'To override and edit this template copy %1$s to your theme folder: %2$s.', 'yith-woocommerce-affiliates' ), '<code>' . esc_html( plugin_basename( $template_file ) ) . '</code>', '<code>' . esc_html( trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ) . '</code>' );
					?>
                </p>

                <div class="editor" style="display:none">
                    <textarea class="code" readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo esc_html( file_get_contents( $template_file ) ); ?></textarea>
                </div>
			<?php else : ?>
                <p><?php esc_html_e( 'File was not found.', 'yith-woocommerce-affiliates' ); ?></p>
			<?php endif; ?>
        </div>
    </td>
</tr>