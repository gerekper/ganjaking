<?php
/**
 * @var WP_Post $post
 * @var int     $post_id
 */

$post_type = get_post_type( $post_id );

$plan_ids = yith_wcmbs_get_plans( array( 'fields' => 'ids' ) );
$plans    = array_combine( $plan_ids, array_map( 'get_the_title', $plan_ids ) );

$alternative_contents_url = add_query_arg( array( 'post_type' => YITH_WCMBS_Post_Types::$alternative_contents ), admin_url( 'edit.php' ) );

$options = array(

	// Flag to check if it's saving: useful to prevent the array of options is empty
	'is_saving' => array(
		'type'           => 'hidden',
		'show_container' => false,
		'value'          => 'yes',
	),

	'_yith_wcmbs_restrict_access_plan' => array(
		'label'             => __( 'Include this content in the following plans', 'yith-woocommerce-membership' ),
		'type'              => 'select',
		'class'             => 'yith-wcmbs-select2',
		'multiple'          => true,
		'options'           => $plans,
		'default'           => array(),
		'description'       => __( 'Choose plans to include this content in.', 'yith-woocommerce-membership' ),
		'custom_attributes' => 'style="width:90%;"',
	),

	'_yith_wcmbs_plan_delay' => array(
		'type'            => 'custom',
		'action'          => 'yith_wcbm_metaboxes_print_custom_field',
		'yith-wcbms-type' => 'plans-delay',
		'show_container'  => false,
	),

	'_yith_wcmbs_credits' => array(
		'label'             => __( 'Credits', 'yith-woocommerce-membership' ),
		'type'              => 'number',
		'wrapper_class'     => 'show_if_downloadable show_if_variable',
		'default'           => get_option( 'yith-wcmbs-default-credits-for-product', 1 ),
		'description'       => __( 'Set the number of credits the user needs to spend to download the product.', 'yith-woocommerce-membership' ),
		'custom_attributes' => array(
			'min'     => 0,
			'step'    => 1,
			'pattern' => '\d*',
		),
		'show'              => 'product' === $post_type,
	),

	'_yith_wcmbs_custom_redirect' => array(
		'label'             => __( 'Redirect non-members to', 'yith-woocommerce-membership' ),
		'description'       => __( 'Enter the URL where to redirect non-member users.', 'yith-woocommerce-membership' ),
		'type'              => 'text',
		'default'           => '',
		'custom_attributes' => 'placeholder="' . yith_wcmbs_settings()->get_option( 'yith-wcmbs-redirect-link' ) . '"',
		'show'              => 'attachment' !== $post_type && 'redirect' === yith_wcmbs_settings()->get_hide_contents(),
	),

	'_alternative-content-mode' => array(
		'label'       => __( 'Alternative content for non-members', 'yith-woocommerce-membership' ),
		'description' => implode( '<br />', array(
			__( 'Choose which alternative content will be shown for non-members.', 'yith-woocommerce-membership' ),
			__( 'You can enter a custom one in the editor of this page or choose a previously created alternative content block to load.', 'yith-woocommerce-membership' ),
			sprintf(
			// translators: %s is the text (with link) of the "YITH > Membership > Alternative Content Blocks" menu
				__( 'You can create alternative content blocks in "%s".', 'yith-woocommerce-membership' ),
				'<a href="' . $alternative_contents_url . '" target="_blank">YITH > Membership > ' . _x( 'Alternative Content Blocks', 'Tab title in plugin settings panel', 'yith-woocommerce-membership' ) . '</a>'
			),
		) ),
		'type'        => 'radio',
		'options'     => array(
			'set'  => __( 'Enter alternative content text', 'yith-woocommerce-membership' ),
			'load' => __( 'Load an alternative content block', 'yith-woocommerce-membership' ),
		),
		'default'     => 'set',
		'show'        => 'attachment' !== $post_type && yith_wcmbs_settings()->is_alternative_content_enabled(),
	),

	'_alternative-content' => array(
		'label'       => __( 'Alternative Content', 'yith-woocommerce-membership' ),
		'description' => __( 'Enter the alternative content to be shown to non-members.', 'yith-woocommerce-membership' ),
		'type'        => 'textarea-editor',
		'default'     => '',
		'deps'        => array(
			'id'    => '_alternative-content-mode',
			'value' => 'set',
		),
		'show'        => 'attachment' !== $post_type && yith_wcmbs_settings()->is_alternative_content_enabled(),
	),

	'_alternative-content-id' => array(
		'label'       => __( 'Alternative Content Block', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose the alternative content block to be shown to non-members.', 'yith-woocommerce-membership' ),
		'type'        => 'ajax-posts',
		'data'        => array(
			'placeholder' => __( 'Search Alternative Content Block', 'yith-woocommerce-membership' ),
			'post_type'   => YITH_WCMBS_Post_Types::$alternative_contents,
			'allow_clear' => true,
		),
		'default'     => '',
		'deps'        => array(
			'id'    => '_alternative-content-mode',
			'value' => 'load',
		),
		'show'        => 'attachment' !== $post_type && yith_wcmbs_settings()->is_alternative_content_enabled(),
	),

	'_protected-files-enabled' => array(
		'label'       => __( 'Add protected files', 'yith-woocommerce-membership' ),
		'description' => implode( '<br />', array(
			__( 'Enable if you want to add files that only members can download.', 'yith-woocommerce-membership' ),
			sprintf( __( 'Use the following shortcode to show protected links: %s', 'yith-woocommerce-membership' ), '<code>[membership_protected_links]</code>' ),
		) ),
		'type'        => 'onoff',
		'default'     => 'no',
		'show'        => 'attachment' !== $post_type,
	),

	'_yith_wcmbs_protected_links' => array(
		'label'                  => __( 'Protected files', 'yith-woocommerce-membership' ),
		'type'                   => 'custom',
		'action'                 => 'yith_wcbm_metaboxes_print_custom_field',
		'yith-wcbms-type'        => 'protected-links',
		'yith-sanitize-callback' => 'yith_wcmbs_sanitize_protected_links',
		'deps'                   => array(
			'id'    => '_protected-files-enabled',
			'value' => 'yes',
		),
		'show'                   => 'attachment' !== $post_type,
	),
);

?>
<div class="yith-wcmbs-membership-options__options yith-plugin-ui">
	<?php foreach ( $options as $key => $option ): ?>
		<?php
		$show = isset( $option['show'] ) ? $option['show'] : true;
		if ( ! $show ) {
			continue;
		}

		$type           = $option['type'];
		$label          = isset( $option['label'] ) ? $option['label'] : '';
		$description    = isset( $option['description'] ) ? $option['description'] : '';
		$default        = isset( $option['default'] ) ? $option['default'] : '';
		$show_container = isset( $option['show_container'] ) ? ! ! $option['show_container'] : true;
		if ( isset( $option['label'] ) ) {
			unset( $option['label'] );
		}
		if ( isset( $option['description'] ) ) {
			unset( $option['description'] );
		}
		if ( isset( $option['show_container'] ) ) {
			unset( $option['show_container'] );
		}

		if ( 'title' === $type ) {
			echo '<h3 class="yith-wcbms-form-group-title">' . esc_html( $label ) . '</h3>';
			if ( $description ) {
				echo '<div class="yith-wcbms-form-group-description">' . esc_html( $description ) . '</div>';
			}
			continue;
		}

		$deps = isset( $option['deps'] ) ? $option['deps'] : array();
		if ( isset( $option['deps'] ) ) {
			unset( $option['deps'] );
		}

		$option['id']   = $key;
		$option['name'] = 'yith-wcmbs-membership-options[' . $key . ']';

		$form_field_class     = "yith-wcmbs-form-field";
		$form_field_data_html = '';
		if ( $deps && isset( $deps['id'] ) && isset( $deps['value'] ) ) {
			$form_field_class .= ' yith-wcmbs-show-conditional';
			$deps_ids         = (array) $deps['id'];
			$deps_values      = (array) $deps['value'];
			$deps_ids         = '#' . implode( ',#', $deps_ids );
			$deps_values      = implode( ',', $deps_values );

			$form_field_data_html = "data-dep-selector='{$deps_ids}' data-dep-value='{$deps_values}'";
		}

		if ( isset( $option['wrapper_class'] ) ) {
			$form_field_class .= ' ' . $option['wrapper_class'] . ' ';
		}

		$option['value'] = metadata_exists( 'post', $post_id, $key ) ? get_post_meta( $post_id, $key, true ) : $default;
		?>

		<?php if ( $show_container ) : ?>
			<div class="<?php echo esc_attr( $form_field_class ); ?>"
				<?php echo $form_field_data_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			>
				<label class="yith-wcmbs-form-field__label"><?php echo esc_html( $label ); ?></label>
				<div class="yith-wcmbs-form-field__content">
					<?php yith_plugin_fw_get_field( $option, true ); ?>
				</div>

				<div class="yith-wcmbs-form-field__description"><?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
		<?php else : ?>
			<?php yith_plugin_fw_get_field( $option, true ); ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
