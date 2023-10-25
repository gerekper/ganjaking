<?php
// * Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)

/**
 *  Add GravityForms section
 */

add_filter( 'seed_cspv5_legacy_enable_wp_head_footer_list', 'seed_cspv5_legacy_enable_wp_head_footer_ninjaforms' );

function seed_cspv5_legacy_enable_wp_head_footer_ninjaforms( $arr ) {
	$arr[] = 'ninjaforms';
	return $arr;
}

function seed_cspv5_legacy_section_ninjaforms( $emaillist, $page_id ) {
	// Get settings
	$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
	$settings      = get_option( $settings_name );
	if ( ! empty( $settings ) ) {
		$settings = maybe_unserialize( $settings );
	}
	ob_start();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Ninja Forms', 'seedprod' ); ?></h3>
		<div class="inside">
		<!-- <p><?php __( 'Configure saving subscribers to Ninja Forms options. <a href="http://support.seedprod.com/article/25-embedding-ninja-forms" target="_blank">Learn More</a>', 'seedprod' ); ?></p> -->
		<form id="seed_cspv5_emaillist_settings">
		<input type="hidden" id="settings_name" name="settings_name" value="<?php echo $settings_name; ?>"/>
		<input type="hidden" id="page_id" name="page_id" value="<?php echo $page_id; ?>"/>
		<input type="hidden" id="emaillist" name="emaillist" value="<?php echo $emaillist; ?>"/>
		<!--<p><a href="http://support.seedprod.com/article/25-embedding-ninja-forms" target="_blank">Learn how to Configure</a></p>-->
		<table class="form-table">
			<tbody>
<!--                 <tr valign="top">
					<th scope="row">
						<strong><?php _e( "Override Gravity Form's confirmation page", 'seedprod' ); ?></strong>
					</th>
					<td>
						<input class="" type="checkbox" id="ninjaforms_enable_thankyou_page" name="ninjaforms_enable_thankyou_page" value="1" <?php echo ( ! empty( $settings['ninjaforms_enable_thankyou_page'] ) ) ? 'checked' : ''; ?>>
						<br>
						<small class="description">Redirect to this plugin's Thank You page instead of the default Gravity Forms Confirmation page.</small>
					</td>
				</tr> -->
				<tr valign="top">
					<th scope="row">
						<strong><?php _e( 'Form', 'seedprod' ); ?></strong>
					</th>
					<td>
						<?php
						$lists = seed_cspv5_get_ninjaforms_forms();
						seed_cspv5_select( 'ninjaforms_form_id', $lists, ( ! empty( $settings['ninjaforms_form_id'] ) ) ? $settings['ninjaforms_form_id'] : '' );
						?>
					</td>
				</tr>


			</tbody>
		</table>
		</form>
		<p>
			<input id="save-btn" type="submit" value="Save and Continue Editing" class="button-primary" >
			<button id="cancel-btn" class="button-secondary">Cancel</button>
		</p>
		</div>
	</div>

	<?php
	$return_url = preg_replace( '/seed_cspv5_customize=\d*(.)*/', 'seed_cspv5_customize=' . $page_id . '&tab=form', urldecode( $_GET['return'] ) );
	?>
	<script>
	<?php $save_ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seed_cspv5_save_emaillist_settings', 'seed_cspv5_save_emaillist_settings' ) ); ?>
	var save_url = '<?php echo $save_ajax_url; ?>';
	<?php $get_list_ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seed_cspv5_get_email_lists', 'seed_cspv5_get_email_lists' ) ); ?>
	var get_list_url = '<?php echo $get_list_ajax_url; ?>';
   
	jQuery( "#save-btn" ).click(function() {
		jQuery(this).prop( "disabled", true );
		var dataString = jQuery( '#seed_cspv5_emaillist_settings' ).serialize();
		var jqxhr = jQuery.post( save_url, dataString)
		  .done(function(data) {
			  if(data == '1'){
				 window.location.href = '<?php echo $return_url; ?>'; 
			  }else{
				  alert('Error. Please try again.');
			  }
		  })
		  .fail(function() {
			  alert('Error. Please try again.');
		  })
		  .always(function() {
			jQuery('#save-btn').prop( "disabled", false );
		});
	});
	
	jQuery( "#cancel-btn" ).click(function(e) {
		e.preventDefault();
		window.location.href = '<?php echo $return_url; ?>';
	});

	</script>
	<?php

	return $output = ob_get_clean();

}


function seed_cspv5_legacy_get_ninjaforms_forms() {
	if ( class_exists( 'Ninja_Forms' ) ) {
		$forms  = array();
		$gforms = Ninja_Forms()->form()->get_forms();

		foreach ( $gforms as $k => $v ) {
			;
			$forms[ $v->get_id() ] = $v->get_setting( 'title' );
		}
	} else {
		$forms = array( '-1' => 'No Forms Found' );
	}
	return $forms;
}


//add_action('gform_after_submission', 'seed_cspv5_after_ninja_subscribed_record_record_into_cspv5', 11, 2);

function seed_cspv5_legacy_after_ninja_subscribed_record_record_into_cspv5( $entry, $form ) {

		global $seed_cspv5_post_result;

		// Page Settings
		$settings = $args['settings'];
		extract( $settings );

		//  Emaillist Settings
		$settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
		$e_settings    = get_option( $settings_name );
		$e_settings    = maybe_unserialize( $e_settings );
		extract( $e_settings );

	if ( ( ( ! empty( $status ) && $status === '1' ) || ( ! empty( $status ) && $status === '2' ) ) || ( isset( $_GET['seed_cspv5_preview'] ) && $_GET['seed_cspv5_preview'] == 'true' ) ) {
		if ( isset( $ninjaforms_form_id ) && ( $form['id'] == $ninjaforms_form_id ) ) {

			if ( $ninjaforms_enable_thankyou_page ) {

				$data = array();
				foreach ( $form['fields'] as $k => $v ) {
					if ( $v['type'] == 'name' ) {
						if ( ! empty( $entry[ $v['id'] . '.3' ] ) ) {
							$data['fname'] = $entry[ $v['id'] . '.3' ];
						}
						if ( ! empty( $entry[ $v['id'] . '.6' ] ) ) {
							$data['lname'] = $entry[ $v['id'] . '.6' ];
						}
					}
					if ( $v['type'] == 'email' ) {
						if ( ! empty( $entry[ $v['id'] ] ) ) {
							$data['email'] = $entry[ $v['id'] ];
						}
					}
				}

				if ( ! empty( $data ) ) {
					$data['gf'] = '1';
				}

				// If tracking enabled
				if ( ! empty( $enable_reflink ) || ! empty( $display_optin_confirm ) ) {
					$_REQUEST['email'] = $data['email'];
					seed_cspv5_legacy_emaillist_database_add_subscriber( $settings );
				}

				$seed_cspv5_post_result['post']   = 'true';
				$seed_cspv5_post_result['status'] = '200';

			}
		}
	}
}
