<?php if (!defined('ABSPATH')) {
	die;
}
/**
 * Used vars list
 *
 * @var string $domain
 * @var string $key
 * @var array $data
 */
?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($data['title']); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html($data['title']); ?></span></legend>
			<input class="input-text regular-input " type="text" readonly name="<?php echo esc_attr($key); ?>"
				   id="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($domain); ?>"/>
			<a href="<?php echo esc_url(admin_url('admin-post.php') . '?action=woocommerce_pinterest_verify_domain'); ?>"
			   class="button"><?php esc_html_e('Verify domain', 'woocommerce-pinterest'); ?></a>
		</fieldset>
	</td>
</tr>
