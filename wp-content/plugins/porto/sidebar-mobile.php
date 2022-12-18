<?php
global $porto_settings, $porto_layout, $porto_sidebar, $porto_mobile_toggle;
?>
<div class="sidebar-overlay"></div>
<div class="mobile-sidebar">
	<?php if ( ! isset( $porto_mobile_toggle ) || false !== $porto_mobile_toggle ) : ?>
		<div class="sidebar-toggle"><i class="fa"></i></div>
	<?php endif; ?>
	<div class="sidebar-content">
		<?php
		do_action( 'porto_before_sidebar' );
		dynamic_sidebar( $porto_sidebar );
		do_action( 'porto_after_sidebar' );
		?>
	</div>
</div>
