<?php
/**
 * Current Affiliate
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.5
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcaf yith-wcaf-current-affiliate woocommerce <?php echo $show_gravatar ? 'with-gravatar' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">

	<?php if ( ! $current_affiliate ) : ?>
		<p class="no-affiliate-message">
			<?php echo esc_html( $no_affiliate_message ); ?>
		</p>
	<?php else : ?>

		<?php if ( 'yes' === $show_gravatar ) : ?>
			<div class="affiliate-gravatar">
				<?php echo get_avatar( $current_affiliate['user_id'], 80 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

		<div class="affiliate-info">

			<div class="affiliate-name">
				<?php echo esc_html( $user->nickname ); ?>
				<?php if ( 'yes' === $show_real_name ) : ?>
					<span class="affiliate-real-name">
						<?php
							// translators: 1. First name 2. Last name.
							echo esc_html( sprintf( '(%s %s)', $user->first_name, $user->last_name ) );
						?>
					</span>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === $show_email ) : ?>
				<div class="affiliate-email">
					<a href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><?php echo esc_html( $user->user_email ); ?></a>
				</div>
			<?php endif; ?>

		</div>

	<?php endif; ?>

</div>
