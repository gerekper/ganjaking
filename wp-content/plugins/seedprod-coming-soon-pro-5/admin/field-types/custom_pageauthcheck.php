<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
// if(empty($options[ $id ])){
// 	$options[ $id ] = '';
// }
// $seed_update_msg = get_option('seedprod-coming-soon-pro_update_msg');
// if(!empty($seed_update_msg)){
// 	echo $seed_update_msg.'<br>';
// }
$ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv5_page_auth','seed_cspv5_page_auth'));

echo "<input autocomplete='off' id='$id' class='" . ( empty( $class ) ? 'regular-text' : $class ) . "' name='{$setting_id}[$id]' type='text' value='" . esc_attr( $options[ $id ] ) . "' />";
echo "<button id='seed_cspv5_check_license' type='button' class='button-secondary'>".__('Authorize Page','seedprod')."</button><br>";
echo "<div id='seed_cspv5_check_license_msg'></div>";
?>
<script type='text/javascript'>
jQuery(document).ready(function($) {
    $('#seed_cspv5_check_license').click(function() {
    	$('#seed_cspv5_check_license').prop("disabled", true);
      $('#seed_cspv5_check_license_msg').hide();
    	auth_code = $('#auth_code').val();
    	if(auth_code != ''){
        $.get('<?php echo $ajax_url; ?>&auth_code='+auth_code, function(data) {
          var response = $.parseJSON(data);

          if(response){
          $('#seed_cspv5_check_license_msg').text('Page has been authorized').fadeIn();
          }else{
          $('#seed_cspv5_check_license_msg').text('Page authorization failed. Please check your token and try again.').fadeIn();  
          }
          $('#seed_cspv5_check_license').prop("disabled", false);
        });
		}else{
      $('#seed_cspv5_check_license_msg').show();
			$('#seed_cspv5_check_license').prop("disabled", false);
		}

    }); 
});
</script>