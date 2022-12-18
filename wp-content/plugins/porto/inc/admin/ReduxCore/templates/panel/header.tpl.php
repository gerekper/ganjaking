<?php
	/**
	 * The template for the panel header area.
	 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
	 *
	 * @author      Redux Framework
	 * @package     ReduxFramework/Templates
	 * @version:    3.5.4.18
	 */

?>
<div id="redux-header">
	<?php if ( ! empty( $this->parent->args['display_name'] ) ) { ?>
		<div class="display_header">

			<h2><?php echo wp_kses_post( $this->parent->args['display_name'] ); ?></h2>

			<?php if ( ! empty( $this->parent->args['display_version'] ) ) { ?>
				<span><?php echo wp_kses_post( $this->parent->args['display_version'] ); ?></span>
			<?php } ?>

		</div>
	<?php } ?>

	<div class="clear"></div>
</div>
