<?php
/**
 * Admin order file status update
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var YWQA_Question $question */
$_product = wc_get_product( $question->product_id );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<div class="ywqa-header">
		<div class="ywqa-product-picture">
			<?php echo get_the_post_thumbnail( $question->product_id, 'thumbnail' ); ?>
		</div>

		<div class="ywqa-description">
			<?php echo sprintf( __( "There is a new question submitted about the product <b>%s</b>.", 'yith-woocommerce-questions-and-answers' ),
				'<a href="' . get_permalink( $question->product_id ) . '">' . $_product->get_title() . '</a>' ); ?>
		</div>
	</div>

	<div class="ywqa-content">
		<div class="ywqa-content-intro"><?php esc_html_e( "The question ", 'yith-woocommerce-questions-and-answers' ); ?></div>
		<div class="ywqa-content-description"><?php echo $question->content; ?></div>
	</div>

	<div class="ywqa-bottom">
		<a class="ywqa-view-product" href="<?php echo get_permalink( $question->product_id ); ?>">
			<?php esc_html_e( 'View product', 'yith-woocommerce-questions-and-answers' ); ?></a>
	</div>

<?php do_action( 'woocommerce_email_footer' );