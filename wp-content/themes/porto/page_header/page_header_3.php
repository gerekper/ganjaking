<?php
global $porto_settings;

$breadcrumbs = $porto_settings['show-breadcrumbs'] ? porto_get_meta_value( 'breadcrumbs', true ) : false;
$page_title  = $porto_settings['show-pagetitle'] ? porto_get_meta_value( 'page_title', true ) : false;

if ( ( is_front_page() && is_home() ) || is_front_page() ) {
	$breadcrumbs = false;
	$page_title  = false;
}

$title      = isset( $porto_shortcode_title ) ? $porto_shortcode_title : porto_page_title();
$sub_title  = isset( $porto_shortcode_sub_title ) ? $porto_shortcode_sub_title : porto_page_sub_title();
$hide_title = ! $title || ! $page_title;

if ( isset( $is_shortcode ) ) {
	$hide_title  = isset( $hide_page_title ) ? true : false;
	$breadcrumbs = isset( $hide_breadcrumb ) ? false : true;
}
?>
<div class="container<?php echo ! $hide_title ? '' : ' hide-title'; ?>">
	<div class="row">
		<div class="col-lg-12">
			<div class="text-center<?php echo ! $hide_title ? '' : ' d-none'; ?>">
				<h1 class="page-title<?php echo ! $sub_title ? '' : ' b-none'; ?>"><?php echo porto_strip_script_tags( $title ); ?></h1>
				<?php
				if ( $sub_title ) :
					?>
					<p class="page-sub-title"><?php echo porto_strip_script_tags( $sub_title ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $breadcrumbs ) : ?>
				<div class="breadcrumbs-wrap text-center<?php echo ! $sub_title ? '' : ' breadcrumbs-with-subtitle'; ?>">
					<?php echo porto_breadcrumbs(); ?>
				</div>
			<?php endif; ?>
			<?php
			porto_breadcrumbs_filter();
			?>
		</div>
	</div>
</div>
