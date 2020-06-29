<?php
global $porto_settings, $porto_layout, $porto_sidebar;
?>
<div class="sidebar-overlay"></div>
<div class="mobile-sidebar">
	<div class="sidebar-toggle"><i class="fa"></i></div>
	<div class="sidebar-content">
		<?php
		do_action( 'porto_before_sidebar' );
		dynamic_sidebar( $porto_sidebar );
		do_action( 'porto_after_sidebar' );
		?>
	</div>
</div>
