<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://wpali.com
 * @since      1.1.0
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/public/partials
 */
class wwob_woocommerce_custom_product_form {
	private $product_id = 0;

	public function __construct($product_id) {
		$this->product_id = $product_id;
	}

	public function get_form($options) {
		global $woocommerce;


		$product = null;

        $product = wc_get_product($this->product_id);

		$form_meta = $options;

		if (!empty($form_meta)) {


			echo '<div class="wwobform_variation_wrapper wwobform_wrapper left">';



			echo '<input type="hidden" name="product_id" value="' . $this->product_id . '" />';

			do_action( 'wwob_before_products_container', $form_meta, $this->product_id );

			echo '<ul id="wwobform_fields_' . $this->product_id . '" class="wwobform_fields">';

            wp_nonce_field('add_to_cart');


			$product_options_position = wwob_get_option( 'product_options_position' ) ? wwob_get_option( 'product_options_position' ) : "items";
			$woptions_status = get_post_meta( $this->product_id, 'wwob_extra_options', true );
			$woptions_type = get_post_meta( $this->product_id, 'wwob_additional_options', true );
			$image_preview = get_post_meta( $this->product_id, 'wwob_preview_image', true );
			if($image_preview == 'on'){
				wp_enqueue_script('wwob-zoom');
			}
			$one = 1;
			$zero = 0;
			$i = 0;

			do_action( 'wwob_before_products', $form_meta, $this->product_id );
			$images = array();
			foreach ($form_meta as $field ) {
				$one++;
				$zero++;

				$field_id = $zero;
				$form_id = $this->product_id;

				$item_container_padding = wwob_get_option( 'item_container_padding' ) ? wwob_get_option( 'item_container_padding' ) : "20";

				$field_label = !empty($field['wwob_field_label']) ? $field['wwob_field_label'] : "Product ".$one;
				$requiredornot = !empty($field['wwob_field_isRequired']) ? $field['wwob_field_isRequired'] : "";
				$placeholder_max_input_select = !empty($field['wwob_field_maxSelect']) ? $field['wwob_field_maxSelect'] : "";
				$placeholder_min_input_select = !empty($field['wwob_field_minSelect']) ? $field['wwob_field_minSelect'] : "";
				$quanitity = !empty($field['wwob_field_quantity']) ? $field['wwob_field_quantity'] : "";
				$quota = !empty($field['wwob_field_quota']) ? $field['wwob_field_quota'] : "";
				$description = !empty($field['wwob_field_description']) ? $field['wwob_field_description'] : "";

				$requiredornot1 = "<span class='wwobfield_required'>*</span>";
				$requiredornot2 = !empty($requiredornot) ? $requiredornot1 : '';

				$disable_images = get_post_meta( $form_id, 'wwob_disable_images', true );


				$content= "";
				$content .= '<li id="field_'. $form_id .'_'. $field_id .'" class="wwobfield wwobform_hidden wwobfield_price wwobfield_price_'. $form_id .'_'. $field_id .' wwobfield_product_'. $form_id .'_'. $field_id .'">';
				$content .= "<label class='wwobfield_label'  ><h3>".$field_label." ".$requiredornot2."</h3></label>";


				if ( has_filter( 'wwob_after_product_label') ){
					$content .= apply_filters( 'wwob_after_product_label', $form_id, $field, $field_id );
				}

				$content .= "<div class='wwobfield_description'><p>".$description."</p></div>";


				if ( has_filter( 'wwob_after_product_description') ){
					$content .= apply_filters( 'wwob_after_product_description', $form_id, $field, $field_id );
				}

				$content .= '<div class="wwobinput_container wwobinput_container_checkbox extended-checkboxes"><ul quantity="'. $quanitity .'"  quota="'. $quota .'" minselect="'. $placeholder_min_input_select .'" maxselect="'. $placeholder_max_input_select .'" class="wwobfield_checkbox" id="input_'. $form_id .'_'. $field_id .'">';


				$index0 = 0;
				$index1 = 1;


				if ( has_filter( 'wwob_before_product_item') ){
					$content .= apply_filters( 'wwob_before_product_item', $form_id, $field, $field_id );
				}

				if (!empty($field['wwob_product_field'])){
					foreach ($field['wwob_product_field'] as $choice) {
						$number = $index0++;
						$number1 = $index1++;
						$product_id = $choice['product-id'];
						$item_name = $choice['product-name'];
						$img_url = !empty($choice['product-photo']) ? $choice['product-photo'] : plugins_url( 'img/no-img.png', dirname(__FILE__) );
						$images['wwobchoice_'. $form_id .'_'. $field_id .'_'. $number1 .''] = $img_url;
						$img_preview_data = $image_preview == 'on' ? "data-thumb='". $img_url ."' data-src='". $img_url ."'" : '';
						$additional_class = $image_preview == 'on' ? " wwob_preview" : '';

						$price = !empty($choice['product-price']) ? $choice['product-price'] : "0";




						$symbol = get_woocommerce_currency_symbol();
						$sanitize_value = number_format($price, 2, '.', '');
						$sanitized_value = $symbol . $sanitize_value;

						$fieldid = 'item';

							$content .= "<li ".$img_preview_data." titletrigger ='". $form_id ."_". $field_id ."' class='wwobchoice_". $form_id ."_". $field_id ." wwobchoice_". $form_id ."_". $field_id ."_". $number1 ."".$additional_class."'><input name='input_". $fieldid ."_". $i++ ."' type='checkbox' ". $fieldid ." class ='checkbox-meta' value='". $product_id ."' details='" . $sanitize_value . "|" . $item_name . "|" . $field_label . "' id='choice_". $form_id ."_". $field_id ."_". $number1 ."' tabindex='". $number1 ."'    />";
							$content .= '<label class= "wwob-checkbox-label wwob-checkbox-img" for="choice_'. $form_id .'_'. $field_id .'_'. $number1 .'" id="label_'. $form_id .'_'. $field_id .'_'. $number1 .'" price="'. $sanitized_value .'">';
							if ($disable_images !== 'on' ){
								$content .='<img src="'. $img_url .'"  />';
							}

							if ( has_filter( 'wwob_after_product_item_image') ){
								$content .= apply_filters( 'wwob_after_product_item_image', $form_id, $choice );
							}

							$content .='<div class="label-meta-container"><p class="wwob-item-name">' . $item_name . '</p><span class="wwobinput_price">'.$sanitized_value.'</span>';

							if ( has_filter( 'wwob_after_product_item_price') ){
								$content .= apply_filters( 'wwob_after_product_item_price', $form_id, $choice );
							}

							$content .='</div></label>';
							$content .= "</li>";
					}
				}

				if ( has_filter( 'wwob_after_product_item') ){
					$content .= apply_filters( 'wwob_after_product_item', $form_id, $field );
				}

				$content .= "</ul></div><div class='clear'></div>";
				$content .= "</li>";

				$content = apply_filters( 'wwob_change_product_items_display', $content, $field, $form_id, $field_id, $i  );

			echo $content;

			}
			do_action( 'wwob_after_products', $form_meta, $this->product_id  );

			$extra_options = "";

			if ($product_options_position == "items"){
				if ($woptions_status == 'on' and !empty($woptions_type) ){
					$extra_options .= '<div class="wwob-accordion-container">';
					$extra_options .= '<a class="wwob-accordion">'.__( 'Additional Options', 'wpali-woocommerce-order-builder' ).'</a>';
					$extra_options .= '<div class="panel">';
					$extra_options .= $this->extra_options($this->product_id);
					$extra_options .= '</div>';
					$extra_options .= '</div>';
				}
			}
			echo apply_filters( 'wwob_change_options_accordion_display', $extra_options, $this->product_id, $this->extra_options($this->product_id) );

			$instructions = "";
			$instructions_status = get_post_meta( $this->product_id, 'wwob_product_instructions', true );
			if ($instructions_status == 'on' ){

				$customer_instructions = get_post_meta( $this->product_id, 'wwob_customer_instructions', true );
				if($customer_instructions == 'on'){
					$customer_req = get_post_meta( $this->product_id, 'wwob_customer_instructions_req', true );
					$customer_label = get_post_meta( $this->product_id, 'wwob_customer_instructions_label', true );
					$customer_text = get_post_meta( $this->product_id, 'wwob_customer_instructions_text', true );

					$isInstructionRequired = !empty($customer_req) ? 'required' : '';
					$isInstructionRequired = !empty($customer_req) ? 'options_required="on"' : '';
					$InstructionRequiredSymbol = !empty($isInstructionRequired) ? '*' : '';
					if (!empty($customer_label) and !empty($customer_text)){
						$instructions .= '<div class="wwob-accordion-container">';
						$instructions .= '<a class="wwob-accordion">'.$customer_label.'</a>';
						$instructions .= '<div class="panel">';
						$instructions .= '<ul class="wwob-option extra-option-text extra-option-instruction" '.$isInstructionRequired.'>';
						$instructions .= '<li class="wwob-customer-instruction">';
						$instructions .= '<textarea maxlength="150" name="special_instructions" id="special_instructions" rows="4" placeholder="'.htmlentities($customer_text).' (150 character limit) '.$InstructionRequiredSymbol.'"></textarea>';
						$instructions .= '</li>';
						$instructions .= '</ul>';
						$instructions .= '</div>';
						$instructions .= '</div>';
					}
				}

				$admin_instructions = get_post_meta( $this->product_id, 'wwob_admin_instructions', true );
				if($admin_instructions == 'on'){
					$admin_label = get_post_meta( $this->product_id, 'wwob_admin_instructions_label', true );
					$admin_text = get_post_meta( $this->product_id, 'wwob_admin_instructions_text', true );

					if (!empty($admin_label) and !empty($admin_text)){
						$instructions .= '<div class="wwob-accordion-container">';
						$instructions .= '<a class="wwob-accordion">'.$admin_label.'</a>';
						$instructions .= '<div class="panel">';
						$instructions .= '<p>'.$admin_text.'</p>';
						$instructions .= '</div>';
						$instructions .= '</div>';
					}
				}

			}

			echo apply_filters( 'wwob_change_instructions_accordion_display', $instructions, $this->product_id );

			do_action( 'wwob_after_extra_options', $form_meta, $this->product_id  );

			echo '</ul>';

			do_action( 'wwob_after_products_container', $form_meta, $this->product_id );

			echo '<input type="hidden" id="woocommerce_get_action" value="" />';
			echo '<input type="hidden" id="woocommerce_product_base_price" value="' . $product->get_price() . '" />';

			echo '<input type="hidden" name="wc_wwobforms_form_id"  value="' . $form_id . '" />';

			?>

			<?php
			$this->on_print_scripts_styles();
			if($image_preview == 'on'){
				?>
				<script>
				jQuery(document).ready(function($){
					if ("ontouchstart" in document.documentElement){
						// Don't do anything on touch screen (disable image preview)
					}else{
					<?php
						$images = array_filter($images);
						foreach($images as $key => $img){ ?>
							jQuery('.<?php echo $key; ?>').zoom({url: '<?php echo $img; ?>'});
					<?php
						} ?>
					};
				});
				</script>
				<?php
			}

			echo '</div>';

			// Product Calculator Display
				?>
				<div class="wwob-fixed-element"></div>
				<div id="wwob_sticky" class="right-price-calculation-area" >
				<div class="sidebar__inner sticky">
				<div class="product_totals" >
				<?php
					$currentform = $form_meta;
					$labels = array();
					$labelarray = array();
					$field_num = 1;
					$item_num = 1;

					foreach ($currentform as $fields ) {

						$FullLabel = !empty($fields['wwob_field_label']) ? $fields['wwob_field_label'] : "Product ".$item_num++;

						$AdminLabel_shortname =  $FullLabel;
						$FieldID =  $field_num++;

						$labelarray[] = array ($FieldID, $AdminLabel_shortname, $FullLabel);

						$result = array_merge($labels, $labelarray);
					}

					$num = 0;
					$post_id = get_the_ID();
					if (has_post_thumbnail( $post_id )){
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
						$thumbnial = $image[0];
					}else{
						$thumbnial = "";
					}

					echo '<div class="img-single" style="background-image: url('. $thumbnial .')"><div class="img-single-overlay">';
					echo '<h2 class="product-title">';
					echo ''. the_title() .'';
					echo '</h2></div></div>';
					echo '<div class="side-items wwobchoices">';


					foreach ($result as $label){
						$number = $num++;


						$content = "";
						$content .="<div class='sside side-items wwobchoice_". $this->product_id ."_". $label['0'] ."'>";
						$content .="<a href='#field_". $this->product_id ."_". $label['0'] ."'><h3>".$label['1']."</h3></a>";
						$content .="<span class='side-menu-items'></span>";
						$content .="</div>";
						echo $content;
					}
					echo '</div>';

				?>
				</div>
			<?php
			do_action( 'wwob_after_stickybar_items', $form_meta, $this->product_id );

			$product_options_position = wwob_get_option( 'product_options_position' ) ? wwob_get_option( 'product_options_position' ) : "items";

			if ($product_options_position == "sticky"){
				if ($woptions_status == 'on' and !empty($woptions_type) ){
					echo $this->extra_options($this->product_id);
				}
			}
		}
	}
	public function replaceSpecialChars($text) {
		$text = strtolower(htmlentities($text));
		$text = str_replace(get_html_translation_table(), "-", $text);
		$text = str_replace(" ", "-", $text);
		$text = preg_replace("/[-]+/i", "_", $text);
		return $text;
	}
	public function extra_options($product_id) {

		$extra_options_status = get_post_meta( $product_id, 'wwob_extra_options', true );



		if ($extra_options_status == 'on' ){

			$output = "";
			$output .="<div class='wwob-clearfix'></div>";
			$output .="<div class='extra-options-container'>";

			$selected_option_type = get_post_meta( $product_id, 'wwob_additional_options', true );

			if (!empty($selected_option_type)){

				foreach ( $selected_option_type as $type ) {
					switch($type){
						case 'color':

							$extra_options_color = get_post_meta( $product_id, 'wwob_available_colors_options', true );
							$wwob_available_colors_isrequired = get_post_meta( $product_id, 'wwob_available_colors_isrequired', true );
							$iscolorRequired = !empty($wwob_available_colors_isrequired) ? 'options_required="on"' : '';
							$colorRequiredSymbol = !empty($iscolorRequired) ? '*' : '';

							if( !empty($extra_options_color) ){
								$output .="<ul class='wwob-option extra-options-color' ".$iscolorRequired."><h3>Color ".$colorRequiredSymbol."</h3>";
								foreach ( $extra_options_color as $color ) {
									$output .='<li class="color" >';
									$output .='<input type="radio" class="color" name="wwob_color" alt="'.$color.'" id="wwob_'.$color.'_color" value="'.$color.'" >';
									$output .='<label for="wwob_'.$color.'_color" id="wwob_'.$color.'_color" class="color wwob-ready-options" />';
									$output .='</li>';
								}

								$output .="</ul>";
							}
							break;

						case 'size':

							$extra_options_size = get_post_meta( $product_id, 'wwob_available_sizes_options', true );
							$wwob_available_sizes_isrequired = get_post_meta( $product_id, 'wwob_available_sizes_isrequired', true );
							$issizeRequired = !empty($wwob_available_sizes_isrequired) ? 'options_required="on"' : '';
							$sizeRequiredSymbol = !empty($issizeRequired) ? '*' : '';

							if( !empty($extra_options_size) ){
								$output .="<ul class='wwob-option extra-options-size' ".$issizeRequired."><h3>Size ".$sizeRequiredSymbol."</h3>";
								foreach ( $extra_options_size as $size ) {
									$output .='<li class="size">';
									$output .='<input type="radio" class="size" name="wwob_size" alt="'.$size.'" id="wwob_'.$size.'_size" value="'.$size.'">';
									$output .='<label for="wwob_'.$size.'_size" id="wwob_'.$size.'_size" class="size wwob-ready-options" />';
									$output .='</li>';
								}
								$output .="</ul>";
							}
							break;

						case 'custom':
							$extra_options_custom = get_post_meta( $product_id, 'wwob_extra_custom_option', true );

							if( !empty($extra_options_custom) )
							$n = 0;
							foreach ( $extra_options_custom as $custom_option ) {

								if( !isset($custom_option['wwob_option_type']) ){
									continue;
								}

								$i = 0;

								$clean_label = !empty($custom_option['wwob_option_label']) ? $this->replaceSpecialChars($custom_option['wwob_option_label']) : $custom_option['wwob_option_type'];

								$isOptionRequired = !empty($custom_option['wwob_option_isrequired']) ? 'options_required="on"' : '';
								$RequiredSymbol = !empty($isOptionRequired) ? '*' : '';

								if (!empty($custom_option['wwob_option_choices']) and !empty($custom_option['wwob_option_label']) or $custom_option['wwob_option_type'] == 'text'){
									$output .="<ul class='wwob-option extra-option-".$custom_option['wwob_option_type']."' ".$isOptionRequired.">";
								}
								if ($custom_option['wwob_option_type'] == 'radio'){

									if (!empty($custom_option['wwob_option_choices']) and !empty($custom_option['wwob_option_label'])){

										$output .='<h3>'.$custom_option['wwob_option_label'].'<span style="color:red;">'.$RequiredSymbol.'</span></h3>';
										foreach ( $custom_option['wwob_option_choices'] as $custom ) {
											$i++;
											$output .='<li><input type="radio" name="wwob_choice_'.$clean_label.'" id="choice_'.$clean_label.'_'.$i.'_'.$n.'" value="'.$custom_option['wwob_option_label'].'|'. $custom .'"> <label for="choice_'.$clean_label.'_'.$i.'_'.$n.'">'. $custom .'</label></li>';
										}
									}
								}

								if ($custom_option['wwob_option_type'] == 'checkbox'){

									if (!empty($custom_option['wwob_option_choices']) and !empty($custom_option['wwob_option_label'])){

										$output .='<h3>'.$custom_option['wwob_option_label'].' <span style="color:red;">'.$RequiredSymbol.'</span></h3>';
										foreach ( $custom_option['wwob_option_choices'] as $custom ) {
											$i++;
										$output .='<li ><input type="checkbox" name="wwob_choice_'.$clean_label.'_'.$i.'_'.$n.'" id="choice_'.$clean_label.'_'.$i.'_'.$n.'" value="'.$custom_option['wwob_option_label'].'|'. $custom .'"> <label for="choice_'.$clean_label.'_'.$i.'_'.$n.'">'. $custom .'</label></li>';

									}

									}
								}

								if ($custom_option['wwob_option_type'] == 'select'){

									if (!empty($custom_option['wwob_option_choices']) and !empty($custom_option['wwob_option_label'])){

										$output .='<h3>'.$custom_option['wwob_option_label'].' <span style="color:red;">'.$RequiredSymbol.'</span></h3>';
										$output .='<li><select name="wwob_choice_'.$clean_label.'" id="choice_'.$clean_label.'_'.$n.'">';
										$output .='<option selected="true" disabled="disabled">'. $custom_option['wwob_option_label'] .'</option>';
										foreach ( $custom_option['wwob_option_choices'] as $custom ) {
											$output .='<option value="'.$custom_option['wwob_option_label'].'|'. $custom .'">'. $custom .'</option>';
										}
									}
									$output .='</select></li>';
								}

								if ($custom_option['wwob_option_type'] == 'text'){

									if (!empty($custom_option['wwob_option_choices']))
									if (!empty($custom_option['wwob_option_label'])){
										$output .='<h3>'.$custom_option['wwob_option_label'].' <span style="color:red;">'.$RequiredSymbol.'</span></h3>';
									}
									foreach ( $custom_option['wwob_option_choices'] as $custom ) {
										$i++;
										$output .='<li><input type="text" name="wwob_choice_'.$i.'_'.$n.'_'.$this->replaceSpecialChars($custom).'" id="choice_'.$clean_label.'_'.$i.'_'.$n.'" value="" placeholder="'. $custom .' '.$RequiredSymbol.'"></li><div class="clear"></div>';

									}
								}
								if (!empty($custom_option['wwob_option_choices']) and !empty($custom_option['wwob_option_label']) or $custom_option['wwob_option_type'] == 'text' ){
									$output .="</ul>";
								}
								$n++;
							}

							break;
					}
				}
			}
			$output .="</div><!-- /.extra-options-container -->";
			$output .="<div class='wwob-clearfix'></div>";
			return $output;
		}
	}

	public function on_print_scripts_styles() {
		// Stying
		$primary_color = wwob_get_option( 'primary_color' ) ? wwob_get_option( 'primary_color' ) : "#03d99d";
		$secondary_color = wwob_get_option( 'secondary_color' ) ? wwob_get_option( 'secondary_color' ) : "#1c0055";
		$items_container_bg = wwob_get_option( 'items_container_bg' );
		$items_label_color = wwob_get_option( 'items_label_color' );
		$items_description_color = wwob_get_option( 'items_description_color' );
		$item_bg = wwob_get_option( 'item_bg' ) ? wwob_get_option( 'item_bg' ) : "transparent";
		$item_text_color = wwob_get_option( 'item_text_color' );
		$item_container_padding = wwob_get_option( 'item_container_padding' ) ? wwob_get_option( 'item_container_padding' ) : "20";

		$siderbar_container_bg = wwob_get_option( 'siderbar_container_bg' );
		$siderbar_heading_color = wwob_get_option( 'siderbar_heading_color' ) ? wwob_get_option( 'siderbar_heading_color' ) : "#fff";
		$siderbar_heading_background = wwob_get_option( 'siderbar_heading_background' ) ? wwob_get_option( 'siderbar_heading_background' ) : "#333";
		$sidebar_top_position = wwob_get_option( 'sidebar_top_position' );

		$sticky_button_text_color = wwob_get_option( 'sticky_button_text_color' );
		$sticky_button_hover_text_color = wwob_get_option( 'sticky_button_hover_text_color' );
		$sticky_button_background = wwob_get_option( 'sticky_button_background' );
		$sticky_button_background_hover = wwob_get_option( 'sticky_button_background_hover' );

		// display
		$product_layout = wwob_get_option( 'product_layout' ) ? wwob_get_option( 'product_layout' ) : "wwob";
		$sidebar_background_display = wwob_get_option( 'sidebar_background_display' );
		$product_enhanced_calculator = wwob_get_option( 'product_enhanced_calculator' );

		// Responsiveness

		$items_height = wwob_get_option( 'items_height' ) ? wwob_get_option( 'items_height' ) : "";
		$tablet_items_height = wwob_get_option( 'tablet_items_height' ) ? wwob_get_option( 'tablet_items_height' ) : "";
		$mobile_items_height = wwob_get_option( 'mobile_items_height' ) ? wwob_get_option( 'mobile_items_height' ) : "";

		$sidebar_breakpoint = wwob_get_option( 'sidebar_breakpoint' ) ? wwob_get_option( 'sidebar_breakpoint' ) : "780";
		$items_breakpoint = wwob_get_option( 'items_breakpoint' );
		$items_mobile_breakpoint = wwob_get_option( 'items_mobile_breakpoint' );
		$tablet_items_per_row = wwob_get_option( 'tablet_items_per_row' );
		$mobile_items_per_row = wwob_get_option( 'mobile_items_per_row' );

		?>

<style>
<?php if(!empty($product_enhanced_calculator) and $product_enhanced_calculator == 'on'){?> .woocommerce .entry-summary .wwobfield.side-total-price {height: 60px;display: table;margin-top: 15px;margin-left: -18px;padding: 0;}ul.side_wwobform_totals.wwobform_fields li.wwobfield.side-total-price label.wwobfield_label, ul.side_wwobform_totals.wwobform_fields li.wwobfield.side-total-price .wwobinput_container {display: table-cell;vertical-align: middle;}ul.side_wwobform_totals.wwobform_fields li.wwobfield.side-total-price .wwobinput_container {font-size: 40px;}li.wwobfield.side-total-price label.wwobfield_label {-webkit-transform: rotate(90deg);-ms-transform: rotate(90deg);transform: rotate(90deg);font-weight: 800;text-transform: uppercase;}span.wwob-sticky-item-price {color: <?php echo $primary_color ?>;font-weight: bold;float: right;margin-right: 20px;}.woocommerce .sticky .side-items.wwobchoices a h3, .woocommerce .sticky h3 {font-size: 18px!important;}.woocommerce .sticky span.side-menu-items {border-left: 3px solid <?php echo $primary_color ?>;padding-left: 10px;margin-left: 3px;} <?php }?>
<?php if(!empty($primary_color)){ ?>
.extended-checkboxes ul.wwobfield_checkbox li .selected-product span.selected-product-checked { background-color: <?php echo $primary_color ?>!important;}
.woocommerce .sticky .side-items.wwobchoices a h3:hover, .woocommerce .sticky .side-items.wwobchoices a.selected-choice h3 { color: <?php echo $primary_color ?>; }
.wwobform_wrapper ul.wwobform_fields li.wwobfield label.wwobfield_label { color: <?php echo $primary_color ?>!important; }
ul.side_wwobform_totals.wwobform_fields li.wwobfield.side-total-price label.wwobfield_label, ul.side_wwobform_totals.wwobform_fields li.wwobfield.side-total-price .wwobinput_container { color: <?php echo $primary_color ?>!important; }
<?php }?>
<?php if(!empty($item_text_color)){ ?>
label.wwob-checkbox-label p.wwob-item-name, span.wwobinput_price{ color: <?php echo $item_text_color ?>; }
<?php }?>
<?php if(!empty($secondary_color)){ ?>
.woocommerce .sticky .side-items.wwobchoices a h3, .woocommerce .sticky  h3 { color: <?php echo $secondary_color ?>; }
.woocommerce .sticky span.side-menu-items{ color: <?php echo $secondary_color ?>; }
<?php }?>
<?php if(!empty($siderbar_container_bg)){ ?>
.right-price-calculation-area .sticky { background: <?php echo $siderbar_container_bg ?>!important; }
<?php }?>
<?php if(!empty($primary_color) and empty($items_label_color) ){ ?>
.wwobform_wrapper ul.wwobform_fields li.wwobfield label.wwobfield_label h3, .wwobform_variation_wrapper.wwobform_wrapper.left h3 { color: <?php echo $primary_color ?>; }
<?php }?>
<?php if(!empty($items_label_color)){ ?>
.wwobform_wrapper ul.wwobform_fields li.wwobfield label.wwobfield_label h3, .wwobform_variation_wrapper.wwobform_wrapper.left h3 { color: <?php echo $items_label_color ?>!important; }
<?php }?>
<?php if(!empty($secondary_color) and empty($items_description_color)){ ?>
.wwobfield_description {color: <?php echo $secondary_color ?>;}
<?php }?>
<?php if(!empty($items_description_color)){ ?>
.wwobfield_description { color: <?php echo $items_description_color ?>!important; }
<?php }?>
<?php if(!empty($siderbar_heading_background)){ ?>
.product_totals .img-single{ background-color: <?php echo $siderbar_heading_background ?>; }
<?php }?>
<?php if(!empty($items_container_bg)){?>
.wwobform_variation_wrapper.wwobform_wrapper.left ul.wwobform_fields li.wwobfield, .max-reached-disabled-product { background: <?php echo $items_container_bg ?>!important; }
<?php }?>
.extended-checkboxes ul.wwobfield_checkbox li { <?php if(!empty($item_bg)){ ?> background: <?php echo $item_bg; ?>!important; <?php }?> <?php if(!empty($items_height)){ ?>	height: <?php echo $items_height; ?>px!important; <?php }?> }
.img-single-overlay h2.product-title { <?php if(!empty($siderbar_heading_color)){ ?> color: <?php echo $siderbar_heading_color; ?>!important; <?php }?> }
.woocommerce div.product form.cart .sticky .button{ <?php if(!empty($sticky_button_text_color)){ ?> color: <?php echo $sticky_button_text_color; ?>!important; <?php }?> <?php if(!empty($sticky_button_background)){ ?> background: <?php echo $sticky_button_background; ?>!important; <?php }?> }
.woocommerce div.product form.cart .sticky .button:hover{ <?php if(!empty($sticky_button_hover_text_color)){ ?> color: <?php echo $sticky_button_hover_text_color; ?>!important; <?php }?> <?php if(!empty($sticky_button_background_hover)){ ?> background: <?php echo $sticky_button_background_hover; ?>!important; <?php }?> }
<?php if(!empty($item_container_padding)){ ?>
.wwobform_variation_wrapper.wwobform_wrapper.left ul.wwobform_fields li.wwobfield { padding: <?php echo $item_container_padding ?>px!important; }
<?php }?>
<?php if($sidebar_background_display == 'hide'){ ?>
.woocommerce .product_totals .img-single { background-image: none!important; }
.woocommerce .img-single-overlay { background-image: none!important; padding: 25px 30px; }
<?php } ?>
<?php if(!empty($sidebar_breakpoint)){?>
@media screen and (max-width: <?php echo $sidebar_breakpoint ?>px){ .wwobform_variation_wrapper.wwobform_wrapper.left { width: 100%; margin: 0px;}.woocommerce .right-price-calculation-area .sticky {float: none;position: static;margin-top: 30px;width: 100%;}.woocommerce .right-price-calculation-area {width: 100%;}
}
<?php }?>
<?php if(!empty($items_breakpoint)){
	$breakpoint2 = $items_breakpoint;

}else{
	$breakpoint2 = '768';

}?>
@media screen and (max-width: <?php echo $breakpoint2 ?>px){
<?php
	if( (empty($tablet_items_per_row)) or ($tablet_items_per_row == '3') ){
		$tablet_width = '31.3';
	}else{
		$tablet_width = '48';
	}
?>
.extended-checkboxes ul.wwobfield_checkbox li { <?php if(!empty($tablet_items_height)){ ?> height:<?php echo $tablet_items_height;?>px!important;<?php } ?> width: <?php if(!empty($tablet_width)){ echo $tablet_width;} ?>%;}}

<?php if(!empty($items_mobile_breakpoint)){
	$breakpoint3 = $items_mobile_breakpoint;
}else{
	$breakpoint3 = '480';
}?>
@media screen and (max-width: <?php echo $breakpoint3 ?>px){ <?php
	if( (empty($mobile_items_per_row)) or ($mobile_items_per_row == '2') ){
		$mobile_width = '48';
	}else{
		$mobile_width = '98';
	}
?>
.extended-checkboxes ul.wwobfield_checkbox li { <?php if(!empty($mobile_items_height)){ ?> height:<?php echo $mobile_items_height;?>px!important;<?php } ?> width: <?php if(!empty($mobile_width)){ echo $mobile_width; } ?>%; } }
<?php if($product_layout == "wwob"){ ?> @media screen and (min-width: 48em){.woocommerce #content div.product div.summary, .woocommerce div.product div.summary, .woocommerce-page #content div.product div.summary, .woocommerce-page div.product div.summary {float: none!important;width: 100%!important;margin-left: 0px;margin-right: 0px;}} <?php }else{ ?> div#wwob_sticky {width: 100%;}body .wwobform_variation_wrapper.wwobform_wrapper.left {width: 100%;margin-right: 0px;} <?php } ?>
</style>
<script>
<?php if($product_layout == "woocommerce"){ ?> var WooCommerceLayout = "enabled";  <?php }?>
<?php if(!empty($sidebar_top_position)){?> var topPosition = <?php echo $sidebar_top_position ?>; <?php }?>
<?php if(!empty($sidebar_breakpoint)){?> var Stickybreakpoint = <?php echo $sidebar_breakpoint ?>; <?php }?>
<?php if(!empty($product_enhanced_calculator) and $product_enhanced_calculator == 'on'){?> var EnhancedCalculator = "enabled"; <?php }?>
</script>
		<?php
	}


}
?>
