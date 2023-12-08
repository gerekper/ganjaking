<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="se-wrapper" >
	<div class="se-first-section">
		<div class="se-icon">
			<?php echo $list_img; ?>
			<div class="se-title-desc">
				<?php echo $list_title.$list_sub_title.$description.$loop_button; ?>
			</div>
		</div>
	</div>
	<div class="se-listing-section">		
		<?php echo $se_listing; ?>
	</div>
</div>
	
