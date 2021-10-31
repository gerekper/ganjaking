<?php
if ( ( ! is_singular() && ! isset( $_GET['post'] ) && ! isset( $_GET['post_id'] ) ) && empty( $is_page_layout ) ) {
	return;
}
if ( ! $builder_type || ! in_array( $builder_type, array( 'header', 'footer', 'shop', 'product', 'popup', 'block' ) ) ) {
	return;
}

	$first_conds        = apply_filters(
		'porto_builder_condition_first',
		array(
			''        => __( 'All', 'porto-functionaltiy' ),
			'single'  => __( 'Single', 'porto-functionaltiy' ),
			'archive' => __( 'Archive', 'porto-functionaltiy' ),
		)
	);
	$first_cond_default = '';
	if ( 'shop' == $builder_type ) {
		$first_cond_default = 'archive';
	} elseif ( 'product' == $builder_type ) {
		$first_cond_default = 'single';
	}

	$_post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
	$post_types  = array();
	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $object->label;
	}

	if ( 'product' == $builder_type || 'shop' == $builder_type ) {
		$post_types = array(
			'product' => __( 'Product', 'porto-functionaltiy' ),
		);
	}
	$post_types = apply_filters( 'porto_builder_post_types', $post_types );

	if ( count( $post_types ) > 1 ) {
		$taxonomies = array(
			''               => '',
			'archive/date'   => __( 'Date Archive', 'porto-functionaltiy' ),
			'archive/author' => __( 'Author Archive', 'porto-functionaltiy' ),
			'archive/search' => __( 'Search Results', 'porto-functionaltiy' ),
			'single/page'    => __( 'Pages', 'porto-functionaltiy' ),
			'single/404'     => __( '404 Page', 'porto-functionaltiy' ),
		);
	} else {
		$taxonomies = array( '' => '' );
	}

	foreach ( $post_types as $post_type => $label ) {
		$post_type_taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$post_type_taxonomies = wp_filter_object_list(
			$post_type_taxonomies,
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			)
		);
		if ( empty( $post_type_taxonomies ) ) {
			continue;
		}

		$taxonomies[ $post_type ] = array(
			/* translators: post type name */
			'archive/' . $post_type => sprintf( __( '%s Archive', 'porto-functionaltiy' ), $label ),
			/* translators: post type name */
			'single/' . $post_type  => sprintf( __( 'All %s', 'porto-functionaltiy' ), $label ),
		);
		foreach ( $post_type_taxonomies as $slug => $object ) {
			$taxonomies[ $post_type ][ 'taxonomy/' . $slug ] = $object->label;
		}
	}

	$taxonomies = apply_filters( 'porto_builder_condition_types', $taxonomies );

	$second_cond_default = '';

	/* load saved values */
	$conditions = get_post_meta( $post_id, '_porto_builder_conditions', true );
	?>

<div class="porto-panel porto-builder-cond-wrap porto-setup-wizard<?php echo empty( $conditions ) ? ' notsaved' : ''; ?>">
<?php
$has_condition = true;
if ( empty( $conditions ) ) {
	$conditions    = array( array( $first_cond_default, $second_cond_default, '' ) );
	$has_condition = false;
}
?>
	<p class="porto-logo">
		<img src="<?php echo PORTO_URI . '/images/logo/logo-default-slim.png'; ?>" width="111" alt="">
	</p>
	<h2><?php esc_html_e( 'Where do you want to display this template?', 'porto-functionaltiy' ); ?></h2>
	<p><?php esc_html_e( 'This will override all other settings such as Theme Options, meta box settings, etc.', 'porto-functionaltiy' ); ?></p>
	<form method="POST" class="postoptions">
	<?php foreach ( $conditions as $index => $condition ) : ?>
		<div class="porto-builder-condition"<?php echo ! $has_condition ? ' style="display: none"' : ''; ?>>
			<select class="condition condition-type" name="type[]"<?php disabled( 'shop' == $builder_type || 'product' == $builder_type, true ); ?>>
			<?php foreach ( $first_conds as $type => $label ) : ?>
				<option value="<?php echo esc_attr( $type ); ?>"<?php selected( $condition[0], $type, true ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
			</select>
		<?php if ( 'shop' == $builder_type || 'product' == $builder_type ) : ?>
			<input type="hidden" name="type[]" value="<?php echo esc_html( $condition[0] ); ?>">
		<?php endif; ?>
			<select class="condition condition-object-type" name="object_type[]">
			<?php foreach ( $taxonomies as $type => $val ) : ?>
				<?php if ( ! is_array( $val ) ) : ?>
				<option value="<?php echo esc_attr( $type ); ?>"<?php selected( $condition[1], $type, true ); ?>><?php echo esc_html( $val ); ?></option>
				<?php else : ?>
					<optgroup label="<?php echo isset( $post_types[ $type ] ) ? esc_html( $post_types[ $type ] ) : ''; ?>">
					<?php foreach ( $val as $p => $label ) : ?>
						<option value="<?php echo esc_attr( $p ); ?>"<?php selected( $condition[1], $p, true ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
					</optgroup>
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
			<div class="condition searchform">
				<input type="text" name="query" value="<?php echo isset( $condition[3] ) ? esc_html( $condition[3] ) : ''; ?>">
				<input type="hidden" name="object_name[]" class="condition-object-name" value="<?php echo isset( $condition[3] ) ? esc_html( $condition[3] ) : ''; ?>">
				<input type="hidden" name="object_id[]" class="condition-object-id" value="<?php echo (int) $condition[2]; ?>">
				<a href="#" class="condition-cancel" style="display: none"><i class="fas fa-times"></i></a>
				<div class="live-search-list"></div>
			</div>
			<a href="#" class="condition-close condition-btn" title="<?php esc_html_e( 'Close', 'porto-functionaltiy' ); ?>"><i class="fas fa-times"></i></a>
			<a href="#" class="condition-clone condition-btn" title="<?php esc_html_e( 'Clone', 'porto-functionaltiy' ); ?>"><i class="far fa-clone"></i></a>
		</div>
	<?php endforeach; ?>
		<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>">
		<div style="text-align: right;">
			<a href="#" class="btn btn-quaternary btn-sm btn-add-condition"><i class="fas fa-plus"></i> <?php esc_html_e( 'New Condition', 'porto-functionaltiy' ); ?></a>
		</div>
		<button type="button" class="btn btn-primary save-condition"><?php esc_html_e( 'Save &amp; Close', 'porto-functionaltiy' ); ?></button>
		<?php empty( $is_page_layout ) ? wp_nonce_field( 'porto-builder-condition-nonce' ) : wp_nonce_field( 'porto-page-layouts-nonce' ); ?>
	</form>
</div>
