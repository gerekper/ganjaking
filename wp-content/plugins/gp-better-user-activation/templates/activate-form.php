<form name="activateform" id="activateform" method="post" action="#">
    <p>
      <label for="key"><?php _e( 'Activation Key:', 'gp-better-user-activation' ) ?></label>
      <br /><input type="text" name="key" id="key" value="" size="50" />
    </p>
    <p class="submit">
      <input id="submit" type="submit" name="Submit" class="submit" value="<?php esc_attr_e( 'Activate', 'gp-better-user-activation' ) ?>" />
    </p>
</form>
