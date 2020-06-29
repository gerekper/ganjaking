<?php get_header(); ?>

<?php
global $porto_settings, $wp_query;

$term    = $wp_query->queried_object;
$term_id = $term->term_id;

$member_options = get_metadata( $term->taxonomy, $term->term_id, 'member_options', true ) == 'member_options' ? true : false;

?>

<div id="content" role="main">

	<?php if ( category_description() ) : ?>
		<div class="page-content">
			<?php echo category_description(); ?>
		</div>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<div class="page-members clearfix">

			<?php if ( $porto_settings['member-archive-ajax'] ) : ?>
				<div id="memberAjaxBox" class="ajax-box">
					<div class="bounce-loader">
						<div class="bounce1"></div>
						<div class="bounce2"></div>
						<div class="bounce3"></div>
					</div>
					<div class="ajax-box-content" id="memberAjaxBoxContent"></div>
					<?php if ( function_exists( 'porto_title_archive_name' ) && porto_title_archive_name( 'member' ) ) : ?>
						<?php /* translators: %s: Portfoli archive title */ ?>
						<div class="hide ajax-content-append"><h4 class="m-t-sm m-b-lg"><?php echo sprintf( esc_html__( 'More %s:', 'porto' ), porto_title_archive_name( 'member' ) ); ?></h4></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="member-row members-container row <?php echo porto_generate_column_classes( $porto_settings['member-columns'] ); ?>">
				<?php
				while ( have_posts() ) {
					the_post();

					get_template_part( 'content', 'archive-member' );
				}
				?>
			</div>

			<?php porto_pagination(); ?>

		</div>

		<?php wp_reset_postdata(); ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?></p>

	<?php endif; ?>
</div>

<?php get_footer(); ?>
