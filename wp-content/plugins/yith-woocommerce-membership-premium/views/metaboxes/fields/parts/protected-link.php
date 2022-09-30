<?php
/**
 * @var array      $link  The link.
 * @var int|string $index The ID.
 * @var string     $name  The name of the field.
 * @var array      $plans The plans
 */
defined( 'YITH_WCMBS' ) || exit();

$name_prefix = "{$name}[{$index}]";

if ( ! isset( $link ) ) {
	$link = array( 'name' => '', 'link' => '', 'membership' => array() );
}
?>

<div class="yith-wcmbs-admin-protected-link">
	<input type="hidden" class="yith-wcmbs-admin-protected-link__id" value="<?php echo esc_attr( $index ); ?>">
	<div class="yith-wcmbs-admin-protected-link__name">
		<div class="yith-wcmbs-admin-protected-link__label"><?php esc_html_e( 'Enter file name', 'yith-woocommerce-membership' ); ?></div>
		<div class="yith-wcmbs-admin-protected-link__content">
			<input class="yith-wcmbs-admin-protected-link__name-field" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[name]" value="<?php echo esc_attr( $link['name'] ); ?>"/>
		</div>
	</div>

	<div class="yith-wcmbs-admin-protected-link__url">
		<div class="yith-wcmbs-admin-protected-link__label">
			<?php
			echo wp_kses_post(
				sprintf(
					__( 'Enter file URL or %s', 'yith-woocommerce-membership' ),
					'<span class="yith-wcmbs-admin-protected-link__upload">' . __( 'Upload it', 'yith-woocommerce-membership' ) . '</span>'
				)
			);
			?>
		</div>
		<div class="yith-wcmbs-admin-protected-link__content">
			<input class="yith-wcmbs-admin-protected-link__url-field" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[link]" value="<?php echo esc_attr( $link['link'] ); ?>"/>
		</div>
	</div>

	<div class="yith-wcmbs-admin-protected-link__plans">
		<div class="yith-wcmbs-admin-protected-link__label"><?php esc_html_e( 'Available to members of plans', 'yith-woocommerce-membership' ); ?></div>
		<div class="yith-wcmbs-admin-protected-link__content">
			<select style="width:100%" class="yith-wcmbs-select2 yith-wcmbs-admin-protected-link__membership-field" name="<?php echo esc_attr( $name_prefix ) ?>[membership][]" multiple>
				<?php foreach ( $plans as $id => $title ): ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( in_array( $id, (array) $link['membership'] ) ); ?> ><?php echo esc_html( $title ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="yith-wcmbs-admin-protected-link__actions">
		<span class="yith-wcmbs-admin-protected-link__action__delete"></span>
	</div>
</div>