<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $comment;
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );

if ( '0' === $comment->comment_approved ) { ?>

	<p class="meta">
		<em class="woocommerce-review__awaiting-approval">
			<?php esc_html_e( 'Your review is awaiting approval', 'woocommerce' ); ?>
		</em>
	</p>

<?php } else { ?>

	<p class="meta">
		<strong class="woocommerce-review__author"><?php comment_author(); ?></strong>
		<?php
		if ( 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) && $verified ) {
			echo '<em class="woocommerce-review__verified verified">(' . esc_html__( 'verified owner', 'woocommerce' ) . ')</em> ';
		}
		?>
		<span class="woocommerce-review__dash">&ndash;</span> <time class="woocommerce-review__published-date" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php echo esc_html( get_comment_date( wc_date_format() ) ); ?></time>
	</p>

	<?php
}
