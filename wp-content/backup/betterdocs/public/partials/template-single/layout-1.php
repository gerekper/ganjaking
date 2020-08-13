<?php
/**
 * The template for single doc page
 *
 * @author WPDeveloper
 * @package Documentation/SinglePage
 */

get_header();
$output = betterdocs_generate_output();
?>

<div class="betterdocs-single-wraper betterdocs-single-bg betterdocs-single-layout1">
	<?php 
	$live_search = BetterDocs_DB::get_settings('live_search');
	if($live_search == 1){
	?>
	<div class="betterdocs-search-form-wrap">
		<?php echo do_shortcode( '[betterdocs_search_form]' ); ?>
	</div>
	<?php } ?>
	<div class="betterdocs-content-area">
		<?php 
		$enable_sidebar_cat_list = BetterDocs_DB::get_settings('enable_sidebar_cat_list'); 
		if($enable_sidebar_cat_list == 1){
		?>
        <aside id="betterdocs-sidebar">
            <div class="betterdocs-sidebar-content">
				<?php

				$shortcode = do_shortcode( '[betterdocs_category_grid sidebar_list="true" posts_per_grid="-1"]' );

				echo apply_filters( 'betterdocs_sidebar_category_shortcode', $shortcode );
				
                ?>
			</div>
			<?php
			$enable_toc = BetterDocs_DB::get_settings('enable_toc');
			$enable_sticky_toc = BetterDocs_DB::get_settings('enable_sticky_toc');
			if($enable_toc == 1 && $enable_sticky_toc == 1){
			?>
	        <div class="sticky-toc-container">
	        	<a class="close-toc" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16px" viewBox="0 0 24 24"><path style="line-height:normal;text-indent:0;text-align:start;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#000;text-transform:none;block-progression:tb;isolation:auto;mix-blend-mode:normal" d="M 4.9902344 3.9902344 A 1.0001 1.0001 0 0 0 4.2929688 5.7070312 L 10.585938 12 L 4.2929688 18.292969 A 1.0001 1.0001 0 1 0 5.7070312 19.707031 L 12 13.414062 L 18.292969 19.707031 A 1.0001 1.0001 0 1 0 19.707031 18.292969 L 13.414062 12 L 19.707031 5.7070312 A 1.0001 1.0001 0 0 0 18.980469 3.9902344 A 1.0001 1.0001 0 0 0 18.292969 4.2929688 L 12 10.585938 L 5.7070312 4.2929688 A 1.0001 1.0001 0 0 0 4.9902344 3.9902344 z" font-weight="400" font-family="sans-serif" white-space="normal" overflow="visible"></path></svg></a>
			</div><!-- #sticky toc -->
			<?php } ?>
		</aside><!-- #sidebar -->
		<?php } ?>

		<div id="betterdocs-single-main" class="docs-single-main">
			<?php
            /* Start the Loop */
			while ( have_posts() ) : the_post();
				?>
				<header class="betterdocs-entry-header">
					<div class="docs-single-title">
						<?php
						if ( is_single() ) {
							$enable_breadcrumb = BetterDocs_DB::get_settings('enable_breadcrumb');
							if($enable_breadcrumb == 1){
								betterdocs_breadcrumbs();
							}
							$enable_post_title = BetterDocs_DB::get_settings('enable_post_title');
							if ( $enable_post_title == 1 ) {
								the_title( '<h1 id="betterdocs-entry-title" class="betterdocs-entry-title">', '</h1>' );
							}
						?>
					</div>
				</header><!-- .entry-header -->
				<?php } ?>
				<div class="betterdocs-entry-content" itemscope itemtype="http://schema.org/PublicationIssue">
					<?php  
					$enable_print_icon = BetterDocs_DB::get_settings('enable_print_icon'); 
					if($enable_print_icon == 1) {
					?>
					<div class="betterdocs-print-pdf">
						<span class="betterdocs-print-btn"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="20px"><path fill="#66798f" d="M14 16H66V24H14z"></path><path fill="#b0c1d4" d="M8,63.5c-3,0-5.5-2.5-5.5-5.5V26c0-3,2.5-5.5,5.5-5.5h64c3,0,5.5,2.5,5.5,5.5v32 c0,3-2.5,5.5-5.5,5.5H8z"></path><path fill="#66798f" d="M72,21c2.8,0,5,2.2,5,5v32c0,2.8-2.2,5-5,5H8c-2.8,0-5-2.2-5-5V26c0-2.8,2.2-5,5-5H72 M72,20H8 c-3.3,0-6,2.7-6,6v32c0,3.3,2.7,6,6,6h64c3.3,0,6-2.7,6-6V26C78,22.7,75.3,20,72,20L72,20z"></path><path fill="#fff" d="M16.5 2.5H63.5V23.5H16.5z"></path><path fill="#788b9c" d="M63,3v20H17V3H63 M64,2H16v22h48V2L64,2z"></path><path fill="#8bb7f0" d="M22,41.5c-3,0-5.5-2.5-5.5-5.5V20.5h47V36c0,3-2.5,5.5-5.5,5.5H22z"></path><path fill="#4e7ab5" d="M63,21v15c0,2.8-2.2,5-5,5H22c-2.8,0-5-2.2-5-5V21H63 M64,20H16v16c0,3.3,2.7,6,6,6h36 c3.3,0,6-2.7,6-6V20L64,20z"></path><path fill="#fff" d="M16.5 50.5H63.5V77.5H16.5z"></path><path fill="#788b9c" d="M63,51v26H17V51H63 M64,50H16v28h48V50L64,50z"></path><path fill="#d6e3ed" d="M17 52H63V56H17z"></path><path fill="#788b9c" d="M26 59H54V60H26zM26 67H54V68H26z"></path><g><path fill="#ffeea3" d="M70 28A2 2 0 1 0 70 32A2 2 0 1 0 70 28Z"></path></g><path fill="#66798f" d="M17,56v-4h46v4h2c1.7,0,3-1.3,3-3l0,0c0-1.7-1.3-3-3-3H15c-1.7,0-3,1.3-3,3l0,0c0,1.7,1.3,3,3,3H17z"></path></svg></span>
					</div>
					<?php 
					}
                    the_content(); 
                    ?>
				</div><!-- .entry-content -->
				<div class="betterdocs-entry-footer">
					<?php 
					$enable_tags = BetterDocs_DB::get_settings('enable_tags');
					$product_terms = wp_get_object_terms( $post->ID, 'doc_tag' );
					if ( ! empty( $product_terms ) && $enable_tags == 1) {
						if ( ! is_wp_error( $product_terms ) ) {
							$tag_links = array();
							foreach( $product_terms as $term ) {
								$tag_links[] = '<a href="' . get_term_link( $term->slug, 'doc_tag' ) . '">' . esc_html( $term->name ) . '</a>';
							}
							$tags = join( ", ", $tag_links );
							echo '<div class="betterdocs-tags">'.$tags.'</div>';
							
						}
					}

					do_action( 'betterdocs_docs_before_social' );

					$post_social_share = get_theme_mod('betterdocs_post_social_share', true);
					if($post_social_share == true){
						echo do_shortcode( '[betterdocs_social_share]' );
					}
					?>

					<div class="feedback-update-form">
						<?php 
						$email_feedback = BetterDocs_DB::get_settings('email_feedback');
						if($email_feedback == 1){
						?>
						<div class="feedback-form">
							<a class="feedback-form-link" href="#betterdocs-form-modal" name="betterdocs-form-modal">
								<span class="feedback-form-icon">
									<?php 
									if ( $output['betterdocs_single_doc_feedback_icon'] != '' ) {
										echo '<img src="' . $output['betterdocs_single_doc_feedback_icon'] . '" />';
									} else { 
									?>
									<svg xmlns="http://www.w3.org/2000/svg" width="32px" viewBox="0 0 64 64"><linearGradient id="zWPy7gPuySZ8fg4Y3QF24a" x1="26" x2="26" y1="630.833" y2="619.332" gradientTransform="matrix(1 0 0 -1 0 654)" gradientUnits="userSpaceOnUse" spreadMethod="reflect"><stop offset="0" stop-color="#6dc7ff"></stop><stop offset="1" stop-color="#e6abff"></stop></linearGradient><path fill="url(#zWPy7gPuySZ8fg4Y3QF24a)" d="M15.082,25.762l9.625,8.141c0.746,0.633,1.84,0.633,2.59,0l9.621-8.141 C37.629,25.16,37.203,24,36.27,24H15.73C14.797,24,14.371,25.16,15.082,25.762z"></path><linearGradient id="zWPy7gPuySZ8fg4Y3QF24b" x1="26" x2="26" y1="647.5" y2="596.439" gradientTransform="matrix(1 0 0 -1 0 654)" gradientUnits="userSpaceOnUse" spreadMethod="reflect"><stop offset="0" stop-color="#1a6dff"></stop><stop offset="1" stop-color="#c822ff"></stop></linearGradient><path fill="url(#zWPy7gPuySZ8fg4Y3QF24b)" d="M18,49h16v2H18V49z"></path><linearGradient id="zWPy7gPuySZ8fg4Y3QF24c" x1="32" x2="32" y1="8.915" y2="55.387" gradientUnits="userSpaceOnUse" spreadMethod="reflect"><stop offset="0" stop-color="#1a6dff"></stop><stop offset="1" stop-color="#c822ff"></stop></linearGradient><path fill="url(#zWPy7gPuySZ8fg4Y3QF24c)" d="M48,9c-6.134,0-11.277,4.276-12.637,10H8c-2.758,0-5,2.242-5,5v26c0,2.758,2.242,5,5,5h36 c2.758,0,5-2.242,5-5V35h2v-2h-3c-6.066,0-11-4.934-11-11s4.934-11,11-11s11,4.934,11,11v3c0,1.102-0.898,2-2,2s-2-0.898-2-2v-3 c0-3.859-3.141-7-7-7s-7,3.141-7,7s3.141,7,7,7c2.125,0,4.027-0.953,5.312-2.453C53.918,27.984,55.344,29,57,29c2.207,0,4-1.793,4-4 v-3C61,14.832,55.168,9,48,9z M5,24.109L17.086,34L5,43.891V24.109z M47,50c0,1.652-1.348,3-3,3H8c-1.652,0-3-1.348-3-3v-3.527 l13.668-11.18l4.168,3.41c0.914,0.75,2.039,1.125,3.164,1.125s2.25-0.375,3.164-1.125l4.172-3.41L47,46.473V50z M47,34.949v8.941 L34.914,34l3.618-3.12C40.691,33.18,43.668,34.694,47,34.949z M37.264,29.317l-9.365,7.835c-1.102,0.902-2.699,0.902-3.801,0 L5.699,22.098C6.25,21.434,7.07,21,8,21h27.051C35.025,21.331,35,21.662,35,22C35,24.712,35.837,27.231,37.264,29.317z M48,27 c-2.758,0-5-2.242-5-5s2.242-5,5-5s5,2.242,5,5S50.758,27,48,27z"></path></svg>
									<?php } ?>
								</span>
								<?php esc_html_e('Still stuck? How can we help?','betterdocs') ?></a>
							<div id="betterdocs-form-modal" class="betterdocs-modalwindow">
								<div class="modal-inner">
									<div class="modal-content">
										<a href="#" class="close"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 9.15625 6.3125 L 6.3125 9.15625 L 22.15625 25 L 6.21875 40.96875 L 9.03125 43.78125 L 25 27.84375 L 40.9375 43.78125 L 43.78125 40.9375 L 27.84375 25 L 43.6875 9.15625 L 40.84375 6.3125 L 25 22.15625 Z "></path></g></svg></a>
										<h2><?php esc_html_e('How can we help?','betterdocs') ?></h2>
										<div class="modal-content-inner">
											<?php echo do_shortcode('[betterdocs_feedback_form]'); ?>
										</div>
									</div>
								</div>		
							</div>
						</div>
						<?php } 
						$show_last_update_time = BetterDocs_DB::get_settings('show_last_update_time');
						if($show_last_update_time == 1){
						?>
						<div class="update-date">
							<?php 
								printf(
									esc_html__( 'Updated on %s', 'betterdocs' ),
									get_the_modified_date()
								);
							?>
						</div>
						<?php } ?>
					</div>
				</div><!-- .entry-footer -->
				<?php
			endwhile; // End of the loop.
			$enable_navigation = BetterDocs_DB::get_settings('enable_navigation');
			if($enable_navigation == 1){
			?>
			<div class="docs-navigation">
				<?php previous_post_link( '%link', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 11.957031 13.988281 C 11.699219 14.003906 11.457031 14.117188 11.28125 14.308594 L 1.015625 25 L 11.28125 35.691406 C 11.527344 35.953125 11.894531 36.0625 12.242188 35.976563 C 12.589844 35.890625 12.867188 35.625 12.964844 35.28125 C 13.066406 34.933594 12.972656 34.5625 12.71875 34.308594 L 4.746094 26 L 48 26 C 48.359375 26.003906 48.695313 25.816406 48.878906 25.503906 C 49.058594 25.191406 49.058594 24.808594 48.878906 24.496094 C 48.695313 24.183594 48.359375 23.996094 48 24 L 4.746094 24 L 12.71875 15.691406 C 13.011719 15.398438 13.09375 14.957031 12.921875 14.582031 C 12.753906 14.203125 12.371094 13.96875 11.957031 13.988281 Z "></path></g></svg> %title', TRUE, ' ', 'doc_category' ); ?>
				<?php next_post_link( '%link', '%title <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 38.035156 13.988281 C 37.628906 13.980469 37.257813 14.222656 37.09375 14.59375 C 36.933594 14.96875 37.015625 15.402344 37.300781 15.691406 L 45.277344 24 L 2.023438 24 C 1.664063 23.996094 1.328125 24.183594 1.148438 24.496094 C 0.964844 24.808594 0.964844 25.191406 1.148438 25.503906 C 1.328125 25.816406 1.664063 26.003906 2.023438 26 L 45.277344 26 L 37.300781 34.308594 C 36.917969 34.707031 36.933594 35.339844 37.332031 35.722656 C 37.730469 36.105469 38.363281 36.09375 38.746094 35.691406 L 49.011719 25 L 38.746094 14.308594 C 38.5625 14.109375 38.304688 13.996094 38.035156 13.988281 Z "></path></g></svg>', TRUE, ' ', 'doc_category' ); ?>
			</div>
			<?php } ?>

			<?php
				$enable_credit = BetterDocs_DB::get_settings('enable_credit');
				if($enable_credit == 1){
				?>
					<div class="betterdocs-credit">
						<p><?php printf(__('Powered by ', 'betterdocs').'<a href="%s" target="_blank">' . __('BetterDocs', 'betterdocs') . '</a>', 'https://betterdocs.co'); ?></p>
					</div>

			<?php } ?>

			<?php 
			$enable_comment = BetterDocs_DB::get_settings('enable_comment');
			if($enable_comment == 1){
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			}
			?>
		</div><!-- #main -->

	</div><!-- #primary -->
</div><!-- .betterdocs-single-wraper -->

<?php get_footer(); ?>
