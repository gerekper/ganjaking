<?php
defined( 'ABSPATH' ) || exit;

global $up_user;

if(is_current_user_profile($up_user->getUserId())):
global $userpro;

?>
<div class="up-connections">
    <a href="<?php echo $userpro->permalink($up_user->getUserId(), 'connections','userpro_connections'); ?>" class="up-professional-btn up-professional-btn--full-width "><span><?php echo __('Connections', 'userpro') ?></span> <span class="connections-counter"><?php echo $up_user->user_social->getConnectionsCount() ?></span></a>
</div>
<?php endif; ?>

<?php do_action('up_after_professional_layout_connections_block'); ?>