

<h3><?php _e('Delete Wall Content','userpro-userwall'); ?></h3>
<table class="form-table">

	
	<tr valign="top">
		<td scope="row"><label ><?php _e('From Date','userpro-userwall'); ?></label></td>
		<td>
			<input type="text" data-fieldtype='datepicker' class='socialwall-datepicker' name="formdate" id="sw_datepickerform"  class="regular-text" />
		</td>
		<td scope="row"><label ><?php _e('To Date','userpro-userwall'); ?></label></td>
		<td>
		<input type="text" data-fieldtype='datepicker' class='socialwall-datepicker' name="todate" id="sw_datepickerto"  class="regular-text" />
		</td>
		
	</tr>
<tr>
<td>

</td>
<td>

			<span class="description" id="result_msg"></span>
		</td>
</tr>	
</table>



<p class="submit">
	<input type="submit" name="mypost" id="deletepost" class="button button-primary" value="Delete"  onclick="sw_delete_post_by_date('sw_datepickerform','sw_datepickerto')"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-userwall'); ?>"  />
</p>



