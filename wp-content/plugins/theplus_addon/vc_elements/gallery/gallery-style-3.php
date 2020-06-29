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
		</div>
	</div>
</div>
