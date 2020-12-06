<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Used vars list
 *
 * @var string $domain
 * @var bool $is_already_verified
 * @var string $key
 * @var array $data
 */

?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $data['title'] ); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $data['title'] ); ?></span></legend>
			<input class="input-text regular-input " type="text" readonly name="<?php echo esc_attr( $key ); ?>"
				   id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $domain ); ?>"/>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php' ) . '?action=woocommerce_pinterest_verify_domain' ); ?>"
				<?php echo $is_already_verified ? 'data-already-verified' : ''; ?>
			   class="button"><?php esc_html_e( 'Verify domain', 'woocommerce-pinterest' ); ?></a>
		</fieldset>
	</td>
</tr>
<script>
	jQuery('[data-already-verified]').click(function (e) {
		e.preventDefault();
		alert('The domain is already verified');
	})
</script>
