<?php
/**
 * Questions Template for YITH WooCommerce Questions and Answers
 *
 * @author        Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<input type="hidden" id="ywqa-questions-and-answers-product-id" name="ywqa-questions-and-answers" value="<?php echo $product_id; ?>">

        <?php do_action( 'yith_questions_and_answers_before_container' ); ?>


<div id="ywqa-questions-and-answers" data-product-id="<?php echo $product_id; ?>" class="ywqa-container">

    <?php do_action( 'yith_questions_and_answers_before_content' ); ?>


    <div class="ywqa-content">
		<?php do_action( 'yith_questions_and_answers_content' ); ?>
	</div>

	<?php do_action( 'yith_questions_and_answers_after_content' ); ?>
</div>
