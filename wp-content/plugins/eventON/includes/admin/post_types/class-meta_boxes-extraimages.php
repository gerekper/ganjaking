<?php
/**
 * Event Extra Images Meta Box
 */


$event_id = $this->EVENT->ID;

$after = '';

$thumbnail_id = get_post_thumbnail_id();

if(!apply_filters('evo_eventedit_feature_img_id_check',$thumbnail_id) ){
}else{
	ob_start();

	echo "<div class='evo_edit_field_box'>";

	$saved = $this->EVENT->get_prop('_evo_images');

	// compatibility with old event photos addon
		$evoep_imgs = $this->EVENT->get_prop('evoep_images');

		if( $evoep_imgs){
			$saved = $evoep_imgs. $saved;
			update_post_meta( $event_id, '_evo_images', $saved);
			delete_post_meta($event_id, 'evoep_images');

			echo "<p style='background-color: #f73436;color: #fff;padding: 15px; margin: 15px -12px;'>! Images set via photos addon has been moved over to here. From now on additional images for event can be managed in here</p>";
		}			

	echo "<div class='evo_event_images'>
		<input type='hidden' name='_evo_images' value='". $saved."'/>
		<div class='evo_event_image_holder'>";

			$imgs = explode(',', $saved);
			$imgs = array_filter($imgs);
			foreach($imgs as $img){
				$caption = get_post_field('post_excerpt',$img);
				$url = wp_get_attachment_thumb_url($img);
				
				echo "<span data-imgid='{$img}'><b class='remove_event_add_img'>X</b><img title='{$caption}' data-imgid='{$img}' src='{$url}'></span>";
			}

		echo "</div>
	</div>";

	do_action('evo_more_images_before_btn', $this->EVENT);

	echo "<span class='evo_btn evo_add_more_images'>".__('Add Extra Images','eventon') ."</span>";

	echo "<span class='evo_event_images_notice'></span>";

	do_action('evo_more_images_end', $this->EVENT);

	echo "</div>";

	$after = ob_get_clean();


}


echo $after;