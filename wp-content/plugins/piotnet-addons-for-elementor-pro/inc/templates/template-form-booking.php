<?php
	function pafe_template_form_booking($settings, $element_id, $post_id, $date = '', $form_id) {
		if (!is_array($settings)) {
			$settings = json_decode(stripslashes($settings),true);
		}
?>
<?php if (!empty($settings['pafe_form_booking_field_label_show'])) : ?>
<label class="elementor-field-label"><?php echo $settings['pafe_form_booking_field_label']; ?></label>
<?php endif; ?>
<form class="pafe-form-booking__inner">
<div data-pafe-form-builder-required></div>
<?php 
	foreach ( $settings['pafe_form_booking'] as $key => $item ) :
		$item['pafe_form_booking_id'] =  $settings['pafe_form_booking_id'];
		$item['pafe_form_booking_date'] = $date;
		$item['pafe_form_booking_element_id'] = $element_id;
		$item['pafe_form_booking_post_id'] = $post_id;
		$item['pafe_form_booking_title'] = !empty($item['pafe_form_booking_title']) ? $item['pafe_form_booking_title'] : $item['pafe_form_booking_slot_id'];

		if (empty($settings['pafe_form_booking_field_allow_multiple']) && !empty($settings['pafe_form_booking_slot_quantity_field'])) {
			$item['pafe_form_booking_slot_quantity_field'] =  $settings['pafe_form_booking_slot_quantity_field'];
		}

		if ($settings['pafe_form_booking_date_type'] == 'specify_date') {
			$date = date( "Y-m-d", strtotime( $settings['pafe_form_booking_date'] ) );
			$item['pafe_form_booking_date'] = $date;
		} else {
			$item['pafe_form_booking_date_field'] = $settings['pafe_form_booking_date_field'];
			
			if (empty($date)) {
				$date = date( "Y-m-d", strtotime("now") );
			} else {
				$date = date( "Y-m-d", strtotime($date) );
			}
		}
		$slot_availble = 0;
		$slot = $item['pafe_form_booking_slot'];
		$slot_query = new WP_Query(array(  
			'posts_per_page' => -1 , 
			'post_type' => 'pafe-form-booking',
			'meta_query' => array(                  
		       'relation' => 'AND',                 
			        array(
			            'key' => 'pafe_form_booking_id',                
			            'value' => $item['pafe_form_booking_id'],                  
			            'type' => 'CHAR',
			            'compare' => '=',
			        ),
			        array(
			            'key' => 'pafe_form_booking_slot_id',                  
			            'value' => $item['pafe_form_booking_slot_id'],                  
			            'type' => 'CHAR',
			            'compare' => '=',                  
			        ),
			        array(
			            'key' => 'pafe_form_booking_date',                  
			            'value' => $date,                  
			            'type' => 'CHAR',
			            'compare' => '=',                
			        ),
			        array(
			            'key' => 'payment_status',                  
			            'value' => 'succeeded',                  
			            'type' => 'CHAR',                  
			            'compare' => '=',                
			        ),
			),	
		));

		$num = 0;

		if ($slot_query->have_posts()) {
			while($slot_query->have_posts()) {
				$slot_query->the_post();
				$num += intval( get_post_meta(get_the_ID(), 'pafe_form_booking_quantity', true) );
			}
		}

		wp_reset_postdata();

		$slot_availble = $slot - $num;
?>	
	<div class="pafe-form-booking__item<?php if(empty($slot_availble)) { echo ' pafe-form-booking__item--disabled'; } ?>">
		<div class="pafe-form-booking__item-inner">
			<input type="checkbox"<?php if(empty($slot_availble)) { echo ' disabled'; } ?> value="<?php echo $item['pafe_form_booking_title']; ?>" data-value="<?php echo $item['pafe_form_booking_title']; ?>" id="form-field-<?php echo $item['pafe_form_booking_id']; ?>-<?php echo $key; ?>" name="form_fields[<?php echo $item['pafe_form_booking_id']; ?>][]" data-pafe-form-builder-default-value="<?php echo $item['pafe_form_booking_title']; ?>" data-pafe-form-builder-form-booking-price="<?php echo $item['pafe_form_booking_price']; ?>" data-pafe-form-builder-form-id="<?php echo $form_id; ?>" data-pafe-form-booking-item data-pafe-form-booking-item-options='<?php echo json_encode( $item, JSON_UNESCAPED_UNICODE ); ?>'<?php if(empty($settings['pafe_form_booking_field_allow_multiple'])) { echo ' data-pafe-form-booking-item-radio'; } ?> data-pafe-form-builder-form-booking-availble="<?php echo $slot_availble; ?>">
			<?php if (!empty($item['pafe_form_booking_title'])) : ?>
				<div class="pafe-form-booking__title"><?php echo $item['pafe_form_booking_title']; ?></div>
			<?php endif; ?>
			<?php if (!empty($settings['pafe_form_booking_field_slot_show'])) : ?>
				<div class="pafe-form-booking__slot" data-pafe-form-booking-slot>
					<?php if (!empty($slot_availble) || empty($settings['pafe_form_booking_sold_out_text'])) : ?>
						<span class="pafe-form-booking__slot-before"><?php echo $settings['pafe_form_booking_before_number_of_slot']; ?></span>
						<span class="pafe-form-booking__slot-number"><?php echo $slot_availble; ?></span>
						<span class="pafe-form-booking__slot-after"><?php echo $settings['pafe_form_booking_after_number_of_slot']; ?></span>
					<?php else : ?>
						<span class="pafe-form-booking__slot-sold-out"><?php echo $settings['pafe_form_booking_sold_out_text']; ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if (!empty($settings['pafe_form_booking_field_price_show']) && !empty($item['pafe_form_booking_price_text'])) : ?>
				<div class="pafe-form-booking__price">
                    <?php echo $item['pafe_form_booking_price_text']; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>	
</form>

<?php
	}
?>