<?php
/**
 * Single answer Template for YITH WooCommerce Questions and Answers
 *
 * @author        Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<li id="li-answer-<?php echo $answer->ID; ?>" class="answer-container <?php echo $classes; ?>">

	<div class="answer-content">
		<span class="answer">
			<?php $content = nl2br( $answer->get_nofollow_content() ); ?>
			<?php if ( ( YITH_YWQA()->answer_excerpt_length > 0 ) && ( strlen( $answer->content ) > YITH_YWQA()->answer_excerpt_length ) ) :
				echo substr( $content, 0, YITH_YWQA()->answer_excerpt_length ) . '... <a href="#" rel="nofollow" data-discussion-id="' . $answer->ID . '" class="read-more">' . esc_html__( "Read more", 'yith-woocommerce-questions-and-answers' ) . '</a>';
			else:
				echo $content;
			endif;
			?>
		</span>
	</div>

	<div class="answer-owner">
		<?php $answer_date_html ='<span class="answer-date">' . date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $answer->date ) )  . '</span>';

		if ( YITH_YWQA()->anonymise_user && !YITH_YWQA()->anonymise_date ) {
			echo sprintf( esc_html__( "Answered on %s", 'yith-woocommerce-questions-and-answers' ), $answer_date_html);
            if ( current_user_can('administrator') || current_user_can('manage_woocommerce') ) {
                ?><a class="edit-on-backend-link" href="<?php echo get_edit_post_link($answer->ID); ?>" target="_blank"><?php esc_html_e("Edit on backend", 'yith-woocommerce-questions-and-answers'); ?></a>
            <?php }
		}
		elseif ( YITH_YWQA()->anonymise_date && !YITH_YWQA()->anonymise_user ){
        echo sprintf( esc_html__( "Answered by %s", 'yith-woocommerce-questions-and-answers' ),
            '<span class="answer-author-name">' . $answer->get_author_name() . '</span>');
        if ( current_user_can('administrator') || current_user_can('manage_woocommerce') ) {
            ?><a class="edit-on-backend-link" href="<?php echo get_edit_post_link($answer->ID); ?>" target="_blank"><?php esc_html_e("Edit on backend", 'yith-woocommerce-questions-and-answers'); ?></a>
            <?php }
        }
		elseif ( !YITH_YWQA()->anonymise_date && !YITH_YWQA()->anonymise_user ) {
			echo sprintf( esc_html__( "%s answered on %s", 'yith-woocommerce-questions-and-answers' ),
				'<span class="answer-author-name">' . $answer->get_author_name() . '</span>',
				$answer_date_html);
            if ( current_user_can('administrator') || current_user_can('manage_woocommerce') ) {
                ?><a class="edit-on-backend-link" href="<?php echo get_edit_post_link($answer->ID); ?>" target="_blank"><?php esc_html_e("Edit on backend", 'yith-woocommerce-questions-and-answers'); ?></a>
            <?php }
		} ?>
	</div>

	<?php
	$vote_answer = ( "yes" === get_option( "ywqa_enable_answer_vote", "no" ) );

	$report_abuse = ( ( 'registered' == get_option( "ywqa_enable_answer_abuse_reporting" ) ) && get_current_user_id() ) ||
	                ( 'everyone' == get_option( "ywqa_enable_answer_abuse_reporting" ) );

	?>
	<?php if ( $report_abuse || $vote_answer ): ?>
		<div class="clearfix vote-answer">
			<?php if ( $vote_answer ) : ?>
				<div class="answer-helpful">
					<span class="answer-stat-text"><?php echo YITH_YWQA()->get_helpful_answer_text( $answer ); ?></span>

					<div class="is-answer-helpful">
						<a href="#" class="answer-helpful" rel="nofollow" 
						   data-discussion-id="<?php echo $answer->ID; ?>"
						   data-discussion-vote="1"><?php esc_html_e( "YES", 'yith-woocommerce-questions-and-answers' ); ?></a>
						<a href="#" class="answer-not-helpful" rel="nofollow" data-discussion-id="<?php echo $answer->ID; ?>"
						   data-discussion-vote="-1"><?php esc_html_e( "NO", 'yith-woocommerce-questions-and-answers' ); ?></a>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $report_abuse ) : ?>
				<div class="answer-abuse">
					<a title="<?php esc_html_e( "Report an inappropriate content", 'yith-woocommerce-questions-and-answers' ); ?>" href="#" data-discussion-id="<?php echo $answer->ID; ?>"
					   rel="nofollow" class="report-answer-abuse"><?php esc_html_e( "Report", 'yith-woocommerce-questions-and-answers' ); ?></a>
				</div>
			<?php endif; ?>

			<div class="clearfix"></div>
		</div>
	<?php endif; ?>
</li>