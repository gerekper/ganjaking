<?php
/**
 * @var YITH_WCMBS_Plan $plan
 */

$options = array(
	'options' => array(
		'type'  => 'title',
		'label' => _x( 'Options', 'Plan option section title', 'yith-woocommerce-membership' ),
	),

	'enable_purchasing' => array(
		'type'        => 'onoff',
		'label'       => __( 'Allow access to this plan after purchasing a specific product', 'yith-woocommerce-membership' ),
		'description' => __( 'Enable if the user has to buy a specific product to get access to this membership plan.', 'yith-woocommerce-membership' ),
	),

	'target_products' => array(
		'type'        => 'ajax-products',
		'label'       => __( 'Choose the products to link to this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which product(s) a user can buy, in order to get access to this plan.', 'yith-woocommerce-membership' ),
		'data'        => array(
			'action'                                    => 'woocommerce_json_search_products_and_variations',
			'security'                                  => wp_create_nonce( 'search-products' ),
			'yith_wcmbs_search_for_membership_products' => true,
		),
		'multiple'    => true,
		'deps'        => array(
			'id'    => 'enable_purchasing',
			'value' => 'yes',
		),
	),

	'duration_enabled' => array(
		'type'        => 'onoff',
		'label'       => __( 'Set an end time for this membership', 'yith-woocommerce-membership' ),
		'description' => __( 'Enable if you want to set an end time for this plan. If disabled, the plan will be unlimited or life-time.', 'yith-woocommerce-membership' ),
	),

	'duration' => array(
		'label' => __( 'Membership ends after', 'yith-woocommerce-membership' ),
		'desc'  => __( 'Set after how many days this membership will end.', 'yith-woocommerce-membership' ),
		'type'  => 'custom',
		'html'  => sprintf(
		// translators: %s is the numeric input field of the duration
			esc_html_x( '%s days', 'Text with inline field', 'yith-woocommerce-membership' ),
			yith_plugin_fw_get_field(
				array(
					'id'    => 'duration',
					'name'  => 'duration',
					'type'  => 'number',
					'class' => 'yith-wcmbs-short-inline-field',
					'min'   => 0,
					'value' => $plan->get_duration( 'edit' ),
				),
				false,
				false
			)
		),
		'deps'  => array(
			'id'    => 'duration_enabled',
			'value' => 'yes',
		),
	),

	'show_contents_in_membership_details' => array(
		'type'        => 'onoff',
		'label'       => __( 'Show contents in membership details', 'yith-woocommerce-membership' ),
		'description' => __( 'Enable if you want to show what content is included in this plan, in membership details.', 'yith-woocommerce-membership' ),
	),

	'linked_plans_enabled' => array(
		'type'        => 'onoff',
		'label'       => __( 'Include contents of other plans', 'yith-woocommerce-membership' ),
		'description' => __( 'Enable if you want to include contents of other plans in this plan.', 'yith-woocommerce-membership' ),
	),

	'linked_plans' => array(
		'type'        => 'ajax-posts',
		'multiple'    => true,
		'data'        => array(
			'placeholder' => __( 'Search Membership Plans', 'yith-woocommerce-membership' ),
			'post_type'   => YITH_WCMBS_Post_Types::$plan,
		),
		'label'       => __( 'Include contents of these plans', 'yith-woocommerce-membership' ),
		'description' => __( 'Select which plans to add. Add other plans to this one, so that their content is also visible under this membership plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'linked_plans_enabled',
			'value' => 'yes',
		),
	),

	'download_limit_type' => array(
		'type'        => 'radio',
		'label'       => __( 'Users can download', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose if users of this plan can download products without any limit or if you want to setup download permissions based on credits.', 'yith-woocommerce-membership' ),
		'options'     => array(
			'no'      => __( 'All downloadable products without any limit', 'yith-woocommerce-membership' ),
			'credits' => __( 'Using credits associated to products to download', 'yith-woocommerce-membership' ),
		),
		'show'        => YITH_WCMBS_Products_Manager()->is_allowed_download(),
	),

	'credits_availability' => array(
		'label' => __( 'Credits availability', 'yith-woocommerce-membership' ),
		'desc'  => __( 'Set how many credits to assign to all members of this plans, for how much time these credits will be available and how to manage unused credits.', 'yith-woocommerce-membership' ),
		'type'  => 'custom',
		'html'  => sprintf(
		// translators: 1. is the numeric input field of the number of credits; 2. is the numeric input field of the duration; 3. is the select field of the duration unit; 4. is the select field of the option; Example: Users of this plan will get "50" credits. Each "10" "days" credits will be restored and unused credits "will be accumulated"
			esc_html_x( 'Users of this plan will get %1$s credits. Every %2$s %3$s credits will be restored and unused credits %4$s', 'Text with inline fields', 'yith-woocommerce-membership' ),
			yith_plugin_fw_get_field(
				array(
					'id'    => 'download_number',
					'name'  => 'download_number',
					'type'  => 'number',
					'class' => 'yith-wcmbs-short-inline-field',
					'min'   => 0,
					'value' => $plan->get_download_number( 'edit' ),
				),
				false,
				false
			),
			yith_plugin_fw_get_field(
				array(
					'id'    => 'download_term_duration',
					'name'  => 'download_term_duration',
					'type'  => 'number',
					'class' => 'yith-wcmbs-short-inline-field',
					'min'   => 0,
					'value' => $plan->get_download_term_duration( 'edit' ),
				),
				false,
				false
			),
			yith_plugin_fw_get_field(
				array(
					'id'      => 'download_term_unit',
					'name'    => 'download_term_unit',
					'type'    => 'select',
					'class'   => 'yith-wcmbs-short-inline-field',
					'options' => array(
						'days'   => __( 'days', 'yith-woocommerce-membership' ),
						'weeks'  => __( 'weeks', 'yith-woocommerce-membership' ),
						'months' => __( 'months', 'yith-woocommerce-membership' ),
						'years'  => __( 'years', 'yith-woocommerce-membership' ),
					),
					'value'   => $plan->get_download_term_unit( 'edit' ),
				),
				false,
				false
			),
			yith_plugin_fw_get_field(
				array(
					'id'      => 'can_credits_be_accumulated',
					'name'    => 'can_credits_be_accumulated',
					'type'    => 'select',
					'class'   => 'yith-wcmbs-short-inline-field',
					'options' => array(
						'yes' => __( 'will be accumulated', 'yith-woocommerce-membership' ),
						'no'  => __( 'will be lost', 'yith-woocommerce-membership' ),
					),
					'value'   => $plan->get_can_credits_be_accumulated( 'edit' ),
				),
				false,
				false
			)
		),
		'show'  => YITH_WCMBS_Products_Manager()->is_allowed_download(),
		'deps'  => array(
			'id'    => 'download_limit_type',
			'value' => 'credits',
		),
	),

	'different_download_number_first_term_enabled' => array(
		'type'        => 'onoff',
		'label'       => __( 'Set a different credits availability for first time', 'yith-woocommerce-membership' ),
		'description' => __( 'Enable if you want to assign a different number of credits for the first time, when the user becomes a member.', 'yith-woocommerce-membership' ),
		'show'        => YITH_WCMBS_Products_Manager()->is_allowed_download(),
		'deps'        => array(
			'id'    => 'download_limit_type',
			'value' => 'credits',
		),
	),

	'download_number_first_term' => array(
		'type'        => 'number',
		'min'         => 0,
		'label'       => __( 'Credits availability for the first time', 'yith-woocommerce-membership' ),
		'description' => __( 'Set how many credits to assign to new members for the first time.', 'yith-woocommerce-membership' ),
		'show'        => YITH_WCMBS_Products_Manager()->is_allowed_download(),
		'deps'        => array(
			'id'    => array( 'download_limit_type', 'different_download_number_first_term_enabled' ),
			'value' => array( 'credits', 'yes' ),
		),
	),

	'discount_enabled' => array(
		'type'        => 'onoff',
		'label'       => __( 'Give a discount', 'yith-woocommerce-membership' ),
		'description' => __( 'Enable if you want to give a discount on all products for members.', 'yith-woocommerce-membership' ),
	),

	'discount' => array(
		'type'        => 'number',
		'min'         => 0,
		'label'       => __( 'Discount on all products (%)', 'yith-woocommerce-membership' ),
		'description' => __( 'Set the percentage discount for all products.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'discount_enabled',
			'value' => 'yes',
		),
	),

	'permissions' => array(
		'type'        => 'title',
		'label'       => _x( 'Permissions', 'Plan option section title', 'yith-woocommerce-membership' ),
		'description' => __( 'Set which content will be restricted and accessible only to members of this plan.', 'yith-woocommerce-membership' ),
	),

	'pages-title' => array(
		'type'  => 'title-secondary',
		'label' => _x( 'Pages', 'Plan option section title', 'yith-woocommerce-membership' ),
	),

	'pages' => array(
		'type'        => 'ajax-posts',
		'multiple'    => true,
		'data'        => array(
			'placeholder' => __( 'Search Pages', 'yith-woocommerce-membership' ),
			'post_type'   => 'page',
		),
		'label'       => __( 'Pages accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which pages will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
	),

	'page_sorting' => array(
		'label'       => __( 'Page sorting', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose how to sort visible pages to members.', 'yith-woocommerce-membership' ),
		'type'        => 'select',
		'options'     => array(
			'name-asc'  => __( 'Alphabetically ascending', 'yith-woocommerce-membership' ),
			'name-desc' => __( 'Alphabetically descending', 'yith-woocommerce-membership' ),
			'date-asc'  => __( 'Date ascending', 'yith-woocommerce-membership' ),
			'date-desc' => __( 'Date descending', 'yith-woocommerce-membership' ),
		),
	),

	'posts-title' => array(
		'type'  => 'title-secondary',
		'label' => _x( 'Posts', 'Plan option section title', 'yith-woocommerce-membership' ),
	),

	'posts_to_include' => array(
		'type'    => 'radio',
		'label'   => __( 'Select posts to include', 'yith-woocommerce-membership' ),
		'options' => array(
			'all'      => __( 'All posts', 'yith-woocommerce-membership' ),
			'specific' => __( 'Specific posts / post categories / post tags', 'yith-woocommerce-membership' ),
		),
	),

	'posts' => array(
		'type'        => 'ajax-posts',
		'multiple'    => true,
		'label'       => __( 'Posts accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which posts will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'posts_to_include',
			'value' => 'specific',
		),
	),

	'post_categories' => array(
		'type'        => 'ajax-terms',
		'data'        => array(
			'taxonomy'    => 'category',
			'placeholder' => __( 'Search Post Categories', 'yith-woocommerce-membership' ),
			'allow-clear' => false,
		),
		'multiple'    => true,
		'label'       => __( 'Post categories accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which post categories will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'posts_to_include',
			'value' => 'specific',
		),
	),

	'post_tags' => array(
		'type'        => 'ajax-terms',
		'data'        => array(
			'taxonomy'    => 'post_tag',
			'placeholder' => __( 'Search Post Tags', 'yith-woocommerce-membership' ),
			'allow-clear' => false,
		),
		'multiple'    => true,
		'label'       => __( 'Post tags accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which post tags will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'posts_to_include',
			'value' => 'specific',
		),
	),

	'post_sorting' => array(
		'label'       => __( 'Post sorting', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose how to sort visible posts to members.', 'yith-woocommerce-membership' ),
		'type'        => 'select',
		'options'     => array(
			'name-asc'  => __( 'Alphabetically ascending', 'yith-woocommerce-membership' ),
			'name-desc' => __( 'Alphabetically descending', 'yith-woocommerce-membership' ),
			'date-asc'  => __( 'Date ascending', 'yith-woocommerce-membership' ),
			'date-desc' => __( 'Date descending', 'yith-woocommerce-membership' ),
		),
	),

	'products-title' => array(
		'type'  => 'title-secondary',
		'label' => _x( 'Products', 'Plan option section title', 'yith-woocommerce-membership' ),
	),

	'products_to_include' => array(
		'type'    => 'radio',
		'label'   => __( 'Select products to include', 'yith-woocommerce-membership' ),
		'options' => array(
			'all'      => __( 'All products', 'yith-woocommerce-membership' ),
			'specific' => __( 'Specific products / product categories / product tags', 'yith-woocommerce-membership' ),
		),
	),

	'products' => array(
		'type'        => 'ajax-products',
		'multiple'    => true,
		'label'       => __( 'Products accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which products will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'products_to_include',
			'value' => 'specific',
		),
	),

	'product_categories' => array(
		'type'        => 'ajax-terms',
		'data'        => array(
			'taxonomy'    => 'product_cat',
			'placeholder' => __( 'Search Product Categories', 'yith-woocommerce-membership' ),
			'allow-clear' => false,
		),
		'multiple'    => true,
		'label'       => __( 'Product categories accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which product categories will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'products_to_include',
			'value' => 'specific',
		),
	),

	'product_tags' => array(
		'type'        => 'ajax-terms',
		'data'        => array(
			'taxonomy'    => 'product_tag',
			'placeholder' => __( 'Search Product Tags', 'yith-woocommerce-membership' ),
			'allow-clear' => false,
		),
		'multiple'    => true,
		'label'       => __( 'Product tags accessible with this plan', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose which product tags will be accessible to members of this plan.', 'yith-woocommerce-membership' ),
		'deps'        => array(
			'id'    => 'products_to_include',
			'value' => 'specific',
		),
	),

	'product_sorting' => array(
		'label'       => __( 'Product sorting', 'yith-woocommerce-membership' ),
		'description' => __( 'Choose how to sort visible products to members.', 'yith-woocommerce-membership' ),
		'type'        => 'select',
		'options'     => array(
			'name-asc'  => __( 'Alphabetically ascending', 'yith-woocommerce-membership' ),
			'name-desc' => __( 'Alphabetically descending', 'yith-woocommerce-membership' ),
			'date-asc'  => __( 'Date ascending', 'yith-woocommerce-membership' ),
			'date-desc' => __( 'Date descending', 'yith-woocommerce-membership' ),
		),
	),
);

$options = apply_filters( 'yith_wcmbs_plan_meta_box_options', $options, $plan );

?>
<div class="yith-wcmbs-plan-options__options yith-plugin-ui">
	<?php foreach ( $options as $key => $option ): ?>
		<?php
		$show = isset( $option['show'] ) ? $option['show'] : true;
		if ( ! $show ) {
			continue;
		}

		$type        = $option['type'];
		$label       = isset( $option['label'] ) ? $option['label'] : '';
		$description = isset( $option['description'] ) ? $option['description'] : '';
		if ( isset( $option['label'] ) ) {
			unset( $option['label'] );
		}
		if ( isset( $option['description'] ) ) {
			unset( $option['description'] );
		}

		if ( 'title' === $type ) {
			echo '<h3 class="yith-wcbms-form-group-title">' . esc_html( $label ) . '</h3>';
			if ( $description ) {
				echo '<div class="yith-wcbms-form-group-description">' . esc_html( $description ) . '</div>';
			}
			continue;
		} else if ( 'title-secondary' === $type ) {
			echo '<h4 class="yith-wcbms-form-group-title yith-wcbms-form-group-title--secondary">' . esc_html( $label ) . '</h4>';
			continue;
		}

		$deps = isset( $option['deps'] ) ? $option['deps'] : array();
		if ( isset( $option['deps'] ) ) {
			unset( $option['deps'] );
		}

		$option['id']   = $key;
		$option['name'] = $key;
		if ( ! empty( $option['multiple'] ) ) {
			$option['name'] .= '[]';
		}

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
		?>


		<div class="<?php echo esc_attr( $form_field_class ); ?>"
			<?php echo $form_field_data_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		>
			<label class="yith-wcmbs-form-field__label"><?php echo esc_html( $label ); ?></label>
			<div class="yith-wcmbs-form-field__content">
				<?php

				if ( 'custom' === $type ) {
					echo $option['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					$getter          = 'get_' . $key;
					$option['value'] = is_callable( array( $plan, $getter ) ) ? $plan->$getter( 'edit' ) : $plan->get_meta_data( $key );
					yith_plugin_fw_get_field( $option, true );
				}
				?>
			</div>

			<div class="yith-wcmbs-form-field__description"><?php echo esc_html( $description ); ?></div>
		</div>
	<?php endforeach; ?>
</div>
