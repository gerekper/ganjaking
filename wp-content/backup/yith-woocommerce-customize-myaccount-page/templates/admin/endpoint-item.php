<?php
/**
 * MY ACCOUNT ENDPOINT FIELDS
 *
 * @package YITH WooCommerce Customize My Account Page
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$editor_args = array(
	'wpautop'       => true, // use wpautop?
	'media_buttons' => true, // show insert/upload button(s)
	'textarea_name' => $id . '_' . $endpoint . '[content]', // set the textarea name to something different, square brackets [] can be used here
	'textarea_rows' => 15, // rows="..."
	'tabindex'      => '',
	'editor_css'    => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
	'editor_class'  => '', // add extra class(es) to the editor textarea
	'teeny'         => false, // output the minimal editor config used in Press This
	'dfw'           => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
	'tinymce'       => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
	'quicktags'     => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
);

?>

<li class="dd-item endpoint" data-id="<?php echo esc_attr( $endpoint ); ?>" data-type="endpoint">

	<label class="on-off-endpoint" for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_active">
		<input type="checkbox" class="hide-show-check" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[active]"
			id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_active"
			value="<?php echo esc_attr( $endpoint ); ?>" <?php checked( $options['active'] ) ?>/>
		<i class="fa fa-power-off"></i>
	</label>

	<div class="open-options field-type">
		<span><?php esc_html_e( 'Endpoint', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
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
				<?php if ( ! yith_wcmap_is_plugin_item( $endpoint ) && ! yith_wcmap_is_default_item( $endpoint ) ) : ?>
					<span class="sep">|</span>
					<span class="remove-trigger"
						data-endpoint="<?php echo esc_attr( $endpoint ); ?>"><?php esc_html_e( 'Remove', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
				<?php endif; ?>
			</div>

			<table class="options-table form-table">
				<tbody>

				<?php if ( $endpoint != 'dashboard' ) : ?>
					<tr>
						<th>
							<label
								for="<?php echo esc_attr( $id . '_' . $endpoint ) ?>_slug"><?php esc_html_e( 'Endpoint slug', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
							<img class="help_tip"
								data-tip="<?php esc_attr_e( 'Text appended to your page URLs to manage new contents in account pages. It must be unique for every page.', 'yith-woocommerce-customize-myaccount-page' ) ?>"
								src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
								width="16"/>
						</th>
						<td>
							<input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[slug]"
								id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_slug"
								value="<?php echo esc_attr( $options['slug'] ); ?>">
						</td>
					</tr>
				<?php endif; ?>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_label"><?php esc_html_e( 'Endpoint label', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Menu item for this endpoint in "My Account".',
								'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
							width="16"/>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[label]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_label"
							value="<?php echo esc_attr( $options['label'] ); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_icon"><?php esc_html_e( 'Endpoint icon', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Endpoint icon for "My Account" menu option', 'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
							width="16"/>
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
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_class"><?php esc_html_e( 'Endpoint class', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Add additional classes to endpoint container.', 'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
							width="16"/>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[class]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_class"
							value="<?php echo esc_attr( $options['class'] ); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label
							for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_usr_roles"><?php esc_html_e( 'User roles', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Restrict endpoint visibility to the following user role(s).', 'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
							width="16"/>
					</th>
					<td>
						<select name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[usr_roles][]"
							id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_usr_roles" multiple="multiple">
							<?php foreach ( $usr_roles as $role => $role_name ) :
								! isset( $options['usr_roles'] ) && $options['usr_roles'] = array();
								?>
								<option
									value="<?php echo esc_attr( $role ); ?>" <?php selected( in_array( $role, (array) $options['usr_roles'] ), true ); ?>>
									<?php echo esc_html( $role_name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<?php if ( defined( 'YITH_WCMBS_PREMIUM' ) ): ?>
					<?php $membership_plans = YITH_WCMBS_Manager()->get_plans(); ?>

					<tr>
						<th>
							<label
								for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_membership_plans"><?php esc_html_e( 'Membership plans', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
							<img class="help_tip"
								data-tip="<?php esc_attr_e( 'Restrict endpoint visibility to users who are purchased following membership plan(s)', 'yith-woocommerce-customize-myaccount-page' ) ?>"
								src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
								width="16"/>
						</th>
						<td>
							<select name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[membership_plans][]"
								id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_membership_plans"
								multiple="multiple">
								<?php foreach ( $membership_plans as $plan ) :
									! isset( $options['membership_plans'] ) && $options['membership_plans'] = array();
									?>
									<option
										value="<?php echo esc_attr( $plan->ID ); ?>" <?php selected( in_array( $plan->ID, (array) $options['membership_plans'] ), true ); ?>><?php echo esc_html( $plan->post_title ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

				<?php endif; ?>

				<tr>
					<th>
						<label><?php esc_html_e( 'Endpoint custom content', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
						<img class="help_tip"
							data-tip="<?php esc_attr_e( 'Custom endpoint content. Leave it black to use default content.',
								'yith-woocommerce-customize-myaccount-page' ) ?>"
							src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16"
							width="16"/>
					</th>
					<td>
						<div
							class="editor"><?php wp_editor( stripslashes( $options['content'] ), esc_attr( $id . '_' . $endpoint ) . '_content', $editor_args ); ?></div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>

	</div>
</li>