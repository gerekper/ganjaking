<form method="post" action="">

<!--<p class="upadmin-highlight"><?php _e('Please note: <strong>cURL must be enabled</strong> to successfully activate your license. If you want to find your purchase code, please look <a href="http://userproplugin.com/userpro/docs/#install">here</a>. If you cannot enable cURL or getting another error, please use the support forum link and we will activate your license manually.','userpro-memberlists'); ?></p>-->

<h3><?php _e('Activate UserPro Memberlist Layouts ','userpro-memberlists'); ?></h3>
<table class="form-table">

	<tr valign="top">
            <th scope="row"><label for="userpro_memberlists_envato_code"><?php _e('Envato Purchase Code','userpro-memberlists'); ?></label></th>
            <td>
                    <input type="text" style="width:300px !important;" name="userpro_memberlists_envato_code" id="userpro_memberlists_envato_code" value="<?php echo (userpro_memberlists_get_option('userpro_memberlists_envato_code')) ? userpro_memberlists_get_option('userpro_memberlists_envato_code') : ''; ?>" class="regular-text" />
                    <span class="description"><?php _e('Enter your envato purchase code.','userpro-memberlists'); ?></span>
            </td>
        </tr>

</table>

<p class="submit">
	<input type="submit" name="verify-license-memberslists" id="verify-license-memberslists" class="button button-primary" value="<?php _e('Save Changes','userpro-memberlists'); ?>"  />
</p>

</form>
