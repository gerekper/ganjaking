<?php

	add_action('userpro_modify_search_filters', 'userpro_modify_search_filters');
	function userpro_modify_search_filters($args){
		global $userpro_emd;

		$big_array = userpro_retrieve_metakeys();
		foreach($args as $key=>$v) {
			if (in_array($key, $big_array) || in_array( str_replace('search_','',$key), $big_array) ) {

				$options = explode(',', $args[$key]);
				if (!isset($options[1])){
					$type = 'input';
				} else {
					$type = $options[1];
				}
				if (in_array($key, $userpro_emd->must_be_custom_fields)) $type = 'custom';
				$label = $options[0];
				if ($label) {
				$array[ $key ] = array('label' => $label, 'type' => $type );
				}

			}

			if (in_array($key, $big_array) || in_array( str_replace('search_from_','',$key), $big_array) || in_array( str_replace('search_to_','',$key), $big_array) ) {

				$options = explode(',', $args[$key]);
				if (!isset($options[1])){
					$type = 'input';
				} else {
					$type = $options[1];
				}
				if (in_array($key, $userpro_emd->must_be_custom_fields)) $type = 'custom';
				$label = $options[0];
				if ($label) {
				$array[ $key ] = array('label' => $label, 'type' => $type );
				}

			}
		}

		if (isset($array) && userpro_retrieve_metakeys()){
			foreach(userpro_retrieve_metakeys() as $key){
				unset($array[$key]); // remove native keys
			}
		}

		if (isset($array) && isset($array['role'])){
		unset($array['role']); // remove role
		}

		if (isset($array) && is_array($array)){
		foreach( $array as $custom_field => $data ) {
			$custom_field = str_replace('search_','',$custom_field);
			$purekey = str_replace('from_','',$custom_field);
			$purekey = str_replace('to_','',$purekey);
		?>

		<div class="emd-filter">

			<div class="emd-filter-head"><?php echo $data['label']; ?></div>

			<?php if ($data['type'] == 'dropdown') { ?>

			<?php if ( strstr($custom_field, 'from_') || strstr($custom_field, 'to_') ) { ?>

			<?php
			if (isset( $args[ $purekey . '_range'])) {
				$range = $args[ $purekey . '_range'];
			} else {
				$range = '1,100';
			}
			?>

			<select name="emd-<?php echo $custom_field; ?>" id="emd-<?php echo $custom_field; ?>" class="chosen-select-compact" data-placeholder="<?php echo $data['label']; ?>">
				<?php $userpro_emd->loop_options( $custom_field, $num_range = $range ); ?>
			</select>

			<?php } else { ?>

			<select name="emd-<?php echo $custom_field; ?>" id="emd-<?php echo $custom_field; ?>" class="chosen-select" data-placeholder="<?php echo $data['label']; ?>">
                <?php $userpro_emd->loop_options( $custom_field, null, $data['label']  ); ?>
			</select>

			<?php } ?>

			<?php } ?>

			<?php if ($data['type'] == 'radio') { ?>
			<?php $userpro_emd->loop_options_radio( $custom_field ); ?>
			<?php } ?>

			<?php if ($data['type'] == 'input') { ?>
			<div class="userpro-input">
				<input type="text" name="emd-<?php echo $custom_field; ?>" id="emd-<?php echo $custom_field; ?>" placeholder="<?php echo $data['label']; ?>" value="<?php echo $userpro_emd->try_text_value( $custom_field ); ?>" />
			</div><div class="userpro-clear"></div>
			<?php } ?>

			<?php if ($data['type'] == 'custom') { ?>
			<?php $userpro_emd->loop_custom_options( $custom_field ); ?>
			<?php } ?>

		</div>

		<?php
		}
		}

	}
