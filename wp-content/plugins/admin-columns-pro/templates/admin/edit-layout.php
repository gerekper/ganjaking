<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="row name">
	<label for="layout-name-<?php echo $this->id; ?>">
		<?php _e( 'Name', 'codepress-admin-columns' ); ?>
	</label>
	<div class="input">
		<div class="ac-error-message">
			<p>
				<?php _e( 'Please enter a name.', 'codepress-admin-columns' ); ?>
			<p>
		</div>
		<?php echo $this->input_name; ?>
	</div>
</div>
<div class="row info">
	<em><?php _e( 'Make this set available only for specific users or roles (optional)', 'codepress-admin-columns' ); ?></em>
</div>
<div class="row roles">
	<label for="layout-roles-<?php echo $this->id; ?>">
		<?php _e( 'Roles', 'codepress-admin-columns' ); ?>
		<span>(<?php _e( 'optional', 'codepress-admin-columns' ); ?>)</span>
	</label>
	<div class="input">
		<?php echo $this->select_roles; ?>
	</div>
</div>
<div class="row users">
	<label for="layout-users-<?php echo $this->id; ?>">
		<?php _e( 'Users' ); ?>
		<span>(<?php _e( 'optional', 'codepress-admin-columns' ); ?>)</span>
	</label>
	<div class="input">
		<?php echo $this->select_users; ?>
	</div>
</div>