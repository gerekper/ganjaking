<?php
/**
 * Notice template.
 *
 * @since 3.6.0
 * @package WP_Smush
 *
 * @var string $classes  Notice classes.
 * @var string $message  Notice message.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-notice-top sui-can-dismiss <?php echo esc_attr( $classes ); ?>">
	<p><?php echo wp_kses_post( $message ); ?></p>
	<span class="sui-notice-dismiss">
		<a role="button" href="#" aria-label="<?php esc_attr_e( 'Dismiss', 'wp-smushit' ); ?>" class="sui-icon-check"></a>
	</span>
</div>