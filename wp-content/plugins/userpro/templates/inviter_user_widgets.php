<div class="userpro-head">

<div class="userpro-invite-left">Invite Users</div>
	<div class="userpro-clear"></div>
</div>
<div class="userpro-invite-body">
<form name='inviteuser' id='inviteuser' method='POST'>  
<label for="name"><?php _e('Email','userpro');?></label><br>     	 
<input type="text" id="useremail" placeholder="Enter invite user email id" name="useremail" style="width:100%";><br><br>
<div>	
<input type='submit' value='Invite' id='inviteuser' class='userpro-inviteuser-button' onclick="userpro_user_invite(useremail)"/>
<div id="invite_success_msg"></div></div>
<span class="description"><?php _e('You can invite users for registration,Enter email addresses separated by comma','userpro'); ?></span>
</form>
</div>
