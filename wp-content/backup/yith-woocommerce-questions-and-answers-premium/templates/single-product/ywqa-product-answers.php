<?php
/**
 * Answers template for YITH WooCommerce Questions and Answers
 * *
 *
 * @author        Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/** @var YWQA_Question $question */
$answers_count = $question->get_answers_count();

$c_page = isset( $_GET["c_page"] ) ? $_GET["c_page"] : 1;
$p_page = $c_page > 1 ? $c_page - 1 : 0;
$n_page = ( $c_page * $max_items ) < $answers_count ? $c_page + 1 : 0;
$order  = empty( $order ) ? "recent" : $order;
?>

<div id="ywqa-questions-and-answers" data-product-id="<?php echo $question->product_id; ?>" class="ywqa-answers">
    <div id="new-answer-header">
        <div id="parent-question">
            <div class="parent-question">
			<span class="question-text"><span
                        class="question-symbol"><?php esc_html_e( "Q", 'yith-woocommerce-questions-and-answers' ); ?></span>
                <?php echo nl2br( $question->content ); ?></span>
                <a class="back-to-product" rel="nofollow"
                   href="<?php echo esc_url( add_query_arg( "qa", 1, remove_query_arg( array(
                       "show-all-questions",
                       "reply-to-question",
                   ), get_permalink( $question->product_id ) ) ) ); ?>"
                   title="<?php esc_html_e( "Back to questions", 'yith-woocommerce-questions-and-answers' ); ?>"><?php esc_html_e( "<<< Back to questions", 'yith-woocommerce-questions-and-answers' ); ?>
                </a>
            </div>

            <?php if ( YITH_YWQA()->anonymise_user && !YITH_YWQA()->anonymise_date ) : ?>
                <div class="question-owner">
                    <?php echo sprintf( esc_html__( "Asked on %s", 'yith-woocommerce-questions-and-answers' ),
                        '<span class="question-date">' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $question->date ) ) . '</span>' );
                    if ( current_user_can('administrator') || current_user_can('manage_woocommerce') ) {
                        ?><a class="edit-on-backend-link" href="<?php echo get_edit_post_link($question->ID); ?>" target="_blank"><?php esc_html_e("Edit on backend", 'yith-woocommerce-questions-and-answers'); ?></a>
                    <?php } ?>
                </div>
            <?php elseif ( YITH_YWQA()->anonymise_date && !YITH_YWQA()->anonymise_user ) : ?>
                <div class="question-owner">
                    <?php echo sprintf( esc_html__( "Asked by %s ", 'yith-woocommerce-questions-and-answers' ),
                        '<span class="question-author-name">' . $question->get_author_name() . '</span>' . '</span>' );
                    if ( current_user_can('administrator') || current_user_can('manage_woocommerce') ) {
                        ?><a class="edit-on-backend-link" href="<?php echo get_edit_post_link($question->ID); ?>" target="_blank"><?php esc_html_e("Edit on backend", 'yith-woocommerce-questions-and-answers'); ?></a>
                    <?php } ?>
                </div>
            <?php elseif ( !YITH_YWQA()->anonymise_date && !YITH_YWQA()->anonymise_user ) : ?>
            <div class="question-owner">
                <?php echo sprintf( esc_html__( "Asked by %s on %s", 'yith-woocommerce-questions-and-answers' ),
                    '<span class="question-author-name">' . $question->get_author_name() . '</span>',
                    '<span class="question-date">' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $question->date ) ) . '</span>' );
                if ( current_user_can('administrator') || current_user_can('manage_woocommerce') ) {
                    ?><a class="edit-on-backend-link" href="<?php echo get_edit_post_link($question->ID); ?>" target="_blank"><?php esc_html_e("Edit on backend", 'yith-woocommerce-questions-and-answers'); ?></a>
                <?php } ?>
            </div>
            <?php endif; ?>


        </div>

        <?php if ( ! $answers_count ) : ?>
            <p class="no-answers"><?php echo apply_filters( 'yith_ywqa_no_answer_text', esc_html__( 'There are no answers for this question, be the first to respond.', 'yith-woocommerce-questions-and-answers' )); ?></p>
        <?php endif; ?>

        <?php //    If the plugin is in FAQ mode, don't show the submit section
        if ( ! YITH_YWQA()->faq_mode ) : ?>
            <?php if ( (get_current_user_id() || YITH_YWQA()->allow_guest_users) && apply_filters('yith_wcqa_allow_user_to_reply',true) ): ?>

                <div id="submit_answer">
                    <form id="submit_answer_form" method="POST">
                        <input type="hidden" name="ywqa_product_id" value="<?php echo $question->product_id; ?>">
                        <input type="hidden" name="ywqa_question_id" value="<?php echo $question->ID; ?>">
                        <input type="hidden" name="add_new_answer" value="1">
                        <?php wp_nonce_field( 'submit_answer_' . $question->ID, 'send_answer' ); ?>

                        <div class="ywqa-send-answer">
                            <p class="ywqa_send_answer_text">
                                <label
                                        for="ywqa_user_content"><?php esc_html_e( 'Your answer', 'yith-woocommerce-questions-and-answers' ); ?>

                                    <span class="required">*</span>

                                </label>
                                <textarea
                                        placeholder="<?php esc_html_e( "Type your answer here", 'yith-woocommerce-questions-and-answers' ); ?>"
                                        class="ywqa-send-answer-text required"
                                        id="ywqa_user_content"
                                        name="ywqa_user_content"></textarea>
                            </p>

                            <?php if ( ! get_current_user_id() ): ?>
                                <p class="ywqa-guest-name-section">
                                    <label
                                            for="ywqa-guest-name"><?php esc_html_e( 'Name', 'yith-woocommerce-questions-and-answers' ); ?>
                                        <?php if ( YITH_YWQA()->mandatory_guest_data ): ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <input id="ywqa-guest-name" name="ywqa-guest-name" class="ywqa-guest-name required"
                                           type="text"
                                           placeholder="<?php esc_html_e( "Enter your name", 'yith-woocommerce-questions-and-answers' ); ?>"
                                           value="" aria-required="true" <?php echo YITH_YWQA()->mandatory_guest_data ? "required" : ""; ?>>
                                </p>
                                <p class="ywqa-guest-email-section">
                                    <label
                                            for="ywqa-guest-email"><?php esc_html_e( 'Email', 'yith-woocommerce-questions-and-answers' ); ?>
                                        <?php if ( YITH_YWQA()->mandatory_guest_data ): ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <input id="ywqa-guest-email" name="ywqa-guest-email"
                                           class="ywqa-guest-email required"
                                           type="text"
                                           placeholder="<?php esc_html_e( "Enter your email", 'yith-woocommerce-questions-and-answers' ); ?>"
                                           value="" aria-required="true" <?php echo YITH_YWQA()->mandatory_guest_data ? "required" : ""; ?>>
                                </p>
                            <?php endif; ?>

                            <?php if ( YITH_YWQA()->recaptcha_enabled ): ?>
                                <div id="ywqa-g-recaptcha"></div>
                            <?php endif; ?>

                            <input id="ywqa-submit-answer" name="ywqa-submit-answer" type="submit"
                                   class="ywqa_submit_answer"
                                   value="<?php esc_html_e( "Answer", 'yith-woocommerce-questions-and-answers' ); ?>"
                                   title="<?php esc_html_e( "Answer now to the question", 'yith-woocommerce-questions-and-answers' ); ?>">
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="ywqa-guest-user">
                    <?php echo apply_filters( 'yith_wcqa_unable_submit_answer_message', esc_html__( "Only registered users are eligible to enter answers", 'yith-woocommerce-questions-and-answers' )); ?>
                </div>
            <?php endif; ?>
        <?php endif;

        if (  YITH_YWQA()->faq_mode ) : ?>
            <?php if ( (get_current_user_id() || YITH_YWQA()->allow_guest_users) && apply_filters('yith_wcqa_allow_user_to_reply',true) ): ?>

                <div id="submit_answer" style="display: none">
                    <form id="submit_answer_form" method="POST">
                        <input type="hidden" name="ywqa_product_id" value="<?php echo $question->product_id; ?>">
                        <input type="hidden" name="ywqa_question_id" value="<?php echo $question->ID; ?>">
                        <input type="hidden" name="add_new_answer" value="1">
                        <?php wp_nonce_field( 'submit_answer_' . $question->ID, 'send_answer' ); ?>

                        <div class="ywqa-send-answer">
                            <p class="ywqa_send_answer_text">
                                <label
                                        for="ywqa_user_content"><?php esc_html_e( 'Your answer', 'yith-woocommerce-questions-and-answers' ); ?>

                                    <span class="required">*</span>

                                </label>
                                <textarea
                                        placeholder="<?php esc_html_e( "Type your answer here", 'yith-woocommerce-questions-and-answers' ); ?>"
                                        class="ywqa-send-answer-text required"
                                        id="ywqa_user_content"
                                        name="ywqa_user_content"></textarea>
                            </p>

                            <?php if ( ! get_current_user_id() ): ?>
                                <p class="ywqa-guest-name-section">
                                    <label
                                            for="ywqa-guest-name"><?php esc_html_e( 'Name', 'yith-woocommerce-questions-and-answers' ); ?>
                                        <?php if ( YITH_YWQA()->mandatory_guest_data ): ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <input id="ywqa-guest-name" name="ywqa-guest-name" class="ywqa-guest-name required"
                                           type="text"
                                           placeholder="<?php esc_html_e( "Enter your name", 'yith-woocommerce-questions-and-answers' ); ?>"
                                           value="" aria-required="true" <?php echo YITH_YWQA()->mandatory_guest_data ? "required" : ""; ?>>
                                </p>
                                <p class="ywqa-guest-email-section">
                                    <label
                                            for="ywqa-guest-email"><?php esc_html_e( 'Email', 'yith-woocommerce-questions-and-answers' ); ?>
                                        <?php if ( YITH_YWQA()->mandatory_guest_data ): ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <input id="ywqa-guest-email" name="ywqa-guest-email"
                                           class="ywqa-guest-email required"
                                           type="text"
                                           placeholder="<?php esc_html_e( "Enter your email", 'yith-woocommerce-questions-and-answers' ); ?>"
                                           value="" aria-required="true" <?php echo YITH_YWQA()->mandatory_guest_data ? "required" : ""; ?>>
                                </p>
                            <?php endif; ?>

                            <?php if ( YITH_YWQA()->recaptcha_enabled ): ?>
                                <div id="ywqa-g-recaptcha"></div>
                            <?php endif; ?>

                            <input id="ywqa-submit-answer" name="ywqa-submit-answer" type="submit"
                                   class="ywqa_submit_answer"
                                   value="<?php esc_html_e( "Answer", 'yith-woocommerce-questions-and-answers' ); ?>"
                                   title="<?php esc_html_e( "Answer now to the question", 'yith-woocommerce-questions-and-answers' ); ?>">
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="ywqa-guest-user">
                    <?php echo apply_filters( 'yith_wcqa_unable_submit_answer_message', esc_html__( "Only registered users are eligible to enter answers", 'yith-woocommerce-questions-and-answers' )); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>

    <div id="ywqa_answer_list">

        <?php if ( $answers_count ) : ?>
            <div class="visualize-answers-header">
		<span
                class="answer-list-count"><?php echo sprintf( _n( "%s answer shown", "%s answers shown", $answers_count, 'yith-woocommerce-questions-and-answers' ), $answers_count ); ?></span>

                <div class="order-by">
                    <span><?php esc_html_e( "Sort by:", 'yith-woocommerce-questions-and-answers' ); ?></span>
                    <a rel="nofollow" class="ywqa-question-order" data-order="useful"><?php esc_html_e( "Most useful", 'yith-woocommerce-questions-and-answers' ); ?></a>
                    |
                    <a rel="nofollow" class="ywqa-question-order" data-order="recent"><?php esc_html_e( "Most recent", 'yith-woocommerce-questions-and-answers' ); ?></a>
                    |
                    <a rel="nofollow" class="ywqa-question-order" data-order="oldest"><?php esc_html_e( "Oldest", 'yith-woocommerce-questions-and-answers' ); ?></a>
                </div>
            </div>

            <ol class="ywqa-items-list answers <?php echo $order; ?>">
                <?php YITH_YWQA()->show_answers( $question, $max_items, $c_page, $order ); ?>
            </ol>

            <?php if ( ( $max_items > 0 ) && ( $answers_count > ( $c_page * $max_items ) ) ): ?>
                <div class="show-more-section">
                    <ol class="item-navigation answers">
                        <?php YITH_YWQA()->show_items_pagination( $question->product_id, $c_page, $max_items, $answers_count, $order, $question->ID ); ?>
                    </ol>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="clear"></div>
    </div>
</div>