<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

global $default_template;

$dce_default_options = get_option( DCE_TEMPLATE_SYSTEM_OPTION );
$cptype_archive = get_post_type();

// after before data id
if ( isset( $cptype_archive ) && isset( $dce_default_options[ 'dyncontel_before_field_archive' . $cptype_archive ] ) ) {
	$dce_before_archive = $dce_default_options[ 'dyncontel_before_field_archive' . $cptype_archive ];
} else {
	$dce_before_archive = '';
}
if ( isset( $cptype_archive ) && isset( $dce_default_options[ 'dyncontel_after_field_archive' . $cptype_archive ] ) ) {
	$dce_after_archive = $dce_default_options[ 'dyncontel_after_field_archive' . $cptype_archive ];
} else {
	$dce_after_archive = '';
}

$queried_object = get_queried_object();
if (
	isset( $queried_object->taxonomy )
	&& isset( $dce_default_options[ 'dyncontel_field_archive_taxonomy_' . $queried_object->taxonomy ] )
	&& $dce_default_options[ 'dyncontel_field_archive_taxonomy_' . $queried_object->taxonomy ]
	&& isset( $dce_default_options[ 'dyncontel_field_archive_taxonomy_' . $queried_object->taxonomy . '_template' ] )
	&& $dce_default_options[ 'dyncontel_field_archive_taxonomy_' . $queried_object->taxonomy . '_template' ]
) {
	$dce_elementor_templates = 'dyncontel_field_archive_taxonomy_' . $queried_object->taxonomy;
} else {
	$dce_elementor_templates = 'dyncontel_field_archive' . $cptype_archive;
}

$dce_default_template = $dce_default_options[ $dce_elementor_templates ]; // ID
$dce_default_template_base = $dce_default_options[ $dce_elementor_templates . '_template' ]; // canvas | boxed | fullwidth

if ( is_tax() || is_category() || is_tag() ) {
	$termine_id = get_queried_object()->term_id;
	$dce_default_template_term = get_term_meta( $termine_id, 'dynamic_content_block', true );

	if ( ! empty( $dce_default_template_term ) && $dce_default_template_term > 1 ) {
		$dce_default_template = $dce_default_template_term;
	}
}

$dce_col_md = $dce_default_options[ $dce_elementor_templates . '_col_md' ];
$dce_col_sm = $dce_default_options[ $dce_elementor_templates . '_col_sm' ];
$dce_col_xs = $dce_default_options[ $dce_elementor_templates . '_col_xs' ];

?>

<div id="content-wrap" class="clr">

	<div id="primary" class="clr">

		<div id="content" class="site-content clr">
			<?php do_action( 'dce_before_content_inner' ); ?>

			<div class="dce-wrapper-container">
				<div class="dce-container <?php if ( $dce_default_template_base == 'boxed' ) {
					?>container<?php } else {
												?>container-fluid<?php } ?>">

					<?php
					if ( $dce_default_template_base == 'canvas' ) {
							echo do_shortcode( '[dce-elementor-template id="' . $dce_default_template . '"]' );
					} else {
						?>

					<!-- The Loop -->
					<div class="grid-archive-page grid-page grid-col-md-<?php echo $dce_col_md; ?> grid-col-sm-<?php echo $dce_col_sm; ?> grid-col-xs-<?php echo $dce_col_xs; ?>">
						<?php

						$data_columns = ' data-col-md="' . $dce_col_md . '" data-col-sm="' . $dce_col_sm . '" data-col-xs="' . $dce_col_xs . '"';
						if ( $dce_default_template ) {
							if ( have_posts() ) :
								while ( have_posts() ) :
									the_post();
									echo '<div class="item-archive-page item-page"' . $data_columns . '>';
									the_content();
									echo '</div>';
							 endwhile;

							\DynamicContentForElementor\Helper::numeric_posts_nav();
							 else : ?>
								<p><?php __( 'No posts by this author.', 'dynamic-content-for-elementor' ); ?></p>
							<?php endif;

						}
						?>
					</div>
				<?php } ?>
				</div>
			</div>
		<?php do_action( 'dce_after_content_inner' ); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
