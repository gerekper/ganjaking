<?php
/**
 * Select Badge Image Field
 *
 * @var array $args Field Args.
 *
 * @package YITH\BadgeManagement\Views
 */

$uploaded_image_id = '';

if ( defined( 'YITH_WCBM_PREMIUM' ) ) {
	if ( ! empty( $args['allow_upload'] ) && 'yes' === $args['allow_upload'] ) {
		$badge             = yith_wcbm_get_badge_object();
		$uploaded_image_id = $badge->get_uploaded_image_id();
	}
}

if ( empty( $args['value'] ) ) {
	$args['value'] = current( $args['local_badges'] );
}

$has_license = yith_wcbm_has_active_license();

?>

<div class="yith-wcbm-badge-library-wrapper">
	<?php if ( ! empty( $args['custom_label'] ) ) : ?>
		<div class="yith-wcbm-badge-library-title">
			<?php echo $args['custom_label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
	<div class="yith-wcbm-badge-library-tabs">
		<div class="yith-wcbm-badge-library-tab yith-wcbm-badge-library-tab--selected yith-wcbm-badge-library-tab__library"><?php echo esc_html_x( 'Library', '[ADMIN] Badges Library tab title', 'yith-woocommerce-badges-management' ); ?></div>
		<div class="yith-wcbm-badge-library-tab yith-wcbm-badge-library-tab__more"><?php echo esc_html_x( 'Get more badges', '[ADMIN] Badges Library tab title', 'yith-woocommerce-badges-management' ); ?></div>
	</div>

	<div class="yith-wcbm-badge-list-container yith-wcbm-badge-library__badges" id="<?php echo esc_attr( $args['id'] ); ?>" data-assets-url="<?php echo esc_url_raw( $args['url'] ); ?>">

		<?php if ( ! empty( $args['allow_upload'] ) && 'yes' === $args['allow_upload'] ) : ?>
			<?php
			$uploader_classes = array(
				'yith-wcbm-badge-list-element',
				'yith-wcbm-upload-image',
			);
			if ( absint( $uploaded_image_id ) ) {
				$uploader_classes[] = 'yith-wcbm-upload-image--uploaded';
			}
			if ( $uploaded_image_id && 'upload' === $args['value'] ) {
				$uploader_classes[] = esc_attr( 'yith-wcbm-badge-list-element--selected ' );
			}
			if ( ! $has_license ) {
				$uploader_classes[] = esc_attr( 'yith-wcbm-upload-image--locked yith-plugin-fw__tips ' );
			}
			?>
			<label class="<?php echo esc_attr( implode( ' ', $uploader_classes ) ); ?>" data-tip="<?php echo ! $has_license ? esc_html_x( 'Need a valid license to upload an image', '[ADMIN] ToolTip that shows up when hovering the image uploader without a valid license', 'yith-woocommerce-badges-management' ) : ''; ?>">
				<span class="yith-wcbm-upload-image-field-content">
						<img src="<?php echo absint( $uploaded_image_id ) ? esc_url_raw( wp_get_attachment_image_url( absint( $uploaded_image_id ), 'full' ) ) : ''; ?>" alt="<?php echo esc_html_x( 'Uploaded image', '[ADMIN] alt tag for uploaded attachment in image badge type', 'yith-woocommerce-badges-management' ); ?>">
						<span class="yith-wcbm-upload-image-field">
							<span class="yith-icon yith-icon-upload"></span>
							<?php esc_html_e( 'Upload image', 'yith-woocommerce-badges-management' ); ?>
						</span>
				</span>
				<?php if ( $has_license ) : ?>
					<input type="radio" id="yith-wcbm-upload-image" value="upload" name="<?php echo esc_attr( $args['name'] ); ?>" <?php checked( 'upload', $args['value'] ); ?>>
					<input type="hidden" id="yith-wcbm-upload-image-attachment-url" value="<?php echo esc_url_raw( wp_get_attachment_image_url( absint( $uploaded_image_id ), 'full' ) ); ?>">
					<input type="hidden" id="yith-wcbm-upload-image-attachment-id" name="<?php echo esc_attr( str_replace( $args['id'], '_uploaded_' . $args['id'] . '_id', $args['name'] ) ); ?>" value="<?php echo esc_attr( $uploaded_image_id ); ?>">
				<?php endif; ?>
			</label>
		<?php endif; ?>

		<?php foreach ( $args['library'] as $badge => $badge_url ) : ?>
			<label class="yith-wcbm-badge-list-element yith-wcbm-badge-library__badge <?php echo $badge === $args['value'] ? 'yith-wcbm-badge-list-element--selected' : ''; ?>" style="background-image: url('<?php echo esc_url_raw( $badge_url ); ?>')">
				<input type="radio" class="yith-wcbm-badge-list-element--input" value="<?php echo esc_html( $badge ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" <?php checked( $badge, $args['value'] ); ?>>
			</label>
		<?php endforeach; ?>

		<script type="text/html" id="tmpl-yith-wcbm-badge-library-<?php echo esc_attr( substr( $args['id'], 1 ) ); ?>">
			<label class="yith-wcbm-badge-list-element yith-wcbm-badge-library__badge" style="background-image: url('{{data.previewUrl}}')">
				<input type="radio" class="yith-wcbm-badge-list-element--input" value="{{data.value}}" name="<?php echo esc_attr( $args['name'] ); ?>">
			</label>
		</script>

	</div>

	<div class="yith-wcbm-badge-list-container yith-wcbm-badge-more__badges <?php echo ! $has_license ? 'yith-wcbm-has-no-license' : ''; ?>" id="<?php echo esc_attr( $args['id'] ); ?>-more">
		<div class="yith-wcbm-badge-more__info">
				<span class="yith-wcbm-badge-more__title">
					<?php
					// translators: %d is the number of extra badges, Ex. Extra badges (16).
					echo sprintf( esc_html__( 'Extra badges (%d)', 'yith-woocommerce-badges-management' ), count( $args['importable_badges'] ) );
					?>
				</span>
			<?php if ( ! empty( $args['importable_badges'] ) && $has_license ) : ?>
				<button class="yith-wcbm-badge-more__add-all-button yith-plugin-fw__button--primary"><?php esc_html_e( 'Add all badges to your library', 'yith-woocommerce-badges-management' ); ?></button>
			<?php endif; ?>
			<div class="yith-wcbm-badge-more__info-content">
				<span class="yith-wcbm-badge-more__description"><?php esc_html_e( 'Add the badges to your library to use them in your shop.', 'yith-woocommerce-badges-management' ); ?></span>
				<?php if ( ! $has_license ) : ?>
					<div class="yith-wcbm-badge-more__active-your-license">
						<span class="yith-wcbm-badge-more__active-your-license-text">
							<?php
							// Translators: the placeholders are tags to bold the text; %1$s - <b>; %2$s - </b>.
							echo sprintf( esc_html__( '%1$sWarning:%2$s Only users with a valid license can get our extra badges.', 'yith-woocommerce-badges-management' ), '<b>', '</b>' );
							?>
						</span>
						<a href="<?php echo esc_url_raw( yith_wcbm_get_license_activation_url() ); ?>" class="yith-wcbm-badge-more__active-your-license-button" target="_blank"><?php esc_html_e( 'Activate your license', 'yith-woocommerce-badges-management' ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( ! empty( $args['importable_badges'] ) ) : ?>
			<?php foreach ( $args['importable_badges'] as $badge ) : ?>
				<label class="yith-wcbm-badge-list-element yith-wcbm-badge-more__badge <?php echo $has_license ? '' : 'yith-wcbm-badge-list-element--disabled yith-plugin-fw__tips'; ?>" style="background-image: url('<?php echo esc_url_raw( $badge['previewUrl'] ); ?>')" data-badge-id="<?php echo esc_attr( $badge['id'] ); ?>" data-badge-preview-url="<?php echo esc_url_raw( $badge['previewUrl'] ); ?>"
						data-tip="<?php echo ! $has_license ? esc_html_x( 'Need a valid license to use this badge', '[ADMIN] ToolTip that shows up when hovering the badge in "Get more badges" tab in badge selector without a valid license', 'yith-woocommerce-badges-management' ) : ''; ?>">
					<span class="yith-wcbm-badge-more__add-to-library-button <?php echo $has_license ? '' : 'yith-wcbm-badge-more__add-to-library-button--locked'; ?>">
						<span class=" <?php echo $has_license ? 'yith-wcbm-badge-more__add-to-library-button-loader' : 'yith-icon yith-icon-lock'; ?>"></span>
						<span class="yith-wcbm-badge-more__add-to-library-button-text"><?php esc_html_e( 'Add', 'yith-woocommerce-badges-management' ); ?></span>
					</span>
				</label>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="yith-wcbm-badge-more-empty-state">
				<div class="yith-wcbm-badge-more-empty-state__icon">
					<?php yith_wcbm_get_icon( 'empty-extra-badges', true ); ?>
				</div>
				<div class="yith-wcbm-badge-more-empty-state__message">
					<?php
					// Translators: %s is the line break '<br>'.
					echo sprintf( esc_html__( "All badges are already added in your library.%sDon't worry: soon we will add new badges here!", 'yith-woocommerce-badges-management' ), '<br>' );
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>

</div>
