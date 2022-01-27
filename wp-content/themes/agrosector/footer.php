        </div><!-- .main_wrapper -->
	</div><!-- .site_wrapper -->
	<?php
		$mb_footer_switch = class_exists('RWMB_Loader') ? rwmb_meta('mb_footer_switch') : '';

		if(gt3_option('back_to_top') == '1' && $mb_footer_switch != 'no'){
			echo "<div class='back_to_top_container'>";
				echo "<a href='" . esc_js("javascript:void(0)") . "' class='gt3_back2top' id='back_to_top'></a>";
			echo "</div>";
		}
		gt3_footer_area(); 
		if (class_exists('Woocommerce') && is_product()) do_action( 'gt3_footer_action' );
				
	wp_footer();
    ?>
</body>
</html>