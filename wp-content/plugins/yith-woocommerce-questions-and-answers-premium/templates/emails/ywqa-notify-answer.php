<?php
/**
 * Admin order file status update
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var YWQA_Answer $answer */
$question = $answer->get_question();
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<div class="ywqa-header">

		<div class="ywqa-product-picture">
			<?php echo get_the_post_thumbnail( $answer->product_id, 'thumbnail' ); ?>
		</div>

		<div class="ywqa-description">
			<?php esc_html_e( "There is a new answer for the question: ", 'yith-woocommerce-questions-and-answers' ); ?>
			<span class="ywqa-the-content"><?php echo '"' . wc_trim_string( wp_strip_all_tags( $question->content ),
						apply_filters( 'ywqa_email_content_trimmed_length', 100 ) ) . '"'; ?></span>

		</div>
	</div>

	<div class="ywqa-content">
		<div class="ywqa-content-intro"><?php esc_html_e( "The answer", 'yith-woocommerce-questions-and-answers' ); ?></div>
		<div class="ywqa-content-description"><?php echo $answer->content; ?></div>
	</div>

	<div class="ywqa-bottom">
		<a class="ywqa-view-product" href="<?php echo get_permalink( $answer->product_id ); ?>">
			<?php esc_html_e( 'View product', 'yith-woocommerce-questions-and-answers' ); ?></a>
	</div>

<?php do_action( 'woocommerce_email_footer' );