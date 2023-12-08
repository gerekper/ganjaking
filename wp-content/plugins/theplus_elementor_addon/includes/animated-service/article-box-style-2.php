<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="article-box-main">
	<div class="article-box-main-wrapper" style="background:url(<?php echo $featured_image;?>)center/cover;">
		<div class="article-box-front-wrapper"><?php echo $list_img.$list_title.$list_sub_title; ?></div>
		<div class="article-box-hover-wrapper">
			<?php echo $description.$loop_button; ?>
		</div>
	</div>	
</div>

