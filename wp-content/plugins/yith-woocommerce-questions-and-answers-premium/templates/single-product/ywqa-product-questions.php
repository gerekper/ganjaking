<?php
/**
 * Questions Template for YITH WooCommerce Questions and Answers
 *
 * @author        Yithemes
 */

if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$question_count = YITH_YWQA ()->get_questions_count ( $product_id );
$answered_count = YITH_YWQA ()->get_questions_count ( $product_id, true );

$unanswered_count = $question_count - $answered_count;
$item_shown       = 0;

$c_page = isset( $_GET["c_page"] ) ? $_GET["c_page"] : 1;
$p_page = $c_page > 1 ? $c_page - 1 : 0;
$n_page = ( $c_page * $max_items ) < $answered_count ? $c_page + 1 : 0;

?>
<div id="ywqa-questions-and-answers" data-product-id="<?php echo $product_id; ?>" class="ywqa-product-questions">
	<div class="questions-section">
		<h3><?php echo get_option('ywqa_question_section_title','Questions and answers of the customers');?></h3>
		
		<?php do_action ( 'yith_question_answer_before_question_list_section' ); ?>
		<div id="ywqa_question_list">
			<?php if ( $question_count ) : ?>
				<?php do_action ( 'yith_question_answer_before_question_list' ); ?>
				
				<ol class="ywqa-items-list questions">
					<?php
					if ( ! ( $item_shown = YITH_YWQA ()->show_questions ( $product_id, $max_items, $c_page, $only_answered ) ) && $only_answered ) {
						$item_shown = YITH_YWQA ()->show_questions ( $product_id, $max_items, $c_page, false );
					}
					?>
				</ol>
				<?php do_action ( 'yith_question_answer_after_question_list' ); ?>
				
				
				<?php if ( 0 == $item_shown ) : ?>
					<p class="no-questions"><?php esc_html_e( "No question has still received an answer.", 'yith-woocommerce-questions-and-answers' ); ?></p>
				<?php endif; ?>
				
				<?php if ( ( $max_items > 0 ) && ( ( $c_page * $max_items ) < $answered_count ) ): ?>
					<div class="show-more-section">
						<ol class="item-navigation">
							<?php YITH_YWQA ()->show_items_pagination ( $product_id, $c_page, $max_items, $answered_count ); ?>
						</ol>
					</div>
				<?php endif; ?>
				
				<?php if ( $item_shown < $question_count ) : ?>
					<div id="show-all-questions">
						<a class="show-questions" rel="nofollow" 
						   href="<?php echo esc_url ( add_query_arg ( "show-all-questions", $product_id, get_permalink ( $product_id ) ) ); ?>"
						   title="<?php echo sprintf ( esc_html__( "Show all %d questions", 'yith-woocommerce-questions-and-answers' ), $question_count ); ?>"><?php echo sprintf ( esc_html__( "Show all %d questions %s", 'yith-woocommerce-questions-and-answers' ), $question_count, $unanswered_count ? sprintf ( esc_html__( '(%d without an answer)', 'yith-woocommerce-questions-and-answers' ), $unanswered_count ) : '' ); ?></a>
					</div>
				<?php endif; ?>
			<?php elseif ( ! YITH_YWQA ()->faq_mode ) : ?>
                <?php $text = esc_html__( 'There are no questions yet. Be the first to ask a question about this product.', 'yith-woocommerce-questions-and-answers' ); ?>
				<p class="woocommerce-noreviews"><?php echo $text  ?></p>
			<?php endif; ?>
			
			
			<div class="clear"></div>
		</div>
		
		<?php //    If the plugin is in FAQ mode, don't show the submit section
		if ( ! YITH_YWQA ()->faq_mode )  : ?>
			<?php if ( get_current_user_id () || YITH_YWQA ()->allow_guest_users ): ?>
				<div id="ask_question">
					<form id="ask_question_form" method="POST">
						<input type="hidden" name="ywqa_product_id" value="<?php echo $product_id; ?>">
						<input type="hidden" name="add_new_question" value="1">
						<?php wp_nonce_field ( 'ask_question_' . $product_id, 'ywqa_ask_question' ); ?>
						<div class="ywqa-ask-question">
							
							<p class="ywqa_ask_question_text">
								<label
									for="ywqa_user_content"><?php esc_html_e( 'Your question', 'yith-woocommerce-questions-and-answers' ); ?>
									<span class="required">*</span></label>
								<textarea id="ywqa_user_content" name="ywqa_user_content"
								          placeholder="<?php esc_html_e( 'Do you have any questions? Ask now!', 'yith-woocommerce-questions-and-answers' ); ?>"
								          class="ywqa-ask-question-text"></textarea>
							</p>
							
							<?php if ( ! get_current_user_id () ): ?>
								<p class="ywqa-guest-name-section">
									<label
										for="ywqa-guest-name"><?php esc_html_e( 'Name', 'yith-woocommerce-questions-and-answers' ); ?>
										<?php if ( YITH_YWQA ()->mandatory_guest_data ) : ?>
											<span class="required">*</span>
										<?php endif; ?>
									</label>
									<input id="ywqa-guest-name" name="ywqa-guest-name" class="ywqa-guest-name required"
									       type="text"
									       placeholder="<?php esc_html_e( "Enter your name", 'yith-woocommerce-questions-and-answers' ); ?>"
									       value="" aria-required="true">
								</p>
								<p class="ywqa-guest-email-section">
									<label
										for="ywqa-guest-email"><?php esc_html_e( 'Email', 'yith-woocommerce-questions-and-answers' ); ?>
										<?php if ( YITH_YWQA ()->mandatory_guest_data ) : ?>
											<span class="required">*</span>
										<?php endif; ?>
									</label>
									<input id="ywqa-guest-email" name="ywqa-guest-email"
									       class="ywqa-guest-email required"
									       type="text"
									       placeholder="<?php esc_html_e( "Enter your email", 'yith-woocommerce-questions-and-answers' ); ?>"
									       value="" aria-required="true">
								</p>
							<?php endif; ?>
							
							<?php if ( YITH_YWQA ()->recaptcha_enabled ): ?>
								<div id="ywqa-g-recaptcha"></div>
							<?php endif; ?>
							
							<div class="notify-answers">
								<?php if ( YITH_YWQA ()->enable_user_notification ) : ?>
									<input id="ywqa-notify-user" name="ywqa-notify-user" type="checkbox"
									       class="enable-notification"
									       checked="checked"><?php esc_html_e( "Send me a notification for each new answer.", 'yith-woocommerce-questions-and-answers' ); ?>
								<?php endif; ?>
								<input id="ywqa-submit-question" type="submit" class="ywqa_submit_question"
								       value="<?php esc_html_e( "Ask", 'yith-woocommerce-questions-and-answers' ); ?>"
								       title="<?php esc_html_e( "Ask your question", 'yith-woocommerce-questions-and-answers' ); ?>">
							</div>
						</div>
					</form>
				</div>
			<?php else: ?>
				<div class="ywqa-guest-user">
					<?php esc_html_e( "Only registered users are eligible to enter questions", 'yith-woocommerce-questions-and-answers' ); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<div class="clearfix"></div>
		<?php do_action ( 'yith_question_answer_after_question_list_section', 'yith-woocommerce-questions-and-answers' ); ?>
	</div>
</div>