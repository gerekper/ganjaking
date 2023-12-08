<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="info-banner-content-wrapper" >
	<div class="info-banner-front-content">
	<?php echo $list_img.$list_title.$list_sub_title; ?>
	</div>
	<?php $featured_image_src='';
		if(!empty($featured_image)){$featured_image_src = 'style="background-image: url('.$featured_image.');"';}
	?>
	<div class="info-banner-back-content" <?php echo $featured_image_src; ?>>
		<div class="info-banner-back-content-inner">
			<?php echo $description.$loop_button; ?>
		</div>
	</div>

</div>
