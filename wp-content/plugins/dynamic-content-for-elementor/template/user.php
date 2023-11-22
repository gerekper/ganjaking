<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
get_header();
$dce_default_options = get_option( DCE_TEMPLATE_SYSTEM_OPTION );

// BEFORE
$dce_before_template = null;
if ( isset( $dce_default_options['dyncontel_field_singleuser'] ) ) {
	$dce_before_template = $dce_default_options['dyncontel_field_singleuser'];
}
if ( isset( $dce_default_options['dyncontel_before_field_archiveuser'] ) ) {
	$dce_before_template = $dce_default_options['dyncontel_before_field_archiveuser'];
}
// AFTER
$dce_after_template = null;
if ( isset( $dce_default_options['dyncontel_field_singleuser'] ) ) {
	$dce_after_template = $dce_default_options['dyncontel_field_singleuser'];
}
if ( isset( $dce_default_options['dyncontel_after_field_archiveuser'] ) ) {
	$dce_after_template = $dce_default_options['dyncontel_after_field_archiveuser'];
}
$dce_block_template = 'dyncontel_field_archiveuser';
$dce_template_layout = $dce_default_options[ $dce_block_template . '_template' ];
$dce_default_template = $dce_default_options[ $dce_block_template ];
$dce_col_md = $dce_default_options[ $dce_block_template . '_col_md' ];
$dce_col_sm = $dce_default_options[ $dce_block_template . '_col_sm' ];
$dce_col_xs = $dce_default_options[ $dce_block_template . '_col_xs' ];
?>
<div id="content-wrap" class="clr">

	<div id="primary" class="clr">

		<div id="content" class="site-content clr">
		<?php

		// Questa è la pagina del template che viene impostata nei settings di User
		if ( isset( $dce_before_template ) && $dce_before_template > 1 ) {
			echo do_shortcode( '[dce-elementor-template id="' . $dce_before_template . '"]' );
		}
		?>
			</div>
			<?php
			if ( $dce_default_template > 1 ) { ?>
			<div class="grid-user-page grid-page grid-col-md-<?php echo $dce_col_md; ?> grid-col-sm-<?php echo $dce_col_sm; ?> grid-col-xs-<?php echo $dce_col_xs; ?>">
				<?php
				// Questo è il BLOCCO template che viene impostata nei settings di User
				if ( $dce_default_template ) {
					if ( $dce_template_layout == 'canvas' ) {
						echo do_shortcode( '[dce-elementor-template id="' . $dce_default_template . '"]' );
					} else {
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								echo '<div class="item-user-page item-page">';
								the_content();
								echo '</div>';
						 endwhile; else : ?>
						<p><?php __( 'No posts by this author.', 'dynamic-content-for-elementor' ); ?></p>
						<?php endif;
					}
				}
				?>
			<!-- End Loop -->
			</div>
				<?php
			}

			if ( isset( $dce_after_template ) && $dce_after_template > 1 ) {
				echo do_shortcode( '[dce-elementor-template id="' . $dce_after_template . '"]' );
			}
			?>

		</div>
	</div>
</div>

<?php get_footer(); ?>
