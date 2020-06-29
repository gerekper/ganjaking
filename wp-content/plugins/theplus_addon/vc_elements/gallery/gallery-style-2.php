<?php 
	$data_attr='';
	if($layout=='metro'){
		if ( $images ) {
			$data_attr='style="background :url('.esc_url($images[0]).')";';
		}
	}
?>
<div class="gallery-attach-list">
	<div class="gallery-style-content" <?php echo $data_attr; ?>>
		<div class="post-content">
			<?php
				if (!empty($images) && $layout!='metro') { ?>
					<div class="gallery-image" >
						<?php echo '<img src="'.esc_url($images[0]).'" alt="'.esc_attr($image_alt).'">'; ?>
					</div>
				<?php } ?>
				<div class="gallery-hover-content">
					<div class="title-wrap">
						<?php if(!empty($title)){ ?>
							<h3 class="media-title"><span><?php echo esc_html($title); ?></span></h3>
						<?php } ?>
						<?php if(!empty($description)){ ?>
							<div class="media-description"><span><?php echo esc_html($description); ?></span></div>
						<?php } ?>
					</div>
					<div class="gallery-icons">
						<a href="<?php echo esc_url($full_image[0]); ?>" class="pop-icon prettyphoto" <?php echo $pretty_rel_random; ?>><i class="fa fa-clone"></i></a>
					</div>
				</div>
		</div>
	</div>
</div>
