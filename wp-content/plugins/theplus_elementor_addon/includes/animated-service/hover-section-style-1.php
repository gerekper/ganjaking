<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="hover-section-content-wrapper" data-image="<?php echo $featured_image; ?>">
	<?php echo $list_img.$list_title;
	echo '<div class="hover-content-inner-hover">'.$list_sub_title.$description.$loop_button.'</div>'; 
	
	if(!empty($hspi) && $hspi=='yes'){
		echo '<img style="display:none;" src="'.$featured_image.'" />';
	} 
	?>
</div>
