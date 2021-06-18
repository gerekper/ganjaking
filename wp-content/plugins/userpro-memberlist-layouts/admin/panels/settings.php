

<form method="post" action="">

<h3><?php _e('General Settings','userpro-memberlists'); ?></h3>
<table class="form-table">

<tr valign="top">
		<th scope="row"><label for="user_memberlist_template"><?php _e('Select Memberlist Template','userpro'); ?></label></th>
		<td>
			<select name="user_memberlist_template" id="user_memberlist_template" class="chosen-select" style="width:300px">
<!--                                <option value="0" <?php selected('0', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Default','userpro'); ?></option>-->
				<option value="1" <?php selected('1', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 1','userpro'); ?></option>
				<option value="2" <?php selected('2', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 2','userpro'); ?></option>
                                <option value="3" <?php selected('3', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 3','userpro'); ?></option>
                                <option value="4" <?php selected('4', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 4','userpro'); ?></option>
<!--                                <option value="5" <?php selected('5', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 5','userpro'); ?></option>-->
                                <option value="6" <?php selected('6', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 5','userpro'); ?></option>
                                <option value="7" <?php selected('7', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 6','userpro'); ?></option>
                                <option value="8" <?php selected('8', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 7','userpro'); ?></option>
                                <option value="9" <?php selected('9', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 8','userpro'); ?></option>
<!--                                <option value="10" <?php selected('10', userpro_memberlists_get_option('user_memberlist_template')); ?>><?php _e('Template 10','userpro'); ?></option>-->
			</select>
		</td>
	</tr>
</table>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-memberlists'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-memberlists'); ?>"  />
</p>

</form>
