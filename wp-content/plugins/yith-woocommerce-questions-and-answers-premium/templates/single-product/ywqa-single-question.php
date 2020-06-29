<?php
/**
 * Single question Template for YITH WooCommerce Questions and Answers
 *
 * @author        Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( "answer_now_link" ) ) {

	function answer_now_link( $question, $label, $class = '' ) {

		$classes = "goto-question";
		if ( ! empty( $class ) ) {
			$classes .= " " . $class;
		}
		$link = '<a rel="nofollow" class="' . $classes . '" data-discussion-id="' . $question->ID . '" href="' . add_query_arg( array(
				"reply-to-question" => $question->ID,
				"qa"                => 1,
			), remove_query_arg( "show-all-questions", get_permalink( $question->product_id ) ) ) . '">' . ywqa_strip_trim_text( $label, apply_filters('ywqa_single_question_excerpt', 100) ) . '</a>';

		return $link;
	}
}

$all_answers = $question->get_answers();
$all_answers_ordered = array_reverse($all_answers);

$count = $question->get_answers_count();
$answers_to_show = get_option( 'ywqa_answers_to_show', '0');

if ( ! current_user_can('administrator') && get_option( "ywqa_only_admin_answers", "no" ) == 'yes' ){
	$only_admin_can_reply = false;
	$question_content = $question->content;

}
else{
	$only_admin_can_reply = true;
	$question_content = answer_now_link( $question, $question->content );

}





?>

<li id="li-question-<?php echo $question->ID; ?>" class="question-container <?php echo $classes; ?>">
	<?php do_action( 'yith_questions_answers_before_content', $question ); ?>

	<div class="question-text <?php echo $classes; ?>">
		<div class="question-content">
			<span class="question-symbol"><?php esc_html_e( "Q", 'yith-woocommerce-questions-and-answers' ); ?></span>
			<span class="question"><?php echo $question_content; ?>
				<?php if ( $only_admin_can_reply && $all_answers_ordered && ( apply_filters('yith_wcqa_allow_user_to_reply',true) ) ) :
					echo answer_now_link( $question, esc_html__( "answer now", 'yith-woocommerce-questions-and-answers' ), "answer-now" );
				endif;
				?>
			</span>
            <span class="question-owner-co">
                <?php if ( YITH_YWQA()->anonymise_user && !YITH_YWQA()->anonymise_date ) : ?>
                    <div class="question-owner">
                    <?php echo sprintf( esc_html__( "Asked on %s", 'yith-woocommerce-questions-and-answers' ),
                        '<span class="question-date">' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $question->date ) ) . '</span>' ); ?>
                </div>
                <?php elseif ( YITH_YWQA()->anonymise_date && !YITH_YWQA()->anonymise_user ) : ?>
                    <div class="question-owner">
                    <?php echo sprintf( esc_html__( "Asked by %s", 'yith-woocommerce-questions-and-answers' ),
                        '<span class="question-author-name">' . $question->get_author_name() . '</span>' ); ?>
                </div>
                <?php elseif ( !YITH_YWQA()->anonymise_date && !YITH_YWQA()->anonymise_user ) : ?>
                    <div class="question-owner">
                    <?php echo sprintf( esc_html__( "Asked by %s on %s", 'yith-woocommerce-questions-and-answers' ),
                        '<span class="question-author-name">' . $question->get_author_name() . '</span>',
                        '<span class="question-date">' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $question->date ) ) . '</span>' ); ?>
                </div>
                <?php endif; ?>
            </span>
		</div>

		<div class="answer-content">

			<?php if ( $all_answers_ordered ) :


                $cnt = 0;

			foreach ( $all_answers_ordered as $answer ){

			    if( $cnt >= $answers_to_show && $answers_to_show != 0 ){
			        break;
                }
                    $user_can         = user_can( $answer->discussion_author_id, 'manage_options' );
                    $replied_by_admin = apply_filters( 'yith_ywqa_show_as_admin_capabilities', $user_can, $answer );

                    ?><div class="ywqa-answers-list"><?php

				    if ( $replied_by_admin && ! YITH_YWQA()->faq_mode && ! YITH_YWQA()->anonymise_user ): ?>
					    <span class="admin-answer-symbol">
						    <?php echo apply_filters( 'ywqa_answered_by_admin_label',esc_html__( "Answered by the admin", 'yith-woocommerce-questions-and-answers' )); ?>
					    </span>
				    <?php else: ?>
					    <span class="answer-symbol"><?php esc_html_e( "A", 'yith-woocommerce-questions-and-answers' ); ?></span>
				    <?php endif; ?>

				    <?php if ( ( YITH_YWQA()->answer_excerpt_length > 0 ) && ( strlen( $answer->content ) > YITH_YWQA()->answer_excerpt_length ) ) : ?>
					    <span class="answer"><?php echo substr( $answer->content, 0, YITH_YWQA()->answer_excerpt_length ) . '...'; ?></span>
					    <a href="#" rel="nofollow" data-discussion-id="<?php echo $answer->ID; ?>" class="read-more">
						    <?php esc_html_e( "Read more", 'yith-woocommerce-questions-and-answers' ); ?>
					    </a>
				    <?php else: ?>
					    <span class="answer"><?php echo $answer->content; ?></span>
                    <?php endif; ?>

                    </div>

                    <?php $cnt++ ?>

             <?php } ?>

			<?php else: ?>
				<span class="answer">
					<?php esc_html_e( "There are no answers for this question yet.", 'yith-woocommerce-questions-and-answers' ); ?>
				</span>
                <?php if( $only_admin_can_reply && apply_filters('yith_wcqa_allow_user_to_reply',true) ): ?>
                    <a
                            href="<?php echo add_query_arg( array(
                                "reply-to-question" => $question->ID,
                                "qa"                => 1,
                            ), remove_query_arg( "show-all-questions", get_permalink( $question->product_id ) ) ); ?>"
                            rel="nofollow" data-discussion-id="<?php echo $question->ID; ?>"
                            class="goto-question write-first-answer"><?php esc_html_e( "Answer now", 'yith-woocommerce-questions-and-answers' ); ?></a>
                <?php endif; ?>


            <?php endif; ?>
		</div>


		<?php

        if ( ( $count ) > 1 && $answers_to_show != 0 && $count > $answers_to_show ) : ?>
			<div class="all-answers-section">
				<a href="<?php echo add_query_arg( array(
					"reply-to-question" => $question->ID,
					"qa"                => 1,
				), remove_query_arg( "show-all-questions", get_permalink( $question->product_id ) ) ); ?>"
				   rel="nofollow" id="all-answers-<?php echo $question->ID; ?>" class="all-answers goto-question"
				   data-discussion-id="<?php echo $question->ID; ?>">
					<?php echo sprintf( esc_html__( "Show all %s answers", 'yith-woocommerce-questions-and-answers' ), $count ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</li>
