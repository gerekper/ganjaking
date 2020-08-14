<?php
/**
 * MY ACCOUNT LINK FIELDS
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>

<li class="dd-item endpoint link" data-id="<?php echo esc_attr( $endpoint ); ?>" data-type="link">

	<label class="on-off-endpoint" for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_active">
		<input type="checkbox" class="hide-show-check" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[active]"
			id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_active"
			value="<?php echo esc_attr( $endpoint ); ?>" <?php checked( $options['active'] ) ?>/>
		<i class="fa fa-power-off"></i>
	</label>

	<div class="open-options field-type">
		<span><?php esc_html_e( 'Link', 'yith-woocommerce-customize-myaccount-page' ) ?></span>
		<i class="fa fa-chevron-down"></i>
	</div>

	<div class="dd-handle endpoint-content">

		<!-- Header -->
		<div class="endpoint-header">
			<?php echo esc_html( $options['label'] ); ?>
			<span class="sub-item-label"><i><?php esc_html_e( 'sub item', 'yith-woocommerce-customize-myaccount-page' ); ?></i></span>
		</div>

		<!-- Content -->
		<div class="endpoint-options" style="display: none;">

			<div class="options-row">
				<span
					class="hide-show-trigger"><?php echo $options['active'] ? esc_html__( 'Hide', 'yith-woocommerce-customize-myaccount-page' ) : esc_html__( 'Show', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
				<span class="sep">|</span>
				<span class="remove-trigger"
					data-endpoint="<?php echo esc_attr( $endpoint ); ?>"><?php esc_html_e( 'Remove', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
			</div>

			<table class="options-table form-table">
				<tbody>

				<?php if ( $endpoint != 'dashboard' ) : ?>
					<tr>
						<th>
							<label
								for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_url"><?php esc_html_e( 'Link url', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
							<img class="help_tip"
								data-tip="<?php esc_attr_e( 'The url of the menu item.', 'yith-woocommerce-customize-myaccount-page' ) ?>"
								src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16"/>
						</th>
						<td>
							<input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[url]"
								id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_url" value="<?php echo esc_attr( $options['url'] ); ?>">
						</td>
					</tr>
				<?php endif; ?>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_label"><?php esc_html_e( 'Link label', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip" data-tip="<?php esc_attr_e( 'Menu label for this link in "My Account".',
							'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_attr( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16"/>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[label]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_label" value="<?php echo esc_attr( $options['label'] ); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_icon"><?php esc_html_e( 'Link icon', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Link icon for "My Account" menu option', 'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16"/>
					</th>
					<td>
						<select name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[icon]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_icon" class="icon-select">
							<option
								value=""><?php esc_html_e( 'No icon', 'yith-woocommerce-customize-myaccount-page' ) ?></option>
							<?php foreach ( $icon_list as $icon => $label ) : ?>
								<option
									value="<?php echo esc_attr( $label ); ?>" <?php selected( $options['icon'], $label ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>


				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_class"><?php esc_html_e( 'Link class', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Add additional classes to link container.', 'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16"/>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[class]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_class" value="<?php echo esc_attr( $options['class'] ); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_usr_roles"><?php esc_html_e( 'User roles', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Restrict endpoint visibility to the following user role(s).',
								'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16"/>
					</th>
					<td>
						<select name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[usr_roles][]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_usr_roles" multiple="multiple">
							<?php foreach ( $usr_roles as $role => $role_name ) :
								! isset( $options['usr_roles'] ) && $options['usr_roles'] = array();
								?>
								<option
									value="<?php echo esc_attr( $role ); ?>" <?php selected( in_array( $role, (array) $options['usr_roles'] ), true ); ?>><?php echo esc_attr( $role_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_target_blank"><?php esc_html_e( 'Open link in a new tab?', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[target_blank]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_target_blank"
							value="yes" <?php checked( $options['target_blank'] ) ?>>
					</td>
				</tr>
				</tbody>
			</table>
		</div>

	</div>
</li>