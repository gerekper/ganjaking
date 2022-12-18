<?php

if ( ! function_exists( 'porto_get_related_posts' ) ) :
	function porto_get_related_posts( $post_id ) {
		global $porto_settings;

		$args = '';
		$args = wp_parse_args(
			$args,
			array(
				'showposts'           => isset( $porto_settings['post-related-count'] ) ? $porto_settings['post-related-count'] : '10',
				'post__not_in'        => array( $post_id ),
				'ignore_sticky_posts' => 0,
				'category__in'        => wp_get_post_categories( $post_id ),
				'orderby'             => isset( $porto_settings['post-related-orderby'] ) ? $porto_settings['post-related-orderby'] : 'rand',
			)
		);

		$query = new WP_Query( $args );

		return $query;
	}
endif;

if ( ! function_exists( 'porto_get_related_portfolios' ) ) :
	function porto_get_related_portfolios( $post_id ) {
		global $porto_settings;

		$args = '';

		$item_cats  = get_the_terms( $post_id, 'portfolio_cat' );
		$item_array = array();
		if ( $item_cats ) :
			foreach ( $item_cats as $item_cat ) {
				$item_array[] = $item_cat->term_id;
			}
		endif;

		$args = wp_parse_args(
			$args,
			array(
				'showposts'           => isset( $porto_settings['portfolio-related-count'] ) ? $porto_settings['portfolio-related-count'] : 10,
				'post__not_in'        => array( $post_id ),
				'ignore_sticky_posts' => 0,
				'post_type'           => 'portfolio',
				'tax_query'           => array(
					array(
						'taxonomy' => 'portfolio_cat',
						'field'    => 'id',
						'terms'    => $item_array,
					),
				),
				'orderby'             => isset( $porto_settings['portfolio-related-orderby'] ) ? $porto_settings['portfolio-related-orderby'] : 'rand',
			)
		);

		$query = new WP_Query( $args );

		return $query;
	}
endif;

if ( ! function_exists( 'porto_display_related_portfolios' ) ) {
	function porto_display_related_portfolios() {
		return porto_get_template_part( 'views/portfolios/single/related' );
	}
}

if ( ! function_exists( 'porto_get_related_members' ) ) :
	function porto_get_related_members( $post_id ) {
		global $porto_settings;

		$args = '';

		$item_cats  = get_the_terms( $post_id, 'member_cat' );
		$item_array = array();
		if ( $item_cats ) :
			foreach ( $item_cats as $item_cat ) {
				$item_array[] = $item_cat->term_id;
			}
		endif;

		$args = wp_parse_args(
			$args,
			array(
				'showposts'           => isset( $porto_settings['member-related-count'] ) ? $porto_settings['member-related-count'] : 10,
				'post__not_in'        => array( $post_id ),
				'ignore_sticky_posts' => 0,
				'post_type'           => 'member',
				'tax_query'           => array(
					array(
						'taxonomy' => 'member_cat',
						'field'    => 'id',
						'terms'    => $item_array,
					),
				),
				'orderby'             => isset( $porto_settings['member-related-orderby'] ) ? $porto_settings['member-related-orderby'] : 'rand',
			)
		);

		$query = new WP_Query( $args );

		return $query;
	}
endif;

if ( ! function_exists( 'porto_get_portfolios_by_ids' ) ) :
	function porto_get_portfolios_by_ids( $ids ) {
		$args = '';
		$ids  = explode( ',', $ids );
		$ids  = array_map( 'trim', $ids );

		$args = wp_parse_args(
			$args,
			array(
				'post_type'           => 'portfolio',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
				'post__in'            => $ids,
			)
		);

		$query = new WP_Query( $args );

		return $query;
	}
endif;

if ( ! function_exists( 'porto_get_posts_by_ids' ) ) :
	function porto_get_posts_by_ids( $ids ) {
		$args = '';
		$ids  = explode( ',', $ids );
		$ids  = array_map( 'trim', $ids );

		$args = wp_parse_args(
			$args,
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
				'post__in'            => $ids,
			)
		);

		$query = new WP_Query( $args );

		return $query;
	}
endif;

/**
 * Get Trim Description.
 *
 * @since 6.5.0
 *
 * @param  string $text  Whole excerpt content
 * @param  int    $limit Excerpt length
 * @param  string $unit  Excerpt unit
 * @return string Returns exctracted excerpt
 */
function porto_trim_description( $text = '', $limit = 45, $unit = 'words' ) {
	$content = wp_strip_all_tags( $text );
	$content = strip_shortcodes( $content );

	if ( ! $limit ) {
		$limit = 45;
	}

	if ( ! $unit ) {
		$unit = 'words';
	}

	if ( 'words' == $unit ) {
		$content = wp_trim_words( $content, $limit );
	} else { // by characters
		$affix   = ( strlen( $content ) < $limit ? '' : ' ...' );
		$content = mb_substr( $content, 0, $limit ) . $affix;
	}

	if ( $content ) {
		$content = wp_strip_all_tags( $content );
	}

	/**
	 * Filters trim description.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'porto_filter_trim_description', $content );
}

if ( ! function_exists( 'porto_get_excerpt' ) ) :
	function porto_get_excerpt( $limit = 45, $more_link = true, $more_style_block = false, $render_block = true ) {
		global $porto_settings;

		if ( ! $limit ) {
			$limit = 45;
		}

		if ( has_excerpt() ) {
			$content = get_the_excerpt();
		} else {
			$content = get_the_content();
		}

		$pattern = '/\[vc_custom_heading(.+?)?\](?:(.+?)?\[\/vc_custom_heading\])?/';
		$content = preg_replace( $pattern, '', $content );

		$content = porto_strip_tags( porto_the_content( $content, false, $render_block ) );

		if ( isset( $porto_settings['blog-excerpt-base'] ) && 'characters' == $porto_settings['blog-excerpt-base'] ) {
			if ( mb_strlen( $content ) > $limit ) {
				$content = mb_substr( $content, 0, $limit ) . '...';
			}
		} else {
			$content = explode( ' ', $content, $limit + 1 );

			if ( count( $content ) >= $limit + 1 ) {
				array_pop( $content );
				if ( $more_link ) {
					$content = implode( ' ', $content ) . '... ';
				} else {
					$content = implode( ' ', $content ) . '...';
				}
			} else {
				$content = implode( ' ', $content );
			}
		}

		if ( isset( $porto_settings['blog-excerpt-type'] ) && 'html' == $porto_settings['blog-excerpt-type'] ) {
			$content = porto_the_content( $content, false, $render_block );
		}

		if ( $content ) {
			$content = wp_kses_post( $content );
		}
		if ( $more_link ) {
			if ( $more_style_block ) {
				$content .= ' <a class="read-more read-more-block" href="' . esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '">' . esc_html__( 'Read More', 'porto' ) . ' <i class="fas fa-long-arrow-alt-right"></i></a>';
			} else {
				$content .= ' <a class="read-more" href="' . esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '">' . esc_html__( 'read more', 'porto' ) . ' <i class="fas fa-angle-right"></i></a>';
			}
		}

		if ( isset( $porto_settings['blog-excerpt-type'] ) && 'html' != $porto_settings['blog-excerpt-type'] ) {
			$content = '<p class="post-excerpt">' . $content . '</p>';
		}

		return $content;
	}
endif;

if ( ! function_exists( 'porto_the_content' ) ) :
	function porto_the_content( $content = null, $echo = true, $render_block = true ) {
		if ( null === $content ) {
			$content = get_the_content();
		}
		if ( $render_block && function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
			$result = do_shortcode( do_blocks( $content ) );
		} else {
			$result = do_shortcode( $content );
		}
		if ( ! $echo ) {
			return $result;
		}
		echo porto_filter_output( $result );
	}
endif;

if ( ! function_exists( 'porto_get_attachment' ) ) :
	function porto_get_attachment( $attachment_id, $size = 'full', $force_resize = false ) {
		if ( ! $attachment_id ) {
			return false;
		}
		$attachment = get_post( $attachment_id );
		if ( ! $force_resize ) {
			$image = wp_get_attachment_image_src( $attachment_id, $size );
		} else {
			$image = porto_image_resize( $attachment_id, $size );
			if ( ! $image ) {
				return false;
			}
		}

		if ( ! $attachment ) {
			return false;
		}

		return array(
			'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'caption'     => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'href'        => get_permalink( $attachment->ID ),
			'src'         => $image[0],
			'title'       => $attachment->post_title,
			'width'       => $image[1],
			'height'      => $image[2],
		);
	}
endif;

if ( ! function_exists( 'porto_comment' ) ) :
	function porto_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		global $post_layout;
		?>

	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

		<div class="comment-body">
			<div class="img-thumbnail">
				<?php echo get_avatar( $comment, 80 ); ?>
			</div>
			<div class="comment-block">
				<div class="comment-arrow"></div>
				<span class="comment-by">
					<strong><?php echo get_comment_author_link(); ?></strong>
				<?php if ( 'modern' == $post_layout ) : ?>
					<?php /* translators: %s: Comment date and time */ ?>
					<span class="date"><?php printf( esc_html__( '%1$s', 'porto' ), get_comment_date() ); ?></span>
				<?php else : ?>
					<span class="pt-right">
				<?php endif; ?>
					<?php if ( current_user_can( 'edit_comment', $comment ) ) : ?>
						<span> <?php edit_comment_link( esc_html__( 'Edit', 'porto' ), '  ', '' ); ?></span>
					<?php endif; ?>
						<span> 
						<?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'reply_text' => esc_html__( 'Reply', 'porto' ),
									'add_below'  => 'comment',
									'depth'      => $depth,
									'max_depth'  => $args['max_depth'],
								)
							)
						);
						?>
						</span>
				<?php if ( 'modern' != $post_layout ) : ?>
					</span>
				<?php endif; ?>
				</span>
				<div>
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'porto' ); ?></em>
						<br />
					<?php endif; ?>
					<?php comment_text(); ?>
				</div>
			<?php if ( 'modern' != $post_layout ) : ?>
				<?php /* translators: %s: Comment date and time */ ?>
				<span class="date pt-right"><?php printf( esc_html__( '%1$s at %2$s', 'porto' ), get_comment_date(), get_comment_time() ); ?></span>
			<?php endif; ?>
			</div>
		</div>

		<?php
	}
endif;

if ( ! function_exists( 'porto_post_date' ) ) :
	function porto_post_date( $echo = true ) {
		$result  = '<span class="day">' . esc_html( get_the_date( 'd', get_the_ID() ) ) . '</span>';
		$result .= '<span class="month">' . esc_html( get_the_date( 'M', get_the_ID() ) ) . '</span>';
		$result .= '<time datetime="' . esc_attr( get_the_date( 'Y-m-d', get_the_ID() ) ) . '">' . esc_html( get_the_date( '', get_the_ID() ) ) . '</time>';
		if ( $echo ) {
			echo porto_filter_output( $result );
		} else {
			return $result;
		}
	}
endif;

if ( ! function_exists( 'porto_post_format' ) ) :
	function porto_post_format() {
		global $porto_settings;

		$post        = get_post();
		$post_format = get_post_format();
		if ( ! empty( $porto_settings['post-format'] ) && $post_format ) {
			$ext_link = '';
			if ( 'link' == $post_format ) {
				$ext_link = get_post_meta( $post->ID, 'external_url', true );
				if ( $ext_link ) :
					?>
					<a href="<?php echo esc_url( $ext_link ); ?>">
					<?php
				endif;
			}
			if ( $post_format ) :
				?>
				<div class="format <?php echo esc_attr( $post_format ); ?>">
					<?php
					$fa_icon_escaped = '';
					switch ( $post_format ) {
						case 'aside':
							$fa_icon_escaped = 'fas fa-file-alt';
							break;
						case 'gallery':
							$fa_icon_escaped = 'fas fa-camera-retro';
							break;
						case 'link':
							$fa_icon_escaped = 'fas fa-link';
							break;
						case 'image':
							$fa_icon_escaped = 'far fa-image';
							break;
						case 'quote':
							$fa_icon_escaped = 'fas fa-quote-left';
							break;
						case 'video':
							$fa_icon_escaped = 'fas fa-film';
							break;
						case 'audio':
							$fa_icon_escaped = 'fas fa-music';
							break;
						case 'chat':
							$fa_icon_escaped = 'fas fa-comments';
							break;
						case 'status':
							$fa_icon_escaped = 'fas fa-exclamation-triangle';
							break;
					}
					?>
					<i class="<?php echo ! $fa_icon_escaped ? '' : $fa_icon_escaped; ?>"></i>
				</div>
				<?php
			endif;
			if ( $ext_link ) {
				echo '</a>';
			}
		}

		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky">%s</span>', ( ( isset( $porto_settings['hot-label'] ) && $porto_settings['hot-label'] ) ? esc_html( $porto_settings['hot-label'] ) : esc_html__( 'HOT', 'porto' ) ) );
		}
	}
endif;

if ( ! function_exists( 'porto_pagination' ) ) :
	function porto_pagination( $max_num_pages = null, $load_more = false, $query = false, $current_link = false, $current_page = false ) {
		global $wp_query, $wp_rewrite;

		if ( ! $query ) {
			$query = $wp_query;
		}
		$max_num_pages = ( $max_num_pages ) ? $max_num_pages : $query->max_num_pages;

		// Don't print empty markup if there's only one page.
		if ( $max_num_pages < 2 ) {
			return;
		}

		if ( $current_page ) {
			$paged = $current_page;
		} else {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
		}
		if ( $current_link ) {
			$page_num_link = html_entity_decode( esc_url( $current_link ) );
		} else {
			$page_num_link = html_entity_decode( get_pagenum_link() );
		}
		$query_args = array();
		$url_parts  = explode( '?', $page_num_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$page_num_link = esc_url( remove_query_arg( array_keys( $query_args ), $page_num_link ) );
		$page_num_link = trailingslashit( $page_num_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $page_num_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$next_text = ( $load_more ) ? __( 'Load More...', 'porto' ) : __( 'Next&nbsp;&nbsp;<i class="fas fa-long-arrow-alt-right"></i>', 'porto' );

		// Set up paginated links.
		$links = paginate_links(
			array(
				'base'      => $page_num_link,
				'format'    => $format,
				'total'     => $max_num_pages,
				'current'   => $paged,
				'end_size'  => 1,
				'mid_size'  => 1,
				'add_args'  => array_map( 'urlencode', $query_args ),
				'prev_text' => __( '<i class="fas fa-long-arrow-alt-left"></i>&nbsp;&nbsp;Prev', 'porto' ),
				'next_text' => $next_text,
			)
		);

		if ( $links ) :
			?>
			<div class="clearfix"></div>
			<div class="pagination-wrap<?php echo ! $load_more ? '' : ' load-more'; ?>">
				<?php if ( $load_more ) : ?>
				<div class="bounce-loader">
					<div class="bounce1"></div>
					<div class="bounce2"></div>
					<div class="bounce3"></div>
				</div>
				<?php endif; ?>
				<div class="pagination<?php echo ! $load_more ? '' : ' load-more'; ?>" role="navigation">
					<?php echo preg_replace( '/^\s+|\n|\r|\s+$/m', '', $links ); ?>
				</div>
			</div>
			<?php
		endif;
	}
endif;

add_filter( 'comments_popup_link_attributes', 'porto_add_comment_hash_scroll' );
function porto_add_comment_hash_scroll( $attributes ) {
	if ( is_single() ) {
		$attributes .= ' class="hash-scroll"';
	}
	return $attributes;
}

add_filter( 'comment_form_defaults', 'porto_comment_form_defaults' );
function porto_comment_form_defaults( $defaults ) {
	global $porto_settings;

	if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) {
		$defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
		$defaults['title_reply_after']  = '</h3>';
	}

	if ( is_singular( 'post' ) ) {
		global $post_layout;
		if ( 'woocommerce' === $post_layout ) {

			$defaults['title_reply']          = esc_html__( 'LEAVE A COMMENT', 'porto' );
			$defaults['comment_field']        = '<div id="comment-textarea" class="form-group mb20"><textarea id="comment" name="comment" rows="5" aria-required="true" class="form-control" placeholder="' . esc_attr__( 'Message', 'porto' ) . '*"></textarea></div>';
			$defaults['comment_notes_before'] = '<p class="comment-notes">' . esc_html__( 'Your email address will not be published. Required fields are marked *', 'porto' ) . '</p>';
			$defaults['id_submit']            = 'comment-submit';
			$defaults['class_submit']         = 'btn btn-accent btn-lg min-width';
			$defaults['fields']               = apply_filters(
				'comment_form_default_fields',
				array(
					'author'  => '<div class="col-md-4 form-group"><input name="author" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Name', 'porto' ) . '*"> </div>',
					'email'   => '<div class="col-md-4 form-group"><input name="email" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Email', 'porto' ) . '*"> </div>',
					'subject' => '<div class="col-md-4 form-group"><input name="subject" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Subject', 'porto' ) . '"> </div>',
				)
			);
		} else {
			$defaults['class_submit'] = 'btn btn-modern btn-primary';
		}
	}

	return $defaults;
}

add_action( 'comment_form_before_fields', 'porto_comment_form_before_fields' );
add_action( 'comment_form_after_fields', 'porto_comment_form_after_fields' );
if ( ! function_exists( 'porto_comment_form_before_fields' ) ) :
	function porto_comment_form_before_fields( $fields ) {
		if ( is_singular( 'post' ) ) {
			global $porto_settings;
			$post_layout = get_post_meta( get_the_ID(), 'post_layout', true );
			$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? ( isset( $porto_settings['post-content-layout'] ) ? $porto_settings['post-content-layout'] : 'large' ) : $post_layout;
			if ( 'woocommerce' === $post_layout ) {
				echo '<div class="row">';
			}
		}
	}
endif;
if ( ! function_exists( 'porto_comment_form_after_fields' ) ) :
	function porto_comment_form_after_fields( $fields ) {
		if ( is_singular( 'post' ) ) {
			global $porto_settings;
			$post_layout = get_post_meta( get_the_ID(), 'post_layout', true );
			$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? ( isset( $porto_settings['post-content-layout'] ) ? $porto_settings['post-content-layout'] : 'large' ) : $post_layout;
			if ( 'woocommerce' === $post_layout ) {
				echo '</div>';
			}
		}
	}
endif;

add_action( 'wp_ajax_porto_ajax_posts', 'porto_ajax_posts' );
add_action( 'wp_ajax_nopriv_porto_ajax_posts', 'porto_ajax_posts' );
if ( ! function_exists( 'porto_ajax_posts' ) ) :

	/**
	 * get posts by post type and category
	 *
	 * @since 6.2.2
	 */
	function porto_ajax_posts() {
		//check_ajax_referer( 'porto-nonce', 'nonce' );

		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		if ( empty( $_REQUEST['post_type'] ) ) {
			die();
		}
		$post_type    = $_REQUEST['post_type'];
		$is_shortcode = empty( $_REQUEST['extra'] ) ? false : true;

		$post_type_all = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'objects',
			'and'
		);

		$disabled_post_types = array( 'attachment', 'porto_builder', 'page', 'e-landing-page', 'product' );
		foreach ( $disabled_post_types as $disabled ) {
			unset( $post_type_all[ $disabled ] );
		}
		foreach ( $post_type_all as $key => $p_type ) {
			$allowed_post_types[] = esc_html( $key );
		}

		if ( $is_shortcode ) {
			$allowed_post_types[] = 'product';
		}
		if ( ! in_array( $post_type, $allowed_post_types ) ) {
			die();
		}

		if ( $is_shortcode ) {
			if ( isset( $_GET['extra'] ) ) {
				$atts = json_decode( wp_unslash( $_GET['extra'] ), true );
			} else {
				$atts = $_REQUEST['extra'];
			}
			$atts['posts_wrap_cls'] = 'posts-wrap';

			if ( isset( $_REQUEST['page'] ) ) {
				if ( is_front_page() ) {
					set_query_var( 'page', (int) $_REQUEST['page'] );
				} else {
					set_query_var( 'paged', (int) $_REQUEST['page'] );
				}
			}
			if ( ! empty( $_REQUEST['category'] ) ) {
				$atts['cats'] = sanitize_text_field( $_REQUEST['category'] );
			}

			echo '<div>';
			if ( isset( $atts['shortcode'] ) && 'porto_posts_grid' == $atts['shortcode'] ) {
				if ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) {
					if ( ! empty( $atts['post_found_nothing'] ) ) {
						$atts['post_found_nothing'] = str_replace( '%20', ' ', $atts['post_found_nothing'] );
					}
					include $template;
				}
			} elseif ( 'portfolio' == $post_type ) {
				if ( $template = porto_shortcode_template( 'porto_portfolios' ) ) {
					include $template;
				}
			} elseif ( 'member' == $post_type ) {
				if ( $template = porto_shortcode_template( 'porto_members' ) ) {
					include $template;
				}
			} elseif ( 'faq' == $post_type ) {
				if ( $template = porto_shortcode_template( 'porto_faqs' ) ) {
					include $template;
				}
			}
			echo '</div>';
		} else {

			$args = array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
			);

			if ( 'post' == $post_type ) {
				$tax = 'category';
			} else {
				$tax = $post_type . '_cat';
			}

			if ( ! empty( $_REQUEST['category'] ) ) {
				$cats       = sanitize_text_field( $_REQUEST['category'] );
				$cats       = array_map( 'trim', explode( ',', $cats ) );
				$field_name = 'slug';
				if ( is_numeric( $cats[0] ) ) {
					$field_name = 'term_id';
				}
				$args['tax_query'] = array(
					array(
						'taxonomy' => $tax,
						'field'    => $field_name,
						'terms'    => $cats,
					),
				);
			}

			if ( ! empty( $_REQUEST['post_in'] ) ) {
				$args['post__in'] = array_map( 'intval', explode( ',', $_REQUEST['post_in'] ) );
				$args['orderby']  = 'post__in';
			}

			if ( isset( $_REQUEST['count'] ) ) {
				$args['posts_per_page'] = (int) $_REQUEST['count'];
			}
			if ( isset( $_REQUEST['orderby'] ) ) {
				$args['orderby'] = sanitize_text_field( $_REQUEST['orderby'] );
			}
			if ( isset( $_REQUEST['order'] ) ) {
				$args['order'] = sanitize_text_field( $_REQUEST['order'] );
			}
			if ( isset( $_REQUEST['page'] ) ) {
				$args['paged'] = (int) $_REQUEST['page'];
			}

			$posts = new WP_Query( $args );
			if ( $posts->have_posts() ) {
				echo '<div>';
				global $porto_settings;
				if ( 'post' != $post_type && isset( $porto_settings[ $post_type . '-cat-sort-pos' ] ) && 'content' === $porto_settings[ $post_type . '-cat-sort-pos' ] ) {

					$terms = array();
					$taxs  = get_terms(
						array(
							'taxonomy'   => $post_type . '_cat',
							'hide_empty' => true,
							'orderby'    => isset( $porto_settings[ $post_type . '-cat-orderby' ] ) ? $porto_settings[ $post_type . '-cat-orderby' ] : 'name',
							'order'      => isset( $porto_settings[ $post_type . '-cat-order' ] ) ? $porto_settings[ $post_type . '-cat-order' ] : 'asc',
						)
					);

					foreach ( $taxs as $tax ) {
						$terms[ urldecode( $tax->slug ) ] = $tax->name;
					}
					if ( isset( $porto_settings[ $post_type . '-infinite' ] ) && 'infinite' != $porto_settings[ $post_type . '-infinite' ] && 'load_more' != $porto_settings[ $post_type . '-infinite' ] ) {
						$posts_terms = array();
						foreach ( $posts->posts as $post ) {
							$post_taxs = wp_get_post_terms( $post->ID, $post_type . '_cat', array( 'fields' => 'slugs' ) );
							if ( is_array( $post_taxs ) && ! empty( $post_taxs ) ) {
								$posts_terms = array_unique( array_merge( $posts_terms, $post_taxs ) );
							}
						}
						foreach ( $terms as $key => $value ) {
							if ( ! in_array( $key, $posts_terms ) ) {
								unset( $terms[ $key ] );
							}
						}
					}
					?>
					<ul class="<?php echo esc_attr( $post_type ); ?>-filter nav sort-source <?php echo isset( $porto_settings[ $post_type . '-cat-sort-style' ] ) && $porto_settings[ $post_type . '-cat-sort-style' ] ? 'sort-source-' . esc_attr( $porto_settings[ $post_type . '-cat-sort-style' ] ) : 'nav-pills', empty( $porto_settings[ $post_type . '-cat-ft' ] ) || empty( $porto_settings[ $post_type . '-infinite' ] ) ? '' : ' porto-ajax-filter'; ?>">
						<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto' ); ?></a></li>
						<?php foreach ( $terms as $tax_slug => $tax_name ) : ?>
							<li data-filter="<?php echo esc_attr( $tax_slug ); ?>"><a href="#"><?php echo esc_html( $tax_name ); ?></a></li>
						<?php endforeach; ?>
					</ul>

					<?php
				}

				echo '<div class="posts-wrap ' . esc_attr( $post_type ) . 's-container" data-cur_page="' . ( isset( $_REQUEST['page'] ) ? (int) $_REQUEST['page'] : 1 ) . '" data-max_page="' . intval( $posts->max_num_pages ) . '">';
				$post_layout = isset( $_REQUEST['post_layout'] ) ? $_REQUEST['post_layout'] : null;

				$template_args = array();
				if ( ! empty( $_REQUEST['image_size'] ) ) {
					$template_args['image_size'] = $_REQUEST['image_size'];
				}

				if ( 'member' == $post_type && $post_layout && 'advanced' != $post_layout ) {
					$GLOBALS['porto_member_view'] = $post_layout;
				}

				$post_counter = 0;
				while ( $posts->have_posts() ) {
					$posts->the_post();
					if ( 'post' == $post_type ) {
						get_template_part( 'content-blog', $post_layout );
					} elseif ( 'member' == $post_type && 'advanced' == $post_layout ) {
						$template_args['member_counter'] = $post_counter;
						porto_get_template_part( 'content', 'member', $template_args );
						$post_counter++;
					} else {
						porto_get_template_part( 'content-archive-' . $post_type, $post_layout, $template_args );
					}
				}

				echo '</div>';

				porto_pagination( null, false, $posts, ! empty( $_REQUEST['current_link'] ) ? $_REQUEST['current_link'] : false, ! empty( $_REQUEST['page'] ) ? (int) $_REQUEST['page'] : false );

				wp_reset_postdata();

				echo '</div>';
			}
		}
		// phpcs: enable
		die();
	}
endif;
