<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db


echo '<ul>';
foreach ( $option_values as $k => $v ) {
    echo "<li><input id='status-$k' class='$id' type='radio' name='{$setting_id}[$id]' value='$k' " . checked( $options[ $id ], $k, false ) . "  /> $v";
    if($k == '3'){
        if(empty($options[ 'redirect_url' ])){
        	$options[ 'redirect_url' ] = '';
        }
        echo "<br><input style='display:none;margin-top:10px' placeholder='Enter the Redirect URL' id='redirect_url' class='" . ( empty( $class ) ? 'regular-text' : $class ) . "' name='{$setting_id}[redirect_url]' type='text' value='" . esc_attr( $options[ 'redirect_url' ] ) . "' />";
        //echo "<br><small class='description'>Redirect Url</small>";
    }
    echo '</li>';
}
echo '</ul>';
?>
<script>
jQuery( document ).ready(function($) {
	if ($("#status-3").is(':checked')) {
        $("#redirect_url").show();
    }else{
        $("#redirect_url").hide();
    }
});

jQuery(".status").change(function() {
    if(this.value == '3' && this.checked) {
        jQuery("#redirect_url").fadeIn();
    }else{
        jQuery("#redirect_url").fadeOut();
    }
});
</script>

