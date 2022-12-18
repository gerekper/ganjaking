<div class="wrap">
	<h1 class="screen-reader-text"><?php esc_html_e( 'Page Layouts', 'porto' ); ?></h1>
</div>
<div class="wrap porto-wrap">
	<?php
		porto_get_template_part(
			'inc/admin/admin_pages/header',
			null,
			array(
				'active_item' => 'page_layouts',
				'title'       => __( 'Page Layouts', 'porto' ),
				'subtitle'    => __( 'Create page layouts and assign them to different pages with display condition.', 'porto' ),
			)
		);
		$nonce = wp_create_nonce( 'porto-page-layouts' );
		?>
	<main style="display: block">
		<div class="page-layouts">
			<div class="layout-box">
				<h3 class="layout-header">
					<a href="#" class="back"><i class="fas fa-arrow-left"></i></a>
					<?php esc_html_e( 'Page Layout for Template Bulider', 'porto' ); ?>
				</h3>
				<div class="layout porto-layout">
					<div class="block popup-builder layout-part" data-part="popup">
						<p><?php esc_html_e( 'Popup Builder for Any Page', 'porto' ); ?></p>
					</div>				
					<div class="top-block layout-part" data-part="top-block">
						<p><?php esc_html_e( 'Top', 'porto' ); ?></p>
					</div>
					<div class="header layout-part" data-part="header">
						<p><?php esc_html_e( 'Header', 'porto' ); ?></p>
					</div>
					<div class="banner-block layout-part" data-part="banner-block">
						<p><?php esc_html_e( 'Banner', 'porto' ); ?></p>
					</div>
					<div class="content-top-block layout-part" data-part="content-top-block">
						<p><?php esc_html_e( 'Content Top', 'porto' ); ?></p>
					</div>										
					<div class="content-wrapper">
						<div class="content">
							<div class="block content-inner-top-block layout-part" data-part="content-inner-top-block">
								<p><?php esc_html_e( 'Content Inner top', 'porto' ); ?></p>
							</div>						
							<div class="block single layout-part" data-part="single">
								<p><?php esc_html_e( 'Single', 'porto' ); ?></p>
							</div>			
							<div class="block archive layout-part" data-part="archive">
								<p><?php esc_html_e( 'Archive', 'porto' ); ?></p>
							</div>
							<div class="block single-product layout-part" data-part="product">
								<p><?php esc_html_e( 'Single Product', 'porto' ); ?></p>
							</div>					
							<div class="block product-archive layout-part" data-part="shop">
								<p><?php esc_html_e( 'Product Archive', 'porto' ); ?></p>
							</div>
							<div class="block content-inner-bottom-block layout-part" data-part="content-inner-bottom-block">
								<p><?php esc_html_e( 'Content Inner Bottom', 'porto' ); ?></p>
							</div>
						</div>
						<div class="right-sidebar layout-part" data-part="right-sidebar">
							<p><?php esc_html_e( 'Sidebar', 'porto' ); ?></p>
						</div>
					</div>
					<div class="content-bottom-block layout-part" data-part="content-bottom-block">
						<p><?php esc_html_e( 'Content Bottom', 'porto' ); ?></p>
					</div>						
					<div class="footer layout-part" data-part="footer">
						<p><?php esc_html_e( 'Footer', 'porto' ); ?></p>
					</div>
					<div class="bottom-block layout-part" data-part="bottom-block">
						<p><?php esc_html_e( 'Bottom', 'porto' ); ?></p>
					</div>						
				</div>
				<div class="part-options">
				</div>				
			</div>
		</div>
		<?php
		$parts = array( 'header', 'product', 'shop', 'single', 'archive', 'popup', 'footer', 'top-block', 'banner-block', 'content-top-block', 'content-inner-top-block', 'content-inner-bottom-block', 'right-sidebar', 'content-bottom-block', 'bottom-block' );
		foreach ( $parts as &$part ) :
			ob_start();
			$backup_part = $part;
			if ( in_array( $part, array( 'top-block', 'banner-block', 'content-top-block', 'content-inner-top-block', 'content-inner-bottom-block', 'content-bottom-block', 'bottom-block' ) ) ) {
				$part = 'block';
			}
			$this->add_control( 'note', $this->options[ $part ]['note'] );
			if ( 'right-sidebar' != $part ) :
				foreach ( $this->template_list[ $part ] as $page_id => $page_title ) {
					/* load saved values */
					$conditions = get_post_meta( $page_id, '_porto_builder_conditions', true );
					$block_pos  = get_post_meta( $page_id, '_porto_block_pos', true );
					if ( ! empty( $conditions ) && ( ( 'block' == $part && ! empty( $block_pos ) && 'block_' . $backup_part == $block_pos ) || ( 'block' != $part ) ) ) {
						$this->add_control( 'builder-blocks', $this->options[ $part ]['builder-blocks'], $page_id );
					}
				}
				$this->add_control( 'builder-blocks', $this->options[ $part ]['builder-blocks'], 'preset' );
				?>
			<div class="add-new-layout">
				<a href="#"><?php esc_html_e( 'Add New Layout Condition', 'porto' ); ?></a>
			</div>
				<?php
			endif;
			$output = ob_get_clean();
			?>
			<script type="text/template" id="porto-layout-<?php echo esc_attr( $backup_part ); ?>-options-html"><?php echo porto_filter_output( $output ); ?></script>
		<?php endforeach; ?>
	</main>
</div>
