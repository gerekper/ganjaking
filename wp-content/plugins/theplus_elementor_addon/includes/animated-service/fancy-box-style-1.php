<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="fancybox-inner-wrapper">
		<div class="fancybox-image-background" style="background-image:url(<?php echo $featured_image;?>)"></div>
		<div class="fancybox-inner-content">
			<div class="fb-content"><?php echo $list_title.$list_sub_title.$description; ?></div>
			<div class="fb-button"><?php echo $loop_button; ?></div>
		</div>
</div>