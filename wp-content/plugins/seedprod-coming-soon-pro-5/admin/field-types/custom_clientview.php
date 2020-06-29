<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
if(empty($options[ $id ])){
	$options[ $id ] = '';
}
echo '<strong>Enter your secret phrase: '."</strong><input id='$id' class='" . ( empty( $class ) ? 'regular-text' : $class ) . "' name='{$setting_id}[$id]' type='text' value='" . esc_attr( $options[ $id ] ) . "' /><br>";
echo '<small class="description">Enter a phrase above and give your client or visitors a secret url that will allow them to bypass the Coming Soon page. Use only letter numbers and dashes.<br>After the cookie expires the user will need to revisit the bypass url to regain access.</small><br>';
echo '<strong>Bypass URL: </strong><input id="my_bypass_url" value="'.home_url().'?bypass=" readonly class="regular-text"/><br>';
echo '<small class="description">Above is your bypass URL, copy and it\'s best to test in a Chrome incognito window or a separate browser to simulate an anonymous visitor.</small><br><small>Need to display your bypass url on the page or have visitors enter it as a password? <a target="_blank" href="https://support.seedprod.com/article/85-add-password-protection-or-a-bypass-url">Learn More</a></small>';
echo '
<script>
jQuery( document ).ready(function($) {
   $("#my_bypass_url").val("'.home_url().'?bypass=" + $("#client_view_url").val());
    $("#client_view_url").on("input",function(e){
	    $("#my_bypass_url").val("'.home_url().'?bypass=" + $("#client_view_url").val());
	});
});
</script>
';

$permalink_structure = get_option('permalink_structure');
if(empty($permalink_structure)){
	echo '<small class="description highlight"><strong>WARNING:</strong> Permalinks need to be enabled for this feature to work. <a href="http://support.seedprod.com/article/45-clientview-url-not-working-404-error" target="_blank">Learn more</a>. </small><br>';
}